<?php

namespace App\Livewire\TaskPackSetup;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Livewire\Concerns\RefreshesFromWorkspace;

use App\Models\MasterRecord;
use App\Models\TaskPack;
use App\Models\User;
use App\Services\FilterOptionService;
use App\Services\MasterDataService;
use App\Services\TaskPackService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    use RefreshesFromWorkspace;
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
                ->with([
                    'items.defaultAssignee:id,name',
                    'items.defaultDepartment:id,name',
                    'items.documentCategory:id,name',
                ])
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
                'default_assignee_label' => (string) ($item->defaultAssignee?->name ?: 'Unassigned'),
                'default_department_id' => $item->default_department_id,
                'default_department_label' => (string) ($item->defaultDepartment?->name ?: 'No department default'),
                'priority_id' => $item->priority_id,
                'document_category_id' => $item->document_category_id,
                'document_category_label' => (string) ($item->documentCategory?->name ?: 'No task-specific file'),
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

    public function setTaskPackAssignee(string $property, mixed $value): void
    {
        abort_unless(auth()->user()?->canModule('taskpacks', $this->taskPackId ? 'edit' : 'create'), 403);
        abort_unless(preg_match('/^tasks\.(\d+)\.default_assignee_id$/', $property, $matches) === 1, 422);

        $index = (int) $matches[1];
        abort_unless(array_key_exists($index, $this->tasks), 422);

        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            $this->tasks[$index]['default_assignee_id'] = null;
            $this->tasks[$index]['default_assignee_label'] = 'Unassigned';
            $this->resetValidation("tasks.$index.default_assignee_id");
            return;
        }

        abort_unless(ctype_digit($raw), 422);
        $assignee = User::query()->where('is_active', true)->findOrFail((int) $raw);

        $this->tasks[$index]['default_assignee_id'] = (int) $assignee->id;
        $this->tasks[$index]['default_assignee_label'] = (string) $assignee->name;
        $this->resetValidation("tasks.$index.default_assignee_id");
    }

    public function setTaskPackDepartment(string $property, mixed $value): void
    {
        abort_unless(auth()->user()?->canModule('taskpacks', $this->taskPackId ? 'edit' : 'create'), 403);
        abort_unless(preg_match('/^tasks\.(\d+)\.default_department_id$/', $property, $matches) === 1, 422);

        $index = (int) $matches[1];
        abort_unless(array_key_exists($index, $this->tasks), 422);

        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            $this->tasks[$index]['default_department_id'] = null;
            $this->tasks[$index]['default_department_label'] = 'No department default';
            $this->resetValidation("tasks.$index.default_department_id");
            return;
        }

        abort_unless(ctype_digit($raw), 422);

        $department = MasterRecord::query()
            ->where('workspace_id', app(TaskPackService::class)->workspaceId())
            ->where('type', 'department')
            ->where('status', 'active')
            ->findOrFail((int) $raw);

        $this->tasks[$index]['default_department_id'] = (int) $department->id;
        $this->tasks[$index]['default_department_label'] = (string) $department->name;
        $this->resetValidation("tasks.$index.default_department_id");
    }

    public function setTaskPackDocumentCategory(string $property, mixed $value): void
    {
        abort_unless(auth()->user()?->canModule('taskpacks', $this->taskPackId ? 'edit' : 'create'), 403);
        abort_unless(preg_match('/^tasks\.(\d+)\.document_category_id$/', $property, $matches) === 1, 422);

        $index = (int) $matches[1];
        abort_unless(array_key_exists($index, $this->tasks), 422);

        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            $this->tasks[$index]['document_category_id'] = null;
            $this->tasks[$index]['document_category_label'] = 'No task-specific file';
            $this->resetValidation("tasks.$index.document_category_id");
            return;
        }

        abort_unless(ctype_digit($raw), 422);

        $documentCategory = MasterRecord::query()
            ->where('workspace_id', app(TaskPackService::class)->workspaceId())
            ->where('type', 'document_category')
            ->where('status', 'active')
            ->findOrFail((int) $raw);

        $this->tasks[$index]['document_category_id'] = (int) $documentCategory->id;
        $this->tasks[$index]['document_category_label'] = (string) $documentCategory->name;
        $this->resetValidation("tasks.$index.document_category_id");
    }

    public function removeTask(int $index): void
    {
        if (!array_key_exists($index, $this->tasks)) return;
        if (!empty($this->tasks[$index]['id'])) {
            abort_unless(auth()->user()?->canModule('taskpacks', 'delete'), 403);
        }
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
            'default_assignee_label' => 'Unassigned',
            'default_department_id' => null,
            'default_department_label' => 'No department default',
            'priority_id' => null,
            'document_category_id' => null,
            'document_category_label' => 'No task-specific file',
            'due_offset_days' => 1,
            'is_required' => true,
        ];
    }

    public function render()
    {
        $master = app(MasterDataService::class);
        $user = auth()->user();

        $assigneeFilterOptions = $this->optionsReady
            ? app(FilterOptionService::class)->options($user, 'users', 'task-pack-setup', '', null, 5)
            : collect();

        $departmentFilterOptions = $this->optionsReady
            ? app(FilterOptionService::class)->options($user, 'department-records', 'task-pack-setup', '', null, 5)
            : collect();

        $documentFilterOptions = $this->optionsReady
            ? app(FilterOptionService::class)->options($user, 'document-category-records', 'task-pack-setup', '', null, 5)
            : collect();

        return view('livewire.task-pack-setup.form', [
            'assigneeFilterOptions' => $assigneeFilterOptions,
            'departmentFilterOptions' => $departmentFilterOptions,
            'documentFilterOptions' => $documentFilterOptions,
            'priorities' => $this->optionsReady ? $master->active('priority') : collect(),
            'canDeleteTaskPack' => (bool) ($user?->canModule('taskpacks', 'delete')),
        ]);
    }
}
