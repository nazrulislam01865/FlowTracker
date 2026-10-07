<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'data', 'icon']));

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

foreach (array_filter((['title', 'data', 'icon']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="ft-tracking-summary-item is-<?php echo e($data['state']); ?>">
    <span class="ft-tracking-summary-icon"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => $icon,'size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'size' => 24]); ?>
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
    <span>
        <strong><?php echo e($title); ?></strong>
        <small><?php echo e($data['label']); ?></small>
    </span>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/order-tracking/summary-item.blade.php ENDPATH**/ ?>