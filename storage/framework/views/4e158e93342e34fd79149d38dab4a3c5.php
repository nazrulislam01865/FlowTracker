<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'supplier',
    'selected' => false,
    'model' => 'createRfqSupplierIds',
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
    'selected' => false,
    'model' => 'createRfqSupplierIds',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $supplierId = (int) ($supplier['id'] ?? 0);
    $parts = preg_split('/\s+/u', trim((string) ($supplier['name'] ?? ''))) ?: [];
    $initials = strtoupper(mb_substr(implode('', array_map(fn ($part) => mb_substr($part, 0, 1), $parts)), 0, 2)) ?: '—';
    $badge = trim((string) ($supplier['badge'] ?? ''));
    $badgeTone = (string) ($supplier['badge_tone'] ?? '');
    $invitable = (bool) ($supplier['invitable'] ?? true);
    $emailReady = (bool) ($supplier['email_ready'] ?? filter_var((string) ($supplier['email'] ?? ''), FILTER_VALIDATE_EMAIL));
    $unavailableReason = trim((string) ($supplier['unavailable_reason'] ?? ''));
    $email = trim((string) ($supplier['email'] ?? ''));
?>

<label <?php echo e($attributes->class(['ft-create-rfq-supplier', 'is-selected' => $selected, 'is-unavailable' => ! $invitable])); ?> <?php if(! $invitable): ?> aria-disabled="true" <?php endif; ?>>
    <input
        type="checkbox"
        value="<?php echo e($supplierId); ?>"
        wire:model.live="<?php echo e($model); ?>"
        aria-label="Select <?php echo e($supplier['name']); ?> for RFQ"
        <?php if(! $invitable): echo 'disabled'; endif; ?>
    >
    <span class="ft-create-rfq-check" aria-hidden="true">
        <svg viewBox="0 0 16 16" fill="none"><path d="m3.3 8.1 2.7 2.7 6-6"></path></svg>
    </span>
    <span class="ft-create-rfq-avatar"><?php echo e($initials); ?></span>
    <span class="ft-create-rfq-supplier-copy">
        <strong><?php echo e($supplier['name']); ?></strong>
        <small><?php echo e($supplier['category']); ?> · <?php echo e($email !== '' ? $email : 'No email configured'); ?></small>
    </span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $invitable && $unavailableReason !== ''): ?>
        <span class="ft-create-rfq-badge is-blue"><?php echo e($unavailableReason); ?></span>
    <?php elseif(! $emailReady): ?>
        <span class="ft-create-rfq-badge is-blue">No email configured</span>
    <?php elseif($badge !== ''): ?>
        <span class="ft-create-rfq-badge <?php echo e($badgeTone === 'green' ? 'is-green' : 'is-blue'); ?>"><?php echo e($badge); ?></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</label>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-supplier-choice.blade.php ENDPATH**/ ?>