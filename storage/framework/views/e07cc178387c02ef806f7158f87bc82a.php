<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'itemId' => null,
    'canDelete' => false,
    'removeMethod' => '',
    'confirmText' => 'Remove this product?',
    'disabled' => false,
    'disabledTitle' => 'This product cannot be removed.',
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
    'itemId' => null,
    'canDelete' => false,
    'removeMethod' => '',
    'confirmText' => 'Remove this product?',
    'disabled' => false,
    'disabledTitle' => 'This product cannot be removed.',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemId && $canDelete && $removeMethod !== ''): ?>
    <?php ($target = $removeMethod.'('.(int) $itemId.')'); ?>
    <div class="ft-order-product-row-menu" x-on:click.outside="actionOpen = false">
        <button type="button" class="ft-order-product-kebab" x-on:click.stop="actionOpen = !actionOpen" :aria-expanded="actionOpen.toString()" aria-label="Product actions">⋮</button>
        <div class="ft-order-product-menu-popover" x-cloak x-show="actionOpen" x-transition.opacity>
            <button
                type="button"
                wire:click.stop="<?php echo e($target); ?>"
                wire:confirm="<?php echo e($confirmText); ?>"
                wire:loading.attr="disabled"
                wire:target="<?php echo e($target); ?>"
                x-on:click="actionOpen = false"
                :disabled="categorySaving || productSaving || quantitySaving || priceSaving || notesSaving || <?php echo \Illuminate\Support\Js::from($disabled)->toHtml() ?>"
                <?php if($disabled): ?> title="<?php echo e($disabledTitle); ?>" <?php endif; ?>
            >Remove product</button>
        </div>
    </div>
<?php else: ?>
    <span class="ft-order-product-action-placeholder">—</span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-product-actions.blade.php ENDPATH**/ ?>