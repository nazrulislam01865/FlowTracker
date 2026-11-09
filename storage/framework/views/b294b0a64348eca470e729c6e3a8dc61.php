<td data-label="Purchase order" class="ft-order-po-cell">
    <?php if (isset($component)) { $__componentOriginalc2677fea324d3c45ab0be143b454c82f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2677fea324d3c45ab0be143b454c82f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.orders.po-action','data' => ['orderId' => $job->id,'generated' => (bool) $job->has_active_supplier_po]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('orders.po-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'generated' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $job->has_active_supplier_po)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2677fea324d3c45ab0be143b454c82f)): ?>
<?php $attributes = $__attributesOriginalc2677fea324d3c45ab0be143b454c82f; ?>
<?php unset($__attributesOriginalc2677fea324d3c45ab0be143b454c82f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2677fea324d3c45ab0be143b454c82f)): ?>
<?php $component = $__componentOriginalc2677fea324d3c45ab0be143b454c82f; ?>
<?php unset($__componentOriginalc2677fea324d3c45ab0be143b454c82f); ?>
<?php endif; ?>
</td>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/orders/list/po-cell.blade.php ENDPATH**/ ?>