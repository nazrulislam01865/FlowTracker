<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use App\Support\BoardLaneResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BoardTaskPackService
{
    public const JOBS_PER_PAGE = 10;

    /**
     * Board Task Pack visibility is intentionally different from My Work.
     *
     * Administrators can inspect every active Job. A normal user can inspect a
     * Job only when at least one non-deleted task in that Job is assigned to
     * them. Once a Job is associated, its full task list is visible here so the
     * user can understand the surrounding workflow without exposing unrelated
     * Jobs.
     */
    public function visibleJobQuery(User $user, bool $includeCompleted = false): Builder
    {
        $access = app(AccessControlService::class);
        $query = FlowJob::query()
            ->whereHas('client', fn (Builder $client) => $client->where('is_active', true))
            ->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES);

        if (!$includeCompleted) {
            $query
                ->whereNull('flow_jobs.completed_at')
                ->whereRaw("LOWER(TRIM(flow_jobs.status)) != 'completed'");
        }

        if (!$access->can($user, 'tasks', 'view')) {
            return $query->whereRaw('1 = 0');
        }

        if ($access->isAdministrator($user)) {
            return $query;
        }

        return $query->whereExists(function ($assigned) use ($user): void {
            $assigned
                ->selectRaw('1')
                ->from('tasks as board_assigned_tasks')
                ->whereColumn('board_assigned_tasks.flow_job_id', 'flow_jobs.id')
                ->where('board_assigned_tasks.assignee_id', $user->id)
                ->whereNull('board_assigned_tasks.deleted_at');
        });
    }

    /**
     * Paginate Job groups first, then retrieve only the tasks for those Job IDs.
     * This avoids loading every task into Livewire, keeps a Job together on one
     * page, and makes query cost proportional to the visible page instead of the
     * workspace's total task count.
     */
    public function paginate(
        User $user,
        array $filters,
        int $perPage = self::JOBS_PER_PAGE,
        string $pageName = 'taskPackPage',
    ): array {
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $baseTasks = $this->filteredTaskQuery($user, $filters);
        $quick = (string) ($filters['quick'] ?? 'all');
        $hideCompleted = (bool) ($filters['hide_completed'] ?? true);
        $openOnly = $hideCompleted || $quick !== 'all';

        $grouped = (clone $baseTasks)
            ->reorder()
            ->select('tasks.flow_job_id')
            ->selectRaw(
                "MIN(CASE
                    WHEN tasks.completed_at IS NOT NULL OR LOWER(TRIM(tasks.status)) = 'completed' THEN 6
                    WHEN tasks.needs_attention = 1 THEN 0
                    WHEN tasks.due_date < ? THEN 1
                    WHEN tasks.due_date = ? THEN 2
                    WHEN LOWER(tasks.priority) = 'critical' THEN 3
                    WHEN LOWER(tasks.priority) = 'high' THEN 4
                    ELSE 5
                END) AS action_rank",
                [$today, $today],
            )
            ->selectRaw("MIN(CASE WHEN tasks.completed_at IS NULL AND LOWER(TRIM(tasks.status)) != 'completed' THEN tasks.due_date END) AS min_due")
            ->selectRaw('MAX(tasks.updated_at) AS last_task_update')
            ->groupBy('tasks.flow_job_id');

        $groupsQuery = DB::query()
            ->fromSub($grouped, 'board_task_pack_groups')
            ->join('flow_jobs as board_task_pack_jobs', 'board_task_pack_jobs.id', '=', 'board_task_pack_groups.flow_job_id')
            ->select([
                'board_task_pack_groups.flow_job_id',
                'board_task_pack_groups.action_rank',
                'board_task_pack_groups.min_due',
                'board_task_pack_groups.last_task_update',
                'board_task_pack_jobs.job_number',
            ]);

        match ((string) ($filters['sort'] ?? 'action')) {
            'due' => $groupsQuery
                ->orderByRaw('board_task_pack_groups.min_due is null')
                ->orderBy('board_task_pack_groups.min_due')
                ->orderBy('board_task_pack_jobs.job_number'),
            'job' => $groupsQuery->orderBy('board_task_pack_jobs.job_number'),
            'updated' => $groupsQuery
                ->orderByDesc('board_task_pack_groups.last_task_update')
                ->orderBy('board_task_pack_jobs.job_number'),
            default => $groupsQuery
                ->orderBy('board_task_pack_groups.action_rank')
                ->orderByRaw('board_task_pack_groups.min_due is null')
                ->orderBy('board_task_pack_groups.min_due')
                ->orderBy('board_task_pack_jobs.job_number'),
        };

        $paginator = $groupsQuery->paginate(max(1, min(25, $perPage)), ['*'], $pageName);
        $jobIds = collect($paginator->items())
            ->pluck('flow_job_id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($jobIds->isEmpty()) {
            return [
                'groups' => collect(),
                'paginator' => $paginator,
                'visibleTaskCount' => 0,
            ];
        }

        $jobs = FlowJob::query()
            ->whereIn('id', $jobIds)
            ->select([
                'id', 'job_number', 'title', 'client_id', 'workflow_phase_id',
                'health', 'progress', 'status', 'updated_at',
            ])
            ->with([
                'client:id,name,logo_path',
                'phase:id,name,short_name,sequence',
            ])
            ->get()
            ->keyBy('id');

        // Normally filters choose which Job groups belong on the page and the
        // Board then shows the complete Task Pack for each qualifying Job. The
        // Mentions chip is intentionally different: it is a task-level filter,
        // so only the exact tasks whose own comments contain a valid @mention
        // are loaded. A mention on one task must never reveal sibling task rows
        // merely because they belong to the same Order.
        $mentionsOnly = $quick === 'mentions';
        // An explicit assignee filter is also task-level. This is important for
        // dashboard drill-downs: clicking an assignee's workload must show only
        // that person's tasks, not every sibling task from the same Orders.
        $taskLevelFilter = $mentionsOnly || filled($filters['assignee'] ?? null);
        $tasks = $taskLevelFilter
            ? (clone $baseTasks)->whereIn('tasks.flow_job_id', $jobIds)
            : Task::query()->whereIn('tasks.flow_job_id', $jobIds);

        // Group-level filters intentionally load the surrounding Task Pack for
        // each matching Order. Hide completed is different: it is a display
        // constraint and must also be applied to the hydrated sibling rows.
        // Without this second constraint, a Job qualified through one open task
        // but its completed sibling tasks were added back immediately afterward.
        if (!$taskLevelFilter && $openOnly) {
            $tasks
                ->whereNull('tasks.completed_at')
                ->whereRaw("LOWER(TRIM(tasks.status)) != 'completed'");
        }

        $tasks->select([
                'tasks.id', 'tasks.task_number', 'tasks.flow_job_id', 'tasks.workflow_phase_id',
                'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.priority', 'tasks.progress',
                'tasks.due_date', 'tasks.needs_attention', 'tasks.task_flag_id', 'tasks.attention_reason',
                'tasks.completed_at', 'tasks.updated_at',
            ])
            ->with([
                'phase:id,name,short_name,sequence',
                'assignee:id,name,department_id,profile_image_path',
                'attentionFlag:id,name,status,sort_order',
            ]);

        $this->orderTasks($tasks, (string) ($filters['sort'] ?? 'action'), $today);
        $tasksByJob = $tasks->get()->groupBy('flow_job_id');

        $access = app(AccessControlService::class);
        $displayTimezone = app(WorkspaceSettingsService::class)->displayTimezone();
        $openableJobIds = $access->can($user, 'jobs', 'view')
            ? app(JobService::class)->visibleQuery($user)
                ->whereIn('flow_jobs.id', $jobIds)
                ->pluck('flow_jobs.id')
                ->map(fn ($id) => (int) $id)
                ->flip()
            : collect();

        $groups = $jobIds->map(function (int $jobId) use (
            $jobs,
            $tasksByJob,
            $user,
            $access,
            $displayTimezone,
            $today,
            $openableJobIds,
        ) {
            $job = $jobs->get($jobId);
            if (!$job) return null;

            $canOpenJob = $openableJobIds->has($jobId);
            $taskRows = $tasksByJob->get($jobId, collect())
                ->map(fn (Task $task) => $this->presentTask(
                    $task,
                    $user,
                    $access,
                    $displayTimezone,
                    $today,
                    $canOpenJob,
                ))
                ->values();

            return [
                'id' => (int) $job->id,
                'number' => (string) $job->displayOrderNumber(),
                'title' => (string) $job->title,
                'client' => (string) ($job->client?->name ?: 'No client'),
                'stage' => (string) ($job->phase?->short_name ?: $job->phase?->name ?: 'No phase'),
                'health' => (string) ($job->health ?: 'On Track'),
                'healthTone' => $this->tone((string) ($job->health ?: 'On Track')),
                'progress' => max(0, min(100, (int) $job->progress)),
                'taskCount' => $taskRows->count(),
                'route' => $canOpenJob ? route('jobs.index', ['open' => $job->id]) : null,
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
     * My Work-style summary for the Task Board scope. The aggregate is based
     * on open tasks from Jobs the current user is allowed to inspect here.
     */
    public function metrics(User $user): array
    {
        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->copy()->addDay()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();

        $visibleJobIds = $this->visibleJobQuery($user)
            ->reorder()
            ->select('flow_jobs.id');

        $base = Task::query()
            ->whereIn('tasks.flow_job_id', $visibleJobIds)
            ->whereNull('tasks.completed_at')
            ->whereRaw("LOWER(TRIM(tasks.status)) != 'completed'");

        $row = (clone $base)
            ->reorder()
            ->selectRaw(
                "SUM(CASE WHEN tasks.needs_attention = 1
                    OR (LOWER(TRIM(tasks.status)) NOT LIKE 'waiting%'
                        AND (tasks.due_date <= ? OR LOWER(tasks.priority) IN ('critical','high')))
                    THEN 1 ELSE 0 END) AS attention_count",
                [$weekEnd],
            )
            ->selectRaw('SUM(CASE WHEN tasks.due_date < ? THEN 1 ELSE 0 END) AS overdue_count', [$todayDate])
            ->selectRaw('SUM(CASE WHEN tasks.due_date = ? THEN 1 ELSE 0 END) AS today_count', [$todayDate])
            ->selectRaw(
                "SUM(CASE WHEN tasks.due_date BETWEEN ? AND ? AND LOWER(TRIM(tasks.status)) NOT LIKE 'waiting%' THEN 1 ELSE 0 END) AS upcoming_count",
                [$tomorrow, $weekEnd],
            )
            ->selectRaw("SUM(CASE WHEN LOWER(TRIM(tasks.status)) LIKE 'waiting%' THEN 1 ELSE 0 END) AS waiting_count")
            ->first();

        $mentions = (clone $base)
            ->whereExists($this->commentMentionExistsSubquery())
            ->count();

        return [
            'attention' => (int) ($row?->attention_count ?? 0),
            'overdue' => (int) ($row?->overdue_count ?? 0),
            'today' => (int) ($row?->today_count ?? 0),
            'upcoming' => (int) ($row?->upcoming_count ?? 0),
            'waiting' => (int) ($row?->waiting_count ?? 0),
            'mentions' => (int) $mentions,
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

    private function filteredTaskQuery(User $user, array $filters): Builder
    {
        $quick = (string) ($filters['quick'] ?? 'all');
        $hideCompleted = (bool) ($filters['hide_completed'] ?? true);
        $openOnly = $hideCompleted || $quick !== 'all';

        $visibleJobIds = $this->visibleJobQuery($user, includeCompleted: !$openOnly)
            ->reorder()
            ->select('flow_jobs.id');

        $query = Task::query()
            ->whereIn('tasks.flow_job_id', $visibleJobIds);

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $prefix = $search.'%';
            $looksLikeReference = preg_match('/^(JOB|TSK|TASK|ORD)[-0-9]/i', $search) === 1;

            $query->where(function (Builder $inner) use ($like, $prefix, $looksLikeReference): void {
                $inner->where('tasks.task_number', 'like', $looksLikeReference ? $prefix : $like)
                    ->orWhere('tasks.title', 'like', $like)
                    ->orWhere('tasks.attention_reason', 'like', $like)
                    ->orWhereHas('attentionFlag', fn (Builder $flag) => $flag->where('name', 'like', $like))
                    ->orWhereHas('assignee', fn (Builder $assignee) => $assignee->where('name', 'like', $like))
                    ->orWhereHas('job', fn (Builder $job) => $job
                        ->where('job_number', 'like', $looksLikeReference ? $prefix : $like)
                        ->orWhere('order_number', 'like', $looksLikeReference ? $prefix : $like)
                        ->orWhere('title', 'like', $like)
                        ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $like)));
            });
        }

        $query
            ->when($filters['job'] ?? null, fn (Builder $q, $value) => $q->where('tasks.flow_job_id', $value))
            ->when($filters['client'] ?? null, fn (Builder $q, $value) => $q->whereHas('job', fn (Builder $job) => $job->where('client_id', $value)))
            ->when($filters['assignee'] ?? null, fn (Builder $q, $value) => $q->where('tasks.assignee_id', $value))
            ->when($filters['status'] ?? null, fn (Builder $q, $value) => $q->whereIn('tasks.status', BoardLaneResolver::databaseStatusValues((string) $value)))
            ->when($filters['due'] ?? null, function (Builder $q, $value): void {
                $today = app(WorkspaceSettingsService::class)->localToday();
                match ($value) {
                    'overdue' => $q->where('tasks.due_date', '<', $today->toDateString()),
                    'today' => $q->where('tasks.due_date', $today->toDateString()),
                    'week' => $q->whereBetween('tasks.due_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()]),
                    'month' => $q->whereBetween('tasks.due_date', [$today->toDateString(), $today->copy()->addDays(30)->toDateString()]),
                    'none' => $q->whereNull('tasks.due_date'),
                    default => null,
                };
            });

        $this->applyQuickFilter($query, $user, $quick);

        if ($openOnly) {
            $query
                ->whereNull('tasks.completed_at')
                ->whereRaw("LOWER(TRIM(tasks.status)) != 'completed'");
        }

        return $query;
    }

    private function applyQuickFilter(Builder $query, User $user, string $quick): void
    {
        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->copy()->addDay()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();

        if ($quick !== 'all') {
            $query
                ->whereNull('tasks.completed_at')
                ->whereRaw("LOWER(TRIM(tasks.status)) != 'completed'");
        }

        match ($quick) {
            'attention' => $query->where(fn (Builder $q) => $q
                ->where('tasks.needs_attention', true)
                ->orWhere(fn (Builder $derived) => $derived
                    ->whereRaw("LOWER(TRIM(tasks.status)) NOT LIKE 'waiting%'")
                    ->where(fn (Builder $condition) => $condition
                        ->where('tasks.due_date', '<=', $weekEnd)
                        ->orWhereRaw("LOWER(tasks.priority) IN ('critical','high')")))),
            'overdue' => $query->where('tasks.due_date', '<', $todayDate),
            'today' => $query->where('tasks.due_date', $todayDate),
            'upcoming' => $query
                ->whereBetween('tasks.due_date', [$tomorrow, $weekEnd])
                ->whereRaw("LOWER(TRIM(tasks.status)) NOT LIKE 'waiting%'"),
            'waiting' => $query->whereRaw("LOWER(TRIM(tasks.status)) LIKE 'waiting%'"),
            'mentions' => $query->whereExists($this->commentMentionExistsSubquery()),
            default => null,
        };
    }

    private function commentMentionExistsSubquery(): \Closure
    {
        // Mentions on All Tasks are about the task itself, not about whether the
        // currently signed-in user received a notification. TaskService stores
        // MentionService's parsed IDs on the exact task.comment activity. This
        // keeps the filter user-agnostic and prevents an Order-level mention,
        // description mention, email address, or sibling-task mention from
        // qualifying the wrong task.
        return fn ($activity) => $activity
            ->selectRaw('1')
            ->from('activities as board_task_mention_activity')
            ->whereColumn('board_task_mention_activity.subject_id', 'tasks.id')
            ->where('board_task_mention_activity.subject_type', Task::class)
            ->where('board_task_mention_activity.event', 'task.comment')
            ->whereNotNull('board_task_mention_activity.meta')
            // MySQL normalizes JSON whitespace, so string matching the serialized
            // object is unreliable. Inspect the parsed mention_user_ids array
            // directly so All Tasks and My Work agree in local and cloud builds.
            ->whereJsonLength('board_task_mention_activity.meta->mention_user_ids', '>', 0);
    }

    private function orderTasks(Builder $query, string $sort, string $today): void
    {
        match ($sort) {
            'due' => $query
                ->orderByRaw("CASE WHEN tasks.completed_at IS NOT NULL OR LOWER(TRIM(tasks.status)) = 'completed' THEN 1 ELSE 0 END")
                ->orderByRaw('tasks.due_date is null')
                ->orderBy('tasks.due_date')
                ->orderBy('tasks.id'),
            'job' => $query
                ->orderByRaw("CASE WHEN tasks.completed_at IS NOT NULL OR LOWER(TRIM(tasks.status)) = 'completed' THEN 1 ELSE 0 END")
                ->orderBy('tasks.workflow_phase_id')
                ->orderBy('tasks.id'),
            'updated' => $query
                ->orderByRaw("CASE WHEN tasks.completed_at IS NOT NULL OR LOWER(TRIM(tasks.status)) = 'completed' THEN 1 ELSE 0 END")
                ->orderByDesc('tasks.updated_at')
                ->orderBy('tasks.id'),
            default => $query
                ->orderByRaw(
                    "CASE
                        WHEN tasks.completed_at IS NOT NULL OR LOWER(TRIM(tasks.status)) = 'completed' THEN 6
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

    private function presentTask(
        Task $task,
        User $user,
        AccessControlService $access,
        string $displayTimezone,
        string $today,
        bool $canOpenJob,
    ): array {
        $completed = $task->completed_at !== null || BoardLaneResolver::isCompleted((string) $task->status);
        $dueDate = $task->due_date?->format('Y-m-d');
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
            $flag = app(TaskFlagService::class)->labelForTask($task) ?: 'Management attention';
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
            'assignee' => (string) ($task->assignee?->name ?: 'Unassigned'),
            'assigneeImage' => ($task->assignee?->id && $task->assignee?->profile_image_path)
                ? route('profile-images.show', [
                    'user' => $task->assignee->id,
                    'filename' => basename((string) $task->assignee->profile_image_path),
                ], false)
                : null,
            'isMine' => (int) ($task->assignee_id ?: 0) === (int) $user->id,
            'phase' => (string) ($task->phase?->short_name ?: $task->phase?->name ?: 'No phase'),
            'due' => $dueLabel,
            'dueTone' => $dueTone,
            'status' => (string) $task->status,
            'flag' => $flag,
            'flagTone' => $this->tone($flag),
            'updated' => $updatedAt?->diffForHumans() ?: '—',
            'version' => (string) $task->getRawOriginal('updated_at'),
            'canEdit' => $this->canEditTaskWithoutQuery($user, $task, $access),
            'route' => $canOpenJob ? route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) : null,
        ];
    }

    /**
     * Mirror task-edit authorization using already eager-loaded fields so the
     * list does not execute one authorization query per task row.
     */
    private function canEditTaskWithoutQuery(User $user, Task $task, AccessControlService $access): bool
    {
        if ($access->isAdministrator($user)) return true;
        if (!$access->can($user, 'tasks', 'edit')) return false;

        $scope = $access->scope($user, 'tasks');
        $isOwnTask = (int) ($task->assignee_id ?: 0) === (int) $user->id;

        if ($scope === 'all_records') {
            return $access->canEditAll($user, 'tasks')
                || ($isOwnTask && $access->canEditOwn($user, 'tasks'));
        }

        if ($scope === 'department') {
            $sameDepartment = $user->department_id
                && (int) ($task->assignee?->department_id ?: 0) === (int) $user->department_id;

            if (!$sameDepartment) return false;

            return $access->canEditAll($user, 'tasks')
                || ($isOwnTask && $access->canEditOwn($user, 'tasks'));
        }

        // Assigned/own task scopes stay assignee-strict even though the Board
        // intentionally provides read-only context for the other Job tasks.
        return $isOwnTask && ($access->canEditOwn($user, 'tasks') || $access->canEditAll($user, 'tasks'));
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
}
