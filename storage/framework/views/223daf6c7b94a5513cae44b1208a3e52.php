<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'row',
    'canManage' => false,
    'canEditSupplier' => false,
    'selectedKeys' => [],
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
    'row',
    'canManage' => false,
    'canEditSupplier' => false,
    'selectedKeys' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $supplierId = (int) ($row['supplier_id'] ?? 0);
    $selectionKey = (string) ($row['selection_key'] ?? '');
    $action = $row['action'] ?? ['type' => 'disabled', 'label' => 'Unavailable', 'tone' => 'neutral'];
    $email = trim((string) ($row['email'] ?? ''));
    $checked = collect($selectedKeys)->map(fn ($key) => (string) $key)->contains($selectionKey);
?>

<tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rfq-product-supplier-'.e($selectionKey).''; ?>wire:key="rfq-product-supplier-<?php echo e($selectionKey); ?>">
    <td class="ft-rfq-px-check-col">
        <input
            type="checkbox"
            class="ft-rfq-px-checkbox"
            wire:model.live="rfqSelectedProductSupplierKeys"
            value="<?php echo e($selectionKey); ?>"
            <?php if(! ($row['selectable'] ?? false)): echo 'disabled'; endif; ?>
            <?php if($checked): echo 'checked'; endif; ?>
            aria-label="Select <?php echo e($row['supplier_name'] ?? 'supplier'); ?> for this product"
        >
    </td>
    <td data-label="Supplier">
        <strong class="ft-rfq-px-supplier-name"><?php echo e($row['supplier_name'] ?? 'Supplier'); ?></strong>
    </td>
    <td data-label="Email">
        <span class="ft-rfq-px-email <?php echo e($email === '' ? 'is-missing' : ''); ?>"><?php echo e($email !== '' ? $email : 'No email configured'); ?></span>
    </td>
    <td data-label="Invitation status">
        <div class="ft-rfq-px-status-stack">
            <span class="ft-rfq-px-status is-<?php echo e($row['status_tone'] ?? 'neutral'); ?>"><?php echo e($row['status_label'] ?? '—'); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($row['status_key'] ?? '') !== 'failed' && filled($row['status_detail'] ?? null)): ?>
                <small><?php echo e($row['status_detail']); ?></small>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </td>
    <td data-label="Quotation">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['quotation_received'] ?? false): ?>
            <span class="ft-rfq-px-quotation is-received">Received</span>
        <?php else: ?>
            <span class="ft-rfq-px-dash">—</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
    <td data-label="Last activity">
        <span class="ft-rfq-px-last-activity">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['last_activity_at'] ?? null): ?>
                <?php echo e(\App\Support\UserLocalTime::format($row['last_activity_at'], 'M j, Y')); ?> · <?php echo e(\App\Support\UserLocalTime::format($row['last_activity_at'], 'g:i A')); ?>

            <?php else: ?>
                —
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
    </td>
    <td class="ft-rfq-px-action-col" data-label="Actions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($action['type'] ?? '') === 'setup_email'): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditSupplier): ?>
                <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'edit_supplier' => $supplierId])); ?>" wire:navigate class="ft-rfq-px-row-action is-warning">Set up email</a>
            <?php else: ?>
                <button type="button" class="ft-rfq-px-row-action is-warning" disabled>Set up email</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php elseif(($action['type'] ?? '') === 'send' && $canManage): ?>
            <button
                type="button"
                class="ft-rfq-px-row-action is-<?php echo e($action['tone'] ?? 'success'); ?>"
                wire:click="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)"
                wire:loading.attr="disabled"
                wire:target="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)"
            >
                <span wire:loading.remove wire:target="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)"><?php echo e($action['label'] ?? 'Send invitation'); ?></span>
                <span wire:loading wire:target="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)">Sending…</span>
            </button>
        <?php else: ?>
            <button type="button" class="ft-rfq-px-row-action is-neutral" disabled><?php echo e($action['label'] ?? 'Unavailable'); ?></button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-product-supplier-row.blade.php ENDPATH**/ ?>