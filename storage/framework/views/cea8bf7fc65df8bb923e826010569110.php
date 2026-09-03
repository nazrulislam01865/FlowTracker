<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'item' => [],
    'detail' => null,
    'supplier' => null,
    'supplierSkipped' => false,
    'index' => 0,
    'rowsProperty' => 'jobItems',
    'removeMethod' => 'removeProductRow',
    'rowKeyPrefix' => 'selected-order-product',
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
    'item' => [],
    'detail' => null,
    'supplier' => null,
    'supplierSkipped' => false,
    'index' => 0,
    'rowsProperty' => 'jobItems',
    'removeMethod' => 'removeProductRow',
    'rowKeyPrefix' => 'selected-order-product',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $itemImage = $detail?->productImageUrl();
    $itemCode = (string) ($detail?->code ?? '');
    $itemCategory = (string) ($detail?->parent?->name ?? ($item['category'] ?? ''));
    $itemName = (string) ($detail?->name ?? ($item['product'] ?? 'Product'));
    $quantityError = $errors->first("{$rowsProperty}.{$index}.quantity");
    $supplierError = $errors->first("{$rowsProperty}.{$index}.supplier_id");
    $supplierSkipped = (bool) $supplierSkipped;
    $unitPriceError = $errors->first("{$rowsProperty}.{$index}.unit_price");
    $notesError = $errors->first("{$rowsProperty}.{$index}.notes");
    $productError = $errors->first("{$rowsProperty}.{$index}.product");
?>

<article class="ft-order-selected-product-card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($rowKeyPrefix).'-'.e($item['product_id'] ?? $index).'-'.e($index).''; ?>wire:key="<?php echo e($rowKeyPrefix); ?>-<?php echo e($item['product_id'] ?? $index); ?>-<?php echo e($index); ?>">
    <header class="ft-order-selected-product-card-head">
        <div class="ft-order-selected-product-info ft-order-selected-product-card-info">
            <span class="ft-order-product-thumb">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemImage): ?>
                    <img src="<?php echo e($itemImage); ?>" alt="">
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span>
                <strong title="<?php echo e($itemName); ?>"><?php echo e($itemName); ?></strong>
                <small>SKU: <?php echo e($itemCode ?: 'N/A'); ?> <i>&bull;</i> <?php echo e($itemCategory ?: 'Uncategorized'); ?></small>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productError): ?><small class="validation-error"><?php echo e($productError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
        </div>

        <button type="button" class="ft-order-selected-product-card-remove" wire:click="<?php echo e($removeMethod); ?>(<?php echo e($index); ?>)" aria-label="Remove <?php echo e($itemName); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M8 10v7M12 10v7M16 10v7M6 7l1 14h10l1-14"/></svg>
            <span>Remove</span>
        </button>
    </header>

    <div class="ft-order-selected-product-card-fields">
        <div class="ft-order-product-card-field is-supplier">
            <span class="ft-order-product-card-label">Supplier</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplier): ?>
                <div class="ft-order-product-supplier-readonly" aria-label="Supplier from product">
                    <span class="ft-order-product-supplier-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h10v10H4zM14 10h3l3 3v4h-6z"/><circle cx="8" cy="18" r="1.5"/><circle cx="17" cy="18" r="1.5"/></svg>
                    </span>
                    <span class="ft-order-product-supplier-copy">
                        <strong><?php echo e($supplier->name); ?></strong>
                        <small>From product</small>
                    </span>
                </div>
            <?php else: ?>
                <div class="ft-order-product-supplier-missing <?php echo e($supplierSkipped ? 'is-skipped' : ''); ?>" role="<?php echo e($supplierSkipped ? 'status' : 'alert'); ?>">
                    <span class="ft-order-product-supplier-missing-icon" aria-hidden="true">!</span>
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierSkipped): ?>
                            <strong>Supplier skipped for now</strong>
                            <small>You can assign a supplier later from Order Details.</small>
                        <?php else: ?>
                            <strong>Supplier is not linked</strong>
                            <small>Link a supplier or choose the temporary skip option.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierError): ?><small class="validation-error"><?php echo e($supplierError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-product-card-field is-quantity">
            <span class="ft-order-product-card-label">Quantity</span>
            <div class="ft-order-product-card-stepper">
                <button type="button" wire:click="decrementCreateProductQuantity(<?php echo e($index); ?>)" aria-label="Decrease quantity">&minus;</button>
                <input type="number" min="1" max="999999999" wire:model.live.debounce.300ms="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.quantity" aria-label="Quantity for <?php echo e($itemName); ?>">
                <button type="button" wire:click="incrementCreateProductQuantity(<?php echo e($index); ?>)" aria-label="Increase quantity">+</button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantityError): ?><small class="validation-error"><?php echo e($quantityError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <label class="ft-order-product-card-field is-price">
            <span class="ft-order-product-card-label">Unit price <em>Optional</em></span>
            <input type="number" min="0" max="999999999999.99" step="0.01" wire:model.blur="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.unit_price" placeholder="0.00" aria-label="Unit price for <?php echo e($itemName); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unitPriceError): ?><small class="validation-error"><?php echo e($unitPriceError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>

        <label class="ft-order-product-card-field is-notes">
            <span class="ft-order-product-card-label">Notes <em>Optional</em></span>
            <input type="text" maxlength="2000" wire:model.blur="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.notes" placeholder="Add notes for this product" aria-label="Notes for <?php echo e($itemName); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notesError): ?><small class="validation-error"><?php echo e($notesError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>
    </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/create-order-product-card.blade.php ENDPATH**/ ?>