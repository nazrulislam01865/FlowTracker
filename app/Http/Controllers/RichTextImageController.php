<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RichTextImageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'file', 'max:10240', 'mimetypes:image/png,image/jpeg,image/webp,image/gif'],
        ]);

        $file = $data['image'];
        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'png',
        };

        $filename = Str::uuid().'.'.$extension;
        $disk = (string) config('flowtrack.document_disk', 'public');
        Storage::disk($disk)->putFileAs('rich-text-images', $file, $filename);

        return response()->json([
            'url' => route('rich-text-images.show', ['filename' => $filename], false),
        ]);
    }

    public function show(string $filename): StreamedResponse
    {
        [$disk, $path] = $this->resolvedImage($filename);

        return Storage::disk($disk)->response($path, $filename, [
            'Cache-Control' => 'private, max-age=31536000, immutable',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function download(string $filename): StreamedResponse
    {
        [$disk, $path] = $this->resolvedImage($filename);

        return Storage::disk($disk)->download($path, $filename, [
            'Cache-Control' => 'private, max-age=31536000, immutable',
        ]);
    }

    private function resolvedImage(string $filename): array
    {
        abort_unless(preg_match('/^[A-Za-z0-9-]+\.(?:png|jpe?g|webp|gif)$/i', $filename) === 1, 404);

        $disk = (string) config('flowtrack.document_disk', 'public');
        $path = 'rich-text-images/'.$filename;
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return [$disk, $path];
    }
}
