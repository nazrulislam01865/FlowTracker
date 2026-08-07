<?php

namespace App\Livewire\TaskPackSetup;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\TaskPack;
use App\Models\User;
use App\Services\MasterDataService;
use App\Services\TaskPackService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    use UsesPagePlaceholder;
    public ?int $taskPackId = null;
    public string $packCode = '';
    public string $packName = '';
    public string $packDescription = '';
    public string $packStatus = 'active';
    public array $tasks = [];
    public bool $optionsReady = false;

    public function mount(?int $taskPackId = null): void
    {
        $this->taskPackId = $taskPackId;
        if ($taskPackId) {
            $pack = TaskPack::query()
                ->where('workspace_id', app(TaskPackService::class)->workspaceId())
                ->where('is_snapshot', false)
                ->with('items')
                ->findOrFail($taskPackId);

            $this->packCode = (string) $pack->code;
            $this->packName = (string) $pack->name;
            $this->packDescription = (string) $pack->description;
            $this->packStatus = $pack->is_active ? 'active' : 'inactive';
            $this->tasks = $pack->items->map(fn ($item) => [
                'id' => $item->id,
                'title' => (string) $item->title,
                'description' => (string) $item->description,
                'default_assignee_id' => $item->default_assignee_id,
                'default_department_id' => $item->default_department_id,
                'priority_id' => $item->priority_id,
                'document_category_id' => $item->document_category_id,
                'due_offset_days' => (int) $item->due_offset_days,
                'is_required' => (bool) $item->is_required,
            ])->values()->all();
        } else {
            $this->packCode = app(TaskPackService::class)->nextCode();
            $this->tasks = [$this->blankTask()];
        }

        if (!$this->tasks) {
            $this->tasks = [$this->blankTask()];
        }
    }

    public function addTask(): void
    {
        $this->tasks[] = $this->blankTask();
    }

    public function loadTaskPackOptions(): void
    {
        $this->optionsReady = true;
    }

    public function removeTask(int $index): void
    {
        if (!array_key_exists($index, $this->tasks)) return;
        array_splice($this->tasks, $index, 1);
        $this->tasks = array_values($this->tasks);
        if (!$this->tasks) $this->tasks[] = $this->blankTask();
        $this->resetValidation();
    }

    public function moveTask(int $index, int $direction): void
    {
        $target = $index + $direction;
        if (!isset($this->tasks[$index], $this->tasks[$target])) return;
        [$this->tasks[$index], $this->tasks[$target]] = [$this->tasks[$target], $this->tasks[$index]];
        $this->tasks = array_values($this->tasks);
    }

    public function save(): void
    {
        if (!$this->optionsReady) {
            $this->addError('options', 'Please wait for Task Pack options to finish loading.');
            return;
        }

        $workspaceId = app(TaskPackService::class)->workspaceId();
        $masterRule = fn (string $type) => Rule::exists('master_records', 'id')->where(
            fn ($query) => $query->where('workspace_id', $workspaceId)->where('type', $type)->whereNull('deleted_at')
        );

        $data = $this->validate([
            'packName' => ['required','string','max:255'],
            'packDescription' => ['nullable','string','max:5000'],
            'packStatus' => ['required','in:active,inactive'],
            'tasks' => ['required','array','min:1'],
            'tasks.*.id' => ['nullable','integer'],
            'tasks.*.title' => ['required','string','max:255'],
            'tasks.*.description' => ['nullable','string','max:5000'],
            'tasks.*.default_assignee_id' => ['nullable','integer','exists:users,id'],
            'tasks.*.default_department_id' => ['nullable','integer', $masterRule('department')],
            'tasks.*.priority_id' => ['nullable','integer', $masterRule('priority')],
            'tasks.*.document_category_id' => ['nullable','integer', $masterRule('document_category')],
            'tasks.*.due_offset_days' => ['nullable','integer','min:0','max:3650'],
            'tasks.*.is_required' => ['boolean'],
        ]);

        $savedPack = app(TaskPackService::class)->savePackWithItems([
            'code' => $this->packCode,
            'name' => $data['packName'],
            'description' => $data['packDescription'],
            'is_active' => $data['packStatus'] === 'active',
        ], $data['tasks'], $this->taskPackId);

        session()->flash('success', $this->taskPackId ? 'Task Pack updated.' : 'Task Pack created.');
        app(\App\Services\NotificationService::class)->notifyUser(
            auth()->user(),
            $this->taskPackId ? 'Task Pack updated' : 'Task Pack created',
            $savedPack->name.' · '.$savedPack->items->count().' configured task'.($savedPack->items->count() === 1 ? '' : 's').'.',
            'update',
            null,
            null,
            auth()->user(),
        );
        $this->redirectRoute('task-pack.setup', navigate: true);
    }

    public function cancel(): void
    {
        $this->redirectRoute('task-pack.setup', navigate: true);
    }

    private function blankTask(): array
    {
        return [
            'id' => null,
            'title' => '',
            'description' => '',
            'default_assignee_id' => null,
            'default_department_id' => null,
            'priority_id' => null,
            'document_category_id' => null,
            'due_offset_days' => 1,
            'is_required' => true,
        ];
    }

    public function render()
    {
        $master = app(MasterDataService::class);
        return view('livewire.task-pack-setup.form', [
            'users' => $this->optionsReady
                ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
                : collect(),
            'departments' => $this->optionsReady ? $master->active('department') : collect(),
            'priorities' => $this->optionsReady ? $master->active('priority') : collect(),
            'documentCategories' => $this->optionsReady ? $master->active('document_category') : collect(),
        ]);
    }
}
