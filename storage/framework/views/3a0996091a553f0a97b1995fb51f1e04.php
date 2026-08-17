<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'count' => 0,
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-order-bulk-bar" role="region" aria-label="Bulk order actions">
    <div class="ft-order-bulk-summary">
        <strong><?php echo e(number_format($count)); ?> <?php echo e(\Illuminate\Support\Str::plural('order', $count)); ?> selected</strong>
        <span>Select more rows or delete the selected orders together.</span>
    </div>
    <div class="ft-order-bulk-actions">
        <button
            type="button"
            class="ft-order-bulk-clear"
            wire:click="clearOrderSelection"
            wire:loading.attr="disabled"
            wire:target="clearOrderSelection,openBulkDeleteConfirmation,bulkDeleteOrders"
        >Clear selection</button>
        <button
            type="button"
            class="ft-order-bulk-delete"
            wire:click="openBulkDeleteConfirmation"
            wire:loading.attr="disabled"
            wire:target="openBulkDeleteConfirmation"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
            <span wire:loading.remove wire:target="openBulkDeleteConfirmation">Delete selected</span>
            <span wire:loading wire:target="openBulkDeleteConfirmation">Opening...</span>
        </button>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/bulk-delete-bar.blade.php ENDPATH**/ ?>