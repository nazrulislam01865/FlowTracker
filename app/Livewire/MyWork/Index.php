<?php

namespace App\Livewire\MyWork;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Services\BoardService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use App\Support\BoardLaneResolver;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;
    public string $search = '';
    public string $job = '';
    public string $client = '';
    public string $status = '';
    public string $priority = '';
    public string $assignee = '';
    public string $due = '';
    public string $quick = '';
    public int $cardLimit = 60;

    public function setQuick(string $filter): void
    {
        $this->quick = $this->quick === $filter ? '' : $filter;
        $this->cardLimit = 60;
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
        $this->cardLimit = 60;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'job', 'client', 'status', 'priority', 'assignee', 'due'], true)) {
            $this->cardLimit = 60;
        }
    }

    public function loadMore(): void
    {
        $this->cardLimit = min(300, $this->cardLimit + 60);
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

        $lookups = $service->lookups(auth()->user(), false);
        $visibleJobs = app(\App\Services\JobService::class)->activeQuery(auth()->user())
            ->orderBy('job_number')
            ->limit(250)
            ->get(['id', 'job_number', 'title', 'client_id']);

        $statuses = collect(BoardLaneResolver::taskStatuses(
            $master->active('task_status')->pluck('name')
        ));

        $taskRows = $service->tasks(auth()->user(), $filters, $this->cardLimit + 1);

        return view('livewire.my-work.index', [
            'tasks' => $taskRows->take($this->cardLimit)->values(),
            'hasMoreCards' => $taskRows->count() > $this->cardLimit,
            'counts' => $service->taskCounts(auth()->user(), $filters),
            'users' => $lookups['users'],
            'clients' => $lookups['clients'],
            'taskJobs' => $visibleJobs,
            'taskStatuses' => $statuses,
            'priorities' => $master->active('priority'),
        ]);
    }
}
