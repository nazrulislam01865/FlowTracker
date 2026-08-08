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

    public array $metrics = [];
    public array $statusOptions = [];
    public int $perPage = MyWorkService::JOBS_PER_PAGE;
    public bool $administratorView = false;

    private const QUICK_FILTERS = ['attention', 'all', 'mentions', 'overdue', 'today', 'upcoming', 'waiting'];
    private const SORTS = ['action', 'due', 'job'];

    public function mount(): void
    {
        if (!in_array($this->quick, self::QUICK_FILTERS, true)) $this->quick = 'attention';
        if (!in_array($this->sort, self::SORTS, true)) $this->sort = 'action';

        $user = auth()->user();
        $service = app(MyWorkService::class);
        $this->administratorView = app(\App\Services\AccessControlService::class)->isAdministrator($user);
        $this->metrics = $service->metrics($user);
        $this->statusOptions = $service->statusOptions();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('workPage');
    }

    public function updatedSort(string $value): void
    {
        if (!in_array($value, self::SORTS, true)) $this->sort = 'action';
        $this->resetPage('workPage');
    }

    public function setQuick(string $quick): void
    {
        abort_unless(in_array($quick, self::QUICK_FILTERS, true), 422);
        $this->quick = $quick;
        $this->resetPage('workPage');
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage('workPage');
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
            $this->metrics = app(MyWorkService::class)->metrics(auth()->user());
            $result['metrics'] = $this->metrics;
        }

        return $result;
    }

    #[Renderless]
    public function updateTaskDueDate(int $taskId, ?string $date): array
    {
        $date = trim((string) $date);

        return $this->persistInlineEdit('task due date', function () use ($taskId, $date) {
            $actor = auth()->user();
            if ($date !== '') {
                validator(['date' => $date], ['date' => ['date']])->validate();
            }

            $task = app(MyWorkService::class)->findPersonalVisibleTask($actor, $taskId);
            app(TaskService::class)->updateDueDate($task, $date ?: null, $actor);
        });
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        $service = app(MyWorkService::class);
        $this->metrics = $service->metrics(auth()->user());
        $this->statusOptions = $service->statusOptions();
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
        ], $this->perPage, 'workPage');

        return view('livewire.my-work.index', [
            'workGroups' => $page['groups'],
            'workPaginator' => $page['paginator'],
            'visibleTaskCount' => $page['visibleTaskCount'],
            'searchNeedsMoreCharacters' => trim($this->search) !== '' && mb_strlen(trim($this->search)) < 2,
        ]);
    }
}
