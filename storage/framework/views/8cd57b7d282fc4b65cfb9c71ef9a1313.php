<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $items = collect($initialOptions)->map(fn ($item) => is_array($item) ? $item : (array) $item)->values();
    $selected = $items->first(fn ($item) => (string)($item['id'] ?? '') === (string)$value);
    $resolvedLabel = $selectedLabel ?: ($selected['label'] ?? $placeholder);
?>
<div <?php echo e($attributes->class(['ft-jl-control', 'ft-remote-filter', 'is-disabled' => $disabled])); ?> x-data="window.FlowTrackRemoteFilter({
    property: <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>,
    type: <?php echo \Illuminate\Support\Js::from($type)->toHtml() ?>,
    context: <?php echo \Illuminate\Support\Js::from($context)->toHtml() ?>,
    value: <?php echo \Illuminate\Support\Js::from((string)$value)->toHtml() ?>,
    placeholder: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>,
    selectedLabel: <?php echo \Illuminate\Support\Js::from($resolvedLabel)->toHtml() ?>,
    endpoint: <?php echo \Illuminate\Support\Js::from(route('filter-options.index', ['type' => $type]))->toHtml() ?>,
    initialItems: <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>,
    params: <?php echo \Illuminate\Support\Js::from($params)->toHtml() ?>,
    disabled: <?php echo \Illuminate\Support\Js::from((bool)$disabled)->toHtml() ?>,
    menuWidth: <?php echo \Illuminate\Support\Js::from((int)$menuWidth)->toHtml() ?>,
    fixedMenu: <?php echo \Illuminate\Support\Js::from((bool)$fixedMenu)->toHtml() ?>,
})" x-effect="syncSelection(<?php echo \Illuminate\Support\Js::from(['value' => (string)$value, 'label' => $resolvedLabel])->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($params)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>)" x-on:keydown.escape.window="close()" x-on:resize.window="open && reposition()" x-on:scroll.window="open && reposition()">
    <label><?php echo e($label); ?></label>
    <button x-ref="trigger" type="button" class="ft-remote-filter-button" x-on:click="toggle()" :aria-expanded="open.toString()" <?php if($disabled): echo 'disabled'; endif; ?>>
        <span x-text="selectedLabel"></span><span class="ft-filter-chevron" aria-hidden="true">⌄</span>
    </button>
    <div x-ref="menu" class="ft-remote-filter-menu" x-cloak x-show="open" x-bind:style="menuStyle" x-on:click.outside="close()" x-on:keydown.arrow-down.prevent="moveOption(1)" x-on:keydown.arrow-up.prevent="moveOption(-1)">
        <input x-ref="search" class="ft-remote-filter-search" type="text" role="searchbox" inputmode="search" x-model="query" x-on:input.debounce.300ms="searchOptions()" x-on:keydown.arrow-down.prevent="focusFirst()" placeholder="Search <?php echo e(strtolower($label)); ?>…" autocomplete="off">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearable): ?>
            <button type="button" class="ft-remote-filter-option ft-remote-filter-clear" x-show="selectedValue"
                x-on:click="clearSelection(); Promise.resolve(<?php if($action): ?> $wire.call(<?php echo \Illuminate\Support\Js::from($action)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, '') <?php else: ?> $wire.set(<?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, '') <?php endif; ?>).catch(() => selectionFailed())"><span><?php echo e($placeholder); ?></span><small>Clear</small></button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-remote-filter-list" role="listbox">
            <template x-if="loading"><div><div class="ft-filter-skeleton"></div><div class="ft-filter-skeleton"></div></div></template>
            <template x-if="!loading && items.length === 0"><div class="ft-remote-filter-message">No matching options</div></template>
            <template x-for="item in items" :key="item.id">
                <button type="button" class="ft-remote-filter-option" :aria-selected="String(item.id) === String(selectedValue)"
                    x-on:click="select(item); Promise.resolve(<?php if($action): ?> $wire.call(<?php echo \Illuminate\Support\Js::from($action)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, String(item.id)) <?php else: ?> $wire.set(<?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, String(item.id)) <?php endif; ?>).catch(() => selectionFailed())">
                    <span x-text="item.label"></span><small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <div class="ft-remote-filter-message" x-text="message"></div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/remote-filter.blade.php ENDPATH**/ ?>