<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BoardService
{
    public function __construct(
        private readonly JobService $jobs,
        private readonly TaskService $tasks,
    ) {}

    public function jobs(User $user, array $filters = []): Collection
    {
        return $this->jobQuery($user, $filters)
            ->with([
                'client', 'workflow', 'phase', 'owner', 'coordinator',
                'items', 'members.user',
                'phaseHistories' => fn ($query) => $query->latest('entered_at'),
                'tasks' => fn ($query) => app(AccessControlService::class)->applyTaskScope($query, $user)->with(['assignee', 'phase'])->orderByRaw('completed_at is null desc')->orderByRaw('due_date is null, due_date asc'),
                'activities' => fn ($query) => $query->with('user')->latest()->limit(1),
            ])
            ->whereNull('completed_at')
            ->limit(250)
            ->get();
    }

    public function tasks(User $user, array $filters = []): Collection
    {
        return $this->taskQuery($user, $filters)
            ->with([
                'job.client', 'job.coordinator', 'phase', 'assignee',
                'checklistItems', 'comments.user', 'documents',
            ])
            ->orderByRaw('due_date is null, due_date asc')
            ->limit(250)
            ->get();
    }

    public function jobCounts(User $user, array $baseFilters = []): array
    {
        $base = $this->jobQuery($user, array_diff_key($baseFilters, ['quick' => true]));

        return [
            'all' => (clone $base)->whereNull('completed_at')->count(),
            'mine' => (clone $base)->whereNull('completed_at')->where(fn ($q) => $q
                ->where('owner_id', $user->id)
                ->orWhere('coordinator_id', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
                ->orWhereHas('tasks', fn ($t) => $t->where('assignee_id', $user->id))
            )->count(),
            'overdue' => (clone $base)->whereNull('completed_at')->whereDate('delivery_date', '<', today())->count(),
            'week' => (clone $base)->whereNull('completed_at')->whereBetween('delivery_date', [today(), today()->copy()->addDays(7)])->count(),
            'blocked' => (clone $base)->whereNull('completed_at')->where(fn ($q) => $q->where('health', 'Blocked')->orWhere('status', 'Blocked')->orWhereHas('tasks', fn ($t) => $t->where('status', 'Blocked')->whereNull('completed_at')))->count(),
            'waiting' => (clone $base)->whereNull('completed_at')->where(fn ($q) => $q
                ->whereIn('status', ['Waiting for Client', 'Waiting for Supplier', 'Waiting for Internal Approval'])
                ->orWhereHas('tasks', fn ($t) => $t->whereIn('status', ['Waiting for Client', 'Waiting for Supplier', 'Waiting for Internal Approval'])->whereNull('completed_at'))
            )->count(),
            'unassigned' => (clone $base)->whereNull('completed_at')->where(fn ($q) => $q
                ->whereNull('owner_id')->orWhereNull('coordinator_id')->orWhereHas('tasks', fn ($t) => $t->whereNull('assignee_id')->whereNull('completed_at'))
            )->count(),
        ];
    }

    public function taskCounts(User $user, array $baseFilters = []): array
    {
        $base = $this->taskQuery($user, array_diff_key($baseFilters, ['quick' => true, 'open_only' => true]));

        return [
            'open' => (clone $base)->whereNull('completed_at')->count(),
            'mine' => (clone $base)->whereNull('completed_at')->where('assignee_id', $user->id)->count(),
            'overdue' => (clone $base)->whereNull('completed_at')->whereDate('due_date', '<', today())->count(),
            'week' => (clone $base)->whereNull('completed_at')->whereBetween('due_date', [today(), today()->copy()->addDays(7)])->count(),
            'blocked' => (clone $base)->whereNull('completed_at')->where('status', 'Blocked')->count(),
            'waiting' => (clone $base)->whereNull('completed_at')->whereIn('status', ['Waiting for Client', 'Waiting for Supplier', 'Waiting for Internal Approval'])->count(),
            'unassigned' => (clone $base)->whereNull('completed_at')->whereNull('assignee_id')->count(),
            'completed' => (clone $base)->where(fn ($q) => $q->whereNotNull('completed_at')->orWhere('status', 'Completed'))->count(),
        ];
    }

    public function phases(?int $workflowId = null): Collection
    {
        if (!$workflowId) {
            $workflowId = Workflow::where('is_active', true)->orderBy('id')->value('id');
        }

        return WorkflowPhase::where('workflow_id', $workflowId)->where('is_active', true)->orderBy('sequence')->get();
    }

    public function lookups(User $user): array
    {
        $access = app(AccessControlService::class);
        $clients = $access->applyClientScope(Client::where('is_active', true), $user)->orderBy('name')->get(['id','name']);
        $users = $access->scope($user, 'tasks') === 'all_records'
            ? User::where('is_active', true)->orderBy('name')->get(['id','name'])
            : collect([$user])->map(fn ($u) => (object) ['id' => $u->id, 'name' => $u->name]);
        return [
            'clients' => $clients,
            'users' => $users,
            'workflows' => Workflow::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function jobQuery(User $user, array $filters): Builder
    {
        $query = $this->jobs->visibleQuery($user);

        $query
            ->when(empty($filters['status']), fn ($q) => $q->whereNotIn('status', ['Inactive','Cancelled']))
            ->when($filters['workflow'] ?? null, fn ($q, $value) => $q->where('workflow_id', $value))
            ->when($filters['job'] ?? null, fn ($q, $value) => $q->whereKey($value))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('job_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('product', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('tasks', fn ($task) => $task->where('title', 'like', "%{$search}%")->orWhere('task_number', 'like', "%{$search}%"));
                });
            })
            ->when($filters['client'] ?? null, fn ($q, $value) => $q->where('client_id', $value))
            ->when($filters['assignee'] ?? null, fn ($q, $value) => $q->whereHas('tasks', fn ($task) => $task->where('assignee_id', $value)))
            ->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))
            ->when($filters['due'] ?? null, function ($q, $value) {
                match ($value) {
                    'overdue' => $q->whereDate('delivery_date', '<', today()),
                    'today' => $q->whereDate('delivery_date', today()),
                    'week' => $q->whereBetween('delivery_date', [today(), today()->copy()->addDays(7)]),
                    'month' => $q->whereBetween('delivery_date', [today(), today()->copy()->addDays(30)]),
                    'none' => $q->whereNull('delivery_date'),
                    default => null,
                };
            })
            // Legacy filters retained for compatibility with existing URLs/state.
            ->when($filters['owner'] ?? null, fn ($q, $value) => $q->where(fn ($inner) => $inner->where('owner_id', $value)->orWhere('coordinator_id', $value)))
            ->when($filters['health'] ?? null, fn ($q, $value) => $q->where('health', $value));

        match ($filters['quick'] ?? '') {
            'mine' => $query->where(fn ($q) => $q
                ->where('owner_id', $user->id)
                ->orWhere('coordinator_id', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
                ->orWhereHas('tasks', fn ($t) => $t->where('assignee_id', $user->id))
            ),
            'overdue' => $query->whereDate('delivery_date', '<', today()),
            'week' => $query->whereBetween('delivery_date', [today(), today()->copy()->addDays(7)]),
            'blocked' => $query->where(fn ($q) => $q->where('health', 'Blocked')->orWhere('status', 'Blocked')->orWhereHas('tasks', fn ($t) => $t->where('status', 'Blocked')->whereNull('completed_at'))),
            'waiting' => $query->where(fn ($q) => $q->whereIn('status', ['Waiting for Client', 'Waiting for Supplier', 'Waiting for Internal Approval'])->orWhereHas('tasks', fn ($t) => $t->whereIn('status', ['Waiting for Client', 'Waiting for Supplier', 'Waiting for Internal Approval'])->whereNull('completed_at'))),
            'unassigned' => $query->where(fn ($q) => $q->whereNull('owner_id')->orWhereNull('coordinator_id')->orWhereHas('tasks', fn ($t) => $t->whereNull('assignee_id')->whereNull('completed_at'))),
            default => null,
        };

        $sort = $filters['sort'] ?? 'delivery';
        if ($sort === 'updated') {
            $query->latest('updated_at');
        } elseif ($sort === 'priority') {
            $query->orderByRaw("case priority when 'Critical' then 1 when 'High' then 2 when 'Medium' then 3 else 4 end")->orderBy('delivery_date');
        } else {
            $query->orderByRaw('delivery_date is null, delivery_date asc')->latest('id');
        }

        return $query;
    }

    private function taskQuery(User $user, array $filters): Builder
    {
        $query = $this->tasks->visibleQuery($user)
            ->whereHas('job', fn ($job) => $job->whereNotIn('status', ['Inactive','Cancelled']));

        if (($filters['open_only'] ?? false) === true) {
            $query->whereNull('completed_at');
        }

        $query
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('task_number', 'like', "%{$search}%")
                        ->orWhereHas('job', fn ($job) => $job
                            ->where('job_number', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%")
                            ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))
                        );
                });
            })
            ->when($filters['job'] ?? null, fn ($q, $value) => $q->where('flow_job_id', $value))
            ->when($filters['client'] ?? null, fn ($q, $value) => $q->whereHas('job', fn ($job) => $job->where('client_id', $value)))
            ->when($filters['assignee'] ?? null, fn ($q, $value) => $q->where('assignee_id', $value))
            ->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))
            ->when($filters['priority'] ?? null, fn ($q, $value) => $q->where('priority', $value))
            ->when($filters['due'] ?? null, function ($q, $value) {
                match ($value) {
                    'overdue' => $q->whereDate('due_date', '<', today()),
                    'today' => $q->whereDate('due_date', today()),
                    'week' => $q->whereBetween('due_date', [today(), today()->copy()->addDays(7)]),
                    'month' => $q->whereBetween('due_date', [today(), today()->copy()->addDays(30)]),
                    'none' => $q->whereNull('due_date'),
                    default => null,
                };
            });

        match ($filters['quick'] ?? '') {
            'open' => $query->whereNull('completed_at'),
            'mine' => $query->where('assignee_id', $user->id),
            'overdue' => $query->whereNull('completed_at')->whereDate('due_date', '<', today()),
            'week' => $query->whereNull('completed_at')->whereBetween('due_date', [today(), today()->copy()->addDays(7)]),
            'blocked' => $query->whereNull('completed_at')->where('status', 'Blocked'),
            'waiting' => $query->whereNull('completed_at')->whereIn('status', ['Waiting for Client', 'Waiting for Supplier', 'Waiting for Internal Approval']),
            'unassigned' => $query->whereNull('completed_at')->whereNull('assignee_id'),
            'completed' => $query->where(fn ($q) => $q->whereNotNull('completed_at')->orWhere('status', 'Completed')),
            default => null,
        };

        return $query;
    }
}
