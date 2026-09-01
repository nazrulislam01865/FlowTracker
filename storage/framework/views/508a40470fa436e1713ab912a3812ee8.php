<?php
    $masterData = app(\App\Services\MasterDataService::class);
    $canCreateTaskPack = auth()->user()->canModule('taskpacks', 'create');
    $canEditTaskPack = auth()->user()->canModule('taskpacks', 'edit');
    $canDeleteTaskPack = auth()->user()->canModule('taskpacks', 'delete');
?>
<div wire:init="loadTaskPacks" class="ft-admin-reference ft-taskpack-reference">
    <?php if (isset($component)) { $__componentOriginal3163ec004b5d45b515aa12208f25fd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3163ec004b5d45b515aa12208f25fd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.page-header','data' => ['title' => 'Task Pack Setup','description' => 'Create reusable task sequences that activate when a Job enters a workflow phase','wrapActions' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Task Pack Setup','description' => 'Create reusable task sequences that activate when a Job enters a workflow phase','wrap-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateTaskPack): ?><a href="<?php echo e(route('task-pack.create')); ?>" wire:navigate class="ft-admin-primary">＋ Add Task Pack</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3163ec004b5d45b515aa12208f25fd6f)): ?>
<?php $attributes = $__attributesOriginal3163ec004b5d45b515aa12208f25fd6f; ?>
<?php unset($__attributesOriginal3163ec004b5d45b515aa12208f25fd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3163ec004b5d45b515aa12208f25fd6f)): ?>
<?php $component = $__componentOriginal3163ec004b5d45b515aa12208f25fd6f; ?>
<?php unset($__componentOriginal3163ec004b5d45b515aa12208f25fd6f); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash success"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['pack'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['item'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$showPackDeleteModal): ?>
    <div class="ft-admin-stats">
        <div><span>Total Task Packs</span><b><?php echo e($packsReady ? $totalPacks : '…'); ?></b></div>
        <div><span>Active Task Packs</span><b><?php echo e($packsReady ? $activePacks : '…'); ?></b></div>
        <div><span>Configured Tasks</span><b><?php echo e($packsReady ? $configuredTasks : '…'); ?></b></div>
        <div><span>Mapped Phases</span><b><?php echo e($packsReady ? $mappedPhases : '…'); ?></b></div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$packsReady): ?>
        <?php echo $__env->make('livewire.shared.card-list-placeholder', ['cards' => 4], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
    <?php if (isset($component)) { $__componentOriginalf2050c2d7adc455d4aaef8436760cc2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf2050c2d7adc455d4aaef8436760cc2c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.list','data' => ['class' => 'ft-taskpack-grid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-taskpack-grid']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $packs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <section class="ft-taskpack-card">
                <div class="ft-taskpack-card-head">
                    <div>
                        <h2><?php echo e($pack->name); ?></h2>
                        <p><?php echo e($pack->code); ?> · <?php echo e($pack->items->count()); ?> predefined task<?php echo e($pack->items->count() === 1 ? '' : 's'); ?> · <?php echo e($pack->is_active ? 'Active' : 'Inactive'); ?></p>
                    </div>
                    <div class="ft-taskpack-card-actions">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTaskPack): ?><a class="ft-admin-outline-small" href="<?php echo e(route('task-pack.edit', $pack->id)); ?>" wire:navigate>Edit</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteTaskPack): ?><button type="button" class="ft-admin-danger-small" wire:click="requestDeletePack(<?php echo e($pack->id); ?>)" wire:loading.attr="disabled" wire:target="requestDeletePack">Delete</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditTaskPack && !$canDeleteTaskPack): ?><span class="small muted">View only</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <p class="ft-taskpack-description"><?php echo e($pack->description ?: 'No description'); ?></p>

                <div class="ft-taskpack-items">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $pack->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-taskpack-item-row ft-taskpack-item-row--colored" style="<?php echo e(\App\Support\MasterColor::style($item->color ?? '#2563EB')); ?>">
                            <div>
                                <b><span class="ft-task-color-dot" aria-hidden="true"></span><?php echo e($loop->iteration); ?>. <?php echo e($item->title); ?></b>
                                <small>
                                    <?php echo e($item->defaultAssignee?->name ?? 'Unassigned'); ?> · Due set from Task details ·
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->priority): ?>
                                        <?php
                                            $itemPriorityColor = $masterData->displayColorFor('priority', $item->priority->name);
                                        ?>
                                        <span class="ft-master-color-text" style="<?php echo e(\App\Support\MasterColor::style($itemPriorityColor)); ?>"><?php echo e($item->priority->name); ?></span>
                                    <?php else: ?>
                                        Use Job priority
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->documentCategory): ?> · Required file: <?php echo e($item->documentCategory->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </small>
                            </div>
                            <span class="<?php echo e($item->is_required ? 'is-required' : 'is-optional'); ?>"><?php echo e($item->is_required ? 'Mandatory' : 'Optional'); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-taskpack-empty">No predefined tasks.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-admin-empty-wide">No Task Packs configured. Use “Add Task Pack” to create the first one.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf2050c2d7adc455d4aaef8436760cc2c)): ?>
<?php $attributes = $__attributesOriginalf2050c2d7adc455d4aaef8436760cc2c; ?>
<?php unset($__attributesOriginalf2050c2d7adc455d4aaef8436760cc2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf2050c2d7adc455d4aaef8436760cc2c)): ?>
<?php $component = $__componentOriginalf2050c2d7adc455d4aaef8436760cc2c; ?>
<?php unset($__componentOriginalf2050c2d7adc455d4aaef8436760cc2c); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPackDeleteModal): ?>
        <?php if (isset($component)) { $__componentOriginal5cb97297f0cc6a73f3cdc7b6541a9888 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5cb97297f0cc6a73f3cdc7b6541a9888 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.safe-delete-modal','data' => ['title' => 'Delete Task Pack permanently?','closeAction' => 'closePackDelete','label' => 'Delete Task Pack permanently']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.safe-delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Delete Task Pack permanently?','close-action' => 'closePackDelete','label' => 'Delete Task Pack permanently']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="flash error ft-delete-impact-flush">
                    This permanently deletes this reusable Task Pack setup. Existing Job snapshots and Job Tasks are not deleted.
                </div>

                <div>
                    <b class="ft-delete-impact-title"><?php echo e($packDeleteImpact['name'] ?? 'Task Pack'); ?></b>
                    <span class="ft-delete-impact-subtitle">
                        FlowTrack checked Workflow mappings and Jobs that originated from those Workflows before allowing deletion.
                    </span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!($packDeleteImpact['can_delete'] ?? true)): ?>
                    <div class="flash error ft-delete-impact-flush">
                        <?php echo e($packDeleteImpact['blocked_reason'] ?? 'This Task Pack cannot be deleted.'); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ft-admin-stats ft-delete-impact-stats">
                    <div><span>Mapped phases</span><b><?php echo e($packDeleteImpact['mapped_phase_count'] ?? 0); ?></b></div>
                    <div><span>Jobs preserved</span><b><?php echo e($packDeleteImpact['job_count'] ?? 0); ?></b></div>
                    <div><span>Tasks preserved</span><b><?php echo e($packDeleteImpact['task_count'] ?? 0); ?></b></div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['mapped_phase_count'] ?? 0) > 0): ?>
                    <div class="ft-delete-impact-box ft-delete-impact-box--info">
                        <b class="ft-delete-impact-heading">Workflow phases using this Task Pack</b>
                        <div class="ft-delete-impact-list ft-delete-impact-list--compact">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($packDeleteImpact['mapped_phases'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <span class="ft-delete-impact-item"><b><?php echo e($phase['workflow_name']); ?></b> · Stage <?php echo e($phase['sequence']); ?> · <span class="ft-phase-color-label" style="<?php echo e(\App\Support\MasterColor::style($phase['color'] ?? null)); ?>"><?php echo e($phase['name']); ?></span></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['mapped_phase_count'] ?? 0) > count($packDeleteImpact['mapped_phases'] ?? [])): ?>
                            <small class="ft-delete-impact-more">And <?php echo e(($packDeleteImpact['mapped_phase_count'] ?? 0) - count($packDeleteImpact['mapped_phases'] ?? [])); ?> more mapped phases.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small class="ft-delete-impact-note">These Workflow phases will remain, but their Task Pack assignment will be removed.</small>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['job_count'] ?? 0) > 0): ?>
                    <div class="ft-delete-impact-box ft-delete-impact-box--danger">
                        <b class="ft-delete-impact-heading ft-delete-impact-heading--danger">Jobs that remain independent of this Task Pack</b>
                        <div class="ft-delete-impact-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($packDeleteImpact['jobs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="ft-delete-impact-row">
                                    <span class="ft-delete-impact-job"><b><?php echo e($job['job_number']); ?></b> · <?php echo e($job['title']); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job['trashed'] ?? false): ?><small class="ft-delete-impact-muted">Already trashed</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['job_count'] ?? 0) > count($packDeleteImpact['jobs'] ?? [])): ?>
                            <small class="ft-delete-impact-more">And <?php echo e(($packDeleteImpact['job_count'] ?? 0) - count($packDeleteImpact['jobs'] ?? [])); ?> more linked Jobs.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <p class="ft-delete-impact-copy">
                    Deleting this reusable Task Pack does not delete existing Job Tasks. Older Jobs are snapshotted first when needed, and each Job keeps its own copied phase/task definitions.
                </p>
             <?php $__env->slot('footer', null, []); ?> 
                <button type="button" class="ft-admin-cancel" wire:click="closePackDelete">Cancel</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($packDeleteImpact['can_delete'] ?? true): ?>
                    <button type="button" class="ft-admin-danger" wire:click="confirmDeletePack" wire:loading.attr="disabled" wire:target="confirmDeletePack">
                        <span wire:loading.remove wire:target="confirmDeletePack">Delete Task Pack only</span>
                        <span wire:loading wire:target="confirmDeletePack">Deleting…</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5cb97297f0cc6a73f3cdc7b6541a9888)): ?>
<?php $attributes = $__attributesOriginal5cb97297f0cc6a73f3cdc7b6541a9888; ?>
<?php unset($__attributesOriginal5cb97297f0cc6a73f3cdc7b6541a9888); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5cb97297f0cc6a73f3cdc7b6541a9888)): ?>
<?php $component = $__componentOriginal5cb97297f0cc6a73f3cdc7b6541a9888; ?>
<?php unset($__componentOriginal5cb97297f0cc6a73f3cdc7b6541a9888); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/task-pack-setup/index.blade.php ENDPATH**/ ?>