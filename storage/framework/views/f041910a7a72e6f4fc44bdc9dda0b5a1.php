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
    $hasRedo = (bool) ($context['hasRedo'] ?? false);
    $isDiscountScope = $record?->scope === 'discount';
    $scopeLabel = match ($record?->scope) {
        'production' => 'Production',
        'discount' => 'No redo / adjustment',
        default => 'Artwork and production',
    };
    $reportedBy = trim((string) ($record?->issue_reported_by ?? ''));
    $customerAdjustmentType = (string) ($record?->customer_adjustment_type ?: 'percent');
    $customerAdjustmentValue = (float) ($record?->customer_adjustment_value ?? $record?->customer_discount_percent ?? 0);
    $isMissingQty = $isDiscountScope && $customerAdjustmentType === 'pcs';
    $customerAdjustmentLabel = $customerAdjustmentType === 'pcs'
        ? number_format((int) $customerAdjustmentValue).' pcs missing quantity'
        : rtrim(rtrim(number_format($customerAdjustmentValue, 2), '0'), '.').'% customer adjustment';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasRedo && $record): ?>
    <section class="ft-redo-banner show" aria-label="Redo order notice">
        <div class="ft-redo-banner-icon">↻</div>
        <div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope): ?>
                <h3><?php echo e($isMissingQty ? 'Missing quantity deduction' : 'Customer adjustment'); ?> recorded for <?php echo e($record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber()); ?></h3>
                <p>
                    <?php echo e($reportedBy !== '' ? $reportedBy.'-reported issue' : 'Reported issue'); ?>

                    · <?php echo e($customerAdjustmentLabel); ?>

                    · <?php echo e(number_format((int) $record->affected_quantity)); ?> units affected
                    · workflow remains unchanged.
                </p>
            <?php else: ?>
                <h3>Redo order created from <?php echo e($record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber()); ?></h3>
                <p>
                    <?php echo e($reportedBy !== '' ? $reportedBy.'-reported issue' : 'Reported issue'); ?>

                    · <?php echo e($scopeLabel); ?> will be repeated
                    · <?php echo e(number_format((int) $record->redo_quantity)); ?> units
                    · financial recovery recorded.
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <button type="button" class="btn small" wire:click="setDetailTab('redo')">View redo details</button>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/redo-banner.blade.php ENDPATH**/ ?>