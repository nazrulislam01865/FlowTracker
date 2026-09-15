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
    'taskDetailSectionsReady'=>[],
    'orderHoldContext'=>[],
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
    'taskDetailSectionsReady'=>[],
    'orderHoldContext'=>[],
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
    $taskOrderIsOnHold = (bool) ($orderHoldContext['isOnHold'] ?? false);
    $taskOrderHold = is_array($orderHoldContext['hold'] ?? null) ? $orderHoldContext['hold'] : null;
    $checklistReady = (bool) ($taskDetailSectionsReady['checklist'] ?? false);
    $attachmentsReady = (bool) ($taskDetailSectionsReady['attachments'] ?? false);
    $activityReady = (bool) ($taskDetailSectionsReady['activity'] ?? false);
    $done = $checklistReady && $task->relationLoaded('checklistItems') ? $task->checklistItems->where('is_completed',true)->count() : 0;
    $total = $checklistReady && $task->relationLoaded('checklistItems') ? $task->checklistItems->count() : 0;
    $checkTotal = max(1, $total);
    $taskDocumentName = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name;
    $taskDocumentInstructions = trim((string) ($task->setupTemplate?->document_instructions ?? ''));
    $accessControl = app(\App\Services\AccessControlService::class);
    $mayModerateTaskActivity = $accessControl->isAdministrator(auth()->user());
    $mayEditTask = $accessControl->canEditVisibleTask(auth()->user(), $task);
    // A held Order makes Task Details strictly view-only for every role,
    // including administrators. Release hold is intentionally available only
    // from the parent Order so task work cannot bypass the Order-level lock.
    $canModerateTaskActivity = $mayModerateTaskActivity && ! $taskOrderIsOnHold;
    $canEditTask = $mayEditTask && ! $taskOrderIsOnHold;
    $canAssignTask = $accessControl->canAssignTask(auth()->user(), $task) && ! $taskOrderIsOnHold;
    $canCheck = $canEditTask;
    // Task Details permissions must be driven by the actual task authorization,
    // not by how the page was opened. A normal View action can still be an
    // editable task for its assignee/creator/admin, so attachment upload must
    // remain available in that case.
    $canUploadDocument = $canEditTask && $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $canEditTask && $accessControl->can(auth()->user(), 'documents', 'link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $canDeleteDocument = $canEditTask && $accessControl->can(auth()->user(), 'documents', 'delete');
    $effectiveDescription = $task->description ?: $task->setupTemplate?->description;
    $effectiveStartDate = $task->start_date ?: \App\Support\UserLocalTime::localize($task->created_at);
    $completedOn = $task->completed_at?->copy()->timezone($displayTimezone);
    $masterData = app(\App\Services\MasterDataService::class);
    $currentStatusColor = $masterData->colorFor('order_task_status', (string) $task->status);
    $currentPriorityColor = $masterData->displayColorFor('priority', (string) $task->priority);
    $currentTaskFlag = app(\App\Services\OrderTaskFlagService::class)->labelForTask($task) ?: '';
    $currentTaskFlagColor = $currentTaskFlag !== '' ? $masterData->colorFor('order_task_flag', $currentTaskFlag) : null;
    $currentOrderFlag = $job ? (app(\App\Services\OrderTaskFlagService::class)->labelForOrder($job) ?: '') : '';
    $currentOrderFlagColor = $currentOrderFlag !== '' ? $masterData->colorFor('order_flag', $currentOrderFlag) : null;
    $isProductionMonitorTask = app(\App\Services\OrderWorkflowActionService::class)->automationKey($task) === 'PROD_ISSUE';
    $productionMonitorActivity = $job && $job->relationLoaded('latestProductionMonitorActivity')
        ? $job->latestProductionMonitorActivity
        : null;
    $productionMonitorMeta = is_array($productionMonitorActivity?->meta) ? $productionMonitorActivity->meta : [];
    $productionMonitorActivityTaskId = (int) ($productionMonitorMeta['task_id'] ?? 0);
    $productionMonitorActivityMatchesTask = $productionMonitorActivity
        && ($productionMonitorActivityTaskId === 0 || $productionMonitorActivityTaskId === (int) $task->id);
    $productionMonitorDate = $productionMonitorActivityMatchesTask
        ? trim((string) ($productionMonitorMeta['supplier_delivery_date'] ?? ''))
        : '';
    if ($productionMonitorDate === '' && $job?->supplier_delivery_date) {
        $productionMonitorDate = $job->supplier_delivery_date->format('Y-m-d');
    }
    $productionMonitorNote = $productionMonitorActivityMatchesTask
        ? trim(app(\App\Services\RichTextService::class)->plainText((string) ($productionMonitorMeta['production_issue_note'] ?? '')))
        : '';
    $timeline = collect();
    $activityPerPage = 30;
    $timelineTotal = 0;
    $timelinePages = 1;
    $timelineCurrentPage = 1;
    if ($activityReady && $task->relationLoaded('comments') && $task->relationLoaded('activities')) {
        $commentEvents = $task->comments->map(fn($comment)=>(object)[
            'id'=>(int)$comment->id,'kind'=>'comment','event'=>'task.comment','user'=>$comment->user,'body'=>$comment->body,'created_at'=>$comment->created_at,
        ]);
        $activityEvents = $task->activities->reject(fn($activity)=>$activity->event==='task.comment')->map(fn($activity)=>(object)[
            'id'=>(int)$activity->id,'kind'=>'activity','event'=>$activity->event,'user'=>$activity->user,'body'=>$activity->description,'created_at'=>$activity->created_at,
        ]);
        $timeline = $commentEvents->concat($activityEvents)->sortByDesc(fn($entry) => sprintf('%020d-%020d', $entry->created_at?->getTimestamp() ?? 0, $entry->id ?? 0))->values();
        if($activityTab==='comments') $timeline = $timeline->where('kind','comment')->values();
        if($activityTab==='history') $timeline = $timeline->where('kind','activity')->values();
        $timelineTotal = $timeline->count();
        $timelinePages = max(1, (int) ceil($timelineTotal / $activityPerPage));
        $timelineCurrentPage = min(max(1, (int) $activityPage), $timelinePages);
        $timeline = $timeline->forPage($timelineCurrentPage, $activityPerPage)->values();
    }
?>
<div
    <?php echo e($attributes->class('ft-task-detail-page ft-exact-task-detail')); ?>

    x-data="window.FlowTrack.ui.orderHoldGuard({ held: <?php echo \Illuminate\Support\Js::from($taskOrderIsOnHold)->toHtml() ?> })"
    x-on:flowtrack:order-held-blocked.window="showHoldBlocked($event.detail?.action ?? '')"
    x-on:click.capture="guardInteraction($event)"
    x-on:focusin.capture="guardInteraction($event)"
    x-on:change.capture="guardInteraction($event)"
    x-on:submit.capture="guardInteraction($event)"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ft-detail-toolbar task-toolbar ft-exact-task-header">
        <div class="ft-task-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <a href="<?php echo e(route('my-work')); ?>" wire:navigate>My Tasks</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job): ?>
                    <span>/</span><a href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span>/</span><span><?php echo e($task->task_number); ?></span>
            </div>
            <div class="ft-task-title-line">
                <h1
                    class="ft-editable-task-title ft-inline-edit-shell"
                    x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-title')->toHtml() ?>, label: 'task title', value: <?php echo \Illuminate\Support\Js::from($task->title)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->title)->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span x-show="!editing" x-text="display"><?php echo e($task->title); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil ft-detail-edit-button" aria-label="Edit task title" title="Edit task name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskTitle.focus())"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->phase?->name): ?><span class="ft-task-title-phase">· <?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $task->phase]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->phase)]); ?>
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
<?php endif; ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="ft-detail-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskOrderIsOnHold && $mayEditTask && $task->status !== 'Completed'): ?>
                <button type="button" class="ft-new-job-btn ft-mark-complete ft-task-held-action" x-on:click.prevent.stop="showHoldBlocked('Mark complete')" title="Order is on hold. Release the hold before completing this task.">
                    <span class="ft-order-hold-pause-icon" aria-hidden="true"><i></i><i></i></span>
                    <span>Mark complete</span>
                </button>
            <?php elseif($canEditTask): ?>
                <button class="ft-new-job-btn ft-mark-complete" wire:click="markTaskComplete" <?php if($task->status==='Completed'): echo 'disabled'; endif; ?>><?php echo e($task->status==='Completed' ? 'Completed' : 'Mark complete'); ?></button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button class="ft-close-page" wire:click="closeTask" type="button" title="Back to order details" aria-label="Back to order details">×</button>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskCompletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error ft-task-completion-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskOrderIsOnHold && $taskOrderHold): ?>
        <div class="ft-task-order-hold-banner" data-order-hold-view-control>
            <div class="ft-task-order-hold-banner__icon" aria-hidden="true"><span class="ft-order-hold-pause-icon"><i></i><i></i></span></div>
            <div class="ft-task-order-hold-banner__copy">
                <strong>Order on hold — task activities are locked</strong>
                <span><?php echo e($taskOrderHold['holdFromLabel'] ?? 'Order'); ?> hold · held by <?php echo e($taskOrderHold['heldBy'] ?? 'Unknown user'); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job): ?>
                <button type="button" wire:click="openJob(<?php echo e($job->id); ?>)" data-order-hold-allowed>View order</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-task-detail-layout">
        <main>
            <?php echo $__env->make('components.jobs.task-detail.properties', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('components.jobs.task-detail.description', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isProductionMonitorTask && \App\Support\OrderDetailPresenter::isCompletedTask($task)): ?>
                <?php if (isset($component)) { $__componentOriginalc98b8141449a00136a08422b638062b6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc98b8141449a00136a08422b638062b6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.production-monitor-summary','data' => ['task' => $task,'details' => [
                        'supplierDeliveryDate' => $productionMonitorDate,
                        'productionIssueNote' => $productionMonitorNote,
                    ],'canEdit' => $canEditTask && strcasecmp((string) ($job?->status ?? ''), 'Cancelled') !== 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.production-monitor-summary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task),'details' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        'supplierDeliveryDate' => $productionMonitorDate,
                        'productionIssueNote' => $productionMonitorNote,
                    ]),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditTask && strcasecmp((string) ($job?->status ?? ''), 'Cancelled') !== 0)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc98b8141449a00136a08422b638062b6)): ?>
<?php $attributes = $__attributesOriginalc98b8141449a00136a08422b638062b6; ?>
<?php unset($__attributesOriginalc98b8141449a00136a08422b638062b6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc98b8141449a00136a08422b638062b6)): ?>
<?php $component = $__componentOriginalc98b8141449a00136a08422b638062b6; ?>
<?php unset($__componentOriginalc98b8141449a00136a08422b638062b6); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($checklistReady): ?>
                <?php echo $__env->make('components.jobs.task-detail.checklist', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'checklist','method' => 'loadDetailSection','keyPrefix' => 'task-detail','contextType' => 'task','contextId' => $task->id,'rows' => 3,'message' => 'Loading checklist when needed…','rootMargin' => '320px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'checklist','method' => 'loadDetailSection','key-prefix' => 'task-detail','context-type' => 'task','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->id),'rows' => 3,'message' => 'Loading checklist when needed…','root-margin' => '320px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attachmentsReady): ?>
                <?php echo $__env->make('components.jobs.task-detail.attachments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'attachments','method' => 'loadDetailSection','keyPrefix' => 'task-detail','contextType' => 'task','contextId' => $task->id,'rows' => 3,'message' => 'Loading task attachments when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'attachments','method' => 'loadDetailSection','key-prefix' => 'task-detail','context-type' => 'task','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->id),'rows' => 3,'message' => 'Loading task attachments when needed…','root-margin' => '300px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activityReady): ?>
                <?php echo $__env->make('components.jobs.task-detail.activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'activity','method' => 'loadDetailSection','keyPrefix' => 'task-detail','contextType' => 'task','contextId' => $task->id,'rows' => 4,'message' => 'Loading task activity when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'activity','method' => 'loadDetailSection','key-prefix' => 'task-detail','context-type' => 'task','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->id),'rows' => 4,'message' => 'Loading task activity when needed…','root-margin' => '300px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </main>
        <aside>
            <?php echo $__env->make('components.jobs.task-detail.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </aside>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskOrderIsOnHold && $taskOrderHold): ?>
        <?php if (isset($component)) { $__componentOriginal97584fd518cdbfb6a910276b6a92b27a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97584fd518cdbfb6a910276b6a92b27a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.hold-blocked-modal','data' => ['hold' => $taskOrderHold,'canReleaseHold' => false,'orderId' => $job?->id,'directRelease' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.hold-blocked-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['hold' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskOrderHold),'can-release-hold' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'order-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job?->id),'direct-release' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97584fd518cdbfb6a910276b6a92b27a)): ?>
<?php $attributes = $__attributesOriginal97584fd518cdbfb6a910276b6a92b27a; ?>
<?php unset($__attributesOriginal97584fd518cdbfb6a910276b6a92b27a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97584fd518cdbfb6a910276b6a92b27a)): ?>
<?php $component = $__componentOriginal97584fd518cdbfb6a910276b6a92b27a; ?>
<?php unset($__componentOriginal97584fd518cdbfb6a910276b6a92b27a); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/task-detail.blade.php ENDPATH**/ ?>