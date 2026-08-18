<?php
    $masterData = app(\App\Services\MasterDataService::class);
    $canCreateTaskPack = auth()->user()->canModule('taskpacks', 'create');
    $canEditTaskPack = auth()->user()->canModule('taskpacks', 'edit');
    $canDeleteTaskPack = auth()->user()->canModule('taskpacks', 'delete');
?>
<div wire:init="loadTaskPacks" class="ft-admin-reference ft-taskpack-reference">
    <div class="ft-admin-page-head">
        <div>
            <h1>Task Pack Setup</h1>
            <p>Create reusable task sequences that activate when a Job enters a workflow phase</p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateTaskPack): ?><a href="<?php echo e(route('task-pack.create')); ?>" wire:navigate class="ft-admin-primary">＋ Add Task Pack</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

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
    <div class="ft-taskpack-grid">
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
                        <div class="ft-taskpack-item-row">
                            <div>
                                <b><?php echo e($loop->iteration); ?>. <?php echo e($item->title); ?></b>
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
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPackDeleteModal): ?>
        <div class="ft-reference-overlay" wire:click.self="closePackDelete"></div>
        <div class="ft-phase-reference-modal" role="alertdialog" aria-modal="true" aria-label="Delete Task Pack permanently" style="width:min(720px,calc(100vw - 32px))">
            <div class="ft-phase-modal-head">
                <h2>Delete Task Pack permanently?</h2>
                <button type="button" wire:click="closePackDelete">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="flash error" style="margin:0">
                    This permanently deletes this reusable Task Pack setup. Existing Job snapshots and Job Tasks are not deleted.
                </div>

                <div>
                    <b style="display:block;font-size:15px;color:#15263e"><?php echo e($packDeleteImpact['name'] ?? 'Task Pack'); ?></b>
                    <span style="display:block;margin-top:4px;color:#61748e;font-size:11px">
                        FlowTrack checked Workflow mappings and Jobs that originated from those Workflows before allowing deletion.
                    </span>
                </div>

                <div class="ft-admin-stats" style="margin:0">
                    <div><span>Mapped phases</span><b><?php echo e($packDeleteImpact['mapped_phase_count'] ?? 0); ?></b></div>
                    <div><span>Jobs preserved</span><b><?php echo e($packDeleteImpact['job_count'] ?? 0); ?></b></div>
                    <div><span>Tasks preserved</span><b><?php echo e($packDeleteImpact['task_count'] ?? 0); ?></b></div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['mapped_phase_count'] ?? 0) > 0): ?>
                    <div style="border:1px solid #d9e4f2;background:#f8fbff;border-radius:10px;padding:12px">
                        <b style="display:block;font-size:12px;color:#263b58;margin-bottom:8px">Workflow phases using this Task Pack</b>
                        <div style="display:grid;gap:6px">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($packDeleteImpact['mapped_phases'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <span style="font-size:10.5px;color:#526780"><b style="color:#24364f"><?php echo e($phase['workflow_name']); ?></b> · Stage <?php echo e($phase['sequence']); ?> · <span class="ft-phase-color-label" style="<?php echo e(\App\Support\MasterColor::style($phase['color'] ?? null)); ?>"><?php echo e($phase['name']); ?></span></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['mapped_phase_count'] ?? 0) > count($packDeleteImpact['mapped_phases'] ?? [])): ?>
                            <small style="display:block;margin-top:8px;color:#6c7d92">And <?php echo e(($packDeleteImpact['mapped_phase_count'] ?? 0) - count($packDeleteImpact['mapped_phases'] ?? [])); ?> more mapped phases.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small style="display:block;margin-top:9px;color:#526780">These Workflow phases will remain, but their Task Pack assignment will be removed.</small>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['job_count'] ?? 0) > 0): ?>
                    <div style="border:1px solid #f0d2cf;background:#fffafa;border-radius:10px;padding:12px">
                        <b style="display:block;font-size:12px;color:#a72822;margin-bottom:8px">Jobs that remain independent of this Task Pack</b>
                        <div style="display:grid;gap:7px;max-height:190px;overflow:auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($packDeleteImpact['jobs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div style="display:flex;justify-content:space-between;gap:12px;border-bottom:1px solid #f2e4e2;padding-bottom:6px">
                                    <span style="font-size:11px"><b><?php echo e($job['job_number']); ?></b> · <?php echo e($job['title']); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job['trashed'] ?? false): ?><small style="color:#8a6a67">Already trashed</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($packDeleteImpact['job_count'] ?? 0) > count($packDeleteImpact['jobs'] ?? [])): ?>
                            <small style="display:block;margin-top:8px;color:#6c7d92">And <?php echo e(($packDeleteImpact['job_count'] ?? 0) - count($packDeleteImpact['jobs'] ?? [])); ?> more linked Jobs.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <p style="margin:0;color:#526780;font-size:11px;line-height:1.5">
                    Deleting this reusable Task Pack does not delete existing Job Tasks. Older Jobs are snapshotted first when needed, and each Job keeps its own copied phase/task definitions.
                </p>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closePackDelete">Cancel</button>
                <button type="button" class="ft-admin-danger" wire:click="confirmDeletePack" wire:loading.attr="disabled" wire:target="confirmDeletePack">
                    <span wire:loading.remove wire:target="confirmDeletePack">Delete Task Pack only</span>
                    <span wire:loading wire:target="confirmDeletePack">Deleting…</span>
                </button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/task-pack-setup/index.blade.php ENDPATH**/ ?>