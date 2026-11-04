<?php if (isset($component)) { $__componentOriginal00958161278504347177de5bb665faa1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00958161278504347177de5bb665faa1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-document-card','data' => ['title' => 'Certificate & Test Report']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-document-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Certificate & Test Report']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal00958161278504347177de5bb665faa1)): ?>
<?php $attributes = $__attributesOriginal00958161278504347177de5bb665faa1; ?>
<?php unset($__attributesOriginal00958161278504347177de5bb665faa1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal00958161278504347177de5bb665faa1)): ?>
<?php $component = $__componentOriginal00958161278504347177de5bb665faa1; ?>
<?php unset($__componentOriginal00958161278504347177de5bb665faa1); ?>
<?php endif; ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/storage/framework/views/4fe018ae81c5ec197a40015bdb67a843.blade.php ENDPATH**/ ?>