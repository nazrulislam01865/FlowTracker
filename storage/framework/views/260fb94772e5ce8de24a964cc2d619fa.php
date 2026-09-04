<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'overviewPhaseId' => null,
    'taskStatuses' => collect(),
    'context' => [],
    'overviewTaskLinkFormTaskId' => null,
    'showShipmentModal' => false,
    'shipmentModalTaskId' => null,
    'shipmentEditingId' => null,
    'shipmentModalMode' => 'same_address',
    'shipmentForm' => [],
    'showShipmentDetailsModal' => false,
    'shipmentDetailsId' => null,
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
    'job',
    'overviewPhaseId' => null,
    'taskStatuses' => collect(),
    'context' => [],
    'overviewTaskLinkFormTaskId' => null,
    'showShipmentModal' => false,
    'shipmentModalTaskId' => null,
    'shipmentEditingId' => null,
    'shipmentModalMode' => 'same_address',
    'shipmentForm' => [],
    'showShipmentDetailsModal' => false,
    'shipmentDetailsId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    /*
     * Workflow Setup is the workflow-definition source for both Inquiry and Order
     * templates. Active Orders stay attached to the Order workflow selected at
     * creation, so this view renders its saved seven-stage definition and real
     * tasks without hard-coded stage/task definitions in Blade.
     *
     * All relations used here are hydrated by JobService::loadVisibleDetailTab(),
     * so this component does not issue database queries (N+1 safe).
     */
    $phases = \App\Support\OrderDetailPresenter::phases($job);
    $selectedPhase = \App\Support\OrderDetailPresenter::selectedPhase($job, $overviewPhaseId);
    $selectedTasks = $selectedPhase
        ? \App\Support\OrderDetailPresenter::displayTasksForPhase($job, $selectedPhase)
        : collect();
    $selectedState = $selectedPhase
        ? \App\Support\OrderDetailPresenter::phaseState($job, $selectedPhase)
        : 'locked';
    $completedTasks = \App\Support\OrderDetailPresenter::completedCount($selectedTasks);
    $applicableTaskCount = $selectedTasks->count();
    $stageCount = $phases->count();
    $isShipmentPhase = \App\Support\OrderShipmentPresenter::isShipmentPhase($selectedPhase, $selectedTasks);
    $shipmentPresentation = $isShipmentPhase
        ? \App\Support\OrderShipmentPresenter::present($job, $selectedPhase, $selectedTasks, $context)
        : [];

    $archivedArtworkDocuments = ! $isShipmentPhase
        ? \App\Support\OrderDetailPresenter::archivedArtworkDocuments($job, $selectedTasks)
        : collect();

    $taskPackSub = match ($selectedState) {
        'completed' => 'This stage is complete',
        'active' => 'Complete the active task to continue the workflow',
        'cancelled' => 'Order cancelled — workflow actions are blocked',
        default => 'This stage is locked',
    };
?>

<section class="section-card integrated-process ft-order-section-card ft-order-workflow-card" id="workflowSection" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-detail-workflow-'.e($job->id).''; ?>wire:key="order-detail-workflow-<?php echo e($job->id); ?>">
    <div class="process-head">
        <div>
            <h2>Order process &amp; tasks</h2>
            <div class="card-sub">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stageCount): ?>
                    <?php echo e($stageCount); ?> stages · status changes save automatically · conditional tasks appear only when required
                <?php else: ?>
                    No Order workflow is configured
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phases->isEmpty()): ?>
        <div class="empty-stage">
            <b>No Order workflow stages are available.</b><br>
            Configure and save an Order workflow in Workflow Setup before creating Orders.
        </div>
    <?php else: ?>
        <section class="workflow" aria-label="Order workflow stages">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginale4a08d57d4ec392b9adcfcfaf1153dbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4a08d57d4ec392b9adcfcfaf1153dbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.stage-card','data' => ['job' => $job,'phase' => $phase,'selected' => (int) ($selectedPhase?->id ?? 0) === (int) $phase->id,'context' => $context]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.stage-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phase),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($selectedPhase?->id ?? 0) === (int) $phase->id),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4a08d57d4ec392b9adcfcfaf1153dbe)): ?>
<?php $attributes = $__attributesOriginale4a08d57d4ec392b9adcfcfaf1153dbe; ?>
<?php unset($__attributesOriginale4a08d57d4ec392b9adcfcfaf1153dbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4a08d57d4ec392b9adcfcfaf1153dbe)): ?>
<?php $component = $__componentOriginale4a08d57d4ec392b9adcfcfaf1153dbe; ?>
<?php unset($__componentOriginale4a08d57d4ec392b9adcfcfaf1153dbe); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isShipmentPhase): ?>
            <?php if (isset($component)) { $__componentOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.phase','data' => ['job' => $job,'phase' => $selectedPhase,'presentation' => $shipmentPresentation]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.phase'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedPhase),'presentation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentPresentation)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37)): ?>
<?php $attributes = $__attributesOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37; ?>
<?php unset($__attributesOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37)): ?>
<?php $component = $__componentOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37; ?>
<?php unset($__componentOriginal4b8a3bff262dd7a3dfecfbbe1a23bd37); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showShipmentModal && $shipmentModalTaskId): ?>
                <?php
                    $shipmentModalTask = $job->tasks->firstWhere('id', (int) $shipmentModalTaskId);
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipmentModalTask): ?>
                    <?php if (isset($component)) { $__componentOriginala9cab2264441b499cc280e878a99e9f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9cab2264441b499cc280e878a99e9f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.add-modal','data' => ['job' => $job,'task' => $shipmentModalTask,'presentation' => $shipmentPresentation,'editingId' => $shipmentEditingId,'mode' => $shipmentModalMode,'form' => $shipmentForm]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.add-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalTask),'presentation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentPresentation),'editing-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentEditingId),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalMode),'form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentForm)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9cab2264441b499cc280e878a99e9f6)): ?>
<?php $attributes = $__attributesOriginala9cab2264441b499cc280e878a99e9f6; ?>
<?php unset($__attributesOriginala9cab2264441b499cc280e878a99e9f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9cab2264441b499cc280e878a99e9f6)): ?>
<?php $component = $__componentOriginala9cab2264441b499cc280e878a99e9f6; ?>
<?php unset($__componentOriginala9cab2264441b499cc280e878a99e9f6); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showShipmentDetailsModal && $shipmentDetailsId): ?>
                <?php
                    $shipmentDetailsRow = collect($shipmentPresentation['shipments'] ?? [])->firstWhere('id', (int) $shipmentDetailsId);
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipmentDetailsRow): ?>
                    <?php if (isset($component)) { $__componentOriginalb791aa0fb962ca5675a628b8d7901ae5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb791aa0fb962ca5675a628b8d7901ae5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.details-modal','data' => ['shipment' => $shipmentDetailsRow]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.details-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shipment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentDetailsRow)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb791aa0fb962ca5675a628b8d7901ae5)): ?>
<?php $attributes = $__attributesOriginalb791aa0fb962ca5675a628b8d7901ae5; ?>
<?php unset($__attributesOriginalb791aa0fb962ca5675a628b8d7901ae5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb791aa0fb962ca5675a628b8d7901ae5)): ?>
<?php $component = $__componentOriginalb791aa0fb962ca5675a628b8d7901ae5; ?>
<?php unset($__componentOriginalb791aa0fb962ca5675a628b8d7901ae5); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <section class="grid ft-order-workflow-layout ft-order-workflow-layout--full">
              <div class="card ft-order-task-panel">
                <div class="card-head">
                    <div>
                        <div class="card-title"><?php echo e($selectedPhase?->name ?? 'Workflow'); ?> tasks</div>
                        <div class="card-sub"><?php echo e($taskPackSub); ?></div>
                    </div>
                    <div class="completion"><?php echo e($completedTasks); ?> of <?php echo e($applicableTaskCount); ?> complete</div>
                </div>

                <div class="task-columns ft-order-task-columns">
                    <span></span><span>Task</span><span>Assignee</span><span>Due date</span><span>Status / files</span><span>Action</span>
                </div>

                <div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selectedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $mode = \App\Support\OrderDetailPresenter::taskMode($job, $task);
                            if ($selectedState === 'completed' && $mode !== 'done') $mode = 'locked';
                            $displayCode = \App\Support\OrderDetailPresenter::taskDisplayCode($selectedPhase, $task, $index);
                        ?>
                        <?php if (isset($component)) { $__componentOriginal5f94e832e7916accc7269585a1fb47d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f94e832e7916accc7269585a1fb47d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.task-row','data' => ['job' => $job,'task' => $task,'mode' => $mode,'displayCode' => $displayCode,'taskStatuses' => $taskStatuses,'context' => $context,'overviewTaskLinkFormTaskId' => $overviewTaskLinkFormTaskId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.task-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode),'display-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayCode),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'overview-task-link-form-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskLinkFormTaskId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5f94e832e7916accc7269585a1fb47d5)): ?>
<?php $attributes = $__attributesOriginal5f94e832e7916accc7269585a1fb47d5; ?>
<?php unset($__attributesOriginal5f94e832e7916accc7269585a1fb47d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f94e832e7916accc7269585a1fb47d5)): ?>
<?php $component = $__componentOriginal5f94e832e7916accc7269585a1fb47d5; ?>
<?php unset($__componentOriginal5f94e832e7916accc7269585a1fb47d5); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="empty-stage">No tasks are configured for this stage.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
              </div>
            </section>

            <?php if (isset($component)) { $__componentOriginalc4d33f5cb28726121f9615c191b5cc39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4d33f5cb28726121f9615c191b5cc39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.archived-artwork','data' => ['documents' => $archivedArtworkDocuments,'canExportDocument' => (bool) ($context['canExportDocument'] ?? false)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.archived-artwork'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($archivedArtworkDocuments),'can-export-document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) ($context['canExportDocument'] ?? false))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4d33f5cb28726121f9615c191b5cc39)): ?>
<?php $attributes = $__attributesOriginalc4d33f5cb28726121f9615c191b5cc39; ?>
<?php unset($__attributesOriginalc4d33f5cb28726121f9615c191b5cc39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4d33f5cb28726121f9615c191b5cc39)): ?>
<?php $component = $__componentOriginalc4d33f5cb28726121f9615c191b5cc39; ?>
<?php unset($__componentOriginalc4d33f5cb28726121f9615c191b5cc39); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/workflow.blade.php ENDPATH**/ ?>