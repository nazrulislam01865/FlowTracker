<div>
    <?php if (isset($component)) { $__componentOriginal8f6938ac62d0a39f318af1c1674a1814 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.page-head','data' => ['title' => 'Management Dashboard','subtitle' => now()->format('l, F j').' · Exceptions, workload and delivery health']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Management Dashboard','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(now()->format('l, F j').' · Exceptions, workload and delivery health')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('reports','export')): ?>
                <a class="ghost" href="<?php echo e(route('reports')); ?>" wire:navigate>Export summary</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','create')): ?>
                <a class="primary" href="<?php echo e(route('jobs.index',['create'=>1])); ?>" wire:navigate>＋ New Job</a>
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

    <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'dashboard-metrics', defer: true, always: true, token: 'a716e027-1'); ?>

    <div class="grid-2">
        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'dashboard-attention', lazy: true, always: true, token: 'a716e027-2'); ?>
        </div>

        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'dashboard-phases', lazy: true, always: true, token: 'a716e027-3'); ?>
        </div>
    </div>

    <div class="grid-3">
        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'dashboard-workload', lazy: true, always: true, token: 'a716e027-4'); ?>
        </div>

        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'dashboard-deliveries', lazy: true, always: true, token: 'a716e027-5'); ?>
        </div>

        <div class="ft-island-shell">
            <?php if (isset($__livewire)) echo $__livewire->renderIslandDirective(name: 'dashboard-activity', lazy: true, always: true, token: 'a716e027-6'); ?>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/dashboard/index.blade.php ENDPATH**/ ?>