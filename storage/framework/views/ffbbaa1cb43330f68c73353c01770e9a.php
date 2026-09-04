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
    $isCurrentStage = (int) $task->workflow_phase_id === (int) $task->job?->workflow_phase_id;
    $canEditPlan = $canEdit && $isCurrentStage;
    $shipments = collect($presentation['shipments'] ?? []);
    $shipmentCount = $shipments->count();
    $addressMode = (string) ($presentation['address_mode'] ?? \App\Services\OrderShipmentService::MODE_SAME_ADDRESS);
    $planLabel = $shipmentCount <= 1
        ? 'Single shipment'
        : ($addressMode === \App\Services\OrderShipmentService::MODE_MULTIPLE_ADDRESS ? 'Multiple addresses' : 'Same address');
?>

<div class="ft-ms-plan">
    <div class="ft-ms-plan-summary">
        <div class="ft-ms-plan-summary__copy">
            <span class="ft-ms-plan-summary__count"><?php echo e($shipmentCount); ?> <?php echo e(\Illuminate\Support\Str::plural('shipment', $shipmentCount)); ?></span>
            <span class="ft-ms-plan-summary__mode"><?php echo e($planLabel); ?></span>
            <span class="ft-ms-plan-summary__hint">Edit each shipment individually.</span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditPlan): ?>
            <button
                type="button"
                class="ft-ms-outline-btn ft-ms-plan-summary__add"
                wire:click="openAddShipment(<?php echo e($task->id); ?>)"
            >
                <span aria-hidden="true">＋</span>
                Add shipment
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentSettings'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-ms-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentMethod'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-ms-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-ms-table-wrap ft-ms-table-wrap--plan">
        <table class="ft-ms-table ft-ms-table--plan">
            <thead>
                <tr>
                    <th>Shipment</th>
                    <th>Quantity</th>
                    <th>Delivery details</th>
                    <th>Shipping method</th>
                    <th>Package / Reference</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $shipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-plan-row-'.e($shipment['id']).''; ?>wire:key="shipment-plan-row-<?php echo e($shipment['id']); ?>">
                        <td data-label="Shipment">
                            <div class="ft-ms-shipment-number">
                                <b><?php echo e($shipment['sequence']); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipment['is_primary']): ?><span class="ft-ms-primary">Primary</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td data-label="Quantity">
                            <span class="ft-ms-package-reference"><?php echo e($shipment['quantity'] ?? '—'); ?></span>
                        </td>
                        <td data-label="Delivery details">
                            <div class="ft-ms-delivery">
                                <div class="ft-ms-delivery__recipient">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10" cy="6.5" r="2.5"/><path d="M5.5 16v-2.2a4.5 4.5 0 0 1 9 0V16"/></svg>
                                    <div>
                                        <strong><?php echo e($shipment['recipient'] ?: 'Recipient not set'); ?></strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipment['phone']): ?><small><?php echo e($shipment['phone']); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <address><?php echo e($shipment['address'] ?: 'Address not set'); ?></address>
                                <div class="ft-ms-delivery__meta">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = collect([$shipment['city'], $shipment['state'], $shipment['postal_code'], $shipment['country']])->filter(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <span><?php echo e($part); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td data-label="Shipping method">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipment['method_card']): ?>
                                <div class="ft-ms-method-display">
                                    <span class="ft-ms-method-label__icon"><?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal937251c6395c013b7e12535197664182 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-method-icon','data' => ['type' => $shipment['method_card']['kind']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-method-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment['method_card']['kind'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal937251c6395c013b7e12535197664182)): ?>
<?php $attributes = $__attributesOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__attributesOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal937251c6395c013b7e12535197664182)): ?>
<?php $component = $__componentOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__componentOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?></span>
                                    <span>
                                        <strong><?php echo e($shipment['method_card']['title']); ?></strong>
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="ft-ms-missing-value">Not selected</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td data-label="Package / Reference">
                            <span class="ft-ms-package-reference"><?php echo e($shipment['package_reference'] ?: '—'); ?></span>
                        </td>
                        <td data-label="Actions">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditPlan && !$shipment['dispatched']): ?>
                                <div class="ft-ms-row-actions">
                                    <button
                                        type="button"
                                        class="ft-ms-row-edit"
                                        wire:click="openEditShipment(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>)"
                                        title="Edit Shipment <?php echo e($shipment['sequence']); ?>"
                                    >
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                        <span>Edit</span>
                                    </button>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($shipment['is_primary'])): ?>
                                        <div class="ft-ms-kebab" x-data="{ open: false }" x-on:click.outside="open = false">
                                            <button type="button" class="ft-ms-kebab__button" x-on:click="open = !open" aria-label="More actions for Shipment <?php echo e($shipment['sequence']); ?>">⋮</button>
                                            <div class="ft-ms-kebab__menu" x-cloak x-show="open">
                                                <button type="button" class="is-danger" wire:click="removeOrderShipment(<?php echo e($task->id); ?>, <?php echo e($shipment['id']); ?>)" wire:confirm="Remove Shipment <?php echo e($shipment['sequence']); ?>?" x-on:click="open = false">Remove shipment</button>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="ft-ms-readonly-dash">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="6" class="ft-ms-empty">No shipment details are available.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditPlan && ($row['mode'] === 'active' || $row['is_done'])): ?>
        <div class="ft-ms-review-panel ft-ms-review-panel--compact">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_done']): ?>
                <div>
                    <strong>Shipment details confirmed</strong>
                    <p>Each shipment can still be edited individually while the Shipment stage is active.</p>
                </div>
            <?php else: ?>
                <div>
                    <strong>Are the shipment details correct?</strong>
                    <p>Use Edit on a shipment if anything needs changing, or continue with the current details.</p>
                </div>
                <div class="ft-ms-review-panel__actions">
                    <button
                        type="button"
                        class="ft-ms-primary-btn"
                        wire:click="confirmShipmentPlan(<?php echo e($task->id); ?>)"
                        wire:loading.attr="disabled"
                        wire:target="confirmShipmentPlan"
                    >
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4.5 10.5 3.2 3.2 7.8-8"/></svg>
                        No changes — continue
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/plan-table.blade.php ENDPATH**/ ?>