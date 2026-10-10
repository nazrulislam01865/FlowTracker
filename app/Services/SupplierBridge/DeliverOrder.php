<?php

namespace App\Services\SupplierBridge;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/** One-way HTTP bridge. Does not expose FlowTracker API or credentials to suppliers. */
final class DeliverOrder
{
    public function send(int $id): bool
    {
        $claimed = DB::table('supplier_order_deliveries')->where('id', $id)
            ->where(function ($query): void {
                $query->where('status', 'pending')
                    ->orWhere(function ($stale): void {
                        $stale->where('status', 'sending')->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->where(function ($query): void {
                $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now());
            })
            ->update(['status' => 'sending', 'locked_at' => now(), 'updated_at' => now()]);

        if ($claimed !== 1) {
            return false;
        }

        $delivery = DB::table('supplier_order_deliveries')->find($id);
        try {
            $url = config('supplier_bridge.url');
            $secret = config('supplier_bridge.secret');
            if (! config('supplier_bridge.enabled') || strlen($secret) < 32 || ! is_string($url) || ! $this->allowedUrl($url)) {
                throw new RuntimeException('Supplier bridge is not securely configured.');
            }

            // Rebuild at delivery time so retries also carry current stage, files and assigned lines.
            $snapshotAt = now();
            $snapshot = app(SupplierOrderProjection::class)->build(
                (int) $delivery->flow_job_id, (int) $delivery->supplier_id, (string) $delivery->event_id,
            );
            $body = json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
            $timestamp = (string) time();
            $signature = hash_hmac('sha256', $timestamp."\n".$delivery->event_id."\n".$body, $secret);
            $response = Http::connectTimeout(2)->timeout(5)->withHeaders([
                'X-FlowTrack-Timestamp' => $timestamp,
                'X-FlowTrack-Event' => $delivery->event_id,
                'X-FlowTrack-Signature' => $signature,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->withBody($body, 'application/json')->post($url.'/api/v1/integrations/flowtrack/orders');

            if ($response->status() === 409) {
                // Only provision when the supplier mapping is actually missing.
                if (! app(SyncSupplier::class)->sync((int) $delivery->supplier_id)) {
                    throw new RuntimeException('Supplier provisioning could not be completed.');
                }
                $timestamp = (string) time();
                $signature = hash_hmac('sha256', $timestamp."\n".$delivery->event_id."\n".$body, $secret);
                $response = Http::connectTimeout(2)->timeout(5)->withHeaders([
                    'X-FlowTrack-Timestamp' => $timestamp,
                    'X-FlowTrack-Event' => $delivery->event_id,
                    'X-FlowTrack-Signature' => $signature,
                    'Content-Type' => 'application/json', 'Accept' => 'application/json',
                ])->withBody($body, 'application/json')->post($url.'/api/v1/integrations/flowtrack/orders');
            }
            if (! $response->successful()) {
                throw new RuntimeException('Supplier service returned HTTP '.$response->status());
            }

            DB::table('supplier_order_deliveries')->where('id', $id)->update([
                'status' => 'delivered', 'delivered_at' => $snapshotAt, 'locked_at' => null,
                'last_error' => null, 'updated_at' => now(),
            ]);
            return true;
        } catch (\Throwable $exception) {
            $attempts = (int) $delivery->attempts + 1;
            DB::table('supplier_order_deliveries')->where('id', $id)->update([
                'status' => 'pending', 'attempts' => $attempts, 'locked_at' => null,
                'next_attempt_at' => now()->addSeconds(min(3600, 30 * (2 ** min($attempts, 7)))),
                'last_error' => substr($exception instanceof RuntimeException ? $exception->getMessage() : 'Supplier transport unavailable', 0, 160),
                'updated_at' => now(),
            ]);
            Log::warning('Supplier order API delivery deferred.', ['delivery_id' => $id, 'attempt' => $attempts]);
            return false;
        }
    }

    private function allowedUrl(string $url): bool
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        if (! is_string($host) || $host === '' || str_contains($url, '@') || str_contains($url, '?') || str_contains($url, '#')) {
            return false;
        }
        // Never allow an arbitrary HTTP endpoint outside local development.
        return $scheme === 'https' || (app()->environment(['local', 'testing']) && $scheme === 'http' && in_array($host, ['127.0.0.1', 'localhost'], true));
    }
}
