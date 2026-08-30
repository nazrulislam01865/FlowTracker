<?php

namespace App\Http\Controllers\Rfq;

use App\Http\Controllers\Controller;
use App\Services\BrandingService;
use App\Services\Inquiries\InquiryRfqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PublicInquiryRfqController extends Controller
{
    public function show(string $token, InquiryRfqService $rfq, BrandingService $branding): View
    {
        $invitation = $rfq->findPublicInvitation($token);

        return view('rfq.public-show', [
            'invitation' => $invitation,
            'brand' => $branding->current(),
            'token' => $token,
        ]);
    }

    public function respond(Request $request, string $token, InquiryRfqService $rfq): RedirectResponse
    {
        $invitation = $rfq->findPublicInvitation($token);
        $action = trim((string) $request->input('action'));

        if ($action === 'decline') {
            $rfq->markDeclined($invitation);
            return redirect()->route('rfq.public.show', ['token' => $token])->with('success', 'Thank you. Your response has been recorded.');
        }

        $itemIds = $invitation->inquiry->items->pluck('id')->map(fn ($id) => (int) $id)->all();
        $data = $request->validate([
            'currency' => ['required','string','max:8'],
            'freight' => ['nullable','numeric','min:0','max:999999999.99'],
            'lead_time_days' => ['nullable','integer','min:0','max:3650'],
            'validity_days' => ['nullable','integer','min:0','max:3650'],
            'notes' => ['nullable','string','max:5000'],
            'prices' => ['required','array'],
            'prices.*' => ['required','numeric','min:0','max:999999999.9999'],
        ]);

        $prices = collect($data['prices'] ?? [])->mapWithKeys(fn ($price, $itemId) => [(int) $itemId => $price]);
        abort_unless(collect($itemIds)->every(fn (int $itemId) => $prices->has($itemId)), 422, 'Enter a unit price for every product.');

        $items = collect($itemIds)->map(fn (int $itemId) => [
            'inquiry_item_id' => $itemId,
            'unit_price' => $prices->get($itemId),
        ])->all();

        $rfq->submitQuote($invitation, $items, $data);

        return redirect()->route('rfq.public.show', ['token' => $token])->with('success', 'Your quotation has been submitted successfully.');
    }
}
