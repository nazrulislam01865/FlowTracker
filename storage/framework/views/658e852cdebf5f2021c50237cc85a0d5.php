<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'phase',
    'presentation' => [],
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
    'phase',
    'presentation' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section class="ft-shipment-phase ft-ms-phase" aria-label="Shipment tasks" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-phase-'.e($job->id).'-'.e($phase->id).''; ?>wire:key="shipment-phase-<?php echo e($job->id); ?>-<?php echo e($phase->id); ?>">
    <div class="ft-shipment-phase__status">
        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M2.5 5.5h9v8h-9zM11.5 8h3l3 3v2.5h-6z"/><circle cx="6" cy="15" r="1.5"/><circle cx="14.5" cy="15" r="1.5"/></svg>
        Order status: Ready for shipment
    </div>

    <header class="ft-shipment-phase__head">
        <div>
            <h3>Shipment tasks</h3>
            <p>Complete these steps in order to dispatch the package.</p>
        </div>
        <div class="ft-shipment-progress" aria-label="<?php echo e($presentation['completed_count'] ?? 0); ?> of <?php echo e($presentation['total_count'] ?? 0); ?> complete">
            <strong class="<?php echo e(($presentation['completed_count'] ?? 0) === ($presentation['total_count'] ?? 0) && ($presentation['total_count'] ?? 0) > 0 ? 'is-complete' : ''); ?>">
                <?php echo e($presentation['completed_count'] ?? 0); ?> of <?php echo e($presentation['total_count'] ?? 0); ?> complete
            </strong>
        </div>
    </header>

    <div class="ft-shipment-phase__tasks ft-ms-tasks">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($presentation['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <article class="ft-shipment-task ft-shipment-task--<?php echo e($row['mode']); ?> ft-ms-task" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-task-'.e($row['task']->id).'-'.e($row['mode']).'-'.e((int) $row['is_done']).''; ?>wire:key="shipment-task-<?php echo e($row['task']->id); ?>-<?php echo e($row['mode']); ?>-<?php echo e((int) $row['is_done']); ?>">
                <div class="ft-shipment-task__marker-wrap" aria-hidden="true"><span class="ft-shipment-task__marker"><?php echo e($row['is_done'] ? '✓' : ($row['mode'] === 'active' ? '●' : '⌁')); ?></span></div>

                <div class="ft-shipment-task__content">
                    <div class="ft-shipment-task__top">
                        <div class="ft-shipment-task__copy">
                            <div class="ft-shipment-task__eyebrow"><span>TASK <?php echo e($row['display_code']); ?></span></div>
                            <h4><?php echo e($row['title']); ?></h4>
                            <p><?php echo e($row['description']); ?></p>
                        </div>

                        <?php if (isset($component)) { $__componentOriginal222d733c16edabfdd2e93c48ca5b92b6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.task-meta','data' => ['job' => $job,'row' => $row]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.task-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6)): ?>
<?php $attributes = $__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6; ?>
<?php unset($__attributesOriginal222d733c16edabfdd2e93c48ca5b92b6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal222d733c16edabfdd2e93c48ca5b92b6)): ?>
<?php $component = $__componentOriginal222d733c16edabfdd2e93c48ca5b92b6; ?>
<?php unset($__componentOriginal222d733c16edabfdd2e93c48ca5b92b6); ?>
<?php endif; ?>

                        <div class="ft-shipment-task__state">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['mode'] === 'active'): ?>
                                <span class="ft-shipment-state ft-shipment-state--action"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 6.5v4M10 13.5h.01"/></svg>Action required</span>
                            <?php elseif($row['is_done']): ?>
                                <span class="ft-shipment-state ft-shipment-state--done">Completed</span>
                            <?php else: ?>
                                <span class="ft-shipment-state ft-shipment-state--locked"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Locked</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="ft-ms-task__body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['key'] === 'SHIP_CONFIRM_INFO'): ?>
                            <?php if (isset($component)) { $__componentOriginalbad3cff2ecaa3876eef66d6f9ec9edf7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbad3cff2ecaa3876eef66d6f9ec9edf7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.plan-table','data' => ['row' => $row,'presentation' => $presentation]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.plan-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row),'presentation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($presentation)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbad3cff2ecaa3876eef66d6f9ec9edf7)): ?>
<?php $attributes = $__attributesOriginalbad3cff2ecaa3876eef66d6f9ec9edf7; ?>
<?php unset($__attributesOriginalbad3cff2ecaa3876eef66d6f9ec9edf7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbad3cff2ecaa3876eef66d6f9ec9edf7)): ?>
<?php $component = $__componentOriginalbad3cff2ecaa3876eef66d6f9ec9edf7; ?>
<?php unset($__componentOriginalbad3cff2ecaa3876eef66d6f9ec9edf7); ?>
<?php endif; ?>
                        <?php elseif($row['key'] === 'SHIP_LABEL'): ?>
                            <?php if (isset($component)) { $__componentOriginal9a7e4826b8319499b6f57b343196d5f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a7e4826b8319499b6f57b343196d5f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.tracking-table','data' => ['row' => $row,'presentation' => $presentation]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.tracking-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row),'presentation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($presentation)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a7e4826b8319499b6f57b343196d5f6)): ?>
<?php $attributes = $__attributesOriginal9a7e4826b8319499b6f57b343196d5f6; ?>
<?php unset($__attributesOriginal9a7e4826b8319499b6f57b343196d5f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a7e4826b8319499b6f57b343196d5f6)): ?>
<?php $component = $__componentOriginal9a7e4826b8319499b6f57b343196d5f6; ?>
<?php unset($__componentOriginal9a7e4826b8319499b6f57b343196d5f6); ?>
<?php endif; ?>
                        <?php elseif($row['key'] === 'SHIP_PACKAGE'): ?>
                            <?php if (isset($component)) { $__componentOriginal1bcd4647b9469e3987a4bb0fc26a735f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bcd4647b9469e3987a4bb0fc26a735f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.dispatch-table','data' => ['row' => $row,'presentation' => $presentation]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.dispatch-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row),'presentation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($presentation)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bcd4647b9469e3987a4bb0fc26a735f)): ?>
<?php $attributes = $__attributesOriginal1bcd4647b9469e3987a4bb0fc26a735f; ?>
<?php unset($__attributesOriginal1bcd4647b9469e3987a4bb0fc26a735f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bcd4647b9469e3987a4bb0fc26a735f)): ?>
<?php $component = $__componentOriginal1bcd4647b9469e3987a4bb0fc26a735f; ?>
<?php unset($__componentOriginal1bcd4647b9469e3987a4bb0fc26a735f); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </article>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/phase.blade.php ENDPATH**/ ?>