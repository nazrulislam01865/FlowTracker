<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status', 'summary']));

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

foreach (array_filter((['status', 'summary']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-tracking-overview is-<?php echo e($status['tone'] ?? 'success'); ?>">
    <div class="ft-tracking-banner">
        <span class="ft-tracking-banner-icon">
            <?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => $status['icon'],'size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status['icon']),'size' => 28]); ?>
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
        <div class="ft-tracking-banner-copy">
            <strong><?php echo e($status['title']); ?></strong>
            <p><?php echo e($status['message']); ?></p>
        </div>
    </div>

    <div class="ft-tracking-summary">
        <?php if (isset($component)) { $__componentOriginal1d01c42a53fe56489990480be3a473e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d01c42a53fe56489990480be3a473e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.summary-item','data' => ['title' => 'Delivery','data' => $summary['delivery'],'icon' => 'truck']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.summary-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Delivery','data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($summary['delivery']),'icon' => 'truck']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $attributes = $__attributesOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__attributesOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $component = $__componentOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__componentOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal1d01c42a53fe56489990480be3a473e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d01c42a53fe56489990480be3a473e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.summary-item','data' => ['title' => 'Billing','data' => $summary['billing'],'icon' => 'billing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.summary-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Billing','data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($summary['billing']),'icon' => 'billing']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $attributes = $__attributesOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__attributesOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $component = $__componentOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__componentOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal1d01c42a53fe56489990480be3a473e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d01c42a53fe56489990480be3a473e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.summary-item','data' => ['title' => 'Payment','data' => $summary['payment'],'icon' => 'payment']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.summary-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Payment','data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($summary['payment']),'icon' => 'payment']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $attributes = $__attributesOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__attributesOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $component = $__componentOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__componentOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/order-tracking/overview.blade.php ENDPATH**/ ?>