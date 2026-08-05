<div class="ft-board-page ft-my-work-page" x-data="{ allGroupsOpen:true, draggedTask:null }">
    <div class="ft-board-sticky-header ft-mywork-sticky-header">
        <div class="ft-board-page-head">
            <div><h1>My Work</h1><p>All visible jobs and tasks across the workspace</p></div>
            <div class="ft-board-head-actions"><a class="ft-new-job-btn" href="<?php echo e(route('jobs.index')); ?>" wire:navigate>Jobs</a></div>
        </div>

        <section class="ft-board-control-card ft-task-controls ft-reference-filter-card">
            <div class="ft-mywork-filter-grid">
                <label class="ft-filter-search ft-mywork-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input wire:model.live.debounce.300ms="search" placeholder="Search jobs, tasks, clients or assignees"></label>
                <select wire:model.live="job"><option value="">Job</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->id); ?>"><?php echo e($row->job_number); ?> — <?php echo e($row->title); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="client"><option value="">Client</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->id); ?>"><?php echo e($row->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="assignee"><option value="">Assignee</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->id); ?>"><?php echo e($row->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="status"><option value="">Status</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($value); ?>"><?php echo e($value); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="priority"><option value="">Priority</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row->name); ?>"><?php echo e($row->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="due"><option value="">Due date</option><option value="overdue">Overdue</option><option value="today">Due today</option><option value="week">Due this week</option><option value="month">Next 30 days</option><option value="none">No due date</option></select>
                <button type="button" class="ft-clear-wide" wire:click="clearFilters">Clear</button>
            </div>
            <div class="ft-board-quick-row">
                <span class="ft-quick-label">Quick filters</span>
                <button class="ft-quick-chip <?php echo e($quick==='open'?'active':''); ?>" wire:click="setQuick('open')">Open <b><?php echo e($counts['open']); ?></b></button>
                <button class="ft-quick-chip red <?php echo e($quick==='overdue'?'active':''); ?>" wire:click="setQuick('overdue')">Overdue <b><?php echo e($counts['overdue']); ?></b></button>
                <button class="ft-quick-chip <?php echo e($quick==='week'?'active':''); ?>" wire:click="setQuick('week')">Due this week <b><?php echo e($counts['week']); ?></b></button>
                <button class="ft-quick-chip red <?php echo e($quick==='blocked'?'active':''); ?>" wire:click="setQuick('blocked')">Blocked <b><?php echo e($counts['blocked']); ?></b></button>
                <button class="ft-quick-chip <?php echo e($quick==='waiting'?'active':''); ?>" wire:click="setQuick('waiting')">Waiting external <b><?php echo e($counts['waiting']); ?></b></button>
                <button class="ft-quick-chip <?php echo e($quick==='completed'?'active':''); ?>" wire:click="setQuick('completed')">Completed <b><?php echo e($counts['completed']); ?></b></button>
                <span class="ft-board-group-controls" aria-label="Job group controls">
                    <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=true; $dispatch('board-expand-all')" title="Expand all jobs" aria-label="Expand all jobs"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button>
                    <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=false; $dispatch('board-collapse-all')" title="Collapse all jobs" aria-label="Collapse all jobs"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button>
                </span>
            </div>
        </section>
    </div>

    <?php ($displayStatuses = $taskStatuses->filter(fn($value) => $quick === 'completed' ? \App\Support\BoardLaneResolver::isCompleted($value) : ! \App\Support\BoardLaneResolver::isCompleted($value))->values()); ?>
    <div class="ft-lane-sticky-header">
        <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="myWorkHeaderScroll" x-on:scroll="$refs.myWorkBodyScroll && ($refs.myWorkBodyScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-task-board-status-header" style="--ft-lane-count: <?php echo e(max(1, $displayStatuses->count())); ?>;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $displayStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-task-status-head"><span><?php echo e(strtoupper($workStatus)); ?></span><b><?php echo e($tasks->filter(fn($task) => \App\Support\BoardLaneResolver::taskStatusMatches($task->status, $workStatus))->count()); ?></b></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll" x-ref="myWorkBodyScroll" x-on:scroll="$refs.myWorkHeaderScroll && ($refs.myWorkHeaderScroll.scrollLeft = $event.target.scrollLeft)">
        <?php if (isset($component)) { $__componentOriginal67670d28261a498be033858bd5d8e998 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67670d28261a498be033858bd5d8e998 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.task-job-matrix','data' => ['tasks' => $tasks,'statuses' => $displayStatuses,'draggable' => true,'keyPrefix' => 'mywork']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.task-job-matrix'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tasks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tasks),'statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayStatuses),'draggable' => true,'key-prefix' => 'mywork']); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreCards): ?>
        <div class="ft-board-load-more">
            <button type="button" class="ft-outline-btn" wire:click="loadMore">Load 60 more</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/my-work/index.blade.php ENDPATH**/ ?>