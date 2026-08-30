<?php

namespace App\Livewire\MasterData\Concerns;

use App\Actions\MasterData\SaveMasterRecordAction;
use App\Models\MasterRecord;
use App\Services\MasterDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

trait ManagesSupplierCreation
{
    public bool $supplierCreateMode = false;
    public string $supplierContactPerson = '';
    public string $supplierEmail = '';
    public string $supplierPhone = '';
    public string $supplierCodeDraft = '';
    /** @var array<int,string> */
    public array $supplierProductCodes = [];

    public function mountSupplierCreation(): void
    {
        if ($this->group !== 'supplier' || ! request()->boolean('create')) {
            return;
        }

        $this->authorizeGroupAction('create');
        $this->supplierCreateMode = true;
        $this->recordsReady = false;
        $this->showModal = false;
        $this->resetSupplierCreateForm();
    }

    public function resetSupplierCreateForm(): void
    {
        $this->name = '';
        $this->status = 'active';
        $this->supplierContactPerson = '';
        $this->supplierEmail = '';
        $this->supplierPhone = '';
        $this->supplierCodeDraft = '';
        $this->supplierProductCodes = [];
        $this->resetValidation();
    }

    public function commitSupplierProductCodes(?string $raw = null): void
    {
        $raw ??= $this->supplierCodeDraft;
        $codes = preg_split('/[\s,;]+/', strtoupper(trim((string) $raw))) ?: [];

        $this->supplierProductCodes = collect([...$this->supplierProductCodes, ...$codes])
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->supplierCodeDraft = '';
        $this->resetValidation('supplierProductCodes');
    }

    public function removeSupplierProductCode(string $code): void
    {
        $needle = strtoupper(trim($code));
        $this->supplierProductCodes = collect($this->supplierProductCodes)
            ->reject(fn ($item) => strtoupper(trim((string) $item)) === $needle)
            ->values()
            ->all();
        $this->resetValidation('supplierProductCodes');
    }

    public function cancelSupplierCreate(): void
    {
        $this->redirectRoute('master-data', ['group' => 'supplier'], navigate: true);
    }

    public function createSupplier(): void
    {
        abort_unless($this->group === 'supplier' && $this->supplierCreateMode, 404);
        $this->authorizeGroupAction('create');
        $this->commitSupplierProductCodes();

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $data = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_records', 'name')->where(fn ($query) => $query
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'supplier')
                    ->whereNull('deleted_at')),
            ],
            'supplierContactPerson' => ['nullable', 'string', 'max:255'],
            'supplierEmail' => ['nullable', 'email:rfc', 'max:255'],
            'supplierPhone' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'supplierProductCodes' => ['array', 'max:250'],
            'supplierProductCodes.*' => ['string', 'max:80'],
        ], [
            'name.required' => 'Supplier name is required.',
            'name.unique' => 'A supplier with this name already exists.',
            'supplierEmail.email' => 'Enter a valid email address.',
        ]);

        $codeRows = $this->supplierCreateCodeRows($workspaceId);
        $invalid = $codeRows->where('valid', false)->pluck('code')->values();
        if ($invalid->isNotEmpty()) {
            $this->addError('supplierProductCodes', 'Correct or remove unknown product code: '.$invalid->first());
            return;
        }

        $supplier = DB::transaction(function () use ($workspaceId, $data, $codeRows): MasterRecord {
            $service = app(MasterDataService::class);
            $sortOrder = ((int) MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('supplier')
                ->max('sort_order')) + 1;

            $supplier = app(SaveMasterRecordAction::class)->execute('supplier', [
                'code' => $service->nextCode('supplier'),
                'name' => trim((string) $data['name']),
                'description' => null,
                'color' => null,
                'parent_id' => null,
                'status' => (string) $data['status'],
                'sort_order' => $sortOrder,
                'metadata' => array_filter([
                    'contact_person' => trim((string) $data['supplierContactPerson']),
                    'email' => trim((string) $data['supplierEmail']),
                    'phone' => trim((string) $data['supplierPhone']),
                ], fn ($value) => $value !== ''),
            ]);

            $productIds = $codeRows->where('valid', true)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique();
            if ($productIds->isNotEmpty()) {
                $pivotRows = [];
                MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->whereIn('id', $productIds->all())
                    ->get(['id', 'metadata'])
                    ->each(function (MasterRecord $product) use ($supplier, $workspaceId, &$pivotRows): void {
                        $metadata = (array) ($product->metadata ?? []);
                        $supplierIds = collect($product->productSupplierIds())
                            ->push((int) $supplier->id)
                            ->map(fn ($id) => (int) $id)
                            ->filter(fn (int $id) => $id > 0)
                            ->unique()
                            ->values();

                        // Keep the existing default supplier. The newly-created supplier
                        // becomes default only when the product did not have one.
                        if (! $product->productSupplierId()) {
                            $metadata['supplier_id'] = (int) $supplier->id;
                            unset($metadata['default_supplier_id']);
                        }

                        $metadata['supplier_ids'] = $supplierIds->all();
                        $product->metadata = $metadata;
                        $product->save();

                        if (Schema::hasTable('product_supplier_links')) {
                            $pivotRows[] = [
                                'workspace_id' => $workspaceId,
                                'product_id' => (int) $product->id,
                                'supplier_id' => (int) $supplier->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    });

                if ($pivotRows !== []) {
                    DB::table('product_supplier_links')->insertOrIgnore($pivotRows);
                }
            }

            return $supplier;
        });

        $assigned = $codeRows->where('valid', true)->count();
        session()->flash('success', $supplier->name.' created'.($assigned ? ' with '.$assigned.' assigned product'.($assigned === 1 ? '' : 's').'.' : '.'));
        $this->redirectRoute('master-data', ['group' => 'supplier'], navigate: true);
    }

    /**
     * @return Collection<int,array{code:string,valid:bool,product_id:?int,name:string,category:string,has_supplier:bool}>
     */
    protected function supplierCreateCodeRows(?int $workspaceId = null): Collection
    {
        $workspaceId ??= app(MasterDataService::class)->workspaceId();
        $codes = collect($this->supplierProductCodes)
            ->map(fn ($code) => strtoupper(trim((string) $code)))
            ->filter()
            ->unique()
            ->values();

        if ($codes->isEmpty()) {
            return collect();
        }

        $displayIds = $codes->map(function (string $code): ?int {
            if (! preg_match('/^PRD[-\s]*0*(\d+)$/i', $code, $matches)) {
                return null;
            }
            return (int) $matches[1];
        })->filter()->values();

        $products = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->with('parent:id,name')
            ->where(function ($query) use ($displayIds, $codes): void {
                if ($displayIds->isNotEmpty()) {
                    $query->whereIn('id', $displayIds->all());
                }
                $rawCodes = $codes->all();
                if ($rawCodes !== []) {
                    $displayIds->isNotEmpty()
                        ? $query->orWhereIn('code', $rawCodes)
                        : $query->whereIn('code', $rawCodes);
                }
            })
            ->get(['id', 'code', 'name', 'parent_id', 'metadata']);

        $byCode = collect();
        foreach ($products as $product) {
            $byCode->put(strtoupper((string) $product->code), $product);
            $byCode->put(strtoupper($product->productDisplayCode()), $product);
            $byCode->put('PRD-'.(int) $product->id, $product);
        }

        return $codes->map(function (string $code) use ($byCode): array {
            /** @var MasterRecord|null $product */
            $product = $byCode->get($code);
            return [
                'code' => $code,
                'valid' => (bool) $product,
                'product_id' => $product ? (int) $product->id : null,
                'name' => $product ? (string) $product->name : '',
                'category' => $product ? (string) ($product->parent?->name ?: 'Uncategorised') : '',
                'has_supplier' => (bool) ($product && $product->productSupplierIds() !== []),
            ];
        })->values();
    }

    /** @return Collection<int,string> */
    protected function supplierCreateExamples(?int $workspaceId = null): Collection
    {
        $workspaceId ??= app(MasterDataService::class)->workspaceId();
        return MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(3)
            ->get(['id', 'code'])
            ->map(fn (MasterRecord $product) => $product->productDisplayCode())
            ->values();
    }
}
