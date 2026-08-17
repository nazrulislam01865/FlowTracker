<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tasks', 'statuses', 'draggable' => false, 'keyPrefix' => 'task-matrix', 'allGroupsExpanded' => true, 'groupStateKey' => 'default']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['tasks', 'statuses', 'draggable' => false, 'keyPrefix' => 'task-matrix', 'allGroupsExpanded' => true, 'groupStateKey' => 'default']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $matrixTasks = collect($tasks);
    $laneStatuses = collect($statuses)->values();
    $groupedJobs = $matrixTasks->groupBy('flow_job_id');
?>

<div class="ft-task-job-matrix" style="--ft-lane-count: <?php echo e(max(1, $laneStatuses->count())); ?>;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groupedJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobId => $jobTasks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            $job = $jobTasks->first()?->job;
            $resolvedJobId = $job?->id ?? ('unassigned-'.$loop->index);
        ?>
        <section
            class="ft-task-job-matrix-group"
            x-data="{ open: <?php echo e($allGroupsExpanded ? 'true' : 'false'); ?> }"
            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($keyPrefix).'-job-'.e($resolvedJobId).'-'.e($groupStateKey).''; ?>wire:key="<?php echo e($keyPrefix); ?>-job-<?php echo e($resolvedJobId); ?>-<?php echo e($groupStateKey); ?>"
        >
            <div class="ft-task-job-row-head">
                <button type="button" class="ft-task-job-row-toggle" x-on:click="open = !open" :title="open ? 'Collapse order tasks' : 'Expand order tasks'" :aria-expanded="open.toString()">
                    <svg :class="{'rotated': !open}" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job): ?>
                    <a class="ft-task-job-row-number" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                    <span class="ft-task-job-copy" aria-hidden="true">▣</span>
                    <span class="ft-task-job-dot">·</span>
                    <a class="ft-task-job-row-title" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->title); ?></a>
                    <span class="ft-task-job-client-pill"><?php echo e($job->client?->name ?? 'No client'); ?></span>
                <?php else: ?>
                    <span class="ft-task-job-row-number">No order</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="ft-task-job-row-total"><?php echo e($jobTasks->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('task', $jobTasks->count())); ?></span>
            </div>

            <div class="ft-task-job-row-grid" x-show="open">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $laneStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $laneStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $laneTasks = $jobTasks->filter(fn($task) => \App\Support\BoardLaneResolver::taskStatusMatches($task->status, $laneStatus));
                        $laneColor = app(\App\Services\MasterDataService::class)->colorFor('order_task_status', (string) $laneStatus);
                    ?>
                    <div
                        class="ft-task-job-status-cell <?php echo e($laneTasks->isEmpty() ? 'is-empty' : 'has-tasks'); ?>"
                        data-status="<?php echo e($laneStatus); ?>"
                        <?php if($draggable): ?>
                            x-on:dragover.prevent
                            x-on:drop.prevent="if(draggedTask){ $wire.moveTask(draggedTask, <?php echo e(\Illuminate\Support\Js::from($laneStatus)); ?>); draggedTask=null }"
                        <?php endif; ?>
                    >
                        <div class="ft-mobile-lane-label <?php echo e($laneColor ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($laneColor)); ?>"><span><?php echo e($laneStatus); ?></span><b><?php echo e($laneTasks->count()); ?></b></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $laneTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $canDragTask = $draggable && app(\App\Services\AccessControlService::class)->canEditVisibleTask(auth()->user(), $taskRow);
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDragTask): ?>
                                <?php if (isset($component)) { $__componentOriginal637533543995aac582a7a49daaf2271d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal637533543995aac582a7a49daaf2271d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.task-card','data' => ['task' => $taskRow,'draggable' => 'true','xOn:dragstart' => 'draggedTask='.e($taskRow->id).'','xOn:dragend' => 'draggedTask=null','wire:key' => ''.e($keyPrefix).'-'.e(str($laneStatus)->slug()).'-task-'.e($taskRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.task-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskRow),'draggable' => 'true','x-on:dragstart' => 'draggedTask='.e($taskRow->id).'','x-on:dragend' => 'draggedTask=null','wire:key' => ''.e($keyPrefix).'-'.e(str($laneStatus)->slug()).'-task-'.e($taskRow->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal637533543995aac582a7a49daaf2271d)): ?>
<?php $attributes = $__attributesOriginal637533543995aac582a7a49daaf2271d; ?>
<?php unset($__attributesOriginal637533543995aac582a7a49daaf2271d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal637533543995aac582a7a49daaf2271d)): ?>
<?php $component = $__componentOriginal637533543995aac582a7a49daaf2271d; ?>
<?php unset($__componentOriginal637533543995aac582a7a49daaf2271d); ?>
<?php endif; ?>
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginal637533543995aac582a7a49daaf2271d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal637533543995aac582a7a49daaf2271d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.task-card','data' => ['task' => $taskRow,'wire:key' => ''.e($keyPrefix).'-'.e(str($laneStatus)->slug()).'-task-'.e($taskRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.task-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskRow),'wire:key' => ''.e($keyPrefix).'-'.e(str($laneStatus)->slug()).'-task-'.e($taskRow->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal637533543995aac582a7a49daaf2271d)): ?>
<?php $attributes = $__attributesOriginal637533543995aac582a7a49daaf2271d; ?>
<?php unset($__attributesOriginal637533543995aac582a7a49daaf2271d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal637533543995aac582a7a49daaf2271d)): ?>
<?php $component = $__componentOriginal637533543995aac582a7a49daaf2271d; ?>
<?php unset($__componentOriginal637533543995aac582a7a49daaf2271d); ?>
<?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="ft-task-job-empty-cell">No tasks</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="ft-task-job-matrix-empty">No tasks match the current filters.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/board/task-job-matrix.blade.php ENDPATH**/ ?>