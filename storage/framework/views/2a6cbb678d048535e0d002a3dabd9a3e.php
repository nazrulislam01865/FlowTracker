<div class="ft-mgmt-dashboard-overview" data-dashboard-panel="overview">
    <?php if (isset($component)) { $__componentOriginalee5bb7364c37061cbe535f4c41f9060f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee5bb7364c37061cbe535f4c41f9060f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.orders.workflow-stage-overview','data' => ['stages' => $orderStages,'mode' => 'navigate','showHeader' => false,'navigationQuery' => $orderStageNavigationQuery]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('orders.workflow-stage-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderStages),'mode' => 'navigate','show-header' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'navigation-query' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderStageNavigationQuery)]); ?>
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



</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/overview-panel.blade.php ENDPATH**/ ?>