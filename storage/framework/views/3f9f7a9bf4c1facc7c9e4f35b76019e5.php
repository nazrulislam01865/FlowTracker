<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'nextTask' => null, 'currentTasks' => collect()]));

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

foreach (array_filter((['job', 'nextTask' => null, 'currentTasks' => collect()]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $phases = \App\Support\OrderDetailPresenter::phases($job);
    $stageCount = max(1, $phases->count());
    $currentPhaseNumber = \App\Support\OrderDetailPresenter::currentPhaseNumber($job);
    $completedTasks = \App\Support\OrderDetailPresenter::completedCount($currentTasks);
    $applicableTasks = $currentTasks->reject(fn($task) => \App\Support\OrderDetailPresenter::isSkippedTask($task))->values();
    $applicableCount = $applicableTasks->count();
    $progress = max(0, min(100, (int) ($job->progress ?? 0)));
    $nextOwner = $nextTask?->assignee?->name ?: $job->owner?->name ?: 'Unassigned';
    $dependency = $currentPhaseNumber <= 1 ? 'No dependency' : 'Previous stage complete';
?>
<section class="summary-grid ft-order-summary-grid" aria-label="Order workflow summary">
    <div class="summary-card ft-order-summary-card">
        <div class="summary-ic">▣</div>
        <div>
            <div class="summary-label">Current stage</div>
            <div class="summary-value">
                <span><?php echo e($job->phase?->name ?: 'Not configured'); ?></span>
                · Stage <span><?php echo e($currentPhaseNumber); ?></span> of <?php echo e($stageCount); ?>

            </div>
            <div class="summary-sub"><?php echo e($completedTasks); ?> of <?php echo e($applicableCount); ?> applicable tasks complete</div>
        </div>
    </div>

    <div class="summary-card ft-order-summary-card">
        <div class="summary-ic">↗</div>
        <div>
            <div class="summary-label">Overall progress</div>
            <div class="summary-value"><?php echo e($progress); ?>%</div>
            <div class="overall-progress ft-order-overall-progress"><i style="width:<?php echo e($progress); ?>%"></i></div>
            <div class="summary-sub"><span class="status-pill"><?php echo e($job->status ?: 'New'); ?></span></div>
        </div>
    </div>

    <div class="summary-card next-summary ft-order-summary-card next">
        <div class="summary-ic">⌘</div>
        <div>
            <div class="summary-label"><span class="help" title="The next unlocked task from this Order's saved workflow setup.">Next required action</span></div>
            <div class="summary-value"><?php echo e($nextTask?->title ?: ($job->completed_at ? 'Order completed' : 'No action available')); ?></div>
            <div class="summary-sub"><span><?php echo e($nextOwner); ?></span> · <span><?php echo e($dependency); ?></span></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nextTask): ?>
                <button type="button" class="btn primary small summary-cta" wire:click="openTask(<?php echo e((int) $nextTask->id); ?>)">Take action</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/summary.blade.php ENDPATH**/ ?>