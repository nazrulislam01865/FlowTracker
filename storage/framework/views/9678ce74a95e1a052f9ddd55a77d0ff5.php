<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job','compact'=>false,'mentionUsers'=>collect(),'activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'canComment'=>null]));

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

foreach (array_filter((['job','compact'=>false,'mentionUsers'=>collect(),'activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'canComment'=>null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $canComment = $canComment === null ? false : (bool) $canComment;
    // JobService already applies the selected activity filter and database
    // pagination. Keeping only the visible page here prevents large Orders
    // from hydrating their complete activity history on every render.
    $activities = $job->activities->values();
    $activityPerPage = max(1, (int) ($job->activity_per_page ?? 10));
    $activityTotal = max(0, (int) ($job->activity_total_count ?? $activities->count()));
    $activityPages = max(1, (int) ceil($activityTotal / $activityPerPage));
    $activityCurrentPage = min(max(1, (int) ($job->activity_current_page ?? $activityPage)), $activityPages);
?>
<section class="ft-detail-card ft-activity-card ft-friendly-activity <?php echo e($compact ? 'compact' : ''); ?>">
    <div class="ft-activity-head">
        <div>
            <h2>Activity</h2>
            <p>Comments and Order changes, with who changed what and when.</p>
        </div>
        <div class="ft-activity-tabs">
            <button type="button" class="<?php echo e($activityTab==='all'?'active':''); ?>" wire:click="setJobActivityTab('all')">All</button>
            <button type="button" class="<?php echo e($activityTab==='comments'?'active':''); ?>" wire:click="setJobActivityTab('comments')">Comments</button>
            <button type="button" class="<?php echo e($activityTab==='history'?'active':''); ?>" wire:click="setJobActivityTab('history')">History</button>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canComment): ?>
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
            <textarea class="ft-mention-input" data-rich-text data-rich-text-compact wire:model="jobComment" rows="2" autocomplete="off" data-mention-users="<?php echo e($mentionUsers->toJson()); ?>" placeholder="Write a comment. Type @ to mention someone or paste a screenshot..."></textarea>
            <button class="ft-new-job-btn" data-rich-text-submit type="button" wire:click="addJobComment" wire:loading.attr="disabled" wire:target="addJobComment">Comment</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ft-activity-feed">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $isComment = $activity->event === 'job.comment';
                $isArtworkRevision = $activity->event === 'job.artwork_revision_requested';
                $actorName = $activity->user?->name ?? 'System';
                $eventLabel = $isComment ? 'Comment' : \Illuminate\Support\Str::headline(str_replace(['job.','task.'], '', (string) $activity->event));
            ?>
            <?php
                $activityFocusKey = $isComment ? 'job-'.$activity->id : null;
                $activityAnchor = $isComment ? 'job-comment-'.$activity->id : null;
                $isFocusedComment = $activityFocusKey !== null && $focusComment === $activityFocusKey;
            ?>
            <article <?php if($activityAnchor): ?> id="<?php echo e($activityAnchor); ?>" <?php endif; ?> class="ft-activity-entry <?php echo e($isComment ? 'is-comment' : 'is-history'); ?> <?php echo e($isFocusedComment ? 'is-focused-comment' : ''); ?>" <?php if($isFocusedComment): ?> x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' }))" <?php endif; ?>>
                <div class="ft-activity-entry-avatar">
                    <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
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
<?php endif; ?>
                    <span><?php echo e($isComment ? '💬' : '↻'); ?></span>
                </div>
                <div class="ft-activity-entry-content">
                    <div class="ft-activity-entry-head">
                        <div><b><?php echo e($actorName); ?></b><span class="ft-activity-kind <?php echo e($isComment ? 'comment' : 'history'); ?>"><?php echo e($isComment ? 'Comment' : 'Change'); ?></span></div>
                        <time title="<?php echo e(\App\Support\UserLocalTime::format($activity->created_at, 'M j, Y g:i A')); ?>"><?php echo e($activity->created_at?->diffForHumans()); ?></time>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkRevision): ?>
                        <?php if (isset($component)) { $__componentOriginaleb74e077e078dc73a4ecc7ef913acc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleb74e077e078dc73a4ecc7ef913acc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.revision-activity-content','data' => ['activity' => $activity]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.revision-activity-content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['activity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleb74e077e078dc73a4ecc7ef913acc03)): ?>
<?php $attributes = $__attributesOriginaleb74e077e078dc73a4ecc7ef913acc03; ?>
<?php unset($__attributesOriginaleb74e077e078dc73a4ecc7ef913acc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleb74e077e078dc73a4ecc7ef913acc03)): ?>
<?php $component = $__componentOriginaleb74e077e078dc73a4ecc7ef913acc03; ?>
<?php unset($__componentOriginaleb74e077e078dc73a4ecc7ef913acc03); ?>
<?php endif; ?>
                    <?php else: ?>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ft-activity-entry-meta"><span><?php echo e($eventLabel); ?></span><span>•</span><span><?php echo e(\App\Support\UserLocalTime::format($activity->created_at, 'M j, Y · g:i A')); ?></span></div>
                </div>
            </article>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="empty-state">No <?php echo e($activityTab==='comments' ? 'comments' : ($activityTab==='history' ? 'changes' : 'activity')); ?> yet.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activityTotal > $activityPerPage): ?>
        <div class="ft-activity-pagination">
            <span>Showing <?php echo e((($activityCurrentPage - 1) * $activityPerPage) + 1); ?>–<?php echo e(min($activityCurrentPage * $activityPerPage, $activityTotal)); ?> of <?php echo e($activityTotal); ?></span>
            <div>
                <button type="button" wire:click="setJobActivityPage(<?php echo e($activityCurrentPage - 1); ?>)" <?php if($activityCurrentPage <= 1): echo 'disabled'; endif; ?>>Previous</button>
                <span>Page <?php echo e($activityCurrentPage); ?> of <?php echo e($activityPages); ?></span>
                <button type="button" wire:click="setJobActivityPage(<?php echo e($activityCurrentPage + 1); ?>)" <?php if($activityCurrentPage >= $activityPages): echo 'disabled'; endif; ?>>Next</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-activity.blade.php ENDPATH**/ ?>