<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'task', 'config' => [], 'step' => 'main', 'payload' => [], 'emailFallback' => false, 'emailFallbackMessage' => '', 'emailFallbackAttempts' => 0]));

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

foreach (array_filter((['job', 'task', 'config' => [], 'step' => 'main', 'payload' => [], 'emailFallback' => false, 'emailFallbackMessage' => '', 'emailFallbackAttempts' => 0]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $variant = (string) ($config['variant'] ?? 'confirm');
    $title = (string) ($config['title'] ?? 'Task action');
    $copy = (string) ($config['copy'] ?? 'Confirm this workflow action.');
    $choices = collect($config['choices'] ?? ['confirm' => 'Confirm']);
    $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
    $automationKey = $workflowActions->automationKey($task);
    $latestArtworkTask = $job->tasks->first(fn($candidate) => $workflowActions->automationKey($candidate) === 'ART_PREPARE_UPLOAD');
    $artworkDocs = $latestArtworkTask
        ? $job->documents->where('task_id', $latestArtworkTask->id)->sortBy('created_at')->values()
        : collect();
    $artworkVersion = max(1, (int) ($artworkDocs->max('version') ?? 0));
    $latestArtworkDocuments = $artworkDocs->where('version', $artworkVersion)->sortBy('id')->values();
    $latestArtwork = $latestArtworkDocuments->last();
    $activeItems = \App\Support\OrderDetailPresenter::activeItems($job);
    $firstItem = $activeItems->first();
    $productName = (string) ($firstItem?->product_name ?: $job->product ?: 'Order product');
    $supplierName = ($firstItem && $firstItem->relationLoaded('supplier'))
        ? (string) ($firstItem->supplier?->name ?: 'Supplier')
        : 'Supplier';
    $orderNumber = $job->displayOrderNumber();
    $clientName = (string) ($job->client?->name ?: 'Client');
    $ownerName = (string) ($job->owner?->name ?: $job->coordinator?->name ?: 'FlowTrack');
    $orderTotal = (float) $activeItems->sum(fn($item) => (float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 0));
    $emailHandoffPreview = in_array($variant, ['purchase_order_email', 'artwork_email'], true)
        ? app(\App\Services\Orders\OrderWorkflowEmailService::class)->preview($task, auth()->user())
        : [];
    $emailServiceEnabled = (bool) ($emailHandoffPreview['email_service_enabled'] ?? true);
    if (! $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)) {
        $title = $variant === 'artwork_email' ? 'Complete Artwork Handoff' : 'Complete Purchase Order Handoff';
        $copy = 'Order email service is disabled by an administrator. Review the handoff details, then continue without sending email.';
    }
    $emailFallbackDocumentId = (int) ($emailHandoffPreview['document_id'] ?? 0);
    $emailFallbackDocument = $emailFallbackDocumentId > 0
        ? $job->documents->firstWhere('id', $emailFallbackDocumentId)
        : null;
    $emailFallbackAttachmentLabel = $variant === 'artwork_email' ? 'Artwork' : 'Purchase Order';

    // Only the main Artwork preview/handoff screens need the large landscape
    // layout. Revision/issue follow-up steps must stay on the normal compact
    // dialog size even when they originate from an Artwork task.
    $isArtworkPreviewModal = $step === 'main'
        && in_array($variant, ['artwork_review', 'artwork_email', 'client_erp'], true);
    // Billing and Payment render validation inside a two-column form. Give
    // those dialogs a stable validation layout so field errors never resize
    // or horizontally shift the popup after submit.
    $usesStableFinanceValidation = $step === 'main'
        && in_array($variant, ['invoice_prepare', 'payment'], true);
    $modalWide = $variant === 'courier_label';

    if ($step === 'sample') {
        $title = 'Is a Sample or Swatch Required?';
        $copy = 'The artwork is approved. Decide whether supplier sample approval is required before Production.';
    } elseif ($step === 'revision') {
        $title = $automationKey === 'ART_INTERNAL_REVIEW' ? 'Request Artwork Revision' : 'Client Revision Request';
        $copy = $automationKey === 'ART_INTERNAL_REVIEW'
            ? 'Add the internal revision instructions before returning the Artwork task to upload.'
            : 'Record the client feedback before restarting the artwork approval cycle.';
    } elseif ($step === 'issue') {
        $title = $automationKey === 'QC_CHECK' ? 'Report QC Issue' : 'Report Production Issue';
        $copy = 'Describe the issue before notifying the supplier and blocking progression.';
    }
?>
<div class="ft-order-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-workflow-action-modal-'.e($task->id).'-'.e($step).''; ?>wire:key="order-workflow-action-modal-<?php echo e($task->id); ?>-<?php echo e($step); ?>" wire:click.self="closeOrderWorkflowAction">
    <section class="ft-order-task-document-modal ft-order-workflow-action-modal <?php echo e($isArtworkPreviewModal ? 'ft-order-workflow-action-modal--artwork-preview' : ($modalWide ? 'ft-order-workflow-action-modal--wide' : '')); ?> <?php echo e($usesStableFinanceValidation ? 'ft-order-workflow-action-modal--stable-finance-validation' : ''); ?>" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="order-workflow-action-modal-title">
        <header class="ft-order-task-document-modal-head">
            <div><h2 id="order-workflow-action-modal-title"><?php echo e($title); ?></h2><p><?php echo e($copy); ?></p></div>
            <button type="button" wire:click="closeOrderWorkflowAction" aria-label="Close">×</button>
        </header>

        <div class="ft-order-task-document-modal-body ft-prototype-action-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'sample'): ?>
                <div class="ft-prototype-choice-grid">
                    <button type="button" wire:click="submitOrderWorkflowAction('sample_no')">
                        <span class="ft-prototype-choice-icon">→</span>
                        <strong>No</strong>
                        <small>Go directly to Production</small>
                    </button>
                    <button type="button" wire:click="submitOrderWorkflowAction('sample_yes')">
                        <span class="ft-prototype-choice-icon">✓</span>
                        <strong>Yes</strong>
                        <small>Activate Sample Approval</small>
                    </button>
                </div>
            <?php elseif($step === 'revision'): ?>
                <div class="ft-artwork-revision-selector">
                    <div class="ft-artwork-revision-selector-head">
                        <div>
                            <strong>Which artwork needs revision?</strong>
                            <span>Select only the file or files that must be replaced. Every unselected artwork file will remain unchanged in the next version.</span>
                        </div>
                        <em><?php echo e($latestArtworkDocuments->count()); ?> current file<?php echo e($latestArtworkDocuments->count() === 1 ? '' : 's'); ?></em>
                    </div>
                    <div class="ft-artwork-revision-selector-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $latestArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revisionDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php $revisionExtension = strtoupper(pathinfo((string) $revisionDocument->name, PATHINFO_EXTENSION) ?: 'FILE'); ?>
                            <label class="ft-artwork-revision-selector-item">
                                <input
                                    type="checkbox"
                                    wire:model="orderWorkflowActionPayload.revision_document_ids"
                                    value="<?php echo e($revisionDocument->id); ?>"
                                >
                                <span class="ft-artwork-revision-selector-check" aria-hidden="true">✓</span>
                                <span class="ft-artwork-revision-selector-type"><?php echo e($revisionExtension); ?></span>
                                <span class="ft-artwork-revision-selector-copy">
                                    <b title="<?php echo e($revisionDocument->name); ?>"><?php echo e($revisionDocument->name); ?></b>
                                    <small>Artwork V<?php echo e(max(1, (int) $revisionDocument->version)); ?> · Select to replace this file only</small>
                                </span>
                                <a href="<?php echo e(route('documents.open', $revisionDocument)); ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()">View</a>
                            </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="ft-artwork-revision-selector-empty">No current artwork files are available to revise.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.revision_document_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <label class="ft-prototype-field">
                    <span><?php echo e($automationKey === 'ART_INTERNAL_REVIEW' ? 'Revision instructions' : 'Client feedback'); ?></span>
                    <textarea wire:model="orderWorkflowActionComment" rows="5" placeholder="Describe the required artwork changes..."></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
            <?php elseif($step === 'issue'): ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Issue category</span><select wire:model="orderWorkflowActionPayload.issue_category"><option>Fabric color variance</option><option>Print color mismatch</option><option>Incorrect dimensions</option><option>Damaged items</option><option>Other</option></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.issue_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Supplier</span><input value="<?php echo e($supplierName); ?>" disabled></label>
                </div>
                <label class="ft-prototype-field"><span>Description</span><textarea wire:model="orderWorkflowActionComment" rows="5" placeholder="Describe the issue and corrective action required..."></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-prototype-file-placeholder"><strong>Screenshot / photo</strong><span>Supporting images/documents can be added to the task after the issue is recorded.</span></div>
                <div class="ft-prototype-email-preview"><b>Email preview</b><span>The issue notification will be recorded for <?php echo e($supplierName); ?> with this Order and task.</span></div>
            <?php elseif($variant === 'purchase_order_email'): ?>
                <div class="ft-order-task-document-target">
                    <span class="ft-order-task-document-target-icon">PO</span>
                    <div>
                        <small>ATTACHMENT</small>
                        <strong><?php echo e($emailHandoffPreview['document_name'] ?? 'No Purchase Order uploaded'); ?></strong>
                        <span><?php echo e(filled($emailHandoffPreview['document_version'] ?? null) ? 'Version '.(int) $emailHandoffPreview['document_version'] : 'Upload the Purchase Order in the previous task first'); ?></span>
                    </div>
                    <em><?php echo e($emailHandoffPreview['recipient_count'] ?? 0); ?> recipient<?php echo e((int) ($emailHandoffPreview['recipient_count'] ?? 0) === 1 ? '' : 's'); ?></em>
                </div>
                <?php if (isset($component)) { $__componentOriginal70137674ee97e22e87c5d4188f3bbd58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.handoff-preview','data' => ['preview' => $emailHandoffPreview,'defaultSubject' => 'Purchase Order ready — '.$orderNumber,'emptyRecipientText' => 'No active user with a valid email is assigned to this Order\'s Artwork phase.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.handoff-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emailHandoffPreview),'defaultSubject' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Purchase Order ready — '.$orderNumber),'emptyRecipientText' => 'No active user with a valid email is assigned to this Order\'s Artwork phase.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal70137674ee97e22e87c5d4188f3bbd58)): ?>
<?php $attributes = $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58; ?>
<?php unset($__attributesOriginal70137674ee97e22e87c5d4188f3bbd58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal70137674ee97e22e87c5d4188f3bbd58)): ?>
<?php $component = $__componentOriginal70137674ee97e22e87c5d4188f3bbd58; ?>
<?php unset($__componentOriginal70137674ee97e22e87c5d4188f3bbd58); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php elseif($variant === 'artwork_review' || $variant === 'artwork_email' || $variant === 'client_erp'): ?>
                <div
                    class="ft-prototype-artwork-preview"
                    x-data="{ selectedArtworkId: <?php echo e((int) ($latestArtwork?->id ?? 0)); ?> }"
                >
                    <div class="ft-prototype-artwork-canvas">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestArtworkDocuments->isNotEmpty()): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $previewDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $previewExtension = strtolower(pathinfo((string) $previewDocument->name, PATHINFO_EXTENSION)); ?>
                                <div
                                    class="ft-prototype-artwork-canvas-item"
                                    x-cloak
                                    x-show="selectedArtworkId === <?php echo e((int) $previewDocument->id); ?>"
                                >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($previewExtension, ['jpg','jpeg','png','webp','gif'], true)): ?>
                                        <img src="<?php echo e(route('documents.open', $previewDocument)); ?>" alt="Artwork preview: <?php echo e($previewDocument->name); ?>">
                                    <?php else: ?>
                                        <div class="ft-prototype-artwork-file"><span><?php echo e(strtoupper($previewExtension ?: 'FILE')); ?></span><strong><?php echo e($previewDocument->name); ?> · Version <?php echo e(max(1, (int) $previewDocument->version)); ?></strong><a href="<?php echo e(route('documents.open', $previewDocument)); ?>" target="_blank" rel="noopener">Open artwork</a></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php else: ?>
                            <div class="ft-prototype-artwork-file"><span>ART</span><strong>Artwork file</strong><small>No previewable image available</small></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-prototype-artwork-meta">
                        <small>LATEST ARTWORK · <?php echo e($latestArtworkDocuments->count()); ?> FILE<?php echo e($latestArtworkDocuments->count() === 1 ? '' : 'S'); ?></small>
                        <h3><?php echo e($productName); ?></h3>
                        <dl>
                            <div><dt>Version</dt><dd>V<?php echo e($artworkVersion); ?></dd></div>
                            <div><dt>Files</dt><dd><?php echo e($latestArtworkDocuments->isNotEmpty() ? $latestArtworkDocuments->pluck('name')->implode(', ') : 'Artwork file'); ?></dd></div>
                            <div><dt>Uploaded by</dt><dd><?php echo e($latestArtwork?->uploader?->name ?: $task->assignee?->name ?: 'FlowTrack'); ?></dd></div>
                            <div><dt>Client</dt><dd><?php echo e($clientName); ?></dd></div>
                        </dl>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestArtworkDocuments->isNotEmpty()): ?>
                            <div class="ft-prototype-artwork-actions">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $previewDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span x-cloak x-show="selectedArtworkId === <?php echo e((int) $previewDocument->id); ?>">
                                        <a href="<?php echo e(route('documents.open', $previewDocument)); ?>" target="_blank" rel="noopener">Open</a>
                                        <a href="<?php echo e(route('documents.download', $previewDocument)); ?>">Download</a>
                                    </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <div class="ft-artwork-current-file-picker" aria-label="Current artwork files">
                                <div class="ft-artwork-current-file-picker-head">
                                    <strong>Current artwork files</strong>
                                    <span>Select a file below to preview it on the left.</span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button
                                        type="button"
                                        class="ft-artwork-current-file-choice"
                                        x-on:click="selectedArtworkId = <?php echo e((int) $doc->id); ?>"
                                        x-bind:class="{ 'is-active': selectedArtworkId === <?php echo e((int) $doc->id); ?> }"
                                    >
                                        <span class="ft-artwork-current-file-choice-type"><?php echo e(strtoupper(pathinfo((string) $doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span>
                                        <span class="ft-artwork-current-file-choice-copy">
                                            <b title="<?php echo e($doc->name); ?>"><?php echo e($doc->name); ?></b>
                                            <small>Artwork V<?php echo e(max(1, (int) $doc->version)); ?></small>
                                        </span>
                                        <em x-text="selectedArtworkId === <?php echo e((int) $doc->id); ?> ? 'Viewing' : 'Preview'">Preview</em>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($artworkDocs->where('version', '!=', $artworkVersion)->isNotEmpty()): ?>
                            <details class="ft-prototype-version-history">
                                <summary>Previous artwork versions</summary>
                                <div class="ft-prototype-version-list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $artworkDocs->where('version', '!=', $artworkVersion)->sortByDesc('id')->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div>
                                            <span class="ft-prototype-version-file">
                                                <strong><?php echo e($doc->name); ?> · Version <?php echo e(max(1, (int) $doc->version)); ?></strong>
                                                <small><?php echo e(\App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A')); ?></small>
                                            </span>
                                            <span class="ft-prototype-version-status">
                                                <b>Archived</b>
                                                <a href="<?php echo e(route('documents.open', $doc)); ?>" target="_blank" rel="noopener">Open</a>
                                                <a href="<?php echo e(route('documents.download', $doc)); ?>">Download</a>
                                            </span>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </details>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'artwork_email'): ?>
                    <?php if (isset($component)) { $__componentOriginal70137674ee97e22e87c5d4188f3bbd58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.handoff-preview','data' => ['preview' => $emailHandoffPreview,'defaultSubject' => 'Artwork ready — '.$orderNumber,'emptyRecipientText' => 'No active user with a valid email has the Order Team role in Users & role assignments.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.handoff-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emailHandoffPreview),'defaultSubject' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Artwork ready — '.$orderNumber),'emptyRecipientText' => 'No active user with a valid email has the Order Team role in Users & role assignments.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal70137674ee97e22e87c5d4188f3bbd58)): ?>
<?php $attributes = $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58; ?>
<?php unset($__attributesOriginal70137674ee97e22e87c5d4188f3bbd58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal70137674ee97e22e87c5d4188f3bbd58)): ?>
<?php $component = $__componentOriginal70137674ee97e22e87c5d4188f3bbd58; ?>
<?php unset($__componentOriginal70137674ee97e22e87c5d4188f3bbd58); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php elseif($variant === 'client_erp'): ?>
                    <label class="ft-prototype-field ft-prototype-field--top"><span>Client ERP reference</span><input wire:model="orderWorkflowActionPayload.erp_reference" placeholder="Client ERP reference"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.erp_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php elseif($variant === 'client_decision'): ?>
                <p class="ft-prototype-modal-copy">Choose the decision received from <?php echo e($clientName); ?> for Artwork V<?php echo e($artworkVersion); ?>.</p>
                <div class="ft-prototype-choice-grid">
                    <button type="button" wire:click="submitOrderWorkflowAction('revise')"><span class="ft-prototype-choice-icon">↻</span><strong>Client Requested Revision</strong><small>Restart the artwork revision cycle</small></button>
                    <button type="button" wire:click="submitOrderWorkflowAction('approved')"><span class="ft-prototype-choice-icon">✓</span><strong>Client Approved Artwork</strong><small>Continue to sample decision</small></button>
                </div>
            <?php elseif($variant === 'estimated_delivery'): ?>
                <div class="ft-prototype-required-date-panel">
                    <div class="ft-prototype-required-date-icon">*</div>
                    <div>
                        <strong>Required before Production</strong>
                        <span>Set the estimated delivery date to unlock Start Production.</span>
                    </div>
                </div>
                <label class="ft-prototype-field ft-prototype-field--top">
                    <span>Estimated delivery date</span>
                    <input
                        type="date"
                        class="ft-prototype-clickable-date"
                        wire:model="orderWorkflowActionPayload.estimated_delivery_date"
                        onclick="this.focus({ preventScroll: true }); if (typeof this.showPicker === 'function') { try { this.showPicker(); } catch (e) {} }"
                        aria-label="Estimated delivery date. Click anywhere in the field to open the calendar."
                    >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.estimated_delivery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
            <?php elseif($variant === 'production_check'): ?>
                <div class="ft-prototype-choice-grid">
                    <button type="button" class="danger-choice" wire:click="submitOrderWorkflowAction('issue')"><span class="ft-prototype-choice-icon">!</span><strong>Report Issue</strong><small>Notify supplier and keep Production open</small></button>
                    <button type="button" wire:click="submitOrderWorkflowAction('confirm')"><span class="ft-prototype-choice-icon">✓</span><strong>No Issue</strong><small>Continue to Production completion</small></button>
                </div>
            <?php elseif($variant === 'issue_resolution'): ?>
                <label class="ft-prototype-field"><span>Resolution</span><textarea wire:model="orderWorkflowActionComment" rows="5" placeholder="Describe the corrective action and resolution..."></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <?php elseif($variant === 'qc_check'): ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Quantity received</span><input type="number" min="1" wire:model="orderWorkflowActionPayload.qty_received"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.qty_received'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Quantity inspected</span><input type="number" min="1" wire:model="orderWorkflowActionPayload.qty_inspected"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.qty_inspected'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Accepted</span><input type="number" min="0" wire:model="orderWorkflowActionPayload.qty_accepted"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.qty_accepted'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Rejected</span><input type="number" min="0" wire:model="orderWorkflowActionPayload.qty_rejected"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.qty_rejected'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
                <label class="ft-prototype-field"><span>QC comments</span><textarea wire:model="orderWorkflowActionPayload.qc_comments" rows="4" placeholder="Record stitching, dimensions, packaging, print registration, or other QC notes..."></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.qc_comments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-prototype-choice-grid">
                    <button type="button" class="danger-choice" wire:click="submitOrderWorkflowAction('issue')"><span class="ft-prototype-choice-icon">!</span><strong>Report Issue</strong><small>Open a supplier-resolution issue</small></button>
                    <button type="button" wire:click="submitOrderWorkflowAction('pass')"><span class="ft-prototype-choice-icon">✓</span><strong>QC Passed</strong><small>Continue toward Shipment</small></button>
                </div>
            <?php elseif($variant === 'shipment_info'): ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Recipient</span><input wire:model="orderWorkflowActionPayload.recipient"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.recipient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Contact</span><input wire:model="orderWorkflowActionPayload.contact"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
                <label class="ft-prototype-field"><span>Delivery address</span><textarea wire:model="orderWorkflowActionPayload.address" rows="4"></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Packages</span><input wire:model="orderWorkflowActionPayload.packages" placeholder="e.g. 24 cartons"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.packages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Total weight</span><input wire:model="orderWorkflowActionPayload.weight" placeholder="e.g. 312 kg"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Dimensions / carton</span><input wire:model="orderWorkflowActionPayload.dimensions" placeholder="e.g. 60 × 45 × 40 cm"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.dimensions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Declared value</span><input wire:model="orderWorkflowActionPayload.declared_value" placeholder="<?php echo e(number_format($orderTotal,2)); ?>"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.declared_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
            <?php elseif($variant === 'courier_label'): ?>
                <div class="ft-prototype-label-preview">
                    <div><small>SHIP TO</small><h3><?php echo e(mb_strtoupper($clientName)); ?></h3><p><?php echo nl2br(e((string) ($payload['address'] ?? $job->shipping_address ?? ''))); ?></p><div class="ft-prototype-barcode"></div><b>FLOWTRACK · <?php echo e($orderNumber); ?></b></div>
                    <div><small>SERVICE</small><h3>Courier<br>Shipping</h3><p><b><?php echo e($payload['packages'] ?? 'Packages confirmed'); ?></b><br><?php echo e($payload['weight'] ?? ''); ?></p><strong class="ft-prototype-label-code">SHIP</strong></div>
                </div>
            <?php elseif($variant === 'ship_package'): ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Shipping provider</span><select wire:model="orderWorkflowActionPayload.carrier"><option>UPS</option><option>FedEx</option><option>DHL</option><option>Other</option></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.carrier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Tracking number</span><input wire:model="orderWorkflowActionPayload.tracking_number"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.tracking_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Shipment date</span><input type="date" wire:model="orderWorkflowActionPayload.shipment_date"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.shipment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Estimated delivery</span><input type="date" wire:model="orderWorkflowActionPayload.estimated_delivery_date"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.estimated_delivery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
            <?php elseif($variant === 'invoice_prepare'): ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Invoice number</span><input wire:model="orderWorkflowActionPayload.invoice_number"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Invoice date</span><input type="date" wire:model="orderWorkflowActionPayload.invoice_date"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Amount</span><input type="number" step="0.01" wire:model="orderWorkflowActionPayload.invoice_amount"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.invoice_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Currency</span><select wire:model="orderWorkflowActionPayload.invoice_currency"><option>USD</option><option>GBP</option><option>EUR</option></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.invoice_currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Payment terms</span><select wire:model="orderWorkflowActionPayload.payment_terms"><option>Net 15</option><option>Net 30</option><option>Due on receipt</option></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.payment_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Due date</span><input type="date" wire:model="orderWorkflowActionPayload.invoice_due_date"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.invoice_due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
                <div class="ft-prototype-email-preview"><b>Included order:</b> <?php echo e($orderNumber); ?><br><b>Client:</b> <?php echo e($clientName); ?><br><b>Total:</b> <?php echo e($payload['invoice_currency'] ?? 'USD'); ?> <?php echo e(number_format((float) ($payload['invoice_amount'] ?? $orderTotal), 2)); ?></div>
            <?php elseif($variant === 'invoice_send'): ?>
                <div class="ft-prototype-email-preview"><b>To:</b> Client accounts contact<br><b>Subject:</b> Invoice <?php echo e($payload['invoice_number'] ?: '—'); ?> — <?php echo e($orderNumber); ?><br><br>Hello <?php echo e($clientName); ?>,<br><br>Please find attached the invoice for Order <?php echo e($orderNumber); ?>.<br><br>Amount due: <?php echo e($payload['invoice_currency'] ?? 'USD'); ?> <?php echo e(number_format((float) ($payload['invoice_amount'] ?? $orderTotal), 2)); ?><br>Due date: <?php echo e($payload['invoice_due_date'] ?: 'As agreed'); ?><br><br>Regards,<br><?php echo e($ownerName); ?></div>
            <?php elseif($variant === 'payment'): ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Outstanding balance</span><input value="<?php echo e(number_format((float) ($payload['payment_amount'] ?? $orderTotal), 2)); ?>" disabled></label>
                    <label class="ft-prototype-field"><span>Payment amount</span><input type="number" min="0.01" step="0.01" wire:model="orderWorkflowActionPayload.payment_amount"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.payment_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Payment date</span><input type="date" wire:model="orderWorkflowActionPayload.payment_date"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.payment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-prototype-field"><span>Payment reference</span><input wire:model="orderWorkflowActionPayload.payment_reference"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.payment_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
                <label class="ft-prototype-field"><span>Notes</span><textarea wire:model="orderWorkflowActionPayload.payment_notes" rows="4" placeholder="Bank transfer received and matched to invoice..."></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.payment_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <?php else: ?>
                <div class="ft-order-task-document-target"><span class="ft-order-task-document-target-icon">⌘</span><div><small>WORKFLOW TASK</small><strong><?php echo e($task->title); ?></strong><span><?php echo e($task->phase?->name ?? 'Order workflow'); ?></span></div><em><?php echo e($task->status ?: 'Ready'); ?></em></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emailFallback && $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)): ?>
                <div class="ft-prototype-email-preview ft-prototype-email-preview--error" role="alert" aria-live="polite">
                    <b>Email delivery failed after <?php echo e(max(3, (int) $emailFallbackAttempts)); ?> attempts</b>
                    <span><?php echo e($emailFallbackMessage); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emailFallbackDocument): ?>
                        <div class="ft-prototype-artwork-actions">
                            <a href="<?php echo e(route('documents.download', $emailFallbackDocument)); ?>">Download <?php echo e($emailFallbackAttachmentLabel); ?></a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php
            // The three main choice dialogs render their actions inside the body.
            // Once one of those choices opens a nested revision/issue dialog, the
            // normal footer must return so the user can actually submit it.
            $usesInlineWorkflowActions = $step === 'main'
                && in_array($variant, ['client_decision','production_check','qc_check'], true);
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($usesInlineWorkflowActions || $step === 'sample')): ?>
            <footer class="ft-order-task-document-modal-actions ft-order-workflow-action-buttons">
                <button type="button" class="secondary" wire:click="closeOrderWorkflowAction">Cancel</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'revision'): ?>
                    <button type="button" class="primary" wire:click="submitOrderWorkflowAction('revise')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction"><?php echo e($automationKey === 'ART_INTERNAL_REVIEW' ? 'Request Revision' : 'Activate Revision Task'); ?></button>
                <?php elseif($step === 'issue'): ?>
                    <button type="button" class="danger" wire:click="submitOrderWorkflowAction('issue')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">Report Issue</button>
                <?php elseif($emailFallback && $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)): ?>
                    <button type="button" class="secondary" wire:click="submitOrderWorkflowAction('confirm')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">Try email again</button>
                    <button type="button" class="primary" wire:click="completeOrderWorkflowEmailTaskAfterFailure" wire:loading.attr="disabled" wire:target="completeOrderWorkflowEmailTaskAfterFailure">Complete task</button>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $choices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $decision => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $actionLabel = ! $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)
                                ? 'Complete without email'
                                : $label;
                        ?>
                        <button type="button" class="<?php echo e(in_array($decision, ['revise','issue'], true) ? 'danger' : 'primary'); ?>" wire:click="submitOrderWorkflowAction('<?php echo e($decision); ?>')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction"><?php echo e($actionLabel); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </footer>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/workflow-action-modal.blade.php ENDPATH**/ ?>