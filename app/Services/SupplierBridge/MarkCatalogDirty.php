<?php
namespace App\Services\SupplierBridge;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Coalesces repeated product edits into one supplier-scoped catalogue push. */
final class MarkCatalogDirty
{
    public function supplierIds(int $workspaceId, array $ids): void
    {
        if (! config('supplier_bridge.enabled')) return;
        $now = now();
        $rows = [];
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id < 1) continue;
            $rows[] = [
                'supplier_id' => $id, 'workspace_id' => $workspaceId,
                'change_token' => (string) Str::uuid(), 'attempts' => 0,
                'next_attempt_at' => null, 'created_at' => $now, 'updated_at' => $now,
            ];
        }
        if ($rows !== []) DB::table('supplier_catalog_outbox')->upsert(
            $rows, ['supplier_id'], ['change_token', 'attempts', 'next_attempt_at', 'updated_at'],
        );
    }
}
