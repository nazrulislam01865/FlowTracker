<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['task']));

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

foreach (array_filter((['task']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $overdueDays = \App\Support\BoardPresenter::overdueDays($task);
    $waiting = \App\Support\BoardPresenter::waitingLabel($task);
    $checkTotal = (int) ($task->checklist_items_count ?? ($task->relationLoaded('checklistItems') ? $task->checklistItems->count() : 0));
    $checkDone = (int) ($task->completed_checklist_items_count ?? ($task->relationLoaded('checklistItems') ? $task->checklistItems->where('is_completed', true)->count() : 0));
    $documentCount = (int) ($task->documents_count ?? ($task->relationLoaded('documents') ? $task->documents->count() : 0));
    $commentCount = (int) ($task->comments_count ?? ($task->relationLoaded('comments') ? $task->comments->count() : 0));
    $taskFlagLabel = app(\App\Services\OrderTaskFlagService::class)->labelForTask($task);
    $masterData = app(\App\Services\MasterDataService::class);
    $taskFlagColor = $taskFlagLabel ? $masterData->colorFor('order_task_flag', $taskFlagLabel) : null;
    $taskPriorityColor = $masterData->displayColorFor('priority', (string) $task->priority);
?>
<article <?php echo e($attributes->class(['ft-task-board-card'])); ?>>
    <div class="ft-task-board-top">
        <div class="ft-task-card-badges"><span class="ft-priority-pill <?php echo e($taskPriorityColor ? 'ft-master-color' : strtolower($task->priority)); ?>" style="<?php echo e(\App\Support\MasterColor::style($taskPriorityColor)); ?>"><?php echo e($task->priority); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskFlagLabel): ?><span class="ft-attention-pill <?php echo e($taskFlagColor ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($taskFlagColor)); ?>">⚑ <?php echo e($taskFlagLabel); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <div class="ft-task-board-top-right">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overdueDays > 0): ?><span class="ft-overdue-pill">Overdue · <?php echo e($overdueDays); ?>d</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a class="ft-card-kebab" href="<?php echo e(route('jobs.index', ['open'=>$task->flow_job_id, 'task'=>$task->id])); ?>" wire:navigate aria-label="Open task">
                <span></span><span></span><span></span>
            </a>
        </div>
    </div>

    <a class="ft-task-title" href="<?php echo e(route('jobs.index', ['open'=>$task->flow_job_id, 'task'=>$task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a>
    <div class="ft-task-job-ref">
        <a href="<?php echo e(route('jobs.index', ['open'=>$task->flow_job_id])); ?>" wire:navigate><?php echo e($task->job?->displayOrderNumber()); ?></a>
        <span>·</span><span><?php echo e($task->job?->client?->name ?? 'No client'); ?></span>
    </div>
    <div class="ft-task-phase-name"><?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $task->phase,'short' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->phase),'short' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $attributes = $__attributesOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__attributesOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $component = $__componentOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__componentOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?></div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($waiting): ?>
        <div class="ft-waiting-panel">
            <span class="ft-waiting-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m10.5 13.5 3-3"/><path d="m7.5 15.5-1 1a4 4 0 0 1-5.7-5.7l3-3a4 4 0 0 1 5.7 0"/><path d="m16.5 8.5 1-1a4 4 0 0 1 5.7 5.7l-3 3a4 4 0 0 1-5.7 0"/></svg></span>
            <div><b>Waiting on: <span><?php echo e($waiting); ?></span></b><small>Since <?php echo e(\App\Support\UserLocalTime::format($task->updated_at, 'M j')); ?></small></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-task-assignee-row">
        <div class="ft-task-assignee"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $task->assignee,'name' => $task->assignee?->name ?? 'Unassigned','size' => 38]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee?->name ?? 'Unassigned'),'size' => 38]); ?>
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
<?php endif; ?><b><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></b></div>
        <span
            class="ft-inline-date ft-inline-edit-shell <?php echo e(($task->due_date && \App\Support\UserLocalTime::isDatePast($task->due_date)) && !$task->completed_at ? 'overdue' : ''); ?>"
            x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('M j') ?? 'Set due date')->toHtml() ?> })"
            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
        >
            <button type="button" class="ft-inline-date-display" x-show="!editing" :disabled="status === 'saving'" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.dateInput.showPicker ? $refs.dateInput.showPicker() : $refs.dateInput.focus())" title="Set due date">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>
                <span x-text="display"><?php echo e($task->due_date?->format('M j') ?? 'Set due date'); ?></span>
            </button>
            <input x-ref="dateInput" x-cloak x-show="editing" x-model="draftValue" x-on:blur="if (editing) cancelEdit()" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value, true), () => $wire.updateTaskDueDate(<?php echo e($task->id); ?>, draftValue))" type="date" aria-label="Task due date">
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
        </span>
    </div>

    <div class="ft-task-meta-footer">
        <span><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m7 12 3 3 7-7"/></svg><?php echo e($checkDone); ?>/<?php echo e($checkTotal); ?></span>
        <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m20.5 11.5-8.2 8.2a6 6 0 1 1-8.5-8.5l9-9a4 4 0 0 1 5.7 5.7l-9.1 9.1a2 2 0 0 1-2.8-2.8l8.4-8.4"/></svg><?php echo e($documentCount); ?></span>
        <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3v-7a6 6 0 0 1-1-3.3V8a5 5 0 0 1 5-5h10a4 4 0 0 1 4 4z"/></svg><?php echo e($commentCount); ?></span>
        <span class="ft-task-updated">Updated <?php echo e(\App\Support\BoardPresenter::lastUpdatedText($task)); ?> ago</span>
    </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/board/task-card.blade.php ENDPATH**/ ?>