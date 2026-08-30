@props(['job', 'context' => []])
@php
    $record = $context['displayRecord'] ?? null;
    $isDiscountScope = $record?->scope === 'discount';
    $currency = (string) ($record?->originalOrder?->currency ?: $job->currency ?: 'USD');
    $money = fn ($value) => ($currency === 'USD' ? '$' : $currency.' ').number_format((float) $value, 2);
@endphp

@if($record)
    <section class="ft-redo-finance-review">
        <div class="ft-redo-card">
            <header class="ft-redo-cardhead">
                <div>
                    <h2>{{ $isDiscountScope ? 'Customer discount adjustment' : 'Redo financial adjustment' }}</h2>
                    <small>
                        {{ $isDiscountScope
                            ? (($record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber()).' · no redo Order created · original invoice and payments unchanged')
                            : (($record->redoOrder?->displayOrderNumber() ?? 'Redo order').' · original invoice and payments unchanged') }}
                    </small>
                </div>
                <button type="button" class="btn small" wire:click="setDetailTab('redo')">View redo details</button>
            </header>
            <div class="ft-redo-cardbody">
                <table class="ft-redo-fin-table">
                    <tr><td>Affected order value</td><td>{{ $money($record->affected_order_value) }}</td></tr>
                    <tr><td>Customer charge / credit</td><td>{{ $record->customer_resolution === 'discount' ? '-'.$money($record->customer_impact) : $money(0) }}</td></tr>
                    <tr><td>{{ $isDiscountScope ? 'Supplier recovery' : 'Supplier redo charge' }}</td><td>{{ $money($record->supplier_redo_charge) }}</td></tr>
                    <tr><td>Freight deduction</td><td>{{ $money($record->freight_amount) }}</td></tr>
                    <tr class="total"><td>Total supplier recovery</td><td>{{ $money($record->total_supplier_recovery) }}</td></tr>
                </table>
            </div>
        </div>
    </section>
@endif
