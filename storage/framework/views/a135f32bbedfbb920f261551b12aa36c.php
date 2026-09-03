<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'error' => null,
    'required' => false,
    'optional' => false,
    'disabled' => false,
    'readonly' => false,
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
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'error' => null,
    'required' => false,
    'optional' => false,
    'disabled' => false,
    'readonly' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-field" data-ft-ui-component="field">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($label)): ?>
        <div class="ft-field__label-row">
            <label class="ft-field__label" for="<?php echo e($id ?? $name); ?>">
                <?php echo e($label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?><span class="ft-field__required" aria-hidden="true"> *</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($optional): ?><span class="ft-field__optional">Optional</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <input
        id="<?php echo e($id ?? $name); ?>"
        <?php if($name): ?> name="<?php echo e($name); ?>" <?php endif; ?>
        type="<?php echo e($type); ?>"
        <?php if(!is_null($value)): ?> value="<?php echo e($value); ?>" <?php endif; ?>
        <?php if(filled($placeholder)): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
        <?php echo e($attributes->class(['ft-field__control'])); ?>

        <?php if($help || $error || ($name && $errors->has($name))): ?> aria-describedby="<?php echo e($id ?? $name); ?>-feedback" <?php endif; ?>
        <?php if($error || ($name && $errors->has($name))): ?> aria-invalid="true" <?php endif; ?>
        <?php if($required): echo 'required'; endif; ?>
        <?php if($disabled): echo 'disabled'; endif; ?>
        <?php if($readonly): echo 'readonly'; endif; ?>
    >
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($help || $error || ($name && $errors->has($name))): ?>
        <div id="<?php echo e($id ?? $name); ?>-feedback" class="ft-field__feedback">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($help)): ?><div class="ft-field__help"><?php echo e($help); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error || ($name && $errors->has($name))): ?><?php if (isset($component)) { $__componentOriginalce11a07acd8b47e338d25689bef957cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce11a07acd8b47e338d25689bef957cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.validation-message','data' => ['message' => $error ?: $errors->first($name)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.validation-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($error ?: $errors->first($name))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $attributes = $__attributesOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__attributesOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $component = $__componentOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__componentOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/input.blade.php ENDPATH**/ ?>