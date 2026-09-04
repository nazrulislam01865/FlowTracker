<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'canEditJob' => false, 'canChangeOwner' => false, 'shipmentUrgencyOptions' => collect(), 'context' => [], 'remoteArea' => null]));

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

foreach (array_filter((['job', 'canEditJob' => false, 'canChangeOwner' => false, 'shipmentUrgencyOptions' => collect(), 'context' => [], 'remoteArea' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    // Remote Area is resolved once in OrderDetailViewService. Keep this Blade
    // component presentation-only so moving the flag here never adds queries.
    $remoteArea = is_array($remoteArea) && ! empty($remoteArea['postal_code']) ? $remoteArea : null;
?>
<section class="section-card ft-order-section-card ft-order-planning-card">
    <div class="section-head ft-order-section-head"><h2>Planning &amp; ownership</h2><span class="card-sub">Quick edits</span></div>
    <div class="section-body info-list ft-order-info-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($remoteArea): ?>
            <div class="info-row ft-order-info-row ft-order-remote-area-row">
                <span>Remote area</span>
                <b>
                    <span
                        class="pill amber ft-order-remote-area-pill"
                        title="<?php echo e(trim((string) ($remoteArea['name'] ?? 'Remote Area'))); ?> · <?php echo e($remoteArea['postal_code']); ?>"
                        aria-label="Remote Area postal code match"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 21V4m0 1h10l-1.5 3L15 11H5"></path></svg>
                        Remote Area
                    </span>
                </b>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="info-row ft-order-info-row ft-inline-edit-shell"
            x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-delivery-date')->toHtml() ?>, label: 'delivery date', value: <?php echo \Illuminate\Support\Js::from($job->delivery_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->delivery_date?->format('M j, Y') ?? 'Not set')->toHtml() ?> })">
            <span>Required delivery</span>
            <b><span x-show="!editing" x-text="display"><?php echo e($job->delivery_date?->format('M j, Y') ?? 'Not set'); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                    <button x-show="!editing" type="button" class="inline-edit" x-on:click.stop="if(beginEdit()) $nextTick(() => $refs.delivery.focus())">✎</button>
                    <input x-ref="delivery" x-cloak x-show="editing" type="date" x-model="draftValue" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit(draftValue, draftValue ? new Date(draftValue+'T12:00:00').toLocaleDateString(undefined,{month:'short',day:'numeric',year:'numeric'}) : 'Not set', () => $wire.updateJobDeliveryDate(<?php echo e($job->id); ?>, draftValue))">
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </b>
        </div>
        <div class="info-row ft-order-info-row"><span>Reference number</span><b><?php echo e($job->order_number ?: '—'); ?></b></div>
        <div id="order-shipment-urgency" class="info-row ft-order-info-row ft-order-urgency-info-row">
            <span><span class="help" title="Shipment urgency determines operational prioritization for packing, carrier booking, and dispatch.">Shipment urgency</span></span>
            <b>
                <?php if (isset($component)) { $__componentOriginal11e87df7849113b7a617fb258a6fac39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11e87df7849113b7a617fb258a6fac39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment-urgency-inline','data' => ['job' => $job,'canEditJob' => $canEditJob,'shipmentUrgencyOptions' => $shipmentUrgencyOptions,'context' => $context,'variant' => 'planning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment-urgency-inline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'can-edit-job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditJob),'shipment-urgency-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyOptions),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'variant' => 'planning']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11e87df7849113b7a617fb258a6fac39)): ?>
<?php $attributes = $__attributesOriginal11e87df7849113b7a617fb258a6fac39; ?>
<?php unset($__attributesOriginal11e87df7849113b7a617fb258a6fac39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11e87df7849113b7a617fb258a6fac39)): ?>
<?php $component = $__componentOriginal11e87df7849113b7a617fb258a6fac39; ?>
<?php unset($__componentOriginal11e87df7849113b7a617fb258a6fac39); ?>
<?php endif; ?>
            </b>
        </div>
        <div id="order-owner-field" class="info-row ft-order-info-row ft-inline-edit-shell ft-order-owner-inline-field"
            x-data="{
                ...window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-owner')->toHtml() ?>, label: 'Order owner', value: <?php echo \Illuminate\Support\Js::from($job->owner_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->owner?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($job->owner?->profileImageUrl() ?? '')->toHtml() ?> }),
                syncOwner(detail) {
                    if (!detail || Number(detail.jobId) !== Number(<?php echo e($job->id); ?>)) return;
                    const nextValue = String(detail.value ?? '');
                    const nextDisplay = String(detail.label ?? 'Unassigned');
                    const nextAvatarUrl = String(detail.avatarUrl ?? '');
                    const fromSelf = String(detail.sourceKey ?? '') === this.key;

                    this.serverValue = nextValue;
                    this.value = nextValue;
                    this.savedValue = nextValue;
                    this.draftValue = nextValue;
                    this.display = nextDisplay;
                    this.savedDisplay = nextDisplay;
                    this.avatarUrl = nextAvatarUrl;
                    this.savedAvatarUrl = nextAvatarUrl;
                    this.editing = false;

                    if (!fromSelf && this.status !== 'saving') {
                        this.status = '';
                        this.error = '';
                    }
                },
                async saveOwner(detail) {
                    const nextValue = String(detail?.value ?? '');
                    const nextDisplay = String(detail?.label ?? 'Unassigned');
                    const nextAvatarUrl = String(detail?.avatarUrl ?? '');
                    const ok = await this.commit(
                        nextValue,
                        nextDisplay,
                        () => $wire.updateJobOwner(<?php echo e($job->id); ?>, nextValue),
                        { avatarUrl: nextAvatarUrl }
                    );

                    if (!ok) return;

                    const payload = {
                        jobId: <?php echo e($job->id); ?>,
                        value: String(this.value ?? ''),
                        label: String(this.display ?? 'Unassigned'),
                        avatarUrl: String(this.lastResponse?.avatarUrl ?? this.avatarUrl ?? ''),
                        sourceKey: this.key,
                    };
                    this.syncOwner(payload);
                    window.dispatchEvent(new CustomEvent('ft-order-owner-updated', { detail: payload }));
                }
            }"
            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            x-on:click.outside="if(editing) cancelEdit()"
            x-on:ft-inline-remote-cancel.stop="cancelEdit()"
            x-on:ft-inline-remote-selected.stop="saveOwner($event.detail)"
            x-on:ft-order-owner-updated.window="syncOwner($event.detail)">
            <span>Order owner</span>
            <b>
                <span class="ft-order-inline-display-row ft-order-owner-display-row">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeOwner): ?>
                        <button
                            x-ref="ownerAnchor"
                            :disabled="status === 'saving'"
                            type="button"
                            class="ft-order-assignee-display ft-order-owner-display ft-order-inline-name-trigger"
                            :class="{ 'is-open': editing }"
                            title="Edit order owner"
                            aria-label="Edit order owner"
                            x-on:click.stop="openRemotePicker($refs.ownerAnchor)"
                        >
                            <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 28]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?></span>
                            <span class="ft-order-assignee-name" x-text="display"><?php echo e($job->owner?->name ?? 'Unassigned'); ?></span>
                            <span class="ft-order-inline-trigger-icon" aria-hidden="true">✎</span>
                        </button>
                    <?php else: ?>
                        <span class="ft-order-assignee-display ft-order-owner-display">
                            <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 28]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?></span>
                            <span class="ft-order-assignee-name" x-text="display"><?php echo e($job->owner?->name ?? 'Unassigned'); ?></span>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeOwner): ?>
                    <span x-cloak x-show="editing" class="ft-order-owner-picker"><?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $job->owner_id ?? '','selectedLabel' => $job->owner?->name ?? 'Unassigned','context' => 'job-owner','instanceKey' => 'order-planning-owner','parentType' => 'job','parentId' => $job->id,'searchPlaceholder' => 'Search owner...','variant' => 'compact','menuWidth' => 300,'externalTrigger' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner?->name ?? 'Unassigned'),'context' => 'job-owner','instance-key' => 'order-planning-owner','parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'search-placeholder' => 'Search owner...','variant' => 'compact','menu-width' => 300,'external-trigger' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?></span>
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </b>
        </div>
        <div class="info-row ft-order-info-row"><span>Workflow</span><b><?php echo e($job->workflow?->name ?: 'FlowTrack Order Workflow'); ?></b></div>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/planning.blade.php ENDPATH**/ ?>