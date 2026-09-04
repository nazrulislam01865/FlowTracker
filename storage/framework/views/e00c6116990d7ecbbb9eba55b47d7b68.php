<div class="ft-rfq-pane ft-rfq-product-pane" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-rfq-pane-'.e($selectedInquiry->id).''; ?>wire:key="inquiry-rfq-pane-<?php echo e($selectedInquiry->id); ?>">
    <?php if (isset($component)) { $__componentOriginalec2c85c1e3abe6d7204ecfe88374064c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalec2c85c1e3abe6d7204ecfe88374064c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-product-workspace','data' => ['workspace' => $rfqWorkspace ?? [],'canManage' => $canManageInquiryRfq,'canEditSuppliers' => $canEditSuppliers]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-product-workspace'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['workspace' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqWorkspace ?? []),'can-manage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canManageInquiryRfq),'can-edit-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditSuppliers)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalec2c85c1e3abe6d7204ecfe88374064c)): ?>
<?php $attributes = $__attributesOriginalec2c85c1e3abe6d7204ecfe88374064c; ?>
<?php unset($__attributesOriginalec2c85c1e3abe6d7204ecfe88374064c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalec2c85c1e3abe6d7204ecfe88374064c)): ?>
<?php $component = $__componentOriginalec2c85c1e3abe6d7204ecfe88374064c; ?>
<?php unset($__componentOriginalec2c85c1e3abe6d7204ecfe88374064c); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRfqSupplierPicker && $canManageInquiryRfq): ?>
        <?php if (isset($component)) { $__componentOriginala03d28e01946eec899aa0d6ae592d239 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala03d28e01946eec899aa0d6ae592d239 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-add-supplier-modal','data' => ['candidates' => $rfqSupplierCandidates,'products' => $rfqAssignableProducts ?? collect(),'selectedProductId' => $rfqSupplierProductId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-add-supplier-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['candidates' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqSupplierCandidates),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqAssignableProducts ?? collect()),'selected-product-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqSupplierProductId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala03d28e01946eec899aa0d6ae592d239)): ?>
<?php $attributes = $__attributesOriginala03d28e01946eec899aa0d6ae592d239; ?>
<?php unset($__attributesOriginala03d28e01946eec899aa0d6ae592d239); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala03d28e01946eec899aa0d6ae592d239)): ?>
<?php $component = $__componentOriginala03d28e01946eec899aa0d6ae592d239; ?>
<?php unset($__componentOriginala03d28e01946eec899aa0d6ae592d239); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/sections/rfq.blade.php ENDPATH**/ ?>