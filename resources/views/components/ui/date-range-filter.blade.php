@props([
    'fromProperty' => 'dateFrom',
    'toProperty' => 'dateTo',
    'fromValue' => '',
    'toValue' => '',
    'label' => 'Created date',
])

@php
    $active = filled($fromValue) || filled($toValue);
@endphp

@once
    <style>
        .ft-date-range-filter{display:flex;min-width:0;align-items:flex-end;padding:7px 10px;border-bottom:1px solid #dbe3ed;background:#fbfcfe;color:#33445d;font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
        .ft-date-range-filter.is-active{background:#f8fbff}
        .ft-date-range-fields{display:flex;min-width:0;align-items:end;gap:8px;flex-wrap:wrap}
        .ft-date-range-field{display:grid;gap:3px;min-width:148px;color:#64748b;font-size:8px;font-weight:750;line-height:1.1}
        .ft-date-range-field input{width:158px;height:34px;min-height:34px;padding:0 9px;border:1px solid #cfd9e7;border-radius:8px;outline:0;background:#fff;color:#26364f;font:inherit;font-size:10px;font-weight:650;line-height:34px;color-scheme:light}
        .ft-date-range-field input:hover{border-color:#aebed2}
        .ft-date-range-field input:focus{border-color:#7ba2f3;box-shadow:0 0 0 3px rgba(36,99,235,.08)}
        .ft-date-range-filter.is-active .ft-date-range-field input{border-color:#b7cbef}
        @media(max-width:680px){
            .ft-date-range-filter{padding:9px 10px}
            .ft-date-range-fields{display:grid;width:100%;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px}
            .ft-date-range-field{min-width:0}
            .ft-date-range-field input{width:100%}
        }
        @media(max-width:420px){.ft-date-range-fields{grid-template-columns:1fr}}
    </style>
@endonce

<div {{ $attributes->class(['ft-date-range-filter', 'is-active' => $active]) }} role="group" aria-label="{{ $label }} range filter">
    <div class="ft-date-range-fields">
        <label class="ft-date-range-field">
            <span>Date from</span>
            <input
                type="date"
                lang="en-GB"
                wire:model.live="{{ $fromProperty }}"
                @if(filled($toValue)) max="{{ $toValue }}" @endif
                aria-label="{{ $label }} from"
            >
        </label>
        <label class="ft-date-range-field">
            <span>Date to</span>
            <input
                type="date"
                lang="en-GB"
                wire:model.live="{{ $toProperty }}"
                @if(filled($fromValue)) min="{{ $fromValue }}" @endif
                aria-label="{{ $label }} to"
            >
        </label>
    </div>
</div>
