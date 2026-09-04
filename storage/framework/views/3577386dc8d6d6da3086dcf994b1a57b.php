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
    $actionable = $row['mode'] === 'active' || $row['is_done'];
    $couriers = $presentation['couriers'] ?? [];
?>

<div class="ft-ms-table-wrap ft-ms-table-wrap--tracking">
    <table class="ft-ms-table ft-ms-table--tracking">
        <thead>
            <tr>
                <th>Shipment</th>
                <th>Courier</th>
                <th>Tracking Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = ($presentation['shipments'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr
                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-tracking-row-'.e($shipment['id']).''; ?>wire:key="shipment-tracking-row-<?php echo e($shipment['id']); ?>"
                    x-data="{
                        editing: false,
                        courierId: <?php echo \Illuminate\Support\Js::from($shipment['courier_id'] ? (string) $shipment['courier_id'] : '')->toHtml() ?>,
                        originalCourierId: <?php echo \Illuminate\Support\Js::from($shipment['courier_id'] ? (string) $shipment['courier_id'] : '')->toHtml() ?>,
                        tracking: <?php echo \Illuminate\Support\Js::from($shipment['tracking_number'])->toHtml() ?>,
                        originalTracking: <?php echo \Illuminate\Support\Js::from($shipment['tracking_number'])->toHtml() ?>,
                        cancelEdit() {
                            this.courierId = this.originalCourierId;
                            this.tracking = this.originalTracking;
                            this.editing = false;
                        }
                    }"
                >
                    <td data-label="Shipment">
                        <div class="ft-ms-shipment-number"><b><?php echo e($shipment['sequence']); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipment['is_primary']): ?><span class="ft-ms-primary">Primary</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    </td>
                    <td data-label="Courier">
                        <div class="ft-ms-courier-value" x-show="!editing">
                            <strong><?php echo e($shipment['courier_name'] ?: 'Not selected'); ?></strong>
                        </div>
                        <?php if (isset($component)) { $__componentOriginaldc423fbb84f7116067e0e2341b6e7ef3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc423fbb84f7116067e0e2341b6e7ef3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.courier-select','data' => ['xCloak' => true,'xShow' => 'editing','xModel' => 'courierId','couriers' => $couriers,'ariaLabel' => 'Courier for Shipment '.e($shipment['sequence']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.courier-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-cloak' => true,'x-show' => 'editing','x-model' => 'courierId','couriers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($couriers),'aria-label' => 'Courier for Shipment '.e($shipment['sequence']).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc423fbb84f7116067e0e2341b6e7ef3)): ?>
<?php $attributes = $__attributesOriginaldc423fbb84f7116067e0e2341b6e7ef3; ?>
<?php unset($__attributesOriginaldc423fbb84f7116067e0e2341b6e7ef3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc423fbb84f7116067e0e2341b6e7ef3)): ?>
<?php $component = $__componentOriginaldc423fbb84f7116067e0e2341b6e7ef3; ?>
<?php unset($__componentOriginaldc423fbb84f7116067e0e2341b6e7ef3); ?>
<?php endif; ?>
                    </td>
                    <td data-label="Tracking Number">
                        <div class="ft-ms-tracking-value" x-show="!editing">
                            <span x-text="tracking || 'Not added'" :class="tracking ? '' : 'is-empty'"><?php echo e($shipment['tracking_number'] ?: 'Not added'); ?></span>
                        </div>
                        <input
                            x-cloak
                            x-show="editing"
                            x-model.trim="tracking"
                            class="ft-ms-tracking-input"
                            type="text"
                            maxlength="255"
                            placeholder="Enter tracking number"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:keydown.enter.prevent="if (String(courierId || '').trim() && String(tracking || '').trim()) $wire.saveOrderShipmentTracking(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>, Number(courierId), tracking).then(() => { originalCourierId = courierId; originalTracking = tracking; editing = false; })"
                        >
                    </td>
                    <td data-label="Actions">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit && $actionable): ?>
                            <div class="ft-ms-actions" x-show="!editing">
                                <button type="button" class="ft-ms-outline-btn" x-on:click="editing = true; $nextTick(() => $el.closest('tr').querySelector('.ft-ms-courier-select')?.focus())">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                    <span><?php echo e(($shipment['courier_id'] && $shipment['tracking_number']) ? 'Edit courier & tracking' : 'Add courier & tracking'); ?></span>
                                </button>
                            </div>
                            <div class="ft-ms-actions ft-ms-actions--editing" x-cloak x-show="editing">
                                <button type="button" class="ft-ms-ghost-btn" x-on:click="cancelEdit()">Cancel</button>
                                <button
                                    type="button"
                                    class="ft-ms-primary-btn"
                                    x-bind:disabled="!String(courierId || '').trim() || !String(tracking || '').trim()"
                                    x-on:click="$wire.saveOrderShipmentTracking(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>, Number(courierId), tracking).then(() => { originalCourierId = courierId; originalTracking = tracking; editing = false; })"
                                >Save</button>
                            </div>
                        <?php else: ?>
                            <span class="ft-ms-readonly-dash">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="4" class="ft-ms-empty">No shipments are available.</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentTracking'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-ms-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/tracking-table.blade.php ENDPATH**/ ?>