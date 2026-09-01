<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'item' => [],
    'detail' => null,
    'supplier' => null,
    'supplierSkipped' => false,
    'supplierRequired' => false,
    'context' => 'order',
    'index' => 0,
    'rowsProperty' => 'jobItems',
    'removeMethod' => 'removeProductRow',
    'rowKeyPrefix' => 'selected-product',
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
    'supplierRequired' => false,
    'context' => 'order',
    'index' => 0,
    'rowsProperty' => 'jobItems',
    'removeMethod' => 'removeProductRow',
    'rowKeyPrefix' => 'selected-product',
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
    $itemCode = (string) ($detail?->productDisplayCode() ?? ($detail?->code ?? ''));
    $itemCategory = (string) ($detail?->parent?->name ?? ($item['category'] ?? ''));
    $itemName = (string) ($detail?->name ?? ($item['product'] ?? 'Product'));
    $quantityError = $errors->first("{$rowsProperty}.{$index}.quantity");
    $supplierError = $errors->first("{$rowsProperty}.{$index}.supplier_id");
    $unitPriceError = $errors->first("{$rowsProperty}.{$index}.unit_price");
    $productError = $errors->first("{$rowsProperty}.{$index}.product");
    $isOrder = $context === 'order';
    $priceFromProductTable = in_array($context, ['order', 'inquiry'], true);
?>

<article class="ft-order-selected-product-card ft-create-product-card ft-product-quantity-selected-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($rowKeyPrefix).'-'.e($item['product_id'] ?? $index).'-'.e($index).''; ?>wire:key="<?php echo e($rowKeyPrefix); ?>-<?php echo e($item['product_id'] ?? $index); ?>-<?php echo e($index); ?>">
    <div class="ft-pq-selected-product">
        <span class="ft-order-product-thumb">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemImage): ?>
                <img src="<?php echo e($itemImage); ?>" alt="">
            <?php else: ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
        <span class="ft-pq-selected-product-copy">
            <strong title="<?php echo e($itemName); ?>"><?php echo e($itemName); ?></strong>
            <small><?php echo e($itemCode ?: 'N/A'); ?> <i>&middot;</i> <?php echo e($itemCategory ?: 'Uncategorized'); ?></small>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productError): ?><small class="validation-error"><?php echo e($productError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
    </div>

    <label class="ft-pq-selected-field ft-pq-selected-quantity">
        <span>Quantity</span>
        <input type="number" min="1" max="999999999" wire:model.live.debounce.300ms="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.quantity" aria-label="Quantity for <?php echo e($itemName); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantityError): ?><small class="validation-error"><?php echo e($quantityError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </label>

    <div class="ft-pq-selected-field ft-pq-selected-supplier" x-data="{ changingSupplier: false }" x-on:create-order-product-supplier-selected.window="changingSupplier = false">
        <span>Supplier for this order</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOrder && !$supplierRequired): ?>
            <div class="ft-pq-supplier-box"><strong><?php echo e($supplier?->name ?: 'No supplier selected'); ?></strong></div>
        <?php elseif($isOrder): ?>
            <div x-show="!changingSupplier" class="ft-pq-supplier-box <?php echo e(!$supplier ? 'is-missing' : ''); ?>">
                <strong><?php echo e($supplier?->name ?: ($supplierSkipped ? 'Supplier skipped for now' : 'No default supplier linked')); ?></strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplier): ?><i>&middot;</i><b>Default</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" x-on:click="changingSupplier = true">Change</button>
            </div>
            <div x-cloak x-show="changingSupplier" class="ft-pq-supplier-picker">
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Supplier','type' => 'suppliers','context' => 'create-job','property' => 'create-order-item-supplier:'.e($index).'','value' => $supplier?->id,'selectedLabel' => $supplier?->name,'placeholder' => 'Select supplier','searchPlaceholder' => 'Search supplier','clearable' => false,'action' => 'updateCreateOrderProductSupplierFromSelector','menuWidth' => 360,'hideLabel' => true,'fixedMenu' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier','type' => 'suppliers','context' => 'create-job','property' => 'create-order-item-supplier:'.e($index).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier?->id),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier?->name),'placeholder' => 'Select supplier','search-placeholder' => 'Search supplier','clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'action' => 'updateCreateOrderProductSupplierFromSelector','menu-width' => 360,'hide-label' => true,'fixed-menu' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                <button type="button" class="ft-pq-supplier-cancel" x-on:click="changingSupplier = false" aria-label="Cancel supplier change">&times;</button>
            </div>
        <?php else: ?>
            <div class="ft-pq-supplier-box <?php echo e(!$supplier ? 'is-missing' : ''); ?>">
                <strong><?php echo e($supplier?->name ?: 'No default supplier linked'); ?></strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplier): ?><i>&middot;</i><b>Default</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierError): ?><small class="validation-error"><?php echo e($supplierError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <label class="ft-pq-selected-field ft-pq-selected-price">
        <span>Unit price</span>
        <div class="ft-pq-price-input <?php echo e($priceFromProductTable ? 'is-readonly' : ''); ?>">
            <b>$</b>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priceFromProductTable): ?>
                <input
                    type="number"
                    min="0"
                    max="999999999999.99"
                    step="0.01"
                    value="<?php echo e(filled($item['unit_price'] ?? null) ? number_format((float) $item['unit_price'], 2, '.', '') : ''); ?>"
                    placeholder="0.00"
                    aria-label="Unit price for <?php echo e($itemName); ?>"
                    readonly
                >
            <?php else: ?>
                <input type="number" min="0" max="999999999999.99" step="0.01" wire:model.blur="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.unit_price" placeholder="0.00" aria-label="Unit price for <?php echo e($itemName); ?>">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unitPriceError): ?><small class="validation-error"><?php echo e($unitPriceError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </label>

    <button type="button" class="ft-pq-remove-product" wire:click="<?php echo e($removeMethod); ?>(<?php echo e($index); ?>)" aria-label="Remove <?php echo e($itemName); ?>">Remove</button>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/create-product-card.blade.php ENDPATH**/ ?>