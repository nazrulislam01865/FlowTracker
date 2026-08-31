<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'task', 'availableDocuments' => collect(), 'source' => 'upload', 'upload' => null, 'existingDocumentId' => null, 'artworkRevision' => [], 'context' => []]));

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

foreach (array_filter((['job', 'task', 'availableDocuments' => collect(), 'source' => 'upload', 'upload' => null, 'existingDocumentId' => null, 'artworkRevision' => [], 'context' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
    $allowMultipleUploads = $automationKey === 'ART_PREPARE_UPLOAD'
        || (bool) ($task->setupTemplate?->allow_multiple_documents ?? false);
    $hasExistingEvidence = $task->relationLoaded('documents') ? $task->documents->isNotEmpty() : false;
    $artworkRevisionActive = $automationKey === 'ART_PREPARE_UPLOAD' && (bool) ($artworkRevision['active'] ?? false);
    $revisionDocuments = $artworkRevisionActive ? collect($artworkRevision['documents'] ?? [])->values() : collect();
    $retainedArtworkDocuments = $artworkRevisionActive ? collect($artworkRevision['retained_documents'] ?? [])->values() : collect();
    $revisionCount = $revisionDocuments->count();
    // Keep Artwork bound to a multiple-file input because Livewire stores this
    // field in an array property even when a selective revision needs one file.
    $inputAllowsMultiple = $allowMultipleUploads;
    $uploadCopyPlural = $artworkRevisionActive ? $revisionCount > 1 : $allowMultipleUploads;

    $prototypeConfig = match ($automationKey) {
        'NEW_UPLOAD_PO' => [
            'title' => 'Upload Purchase Order',
            'label' => 'Purchase order file',
            'copy' => 'Upload the client purchase order to begin processing this Order.',
            'hint' => 'PDF, Office files, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB',
            'button' => 'Upload Purchase Order',
        ],
        'ART_PREPARE_UPLOAD' => [
            'title' => $hasExistingEvidence ? 'Upload Revised Artwork' : 'Upload Artwork',
            'label' => $artworkRevisionActive ? 'Replacement artwork files' : ($hasExistingEvidence ? 'Revised artwork files' : 'Artwork files'),
            'copy' => $artworkRevisionActive
                ? 'Upload only the '.($revisionCount === 1 ? 'artwork file selected' : $revisionCount.' artwork files selected').' for revision. '.($retainedArtworkDocuments->count() ?: 'No').' other file'.($retainedArtworkDocuments->count() === 1 ? '' : 's').' will remain unchanged automatically.'
                : ($hasExistingEvidence
                    ? 'Upload up to 10 corrected artwork files as one revision. The previous version remains in Order history.'
                    : 'Upload up to 10 artwork files together for internal review.'),
            'hint' => $artworkRevisionActive
                ? 'PDF, AI, EPS, ESP, JPG or PNG · Max 20 MB per file · Exactly '.$revisionCount.' replacement'.($revisionCount === 1 ? '' : 's').' required'
                : 'PDF, AI, EPS, ESP, JPG or PNG · Max 20 MB per file · Up to 10 files',
            'button' => $artworkRevisionActive ? 'Upload Selected Revision' : ($hasExistingEvidence ? 'Upload Revised Artwork' : 'Upload Artwork'),
        ],
        'ART_SAMPLE_APPROVAL' => [
            'title' => 'Upload Sample Approval',
            'label' => 'Signed sample approval',
            'copy' => 'Attach the client sample/swatch approval to continue to Production.',
            'hint' => 'PDF, Office files, JPG or PNG · Max 20 MB',
            'button' => 'Upload Sample Approval',
        ],
        default => [
            'title' => 'Add document to task',
            'label' => 'Task document',
            'copy' => 'Upload a new file or link an existing client document.',
            'hint' => 'PDF, Office, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB',
            'button' => 'Add document',
        ],
    };
    $selectedUploads = collect(is_array($upload) ? $upload : ($upload ? [$upload] : []))->filter()->values();
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
?>
<div class="ft-order-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-document-modal-'.e($task->id).''; ?>wire:key="order-task-document-modal-<?php echo e($task->id); ?>" wire:click.self="closeOverviewTaskDocumentModal">
    <section class="ft-order-task-document-modal ft-order-attachment-upload-modal <?php echo e($prototypeUpload ? 'ft-order-prototype-upload-modal' : ''); ?>" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="order-task-document-modal-title">
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
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($artworkRevisionActive): ?>
                        <div class="ft-artwork-revision-upload-plan">
                            <div class="ft-artwork-revision-upload-plan-head">
                                <div>
                                    <strong>Selective artwork revision</strong>
                                    <span>Replace only the selected artwork. Unchanged files are carried into the new version automatically. If several files are selected, choose the replacement files in the numbered order shown below.</span>
                                </div>
                                <em><?php echo e($revisionCount); ?> replacement<?php echo e($revisionCount === 1 ? '' : 's'); ?></em>
                            </div>
                            <div class="ft-artwork-revision-upload-columns">
                                <div>
                                    <small>REPLACE</small>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $revisionDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $revisionDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="ft-artwork-revision-upload-item is-replace">
                                            <span><?php echo e($index + 1); ?></span>
                                            <b title="<?php echo e($revisionDocument->name); ?>"><?php echo e($revisionDocument->name); ?></b>
                                            <a href="<?php echo e(route('documents.open', $revisionDocument)); ?>" target="_blank" rel="noopener">View</a>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                                <div>
                                    <small>KEEP UNCHANGED</small>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $retainedArtworkDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $retainedDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="ft-artwork-revision-upload-item is-retained">
                                            <span>✓</span>
                                            <b title="<?php echo e($retainedDocument->name); ?>"><?php echo e($retainedDocument->name); ?></b>
                                            <em>Retained</em>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <div class="ft-artwork-revision-upload-empty">All current artwork files were selected for revision.</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($artworkRevision['comment'] ?? null)): ?>
                                <p><b>Revision instruction:</b> <?php echo e($artworkRevision['comment']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedUploads->isNotEmpty()): ?>
                        <div class="ft-order-attachment-selected-count"><?php echo e($selectedUploadCount); ?> file<?php echo e($selectedUploadCount === 1 ? '' : 's'); ?> selected<?php echo e($artworkRevisionActive ? ' · '.$selectedUploadCount.' of '.$revisionCount.' replacements' : ($automationKey === 'ART_PREPARE_UPLOAD' ? ' · One artwork version' : '')); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedUploadDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $selectedUpload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-order-attachment-selected-file" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'overview-task-upload-'.e($task->id).'-'.e($index).'-'.e(md5($selectedUpload['name'])).''; ?>wire:key="overview-task-upload-<?php echo e($task->id); ?>-<?php echo e($index); ?>-<?php echo e(md5($selectedUpload['name'])); ?>">
                                <span class="ft-order-attachment-selected-check" aria-hidden="true">✓</span>
                                <span class="ft-order-attachment-selected-copy">
                                    <strong class="<?php echo e($prototypeUpload ? 'ft-prototype-selected-file-name' : ''); ?>" title="<?php echo e($selectedUpload['name']); ?>"><?php echo e($selectedUpload['name']); ?></strong>
                                    <small><?php echo e($selectedUpload['type']); ?> · <?php echo e($selectedUpload['size']); ?> · Ready to upload</small>
                                </span>
                                <button type="button" wire:click="removeOverviewTaskDocumentUpload(<?php echo e($index); ?>)" wire:loading.attr="disabled" wire:target="overviewTaskDocumentUpload,removeOverviewTaskDocumentUpload(<?php echo e($index); ?>)">Remove</button>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        <div class="<?php echo e($prototypeUpload ? 'ft-prototype-upload-label' : 'ft-order-attachment-field-label'); ?>"><?php echo e($prototypeUpload ? $prototypeConfig['label'] : 'File attachment'); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <label class="ft-order-task-document-dropzone ft-order-attachment-dropzone <?php echo e($selectedUploads->isNotEmpty() ? 'is-compact' : ''); ?>">
                        <input type="file" wire:model="overviewTaskDocumentUpload" <?php if($inputAllowsMultiple): ?> multiple <?php endif; ?> accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai,.eps,.esp" aria-label="<?php echo e($uploadCopyPlural ? 'Choose files to upload' : 'Choose a file to upload'); ?>" title="<?php echo e($uploadCopyPlural ? 'Choose files' : 'Choose file'); ?>">
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
                wire:click="saveOverviewTaskDocument"
                wire:loading.attr="disabled"
                wire:target="saveOverviewTaskDocument,overviewTaskDocumentUpload"
                <?php if($source === 'upload' ? $selectedUploads->isEmpty() : !$existingDocumentId): echo 'disabled'; endif; ?>
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
                    Uploading...
                </span>
            </button>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/document-modal.blade.php ENDPATH**/ ?>