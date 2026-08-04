<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'dark' => false, 'size' => null]));

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

foreach (array_filter((['name', 'dark' => false, 'size' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php ($initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('')); ?>
<span <?php echo e($attributes->class(['avatar', 'dark' => $dark])->merge(['style' => $size ? "width:{$size}px;height:{$size}px;font-size:".max(9, round($size/3.4))."px" : null])); ?>><?php echo e($initials ?: 'FT'); ?></span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/avatar.blade.php ENDPATH**/ ?>