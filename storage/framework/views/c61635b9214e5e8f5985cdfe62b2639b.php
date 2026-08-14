<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'overviewTaskDocumentModalTask'=>null,
    'overviewTaskAvailableDocuments'=>collect(),
    'showOverviewTaskDocumentModal'=>false,
    'showAddOrderTaskForm'=>false,
    'newOrderTaskAssigneeId'=>null,
    'overviewTaskDocumentSource'=>'upload',
    'overviewTaskDocumentUpload'=>null,
    'overviewTaskExistingDocumentId'=>null,
    'overviewTaskLinkFormTaskId'=>null,
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
    'inquiryResults'=>collect(),
    'inquirySearch'=>'',
    'selectedLinkInquiry'=>null,
    'showInquiryLinkConfirm'=>false,
    'showInquiryUnlinkConfirm'=>false,
    'canManageInquiryLink'=>false,
    'linkedInquiryCanOpen'=>false,
    'financeSummary'=>null,
    'financeContacts'=>null,
    'financeUsers'=>null,
    'canCreateFinance'=>false,
    'canEditFinance'=>false,
    'showCreateInvoiceModal'=>false,
    'invoiceType'=>'Final invoice',
    'invoiceCurrency'=>'USD',
    'invoiceIssueDate'=>'',
    'invoicePaymentTerms'=>'15',
    'invoiceDueDate'=>'',
    'invoiceBillingContactId'=>null,
    'invoiceLineItems'=>[],
    'invoicePurchaseOrderReference'=>'',
    'invoiceNotes'=>'',
    'invoiceTaxRate'=>'0',
    'invoiceSupportingDocument'=>null,
    'invoiceEmailAfterCreation'=>true,
    'showRecordPaymentModal'=>false,
    'paymentInvoiceId'=>null,
    'paymentDate'=>'',
    'paymentMethod'=>'Bank transfer',
    'paymentAmount'=>'',
    'paymentReference'=>'',
    'paymentNotes'=>'',
    'showCollectionUpdateModal'=>false,
    'collectionOwnerId'=>null,
    'collectionFollowUpDate'=>'',
    'collectionNextFollowUpDate'=>'',
    'collectionNote'=>'',
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
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'overviewTaskDocumentModalTask'=>null,
    'overviewTaskAvailableDocuments'=>collect(),
    'showOverviewTaskDocumentModal'=>false,
    'showAddOrderTaskForm'=>false,
    'newOrderTaskAssigneeId'=>null,
    'overviewTaskDocumentSource'=>'upload',
    'overviewTaskDocumentUpload'=>null,
    'overviewTaskExistingDocumentId'=>null,
    'overviewTaskLinkFormTaskId'=>null,
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
    'inquiryResults'=>collect(),
    'inquirySearch'=>'',
    'selectedLinkInquiry'=>null,
    'showInquiryLinkConfirm'=>false,
    'showInquiryUnlinkConfirm'=>false,
    'canManageInquiryLink'=>false,
    'linkedInquiryCanOpen'=>false,
    'financeSummary'=>null,
    'financeContacts'=>null,
    'financeUsers'=>null,
    'canCreateFinance'=>false,
    'canEditFinance'=>false,
    'showCreateInvoiceModal'=>false,
    'invoiceType'=>'Final invoice',
    'invoiceCurrency'=>'USD',
    'invoiceIssueDate'=>'',
    'invoicePaymentTerms'=>'15',
    'invoiceDueDate'=>'',
    'invoiceBillingContactId'=>null,
    'invoiceLineItems'=>[],
    'invoicePurchaseOrderReference'=>'',
    'invoiceNotes'=>'',
    'invoiceTaxRate'=>'0',
    'invoiceSupportingDocument'=>null,
    'invoiceEmailAfterCreation'=>true,
    'showRecordPaymentModal'=>false,
    'paymentInvoiceId'=>null,
    'paymentDate'=>'',
    'paymentMethod'=>'Bank transfer',
    'paymentAmount'=>'',
    'paymentReference'=>'',
    'paymentNotes'=>'',
    'showCollectionUpdateModal'=>false,
    'collectionOwnerId'=>null,
    'collectionFollowUpDate'=>'',
    'collectionNextFollowUpDate'=>'',
    'collectionNote'=>'',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $team = \App\Support\JobDetailPresenter::team($job);
    $tabs = ['overview'=>'Overview','inquiry'=>'Inquiry'];
    if (app(\App\Services\AccessControlService::class)->can(auth()->user(), 'finance', 'view')) $tabs['finance'] = 'Invoices & Payments';
    $jobPriorityColor = app(\App\Services\MasterDataService::class)->displayColorFor('priority', (string) $job->priority);
?>
<div <?php echo e($attributes->class('ft-job-detail-page ft-exact-job-detail')); ?>>
    <div class="ft-detail-toolbar ft-exact-job-header">
        <div class="ft-job-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <span>Orders</span><span>/</span>
                <a class="ft-copyable-id-link" href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                <button type="button" class="ft-copy-id-btn" title="Copy Order ID" aria-label="Copy <?php echo e($job->displayOrderNumber()); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($job->displayOrderNumber())->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
            </div>
            <h1
                class="ft-editable-job-title ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-title')->toHtml() ?>, label: 'Order name', value: <?php echo \Illuminate\Support\Js::from($job->title)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->title)->toHtml() ?> })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span x-show="!editing" x-text="display"><?php echo e($job->title); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job)): ?>
                    <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit order title" title="Edit order name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.jobTitle.focus())">✎</button>
                    <input x-ref="jobTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                        x-on:keydown.escape.prevent="cancelEdit()"
                        x-on:keydown.enter.prevent="$event.target.blur()"
                        x-on:blur="if (editing) { draftValue.trim() === value ? cancelEdit() : commit(draftValue.trim(), draftValue.trim(), () => $wire.updateJobTextField(<?php echo e($job->id); ?>, 'title', draftValue.trim())) }">
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h1>
            <div class="ft-order-header-meta" aria-label="Order information">
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span>
                    <span class="ft-client-inline-identity"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $job->client,'name' => $job->client?->name ?: 'Client','size' => 20]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client?->name ?: 'Client'),'size' => 20]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?><span>Client <strong><?php echo e($job->client?->name ?: '—'); ?></strong></span></span>
                </span>
                <span class="ft-order-header-meta-separator" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item ft-order-header-reference">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.5h7l4 4V20.5H7z"></path><path d="M14 3.5v4h4"></path></svg></span>
                    <span>Reference <strong><?php echo e($job->order_number ?: '—'); ?></strong></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->order_number): ?>
                        <button type="button" class="ft-copy-id-btn ft-order-header-copy" title="Copy Reference Number" aria-label="Copy reference number <?php echo e($job->order_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($job->order_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span class="ft-order-header-meta-separator" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span>
                    <span>Created by <strong><?php echo e($job->creator?->name ?: 'System'); ?></strong></span>
                </span>
                <span class="ft-order-header-meta-separator" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 10h16"></path></svg></span>
                    <span>Created <strong><?php echo e($job->created_at ? \App\Support\UserLocalTime::format($job->created_at, 'M j, Y') : '—'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->created_at): ?> at <?php echo e(\App\Support\UserLocalTime::format($job->created_at, 'g:i A')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong></span>
                </span>
            </div>
            <div class="ft-exact-job-meta ft-order-status-row" aria-label="Order status">
                <span class="ft-soft-pill <?php echo e(\App\Support\JobDetailPresenter::healthClass($job->health)); ?>"><?php echo e($job->health); ?></span>
                <span class="ft-soft-pill <?php echo e($jobPriorityColor ? 'ft-master-color' : 'red'); ?>" style="<?php echo e(\App\Support\MasterColor::style($jobPriorityColor)); ?>"><?php echo e($job->priority); ?></span>
                <span class="ft-soft-pill purple"><?php echo e($job->phase?->name ?? $job->status); ?></span>
            </div>
        </div>
        <div class="ft-detail-actions ft-exact-job-team" aria-label="Order team">
            <div class="ft-team-stack">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $team->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $member,'name' => $member->name,'size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($member),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($member->name),'size' => 28]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->count()>4): ?><span class="ft-avatar-more small">+<?php echo e($team->count()-4); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="<?php echo e($detailTab==='finance' ? 'ft-finance-tab-action-row' : ''); ?>">
        <nav class="ft-detail-tabs ft-exact-tabs">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button class="<?php echo e($detailTab===$key ? 'active' : ''); ?>" wire:click="setDetailTab('<?php echo e($key); ?>')">
                    <?php echo e($label); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key==='documents'): ?><span><?php echo e($job->relationLoaded('documents') ? $job->documents->count() : (int) ($job->documents_count ?? 0)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key==='inquiry'): ?><span><?php echo e($job->source_inquiry_id ? 1 : 0); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab==='finance' && $canCreateFinance): ?>
            <div class="ft-finance-tab-actions">
                <button type="button" class="ft-finance-btn secondary" wire:click="openRecordPayment">Record Payment</button>
                <button type="button" class="ft-finance-btn primary" wire:click="openCreateInvoice"><span>＋</span> Create Invoice</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab==='overview'): ?>
        <?php if (isset($component)) { $__componentOriginaldad3229fa826ba1f935ba3112a62f4a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-overview','data' => ['job' => $job,'expandedPhaseIds' => $expandedPhaseIds,'taskStatuses' => $taskStatuses,'users' => $users,'mentionUsers' => $mentionUsers,'priorities' => $priorities,'products' => $products,'categories' => $categories,'jobTaskSearch' => $jobTaskSearch,'activityTab' => $activityTab,'activityPage' => $activityPage,'focusComment' => $focusComment,'jobDocumentUploads' => $jobDocumentUploads,'overviewTaskDocumentModalTask' => $overviewTaskDocumentModalTask,'overviewTaskAvailableDocuments' => $overviewTaskAvailableDocuments,'showOverviewTaskDocumentModal' => $showOverviewTaskDocumentModal,'overviewTaskDocumentSource' => $overviewTaskDocumentSource,'overviewTaskDocumentUpload' => $overviewTaskDocumentUpload,'overviewTaskExistingDocumentId' => $overviewTaskExistingDocumentId,'overviewTaskLinkFormTaskId' => $overviewTaskLinkFormTaskId,'showAddOrderTaskForm' => $showAddOrderTaskForm,'newOrderTaskAssigneeId' => $newOrderTaskAssigneeId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'expanded-phase-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedPhaseIds),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'job-task-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobTaskSearch),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads),'overview-task-document-modal-task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentModalTask),'overview-task-available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskAvailableDocuments),'show-overview-task-document-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showOverviewTaskDocumentModal),'overview-task-document-source' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentSource),'overview-task-document-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentUpload),'overview-task-existing-document-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskExistingDocumentId),'overview-task-link-form-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskLinkFormTaskId),'show-add-order-task-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showAddOrderTaskForm),'new-order-task-assignee-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newOrderTaskAssigneeId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3)): ?>
<?php $attributes = $__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3; ?>
<?php unset($__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldad3229fa826ba1f935ba3112a62f4a3)): ?>
<?php $component = $__componentOriginaldad3229fa826ba1f935ba3112a62f4a3; ?>
<?php unset($__componentOriginaldad3229fa826ba1f935ba3112a62f4a3); ?>
<?php endif; ?>
    <?php elseif($detailTab==='inquiry'): ?>
        <?php if (isset($component)) { $__componentOriginal4fcfaf8252741a09c01f4d7850ec4125 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-inquiry','data' => ['job' => $job,'results' => $inquiryResults,'search' => $inquirySearch,'selectedInquiry' => $selectedLinkInquiry,'showLinkConfirm' => $showInquiryLinkConfirm,'showUnlinkConfirm' => $showInquiryUnlinkConfirm,'canManage' => $canManageInquiryLink,'linkedInquiryCanOpen' => $linkedInquiryCanOpen]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-inquiry'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryResults),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquirySearch),'selected-inquiry' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedLinkInquiry),'show-link-confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showInquiryLinkConfirm),'show-unlink-confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showInquiryUnlinkConfirm),'can-manage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canManageInquiryLink),'linked-inquiry-can-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($linkedInquiryCanOpen)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125)): ?>
<?php $attributes = $__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125; ?>
<?php unset($__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fcfaf8252741a09c01f4d7850ec4125)): ?>
<?php $component = $__componentOriginal4fcfaf8252741a09c01f4d7850ec4125; ?>
<?php unset($__componentOriginal4fcfaf8252741a09c01f4d7850ec4125); ?>
<?php endif; ?>
    <?php elseif($detailTab==='finance'): ?>
        <?php if (isset($component)) { $__componentOriginalc5092f9572675e4d09a4c5d853dd912c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5092f9572675e4d09a4c5d853dd912c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.detail','data' => ['job' => $job,'summary' => $financeSummary,'contacts' => $financeContacts ?? collect(),'users' => $financeUsers ?? collect(),'canCreate' => $canCreateFinance,'canEdit' => $canEditFinance,'showCreateInvoiceModal' => $showCreateInvoiceModal,'invoiceType' => $invoiceType,'invoiceCurrency' => $invoiceCurrency,'invoiceIssueDate' => $invoiceIssueDate,'invoicePaymentTerms' => $invoicePaymentTerms,'invoiceDueDate' => $invoiceDueDate,'invoiceBillingContactId' => $invoiceBillingContactId,'invoiceLineItems' => $invoiceLineItems,'invoicePurchaseOrderReference' => $invoicePurchaseOrderReference,'invoiceNotes' => $invoiceNotes,'invoiceTaxRate' => $invoiceTaxRate,'invoiceSupportingDocument' => $invoiceSupportingDocument,'invoiceEmailAfterCreation' => $invoiceEmailAfterCreation,'showRecordPaymentModal' => $showRecordPaymentModal,'paymentInvoiceId' => $paymentInvoiceId,'paymentDate' => $paymentDate,'paymentMethod' => $paymentMethod,'paymentAmount' => $paymentAmount,'paymentReference' => $paymentReference,'paymentNotes' => $paymentNotes,'showCollectionUpdateModal' => $showCollectionUpdateModal,'collectionOwnerId' => $collectionOwnerId,'collectionFollowUpDate' => $collectionFollowUpDate,'collectionNextFollowUpDate' => $collectionNextFollowUpDate,'collectionNote' => $collectionNote]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'summary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeSummary),'contacts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeContacts ?? collect()),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeUsers ?? collect()),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateFinance),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditFinance),'show-create-invoice-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showCreateInvoiceModal),'invoice-type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceType),'invoice-currency' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceCurrency),'invoice-issue-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceIssueDate),'invoice-payment-terms' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoicePaymentTerms),'invoice-due-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceDueDate),'invoice-billing-contact-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceBillingContactId),'invoice-line-items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceLineItems),'invoice-purchase-order-reference' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoicePurchaseOrderReference),'invoice-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceNotes),'invoice-tax-rate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceTaxRate),'invoice-supporting-document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceSupportingDocument),'invoice-email-after-creation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceEmailAfterCreation),'show-record-payment-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showRecordPaymentModal),'payment-invoice-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentInvoiceId),'payment-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentDate),'payment-method' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentMethod),'payment-amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentAmount),'payment-reference' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentReference),'payment-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentNotes),'show-collection-update-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showCollectionUpdateModal),'collection-owner-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionOwnerId),'collection-follow-up-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionFollowUpDate),'collection-next-follow-up-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionNextFollowUpDate),'collection-note' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionNote)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5092f9572675e4d09a4c5d853dd912c)): ?>
<?php $attributes = $__attributesOriginalc5092f9572675e4d09a4c5d853dd912c; ?>
<?php unset($__attributesOriginalc5092f9572675e4d09a4c5d853dd912c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5092f9572675e4d09a4c5d853dd912c)): ?>
<?php $component = $__componentOriginalc5092f9572675e4d09a4c5d853dd912c; ?>
<?php unset($__componentOriginalc5092f9572675e4d09a4c5d853dd912c); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail.blade.php ENDPATH**/ ?>