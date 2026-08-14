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
        $quick = (string) ($filters['quick'] ?? 'all');
        $hideCompleted = (bool) ($filters['hide_completed'] ?? false);
        $showCompleted = !$hideCompleted && $quick === 'all';

        // Completed work is visible by default in the neutral My Work view.
        // Action/date/mention views remain open-task-only; Hide completed can be
        // enabled explicitly from the toolbar when the user wants a tighter list.
        $baseTasks = $this->personalTaskQuery(
            $user,
            $filters,
            includeOpenConstraint: !$showCompleted,
            includeCompletedJobs: $showCompleted,
        );
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

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
            ->leftJoin('master_records as my_work_task_flags', 'my_work_task_flags.id', '=', 'tasks.task_flag_id')
            ->select([
                'tasks.id', 'tasks.task_number', 'tasks.flow_job_id', 'tasks.workflow_phase_id',
                'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.priority',
                'tasks.due_date', 'tasks.needs_attention', 'tasks.task_flag_id', 'tasks.attention_reason',
                'tasks.completed_at', 'tasks.updated_at',
                'my_work_task_phases.name as my_work_phase_name',
                'my_work_task_phases.short_name as my_work_phase_short_name',
                'my_work_assignees.name as my_work_assignee_name',
                'my_work_assignees.profile_image_path as my_work_assignee_profile_image_path',
                'my_work_task_flags.name as task_flag_name',
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
                'stage' => (string) ($job->getAttribute('my_work_phase_name') ?: $job->getAttribute('my_work_phase_short_name') ?: 'No phase'),
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
    public function metrics(User $user, bool $fresh = false): array
    {
        // This method used to build the aggregate from the full Eloquent
        // visibility query (nested whereHas clauses) in a second wire:init
        // request. On a small PHP-FPM pool that request could stay busy long
        // enough to make the rest of FlowTrack feel blocked. My Work has a
        // simpler, documented scope, so calculate the counters directly from
        // indexed task/job/client columns instead.
        $access = app(AccessControlService::class);
        $administrator = $access->isAdministrator($user);

        if (!$administrator) {
            $scopes = $access->scopes($user, 'tasks');
            $usableScopes = array_values(array_diff($scopes, ['none']));
            if (!$access->can($user, 'tasks', 'view')
                || $usableScopes === []
                || ($usableScopes === ['department'] && !$user->department_id)) {
                return $this->emptyMetrics();
            }
        }

        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->copy()->addDay()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();

        // The Mentions chip means: tasks whose COMMENT contains at least one
        // valid @mention of any active FlowTrack user. Do not use the current
        // user's notification rows here: those answer "who mentioned me?", and
        // can make the list disagree with the task comment itself. TaskService
        // records the parsed mention_user_ids on the task.comment activity, so
        // this remains exact, comment-only, user-agnostic and index-friendly.
        $taskMentions = DB::table('activities')
            ->selectRaw('subject_id as flow_task_id')
            ->where('subject_type', Task::class)
            ->where('event', 'task.comment')
            ->whereNotNull('meta')
            // Never inspect native JSON with a raw LIKE pattern. MySQL normalizes
            // JSON with whitespace (for example "mention_user_ids": [1]), so the
            // old compact-string check returned zero mentions in production even
            // though the exact task.comment activity contained parsed mention IDs.
            ->whereJsonLength('meta->mention_user_ids', '>', 0)
            ->groupBy('subject_id');

        $query = DB::table('tasks')
            ->join('flow_jobs as my_work_metric_jobs', 'my_work_metric_jobs.id', '=', 'tasks.flow_job_id')
            ->join('clients as my_work_metric_clients', 'my_work_metric_clients.id', '=', 'my_work_metric_jobs.client_id')
            ->leftJoinSub($taskMentions, 'my_work_metric_task_mentions', fn ($join) => $join->on('my_work_metric_task_mentions.flow_task_id', '=', 'tasks.id'))
            ->whereNull('tasks.deleted_at')
            ->whereNull('tasks.completed_at')
            ->whereRaw("LOWER(TRIM(tasks.status)) != 'completed'")
            ->whereNull('my_work_metric_jobs.deleted_at')
            ->whereNull('my_work_metric_jobs.completed_at')
            ->whereRaw("LOWER(TRIM(my_work_metric_jobs.status)) != 'completed'")
            ->whereNotIn('my_work_metric_jobs.status', JobService::INACTIVE_STATUSES)
            ->where('my_work_metric_clients.is_active', true);

        // Normal-user My Work is intentionally assignee-strict regardless of
        // the broader task module scope. Administrators see all visible open
        // tasks in active Jobs. This matches personalTaskQuery().
        if (!$administrator) {
            $query->where('tasks.assignee_id', $user->id);
        }

        $row = $query
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
            ->selectRaw('SUM(CASE WHEN my_work_metric_task_mentions.flow_task_id IS NOT NULL THEN 1 ELSE 0 END) AS mentions_count')
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

    private function emptyMetrics(): array
    {
        return [
            'attention' => 0,
            'overdue' => 0,
            'today' => 0,
            'upcoming' => 0,
            'waiting' => 0,
            'mentions' => 0,
        ];
    }


    /** @return list<string> */
    public function orderPhaseOptions(): array
    {
        return DB::table('workflow_phases as my_work_filter_phases')
            ->join('workflow_templates as my_work_filter_workflows', 'my_work_filter_workflows.id', '=', 'my_work_filter_phases.workflow_template_id')
            ->where('my_work_filter_workflows.applies_to', 'orders')
            ->where('my_work_filter_workflows.is_active', true)
            ->where('my_work_filter_phases.is_active', true)
            ->whereNotNull('my_work_filter_phases.name')
            ->where('my_work_filter_phases.name', '!=', '')
            ->orderBy('my_work_filter_phases.sequence')
            ->orderBy('my_work_filter_phases.name')
            ->get(['my_work_filter_phases.name', 'my_work_filter_phases.sequence'])
            ->map(fn ($phase) => trim((string) $phase->name))
            ->filter()
            ->unique(fn ($name) => mb_strtolower($name))
            ->values()
            ->all();
    }

    /** @return list<int> */
    private function orderPhaseSourceIdsForName(string $phaseName): array
    {
        $normalizedPhase = mb_strtolower(trim($phaseName));
        if ($normalizedPhase === '') return [];

        return DB::table('workflow_phases as my_work_source_phases')
            ->join('workflow_templates as my_work_source_workflows', 'my_work_source_workflows.id', '=', 'my_work_source_phases.workflow_template_id')
            ->where('my_work_source_workflows.applies_to', 'orders')
            ->where('my_work_source_workflows.is_active', true)
            ->where('my_work_source_phases.is_active', true)
            ->whereRaw('LOWER(TRIM(my_work_source_phases.name)) = ?', [$normalizedPhase])
            ->selectRaw('COALESCE(my_work_source_phases.source_workflow_phase_id, my_work_source_phases.id) AS source_phase_id')
            ->pluck('source_phase_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
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

    private function personalTaskQuery(
        User $user,
        array $filters,
        bool $includeOpenConstraint = true,
        bool $includeCompletedJobs = false,
    ): Builder
    {
        $access = app(AccessControlService::class);
        $query = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', function (Builder $job) use ($includeCompletedJobs): void {
                $job
                    ->whereHas('client', fn (Builder $client) => $client->where('is_active', true))
                    ->whereNotIn('status', JobService::INACTIVE_STATUSES);

                if (!$includeCompletedJobs) {
                    $job
                        ->whereNull('completed_at')
                        ->whereRaw("LOWER(TRIM(flow_jobs.status)) != 'completed'");
                }
            });

        // My Work scope is intentionally role-sensitive:
        // - administrators: all visible tasks belonging to active Jobs
        // - normal users: only tasks explicitly assigned to that user
        // Mentions remain a filter inside that allowed scope; they never make
        // an unassigned task appear in a normal user's My Work list.
        if (!$access->isAdministrator($user)) {
            $query->where('tasks.assignee_id', $user->id);
        }

        if ($includeOpenConstraint) {
            $query->whereNull('tasks.completed_at')
                ->whereRaw("LOWER(TRIM(tasks.status)) != 'completed'");
        }

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '' && $this->searchIsUsable($search)) {
            $like = '%'.$search.'%';
            $prefix = $search.'%';
            $looksLikeReference = preg_match('/^(JOB|TSK|TASK|ORD)[-0-9]/i', $search) === 1;
            $referencePrefixOnly = mb_strlen($search) < 3 && $this->looksLikeReferencePrefix($search);

            if ($referencePrefixOnly) {
                // A recognised two-character reference prefix is useful, but
                // it must never fan out into title/client/assignee contains
                // searches. Keep this tiny-input path index-friendly.
                $query->where(function (Builder $inner) use ($prefix) {
                    $inner->whereLike('tasks.task_number', $prefix)
                        ->orWhereHas('job', fn (Builder $job) => $job
                            ->whereLike('job_number', $prefix)
                            ->orWhereLike('order_number', $prefix));
                });
            } else {
                $query->where(function (Builder $inner) use ($like, $prefix, $looksLikeReference) {
                    $inner->whereLike('tasks.task_number', $looksLikeReference ? $prefix : $like)
                        ->orWhereLike('tasks.title', $like)
                        ->orWhereLike('tasks.attention_reason', $like)
                        ->orWhereHas('attentionFlag', fn (Builder $flag) => $flag->whereLike('name', $like))
                        ->orWhereHas('assignee', fn (Builder $assignee) => $assignee->whereLike('name', $like))
                        ->orWhereHas('job', fn (Builder $job) => $job
                            ->whereLike('job_number', $looksLikeReference ? $prefix : $like)
                            ->orWhereLike('order_number', $looksLikeReference ? $prefix : $like)
                            ->orWhereLike('title', $like)
                            ->orWhereHas('client', fn (Builder $client) => $client->whereLike('name', $like)));
                });
            }
        }

        $phase = trim((string) ($filters['phase'] ?? ''));
        if ($phase !== '') {
            $normalizedPhase = mb_strtolower($phase);
            $sourcePhaseIds = $this->orderPhaseSourceIdsForName($phase);

            $query->whereHas('phase', function (Builder $phaseQuery) use ($normalizedPhase, $sourcePhaseIds): void {
                $phaseQuery->where(function (Builder $phaseMatch) use ($normalizedPhase, $sourcePhaseIds): void {
                    $phaseMatch->whereRaw('LOWER(TRIM(workflow_phases.name)) = ?', [$normalizedPhase]);

                    if ($sourcePhaseIds !== []) {
                        $phaseMatch
                            ->orWhereIn('workflow_phases.source_workflow_phase_id', $sourcePhaseIds)
                            ->orWhereIn('workflow_phases.id', $sourcePhaseIds);
                    }
                });
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
        // Correlate the task directly to its own task.comment activity. The
        // activity metadata is written from MentionService::userIdsFromText(),
        // so a plain email/@ character does not qualify and a mention on one
        // task can never make sibling tasks from the same Order appear.
        return fn ($activity) => $activity
            ->selectRaw('1')
            ->from('activities as my_work_mention_activity')
            ->whereColumn('my_work_mention_activity.subject_id', 'tasks.id')
            ->where('my_work_mention_activity.subject_type', Task::class)
            ->where('my_work_mention_activity.event', 'task.comment')
            ->whereNotNull('my_work_mention_activity.meta')
            // Use the JSON array itself instead of relying on its serialized text.
            // This works consistently on MySQL and SQLite and keeps this filter
            // tied to the exact task.comment that contains a real parsed mention.
            ->whereJsonLength('my_work_mention_activity.meta->mention_user_ids', '>', 0);
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

    private function presentTask(Task $task, User $user, AccessControlService $access, string $displayTimezone, string $today): array
    {
        $dueDate = $task->due_date?->format('Y-m-d');
        $completed = $task->completed_at !== null || \App\Support\BoardLaneResolver::isCompleted((string) $task->status);
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
        $master = app(MasterDataService::class);
        $statusColor = $master->colorFor('task_status', (string) $task->status);
        $flagColor = (!$completed && $task->needs_attention) ? $master->colorFor('task_flag', $flag) : null;

        return [
            'id' => (int) $task->id,
            'number' => (string) $task->task_number,
            'title' => (string) $task->title,
            'phase' => (string) ($task->getAttribute('my_work_phase_name') ?: $task->getAttribute('my_work_phase_short_name') ?: 'No phase'),
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
            'statusColor' => $statusColor,
            'flag' => $flag,
            'flagTone' => $this->tone($flag),
            'flagColor' => $flagColor,
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

    public function searchIsUsable(string $search): bool
    {
        $search = trim($search);
        $length = mb_strlen($search);

        if ($length >= 3) return true;
        if ($length < 2) return false;

        // Permit known two-character reference prefixes without permitting
        // arbitrary two-character global contains searches.
        return $this->looksLikeReferencePrefix($search);
    }

    private function looksLikeReferencePrefix(string $search): bool
    {
        return preg_match('/^(JO|TS|TA|OR)$/i', trim($search)) === 1;
    }
}
