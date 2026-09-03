<?php
    $canCreateWorkflow = auth()->user()->canModule('workflow', 'create');
    $canEditWorkflow = auth()->user()->canModule('workflow', 'edit');
    $canDeleteWorkflow = auth()->user()->canModule('workflow', 'delete');
?>
<div class="ft-admin-reference ft-workflow-reference">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$showWorkflowDeleteModal): ?>
    <?php if (isset($component)) { $__componentOriginal3163ec004b5d45b515aa12208f25fd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3163ec004b5d45b515aa12208f25fd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.page-header','data' => ['title' => 'Workflow Setup','description' => 'Configure Inquiry and Order workflows in one place. Build and edit reusable Task Packs separately.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Workflow Setup','description' => 'Configure Inquiry and Order workflows in one place. Build and edit reusable Task Packs separately.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('taskpacks.view')): ?><a href="<?php echo e(route('task-pack.setup')); ?>" wire:navigate class="ft-admin-outline">Task Pack Setup</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateWorkflow && $selected): ?>
                <a href="<?php echo e(route('workflow.create', ['source' => $selected->id])); ?>" wire:navigate class="ft-admin-outline">Duplicate</a>
            <?php elseif($canCreateWorkflow): ?>
                <span class="ft-admin-outline is-disabled">Duplicate</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateWorkflow): ?><a href="<?php echo e(route('workflow.create')); ?>" wire:navigate class="ft-admin-primary">＋ New Workflow</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php $__env->endSlot(); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['workflow'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phase'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-admin-stats">
        <div><span>Active Templates</span><b><?php echo e($activeTemplates); ?></b></div>
        <div><span>Phases in Selected Workflow</span><b><?php echo e($selectedPhaseCount); ?></b></div>
        <div><span>Allowed Starting Stages</span><b><?php echo e($allowedStartingStages); ?></b></div>
        <div><span>Automatic Transitions</span><b><?php echo e($automaticTransitions); ?></b></div>
    </div>

    <div class="ft-workflow-admin-layout">
        <?php if (isset($component)) { $__componentOriginalf2050c2d7adc455d4aaef8436760cc2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf2050c2d7adc455d4aaef8436760cc2c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.list','data' => ['tag' => 'aside','class' => 'ft-workflow-template-list']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'aside','class' => 'ft-workflow-template-list']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-workflow-list-label">WORKFLOW TEMPLATES</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button type="button" class="<?php echo e($workflow->id === $selectedWorkflowId ? 'active' : ''); ?>" wire:click="selectWorkflow(<?php echo e($workflow->id); ?>)">
                    <b><?php echo e($workflow->name); ?></b>
                    <span><?php echo e(ucfirst(rtrim((string) $workflow->applies_to, 's'))); ?> · <?php echo e($workflow->phases->count()); ?> phases · <?php echo e($workflow->is_active ? 'Active' : 'Inactive'); ?></span>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-workflow-list-empty">No workflows configured.</div>
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

        <?php if (isset($component)) { $__componentOriginal0d3af2beb9da1a1bfeb2af6029c8b146 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d3af2beb9da1a1bfeb2af6029c8b146 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.editor-panel','data' => ['class' => 'ft-workflow-editor-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.editor-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-workflow-editor-card']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
                <div class="ft-workflow-editor-head">
                    <div>
                        <h2><?php echo e($selected->name); ?></h2>
                        <p><?php echo e($selected->description ?: 'No description'); ?></p>
                        <span class="ft-auto-pill automatic"><?php echo e($selectedIsOrderWorkflow ? 'Order workflow' : 'Inquiry workflow'); ?></span>
                    </div>
                    <div class="ft-workflow-editor-actions">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow): ?><a href="<?php echo e(route('workflow.edit', $selected->id)); ?>" wire:navigate class="ft-admin-outline">Edit Details</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteWorkflow): ?><button type="button" class="ft-admin-danger" wire:click="requestDeleteWorkflow(<?php echo e($selected->id); ?>)" wire:loading.attr="disabled" wire:target="requestDeleteWorkflow">Delete Workflow</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow && !$selectedIsOrderWorkflow): ?><button type="button" class="ft-admin-primary" wire:click="openPhase">＋ Add Phase</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditWorkflow && !$canDeleteWorkflow): ?><span class="small muted">View only</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedIsOrderWorkflow): ?>
                    <div class="ft-workflow-rule-note">
                        <b>Order workflow runtime · <?php echo e($orderWorkflowReady ? 'Ready' : 'Setup incomplete'); ?></b>
                        <p>The seven Order stages are fixed: New Order, Artwork, Production, QC, Shipment, Billing and Payment. Edit stage colors and map a compatible Task Pack here. Edit the tasks themselves from <a href="<?php echo e(route('task-pack.setup')); ?>" wire:navigate>Task Pack Setup</a>. The existing Order branching and action logic is unchanged.</p>
                    </div>
                <?php else: ?>
                    <div class="ft-workflow-rule-note">
                        <b>Automatic phase controls</b>
                        <p>Active Inquiry phases automatically use the standard start, skip and auto-move settings. Task Pack requirements remain the gate for phase completion.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ft-workflow-table-wrap">
                    <table class="ft-workflow-config-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Phase</th>
                                <th>Task Pack</th>
                                <th>Entry / Exit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selected->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="ft-phase-row-color" style="<?php echo e(\App\Support\MasterColor::style($phase->color)); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'workflow-phase-row-'.e($phase->id).''; ?>wire:key="workflow-phase-row-<?php echo e($phase->id); ?>">
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow && !$selectedIsOrderWorkflow): ?>
                                            <div class="ft-sequence-buttons">
                                                <button type="button" wire:click="move(<?php echo e($phase->id); ?>, -1)" <?php if($loop->first): echo 'disabled'; endif; ?>>↑</button>
                                                <button type="button" wire:click="move(<?php echo e($phase->id); ?>, 1)" <?php if($loop->last): echo 'disabled'; endif; ?>>↓</button>
                                            </div>
                                        <?php else: ?>
                                            <span><?php echo e($phase->sequence); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <b><?php echo e($phase->name); ?></b>
                                        <span>Stage <?php echo e($phase->sequence); ?></span>
                                    </td>
                                    <td><?php echo e($phase->taskPack?->name ?? 'No Task Pack'); ?></td>
                                    <td class="ft-entry-exit"><div><b>In:</b> <?php echo e($phase->entry_condition ?: '—'); ?></div><div><b>Out:</b> <?php echo e($phase->exit_condition ?: '—'); ?></div></td>
                                    <td><span class="ft-auto-pill <?php echo e($phase->is_active ? 'automatic' : ''); ?>"><?php echo e($phase->is_active ? 'Active' : 'Inactive'); ?></span></td>
                                    <td>
                                        <div class="ft-row-action-buttons">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow): ?><button type="button" wire:click="openPhase(<?php echo e($phase->id); ?>)">Edit</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteWorkflow && !$selectedIsOrderWorkflow): ?><button type="button" wire:click="deletePhase(<?php echo e($phase->id); ?>)" wire:confirm="Remove this workflow phase?">Remove</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditWorkflow && !$canDeleteWorkflow): ?><span class="small muted">View only</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="6" class="ft-workflow-empty-row">No phases configured. Add the first phase to this workflow.</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="ft-admin-empty-wide">Create a Workflow to begin.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d3af2beb9da1a1bfeb2af6029c8b146)): ?>
<?php $attributes = $__attributesOriginal0d3af2beb9da1a1bfeb2af6029c8b146; ?>
<?php unset($__attributesOriginal0d3af2beb9da1a1bfeb2af6029c8b146); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d3af2beb9da1a1bfeb2af6029c8b146)): ?>
<?php $component = $__componentOriginal0d3af2beb9da1a1bfeb2af6029c8b146; ?>
<?php unset($__componentOriginal0d3af2beb9da1a1bfeb2af6029c8b146); ?>
<?php endif; ?>
    </div>


    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showWorkflowDeleteModal): ?>
        <?php if (isset($component)) { $__componentOriginal5cb97297f0cc6a73f3cdc7b6541a9888 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5cb97297f0cc6a73f3cdc7b6541a9888 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.safe-delete-modal','data' => ['title' => 'Delete Workflow permanently?','closeAction' => 'closeWorkflowDelete','label' => 'Delete Workflow permanently']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.safe-delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Delete Workflow permanently?','close-action' => 'closeWorkflowDelete','label' => 'Delete Workflow permanently']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="flash error ft-delete-impact-flush">
                    This permanently deletes this reusable Workflow setup. Existing Job snapshots and Job Tasks are not deleted.
                </div>

                <div>
                    <b class="ft-delete-impact-title"><?php echo e($workflowDeleteImpact['name'] ?? 'Workflow'); ?></b>
                    <span class="ft-delete-impact-subtitle">
                        FlowTrack checked Jobs created from this Workflow. Existing Jobs use private snapshots and will not be deleted.
                    </span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($workflowDeleteImpact['replacement_default'])): ?>
                    <div class="flash success ft-delete-impact-flush">
                        This is the current default Workflow. After deletion,
                        <b><?php echo e($workflowDeleteImpact['replacement_default']['name']); ?></b> will become the active default automatically.
                    </div>
                <?php elseif($workflowDeleteImpact['will_leave_no_default'] ?? false): ?>
                    <div class="flash success ft-delete-impact-flush">
                        This is the last Workflow. It can be deleted; the next Workflow you create will become the default automatically.
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!($workflowDeleteImpact['can_delete'] ?? true)): ?>
                    <div class="flash error ft-delete-impact-flush">
                        <?php echo e($workflowDeleteImpact['blocked_reason'] ?? 'This Workflow cannot be deleted.'); ?>

                    </div>
                <?php else: ?>
                    <div class="ft-admin-stats ft-delete-impact-stats">
                        <div><span>Workflow phases</span><b><?php echo e($workflowDeleteImpact['phase_count'] ?? 0); ?></b></div>
                        <div><span>Jobs preserved</span><b><?php echo e($workflowDeleteImpact['job_count'] ?? 0); ?></b></div>
                        <div><span>Tasks preserved</span><b><?php echo e($workflowDeleteImpact['task_count'] ?? 0); ?></b></div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['job_count'] ?? 0) > 0): ?>
                        <div class="ft-delete-impact-box ft-delete-impact-box--danger">
                            <b class="ft-delete-impact-heading ft-delete-impact-heading--danger">Jobs that will remain unchanged</b>
                            <div class="ft-delete-impact-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($workflowDeleteImpact['jobs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="ft-delete-impact-row">
                                        <span class="ft-delete-impact-job"><b><?php echo e($job['job_number']); ?></b> · <?php echo e($job['title']); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job['trashed'] ?? false): ?><small class="ft-delete-impact-muted">Already trashed</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['job_count'] ?? 0) > count($workflowDeleteImpact['jobs'] ?? [])): ?>
                                <small class="ft-delete-impact-more">And <?php echo e(($workflowDeleteImpact['job_count'] ?? 0) - count($workflowDeleteImpact['jobs'] ?? [])); ?> more linked Jobs.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['task_count'] ?? 0) > 0): ?>
                        <div class="ft-delete-impact-box">
                            <b class="ft-delete-impact-heading">Tasks included in those Jobs</b>
                            <div class="ft-delete-impact-list ft-delete-impact-list--compact">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($workflowDeleteImpact['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span class="ft-delete-impact-item"><b ><?php echo e($task['task_number']); ?></b> · <?php echo e($task['title']); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['job_number']): ?> · <?php echo e($task['job_number']); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['task_count'] ?? 0) > count($workflowDeleteImpact['tasks'] ?? [])): ?>
                                <small class="ft-delete-impact-more">And <?php echo e(($workflowDeleteImpact['task_count'] ?? 0) - count($workflowDeleteImpact['tasks'] ?? [])); ?> more Tasks.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <p class="ft-delete-impact-copy">
                        Continuing deletes only the reusable Workflow setup and its setup phases. Any older Job that still points directly to this Workflow is first converted to its own private snapshot. No Job, Task, document, comment, or history record is deleted.
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php $__env->slot('footer', null, []); ?> 
                <button type="button" class="ft-admin-cancel" wire:click="closeWorkflowDelete">Cancel</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowDeleteImpact['can_delete'] ?? false): ?>
                    <button type="button" class="ft-admin-danger" wire:click="confirmDeleteWorkflow" wire:loading.attr="disabled" wire:target="confirmDeleteWorkflow">
                        <span wire:loading.remove wire:target="confirmDeleteWorkflow">Delete Workflow only</span>
                        <span wire:loading wire:target="confirmDeleteWorkflow">Deleting…</span>
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPhaseModal): ?>
        <?php if (isset($component)) { $__componentOriginal352ebc629f6c45c317568cb4ed891a0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal352ebc629f6c45c317568cb4ed891a0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.editor-modal','data' => ['label' => $editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase','closeAction' => 'closePhase']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.editor-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase'),'close-action' => 'closePhase']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-phase-modal-head">
                <h2><?php echo e($selectedIsOrderWorkflow ? 'Edit Order Stage' : ($editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase')); ?></h2>
                <button type="button" wire:click="closePhase">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="ft-phase-two-col">
                    <div class="ft-admin-field">
                        <label>Phase name *</label>
                        <input type="text" wire:model="phaseName" placeholder="New Phase" <?php if($selectedIsOrderWorkflow): echo 'disabled'; endif; ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phaseName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-admin-field">
                        <label>Short label *</label>
                        <input type="text" wire:model="shortName" placeholder="New" <?php if($selectedIsOrderWorkflow): echo 'disabled'; endif; ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shortName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedIsOrderWorkflow): ?><div class="small muted" style="margin:-6px 0 12px">Order stage names and sequence are fixed because the runtime automation keys depend on them.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="ft-admin-field">
                    <label>Phase color *</label>
                    <div class="ft-master-color-picker-row" style="<?php echo e(\App\Support\MasterColor::style($phaseColor)); ?>">
                        <?php if (isset($component)) { $__componentOriginal3606f3fe52333140874051de244bafee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3606f3fe52333140874051de244bafee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.color-picker','data' => ['model' => 'phaseColor','label' => 'Choose workflow phase color']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.color-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'phaseColor','label' => 'Choose workflow phase color']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3606f3fe52333140874051de244bafee)): ?>
<?php $attributes = $__attributesOriginal3606f3fe52333140874051de244bafee; ?>
<?php unset($__attributesOriginal3606f3fe52333140874051de244bafee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3606f3fe52333140874051de244bafee)): ?>
<?php $component = $__componentOriginal3606f3fe52333140874051de244bafee; ?>
<?php unset($__componentOriginal3606f3fe52333140874051de244bafee); ?>
<?php endif; ?>
                        <input type="text" maxlength="7" wire:model.blur="phaseColor" placeholder="#2563EB" aria-label="Workflow phase hex color">
                        <span class="ft-master-color-preview"><i class="ft-master-color-dot"></i><span>This color is used for this phase across FlowTrack.</span></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phaseColor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-admin-field">
                    <label>Task Pack <?php echo e($selectedIsOrderWorkflow ? '*' : ''); ?></label>
                    <select wire:model="taskPackId">
                        <option value=""><?php echo e($selectedIsOrderWorkflow ? 'Select compatible Task Pack' : 'No Task Pack'); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskPack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($taskPack->id); ?>"><?php echo e($taskPack->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedIsOrderWorkflow): ?>
                        <small>Only Task Packs containing this stage's protected Order automation tasks are shown. Change task titles, assignees, durations, documents and extra tasks from Task Pack Setup.</small>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskPackId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-admin-field">
                    <label>Entry rule</label>
                    <input type="text" wire:model="entryCondition" placeholder="Previous phase complete" <?php if($selectedIsOrderWorkflow): echo 'disabled'; endif; ?>>
                </div>
                <div class="ft-admin-field">
                    <label>Exit control</label>
                    <input type="text" wire:model="exitCondition" placeholder="Required work complete" <?php if($selectedIsOrderWorkflow): echo 'disabled'; endif; ?>>
                </div>

                <div class="ft-phase-checks">
                    <label><input type="checkbox" wire:model="phaseActive" <?php if($selectedIsOrderWorkflow): echo 'disabled'; endif; ?>><span>Phase active</span></label>
                </div>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closePhase">Cancel</button>
                <button type="button" class="ft-admin-primary" wire:click="savePhase">Save Phase</button>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal352ebc629f6c45c317568cb4ed891a0c)): ?>
<?php $attributes = $__attributesOriginal352ebc629f6c45c317568cb4ed891a0c; ?>
<?php unset($__attributesOriginal352ebc629f6c45c317568cb4ed891a0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal352ebc629f6c45c317568cb4ed891a0c)): ?>
<?php $component = $__componentOriginal352ebc629f6c45c317568cb4ed891a0c; ?>
<?php unset($__componentOriginal352ebc629f6c45c317568cb4ed891a0c); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/workflow-setup/index.blade.php ENDPATH**/ ?>