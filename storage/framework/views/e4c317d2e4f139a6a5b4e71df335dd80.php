<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job','summary'=>[],'redoContext'=>[],'contacts'=>collect(),'users'=>collect(),
    'invoiceTypes'=>collect(),'currencies'=>collect(),'paymentTerms'=>collect(),'paymentMethods'=>collect(),'receivedAccounts'=>collect(),
    'canCreate'=>false,'canEdit'=>false,
    'showCreateInvoiceModal'=>false,'invoiceType'=>'Final invoice','invoiceCurrency'=>'USD','invoiceIssueDate'=>'','invoicePaymentTerms'=>'Net 15 days','invoiceDueDate'=>'','invoiceBillingContactId'=>null,'invoiceLineItems'=>[],'invoicePurchaseOrderReference'=>'','invoiceNotes'=>'','invoiceTaxRate'=>'0','invoiceSupportingDocument'=>null,'invoiceEmailAfterCreation'=>false,
    'showRecordPaymentModal'=>false,'paymentInvoiceId'=>null,'paymentDate'=>'','paymentMethod'=>'Bank transfer','paymentAmount'=>'','paymentReference'=>'','paymentNotes'=>'','paymentReceipt'=>null,
    'showCollectionUpdateModal'=>false,'collectionOwnerId'=>null,'collectionFollowUpDate'=>'','collectionNextFollowUpDate'=>'','collectionNote'=>'',
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
    'job','summary'=>[],'redoContext'=>[],'contacts'=>collect(),'users'=>collect(),
    'invoiceTypes'=>collect(),'currencies'=>collect(),'paymentTerms'=>collect(),'paymentMethods'=>collect(),'receivedAccounts'=>collect(),
    'canCreate'=>false,'canEdit'=>false,
    'showCreateInvoiceModal'=>false,'invoiceType'=>'Final invoice','invoiceCurrency'=>'USD','invoiceIssueDate'=>'','invoicePaymentTerms'=>'Net 15 days','invoiceDueDate'=>'','invoiceBillingContactId'=>null,'invoiceLineItems'=>[],'invoicePurchaseOrderReference'=>'','invoiceNotes'=>'','invoiceTaxRate'=>'0','invoiceSupportingDocument'=>null,'invoiceEmailAfterCreation'=>false,
    'showRecordPaymentModal'=>false,'paymentInvoiceId'=>null,'paymentDate'=>'','paymentMethod'=>'Bank transfer','paymentAmount'=>'','paymentReference'=>'','paymentNotes'=>'','paymentReceipt'=>null,
    'showCollectionUpdateModal'=>false,'collectionOwnerId'=>null,'collectionFollowUpDate'=>'','collectionNextFollowUpDate'=>'','collectionNote'=>'',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $currency = strtoupper((string)($job->currency ?: 'USD'));
    $money = fn($amount, $code = null) => (($code ?: $currency) === 'USD' ? '$' : ($code ?: $currency).' ').number_format((float)$amount, 2);
    $collection = $job->collection;
    $overdueDays = (int)($summary['overdue_days'] ?? 0);
?>
<section class="ft-order-finance">
    <div class="ft-finance-title-row">
        <div><h2>Invoices &amp; Payments</h2><p>Track billing, collections and outstanding balances for this order.</p></div>
    </div>

    <div class="ft-finance-metrics">
        <?php if (isset($component)) { $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.metric-card','data' => ['label' => 'Order Value','value' => $money($summary['order_value'] ?? 0),'icon' => 'document','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Order Value','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($money($summary['order_value'] ?? 0)),'icon' => 'document','tone' => 'blue']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $attributes = $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $component = $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.metric-card','data' => ['label' => 'Total Invoiced','value' => $money($summary['total_invoiced'] ?? 0),'icon' => 'money','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Invoiced','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($money($summary['total_invoiced'] ?? 0)),'icon' => 'money','tone' => 'blue']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $attributes = $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $component = $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.metric-card','data' => ['label' => 'Total Collected','value' => $money($summary['total_collected'] ?? 0),'subline' => number_format((float)($summary['collection_pct'] ?? 0),1).'% collected','icon' => 'collect','tone' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Collected','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($money($summary['total_collected'] ?? 0)),'subline' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((float)($summary['collection_pct'] ?? 0),1).'% collected'),'icon' => 'collect','tone' => 'green']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $attributes = $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $component = $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.metric-card','data' => ['label' => 'Outstanding','value' => $money($summary['outstanding'] ?? 0),'icon' => 'outstanding','tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Outstanding','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($money($summary['outstanding'] ?? 0)),'icon' => 'outstanding','tone' => 'blue']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $attributes = $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $component = $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.metric-card','data' => ['label' => 'Overdue','value' => $money($summary['overdue'] ?? 0),'icon' => 'warning','tone' => 'red','danger' => (float)($summary['overdue'] ?? 0) > 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Overdue','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($money($summary['overdue'] ?? 0)),'icon' => 'warning','tone' => 'red','danger' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((float)($summary['overdue'] ?? 0) > 0)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $attributes = $__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__attributesOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc)): ?>
<?php $component = $__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc; ?>
<?php unset($__componentOriginala9b415a58b93e033eed6c6e8b7e09cdc); ?>
<?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginalb908e79c53df8713122aab976cd82bb9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb908e79c53df8713122aab976cd82bb9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.redo-finance','data' => ['job' => $job,'context' => $redoContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.redo-finance'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($redoContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb908e79c53df8713122aab976cd82bb9)): ?>
<?php $attributes = $__attributesOriginalb908e79c53df8713122aab976cd82bb9; ?>
<?php unset($__attributesOriginalb908e79c53df8713122aab976cd82bb9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb908e79c53df8713122aab976cd82bb9)): ?>
<?php $component = $__componentOriginalb908e79c53df8713122aab976cd82bb9; ?>
<?php unset($__componentOriginalb908e79c53df8713122aab976cd82bb9); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float)($summary['overdue'] ?? 0) > 0): ?>
        <div class="ft-finance-overdue">
            <span class="ft-overdue-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 2.7 20h18.6z"></path><path d="M12 8v5.5M12 17h.01"></path></svg></span>
            <div><strong>Payment overdue</strong><p><?php echo e($money($summary['overdue'])); ?> has been overdue<?php echo e($overdueDays ? ' for '.$overdueDays.' day'.($overdueDays===1?'':'s') : ''); ?>.</p></div>
            <div class="ft-overdue-meta"><span>Collection owner: <b><?php echo e($collection?->owner?->name ?: 'Unassigned'); ?></b></span><i>•</i><span>Next follow-up: <b><?php echo e($collection?->next_follow_up_at ? \App\Support\UserLocalTime::format($collection->next_follow_up_at, 'M j, Y') : 'Not set'); ?></b></span></div>
            <div class="ft-overdue-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" class="reminder" wire:click="sendPaymentReminder" wire:loading.attr="disabled" wire:target="sendPaymentReminder">Send reminder</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" class="update" wire:click="openCollectionUpdate">Add collection update</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php elseif($canEdit && $job->invoices->isNotEmpty()): ?>
        <div class="ft-finance-healthy"><span>✓</span><div><strong>No overdue balance</strong><p>All issued invoices are currently within terms or fully paid.</p></div><button type="button" wire:click="openCollectionUpdate">Add collection update</button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['collectionForm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-finance-form-alert ft-finance-page-alert"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-finance-table-card">
        <h3>Invoices</h3>
        <div class="ft-finance-table-wrap">
            <table class="ft-finance-table">
                <thead><tr><th>Invoice</th><th>Type</th><th>Issue date</th><th>Due date</th><th>Amount</th><th>Collected</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $job->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php ($collected = $invoice->collectedAmount()); ?>
                    <?php ($balance = $invoice->balanceAmount()); ?>
                    <tr>
                        <td><strong class="ft-finance-link"><?php echo e($invoice->invoice_number); ?></strong></td>
                        <td><?php echo e(str_replace(' invoice','',$invoice->type)); ?></td>
                        <td><?php echo e(\App\Support\UserLocalTime::format($invoice->issue_date, 'M j, Y')); ?></td>
                        <td><?php echo e(\App\Support\UserLocalTime::format($invoice->due_date, 'M j, Y')); ?></td>
                        <td><?php echo e($money($invoice->total, $invoice->currency)); ?></td>
                        <td><?php echo e($money($collected, $invoice->currency)); ?></td>
                        <td><?php echo e($money($balance, $invoice->currency)); ?></td>
                        <td><?php if (isset($component)) { $__componentOriginal2ad6b802272f2ec34fcb226aa3a92f08 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ad6b802272f2ec34fcb226aa3a92f08 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.status','data' => ['status' => $invoice->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->status)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ad6b802272f2ec34fcb226aa3a92f08)): ?>
<?php $attributes = $__attributesOriginal2ad6b802272f2ec34fcb226aa3a92f08; ?>
<?php unset($__attributesOriginal2ad6b802272f2ec34fcb226aa3a92f08); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ad6b802272f2ec34fcb226aa3a92f08)): ?>
<?php $component = $__componentOriginal2ad6b802272f2ec34fcb226aa3a92f08; ?>
<?php unset($__componentOriginal2ad6b802272f2ec34fcb226aa3a92f08); ?>
<?php endif; ?></td>
                        <td><button type="button" class="ft-finance-kebab" aria-label="Invoice actions">•••</button></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->pdf_path): ?>
                        <?php if (isset($component)) { $__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.attachment-row','data' => ['colspan' => 9,'name' => $invoice->pdf_name ?: $invoice->invoice_number.'.pdf','meta' => 'Generated invoice · '.($invoice->creator?->name ?: 'System').' · '.($invoice->pdf_generated_at ? \App\Support\UserLocalTime::format($invoice->pdf_generated_at, 'M j, Y, g:i A') : \App\Support\UserLocalTime::format($invoice->created_at, 'M j, Y, g:i A')),'openUrl' => route('invoices.pdf.open', $invoice),'downloadUrl' => route('invoices.pdf.download', $invoice)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.attachment-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 9,'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->pdf_name ?: $invoice->invoice_number.'.pdf'),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Generated invoice · '.($invoice->creator?->name ?: 'System').' · '.($invoice->pdf_generated_at ? \App\Support\UserLocalTime::format($invoice->pdf_generated_at, 'M j, Y, g:i A') : \App\Support\UserLocalTime::format($invoice->created_at, 'M j, Y, g:i A'))),'open-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.pdf.open', $invoice)),'download-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.pdf.download', $invoice))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07)): ?>
<?php $attributes = $__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07; ?>
<?php unset($__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07)): ?>
<?php $component = $__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07; ?>
<?php unset($__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->supporting_document_path): ?>
                        <?php if (isset($component)) { $__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.attachment-row','data' => ['colspan' => 9,'name' => $invoice->supporting_document_name ?: basename($invoice->supporting_document_path),'meta' => 'Invoice supporting document · '.($invoice->creator?->name ?: 'System').' · '.\App\Support\UserLocalTime::format($invoice->created_at, 'M j, Y, g:i A'),'openUrl' => route('invoices.attachment.open', $invoice),'downloadUrl' => route('invoices.attachment.download', $invoice)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.attachment-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 9,'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->supporting_document_name ?: basename($invoice->supporting_document_path)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Invoice supporting document · '.($invoice->creator?->name ?: 'System').' · '.\App\Support\UserLocalTime::format($invoice->created_at, 'M j, Y, g:i A')),'open-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.attachment.open', $invoice)),'download-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.attachment.download', $invoice))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07)): ?>
<?php $attributes = $__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07; ?>
<?php unset($__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07)): ?>
<?php $component = $__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07; ?>
<?php unset($__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="9"><div class="ft-finance-empty"><strong>No invoices yet</strong><span>Create the first invoice for this order when billing is ready.</span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreate): ?><button type="button" wire:click="openCreateInvoice">Create invoice</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="ft-finance-bottom-grid">
        <section class="ft-finance-table-card ft-payment-history">
            <h3>Payment history</h3>
            <div class="ft-finance-table-wrap">
                <table class="ft-finance-table">
                    <thead><tr><th>Payment</th><th>Date</th><th>Method</th><th>Amount</th><th>Applied to</th><th>Recorded by</th></tr></thead>
                    <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $job->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr><td><strong class="ft-finance-link"><?php echo e($payment->payment_number); ?></strong></td><td><?php echo e(\App\Support\UserLocalTime::format($payment->payment_date, 'M j, Y')); ?></td><td><?php echo e($payment->method); ?></td><td><?php echo e($money($payment->amount, $payment->invoice?->currency ?: $currency)); ?></td><td><strong class="ft-finance-link"><?php echo e($payment->invoice?->invoice_number ?: '—'); ?></strong></td><td><?php echo e($payment->recorder?->name ?: 'System'); ?></td></tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->receipt_path): ?>
                            <?php if (isset($component)) { $__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.attachment-row','data' => ['colspan' => 6,'name' => $payment->receipt_name ?: basename($payment->receipt_path),'meta' => 'Payment receipt / bank advice · '.($payment->recorder?->name ?: 'System').' · '.\App\Support\UserLocalTime::format($payment->created_at, 'M j, Y, g:i A'),'openUrl' => route('payments.receipt.open', $payment),'downloadUrl' => route('payments.receipt.download', $payment)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.attachment-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 6,'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payment->receipt_name ?: basename($payment->receipt_path)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Payment receipt / bank advice · '.($payment->recorder?->name ?: 'System').' · '.\App\Support\UserLocalTime::format($payment->created_at, 'M j, Y, g:i A')),'open-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('payments.receipt.open', $payment)),'download-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('payments.receipt.download', $payment))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07)): ?>
<?php $attributes = $__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07; ?>
<?php unset($__attributesOriginal438bf9a580612c2b087ab8e8f3c0fd07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07)): ?>
<?php $component = $__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07; ?>
<?php unset($__componentOriginal438bf9a580612c2b087ab8e8f3c0fd07); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="6"><div class="ft-finance-empty compact"><span>No payments have been recorded for this order.</span></div></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="ft-finance-collection-card">
            <h3>Collection details</h3>
            <dl>
                <div><dt>Collection owner</dt><dd><?php echo e($collection?->owner?->name ?: 'Unassigned'); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="openCollectionUpdate" aria-label="Edit collection owner">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></dd></div>
                <div><dt>Last follow-up</dt><dd><?php echo e($collection?->last_follow_up_at ? \App\Support\UserLocalTime::format($collection->last_follow_up_at, 'M j, Y') : '—'); ?></dd></div>
                <div><dt>Next follow-up</dt><dd><?php echo e($collection?->next_follow_up_at ? \App\Support\UserLocalTime::format($collection->next_follow_up_at, 'M j, Y') : '—'); ?></dd></div>
                <div><dt>Latest note</dt><dd><?php echo e($collection?->latest_note ?: 'No collection note yet.'); ?></dd></div>
            </dl>
            <div class="ft-collection-footer">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($collection?->updates?->isNotEmpty()): ?>
                    <details><summary>View collection history</summary><div class="ft-collection-history"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $collection->updates->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $update): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div><b><?php echo e($update->actor?->name ?: 'System'); ?></b><span><?php echo e($update->note); ?></span><small><?php echo e($update->created_at ? \App\Support\UserLocalTime::format($update->created_at, 'M j, Y · g:i A') : ''); ?></small></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div></details>
                <?php else: ?><span></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="openCollectionUpdate">Add update</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreateInvoiceModal): ?>
        <?php if (isset($component)) { $__componentOriginal33fc4f9daf1150c81db90485667b777e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33fc4f9daf1150c81db90485667b777e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.create-invoice-modal','data' => ['job' => $job,'contacts' => $contacts,'summary' => $summary,'invoiceTypes' => $invoiceTypes,'currencies' => $currencies,'paymentTerms' => $paymentTerms,'invoiceLineItems' => $invoiceLineItems,'invoiceTaxRate' => $invoiceTaxRate,'invoiceCurrency' => $invoiceCurrency,'invoiceType' => $invoiceType,'invoiceSupportingDocument' => $invoiceSupportingDocument]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.create-invoice-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'contacts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contacts),'summary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($summary),'invoice-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceTypes),'currencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currencies),'payment-terms' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentTerms),'invoice-line-items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceLineItems),'invoice-tax-rate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceTaxRate),'invoice-currency' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceCurrency),'invoice-type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceType),'invoice-supporting-document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceSupportingDocument)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33fc4f9daf1150c81db90485667b777e)): ?>
<?php $attributes = $__attributesOriginal33fc4f9daf1150c81db90485667b777e; ?>
<?php unset($__attributesOriginal33fc4f9daf1150c81db90485667b777e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33fc4f9daf1150c81db90485667b777e)): ?>
<?php $component = $__componentOriginal33fc4f9daf1150c81db90485667b777e; ?>
<?php unset($__componentOriginal33fc4f9daf1150c81db90485667b777e); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRecordPaymentModal): ?>
        <?php if (isset($component)) { $__componentOriginala4c493cc0940c93610705d1a0f4a5ee1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala4c493cc0940c93610705d1a0f4a5ee1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.payment-modal','data' => ['job' => $job,'paymentMethods' => $paymentMethods,'receivedAccounts' => $receivedAccounts,'paymentInvoiceId' => $paymentInvoiceId,'paymentAmount' => $paymentAmount,'paymentReceipt' => $paymentReceipt]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.payment-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'payment-methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentMethods),'received-accounts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($receivedAccounts),'payment-invoice-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentInvoiceId),'payment-amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentAmount),'payment-receipt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentReceipt)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala4c493cc0940c93610705d1a0f4a5ee1)): ?>
<?php $attributes = $__attributesOriginala4c493cc0940c93610705d1a0f4a5ee1; ?>
<?php unset($__attributesOriginala4c493cc0940c93610705d1a0f4a5ee1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala4c493cc0940c93610705d1a0f4a5ee1)): ?>
<?php $component = $__componentOriginala4c493cc0940c93610705d1a0f4a5ee1; ?>
<?php unset($__componentOriginala4c493cc0940c93610705d1a0f4a5ee1); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCollectionUpdateModal): ?>
        <?php if (isset($component)) { $__componentOriginalc2bda01050c08ad2038529d4bd1b6216 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2bda01050c08ad2038529d4bd1b6216 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.collection-update-modal','data' => ['users' => $users]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.collection-update-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2bda01050c08ad2038529d4bd1b6216)): ?>
<?php $attributes = $__attributesOriginalc2bda01050c08ad2038529d4bd1b6216; ?>
<?php unset($__attributesOriginalc2bda01050c08ad2038529d4bd1b6216); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2bda01050c08ad2038529d4bd1b6216)): ?>
<?php $component = $__componentOriginalc2bda01050c08ad2038529d4bd1b6216; ?>
<?php unset($__componentOriginalc2bda01050c08ad2038529d4bd1b6216); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/finance/detail.blade.php ENDPATH**/ ?>