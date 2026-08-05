<div>
    <?php if (isset($component)) { $__componentOriginal8f6938ac62d0a39f318af1c1674a1814 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.page-head','data' => ['title' => 'Reports','subtitle' => 'Operational performance, delivery, workload and task completion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reports','subtitle' => 'Operational performance, delivery, workload and task completion']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('reports','export')): ?>
                <button class="ghost">Export PDF</button>
                <button class="primary">Export Excel</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $attributes = $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $component = $__componentOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>

    <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'report-kpis', defer: true, always: true, token: 'd23ffadc-1'); ?>

    <div class="report-grid">
        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'report-phases', lazy: true, always: true, token: 'd23ffadc-2'); ?>
        </div>

        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'report-workload', lazy: true, always: true, token: 'd23ffadc-3'); ?>
        </div>

        <div class="ft-island-shell ft-report-performance">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'report-performance', lazy: true, always: true, token: 'd23ffadc-4'); ?>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/reports/index.blade.php ENDPATH**/ ?>