<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'task',
    'mentionUsers'=>collect(),
    'taskProgress',
    'taskStatuses'=>collect(),
    'priorities'=>collect(),
    'taskFlags'=>collect(),
    'displayTimezone'=>'UTC',
    'availableDocuments'=>collect(),
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'taskDocumentUploads'=>[],
    'showTaskDocumentPicker'=>false,
    'editMode'=>false,
]));

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

foreach (array_filter(([
    'task',
    'mentionUsers'=>collect(),
    'taskProgress',
    'taskStatuses'=>collect(),
    'priorities'=>collect(),
    'taskFlags'=>collect(),
    'displayTimezone'=>'UTC',
    'availableDocuments'=>collect(),
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'taskDocumentUploads'=>[],
    'showTaskDocumentPicker'=>false,
    'editMode'=>false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $job = $task->job;
    $done = $task->checklistItems->where('is_completed',true)->count();
    $total = $task->checklistItems->count();
    $checkTotal = max(1, $total);
    $taskDocumentName = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name;
    $accessControl = app(\App\Services\AccessControlService::class);
    $mayEditTask = $accessControl->canEditVisibleTask(auth()->user(), $task);
    $canEditTask = $editMode && $mayEditTask;
    $canAssignTask = $editMode && $accessControl->canAssignTask(auth()->user(), $task);
    $canCheck = $canEditTask;
    $canUploadDocument = $editMode && $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $editMode && $accessControl->can(auth()->user(), 'documents', 'link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $canDeleteDocument = $editMode && $accessControl->can(auth()->user(), 'documents', 'delete');
    $effectiveDescription = $task->description ?: $task->setupTemplate?->description;
    $effectiveStartDate = $task->start_date ?: \App\Support\UserLocalTime::localize($task->created_at);
    $completedOn = $task->completed_at?->copy()->timezone($displayTimezone);
    $currentTaskFlag = $task->needs_attention ? (trim((string) $task->attention_reason) ?: 'Management attention') : '';
    $taskFlagNames = $taskFlags->pluck('name')->map(fn($name)=>trim((string)$name))->filter()->values();
    $commentEvents = $task->comments->map(fn($comment)=>(object)[
        'id'=>(int)$comment->id,'kind'=>'comment','event'=>'task.comment','user'=>$comment->user,'body'=>$comment->body,'created_at'=>$comment->created_at,
    ]);
    $activityEvents = $task->activities->reject(fn($activity)=>$activity->event==='task.comment')->map(fn($activity)=>(object)[
        'id'=>(int)$activity->id,'kind'=>'activity','event'=>$activity->event,'user'=>$activity->user,'body'=>$activity->description,'created_at'=>$activity->created_at,
    ]);
    $timeline = $commentEvents->concat($activityEvents)->sortByDesc(fn($entry) => sprintf('%020d-%020d', $entry->created_at?->getTimestamp() ?? 0, $entry->id ?? 0))->values();
    if($activityTab==='comments') $timeline = $timeline->where('kind','comment')->values();
    if($activityTab==='history') $timeline = $timeline->where('kind','activity')->values();
    $activityPerPage = 30;
    $timelineTotal = $timeline->count();
    $timelinePages = max(1, (int) ceil($timelineTotal / $activityPerPage));
    $timelineCurrentPage = min(max(1, (int) $activityPage), $timelinePages);
    $timeline = $timeline->forPage($timelineCurrentPage, $activityPerPage)->values();
?>
<div <?php echo e($attributes->class('ft-task-detail-page ft-exact-task-detail')); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ft-detail-toolbar task-toolbar ft-exact-task-header">
        <div class="ft-task-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <a href="<?php echo e(route('my-work')); ?>" wire:navigate>My Work</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job): ?>
                    <span>/</span><a href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span>/</span><span><?php echo e($task->task_number); ?></span>
            </div>
            <div class="ft-task-title-line">
                <h1
                    class="ft-editable-task-title ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-title')->toHtml() ?>, label: 'task title', value: <?php echo \Illuminate\Support\Js::from($task->title)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->title)->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span x-show="!editing" x-text="display"><?php echo e($task->title); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit task title" title="Edit task name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskTitle.focus())">✎</button>
                        <input x-ref="taskTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:keydown.enter.prevent="$event.target.blur()"
                            x-on:blur="if (editing) commit(draftValue.trim(), draftValue.trim(), () => $wire.updateSelectedTaskField('title', draftValue.trim()))">
                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                </h1>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->phase?->name): ?><span class="ft-task-title-phase">· <?php echo e($task->phase->name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="ft-detail-actions"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button class="ft-new-job-btn ft-mark-complete" wire:click="markTaskComplete" <?php if($task->status==='Completed'): echo 'disabled'; endif; ?>><?php echo e($task->status==='Completed' ? 'Completed' : 'Mark complete'); ?></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><button class="ft-close-page" wire:click="closeTask" type="button" title="Back to order details" aria-label="Back to order details">×</button></div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskCompletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error ft-task-completion-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-task-detail-layout">
        <main>
            <section class="ft-task-property-grid ft-friendly-task-properties">
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-assignee')->toHtml() ?>, label: 'task assignee', value: <?php echo \Illuminate\Support\Js::from($task->assignee_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->assignee?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($task->assignee?->profileImageUrl() ?? '')->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateSelectedTaskField('assignee_id', draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
                >
                    <small>Assignee</small>
                    <div x-show="!editing" class="ft-task-property-display ft-inline-person-live">
                        <?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 26]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 26]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
                        <b class="ft-property-value" x-text="display"><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></b>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?><button type="button" :disabled="status === 'saving'" title="Edit assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?>
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor ft-task-property-assignee-editor">
                            <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task->assignee_id ?? '','selectedLabel' => $task->assignee?->name ?? 'Unassigned','triggerClass' => 'ft-task-property-inline-input','variant' => 'compact','menuWidth' => 300]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee?->name ?? 'Unassigned'),'trigger-class' => 'ft-task-property-inline-input','variant' => 'compact','menu-width' => 300]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
                        </div>
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
                </div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-status')->toHtml() ?>, label: 'task status', value: <?php echo \Illuminate\Support\Js::from($task->status)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->status)->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Status</small>
                    <div x-show="!editing" class="ft-task-property-display"><span class="status-dot blue"></span><b class="ft-property-value" x-text="display"><?php echo e($task->status); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" :disabled="status === 'saving'" title="Edit status" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.status?.showPicker ? $refs.status.showPicker() : $refs.status?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="status" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateSelectedTaskField('status', draftValue))"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($status); ?>"><?php echo e($status); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
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
                </div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-priority')->toHtml() ?>, label: 'task priority', value: <?php echo \Illuminate\Support\Js::from($task->priority)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->priority)->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Priority</small>
                    <div x-show="!editing" class="ft-task-property-display"><span class="status-dot amber"></span><b class="ft-property-value" x-text="display"><?php echo e($task->priority); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" :disabled="status === 'saving'" title="Edit priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.priority?.showPicker ? $refs.priority.showPicker() : $refs.priority?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="priority" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateSelectedTaskField('priority', draftValue))"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>"><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
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
                </div>
                <div class="ft-task-property"><small>Phase</small><div class="ft-task-property-display"><b class="ft-property-value"><?php echo e($task->phase?->name ?? '—'); ?></b></div></div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-start-date')->toHtml() ?>, label: 'task start date', value: <?php echo \Illuminate\Support\Js::from($effectiveStartDate?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($effectiveStartDate?->format('M j, Y') ?? 'Not set')->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Start date</small>
                    <div x-show="!editing" class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value" x-text="display"><?php echo e($effectiveStartDate?->format('M j, Y') ?? 'Not set'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" :disabled="status === 'saving'" title="Edit start date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.start?.showPicker ? $refs.start.showPicker() : $refs.start?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><input x-ref="start" x-model="draftValue" class="ft-task-property-inline-input" type="date" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateSelectedTaskField('start_date', draftValue))"></div>
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
                </div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('M j, Y') ?? 'Not set')->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Due date</small>
                    <div x-show="!editing" class="ft-task-property-display <?php echo e(($task->due_date && \App\Support\UserLocalTime::isDatePast($task->due_date)) && !$task->completed_at ? 'danger-text' : ''); ?>"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value" x-text="display"><?php echo e($task->due_date?->format('M j, Y') ?? 'Not set'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" :disabled="status === 'saving'" title="Edit due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.due?.showPicker ? $refs.due.showPicker() : $refs.due?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><input x-ref="due" x-model="draftValue" class="ft-task-property-inline-input" type="date" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateSelectedTaskField('due_date', draftValue))"></div>
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
                </div>
                <div class="ft-task-property ft-task-completed-property"
                    x-data="{ completedDate: <?php echo \Illuminate\Support\Js::from($completedOn?->format('M j, Y') ?? '—')->toHtml() ?>, completedTime: <?php echo \Illuminate\Support\Js::from($completedOn?->format('g:i A') ?? '')->toHtml() ?> }"
                    x-on:task-completion-updated.window="completedDate = $event.detail.completedDate || '—'; completedTime = $event.detail.completedTime || ''">
                    <small>Completed On</small>
                    <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value ft-completed-date-time"><span x-text="completedDate"><?php echo e($completedOn?->format('M j, Y') ?? '—'); ?></span><span class="ft-completed-time" x-show="completedTime" x-text="completedTime"><?php echo e($completedOn?->format('g:i A') ?? ''); ?></span></b></div>
                </div>
            </section>

            <section
                class="ft-detail-card ft-description-card ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-description')->toHtml() ?>, label: 'task description', value: <?php echo \Illuminate\Support\Js::from($effectiveDescription ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($effectiveDescription ?: 'No description has been provided for this task.')->toHtml() ?> })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button x-show="!editing" :disabled="status === 'saving'" class="ft-card-edit" type="button" title="Edit description" x-on:click="beginRichTextEdit($refs.description)">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h2>Description</h2>
                <div x-show="!editing" class="ft-rich-text-content">
                    <div x-show="!hasRichTextOverride"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($effectiveDescription): ?><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $effectiveDescription]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($effectiveDescription)]); ?>
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
<?php endif; ?><?php else: ?> No description has been provided for this task. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div x-cloak x-show="hasRichTextOverride" x-html="richTextOverrideHtml"></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                    <div x-cloak x-show="editing" class="ft-inline-description-editor"><textarea x-ref="description" class="ft-mention-input" data-rich-text rows="4" autocomplete="off" data-mention-users='<?php echo json_encode($mentionUsers->values(), 15, 512) ?>'><?php echo e($effectiveDescription ?? ''); ?></textarea><div class="ft-inline-description-actions"><button type="button" class="ft-outline-btn" x-on:click="cancelRichTextEdit($refs.description)">Cancel</button><button type="button" class="ft-new-job-btn" data-rich-text-submit :disabled="status === 'saving'" x-on:click="saveRichText($refs.description, 'No description has been provided for this task.', (clean) => $wire.updateSelectedTaskField('description', clean))">Save</button></div></div>
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
            </section>

            <section class="ft-detail-card ft-checklist-card" x-data="{adding:false}">
                <div class="ft-card-row-head"><div class="ft-check-title"><h2>Checklist</h2><span><?php echo e($done); ?> of <?php echo e($total); ?> complete</span><div class="ft-small-progress"><span style="width:<?php echo e($done / $checkTotal * 100); ?>%"></span></div></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button class="ft-link-blue" type="button" x-on:click="adding=!adding">＋ Add item</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><div class="ft-checklist-add-row" x-show="adding"><input wire:model="newChecklistItem" wire:keydown.enter="addTaskChecklistItem" placeholder="Checklist item"><button type="button" class="ft-new-job-btn" wire:click="addTaskChecklistItem" x-on:click="adding=false">Add</button><button type="button" class="ft-outline-btn" x-on:click="adding=false">Cancel</button></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newChecklistItem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error ft-checklist-validation"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $task->checklistItems->sortBy('sort_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-checklist-row">
                        <input type="checkbox" <?php if($item->is_completed): echo 'checked'; endif; ?> <?php if(!$canCheck): echo 'disabled'; endif; ?> wire:change="toggleTaskChecklistItem(<?php echo e($item->id); ?>, $event.target.checked)">
                        <span class="<?php echo e($item->is_completed ? 'completed' : ''); ?>"><?php echo e($item->label); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" class="ft-checklist-delete" title="Delete checklist item" wire:click="deleteTaskChecklistItem(<?php echo e($item->id); ?>)" wire:confirm="Delete this checklist item?">×</button><?php else: ?><span></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="empty-state">No checklist items configured.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($canCheck)): ?><p class="ft-checklist-permission-note">Only the assigned person can check or uncheck checklist items.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-detail-card ft-attachment-card">
                <h2>Attachments <span><?php echo e($task->documents->count()); ?></span></h2>
                <div class="ft-upload-zone compact ft-task-upload-zone">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                        <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone for="taskDocumentUpload-<?php echo e($task->id); ?>">
                            <input id="taskDocumentUpload-<?php echo e($task->id); ?>" type="file" wire:model="taskDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                            <span class="ft-paperclip">⌕</span>
                            <div>Drop files here or <strong>browse</strong><small data-drop-status><?php echo e($taskDocumentName ? 'Required document: '.$taskDocumentName.' · ' : ''); ?>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                        </label>
                    <?php else: ?>
                        <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to task attachments.</small></div></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument): ?><button class="ft-outline-btn ft-task-choose-document" type="button" wire:click="toggleTaskDocumentPicker">Choose from Documents</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($taskDocumentUploads ?? [])): ?>
                    <div class="ft-upload-ready-row"><span><?php echo e(count($taskDocumentUploads ?? [])); ?> file<?php echo e(count($taskDocumentUploads ?? [])===1?'':'s'); ?> ready</span><button class="ft-new-job-btn" type="button" wire:click="uploadSelectedTaskDocuments">Upload &amp; link</button></div>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument && $showTaskDocumentPicker): ?>
                    <div class="ft-existing-document-picker ft-task-document-picker">
                        <select wire:model="taskExistingDocumentId"><option value="">Select a stored document</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stored): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($stored->id); ?>"><?php echo e($stored->name); ?> · <?php echo e($stored->job?->displayOrderNumber() ?? 'Archive'); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                        <button class="ft-new-job-btn" type="button" wire:click="attachExistingToSelectedTask">Link document</button>
                        <button class="ft-outline-btn" type="button" wire:click="toggleTaskDocumentPicker">Cancel</button>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskExistingDocumentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $task->documents->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ft-attachment-row"><span class="ft-file-type"><?php echo e(strtoupper(pathinfo($doc->name,PATHINFO_EXTENSION) ?: 'FILE')); ?></span><b><?php echo e($doc->name); ?></b><small><?php echo e(\App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A')); ?></small><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?><button type="button" class="ft-doc-delete-button" wire:click="deleteSelectedTaskDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="ft-upload-note">Every file uploaded here is linked to this task and appears in Order Documents. A required document is counted only when this Task Pack task defines that document type.</p>
            </section>

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
<?php endif; ?><textarea class="ft-mention-input" data-rich-text data-rich-text-compact wire:model="taskComment" rows="2" autocomplete="off" data-mention-users='<?php echo json_encode($mentionUsers->values(), 15, 512) ?>' placeholder="Write a comment. Type @ to mention someone or paste a screenshot..."></textarea><button class="ft-new-job-btn" data-rich-text-submit type="button" wire:click="addTaskComment">Comment</button></div>
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
                                <div class="ft-activity-entry-head"><div><b><?php echo e($actorName); ?></b><span class="ft-activity-kind <?php echo e($entry->kind==='comment' ? 'comment' : 'history'); ?>"><?php echo e($entry->kind==='comment' ? 'Comment' : 'Change'); ?></span></div><time title="<?php echo e($entryLocalTime?->format('M j, Y g:i A')); ?> <?php echo e($displayTimezone); ?>"><?php echo e($entry->created_at?->diffForHumans()); ?></time></div>
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
        </main>
        <aside>
            <section class="ft-detail-card ft-management-card">
                <h2>Management attention</h2>
                <div class="ft-attention-row"><span>Required evidence</span><b><span class="<?php echo e($taskDocumentName ? 'ft-red-doc-icon' : ''); ?>">▯</span> <?php echo e($taskDocumentName ?: 'No required evidence'); ?></b></div>
                <div class="ft-attention-row ft-task-flag-row">
                    <span>Attention</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <select class="ft-task-flag-select <?php echo e($task->needs_attention ? 'is-flagged' : ''); ?>" wire:change="setTaskFlag($event.target.value)">
                            <option value="" <?php if($currentTaskFlag===''): echo 'selected'; endif; ?>>Not flagged</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentTaskFlag!=='' && !$taskFlagNames->contains($currentTaskFlag)): ?>
                                <option value="<?php echo e($currentTaskFlag); ?>" selected><?php echo e($currentTaskFlag); ?></option>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskFlags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($flag->name); ?>" <?php if($currentTaskFlag===$flag->name): echo 'selected'; endif; ?>><?php echo e($flag->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    <?php else: ?>
                        <b class="<?php echo e($task->needs_attention ? 'danger-text' : ''); ?>"><span class="ft-red-flag">⚑</span> <?php echo e($currentTaskFlag ?: 'Not flagged'); ?></b>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask && $taskFlags->isEmpty() && $currentTaskFlag===''): ?>
                    <small class="ft-task-flag-help">Add Task Flags in Master Data to make them available here.</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-detail-card ft-job-context-card"><h2>Order context</h2><b><?php echo e($job?->title); ?></b><div><span>Client</span><b><?php echo e($job?->client?->name); ?></b></div><div><span>Order health</span><b class="<?php echo e($job?->needs_attention ? 'danger-text' : ''); ?>"><span class="<?php echo e($job?->needs_attention ? 'ft-red-dot' : ''); ?>"></span><?php echo e($job?->needs_attention ? 'Needs Attention' : $job?->health); ?></b></div><div><span>Delivery</span><b><?php echo e($job?->delivery_date?->format('M j, Y') ?? '—'); ?></b></div><div class="ft-context-progress"><span>Order progress</span><b><?php echo e($job?->progress); ?>%</b><div class="ft-line-progress"><span style="width:<?php echo e($job?->progress ?? 0); ?>%"></span></div></div><button class="ft-link-blue ft-open-job" wire:click="closeTask">Open order details ↗</button></section>

        </aside>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/task-detail.blade.php ENDPATH**/ ?>