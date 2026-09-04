            <?php
                $taskAutomationKey = app(\App\Services\OrderWorkflowActionService::class)->automationKey($task);
                $isArtworkUploadTask = $taskAutomationKey === 'ART_PREPARE_UPLOAD';
                $taskAttachmentDocuments = $task->documents->sortByDesc('created_at')->values();

                // Current Artwork can contain mixed versions after a selective
                // revision. Accepted files keep their existing version; only the
                // files actually replaced receive the next version number.
                $latestArtworkDocuments = $isArtworkUploadTask
                    ? ($task->relationLoaded('currentArtworkDocuments')
                        ? collect($task->getRelation('currentArtworkDocuments'))->sortBy('id')->values()
                        : app(\App\Services\DocumentService::class)->currentArtworkDocuments($task, $taskAttachmentDocuments))
                    : collect();
                $visibleTaskDocuments = $isArtworkUploadTask
                    ? $latestArtworkDocuments
                    : $taskAttachmentDocuments;
                $visibleAttachmentCount = $visibleTaskDocuments->count()
                    + ($task->relationLoaded('links') ? $task->links->count() : 0);
            ?>
            <section class="ft-detail-card ft-attachment-card">
                <h2>Attachments <span><?php echo e($visibleAttachmentCount); ?></span></h2>
                <div class="ft-upload-zone compact ft-task-upload-zone">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                        <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone data-auto-upload-method="uploadSelectedTaskDocuments" for="taskDocumentUpload-<?php echo e($task->id); ?>">
                            <input id="taskDocumentUpload-<?php echo e($task->id); ?>" type="file" wire:model="taskDocumentUploads" multiple accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>">
                            <span class="ft-paperclip" aria-hidden="true">⌕</span>
                            <div class="ft-task-upload-copy">
                                <div class="ft-task-upload-title">Drop files here or <strong>browse</strong></div>
                                <small data-drop-status><?php echo e($taskDocumentName ? 'Required document: '.$taskDocumentName.' · ' : ''); ?><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></small>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocumentInstructions !== ''): ?>
                                    <small class="ft-task-upload-instruction"><?php echo e($taskDocumentInstructions); ?></small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </label>
                    <?php else: ?>
                        <div class="ft-task-upload-drop ft-task-upload-readonly">
                            <span class="ft-paperclip" aria-hidden="true">⌕</span>
                            <div class="ft-task-upload-copy">
                                <div class="ft-task-upload-title">Attachments</div>
                                <small>You have read-only access to task attachments.</small>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($taskDocumentUploads ?? [])): ?>
                    <div class="ft-upload-ready-row ft-auto-upload-state" aria-live="polite"><span>Uploading and linking <?php echo e(count($taskDocumentUploads ?? [])); ?> file<?php echo e(count($taskDocumentUploads ?? [])===1?'':'s'); ?> automatically…</span></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskDocumentUploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskDocumentUploads.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($visibleTaskDocuments->isNotEmpty()): ?>
                    <div class="ft-task-attachment-list" aria-label="Task attachments">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $visibleTaskDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-order-task-document-row ft-task-detail-document-row <?php echo e($isArtworkUploadTask ? 'is-latest-artwork' : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'task-detail-document-'.e($doc->id).''; ?>wire:key="task-detail-document-<?php echo e($doc->id); ?>">
                                <span class="ft-order-task-file-type"><?php echo e(strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span>
                                <div class="ft-order-task-file-copy">
                                    <b title="<?php echo e($doc->name); ?>"><?php echo e($doc->name); ?></b>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc->note): ?><span class="ft-order-task-file-note"><?php echo e($doc->note); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <small>
                                        <?php echo e($doc->category ?: 'Task attachment'); ?> · <?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A')); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkUploadTask): ?>
                                            · Latest
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </small>
                                </div>
                                <div class="ft-order-task-file-actions">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkUploadTask): ?>
                                        <em class="ft-task-latest-artwork-badge">Latest</em>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <a href="<?php echo e(route('documents.open', $doc)); ?>" target="_blank" rel="noopener">Open</a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','export')): ?><a href="<?php echo e(route('documents.download', $doc)); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?>
                                        <button type="button" wire:click="deleteSelectedTaskDocument(<?php echo e($doc->id); ?>)" wire:loading.attr="disabled" wire:target="deleteSelectedTaskDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?" title="Remove attachment" aria-label="Remove <?php echo e($doc->name); ?>">×</button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->relationLoaded('links') && $task->links->isNotEmpty()): ?>
                    <div class="ft-task-attachment-list ft-task-external-link-list" aria-label="Task external links">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $task->links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-order-task-link-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'task-detail-link-'.e($taskLink->id).''; ?>wire:key="task-detail-link-<?php echo e($taskLink->id); ?>">
                                <span class="ft-order-task-link-type" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                </span>
                                <div class="ft-order-task-link-copy">
                                    <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo e($taskLink->url); ?>"><?php echo e(\Illuminate\Support\Str::limit($taskLink->url, 110)); ?></a>
                                    <small>External link · <?php echo e($taskLink->created_at ? \App\Support\UserLocalTime::format($taskLink->created_at, 'M j, Y, g:i A') : '—'); ?></small>
                                </div>
                                <div class="ft-order-task-link-actions">
                                    <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer">Open ↗</a>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkUploadTask): ?>
                    <p class="ft-upload-note">Every file in the latest artwork revision is shown here. Older artwork revisions remain available in document/version history.</p>
                <?php else: ?>
                    <p class="ft-upload-note">Files and external links attached to this task remain available here and in the Order taskflow. Either can satisfy a Task Pack document requirement.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/task-detail/attachments.blade.php ENDPATH**/ ?>