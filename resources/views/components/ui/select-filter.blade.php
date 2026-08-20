@props([
    'label',
    'property',
    'value' => '',
    'placeholder' => 'All',
    'options' => collect(),
    'selectedLabel' => null,
    'disabled' => false,
    'clearable' => true,
    'action' => null,
    'menuWidth' => 300,
    'searchPlaceholder' => null,
    'required' => false,
    'optional' => false,
    'fixedMenu' => false,
    'footerMessage' => null,
    'hideLabel' => false,
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
    $resolvedSearchPlaceholder = $searchPlaceholder ?: 'Search '.strtolower((string) $label).'…';
@endphp
<div {{ $attributes->class(['ft-jl-control', 'ft-remote-filter', 'ft-local-filter', 'is-disabled' => $disabled]) }}
    x-data="window.FlowTrackLocalFilter({
        property: @js($property),
        value: @js((string)$value),
        placeholder: @js($placeholder),
        selectedLabel: @js($resolvedLabel),
        items: @js($items->all()),
        disabled: @js((bool)$disabled),
        menuWidth: @js((int)$menuWidth),
        fixedMenu: @js((bool)$fixedMenu),
    })"
    x-effect="syncOptions(@js((string)$value), @js($resolvedLabel), @js($items->all()), @js((bool)$disabled), @js($placeholder))"
    x-on:keydown.escape.window="close()"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()">
    @unless($hideLabel)
        <label>{{ $label }}@if($required)<i class="ft-filter-required">*</i>@elseif($optional)<em class="ft-filter-optional">Optional</em>@endif</label>
    @endunless
    <button x-ref="trigger" type="button" class="ft-remote-filter-button" aria-label="{{ $label }}" x-on:click="toggle()" :aria-expanded="open.toString()" @disabled($disabled)>
        <span x-text="selectedLabel"></span><span class="ft-filter-chevron" aria-hidden="true">⌄</span>
    </button>
    <div x-ref="menu" class="ft-remote-filter-menu ft-local-filter-menu" x-cloak x-show="open"
        x-bind:style="menuStyle"
        x-on:click.outside="close()"
        x-on:keydown.arrow-down.prevent="moveOption(1)"
        x-on:keydown.arrow-up.prevent="moveOption(-1)">
        <input x-ref="search" class="ft-remote-filter-search" type="text" role="searchbox" inputmode="search"
            x-model="query" x-on:keydown.arrow-down.prevent="focusFirst()"
            placeholder="{{ $resolvedSearchPlaceholder }}" autocomplete="off">
        @if($clearable)
            <button type="button" class="ft-remote-filter-option ft-remote-filter-clear" :aria-selected="selectedValue === ''" x-on:click="choose('', @js($placeholder)); @if($action) $wire.$call(@js($action), @js($property), '') @else $wire.$set(@js($property), '') @endif">
                <span>{{ $placeholder }}</span><small x-show="selectedValue === ''">Selected</small>
            </button>
        @endif
        <div class="ft-remote-filter-list" role="listbox">
            <template x-if="filteredItems.length === 0">
                <div class="ft-remote-filter-message">No matching options</div>
            </template>
            <template x-for="item in filteredItems" :key="item.id">
                <button type="button" class="ft-remote-filter-option" :aria-selected="String(item.id) === String(selectedValue)" x-on:click="choose(String(item.id), item.label); @if($action) $wire.$call(@js($action), @js($property), String(item.id)) @else $wire.$set(@js($property), String(item.id)) @endif">
                    <span x-text="item.label"></span><small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        @if($footerMessage)<div class="ft-remote-filter-message">{{ $footerMessage }}</div>@endif
    </div>
</div>
