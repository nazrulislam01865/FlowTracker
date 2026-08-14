<?php

namespace App\Livewire\MyWork;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Models\Task;
use App\Services\MyWorkService;
use App\Services\TaskService;
use App\Support\BoardLaneResolver;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use HandlesInlineEdits;
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'filter', history: true)]
    public string $quick = 'all';

    #[Url(as: 'sort', history: true)]
    public string $sort = 'action';

    #[Url(as: 'phase', history: true)]
    public string $phaseFilter = '';

    public array $metrics = [
        'attention' => null,
        'overdue' => null,
        'today' => null,
        'upcoming' => null,
        'waiting' => null,
        'mentions' => null,
    ];
    public bool $metricsLoaded = false;
    public array $statusOptions = [];
    public array $phaseOptions = [];
    public int $perPage = MyWorkService::JOBS_PER_PAGE;
    public bool $administratorView = false;
    public bool $hideCompleted = false;

    private const METRIC_FILTERS = ['attention', 'overdue', 'today', 'upcoming', 'waiting'];
    private const QUICK_FILTERS = ['attention', 'all', 'mentions', 'overdue', 'today', 'upcoming', 'waiting'];
    private const SORTS = ['action', 'due', 'job'];

    public function mount(): void
    {
        if (!in_array($this->quick, self::QUICK_FILTERS, true)) $this->quick = 'attention';
        if (!in_array($this->sort, self::SORTS, true)) $this->sort = 'action';

        $user = auth()->user();
        $service = app(MyWorkService::class);
        $this->administratorView = app(\App\Services\AccessControlService::class)->isAdministrator($user);
        // Load the summary from the optimized My Work aggregate during the same
        // request. This avoids starting a second Livewire request with wire:init,
        // which could occupy a PHP worker long after the page was already visible.
        $this->metrics = $service->metrics($user);
        $this->metricsLoaded = true;
        $this->statusOptions = $service->statusOptions();
        $this->phaseOptions = $service->orderPhaseOptions();
        if ($this->phaseFilter !== '' && !in_array($this->phaseFilter, $this->phaseOptions, true)) {
            $this->phaseFilter = '';
        }
    }

    public function updatedSearch(): void
    {
        $this->clearMetricFilterForToolbar();
        $this->resetPage('workPage');
    }

    public function updatedPhaseFilter(): void
    {
        $this->clearMetricFilterForToolbar();
        $this->resetPage('workPage');
    }

    public function setPhaseFilter(string $phase): void
    {
        $phase = trim($phase);
        abort_unless($phase === '' || in_array($phase, $this->phaseOptions, true), 422);

        $this->clearMetricFilterForToolbar();
        $this->phaseFilter = $this->phaseFilter === $phase ? '' : $phase;
        $this->resetPage('workPage');
    }

    public function updatedSort(string $value): void
    {
        if (!in_array($value, self::SORTS, true)) $this->sort = 'action';
        $this->resetPage('workPage');
    }

    public function updatedHideCompleted(): void
    {
        $this->clearMetricFilterForToolbar();
        $this->resetPage('workPage');
    }

    public function setQuick(string $quick): void
    {
        abort_unless(in_array($quick, ['all', 'mentions'], true), 422);
        $this->quick = $quick;
        $this->resetPage('workPage');
    }

    public function setMetricFilter(string $quick): void
    {
        abort_unless(in_array($quick, self::METRIC_FILTERS, true), 422);

        $this->search = '';
        $this->phaseFilter = '';
        $this->hideCompleted = false;
        $this->quick = $this->quick === $quick ? 'all' : $quick;
        $this->resetPage('workPage');
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage('workPage');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->phaseFilter = '';
        $this->quick = 'all';
        $this->hideCompleted = false;
        $this->resetPage('workPage');
    }

    private function clearMetricFilterForToolbar(): void
    {
        if (in_array($this->quick, self::METRIC_FILTERS, true)) {
            $this->quick = 'all';
        }
    }

    #[Renderless]
    public function loadMetrics(): void
    {
        $this->refreshMetricsSnapshot();
    }

    #[Renderless]
    public function updateTaskStatus(int $taskId, string $status, string $version): array
    {
        $status = trim($status);
        $updatedTask = null;
        $result = $this->persistInlineEdit('task status', function () use ($taskId, $status, $version, &$updatedTask): void {
            $actor = auth()->user();
            $allowed = $this->statusOptions ?: app(MyWorkService::class)->statusOptions();
            validator(['status' => $status], [
                'status' => ['required', Rule::in($allowed)],
            ])->validate();

            $personalTask = app(MyWorkService::class)->findPersonalVisibleTask($actor, $taskId);
            $task = Task::query()
                ->whereKey($personalTask->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((string) $task->getRawOriginal('updated_at') !== $version) {
                throw ValidationException::withMessages([
                    'status' => 'This task changed since the list was loaded. Refresh My Work and try again.',
                ]);
            }

            $updatedTask = app(TaskService::class)->moveStatus($task, $status, $actor);
        });

        if (($result['ok'] ?? false) && $updatedTask instanceof Task) {
            $result['version'] = (string) $updatedTask->getRawOriginal('updated_at');
            $result['status'] = (string) $updatedTask->status;
            $result['completed'] = BoardLaneResolver::isCompleted($updatedTask->status);
            // Keep the counters accurate without launching a second background
            // Livewire request. The optimized aggregate is fast and bounded.
            $this->refreshMetricsSnapshot(true);
        }

        return $result;
    }

    #[Renderless]
    public function updateTaskDueDate(int $taskId, ?string $date): array
    {
        $date = trim((string) $date);

        $result = $this->persistInlineEdit('task due date', function () use ($taskId, $date) {
            $actor = auth()->user();
            if ($date !== '') {
                validator(['date' => $date], ['date' => ['date']])->validate();
            }

            $task = app(MyWorkService::class)->findPersonalVisibleTask($actor, $taskId);
            app(TaskService::class)->updateDueDate($task, $date ?: null, $actor);
        });

        if ($result['ok'] ?? false) {
            $this->refreshMetricsSnapshot(true);
        }

        return $result;
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        $this->refreshMetricsSnapshot(true);
        $this->statusOptions = app(MyWorkService::class)->statusOptions();
    }

    private function refreshMetricsSnapshot(bool $fresh = false): void
    {
        $service = app(MyWorkService::class);
        $this->metrics = $service->metrics(auth()->user(), $fresh);
        $this->metricsLoaded = true;
        $this->dispatch(
            'my-work-metrics',
            attention: $this->metrics['attention'] ?? 0,
            overdue: $this->metrics['overdue'] ?? 0,
            today: $this->metrics['today'] ?? 0,
            upcoming: $this->metrics['upcoming'] ?? 0,
            waiting: $this->metrics['waiting'] ?? 0,
            mentions: $this->metrics['mentions'] ?? 0,
        );
    }

    public function render()
    {
        $service = app(MyWorkService::class);
        $page = $service->paginate(auth()->user(), [
            'search' => $this->search,
            'quick' => $this->quick,
            'sort' => $this->sort,
            'phase' => $this->phaseFilter,
            'hide_completed' => $this->hideCompleted,
        ], $this->perPage, 'workPage');

        return view('livewire.my-work.index', [
            'workGroups' => $page['groups'],
            'workPaginator' => $page['paginator'],
            'visibleTaskCount' => $page['visibleTaskCount'],
            'searchNeedsMoreCharacters' => trim($this->search) !== '' && ! app(MyWorkService::class)->searchIsUsable($this->search),
        ]);
    }
}
