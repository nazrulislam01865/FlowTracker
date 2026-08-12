@props([
    'label',
    'property',
    'type',
    'value' => '',
    'context' => '',
    'placeholder' => 'All',
    'initialOptions' => collect(),
    'selectedLabel' => null,
    'params' => [],
    'disabled' => false,
    'clearable' => true,
    'action' => null,
    'menuWidth' => 300,
    'fixedMenu' => false,
])
@php
    $items = collect($initialOptions)->map(fn ($item) => is_array($item) ? $item : (array) $item)->values();
    $selected = $items->first(fn ($item) => (string)($item['id'] ?? '') === (string)$value);
    $resolvedLabel = $selectedLabel ?: ($selected['label'] ?? $placeholder);
@endphp
<div {{ $attributes->class(['ft-jl-control', 'ft-remote-filter', 'is-disabled' => $disabled]) }} x-data="window.FlowTrackRemoteFilter({
    property: @js($property),
    type: @js($type),
    context: @js($context),
    value: @js((string)$value),
    placeholder: @js($placeholder),
    selectedLabel: @js($resolvedLabel),
    endpoint: @js(route('filter-options.index', ['type' => $type])),
    initialItems: @js($items->all()),
    params: @js($params),
    disabled: @js((bool)$disabled),
    menuWidth: @js((int)$menuWidth),
    fixedMenu: @js((bool)$fixedMenu),
})" x-effect="syncSelection(@js(['value' => (string)$value, 'label' => $resolvedLabel]), @js($params), @js($items->all()))" x-on:keydown.escape.window="close()" x-on:resize.window="open && reposition()" x-on:scroll.window="open && reposition()">
    <label>{{ $label }}</label>
    <button x-ref="trigger" type="button" class="ft-remote-filter-button" x-on:click="toggle()" :aria-expanded="open.toString()" @disabled($disabled)>
        <span x-text="selectedLabel"></span><span class="ft-filter-chevron" aria-hidden="true">⌄</span>
    </button>
    <div x-ref="menu" class="ft-remote-filter-menu" x-cloak x-show="open" x-bind:style="menuStyle" x-on:click.outside="close()" x-on:keydown.arrow-down.prevent="moveOption(1)" x-on:keydown.arrow-up.prevent="moveOption(-1)">
        <input x-ref="search" class="ft-remote-filter-search" type="text" role="searchbox" inputmode="search" x-model="query" x-on:input.debounce.300ms="searchOptions()" x-on:keydown.arrow-down.prevent="focusFirst()" placeholder="Search {{ strtolower($label) }}…" autocomplete="off">
        @if($clearable)
            <button type="button" class="ft-remote-filter-option ft-remote-filter-clear" x-show="selectedValue"
                x-on:click="clearSelection(); Promise.resolve(@if($action) $wire.call(@js($action), @js($property), '') @else $wire.set(@js($property), '') @endif).catch(() => selectionFailed())"><span>{{ $placeholder }}</span><small>Clear</small></button>
        @endif
        <div class="ft-remote-filter-list" role="listbox">
            <template x-if="loading"><div><div class="ft-filter-skeleton"></div><div class="ft-filter-skeleton"></div></div></template>
            <template x-if="!loading && items.length === 0"><div class="ft-remote-filter-message">No matching options</div></template>
            <template x-for="item in items" :key="item.id">
                <button type="button" class="ft-remote-filter-option" :aria-selected="String(item.id) === String(selectedValue)"
                    x-on:click="select(item); Promise.resolve(@if($action) $wire.call(@js($action), @js($property), String(item.id)) @else $wire.set(@js($property), String(item.id)) @endif).catch(() => selectionFailed())">
                    <span x-text="item.label"></span><small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <div class="ft-remote-filter-message" x-text="message"></div>
    </div>
</div>
