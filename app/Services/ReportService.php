<?php

namespace App\Services;

use App\Models\User;

class ReportService
{
    public function data(User $user): array
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($user, 'reports', 'view'), 403);

        $jobs = app(JobService::class)->visibleQuery($user);
        $tasks = app(TaskService::class)->visibleQuery($user);
        $clients = app(ClientService::class)->visibleQuery($user);

        $phase = (clone $jobs)
            ->selectRaw('workflow_phase_id, count(*) total')
            ->groupBy('workflow_phase_id')
            ->with('phase')
            ->get();

        if ($access->isAdministrator($user) || $access->scope($user, 'tasks') === 'all_records') {
            $workload = User::query()
                ->where('is_active', true)
                ->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q->whereNull('completed_at')])
                ->orderByDesc('open_tasks_count')
                ->limit(8)
                ->get();
        } else {
            $workload = User::query()
                ->whereKey($user->id)
                ->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q->whereNull('completed_at')])
                ->get();
        }

        $completed = (clone $jobs)->whereNotNull('completed_at')->count();
        $onTime = (clone $jobs)->whereNotNull('completed_at')->whereColumn('completed_at', '<=', 'delivery_date')->count();
        $taskTotal = (clone $tasks)->count();
        $taskDone = (clone $tasks)->whereNotNull('completed_at')->count();

        return [
            'phase' => $phase,
            'workload' => $workload,
            'kpis' => [
                'active_jobs' => (clone $jobs)->whereNull('completed_at')->count(),
                'task_completion' => $taskTotal ? round($taskDone / $taskTotal * 100) : 0,
                'on_time' => $completed ? round($onTime / $completed * 100) : 0,
                'receivables' => (clone $clients)->sum('outstanding_balance'),
            ],
        ];
    }
}
