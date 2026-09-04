<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $workGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <tbody
        class="ft-my-task-order-group"
        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-order-'.e($group['id']).''; ?>wire:key="my-work-order-<?php echo e($group['id']); ?>"
        x-data="{ open: true }"
        x-effect="open = groupsExpanded"
    >
        <tr class="ft-my-task-order-summary-row">
            <td colspan="8">
                <div class="ft-my-task-order-summary">
                    <button
                        type="button"
                        class="ft-my-task-collapse"
                        x-on:click="open = !open"
                        x-bind:aria-expanded="open.toString()"
                        aria-label="Toggle <?php echo e($group['number']); ?> tasks"
                    >
                        <span x-text="open ? '⌄' : '›'">⌄</span>
                    </button>

                    <div class="ft-my-task-order-identity">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?>
                            <a class="order-cell-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a>
                        <?php else: ?>
                            <span class="order-cell-id"><?php echo e($group['number']); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="order-cell-ref"><?php echo e($group['title']); ?></span>
                    </div>

                    <div class="ft-my-task-summary-meta">
                        <span><b>Client</b><?php echo e($group['client']); ?></span>
                        <span>
                            <b>Stage</b>
                            <i class="stage-chip" style="--stage:<?php echo e($group['stageColor'] ?: '#2563EB'); ?>"><?php echo e($group['stage']); ?></i>
                        </span>
                        <span class="ft-my-task-summary-progress">
                            <b>Progress</b>
                            <i class="row-progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i>
                            <strong><?php echo e($group['progress']); ?>%</strong>
                        </span>
                        <span><b>Visible work</b><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                    </div>
                </div>
            </td>
        </tr>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $taskRowStyle = \App\Support\MasterColor::taskRowStyle($task['taskColor'] ?? null);
            ?>
            <tr
                class="order-row ft-my-task-row <?php echo e(filled($task['taskColor'] ?? null) ? 'has-task-color' : ''); ?>"
                style="<?php echo e($taskRowStyle); ?>"
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-task-'.e($task['id']).''; ?>wire:key="my-work-task-<?php echo e($task['id']); ?>"
                x-show="open"
                x-data="{
                    saving:false,
                    version:<?php echo \Illuminate\Support\Js::from($task['version'])->toHtml() ?>,
                    currentStatus:<?php echo \Illuminate\Support\Js::from($task['status'])->toHtml() ?>,
                    async saveStatus(event){
                        const select=event.currentTarget;
                        const previous=this.currentStatus;
                        const next=select.value;
                        if(next===previous||this.saving)return;
                        this.saving=true;
                        select.disabled=true;
                        try{
                            const result=await $wire.updateTaskStatus(<?php echo e($task['id']); ?>,next,this.version);
                            if(!result?.ok){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);return;}
                            this.currentStatus=result.status||next;
                            this.version=result.version||this.version;
                            if(result.refresh || (result.completed && <?php echo \Illuminate\Support\Js::from($hideCompleted)->toHtml() ?>))await $wire.$refresh();
                        }catch(error){
                            select.value=previous;
                            window.FlowTrack.ui.masterColor?.applySelect(select);
                        }finally{
                            this.saving=false;
                            select.disabled=false;
                        }
                    }
                }"
                x-bind:class="{ 'is-saving': saving }"
                x-on:my-work-task-version.stop="if ($event.detail?.version) version = String($event.detail.version)"
            >
                <td>
                    <a class="order-cell-id ft-my-task-title" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a>
                    <span class="order-cell-ref"><?php echo e($task['number']); ?> · <?php echo e($group['number']); ?></span>
                </td>

                <td>
                    <span class="stage-chip" style="--stage:<?php echo e($task['phaseColor'] ?: '#2563EB'); ?>"><?php echo e($task['phase']); ?></span>
                </td>

                <td>
                    <div
                        class="owner-delivery assignee-editor ft-inline-edit-shell ft-my-task-assignee"
                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-task-'.e($task['id']).'-assignee-'.e($task['assigneeId'] ?: 0).''; ?>wire:key="my-work-task-<?php echo e($task['id']); ?>-assignee-<?php echo e($task['assigneeId'] ?: 0); ?>"
                        title="<?php echo e($task['assignee']); ?>"
                        x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-assignee')->toHtml() ?>, label: 'task assignee', value: <?php echo \Illuminate\Support\Js::from($task['assigneeId'] ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['assignee'])->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($task['assigneeAvatar'] ?? '')->toHtml() ?> })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                        x-on:click.outside="if (editing) cancelEdit()"
                        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssignee(<?php echo e($task['id']); ?>, draftValue, version), { avatarUrl: String($event.detail?.avatarUrl ?? '') }).then(async (ok) => { if (!ok) return; if (lastResponse?.version) $dispatch('my-work-task-version', { version: lastResponse.version }); if (lastResponse?.refresh) await $wire.$refresh(); })"
                    >
                        <div class="ft-my-task-assignee-display" x-show="!editing">
                            <?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
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
<?php endif; ?>
                            <span class="ft-my-task-assignee-copy">
                                <b x-text="display"><?php echo e($task['assignee']); ?></b>
                                <small>Task assignee</small>
                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canAssign']): ?>
                                <button
                                    x-show="!editing"
                                    :disabled="status === 'saving'"
                                    type="button"
                                    class="ft-inline-edit-button compact ft-my-task-edit-button"
                                    title="Edit assignee"
                                    aria-label="Edit assignee for <?php echo e($task['title']); ?>"
                                    x-on:click.stop="openRemotePicker($event.currentTarget)"
                                >✎</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canAssign']): ?>
                            <div x-cloak x-show="editing" class="ft-my-task-assignee-picker">
                                <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task['assigneeId'] ?? '','selectedLabel' => $task['assignee'],'parentType' => 'job','parentId' => $group['id'],'triggerClass' => 'assignee-picker-trigger','variant' => 'compact','menuWidth' => 300]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeId'] ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group['id']),'trigger-class' => 'assignee-picker-trigger','variant' => 'compact','menu-width' => 300]); ?>
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
                </td>

                <td>
                    <span
                        class="ft-inline-edit-shell ft-my-task-due <?php echo e($task['dueTone']); ?>"
                        x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task['dueValue'])->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['dueDisplay'])->toHtml() ?> })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <span x-show="!editing" x-text="display" class="ft-task-inline-display"><?php echo e($task['dueDisplay']); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canEdit']): ?>
                            <button
                                x-show="!editing"
                                :disabled="status === 'saving'"
                                type="button"
                                class="ft-inline-edit-button compact ft-my-task-edit-button"
                                title="Edit due date"
                                aria-label="Edit due date for <?php echo e($task['title']); ?>"
                                x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.myWorkDue.showPicker ? $refs.myWorkDue.showPicker() : $refs.myWorkDue.focus())"
                            >✎</button>
                            <input
                                x-ref="myWorkDue"
                                x-cloak
                                x-show="editing"
                                x-model="draftValue"
                                class="ft-task-inline-input"
                                type="date"
                                x-on:keydown.escape.prevent="cancelEdit()"
                                x-on:blur="if (editing) cancelEdit()"
                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDate(<?php echo e($task['id']); ?>, draftValue))"
                            >
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
                    <span class="order-cell-ref <?php echo e($task['dueTone'] === 'overdue' ? 'ft-my-task-overdue' : ''); ?>"><?php echo e($task['due']); ?></span>
                </td>

                <td>
                    <select
                        data-master-color-select
                        class="status-select ft-my-task-status <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>"
                        style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>"
                        <?php if($task['canEdit']): ?>
                            x-on:change="saveStatus($event); window.FlowTrack.ui.masterColor?.applySelect($event.currentTarget)"
                        <?php else: ?>
                            disabled
                        <?php endif; ?>
                        aria-label="Status for <?php echo e($task['title']); ?>"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $statusOptions, true)): ?>
                            <option value="<?php echo e($task['status']); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status'])); ?>" selected><?php echo e($task['status']); ?></option>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </td>

                <td>
                    <span
                        class="ft-my-task-flag <?php echo e($task['flagColor'] ? 'ft-master-color' : ''); ?>"
                        style="<?php echo e(\App\Support\MasterColor::style($task['flagColor'])); ?>"
                    ><?php echo e($task['flag']); ?></span>
                </td>

                <td>
                    <span class="stage-table-note"><?php echo e($task['updated']); ?></span>
                </td>

                <td>
                    <a class="stage-action ft-my-task-open" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a>
                </td>
            </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </tbody>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/my-work/_order-groups-v5.blade.php ENDPATH**/ ?>