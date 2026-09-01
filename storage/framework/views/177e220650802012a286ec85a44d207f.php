<section class="ft-detail-card ft-attachment-card">
    <h2>Attachments <span><?php echo e($inquiry->documents_count); ?></span></h2>
    <div class="ft-upload-zone compact ft-task-upload-zone">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && $canCreateDocuments): ?>
            <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone data-auto-upload-method="uploadInquiryFiles" for="inquiryOverviewUpload-<?php echo e($inquiry->id); ?>">
                <input id="inquiryOverviewUpload-<?php echo e($inquiry->id); ?>" type="file" wire:model="inquiryUploads" multiple accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>">
                <span class="ft-paperclip">⌕</span>
                <div>Drop files here or <strong>browse</strong><small data-drop-status><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></small></div>
            </label>
        <?php elseif(!$canEditInquiry || !$canCreateDocuments): ?>
            <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to Inquiry attachments.</small></div></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($inquiryUploads ?? [])): ?>
        <div class="ft-upload-ready-row ft-auto-upload-state" aria-live="polite">
            <span>Uploading and linking <?php echo e(count($inquiryUploads ?? [])); ?> file<?php echo e(count($inquiryUploads ?? []) === 1 ? '' : 's'); ?> automatically…</span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['inquiryUploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['inquiryUploads.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryDocuments): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="ft-attachment-row ft-inquiry-attachment-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-overview-doc-'.e($document->id).''; ?>wire:key="inquiry-overview-doc-<?php echo e($document->id); ?>">
                <span class="ft-file-type"><?php echo e(strtoupper(pathinfo($document->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span>
                <div class="ft-inquiry-attachment-main">
                    <b title="<?php echo e($document->name); ?>"><?php echo e($document->name); ?></b>
                    <small><?php echo e($document->created_at ? \App\Support\UserLocalTime::format($document->created_at, 'M j, Y, g:i A') : '—'); ?></small>
                </div>
                <div class="ft-inquiry-attachment-actions">
                    <a class="ft-link-blue ft-inquiry-attachment-open" href="<?php echo e(route('inquiries.documents.open', $document)); ?>" target="_blank" rel="noopener">Open</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocuments): ?><a class="ft-link-blue ft-inquiry-attachment-download" href="<?php echo e(route('inquiries.documents.download', $document)); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && $canDeleteDocuments): ?>
                        <button type="button" class="ft-doc-delete-button" wire:click="deleteInquiryDocument(<?php echo e($document->id); ?>)" wire:confirm="Remove this attachment from the Inquiry?" aria-label="Remove <?php echo e($document->name); ?>">×</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryDocuments->isEmpty()): ?><div class="empty-state">No Inquiry attachments yet.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryDocuments->lastPage() > 1): ?>
            <div class="ft-activity-pagination">
                <span>Showing <?php echo e($inquiryDocuments->firstItem() ?? 0); ?>–<?php echo e($inquiryDocuments->lastItem() ?? 0); ?> of <?php echo e($inquiryDocuments->total()); ?></span>
                <div><button type="button" wire:click="previousPage('inquiryDocumentsPage')" <?php if($inquiryDocuments->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button><span>Page <?php echo e($inquiryDocuments->currentPage()); ?> of <?php echo e($inquiryDocuments->lastPage()); ?></span><button type="button" wire:click="nextPage('inquiryDocumentsPage')" <?php if(!$inquiryDocuments->hasMorePages()): echo 'disabled'; endif; ?>>Next</button></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <p class="ft-upload-note">Every file uploaded here is linked to this Inquiry and remains available from Inquiry Documents.</p>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/_attachments.blade.php ENDPATH**/ ?>