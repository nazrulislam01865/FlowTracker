<?php

namespace App\Services\SupplierBridge;

use App\Jobs\DeliverChangedSupplierOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** Durable pending row plus immediate after-commit queue dispatch. */
final class QueueChangedOrder
{
    public function mark(int $orderId): void
    {
        if ($orderId < 1 || ! config('supplier_bridge.enabled')) return;

        $changed = DB::table('supplier_order_deliveries')
            ->where('flow_job_id', $orderId)
            ->whereIn('status', ['delivered', 'sending'])
            ->update([
                'status' => 'pending', 'attempts' => 0,
                'next_attempt_at' => null, 'locked_at' => null,
                'updated_at' => now(),
            ]);

        // Pending orders already have a dispatch/retry path. Only enqueue when
        // something transitioned, and never send HTTP inside a DB transaction.
        if ($changed < 1 || config('queue.default') === 'sync') return;
        DB::afterCommit(static function () use ($orderId): void {
            try {
                DeliverChangedSupplierOrder::dispatch($orderId)->onQueue('default');
            } catch (\Throwable $error) {
                Log::warning('Immediate supplier delivery deferred to scheduler.', [
                    'order_id' => $orderId, 'error' => $error->getMessage(),
                ]);
            }
        });
    }
}
