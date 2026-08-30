@props(['job', 'context' => []])
@php
    $record = $context['displayRecord'] ?? null;
    $hasRedo = (bool) ($context['hasRedo'] ?? false);
    $isDiscountScope = $record?->scope === 'discount';
    $scopeLabel = match ($record?->scope) {
        'production' => 'Production',
        'discount' => 'Discount only',
        default => 'Artwork and production',
    };
    $reportedBy = trim((string) ($record?->issue_reported_by ?? ''));
    $discountPercent = rtrim(rtrim(number_format((float) ($record?->customer_discount_percent ?? 0), 2), '0'), '.');
@endphp

@if($hasRedo && $record)
    <section class="ft-redo-banner show" aria-label="Redo order notice">
        <div class="ft-redo-banner-icon">↻</div>
        <div>
            @if($isDiscountScope)
                <h3>Customer discount recorded for {{ $record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber() }}</h3>
                <p>
                    {{ $reportedBy !== '' ? $reportedBy.'-reported issue' : 'Reported issue' }}
                    · {{ $discountPercent }}% client discount
                    · {{ number_format((int) $record->affected_quantity) }} units affected
                    · workflow remains unchanged.
                </p>
            @else
                <h3>Redo order created from {{ $record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber() }}</h3>
                <p>
                    {{ $reportedBy !== '' ? $reportedBy.'-reported issue' : 'Reported issue' }}
                    · {{ $scopeLabel }} will be repeated
                    · {{ number_format((int) $record->redo_quantity) }} units
                    · financial recovery recorded.
                </p>
            @endif
        </div>
        <button type="button" class="btn small" wire:click="setDetailTab('redo')">View redo details</button>
    </section>
@endif
