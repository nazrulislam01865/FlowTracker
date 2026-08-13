<section class="panel ft-inquiry-taskflow-panel">
    <header class="panelhead"><div><h2>Inquiry Taskflow</h2><p>Task status can be changed at any time, including reopening a completed task.</p></div><div class="task-control-row"><span class="task-count-pill"><?php echo e($totalTasks); ?> Tasks</span><span class="manage-badge">Taskflow</span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAddInquiryTask): ?><button class="primary" type="button" wire:click="openAddTaskForm" style="min-height:34px">＋ Add Task</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></header>
    <div class="ft-inquiry-task-grid-head" aria-hidden="true">
        <span>#</span><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Files</span><span>Action</span>
    </div>
    <div class="ft-inquiry-task-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inquiry->tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $state = $task->completed_at ? 'done' : ($task->started_at ? 'active' : 'wait');
                $fileOk = !$task->requires_submission || (int)$task->documents_count > 0;
                $completedStatus = \App\Services\InquiryService::AUTO_COMPLETED_STATUS;
                $completionNeedsRequiredFile = (bool) $task->requires_submission && !$fileOk;
                $taskAccess = app(\App\Services\AccessControlService::class);
                $canChangeStatusThisTask = !$inquiry->result && app(\App\Services\InquiryService::class)->canEditTask(auth()->user(), $task);
                // Editing a task and assigning a task are independent matrix permissions.
                $canEditTaskFields = $canChangeStatusThisTask;
                $canAssignThisTask = !$inquiry->result && $taskAccess->canAssignInquiryTask(auth()->user(), $task);
                $canAttachFileThisTask = !$inquiry->result && $canChangeStatusThisTask && ($canCreateDocuments || $canLinkDocuments);
                $canDeleteTaskDocuments = !$inquiry->result && $canChangeStatusThisTask && $canDeleteDocuments;
                $canAttachThisTask = $canAttachFileThisTask; // legacy alias used by the modal/resource block.
                $canEditThisTask = $state !== 'done' && $canChangeStatusThisTask;
                $taskDeepLinked = (int)($selectedTaskId ?? 0) === (int)$task->id;
                $canCompleteThisTask = !$task->completed_at && $task->started_at !== null;
            ?>
            <div class="ft-inquiry-task-row <?php echo e($state); ?> <?php echo e($taskDeepLinked ? 'is-highlighted' : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-row-'.e($task->id).''; ?>wire:key="inquiry-task-row-<?php echo e($task->id); ?>">
                <div class="ft-inquiry-task-step"><span><?php echo e($state === 'done' ? '✓' : $i + 1); ?></span></div>
                <div class="ft-inquiry-task-copy">
                    <strong><?php echo e($task->title); ?></strong>
                    <div class="ft-rich-text-content ft-inquiry-task-description"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->description): ?><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $task->description]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->description)]); ?>
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
<?php endif; ?><?php else: ?> No instructions added. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->requires_submission): ?><span class="reqfile <?php echo e($fileOk ? 'ok' : ''); ?>"><?php echo e($fileOk ? '✓ File submitted' : '□ Required file'); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-inquiry-assignee-inline ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-task-'.$task->id.'-assignee')->toHtml() ?>, label: 'task assignee', value: <?php echo \Illuminate\Support\Js::from($task->assignee_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->assignee?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($task->assignee?->profileImageUrl() ?? '')->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeInline(<?php echo e($task->id); ?>, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })">
                    <div class="ft-inquiry-inline-display-row">
                        <div x-show="!editing" class="ft-inquiry-assignee-display">
                            <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 28]); ?>
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
<?php endif; ?></span>
                            <span class="ft-inquiry-assignee-name" x-text="display"><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignThisTask): ?><button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignThisTask): ?>
                        <div x-cloak x-show="editing" class="ft-inquiry-assignee-picker">
                            <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task->assignee_id ?? '','selectedLabel' => $task->assignee?->name ?? 'Unassigned','parentType' => 'inquiry','parentId' => $inquiry->id,'triggerClass' => 'ft-task-inline-input','variant' => 'compact','menuWidth' => 260]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee?->name ?? 'Unassigned'),'parent-type' => 'inquiry','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->id),'trigger-class' => 'ft-task-inline-input','variant' => 'compact','menu-width' => 260]); ?>
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

                <div class="ft-inquiry-task-date ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-task-'.$task->id.'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('M j, Y') ?? 'Set due date')->toHtml() ?> })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                    <div class="ft-inquiry-inline-display-row" x-show="!editing">
                        <span class="ft-inquiry-inline-value" x-text="display"><?php echo e($task->due_date?->format('M j, Y') ?? 'Set due date'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTaskFields): ?><button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryDue.showPicker ? $refs.inquiryDue.showPicker() : $refs.inquiryDue.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTaskFields): ?>
                        <input x-ref="inquiryDue" x-cloak x-show="editing" x-model="draftValue" class="ft-inquiry-inline-input" type="date"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:blur="if (editing) cancelEdit()"
                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueInline(<?php echo e($task->id); ?>, draftValue))">
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
                <div class="task-status-cell">
                    <span
                        class="ft-task-inline-status-shell ft-inline-edit-shell"
                        x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-task-'.$task->id.'-status')->toHtml() ?>, label: 'task status', value: <?php echo \Illuminate\Support\Js::from($task->status)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->status)->toHtml() ?> })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <?php
                            $taskStatusColor = app(\App\Services\MasterDataService::class)->colorFor('task_status', (string) $task->status);
                        ?>
                        <select
                            data-master-color-select
                            class="ft-inline-task-status <?php echo e($taskStatusColor ? 'ft-master-color' : \App\Support\JobDetailPresenter::taskStatusClass((string) $task->status)); ?>"
                            style="<?php echo e(\App\Support\MasterColor::style($taskStatusColor)); ?>"
                            x-model="draftValue"
                            x-on:change="const select=$event.target; const next=select.value; const needsRequiredFile=(next === <?php echo \Illuminate\Support\Js::from($completedStatus)->toHtml() ?> && <?php echo \Illuminate\Support\Js::from($completionNeedsRequiredFile)->toHtml() ?>); if(needsRequiredFile){ draftValue=value; select.value=value; window.FlowTrackMasterColor?.applySelect(select); $wire.requestTaskCompletionFile(<?php echo e($task->id); ?>); return; } window.FlowTrackMasterColor?.applySelect(select); commit(next, selectedLabel($event), async () => { const result=await $wire.updateTaskStatusInline(<?php echo e($task->id); ?>, draftValue); if(result?.inquiryStatus) inquiryStatus=result.inquiryStatus; if(result?.inquiryColor) inquiryStatusColor=result.inquiryColor; if(result && Object.prototype.hasOwnProperty.call(result,'inquiryStartValue')){ inquiryStartValue=result.inquiryStartValue || ''; inquiryStartDisplay=result.inquiryStartDisplay || '—'; window.dispatchEvent(new CustomEvent('flowtrack-inquiry-started',{detail:{value:inquiryStartValue,display:inquiryStartDisplay}})); } return result; }).then(() => window.FlowTrackMasterColor?.applyAll(document))"
                            :disabled="status === 'saving'"
                            <?php if(!$canChangeStatusThisTask): echo 'disabled'; endif; ?>
                            aria-label="Change <?php echo e($task->title); ?> status"
                        >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$inquiryTaskStatusOptions->contains(fn ($statusOption) => strcasecmp((string) $statusOption, (string) $task->status) === 0)): ?>
                                <option value="<?php echo e($task->status); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('task_status', (string) $task->status)); ?>" selected><?php echo e($task->status); ?></option>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryTaskStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('task_status', $statusOption)); ?>"><?php echo e($statusOption); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeStatusThisTask): ?><?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
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
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
                <div class="ft-inquiry-task-files">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAttachFileThisTask || $canChangeStatusThisTask): ?>
                        <div class="ft-inquiry-task-add-actions" aria-label="Add task resource">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAttachFileThisTask): ?>
                                <button
                                    class="ft-inquiry-task-add-icon"
                                    type="button"
                                    wire:click="openTaskDocumentModal(<?php echo e($task->id); ?>)"
                                    title="Add file"
                                    aria-label="Add file to <?php echo e($task->title); ?>"
                                >
                                    <span class="ft-inquiry-task-add-plus">+</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h6"/></svg>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeStatusThisTask): ?>
                                <button
                                    class="ft-inquiry-task-add-icon <?php echo e((int)$taskLinkFormTaskId === (int)$task->id ? 'is-active' : ''); ?>"
                                    type="button"
                                    wire:click="openTaskLinkForm(<?php echo e($task->id); ?>)"
                                    title="Add link"
                                    aria-label="Add external link to <?php echo e($task->title); ?>"
                                >
                                    <span class="ft-inquiry-task-add-plus">+</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="ft-inquiry-task-resource-count"><b><?php echo e($task->documents_count); ?></b> file<?php echo e($task->documents_count === 1 ? '' : 's'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->links->isNotEmpty()): ?> · <b><?php echo e($task->links->count()); ?></b> link<?php echo e($task->links->count() === 1 ? '' : 's'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </div>
                <div class="ft-inquiry-task-action">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($state === 'done'): ?>
                        <div class="ft-inquiry-complete-block">
                            <span class="ft-inquiry-complete-state">✓ Completed</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->completed_at): ?>
                                <span class="ft-inquiry-completed-at">
                                    <span><?php echo e(\App\Support\UserLocalTime::format($task->completed_at, 'M j, Y')); ?></span>
                                    <span><?php echo e(\App\Support\UserLocalTime::format($task->completed_at, 'g:i A')); ?></span>
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php elseif($canCompleteThisTask): ?>
                        <button class="ft-inquiry-action-button primary-action" type="button" wire:click="completeTaskInline(<?php echo e($task->id); ?>)" wire:loading.attr="disabled" wire:target="completeTaskInline(<?php echo e($task->id); ?>)" <?php if(!$canEditThisTask || !$fileOk): echo 'disabled'; endif; ?>><?php echo e(!$fileOk ? 'File required' : 'Complete'); ?></button>
                    <?php else: ?>
                        <button class="ft-inquiry-action-button" type="button" disabled>Waiting</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int)$taskLinkFormTaskId === (int)$task->id || $task->documents->isNotEmpty() || $task->links->isNotEmpty()): ?>
                <div class="ft-inquiry-task-document-list ft-inquiry-task-resource-list" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-resources-'.e($task->id).''; ?>wire:key="inquiry-task-resources-<?php echo e($task->id); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int)$taskLinkFormTaskId === (int)$task->id && $canChangeStatusThisTask): ?>
                        <form class="ft-inquiry-task-link-form" wire:submit.prevent="saveTaskLink(<?php echo e($task->id); ?>)" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-link-form-'.e($task->id).''; ?>wire:key="inquiry-task-link-form-<?php echo e($task->id); ?>">
                            <div class="ft-inquiry-task-link-input-wrap">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                <input
                                    type="text"
                                    inputmode="url"
                                    wire:model="taskLinkUrl"
                                    placeholder="Paste link, e.g. https://drive.google.com/..."
                                    autocomplete="url"
                                    autofocus
                                    aria-label="External link"
                                >
                            </div>
                            <div class="ft-inquiry-task-link-form-actions">
                                <button class="secondary" type="button" wire:click="cancelTaskLinkForm">Cancel</button>
                                <button class="primary" type="submit" wire:loading.attr="disabled" wire:target="saveTaskLink(<?php echo e($task->id); ?>)">Add</button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskLinkUrl'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-inquiry-task-link-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $task->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-inquiry-task-document-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-document-'.e($taskDocument->id).''; ?>wire:key="inquiry-task-document-<?php echo e($taskDocument->id); ?>">
                            <span class="ft-inquiry-task-file-type"><?php echo e(strtoupper(pathinfo($taskDocument->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span>
                            <div class="ft-inquiry-task-file-copy">
                                <b title="<?php echo e($taskDocument->name); ?>"><?php echo e($taskDocument->name); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocument->note): ?><span class="ft-inquiry-task-file-note"><?php echo e($taskDocument->note); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <small><?php echo e($taskDocument->created_at ? \App\Support\UserLocalTime::format($taskDocument->created_at, 'M j, Y, g:i A') : '—'); ?></small>
                            </div>
                            <div class="ft-inquiry-task-file-actions">
                                <a href="<?php echo e(route('inquiries.documents.open', $taskDocument)); ?>" target="_blank" rel="noopener">Open</a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocuments): ?><a href="<?php echo e(route('inquiries.documents.download', $taskDocument)); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteTaskDocuments): ?>
                                    <button
                                        type="button"
                                        class="ft-inquiry-task-file-remove"
                                        wire:click="deleteTaskDocument(<?php echo e($task->id); ?>, <?php echo e($taskDocument->id); ?>)"
                                        wire:loading.attr="disabled"
                                        wire:target="deleteTaskDocument(<?php echo e($task->id); ?>, <?php echo e($taskDocument->id); ?>)"
                                        wire:confirm="<?php echo e($task->completed_at && $task->requires_submission && (int) $task->documents_count === 1 ? 'Remove this required file? The task will reopen to In Progress because a completed required-file task cannot remain complete without a file.' : 'Remove this attachment from the task?'); ?>"
                                        title="Remove attachment"
                                        aria-label="Remove <?php echo e($taskDocument->name); ?>"
                                    >×</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $task->links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-inquiry-task-link-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-link-'.e($taskLink->id).''; ?>wire:key="inquiry-task-link-<?php echo e($taskLink->id); ?>">
                            <span class="ft-inquiry-task-link-type" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            </span>
                            <div class="ft-inquiry-task-link-copy">
                                <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo e($taskLink->url); ?>"><?php echo e(\Illuminate\Support\Str::limit($taskLink->url, 110)); ?></a>
                                <small><?php echo e($taskLink->created_at ? \App\Support\UserLocalTime::format($taskLink->created_at, 'M j, Y, g:i A') : '—'); ?></small>
                            </div>
                            <div class="ft-inquiry-task-link-actions">
                                <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer" title="Open external link">Open ↗</a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeStatusThisTask): ?>
                                    <button
                                        type="button"
                                        class="ft-inquiry-task-file-remove"
                                        wire:click="deleteTaskLink(<?php echo e($task->id); ?>, <?php echo e($taskLink->id); ?>)"
                                        wire:loading.attr="disabled"
                                        wire:target="deleteTaskLink(<?php echo e($task->id); ?>, <?php echo e($taskLink->id); ?>)"
                                        wire:confirm="Remove this link from the task?"
                                        title="Remove link"
                                        aria-label="Remove link"
                                    >×</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-inquiry-empty-workflow">No taskflow tasks configured.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddTaskForm && $canAddInquiryTask): ?>
        <div class="ft-inquiry-add-task" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-add-task-form'; ?>wire:key="inquiry-add-task-form">
            <div class="ft-inquiry-add-task-head">
                <div><strong>Add taskflow task</strong><span>The task is appended after the existing taskflow. If the taskflow was already complete, this new task becomes active.</span></div>
                <button class="ft-inquiry-add-task-close" type="button" wire:click="cancelAddTask" aria-label="Close add task form">×</button>
            </div>
            <div class="ft-inquiry-add-task-grid">
                <label class="ft-inquiry-add-task-field ft-inquiry-add-task-field-wide"><span>Task name *</span><input type="text" wire:model="newTaskName" placeholder="Task name"></label>
                <label class="ft-inquiry-add-task-field"><span>Assignee</span><select wire:model="newTaskAssigneeId"><option value="">Unassigned</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $userOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($userOption['id']); ?>"><?php echo e($userOption['name']); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label>
                <label class="ft-inquiry-add-task-field"><span>Due date</span><input type="date" wire:model="newTaskDueDate" onclick="this.showPicker && this.showPicker()"></label>
                <label class="ft-inquiry-add-task-field ft-inquiry-add-task-field-wide"><span>Instructions</span><textarea data-rich-text wire:model="newTaskDescription" placeholder="Describe what must be completed for this task or paste screenshots here."></textarea></label>
                <label class="ft-inquiry-add-task-field"><span>Submission</span><select wire:model.live.boolean="newTaskRequiresSubmission"><option value="0">No required file</option><option value="1">Required file</option></select></label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newTaskRequiresSubmission): ?><label class="ft-inquiry-add-task-field"><span>Required file</span><input type="text" wire:model="newTaskSubmissionLabel" placeholder="Submission name"></label><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newTaskName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-inquiry-add-task-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="ft-inquiry-add-task-actions"><button class="secondary" type="button" wire:click="cancelAddTask">Cancel</button><button class="primary" type="button" wire:click="addInquiryTask" wire:loading.attr="disabled" wire:target="addInquiryTask">Add Task</button></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/_taskflow.blade.php ENDPATH**/ ?>