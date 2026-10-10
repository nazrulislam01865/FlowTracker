<?php

namespace App\Http\Controllers\SupplierBridge;

use App\Models\InquiryDocument;
use App\Models\InquiryRfqInvitation;
use App\Services\SecureDocumentStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

/** Private server-to-server download; never publicly issue bearer document URLs. */
final class DownloadRfqDocument extends Controller
{
    public function __invoke(Request $request, int $invitation, int $document, SecureDocumentStorage $storage)
    {
        $secret = (string) config('supplier_bridge.secret');
        $timestamp = $request->header('X-FlowTrack-Timestamp');
        $supplier = $request->header('X-FlowTrack-Supplier');
        $signature = $request->header('X-FlowTrack-Signature');
        if (! config('supplier_bridge.enabled') || strlen($secret) < 32 || ! is_string($timestamp) || ! ctype_digit($timestamp)
            || abs(time() - (int) $timestamp) > 120 || ! is_string($supplier) || ! ctype_digit($supplier)
            || ! is_string($signature) || ! preg_match('/^[0-9a-f]{64}$/i', $signature)
            || ! hash_equals(hash_hmac('sha256', $timestamp."\n".$supplier."\n".$invitation."\n".$document, $secret), $signature)) {
            abort(404);
        }
        $rfq = InquiryRfqInvitation::query()->whereKey($invitation)
            ->where('supplier_id', (int) $supplier)
            ->where('email_status', 'Delivered')->whereNotNull('invited_at')->firstOrFail();
        $file = InquiryDocument::query()->whereKey($document)->where('inquiry_id', $rfq->inquiry_id)
            ->whereNull('inquiry_task_id')->firstOrFail();
        abort_if($file->size > 20971520, 413);
        $located = $storage->locate((string) $file->path);
        abort_unless($located, 404);
        $disk = Storage::disk($located['disk']);
        $name = basename($file->name) ?: 'rfq-document';
        return response()->streamDownload(static function () use ($disk, $located): void {
            $handle = $disk->readStream($located['path']);
            if (! is_resource($handle)) return;
            try { fpassthru($handle); } finally { fclose($handle); }
        }, $name, [
            'Content-Type' => 'application/octet-stream',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
