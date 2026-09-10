<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'hold' => [],
    'canReleaseHold' => false,
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
    'hold' => [],
    'canReleaseHold' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $hold = is_array($hold) ? $hold : [];
    $holdFrom = trim((string) ($hold['holdFromLabel'] ?? '')) ?: '—';
    $sourceName = trim((string) ($hold['sourceName'] ?? '')) ?: '—';
    $heldBy = trim((string) ($hold['heldBy'] ?? '')) ?: 'Unknown user';
    $reason = trim((string) ($hold['reason'] ?? ''));
    $startedAt = $hold['startedAt'] ?? null;
?>
<div class="ft-order-hold-state-card" x-data="{ detailsOpen: false }">
    <div class="ft-order-hold-state-card__top">
        <div class="ft-order-hold-state-card__icon" aria-hidden="true">
            <span class="ft-order-hold-pause-icon"><i></i><i></i></span>
        </div>
        <div class="ft-order-hold-state-card__copy">
            <strong>Order activities are paused</strong>
            <span><?php echo e($holdFrom); ?> hold · held by <?php echo e($heldBy); ?></span>
        </div>
        <span class="ft-order-hold-state-card__badge">On Hold</span>
    </div>

    <div class="ft-order-hold-state-card__facts">
        <div><span>Hold from</span><b><?php echo e($holdFrom); ?></b></div>
        <div><span>Held by</span><b><?php echo e($heldBy); ?></b></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startedAt): ?>
            <div><span>Started</span><b><?php echo e(\App\Support\UserLocalTime::format($startedAt, 'M j · g:i A')); ?></b></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason !== ''): ?>
        <div class="ft-order-hold-state-card__reason">
            <span>Reason</span>
            <div><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $reason]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reason)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $attributes = $__attributesOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $component = $__componentOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__componentOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-order-hold-state-card__actions">
        <button
            type="button"
            class="ft-order-hold-details-toggle"
            x-on:click="detailsOpen = !detailsOpen"
            x-bind:aria-expanded="detailsOpen.toString()"
            data-order-hold-view-control
        >
            <span x-text="detailsOpen ? 'Hide details' : 'View details'">View details</span>
            <span class="ft-order-hold-chevron" :class="detailsOpen ? 'is-open' : ''" aria-hidden="true">⌄</span>
        </button>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canReleaseHold): ?>
            <button
                type="button"
                class="ft-order-release-hold-button"
                wire:click="releaseOrderHold"
                wire:loading.attr="disabled"
                wire:target="releaseOrderHold"
                data-ft-feedback="off"
                data-order-hold-allowed
            >
                <span wire:loading.remove wire:target="releaseOrderHold" class="ft-order-hold-button-content">
                    <span class="ft-order-release-hold-icon" aria-hidden="true"></span>
                    <span>Unhold</span>
                </span>
                <span wire:loading wire:target="releaseOrderHold" class="ft-order-hold-button-content">
                    <span class="ft-order-hold-mini-spinner" aria-hidden="true"></span>
                    <span>Releasing...</span>
                </span>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-order-hold-state-card__details" x-cloak x-show="detailsOpen" x-transition.opacity.duration.120ms>
        <div><span>Hold from</span><b><?php echo e($holdFrom); ?></b></div>
        <div><span>Name</span><b><?php echo e($sourceName); ?></b></div>
        <div><span>Held by</span><b><?php echo e($heldBy); ?></b></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startedAt): ?>
            <div><span>Started at</span><b><?php echo e(\App\Support\UserLocalTime::format($startedAt, 'M j, Y · g:i A')); ?></b></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason !== ''): ?>
            <div class="ft-order-hold-state-card__full-reason">
                <span>Reason</span>
                <div><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $reason]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reason)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $attributes = $__attributesOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $component = $__componentOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__componentOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?></div>
            </div>
        <?php else: ?>
            <div class="ft-order-hold-state-card__no-reason"><span>Reason</span><b>Not provided</b></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/hold-status-card.blade.php ENDPATH**/ ?>