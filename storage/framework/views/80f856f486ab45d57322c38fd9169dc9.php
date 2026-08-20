<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['compact' => false]));

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

foreach (array_filter((['compact' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<span
    <?php echo e($attributes->class(['ft-inline-save-state', 'compact' => $compact])); ?>

    data-ft-ui-component="inline-save-state"
    :class="{
        'is-saving': status === 'saving',
        'is-saved': status === 'saved',
        'is-error': status === 'error'
    }"
    aria-live="polite"
>
    <span x-cloak x-show="status === 'saving'">Saving…</span>
    <span x-cloak x-show="status === 'saved'">Saved</span>
    <button x-cloak x-show="status === 'error'" type="button" x-on:click.stop="retry()" title="Retry this save">Not saved · Retry</button>
</span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/inline-save-state.blade.php ENDPATH**/ ?>