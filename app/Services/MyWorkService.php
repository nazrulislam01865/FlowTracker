<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MyWorkService
{
    public const JOBS_PER_PAGE = 3;

    /**
     * Paginate Jobs, never individual tasks. The first query selects only a
     * bounded page of Job IDs that contain personal work, then the page loads
     * only the matching task rows for those Jobs.
     */
    public function paginate(User $user, array $filters, int $perPage = self::JOBS_PER_PAGE, string $pageName = 'workPage'): array
    {
        $access = app(AccessControlService::class);
        $administratorAllTasks = $access->isAdministrator($user)
            && (string) ($filters['quick'] ?? 'all') === 'all';

        // Only the administrator's explicit All view includes completed tasks.
        // Job scope itself remains unchanged: My Work shows current active Jobs
        // only, so historical/completed/inactive Jobs cannot create extra groups.
        $baseTasks = $this->personalTaskQuery(
            $user,
            $filters,
            includeOpenConstraint: !$administratorAllTasks,
        );
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        $grouped = (clone $baseTasks)
            ->reorder()
            ->select('tasks.flow_job_id')
            ->selectRaw(
                "MIN(CASE
                    WHEN tasks.completed_at IS NOT NULL THEN 6
                    WHEN tasks.needs_attention = 1 THEN 0
                    WHEN tasks.due_date < ? THEN 1
                    WHEN tasks.due_date = ? THEN 2
                    WHEN LOWER(tasks.priority) = 'critical' THEN 3
                    WHEN LOWER(tasks.priority) = 'high' THEN 4
                    ELSE 5
                END) AS action_rank",
                [$today, $today],
            )
            ->selectRaw('MIN(CASE WHEN tasks.completed_at IS NULL THEN tasks.due_date END) AS min_due')
            ->groupBy('tasks.flow_job_id');

        $groupsQuery = DB::query()
            ->fromSub($grouped, 'my_work_groups')
            ->join('flow_jobs as my_work_jobs', 'my_work_jobs.id', '=', 'my_work_groups.flow_job_id')
            ->select([
                'my_work_groups.flow_job_id',
                'my_work_groups.action_rank',
                'my_work_groups.min_due',
                'my_work_jobs.job_number',
            ]);

        match ((string) ($filters['sort'] ?? 'action')) {
            'due' => $groupsQuery
                ->orderByRaw('my_work_groups.min_due is null')
                ->orderBy('my_work_groups.min_due')
                ->orderBy('my_work_jobs.job_number'),
            'job' => $groupsQuery->orderBy('my_work_jobs.job_number'),
            default => $groupsQuery
                ->orderBy('my_work_groups.action_rank')
                ->orderByRaw('my_work_groups.min_due is null')
                ->orderBy('my_work_groups.min_due')
                ->orderBy('my_work_jobs.job_number'),
        };

        $paginator = $groupsQuery->paginate(max(1, min(self::JOBS_PER_PAGE, $perPage)), ['*'], $pageName);
        $jobIds = collect($paginator->items())->pluck('flow_job_id')->map(fn ($id) => (int) $id)->values();

        if ($jobIds->isEmpty()) {
            return [
                'groups' => collect(),
                'paginator' => $paginator,
                'visibleTaskCount' => 0,
            ];
        }

        // Keep the page hydrate query count low. My Work only needs the
        // client/phase labels, so fetch them with LEFT JOINs instead of three
        // separate eager-load queries.
        $jobs = FlowJob::query()
            ->whereIn('flow_jobs.id', $jobIds)
            ->leftJoin('clients as my_work_clients', 'my_work_clients.id', '=', 'flow_jobs.client_id')
            ->leftJoin('workflow_phases as my_work_job_phases', 'my_work_job_phases.id', '=', 'flow_jobs.workflow_phase_id')
            ->select([
                'flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.title', 'flow_jobs.client_id', 'flow_jobs.workflow_phase_id',
                'flow_jobs.health', 'flow_jobs.progress', 'flow_jobs.status', 'flow_jobs.updated_at',
                'my_work_clients.name as my_work_client_name',
                'my_work_job_phases.name as my_work_phase_name',
                'my_work_job_phases.short_name as my_work_phase_short_name',
            ])
            ->get()
            ->keyBy('id');

        $tasks = (clone $baseTasks)
            ->whereIn('tasks.flow_job_id', $jobIds)
            ->leftJoin('workflow_phases as my_work_task_phases', 'my_work_task_phases.id', '=', 'tasks.workflow_phase_id')
            ->leftJoin('users as my_work_assignees', 'my_work_assignees.id', '=', 'tasks.assignee_id')
            ->select([
                'tasks.id', 'tasks.task_number', 'tasks.flow_job_id', 'tasks.workflow_phase_id',
                'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.priority',
                'tasks.due_date', 'tasks.needs_attention', 'tasks.attention_reason',
                'tasks.completed_at', 'tasks.updated_at',
                'my_work_task_phases.name as my_work_phase_name',
                'my_work_task_phases.short_name as my_work_phase_short_name',
                'my_work_assignees.name as my_work_assignee_name',
                'my_work_assignees.profile_image_path as my_work_assignee_profile_image_path',
            ]);

        $this->orderTasks($tasks, (string) ($filters['sort'] ?? 'action'), $today);
        $tasksByJob = $tasks->get()->groupBy('flow_job_id');
        $displayTimezone = app(WorkspaceSettingsService::class)->displayTimezone();
        $canOpenJobs = $access->can($user, 'jobs', 'view');

        $groups = $jobIds->map(function (int $jobId) use ($jobs, $tasksByJob, $user, $access, $displayTimezone, $today, $canOpenJobs) {
            $job = $jobs->get($jobId);
            if (!$job) return null;

            $taskRows = $tasksByJob->get($jobId, collect())
                ->map(fn (Task $task) => $this->presentTask($task, $user, $access, $displayTimezone, $today))
                ->values();

            return [
                'id' => (int) $job->id,
                'number' => (string) $job->displayOrderNumber(),
                'title' => (string) $job->title,
                'client' => (string) ($job->getAttribute('my_work_client_name') ?: 'No client'),
                'stage' => (string) ($job->getAttribute('my_work_phase_short_name') ?: $job->getAttribute('my_work_phase_name') ?: 'No phase'),
                'health' => (string) ($job->health ?: 'On Track'),
                'healthTone' => $this->tone((string) ($job->health ?: 'On Track')),
                'progress' => max(0, min(100, (int) $job->progress)),
                'taskCount' => $taskRows->count(),
                'route' => $canOpenJobs ? route('jobs.index', ['open' => $job->id]) : null,
                'tasks' => $taskRows,
            ];
        })->filter()->values();

        return [
            'groups' => $groups,
            'paginator' => $paginator,
            'visibleTaskCount' => $groups->sum('taskCount'),
        ];
    }

    /**
     * Summary cards are intentionally independent of search/sort so they stay
     * stable while the user explores the list. One aggregate query covers the
     * four date/action summaries; mentions uses the indexed notification link.
     */
    public function metrics(User $user): array
    {
        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->addDay()->toDateString();
        $weekEnd = $today->addDays(7)->toDateString();
        $base = $this->personalTaskQuery($user, []);

        $row = (clone $base)
            ->reorder()
            ->selectRaw(
                "SUM(CASE WHEN LOWER(tasks.status) NOT LIKE 'waiting%'
                    AND (tasks.needs_attention = 1 OR tasks.due_date <= ? OR LOWER(tasks.priority) IN ('critical','high'))
                    THEN 1 ELSE 0 END) AS attention_count",
                [$weekEnd],
            )
            ->selectRaw('SUM(CASE WHEN tasks.due_date < ? THEN 1 ELSE 0 END) AS overdue_count', [$todayDate])
            ->selectRaw('SUM(CASE WHEN tasks.due_date = ? THEN 1 ELSE 0 END) AS today_count', [$todayDate])
            ->selectRaw(
                "SUM(CASE WHEN tasks.due_date BETWEEN ? AND ? AND LOWER(tasks.status) NOT LIKE 'waiting%' THEN 1 ELSE 0 END) AS upcoming_count",
                [$tomorrow, $weekEnd],
            )
            ->selectRaw("SUM(CASE WHEN LOWER(tasks.status) LIKE 'waiting%' THEN 1 ELSE 0 END) AS waiting_count")
            ->selectRaw(
                "SUM(CASE WHEN EXISTS (
                    SELECT 1
                    FROM flow_notifications
                    WHERE flow_notifications.flow_task_id = tasks.id
                      AND flow_notifications.user_id = ?
                      AND flow_notifications.type = 'mention'
                ) THEN 1 ELSE 0 END) AS mentions_count",
                [$user->id],
            )
            ->first();

        return [
            'attention' => (int) ($row?->attention_count ?? 0),
            'overdue' => (int) ($row?->overdue_count ?? 0),
            'today' => (int) ($row?->today_count ?? 0),
            'upcoming' => (int) ($row?->upcoming_count ?? 0),
            'waiting' => (int) ($row?->waiting_count ?? 0),
            'mentions' => (int) ($row?->mentions_count ?? 0),
        ];
    }

    /** @return list<string> */
    public function statusOptions(): array
    {
        return app(MasterDataService::class)
            ->active('task_status')
            ->pluck('name')
            ->map(fn ($status) => trim((string) $status))
            ->filter()
            ->unique(fn ($status) => strtolower($status))
            ->values()
            ->all();
    }

    public function findPersonalVisibleTask(User $user, int $taskId): Task
    {
        return $this->personalTaskQuery($user, [], includeOpenConstraint: false)
            ->whereKey($taskId)
            ->firstOrFail();
    }

    /**
     * Count the same open task scope used by the My Work list. Administrators
     * see every visible open task from active Jobs; normal users see only
     * tasks assigned directly to them. Keeping this in one service prevents
     * the sidebar badge and page results from drifting apart.
     */
    public function openTaskCount(User $user): int
    {
        return $this->personalTaskQuery($user, [])
            ->reorder()
            ->count('tasks.id');
    }

    private function personalTaskQuery(User $user, array $filters, bool $includeOpenConstraint = true): Builder
    {
        $access = app(AccessControlService::class);
        $query = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', fn (Builder $job) => $job
                ->whereHas('client', fn (Builder $client) => $client->where('is_active', true))
                ->whereNull('completed_at')
                ->whereNotIn('status', JobService::INACTIVE_STATUSES));

        // My Work scope is intentionally role-sensitive:
        // - administrators: all visible tasks belonging to active Jobs
        // - normal users: only tasks explicitly assigned to that user
        // Mentions remain a filter inside that allowed scope; they never make
        // an unassigned task appear in a normal user's My Work list.
        if (!$access->isAdministrator($user)) {
            $query->where('tasks.assignee_id', $user->id);
        }

        if ($includeOpenConstraint) {
            $query->whereNull('tasks.completed_at');
        }

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '' && $this->searchIsUsable($search)) {
            $like = '%'.$search.'%';
            $prefix = $search.'%';
            $looksLikeReference = preg_match('/^(JOB|TSK|TASK|ORD)[-0-9]/i', $search) === 1;

            $query->where(function (Builder $inner) use ($like, $prefix, $looksLikeReference) {
                $inner->where('tasks.task_number', 'like', $looksLikeReference ? $prefix : $like)
                    ->orWhere('tasks.title', 'like', $like)
                    ->orWhere('tasks.attention_reason', 'like', $like)
                    ->orWhereHas('assignee', fn (Builder $assignee) => $assignee->where('name', 'like', $like))
                    ->orWhereHas('job', fn (Builder $job) => $job
                        ->where('job_number', 'like', $looksLikeReference ? $prefix : $like)
                        ->orWhere('order_number', 'like', $looksLikeReference ? $prefix : $like)
                        ->orWhere('title', 'like', $like)
                        ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $like)));
            });
        }

        $this->applyQuickFilter($query, $user, (string) ($filters['quick'] ?? 'all'));

        return $query;
    }

    private function applyQuickFilter(Builder $query, User $user, string $quick): void
    {
        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->addDay()->toDateString();
        $weekEnd = $today->addDays(7)->toDateString();

        match ($quick) {
            'attention' => $query
                ->whereRaw("LOWER(tasks.status) NOT LIKE 'waiting%'")
                ->where(fn (Builder $q) => $q
                    ->where('tasks.needs_attention', true)
                    ->orWhere('tasks.due_date', '<=', $weekEnd)
                    ->orWhereRaw("LOWER(tasks.priority) IN ('critical','high')")),
            'overdue' => $query->where('tasks.due_date', '<', $todayDate),
            'today' => $query->where('tasks.due_date', $todayDate),
            'upcoming' => $query
                ->whereBetween('tasks.due_date', [$tomorrow, $weekEnd])
                ->whereRaw("LOWER(tasks.status) NOT LIKE 'waiting%'"),
            'waiting' => $query->whereRaw("LOWER(tasks.status) LIKE 'waiting%'"),
            'mentions' => $query->whereExists($this->mentionExistsSubquery($user)),
            default => null,
        };
    }

    private function mentionExistsSubquery(User $user): \Closure
    {
        return fn ($notification) => $notification
            ->selectRaw('1')
            ->from('flow_notifications')
            ->whereColumn('flow_notifications.flow_task_id', 'tasks.id')
            ->where('flow_notifications.user_id', $user->id)
            ->where('flow_notifications.type', 'mention');
    }

    private function orderTasks(Builder $query, string $sort, string $today): void
    {
        match ($sort) {
            'due' => $query
                ->orderByRaw('tasks.completed_at is not null')
                ->orderByRaw('tasks.due_date is null')
                ->orderBy('tasks.due_date')
                ->orderBy('tasks.id'),
            'job' => $query
                ->orderByRaw('tasks.completed_at is not null')
                ->orderBy('tasks.id'),
            default => $query
                ->orderByRaw(
                    "CASE
                        WHEN tasks.completed_at IS NOT NULL THEN 6
                        WHEN tasks.needs_attention = 1 THEN 0
                        WHEN tasks.due_date < ? THEN 1
                        WHEN tasks.due_date = ? THEN 2
                        WHEN LOWER(tasks.priority) = 'critical' THEN 3
                        WHEN LOWER(tasks.priority) = 'high' THEN 4
                        ELSE 5
                    END",
                    [$today, $today],
                )
                ->orderByRaw('tasks.due_date is null')
                ->orderBy('tasks.due_date')
                ->orderBy('tasks.id'),
        };
    }

    private function presentTask(Task $task, User $user, AccessControlService $access, string $displayTimezone, string $today): array
    {
        $dueDate = $task->due_date?->format('Y-m-d');
        $completed = $task->completed_at !== null;
        $dueLabel = 'No due date';
        $dueTone = 'normal';

        if ($dueDate) {
            if ($completed) {
                $dueLabel = $task->due_date?->format('M j') ?: 'No due date';
            } elseif ($dueDate < $today) {
                $days = abs((int) app(WorkspaceSettingsService::class)->localToday()->diffInDays($task->due_date, false));
                $dueLabel = 'Overdue '.max(1, $days).' '.($days === 1 ? 'day' : 'days');
                $dueTone = 'overdue';
            } elseif ($dueDate === $today) {
                $dueLabel = 'Today';
                $dueTone = 'today';
            } else {
                $dueLabel = $task->due_date?->format('M j') ?: 'No due date';
            }
        }

        $flag = 'No flag';
        if (!$completed && $task->needs_attention) {
            $flag = trim((string) $task->attention_reason) ?: 'Management attention';
        } elseif (!$completed && $dueTone === 'overdue') {
            $flag = 'Overdue';
        } elseif (!$completed && $dueTone === 'today') {
            $flag = 'Due Today';
        } elseif (!$completed && strcasecmp((string) $task->priority, 'Critical') === 0) {
            $flag = 'Critical';
        }

        $updatedAt = $task->updated_at?->copy()->setTimezone($displayTimezone);

        return [
            'id' => (int) $task->id,
            'number' => (string) $task->task_number,
            'title' => (string) $task->title,
            'phase' => (string) ($task->getAttribute('my_work_phase_short_name') ?: $task->getAttribute('my_work_phase_name') ?: 'No phase'),
            'assignee' => (string) ($task->getAttribute('my_work_assignee_name') ?: 'Unassigned'),
            'assigneeId' => $task->assignee_id ? (int) $task->assignee_id : null,
            'assigneeAvatar' => ($task->assignee_id && $task->getAttribute('my_work_assignee_profile_image_path'))
                ? route('profile-images.show', ['user' => $task->assignee_id, 'filename' => basename((string) $task->getAttribute('my_work_assignee_profile_image_path'))], false)
                : null,
            'due' => $dueLabel,
            'dueValue' => $dueDate ?: '',
            'dueDisplay' => $task->due_date?->format('M j, Y') ?? 'Set due date',
            'dueTone' => $dueTone,
            'status' => (string) $task->status,
            'flag' => $flag,
            'flagTone' => $this->tone($flag),
            'updated' => $updatedAt?->diffForHumans() ?: '—',
            'version' => (string) $task->getRawOriginal('updated_at'),
            'canEdit' => $access->canEditVisibleTask($user, $task),
            'route' => route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]),
        ];
    }

    private function tone(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') return 'green';
        if (str_contains($value, 'overdue') || str_contains($value, 'blocked') || str_contains($value, 'risk') || str_contains($value, 'delayed')) return 'red';
        if (str_contains($value, 'critical') || str_contains($value, 'today') || str_contains($value, 'wait') || str_contains($value, 'revision') || str_contains($value, 'dependency') || str_contains($value, 'watch') || str_contains($value, 'attention')) return 'amber';
        if (str_contains($value, 'unassigned') || str_contains($value, 'qc')) return 'blue';
        return 'green';
    }

    private function searchIsUsable(string $search): bool
    {
        if (mb_strlen($search) >= 2) return true;
        return preg_match('/^(JOB|TSK|TASK|ORD)[-0-9]/i', $search) === 1;
    }
}
