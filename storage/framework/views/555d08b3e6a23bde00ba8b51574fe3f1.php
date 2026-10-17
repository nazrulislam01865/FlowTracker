<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['activity']));

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

foreach (array_filter((['activity']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $isReleased = (string) $activity->event === 'job.hold_released';
    $meta = (array) ($activity->meta ?? []);
    $holdFrom = (string) (data_get($meta, 'hold_from_label') ?: \App\Models\OrderHold::labelFor(data_get($meta, 'hold_from')));
    $sourceName = trim((string) data_get($meta, 'source_name', '')) ?: '—';
    $reason = trim((string) data_get($meta, 'reason', '')) ?: '—';
    $heldBy = trim((string) data_get($meta, 'held_by_name', '')) ?: ($activity->user?->name ?? 'Unknown user');
    $releasedBy = trim((string) data_get($meta, 'released_by_name', '')) ?: ($activity->user?->name ?? 'Unknown user');
    $startedAt = null;
    $endedAt = null;
    try {
        if (data_get($meta, 'started_at')) $startedAt = \Illuminate\Support\Carbon::parse((string) data_get($meta, 'started_at'));
        if (data_get($meta, 'ended_at')) $endedAt = \Illuminate\Support\Carbon::parse((string) data_get($meta, 'ended_at'));
    } catch (\Throwable) {
        $startedAt = null;
        $endedAt = null;
    }
    $duration = trim((string) data_get($meta, 'duration', ''));
?>
<div class="ft-order-hold-activity-content">
    <div class="ft-order-hold-activity-title"><?php echo e($isReleased ? 'Order unheld' : 'Order placed on hold'); ?></div>
    <div class="ft-order-hold-activity-grid">
        <dl>
            <div><dt>Hold from:</dt><dd><?php echo e($holdFrom); ?></dd></div>
            <div><dt>Name:</dt><dd><?php echo e($sourceName); ?></dd></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason !== '—'): ?><div><dt>Reason:</dt><dd><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
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
<?php endif; ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </dl>
        <dl>
            <div><dt>Held by:</dt><dd><?php echo e($heldBy); ?></dd></div>
            <div><dt>Started:</dt><dd><?php echo e($startedAt ? \App\Support\UserLocalTime::format($startedAt, 'M j, Y \a\t g:i A') : '—'); ?></dd></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isReleased): ?>
                <div><dt>Ended:</dt><dd><?php echo e($endedAt ? \App\Support\UserLocalTime::format($endedAt, 'M j, Y \a\t g:i A') : '—'); ?></dd></div>
                <div><dt>Duration:</dt><dd><?php echo e($duration ?: '—'); ?></dd></div>
                <div><dt>Unheld by:</dt><dd><?php echo e($releasedBy); ?></dd></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </dl>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/hold-activity-content.blade.php ENDPATH**/ ?>