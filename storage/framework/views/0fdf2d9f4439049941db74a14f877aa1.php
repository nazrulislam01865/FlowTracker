<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
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
?>
<div <?php echo e($attributes->class(['ft-jl-control', 'ft-remote-filter', 'ft-local-filter', 'is-disabled' => $disabled])); ?>

    x-data="window.FlowTrackLocalFilter({
        property: <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>,
        value: <?php echo \Illuminate\Support\Js::from((string)$value)->toHtml() ?>,
        placeholder: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>,
        selectedLabel: <?php echo \Illuminate\Support\Js::from($resolvedLabel)->toHtml() ?>,
        items: <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>,
        disabled: <?php echo \Illuminate\Support\Js::from((bool)$disabled)->toHtml() ?>,
        menuWidth: <?php echo \Illuminate\Support\Js::from((int)$menuWidth)->toHtml() ?>,
        fixedMenu: <?php echo \Illuminate\Support\Js::from((bool)$fixedMenu)->toHtml() ?>,
    })"
    x-effect="syncOptions(<?php echo \Illuminate\Support\Js::from((string)$value)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($resolvedLabel)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>, <?php echo \Illuminate\Support\Js::from((bool)$disabled)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>)"
    x-on:keydown.escape.window="close()"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($hideLabel)): ?>
        <label><?php echo e($label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?><i class="ft-filter-required">*</i><?php elseif($optional): ?><em class="ft-filter-optional">Optional</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <button x-ref="trigger" type="button" class="ft-remote-filter-button" aria-label="<?php echo e($label); ?>" x-on:click="toggle()" :aria-expanded="open.toString()" <?php if($disabled): echo 'disabled'; endif; ?>>
        <span x-text="selectedLabel"></span><span class="ft-filter-chevron" aria-hidden="true">⌄</span>
    </button>
    <div x-ref="menu" class="ft-remote-filter-menu ft-local-filter-menu" x-cloak x-show="open"
        x-bind:style="menuStyle"
        x-on:click.outside="close()"
        x-on:keydown.arrow-down.prevent="moveOption(1)"
        x-on:keydown.arrow-up.prevent="moveOption(-1)">
        <input x-ref="search" class="ft-remote-filter-search" type="text" role="searchbox" inputmode="search"
            x-model="query" x-on:keydown.arrow-down.prevent="focusFirst()"
            placeholder="<?php echo e($resolvedSearchPlaceholder); ?>" autocomplete="off">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearable): ?>
            <button type="button" class="ft-remote-filter-option ft-remote-filter-clear" :aria-selected="selectedValue === ''" x-on:click="choose('', <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>); <?php if($action): ?> $wire.$call(<?php echo \Illuminate\Support\Js::from($action)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, '') <?php else: ?> $wire.$set(<?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, '') <?php endif; ?>">
                <span><?php echo e($placeholder); ?></span><small x-show="selectedValue === ''">Selected</small>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-remote-filter-list" role="listbox">
            <template x-if="filteredItems.length === 0">
                <div class="ft-remote-filter-message">No matching options</div>
            </template>
            <template x-for="item in filteredItems" :key="item.id">
                <button type="button" class="ft-remote-filter-option" :aria-selected="String(item.id) === String(selectedValue)" x-on:click="choose(String(item.id), item.label); <?php if($action): ?> $wire.$call(<?php echo \Illuminate\Support\Js::from($action)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, String(item.id)) <?php else: ?> $wire.$set(<?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, String(item.id)) <?php endif; ?>">
                    <span x-text="item.label"></span><small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerMessage): ?><div class="ft-remote-filter-message"><?php echo e($footerMessage); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/select-filter.blade.php ENDPATH**/ ?>