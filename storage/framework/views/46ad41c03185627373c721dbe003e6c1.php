<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'detailTab', 'canViewFinance' => false, 'canCreateFinance' => false, 'redoContext' => []]));

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

foreach (array_filter((['job', 'detailTab', 'canViewFinance' => false, 'canCreateFinance' => false, 'redoContext' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<nav class="page-tabs ft-order-prototype-tabs" aria-label="Order detail tabs">
    <button type="button" class="page-tab <?php echo e($detailTab === 'overview' ? 'active' : ''); ?>" wire:click="setDetailTab('overview')">Overview</button>
    <button type="button" class="page-tab <?php echo e($detailTab === 'inquiry' ? 'active' : ''); ?>" wire:click="setDetailTab('inquiry')">Inquiry &nbsp;<span class="status-pill"><?php echo e($job->source_inquiry_id ? 1 : 0); ?></span></button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewFinance): ?>
        <button type="button" class="page-tab <?php echo e($detailTab === 'finance' ? 'active' : ''); ?>" wire:click="setDetailTab('finance')">Invoices &amp; Payments</button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($redoContext['hasRedo'] ?? false)): ?>
        <button type="button" class="page-tab <?php echo e($detailTab === 'redo' ? 'active' : ''); ?>" wire:click="setDetailTab('redo')">Redo <span class="ft-redo-tab-count"><?php echo e((int) ($redoContext['redoCount'] ?? 0)); ?></span></button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</nav>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab === 'finance' && $canCreateFinance): ?>
    <div class="finance-tab-actions">
        <button type="button" class="btn" wire:click="openRecordPayment">Record Payment</button>
        <button type="button" class="btn primary" wire:click="openCreateInvoice">＋ Create Invoice</button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/tabs.blade.php ENDPATH**/ ?>