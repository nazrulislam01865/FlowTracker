<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\FlowNotification;
use App\Models\Task;
use App\Models\User;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const CACHE_VERSION = 'v5-safe-values';

    private const SECTIONS = [
        'summary',
        'mentions',
        'mention-count',
        'health',
        'assignees',
        'attention-tasks',
        'ongoing-jobs',
        'ongoing-tasks',
        'activity',
        'clients',
    ];

    public function primaryData(User $user): array
    {
        return [
            'metrics' => $this->summary($user),
            'operationalHealth' => $this->operationalHealth($user),
        ];
    }

    public function secondaryData(User $user): array
    {
        return [
            'assigneePerformance' => $this->assigneePerformance($user),
            'attentionTasks' => $this->attentionTasks($user),
            'ongoingJobs' => $this->ongoingJobs($user),
            'ongoingTasks' => $this->ongoingTasks($user),
            'recentActivity' => $this->recentActivity($user),
            'clientPortfolio' => $this->clientPortfolio($user),
        ];
    }


    /** Backwards-compatible aggregate for reports/tests and non-Livewire callers. */
    public function data(User $user): array
    {
        return $this->primaryData($user) + $this->secondaryData($user);
    }

    public function metrics(User $user): array
    {
        return $this->summary($user);
    }

    public function attentionJobs(User $user): Collection
    {
        return app(JobService::class)->activeQuery($user)
            ->select(['flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.client_id', 'flow_jobs.workflow_phase_id', 'flow_jobs.title', 'flow_jobs.health', 'flow_jobs.needs_attention'])
            ->with(['client:id,name', 'phase:id,short_name'])
            ->where(fn ($query) => $query->where('flow_jobs.needs_attention', true)->orWhereIn('flow_jobs.health', ['At Risk', 'Delayed', 'Blocked', 'Needs Attention']))
            ->latest('flow_jobs.id')
            ->limit(6)
            ->get();
    }

    public function forget(User|int $user): void
    {
        $userId = $user instanceof User ? (int) $user->id : (int) $user;
        foreach (self::SECTIONS as $section) {
            Cache::forget($this->cacheKey($section, $userId));
        }
    }

    public function forgetMentions(User|int $user): void
    {
        $userId = $user instanceof User ? (int) $user->id : (int) $user;
        Cache::forget($this->cacheKey('mentions', $userId));
        Cache::forget($this->cacheKey('mention-count', $userId));
        Cache::forget($this->cacheKey('summary', $userId));
    }

    public function summary(User $user): array
    {
        return $this->remember($user, 'summary', function () use ($user): array {
            $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
            $jobs = app(JobService::class)->activeQuery($user)->reorder();
            $tasks = $this->activeTaskQuery($user)->reorder();

            $jobRow = (clone $jobs)
                ->selectRaw('count(*) as active_jobs')
                ->selectRaw("sum(case when flow_jobs.needs_attention = 1 or flow_jobs.health in ('At Risk','Delayed','Blocked','Needs Attention') then 1 else 0 end) as attention_jobs")
                ->selectRaw("sum(case when exists (select 1 from workflow_phases where workflow_phases.id = flow_jobs.workflow_phase_id and (lower(workflow_phases.name) like '%ship%' or lower(workflow_phases.short_name) like '%ship%')) then 1 else 0 end) as shipping_jobs")
                ->first();

            $taskRow = (clone $tasks)
                ->selectRaw('sum(case when tasks.due_date < ? then 1 else 0 end) as overdue_tasks', [$today])
                ->first();

            $activeClients = app(ClientService::class)->visibleQuery($user)
                ->where('clients.is_active', true)
                ->count();

            return [
                'activeJobs' => (int) ($jobRow?->active_jobs ?? 0),
                'needsAttention' => (int) ($jobRow?->attention_jobs ?? 0),
                'overdueTasks' => (int) ($taskRow?->overdue_tasks ?? 0),
                'activeClients' => (int) $activeClients,
                // The current application has no Inquiry entity/table. Keep the
                // prototype metric present without inventing records.
                'openInquiries' => 0,
                'taggedComments' => $this->unreadMentionCount($user),
                'shipping' => (int) ($jobRow?->shipping_jobs ?? 0),
            ];
        });
    }

    public function mentions(User $user): Collection
    {
        // Do not cache Eloquent model collections. Database/file cache stores
        // serialize objects, which can become __PHP_Incomplete_Class after a
        // deployment or class-map change. This query is intentionally bounded
        // and runs only inside the isolated TaggedComments component.
        return FlowNotification::query()
            ->select(['id', 'user_id', 'flow_job_id', 'flow_task_id', 'type', 'title', 'message', 'read_at', 'created_at'])
            ->where('user_id', $user->id)
            ->where('type', 'mention')
            ->with([
                'job:id,job_number,title,client_id',
                'job.client:id,name',
                'task:id,task_number,title,flow_job_id',
            ])
            ->latest('id')
            ->limit(12)
            ->get();
    }

    public function unreadMentionCount(User $user): int
    {
        return (int) $this->remember($user, 'mention-count', fn () => FlowNotification::query()
            ->where('user_id', $user->id)
            ->where('type', 'mention')
            ->whereNull('read_at')
            ->count());
    }

    public function operationalHealth(User $user): array
    {
        return $this->remember($user, 'health', function () use ($user): array {
            $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
            $jobs = app(JobService::class)->activeQuery($user)->reorder();
            $tasks = $this->activeTaskQuery($user)->reorder();

            $jobRow = (clone $jobs)
                ->selectRaw('count(*) as total')
                ->selectRaw("sum(case when flow_jobs.health in ('On Track','Healthy') then 1 else 0 end) as healthy")
                ->selectRaw("sum(case when flow_jobs.health in ('At Risk','Needs Attention') then 1 else 0 end) as watch_count")
                ->selectRaw("sum(case when flow_jobs.health in ('Delayed','Blocked') then 1 else 0 end) as at_risk")
                ->first();

            $flagRow = (clone $tasks)
                ->selectRaw('sum(case when tasks.due_date < ? then 1 else 0 end) as overdue', [$today])
                ->selectRaw("sum(case when tasks.status = 'Waiting for Client' then 1 else 0 end) as waiting_client")
                ->selectRaw("sum(case when tasks.status = 'Revision Required' then 1 else 0 end) as revision_required")
                ->selectRaw("sum(case when tasks.status = 'Blocked' then 1 else 0 end) as blocked")
                ->selectRaw('sum(case when tasks.assignee_id is null then 1 else 0 end) as unassigned')
                ->first();

            $flags = [
                ['key' => 'overdue', 'label' => 'Overdue', 'count' => (int) ($flagRow?->overdue ?? 0), 'tone' => 'red'],
                ['key' => 'waiting-client', 'label' => 'Waiting for Client', 'count' => (int) ($flagRow?->waiting_client ?? 0), 'tone' => 'amber'],
                ['key' => 'revision', 'label' => 'Revision Required', 'count' => (int) ($flagRow?->revision_required ?? 0), 'tone' => 'purple'],
                ['key' => 'blocked', 'label' => 'Blocked', 'count' => (int) ($flagRow?->blocked ?? 0), 'tone' => 'blue'],
                ['key' => 'unassigned', 'label' => 'Unassigned', 'count' => (int) ($flagRow?->unassigned ?? 0), 'tone' => 'gray'],
            ];
            $maxFlag = max(1, ...array_column($flags, 'count'));
            foreach ($flags as &$flag) {
                $flag['width'] = $flag['count'] > 0 ? max(12, (int) round(($flag['count'] / $maxFlag) * 100)) : 0;
            }
            unset($flag);

            $total = (int) ($jobRow?->total ?? 0);
            $healthy = (int) ($jobRow?->healthy ?? 0);
            $watch = (int) ($jobRow?->watch_count ?? 0);
            $atRisk = max(0, $total - $healthy - $watch);
            $healthyPct = $total > 0 ? (int) round(($healthy / $total) * 100) : 0;
            $watchPct = $total > 0 ? (int) round(($watch / $total) * 100) : 0;
            $riskStart = min(100, $healthyPct + $watchPct);

            return [
                'totalJobs' => $total,
                'healthy' => $healthy,
                'watch' => $watch,
                'atRisk' => $atRisk,
                'healthyPct' => $healthyPct,
                'watchPct' => $watchPct,
                'riskStart' => $riskStart,
                'flags' => $flags,
                'flaggedTotal' => array_sum(array_column($flags, 'count')),
            ];
        });
    }

    public function assigneePerformance(User $user): Collection
    {
        $access = app(AccessControlService::class);
        $query = User::query()->where('is_active', true);

        if (!$access->isAdministrator($user) && $access->scope($user, 'tasks') !== 'all_records') {
            $query->whereKey($user->id);
        }

        return $query
            ->select(['users.id', 'users.name', 'users.profile_image_path'])
            ->withCount([
                'assignedTasks as ongoing_count' => fn ($tasks) => $tasks
                    ->whereNull('completed_at')
                    ->whereHas('job', fn ($job) => $job->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES)),
                'assignedTasks as done_count' => fn ($tasks) => $tasks->whereNotNull('completed_at'),
                'assignedTasks as done_on_time_count' => fn ($tasks) => $tasks
                    ->whereNotNull('completed_at')
                    ->whereNotNull('due_date')
                    ->whereColumn('completed_at', '<=', 'due_date'),
            ])
            ->orderByDesc('ongoing_count')
            ->orderBy('name')
            ->limit(4)
            ->get();
    }

    public function attentionTasks(User $user): Collection
    {
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        return $this->activeTaskQuery($user)
            ->select(['tasks.id', 'tasks.task_number', 'tasks.flow_job_id', 'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.due_date', 'tasks.needs_attention', 'tasks.attention_reason'])
            ->with([
                'job:id,job_number,title,client_id',
                'job.client:id,name',
                'assignee:id,name,profile_image_path',
            ])
            ->where(function ($query) use ($today) {
                $query->where('tasks.needs_attention', true)
                    ->orWhere('tasks.due_date', '<', $today)
                    ->orWhereIn('tasks.status', ['Blocked', 'Waiting for Client', 'Waiting for Internal Approval', 'Revision Required']);
            })
            ->orderByRaw('case when tasks.due_date is not null and tasks.due_date < ? then 0 when tasks.needs_attention = 1 then 1 else 2 end', [$today])
            ->orderByRaw('tasks.due_date is null, tasks.due_date asc')
            ->limit(3)
            ->get();
    }

    public function ongoingJobs(User $user): Collection
    {
        return app(JobService::class)->activeQuery($user)
            ->select(['flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.client_id', 'flow_jobs.workflow_phase_id', 'flow_jobs.title', 'flow_jobs.health', 'flow_jobs.needs_attention', 'flow_jobs.progress', 'flow_jobs.updated_at'])
            ->with(['client:id,name', 'phase:id,name,short_name'])
            ->orderByDesc('flow_jobs.updated_at')
            ->limit(4)
            ->get();
    }

    public function ongoingTasks(User $user): Collection
    {
        return $this->activeTaskQuery($user)
            ->select(['tasks.id', 'tasks.task_number', 'tasks.flow_job_id', 'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.due_date', 'tasks.needs_attention', 'tasks.attention_reason', 'tasks.updated_at'])
            ->with(['job:id,job_number,title', 'assignee:id,name,profile_image_path'])
            ->orderByRaw('tasks.due_date is null, tasks.due_date asc')
            ->orderByDesc('tasks.updated_at')
            ->limit(4)
            ->get();
    }

    public function recentActivity(User $user): Collection
    {
        return FlowNotification::query()
            ->select(['id', 'user_id', 'flow_job_id', 'flow_task_id', 'type', 'title', 'message', 'created_at'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(4)
            ->get();
    }

    public function clientPortfolio(User $user): Collection
    {
        $access = app(AccessControlService::class);
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        return app(ClientService::class)->visibleQuery($user)
            ->where('clients.is_active', true)
            ->select(['clients.id', 'clients.name'])
            ->withCount([
                'jobs as active_jobs_count' => fn ($jobs) => $access->applyJobScope(
                    $jobs->whereNull('flow_jobs.completed_at')->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES),
                    $user
                ),
                'jobs as at_risk_jobs_count' => fn ($jobs) => $access->applyJobScope(
                    $jobs->whereNull('flow_jobs.completed_at')
                        ->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES)
                        ->where(fn ($query) => $query->where('flow_jobs.needs_attention', true)->orWhereIn('flow_jobs.health', ['At Risk', 'Delayed', 'Blocked', 'Needs Attention'])),
                    $user
                ),
                'tasks as open_tasks_count' => fn ($tasks) => $access->applyTaskScope($tasks->whereNull('tasks.completed_at'), $user),
                'tasks as overdue_tasks_count' => fn ($tasks) => $access->applyTaskScope(
                    $tasks->whereNull('tasks.completed_at')->where('tasks.due_date', '<', $today),
                    $user
                ),
            ])
            ->orderByDesc('active_jobs_count')
            ->orderBy('clients.name')
            ->limit(4)
            ->get();
    }

    private function activeTaskQuery(User $user)
    {
        return app(TaskService::class)->visibleQuery($user)
            ->whereNull('tasks.completed_at')
            ->whereHas('job', fn ($job) => $job
                ->whereNull('flow_jobs.completed_at')
                ->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES)
                ->whereHas('client', fn ($client) => $client->where('clients.is_active', true)));
    }

    private function remember(User $user, string $section, Closure $resolver): mixed
    {
        $seconds = max(10, (int) config('performance.dashboard_cache_seconds', 45));
        $key = $this->cacheKey($section, (int) $user->id);

        $cached = Cache::get($key);
        if ($cached !== null && !$this->isSafeCacheValue($cached)) {
            // Self-heal any stale serialized model/collection values left by an
            // older deployment. Dashboard cache is intentionally scalar/array only.
            Cache::forget($key);
        }

        return Cache::remember(
            $key,
            now()->addSeconds($seconds),
            function () use ($resolver, $key) {
                $value = $resolver();

                if (!$this->isSafeCacheValue($value)) {
                    Cache::forget($key);
                    throw new \LogicException('Dashboard cache values must contain only arrays, scalars, or null.');
                }

                return $value;
            },
        );
    }

    private function isSafeCacheValue(mixed $value): bool
    {
        if ($value === null || is_scalar($value)) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!$this->isSafeCacheValue($item)) {
                return false;
            }
        }

        return true;
    }

    private function cacheKey(string $section, int $userId): string
    {
        return 'flowtrack:dashboard:'.self::CACHE_VERSION.':clients-'.app(ClientService::class)->lifecycleVersion().':'.$section.':user:'.$userId;
    }
}
