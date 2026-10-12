<div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-products-workspace-'.e($orderId).''; ?>wire:key="order-products-workspace-<?php echo e($orderId); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'products','method' => 'loadProductsSection','keyPrefix' => 'order-products-isolated','contextType' => 'order','contextId' => $orderId,'queueGroup' => 'order-detail-'.e($orderId).'','queuePriority' => 10,'settleDelay' => 180,'rows' => 4,'message' => 'Loading order products when needed…','rootMargin' => '160px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'products','method' => 'loadProductsSection','key-prefix' => 'order-products-isolated','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderId),'queue-group' => 'order-detail-'.e($orderId).'','queue-priority' => 10,'settle-delay' => 180,'rows' => 4,'message' => 'Loading order products when needed…','root-margin' => '160px 0px']); ?>
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
        <?php if (isset($component)) { $__componentOriginalecfe6bb0ec1e143001ce80be73d172d2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.products','data' => ['job' => $job,'context' => $context,'showAddJobProductForm' => $showAddJobProductForm,'jobProductSearch' => $jobProductSearch,'jobProductSearchResults' => $jobProductSearchResults,'jobProductSearchSuppliers' => $jobProductSearchSuppliers,'jobProductResultTotal' => $jobProductResultTotal,'jobProductShowAllResults' => $jobProductShowAllResults,'jobProductSelectedProduct' => $jobProductSelectedProduct,'jobProductSelectedSupplier' => $jobProductSelectedSupplier,'jobProductCategory' => $jobProductCategory,'jobProductQuantity' => $jobProductQuantity,'jobProductUnitPrice' => $jobProductUnitPrice,'jobProductSupplierId' => $jobProductSupplierId,'jobProductSupplierLabel' => $jobProductSupplierLabel,'jobProductSupplierSkipped' => $jobProductSupplierSkipped,'jobProductSupplierLocked' => $jobProductSupplierLocked,'showEditOrderProductModal' => $showEditOrderProductModal,'editOrderProductItemId' => $editOrderProductItemId,'editOrderProductName' => $editOrderProductName,'editOrderProductCode' => $editOrderProductCode,'editOrderProductCategory' => $editOrderProductCategory,'editOrderProductSearch' => $editOrderProductSearch,'editOrderProductSearchResults' => $editOrderProductSearchResults,'editOrderProductSearchSuppliers' => $editOrderProductSearchSuppliers,'editOrderProductResultTotal' => $editOrderProductResultTotal,'editOrderProductSelectedProduct' => $editOrderProductSelectedProduct,'editOrderProductSelectedSupplier' => $editOrderProductSelectedSupplier,'editOrderProductShowAllResults' => $editOrderProductShowAllResults,'editOrderProductSupplierId' => $editOrderProductSupplierId,'editOrderProductSupplierLabel' => $editOrderProductSupplierLabel,'editOrderProductQuantity' => $editOrderProductQuantity,'editOrderProductUnitPrice' => $editOrderProductUnitPrice,'editOrderProductNotes' => $editOrderProductNotes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.products'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'show-add-job-product-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showAddJobProductForm),'job-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearch),'job-product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchResults),'job-product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchSuppliers),'job-product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductResultTotal),'job-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductShowAllResults),'job-product-selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedProduct),'job-product-selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedSupplier),'job-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductCategory),'job-product-quantity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductQuantity),'job-product-unit-price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductUnitPrice),'job-product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierId),'job-product-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLabel),'job-product-supplier-skipped' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierSkipped),'job-product-supplier-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLocked),'show-edit-order-product-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showEditOrderProductModal),'edit-order-product-item-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductItemId),'edit-order-product-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductName),'edit-order-product-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCode),'edit-order-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCategory),'edit-order-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearch),'edit-order-product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchResults),'edit-order-product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchSuppliers),'edit-order-product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductResultTotal),'edit-order-product-selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedProduct),'edit-order-product-selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedSupplier),'edit-order-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductShowAllResults),'edit-order-product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierId),'edit-order-product-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierLabel),'edit-order-product-quantity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductQuantity),'edit-order-product-unit-price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductUnitPrice),'edit-order-product-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductNotes)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2)): ?>
<?php $attributes = $__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2; ?>
<?php unset($__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalecfe6bb0ec1e143001ce80be73d172d2)): ?>
<?php $component = $__componentOriginalecfe6bb0ec1e143001ce80be73d172d2; ?>
<?php unset($__componentOriginalecfe6bb0ec1e143001ce80be73d172d2); ?>
<?php endif; ?>

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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/jobs/order-products-section.blade.php ENDPATH**/ ?>