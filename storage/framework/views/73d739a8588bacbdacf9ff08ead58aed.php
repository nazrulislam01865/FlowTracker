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

<div
    class="ft-order-delete-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="ft-order-delete-title"
    aria-describedby="ft-order-delete-description"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-bulk-delete-confirmation'; ?>wire:key="order-bulk-delete-confirmation"
    x-data
    x-on:keydown.escape.window="$wire.closeBulkDeleteConfirmation()"
>
    <button
        type="button"
        class="ft-order-delete-backdrop"
        wire:click="closeBulkDeleteConfirmation"
        aria-label="Cancel bulk order deletion"
    ></button>

    <section class="ft-order-delete-card">
        <button
            type="button"
            class="ft-order-delete-close"
            wire:click="closeBulkDeleteConfirmation"
            wire:loading.attr="disabled"
            wire:target="bulkDeleteOrders"
            aria-label="Close confirmation"
        >×</button>

        <div class="ft-order-delete-body">
            <div class="ft-order-delete-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
            </div>

            <h2 id="ft-order-delete-title" class="ft-order-delete-title">Delete selected orders?</h2>
            <p id="ft-order-delete-description" class="ft-order-delete-copy">
                Please confirm before deleting the selected orders from FlowTrack.
            </p>

            <div class="ft-order-delete-count">
                <strong><?php echo e(number_format($count)); ?> <?php echo e(\Illuminate\Support\Str::plural('order', $count)); ?></strong>
                <span>selected for deletion</span>
            </div>

            <p class="ft-order-delete-warning">This action cannot be undone. Cancel if you want to review the selection first.</p>
        </div>

        <footer class="ft-order-delete-actions">
            <button
                type="button"
                class="ft-order-delete-cancel"
                wire:click="closeBulkDeleteConfirmation"
                wire:loading.attr="disabled"
                wire:target="bulkDeleteOrders"
            >Cancel</button>
            <button
                type="button"
                class="ft-order-delete-confirm"
                wire:click="bulkDeleteOrders"
                wire:loading.attr="disabled"
                wire:target="bulkDeleteOrders"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                <span wire:loading.remove wire:target="bulkDeleteOrders">Delete <?php echo e(number_format($count)); ?> <?php echo e(\Illuminate\Support\Str::plural('order', $count)); ?></span>
                <span wire:loading wire:target="bulkDeleteOrders">Deleting...</span>
            </button>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/bulk-delete-confirmation.blade.php ENDPATH**/ ?>