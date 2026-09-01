<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'row',
    'canManage' => false,
    'canEditSupplier' => false,
    'selectedIds' => [],
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
    'selectedIds' => [],
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
    $action = $row['action'] ?? ['type' => 'disabled', 'label' => 'Unavailable', 'tone' => 'neutral', 'disabled' => true];
    $email = trim((string) ($row['email'] ?? ''));
    $selectedSupplierIds = collect($selectedIds)->map(fn ($id) => (int) $id);
    $checked = $selectedSupplierIds->contains($supplierId);
?>

<tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rfq-management-row-'.e($supplierId).''; ?>wire:key="rfq-management-row-<?php echo e($supplierId); ?>">
    <td class="ft-rfq-checkbox-col" data-label="Select">
        <input
            type="checkbox"
            class="ft-rfq-row-checkbox"
            wire:model.live="rfqSelectedSupplierIds"
            value="<?php echo e($supplierId); ?>"
            <?php if(! ($row['selectable'] ?? false)): echo 'disabled'; endif; ?>
            aria-label="Select <?php echo e($row['supplier_name'] ?? 'supplier'); ?>"
            <?php if($checked): echo 'checked'; endif; ?>
        >
    </td>

    <td data-label="Supplier">
        <div class="ft-rfq-management-supplier">
            <span class="ft-rfq-management-avatar"><?php echo e($row['initials'] ?? '—'); ?></span>
            <strong title="<?php echo e($row['supplier_name'] ?? 'Supplier'); ?>"><?php echo e($row['supplier_name'] ?? 'Supplier'); ?></strong>
        </div>
    </td>

    <td data-label="Email">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($email !== ''): ?>
            <span class="ft-rfq-management-email" title="<?php echo e($email); ?>"><?php echo e($email); ?></span>
        <?php else: ?>
            <span class="ft-rfq-management-email is-missing">No email configured</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>

    <td class="ft-rfq-email-status-col" data-label="Email status">
        <div class="ft-rfq-management-status-stack">
            <span class="ft-rfq-management-status is-<?php echo e($row['email_tone'] ?? 'neutral'); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($row['email_status_key'] ?? ''):
                    case ('sent'): ?>
                    <?php case ('ready'): ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><path d="m8.5 12 2.3 2.3 4.7-5"></path></svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                    <?php case ('failed'): ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><path d="M12 8.5v4.5M12 16h.01"></path></svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                    <?php case ('missing'): ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5 21 19H3z"></path><path d="M12 9v4M12 16h.01"></path></svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                    <?php case ('queued'): ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><path d="M12 7.5V12l3 2"></path></svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span><?php echo e($row['email_status_label'] ?? '—'); ?></span>
            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($row['email_status_detail'] ?? null)): ?>
                <small class="is-danger"><?php echo e($row['email_status_detail']); ?></small>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </td>

    <td data-label="RFQ status"><span class="ft-rfq-management-rfq-status"><?php echo e($row['rfq_status_label'] ?? '—'); ?></span></td>

    <td data-label="Last activity">
        <span class="ft-rfq-management-last-activity">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['last_activity_at'] ?? null): ?>
                <?php echo e(\App\Support\UserLocalTime::format($row['last_activity_at'], 'M j, Y')); ?> · <?php echo e(\App\Support\UserLocalTime::format($row['last_activity_at'], 'g:i A')); ?>

            <?php else: ?>
                —
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
    </td>

    <td class="ft-rfq-actions-col" data-label="Actions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($action['type'] ?? '') === 'view_response'): ?>
            <button type="button" class="ft-rfq-row-action is-success" wire:click="setDetailTab('comparison')">View response</button>
        <?php elseif(($action['type'] ?? '') === 'setup_email'): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditSupplier): ?>
                <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'edit_supplier' => $supplierId])); ?>" wire:navigate class="ft-rfq-row-action is-warning">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3"></circle><path d="M3.5 19c.5-3.1 2.4-4.7 5.5-4.7 2.2 0 3.8.8 4.7 2.3M17 13v6M14 16h6"></path></svg>
                    <span>Set up email</span>
                </a>
            <?php else: ?>
                <button type="button" class="ft-rfq-row-action is-warning" disabled>Set up email</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php elseif(($action['type'] ?? '') === 'send' && $canManage): ?>
            <button
                type="button"
                class="ft-rfq-row-action is-<?php echo e($action['tone'] ?? 'success'); ?>"
                wire:click="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)"
                wire:loading.attr="disabled"
                wire:target="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)"
            >
                <span wire:loading.remove wire:target="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)" class="ft-rfq-row-action-content">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($action['label'] ?? '') === 'Retry'): ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18.5 8.5A7 7 0 1 0 19 15"></path><path d="M18.5 4v4.5H14"></path></svg>
                    <?php elseif(($action['label'] ?? '') === 'Resend'): ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18.5 8.5A7 7 0 1 0 19 15"></path><path d="M18.5 4v4.5H14"></path></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect><path d="m5 7 7 5 7-5"></path></svg>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span><?php echo e($action['label'] ?? 'Send email'); ?></span>
                </span>
                <span wire:loading wire:target="sendRfqSupplierEmail(<?php echo e($supplierId); ?>)" class="ft-rfq-row-action-content"><span class="ft-rfq-inline-spinner" aria-hidden="true"></span><span>Sending…</span></span>
            </button>
        <?php else: ?>
            <button type="button" class="ft-rfq-row-action is-neutral" disabled>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($row['email_status_key'] ?? '') === 'queued'): ?><span class="ft-rfq-inline-spinner" aria-hidden="true"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span><?php echo e($action['label'] ?? 'Unavailable'); ?></span>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-management-row.blade.php ENDPATH**/ ?>