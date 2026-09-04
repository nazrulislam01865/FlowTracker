<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'count' => 0,
    'totalUnits' => 0,
    'title' => 'Products & quantities',
    'variant' => 'order',
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
    'count' => 0,
    'totalUnits' => 0,
    'title' => 'Products & quantities',
    'variant' => 'order',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $productCount = max(0, (int) $count);
    $units = (float) $totalUnits;
    $unitDecimals = fmod(abs($units), 1.0) === 0.0 ? 0 : 2;
?>

<section <?php echo e($attributes->class(['ft-detail-card', 'ft-order-products-card', 'ft-detail-products-card', 'ft-detail-products-card--'.$variant])); ?>>
    <header class="ft-order-products-head">
        <div class="ft-order-products-title">
            <h2><?php echo e($title); ?></h2>
            <p class="ft-order-products-summary">
                <?php echo e($productCount); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $productCount)); ?> · <?php echo e(number_format($units, $unitDecimals)); ?> total units
            </p>
        </div>
    </header>

    <div class="ft-order-products-table-wrap">
        <table class="ft-order-products-detail-table ft-inline-product-table <?php echo e($variant === 'inquiry' ? 'ft-order-products-detail-table--inquiry' : ''); ?>">
            <thead>
                <tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($columns)): ?>
                        <?php echo e($columns); ?>

                    <?php else: ?>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Unit<br>price</th>
                        <th>Notes</th>
                        <th>Updated</th>
                        <th class="ft-order-product-actions-heading" aria-label="Actions"></th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            </thead>
            <tbody><?php echo e($slot); ?></tbody>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($afterTable)): ?>
        <?php echo e($afterTable); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer)): ?>
        <footer class="ft-order-products-footer"><?php echo e($footer); ?></footer>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-products-card.blade.php ENDPATH**/ ?>