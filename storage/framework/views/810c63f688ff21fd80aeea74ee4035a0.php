<div class="ft-admin-reference ft-workflow-reference">
    <div class="ft-admin-page-head">
        <div>
            <h1>Workflow Setup</h1>
            <p>Define phase sequence, allowed starting stages, Task Packs, documents and skip rules</p>
        </div>
        <div class="ft-admin-head-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
                <a href="<?php echo e(route('workflow.create', ['source' => $selected->id])); ?>" wire:navigate class="ft-admin-outline">Duplicate</a>
            <?php else: ?>
                <span class="ft-admin-outline is-disabled">Duplicate</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('workflow.create')); ?>" wire:navigate class="ft-admin-primary">＋ New Workflow</a>
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
                        <a href="<?php echo e(route('workflow.edit', $selected->id)); ?>" wire:navigate class="ft-admin-outline">Edit Details</a>
                        <button type="button" class="ft-admin-danger" wire:click="deleteWorkflow(<?php echo e($selected->id); ?>)" wire:confirm="Delete this Workflow? Workflows already used by Jobs cannot be deleted and must be deactivated instead.">Delete Workflow</button>
                        <button type="button" class="ft-admin-primary" wire:click="openPhase">＋ Add Phase</button>
                    </div>
                </div>

                <div class="ft-workflow-rule-note">
                    <b>Starting-stage rule</b>
                    <p>Only phases marked “Allow Job Start” appear in the New Job form. Earlier phases are recorded as skipped, completed outside the system, or migrated.</p>
                </div>

                <div class="ft-workflow-table-wrap">
                    <table class="ft-workflow-config-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Phase</th>
                                <th>Task Pack</th>
                                <th>Allow Job<br>Start</th>
                                <th>Can<br>Skip</th>
                                <th>Auto Move</th>
                                <th>Required<br>Document</th>
                                <th>Entry / Exit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selected->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'workflow-phase-row-'.e($phase->id).''; ?>wire:key="workflow-phase-row-<?php echo e($phase->id); ?>">
                                    <td>
                                        <div class="ft-sequence-buttons">
                                            <button type="button" wire:click="move(<?php echo e($phase->id); ?>, -1)" <?php if($loop->first): echo 'disabled'; endif; ?>>↑</button>
                                            <button type="button" wire:click="move(<?php echo e($phase->id); ?>, 1)" <?php if($loop->last): echo 'disabled'; endif; ?>>↓</button>
                                        </div>
                                    </td>
                                    <td>
                                        <b><?php echo e($phase->name); ?></b>
                                        <span>Stage <?php echo e($phase->sequence); ?></span>
                                    </td>
                                    <td><?php echo e($phase->taskPack?->name ?? 'No Task Pack'); ?></td>
                                    <td>
                                        <label class="ft-table-check"><input type="checkbox" <?php if($phase->allow_job_start): echo 'checked'; endif; ?> disabled><span><?php echo e($phase->allow_job_start ? 'Allowed' : 'Not allowed'); ?></span></label>
                                    </td>
                                    <td>
                                        <label class="ft-table-check"><input type="checkbox" <?php if($phase->is_skippable): echo 'checked'; endif; ?> disabled><span><?php echo e($phase->is_skippable ? 'Yes' : 'No'); ?></span></label>
                                    </td>
                                    <td><span class="ft-auto-pill <?php echo e($phase->auto_advance_on_ready ? 'automatic' : ''); ?>"><?php echo e($phase->auto_advance_on_ready ? 'Automatic' : 'Manual'); ?></span></td>
                                    <td><?php echo e($phase->documentCategory?->name ?? '—'); ?></td>
                                    <td class="ft-entry-exit"><div><b>In:</b> <?php echo e($phase->entry_condition ?: '—'); ?></div><div><b>Out:</b> <?php echo e($phase->exit_condition ?: '—'); ?></div></td>
                                    <td>
                                        <div class="ft-row-action-buttons">
                                            <button type="button" wire:click="openPhase(<?php echo e($phase->id); ?>)">Edit</button>
                                            <button type="button" wire:click="deletePhase(<?php echo e($phase->id); ?>)" wire:confirm="Remove this workflow phase?">Remove</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="9" class="ft-workflow-empty-row">No phases configured. Add the first phase to this workflow.</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="ft-admin-empty-wide">Create a Workflow to begin.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    </div>

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
                    <div class="ft-admin-field">
                        <label>Task Pack</label>
                        <select wire:model="taskPackId">
                            <option value="">No Task Pack</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskPack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($taskPack->id); ?>"><?php echo e($taskPack->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    <div class="ft-admin-field">
                        <label>Required document</label>
                        <select wire:model="documentCategoryId">
                            <option value="">None</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
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
                    <label><input type="checkbox" wire:model="allowJobStart"><span>Allow users to create a Job starting from this phase</span></label>
                    <label><input type="checkbox" wire:model="isSkippable"><span>Allow this phase to be skipped during normal progression</span></label>
                    <label><input type="checkbox" wire:model="autoAdvanceOnReady"><span>Automatically move the Job when all task, document and blocker gates are ready</span></label>
                    <label><input type="checkbox" wire:model="phaseActive"><span>Phase active</span></label>
                </div>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closePhase">Cancel</button>
                <button type="button" class="ft-admin-primary" wire:click="savePhase" wire:loading.attr="disabled">Save Phase</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/workflow-setup/index.blade.php ENDPATH**/ ?>