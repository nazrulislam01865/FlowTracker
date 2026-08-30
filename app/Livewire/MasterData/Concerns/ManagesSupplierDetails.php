<?php

namespace App\Livewire\MasterData\Concerns;

use App\Actions\MasterData\SaveMasterRecordAction;
use App\Models\MasterRecord;
use App\Services\MasterDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;

trait ManagesSupplierDetails
{
    #[Url(as: 'supplier', history: true)]
    public ?int $supplierViewId = null;

    #[Url(as: 'edit_supplier', history: true)]
    public ?int $supplierEditId = null;

    public string $supplierEditContactPerson = '';
    public string $supplierEditEmail = '';
    public string $supplierEditPhone = '';
    public string $supplierEditStatus = 'active';

    public function mountSupplierDetails(): void
    {
        if ($this->group !== 'supplier' || $this->supplierCreateMode) {
            return;
        }

        $requestedEditId = request()->integer('edit_supplier');
        $requestedViewId = request()->integer('supplier');
        if ($requestedEditId > 0) {
            $this->supplierEditId = $requestedEditId;
        } elseif ($requestedViewId > 0) {
            $this->supplierViewId = $requestedViewId;
        }

        if ($this->supplierEditId) {
            $this->authorizeGroupAction('edit', 'supplier');
            $this->supplierViewId = null;
            $this->recordsReady = false;
            $this->hydrateSupplierEditor($this->supplierEditId);
            return;
        }

        if ($this->supplierViewId) {
            $this->authorizeGroupAction('view', 'supplier');
            $this->supplierRecord($this->supplierViewId);
            $this->recordsReady = false;
        }
    }

    public function openSupplier(int $id): void
    {
        abort_unless($this->group === 'supplier', 404);
        $this->authorizeGroupAction('view', 'supplier');
        $this->supplierRecord($id);

        $this->supplierEditId = null;
        $this->supplierViewId = $id;
        $this->recordsReady = false;
        $this->resetValidation();
    }

    public function editSupplier(int $id): void
    {
        abort_unless($this->group === 'supplier', 404);
        $this->authorizeGroupAction('edit', 'supplier');

        $this->supplierViewId = null;
        $this->supplierEditId = $id;
        $this->recordsReady = false;
        $this->hydrateSupplierEditor($id);
    }

    public function cancelSupplierEdit(): void
    {
        $id = $this->supplierEditId;
        $this->resetValidation();

        $this->redirectRoute('master-data', array_filter([
            'group' => 'supplier',
            'supplier' => $id,
        ]), navigate: true);
    }

    public function saveSupplier(): void
    {
        abort_unless($this->group === 'supplier' && $this->supplierEditId, 404);
        $this->authorizeGroupAction('edit', 'supplier');

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $supplier = $this->supplierRecord($this->supplierEditId);

        $data = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_records', 'name')
                    ->ignore($supplier->id)
                    ->where(fn ($query) => $query
                        ->where('workspace_id', $workspaceId)
                        ->where('type', 'supplier')
                        ->whereNull('deleted_at')),
            ],
            'supplierEditContactPerson' => ['nullable', 'string', 'max:255'],
            'supplierEditEmail' => ['nullable', 'email:rfc', 'max:255'],
            'supplierEditPhone' => ['nullable', 'string', 'max:80'],
            'supplierEditStatus' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'name.required' => 'Supplier name is required.',
            'name.unique' => 'A supplier with this name already exists.',
            'supplierEditEmail.email' => 'Enter a valid email address.',
        ]);

        $metadata = (array) ($supplier->metadata ?? []);
        foreach ([
            'contact_person' => trim((string) $data['supplierEditContactPerson']),
            'email' => trim((string) $data['supplierEditEmail']),
            'phone' => trim((string) $data['supplierEditPhone']),
        ] as $key => $value) {
            if ($value === '') {
                unset($metadata[$key]);
            } else {
                $metadata[$key] = $value;
            }
        }

        $supplier = app(SaveMasterRecordAction::class)->execute('supplier', [
            'code' => trim((string) $supplier->code) !== ''
                ? $supplier->code
                : app(MasterDataService::class)->nextCode('supplier'),
            'name' => trim((string) $data['name']),
            'description' => $supplier->description,
            'color' => null,
            'parent_id' => null,
            'status' => (string) $data['supplierEditStatus'],
            'sort_order' => (int) $supplier->sort_order,
            'metadata' => $metadata,
        ], (int) $supplier->id);

        session()->flash('success', $supplier->name.' updated successfully.');
        $this->redirectRoute('master-data', [
            'group' => 'supplier',
            'supplier' => $supplier->id,
        ], navigate: true);
    }

    protected function supplierRecord(int $id): MasterRecord
    {
        return MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('supplier')
            ->findOrFail($id);
    }

    protected function hydrateSupplierEditor(int $id): void
    {
        $supplier = $this->supplierRecord($id);

        $this->name = (string) $supplier->name;
        $this->supplierEditContactPerson = trim((string) data_get($supplier->metadata, 'contact_person'));
        $this->supplierEditEmail = trim((string) data_get($supplier->metadata, 'email'));
        $this->supplierEditPhone = trim((string) data_get($supplier->metadata, 'phone'));
        $this->supplierEditStatus = $supplier->status === 'inactive' ? 'inactive' : 'active';
        $this->resetValidation();
    }

    /**
     * Return products currently associated with one supplier using both the
     * normalized pivot and the legacy metadata formats still supported by FlowTrack.
     *
     * @return Collection<int,MasterRecord>
     */
    protected function supplierDetailProducts(int $workspaceId, MasterRecord $supplier): Collection
    {
        $supplierId = (int) $supplier->id;
        $reverseIds = collect($this->normaliseSupplierProductIds(
            data_get($supplier->metadata, 'product_ids', data_get($supplier->metadata, 'assigned_product_ids', []))
        ));
        $rawCodes = data_get($supplier->metadata, 'product_codes', data_get($supplier->metadata, 'assigned_product_codes', []));
        $reverseCodes = collect(is_array($rawCodes)
            ? $rawCodes
            : (preg_split('/[\s,;|]+/', trim((string) $rawCodes), -1, PREG_SPLIT_NO_EMPTY) ?: []))
            ->map(fn ($code) => strtoupper(trim((string) $code)))
            ->filter()
            ->unique()
            ->values();

        $query = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->with('parent:id,name')
            ->where(function ($match) use ($supplierId, $workspaceId, $reverseIds, $reverseCodes): void {
                $match->where('metadata->supplier_id', $supplierId)
                    ->orWhere('metadata->default_supplier_id', $supplierId)
                    ->orWhereJsonContains('metadata->supplier_ids', $supplierId);

                if (Schema::hasTable('product_supplier_links')) {
                    $match->orWhereExists(function ($link) use ($supplierId, $workspaceId): void {
                        $link->selectRaw('1')
                            ->from('product_supplier_links')
                            ->whereColumn('product_supplier_links.product_id', 'master_records.id')
                            ->where('product_supplier_links.workspace_id', $workspaceId)
                            ->where('product_supplier_links.supplier_id', $supplierId);
                    });
                }

                if ($reverseIds->isNotEmpty()) {
                    $match->orWhereIn('id', $reverseIds->all());
                }

                if ($reverseCodes->isNotEmpty()) {
                    $displayIds = $reverseCodes
                        ->map(function (string $code): ?int {
                            return preg_match('/^PRD[-\s]*0*(\d+)$/i', $code, $matches)
                                ? (int) $matches[1]
                                : null;
                        })
                        ->filter()
                        ->values();

                    $match->orWhereIn('code', $reverseCodes->all());
                    if ($displayIds->isNotEmpty()) {
                        $match->orWhereIn('id', $displayIds->all());
                    }
                }
            });

        return $query
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'parent_id', 'metadata', 'status', 'updated_at'])
            ->unique('id')
            ->values();
    }
}
