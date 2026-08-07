@props([
    'value' => '',
    'selectedLabel' => null,
    'placeholder' => 'Unassigned',
    'context' => 'task-assignee',
    'triggerClass' => '',
    'variant' => 'compact',
    'menuWidth' => 300,
])
@php
    $resolvedLabel = $selectedLabel ?: $placeholder;
@endphp
<div
    {{ $attributes->class(['ft-inline-remote-user', 'ft-inline-remote-user-'.$variant]) }}
    data-ft-inline-remote-picker
    x-data="window.FlowTrackRemoteFilter({
        property: '',
        type: 'users',
        context: @js($context),
        value: @js((string) $value),
        placeholder: @js($placeholder),
        selectedLabel: @js($resolvedLabel),
        endpoint: @js(route('filter-options.index', ['type' => 'users'])),
        initialItems: [],
        params: {},
        disabled: false,
        menuWidth: @js((int) $menuWidth),
    })"
    x-on:ft-inline-remote-open.stop="sync(String($event.detail?.value ?? ''), String($event.detail?.label ?? @js($placeholder))); openMenu()"
    x-on:keydown.escape.window="if (open) { close(); $dispatch('ft-inline-remote-cancel') }"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()"
>
    <button
        x-ref="trigger"
        type="button"
        class="{{ trim($triggerClass.' ft-inline-remote-user-trigger') }}"
        x-on:click.stop="toggle()"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
    >
        <span x-text="selectedLabel">{{ $resolvedLabel }}</span>
        <span class="ft-filter-chevron" aria-hidden="true">⌄</span>
    </button>

    <div
        x-ref="menu"
        class="ft-remote-filter-menu ft-inline-remote-user-menu"
        x-cloak
        x-show="open"
        x-bind:style="menuStyle"
        x-on:click.outside="close()"
        x-on:keydown.arrow-down.prevent="moveOption(1)"
        x-on:keydown.arrow-up.prevent="moveOption(-1)"
    >
        <input
            x-ref="search"
            class="ft-remote-filter-search"
            type="text"
            role="searchbox"
            inputmode="search"
            x-model="query"
            x-on:input.debounce.300ms="searchOptions()"
            x-on:keydown.arrow-down.prevent="focusFirst()"
            placeholder="Search assignee…"
            autocomplete="off"
        >

        <button
            type="button"
            class="ft-remote-filter-option ft-remote-filter-clear"
            x-show="selectedValue"
            x-on:click="clearSelection(); $dispatch('ft-inline-remote-selected', { value: '', label: @js($placeholder) })"
        >
            <span>{{ $placeholder }}</span><small>Clear</small>
        </button>

        <div class="ft-remote-filter-list" role="listbox">
            <template x-if="loading">
                <div><div class="ft-filter-skeleton"></div><div class="ft-filter-skeleton"></div></div>
            </template>
            <template x-if="!loading && items.length === 0">
                <div class="ft-remote-filter-message">No matching options</div>
            </template>
            <template x-for="item in items" :key="item.id">
                <button
                    type="button"
                    class="ft-remote-filter-option"
                    :aria-selected="String(item.id) === String(selectedValue)"
                    x-on:click="select(item); $dispatch('ft-inline-remote-selected', { value: String(item.id), label: item.label })"
                >
                    <span x-text="item.label"></span>
                    <small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <div class="ft-remote-filter-message" x-text="message"></div>
    </div>
</div>
