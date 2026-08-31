<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'context' => [], 'shipmentUrgencyOptions' => collect(), 'redoContext' => []]));

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

foreach (array_filter((['job', 'context' => [], 'shipmentUrgencyOptions' => collect(), 'redoContext' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $team = collect($context['team'] ?? []);
    $canEditJob = (bool) ($context['canEditJob'] ?? false);
    $canChangeOwner = (bool) ($context['canChangeOwner'] ?? false);
    $canCancel = (bool) ($context['canCancel'] ?? false);
    $attentionLocked = (bool) ($context['attentionLocked'] ?? false);
    $flagged = (bool) ($context['flagged'] ?? false);
    $flagReason = trim((string) ($context['flagReason'] ?? ''));
    $isCancelled = strcasecmp((string) $job->status, 'Cancelled') === 0;
    $stageName = $isCancelled ? ($job->phase?->name ?: 'Cancelled') : ($job->phase?->name ?: 'New Order');
    $hasRedo = (bool) ($redoContext['hasRedo'] ?? false);
    $isRedoOrder = (bool) ($redoContext['isRedoOrder'] ?? false);
    $redoOrderCount = (int) ($redoContext['redoOrderCount'] ?? 0);

    // Show this only when an operational Redo Order actually exists.
    // A discount-only resolution creates a Redo record but no Redo Order,
    // so it must not be labelled as "Redo initiated".
    $redoInitiated = $isRedoOrder || $redoOrderCount > 0;

    $canInitiateRedo = (bool) ($redoContext['canInitiate'] ?? false);
?>
<section class="detail-header ft-order-prototype-header">
    <div class="breadcrumbs ft-order-prototype-breadcrumb">
        Orders &nbsp;/&nbsp;
        <b><?php echo e($job->displayOrderNumber()); ?></b>
        <button type="button" class="copy-order-code" title="Copy Order ID" aria-label="Copy <?php echo e($job->displayOrderNumber()); ?>" data-copy-value="<?php echo e($job->displayOrderNumber()); ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="8" y="8" width="10" height="10" rx="1.5"></rect><path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"></path></svg>
        </button>
    </div>

    <div class="detail-title-row ft-order-prototype-title-row">
        <div class="detail-heading" style="flex:1">
            <h1 class="detail-title ft-order-prototype-title"><?php echo e($job->title); ?></h1>

            <div class="meta-line ft-order-prototype-meta" aria-label="Order information">
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg>
                    </span>
                    <span class="ft-client-inline-identity">
                        <?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $job->client,'name' => $job->client?->name ?: 'Client','size' => 20]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client?->name ?: 'Client'),'size' => 20]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
                        <span>Client <strong><?php echo e($job->client?->name ?: '—'); ?></strong></span>
                    </span>
                </span>
                <span class="meta-sep" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item ft-order-header-reference">
                    <span class="ft-order-header-meta-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M7 3.5h7l4 4V20.5H7z"></path><path d="M14 3.5v4h4"></path></svg>
                    </span>
                    <span>Reference <strong><?php echo e($job->order_number ?: '—'); ?></strong></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->order_number): ?>
                        <button type="button" class="ft-order-header-copy" title="Copy Reference Number" aria-label="Copy reference number <?php echo e($job->order_number); ?>" data-copy-value="<?php echo e($job->order_number); ?>">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="8" y="8" width="10" height="10" rx="1.5"></rect><path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span class="meta-sep" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg>
                    </span>
                    <span>Created by <strong><?php echo e($job->creator?->name ?: 'System'); ?></strong></span>
                </span>
                <span class="meta-sep" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 10h16"></path></svg>
                    </span>
                    <span>Created <strong><?php echo e($job->created_at ? \App\Support\UserLocalTime::format($job->created_at, 'M j, Y') : '—'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->created_at): ?> at <?php echo e(\App\Support\UserLocalTime::format($job->created_at, 'g:i A')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong></span>
                </span>
            </div>

            <div class="pills ft-order-prototype-pills">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redoInitiated): ?>
                    <span
                        class="pill redo-initiated"
                        title="A linked Redo Order has been initiated."
                    >
                        Redo initiated
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCancelled): ?>
                    <span class="pill cancelled">⊘ Cancelled</span>
                    <span class="pill purple" id="stagePill" title="The last workflow stage reached before cancellation.">Last stage · <?php echo e($stageName); ?></span>
                <?php else: ?>
                    <span class="pill purple" id="stagePill" title="The workflow stage containing the current required task."><?php echo e($stageName); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isRedoOrder): ?>
                    <span class="pill redo">↻ Redo order</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>

        <div class="ft-order-header-side">
            <div class="people ft-order-team-stack" title="Team members currently involved in this order">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $team->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php ($initials = collect(preg_split('/\s+/', trim((string) $member->name)))->filter()->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('')); ?>
                    <i title="<?php echo e($member->name); ?>"><?php echo e($initials ?: '—'); ?></i>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->count() > 4): ?><i>+<?php echo e($team->count() - 4); ?></i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>

            <div class="order-commandbar ft-order-commandbar">
                <?php if (isset($component)) { $__componentOriginal11e87df7849113b7a617fb258a6fac39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11e87df7849113b7a617fb258a6fac39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment-urgency-inline','data' => ['job' => $job,'canEditJob' => $canEditJob,'shipmentUrgencyOptions' => $shipmentUrgencyOptions,'context' => $context,'variant' => 'header']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment-urgency-inline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'can-edit-job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditJob),'shipment-urgency-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyOptions),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'variant' => 'header']); ?>
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

                <div class="header-owner-control ft-inline-edit-shell"
                    x-data="{
                        ...window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-header-owner')->toHtml() ?>, label: 'Order owner', value: <?php echo \Illuminate\Support\Js::from($job->owner_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->owner?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($job->owner?->profileImageUrl() ?? '')->toHtml() ?> }),
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
                    x-on:click.outside="if(editing) cancelEdit()"
                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                    x-on:ft-inline-remote-selected.stop="saveOwner($event.detail)"
                    x-on:ft-order-owner-updated.window="syncOwner($event.detail)"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeOwner): ?>
                        <button
                            x-ref="headerOwnerAnchor"
                            type="button"
                            class="assignee-stage-chip ft-order-owner-chip-trigger"
                            :class="{ 'is-open': editing }"
                            x-on:click.stop="openRemotePicker($refs.headerOwnerAnchor)"
                            title="Edit order owner"
                            aria-label="Edit order owner"
                        >
                            <?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 22]); ?>
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
<?php endif; ?>
                            <span x-text="display"><?php echo e($job->owner?->name ?: 'Unassigned'); ?></span>
                            <span class="ft-order-inline-trigger-icon" aria-hidden="true">✎</span>
                        </button>
                        <div class="header-owner-picker" x-cloak x-show="editing">
                            <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $job->owner_id ?? '','selectedLabel' => $job->owner?->name ?? 'Unassigned','context' => 'job-owner','instanceKey' => 'order-header-owner','parentType' => 'job','parentId' => $job->id,'searchPlaceholder' => 'Search owner...','variant' => 'compact','menuWidth' => 320,'externalTrigger' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner?->name ?? 'Unassigned'),'context' => 'job-owner','instance-key' => 'order-header-owner','parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'search-placeholder' => 'Search owner...','variant' => 'compact','menu-width' => 320,'external-trigger' => true]); ?>
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
<?php endif; ?>
                        </div>
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
                    <?php else: ?>
                        <span class="assignee-stage-chip"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 22]); ?>
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
<?php endif; ?><span x-text="display"><?php echo e($job->owner?->name ?: 'Unassigned'); ?></span></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-order-header-actions" aria-label="Order actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canInitiateRedo): ?>
                        <button type="button" class="btn redo small" wire:click="openRedoModal" title="Create a controlled redo linked to this order.">↻ Initiate Redo</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isCancelled): ?>
                        <button type="button" class="btn small flag-btn <?php echo e($flagged ? 'flagged' : ''); ?>" wire:click="openOrderAttentionReason" <?php if($attentionLocked): echo 'disabled'; endif; ?> title="<?php echo e($flagReason ?: 'Flag this order so it remains visibly marked for attention across every stage.'); ?>">⚑ <?php echo e($flagged ? 'Flagged' : 'Flag order'); ?></button>
                        <button type="button" class="btn danger small" wire:click="openOrderCancelModal" <?php if(!$canCancel): echo 'disabled'; endif; ?> title="<?php echo e($canCancel ? 'Cancel this order. Cancellation is available only through the QC stage.' : 'Cancellation is available only through the QC stage.'); ?>">⊘ Cancel order</button>
                    <?php else: ?>
                        <span class="ft-order-workflow-lock" title="Workflow actions are blocked because this order is cancelled.">⊘ Workflow locked</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
</section>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCancelled): ?>
    <?php if (isset($component)) { $__componentOriginal2082eb8f2933d1bc150813eaf259cac2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2082eb8f2933d1bc150813eaf259cac2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.cancellation-card','data' => ['job' => $job,'stageName' => $stageName]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.cancellation-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'stage-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stageName)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2082eb8f2933d1bc150813eaf259cac2)): ?>
<?php $attributes = $__attributesOriginal2082eb8f2933d1bc150813eaf259cac2; ?>
<?php unset($__attributesOriginal2082eb8f2933d1bc150813eaf259cac2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2082eb8f2933d1bc150813eaf259cac2)): ?>
<?php $component = $__componentOriginal2082eb8f2933d1bc150813eaf259cac2; ?>
<?php unset($__componentOriginal2082eb8f2933d1bc150813eaf259cac2); ?>
<?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flagged): ?>
    <div class="state-banner flag show ft-order-state-banner"><span>⚑</span><div><b>Flagged for attention</b><p><?php echo e($flagReason ?: 'This order requires attention.'); ?></p></div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/header.blade.php ENDPATH**/ ?>