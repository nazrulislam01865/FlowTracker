<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['stage', 'variant' => 'compact']));

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

foreach (array_filter((['stage', 'variant' => 'compact']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<article
    class="ft-tracking-stage ft-tracking-stage--<?php echo e($variant); ?> is-<?php echo e($stage['state']); ?>"
    data-stage="<?php echo e($stage['sequence']); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'detailed'): ?>
        <span class="ft-tracking-stage-icon">
            <?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => $stage['icon'],'size' => 23]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stage['icon']),'size' => 23]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($stage['marker'] ?? null) === 'check'): ?>
        <span class="ft-tracking-stage-check"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'check','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => 14]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
    <?php elseif(($stage['marker'] ?? null) === 'dot'): ?>
        <span class="ft-tracking-stage-dot" aria-hidden="true"></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-tracking-stage-copy">
        <small>STAGE <?php echo e($stage['sequence']); ?></small>
        <strong><?php echo e($stage['name']); ?></strong>
        <span class="ft-tracking-stage-status"><?php echo e($stage['status']); ?></span>
    </div>

    <progress
        class="ft-tracking-stage-line"
        max="100"
        value="<?php echo e(max(0, min(100, (int) ($stage['progress'] ?? 0)))); ?>"
        aria-label="<?php echo e($stage['name']); ?> progress"
    ></progress>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/order-tracking/stage-card.blade.php ENDPATH**/ ?>