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
    $latestDoc = $job->documents->sortByDesc('updated_at')->first();
    $required = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $receivedRequired = $required->where('complete', true)->count();
    $percent = $required->count() ? round($receivedRequired / $required->count() * 100) : 100;
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

    <div class="ft-detail-doc-layout">
        <main>
            <div class="ft-section-title-row ft-doc-title-row">
                <div><h2>Documents</h2><p><?php echo e($job->documents->count()); ?> files · <?php echo e($receivedRequired); ?> of <?php echo e($required->count()); ?> required received</p></div>
            </div>
            <div class="ft-doc-filter-chips">
                <button class="active" type="button">All files <b><?php echo e($job->documents->count()); ?></b></button>
                <button type="button">Required <b><?php echo e($required->count()); ?></b></button>
                <button class="amber" type="button">Needs action <b><?php echo e($required->where('complete',false)->count()); ?></b></button>
                <button class="green" type="button">Received <b><?php echo e($receivedRequired); ?></b></button>
                <button type="button">Recently updated <b><?php echo e($job->documents->filter(fn($doc)=>$doc->updated_at?->gte(now()->subDays(7)))->count()); ?></b></button>
            </div>

            <section id="job-document-upload-panel" class="ft-detail-card ft-upload-panel" x-data="{ uploading:false, progress:0 }" x-on:livewire-upload-start="uploading=true; progress=0" x-on:livewire-upload-progress="progress=$event.detail.progress" x-on:livewire-upload-error="uploading=false; progress=0" x-on:livewire-upload-finish="progress=100; setTimeout(() => { uploading=false; progress=0 }, 700)">
                <b>Add documents</b>
                <p>Select the Task Pack document requirement first. The uploaded or chosen file is linked to that exact task automatically.</p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required->isNotEmpty()): ?>
                    <label>Document type</label>
                    <select wire:model.live="jobDocumentTaskId" class="ft-document-purpose-select">
                        <option value="">Select document type</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $required; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($item->task->id); ?>"><?php echo e($item->name); ?> · <?php echo e($item->phase->name); ?> · <?php echo e($item->task->title); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <small class="ft-document-match-note">The matching phase, Task Pack requirement and task are resolved automatically.</small>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                        <label class="ft-upload-zone ft-livewire-upload-zone" for="jobDocumentUpload-<?php echo e($job->id); ?>">
                            <input id="jobDocumentUpload-<?php echo e($job->id); ?>" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                            <span class="ft-paperclip">⌕</span>
                            <div>Drop files here or <strong>browse</strong><small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                        </label>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument): ?><div class="ft-document-link-action"><button class="ft-outline-btn" type="button" wire:click="toggleDocumentPicker">Choose from Documents</button></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($canManageDocuments)): ?><p class="muted small">You have read-only access to Job documents.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ft-upload-progress-wrap" x-cloak x-show="uploading" x-transition.opacity>
                        <div class="ft-upload-progress-copy"><span>Uploading document…</span><b x-text="progress + '%'">0%</b></div>
                        <div class="ft-upload-progress-track"><span x-bind:style="`width:${progress}%`"></span></div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument && count($jobDocumentUploads ?? [])): ?>
                        <div class="ft-upload-ready-row">
                            <span><?php echo e(count($jobDocumentUploads ?? [])); ?> file<?php echo e(count($jobDocumentUploads ?? [])===1?'':'s'); ?> ready</span>
                            <button class="ft-new-job-btn" type="button" wire:click="uploadJobDocuments" wire:loading.attr="disabled" wire:target="uploadJobDocuments">Upload &amp; link</button>
                            <span wire:loading wire:target="uploadJobDocuments">Uploading…</span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument && ($showDocumentPicker ?? false)): ?>
                        <div class="ft-existing-document-picker">
                            <div class="ft-card-row-head"><div><b>Choose from Documents</b><p>Select an existing client document and link it to the selected Task Pack requirement.</p></div><button type="button" class="ft-outline-btn" wire:click="toggleDocumentPicker">Close</button></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availableDocuments->isNotEmpty()): ?>
                                <select wire:model="existingDocumentId">
                                    <option value="">Select a stored document</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($doc->id); ?>"><?php echo e($doc->name); ?> · <?php echo e($doc->job?->job_number ?? 'Document archive'); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <button class="ft-new-job-btn" type="button" wire:click="attachExistingDocument">Link selected document</button>
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

            <section class="ft-detail-card ft-job-documents-table">
                <h3>Job documents</h3>
                <p>Grouped by workflow phase and related Task Pack task</p>
                <div class="ft-doc-table-wrap">
                    <table class="ft-doc-table">
                        <thead><tr><th>Document / requirement</th><th>Type</th><th>Version</th><th>Owner</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $phaseRequirements = $required->filter(fn($item)=>(int)$item->phase->id===(int)$phase->id)->values();
                                $phaseTaskIds = $job->tasks->where('workflow_phase_id',$phase->id)->pluck('id');
                                $phaseDocuments = $job->documents->whereIn('task_id',$phaseTaskIds)->values();
                                $requiredDocumentIds = $phaseRequirements->flatMap(function($requirement) use ($job) {
                                    return $job->documents
                                        ->where('task_id', $requirement->task->id)
                                        ->filter(fn($document) => strcasecmp(trim((string)$document->category), trim((string)$requirement->name)) === 0)
                                        ->pluck('id');
                                })->map(fn($id)=>(int)$id)->unique();
                                $phaseAttachments = $phaseDocuments->reject(fn($doc)=>$requiredDocumentIds->contains((int)$doc->id))->values();
                            ?>
                            <tr class="ft-doc-phase-row"><td colspan="7"><span>⌄</span><b><?php echo e($phase->sequence); ?></b> <?php echo e($phase->name); ?> <small><?php echo e($phaseDocuments->count()); ?> documents · <?php echo e($phaseRequirements->count()); ?> requirement<?php echo e($phaseRequirements->count()===1?'':'s'); ?></small><em><?php echo e($phaseDocuments->count()); ?></em></td></tr>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php ($docs = $job->documents->where('task_id',$requirement->task->id)->filter(fn($document)=>strcasecmp(trim((string)$document->category),trim((string)$requirement->name))===0)->values()); ?>
                                <tr class="ft-required-inline-row"><td colspan="7">
                                    <div><strong><?php echo e($requirement->task->title); ?></strong><span><?php echo e($docs->count() ? $docs->count().' received' : 'Required document missing'); ?></span></div>
                                    <div class="ft-inline-requirement">
                                        <span class="<?php echo e($requirement->complete ? 'ok' : 'warn'); ?>"><?php echo e($requirement->complete ? '✓' : '!'); ?></span>
                                        <div><b><?php echo e($requirement->name); ?></b><small><?php echo e($phase->name); ?> · Task: <?php echo e($requirement->task->title); ?></small></div>
                                        <span class="ft-soft-pill <?php echo e($requirement->complete ? 'green' : 'amber'); ?>"><?php echo e($requirement->complete ? 'Received' : 'Needs action'); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$requirement->complete && $canUploadDocument): ?>
                                            <button class="ft-outline-btn" type="button" x-on:click="await $wire.set('jobDocumentTaskId', <?php echo e($requirement->task->id); ?>); document.getElementById('jobDocumentUpload-<?php echo e($job->id); ?>').click()">Upload</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td></tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr>
                                        <td><span class="ft-file-icon <?php echo e(str_contains(strtolower($doc->mime_type ?? ''),'pdf') ? 'pdf' : 'sheet'); ?>">▣</span><a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener"><?php echo e($doc->name); ?></a><small class="ft-doc-task-caption"><?php echo e($requirement->task->title); ?></small></td>
                                        <td><?php echo e(strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></td><td>v<?php echo e($doc->version); ?></td><td><?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?></td>
                                        <td><span class="ft-soft-pill green">Linked</span></td><td><?php echo e($doc->updated_at?->isToday() ? 'Today '.$doc->updated_at?->format('H:i') : $doc->updated_at?->format('M j, Y')); ?></td>
                                        <td><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','delete')): ?><button class="ft-doc-delete-button" type="button" wire:click="deleteJobDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="7" class="ft-doc-empty-row">No document requirement in this phase's Task Pack.</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phaseAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <td><span class="ft-file-icon">▣</span><a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener"><?php echo e($doc->name); ?></a><small class="ft-doc-task-caption">Task attachment · <?php echo e($doc->task?->title); ?></small></td>
                                    <td><?php echo e(strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></td><td>v<?php echo e($doc->version); ?></td><td><?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?></td><td><span class="ft-soft-pill gray">Attachment</span></td><td><?php echo e($doc->updated_at?->format('M j, Y')); ?></td><td><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unlinkedDocs->isNotEmpty()): ?>
                            <tr class="ft-doc-phase-row"><td colspan="7"><span>⌄</span><b>—</b> Existing Job attachments <small>Not counted as Task Pack requirements</small><em><?php echo e($unlinkedDocs->count()); ?></em></td></tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $unlinkedDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr><td><span class="ft-file-icon">▣</span><a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener"><?php echo e($doc->name); ?></a></td><td><?php echo e(strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></td><td>v<?php echo e($doc->version); ?></td><td><?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?></td><td><span class="ft-soft-pill gray">Attachment</span></td><td><?php echo e($doc->updated_at?->format('M j, Y')); ?></td><td><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a></td></tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <aside>
            <section class="ft-detail-card ft-required-docs-card">
                <div class="ft-card-row-head"><h3>Required documents</h3><span><?php echo e($receivedRequired); ?> of <?php echo e($required->count()); ?> received</span></div>
                <div class="ft-doc-progress"><span style="width:<?php echo e($percent); ?>%"></span></div><b class="ft-percent"><?php echo e($percent); ?>%</b>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $required; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-required-doc-row">
                        <span class="<?php echo e($doc->complete ? 'ok' : 'warn'); ?>"><?php echo e($doc->complete ? '✓' : '!'); ?></span>
                        <div><b><?php echo e($doc->name); ?></b><p><?php echo e($doc->phase->name); ?> · <?php echo e($doc->task->title); ?></p><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($doc->complete)): ?><small>Required document missing.</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$doc->complete && $canUploadDocument): ?><button class="ft-outline-btn ft-doc-upload-btn" type="button" x-on:click="await $wire.set('jobDocumentTaskId', <?php echo e($doc->task->id); ?>); document.getElementById('jobDocumentUpload-<?php echo e($job->id); ?>').click()">Upload</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="muted small">No document requirements exist in the selected Task Packs.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
            <section class="ft-detail-card ft-doc-health-card"><h3>Document health</h3><div><span>Received</span><b class="green-text"><?php echo e($receivedRequired); ?></b></div><div><span>Draft / needs action</span><b class="danger-text"><?php echo e($required->where('complete',false)->count()); ?></b></div><div><span>Total Job files</span><b><?php echo e($job->documents->count()); ?></b></div><hr><div><span>Latest update</span><b><?php echo e($latestDoc?->updated_at?->format('M j, Y') ?? '—'); ?></b></div></section>
        </aside>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-documents.blade.php ENDPATH**/ ?>