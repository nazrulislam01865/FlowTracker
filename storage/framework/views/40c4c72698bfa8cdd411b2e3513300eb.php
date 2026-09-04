<section class="ft-detail-card ft-task-activity-card ft-friendly-activity ft-inquiry-overview-activity-card">
    <div class="ft-activity-head">
        <div><h2>Activity</h2><p>Comments and Inquiry changes, with who changed what and when.</p></div>
        <div class="ft-activity-tabs">
            <button type="button" class="<?php echo e($inquiryActivityTab === 'all' ? 'active' : ''); ?>" wire:click="setInquiryActivityTab('all')">All</button>
            <button type="button" class="<?php echo e($inquiryActivityTab === 'comments' ? 'active' : ''); ?>" wire:click="setInquiryActivityTab('comments')">Comments</button>
            <button type="button" class="<?php echo e($inquiryActivityTab === 'history' ? 'active' : ''); ?>" wire:click="setInquiryActivityTab('history')">History</button>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry): ?>
        <div class="ft-comment-composer ft-friendly-composer ft-rich-comment-composer">
            <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
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
<?php endif; ?>
            <textarea class="ft-mention-input" data-rich-text data-rich-text-compact wire:model="inquiryComment" rows="2" autocomplete="off" data-mention-users='<?php echo json_encode($inquiryMentionUsers->values(), 15, 512) ?>' placeholder="Write a comment. Type @ to mention someone or paste a screenshot..."></textarea>
            <button class="ft-new-job-btn" data-rich-text-submit type="button" wire:click="addInquiryComment" wire:loading.attr="disabled" wire:target="addInquiryComment">Comment</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ft-activity-feed">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inquiryActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $isComment = $activity->event === 'inquiry.comment';
                $actorName = $activity->user?->name ?? 'System';
                $eventLabel = $isComment ? 'Comment' : str($activity->event)->after('inquiry.')->replace('_',' ')->title();
                $activityTaskId = (int) data_get($activity->meta, 'inquiry_task_id', 0);
                $canModerateTaskActivity = $activityTaskId > 0 && app(\App\Services\AccessControlService::class)->isAdministrator(auth()->user()) && $activity->event !== 'inquiry.task_moderation_deleted';
            ?>
            <article class="ft-activity-entry <?php echo e($isComment ? 'is-comment' : 'is-history'); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-overview-activity-'.e($activity->id).''; ?>wire:key="inquiry-overview-activity-<?php echo e($activity->id); ?>">
                <div class="ft-activity-entry-avatar"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $activity->user,'name' => $actorName,'size' => 32]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->user),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actorName),'size' => 32]); ?>
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
<?php endif; ?><span><?php echo e($isComment ? '💬' : '↻'); ?></span></div>
                <div class="ft-activity-entry-content">
                    <div class="ft-activity-entry-head"><div><b><?php echo e($actorName); ?></b><span class="ft-activity-kind <?php echo e($isComment ? 'comment' : 'history'); ?>"><?php echo e($isComment ? 'Comment' : 'Change'); ?></span></div><div class="ft-activity-entry-actions"><time><?php echo e($activity->created_at?->diffForHumans()); ?></time><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canModerateTaskActivity): ?><button type="button" class="ft-activity-delete-action" wire:click="deleteInquiryTaskActivity(<?php echo e($activity->id); ?>)" wire:confirm="Delete this Inquiry task comment/mention/activity? The deletion itself will remain recorded in activity.">Delete</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
                    <div class="ft-rich-text-content"><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $activity->description]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->description)]); ?>
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
                    <div class="ft-activity-entry-meta"><span><?php echo e($eventLabel); ?></span><span>•</span><span><?php echo e($activity->created_at ? \App\Support\UserLocalTime::format($activity->created_at, 'M j, Y · g:i A') : '—'); ?></span></div>
                </div>
            </article>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="empty-state">No <?php echo e($inquiryActivityTab === 'comments' ? 'comments' : ($inquiryActivityTab === 'history' ? 'changes' : 'activity')); ?> yet.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryActivities && $inquiryActivities->lastPage() > 1): ?>
        <div class="ft-activity-pagination">
            <span>Showing <?php echo e($inquiryActivities->firstItem() ?? 0); ?>–<?php echo e($inquiryActivities->lastItem() ?? 0); ?> of <?php echo e($inquiryActivities->total()); ?></span>
            <div><button type="button" wire:click="previousPage('inquiryActivityPage')" <?php if($inquiryActivities->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button><span>Page <?php echo e($inquiryActivities->currentPage()); ?> of <?php echo e($inquiryActivities->lastPage()); ?></span><button type="button" wire:click="nextPage('inquiryActivityPage')" <?php if(!$inquiryActivities->hasMorePages()): echo 'disabled'; endif; ?>>Next</button></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/_activity.blade.php ENDPATH**/ ?>