@props([
    'label',
    'property',
    'value' => '',
    'placeholder' => 'All',
    'options' => collect(),
    'selectedLabel' => null,
    'disabled' => false,
    'clearable' => true,
])
@php
    $items = collect($options)->map(function ($item) {
        if (is_array($item)) {
            return [
                'id' => (string)($item['id'] ?? $item['value'] ?? $item['name'] ?? ''),
                'label' => (string)($item['label'] ?? $item['name'] ?? $item['value'] ?? $item['id'] ?? ''),
                'meta' => (string)($item['meta'] ?? ''),
            ];
        }

        if (is_object($item)) {
            return [
                'id' => (string)($item->id ?? $item->value ?? $item->name ?? ''),
                'label' => (string)($item->label ?? $item->name ?? $item->value ?? $item->id ?? ''),
                'meta' => (string)($item->meta ?? ''),
            ];
        }

        return ['id' => (string)$item, 'label' => (string)$item, 'meta' => ''];
    })->values();

    $normaliseFilterValue = static function ($candidate): string {
        $candidate = strtolower(trim((string) $candidate));
        $candidate = preg_replace('/[_-]+/', ' ', $candidate) ?? $candidate;
        $candidate = preg_replace('/\s+/', ' ', $candidate) ?? $candidate;

        return trim($candidate);
    };

    $selected = $items->first(function ($item) use ($value, $normaliseFilterValue) {
        return (string) $item['id'] === (string) $value
            || (
                (string) $value !== ''
                && $normaliseFilterValue($item['id']) === $normaliseFilterValue($value)
            );
    });
    $resolvedLabel = $selectedLabel ?: ($selected['label'] ?? ($value !== '' ? (string) $value : $placeholder));
@endphp
<div {{ $attributes->class(['ft-jl-control', 'ft-remote-filter', 'ft-local-filter', 'is-disabled' => $disabled]) }}
    x-data="window.FlowTrackLocalFilter({
        property: @js($property),
        value: @js((string)$value),
        placeholder: @js($placeholder),
        selectedLabel: @js($resolvedLabel),
        items: @js($items->all()),
        disabled: @js((bool)$disabled),
    })"
    x-effect="sync(@js((string)$value), @js($resolvedLabel))"
    x-on:keydown.escape.window="close()"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()">
    <label>{{ $label }}</label>
    <button x-ref="trigger" type="button" class="ft-remote-filter-button" x-on:click="toggle()" :aria-expanded="open.toString()" @disabled($disabled)>
        <span x-text="selectedLabel"></span><span class="ft-filter-chevron" aria-hidden="true">⌄</span>
    </button>
    <div x-ref="menu" class="ft-remote-filter-menu ft-local-filter-menu" x-cloak x-show="open"
        x-bind:style="menuStyle"
        x-on:click.outside="close()"
        x-on:keydown.arrow-down.prevent="moveOption(1)"
        x-on:keydown.arrow-up.prevent="moveOption(-1)">
        @if($clearable)
            <button type="button" class="ft-remote-filter-option ft-remote-filter-clear" :aria-selected="selectedValue === ''" x-on:click="choose('', @js($placeholder)); $wire.set(@js($property), '')">
                <span>{{ $placeholder }}</span><small x-show="selectedValue === ''">Selected</small>
            </button>
        @endif
        <div class="ft-remote-filter-list" role="listbox">
            <template x-for="item in items" :key="item.id">
                <button type="button" class="ft-remote-filter-option" :aria-selected="String(item.id) === String(selectedValue)" x-on:click="choose(String(item.id), item.label); $wire.set(@js($property), String(item.id))">
                    <span x-text="item.label"></span><small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
    </div>
</div>
