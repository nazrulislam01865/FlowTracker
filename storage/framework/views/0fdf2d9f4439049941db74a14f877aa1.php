<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label', 'property', 'value' => '', 'placeholder' => 'All', 'options' => collect(),
    'selectedLabel' => null, 'disabled' => false, 'clearable' => true, 'action' => null,
    'menuWidth' => 300, 'searchPlaceholder' => null, 'required' => false, 'optional' => false,
    'fixedMenu' => false, 'footerMessage' => null, 'hideLabel' => false,
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
    'label', 'property', 'value' => '', 'placeholder' => 'All', 'options' => collect(),
    'selectedLabel' => null, 'disabled' => false, 'clearable' => true, 'action' => null,
    'menuWidth' => 300, 'searchPlaceholder' => null, 'required' => false, 'optional' => false,
    'fixedMenu' => false, 'footerMessage' => null, 'hideLabel' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['attributes' => $attributes,'label' => $label,'property' => $property,'value' => $value,'placeholder' => $placeholder,'options' => $options,'selectedLabel' => $selectedLabel,'disabled' => $disabled,'clearable' => $clearable,'action' => $action,'menuWidth' => $menuWidth,'searchPlaceholder' => $searchPlaceholder,'required' => $required,'optional' => $optional,'fixedMenu' => $fixedMenu,'footerMessage' => $footerMessage,'hideLabel' => $hideLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($options),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedLabel),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($disabled),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clearable),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action),'menu-width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($menuWidth),'search-placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchPlaceholder),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($required),'optional' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($optional),'fixed-menu' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fixedMenu),'footer-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerMessage),'hide-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hideLabel)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/select-filter.blade.php ENDPATH**/ ?>