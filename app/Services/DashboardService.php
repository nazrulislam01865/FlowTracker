<?php

namespace App\Services;

use App\Models\FlowNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function forget(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        Cache::forget('flowtrack:dashboard:metrics:user:'.$userId);
    }

    /**
     * Backwards-compatible aggregate used by tests and non-Livewire callers.
     * The Dashboard Livewire component now requests these sections independently.
     */
    public function data(User $user): array
    {
        return [
            'metrics' => $this->metrics($user),
            'attentionJobs' => $this->attentionJobs($user),
            'phaseCounts' => $this->phaseCounts($user),
            'workload' => $this->workload($user),
            'deliveries' => $this->deliveries($user),
            'activity' => $this->activity($user),
        ];
    }

    public function metrics(User $user): array
    {
        return Cache::remember('flowtrack:dashboard:metrics:user:'.$user->id, now()->addSeconds(20), function () use ($user) {
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
        return app(JobService::class)->activeQuery($user)
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
            ->get();
    }

    public function phaseCounts(User $user)
    {
        return app(JobService::class)->activeQuery($user)
            ->reorder()
            ->selectRaw('workflow_phase_id, count(*) total')
            ->groupBy('workflow_phase_id')
            ->with('phase:id,name,short_name,sequence')
            ->get();
    }

    public function workload(User $user)
    {
        $access = app(AccessControlService::class);
        $query = User::query()->where('is_active', true);

        if (!$access->isAdministrator($user) && $access->scope($user, 'tasks') !== 'all_records') {
            $query->whereKey($user->id);
        }

        return $query
            ->select(['users.id', 'users.name'])
            ->withCount(['assignedTasks as open_tasks_count' => fn ($tasks) => $tasks
                ->whereNull('completed_at')
                ->whereHas('job', fn ($job) => $job->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES))])
            ->orderByDesc('open_tasks_count')
            ->limit(5)
            ->get();
    }

    public function deliveries(User $user)
    {
        return app(JobService::class)->activeQuery($user)
            ->select(['flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.client_id', 'flow_jobs.title', 'flow_jobs.delivery_date'])
            ->with('client:id,name')
            ->whereDate('delivery_date', '>=', today())
            ->orderBy('delivery_date')
            ->limit(6)
            ->get();
    }

    public function activity(User $user)
    {
        return FlowNotification::query()
            ->select(['id', 'user_id', 'flow_job_id', 'title', 'message', 'created_at'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
    }
}
