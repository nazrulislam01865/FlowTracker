<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['value']));

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

foreach (array_filter((['value']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $sizeValue = trim((string) $value); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sizeValue === ''): ?>
    <span class="ft-product-size-empty">—</span>
<?php else: ?>
    <div class="ft-product-size-control" x-data="{ copied: false, left: 0, top: 0 }">
        <button
            x-ref="trigger"
            type="button"
            class="ft-product-size-trigger"
            title="View full size details"
            x-on:click="
                const r = $refs.trigger.getBoundingClientRect();
                const w = Math.min(360, window.innerWidth - 24);
                left = Math.max(12, Math.min(r.left, window.innerWidth - w - 12));
                top = r.bottom + 8;
                if (top + 180 > window.innerHeight) top = Math.max(12, r.top - 188);
                $refs.panel.style.left = left + 'px';
                $refs.panel.style.top = top + 'px';
                $refs.panel.style.width = w + 'px';
                $refs.panel.showPopover();
            "
        ><?php echo e($sizeValue); ?></button>
        <div x-ref="panel" popover="auto" class="ft-product-size-popover">
            <strong>Full size details</strong>
            <p><?php echo e($sizeValue); ?></p>
            <button type="button" x-on:click="navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($sizeValue)->toHtml() ?>); copied=true; setTimeout(()=>copied=false,1600)" x-text="copied ? 'Copied' : 'Copy details'"></button>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/product-size.blade.php ENDPATH**/ ?>