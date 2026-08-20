<?php

namespace App\Http\Controllers;

use App\Services\FilterOptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterOptionController
{
    public function __invoke(Request $request, string $type, FilterOptionService $service): JsonResponse
    {
        abort_unless(in_array($type, ['clients','jobs','users','product-categories','products','workflows','priorities','task-statuses','document-categories','document-category-records','department-records','departments','countries','phone-country-codes','job-statuses','job-healths','phases'], true), 404);
        $data = $request->validate([
            'q' => ['nullable','string','max:100'],
            'context' => ['nullable','string','max:30'],
            'selected' => ['nullable','string','max:255'],
            'category' => ['nullable','string','max:255'],
            'client_id' => ['nullable','integer','exists:clients,id'],
            'parent_type' => ['nullable','string','in:job,inquiry'],
            'parent_id' => ['nullable','integer'],
        ]);

        $context = (string) ($data['context'] ?? '');
        $search = trim((string) ($data['q'] ?? ''));

        // Inline pickers stay intentionally compact when first opened.
        // Once the user searches (2+ characters), return the normal larger result set.
        $compactInitialList = strlen($search) < 2 && (
            in_array($type, ['clients','jobs','users','workflows','priorities','task-statuses','document-categories','document-category-records','department-records','departments','countries','phone-country-codes','job-statuses','job-healths','phases'], true)
            || (in_array($context, ['job-detail', 'create-inquiry'], true) && in_array($type, ['product-categories', 'products'], true))
        );
        $limit = $compactInitialList ? 5 : 20;

        return response()->json([
            'items' => $service->options(
                $request->user(),
                $type,
                $context,
                $search,
                $data['selected'] ?? null,
                $limit,
                [
                    'category' => (string) ($data['category'] ?? ''),
                    'client_id' => (int) ($data['client_id'] ?? 0) ?: null,
                    'parent_type' => (string) ($data['parent_type'] ?? ''),
                    'parent_id' => (int) ($data['parent_id'] ?? 0) ?: null,
                ],
            )->values(),
        ]);
    }
}
