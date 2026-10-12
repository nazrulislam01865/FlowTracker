<?php

namespace App\Jobs;

use App\Services\SupplierBridge\DeliverOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/** Fast delivery for one changed order; existing minute scheduler retries failures. */
final class DeliverChangedSupplierOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public readonly int $orderId) {}

    public function handle(DeliverOrder $sender): void
    {
        $ids = DB::table('supplier_order_deliveries')
            ->where('flow_job_id', $this->orderId)->where('status', 'pending')
            ->where(function ($query): void {
                $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now());
            })->pluck('id');
        foreach ($ids as $id) {
            $sender->send((int) $id);
        }
    }
}
