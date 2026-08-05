<?php

namespace App\Services;

use App\Models\FlowNotification;
use App\Models\FlowJob;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const CACHE_VERSION = 'v2';

    private const SECTIONS = [
        'metrics',
        'attention',
        'phases',
        'workload',
        'deliveries',
        'activity',
    ];

    public function forget(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        foreach (self::SECTIONS as $section) {
            Cache::forget($this->cacheKey($section, $userId));
            Cache::forget($this->legacyCacheKey($section, $userId));
        }
    }

    public function initialData(User $user): array
    {
        return [
            'metrics' => $this->metrics($user),
            'attentionJobs' => $this->attentionJobs($user),
        ];
    }

    public function secondaryData(User $user): array
    {
        return [
            'phaseCounts' => $this->phaseCounts($user),
            'workload' => $this->workload($user),
            'deliveries' => $this->deliveries($user),
            'activity' => $this->activity($user),
        ];
    }

    /** Backwards-compatible aggregate for tests and non-Livewire callers. */
    public function data(User $user): array
    {
        return $this->initialData($user) + $this->secondaryData($user);
    }

    public function metrics(User $user): array
    {
        return $this->remember($user, 'metrics', function () use ($user) {
            $jobs = app(JobService::class)->activeQuery($user)->reorder();
            $tasks = app(TaskService::class)->visibleQuery($user)
                ->whereHas('job', fn ($job) => $job->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES))
                ->reorder();

            $jobMetrics = $jobs
                ->selectRaw('count(*) as active_jobs')
                ->selectRaw("sum(case when flow_jobs.needs_attention = 1 or flow_jobs.health in ('At Risk','Delayed','Blocked','Needs Attention') or exists (select 1 from tasks where tasks.flow_job_id = flow_jobs.id and tasks.needs_attention = 1 and tasks.completed_at is null and tasks.deleted_at is null) then 1 else 0 end) as risk_jobs")
                ->selectRaw("sum(case when exists (select 1 from workflow_phases where workflow_phases.id = flow_jobs.workflow_phase_id and (lower(workflow_phases.name) like '%ship%' or lower(workflow_phases.short_name) like '%ship%')) then 1 else 0 end) as shipping_jobs")
                ->first();

            $taskMetrics = $tasks
                ->selectRaw('sum(case when tasks.completed_at is null and tasks.due_date < ? then 1 else 0 end) as overdue_tasks', [today()->format('Y-m-d')])
                ->selectRaw("sum(case when tasks.completed_at is null and tasks.status in ('Waiting for Client','Waiting for Internal Approval') then 1 else 0 end) as pending_approvals")
                ->first();

            return [
                'activeJobs' => (int) ($jobMetrics?->active_jobs ?? 0),
                'riskJobs' => (int) ($jobMetrics?->risk_jobs ?? 0),
                'overdueTasks' => (int) ($taskMetrics?->overdue_tasks ?? 0),
                'pendingApprovals' => (int) ($taskMetrics?->pending_approvals ?? 0),
                'shipping' => (int) ($jobMetrics?->shipping_jobs ?? 0),
            ];
        });
    }

    public function attentionJobs(User $user)
    {
        return $this->remember($user, 'attention', fn () =>
            app(JobService::class)->activeQuery($user)
                ->select(['flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.client_id', 'flow_jobs.workflow_phase_id', 'flow_jobs.title', 'flow_jobs.next_action', 'flow_jobs.health'])
                ->with([
                    'client:id,name',
                    'phase:id,short_name',
                    'tasks' => fn ($query) => app(AccessControlService::class)
                        ->applyTaskScope($query, $user)
                        ->select(['tasks.id', 'tasks.flow_job_id', 'tasks.title', 'tasks.needs_attention', 'tasks.created_at'])
                        ->where('needs_attention', true)
                        ->whereNull('completed_at')
                        ->latest(),
                ])
                ->where(function ($query) {
                    $query->where('needs_attention', true)
                        ->orWhereIn('health', ['At Risk', 'Delayed', 'Blocked', 'Needs Attention'])
                        ->orWhereHas('tasks', fn ($task) => $task->where('needs_attention', true)->whereNull('completed_at'));
                })
                ->latest('flow_jobs.id')
                ->limit(6)
                ->get()
        );
    }

    public function phaseCounts(User $user)
    {
        return $this->remember($user, 'phases', fn () =>
            app(JobService::class)->activeQuery($user)
                ->reorder()
                ->selectRaw('workflow_phase_id, count(*) total')
                ->groupBy('workflow_phase_id')
                ->with('phase:id,name,short_name,sequence')
                ->get()
        );
    }

    public function workload(User $user)
    {
        $access = app(AccessControlService::class);
        $query = User::query()->where('is_active', true);

        if (!$access->isAdministrator($user) && $access->scope($user, 'tasks') !== 'all_records') {
            $query->whereKey($user->id);
        }

        return $this->remember($user, 'workload', fn () =>
            $query
                ->select(['users.id', 'users.name'])
                ->withCount(['assignedTasks as open_tasks_count' => fn ($tasks) => $tasks
                    ->whereNull('completed_at')
                    ->whereHas('job', fn ($job) => $job->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES))])
                ->orderByDesc('open_tasks_count')
                ->limit(5)
                ->get()
        );
    }

    public function deliveries(User $user)
    {
        return $this->remember($user, 'deliveries', fn () =>
            app(JobService::class)->activeQuery($user)
                ->select(['flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.client_id', 'flow_jobs.title', 'flow_jobs.delivery_date'])
                ->with('client:id,name')
                ->where('delivery_date', '>=', today()->toDateString())
                ->orderBy('delivery_date')
                ->limit(6)
                ->get()
        );
    }

    public function activity(User $user)
    {
        return $this->remember($user, 'activity', fn () =>
            FlowNotification::query()
                ->select(['id', 'user_id', 'flow_job_id', 'title', 'message', 'created_at'])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get()
        );
    }

    private function remember(User $user, string $section, Closure $resolver): mixed
    {
        $seconds = max(5, (int) config('performance.dashboard_cache_seconds', 30));
        $key = $this->cacheKey($section, (int) $user->id);

        $value = Cache::remember(
            $key,
            now()->addSeconds($seconds),
            $resolver,
        );

        if ($this->isValidCachedValue($section, $value)) {
            return $value;
        }

        // A cache value may have been written by an older Dashboard version or
        // an interrupted deployment. Never allow malformed cached data to make
        // the Blade view fail; discard it and rebuild the section once.
        Cache::forget($key);
        $value = $resolver();
        Cache::put($key, $value, now()->addSeconds($seconds));

        return $value;
    }

    private function cacheKey(string $section, int $userId): string
    {
        return 'flowtrack:dashboard:'.self::CACHE_VERSION.':'.$section.':user:'.$userId;
    }

    private function legacyCacheKey(string $section, int $userId): string
    {
        return 'flowtrack:dashboard:'.$section.':user:'.$userId;
    }

    private function isValidCachedValue(string $section, mixed $value): bool
    {
        if ($section === 'metrics') {
            return is_array($value)
                && array_is_list($value) === false
                && count(array_intersect([
                    'activeJobs',
                    'riskJobs',
                    'overdueTasks',
                    'pendingApprovals',
                    'shipping',
                ], array_keys($value))) === 5;
        }

        if (!$value instanceof EloquentCollection) {
            return false;
        }

        $expectedModel = match ($section) {
            'attention', 'phases', 'deliveries' => FlowJob::class,
            'workload' => User::class,
            'activity' => FlowNotification::class,
            default => null,
        };

        return $expectedModel !== null
            && $value->every(fn ($item) => $item instanceof $expectedModel);
    }
}
