<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tasks', 'draggable' => false, 'keyPrefix' => 'task-job']));

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

foreach (array_filter((['tasks', 'draggable' => false, 'keyPrefix' => 'task-job']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $groupTasks = collect($tasks);
    $job = $groupTasks->first()?->job;
    $jobId = $job?->id ?? 'unassigned';
?>
<section class="ft-task-job-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($keyPrefix).'-'.e($jobId).''; ?>wire:key="<?php echo e($keyPrefix); ?>-<?php echo e($jobId); ?>">
    <div class="ft-task-job-group-head">
        <div class="ft-task-job-group-main">
            <a class="ft-task-job-group-title" href="<?php echo e($job ? route('jobs.index', ['open' => $job->id]) : '#'); ?>" <?php if($job): ?> wire:navigate <?php endif; ?>>
                <?php echo e($job?->title ?? 'No job'); ?>

            </a>
            <div class="ft-task-job-group-meta">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job): ?>
                    <a href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                    <span>·</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span><?php echo e($job?->client?->name ?? 'No client'); ?></span>
            </div>
        </div>
        <span class="ft-task-job-group-count"><?php echo e($groupTasks->count()); ?></span>
    </div>

    <div class="ft-task-job-group-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groupTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($draggable): ?>
                <?php if (isset($component)) { $__componentOriginal637533543995aac582a7a49daaf2271d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal637533543995aac582a7a49daaf2271d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.task-card','data' => ['task' => $taskRow,'draggable' => 'true','xOn:dragstart' => 'draggedTask='.e($taskRow->id).'','wire:key' => ''.e($keyPrefix).'-task-'.e($taskRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.task-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskRow),'draggable' => 'true','x-on:dragstart' => 'draggedTask='.e($taskRow->id).'','wire:key' => ''.e($keyPrefix).'-task-'.e($taskRow->id).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.task-card','data' => ['task' => $taskRow,'wire:key' => ''.e($keyPrefix).'-task-'.e($taskRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.task-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskRow),'wire:key' => ''.e($keyPrefix).'-task-'.e($taskRow->id).'']); ?>
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
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/board/task-job-group.blade.php ENDPATH**/ ?>