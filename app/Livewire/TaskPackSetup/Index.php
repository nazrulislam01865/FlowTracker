<?php

namespace App\Livewire\TaskPackSetup;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\MasterRecord;
use App\Models\TaskPack;
use App\Models\TaskPackItem;
use App\Models\User;
use App\Models\WorkflowPhase;
use App\Services\MasterDataService;
use App\Services\TaskPackService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;
    public ?int $selectedPackId = null;
    public bool $showPackModal = false;
    public bool $showItemModal = false;
    public ?int $editPackId = null;
    public ?int $editItemId = null;

    public string $packCode = '';
    public string $packName = '';
    public string $packDescription = '';
    public bool $packActive = true;

    public string $itemTitle = '';
    public string $itemDescription = '';
    public ?int $defaultAssigneeId = null;
    public ?int $defaultDepartmentId = null;
    public ?int $priorityId = null;
    public ?int $documentCategoryId = null;
    public int $dueOffsetDays = 1;
    public bool $itemRequired = true;

    public function mount(): void
    {
        $this->selectedPackId = app(TaskPackService::class)->all()->first()?->id;
    }

    public function selectPack(int $id): void { $this->selectedPackId = $id; $this->resetValidation(); }

    public function openPack(?int $id = null): void
    {
        $this->showPackModal = true; $this->editPackId = $id; $this->resetValidation();
        if ($id) {
            $p = TaskPack::findOrFail($id);
            $this->packCode = (string) $p->code; $this->packName = $p->name; $this->packDescription = (string) $p->description; $this->packActive = (bool) $p->is_active;
        } else {
            $this->reset(['packCode','packName','packDescription']); $this->packActive = true;
        }
    }

    public function closePack(): void { $this->showPackModal = false; $this->resetValidation(); }

    public function savePack(): void
    {
        $data = $this->validate([
            'packCode' => ['required','string','max:40'], 'packName' => ['required','string','max:255'], 'packDescription' => ['nullable','string','max:5000'], 'packActive' => ['boolean'],
        ]);
        $pack = app(TaskPackService::class)->savePack(['code'=>$data['packCode'],'name'=>$data['packName'],'description'=>$data['packDescription'],'is_active'=>$data['packActive']], $this->editPackId);
        $this->selectedPackId = $pack->id; $this->showPackModal = false; session()->flash('success','Task Pack saved.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Task Pack updated', $pack->name.' was saved.', 'update', null, null, auth()->user());
    }

    public function togglePack(int $id): void
    {
        try { $pack = TaskPack::findOrFail($id); app(TaskPackService::class)->togglePack($id); session()->flash('success','Task Pack status updated.'); app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Task Pack status updated', $pack->name.' status was changed.', 'update', null, null, auth()->user()); }
        catch (ValidationException $e) { $this->addError('pack', collect($e->errors())->flatten()->first()); }
    }

    public function deletePack(int $id): void
    {
        try {
            app(TaskPackService::class)->deletePack($id);
            $this->selectedPackId = app(TaskPackService::class)->all()->first()?->id;
            session()->flash('success','Task Pack deleted.');
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Task Pack deleted', 'A Task Pack was deleted.', 'update', null, null, auth()->user());
        } catch (ValidationException $e) { $this->addError('pack', collect($e->errors())->flatten()->first()); }
    }

    public function openItem(?int $id = null): void
    {
        abort_unless($this->selectedPackId, 422);
        $this->showItemModal = true; $this->editItemId = $id; $this->resetValidation();
        if ($id) {
            $i = TaskPackItem::where('task_pack_id',$this->selectedPackId)->findOrFail($id);
            $this->itemTitle=$i->title; $this->itemDescription=(string)$i->description; $this->defaultAssigneeId=$i->default_assignee_id; $this->defaultDepartmentId=$i->default_department_id; $this->priorityId=$i->priority_id; $this->documentCategoryId=$i->document_category_id; $this->dueOffsetDays=(int)$i->due_offset_days; $this->itemRequired=(bool)$i->is_required;
        } else {
            $this->reset(['itemTitle','itemDescription','defaultAssigneeId','defaultDepartmentId','priorityId','documentCategoryId']); $this->dueOffsetDays=1; $this->itemRequired=true;
        }
    }

    public function closeItem(): void { $this->showItemModal=false; $this->resetValidation(); }

    public function saveItem(): void
    {
        $data = $this->validate([
            'itemTitle'=>['required','string','max:255'], 'itemDescription'=>['nullable','string','max:5000'], 'defaultAssigneeId'=>['nullable','exists:users,id'],
            'defaultDepartmentId'=>['nullable','exists:master_records,id'], 'priorityId'=>['nullable','exists:master_records,id'], 'documentCategoryId'=>['nullable','exists:master_records,id'],
            'dueOffsetDays'=>['required','integer','min:0','max:3650'], 'itemRequired'=>['boolean'],
        ]);
        $pack=TaskPack::findOrFail($this->selectedPackId);
        app(TaskPackService::class)->saveItem($pack,[
            'title'=>$data['itemTitle'],'description'=>$data['itemDescription'],'default_assignee_id'=>$data['defaultAssigneeId'],'default_department_id'=>$data['defaultDepartmentId'],
            'priority_id'=>$data['priorityId'],'document_category_id'=>$data['documentCategoryId'],'due_offset_days'=>$data['dueOffsetDays'],'is_required'=>$data['itemRequired'],
        ],$this->editItemId);
        $this->showItemModal=false; session()->flash('success','Task Pack item saved.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Task Pack task updated', $data['itemTitle'].' was saved.', 'update', null, null, auth()->user());
    }

    public function deleteItem(int $id): void
    {
        try { app(TaskPackService::class)->deleteItem($id); session()->flash('success','Task Pack item deleted.'); app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Task Pack task deleted', 'A Task Pack task was deleted.', 'update', null, null, auth()->user()); }
        catch (ValidationException $e) { $this->addError('item', collect($e->errors())->flatten()->first()); }
    }

    public function moveItem(int $id, int $direction): void { app(TaskPackService::class)->moveItem($id,$direction); }

    public function render()
    {
        $service=app(TaskPackService::class); $packs=$service->all();
        if (!$this->selectedPackId && $packs->isNotEmpty()) $this->selectedPackId=$packs->first()->id;
        $selected=$packs->firstWhere('id',$this->selectedPackId);
        $master=app(MasterDataService::class); $master->syncLegacy(); $workspaceId=$master->workspaceId();
        $packIds = $packs->pluck('id');
        return view('livewire.task-pack-setup.index',[
            'packs'=>$packs,'selected'=>$selected,
            'totalPacks'=>$packs->count(),
            'activePacks'=>$packs->where('is_active',true)->count(),
            'configuredTasks'=>$packs->sum(fn($pack) => $pack->items->count()),
            'mappedPhases'=>$packIds->isEmpty() ? 0 : WorkflowPhase::whereIn('task_pack_id',$packIds)->count(),
            'users'=>User::where('is_active',true)->orderBy('name')->get(),
            'departments'=>MasterRecord::where('workspace_id',$workspaceId)->where('type','department')->where('status','active')->orderBy('sort_order')->orderBy('name')->get(),
            'priorities'=>MasterRecord::where('workspace_id',$workspaceId)->where('type','priority')->where('status','active')->orderBy('sort_order')->orderBy('name')->get(),
            'documentCategories'=>MasterRecord::where('workspace_id',$workspaceId)->where('type','document_category')->where('status','active')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
