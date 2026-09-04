<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="ft-order-list-flash" role="status"><?php echo e(session('success')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<header class="list-head">
    <div>
        <div class="breadcrumbs"><?php echo e($pageBreadcrumbs); ?></div>
        <h1><?php echo e($pageTitle); ?></h1>
        <p class="sub"><?php echo e($pageDescription); ?></p>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPageActions): ?>
        <div class="top-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('jobs.create')): ?>
                <a class="btn" href="<?php echo e(route('orders.bulk-import')); ?>">⇧ Bulk order</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs', 'create')): ?>
                <a class="btn primary" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate>＋ Create order</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</header>

<?php if (isset($component)) { $__componentOriginalee5bb7364c37061cbe535f4c41f9060f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee5bb7364c37061cbe535f4c41f9060f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.orders.workflow-stage-overview','data' => ['stages' => $stages,'selectedStageId' => $phaseFilter,'mode' => 'filter','title' => $workflowTitle,'description' => $workflowDescription]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('orders.workflow-stage-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stages),'selected-stage-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilter),'mode' => 'filter','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowTitle),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowDescription)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee5bb7364c37061cbe535f4c41f9060f)): ?>
<?php $attributes = $__attributesOriginalee5bb7364c37061cbe535f4c41f9060f; ?>
<?php unset($__attributesOriginalee5bb7364c37061cbe535f4c41f9060f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee5bb7364c37061cbe535f4c41f9060f)): ?>
<?php $component = $__componentOriginalee5bb7364c37061cbe535f4c41f9060f; ?>
<?php unset($__componentOriginalee5bb7364c37061cbe535f4c41f9060f); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/orders/list/header-and-stages.blade.php ENDPATH**/ ?>