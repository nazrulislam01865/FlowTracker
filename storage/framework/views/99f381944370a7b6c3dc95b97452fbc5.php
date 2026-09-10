<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'context' => []]));

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

foreach (array_filter((['job', 'context' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $record = $context['displayRecord'] ?? null;
    $isDiscountScope = $record?->scope === 'discount';
    $customerAdjustmentType = (string) ($record?->customer_adjustment_type ?: 'percent');
    $customerAdjustmentValue = (float) ($record?->customer_adjustment_value ?? $record?->customer_discount_percent ?? 0);
    $supplierAdjustmentType = (string) ($record?->supplier_adjustment_type ?: 'percent');
    $supplierAdjustmentValue = (float) ($record?->supplier_adjustment_value ?? $record?->supplier_redo_charge_percent ?? 0);
    $isMissingQty = $isDiscountScope && $customerAdjustmentType === 'pcs';
    $customerAdjustmentLabel = $customerAdjustmentType === 'pcs'
        ? number_format((int) $customerAdjustmentValue).' pcs missing quantity'
        : rtrim(rtrim(number_format($customerAdjustmentValue, 2), '0'), '.').'%';
    $supplierAdjustmentLabel = $supplierAdjustmentType === 'pcs'
        ? number_format((int) $supplierAdjustmentValue).' pcs'
        : rtrim(rtrim(number_format($supplierAdjustmentValue, 2), '0'), '.').'%';
    $currency = (string) ($record?->originalOrder?->currency ?: $job->currency ?: 'USD');
    $money = fn ($value) => ($currency === 'USD' ? '$' : $currency.' ').number_format((float) $value, 2);
    $originalOrderValue = max(0, (float) ($record?->order_value_before_adjustment ?? 0));
    if ($originalOrderValue <= 0) {
        $originalOrderValue = max(0, (float) ($record?->originalOrder?->commercial_value ?? 0));
    }
    $adjustedOrderValue = max(0, (float) ($record?->order_value_after_adjustment ?? 0));
    if ($adjustedOrderValue <= 0 && $originalOrderValue > 0) {
        $adjustedOrderValue = max(0, $originalOrderValue - (float) ($record?->customer_impact ?? 0));
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record): ?>
    <section class="ft-redo-finance-review">
        <div class="ft-redo-card">
            <header class="ft-redo-cardhead">
                <div>
                    <h2><?php echo e($isDiscountScope ? ($isMissingQty ? 'Missing quantity adjustment' : 'Customer financial adjustment') : 'Redo financial adjustment'); ?></h2>
                    <small>
                        <?php echo e($isDiscountScope
                            ? (($record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber()).' · no redo Order created · workflow unchanged')
                            : (($record->redoOrder?->displayOrderNumber() ?? 'Redo order').' · original invoice and payments unchanged')); ?>

                    </small>
                </div>
                <button type="button" class="btn small" wire:click="setDetailTab('redo')">View redo details</button>
            </header>
            <div class="ft-redo-cardbody">
                <table class="ft-redo-fin-table">
                    <tr><td>Affected order value</td><td><?php echo e($money($record->affected_order_value)); ?></td></tr>
                    <tr><td>Customer · <?php echo e($customerAdjustmentLabel); ?></td><td><?php echo e($record->customer_resolution === 'discount' ? '-'.$money($record->customer_impact) : $money(0)); ?></td></tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope && $record->customer_resolution === 'discount'): ?>
                        <tr><td>Order total after deduction</td><td><?php echo e($money($adjustedOrderValue)); ?></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <tr><td>Supplier · <?php echo e($supplierAdjustmentLabel); ?></td><td><?php echo e($money($record->supplier_redo_charge)); ?></td></tr>
                    <tr><td>Freight deduction</td><td><?php echo e($money($record->freight_amount)); ?></td></tr>
                    <tr class="total"><td>Total supplier recovery</td><td><?php echo e($money($record->total_supplier_recovery)); ?></td></tr>
                </table>
            </div>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/redo-finance.blade.php ENDPATH**/ ?>