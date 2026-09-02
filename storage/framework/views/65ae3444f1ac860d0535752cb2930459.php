<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job','mentionUsers'=>collect(),'activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'canComment'=>false]));

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

foreach (array_filter((['job','mentionUsers'=>collect(),'activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'canComment'=>false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $activities = $job->activities->values();
    $activityPerPage = max(1, (int) ($job->activity_per_page ?? 10));
    $activityTotal = max(0, (int) ($job->activity_total_count ?? $activities->count()));
    $activityPages = max(1, (int) ceil($activityTotal / $activityPerPage));
    $activityCurrentPage = min(max(1, (int) ($job->activity_current_page ?? $activityPage)), $activityPages);
?>
<section class="section-card activity-wide ft-order-section-card" id="billingSection" x-data="{ open:true }">
    <div class="section-head ft-order-section-head">
        <div><h2>Activity</h2><div class="card-sub">Comments, ownership changes, flags, cancellations, and workflow history.</div></div>
        <div class="activity-head-actions">
            <div class="page-tabs activity-tabs-inline">
                <button type="button" class="page-tab <?php echo e($activityTab==='all'?'active':''); ?>" wire:click="setJobActivityTab('all')">All</button>
                <button type="button" class="page-tab <?php echo e($activityTab==='comments'?'active':''); ?>" wire:click="setJobActivityTab('comments')">Comments</button>
                <button type="button" class="page-tab <?php echo e($activityTab==='history'?'active':''); ?>" wire:click="setJobActivityTab('history')">History</button>
            </div>
            <button type="button" class="section-toggle ft-order-compact-toggle" x-on:click="open=!open" x-text="open ? 'Hide activity' : 'Show activity'">Hide activity</button>
        </div>
    </div>
    <div class="collapse-body" x-show="open">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canComment): ?>
            <div class="activity-composer">
                <div class="avatar"><?php echo e(collect(preg_split('/\s+/', trim((string) auth()->user()?->name)))->filter()->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))->take(2)->implode('') ?: 'SP'); ?></div>
                <div class="composer-box"><div class="composer-tools">B &nbsp;&nbsp; <i>I</i> &nbsp;&nbsp; <u>U</u> &nbsp;&nbsp; • List &nbsp;&nbsp; 1. List</div><textarea class="ft-mention-input" data-rich-text data-rich-text-compact wire:model="jobComment" rows="2" autocomplete="off" data-mention-users="<?php echo e($mentionUsers->toJson()); ?>" placeholder="Write a comment. Type @ to mention someone or paste a screenshot..."></textarea></div>
                <button class="btn primary ft-order-comment-submit" data-rich-text-submit type="button" wire:click="addJobComment" wire:loading.attr="disabled" wire:target="addJobComment">Comment</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="wide-activity-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $isComment = $activity->event === 'job.comment';
                    $isCancellation = $activity->event === 'job.cancelled';
                    $isArtworkRevision = $activity->event === 'job.artwork_revision_requested';
                    $customerComment = trim((string) data_get($activity->meta, 'customer_comment', ''));
                    $isArtworkCustomerComment = $customerComment !== '' && in_array((string) $activity->event, [
                        'job.artwork_emailed_to_order_team',
                        'job.workflow_email_skipped',
                    ], true);
                    $actorName = $activity->user?->name ?? 'System';
                    $actorInitials = collect(preg_split('/\s+/', trim($actorName)))->filter()->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))->take(2)->implode('');
                    $activityFocusKey = $isComment ? 'job-'.$activity->id : null;
                    $activityAnchor = $isComment ? 'job-comment-'.$activity->id : null;
                    $isFocusedComment = $activityFocusKey !== null && $focusComment === $activityFocusKey;
                ?>
                <div <?php if($activityAnchor): ?> id="<?php echo e($activityAnchor); ?>" <?php endif; ?> class="wide-activity <?php echo e($isFocusedComment ? 'is-focused-comment' : ''); ?>" <?php if($isFocusedComment): ?> x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' }))" <?php endif; ?>>
                    <div class="avatar"><?php echo e($actorInitials ?: 'SP'); ?></div>
                    <div>
                        <b>
                            <?php echo e($actorName); ?>

                            <span class="card-sub activity-kind <?php echo e($isArtworkCustomerComment ? 'is-customer-comment' : ''); ?>">
                                <?php echo e($isArtworkCustomerComment ? 'CUSTOMER COMMENT' : ($isComment ? 'COMMENT' : 'CHANGE')); ?>

                            </span>
                        </b>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkCustomerComment): ?>
                            <div class="ft-order-customer-comment-activity">
                                <div class="ft-order-customer-comment-activity__label">Comment sent with artwork</div>
                                <div class="ft-order-customer-comment-activity__copy"><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $customerComment]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($customerComment)]); ?>
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
                                <div class="ft-order-customer-comment-activity__context"><?php echo e($activity->event === 'job.workflow_email_skipped' ? 'Recorded for manual customer handoff' : 'Sent with customer artwork handoff'); ?></div>
                            </div>
                        <?php else: ?>
                            <div class="wide-activity-copy <?php echo e($isCancellation ? 'ft-rich-text-content ft-order-cancellation-activity-copy' : ''); ?>"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkRevision): ?><?php if (isset($component)) { $__componentOriginaleb74e077e078dc73a4ecc7ef913acc03 = $component; } ?>
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
<?php endif; ?><?php else: ?><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
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
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="card-sub"><?php echo e(\Illuminate\Support\Str::headline(str_replace(['job.','task.'], '', (string) $activity->event))); ?></div>
                    </div>
                    <time title="<?php echo e(\App\Support\UserLocalTime::format($activity->created_at, 'M j, Y g:i A')); ?>"><?php echo e($activity->created_at?->diffForHumans()); ?></time>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="empty-stage">No <?php echo e($activityTab==='comments' ? 'comments' : ($activityTab==='history' ? 'history' : 'activity')); ?> yet.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activityTotal > $activityPerPage): ?>
            <div class="activity-pagination"><span>Showing <?php echo e((($activityCurrentPage - 1) * $activityPerPage) + 1); ?>–<?php echo e(min($activityCurrentPage * $activityPerPage, $activityTotal)); ?> of <?php echo e($activityTotal); ?></span><div><button type="button" class="btn small" wire:click="setJobActivityPage(<?php echo e($activityCurrentPage - 1); ?>)" <?php if($activityCurrentPage <= 1): echo 'disabled'; endif; ?>>←</button><span>Page <?php echo e($activityCurrentPage); ?> of <?php echo e($activityPages); ?></span><button type="button" class="btn small" wire:click="setJobActivityPage(<?php echo e($activityCurrentPage + 1); ?>)" <?php if($activityCurrentPage >= $activityPages): echo 'disabled'; endif; ?>>→</button></div></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/activity.blade.php ENDPATH**/ ?>