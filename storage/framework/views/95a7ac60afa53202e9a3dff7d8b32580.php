            <section class="ft-detail-card ft-task-activity-card ft-friendly-activity">
                <div class="ft-activity-head">
                    <div><h2>Activity</h2><p>Comments and task changes, with who changed what and when.</p></div>
                    <div class="ft-activity-tabs"><button type="button" class="<?php echo e($activityTab==='all'?'active':''); ?>" wire:click="setTaskActivityTab('all')">All</button><button type="button" class="<?php echo e($activityTab==='comments'?'active':''); ?>" wire:click="setTaskActivityTab('comments')">Comments</button><button type="button" class="<?php echo e($activityTab==='history'?'active':''); ?>" wire:click="setTaskActivityTab('history')">History</button></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                    <div class="ft-comment-composer ft-friendly-composer ft-rich-comment-composer"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => auth()->user(),'name' => auth()->user()->name,'size' => 32]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->name),'size' => 32]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><textarea class="ft-mention-input" data-rich-text data-rich-text-compact wire:model="taskComment" rows="2" autocomplete="off" data-mention-users='<?php echo json_encode($mentionUsers->values(), 15, 512) ?>' placeholder="Write a comment. Type @ to mention someone or paste a screenshot..."></textarea><button class="ft-new-job-btn" data-rich-text-submit type="button" wire:click="addTaskComment" wire:loading.attr="disabled" wire:target="addTaskComment">Comment</button></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="ft-activity-feed">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $eventLabel = $entry->kind === 'comment' ? 'Comment' : \Illuminate\Support\Str::headline(str_replace(['task.','job.'], '', (string) $entry->event));
                            $actorName = $entry->user?->name ?? 'System';
                            $entryLocalTime = $entry->created_at?->copy()->timezone($displayTimezone);
                        ?>
                        <?php
                            $entryFocusKey = $entry->kind === 'comment' ? 'task-'.$entry->id : null;
                            $entryAnchor = $entry->kind === 'comment' ? 'task-comment-'.$entry->id : null;
                            $isFocusedComment = $entryFocusKey !== null && $focusComment === $entryFocusKey;
                        ?>
                        <article <?php if($entryAnchor): ?> id="<?php echo e($entryAnchor); ?>" <?php endif; ?> class="ft-activity-entry <?php echo e($entry->kind==='comment' ? 'is-comment' : 'is-history'); ?> <?php echo e($isFocusedComment ? 'is-focused-comment' : ''); ?>" <?php if($isFocusedComment): ?> x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' }))" <?php endif; ?>>
                            <div class="ft-activity-entry-avatar"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $entry->user,'name' => $actorName,'size' => 32]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entry->user),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actorName),'size' => 32]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><span><?php echo e($entry->kind==='comment' ? '💬' : '↻'); ?></span></div>
                            <div class="ft-activity-entry-content">
                                <div class="ft-activity-entry-head"><div><b><?php echo e($actorName); ?></b><span class="ft-activity-kind <?php echo e($entry->kind==='comment' ? 'comment' : 'history'); ?>"><?php echo e($entry->kind==='comment' ? 'Comment' : 'Change'); ?></span></div><div class="ft-activity-entry-actions"><time title="<?php echo e($entryLocalTime?->format('M j, Y g:i A')); ?> <?php echo e($displayTimezone); ?>"><?php echo e($entry->created_at?->diffForHumans()); ?></time><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canModerateTaskActivity && $entry->event !== 'task.moderation_deleted'): ?><button type="button" class="ft-activity-delete-action" wire:click="<?php echo e($entry->kind === 'comment' ? 'deleteTaskComment('.$entry->id.')' : 'deleteTaskActivity('.$entry->id.')'); ?>" wire:confirm="Delete this <?php echo e($entry->kind === 'comment' ? 'comment/mention' : 'task activity'); ?>? The deletion itself will remain recorded in activity.">Delete</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
                                <div class="ft-rich-text-content"><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $entry->body]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entry->body)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $attributes = $__attributesOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $component = $__componentOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__componentOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?></div>
                                <div class="ft-activity-entry-meta"><span><?php echo e($eventLabel); ?></span><span>•</span><span><?php echo e($entryLocalTime?->format('M j, Y · g:i A')); ?></span></div>
                            </div>
                        </article>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="empty-state">No <?php echo e($activityTab==='comments' ? 'comments' : ($activityTab==='history' ? 'changes' : 'activity')); ?> yet.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($timelineTotal > $activityPerPage): ?>
                    <div class="ft-activity-pagination">
                        <span>Showing <?php echo e((($timelineCurrentPage - 1) * $activityPerPage) + 1); ?>–<?php echo e(min($timelineCurrentPage * $activityPerPage, $timelineTotal)); ?> of <?php echo e($timelineTotal); ?></span>
                        <div>
                            <button type="button" wire:click="setTaskActivityPage(<?php echo e($timelineCurrentPage - 1); ?>)" <?php if($timelineCurrentPage <= 1): echo 'disabled'; endif; ?>>Previous</button>
                            <span>Page <?php echo e($timelineCurrentPage); ?> of <?php echo e($timelinePages); ?></span>
                            <button type="button" wire:click="setTaskActivityPage(<?php echo e($timelineCurrentPage + 1); ?>)" <?php if($timelineCurrentPage >= $timelinePages): echo 'disabled'; endif; ?>>Next</button>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/task-detail/activity.blade.php ENDPATH**/ ?>