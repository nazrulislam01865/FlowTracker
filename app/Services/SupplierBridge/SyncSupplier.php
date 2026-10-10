<?php

namespace App\Services\SupplierBridge;

use App\Models\MasterRecord;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/** Supplier identity and approved product catalogue only: never client/customer data. */
final class SyncSupplier
{
    public function __construct(private readonly PortalClient $portal) {}

    public function sync(int $supplierId): bool
    {
        $supplier = MasterRecord::query()->ofType('supplier')->findOrFail($supplierId);
        $id = (int) $supplier->id;
        $workspace = (int) $supplier->workspace_id;
        $metadata = (array) ($supplier->metadata ?? []);
        $reverseIds = collect(data_get($metadata, 'product_ids', data_get($metadata, 'assigned_product_ids', [])))
            ->filter(fn ($value) => is_numeric($value) && (int) $value > 0)->map(fn ($value) => (int) $value)->values()->all();
        $reverseCodes = data_get($metadata, 'product_codes', data_get($metadata, 'assigned_product_codes', []));
        $reverseCodes = collect(is_array($reverseCodes) ? $reverseCodes : preg_split('/[\s,;|]+/', (string) $reverseCodes, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($code) => strtoupper(trim((string) $code)))->filter()->values()->all();
        $hasLinks = Schema::hasTable('product_supplier_links');
        $products = MasterRecord::query()->forWorkspace($workspace)->ofType('product')
            ->where(function ($query) use ($id, $workspace, $hasLinks, $reverseIds, $reverseCodes): void {
                $query->where('metadata->supplier_id', $id)
                    ->orWhere('metadata->default_supplier_id', $id)
                    ->orWhereJsonContains('metadata->supplier_ids', $id);
                if ($hasLinks) {
                    $query->orWhereExists(fn ($links) => $links->selectRaw('1')->from('product_supplier_links')
                        ->whereColumn('product_supplier_links.product_id', 'master_records.id')
                        ->where('product_supplier_links.workspace_id', $workspace)
                        ->where('product_supplier_links.supplier_id', $id));
                }
                if ($reverseIds !== []) $query->orWhereIn('id', $reverseIds);
                if ($reverseCodes !== []) {
                    $query->orWhereIn('code', $reverseCodes);
                    $displayIds = collect($reverseCodes)->map(function ($code) {
                        return preg_match('/^PRD[-\s]*0*(\d+)$/i', $code, $match) ? (int) $match[1] : null;
                    })->filter()->all();
                    if ($displayIds !== []) $query->orWhereIn('id', $displayIds);
                }
            })
            ->orderBy('id')->limit(2001)->get(['id', 'type', 'code', 'name', 'status']);
        if ($products->count() > 2000) {
            throw new RuntimeException('Supplier has more than 2,000 products; catalogue sync was not truncated.');
        }
        if ($products->isEmpty()) {
            return false; // No product assignment: no supplier catalogue is published.
        }
        $response = $this->portal->post('suppliers', [
            'supplier_id' => $id,
            'reference' => 'FLOWTRACK-'.$id,
            'name' => (string) $supplier->name,
            'short_code' => $supplier->supplierShortCode(),
            'active' => $supplier->status === 'active',
            'products' => $products->map(fn ($product) => [
                'id' => (int) $product->id,
                'code' => $product->productDisplayCode(),
                'name' => (string) $product->name,
                'active' => $product->status === 'active',
            ])->all(),
        ]);
        if (! $response->successful()) {
            throw new RuntimeException('Supplier catalogue API returned HTTP '.$response->status().'.');
        }
        return true;
    }

    /** Secrets are never stored in FlowTracker. Send once over the signed HTTPS API. */
    public function provision(int $supplierId, string $username, string $email, string $password, string $contact): void
    {
        if (! $this->sync($supplierId)) {
            throw new RuntimeException('Assign at least one product before creating supplier portal access.');
        }
        $response = $this->portal->post('supplier-access', [
            'supplier_id' => $supplierId,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'name' => $contact,
        ]);
        if (! $response->successful()) {
            throw new RuntimeException(match ($response->status()) {
                409 => 'Username or email already belongs to another portal account. Existing accounts are not reassigned automatically.',
                default => 'Supplier account API returned HTTP '.$response->status().'.',
            });
        }
    }
}
