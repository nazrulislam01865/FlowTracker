<?php

namespace App\Http\Controllers;

use App\Models\MasterRecord;
use App\Services\MasterDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductDocumentController extends Controller
{
    public function __invoke(Request $request, MasterRecord $product, string $kind, string $filename)
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'view'), 403);
        abort_unless($product->workspace_id === app(MasterDataService::class)->workspaceId(), 404);
        abort_unless($product->type === 'product', 404);

        $pathKey = match ($kind) {
            'certificate' => 'certificate_test_report_path',
            'template' => 'template_doc_path',
            default => null,
        };
        abort_unless($pathKey, 404);

        $path = trim((string) data_get($product->metadata, $pathKey));
        $prefix = 'product-documents/'.$product->workspace_id.'/'.$product->id.'/';
        abort_unless($path !== '' && str_starts_with($path, $prefix), 404);
        abort_unless(basename($path) === $filename, 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        $headers = ['X-Content-Type-Options' => 'nosniff'];
        if ($request->boolean('download')) {
            return Storage::disk('public')->download($path, $filename, $headers);
        }

        return Storage::disk('public')->response($path, $filename, $headers);
    }
}
