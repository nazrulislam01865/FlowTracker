<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'expanded' => false]));

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

foreach (array_filter((['job', 'expanded' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $currentTasks = \App\Support\BoardPresenter::currentTasks($job);
    $openTasks = \App\Support\BoardPresenter::openTasks($job);
    $nextTask = \App\Support\BoardPresenter::nextTask($job);
    $team = \App\Support\BoardPresenter::team($job);
    $completedCurrent = $currentTasks->filter(fn($task) => $task->completed_at || $task->status === 'Completed')->count();
    $attentionActive = (bool) ($job->attention_requested ?? false) || (bool) $job->needs_attention;
?>
<article <?php echo e($attributes->class(['ft-job-card', 'is-expanded' => $expanded])); ?>>

    <div class="ft-job-card-top">
        <div class="ft-job-card-signals">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attentionActive): ?>
                <span class="ft-health-pill red"><span class="ft-health-dot"></span>Needs Attention</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="ft-phase-age">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                <?php echo e(\App\Support\BoardPresenter::phaseDays($job)); ?>d in phase
            </span>
        </div>
        <button type="button" class="ft-card-kebab" wire:click.stop="toggleJobCard(<?php echo e($job->id); ?>)" title="<?php echo e($expanded ? 'Collapse card' : 'Expand card'); ?>" aria-label="<?php echo e($expanded ? 'Collapse card' : 'Expand card'); ?>">
            <span></span><span></span><span></span>
        </button>
    </div>

    <h3 class="ft-job-card-title"><a href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->title); ?></a></h3>
    <div class="ft-job-reference">
        <a href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
        <span>·</span>
        <span><?php echo e($job->client?->name ?? 'No client'); ?></span>
    </div>

    <div class="ft-job-products">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/></svg>
        <span><b><?php echo e(\App\Support\BoardPresenter::productCount($job)); ?> <?php echo e(\Illuminate\Support\Str::plural('product', \App\Support\BoardPresenter::productCount($job))); ?></b></span>
        <span class="ft-dot-separator">·</span>
        <span><?php echo e(number_format(\App\Support\BoardPresenter::totalUnits($job))); ?> units</span>
    </div>

    <div class="ft-job-progress-head"><span>Overall progress</span><b><?php echo e($job->progress); ?>%</b></div>
    <div class="ft-job-progress"><span style="width:<?php echo e(max(0,min(100,(int)$job->progress))); ?>%"></span></div>

    <div class="ft-job-stats">
        <span class="ft-stat-chip neutral">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h12M8 12h12M8 18h12"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
            <?php echo e($openTasks->count()); ?> open
        </span>
        <span class="ft-stat-chip amber">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            <?php echo e(\App\Support\BoardPresenter::dueSoonCount($job)); ?> due soon
        </span>
        <span class="ft-stat-chip green">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
            <?php echo e(\App\Support\BoardPresenter::blockedCount($job)); ?> blocked
        </span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expanded): ?>
        <div class="ft-phase-task-panel">
            <div class="ft-phase-task-head">
                <span>PHASE TASKS</span>
                <span><?php echo e($completedCurrent); ?> of <?php echo e($currentTasks->count()); ?> complete</span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 15 6-6 6 6"/></svg>
            </div>
            <div class="ft-phase-task-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $currentTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phaseTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isNext = $nextTask && $nextTask->id === $phaseTask->id;
                    ?>
                    <a class="ft-phase-task-row" href="<?php echo e(route('jobs.index', ['open'=>$job->id, 'task'=>$phaseTask->id])); ?>" wire:navigate>
                        <div class="ft-phase-task-main">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNext): ?><span class="ft-next-badge">NEXT</span><?php else: ?><span class="ft-next-spacer"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="ft-phase-task-copy">
                                <b><?php echo e($phaseTask->title); ?></b>
                                <div class="ft-phase-task-person">
                                    <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $phaseTask->assignee,'name' => $phaseTask->assignee?->name ?? 'Unassigned','size' => 30]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseTask->assignee),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseTask->assignee?->name ?? 'Unassigned'),'size' => 30]); ?>
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
                                    <span><?php echo e($phaseTask->assignee?->name ?? 'Unassigned'); ?></span>
                                </div>
                            </div>
                        </div>
                        <span class="ft-task-status-mini <?php echo e($phaseTask->status === 'Completed' ? 'done' : (str_contains($phaseTask->status,'Waiting') ? 'waiting' : ($phaseTask->status === 'Blocked' ? 'blocked' : 'ready'))); ?>"><?php echo e($phaseTask->status); ?></span>
                        <span class="ft-phase-task-due <?php echo e(($phaseTask->due_date && \App\Support\UserLocalTime::isDatePast($phaseTask->due_date)) && !$phaseTask->completed_at ? 'overdue' : ''); ?>"><?php echo e($phaseTask->due_date?->format('M j') ?? '—'); ?></span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-phase-task-empty">No phase tasks configured.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php elseif($nextTask): ?>
        <a class="ft-next-action" href="<?php echo e(route('jobs.index', ['open'=>$job->id, 'task'=>$nextTask->id])); ?>" wire:navigate>
            <span class="ft-next-action-label">NEXT ACTION</span>
            <b><?php echo e($nextTask->title); ?></b>
            <div class="ft-next-action-meta">
                <span class="ft-next-assignee"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $nextTask->assignee,'name' => $nextTask->assignee?->name ?? 'Unassigned','size' => 34]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nextTask->assignee),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nextTask->assignee?->name ?? 'Unassigned'),'size' => 34]); ?>
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
<?php endif; ?> <?php echo e($nextTask->assignee?->name ?? 'Unassigned'); ?></span>
                <span class="ft-next-divider"></span>
                <span class="ft-next-due <?php echo e(($nextTask?->due_date && \App\Support\UserLocalTime::isDatePast($nextTask->due_date)) ? 'overdue' : ''); ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>
                    Due <?php echo e($nextTask->due_date?->format('M j') ?? '—'); ?>

                </span>
            </div>
        </a>
    <?php elseif($job->next_action): ?>
        <div class="ft-next-action">
            <span class="ft-next-action-label">NEXT ACTION</span>
            <b><?php echo e($job->next_action); ?></b>
            <div class="ft-next-action-meta"><span class="ft-next-assignee"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $job->coordinator,'name' => $job->coordinator?->name ?? 'Unassigned','size' => 34]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->coordinator),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->coordinator?->name ?? 'Unassigned'),'size' => 34]); ?>
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
<?php endif; ?> <?php echo e($job->coordinator?->name ?? 'Unassigned'); ?></span></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-job-team">
        <span class="ft-job-team-label">Team</span>
        <div class="ft-team-avatars">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $team->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $member,'name' => $member->name,'size' => 32,'class' => ''.e($loop->even ? 'ft-avatar-green' : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($member),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($member->name),'size' => 32,'class' => ''.e($loop->even ? 'ft-avatar-green' : '').'']); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->count() > 3): ?><span class="ft-avatar-more">+<?php echo e($team->count()-3); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <span class="ft-team-lead">Lead: <?php echo e($job->owner?->name ?? $job->coordinator?->name ?? 'Unassigned'); ?></span>
    </div>

    <div class="ft-job-footer-grid">
        <div class="ft-job-footer-cell">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>
            <div><span>Delivery</span>
                <span
                    class="ft-inline-date ft-job-inline-date ft-inline-edit-shell <?php echo e(($job->delivery_date && \App\Support\UserLocalTime::isDatePast($job->delivery_date)) && !$job->completed_at ? 'overdue' : ''); ?>"
                    x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-delivery-date')->toHtml() ?>, label: 'Job delivery date', value: <?php echo \Illuminate\Support\Js::from($job->delivery_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->delivery_date?->format('M j') ?? 'Set due date')->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span class="ft-inline-date-display" x-show="!editing"><b x-text="display"><?php echo e($job->delivery_date?->format('M j') ?? 'Set due date'); ?></b></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job)): ?>
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact" aria-label="Edit delivery date" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.jobDate.showPicker ? $refs.jobDate.showPicker() : $refs.jobDate.focus())">✎</button>
                        <input x-ref="jobDate" x-cloak x-show="editing" x-model="draftValue" x-on:blur="if (editing) cancelEdit()" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value, true), () => $wire.updateJobDueDate(<?php echo e($job->id); ?>, draftValue))" type="date" aria-label="Job delivery date">
                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
        </div>
        <div class="ft-job-footer-cell commercial">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h9l5 5v15H6z"/><path d="M15 2v6h5"/><path d="M13.5 11.5c-.5-.6-1.2-.9-2-.9-1.2 0-2 .6-2 1.5 0 2.3 4.5 1.1 4.5 3.5 0 .9-.9 1.6-2.2 1.6-.9 0-1.8-.4-2.4-1.1M11.8 9v10"/></svg>
            <div><span>Commercial</span><b><?php echo e(\App\Support\BoardPresenter::commercialLabel($job)); ?></b></div>
        </div>
    </div>

    <div class="ft-job-updated">Updated <?php echo e(\App\Support\BoardPresenter::lastUpdatedText($job)); ?> ago by <?php echo e(\App\Support\BoardPresenter::updatedBy($job)); ?></div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/board/job-card.blade.php ENDPATH**/ ?>