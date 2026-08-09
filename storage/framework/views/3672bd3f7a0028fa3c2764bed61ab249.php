<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'availableDocuments'=>collect(),
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
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
    'availableDocuments'=>collect(),
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $required = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $receivedRequired = $required->where('complete', true)->count();
    $missingRequired = $required->where('complete', false)->count();
    $requiredProgress = $required->count() ? (int) round(($receivedRequired / $required->count()) * 100) : 100;
    $unlinkedDocs = $job->documents->whereNull('task_id')->values();
    $canUploadDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','create');
    $canLinkDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
?>
<div class="ft-documents-detail-section ft-exact-documents">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobDocumentTaskId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobDocumentUploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobDocumentUploads.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-detail-doc-full">
        <main>
            <div class="ft-section-title-row ft-doc-title-row ft-doc-ux-title">
                <div>
                    <h2>Documents</h2>
                    <p>Keep required files, task attachments and existing client documents together.</p>
                </div>
                <div class="ft-doc-completion-copy">
                    <strong><?php echo e($receivedRequired); ?>/<?php echo e($required->count()); ?></strong>
                    <span>required received</span>
                </div>
            </div>

            <div class="ft-doc-summary-strip" aria-label="Document summary">
                <div class="ft-doc-summary-item">
                    <span class="ft-doc-summary-icon blue">▣</span>
                    <div><small>Total files</small><b><?php echo e($job->documents->count()); ?></b></div>
                </div>
                <div class="ft-doc-summary-item">
                    <span class="ft-doc-summary-icon neutral">✓</span>
                    <div><small>Required</small><b><?php echo e($required->count()); ?></b></div>
                </div>
                <div class="ft-doc-summary-item <?php echo e($missingRequired ? 'needs-action' : ''); ?>">
                    <span class="ft-doc-summary-icon amber">!</span>
                    <div><small>Needs action</small><b><?php echo e($missingRequired); ?></b></div>
                </div>
                <div class="ft-doc-summary-item">
                    <span class="ft-doc-summary-icon green">✓</span>
                    <div><small>Received</small><b><?php echo e($receivedRequired); ?></b></div>
                </div>
            </div>

            <div class="ft-doc-required-progress" aria-label="Required document progress">
                <div><span>Required document progress</span><b><?php echo e($requiredProgress); ?>%</b></div>
                <span class="ft-doc-progress-track"><i style="width: <?php echo e($requiredProgress); ?>%"></i></span>
            </div>

            <section id="job-document-upload-panel" class="ft-detail-card ft-upload-panel ft-doc-upload-card">
                <div class="ft-doc-upload-heading">
                    <span class="ft-doc-upload-heading-icon">＋</span>
                    <div>
                        <b>Add a required document</b>
                        <p>Choose where the document belongs, then upload a new file or link one that already exists.</p>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required->isNotEmpty()): ?>
                    <div class="ft-doc-upload-steps">
                        <div class="ft-doc-upload-step">
                            <span>1</span>
                            <div><b>Choose requirement</b><small>Select the Task Pack requirement this file satisfies.</small></div>
                        </div>
                        <div class="ft-doc-upload-step">
                            <span>2</span>
                            <div><b>Add the file</b><small>Upload from your device or choose an existing document.</small></div>
                        </div>
                    </div>

                    <div class="ft-document-purpose-field ft-doc-purpose-ux">
                        <label for="jobDocumentRequirement-<?php echo e($job->id); ?>">Required document</label>
                        <select id="jobDocumentRequirement-<?php echo e($job->id); ?>" wire:model.live="jobDocumentTaskId" class="ft-document-purpose-select">
                            <option value="">Select requirement, phase and task</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $required; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($item->task->id); ?>"><?php echo e($item->name); ?> · <?php echo e($item->phase->name); ?> · <?php echo e($item->task->title); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <small class="ft-document-match-note">FlowTrack links the file to the selected phase and task automatically.</small>
                    </div>

                    <div class="ft-upload-zone compact ft-task-upload-zone ft-job-document-attachment-zone ft-doc-upload-actions">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                            <label class="ft-task-upload-drop ft-livewire-upload-zone ft-doc-primary-drop" data-file-dropzone for="jobDocumentUpload-<?php echo e($job->id); ?>">
                                <input id="jobDocumentUpload-<?php echo e($job->id); ?>" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                                <span class="ft-paperclip">⌕</span>
                                <div><b>Upload from device</b><span>Drop files here or <strong>browse</strong></span><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                            </label>
                        <?php else: ?>
                            <div class="ft-task-upload-drop ft-task-upload-readonly ft-doc-primary-drop"><span class="ft-paperclip">⌕</span><div><b>Document upload</b><span>Read-only access</span><small>You do not have permission to upload Job documents.</small></div></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument): ?>
                            <button class="ft-outline-btn ft-task-choose-document ft-doc-existing-action" type="button" wire:click="toggleDocumentPicker">
                                <span>▤</span><span>Choose existing</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($canManageDocuments)): ?><p class="muted small">You have read-only access to Job documents.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument && count($jobDocumentUploads ?? [])): ?>
                        <div class="ft-upload-ready-row ft-doc-upload-ready">
                            <span><b><?php echo e(count($jobDocumentUploads ?? [])); ?></b> file<?php echo e(count($jobDocumentUploads ?? [])===1?'':'s'); ?> ready to link</span>
                            <button class="ft-new-job-btn" type="button" wire:click="uploadJobDocuments">Upload &amp; link</button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument && ($showDocumentPicker ?? false)): ?>
                        <div class="ft-existing-document-picker ft-doc-existing-picker">
                            <div class="ft-card-row-head">
                                <div><b>Choose an existing document</b><p>Link a stored client document to the requirement selected above.</p></div>
                                <button type="button" class="ft-outline-btn" wire:click="toggleDocumentPicker">Close</button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availableDocuments->isNotEmpty()): ?>
                                <div class="ft-doc-existing-picker-actions">
                                    <select wire:model="existingDocumentId">
                                        <option value="">Select a stored document</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($doc->id); ?>"><?php echo e($doc->name); ?> · <?php echo e($doc->job?->displayOrderNumber() ?? 'Document archive'); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <button class="ft-new-job-btn" type="button" wire:click="attachExistingDocument">Link document</button>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['existingDocumentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <p class="muted small">No stored documents are available for this client yet.</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <div class="ft-empty-taskpack-docs">No document requirement exists in the Task Packs selected by this Job. Upload requirements are never created outside the Task Packs.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-detail-card ft-job-documents-table ft-doc-library-card">
                <div class="ft-doc-library-heading">
                    <div>
                        <h3>Job documents</h3>
                        <p>Organized by workflow phase so you can see what is missing without scanning a table.</p>
                    </div>
                    <span class="ft-soft-pill <?php echo e($missingRequired ? 'amber' : 'green'); ?>"><?php echo e($missingRequired ? $missingRequired.' required missing' : 'All required received'); ?></span>
                </div>

                <div class="ft-doc-phase-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $phaseRequirements = $required->filter(fn($item)=>(int)$item->phase->id===(int)$phase->id)->values();
                            $phaseTaskIds = $job->tasks->where('workflow_phase_id',$phase->id)->pluck('id');
                            $phaseDocuments = $job->documents->whereIn('task_id',$phaseTaskIds)->values();
                            $phaseRequiredReceived = $phaseRequirements->where('complete', true)->count();
                            $phaseMissing = $phaseRequirements->where('complete', false)->count();
                            $requiredDocumentIds = $phaseRequirements->flatMap(function($requirement) use ($job) {
                                return $job->documents
                                    ->where('task_id', $requirement->task->id)
                                    ->filter(fn($document) => strcasecmp(trim((string)$document->category), trim((string)$requirement->name)) === 0)
                                    ->pluck('id');
                            })->map(fn($id)=>(int)$id)->unique();
                            $phaseAttachments = $phaseDocuments->reject(fn($doc)=>$requiredDocumentIds->contains((int)$doc->id))->values();
                            $openPhase = (int)($job->workflow_phase_id ?? 0) === (int)$phase->id || ((int)($job->workflow_phase_id ?? 0) === 0 && (int)$phase->sequence === 1);
                        ?>

                        <details class="ft-doc-phase-group" <?php if($openPhase): ?> open <?php endif; ?>>
                            <summary class="ft-doc-phase-summary">
                                <span class="ft-doc-phase-chevron">›</span>
                                <b class="ft-doc-phase-number"><?php echo e($phase->sequence); ?></b>
                                <span class="ft-doc-phase-copy">
                                    <strong><?php echo e($phase->name); ?></strong>
                                    <small><?php echo e($phaseRequiredReceived); ?> of <?php echo e($phaseRequirements->count()); ?> required received · <?php echo e($phaseDocuments->count()); ?> file<?php echo e($phaseDocuments->count()===1?'':'s'); ?></small>
                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phaseMissing): ?>
                                    <span class="ft-soft-pill amber"><?php echo e($phaseMissing); ?> needs action</span>
                                <?php elseif($phaseRequirements->isNotEmpty()): ?>
                                    <span class="ft-soft-pill green">Complete</span>
                                <?php else: ?>
                                    <span class="ft-soft-pill gray">No requirements</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </summary>

                            <div class="ft-doc-phase-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $docs = $job->documents->where('task_id',$requirement->task->id)->filter(fn($document)=>strcasecmp(trim((string)$document->category),trim((string)$requirement->name))===0)->values();
                                    ?>
                                    <article class="ft-doc-requirement-card <?php echo e($requirement->complete ? 'is-complete' : 'needs-action'); ?>">
                                        <div class="ft-doc-requirement-main">
                                            <span class="ft-doc-requirement-state"><?php echo e($requirement->complete ? '✓' : '!'); ?></span>
                                            <div class="ft-doc-requirement-copy">
                                                <div class="ft-doc-requirement-title-line">
                                                    <b><?php echo e($requirement->name); ?></b>
                                                    <span class="ft-soft-pill <?php echo e($requirement->complete ? 'green' : 'amber'); ?>"><?php echo e($requirement->complete ? 'Received' : 'Required'); ?></span>
                                                </div>
                                                <small>Task: <?php echo e($requirement->task->title); ?></small>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($docs->isEmpty()): ?>
                                                    <p>No file has been received for this requirement yet.</p>
                                                <?php else: ?>
                                                    <p><?php echo e($docs->count()); ?> file<?php echo e($docs->count()===1?'':'s'); ?> linked to this requirement.</p>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$requirement->complete && $canUploadDocument): ?>
                                                <button class="ft-outline-btn ft-doc-requirement-upload" type="button" x-on:click="await $wire.set('jobDocumentTaskId', <?php echo e($requirement->task->id); ?>); document.getElementById('jobDocumentUpload-<?php echo e($job->id); ?>').click()">Upload file</button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($docs->isNotEmpty()): ?>
                                            <div class="ft-doc-linked-files">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <?php $extension = strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE'); ?>
                                                    <div class="ft-doc-linked-file">
                                                        <span class="ft-file-icon <?php echo e(str_contains(strtolower($doc->mime_type ?? ''),'pdf') ? 'pdf' : 'sheet'); ?>">▣</span>
                                                        <div class="ft-doc-linked-file-copy">
                                                            <a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener"><?php echo e($doc->name); ?></a>
                                                            <small><?php echo e($extension); ?> · v<?php echo e($doc->version); ?> · <?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::isToday($doc->updated_at) ? 'Today '.\App\Support\UserLocalTime::format($doc->updated_at, 'g:i A') : \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y')); ?></small>
                                                        </div>
                                                        <span class="ft-soft-pill green">Linked</span>
                                                        <div class="ft-doc-linked-actions">
                                                            <a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','delete')): ?>
                                                                <button class="ft-doc-delete-button" type="button" wire:click="deleteJobDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?" aria-label="Delete <?php echo e($doc->name); ?>">×</button>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </article>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div class="ft-doc-empty-phase">No Task Pack document requirements in this phase.</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phaseAttachments->isNotEmpty()): ?>
                                    <div class="ft-doc-attachments-block">
                                        <div class="ft-doc-subsection-label"><span>Attachments</span><small>Files linked to tasks but not counted as required documents</small></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phaseAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php $extension = strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE'); ?>
                                            <div class="ft-doc-linked-file is-attachment">
                                                <span class="ft-file-icon">▣</span>
                                                <div class="ft-doc-linked-file-copy">
                                                    <a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener"><?php echo e($doc->name); ?></a>
                                                    <small><?php echo e($extension); ?> · v<?php echo e($doc->version); ?> · Task: <?php echo e($doc->task?->title); ?> · <?php echo e(\App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y')); ?></small>
                                                </div>
                                                <span class="ft-soft-pill gray">Attachment</span>
                                                <div class="ft-doc-linked-actions"><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a></div>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </details>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unlinkedDocs->isNotEmpty()): ?>
                        <details class="ft-doc-phase-group ft-doc-unlinked-group">
                            <summary class="ft-doc-phase-summary">
                                <span class="ft-doc-phase-chevron">›</span>
                                <b class="ft-doc-phase-number">—</b>
                                <span class="ft-doc-phase-copy"><strong>Existing Job attachments</strong><small>Files not linked to a Task Pack requirement</small></span>
                                <span class="ft-soft-pill gray"><?php echo e($unlinkedDocs->count()); ?> file<?php echo e($unlinkedDocs->count()===1?'':'s'); ?></span>
                            </summary>
                            <div class="ft-doc-phase-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $unlinkedDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php $extension = strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE'); ?>
                                    <div class="ft-doc-linked-file is-attachment">
                                        <span class="ft-file-icon">▣</span>
                                        <div class="ft-doc-linked-file-copy">
                                            <a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener"><?php echo e($doc->name); ?></a>
                                            <small><?php echo e($extension); ?> · v<?php echo e($doc->version); ?> · <?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y')); ?></small>
                                        </div>
                                        <span class="ft-soft-pill gray">Attachment</span>
                                        <div class="ft-doc-linked-actions"><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a></div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </details>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-documents.blade.php ENDPATH**/ ?>