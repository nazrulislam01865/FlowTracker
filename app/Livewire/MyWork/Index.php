<?php

namespace App\Livewire\MyWork;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\User;
use App\Services\BoardService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use App\Support\BoardLaneResolver;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;
    use HandlesInlineEdits;
    public string $search = '';
    public string $job = '';
    public string $client = '';
    public string $status = '';
    public string $priority = '';
    public string $assignee = '';
    public string $due = '';
    public bool $tasksReady = false;
    public int $cardLimit = 60;

    public function clearFilters(): void
    {
        $this->tasksReady = true;
        $this->search = '';
        $this->job = '';
        $this->client = '';
        $this->status = '';
        $this->priority = '';
        $this->assignee = '';
        $this->due = '';
        $this->cardLimit = 60;
    }

    public function clearFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['search','job','client','status','priority','assignee','due'], true), 422);
        $this->{$filter} = '';
        $this->tasksReady = true;
        $this->cardLimit = 60;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'job', 'client', 'status', 'priority', 'assignee', 'due'], true)) {
            $this->tasksReady = true;
            $this->cardLimit = 60;
        }
    }

    public function loadMore(): void
    {
        $this->tasksReady = true;
        $this->cardLimit = min(300, $this->cardLimit + 60);
    }

    public function loadMyWorkTasks(): void
    {
        $this->tasksReady = true;
    }

    public function moveTask(int $taskId, string $status): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $status = trim($status);
        abort_if($status === '', 422, 'Task status is required.');

        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
        app(TaskService::class)->moveStatus($task, $status, auth()->user());
    }

    #[Renderless]
    public function updateTaskDueDate(int $taskId, ?string $date): array
    {
        return $this->persistInlineEdit('task due date', function () use ($taskId, $date) {
            abort_unless(auth()->user()->canAccess('tasks.update'), 403);
            $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
            app(TaskService::class)->updateDueDate($task, $date ?: null, auth()->user());
        });
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render this screen when Pusher reports a visible Job/Task change.
    }

    public function render()
    {
        return view('livewire.my-work.index', $this->myWorkPageData(auth()->user()));
    }

    private function myWorkPageData(User $user): array
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
            'open_only' => strcasecmp($this->status, 'Completed') !== 0,
        ];

        $taskRows = $this->tasksReady
            ? $service->tasks($user, $filters, $this->cardLimit + 1)
            : collect();
        $optionService = app(\App\Services\FilterOptionService::class);

        return [
            'tasks' => $taskRows->take($this->cardLimit)->values(),
            'hasMoreCards' => $taskRows->count() > $this->cardLimit,
            'jobFilterOptions' => $optionService->options($user, 'jobs', 'my-work', '', $this->job !== '' ? (int) $this->job : null, 6),
            'clientFilterOptions' => $optionService->options($user, 'clients', 'my-work', '', $this->client !== '' ? (int) $this->client : null, 6),
            'assigneeFilterOptions' => $optionService->options($user, 'users', 'my-work', '', $this->assignee !== '' ? (int) $this->assignee : null, 6),
            'taskStatuses' => collect(BoardLaneResolver::taskStatuses($master->active('task_status')->pluck('name'))),
            'priorities' => $master->active('priority'),
        ];
    }
}
