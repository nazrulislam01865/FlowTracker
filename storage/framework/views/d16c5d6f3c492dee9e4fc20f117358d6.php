<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'phase',
    'selected' => false,
    'context' => [],
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
    'selected' => false,
    'context' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $state = \App\Support\OrderDetailPresenter::phaseState($job, $phase);
    $progress = \App\Support\OrderDetailPresenter::phaseProgress($job, $phase);
    $phaseTasks = \App\Support\OrderDetailPresenter::phaseTasks($job, $phase)
        ->sortBy(fn ($task) => [(int) ($task->setupTemplate?->sort_order ?? $task->template?->sequence ?? 999999), (int) $task->id])
        ->values();
    $ownerTask = $phaseTasks->first();
    $ownerName = $ownerTask?->assignee?->name ?: \App\Support\OrderDetailPresenter::phaseOwnerName($job, $phase);
    $ownerInitials = \App\Support\OrderDetailPresenter::initials($ownerName);
    $canOpen = in_array($state, ['active', 'completed', 'cancelled'], true);
    $color = \App\Support\MasterColor::normalize((string) ($phase->color ?? '')) ?: '#94a3b8';
    $ownerPermissions = $ownerTask ? data_get($context, 'taskPermissions.'.(int) $ownerTask->id, []) : [];
    $canAssignOwner = $ownerTask && (bool) data_get($ownerPermissions, 'assign', false)
        && strcasecmp((string) $job->status, 'Cancelled') !== 0;
?>
<div
    class="stage <?php echo e($state); ?> <?php echo e($selected ? 'viewing' : ''); ?>"
    style="--stage:<?php echo e($color); ?>"
    <?php if($canOpen): ?>
        wire:click="selectOverviewPhase(<?php echo e((int) $phase->id); ?>)"
        role="button"
        tabindex="0"
        x-on:keydown.enter.prevent="$wire.selectOverviewPhase(<?php echo e((int) $phase->id); ?>)"
        x-on:keydown.space.prevent="$wire.selectOverviewPhase(<?php echo e((int) $phase->id); ?>)"
    <?php endif; ?>
    title="<?php echo e($canOpen ? 'Open '.$phase->name.' tasks.' : \App\Support\OrderDetailPresenter::phaseDependencyLabel($job, $phase)); ?>"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-workflow-stage-'.e($phase->id).''; ?>wire:key="order-workflow-stage-<?php echo e($phase->id); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($state === 'completed'): ?><span class="stage-check">✓</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="stage-num">STAGE <?php echo e((int) $phase->sequence); ?></div>
    <div class="stage-name"><?php echo e($phase->name); ?></div>
    <div class="stage-state"><?php echo e(\App\Support\OrderDetailPresenter::phaseStateLabel($job, $phase)); ?></div>
    <div class="progress"><i style="width:<?php echo e($progress); ?>%"></i></div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ownerTask): ?>
        <div
            class="stage-owner ft-order-stage-owner-inline ft-inline-edit-shell"
            x-data="window.FlowTrack.ui.inlineEdit({ key:<?php echo \Illuminate\Support\Js::from('stage-'.$phase->id.'-owner')->toHtml() ?>, label:'stage assignee', value:<?php echo \Illuminate\Support\Js::from($ownerTask->assignee_id ?? '')->toHtml() ?>, display:<?php echo \Illuminate\Support\Js::from($ownerName)->toHtml() ?>, avatarUrl:<?php echo \Illuminate\Support\Js::from($ownerTask->assignee?->profileImageUrl() ?? '')->toHtml() ?> })"
            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            x-on:click.stop
            x-on:click.outside="if(editing) cancelEdit()"
            x-on:ft-inline-remote-cancel.stop="cancelEdit()"
            x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeFromJob(<?php echo e((int) $ownerTask->id); ?>, draftValue), { avatarUrl:String($event.detail?.avatarUrl ?? '') })"
        >
            <div class="ft-order-inline-display-row">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignOwner): ?>
                    <button
                        x-ref="stageAssigneeAnchor"
                        :disabled="status === 'saving'"
                        type="button"
                        class="ft-order-assignee-display ft-order-stage-assignee-display ft-order-inline-name-trigger"
                        :class="{ 'is-open': editing }"
                        title="Edit stage assignee"
                        aria-label="Edit <?php echo e($phase->name); ?> stage assignee"
                        x-on:click.stop="openRemotePicker($refs.stageAssigneeAnchor)"
                    >
                        <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 21]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 21]); ?>
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
                        <span class="ft-order-assignee-name" x-text="display"><?php echo e($ownerName); ?></span>
                        <span class="ft-order-inline-trigger-icon" aria-hidden="true">✎</span>
                    </button>
                <?php else: ?>
                    <span class="ft-order-assignee-display ft-order-stage-assignee-display">
                        <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 21]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 21]); ?>
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
                        <span class="ft-order-assignee-name" x-text="display"><?php echo e($ownerName); ?></span>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignOwner): ?>
                <span x-cloak x-show="editing" class="ft-order-stage-assignee-picker">
                    <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $ownerTask->assignee_id ?? '','selectedLabel' => $ownerName,'parentType' => 'job','parentId' => $job->id,'variant' => 'compact','menuWidth' => 300,'externalTrigger' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerTask->assignee_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerName),'parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'variant' => 'compact','menu-width' => 300,'external-trigger' => true]); ?>
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
                </span>
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
        </div>
    <?php else: ?>
        <div class="stage-owner"><i class="mini-avatar"><?php echo e($ownerInitials); ?></i><span><?php echo e($ownerName); ?></span></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="stage-dependency"><?php echo e(\App\Support\OrderDetailPresenter::phaseDependencyLabel($job, $phase)); ?></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/stage-card.blade.php ENDPATH**/ ?>