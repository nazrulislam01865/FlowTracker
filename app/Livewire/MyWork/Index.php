<?php

namespace App\Livewire\MyWork;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\User;
use App\Services\BoardService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';
    public string $job = '';
    public string $client = '';
    public string $status = '';
    public string $priority = '';
    public string $assignee = '';
    public string $due = '';
    public string $quick = '';

    public function setQuick(string $filter): void
    {
        $this->quick = $this->quick === $filter ? '' : $filter;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->job = '';
        $this->client = '';
        $this->status = '';
        $this->priority = '';
        $this->assignee = '';
        $this->due = '';
        $this->quick = '';
    }

    public function moveTask(int $taskId, string $status): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $status = trim($status);
        abort_if($status === '', 422, 'Task status is required.');

        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
        app(TaskService::class)->moveStatus($task, $status, auth()->user());
    }

    public function updateTaskDueDate(int $taskId, ?string $date): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
        app(TaskService::class)->updateDueDate($task, $date ?: null, auth()->user());
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render this screen when Pusher reports a visible Job/Task change.
    }

    public function render()
    {
        $service = app(BoardService::class);
        $master = app(MasterDataService::class);
        $filters = [
            'search' => $this->search,
            'job' => $this->job,
            'client' => $this->client,
            'status' => $this->status,
            'priority' => $this->priority,
            'assignee' => $this->assignee,
            'due' => $this->due,
            'quick' => $this->quick,
            'open_only' => $this->quick !== 'completed' && strcasecmp($this->status, 'Completed') !== 0,
        ];

        $visibleJobs = app(\App\Services\JobService::class)->visibleQuery(auth()->user())
            ->whereNull('completed_at')->whereNotIn('status', ['Inactive','Cancelled'])->orderBy('job_number')->get(['id', 'job_number', 'title', 'client_id']);

        $configuredStatuses = $master->active('task_status')->pluck('name')->filter()->values();
        $actualStatuses = app(TaskService::class)->visibleQuery(auth()->user())->whereNotNull('status')->distinct()->orderBy('status')->pluck('status')->filter()->values();
        $statuses = $configuredStatuses->concat($actualStatuses)->unique()->values();

        return view('livewire.my-work.index', [
            'tasks' => $service->tasks(auth()->user(), $filters),
            'counts' => $service->taskCounts(auth()->user(), $filters),
            'users' => auth()->user()->accessScope('tasks') === 'all_records'
                ? User::where('is_active', true)->orderBy('name')->get(['id', 'name'])
                : collect([auth()->user()]),
            'clients' => app(\App\Services\ClientService::class)->visibleQuery(auth()->user())->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taskJobs' => $visibleJobs,
            'taskStatuses' => $statuses,
            'priorities' => $master->active('priority'),
        ]);
    }
}
