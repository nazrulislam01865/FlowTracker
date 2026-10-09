<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'hold' => null,
    'canReleaseHold' => false,
    'orderId' => null,
    'directRelease' => true,
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
    'hold' => null,
    'canReleaseHold' => false,
    'orderId' => null,
    'directRelease' => true,
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
    $heldBy = trim((string) ($hold['heldBy'] ?? '')) ?: 'Unknown user';
    $reason = trim((string) ($hold['reason'] ?? ''));
    $startedAt = $hold['startedAt'] ?? null;
?>
<div
    class="ft-order-hold-blocked-backdrop"
    x-cloak
    x-show="holdBlockedOpen"
    x-transition.opacity.duration.140ms
    x-on:keydown.escape.window="closeHoldBlocked()"
    x-on:click.self="closeHoldBlocked()"
    role="presentation"
    data-order-hold-allowed
>
    <section
        class="ft-order-hold-blocked-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="order-hold-blocked-title"
        x-show="holdBlockedOpen"
        x-transition.scale.origin.center.duration.140ms
    >
        <button
            x-ref="orderHoldBlockedClose"
            type="button"
            class="ft-order-hold-blocked-close"
            x-on:click="closeHoldBlocked()"
            aria-label="Close"
            data-order-hold-allowed
        >×</button>

        <div class="ft-order-hold-blocked-icon" aria-hidden="true">
            <span class="ft-order-hold-pause-icon"><i></i><i></i></span>
        </div>

        <div class="ft-order-hold-blocked-copy">
            <span class="ft-order-hold-blocked-eyebrow">ORDER ON HOLD</span>
            <h2 id="order-hold-blocked-title">This activity is currently locked</h2>
            <p>
                To perform any activity on this order, unhold the order first.
                <span x-show="blockedAction" x-cloak> You tried to <b x-text="blockedAction"></b>.</span>
            </p>
        </div>

        <div class="ft-order-hold-blocked-summary">
            <div><span>Hold from</span><strong><?php echo e($holdFrom); ?></strong></div>
            <div><span>Held by</span><strong><?php echo e($heldBy); ?></strong></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startedAt): ?>
                <div><span>Since</span><strong><?php echo e(\App\Support\UserLocalTime::format($startedAt, 'M j, Y · g:i A')); ?></strong></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason !== ''): ?>
            <div class="ft-order-hold-blocked-reason">
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

        <div class="ft-order-hold-blocked-actions">
            <button type="button" class="secondary" x-on:click="closeHoldBlocked()" data-order-hold-allowed>Close</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canReleaseHold && $directRelease): ?>
                <button
                    type="button"
                    class="ft-order-hold-blocked-release"
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
            <?php elseif($orderId): ?>
                <button
                    type="button"
                    class="ft-order-hold-blocked-release"
                    wire:click="openJob(<?php echo e((int) $orderId); ?>)"
                    data-order-hold-allowed
                >
                    <span>Open order to release</span>
                </button>
            <?php else: ?>
                <div class="ft-order-hold-blocked-permission">Ask an authorized user to release this hold.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/hold-blocked-modal.blade.php ENDPATH**/ ?>