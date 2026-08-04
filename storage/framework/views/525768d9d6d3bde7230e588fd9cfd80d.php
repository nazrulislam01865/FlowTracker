<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'task',
    'users',
    'taskProgress',
    'taskStatuses'=>collect(),
    'priorities'=>collect(),
    'availableDocuments'=>collect(),
    'activityTab'=>'all',
    'activityPage'=>1,
    'taskDocumentUploads'=>[],
    'showTaskDocumentPicker'=>false,
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
    'users',
    'taskProgress',
    'taskStatuses'=>collect(),
    'priorities'=>collect(),
    'availableDocuments'=>collect(),
    'activityTab'=>'all',
    'activityPage'=>1,
    'taskDocumentUploads'=>[],
    'showTaskDocumentPicker'=>false,
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
    $previousTask = $job?->tasks?->where('workflow_phase_id',$task->workflow_phase_id)->where('id','<',$task->id)->sortByDesc('id')->first();
    $taskDocumentName = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name;
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditTask = $accessControl->canEditTask(auth()->user(), $task);
    $canAssignTask = $accessControl->canAssignTask(auth()->user(), $task);
    $canCheck = $canEditTask;
    $canUploadDocument = $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $accessControl->can(auth()->user(), 'documents', 'link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $effectiveDescription = $task->description ?: $task->setupTemplate?->description;
    $effectiveStartDate = $task->start_date ?: $task->created_at;
    $commentEvents = $task->comments->map(fn($comment)=>(object)[
        'kind'=>'comment','event'=>'task.comment','user'=>$comment->user,'body'=>$comment->body,'created_at'=>$comment->created_at,
    ]);
    $activityEvents = $task->activities->reject(fn($activity)=>$activity->event==='task.comment')->map(fn($activity)=>(object)[
        'kind'=>'activity','event'=>$activity->event,'user'=>$activity->user,'body'=>$activity->description,'created_at'=>$activity->created_at,
    ]);
    $timeline = $commentEvents->concat($activityEvents)->sortByDesc('created_at')->values();
    if($activityTab==='comments') $timeline = $timeline->where('kind','comment')->values();
    if($activityTab==='history') $timeline = $timeline->where('kind','activity')->values();
    $activityPerPage = 30;
    $timelineTotal = $timeline->count();
    $timelinePages = max(1, (int) ceil($timelineTotal / $activityPerPage));
    $timelineCurrentPage = min(max(1, (int) $activityPage), $timelinePages);
    $timeline = $timeline->forPage($timelineCurrentPage, $activityPerPage)->values();
?>
<div class="ft-task-detail-page ft-exact-task-detail">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ft-detail-toolbar task-toolbar ft-exact-task-header">
        <div class="ft-task-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <a href="<?php echo e(route('my-work')); ?>" wire:navigate>My Work</a><span>/</span>
                <a class="ft-copyable-id-link" href="<?php echo e(route('jobs.index', ['open'=>$task->flow_job_id, 'task'=>$task->id])); ?>" wire:navigate><?php echo e($task->task_number); ?></a>
                <button type="button" class="ft-copy-id-btn" title="Copy Task ID" aria-label="Copy <?php echo e($task->task_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($task->task_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job): ?>
                <div class="ft-detail-number ft-detail-linked-number">
                    <a href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->job_number); ?></a>
                    <button type="button" class="ft-copy-id-btn" title="Copy Job ID" aria-label="Copy <?php echo e($job->job_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($job->job_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <h1 class="ft-editable-task-title" x-data="{editing:false}">
                <span x-show="!editing"><?php echo e($task->title); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                    <button x-show="!editing" type="button" class="ft-pencil" aria-label="Edit task title" title="Edit task name" x-on:click.stop="editing=true; $nextTick(() => $refs.taskTitle.focus())">✎</button>
                    <input x-ref="taskTitle" x-show="editing" type="text" value="<?php echo e($task->title); ?>" maxlength="255"
                        x-on:keydown.escape="editing=false"
                        x-on:keydown.enter="$event.target.blur()"
                        x-on:blur="editing=false"
                        wire:change="updateSelectedTaskField('title', $event.target.value)">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h1>
        </div>
        <div class="ft-detail-actions"><button class="ft-new-job-btn ft-mark-complete" wire:click="markTaskComplete" wire:loading.attr="disabled" wire:target="markTaskComplete" <?php if($task->status==='Completed' || !$canEditTask): echo 'disabled'; endif; ?>><span wire:loading.remove wire:target="markTaskComplete"><?php echo e($task->status==='Completed' ? 'Completed' : 'Mark complete'); ?></span><span wire:loading wire:target="markTaskComplete">Saving…</span></button><button class="ft-outline-btn ft-square-action" type="button">•••</button><button class="ft-close-page" wire:click="closeTask" type="button" title="Back to job details" aria-label="Back to job details">×</button></div>
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
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Assignee</small>
                    <div class="ft-task-property-display"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task->assignee?->name ?? 'Unassigned','size' => 26]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee?->name ?? 'Unassigned'),'size' => 26]); ?>
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
<?php endif; ?><b class="ft-property-value"><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?><button type="button" title="Edit assignee" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.assignee?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?><div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><select x-ref="assignee" class="ft-task-property-input" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('assignee_id',$event.target.value)"><option value="">Unassigned</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($user->id); ?>" <?php if((int)$task->assignee_id===(int)$user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Status</small>
                    <div class="ft-task-property-display"><span class="status-dot blue"></span><b class="ft-property-value"><?php echo e($task->status); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" title="Edit status" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.status?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><select x-ref="status" class="ft-task-property-input" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('status',$event.target.value)"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($status); ?>" <?php if($task->status===$status): echo 'selected'; endif; ?>><?php echo e($status); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Priority</small>
                    <div class="ft-task-property-display"><span class="status-dot amber"></span><b class="ft-property-value"><?php echo e($task->priority); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" title="Edit priority" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.priority?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><select x-ref="priority" class="ft-task-property-input" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('priority',$event.target.value)"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>" <?php if($task->priority===$priority->name): echo 'selected'; endif; ?>><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-task-property"><small>Phase</small><div class="ft-task-property-display"><b class="ft-property-value"><?php echo e($task->phase?->name ?? '—'); ?></b></div></div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Start date</small>
                    <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value"><?php echo e($effectiveStartDate?->format('M j, Y') ?? 'Not set'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" title="Edit start date" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.start?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><input x-ref="start" class="ft-task-property-input" type="date" value="<?php echo e($effectiveStartDate?->format('Y-m-d')); ?>" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('start_date',$event.target.value)"></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Due date</small>
                    <div class="ft-task-property-display <?php echo e($task->due_date?->isPast() && !$task->completed_at ? 'danger-text' : ''); ?>"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value"><?php echo e($task->due_date?->format('M j, Y') ?? 'Not set'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" title="Edit due date" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.due?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><input x-ref="due" class="ft-task-property-input" type="date" value="<?php echo e($task->due_date?->format('Y-m-d')); ?>" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('due_date',$event.target.value)"></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>

            <section class="ft-detail-card ft-description-card" x-data="{editing:false}">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button x-show="!editing" class="ft-card-edit" type="button" title="Edit description" x-on:click="editing=true;$nextTick(()=>$refs.description.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h2>Description</h2>
                <p x-show="!editing"><?php echo e($effectiveDescription ?: 'No description has been provided for this task.'); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><div x-show="editing" class="ft-inline-description-editor"><textarea x-ref="description" rows="4"><?php echo e($task->description ?: $task->setupTemplate?->description); ?></textarea><div><button type="button" class="ft-outline-btn" x-on:click="editing=false">Cancel</button><button type="button" class="ft-new-job-btn" x-on:click="$wire.updateSelectedTaskField('description',$refs.description.value); editing=false">Save</button></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

            <section class="ft-detail-card ft-attachment-card" x-data="{ uploading:false, progress:0 }" x-on:livewire-upload-start="uploading=true; progress=0" x-on:livewire-upload-progress="progress=$event.detail.progress" x-on:livewire-upload-error="uploading=false; progress=0" x-on:livewire-upload-finish="progress=100; setTimeout(() => { uploading=false; progress=0 }, 700)">
                <h2>Attachments <span><?php echo e($task->documents->count()); ?></span></h2>
                <div class="ft-upload-zone compact ft-task-upload-zone">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                        <label class="ft-task-upload-drop ft-livewire-upload-zone" for="taskDocumentUpload-<?php echo e($task->id); ?>">
                            <input id="taskDocumentUpload-<?php echo e($task->id); ?>" type="file" wire:model="taskDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                            <span class="ft-paperclip">⌕</span>
                            <div>Drop files here or <strong>browse</strong><small><?php echo e($taskDocumentName ? 'Required document: '.$taskDocumentName.' · ' : ''); ?>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                        </label>
                    <?php else: ?>
                        <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to task attachments.</small></div></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkDocument): ?><button class="ft-outline-btn ft-task-choose-document" type="button" wire:click="toggleTaskDocumentPicker">Choose from Documents</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-upload-progress-wrap" x-cloak x-show="uploading" x-transition.opacity>
                    <div class="ft-upload-progress-copy"><span>Uploading attachment…</span><b x-text="progress + '%'">0%</b></div>
                    <div class="ft-upload-progress-track"><span x-bind:style="`width:${progress}%`"></span></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($taskDocumentUploads ?? [])): ?>
                    <div class="ft-upload-ready-row"><span><?php echo e(count($taskDocumentUploads ?? [])); ?> file<?php echo e(count($taskDocumentUploads ?? [])===1?'':'s'); ?> ready</span><button class="ft-new-job-btn" type="button" wire:click="uploadSelectedTaskDocuments" wire:loading.attr="disabled" wire:target="uploadSelectedTaskDocuments">Upload &amp; link</button><span wire:loading wire:target="uploadSelectedTaskDocuments">Uploading…</span></div>
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
                        <select wire:model="taskExistingDocumentId"><option value="">Select a stored document</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stored): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($stored->id); ?>"><?php echo e($stored->name); ?> · <?php echo e($stored->job?->job_number ?? 'Archive'); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $task->documents->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ft-attachment-row"><span class="ft-file-type"><?php echo e(strtoupper(pathinfo($doc->name,PATHINFO_EXTENSION) ?: 'FILE')); ?></span><b><?php echo e($doc->name); ?></b><small><?php echo e($doc->created_at?->format('M j, Y, H:i')); ?></small><a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?><button type="button" class="ft-doc-delete-button" wire:click="deleteSelectedTaskDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="ft-upload-note">Every file uploaded here is linked to this task and appears in Job Documents. A required document is counted only when this Task Pack task defines that document type.</p>
            </section>

            <section class="ft-detail-card ft-task-activity-card ft-friendly-activity">
                <div class="ft-activity-head">
                    <div><h2>Activity</h2><p>Comments and task changes, with who changed what and when.</p></div>
                    <div class="ft-activity-tabs"><button type="button" class="<?php echo e($activityTab==='all'?'active':''); ?>" wire:click="setTaskActivityTab('all')">All</button><button type="button" class="<?php echo e($activityTab==='comments'?'active':''); ?>" wire:click="setTaskActivityTab('comments')">Comments</button><button type="button" class="<?php echo e($activityTab==='history'?'active':''); ?>" wire:click="setTaskActivityTab('history')">History</button></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                    <div class="ft-comment-composer ft-friendly-composer"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => auth()->user()->name,'size' => 32]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->name),'size' => 32]); ?>
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
<?php endif; ?><input wire:model="taskComment" wire:keydown.enter="addTaskComment" placeholder="Write a comment about this task..."><button class="ft-new-job-btn" type="button" wire:click="addTaskComment" wire:loading.attr="disabled" wire:target="addTaskComment">Comment</button></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="ft-activity-feed">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $eventLabel = $entry->kind === 'comment' ? 'Comment' : \Illuminate\Support\Str::headline(str_replace(['task.','job.'], '', (string) $entry->event));
                            $actorName = $entry->user?->name ?? 'System';
                        ?>
                        <article class="ft-activity-entry <?php echo e($entry->kind==='comment' ? 'is-comment' : 'is-history'); ?>">
                            <div class="ft-activity-entry-avatar"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $actorName,'size' => 32]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actorName),'size' => 32]); ?>
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
                                <div class="ft-activity-entry-head"><div><b><?php echo e($actorName); ?></b><span class="ft-activity-kind <?php echo e($entry->kind==='comment' ? 'comment' : 'history'); ?>"><?php echo e($entry->kind==='comment' ? 'Comment' : 'Change'); ?></span></div><time title="<?php echo e($entry->created_at?->format('M j, Y g:i A')); ?>"><?php echo e($entry->created_at?->diffForHumans()); ?></time></div>
                                <p><?php echo e($entry->body); ?></p>
                                <div class="ft-activity-entry-meta"><span><?php echo e($eventLabel); ?></span><span>•</span><span><?php echo e($entry->created_at?->format('M j, Y · g:i A')); ?></span></div>
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
            <section class="ft-detail-card ft-management-card"><h2>Management attention</h2><div class="ft-attention-row"><span>Required evidence</span><b><span class="<?php echo e($taskDocumentName ? 'ft-red-doc-icon' : ''); ?>">▯</span> <?php echo e($taskDocumentName ?: 'No required evidence'); ?></b></div><div class="ft-attention-row"><span>Attention</span><b class="<?php echo e($task->needs_attention ? 'danger-text' : ''); ?>"><span class="ft-red-flag">⚑</span> <?php echo e($task->needs_attention ? 'Marked as attention needed' : 'Not flagged'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button class="ft-link-blue" type="button" wire:click="toggleTaskAttention"><?php echo e($task->needs_attention ? 'Clear flag' : 'Flag task'); ?></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></section>

            <section class="ft-detail-card ft-job-context-card"><h2>Job context</h2><button class="ft-link-blue ft-job-context-link" wire:click="closeTask"><?php echo e($job?->job_number); ?> ↗</button><b><?php echo e($job?->title); ?></b><div><span>Client</span><b><?php echo e($job?->client?->name); ?></b></div><div><span>Job health</span><b class="<?php echo e($job?->needs_attention ? 'danger-text' : ''); ?>"><span class="<?php echo e($job?->needs_attention ? 'ft-red-dot' : ''); ?>"></span><?php echo e($job?->needs_attention ? 'Needs Attention' : $job?->health); ?></b></div><div><span>Delivery</span><b><?php echo e($job?->delivery_date?->format('M j, Y') ?? '—'); ?></b></div><div class="ft-context-progress"><span>Job progress</span><b><?php echo e($job?->progress); ?>%</b><div class="ft-line-progress"><span style="width:<?php echo e($job?->progress ?? 0); ?>%"></span></div></div><button class="ft-link-blue ft-open-job" wire:click="closeTask">Open job details ↗</button></section>

            <section class="ft-detail-card ft-dependency-card"><h2>Dependencies</h2><p class="ok-text"><span class="ft-check-circle">✓</span> No active blockers</p><p><span>Previous task:</span> <b><?php echo e($previousTask?->title ?? 'None'); ?></b></p></section>
            <section class="ft-detail-card ft-task-meta-card">Created <?php echo e($task->created_at?->format('M j, Y')); ?> <span>·</span> Updated <?php echo e($task->updated_at?->diffForHumans()); ?></section>
        </aside>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/task-detail.blade.php ENDPATH**/ ?>