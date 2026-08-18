<?php
    $canCreateWorkflow = auth()->user()->canModule('workflow', 'create');
    $canEditWorkflow = auth()->user()->canModule('workflow', 'edit');
    $canDeleteWorkflow = auth()->user()->canModule('workflow', 'delete');
?>
<div class="ft-admin-reference ft-workflow-reference">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$showWorkflowDeleteModal): ?>
    <div class="ft-admin-page-head">
        <div>
            <h1>Workflow Setup</h1>
            <p>Define phase sequence, Task Packs and the rules that control each stage</p>
        </div>
        <div class="ft-admin-head-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateWorkflow && $selected): ?>
                <a href="<?php echo e(route('workflow.create', ['source' => $selected->id])); ?>" wire:navigate class="ft-admin-outline">Duplicate</a>
            <?php elseif($canCreateWorkflow): ?>
                <span class="ft-admin-outline is-disabled">Duplicate</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateWorkflow): ?><a href="<?php echo e(route('workflow.create')); ?>" wire:navigate class="ft-admin-primary">＋ New Workflow</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

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
        <aside class="ft-workflow-template-list">
            <div class="ft-workflow-list-label">WORKFLOW TEMPLATES</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button type="button" class="<?php echo e($workflow->id === $selectedWorkflowId ? 'active' : ''); ?>" wire:click="selectWorkflow(<?php echo e($workflow->id); ?>)">
                    <b><?php echo e($workflow->name); ?></b>
                    <span><?php echo e($workflow->phases->count()); ?> phases · <?php echo e($workflow->is_active ? 'Active' : 'Inactive'); ?></span>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-workflow-list-empty">No workflows configured.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </aside>

        <section class="ft-workflow-editor-card">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
                <div class="ft-workflow-editor-head">
                    <div>
                        <h2><?php echo e($selected->name); ?></h2>
                        <p><?php echo e($selected->description ?: 'No description'); ?></p>
                    </div>
                    <div class="ft-workflow-editor-actions">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow): ?><a href="<?php echo e(route('workflow.edit', $selected->id)); ?>" wire:navigate class="ft-admin-outline">Edit Details</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteWorkflow): ?><button type="button" class="ft-admin-danger" wire:click="requestDeleteWorkflow(<?php echo e($selected->id); ?>)" wire:loading.attr="disabled" wire:target="requestDeleteWorkflow">Delete Workflow</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow): ?><button type="button" class="ft-admin-primary" wire:click="openPhase">＋ Add Phase</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditWorkflow && !$canDeleteWorkflow): ?><span class="small muted">View only</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="ft-workflow-rule-note">
                    <b>Automatic phase controls</b>
                    <p>Active phases automatically use the standard Job-start, skip and auto-move settings. Task Pack requirements remain the gate for phase completion.</p>
                </div>

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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditWorkflow): ?>
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
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteWorkflow): ?><button type="button" wire:click="deletePhase(<?php echo e($phase->id); ?>)" wire:confirm="Remove this workflow phase?">Remove</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
        </section>
    </div>


    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showWorkflowDeleteModal): ?>
        <div class="ft-reference-overlay" wire:click.self="closeWorkflowDelete"></div>
        <div class="ft-phase-reference-modal" role="alertdialog" aria-modal="true" aria-label="Delete Workflow permanently" style="width:min(720px,calc(100vw - 32px))">
            <div class="ft-phase-modal-head">
                <h2>Delete Workflow permanently?</h2>
                <button type="button" wire:click="closeWorkflowDelete">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="flash error" style="margin:0">
                    This permanently deletes this reusable Workflow setup. Existing Job snapshots and Job Tasks are not deleted.
                </div>

                <div>
                    <b style="display:block;font-size:15px;color:#15263e"><?php echo e($workflowDeleteImpact['name'] ?? 'Workflow'); ?></b>
                    <span style="display:block;margin-top:4px;color:#61748e;font-size:11px">
                        FlowTrack checked Jobs created from this Workflow. Existing Jobs use private snapshots and will not be deleted.
                    </span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($workflowDeleteImpact['replacement_default'])): ?>
                    <div class="flash success" style="margin:0">
                        This is the current default Workflow. After deletion,
                        <b><?php echo e($workflowDeleteImpact['replacement_default']['name']); ?></b> will become the active default automatically.
                    </div>
                <?php elseif($workflowDeleteImpact['will_leave_no_default'] ?? false): ?>
                    <div class="flash success" style="margin:0">
                        This is the last Workflow. It can be deleted; the next Workflow you create will become the default automatically.
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!($workflowDeleteImpact['can_delete'] ?? true)): ?>
                    <div class="flash error" style="margin:0">
                        <?php echo e($workflowDeleteImpact['blocked_reason'] ?? 'This Workflow cannot be deleted.'); ?>

                    </div>
                <?php else: ?>
                    <div class="ft-admin-stats" style="margin:0">
                        <div><span>Workflow phases</span><b><?php echo e($workflowDeleteImpact['phase_count'] ?? 0); ?></b></div>
                        <div><span>Jobs preserved</span><b><?php echo e($workflowDeleteImpact['job_count'] ?? 0); ?></b></div>
                        <div><span>Tasks preserved</span><b><?php echo e($workflowDeleteImpact['task_count'] ?? 0); ?></b></div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['job_count'] ?? 0) > 0): ?>
                        <div style="border:1px solid #f0d2cf;background:#fffafa;border-radius:10px;padding:12px">
                            <b style="display:block;font-size:12px;color:#a72822;margin-bottom:8px">Jobs that will remain unchanged</b>
                            <div style="display:grid;gap:7px;max-height:190px;overflow:auto">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($workflowDeleteImpact['jobs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div style="display:flex;justify-content:space-between;gap:12px;border-bottom:1px solid #f2e4e2;padding-bottom:6px">
                                        <span style="font-size:11px"><b><?php echo e($job['job_number']); ?></b> · <?php echo e($job['title']); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job['trashed'] ?? false): ?><small style="color:#8a6a67">Already trashed</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['job_count'] ?? 0) > count($workflowDeleteImpact['jobs'] ?? [])): ?>
                                <small style="display:block;margin-top:8px;color:#6c7d92">And <?php echo e(($workflowDeleteImpact['job_count'] ?? 0) - count($workflowDeleteImpact['jobs'] ?? [])); ?> more linked Jobs.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['task_count'] ?? 0) > 0): ?>
                        <div style="border:1px solid #e1e7ef;border-radius:10px;padding:12px">
                            <b style="display:block;font-size:12px;color:#263b58;margin-bottom:8px">Tasks included in those Jobs</b>
                            <div style="display:grid;gap:6px">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($workflowDeleteImpact['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span style="font-size:10.5px;color:#526780"><b style="color:#24364f"><?php echo e($task['task_number']); ?></b> · <?php echo e($task['title']); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['job_number']): ?> · <?php echo e($task['job_number']); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowDeleteImpact['task_count'] ?? 0) > count($workflowDeleteImpact['tasks'] ?? [])): ?>
                                <small style="display:block;margin-top:8px;color:#6c7d92">And <?php echo e(($workflowDeleteImpact['task_count'] ?? 0) - count($workflowDeleteImpact['tasks'] ?? [])); ?> more Tasks.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <p style="margin:0;color:#526780;font-size:11px;line-height:1.5">
                        Continuing deletes only the reusable Workflow setup and its setup phases. Any older Job that still points directly to this Workflow is first converted to its own private snapshot. No Job, Task, document, comment, or history record is deleted.
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closeWorkflowDelete">Cancel</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowDeleteImpact['can_delete'] ?? false): ?>
                    <button type="button" class="ft-admin-danger" wire:click="confirmDeleteWorkflow" wire:loading.attr="disabled" wire:target="confirmDeleteWorkflow">
                        <span wire:loading.remove wire:target="confirmDeleteWorkflow">Delete Workflow only</span>
                        <span wire:loading wire:target="confirmDeleteWorkflow">Deleting…</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPhaseModal): ?>
        <div class="ft-reference-overlay" wire:click.self="closePhase"></div>
        <div class="ft-phase-reference-modal" role="dialog" aria-modal="true" aria-label="<?php echo e($editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase'); ?>">
            <div class="ft-phase-modal-head">
                <h2><?php echo e($editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase'); ?></h2>
                <button type="button" wire:click="closePhase">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="ft-phase-two-col">
                    <div class="ft-admin-field">
                        <label>Phase name *</label>
                        <input type="text" wire:model="phaseName" placeholder="New Phase">
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
                        <input type="text" wire:model="shortName" placeholder="New">
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
                <div class="ft-admin-field">
                    <label>Phase color *</label>
                    <div class="ft-master-color-picker-row" style="<?php echo e(\App\Support\MasterColor::style($phaseColor)); ?>">
                        <input class="ft-master-color-picker" type="color" wire:model.live="phaseColor" aria-label="Choose workflow phase color">
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
                    <label>Task Pack</label>
                    <select wire:model="taskPackId">
                        <option value="">No Task Pack</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskPack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($taskPack->id); ?>"><?php echo e($taskPack->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="ft-admin-field">
                    <label>Entry rule</label>
                    <input type="text" wire:model="entryCondition" placeholder="Previous phase complete">
                </div>
                <div class="ft-admin-field">
                    <label>Exit control</label>
                    <input type="text" wire:model="exitCondition" placeholder="Required work complete">
                </div>

                <div class="ft-phase-checks">
                    <label><input type="checkbox" wire:model="phaseActive"><span>Phase active</span></label>
                </div>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closePhase">Cancel</button>
                <button type="button" class="ft-admin-primary" wire:click="savePhase">Save Phase</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/workflow-setup/index.blade.php ENDPATH**/ ?>