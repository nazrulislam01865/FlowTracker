<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function data(User $user): array
    {
        $access = app(AccessControlService::class);
        $jobQuery = app(JobService::class)->visibleQuery($user);
        $taskQuery = app(TaskService::class)->visibleQuery($user);

        // Only scalar dashboard metrics are cached. Eloquent collections remain
        // live so the cache never serializes model objects and attention/activity
        // rows still react immediately to Pusher/Livewire refreshes.
        $metrics = Cache::remember('flowtrack:dashboard:metrics:user:'.$user->id, now()->addSeconds(20), function () use ($user) {
            $access = app(AccessControlService::class);
            $jobs = app(JobService::class)->visibleQuery($user);
            $tasks = app(TaskService::class)->visibleQuery($user);

            $activeJobs = (clone $jobs)->whereNull('completed_at')->count();
            $riskJobs = (clone $jobs)->where(function ($q) {
                $q->where('needs_attention', true)
                    ->orWhereIn('health', ['At Risk','Delayed','Blocked','Needs Attention'])
                    ->orWhereHas('tasks', fn ($t) => $t->where('needs_attention', true));
            })->count();
            $overdueTasks = (clone $tasks)->whereNull('completed_at')->whereDate('due_date', '<', today())->count();
            $pendingApprovals = (clone $tasks)->whereIn('status', ['Waiting for Client','Waiting for Internal Approval'])->count();
            $shipping = (clone $jobs)->whereHas('phase', fn ($q) => $q->where('short_name', 'Shipment'))->count();
            $outstanding = $access->applyClientScope(Client::query(), $user)->sum('outstanding_balance');

            return compact('activeJobs','riskJobs','overdueTasks','pendingApprovals','shipping','outstanding');
        });

        $workload = User::query()->where('is_active', true);
        if (!$access->isAdministrator($user) && $access->scope($user, 'tasks') !== 'all_records') {
            $workload->whereKey($user->id);
        }

        return [
            'metrics' => $metrics,
            'attentionJobs' => (clone $jobQuery)->with(['client','phase','tasks' => fn ($q) => app(AccessControlService::class)->applyTaskScope($q->where('needs_attention', true), $user)->latest()])
                ->where(function ($q) {
                    $q->where('needs_attention', true)->orWhereHas('tasks', fn ($t) => $t->where('needs_attention', true));
                })->latest()->limit(6)->get(),
            'phaseCounts' => (clone $jobQuery)->selectRaw('workflow_phase_id, count(*) total')->groupBy('workflow_phase_id')->with('phase')->get(),
            'workload' => $workload->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q->whereNull('completed_at')])->orderByDesc('open_tasks_count')->limit(5)->get(),
            'deliveries' => (clone $jobQuery)->with('client')->whereNull('completed_at')->whereNotNull('delivery_date')->orderBy('delivery_date')->limit(6)->get(),
            'activity' => FlowNotification::with('job')->where('user_id', $user->id)->latest()->limit(5)->get(),
        ];
    }
}
