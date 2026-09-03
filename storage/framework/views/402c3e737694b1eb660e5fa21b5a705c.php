<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <article class="order-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-inquiry-'.e($group['id']).''; ?>wire:key="my-work-inquiry-<?php echo e($group['id']); ?>" x-data="{ open: true }">
        <header class="order-head">
            <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Collapse <?php echo e($group['number']); ?>"><span x-text="open ? '⌄' : '›'">⌄</span></button>
            <span class="order-identity">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?><a class="order-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a><?php else: ?><span class="order-id"><?php echo e($group['number']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="order-title"><?php echo e($group['title']); ?></span>
            </span>
            <span class="order-client"><?php echo e($group['client']); ?></span>
            <span class="order-stage"><?php echo e($group['stage']); ?></span>
            <span class="health <?php echo e($group['healthColor'] ? 'ft-master-color' : $group['healthTone']); ?>" style="<?php echo e(\App\Support\MasterColor::style($group['healthColor'] ?? null)); ?>"><?php echo e($group['health']); ?></span>
            <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
            <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
        </header>

        <div class="task-rows" x-show="open">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div
                    class="task-row"
                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-inquiry-task-'.e($task['id']).''; ?>wire:key="my-work-inquiry-task-<?php echo e($task['id']); ?>"
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
                                const result=await $wire.updateInquiryTaskStatus(<?php echo e($task['id']); ?>,next,this.version);
                                if(!result?.ok){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);return;}
                                this.currentStatus=result.status||next;
                                this.version=result.version||this.version;
                                if(result.refresh)await $wire.$refresh();
                            }catch(error){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);}
                            finally{this.saving=false;select.disabled=false;}
                        }
                    }"
                    x-bind:class="{ 'saving': saving }"
                >
                    <div class="task-main">
                        <a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a>
                        <span class="task-ref"><?php echo e($task['number']); ?></span>
                    </div>
                    <span class="phase"><?php echo e($task['phase']); ?></span>
                    <span class="assignee" title="<?php echo e($task['assignee']); ?>">
                        <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task['assignee'],'src' => $task['assigneeAvatar'],'size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeAvatar']),'size' => 22]); ?>
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
                        <span class="assignee-name"><?php echo e($task['assignee']); ?></span>
                    </span>
                    <span
                        class="due-editor ft-inline-edit-shell <?php echo e($task['dueTone']); ?>"
                        x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-inquiry-task-'.$task['id'].'-due-date')->toHtml() ?>, label: 'inquiry task due date', value: <?php echo \Illuminate\Support\Js::from($task['dueValue'])->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['dueDisplay'])->toHtml() ?> })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <span x-show="!editing" x-text="display" class="ft-task-inline-display"><?php echo e($task['dueDisplay']); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canEdit']): ?>
                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact" title="Edit due date" aria-label="Edit due date for <?php echo e($task['title']); ?>" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.myInquiryDue.showPicker ? $refs.myInquiryDue.showPicker() : $refs.myInquiryDue.focus())">✎</button>
                            <input x-ref="myInquiryDue" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input" type="date"
                                x-on:keydown.escape.prevent="cancelEdit()"
                                x-on:blur="if (editing) cancelEdit()"
                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateInquiryTaskDueDate(<?php echo e($task['id']); ?>, draftValue))">
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
                    <select data-master-color-select class="status-select <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event); window.FlowTrack.ui.masterColor?.applySelect($event.currentTarget)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                        <?php $inquiryTaskStatusOptions = app(\App\Services\InquiryService::class)->openTaskStatusOptions((string) $task['status']); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryTaskStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('inquiry_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <span class="flag <?php echo e($task['flagTone']); ?>"><?php echo e($task['flag']); ?></span>
                    <span class="updated"><?php echo e($task['updated']); ?></span>
                    <a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </article>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/my-work/_inquiry-groups.blade.php ENDPATH**/ ?>