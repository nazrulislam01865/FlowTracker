<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'fromProperty' => 'dateFrom', 'toProperty' => 'dateTo', 'fromValue' => '', 'toValue' => '',
    'label' => 'Created date', 'fromLabel' => 'Date from', 'toLabel' => 'Date to',
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
    'fromProperty' => 'dateFrom', 'toProperty' => 'dateTo', 'fromValue' => '', 'toValue' => '',
    'label' => 'Created date', 'fromLabel' => 'Date from', 'toLabel' => 'Date to',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php if (isset($component)) { $__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.date-range','data' => ['attributes' => $attributes,'fromProperty' => $fromProperty,'toProperty' => $toProperty,'fromValue' => $fromValue,'toValue' => $toValue,'label' => $label,'fromLabel' => $fromLabel,'toLabel' => $toLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.date-range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes),'from-property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fromProperty),'to-property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($toProperty),'from-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fromValue),'to-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($toValue),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'from-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fromLabel),'to-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($toLabel)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d)): ?>
<?php $attributes = $__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d; ?>
<?php unset($__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d)): ?>
<?php $component = $__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d; ?>
<?php unset($__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/date-range-filter.blade.php ENDPATH**/ ?>