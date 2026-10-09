<div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-attachments-workspace-'.e($orderId).''; ?>wire:key="order-attachments-workspace-<?php echo e($orderId); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'attachments','method' => 'loadAttachmentsSection','keyPrefix' => 'order-attachments-isolated','contextType' => 'order','contextId' => $orderId,'queueGroup' => 'order-detail-'.e($orderId).'','queuePriority' => 30,'settleDelay' => 180,'rows' => 3,'message' => 'Loading attachments when needed…','rootMargin' => '80px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'attachments','method' => 'loadAttachmentsSection','key-prefix' => 'order-attachments-isolated','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderId),'queue-group' => 'order-detail-'.e($orderId).'','queue-priority' => 30,'settle-delay' => 180,'rows' => 3,'message' => 'Loading attachments when needed…','root-margin' => '80px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginalc381fed9822a6501599c1e870652f2cd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc381fed9822a6501599c1e870652f2cd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.attachments','data' => ['job' => $job,'context' => $context,'jobDocumentUploads' => $jobDocumentUploads]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.attachments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc381fed9822a6501599c1e870652f2cd)): ?>
<?php $attributes = $__attributesOriginalc381fed9822a6501599c1e870652f2cd; ?>
<?php unset($__attributesOriginalc381fed9822a6501599c1e870652f2cd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc381fed9822a6501599c1e870652f2cd)): ?>
<?php $component = $__componentOriginalc381fed9822a6501599c1e870652f2cd; ?>
<?php unset($__componentOriginalc381fed9822a6501599c1e870652f2cd); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/jobs/order-attachments-section.blade.php ENDPATH**/ ?>