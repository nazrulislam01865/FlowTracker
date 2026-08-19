<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class StoredFileResponse
{
    public static function inline(string $path, string $originalName, ?string $mimeType = null): StreamedResponse
    {
        return self::make($path, $originalName, $mimeType, 'inline');
    }

    public static function download(string $path, string $originalName, ?string $mimeType = null): StreamedResponse
    {
        return self::make($path, $originalName, $mimeType, 'attachment');
    }

    private static function make(string $path, string $originalName, ?string $mimeType, string $disposition): StreamedResponse
    {
        $disk = Storage::disk((string) config('flowtrack.document_disk', 'public'));
        abort_unless($path !== '' && $disk->exists($path), 404, 'The requested attachment could not be found.');

        $filename = self::filename($originalName, $path);
        $fallback = self::asciiFallback($filename);
        $type = self::mimeType($filename, $mimeType);

        if ($type === '') {
            try {
                $type = (string) $disk->mimeType($path);
            } catch (\Throwable) {
                $type = '';
            }
        }

        if ($type === '') {
            $type = 'application/octet-stream';
        }

        $headers = [
            'Content-Type' => $type,
            'Content-Disposition' => HeaderUtils::makeDisposition($disposition, $filename, $fallback),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ];

        try {
            $size = $disk->size($path);
            if (is_int($size) && $size >= 0) {
                $headers['Content-Length'] = (string) $size;
            }
        } catch (\Throwable) {
            // Some remote disks do not expose object size cheaply. Streaming still works.
        }

        return response()->stream(function () use ($disk, $path): void {
            $stream = $disk->readStream($path);
            if ($stream === false) {
                return;
            }

            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, $headers);
    }


    public static function mimeType(string $filename, ?string $storedMimeType = null): string
    {
        $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
        $known = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'esp' => 'application/octet-stream',
        ];

        if ($extension !== '' && isset($known[$extension])) {
            return $known[$extension];
        }

        return trim((string) $storedMimeType);
    }

    private static function filename(string $originalName, string $path): string
    {
        $name = trim(str_replace('\\', '/', $originalName));
        $name = basename($name);

        return $name !== '' && $name !== '.' ? $name : basename($path);
    }

    private static function asciiFallback(string $filename): string
    {
        $fallback = Str::ascii($filename);
        $fallback = preg_replace('/[^A-Za-z0-9._ -]+/', '_', $fallback) ?: 'attachment';
        $fallback = trim($fallback, " .\t\n\r\0\x0B");

        return $fallback !== '' ? $fallback : 'attachment';
    }
}
