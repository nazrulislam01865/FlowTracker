<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => '',
    'selectedLabel' => null,
    'placeholder' => 'Unassigned',
    'context' => 'task-assignee',
    'triggerClass' => '',
    'variant' => 'compact',
    'menuWidth' => 300,
    'searchPlaceholder' => 'Search assignee…',
    'parentType' => '',
    'parentId' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'value' => '',
    'selectedLabel' => null,
    'placeholder' => 'Unassigned',
    'context' => 'task-assignee',
    'triggerClass' => '',
    'variant' => 'compact',
    'menuWidth' => 300,
    'searchPlaceholder' => 'Search assignee…',
    'parentType' => '',
    'parentId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $resolvedLabel = $selectedLabel ?: $placeholder;
?>
<div
    <?php echo e($attributes->class(['ft-inline-remote-user', 'ft-inline-remote-user-'.$variant])); ?>

    data-ft-inline-remote-picker
    x-data="window.FlowTrackRemoteFilter({
        property: '',
        type: 'users',
        context: <?php echo \Illuminate\Support\Js::from($context)->toHtml() ?>,
        value: <?php echo \Illuminate\Support\Js::from((string) $value)->toHtml() ?>,
        placeholder: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>,
        selectedLabel: <?php echo \Illuminate\Support\Js::from($resolvedLabel)->toHtml() ?>,
        endpoint: <?php echo \Illuminate\Support\Js::from(route('filter-options.index', ['type' => 'users']))->toHtml() ?>,
        initialItems: [],
        params: { parent_type: <?php echo \Illuminate\Support\Js::from((string) $parentType)->toHtml() ?>, parent_id: <?php echo \Illuminate\Support\Js::from($parentId)->toHtml() ?> },
        disabled: false,
        menuWidth: <?php echo \Illuminate\Support\Js::from((int) $menuWidth)->toHtml() ?>,
        fixedMenu: true,
    })"
    x-on:ft-inline-remote-open.stop="sync(String($event.detail?.value ?? ''), String($event.detail?.label ?? <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>)); openMenu()"
    x-on:keydown.escape.window="if (open) { close(); $dispatch('ft-inline-remote-cancel') }"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()"
>
    <button
        x-ref="trigger"
        type="button"
        class="<?php echo e(trim($triggerClass.' ft-inline-remote-user-trigger')); ?>"
        x-on:click.stop="toggle()"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
    >
        <span x-text="selectedLabel"><?php echo e($resolvedLabel); ?></span>
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
            placeholder="<?php echo e($searchPlaceholder); ?>"
            autocomplete="off"
        >

        <button
            type="button"
            class="ft-remote-filter-option ft-remote-filter-clear"
            x-show="selectedValue"
            x-on:click="clearSelection(); $dispatch('ft-inline-remote-selected', { value: '', label: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>, avatarUrl: '' })"
        >
            <span><?php echo e($placeholder); ?></span><small>Clear</small>
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
                    x-on:click="select(item); $dispatch('ft-inline-remote-selected', { value: String(item.id), label: item.label, avatarUrl: String(item.avatarUrl || '') })"
                >
                    <span x-text="item.label"></span>
                    <small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <div class="ft-remote-filter-message" x-text="message"></div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/inline-remote-user.blade.php ENDPATH**/ ?>