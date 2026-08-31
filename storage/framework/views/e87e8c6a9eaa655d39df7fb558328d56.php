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
        'discount' => 'Discount only',
        default => 'Artwork and production',
    };
    $reportedBy = trim((string) ($record?->issue_reported_by ?? ''));
    $discountPercent = rtrim(rtrim(number_format((float) ($record?->customer_discount_percent ?? 0), 2), '0'), '.');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasRedo && $record): ?>
    <section class="ft-redo-banner show" aria-label="Redo order notice">
        <div class="ft-redo-banner-icon">↻</div>
        <div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope): ?>
                <h3>Customer discount recorded for <?php echo e($record->originalOrder?->displayOrderNumber() ?? $job->displayOrderNumber()); ?></h3>
                <p>
                    <?php echo e($reportedBy !== '' ? $reportedBy.'-reported issue' : 'Reported issue'); ?>

                    · <?php echo e($discountPercent); ?>% client discount
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/redo-banner.blade.php ENDPATH**/ ?>