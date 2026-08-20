<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'variant' => null,
    'dynamicColor' => null,
    'dot' => false,
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
    'variant' => null,
    'dynamicColor' => null,
    'dot' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<span
    <?php echo e($attributes->class([
        'ft-badge',
        'ft-badge--neutral' => $variant === 'neutral',
        'ft-badge--info' => $variant === 'info' || ($variant === null && preg_match('/In Progress|Submitted|Ready|Transit|Artwork|Shipment|Invoice/i', (string) $label) === 1),
        'ft-badge--success' => $variant === 'success' || ($variant === null && preg_match('/Completed|Approved|Paid|Delivered|On Track|Active/i', (string) $label) === 1),
        'ft-badge--warning' => $variant === 'warning' || ($variant === null && preg_match('/Waiting|Negotiation|Partially|Needs Attention/i', (string) $label) === 1),
        'ft-badge--danger' => $variant === 'danger' || ($variant === null && preg_match('/Blocked|Critical|Overdue|Revision|Delayed|At Risk/i', (string) $label) === 1),
        'ft-badge--purple' => $variant === 'purple',
        'ft-badge--dynamic' => filled($dynamicColor),
        'badge' => $variant === null,
        'b-green' => $variant === null && preg_match('/Completed|Approved|Paid|Delivered|On Track|Active/i', (string) $label) === 1,
        'b-red' => $variant === null && preg_match('/Blocked|Critical|Overdue|Revision|Delayed|At Risk/i', (string) $label) === 1,
        'b-amber' => $variant === null && preg_match('/Waiting|Negotiation|Partially|Needs Attention/i', (string) $label) === 1,
        'b-blue' => $variant === null && preg_match('/In Progress|Submitted|Ready|Transit|Artwork|Shipment|Invoice/i', (string) $label) === 1,
        'b-gray' => $variant === null && preg_match('/Completed|Approved|Paid|Delivered|On Track|Active|Blocked|Critical|Overdue|Revision|Delayed|At Risk|Waiting|Negotiation|Partially|Needs Attention|In Progress|Submitted|Ready|Transit|Artwork|Shipment|Invoice/i', (string) $label) !== 1,
    ])->merge(filled($dynamicColor) ? ['style' => \App\Support\MasterColor::style($dynamicColor)] : [])); ?>

    <?php if($variant !== null || filled($dynamicColor)): ?> data-ft-ui-component="badge" <?php endif; ?>
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dot): ?><span class="ft-badge__dot" aria-hidden="true"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($label ?? $slot); ?>

</span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/badge.blade.php ENDPATH**/ ?>