<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowNotification;
use App\Models\User;

class DashboardService
{
    public function data(User $user): array
    {
        $access = app(AccessControlService::class);
        $jobQuery = app(JobService::class)->visibleQuery($user);
        $taskQuery = app(TaskService::class)->visibleQuery($user);

        $activeJobs = (clone $jobQuery)->whereNull('completed_at')->count();
        $riskJobs = (clone $jobQuery)->where(function ($q) {
            $q->where('needs_attention', true)
                ->orWhereIn('health', ['At Risk','Delayed','Blocked','Needs Attention'])
                ->orWhereHas('tasks', fn ($t) => $t->where('needs_attention', true));
        })->count();
        $overdueTasks = (clone $taskQuery)->whereNull('completed_at')->whereDate('due_date', '<', today())->count();
        $pendingApprovals = (clone $taskQuery)->whereIn('status', ['Waiting for Client','Waiting for Internal Approval'])->count();
        $shipping = (clone $jobQuery)->whereHas('phase', fn ($q) => $q->where('short_name', 'Shipment'))->count();
        $outstanding = $access->applyClientScope(Client::query(), $user)->sum('outstanding_balance');

        $workload = User::query()->where('is_active', true);
        if (!$access->isAdministrator($user) && $access->scope($user, 'tasks') !== 'all_records') {
            $workload->whereKey($user->id);
        }

        return [
            'metrics' => compact('activeJobs','riskJobs','overdueTasks','pendingApprovals','shipping','outstanding'),
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
