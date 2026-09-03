<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'supplier',
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
    'supplier',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $supplierId = (int) data_get($supplier, 'id');
    $name = trim((string) data_get($supplier, 'name')) ?: 'Supplier';
    $email = trim((string) data_get($supplier, 'email'));
    $contact = trim((string) data_get($supplier, 'contact'));
    $code = trim((string) data_get($supplier, 'code'));
    $parts = preg_split('/\s+/u', $name) ?: [];
    $initials = strtoupper(mb_substr(implode('', array_map(fn ($part) => mb_substr($part, 0, 1), $parts)), 0, 2)) ?: 'S';
    $secondary = collect([
        $contact !== '' ? $contact : null,
        $email !== '' ? $email : 'No email configured',
        $code !== '' ? 'Code '.$code : null,
    ])->filter()->implode(' · ');
?>

<article <?php echo e($attributes->class(['ft-create-rfq-selected-card'])); ?>>
    <span class="ft-create-rfq-selected-avatar" aria-hidden="true"><?php echo e($initials); ?></span>

    <span class="ft-create-rfq-selected-copy">
        <strong title="<?php echo e($name); ?>"><?php echo e($name); ?></strong>
        <small title="<?php echo e($secondary); ?>"><?php echo e($secondary); ?></small>
    </span>

    <span class="ft-create-rfq-selected-status">
        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3.3 8.1 2.7 2.7 6-6"></path></svg>
        Selected
    </span>

    <button
        type="button"
        class="ft-create-rfq-selected-remove"
        wire:click="removeCreateRfqSupplier(<?php echo e($supplierId); ?>)"
        wire:loading.attr="disabled"
        wire:target="removeCreateRfqSupplier(<?php echo e($supplierId); ?>)"
        aria-label="Remove <?php echo e($name); ?> from RFQ"
    >
        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 6h12M8 6V4h4v2M7 8.5v6M10 8.5v6M13 8.5v6M6 6l.6 10h6.8L14 6"></path>
        </svg>
        <span wire:loading.remove wire:target="removeCreateRfqSupplier(<?php echo e($supplierId); ?>)">Remove</span>
        <span wire:loading wire:target="removeCreateRfqSupplier(<?php echo e($supplierId); ?>)">Removing…</span>
    </button>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-selected-supplier-card.blade.php ENDPATH**/ ?>