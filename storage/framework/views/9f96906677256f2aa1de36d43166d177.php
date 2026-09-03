<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label', 'property', 'type' => null, 'context' => '', 'values' => [], 'options' => [],
    'initialOptions' => [], 'placeholder' => 'Select options', 'params' => [], 'disabled' => false,
    'menuWidth' => 320, 'fixedMenu' => false, 'maxSelected' => 100,
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
    'label', 'property', 'type' => null, 'context' => '', 'values' => [], 'options' => [],
    'initialOptions' => [], 'placeholder' => 'Select options', 'params' => [], 'disabled' => false,
    'menuWidth' => 320, 'fixedMenu' => false, 'maxSelected' => 100,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $remote = filled($type);
    $items = collect($remote ? $initialOptions : ($options ?: $initialOptions))->map(fn ($item) => [
        'id' => (string) data_get($item, 'id', data_get($item, 'value', data_get($item, 'name', ''))),
        'label' => (string) data_get($item, 'label', data_get($item, 'name', data_get($item, 'value', data_get($item, 'id', '')))),
        'meta' => (string) data_get($item, 'meta', ''),
    ])->filter(fn ($item) => $item['id'] !== '')->values();
    $values = collect($values)->map(fn ($value) => (string) $value)->filter()->unique()->values();
    $componentId = 'ft-multi-select-'.substr(md5($property.'|'.$type.'|'.$context.'|'.$label), 0, 12);
?>
<div
    <?php echo e($attributes->class(['ft-multi-select', 'is-disabled' => $disabled])); ?>

    data-ft-ui-component="multi-select"
    x-data="window.FlowTrack.ui.multiSelect({
        property: <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>,
        endpoint: <?php echo \Illuminate\Support\Js::from($remote ? route('filter-options.index', ['type' => $type]) : '')->toHtml() ?>,
        context: <?php echo \Illuminate\Support\Js::from($context)->toHtml() ?>,
        params: <?php echo \Illuminate\Support\Js::from($params)->toHtml() ?>,
        remote: <?php echo \Illuminate\Support\Js::from($remote)->toHtml() ?>,
        values: <?php echo \Illuminate\Support\Js::from($values->all())->toHtml() ?>,
        initialItems: <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>,
        placeholder: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>,
        disabled: <?php echo \Illuminate\Support\Js::from((bool) $disabled)->toHtml() ?>,
        menuWidth: <?php echo \Illuminate\Support\Js::from((int) $menuWidth)->toHtml() ?>,
        fixedMenu: <?php echo \Illuminate\Support\Js::from((bool) $fixedMenu)->toHtml() ?>,
        maxSelected: <?php echo \Illuminate\Support\Js::from(max(1, min(100, (int) $maxSelected)))->toHtml() ?>,
    })"
    x-effect="syncValues(<?php echo \Illuminate\Support\Js::from($values->all())->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($params)->toHtml() ?>)"
    x-on:keydown.escape.window="close()"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()"
>
    <label id="<?php echo e($componentId); ?>-label" class="ft-multi-select__label"><?php echo e($label); ?></label>
    <button type="button" class="ft-multi-select__trigger" x-ref="trigger" x-on:click="toggle()" :aria-expanded="open.toString()" aria-haspopup="listbox" aria-controls="<?php echo e($componentId); ?>-listbox" aria-labelledby="<?php echo e($componentId); ?>-label" <?php if($disabled): echo 'disabled'; endif; ?>>
        <span class="ft-multi-select__chips" x-show="selected.length">
            <template x-for="item in selectedItems" :key="item.id"><span class="ft-multi-select__chip" x-text="item.label"></span></template>
        </span>
        <span class="ft-multi-select__placeholder" x-show="!selected.length"><?php echo e($placeholder); ?></span>
        <span aria-hidden="true">⌄</span>
    </button>
    <div class="ft-multi-select__menu" x-ref="menu" x-cloak x-show="open" x-bind:style="menuStyle" x-on:click.outside="close()">
        <input x-ref="search" class="ft-multi-select__search" type="text" role="searchbox" x-model="query" <?php if($remote): ?> x-on:input.debounce.300ms="searchOptions()" <?php endif; ?> placeholder="Search <?php echo e(strtolower($label)); ?>…" autocomplete="off">
        <div id="<?php echo e($componentId); ?>-listbox" class="ft-multi-select__options" role="listbox" aria-multiselectable="true">
            <template x-if="loading"><div class="ft-multi-select__message">Loading…</div></template>
            <template x-if="!loading && visibleItems.length === 0"><div class="ft-multi-select__message">No matching options</div></template>
            <template x-for="item in visibleItems" :key="item.id">
                <label class="ft-multi-select__option" :aria-selected="isSelected(item.id).toString()">
                    <input type="checkbox" :checked="isSelected(item.id)" x-on:change="toggleValue(item); $wire.set(<?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, selected)" <?php if($disabled): echo 'disabled'; endif; ?>>
                    <span><b x-text="item.label"></b><small x-text="item.meta"></small></span>
                </label>
            </template>
        </div>
        <button type="button" class="ft-multi-select__load-more" x-show="remote && hasMore && !loading" x-on:click="loadMore()">Load more</button>
        <div class="ft-multi-select__message" x-text="message"></div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/multi-select.blade.php ENDPATH**/ ?>