<div class="ft-board-page ft-operations-board" x-data="{ draggedTask:null, draggedJob:null, allGroupsOpen:true, phaseClosed:{} }">
    <div class="ft-board-sticky-header">
        <div class="ft-board-page-head">
            <div><h1>Operations Board</h1><p>Track work across all active Jobs</p></div>
            <div class="ft-board-head-actions"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','create')): ?><a class="ft-new-job-btn" href="<?php echo e(route('jobs.index', ['create'=>1])); ?>" wire:navigate>＋ New Job</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?><div class="flash"><?php echo e($message); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-board-tabs ft-reference-tabs">
            <button type="button" class="<?php echo e($mode === 'jobs' ? 'active' : ''); ?>" wire:click="setMode('jobs')">Job Board</button>
            <button type="button" class="<?php echo e($mode === 'tasks' ? 'active' : ''); ?>" wire:click="setMode('tasks')">Task Board</button>
        </div>

        <section class="ft-board-control-card ft-reference-filter-card">
            <div class="ft-board-reference-filter-grid">
                <label class="ft-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input wire:model.live.debounce.300ms="search" placeholder="Search job ID, task or client"></label>
                <select wire:model.live="job"><option value="">Job</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->id); ?>"><?php echo e($row->job_number); ?> — <?php echo e($row->title); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="client"><option value="">Client</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->id); ?>"><?php echo e($row->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="assignee"><option value="">Assignee</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->id); ?>"><?php echo e($row->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
                    <select wire:model.live="status"><option value="">Status</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($value); ?>"><?php echo e($value); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <?php else: ?>
                    <select wire:model.live="status"><option value="">Status</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($value); ?>"><?php echo e($value); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <select wire:model.live="due"><option value="">Due date</option><option value="overdue">Overdue</option><option value="today">Due today</option><option value="week">Due this week</option><option value="month">Next 30 days</option><option value="none">No due date</option></select>
                <button type="button" class="ft-clear-wide" wire:click="clearFilters">Clear</button>
            </div>

            <div class="ft-board-quick-row">
                <span class="ft-quick-label">Quick filters</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
                    <button class="ft-quick-chip <?php echo e($quick==='mine'?'active':''); ?>" wire:click="setQuick('mine')">My job <b><?php echo e($jobCounts['mine']); ?></b></button>
                    <button class="ft-quick-chip red <?php echo e($quick==='overdue'?'active':''); ?>" wire:click="setQuick('overdue')">Overdue <b><?php echo e($jobCounts['overdue']); ?></b></button>
                    <button class="ft-quick-chip <?php echo e($quick==='week'?'active':''); ?>" wire:click="setQuick('week')">Due this week <b><?php echo e($jobCounts['week']); ?></b></button>
                    <button class="ft-quick-chip red <?php echo e($quick==='blocked'?'active':''); ?>" wire:click="setQuick('blocked')">Blocked <b><?php echo e($jobCounts['blocked']); ?></b></button>
                    <button class="ft-quick-chip <?php echo e($quick==='waiting'?'active':''); ?>" wire:click="setQuick('waiting')">Waiting external <b><?php echo e($jobCounts['waiting']); ?></b></button>
                    <button class="ft-quick-chip amber <?php echo e($quick==='unassigned'?'active':''); ?>" wire:click="setQuick('unassigned')">Unassigned <b><?php echo e($jobCounts['unassigned']); ?></b></button>
                    <span class="ft-board-group-controls" aria-label="Job group controls">
                        <button type="button" class="ft-filter-collapse" wire:click="expandAll" title="Expand all job cards" aria-label="Expand all job cards"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button>
                        <button type="button" class="ft-filter-collapse" wire:click="collapseAll" title="Collapse all job cards" aria-label="Collapse all job cards"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button>
                    </span>
                <?php else: ?>
                    <button class="ft-quick-chip <?php echo e($quick==='mine'?'active':''); ?>" wire:click="setQuick('mine')">My task <b><?php echo e($taskCounts['mine']); ?></b></button>
                    <button class="ft-quick-chip red <?php echo e($quick==='overdue'?'active':''); ?>" wire:click="setQuick('overdue')">Overdue <b><?php echo e($taskCounts['overdue']); ?></b></button>
                    <button class="ft-quick-chip <?php echo e($quick==='week'?'active':''); ?>" wire:click="setQuick('week')">Due this week <b><?php echo e($taskCounts['week']); ?></b></button>
                    <button class="ft-quick-chip red <?php echo e($quick==='blocked'?'active':''); ?>" wire:click="setQuick('blocked')">Blocked <b><?php echo e($taskCounts['blocked']); ?></b></button>
                    <button class="ft-quick-chip <?php echo e($quick==='waiting'?'active':''); ?>" wire:click="setQuick('waiting')">Waiting external <b><?php echo e($taskCounts['waiting']); ?></b></button>
                    <button class="ft-quick-chip amber <?php echo e($quick==='unassigned'?'active':''); ?>" wire:click="setQuick('unassigned')">Unassigned <b><?php echo e($taskCounts['unassigned']); ?></b></button>
                    <span class="ft-board-group-controls" aria-label="Task job group controls">
                        <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=true; $dispatch('board-expand-all')" title="Expand all jobs" aria-label="Expand all jobs"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button>
                        <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=false; $dispatch('board-collapse-all')" title="Collapse all jobs" aria-label="Collapse all jobs"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <div class="ft-board-summary-row ft-reference-summary">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
                <span>Showing <b><?php echo e($jobs->count()); ?></b> Jobs across <b><?php echo e($jobs->pluck('workflow_id')->unique()->count()); ?></b> <?php echo e(\Illuminate\Support\Str::plural('workflow', $jobs->pluck('workflow_id')->unique()->count())); ?></span>
            <?php else: ?>
                <span>Showing <b><?php echo e($tasks->count()); ?></b> of <b><?php echo e($taskCounts['open'] + $taskCounts['completed']); ?></b> tasks across <b><?php echo e($tasks->pluck('flow_job_id')->unique()->count()); ?></b> <?php echo e(\Illuminate\Support\Str::plural('job', $tasks->pluck('flow_job_id')->unique()->count())); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
        <section class="ft-workflow-reference-card">
            <label>Workflow</label>
            <select wire:model.live="workflow"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($flow->id); ?>"><?php echo e($flow->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <p>Job cards show current phase progress, next action and expandable phase tasks.</p>
        </section>

        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="jobHeaderScroll" x-on:scroll="$refs.jobBodyScroll && ($refs.jobBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-job-board-header-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php ($phaseJobs = $jobs->where('workflow_phase_id', $phase->id)); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hideEmptyPhases && $phaseJobs->isEmpty()): ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button type="button" class="ft-board-column-head ft-external-lane-head" x-on:click="phaseClosed[<?php echo e($phase->id); ?>]=!phaseClosed[<?php echo e($phase->id); ?>]">
                            <span><?php echo e(strtoupper($phase->short_name)); ?></span><b><?php echo e($phaseJobs->count()); ?></b>
                            <svg :class="{'rotated':phaseClosed[<?php echo e($phase->id); ?>]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll" x-ref="jobBodyScroll" x-on:scroll="$refs.jobHeaderScroll && ($refs.jobHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-job-board-grid ft-job-board-body-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php ($phaseJobs = $jobs->where('workflow_phase_id', $phase->id)); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hideEmptyPhases && $phaseJobs->isEmpty()): ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <section class="ft-board-column ft-job-column ft-board-column-nohead" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-phase-'.e($phase->id).''; ?>wire:key="job-phase-<?php echo e($phase->id); ?>">
                        <button type="button" class="ft-mobile-phase-head" x-on:click="phaseClosed[<?php echo e($phase->id); ?>]=!phaseClosed[<?php echo e($phase->id); ?>]">
                            <span><?php echo e($phase->short_name); ?></span><b><?php echo e($phaseJobs->count()); ?></b>
                            <svg :class="{'rotated':phaseClosed[<?php echo e($phase->id); ?>]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="ft-board-column-list" x-show="!phaseClosed[<?php echo e($phase->id); ?>]" x-on:dragover.prevent x-on:drop.prevent="if(draggedJob){$wire.moveJob(draggedJob,<?php echo e($phase->id); ?>);draggedJob=null}">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php ($canMoveJob = app(\App\Services\AccessControlService::class)->canChangeJobStatus(auth()->user(), $jobRow)); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canMoveJob): ?>
                                    <?php if (isset($component)) { $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.job-card','data' => ['job' => $jobRow,'expanded' => in_array($jobRow->id,$expandedJobs,true),'draggable' => 'true','xOn:dragstart' => 'draggedJob='.e($jobRow->id).'','xOn:dragend' => 'draggedJob=null','wire:key' => 'job-card-'.e($jobRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.job-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobRow),'expanded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array($jobRow->id,$expandedJobs,true)),'draggable' => 'true','x-on:dragstart' => 'draggedJob='.e($jobRow->id).'','x-on:dragend' => 'draggedJob=null','wire:key' => 'job-card-'.e($jobRow->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $attributes = $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $component = $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <?php if (isset($component)) { $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.job-card','data' => ['job' => $jobRow,'expanded' => in_array($jobRow->id,$expandedJobs,true),'draggable' => 'false','wire:key' => 'job-card-'.e($jobRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.job-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobRow),'expanded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array($jobRow->id,$expandedJobs,true)),'draggable' => 'false','wire:key' => 'job-card-'.e($jobRow->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $attributes = $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $component = $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div class="ft-board-empty-column">No Jobs</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="taskHeaderScroll" x-on:scroll="$refs.taskBodyScroll && ($refs.taskBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-task-board-status-header" style="--ft-lane-count: <?php echo e(max(1, $taskStatuses->count())); ?>;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-task-status-head"><span><?php echo e(strtoupper($taskStatus)); ?></span><b><?php echo e($tasks->filter(fn($task) => \App\Support\BoardLaneResolver::taskStatusMatches($task->status, $taskStatus))->count()); ?></b></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll" x-ref="taskBodyScroll" x-on:scroll="$refs.taskHeaderScroll && ($refs.taskHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <?php if (isset($component)) { $__componentOriginal67670d28261a498be033858bd5d8e998 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67670d28261a498be033858bd5d8e998 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.task-job-matrix','data' => ['tasks' => $tasks,'statuses' => $taskStatuses,'draggable' => true,'keyPrefix' => 'board']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.task-job-matrix'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tasks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tasks),'statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'draggable' => true,'key-prefix' => 'board']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67670d28261a498be033858bd5d8e998)): ?>
<?php $attributes = $__attributesOriginal67670d28261a498be033858bd5d8e998; ?>
<?php unset($__attributesOriginal67670d28261a498be033858bd5d8e998); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67670d28261a498be033858bd5d8e998)): ?>
<?php $component = $__componentOriginal67670d28261a498be033858bd5d8e998; ?>
<?php unset($__componentOriginal67670d28261a498be033858bd5d8e998); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreCards): ?>
        <div class="ft-board-load-more">
            <button type="button" class="ft-outline-btn" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore">
                <span wire:loading.remove wire:target="loadMore">Load 60 more</span>
                <span wire:loading wire:target="loadMore">Loading…</span>
            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/board/index.blade.php ENDPATH**/ ?>