<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Inspects quarantined uploads before they become normal FlowTrack documents.
 * The built-in checks are always applied. ClamAV can be enabled in production
 * without changing the upload callers.
 */
class UploadSecurityService
{
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp', 'gif',
        'ico', 'zip', 'txt', 'csv', 'ai', 'eps', 'esp',
    ];

    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh', 'fish', 'bat', 'cmd',
        'com', 'exe', 'dll', 'msi', 'scr', 'jar', 'class', 'js', 'mjs', 'cjs',
        'html', 'htm', 'xhtml', 'svg', 'svgz', 'vbs', 'wsf', 'ps1', 'reg',
    ];

    /** @return array{status:string,engine:string,reason:?string,mime:string,size:int} */
    public function inspect(string $absolutePath, string $originalName, ?string $reportedMime = null): array
    {
        abort_unless(is_file($absolutePath), 422, 'The uploaded file could not be inspected.');

        $size = (int) filesize($absolutePath);
        $maxBytes = max(1, (int) config('flowtrack.upload_security.max_file_bytes', 52428800));
        abort_if($size <= 0, 422, 'Empty files are not allowed.');
        abort_if($size > $maxBytes, 422, 'The uploaded file exceeds the secure storage limit.');

        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        abort_if($extension === '' || ! in_array($extension, self::ALLOWED_EXTENSIONS, true), 422, 'Unsupported business document type.');
        abort_if(in_array($extension, self::BLOCKED_EXTENSIONS, true), 422, 'Executable or script files are not allowed.');

        $detectedMime = $this->detectMime($absolutePath, $reportedMime);
        $this->rejectExecutableSignature($absolutePath);
        $this->validateKnownSignature($absolutePath, $extension, $detectedMime);

        if ($extension === 'zip' || in_array($extension, ['docx', 'xlsx'], true)) {
            $this->inspectZipContainer($absolutePath);
        }

        $engine = strtolower((string) config('flowtrack.upload_security.scanner', 'basic'));
        if ($engine === 'clamav') {
            $this->scanWithClamAv($absolutePath);
        }

        return [
            'status' => 'clean',
            'engine' => $engine === 'clamav' ? 'clamav+flowtrack' : 'flowtrack-basic',
            'reason' => null,
            'mime' => $detectedMime,
            'size' => $size,
        ];
    }

    private function detectMime(string $path, ?string $reportedMime): string
    {
        $detected = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detected = trim((string) finfo_file($finfo, $path));
                finfo_close($finfo);
            }
        }

        return $detected !== '' ? $detected : trim((string) $reportedMime);
    }

    private function rejectExecutableSignature(string $path): void
    {
        $handle = fopen($path, 'rb');
        abort_if($handle === false, 422, 'The uploaded file could not be inspected.');
        $prefix = (string) fread($handle, 4096);
        fclose($handle);

        $binarySignatures = ["MZ", "\x7FELF"];
        foreach ($binarySignatures as $signature) {
            abort_if(str_starts_with($prefix, $signature), 422, 'Executable files are not allowed.');
        }

        $lower = strtolower($prefix);
        foreach (['<?php', '<script', '#!/bin/', '#!/usr/bin/env'] as $marker) {
            abort_if(str_contains($lower, strtolower($marker)), 422, 'Script content is not allowed in uploaded business documents.');
        }
    }

    private function validateKnownSignature(string $path, string $extension, string $mime): void
    {
        if ($extension === '' || in_array($extension, ['eps', 'esp', 'ai', 'txt', 'csv'], true)) {
            return;
        }

        $prefix = (string) file_get_contents($path, false, null, 0, 16);
        $ok = match ($extension) {
            'pdf' => str_starts_with($prefix, '%PDF-'),
            'jpg', 'jpeg' => str_starts_with($prefix, "\xFF\xD8\xFF"),
            'png' => str_starts_with($prefix, "\x89PNG\r\n\x1A\n"),
            'gif' => str_starts_with($prefix, 'GIF87a') || str_starts_with($prefix, 'GIF89a'),
            'webp' => str_starts_with($prefix, 'RIFF') && substr($prefix, 8, 4) === 'WEBP',
            'zip', 'docx', 'xlsx' => str_starts_with($prefix, "PK\x03\x04") || str_starts_with($prefix, "PK\x05\x06") || str_starts_with($prefix, "PK\x07\x08"),
            default => true,
        };

        abort_unless($ok, 422, 'The file contents do not match the selected file type.');

        if ($mime !== '' && str_starts_with($mime, 'text/html')) {
            abort(422, 'HTML content is not allowed in uploaded business documents.');
        }
    }

    /** Inspect archive metadata only; FlowTrack never extracts user archives. */
    private function inspectZipContainer(string $path): void
    {
        if (! class_exists(\ZipArchive::class)) {
            $this->inspectZipCentralDirectory($path);
            return;
        }

        $zip = new \ZipArchive();
        abort_unless($zip->open($path) === true, 422, 'The ZIP/Office archive is invalid or damaged.');

        try {
            $maxEntries = max(1, (int) config('flowtrack.upload_security.zip_max_entries', 1000));
            $maxUncompressed = max(1, (int) config('flowtrack.upload_security.zip_max_uncompressed_bytes', 536870912));
            $maxRatio = max(1, (int) config('flowtrack.upload_security.zip_max_ratio', 150));
            abort_if($zip->numFiles > $maxEntries, 422, 'The archive contains too many entries.');

            $compressed = 0;
            $uncompressed = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                abort_unless(is_array($stat), 422, 'The archive metadata could not be inspected.');
                $name = str_replace('\\', '/', (string) ($stat['name'] ?? ''));
                abort_if(str_starts_with($name, '/') || str_contains('/'.$name, '/../'), 422, 'The archive contains an unsafe path.');
                $compressed += max(0, (int) ($stat['comp_size'] ?? 0));
                $uncompressed += max(0, (int) ($stat['size'] ?? 0));
                abort_if($uncompressed > $maxUncompressed, 422, 'The archive expands beyond the allowed safety limit.');
            }

            if ($compressed > 0) {
                abort_if(($uncompressed / $compressed) > $maxRatio, 422, 'The archive compression ratio is unsafe.');
            }
        } finally {
            $zip->close();
        }
    }


    /**
     * Minimal ZIP central-directory parser used when ext-zip is unavailable.
     * It reads metadata only and never extracts archive contents.
     */
    private function inspectZipCentralDirectory(string $path): void
    {
        $size = (int) filesize($path);
        $tailLength = min($size, 65557);
        $handle = fopen($path, 'rb');
        abort_if($handle === false, 422, 'The archive metadata could not be inspected.');
        try {
            fseek($handle, $size - $tailLength);
            $tail = (string) fread($handle, $tailLength);
            $eocdPos = strrpos($tail, "PK\x05\x06");
            abort_if($eocdPos === false || strlen($tail) < $eocdPos + 22, 422, 'The ZIP/Office archive is invalid or damaged.');
            $eocd = substr($tail, $eocdPos, 22);
            $meta = unpack('ventries/Vsize/Voffset', substr($eocd, 10, 10));
            abort_unless(is_array($meta), 422, 'The archive metadata could not be inspected.');

            $entries = (int) ($meta['entries'] ?? 0);
            $cdSize = (int) ($meta['size'] ?? 0);
            $cdOffset = (int) ($meta['offset'] ?? 0);
            $maxEntries = max(1, (int) config('flowtrack.upload_security.zip_max_entries', 1000));
            abort_if($entries > $maxEntries, 422, 'The archive contains too many entries.');
            abort_if($cdOffset < 0 || $cdSize < 0 || $cdOffset + $cdSize > $size, 422, 'The archive central directory is invalid.');

            fseek($handle, $cdOffset);
            $compressed = 0;
            $uncompressed = 0;
            $maxUncompressed = max(1, (int) config('flowtrack.upload_security.zip_max_uncompressed_bytes', 536870912));
            $maxRatio = max(1, (int) config('flowtrack.upload_security.zip_max_ratio', 150));

            for ($i = 0; $i < $entries; $i++) {
                $fixed = (string) fread($handle, 46);
                abort_if(strlen($fixed) !== 46 || substr($fixed, 0, 4) !== "PK\x01\x02", 422, 'The archive central directory is invalid.');
                $stat = unpack('Vcompressed/Vuncompressed/vname/vextra/vcomment', substr($fixed, 20, 14));
                abort_unless(is_array($stat), 422, 'The archive metadata could not be inspected.');
                $nameLength = (int) ($stat['name'] ?? 0);
                $extraLength = (int) ($stat['extra'] ?? 0);
                $commentLength = (int) ($stat['comment'] ?? 0);
                $entryCompressed = (int) ($stat['compressed'] ?? 0);
                $entryUncompressed = (int) ($stat['uncompressed'] ?? 0);
                abort_if($entryCompressed === 0xFFFFFFFF || $entryUncompressed === 0xFFFFFFFF, 422, 'ZIP64 archives require server ZIP inspection support.');

                $name = (string) fread($handle, $nameLength);
                abort_if(strlen($name) !== $nameLength, 422, 'The archive entry name is invalid.');
                if ($extraLength > 0) fseek($handle, $extraLength, SEEK_CUR);
                if ($commentLength > 0) fseek($handle, $commentLength, SEEK_CUR);

                $normalized = str_replace('\\', '/', $name);
                abort_if(str_starts_with($normalized, '/') || str_contains('/'.$normalized, '/../'), 422, 'The archive contains an unsafe path.');
                $compressed += max(0, $entryCompressed);
                $uncompressed += max(0, $entryUncompressed);
                abort_if($uncompressed > $maxUncompressed, 422, 'The archive expands beyond the allowed safety limit.');
            }

            if ($compressed > 0) {
                abort_if(($uncompressed / $compressed) > $maxRatio, 422, 'The archive compression ratio is unsafe.');
            }
        } finally {
            fclose($handle);
        }
    }

    private function scanWithClamAv(string $path): void
    {
        $binary = trim((string) config('flowtrack.upload_security.clamav_binary', 'clamdscan'));
        abort_if($binary === '', 503, 'Malware scanning is required but not configured.');

        try {
            $process = new Process([$binary, '--no-summary', $path]);
            $process->setTimeout(max(5, (int) config('flowtrack.upload_security.scan_timeout_seconds', 30)));
            $process->run();
        } catch (\Throwable $exception) {
            report($exception);
            abort(503, 'Malware scanning is temporarily unavailable. The upload remains quarantined.');
        }

        if ($process->getExitCode() === 1) {
            abort(422, 'The uploaded file was rejected by malware scanning.');
        }
        if (! $process->isSuccessful()) {
            abort(503, 'Malware scanning is temporarily unavailable. The upload remains quarantined.');
        }
    }
}
