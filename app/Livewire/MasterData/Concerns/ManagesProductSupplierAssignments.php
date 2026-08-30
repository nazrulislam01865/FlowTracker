<?php

namespace App\Livewire\MasterData\Concerns;

use App\Models\MasterRecord;
use App\Services\MasterDataService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

trait ManagesProductSupplierAssignments
{
    public function openProductSupplierAssignment(): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);
        abort_if($this->productSelectionCount() < 1, 422, 'Select at least one product.');

        $this->bulkProductSupplierId = null;
        $this->resetValidation('bulkProductSupplierId');
        $this->bulkProductPanel = 'supplier';
    }

    public function openProductSupplierAssignmentFor(int $productId): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $product = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->findOrFail($productId, ['id']);

        $this->clearProductSelection();
        $this->selectedProductIds = [(int) $product->id];
        $this->bulkProductSupplierId = null;
        $this->bulkProductPanel = 'supplier';
        $this->resetValidation('bulkProductSupplierId');
    }

    public function chooseBulkProductSupplier(int $supplierId): void
    {
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $supplier = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('supplier')
            ->active()
            ->findOrFail($supplierId, ['id']);

        $this->bulkProductSupplierId = (int) $supplier->id;
        $this->resetValidation('bulkProductSupplierId');
    }

    public function applyBulkProductSupplier(): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $data = $this->validate([
            'bulkProductSupplierId' => [
                'required',
                'integer',
                Rule::exists('master_records', 'id')->where(fn ($query) => $query
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'supplier')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
        ], [
            'bulkProductSupplierId.required' => 'Choose a supplier to continue.',
        ]);

        $count = $this->productSelectionCount();
        if ($count < 1) return;

        $supplierId = (int) $data['bulkProductSupplierId'];
        $supplier = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('supplier')
            ->active()
            ->findOrFail($supplierId, ['id', 'name']);

        $newLinks = 0;
        DB::transaction(function () use ($supplierId, $workspaceId, &$newLinks): void {
            $this->selectedProductsQuery()
                ->select(['id', 'metadata'])
                ->reorder('id')
                ->chunkById(200, function ($products) use ($supplierId, $workspaceId, &$newLinks): void {
                    $pivotRows = [];

                    foreach ($products as $product) {
                        $metadata = (array) ($product->metadata ?? []);
                        $supplierIds = collect($product->productSupplierIds())
                            ->map(fn ($id) => (int) $id)
                            ->filter(fn (int $id) => $id > 0);

                        if (! $supplierIds->contains($supplierId)) {
                            $supplierIds->push($supplierId);
                            $newLinks++;
                        }

                        // Preserve the current default. If this is the first supplier,
                        // make it the default so existing Order/Inquiry logic keeps working.
                        if (! $product->productSupplierId()) {
                            $metadata['supplier_id'] = $supplierId;
                            unset($metadata['default_supplier_id']);
                        }

                        $metadata['supplier_ids'] = $supplierIds->unique()->values()->all();
                        $product->metadata = $metadata;
                        $product->save();

                        if (Schema::hasTable('product_supplier_links')) {
                            $pivotRows[] = [
                                'workspace_id' => $workspaceId,
                                'product_id' => (int) $product->id,
                                'supplier_id' => $supplierId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if ($pivotRows !== []) {
                        DB::table('product_supplier_links')->insertOrIgnore($pivotRows);
                    }
                });
        });

        app(\App\Services\WorkspaceRefreshService::class)->touch('MasterRecord:bulk-product-supplier');
        $this->bulkProductPanel = null;
        $this->clearProductSelection();
        $this->recordsReady = true;

        session()->flash(
            'success',
            $supplier->name.' linked to '.number_format($count).' '.strtolower(\Illuminate\Support\Str::plural('product', $count))
            .($newLinks < $count ? ' (existing links were kept).' : '.')
        );
    }
}
