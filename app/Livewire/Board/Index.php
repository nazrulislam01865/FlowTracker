<?php

namespace App\Livewire\Board;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Services\BoardService;
use App\Services\JobService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use App\Support\BoardLaneResolver;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class Index extends Component
{
    use UsesPagePlaceholder;
    public string $mode = 'jobs';
    public ?string $message = null;

    public string $workflow = '';
    public string $search = '';
    public string $job = '';
    public string $client = '';
    public string $assignee = '';
    public string $status = '';
    public string $due = '';
    public string $quick = '';
    public string $sort = 'delivery';
    public bool $hideEmptyPhases = false;
    public bool $cardsReady = false;
    public int $cardLimit = 60;
    public array $expandedJobs = [];

    public function mount(): void
    {
        $this->workflow = (string) \App\Models\Workflow::where('is_active', true)->orderBy('id')->value('id');
    }

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['tasks', 'jobs'], true) ? $mode : 'jobs';
        // A mode switch is already a Livewire request, so render the requested
        // board directly instead of adding a second follow-up request.
        $this->cardsReady = true;
        $this->quick = '';
        $this->cardLimit = 60;
        $this->message = null;
    }

    public function setQuick(string $filter): void
    {
        $this->cardsReady = true;
        $this->quick = $this->quick === $filter ? '' : $filter;
        $this->cardLimit = 60;
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
        $this->quick = '';
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
        $this->expandedJobs = app(BoardService::class)->jobs(auth()->user(), $this->jobFilters(), $this->cardLimit)->pluck('id')->all();
    }

    public function collapseAll(): void
    {
        $this->expandedJobs = [];
    }

    public function moveTask(int $taskId, string $status): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
        app(TaskService::class)->moveStatus($task, $status, auth()->user());
        $this->message = 'Board updated successfully.';
    }

    public function updateTaskDueDate(int $taskId, ?string $date): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($taskId);
        app(TaskService::class)->updateDueDate($task, $date ?: null, auth()->user());
    }

    public function updateJobDueDate(int $jobId, ?string $date): void
    {
        abort_unless(auth()->user()->canAccess('jobs.update'), 403);
        $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
        app(JobService::class)->updateDeliveryDate($job, $date ?: null, auth()->user());
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
            'quick' => $this->quick,
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
            'quick' => $this->quick,
        ];
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render this screen when Pusher reports a visible Job/Task change.
    }

    public function render()
    {
        $service = app(BoardService::class);
        $lookups = $service->lookups(auth()->user(), $this->mode === 'jobs');

        $data = [
            'jobs' => collect(),
            'tasks' => collect(),
            'phases' => collect(),
            'jobCounts' => ['all'=>0,'mine'=>0,'overdue'=>0,'week'=>0,'blocked'=>0,'waiting'=>0,'unassigned'=>0],
            'taskCounts' => ['open'=>0,'mine'=>0,'overdue'=>0,'week'=>0,'blocked'=>0,'waiting'=>0,'unassigned'=>0,'completed'=>0],
            'clients' => $lookups['clients'],
            'users' => $lookups['users'],
            'workflows' => collect(),
            'taskJobs' => collect(),
            'jobStatuses' => collect(),
            'taskStatuses' => collect(),
            'hasMoreCards' => false,
        ];

        $data['taskJobs'] = app(JobService::class)->activeQuery(auth()->user())
            ->orderBy('job_number')
            ->limit(250)
            ->get(['id', 'job_number', 'title']);

        if ($this->mode === 'jobs') {
            $filters = $this->jobFilters();
            $visibleJobQuery = app(JobService::class)->visibleQuery(auth()->user());
            $jobRows = $this->cardsReady
                ? $service->jobs(auth()->user(), $filters, $this->cardLimit + 1)
                : collect();
            $data['hasMoreCards'] = $jobRows->count() > $this->cardLimit;
            $data['jobs'] = $jobRows->take($this->cardLimit)->values();
            $data['phases'] = $service->phases($this->workflow ? (int) $this->workflow : null);
            $data['jobCounts'] = $service->jobCounts(auth()->user(), $filters);
            $data['workflows'] = $lookups['workflows'];
            $data['jobStatuses'] = (clone $visibleJobQuery)
                ->whereNotNull('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status')
                ->filter()
                ->values();
        } else {
            $filters = $this->taskFilters();
            $taskRows = $this->cardsReady
                ? $service->tasks(auth()->user(), $filters, $this->cardLimit + 1)
                : collect();
            $data['hasMoreCards'] = $taskRows->count() > $this->cardLimit;
            $data['tasks'] = $taskRows->take($this->cardLimit)->values();
            $data['taskCounts'] = $service->taskCounts(auth()->user(), $filters);
            $data['taskStatuses'] = collect(BoardLaneResolver::taskStatuses(
                app(MasterDataService::class)->active('task_status')->pluck('name')
            ));
        }

        return view('livewire.board.index', $data);
    }

}
