<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label' => 'Setup editor', 'class' => 'ft-phase-reference-modal', 'closeAction' => null]));

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

foreach (array_filter((['label' => 'Setup editor', 'class' => 'ft-phase-reference-modal', 'closeAction' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="ft-reference-overlay" <?php if(isset($closeAction)): ?> wire:click.self="<?php echo e($closeAction); ?>" <?php endif; ?>></div>
<div class="<?php echo e($class); ?>" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-label="<?php echo e($label); ?>">
    <?php echo e($slot); ?>

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/setup/editor-modal.blade.php ENDPATH**/ ?>