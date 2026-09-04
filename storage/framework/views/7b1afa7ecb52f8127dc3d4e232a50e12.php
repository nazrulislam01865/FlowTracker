<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['stage', 'index']));

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

foreach (array_filter((['stage', 'index']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $tasks = collect($stage['tasks'] ?? []);
    $requiredCount = $tasks->where('is_required', true)->count();
    $documentCount = $tasks->where('document_enabled', true)->count();
?>
<div class="ft-order-workflow-stage-row" style="--stage: <?php echo e($stage['color']); ?>">
    <div class="ft-order-workflow-stage-number"><?php echo e($index + 1); ?></div>
    <div class="ft-order-workflow-stage-copy">
        <b><?php echo e($stage['name']); ?></b>
        <small><?php echo e($stage['short']); ?> · stage color <?php echo e($stage['color']); ?></small>
    </div>
    <div class="ft-order-workflow-task-summary">
        <b>
            <?php echo e($tasks->count()); ?> tasks · <?php echo e($requiredCount); ?> required
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentCount): ?><span class="ft-order-workflow-doc-count">📎 <?php echo e($documentCount); ?> document</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </b>
        <div class="ft-order-workflow-task-preview">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tasks->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <span class="ft-order-workflow-task-pill <?php echo e(!empty($task['document_enabled']) ? 'has-doc' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($task['document_enabled'])): ?>📎 <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e($task['title']); ?>

                </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tasks->count() > 4): ?><span class="ft-order-workflow-task-pill">+<?php echo e($tasks->count() - 4); ?> more</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <small>Tasks unlock automatically. Document validation is applied only where configured.</small>
    </div>
    <span class="ft-order-workflow-state">Active</span>
    <div class="ft-order-workflow-stage-actions">
        <button type="button" class="ft-order-workflow-btn ft-order-workflow-btn--small" wire:click="openStageEditor(<?php echo e($index); ?>)">Edit stage &amp; tasks</button>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/order-workflow/stage-row.blade.php ENDPATH**/ ?>