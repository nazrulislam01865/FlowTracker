<?php
    $categorySearchValue = trim((string) $newProductCategorySearch);
    $categorySuggestions = $categorySearchValue === '' ? $productCategories->take(6) : $newProductCategoryMatches;
    $hasDuplicateCode = (bool) $duplicateProduct;
    $hasSimilarProductName = $newProductSimilarProducts->isNotEmpty();
    $manualProductCode = trim((string) $newProductCode);
    $productCodeFormatValid = $manualProductCode === ''
        || (mb_strlen($manualProductCode) <= 40
            && preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $manualProductCode) === 1);
    $productCodeReady = $productCodeFormatValid && !$hasDuplicateCode;
    $productCategoryReady = $productCodeReady && (bool) $newProductSelectedCategory;
    $productNameReady = $productCategoryReady && trim((string) $newProductName) !== '';
    $productSupplierReady = $productNameReady;
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($catalogReady && $canUseOrderProductSelector): ?>
    <?php if (isset($component)) { $__componentOriginal39599d37e505322c8766761a0aaccbf0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39599d37e505322c8766761a0aaccbf0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.create-product-quantity','data' => ['context' => 'order','rows' => $jobItems,'rowsProperty' => 'jobItems','removeMethod' => 'removeProductRow','required' => true,'step' => 3,'requiredErrorField' => 'jobItems','activeProductCount' => $activeProductCount,'productSearchResults' => $productSearchResults,'productSearchSuppliers' => $productSearchSuppliers,'productResultTotal' => $productResultTotal,'createProductSearch' => $createProductSearch,'createProductShowAllResults' => $createProductShowAllResults,'selectedProductDetails' => $selectedProductDetails,'selectedProductSuppliers' => $selectedProductSuppliers,'supplierSkippedProductIds' => $createOrderSupplierSkipProductIds,'supplierRequired' => true,'canCreateCatalogProduct' => $canCreateCatalogProduct,'wireKey' => 'create-order-products-ready','rowKeyPrefix' => 'selected-order-product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.create-product-quantity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['context' => 'order','rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobItems),'rows-property' => 'jobItems','remove-method' => 'removeProductRow','required' => true,'step' => 3,'required-error-field' => 'jobItems','active-product-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeProductCount),'product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSearchResults),'product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSearchSuppliers),'product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productResultTotal),'create-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductSearch),'create-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductShowAllResults),'selected-product-details' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductDetails),'selected-product-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductSuppliers),'supplier-skipped-product-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createOrderSupplierSkipProductIds),'supplier-required' => true,'can-create-catalog-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateCatalogProduct),'wire-key' => 'create-order-products-ready','row-key-prefix' => 'selected-order-product']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal39599d37e505322c8766761a0aaccbf0)): ?>
<?php $attributes = $__attributesOriginal39599d37e505322c8766761a0aaccbf0; ?>
<?php unset($__attributesOriginal39599d37e505322c8766761a0aaccbf0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal39599d37e505322c8766761a0aaccbf0)): ?>
<?php $component = $__componentOriginal39599d37e505322c8766761a0aaccbf0; ?>
<?php unset($__componentOriginal39599d37e505322c8766761a0aaccbf0); ?>
<?php endif; ?>
<?php elseif(!$catalogReady): ?>
    <?php if (isset($component)) { $__componentOriginal732a8e3f5371418be0dfaaa000db0561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal732a8e3f5371418be0dfaaa000db0561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create-section-placeholder','data' => ['number' => '3','title' => 'Products & quantities','section' => 'catalog','rows' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create-section-placeholder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '3','title' => 'Products & quantities','section' => 'catalog','rows' => 3]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal732a8e3f5371418be0dfaaa000db0561)): ?>
<?php $attributes = $__attributesOriginal732a8e3f5371418be0dfaaa000db0561; ?>
<?php unset($__attributesOriginal732a8e3f5371418be0dfaaa000db0561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal732a8e3f5371418be0dfaaa000db0561)): ?>
<?php $component = $__componentOriginal732a8e3f5371418be0dfaaa000db0561; ?>
<?php unset($__componentOriginal732a8e3f5371418be0dfaaa000db0561); ?>
<?php endif; ?>
<?php else: ?>
    <section class="ft-create-section ft-order-products-prototype" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-catalog-restricted'; ?>wire:key="create-catalog-restricted">
        <div class="ft-order-products-title-row">
            <div class="ft-create-section-title ft-order-products-title">
                <span>3</span><h2>Products &amp; quantities</h2>
            </div>
        </div>
        <div class="ft-create-note">Products are hidden because this role does not have <b>Products → View</b> permission. Product access on Create Order is controlled only by the Products role.</div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->make('components.jobs.create.missing-product-supplier-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('components.jobs.create.product-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create-products.blade.php ENDPATH**/ ?>