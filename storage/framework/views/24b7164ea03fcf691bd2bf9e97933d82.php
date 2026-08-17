<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'step' => 4,
    'title' => 'What happens next',
    'workflowOptions' => collect(),
    'selectedWorkflowId' => null,
    'selectedWorkflowName' => 'Select workflow',
    'phaseCount' => 0,
    'taskCount' => 0,
    'selectionAction' => 'setCreateSelector',
    'selectionProperty' => 'workflowId',
    'optionFallback' => 'Workflow',
    'footnote' => 'Tasks are created when you create this record.',
    'previewAllowed' => false,
    'emptyMessage' => null,
    'errorField' => null,
    'startPhases' => collect(),
    'startPhaseId' => null,
    'startPhaseProperty' => null,
    'startPhaseErrorField' => null,
]));

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

foreach (array_filter(([
    'step' => 4,
    'title' => 'What happens next',
    'workflowOptions' => collect(),
    'selectedWorkflowId' => null,
    'selectedWorkflowName' => 'Select workflow',
    'phaseCount' => 0,
    'taskCount' => 0,
    'selectionAction' => 'setCreateSelector',
    'selectionProperty' => 'workflowId',
    'optionFallback' => 'Workflow',
    'footnote' => 'Tasks are created when you create this record.',
    'previewAllowed' => false,
    'emptyMessage' => null,
    'errorField' => null,
    'startPhases' => collect(),
    'startPhaseId' => null,
    'startPhaseProperty' => null,
    'startPhaseErrorField' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $workflowOptions = collect($workflowOptions);
    $startPhases = collect($startPhases);
    $workflowOptionCount = $workflowOptions->count();
    $phaseCount = (int) $phaseCount;
    $taskCount = (int) $taskCount;
    $selectedWorkflowId = $selectedWorkflowId !== null ? (int) $selectedWorkflowId : null;
    $showStartPhasePicker = filled($startPhaseProperty) && $startPhases->count() > 1;
?>

<section <?php echo e($attributes->class('ft-create-workflow-next')); ?> x-data="{ workflowOpen: false }">
    <div class="ft-create-workflow-heading">
        <span><?php echo e($step); ?></span>
        <h2><?php echo e($title); ?></h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowOptionCount > 0): ?>
            <em><?php echo e($workflowOptionCount); ?> <?php echo e(\Illuminate\Support\Str::plural('workflow', $workflowOptionCount)); ?> available</em>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-create-workflow-card" :class="{ 'is-open': workflowOpen }">
        <button
            class="ft-create-workflow-selected"
            type="button"
            x-on:click="workflowOpen = !workflowOpen"
            :aria-expanded="workflowOpen.toString()"
            aria-haspopup="listbox"
        >
            <span class="ft-create-workflow-icon" aria-hidden="true">✓</span>
            <span class="ft-create-workflow-copy">
                <small>Default workflow</small>
                <strong><?php echo e($selectedWorkflowName ?: 'Select workflow'); ?></strong>
                <span><?php echo e($phaseCount); ?> <?php echo e(\Illuminate\Support\Str::plural('phase', $phaseCount)); ?> · <?php echo e($taskCount); ?> <?php echo e(\Illuminate\Support\Str::plural('task', $taskCount)); ?> will be created</span>
            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previewAllowed): ?>
                <span class="ft-workflow-preview-muted" title="Workflow Setup is temporarily disabled">Preview workflow unavailable</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="ft-create-workflow-chevron" aria-hidden="true">⌄</span>
        </button>

        <div class="ft-create-workflow-options" x-cloak x-show="workflowOpen" role="listbox" aria-label="Available workflows">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $workflowOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflowOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $optionId = (int) ($workflowOption['id'] ?? 0);
                    $wireClick = $selectionAction."('".$selectionProperty."', '".$optionId."')";
                ?>
                <button
                    type="button"
                    class="ft-create-workflow-option <?php echo e($optionId === $selectedWorkflowId ? 'is-selected' : ''); ?>"
                    wire:click="<?php echo e($wireClick); ?>"
                    x-on:click="workflowOpen = false"
                    role="option"
                    aria-selected="<?php echo e($optionId === $selectedWorkflowId ? 'true' : 'false'); ?>"
                >
                    <span class="ft-create-workflow-radio" aria-hidden="true"></span>
                    <span>
                        <strong><?php echo e($workflowOption['label'] ?? 'Workflow'); ?></strong>
                        <small><?php echo e(filled($workflowOption['meta'] ?? null) ? $workflowOption['meta'] : $optionFallback); ?></small>
                    </span>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-create-workflow-empty">No workflow is available for the selected client.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showStartPhasePicker): ?>
                <label class="ft-create-workflow-start-phase">
                    <span>Starting phase</span>
                    <select wire:model.live="<?php echo e($startPhaseProperty); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $startPhases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($phase->id); ?>"><?php echo e($phase->sequence); ?>. <?php echo e($phase->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <small>Choose where this Order should enter the selected workflow.</small>
                </label>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emptyMessage): ?>
        <small class="field-error validation-error"><?php echo e($emptyMessage); ?></small>
    <?php elseif($errorField && $errors->has($errorField)): ?>
        <small class="field-error validation-error"><?php echo e($errors->first($errorField)); ?></small>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startPhaseErrorField && $errors->has($startPhaseErrorField)): ?>
        <small class="field-error validation-error"><?php echo e($errors->first($startPhaseErrorField)); ?></small>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footnote): ?>
        <p class="ft-create-workflow-footnote"><?php echo e($footnote); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/create-workflow-picker.blade.php ENDPATH**/ ?>