<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ClientService
{
    public function visibleQuery(User $user): Builder
    {
        return app(AccessControlService::class)->applyClientScope(Client::query(), $user);
    }

    public function filteredQuery(User $user, array $filters = []): Builder
    {
        $access = app(AccessControlService::class);
        $quick = (string) ($filters['quick'] ?? 'all');

        return $this->visibleQuery($user)
            ->where('clients.is_active', true)
            ->with('accountManager')
            ->withMin([
                'jobs as next_delivery_at' => fn ($q) => $access->applyJobScope($q->whereNull('flow_jobs.completed_at')->whereNotNull('flow_jobs.delivery_date'), $user),
            ], 'delivery_date')
            ->withCount([
                'jobs as total_jobs_count' => fn ($q) => $access->applyJobScope($q, $user),
                'jobs as active_jobs_count' => fn ($q) => $access->applyJobScope($q->whereNull('flow_jobs.completed_at'), $user),
                'jobs as attention_jobs_count' => fn ($q) => $access->applyJobScope($q->whereNull('flow_jobs.completed_at')->where(fn ($x) => $x->where('flow_jobs.needs_attention', true)->orWhereIn('flow_jobs.health', ['Needs Attention','At Risk','Delayed','Blocked'])), $user),
                'tasks as open_tasks_count' => fn ($q) => $access->applyTaskScope($q->whereNull('tasks.completed_at'), $user),
                'tasks as overdue_tasks_count' => fn ($q) => $access->applyTaskScope($q->whereNull('tasks.completed_at')->whereDate('tasks.due_date', '<', today()), $user),
                'tasks as blocked_tasks_count' => fn ($q) => $access->applyTaskScope($q->whereNull('tasks.completed_at')->where(fn ($x) => $x->where('tasks.status', 'Blocked')->orWhere('tasks.needs_attention', true)), $user),
            ])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($x) use ($search) {
                    $x->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhereHas('accountManager', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('jobs', fn ($j) => $j->where('job_number', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%"));
                });
            })
            ->when($filters['country'] ?? null, fn ($q, $v) => $q->where('country', $v))
            ->when($filters['manager'] ?? null, fn ($q, $v) => $q->where('account_manager_id', $v))
            ->when($filters['health'] ?? null, fn ($q, $v) => $q->whereHas('jobs', fn ($j) => $access->applyJobScope($j->whereNull('completed_at')->where('health', $v), $user)))
            ->when(($filters['outstanding'] ?? null) === 'positive', fn ($q) => $q->where('outstanding_balance', '>', 0))
            ->when(($filters['outstanding'] ?? null) === 'high', fn ($q) => $q->where('outstanding_balance', '>=', 10000))
            ->when(($filters['outstanding'] ?? null) === 'zero', fn ($q) => $q->where('outstanding_balance', '<=', 0))
            ->when($quick === 'active_jobs', fn ($q) => $q->whereHas('jobs', fn ($j) => $access->applyJobScope($j->whereNull('completed_at'), $user)))
            ->when($quick === 'attention', fn ($q) => $q->whereHas('jobs', fn ($j) => $access->applyJobScope($j->whereNull('completed_at')->where(fn ($x) => $x->where('needs_attention', true)->orWhereIn('health', ['Needs Attention','At Risk','Delayed','Blocked'])), $user)))
            ->when($quick === 'outstanding', fn ($q) => $q->where('outstanding_balance', '>', 0))
            ->orderBy('name');
    }

    public function paginate(User $user, array $filters = [], int $perPage = 10)
    {
        return $this->filteredQuery($user, $filters)->paginate($perPage);
    }

    public function summary(User $user): array
    {
        $clients = $this->visibleQuery($user)->where('clients.is_active', true);
        $jobs = app(AccessControlService::class)->applyJobScope(FlowJob::query(), $user);

        return [
            'clients' => (clone $clients)->count(),
            'active_jobs' => (clone $jobs)->whereNull('completed_at')->count(),
            'attention' => (clone $jobs)->whereNull('completed_at')->where(fn ($q) => $q->where('needs_attention', true)->orWhereIn('health', ['Needs Attention','At Risk','Delayed','Blocked']))->count(),
            'outstanding' => (float) (clone $clients)->sum('outstanding_balance'),
            'clients_active' => (clone $clients)->whereHas('jobs', fn ($j) => app(AccessControlService::class)->applyJobScope($j->whereNull('completed_at'), $user))->count(),
            'clients_attention' => (clone $clients)->whereHas('jobs', fn ($j) => app(AccessControlService::class)->applyJobScope($j->whereNull('completed_at')->where(fn ($x) => $x->where('needs_attention', true)->orWhereIn('health', ['Needs Attention','At Risk','Delayed','Blocked'])), $user))->count(),
            'clients_outstanding' => (clone $clients)->where('outstanding_balance', '>', 0)->count(),
        ];
    }

    public function detail(User $user, int $clientId): array
    {
        $client = $this->visibleQuery($user)->with('accountManager')->findOrFail($clientId);
        $jobs = app(JobService::class)->visibleQuery($user)
            ->where('client_id', $client->id)
            ->with(['phase','owner'])
            ->latest('id')
            ->get();
        $tasks = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', fn ($q) => $q->where('client_id', $client->id))
            ->with(['assignee','job'])
            ->whereNull('completed_at')
            ->where(function ($q) {
                $q->where('needs_attention', true)
                    ->orWhere('status', 'Blocked')
                    ->orWhereDate('due_date', '<', today());
            })
            ->orderByRaw('due_date is null, due_date asc')
            ->limit(5)
            ->get();

        $active = $jobs->whereNull('completed_at');
        $overdue = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', fn ($q) => $q->where('client_id', $client->id))
            ->whereNull('completed_at')->whereDate('due_date', '<', today())->count();
        $openTasks = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', fn ($q) => $q->where('client_id', $client->id))
            ->whereNull('completed_at')->count();

        $health = 'On Track';
        if ($active->contains(fn ($job) => $job->needs_attention || in_array($job->health, ['Needs Attention','Blocked','Delayed'], true))) $health = 'Needs Attention';
        elseif ($overdue > 0 || $active->contains(fn ($job) => $job->health === 'At Risk')) $health = 'At Risk';

        return compact('client','jobs','tasks','active','overdue','openTasks','health');
    }
}
