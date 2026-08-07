<?php

namespace App\Http\Controllers;

use App\Services\FilterOptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterOptionController
{
    public function __invoke(Request $request, string $type, FilterOptionService $service): JsonResponse
    {
        abort_unless(in_array($type, ['clients','jobs','users','product-categories','products','workflows'], true), 404);
        $data = $request->validate([
            'q' => ['nullable','string','max:100'],
            'context' => ['nullable','string','max:30'],
            'selected' => ['nullable','string','max:255'],
            'category' => ['nullable','string','max:255'],
        ]);

        return response()->json([
            'items' => $service->options(
                $request->user(),
                $type,
                (string) ($data['context'] ?? ''),
                (string) ($data['q'] ?? ''),
                $data['selected'] ?? null,
                20,
                ['category' => (string) ($data['category'] ?? '')],
            )->values(),
        ]);
    }
}
