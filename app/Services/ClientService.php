<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

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

        $archived = (bool) ($filters['archived'] ?? false);

        return $this->visibleQuery($user)
            ->where('clients.is_active', !$archived)
            ->with('accountManager')
            ->withMin([
                'jobs as next_delivery_at' => fn ($q) => $access->applyJobScope($q->whereNull('flow_jobs.completed_at')->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES)->whereNotNull('flow_jobs.delivery_date'), $user),
            ], 'delivery_date')
            ->withCount([
                'jobs as total_jobs_count' => fn ($q) => $access->applyJobScope($q, $user),
                'jobs as active_jobs_count' => fn ($q) => $access->applyJobScope($q->whereNull('flow_jobs.completed_at')->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES), $user),
                'jobs as attention_jobs_count' => fn ($q) => $access->applyJobScope($q->whereNull('flow_jobs.completed_at')->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES)->where(fn ($x) => $x->where('flow_jobs.needs_attention', true)->orWhereIn('flow_jobs.health', ['Needs Attention','At Risk','Delayed','Blocked'])), $user),
                'tasks as open_tasks_count' => fn ($q) => $access->applyTaskScope($q->whereNull('tasks.completed_at'), $user),
                'tasks as overdue_tasks_count' => fn ($q) => $access->applyTaskScope($q->whereNull('tasks.completed_at')->where('tasks.due_date', '<', app(WorkspaceSettingsService::class)->localToday()->toDateString()), $user),
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
            ->when($filters['health'] ?? null, fn ($q, $v) => $q->whereHas('jobs', fn ($j) => $access->applyJobScope($j->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES)->where('health', $v), $user)))
            ->when(($filters['outstanding'] ?? null) === 'positive', fn ($q) => $q->where('outstanding_balance', '>', 0))
            ->when(($filters['outstanding'] ?? null) === 'high', fn ($q) => $q->where('outstanding_balance', '>=', 10000))
            ->when(($filters['outstanding'] ?? null) === 'zero', fn ($q) => $q->where('outstanding_balance', '<=', 0))
            ->when($quick === 'active_jobs', fn ($q) => $q->whereHas('jobs', fn ($j) => $access->applyJobScope($j->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES), $user)))
            ->when($quick === 'attention', fn ($q) => $q->whereHas('jobs', fn ($j) => $access->applyJobScope($j->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES)->where(fn ($x) => $x->where('needs_attention', true)->orWhereIn('health', ['Needs Attention','At Risk','Delayed','Blocked'])), $user)))
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

        $clientMetrics = (clone $clients)
            ->reorder()
            ->selectRaw('count(*) as client_count')
            ->selectRaw('coalesce(sum(outstanding_balance), 0) as outstanding_total')
            ->selectRaw('sum(case when outstanding_balance > 0 then 1 else 0 end) as outstanding_client_count')
            ->first();

        $jobMetrics = (clone $jobs)
            ->reorder()
            ->selectRaw("sum(case when completed_at is null and status not in ('Inactive','Cancelled') then 1 else 0 end) as active_job_count")
            ->selectRaw("sum(case when completed_at is null and status not in ('Inactive','Cancelled') and (needs_attention = 1 or health in ('Needs Attention','At Risk','Delayed','Blocked')) then 1 else 0 end) as attention_job_count")
            ->first();

        return [
            'clients' => (int) ($clientMetrics?->client_count ?? 0),
            'active_jobs' => (int) ($jobMetrics?->active_job_count ?? 0),
            'attention' => (int) ($jobMetrics?->attention_job_count ?? 0),
            'outstanding' => (float) ($clientMetrics?->outstanding_total ?? 0),
            'clients_active' => (clone $clients)->whereHas('jobs', fn ($j) => app(AccessControlService::class)->applyJobScope($j->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES), $user))->count(),
            'clients_attention' => (clone $clients)->whereHas('jobs', fn ($j) => app(AccessControlService::class)->applyJobScope($j->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES)->where(fn ($x) => $x->where('needs_attention', true)->orWhereIn('health', ['Needs Attention','At Risk','Delayed','Blocked'])), $user))->count(),
            'clients_outstanding' => (int) ($clientMetrics?->outstanding_client_count ?? 0),
            'archived' => $this->visibleQuery($user)->where('clients.is_active', false)->count(),
        ];
    }

    public function archive(User $user, int $clientId): Client
    {
        $client = $this->visibleQuery($user)->findOrFail($clientId);
        if ($client->is_active) {
            $client->update(['is_active' => false]);
            $this->touchLifecycleVersion();
        }

        return $client->refresh();
    }

    public function restore(User $user, int $clientId): Client
    {
        $client = $this->visibleQuery($user)->findOrFail($clientId);
        if (!$client->is_active) {
            $client->update(['is_active' => true]);
            $this->touchLifecycleVersion();
        }

        return $client->refresh();
    }

    public function lifecycleVersion(): int
    {
        return max(1, (int) Cache::get('flowtrack:clients:lifecycle-version', 1));
    }

    private function touchLifecycleVersion(): void
    {
        if (!Cache::has('flowtrack:clients:lifecycle-version')) {
            Cache::forever('flowtrack:clients:lifecycle-version', 1);
        }
        Cache::increment('flowtrack:clients:lifecycle-version');
    }

    public function detail(User $user, int $clientId): array
    {
        $client = $this->visibleQuery($user)->with(['accountManager','shippingAddresses'])->findOrFail($clientId);
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
                    ->orWhereDate('due_date', '<', app(WorkspaceSettingsService::class)->localToday());
            })
            ->orderByRaw('due_date is null, due_date asc')
            ->limit(5)
            ->get();

        $active = $jobs->whereNull('completed_at')->whereNotIn('status', JobService::INACTIVE_STATUSES);
        $overdue = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', fn ($q) => $q->where('client_id', $client->id))
            ->whereNull('completed_at')->where('due_date', '<', app(WorkspaceSettingsService::class)->localToday()->toDateString())->count();
        $openTasks = app(TaskService::class)->visibleQuery($user)
            ->whereHas('job', fn ($q) => $q->where('client_id', $client->id))
            ->whereNull('completed_at')->count();

        $health = 'On Track';
        if ($active->contains(fn ($job) => $job->needs_attention || in_array($job->health, ['Needs Attention','Blocked','Delayed'], true))) $health = 'Needs Attention';
        elseif ($overdue > 0 || $active->contains(fn ($job) => $job->health === 'At Risk')) $health = 'At Risk';

        return compact('client','jobs','tasks','active','overdue','openTasks','health');
    }
}
