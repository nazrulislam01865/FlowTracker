            <section class="ft-detail-card ft-job-documents-table ft-doc-library-card">
                <div class="ft-doc-library-heading">
                    <div>
                        <h3>Job documents</h3>
                        <p>Organized by workflow phase so you can see what is missing without scanning a table.</p>
                    </div>
                    <span class="ft-soft-pill <?php echo e($missingRequired ? 'amber' : 'green'); ?>"><?php echo e($missingRequired ? $missingRequired.' required missing' : 'All requirements satisfied'); ?></span>
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
                                // task_id is the authoritative requirement link; the
                                // stored category label may be generic on legacy files.
                                return $job->documents
                                    ->where('task_id', $requirement->task->id)
                                    ->pluck('id');
                            })->map(fn($id)=>(int)$id)->unique();
                            $phaseAttachments = $phaseDocuments->reject(fn($doc)=>$requiredDocumentIds->contains((int)$doc->id))->values();
                            $openPhase = (int)($job->workflow_phase_id ?? 0) === (int)$phase->id || ((int)($job->workflow_phase_id ?? 0) === 0 && (int)$phase->sequence === 1);
                        ?>

                        <details class="ft-doc-phase-group" style="<?php echo e(\App\Support\MasterColor::style($phase->color)); ?>" <?php if ($openPhase): ?>open<?php endif; ?>>
                            <summary class="ft-doc-phase-summary">
                                <span class="ft-doc-phase-chevron">›</span>
                                <b class="ft-doc-phase-number"><?php echo e($phase->sequence); ?></b>
                                <span class="ft-doc-phase-copy">
                                    <strong><?php echo e($phase->name); ?></strong>
                                    <small><?php echo e($phaseRequiredReceived); ?> of <?php echo e($phaseRequirements->count()); ?> requirements satisfied · <?php echo e($phaseDocuments->count()); ?> file<?php echo e($phaseDocuments->count()===1?'':'s'); ?></small>
                                </span>
                                <?php if ($phaseMissing): ?>
                                    <span class="ft-soft-pill amber"><?php echo e($phaseMissing); ?> needs action</span>
                                <?php elseif ($phaseRequirements->isNotEmpty()): ?>
                                    <span class="ft-soft-pill green">Complete</span>
                                <?php else: ?>
                                    <span class="ft-soft-pill gray">No requirements</span>
                                <?php endif; ?>
                            </summary>

                            <div class="ft-doc-phase-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $docs = $job->documents->where('task_id',$requirement->task->id)->values();
                                        $links = \App\Support\JobDetailPresenter::taskLinks($job, $requirement->task);
                                    ?>
                                    <article class="ft-doc-requirement-card <?php echo e($requirement->complete ? 'is-complete' : 'needs-action'); ?>">
                                        <div class="ft-doc-requirement-main">
                                            <span class="ft-doc-requirement-state"><?php echo e($requirement->complete ? '✓' : '!'); ?></span>
                                            <div class="ft-doc-requirement-copy">
                                                <div class="ft-doc-requirement-title-line">
                                                    <b><?php echo e($requirement->name); ?></b>
                                                    <span class="ft-soft-pill <?php echo e($requirement->complete ? 'green' : 'amber'); ?>"><?php echo e($requirement->complete ? 'Satisfied' : 'Required'); ?></span>
                                                </div>
                                                <small>Task: <?php echo e($requirement->task->title); ?></small>
                                                <?php if ($docs->isEmpty() && $links->isEmpty()): ?>
                                                    <p>No file or document link has been submitted for this requirement yet.</p>
                                                <?php else: ?>
                                                    <p>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($docs->isNotEmpty()): ?><?php echo e($docs->count()); ?> file<?php echo e($docs->count()===1?'':'s'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($docs->isNotEmpty() && $links->isNotEmpty()): ?> · <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($links->isNotEmpty()): ?><?php echo e($links->count()); ?> link<?php echo e($links->count()===1?'':'s'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        submitted for this requirement.
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (! $requirement->complete && $canUploadDocument): ?>
                                                <button class="ft-outline-btn ft-doc-requirement-upload" type="button" x-on:click="await $wire.set('jobDocumentTaskId', <?php echo e($requirement->task->id); ?>); document.getElementById('jobDocumentUpload-<?php echo e($job->id); ?>').click()">Upload file</button>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($docs->isNotEmpty()): ?>
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
                                                            <?php if (auth()->user()->canModule('documents','delete')): ?>
                                                                <button class="ft-doc-delete-button" type="button" wire:click="deleteJobDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?" aria-label="Delete <?php echo e($doc->name); ?>">×</button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($links->isNotEmpty()): ?>
                                            <div class="ft-doc-linked-files">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <div class="ft-doc-linked-file">
                                                        <span class="ft-file-icon sheet">↗</span>
                                                        <div class="ft-doc-linked-file-copy">
                                                            <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer"><?php echo e(\Illuminate\Support\Str::limit($taskLink->url, 100)); ?></a>
                                                            <small>External document link · <?php echo e($taskLink->created_at ? \App\Support\UserLocalTime::format($taskLink->created_at, 'M j, Y, g:i A') : '—'); ?></small>
                                                        </div>
                                                        <span class="ft-soft-pill green">Accepted</span>
                                                        <div class="ft-doc-linked-actions">
                                                            <a class="ft-link-blue" href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer">Open ↗</a>
                                                        </div>
                                                    </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </article>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div class="ft-doc-empty-phase">No Task Pack document requirements in this phase.</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if ($phaseAttachments->isNotEmpty()): ?>
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
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <?php if ($unlinkedDocs->isNotEmpty()): ?>
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
                    <?php endif; ?>
                </div>
            </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/documents/document-library.blade.php ENDPATH**/ ?>