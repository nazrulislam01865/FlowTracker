<?php

namespace App\Livewire\TaskPackSetup;

use App\Models\MasterRecord;
use App\Models\TaskPack;
use App\Models\User;
use App\Services\MasterDataService;
use App\Services\TaskPackService;
use Livewire\Component;

class Form extends Component
{
    public ?int $taskPackId = null;
    public string $packCode = '';
    public string $packName = '';
    public string $packDescription = '';
    public string $packStatus = 'active';
    public array $tasks = [];

    public function mount(?int $taskPackId = null): void
    {
        $this->taskPackId = $taskPackId;
        app(TaskPackService::class)->syncLegacy();
        app(MasterDataService::class)->syncLegacy();

        if ($taskPackId) {
            $pack = TaskPack::query()
                ->where('workspace_id', app(TaskPackService::class)->workspaceId())
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
        $data = $this->validate([
            'packName' => ['required','string','max:255'],
            'packDescription' => ['nullable','string','max:5000'],
            'packStatus' => ['required','in:active,inactive'],
            'tasks' => ['required','array','min:1'],
            'tasks.*.id' => ['nullable','integer'],
            'tasks.*.title' => ['required','string','max:255'],
            'tasks.*.description' => ['nullable','string','max:5000'],
            'tasks.*.default_assignee_id' => ['nullable','integer','exists:users,id'],
            'tasks.*.default_department_id' => ['nullable','integer','exists:master_records,id'],
            'tasks.*.priority_id' => ['nullable','integer','exists:master_records,id'],
            'tasks.*.document_category_id' => ['nullable','integer','exists:master_records,id'],
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
        $workspaceId = $master->workspaceId();

        return view('livewire.task-pack-setup.form', [
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'departments' => MasterRecord::query()->where('workspace_id', $workspaceId)->where('type', 'department')->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(),
            'priorities' => MasterRecord::query()->where('workspace_id', $workspaceId)->where('type', 'priority')->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(),
            'documentCategories' => MasterRecord::query()->where('workspace_id', $workspaceId)->where('type', 'document_category')->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
