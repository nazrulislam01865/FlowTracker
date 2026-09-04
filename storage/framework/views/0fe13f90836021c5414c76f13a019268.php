<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['shipment']));

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

foreach (array_filter((['shipment']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-ms-modal-backdrop" role="presentation" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-details-modal-'.e($shipment['id']).''; ?>wire:key="shipment-details-modal-<?php echo e($shipment['id']); ?>">
    <section class="ft-ms-modal ft-ms-modal--details" role="dialog" aria-modal="true" aria-labelledby="shipment-details-title" x-data x-on:keydown.escape.window="$wire.closeOrderShipmentDetails()">
        <header class="ft-ms-modal__head">
            <div>
                <h2 id="shipment-details-title">Shipment <?php echo e($shipment['sequence']); ?> details</h2>
                <p><?php echo e($shipment['is_primary'] ? 'Primary shipment' : 'Shipment details and dispatch status.'); ?></p>
            </div>
            <button type="button" class="ft-ms-modal__close" wire:click="closeOrderShipmentDetails" aria-label="Close">×</button>
        </header>
        <div class="ft-ms-modal__body">
            <dl class="ft-ms-details-grid">
                <div><dt>Recipient</dt><dd><?php echo e($shipment['recipient'] ?: '—'); ?></dd></div>
                <div><dt>Phone</dt><dd><?php echo e($shipment['phone'] ?: '—'); ?></dd></div>
                <div class="is-wide"><dt>Address</dt><dd><?php echo e($shipment['address'] ?: '—'); ?></dd></div>
                <div><dt>City</dt><dd><?php echo e($shipment['city'] ?: '—'); ?></dd></div>
                <div><dt>State</dt><dd><?php echo e($shipment['state'] ?: '—'); ?></dd></div>
                <div><dt>Postal Code</dt><dd><?php echo e($shipment['postal_code'] ?: '—'); ?></dd></div>
                <div><dt>Country</dt><dd><?php echo e($shipment['country'] ?: '—'); ?></dd></div>
                <div><dt>Shipping Method</dt><dd><?php echo e(data_get($shipment, 'method_card.title', '—')); ?></dd></div>
                <div><dt>Courier</dt><dd><?php echo e($shipment['courier_name'] ?: '—'); ?></dd></div>
                <div><dt>Tracking Number</dt><dd><?php echo e($shipment['tracking_number'] ?: '—'); ?></dd></div>
                <div><dt>Status</dt><dd><?php echo e($shipment['dispatched'] ? 'Dispatched' : 'Pending'); ?></dd></div>
                <div><dt>Dispatched On</dt><dd><?php echo e($shipment['dispatched_on']); ?></dd></div>
                <div><dt>Quantity</dt><dd><?php echo e($shipment['quantity'] ?? '—'); ?></dd></div>
                <div><dt>Package / Reference</dt><dd><?php echo e($shipment['package_reference'] ?: '—'); ?></dd></div>
            </dl>
        </div>
        <footer class="ft-ms-modal__footer">
            <button type="button" class="ft-ms-primary-btn" wire:click="closeOrderShipmentDetails">Close</button>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/details-modal.blade.php ENDPATH**/ ?>