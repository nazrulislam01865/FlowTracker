<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'availableDocuments'=>collect(),
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
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
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
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
    $canDeleteDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','delete');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $lastUploadedDocument = $lastJobDocumentUploadId
        && (int) $lastJobDocumentTaskId === (int) $jobDocumentTaskId
        ? $job->documents->firstWhere('id', (int) $lastJobDocumentUploadId)
        : null;
    $pendingUploadName = $jobRequiredDocumentUpload && method_exists($jobRequiredDocumentUpload, 'getClientOriginalName')
        ? $jobRequiredDocumentUpload->getClientOriginalName()
        : '';
    $pendingUploadSize = $jobRequiredDocumentUpload && method_exists($jobRequiredDocumentUpload, 'getSize')
        ? (int) $jobRequiredDocumentUpload->getSize()
        : 0;
    $uploadError = $errors->first('jobRequiredDocumentUpload')
        ?: ($pendingUploadName ? $errors->first('jobDocumentTaskId') : '');
    $uploadInitialState = $uploadError ? 'error' : ($lastUploadedDocument ? 'success' : 'idle');
?>
<div class="ft-documents-detail-section ft-exact-documents">
    <?php if (session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?>

    <div class="ft-detail-doc-full">
        <main>
            <div class="ft-section-title-row ft-doc-title-row ft-doc-ux-title">
                <div>
                    <h2>Documents</h2>
                    <p>Keep required files, document links, task attachments and existing client documents together.</p>
                </div>
                <div class="ft-doc-completion-copy">
                    <strong><?php echo e($receivedRequired); ?>/<?php echo e($required->count()); ?></strong>
                    <span>requirements satisfied</span>
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
                    <div><small>Satisfied</small><b><?php echo e($receivedRequired); ?></b></div>
                </div>
            </div>

            <div class="ft-doc-required-progress" aria-label="Required submission progress">
                <div><span>Required submission progress</span><b><?php echo e($requiredProgress); ?>%</b></div>
                <span class="ft-doc-progress-track"><i style="width: <?php echo e($requiredProgress); ?>%"></i></span>
            </div>

            <?php echo $__env->make('components.jobs.documents.required-document-uploader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('components.jobs.documents.document-library', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </main>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-documents.blade.php ENDPATH**/ ?>