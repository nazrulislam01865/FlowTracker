<?php

namespace App\Livewire\Board;

use App\Services\BoardService;
use App\Services\JobService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class Index extends Component
{
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
    public array $expandedJobs = [];

    public function mount(): void
    {
        $this->workflow = (string) \App\Models\Workflow::where('is_active', true)->orderBy('id')->value('id');
    }

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['tasks', 'jobs'], true) ? $mode : 'jobs';
        $this->quick = '';
        $this->message = null;
    }

    public function setQuick(string $filter): void
    {
        $this->quick = $this->quick === $filter ? '' : $filter;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->job = '';
        $this->client = '';
        $this->assignee = '';
        $this->status = '';
        $this->due = '';
        $this->quick = '';
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
        $this->expandedJobs = app(BoardService::class)->jobs(auth()->user(), $this->jobFilters())->pluck('id')->all();
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
        $master = app(MasterDataService::class);
        $lookups = $service->lookups(auth()->user());
        $jobFilters = $this->jobFilters();
        $taskFilters = $this->taskFilters();
        $jobs = $service->jobs(auth()->user(), $jobFilters);
        $tasks = $service->tasks(auth()->user(), $taskFilters);
        $visibleJobQuery = app(JobService::class)->visibleQuery(auth()->user());
        $taskJobs = (clone $visibleJobQuery)->whereNull('completed_at')->whereNotIn('status', ['Inactive','Cancelled'])->orderBy('job_number')->limit(250)->get(['id', 'job_number', 'title']);
        $jobStatuses = (clone $visibleJobQuery)->whereNotNull('status')->distinct()->orderBy('status')->pluck('status')->filter()->values();
        $configuredTaskStatuses = $master->active('task_status')->pluck('name')->filter()->values();
        $actualTaskStatuses = app(TaskService::class)->visibleQuery(auth()->user())->whereNotNull('status')->distinct()->orderBy('status')->pluck('status')->filter()->values();
        $taskStatuses = $configuredTaskStatuses->concat($actualTaskStatuses)->unique()->values();

        return view('livewire.board.index', [
            'jobs' => $jobs,
            'tasks' => $tasks,
            'phases' => $service->phases($this->workflow ? (int) $this->workflow : null),
            'jobCounts' => $service->jobCounts(auth()->user(), $jobFilters),
            'taskCounts' => $service->taskCounts(auth()->user(), $taskFilters),
            'clients' => $lookups['clients'],
            'users' => $lookups['users'],
            'workflows' => $lookups['workflows'],
            'taskJobs' => $taskJobs,
            'jobStatuses' => $jobStatuses,
            'taskStatuses' => $taskStatuses,
        ]);
    }
}
