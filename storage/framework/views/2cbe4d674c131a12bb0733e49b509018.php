<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'task', 'config' => [], 'step' => 'main', 'payload' => [], 'attachment' => null, 'revisionComments' => [], 'revisionAttachments' => [], 'mentionUsers' => collect(), 'emailFallback' => false, 'emailFallbackMessage' => '', 'emailFallbackAttempts' => 0]));

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

foreach (array_filter((['job', 'task', 'config' => [], 'step' => 'main', 'payload' => [], 'attachment' => null, 'revisionComments' => [], 'revisionAttachments' => [], 'mentionUsers' => collect(), 'emailFallback' => false, 'emailFallbackMessage' => '', 'emailFallbackAttempts' => 0]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
    $latestArtworkDocuments = $latestArtworkTask
        ? ($latestArtworkTask->relationLoaded('currentArtworkDocuments')
            ? collect($latestArtworkTask->getRelation('currentArtworkDocuments'))->sortBy('id')->values()
            : app(\App\Services\DocumentService::class)->currentArtworkDocuments($latestArtworkTask, $artworkDocs))
        : collect();
    $latestArtwork = $latestArtworkDocuments->last();
    $currentArtworkVersions = $latestArtworkDocuments
        ->pluck('version')
        ->map(fn($version) => max(1, (int) $version))
        ->unique()
        ->sort()
        ->values();
    $artworkVersionLabel = $currentArtworkVersions->isEmpty()
        ? 'V1'
        : $currentArtworkVersions->map(fn($version) => 'V'.$version)->implode(' · ');
    $currentArtworkDocumentIds = $latestArtworkDocuments->pluck('id')->map(fn($id) => (int) $id);
    $archivedArtworkDocuments = $artworkDocs
        ->reject(fn($document) => $currentArtworkDocumentIds->contains((int) $document->id))
        ->sortByDesc('id')
        ->values();
    $activeItems = \App\Support\OrderDetailPresenter::activeItems($job);
    $firstItem = $activeItems->first();
    $productName = (string) ($firstItem?->product_name ?: $job->product ?: 'Order product');
    // activeItems() can return a lightweight stdClass for legacy orders that
    // still use the order-level product fields. Only Eloquent-backed items
    // expose relationLoaded(), so guard that call before checking supplier.
    $firstItemHasLoadedSupplier = is_object($firstItem)
        && method_exists($firstItem, 'relationLoaded')
        && $firstItem->relationLoaded('supplier');
    $supplierName = $firstItemHasLoadedSupplier
        ? (string) ($firstItem->supplier?->name ?: 'Supplier')
        : 'Supplier';
    $orderNumber = $job->displayOrderNumber();
    $clientName = (string) ($job->client?->name ?: 'Client');
    $ownerName = (string) ($job->owner?->name ?: $job->coordinator?->name ?: 'FlowTrack');
    $orderTotal = (float) $activeItems->sum(fn($item) => (float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 0));
    $emailHandoffPreview = in_array($variant, ['purchase_order_email', 'artwork_email'], true)
        ? app(\App\Services\Orders\OrderWorkflowEmailService::class)->preview($task, auth()->user(), $payload)
        : [];
    $invoiceEmailPreview = $variant === 'invoice_send'
        ? $workflowActions->invoiceEmailPreview($task, $payload)
        : [];
    $workflowInvoice = $variant === 'invoice_send'
        ? $workflowActions->preparedWorkflowInvoice($job)
        : null;
    $emailServiceEnabled = (bool) (($variant === 'invoice_send' ? $invoiceEmailPreview : $emailHandoffPreview)['email_service_enabled'] ?? true);
    if (! $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)) {
        $title = $variant === 'artwork_email' ? 'Complete Artwork Handoff' : 'Complete Purchase Order Handoff';
        $copy = 'Email sending is currently disabled. Choose the intended recipients, then complete the handoff and send the file manually.';
    } elseif (! $emailServiceEnabled && $variant === 'invoice_send') {
        $title = 'Complete Send Invoice';
        $copy = 'Email sending is currently disabled. Confirm the intended recipient and complete the task now; the invoice can be resent from the completed task later.';
    }
    $emailFallbackDocumentId = (int) ($emailHandoffPreview['document_id'] ?? 0);
    $emailFallbackDocument = $emailFallbackDocumentId > 0
        ? $job->documents->firstWhere('id', $emailFallbackDocumentId)
        : null;
    $emailFallbackAttachmentLabel = $variant === 'artwork_email' ? 'Artwork' : 'Purchase Order';
    $artworkHandoffCommentHistory = ($variant === 'artwork_email' && $job->relationLoaded('workflowEmailActivities'))
        ? collect($job->getRelation('workflowEmailActivities'))
            ->filter(fn ($activity) => (int) data_get($activity->meta, 'task_id', 0) === (int) $task->id)
            ->filter(fn ($activity) => in_array((string) $activity->event, [
                'job.artwork_emailed_to_order_team',
                'job.workflow_email_skipped',
            ], true))
            ->sortByDesc('id')
            ->map(fn ($activity) => [
                'id' => (int) $activity->id,
                'comment' => trim((string) data_get($activity->meta, 'customer_comment', '')),
                'created_at' => $activity->created_at,
            ])
            ->filter(fn (array $entry) => $entry['comment'] !== '')
            ->values()
        : collect();
    $revisionMentionUsers = collect($mentionUsers)->values();
    $selectedRevisionDocumentIds = collect($payload['revision_document_ids'] ?? [])
        ->map(fn($id) => (int) $id)
        ->filter(fn($id) => $id > 0)
        ->unique()
        ->values();


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
    $modalWide = in_array($variant, ['courier_label', 'shipment_info', 'invoice_send'], true);
    // Every artwork revision dialog uses the same compact prototype shell.
    // The copy/labels still vary by task, but layout and controls stay consistent.
    $isArtworkRevisionRequest = $step === 'revision';

    if ($step === 'sample') {
        $title = 'Is a Sample or Swatch Required?';
        $copy = 'The artwork is approved. Decide whether supplier sample approval is required before Production.';
    } elseif ($step === 'revision') {
        $title = $automationKey === 'ART_INTERNAL_REVIEW' ? 'Request Artwork Revision' : 'Client Revision Request';
        $copy = $automationKey === 'ART_INTERNAL_REVIEW'
            ? 'Select one or more artworks. Add the required change and any supporting files under each selected artwork.'
            : 'Select one or more artworks and record the client feedback for each file before restarting the approval cycle.';
    } elseif ($step === 'issue') {
        $title = $automationKey === 'QC_CHECK' ? 'Report QC Issue' : 'Report Production Issue';
        $copy = 'Describe the issue before notifying the supplier and blocking progression.';
    }
?>

<div class="ft-order-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-workflow-action-modal-'.e($task->id).'-'.e($step).''; ?>wire:key="order-workflow-action-modal-<?php echo e($task->id); ?>-<?php echo e($step); ?>">
    <section
        class="ft-order-task-document-modal ft-order-workflow-action-modal <?php echo e($isArtworkPreviewModal ? 'ft-order-workflow-action-modal--artwork-preview' : ($modalWide ? 'ft-order-workflow-action-modal--wide' : '')); ?> <?php echo e($usesStableFinanceValidation ? 'ft-order-workflow-action-modal--stable-finance-validation' : ''); ?> <?php echo e($isArtworkRevisionRequest ? 'ft-order-workflow-action-modal--artwork-revision-request' : ''); ?>"
        data-ft-feedback-scope="form"
        role="dialog"
        aria-modal="true"
        aria-labelledby="order-workflow-action-modal-title"
    >
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
                            <span>Select one or more files. Each selected artwork opens its own required change and supporting attachments.</span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedRevisionDocumentIds->isNotEmpty()): ?>
                            <em><?php echo e($selectedRevisionDocumentIds->count()); ?> selected</em>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ft-artwork-revision-selector-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $latestArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revisionDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $revisionDocumentId = (int) $revisionDocument->id;
                                $revisionExtension = strtoupper(pathinfo((string) $revisionDocument->name, PATHINFO_EXTENSION) ?: 'FILE');
                                $revisionSelected = $selectedRevisionDocumentIds->contains($revisionDocumentId);
                                $documentAttachments = collect($revisionAttachments[$revisionDocumentId] ?? $revisionAttachments[(string) $revisionDocumentId] ?? [])->filter()->values();
                                $documentAttachmentDetails = $documentAttachments->map(function ($file) {
                                    $name = $file->getClientOriginalName();
                                    return [
                                        'name' => $name,
                                        'type' => strtoupper((string) pathinfo($name, PATHINFO_EXTENSION)) ?: 'FILE',
                                        'size' => $file->getSize() >= 1048576
                                            ? number_format($file->getSize() / 1048576, 1).' MB'
                                            : number_format(max(1, (int) ceil($file->getSize() / 1024))).' KB',
                                    ];
                                });
                            ?>
                            <div
                                class="ft-artwork-revision-selector-item <?php echo e($revisionSelected ? 'is-selected' : ''); ?>"
                                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'artwork-revision-request-item-'.e($task->id).'-'.e($revisionDocumentId).''; ?>wire:key="artwork-revision-request-item-<?php echo e($task->id); ?>-<?php echo e($revisionDocumentId); ?>"
                            >
                                <div class="ft-artwork-revision-selector-summary">
                                    <label class="ft-artwork-revision-selector-choice">
                                        <input
                                            type="checkbox"
                                            wire:model.live="orderWorkflowActionPayload.revision_document_ids"
                                            value="<?php echo e($revisionDocumentId); ?>"
                                        >
                                        <span class="ft-artwork-revision-selector-check" aria-hidden="true">✓</span>
                                        <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $revisionExtension,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($revisionExtension),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $attributes = $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $component = $__componentOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
                                        <span class="ft-artwork-revision-selector-copy">
                                            <b title="<?php echo e($revisionDocument->name); ?>"><?php echo e($revisionDocument->name); ?></b>
                                            <small>Artwork</small>
                                        </span>
                                    </label>
                                    <a href="<?php echo e(route('documents.open', $revisionDocument)); ?>" target="_blank" rel="noopener">View</a>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revisionSelected): ?>
                                    <div class="ft-artwork-revision-item-details">
                                        <label class="ft-artwork-revision-item-change">
                                            <span><?php echo e($automationKey === 'ART_INTERNAL_REVIEW' ? 'Required change' : 'Client feedback'); ?> <b>*</b></span>
                                            <textarea
                                                wire:model="orderWorkflowActionRevisionComments.<?php echo e($revisionDocumentId); ?>"
                                                rows="3"
                                                autocomplete="off"
                                                placeholder="<?php echo e($automationKey === 'ART_INTERNAL_REVIEW' ? 'Describe the required artwork changes...' : 'Record the client feedback...'); ?>"
                                            ></textarea>
                                        </label>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionRevisionComments.'.$revisionDocumentId];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <div
                                            class="ft-artwork-revision-item-support"
                                            x-data="{ uploading: false, progress: 0 }"
                                            x-on:livewire-upload-start="uploading = true; progress = 0"
                                            x-on:livewire-upload-progress="progress = Math.max(0, Math.min(100, Number($event.detail.progress) || 0))"
                                            x-on:livewire-upload-finish="progress = 100; window.setTimeout(() => { uploading = false; progress = 0 }, 350)"
                                            x-on:livewire-upload-error="uploading = false; progress = 0"
                                            x-on:livewire-upload-cancel="uploading = false; progress = 0"
                                        >
                                            <div class="ft-artwork-revision-item-support-head">
                                                <div>
                                                    <strong>Supporting attachments <span>(optional)</span></strong>
                                                    <small>Add marked-up artwork, screenshots, or reference documents for this file.</small>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentAttachments->isNotEmpty()): ?>
                                                    <em><?php echo e($documentAttachments->count()); ?> file<?php echo e($documentAttachments->count() === 1 ? '' : 's'); ?></em>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentAttachmentDetails->isNotEmpty()): ?>
                                                <div class="ft-artwork-revision-support-files">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentAttachmentDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $supportFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <div class="ft-order-attachment-selected-file" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'artwork-revision-support-'.e($task->id).'-'.e($revisionDocumentId).'-'.e($index).'-'.e(md5($supportFile['name'])).''; ?>wire:key="artwork-revision-support-<?php echo e($task->id); ?>-<?php echo e($revisionDocumentId); ?>-<?php echo e($index); ?>-<?php echo e(md5($supportFile['name'])); ?>">
                                                            <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $supportFile['type'],'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supportFile['type']),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $attributes = $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $component = $__componentOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
                                                            <span class="ft-order-attachment-selected-copy">
                                                                <strong title="<?php echo e($supportFile['name']); ?>"><?php echo e($supportFile['name']); ?></strong>
                                                                <small><?php echo e($supportFile['type']); ?> · <?php echo e($supportFile['size']); ?> · Ready</small>
                                                            </span>
                                                            <button
                                                                type="button"
                                                                wire:click="removeOrderWorkflowActionRevisionAttachment(<?php echo e($revisionDocumentId); ?>, <?php echo e($index); ?>)"
                                                                wire:loading.attr="disabled"
                                                                wire:target="orderWorkflowActionRevisionAttachments.<?php echo e($revisionDocumentId); ?>,removeOrderWorkflowActionRevisionAttachment(<?php echo e($revisionDocumentId); ?>, <?php echo e($index); ?>)"
                                                            >Remove</button>
                                                        </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <label class="ft-order-task-document-dropzone ft-order-attachment-dropzone ft-artwork-revision-support-dropzone <?php echo e($documentAttachments->isNotEmpty() ? 'is-compact' : ''); ?>" data-file-dropzone>
                                                <input
                                                    type="file"
                                                    multiple
                                                    wire:model="orderWorkflowActionRevisionAttachments.<?php echo e($revisionDocumentId); ?>"
                                                    accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>"
                                                    aria-label="Choose supporting files for <?php echo e($revisionDocument->name); ?>"
                                                >
                                                <svg class="ft-order-attachment-upload-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 16l-4-4-4 4M12 12v9M20.4 17.5A5 5 0 0 0 18 8.2 7 7 0 0 0 4.3 10.8 4.5 4.5 0 0 0 5.5 19H7"/></svg>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentAttachments->isNotEmpty()): ?>
                                                    <strong>Choose supporting files</strong>
                                                    <b>Drag &amp; drop or <span>browse</span></b>
                                                <?php else: ?>
                                                    <strong>Drag &amp; drop files here</strong>
                                                    <b>or choose from your computer</b>
                                                    <span class="ft-order-attachment-browse">Browse files</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <small data-drop-status><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?> · Up to 10 files</small>
                                            </label>

                                            <div
                                                class="ft-create-attachment-progress"
                                                x-cloak
                                                x-show="uploading"
                                                x-transition.opacity.duration.120ms
                                                aria-live="polite"
                                            >
                                                <div class="ft-create-attachment-progress-meta">
                                                    <span>Uploading attachment<?php echo e($documentAttachments->count() === 1 ? '' : 's'); ?>...</span>
                                                    <b x-text="`${Math.round(progress)}%`">0%</b>
                                                </div>
                                                <div
                                                    class="ft-create-attachment-progress-track"
                                                    role="progressbar"
                                                    aria-label="Supporting attachment upload progress"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    x-bind:aria-valuenow="Math.round(progress)"
                                                >
                                                    <span x-bind:style="`width: ${progress}%`"></span>
                                                </div>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionRevisionAttachments.'.$revisionDocumentId];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionRevisionAttachments.'.$revisionDocumentId.'.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($automationKey === 'ART_INTERNAL_REVIEW' && $selectedRevisionDocumentIds->isNotEmpty()): ?>
                        <div class="ft-artwork-revision-visibility-note">
                            <span aria-hidden="true">▢</span>
                            Supporting attachments will be visible to the artwork assignee and other relevant team members.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
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
                <?php
                    $recipientOptions = collect($emailHandoffPreview['recipient_options'] ?? []);

                    $toEmail = trim((string) ($payload['to_email'] ?? ''));
                    if ($toEmail === '') {
                        $legacyToUser = $recipientOptions->firstWhere('id', (int) ($payload['to_user_id'] ?? 0));
                        $toEmail = trim((string) ($legacyToUser['email'] ?? ($payload['external_to_email'] ?? '')));
                    }

                    $matchedToUser = $recipientOptions->first(
                        fn($option) => mb_strtolower(trim((string) ($option['email'] ?? ''))) === mb_strtolower($toEmail)
                    );
                    $toQuery = mb_strtolower($toEmail);
                    $toSuggestions = $toQuery === '' || $matchedToUser
                        ? collect()
                        : $recipientOptions
                            ->filter(function($option) use ($toQuery) {
                                return str_contains(mb_strtolower((string) ($option['name'] ?? '')), $toQuery)
                                    || str_contains(mb_strtolower((string) ($option['email'] ?? '')), $toQuery);
                            })
                            ->take(6)
                            ->values();

                    $ccEmails = trim((string) ($payload['cc_emails'] ?? ''));
                    if ($ccEmails === '') {
                        $legacyCcUserEmails = collect($payload['cc_user_ids'] ?? [])
                            ->map(fn($id) => $recipientOptions->firstWhere('id', (int) $id)['email'] ?? null)
                            ->filter();
                        $legacyExternalCc = collect(preg_split('/[\s,;]+/', trim((string) ($payload['external_cc_emails'] ?? ''))) ?: [])->filter();
                        $ccEmails = $legacyCcUserEmails->concat($legacyExternalCc)->unique()->implode(', ');
                    }

                    $ccParts = collect(preg_split('/[,;]+/', $ccEmails) ?: [])
                        ->map(fn($value) => trim((string) $value));
                    $ccQuery = mb_strtolower((string) ($ccParts->last() ?? ''));
                    $ccPrefix = $ccParts->slice(0, max(0, $ccParts->count() - 1))->filter()->values();
                    $ccExactMatch = $recipientOptions->contains(
                        fn($option) => mb_strtolower(trim((string) ($option['email'] ?? ''))) === $ccQuery
                    );
                    $ccSuggestions = $ccQuery === '' || $ccExactMatch
                        ? collect()
                        : $recipientOptions
                            ->reject(fn($option) => $matchedToUser && (int) $option['id'] === (int) $matchedToUser['id'])
                            ->filter(function($option) use ($ccQuery) {
                                return str_contains(mb_strtolower((string) ($option['name'] ?? '')), $ccQuery)
                                    || str_contains(mb_strtolower((string) ($option['email'] ?? '')), $ccQuery);
                            })
                            ->reject(function($option) use ($ccPrefix) {
                                $email = mb_strtolower(trim((string) ($option['email'] ?? '')));
                                return $ccPrefix->contains(fn($value) => mb_strtolower((string) $value) === $email);
                            })
                            ->take(6)
                            ->values();
                    $hasCcRecipients = filled($ccEmails);
                    $recipientCount = (int) ($emailHandoffPreview['recipient_count'] ?? 0);
                    $toIsValidEmail = $toEmail !== '' && filter_var($toEmail, FILTER_VALIDATE_EMAIL);
                ?>

                <div class="ft-order-task-document-target">
                    <span class="ft-order-task-document-target-icon">PO</span>
                    <div>
                        <small>ATTACHMENT</small>
                        <strong><?php echo e($emailHandoffPreview['document_name'] ?? 'No Purchase Order uploaded'); ?></strong>
                        <span><?php echo e(filled($emailHandoffPreview['document_version'] ?? null) ? 'Version '.(int) $emailHandoffPreview['document_version'] : 'Upload the Purchase Order in the previous task first'); ?></span>
                    </div>
                    <em><?php echo e($recipientCount > 0 ? $recipientCount.' recipient'.($recipientCount === 1 ? '' : 's') : 'Choose recipient'); ?></em>
                </div>

                <section
                    class="ft-po-mail-recipients"
                    aria-label="Purchase Order email recipients"
                    x-data="{ ccOpen: <?php echo \Illuminate\Support\Js::from($hasCcRecipients)->toHtml() ?> }"
                >
                    <div class="ft-po-mail-row ft-po-mail-row--to">
                        <label for="po-to-email-<?php echo e($task->id); ?>">To</label>
                        <div class="ft-po-mail-row__control ft-po-mail-recipient-control">
                            <input
                                id="po-to-email-<?php echo e($task->id); ?>"
                                type="email"
                                wire:model.live.debounce.300ms="orderWorkflowActionPayload.to_email"
                                placeholder="Enter email or search Artwork Team"
                                autocomplete="off"
                                spellcheck="false"
                            >

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($toSuggestions->isNotEmpty()): ?>
                                <div class="ft-po-mail-suggestions" role="listbox" aria-label="Artwork Team suggestions">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $toSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <button
                                            type="button"
                                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'po-to-suggestion-'.e($task->id).'-'.e((int) $option['id']).''; ?>wire:key="po-to-suggestion-<?php echo e($task->id); ?>-<?php echo e((int) $option['id']); ?>"
                                            wire:click="$set('orderWorkflowActionPayload.to_email', <?php echo \Illuminate\Support\Js::from((string) $option['email'])->toHtml() ?>)"
                                            class="ft-po-mail-suggestion"
                                        >
                                            <span class="ft-po-mail-suggestion__avatar"><?php echo e(mb_strtoupper(mb_substr((string) ($option['name'] ?? $option['email']), 0, 1))); ?></span>
                                            <span><b><?php echo e($option['name']); ?></b><small><?php echo e($option['email']); ?></small></span>
                                        </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button
                            type="button"
                            class="ft-po-mail-cc-toggle"
                            x-on:click="ccOpen = !ccOpen"
                            x-bind:aria-expanded="ccOpen.toString()"
                            aria-controls="po-cc-fields-<?php echo e($task->id); ?>"
                        >Cc</button>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.to_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-po-mail-validation"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matchedToUser): ?>
                        <div class="ft-po-assignment-note ft-po-assignment-note--mail">
                            <span aria-hidden="true">✓</span>
                            <p><b><?php echo e($matchedToUser['name']); ?></b> will receive the Purchase Order by email and will be automatically assigned to <strong>Prepare &amp; Upload Artwork</strong>.</p>
                        </div>
                    <?php elseif($toIsValidEmail): ?>
                        <p class="ft-po-mail-help">This email will receive the Purchase Order. It does not match an Artwork Team user, so no internal artwork task will be auto-assigned.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div id="po-cc-fields-<?php echo e($task->id); ?>" class="ft-po-mail-cc" x-cloak x-show="ccOpen">
                        <div class="ft-po-mail-row ft-po-mail-row--cc">
                            <label for="po-cc-emails-<?php echo e($task->id); ?>">Cc</label>
                            <div class="ft-po-mail-row__control ft-po-mail-recipient-control">
                                <input
                                    id="po-cc-emails-<?php echo e($task->id); ?>"
                                    type="text"
                                    wire:model.live.debounce.300ms="orderWorkflowActionPayload.cc_emails"
                                    placeholder="Add email addresses, separated by commas"
                                    autocomplete="off"
                                    spellcheck="false"
                                >

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ccSuggestions->isNotEmpty()): ?>
                                    <div class="ft-po-mail-suggestions" role="listbox" aria-label="Artwork Team CC suggestions">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ccSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php
                                                $nextCcEmails = $ccPrefix
                                                    ->concat([(string) $option['email']])
                                                    ->unique(fn($email) => mb_strtolower(trim((string) $email)))
                                                    ->implode(', ');
                                            ?>
                                            <button
                                                type="button"
                                                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'po-cc-suggestion-'.e($task->id).'-'.e((int) $option['id']).''; ?>wire:key="po-cc-suggestion-<?php echo e($task->id); ?>-<?php echo e((int) $option['id']); ?>"
                                                wire:click="$set('orderWorkflowActionPayload.cc_emails', <?php echo \Illuminate\Support\Js::from($nextCcEmails)->toHtml() ?>)"
                                                class="ft-po-mail-suggestion"
                                            >
                                                <span class="ft-po-mail-suggestion__avatar"><?php echo e(mb_strtoupper(mb_substr((string) ($option['name'] ?? $option['email']), 0, 1))); ?></span>
                                                <span><b><?php echo e($option['name']); ?></b><small><?php echo e($option['email']); ?></small></span>
                                            </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.cc_emails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-po-mail-validation"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>

                <?php if (isset($component)) { $__componentOriginal70137674ee97e22e87c5d4188f3bbd58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.handoff-preview','data' => ['preview' => $emailHandoffPreview,'defaultSubject' => 'Purchase Order ready — '.$orderNumber,'emptyRecipientText' => 'Enter an email address in To to see the final email details.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.handoff-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emailHandoffPreview),'defaultSubject' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Purchase Order ready — '.$orderNumber),'emptyRecipientText' => 'Enter an email address in To to see the final email details.']); ?>
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
                                        <div class="ft-prototype-artwork-file"><span><?php echo e(strtoupper($previewExtension ?: 'FILE')); ?></span><strong><?php echo e($previewDocument->name); ?></strong><a href="<?php echo e(route('documents.open', $previewDocument)); ?>" target="_blank" rel="noopener">Open artwork</a></div>
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
                            <div><dt>Versions</dt><dd><?php echo e($artworkVersionLabel); ?></dd></div>
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
                                            <small>Artwork</small>
                                        </span>
                                        <em x-text="selectedArtworkId === <?php echo e((int) $doc->id); ?> ? 'Viewing' : 'Preview'">Preview</em>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($archivedArtworkDocuments->isNotEmpty()): ?>
                            <details class="ft-prototype-version-history">
                                <summary>Previous artwork versions</summary>
                                <div class="ft-prototype-version-list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $archivedArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div>
                                            <span class="ft-prototype-version-file">
                                                <strong><?php echo e($doc->name); ?></strong>
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
                    <?php
                        $artworkRecipientOptions = collect($emailHandoffPreview['recipient_options'] ?? []);
                        $artworkToEmails = trim((string) ($payload['to_emails'] ?? ''));
                        if ($artworkToEmails === '') {
                            $artworkToEmails = trim((string) ($payload['to_email'] ?? ''));
                        }

                        $artworkToParts = collect(preg_split('/[,;]+/', $artworkToEmails) ?: [])
                            ->map(fn($value) => trim((string) $value));
                        $artworkToQuery = mb_strtolower((string) ($artworkToParts->last() ?? ''));
                        $artworkToPrefix = $artworkToParts
                            ->slice(0, max(0, $artworkToParts->count() - 1))
                            ->filter()
                            ->values();
                        $artworkToExactMatch = $artworkRecipientOptions->contains(
                            fn($option) => mb_strtolower(trim((string) ($option['email'] ?? ''))) === $artworkToQuery
                        );
                        $artworkToSuggestions = $artworkToQuery === '' || $artworkToExactMatch
                            ? collect()
                            : $artworkRecipientOptions
                                ->filter(function($option) use ($artworkToQuery) {
                                    return str_contains(mb_strtolower((string) ($option['name'] ?? '')), $artworkToQuery)
                                        || str_contains(mb_strtolower((string) ($option['email'] ?? '')), $artworkToQuery);
                                })
                                ->reject(function($option) use ($artworkToPrefix) {
                                    $email = mb_strtolower(trim((string) ($option['email'] ?? '')));
                                    return $artworkToPrefix->contains(fn($value) => mb_strtolower((string) $value) === $email);
                                })
                                ->take(6)
                                ->values();
                    ?>

                    <section class="ft-po-mail-recipients ft-artwork-mail-recipients" aria-label="Artwork email recipients">
                        <div class="ft-po-mail-row ft-po-mail-row--single">
                            <label for="artwork-to-emails-<?php echo e($task->id); ?>">To</label>
                            <div class="ft-po-mail-row__control ft-po-mail-recipient-control">
                                <input
                                    id="artwork-to-emails-<?php echo e($task->id); ?>"
                                    type="text"
                                    wire:model.live.debounce.300ms="orderWorkflowActionPayload.to_emails"
                                    placeholder="Enter email or search users"
                                    autocomplete="off"
                                    spellcheck="false"
                                >

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($artworkToSuggestions->isNotEmpty()): ?>
                                    <div class="ft-po-mail-suggestions" role="listbox" aria-label="System user suggestions">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $artworkToSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php
                                                $nextArtworkToEmails = $artworkToPrefix
                                                    ->concat([(string) $option['email']])
                                                    ->unique(fn($email) => mb_strtolower(trim((string) $email)))
                                                    ->implode(', ');
                                            ?>
                                            <button
                                                type="button"
                                                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'artwork-to-suggestion-'.e($task->id).'-'.e((int) $option['id']).''; ?>wire:key="artwork-to-suggestion-<?php echo e($task->id); ?>-<?php echo e((int) $option['id']); ?>"
                                                wire:click="$set('orderWorkflowActionPayload.to_emails', <?php echo \Illuminate\Support\Js::from($nextArtworkToEmails)->toHtml() ?>)"
                                                class="ft-po-mail-suggestion"
                                            >
                                                <span class="ft-po-mail-suggestion__avatar"><?php echo e(mb_strtoupper(mb_substr((string) ($option['name'] ?? $option['email']), 0, 1))); ?></span>
                                                <span><b><?php echo e($option['name']); ?></b><small><?php echo e($option['email']); ?></small></span>
                                            </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.to_emails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-po-mail-validation"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($artworkHandoffCommentHistory->isNotEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal39a20eb4ceedbe5e138853d2dc8a5785 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39a20eb4ceedbe5e138853d2dc8a5785 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.artwork-handoff-comment-history','data' => ['history' => $artworkHandoffCommentHistory,'label' => 'Previous customer comments']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.artwork-handoff-comment-history'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['history' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($artworkHandoffCommentHistory),'label' => 'Previous customer comments']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39a20eb4ceedbe5e138853d2dc8a5785)): ?>
<?php $attributes = $__attributesOriginal39a20eb4ceedbe5e138853d2dc8a5785; ?>
<?php unset($__attributesOriginal39a20eb4ceedbe5e138853d2dc8a5785); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39a20eb4ceedbe5e138853d2dc8a5785)): ?>
<?php $component = $__componentOriginal39a20eb4ceedbe5e138853d2dc8a5785; ?>
<?php unset($__componentOriginal39a20eb4ceedbe5e138853d2dc8a5785); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <label class="ft-artwork-handoff-comment" for="artwork-customer-comment-<?php echo e($task->id); ?>">
                        <span>Comment to customer <em>(optional)</em></span>
                        <textarea
                            id="artwork-customer-comment-<?php echo e($task->id); ?>"
                            wire:model.live.debounce.300ms="orderWorkflowActionPayload.customer_comment"
                            rows="3"
                            maxlength="2000"
                            placeholder="Write your message or any important note for the customer..."
                        ></textarea>
                    </label>

                    <?php if (isset($component)) { $__componentOriginal70137674ee97e22e87c5d4188f3bbd58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.handoff-preview','data' => ['preview' => $emailHandoffPreview,'defaultSubject' => 'Artwork ready — '.$orderNumber,'emptyRecipientText' => 'Enter one or more email addresses in To.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.handoff-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emailHandoffPreview),'defaultSubject' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Artwork ready — '.$orderNumber),'emptyRecipientText' => 'Enter one or more email addresses in To.']); ?>
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
                <p class="ft-prototype-modal-copy">Choose the decision received from <?php echo e($clientName); ?> for the current artwork (<?php echo e($artworkVersionLabel); ?>).</p>
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
                <?php if (isset($component)) { $__componentOriginala71941c0208bdab3d16b9d1f53b9e592 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala71941c0208bdab3d16b9d1f53b9e592 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.update-details-form','data' => ['job' => $job,'payload' => $payload]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.update-details-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'payload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payload)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala71941c0208bdab3d16b9d1f53b9e592)): ?>
<?php $attributes = $__attributesOriginala71941c0208bdab3d16b9d1f53b9e592; ?>
<?php unset($__attributesOriginala71941c0208bdab3d16b9d1f53b9e592); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala71941c0208bdab3d16b9d1f53b9e592)): ?>
<?php $component = $__componentOriginala71941c0208bdab3d16b9d1f53b9e592; ?>
<?php unset($__componentOriginala71941c0208bdab3d16b9d1f53b9e592); ?>
<?php endif; ?>
            <?php elseif($variant === 'shipment_tracking'): ?>
                <?php
                    $shipmentCourierOptions = collect($payload['courier_options'] ?? []);
                ?>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field">
                        <span>Courier</span>
                        <select wire:model="orderWorkflowActionPayload.carrier">
                            <option value="">Select courier</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shipmentCourierOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $courierOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($courierOption['value']); ?>"><?php echo e($courierOption['label']); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.carrier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentLabel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <label class="ft-prototype-field">
                        <span>Tracking number</span>
                        <input wire:model="orderWorkflowActionPayload.tracking_number" placeholder="Enter tracking number">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.tracking_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                </div>
                <div class="ft-prototype-email-preview">
                    <b>Shipment task:</b> Add tracking number &amp; print courier label<br>
                    <span>Saving the courier and tracking number completes Task 5.2 and unlocks Dispatch shipment.</span>
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
                    <label class="ft-prototype-field"><span>Invoice number</span><input value="<?php echo e($payload['invoice_number'] ?? ''); ?>" readonly aria-readonly="true" title="Generated automatically"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.invoice_number'];
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
                <?php
                    $invoiceRecipientOptions = collect($invoiceEmailPreview['recipient_options'] ?? []);
                    $invoiceToEmail = trim((string) ($payload['to_email'] ?? ''));
                    $invoiceMatchedToUser = $invoiceRecipientOptions->first(
                        fn($option) => mb_strtolower(trim((string) ($option['email'] ?? ''))) === mb_strtolower($invoiceToEmail)
                    );
                    $invoiceToQuery = mb_strtolower($invoiceToEmail);
                    $invoiceToSuggestions = $invoiceToQuery === '' || $invoiceMatchedToUser
                        ? collect()
                        : $invoiceRecipientOptions
                            ->filter(function($option) use ($invoiceToQuery) {
                                return str_contains(mb_strtolower((string) ($option['name'] ?? '')), $invoiceToQuery)
                                    || str_contains(mb_strtolower((string) ($option['email'] ?? '')), $invoiceToQuery);
                            })
                            ->take(6)
                            ->values();
                    $invoiceToIsValidEmail = $invoiceToEmail !== '' && filter_var($invoiceToEmail, FILTER_VALIDATE_EMAIL);
                    $invoiceNoSystemMatch = $invoiceToQuery !== ''
                        && ! $invoiceMatchedToUser
                        && $invoiceToSuggestions->isEmpty()
                        && ! $invoiceToIsValidEmail;

                    $invoiceCcEmails = trim((string) ($payload['cc_emails'] ?? ''));
                    $invoiceCcParts = collect(preg_split('/[,;]+/', $invoiceCcEmails) ?: [])
                        ->map(fn($value) => trim((string) $value));
                    $invoiceCcQuery = mb_strtolower((string) ($invoiceCcParts->last() ?? ''));
                    $invoiceCcPrefix = $invoiceCcParts
                        ->slice(0, max(0, $invoiceCcParts->count() - 1))
                        ->filter()
                        ->values();
                    $invoiceCcExactMatch = $invoiceRecipientOptions->contains(
                        fn($option) => mb_strtolower(trim((string) ($option['email'] ?? ''))) === $invoiceCcQuery
                    );
                    $invoiceCcSuggestions = $invoiceCcQuery === '' || $invoiceCcExactMatch
                        ? collect()
                        : $invoiceRecipientOptions
                            ->reject(fn($option) => $invoiceMatchedToUser && (int) $option['id'] === (int) $invoiceMatchedToUser['id'])
                            ->filter(function($option) use ($invoiceCcQuery) {
                                return str_contains(mb_strtolower((string) ($option['name'] ?? '')), $invoiceCcQuery)
                                    || str_contains(mb_strtolower((string) ($option['email'] ?? '')), $invoiceCcQuery);
                            })
                            ->reject(function($option) use ($invoiceCcPrefix) {
                                $email = mb_strtolower(trim((string) ($option['email'] ?? '')));
                                return $invoiceCcPrefix->contains(fn($value) => mb_strtolower((string) $value) === $email);
                            })
                            ->take(6)
                            ->values();
                ?>
                <div class="ft-invoice-send-workspace">
                    
                        <section
                            class="ft-invoice-send-document"
                            aria-label="Generated invoice"
                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'invoice-send-document-'.e($task->id).'-'.e($workflowInvoice?->id ?? 0).''; ?>wire:key="invoice-send-document-<?php echo e($task->id); ?>-<?php echo e($workflowInvoice?->id ?? 0); ?>"
                            wire:ignore
                        >
                        <header class="ft-invoice-send-document__head">
                            <div>
                                <small>GENERATED INVOICE</small>
                                <strong><?php echo e($workflowInvoice?->invoice_number ?: ($payload['invoice_number'] ?? 'Invoice')); ?></strong>
                                <span>This exact PDF will be attached to the client email.</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowInvoice): ?>
                                <div class="ft-invoice-send-document__actions">
                                    <a href="<?php echo e(route('invoices.pdf.open', $workflowInvoice)); ?>" target="_blank" rel="noopener">Open invoice</a>
                                    <a href="<?php echo e(route('invoices.pdf.download', $workflowInvoice)); ?>">Download PDF</a>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </header>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowInvoice): ?>
                            <div class="ft-invoice-send-document__summary">
                                <span><b><?php echo e($workflowInvoice->currency); ?> <?php echo e(number_format((float) $workflowInvoice->total, 2)); ?></b><small>Amount due</small></span>
                                <span><b><?php echo e($workflowInvoice->issue_date?->format('M j, Y') ?: '—'); ?></b><small>Invoice date</small></span>
                                <span><b><?php echo e($workflowInvoice->due_date?->format('M j, Y') ?: '—'); ?></b><small>Due date</small></span>
                            </div>
                            <div class="ft-invoice-send-pdf-preview">
                                <div class="ft-invoice-send-pdf-preview__bar"><span>PDF</span><b><?php echo e($workflowInvoice->pdf_name ?: $workflowInvoice->invoice_number.'.pdf'); ?></b></div>
                                <iframe title="Generated invoice PDF preview" src="<?php echo e(route('invoices.pdf.open', $workflowInvoice)); ?>"></iframe>
                            </div>
                        <?php else: ?>
                            <div class="ft-order-email-preview-unavailable">The generated invoice PDF could not be found. Return to Prepare Invoice and generate it before sending.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>

                    <section class="ft-invoice-send-compose" aria-label="Invoice email compose and preview">
                        <div class="ft-invoice-send-compose__title">
                            <small>EMAIL DELIVERY</small>
                            <strong>Review recipients and message</strong>
                            <span>Change the billing email if needed, then verify the exact message before sending.</span>
                        </div>

                        <section
                            class="ft-po-mail-recipients ft-invoice-mail-recipients"
                            aria-label="Invoice email recipients"
                            x-data="{ ccOpen: <?php echo \Illuminate\Support\Js::from(filled($payload['cc_emails'] ?? ''))->toHtml() ?> }"
                        >
                            <div class="ft-po-mail-row ft-po-mail-row--to">
                                <label for="invoice-to-email-<?php echo e($task->id); ?>">To</label>
                                <div class="ft-po-mail-row__control ft-po-mail-recipient-control">
                                    <input
                                        id="invoice-to-email-<?php echo e($task->id); ?>"
                                        type="text"
                                        wire:model.live.debounce.300ms="orderWorkflowActionPayload.to_email"
                                        placeholder="Enter email or search system users"
                                        autocomplete="off"
                                        spellcheck="false"
                                    >

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoiceToSuggestions->isNotEmpty()): ?>
                                        <div class="ft-po-mail-suggestions" role="listbox" aria-label="System user suggestions">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoiceToSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <button
                                                    type="button"
                                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'invoice-to-suggestion-'.e($task->id).'-'.e((int) $option['id']).''; ?>wire:key="invoice-to-suggestion-<?php echo e($task->id); ?>-<?php echo e((int) $option['id']); ?>"
                                                    wire:click="$set('orderWorkflowActionPayload.to_email', <?php echo \Illuminate\Support\Js::from((string) $option['email'])->toHtml() ?>)"
                                                    class="ft-po-mail-suggestion"
                                                >
                                                    <span class="ft-po-mail-suggestion__avatar"><?php echo e(mb_strtoupper(mb_substr((string) ($option['name'] ?? $option['email']), 0, 1))); ?></span>
                                                    <span><b><?php echo e($option['name']); ?></b><small><?php echo e($option['email']); ?></small></span>
                                                </button>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <button
                                    type="button"
                                    class="ft-po-mail-cc-toggle"
                                    x-on:click="ccOpen = !ccOpen"
                                    x-bind:aria-expanded="ccOpen.toString()"
                                    aria-controls="invoice-cc-fields-<?php echo e($task->id); ?>"
                                >Cc</button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.to_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-po-mail-validation"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoiceMatchedToUser): ?>
                                <div class="ft-po-assignment-note ft-po-assignment-note--mail ft-invoice-system-user-note">
                                    <span aria-hidden="true">✓</span>
                                    <p><b><?php echo e($invoiceMatchedToUser['name']); ?></b> is an active FlowTrack user. The invoice will be sent to <strong><?php echo e($invoiceMatchedToUser['email']); ?></strong>.</p>
                                </div>
                            <?php elseif($invoiceToIsValidEmail): ?>
                                <p class="ft-po-mail-help">This address does not match an active FlowTrack user. It will be sent as an external email recipient.</p>
                            <?php elseif($invoiceNoSystemMatch): ?>
                                <p class="ft-po-mail-help ft-invoice-user-search-empty">No active system user matches “<?php echo e($invoiceToEmail); ?>”. Choose a suggested user or enter a complete external email address.</p>
                            <?php else: ?>
                                <p class="ft-po-mail-help">Billing contact: <?php echo e($workflowInvoice?->billing_contact_name ?: $clientName); ?> · You can search active system users by name or email.</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div id="invoice-cc-fields-<?php echo e($task->id); ?>" class="ft-po-mail-cc" x-cloak x-show="ccOpen">
                                <div class="ft-po-mail-row ft-po-mail-row--cc">
                                    <label for="invoice-cc-emails-<?php echo e($task->id); ?>">Cc</label>
                                    <div class="ft-po-mail-row__control ft-po-mail-recipient-control">
                                        <input
                                            id="invoice-cc-emails-<?php echo e($task->id); ?>"
                                            type="text"
                                            wire:model.live.debounce.300ms="orderWorkflowActionPayload.cc_emails"
                                            placeholder="Add email or search system users"
                                            autocomplete="off"
                                            spellcheck="false"
                                        >

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoiceCcSuggestions->isNotEmpty()): ?>
                                            <div class="ft-po-mail-suggestions" role="listbox" aria-label="System user CC suggestions">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoiceCcSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <?php
                                                        $nextInvoiceCcEmails = $invoiceCcPrefix
                                                            ->concat([(string) $option['email']])
                                                            ->unique(fn($email) => mb_strtolower(trim((string) $email)))
                                                            ->implode(', ');
                                                    ?>
                                                    <button
                                                        type="button"
                                                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'invoice-cc-suggestion-'.e($task->id).'-'.e((int) $option['id']).''; ?>wire:key="invoice-cc-suggestion-<?php echo e($task->id); ?>-<?php echo e((int) $option['id']); ?>"
                                                        wire:click="$set('orderWorkflowActionPayload.cc_emails', <?php echo \Illuminate\Support\Js::from($nextInvoiceCcEmails)->toHtml() ?>)"
                                                        class="ft-po-mail-suggestion"
                                                    >
                                                        <span class="ft-po-mail-suggestion__avatar"><?php echo e(mb_strtoupper(mb_substr((string) ($option['name'] ?? $option['email']), 0, 1))); ?></span>
                                                        <span><b><?php echo e($option['name']); ?></b><small><?php echo e($option['email']); ?></small></span>
                                                    </button>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.cc_emails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-po-mail-validation"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </section>

                        <?php if (isset($component)) { $__componentOriginal70137674ee97e22e87c5d4188f3bbd58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal70137674ee97e22e87c5d4188f3bbd58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.handoff-preview','data' => ['preview' => $invoiceEmailPreview,'defaultSubject' => 'Invoice '.($payload['invoice_number'] ?? '').' — '.$orderNumber,'emptyRecipientText' => 'Enter the client billing email in To before sending this invoice.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.handoff-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceEmailPreview),'defaultSubject' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Invoice '.($payload['invoice_number'] ?? '').' — '.$orderNumber),'emptyRecipientText' => 'Enter the client billing email in To before sending this invoice.']); ?>
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
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error ft-invoice-send-email-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>
                </div>
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
            $usesShipmentFooter = $step === 'main' && $variant === 'shipment_info';
            $editingCompletedShipmentInformation = $usesShipmentFooter
                && \App\Support\OrderDetailPresenter::isCompletedTask($task);
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usesShipmentFooter): ?>
            <footer class="ft-order-task-document-modal-actions ft-shipment-modal-footer">
                <button type="button" class="ft-shipment-modal-reset" wire:click="resetShipmentActionDetails">Reset changes</button>
                <div class="ft-shipment-modal-footer__actions">
                    <div>
                        <button type="button" class="secondary" wire:click="closeOrderWorkflowAction">Cancel</button>
                        <button type="button" class="primary" wire:click="submitOrderWorkflowAction('confirm')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction"><?php echo e($editingCompletedShipmentInformation ? 'Save changes' : 'Save & complete task'); ?></button>
                    </div>
                    <small><?php echo e($editingCompletedShipmentInformation ? 'The shipment task stays completed; only the latest shipment details are updated.' : 'Saving unlocks Add tracking number & print courier label.'); ?></small>
                </div>
            </footer>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($usesInlineWorkflowActions || $usesShipmentFooter || $step === 'sample')): ?>
            <footer class="ft-order-task-document-modal-actions ft-order-workflow-action-buttons">
                <button type="button" class="secondary" wire:click="closeOrderWorkflowAction">Cancel</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'revision'): ?>
                    <button type="button" class="danger ft-artwork-revision-submit" wire:click="submitOrderWorkflowAction('revise')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction,orderWorkflowActionRevisionAttachments"><?php echo e($automationKey === 'ART_INTERNAL_REVIEW' ? 'Submit Revision' : 'Activate Revision Task'); ?></button>
                <?php elseif($step === 'issue'): ?>
                    <button type="button" class="danger" wire:click="submitOrderWorkflowAction('issue')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">Report Issue</button>
                <?php elseif($emailFallback && $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)): ?>
                    <button type="button" class="secondary" wire:click="submitOrderWorkflowAction('confirm')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">Try email again</button>
                    <button type="button" class="primary" wire:click="completeOrderWorkflowEmailTaskAfterFailure" wire:loading.attr="disabled" wire:target="completeOrderWorkflowEmailTaskAfterFailure">Complete task</button>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $choices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $decision => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $actionLabel = ! $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email', 'invoice_send'], true)
                                ? 'Complete without email'
                                : $label;
                            $actionDisabled = $variant === 'invoice_send' && ! $workflowInvoice;
                        ?>
                        <button type="button" class="<?php echo e(in_array($decision, ['revise','issue'], true) ? 'danger' : 'primary'); ?>" wire:click="submitOrderWorkflowAction('<?php echo e($decision); ?>')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction" <?php if($actionDisabled): echo 'disabled'; endif; ?>><?php echo e($actionLabel); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </footer>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/workflow-action-modal.blade.php ENDPATH**/ ?>