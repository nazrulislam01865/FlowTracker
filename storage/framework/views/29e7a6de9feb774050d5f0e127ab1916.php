<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'context' => [], 'jobDocumentUploads' => []]));

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

foreach (array_filter((['job', 'context' => [], 'jobDocumentUploads' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $canUpload = (bool) ($context['canUploadDocument'] ?? false);
    $canDelete = (bool) ($context['canDeleteDocument'] ?? false);
    $canExport = (bool) ($context['canExportDocument'] ?? false);
    // Task evidence (Purchase Order, Artwork, Sample Approval, etc.) belongs
    // against its workflow task. This section is intentionally order-level only.
    $otherDocuments = $job->documents
        ->filter(fn ($document) => blank($document->task_id))
        ->sortByDesc('created_at')
        ->values();
?>
<section class="section-card attachments-card ft-order-section-card ft-order-attachments-card" data-order-disclosure>
    <div class="section-head ft-order-section-head">
        <h2>Other document <span class="status-pill"><?php echo e($otherDocuments->count()); ?></span></h2>
        <button type="button" class="section-toggle ft-order-disclosure-button ft-order-compact-toggle" data-order-disclosure-trigger aria-expanded="true">Hide files</button>
    </div>
    <div class="collapse-body ft-order-disclosure-body" data-order-disclosure-body>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUpload): ?>
            <div class="attachment-drop ft-order-attachment-drop <?php echo e($errors->has('jobDocumentUploads') || $errors->has('jobDocumentUploads.*') ? 'has-error' : ''); ?>">
                <label data-file-dropzone data-auto-upload-method="uploadGeneralOrderDocuments" for="orderGeneralAttachment-<?php echo e($job->id); ?>"><b>⌕ &nbsp; Drop files here or <span>browse</span></b><div class="card-sub"><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></div><input id="orderGeneralAttachment-<?php echo e($job->id); ?>" type="file" wire:model="jobDocumentUploads" multiple accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>"></label>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobDocumentUploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error ft-order-upload-error"><?php echo e($message); ?> <button type="button" wire:click="clearJobDocumentUploads">Clear</button></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($jobDocumentUploads ?? [])): ?>
                <div class="ft-order-pending-files">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobDocumentUploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php ($name = method_exists($upload, 'getClientOriginalName') ? $upload->getClientOriginalName() : 'File '.($index + 1)); ?>
                        <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-pending-file-'.e($index).'-'.e(md5($name)).''; ?>wire:key="order-pending-file-<?php echo e($index); ?>-<?php echo e(md5($name)); ?>"><span><?php echo e($name); ?></span><button type="button" wire:click="removeJobDocumentUpload(<?php echo e($index); ?>)">Remove</button></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="attachment-drop readonly"><div><b>⌕ &nbsp; Other documents</b><div class="card-sub">You have read-only access.</div></div></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="attachment-list ft-order-file-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $otherDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="attachment-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-document-'.e($document->id).''; ?>wire:key="order-document-<?php echo e($document->id); ?>">
                    <div class="file-icon ft-order-file-icon"><?php echo e(strtoupper(pathinfo($document->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></div>
                    <div style="flex:1"><b><?php echo e($document->name); ?></b><div class="card-sub"><?php echo e($document->task?->title ?: 'Order document'); ?> · <?php echo e($document->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($document->created_at, 'M j, Y, g:i A')); ?></div></div>
                    <a class="btn small" href="<?php echo e(route('documents.open', $document)); ?>" target="_blank" rel="noopener">Open</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExport): ?><a class="btn small" href="<?php echo e(route('documents.download', $document)); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?><button type="button" class="btn danger small" wire:click="deleteJobDocument(<?php echo e($document->id); ?>)" wire:confirm="Delete this document link?">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="empty-stage ft-order-empty-state">No other documents yet.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/attachments.blade.php ENDPATH**/ ?>