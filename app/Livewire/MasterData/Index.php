<?php

namespace App\Livewire\MasterData;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\MasterRecord;
use App\Services\MasterDataService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use UsesPagePlaceholder;
    use WithPagination;

    public string $group = 'product_category';
    public string $search = '';
    public bool $recordsReady = false;
    public bool $showModal = false;
    public ?int $editId = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public ?int $parentId = null;
    public string $status = 'active';
    public int $sortOrder = 0;
    public string $metadataJson = '';

    public function selectGroup(string $group): void
    {
        abort_unless(array_key_exists($group, MasterDataService::LABELS), 404);
        $this->group = $group;
        $this->recordsReady = true;
        $this->search = '';
        $this->parentId = null;
        $this->resetPage('masterPage');
        $this->resetValidation();
    }

    public function open(?int $id = null): void
    {
        $this->recordsReady = true;
        $this->showModal = true;
        $this->editId = $id;
        $this->resetValidation();
        if ($id) {
            $r = MasterRecord::where('workspace_id', app(MasterDataService::class)->workspaceId())->findOrFail($id);
            abort_unless($r->type === $this->group, 404);
            $this->code = $r->code;
            $this->name = $r->name;
            $this->description = (string) $r->description;
            $this->parentId = $this->group === 'product' ? $r->parent_id : null;
            $this->status = $r->status;
            $this->sortOrder = (int) $r->sort_order;
            $this->metadataJson = $r->metadata ? json_encode($r->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
        } else {
            $this->reset(['code','name','description','parentId','metadataJson']);
            $this->status = 'active';
            $this->sortOrder = (int) MasterRecord::where('workspace_id', app(MasterDataService::class)->workspaceId())->where('type', $this->group)->max('sort_order') + 1;
        }
    }

    public function close(): void { $this->showModal = false; $this->resetValidation(); }

    public function updatedSearch(): void
    {
        $this->recordsReady = true;
        $this->resetPage('masterPage');
    }

    public function loadMasterRecords(): void
    {
        $this->recordsReady = true;
    }

    public function save(): void
    {
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $data = $this->validate([
            'code' => ['required','string','max:40'],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:5000'],
            'parentId' => $this->group === 'product'
                ? ['nullable','integer', Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'product_category')
                    ->whereNull('deleted_at'))]
                : ['nullable'],
            'status' => ['required','in:active,inactive'],
            'sortOrder' => ['required','integer','min:0','max:1000000'],
            'metadataJson' => ['nullable','string'],
        ]);

        $metadata = null;
        if (filled($data['metadataJson'])) {
            $metadata = json_decode($data['metadataJson'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($metadata)) {
                throw ValidationException::withMessages(['metadataJson' => 'Metadata must be valid JSON.']);
            }
        }

        app(MasterDataService::class)->save($this->group, [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'parent_id' => $this->group === 'product' ? $data['parentId'] : null,
            'status' => $data['status'],
            'sort_order' => $data['sortOrder'],
            'metadata' => $metadata,
        ], $this->editId);

        $this->showModal = false;
        session()->flash('success', 'Master data saved.');
        app(\App\Services\NotificationService::class)->notifyUser(
            auth()->user(),
            'Master data updated',
            ($this->name ?: $this->code).' was saved in '.(MasterDataService::LABELS[$this->group] ?? 'Master Data').'.',
            'update',
            null,
            null,
            auth()->user(),
        );
    }

    public function toggle(int $id): void
    {
        $this->recordsReady = true;
        $record = MasterRecord::where('workspace_id', app(MasterDataService::class)->workspaceId())->findOrFail($id);
        app(MasterDataService::class)->toggle($id);
        session()->flash('success', 'Master data status updated.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Master data status updated', $record->name.' status was changed.', 'update', null, null, auth()->user());
    }

    public function deleteRecord(int $id): void
    {
        $this->recordsReady = true;
        try {
            app(MasterDataService::class)->delete($id);
            $this->resetPage('masterPage');
            session()->flash('success', 'Master data record deleted.');
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Master data deleted', 'A master data record was deleted.', 'update', null, null, auth()->user());
        } catch (ValidationException $e) {
            $this->addError('record', collect($e->errors())->flatten()->first());
        }
    }

    public function render()
    {
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $summaries = MasterRecord::query()
            ->where('workspace_id', $workspaceId)
            ->selectRaw('type, count(*) as total_count')
            ->selectRaw("sum(case when status = 'active' then 1 else 0 end) as active_count")
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        return view('livewire.master-data.index', [
            'labels' => MasterDataService::LABELS,
            'rows' => $this->recordsReady
                ? $service->paginate($this->group, $this->search, 30)
                : null,
            'parents' => $this->showModal && $this->group === 'product'
                ? $service->active('product_category')
                : collect(),
            'groupCounts' => collect(MasterDataService::LABELS)->mapWithKeys(
                fn ($label, $type) => [$type => (int) ($summaries->get($type)?->total_count ?? 0)]
            ),
            'total' => (int) $summaries->sum('total_count'),
            'active' => (int) $summaries->sum('active_count'),
        ]);
    }
}
