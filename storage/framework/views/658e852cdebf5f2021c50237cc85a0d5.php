<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'phase',
    'presentation' => [],
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
    'job',
    'phase',
    'presentation' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section class="ft-shipment-phase" aria-label="Shipment tasks" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-phase-'.e($job->id).'-'.e($phase->id).''; ?>wire:key="shipment-phase-<?php echo e($job->id); ?>-<?php echo e($phase->id); ?>">
    <div class="ft-shipment-phase__status">
        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M2.5 5.5h9v8h-9zM11.5 8h3l3 3v2.5h-6z"/><circle cx="6" cy="15" r="1.5"/><circle cx="14.5" cy="15" r="1.5"/></svg>
        Order status: Ready for shipment
    </div>

    <header class="ft-shipment-phase__head">
        <div>
            <h3>Shipment tasks</h3>
            <p>Complete these steps in order to dispatch the package.</p>
        </div>
        <div class="ft-shipment-progress" aria-label="<?php echo e($presentation['completed_count'] ?? 0); ?> of <?php echo e($presentation['total_count'] ?? 0); ?> complete">
            <strong><?php echo e($presentation['completed_count'] ?? 0); ?> of <?php echo e($presentation['total_count'] ?? 0); ?> complete</strong>
            <div class="ft-shipment-progress__track" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($presentation['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $progressIndex => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($progressIndex > 0): ?><span class="ft-shipment-progress__line <?php echo e($row['progress_line_done'] ? 'is-done' : ''); ?>"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="ft-shipment-progress__dot <?php echo e($row['is_done'] ? 'is-done' : ($row['mode'] === 'active' ? 'is-current' : '')); ?>"></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </header>

    <div class="ft-shipment-phase__tasks">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($presentation['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <article
                class="ft-shipment-task ft-shipment-task--<?php echo e($row['mode']); ?>"
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-task-'.e($row['task']->id).''; ?>wire:key="shipment-task-<?php echo e($row['task']->id); ?>"
                x-data="{ open: <?php echo \Illuminate\Support\Js::from($row['mode'] === 'active')->toHtml() ?> }"
            >
                <div class="ft-shipment-task__rail">
                    <span class="ft-shipment-task__number">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_done']): ?>
                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5.2 10.2 3 3L14.9 6.7"/></svg>
                        <?php else: ?>
                            <?php echo e($index + 1); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?><span class="ft-shipment-task__rail-line"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-shipment-task__content">
                    <div class="ft-shipment-task__top">
                        <div class="ft-shipment-task__copy">
                            <div class="ft-shipment-task__eyebrow">
                                <span>TASK <?php echo e($row['display_code']); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['mode'] === 'active'): ?><em>Current task</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <h4><?php echo e($row['title']); ?></h4>
                            <p><?php echo e($row['description']); ?></p>
                        </div>

                        <?php if (isset($component)) { $__componentOriginal222d733c16edabfdd2e93c48ca5b92b6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.task-meta','data' => ['job' => $job,'row' => $row]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.task-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6)): ?>
<?php $attributes = $__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6; ?>
<?php unset($__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal222d733c16edabfdd2e93c48ca5b92b6)): ?>
<?php $component = $__componentOriginal222d733c16edabfdd2e93c48ca5b92b6; ?>
<?php unset($__componentOriginal222d733c16edabfdd2e93c48ca5b92b6); ?>
<?php endif; ?>

                        <div class="ft-shipment-task__state">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['mode'] === 'active'): ?>
                                <span class="ft-shipment-state ft-shipment-state--action"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 6.5v4M10 13.5h.01"/></svg>Action required</span>
                            <?php elseif($row['is_done']): ?>
                                <span class="ft-shipment-state ft-shipment-state--done">Completed</span>
                            <?php else: ?>
                                <span class="ft-shipment-state ft-shipment-state--locked"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Locked</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <button type="button" class="ft-shipment-task__chevron" x-on:click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle task details">
                                <svg viewBox="0 0 20 20" aria-hidden="true" :class="{ 'is-open': open }"><path d="m5.5 7.5 4.5 4.5 4.5-4.5"/></svg>
                            </button>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['key'] === 'SHIP_CONFIRM_INFO'): ?>
                        <div class="ft-shipment-task__expanded" x-cloak x-show="open">
                            <section class="ft-shipment-current-details">
                                <header>
                                    <strong>Current shipment details</strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['can_edit'] && !$row['is_done']): ?>
                                        <button type="button" wire:click="openOrderWorkflowAction(<?php echo e($row['task']->id); ?>)">
                                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                            Edit
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </header>
                                <dl>
                                    <div><dt>Recipient:</dt><dd><?php echo e($presentation['recipient'] ?: '—'); ?></dd></div>
                                    <div><dt>Address:</dt><dd><?php echo e($presentation['address'] ?: '—'); ?></dd></div>
                                    <div><dt>Client:</dt><dd><?php echo e($presentation['client_name'] ?: '—'); ?></dd></div>
                                    <div><dt>Postal code:</dt><dd><?php echo e($presentation['postal_code'] ?: '—'); ?></dd></div>
                                    <div><dt>Phone:</dt><dd><?php echo e($presentation['phone'] ?: '—'); ?></dd></div>
                                </dl>
                            </section>

                            <section class="ft-shipment-review-panel">
                                <h5>Do you want to update the shipment details?</h5>
                                <p>Choose Update details to edit the existing information, or continue without changes.</p>
                                <div class="ft-shipment-review-panel__actions">
                                    <button type="button" class="ft-shipment-btn ft-shipment-btn--outline" <?php if(!$row['can_edit'] || $row['is_done']): echo 'disabled'; endif; ?> wire:click="confirmShipmentDetailsWithoutChanges(<?php echo e($row['task']->id); ?>)" wire:loading.attr="disabled" wire:target="confirmShipmentDetailsWithoutChanges">
                                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4.5 10.5 3.2 3.2 7.8-8"/></svg>No changes — continue
                                    </button>
                                    <button type="button" class="ft-shipment-btn ft-shipment-btn--primary" <?php if(!$row['can_edit'] || $row['is_done']): echo 'disabled'; endif; ?> wire:click="openOrderWorkflowAction(<?php echo e($row['task']->id); ?>)">
                                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>Update details
                                    </button>
                                </div>
                                <small><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 9v4M10 6.5h.01"/></svg>Either action completes this task and unlocks tracking setup.</small>
                            </section>
                        </div>
                    <?php elseif($row['key'] === 'SHIP_LABEL'): ?>
                        <div class="ft-shipment-inline-work" x-data="{ carrier: <?php echo \Illuminate\Support\Js::from(($presentation['carrier'] ?? '') ?: 'UPS')->toHtml() ?>, tracking: <?php echo \Illuminate\Support\Js::from($presentation['tracking'] ?? '')->toHtml() ?> }">
                            <label>
                                <span>COURIER</span>
                                <select x-model="carrier" <?php if($row['mode'] !== 'active' || !$row['can_edit'] || $row['label_generated']): echo 'disabled'; endif; ?>>
                                    <option value="">Select courier</option>
                                    <option value="UPS">UPS</option>
                                    <option value="FedEx">FedEx</option>
                                    <option value="DHL">DHL</option>
                                    <option value="Other">Other</option>
                                </select>
                            </label>
                            <label>
                                <span>TRACKING NUMBER</span>
                                <input type="text" x-model.trim="tracking" placeholder="Enter tracking number" <?php if($row['mode'] !== 'active' || !$row['can_edit'] || $row['label_generated']): echo 'disabled'; endif; ?>>
                            </label>
                            <div class="ft-shipment-inline-work__buttons">
                                <button
                                    type="button"
                                    class="ft-shipment-btn ft-shipment-btn--primary ft-shipment-btn--continue"
                                    <?php if($row['mode'] !== 'active' || !$row['can_edit']): echo 'disabled'; endif; ?>
                                    x-bind:disabled="<?php echo \Illuminate\Support\Js::from($row['mode'] !== 'active' || !$row['can_edit'])->toHtml() ?> || !carrier || !tracking"
                                    x-on:click="$wire.completeShipmentTrackingTask(<?php echo e($row['task']->id); ?>, carrier, tracking)"
                                    wire:loading.attr="disabled"
                                    wire:target="completeShipmentTrackingTask"
                                >
                                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h11M11 6l4 4-4 4"/></svg>Continue to next task
                                </button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['mode'] !== 'active' && !$row['is_done']): ?>
                                <small class="ft-shipment-unlock-note"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Available after Review or update shipment details</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentLabel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php elseif($row['key'] === 'SHIP_PACKAGE'): ?>
                        <div class="ft-shipment-dispatch-work">
                            <button type="button" class="ft-shipment-btn ft-shipment-btn--primary" <?php if($row['mode'] !== 'active' || !$row['can_edit']): echo 'disabled'; endif; ?> wire:click="dispatchShipment(<?php echo e($row['task']->id); ?>)" wire:loading.attr="disabled" wire:target="dispatchShipment">
                                <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m6.5 10 2.2 2.2 4.8-4.8"/></svg>Mark as dispatched
                            </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['mode'] !== 'active' && !$row['is_done']): ?>
                                <small class="ft-shipment-unlock-note"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Available after Add tracking number &amp; print courier label</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentDispatch'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </article>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/phase.blade.php ENDPATH**/ ?>