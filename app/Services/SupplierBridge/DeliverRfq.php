<?php

namespace App\Services\SupplierBridge;

use App\Models\InquiryRfqInvitation;
use App\Models\MasterRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

/** Durable, supplier-scoped RFQ projection. Does not publish buyer/customer identities. */
final class DeliverRfq
{
    public function queue(InquiryRfqInvitation $invitation): void
    {
        if (! config('supplier_bridge.enabled') || ! Schema::hasTable('supplier_rfq_deliveries')) return;
        DB::table('supplier_rfq_deliveries')->upsert([[
            'invitation_id' => $invitation->id,
            'supplier_id' => $invitation->supplier_id,
            'status' => 'pending', 'attempts' => 0,
            'next_attempt_at' => null, 'locked_at' => null, 'delivered_at' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]], ['invitation_id'], ['status', 'attempts', 'next_attempt_at', 'locked_at', 'delivered_at', 'updated_at']);
        $id = DB::table('supplier_rfq_deliveries')->where('invitation_id', $invitation->id)->value('id');
        DB::afterCommit(static function () use ($id): void {
            if (config('queue.default') !== 'sync' && $id) {
                try { \App\Jobs\DeliverSupplierRfq::dispatch((int) $id); }
                catch (Throwable $exception) { Log::warning('Supplier RFQ retained for scheduled retry.', ['delivery_id' => $id]); }
            }
        });
    }

    public function send(int $id): bool
    {
        $claimed = DB::table('supplier_rfq_deliveries')->where('id', $id)
            ->where(function ($query): void {
                $query->where('status', 'pending')->orWhere(fn ($stale) => $stale->where('status', 'sending')->where('locked_at', '<', now()->subMinutes(5)));
            })
            ->where(fn ($query) => $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now()))
            ->update(['status' => 'sending', 'locked_at' => now(), 'updated_at' => now()]);
        if ($claimed !== 1) return false;

        $delivery = DB::table('supplier_rfq_deliveries')->find($id);
        try {
            $invitation = InquiryRfqInvitation::query()->with([
                'inquiry:id,workspace_id,inquiry_number,subject,requirement_notes,required_delivery_date,currency,result',
                'inquiry.items:id,inquiry_id,item_name,category,quantity,unit,notes,sort_order',
                'inquiry.documents' => fn ($q) => $q->whereNull('inquiry_task_id')->select('id', 'inquiry_id', 'inquiry_task_id', 'name', 'size', 'mime_type'),
            ])->findOrFail($delivery->invitation_id);
            if ($invitation->email_status !== 'Delivered' || ! $invitation->invited_at || $invitation->inquiry?->result === 'dead') {
                throw new RuntimeException('RFQ invitation is not active for delivery.');
            }
            $inquiry = $invitation->inquiry;
            // A single bounded product lookup resolves display codes only when the
            // Inquiry name/category uniquely identifies the catalogue product.
            $names = $inquiry->items->pluck('item_name')->filter()->unique()->all();
            $catalog = MasterRecord::query()->forWorkspace((int) $inquiry->workspace_id)
                ->ofType('product')->whereIn('name', $names)
                ->with('parent:id,name')->limit(501)->get(['id', 'parent_id', 'name', 'type', 'metadata', 'code']);
            $codes = [];
            if ($catalog->count() <= 500) {
                foreach ($catalog as $product) {
                    $key = mb_strtolower(trim((string) $product->name)).'|'.mb_strtolower(trim((string) ($product->parent?->name ?? '')));
                    $codes[$key][] = $product->productDisplayCode();
                }
            }
            $items = $inquiry->items->map(static function ($item) use ($codes): array {
                $key = mb_strtolower(trim((string) $item->item_name)).'|'.mb_strtolower(trim((string) ($item->category ?? '')));
                $matches = $codes[$key] ?? [];
                return [
                    'id' => (int) $item->id,
                    'product_code' => count($matches) === 1 ? $matches[0] : '',
                    'product_name' => mb_substr((string) $item->item_name, 0, 255),
                    'category' => mb_substr((string) ($item->category ?? ''), 0, 150),
                    'quantity' => (float) $item->quantity,
                    'unit' => mb_substr((string) ($item->unit ?? ''), 0, 50),
                    'notes' => mb_substr((string) ($item->notes ?? ''), 0, 2000),
                ];
            })->all();
            if ($items === [] || count($items) > 100) throw new RuntimeException('RFQ must contain 1-100 products.');
            $docs = $inquiry->documents->map(static fn ($document): array => [
                'id' => (int) $document->id,
                'name' => mb_substr(basename((string) $document->name), 0, 255),
                'size' => (int) $document->size,
                'mime_type' => (string) ($document->mime_type ?? 'application/octet-stream'),
            ])->take(100)->all();
            if ($inquiry->documents->count() > 100) throw new RuntimeException('RFQ exceeds the maximum document count.');
            $payload = [
                'supplier_id' => (int) $delivery->supplier_id,
                'rfq' => [
                    'invitation_id' => (int) $invitation->id,
                    'reference' => 'RFQ-'.str_pad((string) $invitation->id, 6, '0', STR_PAD_LEFT),
                    'inquiry_reference' => (string) $inquiry->inquiry_number,
                    'subject' => mb_substr((string) $inquiry->subject, 0, 255),
                    'buyer_requirements' => mb_substr((string) ($inquiry->requirement_notes ?? ''), 0, 8000),
                    'request_message' => mb_substr((string) ($invitation->request_message ?? ''), 0, 2000),
                    'supplier_details' => mb_substr((string) ($invitation->supplier_details ?? ''), 0, 2000),
                    'currency' => (string) ($inquiry->currency ?: 'USD'),
                    'delivery_date' => $inquiry->required_delivery_date?->format('Y-m-d'),
                    'due_at' => $invitation->due_at?->toIso8601String(),
                    'items' => $items,
                    'documents' => $docs,
                ],
            ];
            $response = app(PortalClient::class)->post('rfqs', $payload);
            if ($response->status() === 409) {
                // Most suppliers are already provisioned; sync catalog only on a
                // missing portal identity, avoiding an extra request per RFQ.
                if (! app(SyncSupplier::class)->sync((int) $delivery->supplier_id)) {
                    throw new RuntimeException('Supplier catalog sync has no assigned products.');
                }
                $response = app(PortalClient::class)->post('rfqs', $payload);
            }
            if (! $response->successful()) throw new RuntimeException('Supplier RFQ API returned HTTP '.$response->status().'.');
            DB::table('supplier_rfq_deliveries')->where('id', $id)->update([
                'status' => 'delivered', 'delivered_at' => now(), 'last_error' => null,
                'next_attempt_at' => null, 'locked_at' => null, 'updated_at' => now(),
            ]);
            return true;
        } catch (Throwable $exception) {
            $attempts = (int) $delivery->attempts + 1;
            DB::table('supplier_rfq_deliveries')->where('id', $id)->update([
                'status' => 'pending', 'attempts' => $attempts, 'locked_at' => null,
                'next_attempt_at' => now()->addSeconds(min(3600, 30 * (2 ** min($attempts, 7)))),
                'last_error' => mb_substr($exception instanceof RuntimeException ? $exception->getMessage() : 'Supplier RFQ transport failed.', 0, 160),
                'updated_at' => now(),
            ]);
            Log::warning('Supplier RFQ API delivery deferred.', ['delivery_id' => $id, 'attempt' => $attempts]);
            return false;
        }
    }
}
