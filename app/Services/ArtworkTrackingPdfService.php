<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use App\Support\SimplePdfDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class ArtworkTrackingPdfService
{
    public function generate(FlowJob $job, Task $reviewTask, User $actor): Document
    {
        $uploadTask = Task::query()
            ->where('flow_job_id', $job->id)
            ->where('workflow_phase_id', $reviewTask->workflow_phase_id)
            ->whereNotNull('task_pack_task_id')
            ->with('setupTemplate')
            ->get()
            ->first(fn (Task $candidate): bool => app(OrderWorkflowActionService::class)->automationKey($candidate) === 'ART_PREPARE_UPLOAD');

        if (! $uploadTask) {
            throw new RuntimeException('The confirmed Artwork upload task could not be found.');
        }

        $artwork = app(DocumentService::class)->currentArtworkDocuments($uploadTask);
        if ($artwork->isEmpty()) {
            throw new RuntimeException('Artwork cannot be confirmed until at least one current artwork file is available.');
        }

        $artworkVersion = max(1, (int) $artwork->max('version'));
        $existing = Document::query()
            ->where('flow_job_id', $job->id)
            ->where('category', Document::CATEGORY_ARTWORK_TRACKING_PDF)
            ->where('version', $artworkVersion)
            ->latest('id')
            ->first();

        if ($existing && app(SecureDocumentStorage::class)->locate((string) $existing->path)) {
            return $existing;
        }

        $job->ensureTrackingToken();
        $trackingUrl = $job->trackingUrl();
        $pdf = $this->render($job, $artwork, $trackingUrl, $artworkVersion, $actor);

        $orderNumber = $job->job_number ?: $job->order_number ?: 'ORDER-'.$job->id;
        $safeOrder = preg_replace('/[^A-Za-z0-9._-]+/', '-', $orderNumber) ?: 'order-'.$job->id;
        $filename = trim($safeOrder, '-').'-confirmed-artwork-v'.$artworkVersion.'-tracking.pdf';
        $path = 'flowtrack/documents/'.$job->id.'/generated/'.$filename;
        $disk = Storage::disk((string) config('flowtrack.document_disk', 'flowtrack_private'));

        if (! $disk->put($path, $pdf)) {
            throw new RuntimeException('The confirmed artwork tracking PDF could not be stored.');
        }

        try {
            $document = Document::updateOrCreate(
                [
                    'flow_job_id' => $job->id,
                    'category' => Document::CATEGORY_ARTWORK_TRACKING_PDF,
                    'version' => $artworkVersion,
                ],
                [
                    'document_number' => 'DOC-TRACK-'.$job->id.'-V'.$artworkVersion,
                    'client_id' => $job->client_id,
                    'task_id' => $reviewTask->id,
                    'uploaded_by' => $actor->id,
                    'name' => $filename,
                    'path' => $path,
                    'mime_type' => 'application/pdf',
                    'size' => strlen($pdf),
                    'is_final' => true,
                    'note' => 'Generated automatically when the artwork was confirmed. Includes the confirmed artwork and the Order Tracking QR code.',
                ],
            );
        } catch (\Throwable $exception) {
            $disk->delete($path);
            throw $exception;
        }

        return $document->refresh();
    }

    /** @param Collection<int,Document> $artwork */
    private function render(FlowJob $job, Collection $artwork, string $trackingUrl, int $artworkVersion, User $actor): string
    {
        $doc = new SimplePdfDocument();
        $navy = [0.055, 0.145, 0.285];
        $green = [0.00, 0.50, 0.42];
        $text = [0.08, 0.14, 0.24];
        $muted = [0.38, 0.44, 0.54];
        $border = [0.84, 0.88, 0.93];
        $soft = [0.965, 0.98, 0.985];

        $orderNumber = $job->displayOrderNumber() ?: ($job->job_number ?: $job->order_number ?: 'ORDER-'.$job->id);
        $reference = trim((string) ($job->reference_number ?? '')) ?: ($job->job_number ?: $job->order_number ?: '-');
        $companyHeader = $this->companyHeader();

        $this->renderCompanyHeader($doc, $companyHeader, $navy, $text, $muted, $border);

        $doc->text(42, 728, 'CONFIRMED ARTWORK', 22, true, $navy);
        $doc->text(42, 705, 'Order tracking document', 11, false, $muted);
        $doc->fillRect(42, 685, 511, 3, $green);

        $doc->text(42, 652, 'Order number', 8, true, $muted);
        $doc->text(42, 634, $this->plain($orderNumber), 13, true, $text);
        $doc->text(42, 603, 'Reference', 8, true, $muted);
        $doc->text(42, 585, $this->plain($reference), 10, false, $text);
        $doc->text(42, 554, 'Artwork version', 8, true, $muted);
        $doc->text(42, 536, 'V'.$artworkVersion, 10, true, $text);
        $doc->text(42, 505, 'Confirmed by', 8, true, $muted);
        $doc->text(42, 487, $this->plain((string) $actor->name), 10, false, $text);
        $doc->text(42, 456, 'Confirmed at', 8, true, $muted);
        $doc->text(42, 438, now()->format('M j, Y g:i A'), 10, false, $text);

        $qrX = 344.0;
        $qrY = 458.0;
        $qrSize = 185.0;
        $doc->fillRect($qrX - 12, $qrY - 12, $qrSize + 24, $qrSize + 24, [1, 1, 1]);
        $doc->rect($qrX - 12, $qrY - 12, $qrSize + 24, $qrSize + 24, 0.8, $border);
        $this->drawQr($doc, $trackingUrl, $qrX, $qrY, $qrSize);
        $doc->textCentered($qrX + ($qrSize / 2), 425, 'Scan to view the current order status', 9, true, $green);

        $doc->fillRect(42, 352, 511, 58, $soft);
        $doc->rect(42, 352, 511, 58, 0.7, $border);
        $doc->text(56, 390, 'ORDER TRACKING', 8, true, $green);
        $doc->wrappedText(
            56,
            373,
            'The QR code opens the live Order Tracking page. The status shown there is read from the current FlowTrack order, so it continues to update after this PDF is generated.',
            482,
            9,
            12,
            false,
            $text,
            3,
        );

        $doc->text(42, 318, 'CONFIRMED ARTWORK FILES', 9, true, $navy);
        $y = 297.0;
        foreach ($artwork as $index => $artworkDocument) {
            $label = ($index + 1).'. '.$this->plain((string) $artworkDocument->name);
            $doc->text(50, $y, $label, 9, false, $text);
            $y -= 16;
            if ($y < 80) break;
        }

        foreach ($artwork as $index => $artworkDocument) {
            $doc->newPage();
            $this->renderArtworkPage($doc, $artworkDocument, $index + 1, $artwork->count(), $orderNumber, $companyHeader, $border, $navy, $text, $muted);
        }

        return $doc->output();
    }

    private function drawQr(SimplePdfDocument $doc, string $url, float $x, float $y, float $size): void
    {
        $matrix = app(QrCodeService::class)->booleanMatrix($url);
        $count = count($matrix);
        if ($count === 0) {
            throw new RuntimeException('The Order Tracking QR code could not be generated.');
        }

        $module = $size / $count;
        foreach ($matrix as $row => $columns) {
            foreach ($columns as $column => $dark) {
                if (! $dark) continue;
                $doc->fillRect(
                    $x + ($column * $module),
                    $y + $size - (($row + 1) * $module),
                    $module + 0.02,
                    $module + 0.02,
                    [0.02, 0.04, 0.06],
                );
            }
        }
    }

    private function renderArtworkPage(
        SimplePdfDocument $doc,
        Document $artwork,
        int $position,
        int $total,
        string $orderNumber,
        array $companyHeader,
        array $border,
        array $navy,
        array $text,
        array $muted,
    ): void {
        $this->renderCompanyHeader($doc, $companyHeader, $navy, $text, $muted, $border);

        $doc->text(42, 728, 'CONFIRMED ARTWORK', 18, true, $navy);
        $doc->textRight(553, 729, $position.' / '.$total, 9, true, $muted);
        $doc->text(42, 704, $this->plain($orderNumber), 9, false, $muted);
        $doc->line(42, 688, 553, 688, 0.8, $border);

        $doc->text(42, 663, $this->plain((string) $artwork->name), 11, true, $text);
        $doc->text(42, 645, 'Artwork V'.max(1, (int) $artwork->version).'  |  '.$this->plain((string) ($artwork->mime_type ?: 'file')), 8, false, $muted);

        $preview = $this->previewPath($artwork);
        try {
            if ($preview !== null && ($dimensions = @getimagesize($preview))) {
                $maxWidth = 511.0;
                $maxHeight = 548.0;
                $scale = min($maxWidth / max(1, (int) $dimensions[0]), $maxHeight / max(1, (int) $dimensions[1]));
                $width = max(1.0, (int) $dimensions[0] * $scale);
                $height = max(1.0, (int) $dimensions[1] * $scale);
                $x = 42 + (($maxWidth - $width) / 2);
                $y = 70 + (($maxHeight - $height) / 2);
                $doc->rect(42, 70, $maxWidth, $maxHeight, 0.7, $border);
                if ($doc->image($preview, $x, $y, $width, $height)) {
                    return;
                }
            }

            $doc->rect(42, 120, 511, 470, 0.7, $border);
            $doc->textCentered(297.5, 390, 'Confirmed artwork file', 15, true, $navy);
            $doc->wrappedText(92, 360, $this->plain((string) $artwork->name), 411, 11, 15, false, $text, 4);
            $doc->textCentered(297.5, 290, 'A visual preview is unavailable for this file format.', 9, false, $muted);
            $doc->textCentered(297.5, 273, 'The file remains the confirmed artwork stored with this order.', 9, false, $muted);
        } finally {
            if ($preview !== null && str_starts_with($preview, sys_get_temp_dir().DIRECTORY_SEPARATOR.'flowtrack-artwork-')) {
                @unlink($preview);
            }
        }
    }

    /** @return array{name:string,legal_name:string,address:string,email:string,phone:string,website:string,registration:string,tax:string,logo_path:?string} */
    private function companyHeader(): array
    {
        $companyService = app(CompanyProfileService::class);
        $company = $companyService->current();
        $branding = app(BrandingService::class)->current();

        $name = trim((string) ($company['trading_name'] ?? ''))
            ?: trim((string) ($company['legal_name'] ?? ''))
            ?: trim((string) ($branding['name'] ?? ''))
            ?: 'FlowTrack';
        $legalName = trim((string) ($company['legal_name'] ?? '')) ?: $name;

        return [
            'name' => $name,
            'legal_name' => $legalName,
            'address' => implode(', ', $companyService->addressLines($company)),
            'email' => trim((string) ($company['billing_email'] ?? '')),
            'phone' => trim((string) ($company['phone'] ?? '')),
            'website' => trim((string) ($company['website'] ?? '')),
            'registration' => trim((string) ($company['registration_number'] ?? '')),
            'tax' => trim((string) ($company['tax_number'] ?? '')),
            'logo_path' => $this->brandingLogoPath($branding),
        ];
    }

    /** @param array{name:string,legal_name:string,address:string,email:string,phone:string,website:string,registration:string,tax:string,logo_path:?string} $company */
    private function renderCompanyHeader(
        SimplePdfDocument $doc,
        array $company,
        array $navy,
        array $text,
        array $muted,
        array $border,
    ): void {
        $logoDrawn = $this->drawLogo($doc, $company['logo_path'], 42, 774, 92, 34);
        $textX = $logoDrawn ? 148.0 : 42.0;
        $leftWidth = $logoDrawn ? 238.0 : 344.0;

        $doc->wrappedText($textX, 806, $this->plain($company['name']), $leftWidth, 10.5, 12, true, $navy, 1);

        $legalName = $this->plain($company['legal_name']);
        if ($legalName !== '' && $legalName !== $this->plain($company['name'])) {
            $doc->wrappedText($textX, 791, $legalName, $leftWidth, 7.2, 9, false, $muted, 1);
        }

        if ($company['address'] !== '') {
            $doc->wrappedText($textX, 778, $this->plain($company['address']), $leftWidth, 7.2, 9, false, $muted, 2);
        }

        $rightLines = [];
        if ($company['email'] !== '') $rightLines[] = $this->plain($company['email']);
        if ($company['phone'] !== '') $rightLines[] = $this->plain($company['phone']);
        if ($company['website'] !== '') $rightLines[] = $this->plain($company['website']);

        $registration = [];
        if ($company['registration'] !== '') $registration[] = 'Reg: '.$this->plain($company['registration']);
        if ($company['tax'] !== '') $registration[] = 'Tax: '.$this->plain($company['tax']);
        if ($registration !== []) $rightLines[] = implode('  |  ', $registration);

        $rightY = 806.0;
        foreach (array_slice($rightLines, 0, 4) as $line) {
            $doc->textRight(553, $rightY, $this->compact($line, 44), 7.2, false, $text);
            $rightY -= 11;
        }

        $doc->line(42, 754, 553, 754, 0.7, $border);
    }

    private function brandingLogoPath(array $branding): ?string
    {
        $path = trim((string) ($branding['logo_path'] ?? ''));
        if ($path === '') return null;

        try {
            $disk = Storage::disk('public');
            if (! $disk->exists($path)) return null;
            return $disk->path($path);
        } catch (\Throwable) {
            return null;
        }
    }

    private function drawLogo(SimplePdfDocument $doc, ?string $path, float $x, float $y, float $maxWidth, float $maxHeight): bool
    {
        if (! $path || ! is_file($path)) return false;
        $info = @getimagesize($path);
        if (! $info || empty($info[0]) || empty($info[1])) return false;

        $scale = min($maxWidth / (float) $info[0], $maxHeight / (float) $info[1], 1.0);
        $width = max(1.0, (float) $info[0] * $scale);
        $height = max(1.0, (float) $info[1] * $scale);

        return $doc->image($path, $x, $y, $width, $height);
    }

    private function compact(string $value, int $limit): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) <= $limit) return $value;
        return rtrim(mb_substr($value, 0, max(1, $limit - 1))).'...';
    }

    private function previewPath(Document $artwork): ?string
    {
        $resolved = app(SecureDocumentStorage::class)->locate((string) $artwork->path);
        if ($resolved === null) return null;

        $disk = Storage::disk($resolved['disk']);
        $path = $resolved['path'];
        $mime = strtolower(trim((string) $artwork->mime_type));

        if (str_starts_with($mime, 'image/')) {
            return $this->localCopy($disk, $path);
        }

        if ($mime === 'application/pdf' && class_exists(\Imagick::class)) {
            $source = $this->localCopy($disk, $path);
            if ($source === null) return null;

            $preview = tempnam(sys_get_temp_dir(), 'flowtrack-artwork-');
            if ($preview === false) {
                if (str_starts_with($source, sys_get_temp_dir().DIRECTORY_SEPARATOR.'flowtrack-artwork-')) @unlink($source);
                return null;
            }

            try {
                $image = new \Imagick();
                $image->setResolution(144, 144);
                $image->readImage($source.'[0]');
                $image->setImageFormat('png');
                $image->setImageBackgroundColor('white');
                $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $image->writeImage($preview);
                $image->clear();
                $image->destroy();
                return $preview;
            } catch (\Throwable) {
                @unlink($preview);
                return null;
            } finally {
                if (str_starts_with($source, sys_get_temp_dir().DIRECTORY_SEPARATOR.'flowtrack-artwork-')) @unlink($source);
            }
        }

        return null;
    }

    private function localCopy($disk, string $path): ?string
    {
        try {
            $local = $disk->path($path);
            if (is_string($local) && $local !== '' && is_file($local) && is_readable($local)) {
                return $local;
            }
        } catch (\Throwable) {
        }

        $stream = $disk->readStream($path);
        if ($stream === false) return null;

        $temporary = tempnam(sys_get_temp_dir(), 'flowtrack-artwork-');
        if ($temporary === false) {
            if (is_resource($stream)) fclose($stream);
            return null;
        }

        $target = fopen($temporary, 'wb');
        if ($target === false) {
            if (is_resource($stream)) fclose($stream);
            @unlink($temporary);
            return null;
        }

        try {
            stream_copy_to_stream($stream, $target);
        } finally {
            if (is_resource($stream)) fclose($stream);
            fclose($target);
        }

        return $temporary;
    }

    private function plain(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? $value);
    }
}
