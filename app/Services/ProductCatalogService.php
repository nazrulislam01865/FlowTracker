<?php

namespace App\Services;

use App\Models\MasterRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Canonical read-side for the Product catalogue.
 *
 * Product Categories are deliberately never returned from this service. They
 * may only participate as a parent/filter for a Product record. Keeping this
 * in one service prevents Create Order/Inquiry selectors from accidentally
 * falling back to a category master-data source.
 */
class ProductCatalogService
{
    public function workspaceId(): int
    {
        return app(MasterDataService::class)->workspaceId();
    }

    public function activeProductsQuery(): Builder
    {
        // IMPORTANT: use the exact same canonical source as Master Data > Products.
        // In this project Products are stored in master_records with type=product;
        // product_category rows are a different master-data type and can never be
        // returned by this query.
        return app(MasterDataService::class)
            ->query('product', '', ['status' => 'active'])
            ->where('master_records.type', 'product')
            ->where('master_records.status', 'active');
    }

    public function searchForOrderCreation(string $search = '', ?int $categoryId = null, int $limit = 3): Collection
    {
        $search = trim($search);
        $categoryId = max(0, (int) $categoryId);
        $limit = max(1, min(100, $limit));

        return $this->filteredOrderProductQuery($search, $categoryId)
            ->with(['parent' => fn ($parent) => $parent
                ->where('type', 'product_category')
                ->select(['id', 'name', 'status', 'type'])])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'type', 'parent_id', 'name', 'code', 'description', 'metadata', 'status'])
            // Defense in depth: even if this query is changed later, a
            // Product Category can never be handed to the Create Order view.
            ->filter(fn (MasterRecord $record): bool => $record->type === 'product')
            ->values();
    }

    public function orderSearchCount(string $search = '', ?int $categoryId = null): int
    {
        return $this->filteredOrderProductQuery(trim($search), max(0, (int) $categoryId))->count();
    }


    public function mainCategories(): Collection
    {
        $catalogued = MasterRecord::query()
            ->forWorkspace($this->workspaceId())
            ->ofType('product_main_category')
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        $legacy = MasterRecord::query()
            ->forWorkspace($this->workspaceId())
            ->ofType('product')
            ->with('parent:id,metadata,name')
            ->get(['id', 'parent_id', 'metadata'])
            ->map(fn (MasterRecord $product) => $product->productMainCategory());

        return $catalogued
            ->concat($legacy)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => mb_strtolower((string) $value))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    public function activeCount(): int
    {
        return $this->activeProductsQuery()->count();
    }

    public function selectedProducts(iterable $ids): Collection
    {
        $ids = collect($ids)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) return collect();

        return $this->activeProductsQuery()
            ->whereIn('id', $ids)
            ->with(['parent' => fn ($parent) => $parent
                ->where('type', 'product_category')
                ->select(['id', 'name', 'status', 'type'])])
            ->get(['id', 'type', 'parent_id', 'name', 'code', 'description', 'metadata', 'status'])
            ->filter(fn (MasterRecord $record): bool => $record->type === 'product')
            ->keyBy('id');
    }

    public function findActiveProductOrFail(int $productId): MasterRecord
    {
        return $this->activeProductsQuery()
            ->with(['parent' => fn ($parent) => $parent
                ->where('type', 'product_category')
                ->select(['id', 'name', 'status', 'type'])])
            ->findOrFail($productId);
    }

    private function filteredOrderProductQuery(string $search, int $categoryId): Builder
    {
        return $this->activeProductsQuery()
            ->when($categoryId > 0, fn (Builder $query) => $query
                ->where('parent_id', $categoryId)
                ->whereHas('parent', fn (Builder $parent) => $parent
                    ->where('type', 'product_category')
                    ->where('status', 'active')))
            // The product search box searches PRODUCTS only. Product Category is
            // intentionally not part of the text search; categories are available
            // exclusively through the separate category filter beside the box.
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $match) use ($search): void {
                $match->whereLike('master_records.name', '%'.$search.'%')
                    ->orWhereLike('master_records.code', '%'.$search.'%')
                    ->orWhereLike('master_records.metadata->reference_code', '%'.$search.'%');
            }));
    }
}
