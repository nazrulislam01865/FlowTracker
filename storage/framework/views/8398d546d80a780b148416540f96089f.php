<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['row', 'presentation']));

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

foreach (array_filter((['row', 'presentation']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $task = $row['task'];
    $canEdit = (bool) ($row['can_edit'] ?? false);
?>

<div class="ft-ms-table-wrap">
    <table class="ft-ms-table ft-ms-table--dispatch">
        <thead>
            <tr>
                <th>Shipment</th>
                <th>Courier</th>
                <th>Status</th>
                <th>Dispatched On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = ($presentation['shipments'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-dispatch-row-'.e($shipment['id']).''; ?>wire:key="shipment-dispatch-row-<?php echo e($shipment['id']); ?>">
                    <td><div class="ft-ms-shipment-number"><b><?php echo e($shipment['sequence']); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipment['is_primary']): ?><span class="ft-ms-primary">Primary</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></td>
                    <td data-label="Courier">
                        <div class="ft-ms-courier-value">
                            <strong><?php echo e($shipment['courier_name'] ?: 'Not selected'); ?></strong>
                        </div>
                    </td>
                    <td>
                        <span class="ft-ms-status <?php echo e($shipment['dispatched'] ? 'is-dispatched' : 'is-pending'); ?>"><?php echo e($shipment['dispatched'] ? 'Dispatched' : 'Pending'); ?></span>
                    </td>
                    <td><?php echo e($shipment['dispatched_on']); ?></td>
                    <td>
                        <div class="ft-ms-actions">
                            <button
                                type="button"
                                class="ft-ms-outline-btn"
                                <?php if($shipment['dispatched'] || !$canEdit || $row['mode'] !== 'active' || !$shipment['courier_id'] || !$shipment['tracking_number']): echo 'disabled'; endif; ?>
                                wire:click="dispatchOrderShipment(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>)"
                                wire:loading.attr="disabled"
                                wire:target="dispatchOrderShipment"
                            >
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m6.5 10 2.2 2.2 4.8-4.8"/></svg>
                                Mark as dispatched
                            </button>
                            <button type="button" class="ft-ms-outline-btn" wire:click="openOrderShipmentDetails(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>)">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M2.5 10s2.8-5 7.5-5 7.5 5 7.5 5-2.8 5-7.5 5-7.5-5-7.5-5Z"/><circle cx="10" cy="10" r="2"/></svg>
                                View details
                            </button>
                        </div>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="5" class="ft-ms-empty">No shipments are available.</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentDispatch'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-ms-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/dispatch-table.blade.php ENDPATH**/ ?>