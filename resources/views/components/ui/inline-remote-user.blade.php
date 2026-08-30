@props([
    'value' => '',
    'selectedLabel' => null,
    'placeholder' => 'Unassigned',
    'context' => 'task-assignee',
    'triggerClass' => '',
    'variant' => 'compact',
    'menuWidth' => 300,
    'fixedMenu' => true,
    'searchPlaceholder' => 'Search assignee…',
    'parentType' => '',
    'parentId' => null,
    'externalTrigger' => false,
    'instanceKey' => '',
])
@php
    $resolvedLabel = $selectedLabel ?: $placeholder;
    $listboxKey = $instanceKey !== '' ? $instanceKey : $context.'|'.$parentType.'|'.$parentId;
    $listboxId = 'ft-inline-user-list-'.substr(md5($listboxKey), 0, 10);
@endphp
<div
    {{ $attributes->class(['ft-inline-remote-user', 'ft-inline-remote-user-'.$variant]) }}
    data-ft-inline-remote-picker
    x-data="window.FlowTrack.ui.searchSelect({
        property: '',
        type: 'users',
        context: @js($context),
        value: @js((string) $value),
        placeholder: @js($placeholder),
        selectedLabel: @js($resolvedLabel),
        endpoint: @js(route('filter-options.index', ['type' => 'users'])),
        initialItems: [],
        params: { parent_type: @js((string) $parentType), parent_id: @js($parentId) },
        disabled: false,
        menuWidth: @js((int) $menuWidth),
        fixedMenu: @js((bool) $fixedMenu),
    })"
    x-on:ft-inline-remote-open.stop="externalAnchorEl = $event.detail?.anchor || null; externalAnchorRect = $event.detail?.rect || null; sync(String($event.detail?.value ?? ''), String($event.detail?.label ?? @js($placeholder))); openMenu()"
    x-on:keydown.escape.window="if (open) { close(); $dispatch('ft-inline-remote-cancel') }"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()"
>
    @unless($externalTrigger)
        <button
            x-ref="trigger"
            type="button"
            class="{{ trim($triggerClass.' ft-inline-remote-user-trigger') }}"
            x-on:click.stop="toggle()"
            :aria-expanded="open.toString()"
            aria-haspopup="listbox"
            aria-controls="{{ $listboxId }}"
        >
            <span x-text="selectedLabel">{{ $resolvedLabel }}</span>
            <span class="ft-filter-chevron" aria-hidden="true">⌄</span>
        </button>
    @endunless

    @if($fixedMenu)
        {{--
            Fixed inline pickers live inside tables/cards that may use overflow,
            containment, or content-visibility for performance. Render the menu
            at <body> level so it cannot be clipped or offset by those ancestors.
            This mirrors the shared search-select fixed-menu contract.
        --}}
        <template x-teleport="body">
    @endif
    <div
        x-ref="menu"
        class="ft-remote-filter-menu ft-inline-remote-user-menu"
        data-ft-inline-remote-menu
        x-cloak
        x-bind:style="open ? menuStyle + ';display:flex!important;' : 'display:none!important;'"
        x-on:click.outside="close()"
        x-on:keydown.arrow-down.prevent="moveOption(1)"
        x-on:keydown.arrow-up.prevent="moveOption(-1)"
        x-on:keydown.home.prevent="focusBoundary('first')"
        x-on:keydown.end.prevent="focusBoundary('last')"
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
            placeholder="{{ $searchPlaceholder }}"
            autocomplete="off"
        >

        <button
            type="button"
            class="ft-remote-filter-option ft-remote-filter-clear"
            x-show="selectedValue"
            x-on:click="clearSelection(); $dispatch('ft-inline-remote-selected', { value: '', label: @js($placeholder), avatarUrl: '' })"
        >
            <span>{{ $placeholder }}</span><small>Clear</small>
        </button>

        <div id="{{ $listboxId }}" class="ft-remote-filter-list" role="listbox">
            <template x-if="loading">
                <div><div class="ft-filter-skeleton"></div><div class="ft-filter-skeleton"></div></div>
            </template>
            <template x-if="!loading && visibleItems.length === 0">
                <div class="ft-remote-filter-message">No matching options</div>
            </template>
            <template x-for="item in visibleItems" :key="item.id">
                <button
                    type="button"
                    class="ft-remote-filter-option"
                    :aria-selected="String(item.id) === String(selectedValue)"
                    x-on:click="select(item); $dispatch('ft-inline-remote-selected', { value: String(item.id), label: item.label, avatarUrl: String(item.avatarUrl || '') })"
                >
                    <span x-text="item.label"></span>
                    <small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <button type="button" class="ft-search-select__load-more" x-show="hasMore && !loading" x-on:click="loadMore()">Load more</button>
        <div class="ft-remote-filter-message" x-text="message"></div>
    </div>
    @if($fixedMenu)
        </template>
    @endif
</div>
