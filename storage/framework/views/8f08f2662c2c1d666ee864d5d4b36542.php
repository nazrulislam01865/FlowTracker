<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id',
    'title',
    'size' => 'md',
    'open' => false,
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
    'id',
    'title',
    'size' => 'md',
    'open' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    <?php echo e($attributes->class(['ft-modal', 'ft-modal--sm' => $size === 'sm', 'ft-modal--lg' => $size === 'lg'])); ?>

    data-ft-ui-component="modal"
    <?php if (! ($open || $attributes->has('x-show'))): ?> hidden <?php endif; ?>
>
    <div class="ft-modal__backdrop" aria-hidden="true"></div>
    <section class="ft-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo e($id); ?>-title" tabindex="-1">
        <header class="ft-modal__header">
            <h2 id="<?php echo e($id); ?>-title" class="ft-modal__title"><?php echo e($title); ?></h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($close)): ?><?php echo e($close); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </header>
        <div class="ft-modal__body"><?php echo e($slot); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer)): ?><footer class="ft-modal__footer"><?php echo e($footer); ?></footer><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/modal.blade.php ENDPATH**/ ?>