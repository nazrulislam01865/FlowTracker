<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
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
    'label',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<button
    type="<?php echo e($type); ?>"
    <?php echo e($attributes->class([
        'ft-icon-btn',
        'ft-icon-btn--sm' => $size === 'sm',
        'ft-icon-btn--lg' => $size === 'lg',
    ])); ?>

    data-ft-ui-component="icon-button"
    aria-label="<?php echo e($label); ?>"
    title="<?php echo e($label); ?>"
    <?php if($disabled || $loading): echo 'disabled'; endif; ?>
    <?php if($loading): ?> aria-busy="true" <?php endif; ?>
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loading): ?>
        <span class="ft-icon-btn__spinner" aria-hidden="true"></span>
    <?php else: ?>
        <?php echo e($slot); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</button>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/icon-button.blade.php ENDPATH**/ ?>