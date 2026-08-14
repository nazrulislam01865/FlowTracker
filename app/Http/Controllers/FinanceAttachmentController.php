<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AccessControlService;
use App\Services\JobService;
use App\Support\StoredFileResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceAttachmentController extends Controller
{
    public function invoiceOpen(Invoice $invoice): StreamedResponse
    {
        $this->authorizeFinanceRecord((int) $invoice->flow_job_id);

        return $this->respond(
            (string) $invoice->supporting_document_path,
            (string) ($invoice->supporting_document_name ?: basename((string) $invoice->supporting_document_path)),
            'inline'
        );
    }

    public function invoiceDownload(Invoice $invoice): StreamedResponse
    {
        $this->authorizeFinanceRecord((int) $invoice->flow_job_id);

        return $this->respond(
            (string) $invoice->supporting_document_path,
            (string) ($invoice->supporting_document_name ?: basename((string) $invoice->supporting_document_path)),
            'attachment'
        );
    }

    public function paymentOpen(Payment $payment): StreamedResponse
    {
        $this->authorizeFinanceRecord((int) $payment->flow_job_id);

        return $this->respond(
            (string) $payment->receipt_path,
            (string) ($payment->receipt_name ?: basename((string) $payment->receipt_path)),
            'inline'
        );
    }

    public function paymentDownload(Payment $payment): StreamedResponse
    {
        $this->authorizeFinanceRecord((int) $payment->flow_job_id);

        return $this->respond(
            (string) $payment->receipt_path,
            (string) ($payment->receipt_name ?: basename((string) $payment->receipt_path)),
            'attachment'
        );
    }

    private function authorizeFinanceRecord(int $jobId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'view'), 403);
        app(JobService::class)->findVisibleBase($user, $jobId);
    }

    private function respond(string $path, string $name, string $disposition): StreamedResponse
    {
        $disk = Storage::disk('local');
        abort_unless($path !== '' && $disk->exists($path), 404, 'The requested finance attachment could not be found.');

        $filename = trim($name) !== '' ? basename(str_replace('\\', '/', $name)) : basename($path);
        $mime = StoredFileResponse::mimeType($filename);
        if ($mime === '') {
            try {
                $mime = (string) $disk->mimeType($path);
            } catch (\Throwable) {
                $mime = '';
            }
        }
        if ($mime === '') {
            $mime = 'application/octet-stream';
        }

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => HeaderUtils::makeDisposition($disposition, $filename),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ];

        try {
            $headers['Content-Length'] = (string) $disk->size($path);
        } catch (\Throwable) {
        }

        return response()->stream(function () use ($disk, $path): void {
            $stream = $disk->readStream($path);
            abort_if($stream === false, 404);
            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, $headers);
    }
}
