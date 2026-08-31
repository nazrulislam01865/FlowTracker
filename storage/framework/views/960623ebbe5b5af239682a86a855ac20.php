<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'supplier' => null,
    'name' => null,
    'meta' => null,
    'fallback' => 'No default supplier',
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
    'supplier' => null,
    'name' => null,
    'meta' => null,
    'fallback' => 'No default supplier',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $supplierName = trim((string) ($supplier?->name ?? $name ?? ''));
    $initials = $supplierName !== ''
        ? collect(preg_split('/\s+/', $supplierName) ?: [])
            ->filter()
            ->map(fn ($word) => mb_strtoupper(mb_substr((string) $word, 0, 1)))
            ->take(2)
            ->implode('')
        : '—';
?>

<div class="ft-detail-product-supplier <?php echo e($supplierName === '' ? 'is-empty' : ''); ?>">
    <span class="ft-detail-product-supplier__badge" aria-hidden="true"><?php echo e($initials ?: '—'); ?></span>
    <span class="ft-detail-product-supplier__copy">
        <strong><?php echo e($supplierName !== '' ? $supplierName : $fallback); ?></strong>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($meta)): ?>
            <span><?php echo e($meta); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-product-supplier.blade.php ENDPATH**/ ?>