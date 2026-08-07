<?php

namespace App\Livewire\Board;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Models\User;

use App\Services\BoardService;
use App\Services\JobService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use App\Support\BoardLaneResolver;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Throwable;

class Index extends Component
{
    use UsesPagePlaceholder;
    use HandlesInlineEdits;
    public string $mode = 'jobs';
    public ?string $message = null;

    public string $workflow = '';
    public string $search = '';
    public string $job = '';
    public string $client = '';
    public string $assignee = '';
    public string $status = '';
    public string $due = '';
    public string $sort = 'delivery';
    public bool $hideEmptyPhases = false;
    public bool $cardsReady = false;
    public int $cardLimit = 60;
    public array $expandedJobs = [];
    public bool $taskGroupsExpanded = true;

    public function mount(): void
    {
        $this->workflow = (string) (\App\Models\Workflow::where('is_snapshot', false)->where('is_active', true)->orderBy('id')->value('id')
            ?: \App\Models\FlowJob::query()->whereNotNull('source_workflow_id')->value('source_workflow_id'));
    }

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['tasks', 'jobs'], true) ? $mode : 'jobs';
        // A mode switch is already a Livewire request, so render the requested
        // board directly instead of adding a second follow-up request.
        $this->cardsReady = true;
        $this->cardLimit = 60;
        $this->message = null;
    }

    public function clearFilters(): void
    {
        $this->cardsReady = true;
        $this->search = '';
        $this->job = '';
        $this->client = '';
        $this->assignee = '';
        $this->status = '';
        $this->due = '';
        $this->cardLimit = 60;
    }

    public function clearFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['search','job','client','assignee','status','due'], true), 422);
        $this->{$filter} = '';
        $this->cardsReady = true;
        $this->cardLimit = 60;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['workflow', 'search', 'job', 'client', 'assignee', 'status', 'due', 'sort'], true)) {
            $this->cardsReady = true;
            $this->cardLimit = 60;
        }
    }

    public function loadMore(): void
    {
        $this->cardsReady = true;
        $this->cardLimit = min(300, $this->cardLimit + 60);
    }

    public function loadBoardCards(): void
    {
        $this->cardsReady = true;
    }

    public function toggleJobCard(int $jobId): void
    {
        if (in_array($jobId, $this->expandedJobs, true)) {
            $this->expandedJobs = array_values(array_filter($this->expandedJobs, fn ($id) => $id !== $jobId));
            return;
        }
        $this->expandedJobs[] = $jobId;
    }

    public function toggleEmptyPhases(): void
    {
        $this->hideEmptyPhases = !$this->hideEmptyPhases;
    }

    public function expandAll(): void
    {
        $this->cardsReady = true;
        $this->expandedJobs = app(BoardService::class)
            ->jobs(auth()->user(), $this->jobFilters(), $this->cardLimit)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function expandVisibleJobs(string $jobIds): void
    {
        $this->cardsReady = true;
        $this->expandedJobs = collect(explode(',', $jobIds))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function collapseAll(): void
    {
        $this->expandedJobs = [];
    }

    public function expandAllTaskGroups(): void
    {
        $this->taskGroupsExpanded = true;
    }

    public function collapseAllTaskGroups(): void
    {
        $this->taskGroupsExpanded = false;
    }

    public function moveTask(int $taskId, string $status): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
        app(TaskService::class)->moveStatus($task, $status, auth()->user());
        $this->message = 'Board updated successfully.';
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

    #[Renderless]
    public function updateJobDueDate(int $jobId, ?string $date): array
    {
        return $this->persistInlineEdit('Job delivery date', function () use ($jobId, $date) {
            abort_unless(auth()->user()->canAccess('jobs.update'), 403);
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateDeliveryDate($job, $date ?: null, auth()->user());
        });
    }

    public function moveJob(int $jobId, int $phaseId): void
    {
        abort_unless(auth()->user()->canAccess('jobs.update'), 403);
        try {
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->moveToPhase($job, $phaseId, auth()->user());
            $this->message = 'Board updated successfully.';
        } catch (Throwable $e) {
            $this->message = $e->getMessage();
        }
    }

    private function jobFilters(): array
    {
        return [
            'workflow' => $this->workflow,
            'search' => $this->search,
            'job' => $this->job,
            'client' => $this->client,
            'assignee' => $this->assignee,
            'status' => $this->status,
            'due' => $this->due,
            'sort' => $this->sort,
        ];
    }

    private function taskFilters(): array
    {
        return [
            'search' => $this->search,
            'job' => $this->job,
            'client' => $this->client,
            'assignee' => $this->assignee,
            'status' => $this->status,
            'due' => $this->due,
        ];
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render this screen when Pusher reports a visible Job/Task change.
    }

    public function render()
    {
        $user = auth()->user();
        $service = app(BoardService::class);

        $data = $this->mode === 'jobs'
            ? $this->jobBoardData($user, $service)
            : $this->taskBoardData($user, $service);

        return view('livewire.board.index', $data);
    }

    private function boardBaseData(User $user): array
    {
        return [
            'jobs' => collect(),
            'tasks' => collect(),
            'phases' => collect(),
            'jobFilterOptions' => app(\App\Services\FilterOptionService::class)->options($user, 'jobs', 'board', '', $this->job !== '' ? (int) $this->job : null, 6),
            'clientFilterOptions' => app(\App\Services\FilterOptionService::class)->options($user, 'clients', 'board', '', $this->client !== '' ? (int) $this->client : null, 6),
            'assigneeFilterOptions' => app(\App\Services\FilterOptionService::class)->options($user, 'users', 'board', '', $this->assignee !== '' ? (int) $this->assignee : null, 6),
            'workflows' => collect(),
            'jobStatuses' => collect(),
            'taskStatuses' => collect(),
            'hasMoreCards' => false,
        ];
    }

    private function jobBoardData(User $user, BoardService $service): array
    {
        $data = $this->boardBaseData($user);
        $filters = $this->jobFilters();
        $jobRows = $this->cardsReady
            ? $service->jobs($user, $filters, $this->cardLimit + 1)
            : collect();

        $data['hasMoreCards'] = $jobRows->count() > $this->cardLimit;
        $data['jobs'] = $jobRows->take($this->cardLimit)->values();
        $data['phases'] = $service->phases($this->workflow ? (int) $this->workflow : null);
        $data['workflows'] = $service->workflowOptions();
        $data['jobStatuses'] = app(JobService::class)
            ->visibleQuery($user)
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        return $data;
    }

    private function taskBoardData(User $user, BoardService $service): array
    {
        $data = $this->boardBaseData($user);
        $filters = $this->taskFilters();
        $taskRows = $this->cardsReady
            ? $service->tasks($user, $filters, $this->cardLimit + 1)
            : collect();

        $data['hasMoreCards'] = $taskRows->count() > $this->cardLimit;
        $data['tasks'] = $taskRows->take($this->cardLimit)->values();
        $data['taskStatuses'] = collect(BoardLaneResolver::taskStatuses(
            app(MasterDataService::class)->active('task_status')->pluck('name')
        ));

        return $data;
    }

}
