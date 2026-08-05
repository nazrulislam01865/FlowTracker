<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label']));

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

foreach (array_filter((['label']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
$label = (string) $label;
$class = match (true) {
    preg_match('/Completed|Approved|Paid|Delivered|On Track|Active/i', $label) === 1 => 'b-green',
    preg_match('/Blocked|Critical|Overdue|Revision|Delayed|At Risk/i', $label) === 1 => 'b-red',
    preg_match('/Waiting|Negotiation|Partially|Needs Attention/i', $label) === 1 => 'b-amber',
    preg_match('/In Progress|Submitted|Ready|Transit|Artwork|Shipment|Invoice/i', $label) === 1 => 'b-blue',
    default => 'b-gray',
};
?>
<span <?php echo e($attributes->class(['badge', $class])); ?>><?php echo e($label); ?></span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/badge.blade.php ENDPATH**/ ?>