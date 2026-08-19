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

    private const INITIAL_TASK_STATUSES = ['not started', 'not start', 'ready', 'to do', 'todo'];

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
        $statusFilter = trim((string) ($filters['status'] ?? ''));
        $showCompleted = !$hideCompleted && ($statusFilter !== '' || in_array($quick, ['all', 'createdToday', 'completedThisWeek'], true));

        // Most My Task views are open-task-only. Created Today intentionally
        // includes every task created today regardless of status, while Completed
        // This Week must include completed tasks (and completed Orders) by design.
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
                'flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.title', 'flow_jobs.client_id', 'flow_jobs.workflow_phase_id', 'flow_jobs.created_by',
                'flow_jobs.health', 'flow_jobs.progress', 'flow_jobs.status', 'flow_jobs.updated_at',
                'my_work_clients.name as my_work_client_name',
                'my_work_job_phases.name as my_work_phase_name',
                'my_work_job_phases.short_name as my_work_phase_short_name',
                'my_work_job_phases.color as my_work_phase_color',
            ])
            ->get()
            ->keyBy('id');

        $tasks = (clone $baseTasks)
            ->whereIn('tasks.flow_job_id', $jobIds)
            ->leftJoin('workflow_phases as my_work_task_phases', 'my_work_task_phases.id', '=', 'tasks.workflow_phase_id')
            ->leftJoin('users as my_work_assignees', 'my_work_assignees.id', '=', 'tasks.assignee_id')
            ->leftJoin('master_records as my_work_task_flags', 'my_work_task_flags.id', '=', 'tasks.order_task_flag_id')
            ->select([
                'tasks.id', 'tasks.task_number', 'tasks.flow_job_id', 'tasks.workflow_phase_id',
                'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.priority',
                'tasks.due_date', 'tasks.needs_attention', 'tasks.order_task_flag_id', 'tasks.attention_reason',
                'tasks.completed_at', 'tasks.updated_at',
                'my_work_task_phases.name as my_work_phase_name',
                'my_work_task_phases.short_name as my_work_phase_short_name',
                'my_work_task_phases.color as my_work_phase_color',
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

            $parentCreatedByUser = (int) ($job->created_by ?: 0) === (int) $user->id;
            $taskRows = $tasksByJob->get($jobId, collect())
                ->map(fn (Task $task) => $this->presentTask($task, $user, $access, $displayTimezone, $today, $parentCreatedByUser))
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
     * Keep My Task counters aligned with the reusable Inquiry/Order summary-card
     * language while counting task records inside the user's personal task scope.
     * One aggregate query keeps this page fast even with a large task history.
     */
    public function metrics(User $user, bool $fresh = false): array
    {
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

        $workspace = app(WorkspaceSettingsService::class);
        $today = $workspace->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->addDay()->toDateString();
        $weekStartDate = $today->startOfWeek()->toDateString();
        $weekEndDate = $today->endOfWeek()->toDateString();
        $todayStartUtc = $today->utc();
        $todayEndUtc = $today->endOfDay()->utc();
        [$weekStartUtc, $weekEndUtc] = $workspace->localWeekUtcBounds();

        $taskMentions = DB::table('activities')
            ->selectRaw('subject_id as flow_task_id')
            ->where('subject_type', Task::class)
            ->where('event', 'task.comment')
            ->whereNotNull('meta')
            ->whereJsonLength('meta->mention_user_ids', '>', 0)
            ->groupBy('subject_id');

        // Do not globally remove completed tasks here: Created Today is explicitly
        // status-agnostic and Completed This Week needs task history. Open-task
        // conditions are applied inside the aggregate expressions instead.
        $query = DB::table('tasks')
            ->join('flow_jobs as my_work_metric_jobs', 'my_work_metric_jobs.id', '=', 'tasks.flow_job_id')
            ->join('clients as my_work_metric_clients', 'my_work_metric_clients.id', '=', 'my_work_metric_jobs.client_id')
            ->leftJoinSub($taskMentions, 'my_work_metric_task_mentions', fn ($join) => $join->on('my_work_metric_task_mentions.flow_task_id', '=', 'tasks.id'))
            ->whereNull('tasks.deleted_at')
            ->whereNull('my_work_metric_jobs.deleted_at')
            ->whereNotIn('my_work_metric_jobs.status', JobService::INACTIVE_STATUSES)
            ->where('my_work_metric_clients.is_active', true);

        // Match the same operational responsibility used by personalTaskQuery().
        if (!$administrator) {
            $scopes = $access->scopes($user, 'tasks');
            $canSeeAssigned = $access->can($user, 'tasks', 'view') && (
                in_array('all_records', $scopes, true)
                || in_array('own_records', $scopes, true)
                || in_array('assigned_jobs', $scopes, true)
                || (in_array('department', $scopes, true) && (bool) $user->department_id)
            );

            $query->where(function ($personal) use ($user, $canSeeAssigned): void {
                $personal->where('my_work_metric_jobs.created_by', $user->id);
                if ($canSeeAssigned) {
                    $personal->orWhere('tasks.assignee_id', $user->id);
                }
            });
        }

        $initialStatuses = "'".implode("','", self::INITIAL_TASK_STATUSES)."'";
        $openTask = "tasks.completed_at IS NULL
            AND LOWER(TRIM(COALESCE(tasks.status, ''))) <> 'completed'
            AND my_work_metric_jobs.completed_at IS NULL
            AND LOWER(TRIM(COALESCE(my_work_metric_jobs.status, ''))) <> 'completed'";
        $notStarted = "COALESCE(tasks.progress, 0) = 0
            AND LOWER(TRIM(COALESCE(tasks.status, ''))) IN ($initialStatuses)";
        $inProgress = "(COALESCE(tasks.progress, 0) > 0
            OR (LOWER(TRIM(COALESCE(tasks.status, ''))) <> ''
                AND LOWER(TRIM(COALESCE(tasks.status, ''))) NOT IN ($initialStatuses)))";
        $needsAttention = "(tasks.needs_attention = 1
            OR tasks.order_task_flag_id IS NOT NULL
            OR tasks.assignee_id IS NULL
            OR tasks.due_date < ?
            OR LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%blocked%'
            OR LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%revision%'
            OR LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%overdue%'
            OR LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%delayed%'
            OR LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%attention%')";

        $row = $query
            ->selectRaw("SUM(CASE WHEN $openTask THEN 1 ELSE 0 END) AS my_tasks_count")
            ->selectRaw('SUM(CASE WHEN tasks.created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS created_today_count', [$todayStartUtc, $todayEndUtc])
            ->selectRaw("SUM(CASE WHEN $openTask AND $notStarted THEN 1 ELSE 0 END) AS not_started_count")
            ->selectRaw("SUM(CASE WHEN $openTask AND $inProgress THEN 1 ELSE 0 END) AS in_progress_count")
            ->selectRaw("SUM(CASE WHEN $openTask AND tasks.due_date BETWEEN ? AND ? THEN 1 ELSE 0 END) AS due_this_week_count", [$weekStartDate, $weekEndDate])
            ->selectRaw('SUM(CASE WHEN tasks.completed_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS completed_this_week_count', [$weekStartUtc, $weekEndUtc])
            ->selectRaw("SUM(CASE WHEN $openTask AND $needsAttention THEN 1 ELSE 0 END) AS attention_count", [$todayDate])
            // Legacy counts remain available for old bookmarked quick-filter URLs
            // and the Mentions chip; they are no longer displayed as summary cards.
            ->selectRaw("SUM(CASE WHEN $openTask AND tasks.due_date < ? THEN 1 ELSE 0 END) AS overdue_count", [$todayDate])
            ->selectRaw("SUM(CASE WHEN $openTask AND tasks.due_date = ? THEN 1 ELSE 0 END) AS today_count", [$todayDate])
            ->selectRaw("SUM(CASE WHEN $openTask AND tasks.due_date BETWEEN ? AND ? AND LOWER(TRIM(tasks.status)) NOT LIKE 'waiting%' THEN 1 ELSE 0 END) AS upcoming_count", [$tomorrow, $weekEndDate])
            ->selectRaw("SUM(CASE WHEN $openTask AND LOWER(TRIM(tasks.status)) LIKE 'waiting%' THEN 1 ELSE 0 END) AS waiting_count")
            ->selectRaw("SUM(CASE WHEN $openTask AND my_work_metric_task_mentions.flow_task_id IS NOT NULL THEN 1 ELSE 0 END) AS mentions_count")
            ->first();

        return [
            'my_tasks' => (int) ($row?->my_tasks_count ?? 0),
            'createdToday' => (int) ($row?->created_today_count ?? 0),
            'notStarted' => (int) ($row?->not_started_count ?? 0),
            'inProgress' => (int) ($row?->in_progress_count ?? 0),
            'dueThisWeek' => (int) ($row?->due_this_week_count ?? 0),
            'completedThisWeek' => (int) ($row?->completed_this_week_count ?? 0),
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
            'my_tasks' => 0,
            'createdToday' => 0,
            'notStarted' => 0,
            'inProgress' => 0,
            'dueThisWeek' => 0,
            'completedThisWeek' => 0,
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
    public function orderPhaseSourceIdsForName(string $phaseName): array
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
            ->active('order_task_status')
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
     * Count the same open task scope used by My Tasks. Administrators see all
     * visible tasks; other users see tasks assigned to them plus tasks from
     * Orders they created. Keeping this in one service prevents the sidebar
     * badge and page results from drifting apart.
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

        // Keep administrator visibility broad. For non-admin users, My Tasks is
        // intentionally limited to their own operational responsibility: tasks
        // assigned to them or any task under an Order they created. Because the
        // base query comes from TaskService::visibleQuery(), the role matrix and
        // record-scope rules still remain authoritative.
        if (!$access->isAdministrator($user)) {
            $query->where(function (Builder $personal) use ($user): void {
                $personal
                    ->where('tasks.assignee_id', $user->id)
                    ->orWhereHas('job', fn (Builder $job) => $job->where('created_by', $user->id));
            });
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

        $statusFilter = trim((string) ($filters['status'] ?? ''));
        if ($statusFilter !== '') {
            $query->whereRaw('LOWER(TRIM(tasks.status)) = ?', [mb_strtolower($statusFilter)]);
        }

        $this->applyQuickFilter($query, $user, (string) ($filters['quick'] ?? 'all'));

        return $query;
    }

    private function applyQuickFilter(Builder $query, User $user, string $quick): void
    {
        $workspace = app(WorkspaceSettingsService::class);
        $today = $workspace->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->addDay()->toDateString();
        $weekStartDate = $today->startOfWeek()->toDateString();
        $weekEndDate = $today->endOfWeek()->toDateString();
        [$weekStartUtc, $weekEndUtc] = $workspace->localWeekUtcBounds();
        $initialStatuses = self::INITIAL_TASK_STATUSES;
        $initialStatusPlaceholders = implode(',', array_fill(0, count($initialStatuses), '?'));

        match ($quick) {
            'createdToday' => $query->whereBetween('tasks.created_at', [
                $today->utc(),
                $today->endOfDay()->utc(),
            ]),
            'notStarted' => $query
                ->whereRaw('COALESCE(tasks.progress, 0) = 0')
                ->whereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) IN ($initialStatusPlaceholders)", $initialStatuses),
            'inProgress' => $query->where(function (Builder $started) use ($initialStatuses, $initialStatusPlaceholders): void {
                $started->where('tasks.progress', '>', 0)
                    ->orWhere(function (Builder $status) use ($initialStatuses, $initialStatusPlaceholders): void {
                        $status->whereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) <> ''")
                            ->whereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) NOT IN ($initialStatusPlaceholders)", $initialStatuses);
                    });
            }),
            'dueThisWeek' => $query->whereBetween('tasks.due_date', [$weekStartDate, $weekEndDate]),
            'completedThisWeek' => $query
                ->whereNotNull('tasks.completed_at')
                ->whereBetween('tasks.completed_at', [$weekStartUtc, $weekEndUtc]),
            'attention' => $query->where(function (Builder $attention) use ($todayDate): void {
                $attention->where('tasks.needs_attention', true)
                    ->orWhereNotNull('tasks.order_task_flag_id')
                    ->orWhereNull('tasks.assignee_id')
                    ->orWhereDate('tasks.due_date', '<', $todayDate)
                    ->orWhereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%blocked%'")
                    ->orWhereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%revision%'")
                    ->orWhereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%overdue%'")
                    ->orWhereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%delayed%'")
                    ->orWhereRaw("LOWER(TRIM(COALESCE(tasks.status, ''))) LIKE '%attention%'");
            }),
            // Preserve old URLs/chips that may still exist in bookmarks.
            'overdue' => $query->where('tasks.due_date', '<', $todayDate),
            'today' => $query->where('tasks.due_date', $todayDate),
            'upcoming' => $query
                ->whereBetween('tasks.due_date', [$tomorrow, $weekEndDate])
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

    private function presentTask(
        Task $task,
        User $user,
        AccessControlService $access,
        string $displayTimezone,
        string $today,
        bool $parentCreatedByUser = false,
    ): array
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
        if (!$completed) {
            $flag = app(TaskFlagService::class)->labelForTask($task) ?: 'No flag';
        }

        $updatedAt = $task->updated_at?->copy()->setTimezone($displayTimezone);
        $master = app(MasterDataService::class);
        $statusColor = $master->colorFor('order_task_status', (string) $task->status);
        $flagColor = (!$completed && $flag !== 'No flag') ? $master->colorFor('order_task_flag', $flag) : null;

        return [
            'id' => (int) $task->id,
            'number' => (string) $task->task_number,
            'title' => (string) $task->title,
            'phase' => (string) ($task->getAttribute('my_work_phase_short_name') ?: $task->getAttribute('my_work_phase_name') ?: 'No phase'),
            'phaseColor' => $task->getAttribute('my_work_phase_color'),
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
            // Tasks on this page already came through TaskService::visibleQuery(),
            // so assignment authorization can be decided without another scope
            // query for every rendered row.
            'canAssign' => $access->isAdministrator($user)
                || $parentCreatedByUser
                || $access->can($user, 'tasks', 'assign'),
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
