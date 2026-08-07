<?php

namespace App\Http\Controllers;

use App\Services\BrandingService;
use Illuminate\Support\Facades\Storage;

class BrandingAssetController extends Controller
{
    public function __invoke(string $type, string $filename)
    {
        abort_unless(in_array($type, ['logo', 'favicon'], true), 404);
        abort_unless($filename === basename($filename), 404);

        $workspaceId = (int) app(BrandingService::class)->current()['workspace_id'];
        $path = 'branding/'.$workspaceId.'/'.$type.'/'.$filename;

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
