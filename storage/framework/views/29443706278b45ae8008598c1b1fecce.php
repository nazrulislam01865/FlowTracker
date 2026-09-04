<?php if (isset($component)) { $__componentOriginal01cd1b9acb87f79a52a4de5154a1445e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01cd1b9acb87f79a52a4de5154a1445e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.missing-product-supplier-modal','data' => ['show' => $showMissingProductSupplierModal,'productName' => $missingProductSupplierName,'choice' => $missingProductSupplierChoice,'existingSupplierId' => $missingProductExistingSupplierId,'existingSupplierLabel' => $missingProductExistingSupplierLabel,'newSupplierName' => $missingProductNewSupplierName,'newSupplierEmail' => $missingProductNewSupplierEmail,'allowSkip' => $missingProductSupplierAllowSkip,'recordLabel' => $missingProductSupplierRecordLabel,'submitMode' => $missingProductSupplierSubmitMode,'selectorContext' => $missingProductSupplierSelectorContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.missing-product-supplier-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showMissingProductSupplierModal),'product-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductSupplierName),'choice' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductSupplierChoice),'existing-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductExistingSupplierId),'existing-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductExistingSupplierLabel),'new-supplier-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductNewSupplierName),'new-supplier-email' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductNewSupplierEmail),'allow-skip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductSupplierAllowSkip),'record-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductSupplierRecordLabel),'submit-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductSupplierSubmitMode),'selector-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($missingProductSupplierSelectorContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal01cd1b9acb87f79a52a4de5154a1445e)): ?>
<?php $attributes = $__attributesOriginal01cd1b9acb87f79a52a4de5154a1445e; ?>
<?php unset($__attributesOriginal01cd1b9acb87f79a52a4de5154a1445e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal01cd1b9acb87f79a52a4de5154a1445e)): ?>
<?php $component = $__componentOriginal01cd1b9acb87f79a52a4de5154a1445e; ?>
<?php unset($__componentOriginal01cd1b9acb87f79a52a4de5154a1445e); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/missing-product-supplier-modal.blade.php ENDPATH**/ ?>