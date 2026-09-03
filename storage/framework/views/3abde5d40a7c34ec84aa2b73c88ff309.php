<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'task', 'availableDocuments' => collect(), 'source' => 'upload', 'upload' => null, 'revisionUpload' => null, 'stagedUploads' => [], 'stagedRevisionUploads' => [], 'existingDocumentId' => null, 'artworkRevision' => [], 'revisionDocumentIds' => [], 'context' => []]));

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

foreach (array_filter((['job', 'task', 'availableDocuments' => collect(), 'source' => 'upload', 'upload' => null, 'revisionUpload' => null, 'stagedUploads' => [], 'stagedRevisionUploads' => [], 'existingDocumentId' => null, 'artworkRevision' => [], 'revisionDocumentIds' => [], 'context' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $canUpload = (bool) ($context['canUploadDocument'] ?? false);
    $canLink = (bool) ($context['canLinkDocument'] ?? false);
    $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
    $automationKey = $workflowActions->automationKey($task);
    $prototypeUpload = in_array($automationKey, ['NEW_UPLOAD_PO', 'ART_PREPARE_UPLOAD', 'ART_SAMPLE_APPROVAL'], true);
    $chunkedArtworkUpload = in_array($automationKey, ['ART_PREPARE_UPLOAD', 'ART_SAMPLE_APPROVAL'], true);
    $allowMultipleUploads = $chunkedArtworkUpload
        || $automationKey === 'NEW_UPLOAD_PO'
        || (bool) ($task->setupTemplate?->allow_multiple_documents ?? false);
    $hasExistingEvidence = $task->relationLoaded('documents') ? $task->documents->isNotEmpty() : false;
    $artworkRevisionActive = $automationKey === 'ART_PREPARE_UPLOAD' && (bool) ($artworkRevision['active'] ?? false);
    $allRevisionCandidates = $artworkRevisionActive
        ? collect($artworkRevision['documents'] ?? [])->merge(collect($artworkRevision['retained_documents'] ?? []))->unique('id')->sortBy('id')->values()
        : collect();
    $selectedRevisionIds = collect($revisionDocumentIds ?: ($artworkRevision['document_ids'] ?? []))
        ->map(fn($id) => (int) $id)
        ->filter(fn($id) => $id > 0)
        ->unique()
        ->values();
    $revisionDocuments = $artworkRevisionActive
        ? $allRevisionCandidates->filter(fn($document) => $selectedRevisionIds->contains((int) $document->id))->values()
        : collect();
    $retainedArtworkDocuments = $artworkRevisionActive
        ? $allRevisionCandidates->reject(fn($document) => $selectedRevisionIds->contains((int) $document->id))->values()
        : collect();
    $revisionCount = $revisionDocuments->count();
    // Normal Artwork uploads can still be multi-file. Selective revision replacements
    // are intentionally chosen one-at-a-time under the exact source artwork.
    $inputAllowsMultiple = $allowMultipleUploads;
    $uploadCopyPlural = $allowMultipleUploads;
    $revisionItemsByDocumentId = collect($artworkRevision['items'] ?? [])->mapWithKeys(function ($item) {
        $id = (int) data_get($item, 'document_id', 0);
        return $id > 0 ? [$id => (array) $item] : [];
    });

    $prototypeConfig = match ($automationKey) {
        'NEW_UPLOAD_PO' => [
            'title' => $hasExistingEvidence ? 'Add other documents' : 'Upload Purchase Order',
            'label' => $hasExistingEvidence ? 'Other purchase order documents' : 'Purchase order documents',
            'copy' => $hasExistingEvidence
                ? 'Add more documents to the completed Purchase Order task.'
                : 'Upload the client purchase order and any supporting documents to begin processing this Order.',
            'hint' => \App\Support\AttachmentUpload::helperText(20).' · Up to 10 files',
            'button' => $hasExistingEvidence ? 'Add other documents' : 'Upload Purchase Order',
        ],
        'ART_PREPARE_UPLOAD' => [
            'title' => $artworkRevisionActive ? 'Upload Revised Artwork' : ($hasExistingEvidence ? 'Upload Revised Artwork' : 'Upload Artwork'),
            'label' => $artworkRevisionActive ? 'Replacement artwork files' : ($hasExistingEvidence ? 'Revised artwork files' : 'Artwork files'),
            'copy' => $artworkRevisionActive
                ? ($revisionCount > 0
                    ? 'Upload each replacement directly under the artwork it replaces. '.$revisionCount.' artwork file'.($revisionCount === 1 ? ' is' : 's are').' waiting for replacement. Unselected artwork remains unchanged automatically.'
                    : 'Upload one replacement directly under each artwork selected in the revision request.')
                : ($hasExistingEvidence
                    ? 'Upload up to 50 corrected artwork files as one revision. The previous version remains in Order history.'
                    : 'Upload up to 50 artwork files together for internal review.'),
            'hint' => $artworkRevisionActive
                ? \App\Support\AttachmentUpload::helperText(400).' · '.$revisionCount.' replacement'.($revisionCount === 1 ? '' : 's').' required'
                : \App\Support\AttachmentUpload::helperText(400).' · Up to 50 files',
            'button' => $artworkRevisionActive ? 'Upload Revised Artwork' : ($hasExistingEvidence ? 'Upload Revised Artwork' : 'Upload Artwork'),
        ],
        'ART_SAMPLE_APPROVAL' => [
            'title' => 'Upload Sample Approval',
            'label' => 'Signed sample approval',
            'copy' => 'Attach the client sample/swatch approval to continue to Production.',
            'hint' => \App\Support\AttachmentUpload::helperText(400).' · Up to 50 files',
            'button' => 'Upload Sample Approval',
        ],
        default => [
            'title' => 'Add document to task',
            'label' => 'Task document',
            'copy' => 'Upload a new file or link an existing client document.',
            'hint' => \App\Support\AttachmentUpload::helperText(20),
            'button' => 'Add document',
        ],
    };
    $effectiveUpload = $artworkRevisionActive ? $revisionUpload : $upload;
    if ($chunkedArtworkUpload) {
        $selectedUploads = $artworkRevisionActive
            ? collect($stagedRevisionUploads)->filter()
            : collect($stagedUploads)->filter()->values();
        $selectedUploadCount = $selectedUploads->count();
        $selectedUploadDetails = $artworkRevisionActive ? collect() : $selectedUploads->map(function ($file) {
            $name = (string) data_get($file, 'name', 'Artwork file');
            $size = (int) data_get($file, 'size', 0);
            return [
                'name' => $name,
                'type' => (string) (data_get($file, 'type') ?: (strtoupper((string) pathinfo($name, PATHINFO_EXTENSION)) ?: 'FILE')),
                'size' => $size >= 1048576
                    ? number_format($size / 1048576, 1).' MB'
                    : number_format(max(1, (int) ceil($size / 1024))).' KB',
            ];
        });
    } else {
        $selectedUploads = collect(is_array($effectiveUpload) ? $effectiveUpload : ($effectiveUpload ? [$effectiveUpload] : []))->filter()->values();
        $selectedUploadCount = $selectedUploads->count();
        $selectedUploadDetails = $selectedUploads->map(function ($file) {
            $name = $file->getClientOriginalName();

            return [
                'name' => $name,
                'type' => strtoupper((string) pathinfo($name, PATHINFO_EXTENSION)) ?: 'FILE',
                'size' => $file->getSize() >= 1048576
                    ? number_format($file->getSize() / 1048576, 1).' MB'
                    : number_format(max(1, (int) ceil($file->getSize() / 1024))).' KB',
            ];
        });
    }
?>
<div class="ft-order-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-document-modal-'.e($task->id).''; ?>wire:key="order-task-document-modal-<?php echo e($task->id); ?>" wire:click.self="closeOverviewTaskDocumentModal">
    <section class="ft-order-task-document-modal ft-order-attachment-upload-modal <?php echo e($prototypeUpload ? 'ft-order-prototype-upload-modal' : ''); ?> <?php echo e($artworkRevisionActive ? 'ft-order-prototype-upload-modal--artwork-revision' : ''); ?>" data-ft-feedback-scope="form" data-artwork-upload-modal-task="<?php echo e($task->id); ?>" role="dialog" aria-modal="true" aria-labelledby="order-task-document-modal-title">
        <header class="ft-order-task-document-modal-head">
            <div>
                <h2 id="order-task-document-modal-title"><?php echo e($prototypeConfig['title']); ?></h2>
                <p><?php echo e($prototypeConfig['copy']); ?></p>
            </div>
            <button type="button" wire:click="closeOverviewTaskDocumentModal" aria-label="Close">×</button>
        </header>
        <div class="ft-order-task-document-modal-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($prototypeUpload)): ?>
                <div class="ft-order-task-document-target"><span class="ft-order-task-document-target-icon">▣</span><div><small>ATTACHING TO</small><strong><?php echo e($task->title); ?></strong><span><?php echo e($task->task_number ?: 'TASK-'.str_pad((string) $task->id, 5, '0', STR_PAD_LEFT)); ?> · <?php echo e($task->phase?->name ?? 'Order Taskflow'); ?></span><span><b>Order Reference:</b> <?php echo e($job->order_number ?: '—'); ?></span></div><em>Task selected</em></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$prototypeUpload || !$canUpload): ?>
                <div class="ft-order-task-document-source-tabs">
                    <button type="button" class="<?php echo e($source === 'upload' ? 'active' : ''); ?>" wire:click="setOverviewTaskDocumentSource('upload')" <?php if(!$canUpload): echo 'disabled'; endif; ?>>Upload new</button>
                    <button type="button" class="<?php echo e($source === 'existing' ? 'active' : ''); ?>" wire:click="setOverviewTaskDocumentSource('existing')" <?php if(!$canLink): echo 'disabled'; endif; ?>>Choose existing</button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($source === 'upload' && $canUpload): ?>
                
                <div
                    class="ft-order-popup-field"
                    x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true; progress = 0"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    x-on:livewire-upload-finish="progress = 100; window.setTimeout(() => { uploading = false; progress = 0 }, 250)"
                    x-on:livewire-upload-error="uploading = false; progress = 0"
                    x-on:livewire-upload-cancel="uploading = false; progress = 0"
                    x-on:flowtrack-artwork-upload-start="uploading = true; progress = 0"
                    x-on:flowtrack-artwork-upload-progress="progress = Math.max(0, Math.min(100, Number($event.detail.progress) || 0))"
                    x-on:flowtrack-artwork-upload-finish="progress = 100; window.setTimeout(() => { uploading = false; progress = 0 }, 250)"
                    x-on:flowtrack-artwork-upload-error="uploading = false; progress = 0"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($artworkRevisionActive): ?>
                        <div class="ft-artwork-revision-upload-plan">
                            <div class="ft-artwork-revision-upload-plan-head">
                                <div>
                                    <strong>Artwork selected for revision</strong>
                                    <span>Upload one replacement under each artwork below. Files not listed here remain unchanged.</span>
                                </div>
                                <em><?php echo e($selectedUploadCount); ?> / <?php echo e($revisionCount); ?> ready</em>
                            </div>

                            <div class="ft-artwork-revision-replacement-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $revisionDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revisionCandidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $revisionDocumentId = (int) $revisionCandidate->id;
                                        $candidateExtension = strtoupper(pathinfo((string) $revisionCandidate->name, PATHINFO_EXTENSION) ?: 'FILE');
                                        $revisionItem = (array) ($revisionItemsByDocumentId[$revisionDocumentId] ?? []);
                                        $revisionInstruction = trim((string) data_get($revisionItem, 'comment', ''));
                                        $replacementFile = $revisionUpload[$revisionDocumentId] ?? $revisionUpload[(string) $revisionDocumentId] ?? null;
                                        $stagedReplacement = $stagedRevisionUploads[$revisionDocumentId] ?? $stagedRevisionUploads[(string) $revisionDocumentId] ?? null;
                                        $replacementDetail = null;
                                        if ($stagedReplacement) {
                                            $replacementName = (string) data_get($stagedReplacement, 'name', 'Artwork file');
                                            $replacementSize = (int) data_get($stagedReplacement, 'size', 0);
                                            $replacementDetail = [
                                                'name' => $replacementName,
                                                'type' => (string) (data_get($stagedReplacement, 'type') ?: (strtoupper((string) pathinfo($replacementName, PATHINFO_EXTENSION)) ?: 'FILE')),
                                                'size' => $replacementSize >= 1048576
                                                    ? number_format($replacementSize / 1048576, 1).' MB'
                                                    : number_format(max(1, (int) ceil($replacementSize / 1024))).' KB',
                                            ];
                                        } elseif ($replacementFile) {
                                            $replacementName = $replacementFile->getClientOriginalName();
                                            $replacementDetail = [
                                                'name' => $replacementName,
                                                'type' => strtoupper((string) pathinfo($replacementName, PATHINFO_EXTENSION)) ?: 'FILE',
                                                'size' => $replacementFile->getSize() >= 1048576
                                                    ? number_format($replacementFile->getSize() / 1048576, 1).' MB'
                                                    : number_format(max(1, (int) ceil($replacementFile->getSize() / 1024))).' KB',
                                            ];
                                        }
                                    ?>
                                    <div
                                        class="ft-artwork-revision-replacement-item <?php echo e($replacementDetail ? 'has-replacement' : ''); ?>"
                                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'artwork-revision-replacement-'.e($task->id).'-'.e($revisionDocumentId).''; ?>wire:key="artwork-revision-replacement-<?php echo e($task->id); ?>-<?php echo e($revisionDocumentId); ?>"
                                        x-data="{ uploadingReplacement: false, replacementProgress: 0 }"
                                        x-on:livewire-upload-start="uploadingReplacement = true; replacementProgress = 0"
                                        x-on:livewire-upload-progress="replacementProgress = Math.max(0, Math.min(100, Number($event.detail.progress) || 0))"
                                        x-on:livewire-upload-finish="replacementProgress = 100; window.setTimeout(() => { uploadingReplacement = false; replacementProgress = 0 }, 250)"
                                        x-on:livewire-upload-error="uploadingReplacement = false; replacementProgress = 0"
                                        x-on:livewire-upload-cancel="uploadingReplacement = false; replacementProgress = 0"
                                        x-on:flowtrack-artwork-upload-start="uploadingReplacement = true; replacementProgress = 0"
                                        x-on:flowtrack-artwork-upload-progress="replacementProgress = Math.max(0, Math.min(100, Number($event.detail.progress) || 0))"
                                        x-on:flowtrack-artwork-upload-finish="replacementProgress = 100; window.setTimeout(() => { uploadingReplacement = false; replacementProgress = 0 }, 250)"
                                        x-on:flowtrack-artwork-upload-error="uploadingReplacement = false; replacementProgress = 0"
                                    >
                                        <div class="ft-artwork-revision-replacement-summary">
                                            <div class="ft-artwork-revision-replacement-source">
                                                <span class="ft-artwork-revision-selector-check is-checked" aria-hidden="true">✓</span>
                                                <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $candidateExtension,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($candidateExtension),'size' => 'sm']); ?>
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
                                                <span class="ft-artwork-revision-upload-selector-copy">
                                                    <b title="<?php echo e($revisionCandidate->name); ?>"><?php echo e($revisionCandidate->name); ?></b>
                                                    <small><?php echo e($candidateExtension); ?> · Current artwork</small>
                                                </span>
                                            </div>
                                            <div class="ft-artwork-revision-replacement-actions">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($replacementDetail): ?>
                                                    <span>Ready</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <a href="<?php echo e(route('documents.open', $revisionCandidate)); ?>" target="_blank" rel="noopener">Open</a>
                                            </div>
                                        </div>

                                        <div class="ft-artwork-revision-replacement-details">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revisionInstruction !== ''): ?>
                                                <div class="ft-artwork-revision-replacement-change">
                                                    <strong>Required change</strong>
                                                    <p><?php echo e($revisionInstruction); ?></p>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <div class="ft-artwork-revision-replacement-upload">
                                                <div class="ft-artwork-revision-replacement-upload-head">
                                                    <div>
                                                        <strong>Replacement artwork <b>*</b></strong>
                                                        <small>Upload the corrected file for this artwork only.</small>
                                                    </div>
                                                </div>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($replacementDetail): ?>
                                                    <div class="ft-order-attachment-selected-file ft-artwork-revision-replacement-selected-file">
                                                        <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $replacementDetail['type'],'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($replacementDetail['type']),'size' => 'sm']); ?>
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
                                                            <strong title="<?php echo e($replacementDetail['name']); ?>"><?php echo e($replacementDetail['name']); ?></strong>
                                                            <small><?php echo e($replacementDetail['type']); ?> · <?php echo e($replacementDetail['size']); ?> · Ready to replace this artwork</small>
                                                        </span>
                                                        <button
                                                            type="button"
                                                            wire:click="removeOverviewTaskDocumentUpload(<?php echo e($revisionDocumentId); ?>)"
                                                            wire:loading.attr="disabled"
                                                            wire:target="overviewTaskRevisionUpload.<?php echo e($revisionDocumentId); ?>,removeOverviewTaskDocumentUpload(<?php echo e($revisionDocumentId); ?>)"
                                                        >Remove</button>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <label class="ft-order-task-document-dropzone ft-order-attachment-dropzone ft-artwork-revision-replacement-dropzone <?php echo e($replacementDetail ? 'is-compact' : ''); ?>" data-file-dropzone>
                                                    <input
                                                        type="file"
                                                        data-artwork-chunk-input
                                                        data-artwork-upload-start-url="<?php echo e(route('orders.artwork-uploads.start', [], false)); ?>"
                                                        data-artwork-task-id="<?php echo e($task->id); ?>"
                                                        data-revision-document-id="<?php echo e($revisionDocumentId); ?>"
                                                        accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>"
                                                        aria-label="Choose replacement artwork for <?php echo e($revisionCandidate->name); ?>"
                                                        title="Choose replacement file"
                                                    >
                                                    <svg class="ft-order-attachment-upload-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 16l-4-4-4 4M12 12v9M20.4 17.5A5 5 0 0 0 18 8.2 7 7 0 0 0 4.3 10.8 4.5 4.5 0 0 0 5.5 19H7"/></svg>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($replacementDetail): ?>
                                                        <strong>Choose a different replacement</strong>
                                                        <b>Drag &amp; drop or <span>browse</span></b>
                                                    <?php else: ?>
                                                        <strong>Drag &amp; drop a file here</strong>
                                                        <b>or choose from your computer</b>
                                                        <span class="ft-order-attachment-browse">Browse file</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <small data-drop-status><?php echo e(\App\Support\AttachmentUpload::helperText(400)); ?></small>
                                                </label>

                                                <div
                                                    class="ft-prototype-upload-progress ft-artwork-revision-replacement-uploading"
                                                    x-cloak
                                                    x-show="uploadingReplacement"
                                                    x-transition.opacity.duration.120ms
                                                >
                                                    <div class="ft-prototype-upload-progress-meta">
                                                        <span>Uploading replacement...</span>
                                                        <b x-text="`${replacementProgress}%`">0%</b>
                                                    </div>
                                                    <div
                                                        class="ft-prototype-upload-progress-track"
                                                        role="progressbar"
                                                        aria-label="Replacement artwork upload progress"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100"
                                                        x-bind:aria-valuenow="replacementProgress"
                                                    >
                                                        <span x-bind:style="`width: ${replacementProgress}%`"></span>
                                                    </div>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskRevisionUpload.'.$revisionDocumentId];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskRevisionDocumentIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($artworkRevisionActive)): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedUploads->isNotEmpty()): ?>
                        <div class="ft-order-attachment-selected-count"><?php echo e($selectedUploadCount); ?> file<?php echo e($selectedUploadCount === 1 ? '' : 's'); ?> selected<?php echo e($artworkRevisionActive ? ' · '.$selectedUploadCount.' of '.$revisionCount.' replacements' : ($automationKey === 'ART_PREPARE_UPLOAD' ? ' · One artwork version' : '')); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedUploadDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $selectedUpload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-order-attachment-selected-file" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'overview-task-upload-'.e($task->id).'-'.e($index).'-'.e(md5($selectedUpload['name'])).''; ?>wire:key="overview-task-upload-<?php echo e($task->id); ?>-<?php echo e($index); ?>-<?php echo e(md5($selectedUpload['name'])); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($artworkRevisionActive): ?>
                                    <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $selectedUpload['type'],'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedUpload['type']),'size' => 'sm']); ?>
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
                                <?php else: ?>
                                    <span class="ft-order-attachment-selected-check" aria-hidden="true">✓</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="ft-order-attachment-selected-copy">
                                    <strong class="<?php echo e($prototypeUpload ? 'ft-prototype-selected-file-name' : ''); ?>" title="<?php echo e($selectedUpload['name']); ?>"><?php echo e($selectedUpload['name']); ?></strong>
                                    <small><?php echo e($selectedUpload['type']); ?> · <?php echo e($selectedUpload['size']); ?> · Uploaded · Ready to save</small>
                                </span>
                                <button type="button" wire:click="removeOverviewTaskDocumentUpload(<?php echo e($index); ?>)" wire:loading.attr="disabled" wire:target="overviewTaskDocumentUpload,overviewTaskRevisionUpload,removeOverviewTaskDocumentUpload(<?php echo e($index); ?>)">Remove</button>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        <div class="<?php echo e($prototypeUpload ? 'ft-prototype-upload-label' : 'ft-order-attachment-field-label'); ?>"><?php echo e($prototypeUpload ? $prototypeConfig['label'] : 'File attachment'); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <label class="ft-order-task-document-dropzone ft-order-attachment-dropzone <?php echo e($selectedUploads->isNotEmpty() ? 'is-compact' : ''); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($chunkedArtworkUpload): ?>
                            <input
                                type="file"
                                data-artwork-chunk-input
                                data-artwork-upload-start-url="<?php echo e(route('orders.artwork-uploads.start', [], false)); ?>"
                                data-artwork-task-id="<?php echo e($task->id); ?>"
                                data-artwork-current-count="<?php echo e($selectedUploadCount); ?>"
                                <?php if($inputAllowsMultiple): ?> multiple <?php endif; ?>
                                accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>"
                                aria-label="<?php echo e($uploadCopyPlural ? 'Choose files to upload' : 'Choose a file to upload'); ?>"
                                title="<?php echo e($uploadCopyPlural ? 'Choose files' : 'Choose file'); ?>"
                            >
                        <?php else: ?>
                            <input type="file" wire:model="overviewTaskDocumentUpload" <?php if($inputAllowsMultiple): ?> multiple <?php endif; ?> accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>" aria-label="<?php echo e($uploadCopyPlural ? 'Choose files to upload' : 'Choose a file to upload'); ?>" title="<?php echo e($uploadCopyPlural ? 'Choose files' : 'Choose file'); ?>">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <svg class="ft-order-attachment-upload-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 16l-4-4-4 4M12 12v9M20.4 17.5A5 5 0 0 0 18 8.2 7 7 0 0 0 4.3 10.8 4.5 4.5 0 0 0 5.5 19H7"/></svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedUploads->isNotEmpty()): ?>
                            <strong><?php echo e($uploadCopyPlural ? 'Choose a different file set' : 'Choose a different file'); ?></strong>
                            <b>Drag &amp; drop or <span>browse</span></b>
                        <?php else: ?>
                            <strong>Drag &amp; drop <?php echo e($uploadCopyPlural ? 'files' : 'a file'); ?> here</strong>
                            <b>or choose from your computer<?php echo e($uploadCopyPlural ? ' (use Shift/Ctrl/Cmd to select several)' : ''); ?></b>
                            <span class="ft-order-attachment-browse">Browse file<?php echo e($uploadCopyPlural ? 's' : ''); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small><?php echo e($prototypeConfig['hint']); ?></small>
                    </label>

                    
                    <div
                        class="ft-prototype-upload-progress"
                        x-cloak
                        x-show="uploading"
                        x-transition.opacity.duration.120ms
                    >
                        <div class="ft-prototype-upload-progress-meta">
                            <span>Uploading <?php echo e($uploadCopyPlural ? 'files' : 'file'); ?>...</span>

                            <b x-text="`${progress}%`">
                                0%
                            </b>
                        </div>

                        <div
                            class="ft-prototype-upload-progress-track"
                            role="progressbar"
                            aria-label="File upload progress"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            x-bind:aria-valuenow="progress"
                        >
                            <span
                                x-bind:style="`width: ${progress}%`"
                            ></span>
                        </div>
                    </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskDocumentUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="validation-error">
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskDocumentUpload.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="validation-error">
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskRevisionUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="validation-error">
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($prototypeUpload)): ?>
                <label class="ft-order-task-document-note"><span>Document note (optional)</span><input type="text" wire:model="overviewTaskDocumentNote" placeholder="Add a short note..."><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskDocumentNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-order-task-document-info">This document remains linked to this task and is available from the task and Documents archive.</div>
            <?php else: ?>
                <div class="ft-prototype-upload-meta"><span>Task</span><b><?php echo e($task->title); ?></b><span>Order</span><b><?php echo e($job->displayOrderNumber()); ?></b></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <footer class="ft-order-task-document-modal-actions">
            <button type="button" class="secondary" wire:click="closeOverviewTaskDocumentModal">Cancel</button>
            <button
                type="button"
                class="primary"
                data-artwork-upload-save
                data-server-disabled="<?php echo e(($source === 'upload' ? ($artworkRevisionActive ? ($revisionCount < 1 || $selectedUploadCount !== $revisionCount) : $selectedUploads->isEmpty()) : !$existingDocumentId) ? '1' : '0'); ?>"
                wire:click="saveOverviewTaskDocument"
                wire:loading.attr="disabled"
                wire:target="saveOverviewTaskDocument,overviewTaskDocumentUpload,overviewTaskRevisionUpload"
                <?php if($source === 'upload' ? ($artworkRevisionActive ? ($revisionCount < 1 || $selectedUploadCount !== $revisionCount) : $selectedUploads->isEmpty()) : !$existingDocumentId): echo 'disabled'; endif; ?>
            >
                <span
                    wire:loading.remove
                    wire:target="saveOverviewTaskDocument"
                >
                    <?php echo e($prototypeUpload
                            ? $prototypeConfig['button']
                            : ($selectedUploadCount > 0 ? 'Add '.$selectedUploadCount.' document'.($selectedUploadCount === 1 ? '' : 's') : 'Add document')); ?>

                </span>

                <span
                    wire:loading
                    wire:target="saveOverviewTaskDocument"
                >
                    Saving...
                </span>
            </button>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/document-modal.blade.php ENDPATH**/ ?>