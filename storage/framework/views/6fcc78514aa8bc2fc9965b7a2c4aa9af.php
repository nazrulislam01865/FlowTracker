<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => '',
    'extension' => null,
    'size' => 'md',
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
    'name' => '',
    'extension' => null,
    'size' => 'md',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $ext = strtolower(trim((string) ($extension ?: pathinfo((string) $name, PATHINFO_EXTENSION) ?: 'file')));
    $label = strtoupper($ext ?: 'FILE');
    $group = match ($ext) {
        'pdf' => 'pdf',
        'jpg', 'jpeg' => 'jpg',
        'png', 'webp', 'gif', 'ico', 'bmp', 'svg' => 'image',
        'ai' => 'ai',
        'eps', 'esp', 'ps' => 'eps',
        'cdr' => 'cdr',
        'zip', 'rar', '7z' => 'archive',
        'doc', 'docx' => 'doc',
        'xls', 'xlsx', 'csv' => 'sheet',
        'ppt', 'pptx' => 'slide',
        'txt' => 'text',
        default => 'file',
    };
?>
<span <?php echo e($attributes->class(['ft-file-type-badge', 'ft-file-type-badge--'.$group, 'ft-file-type-badge--'.$size])); ?> aria-hidden="true"><?php echo e($label); ?></span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/file-type-badge.blade.php ENDPATH**/ ?>