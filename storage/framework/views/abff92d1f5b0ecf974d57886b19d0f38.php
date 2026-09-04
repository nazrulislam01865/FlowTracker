        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showProductView && $viewProduct): ?>
            <?php if (isset($component)) { $__componentOriginal9f6a7ffdac90566f77a8f63e0bb16612 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f6a7ffdac90566f77a8f63e0bb16612 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-view','data' => ['product' => $viewProduct,'canEdit' => $canEditMaster,'canDelete' => $canDeleteMaster,'displayTimezone' => $displayTimezone,'detailSectionsReady' => $productDetailSectionsReady ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-view'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($viewProduct),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteMaster),'display-timezone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayTimezone),'detail-sections-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productDetailSectionsReady ?? [])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f6a7ffdac90566f77a8f63e0bb16612)): ?>
<?php $attributes = $__attributesOriginal9f6a7ffdac90566f77a8f63e0bb16612; ?>
<?php unset($__attributesOriginal9f6a7ffdac90566f77a8f63e0bb16612); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f6a7ffdac90566f77a8f63e0bb16612)): ?>
<?php $component = $__componentOriginal9f6a7ffdac90566f77a8f63e0bb16612; ?>
<?php unset($__componentOriginal9f6a7ffdac90566f77a8f63e0bb16612); ?>
<?php endif; ?>
        <?php elseif($showModal): ?>
            <?php if (isset($component)) { $__componentOriginalacd0e783f63677472d00819cf224a2a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalacd0e783f63677472d00819cf224a2a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-form','data' => ['editProduct' => $editProduct,'parents' => $productFormCategories,'allProductCategories' => $parents,'mainCategories' => $productMainCategories,'subcategories' => $productSubcategories,'clients' => $productClients,'canCreateProductCategory' => $canCreateProductCategory,'productImagePreview' => $productImagePreview,'clientAvailabilityMode' => $productClientAvailabilityMode,'clientIds' => $productClientIds,'productSupplierId' => $productSupplierId,'certificateUpload' => $productCertificateUpload,'templateUpload' => $productTemplateUpload,'removeCertificate' => $removeProductCertificate,'removeTemplate' => $removeProductTemplate,'categoryCreator' => $categoryCreator,'selectedMainCategory' => $productFormMainCategory,'selectedProductCategoryId' => $parentId,'selectedSubcategory' => $productSubcategory,'pricePreview' => $productPricePreview,'remoteSurchargePreview' => $productRemoteSurchargePreview,'productOptions' => $productOptions,'productOptionUploads' => $productOptionUploads,'shipmentUrgencies' => $availableProductShipmentUrgencies,'productShipmentUrgencies' => $productShipmentUrgencies,'shipmentUrgencyPickerOpen' => $productShipmentUrgencyPickerOpen,'shipmentUrgencyPickerSelection' => $productShipmentUrgencyPickerSelection,'newProductCategoryMain' => $newProductCategoryMain,'newSubcategoryProductCategoryId' => $newSubcategoryProductCategoryId,'taxonomyReady' => $productTaxonomyReady || (bool) $editProduct,'shipmentOptionsReady' => $productShipmentOptionsReady || (bool) $editProduct]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['edit-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editProduct),'parents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productFormCategories),'all-product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($parents),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategories),'subcategories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSubcategories),'clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClients),'can-create-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateProductCategory),'product-image-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productImagePreview),'client-availability-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientAvailabilityMode),'client-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientIds),'product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSupplierId),'certificate-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCertificateUpload),'template-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productTemplateUpload),'remove-certificate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removeProductCertificate),'remove-template' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removeProductTemplate),'category-creator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryCreator),'selected-main-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productFormMainCategory),'selected-product-category-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($parentId),'selected-subcategory' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSubcategory),'price-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productPricePreview),'remote-surcharge-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productRemoteSurchargePreview),'product-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productOptions),'product-option-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productOptionUploads),'shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableProductShipmentUrgencies),'product-shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencies),'shipment-urgency-picker-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencyPickerOpen),'shipment-urgency-picker-selection' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencyPickerSelection),'new-product-category-main' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newProductCategoryMain),'new-subcategory-product-category-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newSubcategoryProductCategoryId),'taxonomy-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productTaxonomyReady || (bool) $editProduct),'shipment-options-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentOptionsReady || (bool) $editProduct)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalacd0e783f63677472d00819cf224a2a3)): ?>
<?php $attributes = $__attributesOriginalacd0e783f63677472d00819cf224a2a3; ?>
<?php unset($__attributesOriginalacd0e783f63677472d00819cf224a2a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalacd0e783f63677472d00819cf224a2a3)): ?>
<?php $component = $__componentOriginalacd0e783f63677472d00819cf224a2a3; ?>
<?php unset($__componentOriginalacd0e783f63677472d00819cf224a2a3); ?>
<?php endif; ?>
        <?php else: ?>
        <div class="ft-product-list-head">
            <div>
                <h1>Products</h1>
                <p>Manage the product catalog, client availability and supporting documents.</p>
            </div>
            <div class="ft-product-list-head-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditMaster): ?>
                    <button type="button" class="ft-product-page-btn is-secondary ft-product-supplier-filter-btn" wire:click="showProductsWithoutSupplier">Show products without supplier</button>
                    <button type="button" class="ft-product-page-btn is-primary ft-product-assign-supplier-btn" wire:click="openProductSupplierAssignment" <?php if($productSelectionCount < 1): echo 'disabled'; endif; ?>>Assign supplier</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateMaster): ?>
                    <button type="button" class="ft-product-add-button" wire:click="open">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Add product</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash success ft-master-flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['record'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error ft-master-flash"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ft-product-list-shell" x-data="{}">
            <div class="ft-product-filter-card">
                <label class="ft-product-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search product name, product code or reference code" aria-label="Search products">
                </label>

                <?php
                    $productMainCategoryListOptions = collect($productMainCategoryFilterOptions)->map(fn ($mainCategory) => [
                        'id' => (string) $mainCategory->name,
                        'label' => (string) $mainCategory->name,
                        'meta' => (string) ($mainCategory->code ?? ''),
                    ])->values();
                    $productCategoryListOptions = collect($productCategories)->map(fn ($category) => [
                        'id' => (string) $category->id,
                        'label' => (string) $category->name,
                        'meta' => (string) ($category->code ?? ''),
                    ])->values();
                    $productClientAvailabilityListOptions = collect([
                        ['id' => 'all', 'label' => 'All clients'],
                        ['id' => 'specific', 'label' => 'Specific clients'],
                    ]);
                    $productStatusListOptions = collect([
                        ['id' => 'active', 'label' => 'Active'],
                        ['id' => 'inactive', 'label' => 'Inactive'],
                    ]);
                ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-list-filter','label' => 'Main category','property' => 'productMainCategory','value' => $productMainCategory,'placeholder' => 'All main categories','options' => $productMainCategoryListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'searchPlaceholder' => 'Search main category…','footerMessage' => 'Options shown instantly. Type to search.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Main category','property' => 'productMainCategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategory),'placeholder' => 'All main categories','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategoryListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'search-placeholder' => 'Search main category…','footer-message' => 'Options shown instantly. Type to search.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-list-filter','label' => 'Product category','property' => 'productCategory','value' => $productCategory,'placeholder' => 'All product categories','options' => $productCategoryListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'searchPlaceholder' => 'Search product category…','footerMessage' => 'Options shown instantly. Type to search.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Product category','property' => 'productCategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategory),'placeholder' => 'All product categories','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategoryListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'search-placeholder' => 'Search product category…','footer-message' => 'Options shown instantly. Type to search.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-list-filter','label' => 'Client availability','property' => 'productClientAvailability','value' => $productClientAvailability,'placeholder' => 'All client availability','options' => $productClientAvailabilityListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 280,'searchPlaceholder' => 'Search client availability…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Client availability','property' => 'productClientAvailability','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientAvailability),'placeholder' => 'All client availability','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientAvailabilityListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 280,'search-placeholder' => 'Search client availability…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-list-filter','label' => 'Status','property' => 'productStatus','value' => $productStatus,'placeholder' => 'All statuses','options' => $productStatusListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 240,'searchPlaceholder' => 'Search status…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Status','property' => 'productStatus','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productStatus),'placeholder' => 'All statuses','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productStatusListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 240,'search-placeholder' => 'Search status…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <button type="button" class="ft-product-clear" wire:click="clearProductFilters">Clear</button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productSupplierState === 'unassigned'): ?>
                <div class="ft-product-supplier-filter-state">
                    <span>Showing products without a supplier</span>
                    <button type="button" wire:click="clearProductFilters">Clear</button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$recordsReady): ?>
                <?php echo $__env->make('livewire.shared.table-rows-placeholder', ['columns' => 9, 'rows' => 8], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <?php
                    $pageProductIds = $rows->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
                    $selectedProductIdSet = collect($selectedProductIds)->map(fn ($id) => (int) $id);
                    $excludedProductIdSet = collect($excludedProductIds)->map(fn ($id) => (int) $id);
                    $allPageProductsSelected = count($pageProductIds) > 0 && collect($pageProductIds)->every(
                        fn ($id) => $selectAllMatchingProducts ? !$excludedProductIdSet->contains($id) : $selectedProductIdSet->contains($id)
                    );
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productSelectionCount > 0): ?>
                    <?php if (isset($component)) { $__componentOriginal384787add95d518db9c85a9a4a570b75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal384787add95d518db9c85a9a4a570b75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.bulk-actions','data' => ['count' => $productSelectionCount,'matchingTotal' => $rows->total(),'allMatchingSelected' => $selectAllMatchingProducts && empty($excludedProductIds),'canEdit' => $canEditMaster,'canDelete' => $canDeleteMaster]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.bulk-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSelectionCount),'matching-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rows->total()),'all-matching-selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectAllMatchingProducts && empty($excludedProductIds)),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteMaster)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal384787add95d518db9c85a9a4a570b75)): ?>
<?php $attributes = $__attributesOriginal384787add95d518db9c85a9a4a570b75; ?>
<?php unset($__attributesOriginal384787add95d518db9c85a9a4a570b75); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal384787add95d518db9c85a9a4a570b75)): ?>
<?php $component = $__componentOriginal384787add95d518db9c85a9a4a570b75; ?>
<?php unset($__componentOriginal384787add95d518db9c85a9a4a570b75); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ft-product-table-card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-catalog-'.e($productMainCategory).'-'.e($productCategory).'-'.e($productClientAvailability).'-'.e($productStatus).'-'.e($productSupplierState).'-'.e($productSupplierFilterId).'-'.e($productPerPage).''; ?>wire:key="product-catalog-<?php echo e($productMainCategory); ?>-<?php echo e($productCategory); ?>-<?php echo e($productClientAvailability); ?>-<?php echo e($productStatus); ?>-<?php echo e($productSupplierState); ?>-<?php echo e($productSupplierFilterId); ?>-<?php echo e($productPerPage); ?>">
                    <div class="ft-product-table-scroll">
                        <table class="ft-product-list-table">
                            <thead>
                                <tr>
                                    <th class="ft-product-checkbox-cell">
                                        <input
                                            type="checkbox"
                                            aria-label="Select all products on this page"
                                            <?php if($allPageProductsSelected): echo 'checked'; endif; ?>
                                            x-on:change="$wire.toggleProductPageSelection(<?php echo \Illuminate\Support\Js::from($pageProductIds)->toHtml() ?>, $event.target.checked)"
                                        >
                                    </th>
                                    <th>Product</th>
                                    <th>Product code</th>
                                    <th>Classification</th>
                                    <th>Supplier</th>
                                    <th>Size</th>
                                    <th>Availability</th>
                                    <th>Documents</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th class="ft-product-actions-heading" aria-label="Actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $updatedAt = $r->updated_at?->copy()->timezone($displayTimezone);
                                    $updatedLabel = !$updatedAt
                                        ? '—'
                                        : ($updatedAt->isToday() ? $updatedAt->diffForHumans() : ($updatedAt->isYesterday() ? '1 day ago' : $updatedAt->diffForHumans()));
                                    $documents = $r->productDocuments();
                                    $classificationPath = $r->productClassificationPath();
                                    $isProductSelected = $selectAllMatchingProducts
                                        ? !$excludedProductIdSet->contains((int) $r->id)
                                        : $selectedProductIdSet->contains((int) $r->id);
                                ?>
                                <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-list-row-'.e($r->id).''; ?>wire:key="product-list-row-<?php echo e($r->id); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-selected' => $isProductSelected]); ?>">
                                    <td class="ft-product-checkbox-cell"><input type="checkbox" value="<?php echo e($r->id); ?>" aria-label="Select <?php echo e($r->name); ?>" <?php if($isProductSelected): echo 'checked'; endif; ?> wire:change="toggleProductSelection(<?php echo e($r->id); ?>)"></td>
                                    <td>
                                        <div class="ft-product-name-cell">
                                            <div class="ft-product-list-thumb">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->productImageUrl()): ?>
                                                    <img src="<?php echo e($r->productImageUrl()); ?>" alt="<?php echo e($r->name); ?>">
                                                <?php else: ?>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <a
                                                class="ft-product-name-link"
                                                href="<?php echo e(route('master-data', ['group' => 'product', 'open' => $r->id])); ?>"
                                                wire:navigate
                                                title="Open <?php echo e($r->name); ?> details"
                                            ><?php echo e($r->name); ?></a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ft-product-code-cell">
                                            <strong><?php echo e($r->productDisplayCode()); ?></strong>
                                            <span>Ref: <?php echo e($r->productReferenceCode() ?: '—'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ft-product-classification">
                                            <strong><?php echo e($r->productMainCategory() ?: '—'); ?></strong>
                                            <span><?php echo e($classificationPath ?: '—'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (isset($component)) { $__componentOriginale33e2de7ceca966a28c65194a973b0fe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale33e2de7ceca966a28c65194a973b0fe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.product-supplier-links','data' => ['suppliers' => $productSuppliersByProduct->get((int) $r->id, collect()),'defaultSupplierId' => $r->productSupplierId()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.product-supplier-links'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSuppliersByProduct->get((int) $r->id, collect())),'default-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->productSupplierId())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale33e2de7ceca966a28c65194a973b0fe)): ?>
<?php $attributes = $__attributesOriginale33e2de7ceca966a28c65194a973b0fe; ?>
<?php unset($__attributesOriginale33e2de7ceca966a28c65194a973b0fe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale33e2de7ceca966a28c65194a973b0fe)): ?>
<?php $component = $__componentOriginale33e2de7ceca966a28c65194a973b0fe; ?>
<?php unset($__componentOriginale33e2de7ceca966a28c65194a973b0fe); ?>
<?php endif; ?>
                                    </td>
                                    <td><?php if (isset($component)) { $__componentOriginalfbc29ed9079800fe5a80c54a7a1e4eca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbc29ed9079800fe5a80c54a7a1e4eca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-size','data' => ['value' => $r->productSize()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-size'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->productSize())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbc29ed9079800fe5a80c54a7a1e4eca)): ?>
<?php $attributes = $__attributesOriginalfbc29ed9079800fe5a80c54a7a1e4eca; ?>
<?php unset($__attributesOriginalfbc29ed9079800fe5a80c54a7a1e4eca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbc29ed9079800fe5a80c54a7a1e4eca)): ?>
<?php $component = $__componentOriginalfbc29ed9079800fe5a80c54a7a1e4eca; ?>
<?php unset($__componentOriginalfbc29ed9079800fe5a80c54a7a1e4eca); ?>
<?php endif; ?></td>
                                    <td><?php if (isset($component)) { $__componentOriginal49c108d40689ea76dfb1d69157bbf32d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c108d40689ea76dfb1d69157bbf32d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.availability','data' => ['labels' => $r->productAvailabilityLabels()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.availability'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->productAvailabilityLabels())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49c108d40689ea76dfb1d69157bbf32d)): ?>
<?php $attributes = $__attributesOriginal49c108d40689ea76dfb1d69157bbf32d; ?>
<?php unset($__attributesOriginal49c108d40689ea76dfb1d69157bbf32d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49c108d40689ea76dfb1d69157bbf32d)): ?>
<?php $component = $__componentOriginal49c108d40689ea76dfb1d69157bbf32d; ?>
<?php unset($__componentOriginal49c108d40689ea76dfb1d69157bbf32d); ?>
<?php endif; ?></td>
                                    <td>
                                        <div class="ft-product-documents">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($documents)): ?>
                                                <div class="ft-product-document-count">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M8.5 13h7M8.5 17h5"/></svg>
                                                    <span><?php echo e(count($documents)); ?> <?php echo e(\Illuminate\Support\Str::plural('file', count($documents))); ?></span>
                                                </div>
                                                <small title="<?php echo e($documents[0]['label']); ?>"><?php echo e(\Illuminate\Support\Str::limit($documents[0]['label'], 18)); ?></small>
                                            <?php else: ?>
                                                <span class="ft-product-documents-empty">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php if (isset($component)) { $__componentOriginal18c3afe41550a8e1c941be61b2a6df77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c3afe41550a8e1c941be61b2a6df77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.status','data' => ['active' => $r->status === 'active']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status === 'active')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c3afe41550a8e1c941be61b2a6df77)): ?>
<?php $attributes = $__attributesOriginal18c3afe41550a8e1c941be61b2a6df77; ?>
<?php unset($__attributesOriginal18c3afe41550a8e1c941be61b2a6df77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c3afe41550a8e1c941be61b2a6df77)): ?>
<?php $component = $__componentOriginal18c3afe41550a8e1c941be61b2a6df77; ?>
<?php unset($__componentOriginal18c3afe41550a8e1c941be61b2a6df77); ?>
<?php endif; ?></td>
                                    <td><span class="ft-product-updated" title="<?php echo e($updatedAt?->format('M j, Y g:i A')); ?> <?php echo e($displayTimezone); ?>"><?php echo e($updatedLabel); ?></span></td>
                                    <td class="ft-product-actions-cell">
                                        <?php if (isset($component)) { $__componentOriginal52718073bb91d39800d9980236e22c53 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal52718073bb91d39800d9980236e22c53 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.action-menu','data' => ['productId' => $r->id,'isActive' => $r->status === 'active','canEdit' => $canEditMaster,'canDelete' => $canDeleteMaster]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.action-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->id),'is-active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status === 'active'),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteMaster)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal52718073bb91d39800d9980236e22c53)): ?>
<?php $attributes = $__attributesOriginal52718073bb91d39800d9980236e22c53; ?>
<?php unset($__attributesOriginal52718073bb91d39800d9980236e22c53); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal52718073bb91d39800d9980236e22c53)): ?>
<?php $component = $__componentOriginal52718073bb91d39800d9980236e22c53; ?>
<?php unset($__componentOriginal52718073bb91d39800d9980236e22c53); ?>
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="11"><div class="empty-state">No products found.</div></td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                        $lastPage = max(1, $rows->lastPage());
                        $currentPage = $rows->currentPage();
                        $pageStart = max(1, min($currentPage - 1, max(1, $lastPage - 2)));
                        $pageEnd = min($lastPage, $pageStart + 2);
                    ?>
                    <div class="ft-product-pagination">
                        <div class="ft-product-pagination-left">
                            <div class="ft-product-result-count ft-product-result-count-footer">
                                Showing <?php echo e($rows->firstItem() ?? 0); ?>–<?php echo e($rows->lastItem() ?? 0); ?> of <?php echo e(number_format($rows->total())); ?> products
                            </div>
                            <div class="ft-product-rows-per-page">
                                <span>Rows per page</span>
                                <?php if (isset($component)) { $__componentOriginalb50ed84adea6cd7ed421d4754d7b0b04 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb50ed84adea6cd7ed421d4754d7b0b04 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.filter-select','data' => ['model' => 'productPerPage','label' => 'Rows per page']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.filter-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'productPerPage','label' => 'Rows per page']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb50ed84adea6cd7ed421d4754d7b0b04)): ?>
<?php $attributes = $__attributesOriginalb50ed84adea6cd7ed421d4754d7b0b04; ?>
<?php unset($__attributesOriginalb50ed84adea6cd7ed421d4754d7b0b04); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb50ed84adea6cd7ed421d4754d7b0b04)): ?>
<?php $component = $__componentOriginalb50ed84adea6cd7ed421d4754d7b0b04; ?>
<?php unset($__componentOriginalb50ed84adea6cd7ed421d4754d7b0b04); ?>
<?php endif; ?>
                            </div>
                        </div>
                        <div class="ft-product-page-position">Page <?php echo e($currentPage); ?> of <?php echo e($lastPage); ?></div>
                        <div class="ft-product-page-buttons">
                            <button type="button" wire:click="previousPage('masterPage')" <?php if($rows->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button
                                    type="button"
                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-current' => $pageNumber === $currentPage]); ?>"
                                    wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'masterPage')"
                                    aria-label="Go to page <?php echo e($pageNumber); ?>"
                                    <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>
                                ><?php echo e($pageNumber); ?></button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <button type="button" wire:click="nextPage('masterPage')" <?php if(!$rows->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bulkProductPanel === 'supplier'): ?>
                    <?php if (isset($component)) { $__componentOriginal7c9a9a13c0eb259b948275065272dd6c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c9a9a13c0eb259b948275065272dd6c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.assign-products-modal','data' => ['suppliers' => $bulkProductSupplierOptions,'productCounts' => $bulkProductSupplierProductCounts,'selectedSupplierId' => $bulkProductSupplierId,'selectionCount' => $productSelectionCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.assign-products-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSupplierOptions),'product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSupplierProductCounts),'selected-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSupplierId),'selection-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSelectionCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c9a9a13c0eb259b948275065272dd6c)): ?>
<?php $attributes = $__attributesOriginal7c9a9a13c0eb259b948275065272dd6c; ?>
<?php unset($__attributesOriginal7c9a9a13c0eb259b948275065272dd6c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c9a9a13c0eb259b948275065272dd6c)): ?>
<?php $component = $__componentOriginal7c9a9a13c0eb259b948275065272dd6c; ?>
<?php unset($__componentOriginal7c9a9a13c0eb259b948275065272dd6c); ?>
<?php endif; ?>
                <?php elseif($bulkProductPanel === 'clients'): ?>
                    <?php if (isset($component)) { $__componentOriginal5835968f22246c90d00dd620e450bc3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5835968f22246c90d00dd620e450bc3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.bulk-modal','data' => ['title' => 'Assign clients','subtitle' => 'Choose who can find and use the selected products.','saveLabel' => 'Assign clients','saveAction' => 'applyBulkProductClients']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.bulk-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Assign clients','subtitle' => 'Choose who can find and use the selected products.','save-label' => 'Assign clients','save-action' => 'applyBulkProductClients']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <div class="ft-product-bulk-radio-row">
                            <label><input type="radio" wire:model.live="bulkProductClientMode" value="all"> <span>All clients</span></label>
                            <label><input type="radio" wire:model.live="bulkProductClientMode" value="specific"> <span>Selected clients</span></label>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bulkProductClientMode === 'specific'): ?>
                            <div class="ft-product-bulk-client-picker" x-data="{ q: '' }">
                                <label>Available clients</label>
                                <input type="search" x-model="q" placeholder="Search clients…" autocomplete="off">
                                <div class="ft-product-bulk-client-list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php $bulkClientSelected = in_array((int)$client->id, collect($bulkProductClientIds)->map(fn($v)=>(int)$v)->all(), true); ?>
                                        <button type="button"
                                            x-show="!q || <?php echo \Illuminate\Support\Js::from(mb_strtolower($client->name.' '.$client->code))->toHtml() ?>.includes(q.toLowerCase())"
                                            wire:click="toggleBulkProductClient(<?php echo e($client->id); ?>)"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-selected' => $bulkClientSelected]); ?>">
                                            <span><?php echo e($client->name); ?></span><small><?php echo e($client->code); ?></small><b><?php echo e($bulkClientSelected ? '✓' : ''); ?></b>
                                        </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bulkProductClientIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="ft-product-bulk-note">All clients will be able to find and use these products.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5835968f22246c90d00dd620e450bc3f)): ?>
<?php $attributes = $__attributesOriginal5835968f22246c90d00dd620e450bc3f; ?>
<?php unset($__attributesOriginal5835968f22246c90d00dd620e450bc3f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5835968f22246c90d00dd620e450bc3f)): ?>
<?php $component = $__componentOriginal5835968f22246c90d00dd620e450bc3f; ?>
<?php unset($__componentOriginal5835968f22246c90d00dd620e450bc3f); ?>
<?php endif; ?>
                <?php elseif($bulkProductPanel === 'category'): ?>
                    <?php if (isset($component)) { $__componentOriginal5835968f22246c90d00dd620e450bc3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5835968f22246c90d00dd620e450bc3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.bulk-modal','data' => ['title' => 'Change category','subtitle' => 'Move the selected products to a new category hierarchy.','saveLabel' => 'Change category','saveAction' => 'applyBulkProductCategory']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.bulk-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Change category','subtitle' => 'Move the selected products to a new category hierarchy.','save-label' => 'Change category','save-action' => 'applyBulkProductCategory']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <div class="ft-product-bulk-category-grid">
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Main category','property' => 'bulkProductMainCategory','value' => $bulkProductMainCategory,'placeholder' => 'Select main category','options' => $productMainCategories,'clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 360,'searchPlaceholder' => 'Search main category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Main category','property' => 'bulkProductMainCategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductMainCategory),'placeholder' => 'Select main category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategories),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 360,'search-placeholder' => 'Search main category…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Product category','property' => 'bulkProductCategoryId','value' => $bulkProductCategoryId,'placeholder' => trim($bulkProductMainCategory) === '' ? 'Select main category first' : 'Select product category','options' => $bulkProductCategories,'disabled' => trim($bulkProductMainCategory) === '','clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search product category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Product category','property' => 'bulkProductCategoryId','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductCategoryId),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim($bulkProductMainCategory) === '' ? 'Select main category first' : 'Select product category'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductCategories),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim($bulkProductMainCategory) === ''),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search product category…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Subcategory','property' => 'bulkProductSubcategory','value' => $bulkProductSubcategory,'placeholder' => $bulkProductCategoryId ? 'No subcategory' : 'Select product category first','options' => $bulkProductSubcategories,'disabled' => !$bulkProductCategoryId,'clearable' => true,'optional' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search subcategory…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Subcategory','property' => 'bulkProductSubcategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSubcategory),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductCategoryId ? 'No subcategory' : 'Select product category first'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSubcategories),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$bulkProductCategoryId),'clearable' => true,'optional' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search subcategory…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bulkProductMainCategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bulkProductCategoryId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5835968f22246c90d00dd620e450bc3f)): ?>
<?php $attributes = $__attributesOriginal5835968f22246c90d00dd620e450bc3f; ?>
<?php unset($__attributesOriginal5835968f22246c90d00dd620e450bc3f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5835968f22246c90d00dd620e450bc3f)): ?>
<?php $component = $__componentOriginal5835968f22246c90d00dd620e450bc3f; ?>
<?php unset($__componentOriginal5835968f22246c90d00dd620e450bc3f); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/product.blade.php ENDPATH**/ ?>