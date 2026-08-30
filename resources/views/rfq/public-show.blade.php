<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $invitation->inquiry->inquiry_number }} · Supplier quotation</title>
    <script
        src="{{ asset('js/flowtrack-image-fallback.js') }}?v={{ \App\Support\FrontendBuildVersion::current() }}"
        data-fallback-src="{{ asset('images/flowtrack-image-fallback.svg') }}"
    ></script>
    @vite(['resources/theme/flowtrack/core.css', 'resources/css/app.css'])
</head>
<body class="ft-public-rfq-page">
@php
    $inquiry = $invitation->inquiry;
    $supplier = $invitation->supplier;
    $quote = $invitation->quote;
    $locked = (bool) ($invitation->awarded_at || $invitation->rejected_at || $invitation->interest_status === 'declined');
    $submitted = $invitation->quote_status === 'submitted' && $quote;
    $statusLabel = $invitation->awarded_at
        ? 'Awarded'
        : ($invitation->rejected_at
            ? 'Closed'
            : ($submitted ? 'Submitted' : ($invitation->interest_status === 'declined' ? 'Declined' : 'Awaiting quotation')));
    $totalQuantity = (float) $inquiry->items->sum('quantity');
    $currency = old('currency', $quote?->currency ?? $inquiry->currency ?? 'USD');
@endphp

<main class="ft-public-rfq-shell">
    <header class="ft-public-rfq-brand" aria-label="{{ $brand['name'] ?? 'FlowTrack' }} supplier quotation portal">
        <div class="ft-public-rfq-brand-lockup">
            <div class="ft-public-rfq-brand-name">{{ strtoupper($brand['name'] ?? 'FlowTrack') }}</div>
            <div class="ft-public-rfq-brand-subtitle">Supplier quotation portal</div>
        </div>
        <div class="ft-public-rfq-secure-badge" title="This quotation link is unique to your company">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.4-2.7 8.3-7 10-4.3-1.7-7-5.6-7-10V6l7-3zm0 2.2L7 7.3V11c0 3.3 1.9 6.4 5 7.8 3.1-1.4 5-4.5 5-7.8V7.3l-5-2.1z"/></svg>
            Secure RFQ
        </div>
    </header>

    @if(session('success'))
        <div class="ft-rfq-public-success" role="status">
            <span class="ft-rfq-public-feedback-icon">✓</span>
            <div><strong>Response saved</strong><span>{{ session('success') }}</span></div>
        </div>
    @endif

    <section class="ft-public-rfq-card ft-public-rfq-intro-card">
        <div class="ft-public-rfq-intro-top">
            <div>
                <div class="ft-public-rfq-eyebrow">Request for quotation · {{ $inquiry->inquiry_number }}</div>
                <h1>{{ $inquiry->subject }}</h1>
                <p>Hello {{ $invitation->supplierContactName() }}. Review the request below and submit your commercial quotation.</p>
            </div>
            <span class="ft-public-rfq-status {{ $submitted ? 'is-submitted' : ($locked ? 'is-closed' : 'is-pending') }}">{{ $statusLabel }}</span>
        </div>

        <div class="ft-public-rfq-meta">
            <div class="ft-public-rfq-meta-item">
                <span class="ft-public-rfq-meta-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 20V8l8-4 8 4v12h-6v-6h-4v6H4zm2-2h2v-6h8v6h2V9.2l-6-3-6 3V18z"/></svg>
                </span>
                <div><small>Supplier</small><strong>{{ $supplier->name }}</strong></div>
            </div>
            <div class="ft-public-rfq-meta-item">
                <span class="ft-public-rfq-meta-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M7 2v2h10V2h2v2h1a2 2 0 012 2v14a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h1V2h2zm13 8H4v10h16V10zM4 8h16V6H4v2z"/></svg>
                </span>
                <div><small>Quotation due</small><strong>{{ $invitation->due_at?->format('M j, Y') ?? 'No due date' }}</strong></div>
            </div>
            <div class="ft-public-rfq-meta-item">
                <span class="ft-public-rfq-meta-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm0 2v14h14V5H5zm2 3h10v2H7V8zm0 4h10v2H7v-2zm0 4h6v2H7v-2z"/></svg>
                </span>
                <div><small>Requested quantity</small><strong>{{ number_format($totalQuantity, 0) }} {{ $totalQuantity === 1.0 ? 'unit' : 'units' }}</strong></div>
            </div>
        </div>
    </section>

    <form method="post" action="{{ route('rfq.public.respond', ['token' => $token]) }}" class="ft-public-rfq-card ft-public-rfq-form" id="rfq-quotation-form">
        @csrf
        <div class="ft-public-rfq-card-head">
            <div>
                <span class="ft-public-rfq-section-kicker">Quotation</span>
                <h2>{{ $quote ? 'Review your quotation' : 'Enter your quotation' }}</h2>
                <p>Enter a unit price for every requested product, then complete the commercial terms.</p>
            </div>
            @if($submitted)<span class="ft-rfq-status-pill is-green">Submitted</span>@endif
        </div>

        @if($errors->any())
            <div class="ft-rfq-public-error" role="alert">
                <span class="ft-rfq-public-feedback-icon">!</span>
                <div><strong>Please check your quotation</strong><span>{{ $errors->first() }}</span></div>
            </div>
        @endif

        <section class="ft-public-rfq-form-section" aria-labelledby="rfq-products-title">
            <div class="ft-public-rfq-subhead">
                <div>
                    <h3 id="rfq-products-title">Requested products</h3>
                    <p>{{ $inquiry->items->count() }} {{ $inquiry->items->count() === 1 ? 'product' : 'products' }} · {{ number_format($totalQuantity, 0) }} total units</p>
                </div>
                <span>Unit price <b>*</b></span>
            </div>

            <div class="ft-public-rfq-items">
                @foreach($inquiry->items as $item)
                    @php($existingPrice = $quote?->items?->firstWhere('inquiry_item_id', $item->id)?->unit_price)
                    <div class="ft-public-rfq-item" data-rfq-item data-quantity="{{ (float) $item->quantity }}">
                        <div class="ft-public-rfq-item-main">
                            <div class="ft-public-rfq-item-index">{{ $loop->iteration }}</div>
                            <div class="ft-public-rfq-item-copy">
                                <strong>{{ $item->item_name }}</strong>
                                <span>{{ $item->category ?: 'Product' }} <i>·</i> {{ number_format((float) $item->quantity, 0) }} {{ $item->unit ?: 'units' }}</span>
                            </div>
                        </div>
                        <label class="ft-public-rfq-price-field">
                            <span class="sr-only">Unit price for {{ $item->item_name }}</span>
                            <span class="ft-public-rfq-currency-prefix" data-currency-prefix>{{ $currency }}</span>
                            <input
                                type="number"
                                name="prices[{{ $item->id }}]"
                                min="0"
                                step="0.0001"
                                inputmode="decimal"
                                value="{{ old('prices.'.$item->id, $existingPrice) }}"
                                placeholder="0.00"
                                required
                                data-rfq-price
                                @disabled($locked)
                            >
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="ft-public-rfq-form-section ft-public-rfq-commercial" aria-labelledby="rfq-terms-title">
            <div class="ft-public-rfq-subhead">
                <div>
                    <h3 id="rfq-terms-title">Commercial terms</h3>
                    <p>Add freight, lead time and quotation validity.</p>
                </div>
            </div>

            <div class="ft-public-rfq-grid">
                <label>
                    <span>Currency <b>*</b></span>
                    <select name="currency" required data-rfq-currency @disabled($locked)>
                        <option value="USD" @selected($currency === 'USD')>USD — US Dollar</option>
                        <option value="EUR" @selected($currency === 'EUR')>EUR — Euro</option>
                        <option value="GBP" @selected($currency === 'GBP')>GBP — British Pound</option>
                        <option value="CNY" @selected($currency === 'CNY')>CNY — Chinese Yuan</option>
                    </select>
                </label>
                <label>
                    <span>Freight</span>
                    <div class="ft-public-rfq-input-prefix"><span data-currency-prefix>{{ $currency }}</span><input type="number" name="freight" min="0" step="0.01" inputmode="decimal" value="{{ old('freight', $quote?->freight ?? 0) }}" placeholder="0.00" data-rfq-freight @disabled($locked)></div>
                </label>
                <label>
                    <span>Lead time <small>days</small></span>
                    <input type="number" name="lead_time_days" min="0" inputmode="numeric" value="{{ old('lead_time_days', $quote?->lead_time_days) }}" placeholder="e.g. 14" @disabled($locked)>
                </label>
                <label>
                    <span>Quote validity <small>days</small></span>
                    <input type="number" name="validity_days" min="0" inputmode="numeric" value="{{ old('validity_days', $quote?->validity_days) }}" placeholder="e.g. 30" @disabled($locked)>
                </label>
                <label class="ft-public-rfq-wide">
                    <span>Notes <small>optional</small></span>
                    <textarea name="notes" rows="4" placeholder="Add production notes, MOQ information, exclusions or other terms…" @disabled($locked)>{{ old('notes', $quote?->notes) }}</textarea>
                </label>
            </div>
        </section>

        @unless($locked)
            <div class="ft-public-rfq-review-bar" aria-live="polite">
                <div class="ft-public-rfq-total">
                    <small>Estimated quotation total</small>
                    <strong><span data-rfq-total-currency>{{ $currency }}</span> <span data-rfq-total>—</span></strong>
                    <span>Product subtotal + freight</span>
                </div>
                <div class="ft-public-rfq-actions">
                    @unless($quote)
                        <button type="submit" name="action" value="decline" class="ft-rfq-public-secondary" formnovalidate data-rfq-decline>Decline</button>
                    @endunless
                    <button type="submit" name="action" value="submit" class="ft-rfq-public-primary">
                        <span>{{ $quote ? 'Update quotation' : 'Submit quotation' }}</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.3 16.6L4.7 12l-1.4 1.4 6 6L21 7.7l-1.4-1.4-10.3 10.3z"/></svg>
                    </button>
                </div>
            </div>
            <p class="ft-public-rfq-security-note">By submitting, you confirm that the pricing and commercial terms above are accurate for this request.</p>
        @else
            <div class="ft-rfq-public-note">
                <strong>{{ $submitted ? 'Quotation submitted' : 'This RFQ is closed' }}</strong>
                <span>{{ $submitted ? 'Your latest quotation is shown above. Contact the buyer if a change is required after the request has been closed.' : 'This request can no longer be edited.' }}</span>
            </div>
        @endunless
    </form>

    <footer class="ft-public-rfq-footer">
        <span>Secure supplier response for {{ $inquiry->inquiry_number }}</span>
        <span>Powered by {{ $brand['name'] ?? 'FlowTrack' }}</span>
    </footer>
</main>

@if(!$locked)
<script>
(() => {
    const form = document.getElementById('rfq-quotation-form');
    if (!form) return;

    const currency = form.querySelector('[data-rfq-currency]');
    const priceInputs = Array.from(form.querySelectorAll('[data-rfq-price]'));
    const freight = form.querySelector('[data-rfq-freight]');
    const total = form.querySelector('[data-rfq-total]');
    const totalCurrency = form.querySelector('[data-rfq-total-currency]');
    const prefixes = Array.from(form.querySelectorAll('[data-currency-prefix]'));

    const recalculate = () => {
        const code = currency?.value || 'USD';
        prefixes.forEach(node => { node.textContent = code; });
        if (totalCurrency) totalCurrency.textContent = code;

        let subtotal = 0;
        let hasPrice = false;
        priceInputs.forEach(input => {
            const row = input.closest('[data-rfq-item]');
            const quantity = Number(row?.dataset.quantity || 0);
            const price = Number(input.value);
            if (input.value !== '' && Number.isFinite(price)) {
                subtotal += quantity * price;
                hasPrice = true;
            }
        });
        const freightValue = Number(freight?.value || 0);
        if (total) total.textContent = hasPrice
            ? (subtotal + (Number.isFinite(freightValue) ? freightValue : 0)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : '—';
    };

    currency?.addEventListener('change', recalculate);
    freight?.addEventListener('input', recalculate);
    priceInputs.forEach(input => input.addEventListener('input', recalculate));
    form.querySelector('[data-rfq-decline]')?.addEventListener('click', event => {
        if (!window.confirm('Decline this request for quotation?')) event.preventDefault();
    });
    recalculate();
})();
</script>
@endif
</body>
</html>
