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
    $editable = $canEdit && $actionable;
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
                <?php
                    $hasCourier = ! empty($shipment['courier_id']);
                    $hasTracking = trim((string) ($shipment['tracking_number'] ?? '')) !== '';
                    $initialEntry = ! $hasCourier || ! $hasTracking;
                    // As soon as Task 5.2 becomes active, incomplete shipment rows
                    // open automatically. Initial entry is intentionally NOT auto-saved:
                    // the user reviews both values and clicks Save for that shipment.
                    // After the first save, each field can still be edited inline with
                    // the pencil control and those later edits save immediately.
                    $autoOpen = $editable && $row['mode'] === 'active' && $initialEntry;
                ?>
                <tr
                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-tracking-row-'.e($shipment['id']).'-'.e($row['mode']).'-'.e((int) $autoOpen).''; ?>wire:key="shipment-tracking-row-<?php echo e($shipment['id']); ?>-<?php echo e($row['mode']); ?>-<?php echo e((int) $autoOpen); ?>"
                    x-data="{
                        courierId: <?php echo \Illuminate\Support\Js::from($shipment['courier_id'] ? (string) $shipment['courier_id'] : '')->toHtml() ?>,
                        originalCourierId: <?php echo \Illuminate\Support\Js::from($shipment['courier_id'] ? (string) $shipment['courier_id'] : '')->toHtml() ?>,
                        courierName: <?php echo \Illuminate\Support\Js::from($shipment['courier_name'] ?: 'Not selected')->toHtml() ?>,
                        originalCourierName: <?php echo \Illuminate\Support\Js::from($shipment['courier_name'] ?: 'Not selected')->toHtml() ?>,
                        tracking: <?php echo \Illuminate\Support\Js::from($shipment['tracking_number'])->toHtml() ?>,
                        originalTracking: <?php echo \Illuminate\Support\Js::from($shipment['tracking_number'])->toHtml() ?>,
                        courierEditing: <?php echo \Illuminate\Support\Js::from($autoOpen)->toHtml() ?>,
                        trackingEditing: <?php echo \Illuminate\Support\Js::from($autoOpen)->toHtml() ?>,
                        initialEntry: <?php echo \Illuminate\Support\Js::from($initialEntry)->toHtml() ?>,
                        saving: false,
                        resetCourier() {
                            this.courierId = this.originalCourierId;
                            this.courierName = this.originalCourierName;
                            this.courierEditing = false;
                        },
                        resetTracking() {
                            this.tracking = this.originalTracking;
                            this.trackingEditing = false;
                        },
                        canSave() {
                            return String(this.courierId || '').trim() !== '' && String(this.tracking || '').trim() !== '';
                        },
                        persist() {
                            const courierId = String(this.courierId || '').trim();
                            const tracking = String(this.tracking || '').trim();
                            if (this.saving || !courierId || !tracking) return;

                            this.saving = true;
                            this.tracking = tracking;
                            $wire.saveOrderShipmentTracking(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>, Number(courierId), tracking)
                                .then(() => {
                                    this.originalCourierId = courierId;
                                    this.originalCourierName = this.courierName;
                                    this.originalTracking = tracking;
                                    this.initialEntry = false;
                                    this.courierEditing = false;
                                    this.trackingEditing = false;
                                })
                                .finally(() => { this.saving = false; });
                        }
                    }"
                    :class="{ 'is-inline-saving': saving }"
                >
                    <td data-label="Shipment">
                        <div class="ft-ms-shipment-number"><b><?php echo e($shipment['sequence']); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipment['is_primary']): ?><span class="ft-ms-primary">Primary</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    </td>

                    <td data-label="Courier">
                        <div class="ft-ms-inline-field">
                            <div class="ft-ms-courier-value" x-show="!courierEditing">
                                <strong x-text="courierName"><?php echo e($shipment['courier_name'] ?: 'Not selected'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editable): ?>
                                    <button
                                        type="button"
                                        class="ft-ms-cell-edit-button"
                                        title="Edit courier"
                                        aria-label="Edit courier for Shipment <?php echo e($shipment['sequence']); ?>"
                                        x-on:click.stop="courierEditing = true; $nextTick(() => $refs.courierSelect?.focus())"
                                    >
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editable): ?>
                                <?php if (isset($component)) { $__componentOriginaldc423fbb84f7116067e0e2341b6e7ef3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc423fbb84f7116067e0e2341b6e7ef3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.courier-select','data' => ['xRef' => 'courierSelect','xCloak' => true,'xShow' => 'courierEditing','xModel' => 'courierId','xBind:disabled' => 'saving','xOn:change' => 'courierName = $event.target.options[$event.target.selectedIndex]?.text || \'Not selected\'; if (!initialEntry) persist()','xOn:keydown.escape.prevent' => 'if (!initialEntry) resetCourier()','couriers' => $couriers,'ariaLabel' => 'Courier for Shipment '.e($shipment['sequence']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.courier-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-ref' => 'courierSelect','x-cloak' => true,'x-show' => 'courierEditing','x-model' => 'courierId','x-bind:disabled' => 'saving','x-on:change' => 'courierName = $event.target.options[$event.target.selectedIndex]?.text || \'Not selected\'; if (!initialEntry) persist()','x-on:keydown.escape.prevent' => 'if (!initialEntry) resetCourier()','couriers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($couriers),'aria-label' => 'Courier for Shipment '.e($shipment['sequence']).'']); ?>
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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>

                    <td data-label="Tracking Number">
                        <div class="ft-ms-inline-field">
                            <div class="ft-ms-tracking-value" x-show="!trackingEditing">
                                <span x-text="tracking || 'Not added'" :class="tracking ? '' : 'is-empty'"><?php echo e($shipment['tracking_number'] ?: 'Not added'); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editable): ?>
                                    <button
                                        type="button"
                                        class="ft-ms-cell-edit-button"
                                        title="Edit tracking number"
                                        aria-label="Edit tracking number for Shipment <?php echo e($shipment['sequence']); ?>"
                                        x-on:click.stop="trackingEditing = true; $nextTick(() => { $refs.trackingInput?.focus(); $refs.trackingInput?.select(); })"
                                    >
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editable): ?>
                                <input
                                    x-ref="trackingInput"
                                    x-cloak
                                    x-show="trackingEditing"
                                    x-model.trim="tracking"
                                    x-bind:disabled="saving"
                                    class="ft-ms-tracking-input"
                                    type="text"
                                    maxlength="255"
                                    placeholder="Enter tracking number"
                                    x-on:keydown.escape.prevent="if (!initialEntry) resetTracking()"
                                    x-on:keydown.enter.prevent="if (!initialEntry) persist()"
                                    x-on:blur="if (!initialEntry) { if (String(tracking || '').trim() === String(originalTracking || '').trim()) { trackingEditing = false } else { persist() } }"
                                >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>

                    <td data-label="Actions">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editable && $initialEntry): ?>
                            <div class="ft-ms-actions" x-show="initialEntry">
                                <button
                                    type="button"
                                    class="ft-ms-primary-btn"
                                    x-on:click="persist()"
                                    x-bind:disabled="saving || !canSave()"
                                >
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4.5 10.5 3.2 3.2 7.8-8"/></svg>
                                    <span x-text="saving ? 'Saving...' : 'Save'">Save</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <span class="ft-ms-row-action-empty">—</span>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/shipment/tracking-table.blade.php ENDPATH**/ ?>