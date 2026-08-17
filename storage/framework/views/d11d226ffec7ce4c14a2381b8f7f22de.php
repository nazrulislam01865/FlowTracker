<div class="ft-master-page" wire:init="loadMasterRecords">
    
    <?php
        $hasParent = in_array($group, ['product', 'state'], true);
        $hasColor = in_array($group, \App\Services\MasterDataService::COLOR_TYPES, true);
        $columnCount = 6 + ($hasParent ? 1 : 0) + ($hasColor ? 1 : 0) + ($group === 'inquiry_task_status' ? 2 : 0) + (in_array($group, ['order_task_status', 'order_task_flag'], true) ? 1 : 0);
        $colorUsageLabel = match ($group) {
            'task_status' => 'legacy task status',
            'task_flag' => 'legacy task flag',
            'order_task_status' => 'order task status',
            'order_task_flag' => 'order task flag',
            'order_flag' => 'order flag',
            'priority' => 'priority',
            'inquiry_task_status' => 'inquiry task status',
            default => 'master data',
        };
        $permissionModule = \App\Services\MasterDataService::permissionModuleForType($group);
        $canCreateMaster = auth()->user()->canModule($permissionModule, 'create');
        $canEditMaster = auth()->user()->canModule($permissionModule, 'edit');
        $canDeleteMaster = auth()->user()->canModule($permissionModule, 'delete');
        $canCreateProductCategory = auth()->user()->canModule('product_categories', 'create');
        $catalogueGroup = in_array($group, ['product', 'product_category', 'supplier'], true);
        $financialGroup = in_array($group, \App\Services\MasterDataService::FINANCIAL_TYPES, true);
        $masterSectionLabel = $catalogueGroup ? 'Catalogue' : ($financialGroup ? 'Financial Master Data' : 'Master Data');
        $pageTitle = $labels[$group] ?? 'Master Data';
        $singularLabel = match ($group) {
            'product' => 'product',
            'product_category' => 'category',
            'document_category' => 'document category',
            'production_unit' => 'production unit',
            'shipment_method' => 'shipment method',
            'task_status' => 'legacy task status',
            'inquiry_task_status' => 'inquiry task status',
            'task_flag' => 'legacy task flag',
            'order_task_status' => 'order task status',
            'order_task_flag' => 'order task flag',
            'order_flag' => 'order flag',
            default => strtolower(\Illuminate\Support\Str::singular($pageTitle)),
        };
        $displayTimezone = app(\App\Services\WorkspaceSettingsService::class)->displayTimezone();
        $pageSubtitle = match ($group) {
            'product' => 'Manage the product catalogue used in Inquiries and Orders.',
            'product_category' => 'Manage the categories used to organise products across FlowTrack.',
            'department' => 'Maintain departments used for assignment, routing and task ownership.',
            'supplier' => 'Maintain supplier values available throughout Order processing.',
            'production_unit' => 'Maintain the production units used by workflows and operations.',
            'shipment_method' => 'Maintain shipment methods available for orders and deliveries.',
            'currency' => 'Maintain currencies available for clients, orders, invoices and payments.',
            'received_account' => 'Maintain the receiving accounts available when recording customer payments.',
            'payment_method' => 'Maintain payment methods available when recording payments.',
            'payment_term' => 'Maintain payment terms available for invoices and client finance settings.',
            'invoice_type' => 'Maintain invoice types available when creating invoices.',
            'country' => 'Maintain countries used by client and address records.',
            'state' => 'Maintain states and their parent countries.',
            'phone_country_code' => 'Maintain searchable international phone codes used on shipping and contact forms.',
            'document_category' => 'Maintain document categories used across uploads and workflows.',
            'priority' => 'Maintain priority levels and the colours used throughout FlowTrack.',
            'task_status' => 'Legacy task statuses retained for compatibility. New Order tasks use Order Task Statuses.',
            'inquiry_task_status' => 'Maintain Inquiry task statuses, their automatic Inquiry status mapping, attention flag rule and display colours.',
            'task_flag' => 'Legacy task flags retained for compatibility. New Order task flags are separate.',
            'order_task_status' => 'Maintain Order task statuses and choose which Order Task Flag each status applies automatically.',
            'order_task_flag' => 'Maintain Order task flags and map each one to the Order Flag that should appear on the parent Order.',
            'order_flag' => 'Maintain the separate Order-level flags shown on Order lists and details.',
            default => 'Maintain values used throughout FlowTrack.',
        };
        $productImagePreview = null;
        if ($group === 'product' && $productImage) {
            try { $productImagePreview = $productImage->temporaryUrl(); } catch (\Throwable $exception) { $productImagePreview = null; }
        }
        if (!$productImagePreview && !$removeProductImage && $existingProductImageUrl) {
            $productImagePreview = $existingProductImageUrl;
        }
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'product'): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showProductView && $viewProduct): ?>
            <?php if (isset($component)) { $__componentOriginal9f6a7ffdac90566f77a8f63e0bb16612 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f6a7ffdac90566f77a8f63e0bb16612 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-view','data' => ['product' => $viewProduct,'canEdit' => $canEditMaster,'canDelete' => $canDeleteMaster,'displayTimezone' => $displayTimezone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-view'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($viewProduct),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteMaster),'display-timezone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayTimezone)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-form','data' => ['editProduct' => $editProduct,'parents' => $productFormCategories,'allProductCategories' => $parents,'mainCategories' => $productMainCategories,'subcategories' => $productSubcategories,'clients' => $productClients,'canCreateProductCategory' => $canCreateProductCategory,'productImagePreview' => $productImagePreview,'clientAvailabilityMode' => $productClientAvailabilityMode,'clientIds' => $productClientIds,'certificateUpload' => $productCertificateUpload,'templateUpload' => $productTemplateUpload,'removeCertificate' => $removeProductCertificate,'removeTemplate' => $removeProductTemplate,'categoryCreator' => $categoryCreator,'selectedMainCategory' => $productFormMainCategory,'selectedProductCategoryId' => $parentId,'selectedSubcategory' => $productSubcategory,'pricePreview' => $productPricePreview,'remoteSurchargePreview' => $productRemoteSurchargePreview,'productOptions' => $productOptions,'productOptionUploads' => $productOptionUploads,'shipmentUrgencies' => $availableProductShipmentUrgencies,'productShipmentUrgencies' => $productShipmentUrgencies,'shipmentUrgencyPickerOpen' => $productShipmentUrgencyPickerOpen,'shipmentUrgencyPickerSelection' => $productShipmentUrgencyPickerSelection,'newProductCategoryMain' => $newProductCategoryMain,'newSubcategoryProductCategoryId' => $newSubcategoryProductCategoryId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['edit-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editProduct),'parents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productFormCategories),'all-product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($parents),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategories),'subcategories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSubcategories),'clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClients),'can-create-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateProductCategory),'product-image-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productImagePreview),'client-availability-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientAvailabilityMode),'client-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientIds),'certificate-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCertificateUpload),'template-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productTemplateUpload),'remove-certificate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removeProductCertificate),'remove-template' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removeProductTemplate),'category-creator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryCreator),'selected-main-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productFormMainCategory),'selected-product-category-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($parentId),'selected-subcategory' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSubcategory),'price-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productPricePreview),'remote-surcharge-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productRemoteSurchargePreview),'product-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productOptions),'product-option-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productOptionUploads),'shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableProductShipmentUrgencies),'product-shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencies),'shipment-urgency-picker-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencyPickerOpen),'shipment-urgency-picker-selection' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencyPickerSelection),'new-product-category-main' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newProductCategoryMain),'new-subcategory-product-category-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newSubcategoryProductCategoryId)]); ?>
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

                <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-list-filter','label' => 'Main category','property' => 'productMainCategory','value' => $productMainCategory,'placeholder' => 'All main categories','options' => $productMainCategoryListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'searchPlaceholder' => 'Search main category…','footerMessage' => 'Options shown instantly. Type to search.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Main category','property' => 'productMainCategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategory),'placeholder' => 'All main categories','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategoryListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'search-placeholder' => 'Search main category…','footer-message' => 'Options shown instantly. Type to search.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-list-filter','label' => 'Product category','property' => 'productCategory','value' => $productCategory,'placeholder' => 'All product categories','options' => $productCategoryListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'searchPlaceholder' => 'Search product category…','footerMessage' => 'Options shown instantly. Type to search.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Product category','property' => 'productCategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategory),'placeholder' => 'All product categories','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategoryListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'search-placeholder' => 'Search product category…','footer-message' => 'Options shown instantly. Type to search.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-list-filter','label' => 'Client availability','property' => 'productClientAvailability','value' => $productClientAvailability,'placeholder' => 'All client availability','options' => $productClientAvailabilityListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 280,'searchPlaceholder' => 'Search client availability…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Client availability','property' => 'productClientAvailability','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientAvailability),'placeholder' => 'All client availability','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productClientAvailabilityListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 280,'search-placeholder' => 'Search client availability…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-list-filter','label' => 'Status','property' => 'productStatus','value' => $productStatus,'placeholder' => 'All statuses','options' => $productStatusListOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 240,'searchPlaceholder' => 'Search status…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-list-filter','label' => 'Status','property' => 'productStatus','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productStatus),'placeholder' => 'All statuses','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productStatusListOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 240,'search-placeholder' => 'Search status…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>

                <button type="button" class="ft-product-clear" wire:click="clearProductFilters">Clear</button>
            </div>

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

                <div class="ft-product-table-card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-catalog-'.e($productMainCategory).'-'.e($productCategory).'-'.e($productClientAvailability).'-'.e($productStatus).'-'.e($productPerPage).''; ?>wire:key="product-catalog-<?php echo e($productMainCategory); ?>-<?php echo e($productCategory); ?>-<?php echo e($productClientAvailability); ?>-<?php echo e($productStatus); ?>-<?php echo e($productPerPage); ?>">
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
                                <tr><td colspan="10"><div class="empty-state">No products found.</div></td></tr>
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

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bulkProductPanel === 'clients'): ?>
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
                            <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Main category','property' => 'bulkProductMainCategory','value' => $bulkProductMainCategory,'placeholder' => 'Select main category','options' => $productMainCategories,'clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 360,'searchPlaceholder' => 'Search main category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Main category','property' => 'bulkProductMainCategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductMainCategory),'placeholder' => 'Select main category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productMainCategories),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 360,'search-placeholder' => 'Search main category…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Product category','property' => 'bulkProductCategoryId','value' => $bulkProductCategoryId,'placeholder' => trim($bulkProductMainCategory) === '' ? 'Select main category first' : 'Select product category','options' => $bulkProductCategories,'disabled' => trim($bulkProductMainCategory) === '','clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search product category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Product category','property' => 'bulkProductCategoryId','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductCategoryId),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim($bulkProductMainCategory) === '' ? 'Select main category first' : 'Select product category'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductCategories),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim($bulkProductMainCategory) === ''),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search product category…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Subcategory','property' => 'bulkProductSubcategory','value' => $bulkProductSubcategory,'placeholder' => $bulkProductCategoryId ? 'No subcategory' : 'Select product category first','options' => $bulkProductSubcategories,'disabled' => !$bulkProductCategoryId,'clearable' => true,'optional' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search subcategory…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Subcategory','property' => 'bulkProductSubcategory','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSubcategory),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductCategoryId ? 'No subcategory' : 'Select product category first'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bulkProductSubcategories),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$bulkProductCategoryId),'clearable' => true,'optional' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search subcategory…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
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
    <?php elseif($group === 'product_category'): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$recordsReady): ?>
            <?php echo $__env->make('livewire.shared.table-rows-placeholder', ['columns' => 9, 'rows' => 8], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-list','data' => ['mainPage' => $categoryMainPage,'productChildren' => $categoryProductChildren,'subcategoryChildren' => $categorySubcategoryChildren,'mainCategories' => $categoryMainCategories,'productCategories' => $categoryProductCategories,'parentOptions' => $categoryParentOptions,'counts' => $categoryCounts,'productCounts' => $categoryProductCounts,'mainProductCounts' => $categoryMainProductCounts,'subcategoryProductCounts' => $categorySubcategoryProductCounts,'productChildTotals' => $categoryProductChildTotals,'subcategoryChildTotals' => $categorySubcategoryChildTotals,'expandedMainIds' => $expandedMainCategoryIds,'expandedProductIds' => $expandedProductCategoryIds,'canCreate' => $canCreateMaster,'canEdit' => $canEditMaster,'canDelete' => $canDeleteMaster,'displayTimezone' => $displayTimezone,'search' => $search,'levelFilter' => $categoryLevelFilter,'parentFilter' => $categoryParentFilter,'statusFilter' => $categoryStatusFilter,'perPage' => $categoryPerPage,'selectedCategoryKeys' => $selectedCategoryKeys,'selectionCount' => $categorySelectionCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['main-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainPage),'product-children' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductChildren),'subcategory-children' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySubcategoryChildren),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainCategories),'product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductCategories),'parent-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryParentOptions),'counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryCounts),'product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductCounts),'main-product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainProductCounts),'subcategory-product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySubcategoryProductCounts),'product-child-totals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductChildTotals),'subcategory-child-totals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySubcategoryChildTotals),'expanded-main-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedMainCategoryIds),'expanded-product-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedProductCategoryIds),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateMaster),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteMaster),'display-timezone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayTimezone),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'level-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryLevelFilter),'parent-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryParentFilter),'status-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryStatusFilter),'per-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryPerPage),'selected-category-keys' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedCategoryKeys),'selection-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySelectionCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2)): ?>
<?php $attributes = $__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2; ?>
<?php unset($__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2)): ?>
<?php $component = $__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2; ?>
<?php unset($__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCategoryDeleteConfirm): ?>
                <?php if (isset($component)) { $__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-delete-modal','data' => ['preview' => $categoryDeletePreview]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryDeletePreview)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5)): ?>
<?php $attributes = $__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5; ?>
<?php unset($__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5)): ?>
<?php $component = $__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5; ?>
<?php unset($__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <div class="ft-master-breadcrumb" aria-label="Breadcrumb">
            <span><?php echo e($masterSectionLabel); ?></span><i>/</i><strong><?php echo e($pageTitle); ?></strong>
        </div>

        <div class="ft-master-page-head">
            <div>
                <h1><?php echo e($pageTitle); ?></h1>
                <p><?php echo e($pageSubtitle); ?></p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateMaster): ?>
                <button type="button" class="primary ft-master-add-button" wire:click="open">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Add <?php echo e($singularLabel); ?></span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        <div class="ft-master-single-stat ft-master-generic-stat">
            <div class="ft-master-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v14H5zM8 9h8M8 13h8M8 17h5"/></svg>
            </div>
            <div class="ft-master-stat-copy">
                <span>Total <?php echo e(strtolower($pageTitle)); ?></span>
                <strong><?php echo e(number_format($selectedTotal)); ?></strong>
            </div>
            <small><?php echo e(number_format($selectedActive)); ?> active</small>
        </div>

        <section class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-master-generic-card', 'ft-master-supplier-card' => $group === 'supplier']); ?>">
            <div class="ft-master-generic-toolbar">
                <label class="ft-master-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search <?php echo e(strtolower($pageTitle)); ?>..." aria-label="Search <?php echo e(strtolower($pageTitle)); ?>">
                </label>
            </div>

            <div class="ft-master-product-count">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recordsReady && $rows): ?>
                    Showing <?php echo e($rows->firstItem() ?? 0); ?>–<?php echo e($rows->lastItem() ?? 0); ?> of <?php echo e(number_format($rows->total())); ?> records
                <?php else: ?>
                    Loading records…
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$recordsReady): ?>
                <?php echo $__env->make('livewire.shared.table-rows-placeholder', ['columns' => $columnCount, 'rows' => 8], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <div class="table-wrap ft-master-generic-table-wrap" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'master-records-'.e($group).''; ?>wire:key="master-records-<?php echo e($group); ?>">
                    <table class="<?php echo \Illuminate\Support\Arr::toCssClasses(['master-table', 'ft-master-generic-table', 'ft-master-supplier-table' => $group === 'supplier']); ?>">
                        <thead>
                            <tr>
                                <th>Sort order</th>
                                <th>Code</th>
                                <th><?php echo e($group === 'phone_country_code' ? 'Phone code' : 'Name'); ?></th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'inquiry_task_status'): ?><th>Inquiry status auto</th><th>Flag</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_status'): ?><th>Automatic task flag</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_flag'): ?><th>Order flag</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasParent): ?><th><?php echo e($group === 'state' ? 'Country' : 'Product Category'); ?></th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th>Description / Use</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasColor): ?><th>Color</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td class="ft-master-mobile-sort" data-label="Sort order"><?php echo e($r->sort_order); ?></td>
                                <td class="ft-master-mobile-code" data-label="Code"><strong class="ft-master-product-code"><?php echo e($r->code); ?></strong></td>
                                <td class="ft-master-mobile-name" data-label="<?php echo e($group === 'phone_country_code' ? 'Phone code' : 'Name'); ?>"><?php echo e($r->name); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'inquiry_task_status'): ?>
                                    <td class="ft-master-mobile-auto-status" data-label="Inquiry status auto"><strong><?php echo e($r->inquiryAutoStatus()); ?></strong></td>
                                    <td class="ft-master-mobile-flag" data-label="Flag">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->requiresAttention()): ?>
                                            <span class="ft-inquiry-status-rule-flag is-attention">Requires attention</span>
                                        <?php else: ?>
                                            <span class="ft-inquiry-status-rule-flag">Not needed</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_status'): ?>
                                    <?php $mappedTaskFlag = $orderTaskFlagOptions->firstWhere('id', $r->orderTaskFlagId()); ?>
                                    <td class="ft-master-mobile-flag" data-label="Automatic task flag">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mappedTaskFlag): ?>
                                            <span class="ft-inquiry-status-rule-flag is-attention" style="<?php echo e(\App\Support\MasterColor::style($mappedTaskFlag->color)); ?>"><?php echo e($mappedTaskFlag->name); ?></span>
                                        <?php else: ?>
                                            <span class="ft-inquiry-status-rule-flag">No flag</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_flag'): ?>
                                    <?php $mappedOrderFlag = $orderFlagOptions->firstWhere('id', $r->orderFlagId()); ?>
                                    <td class="ft-master-mobile-flag" data-label="Order flag">
                                        <strong><?php echo e($mappedOrderFlag?->name ?? 'Not mapped'); ?></strong>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasParent): ?><td class="ft-master-mobile-parent" data-label="<?php echo e($group === 'state' ? 'Country' : 'Product Category'); ?>"><?php echo e($r->parent?->name ?? '—'); ?></td><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <td class="ft-master-mobile-description" data-label="Description / Use"><?php echo e($r->description ?: '—'); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasColor): ?>
                                    <?php
                                        $rowColor = \App\Support\MasterColor::normalize($r->color) ?: \App\Support\MasterColor::defaultFor($group, $r->name);
                                    ?>
                                    <td class="ft-master-mobile-color" data-label="Color">
                                        <label class="ft-master-color-chip" style="<?php echo e(\App\Support\MasterColor::style($rowColor)); ?>" title="Choose color for <?php echo e($r->name); ?>">
                                            <input
                                                class="ft-master-inline-color"
                                                type="color"
                                                value="<?php echo e($rowColor); ?>"
                                                wire:change="updateColor(<?php echo e($r->id); ?>, $event.target.value)"
                                                wire:loading.attr="disabled"
                                                <?php if(!$canEditMaster): echo 'disabled'; endif; ?>
                                                aria-label="Choose color for <?php echo e($r->name); ?>"
                                            >
                                            <span><?php echo e($rowColor); ?></span>
                                        </label>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <td class="ft-master-mobile-status" data-label="Status"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['label' => $r->status === 'active' ? 'Active' : 'Inactive']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status === 'active' ? 'Active' : 'Inactive')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                                <td class="ft-master-mobile-actions" data-label="Actions">
                                    <div class="row-actions">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditMaster): ?>
                                            <button class="mini-btn" wire:click="open(<?php echo e($r->id); ?>)">Edit</button>
                                            <button class="mini-btn" wire:click="toggle(<?php echo e($r->id); ?>)"><?php echo e($r->status === 'active' ? 'Deactivate' : 'Activate'); ?></button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteMaster): ?>
                                            <button class="mini-btn" wire:click="deleteRecord(<?php echo e($r->id); ?>)" wire:confirm="Delete this master record?">Delete</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditMaster && !$canDeleteMaster): ?>
                                            <span class="small muted">View only</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-master-empty-row"><td colspan="<?php echo e($columnCount); ?>"><div class="empty-state">No records found.</div></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rows->total() > 30): ?>
                    <div class="ft-list-pagination ft-master-pagination">
                        <span>Showing <b><?php echo e($rows->firstItem() ?? 0); ?>–<?php echo e($rows->lastItem() ?? 0); ?></b> of <?php echo e($rows->total()); ?> records</span>
                        <div class="ft-page-actions">
                            <button type="button" wire:click="previousPage('masterPage')" <?php if($rows->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                            <span>Page <?php echo e($rows->currentPage()); ?> of <?php echo e($rows->lastPage()); ?></span>
                            <button type="button" wire:click="nextPage('masterPage')" <?php if(!$rows->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal && !in_array($group, ['product', 'product_category'], true)): ?>
        <div class="overlay livewire-overlay" wire:click.self="close"></div>
        <div class="modal livewire-modal ft-master-modal">
            <div class="modal-head">
                <div>
                    <h2><?php echo e($editId ? 'Edit' : 'Add'); ?> <?php echo e(ucfirst($singularLabel)); ?></h2>
                    <p><?php echo e($editId ? 'Update this master data record.' : 'Create a new '.$singularLabel.' for FlowTrack.'); ?></p>
                </div>
                <button class="close-btn" wire:click="close" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Code</label>
                        <div class="ft-admin-locked"><?php echo e($code); ?></div>
                        <small class="small muted"><?php echo e($editId ? 'System code is permanently locked.' : 'Automatically generated and permanently locked.'); ?></small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field">
                        <label><?php echo e($group === 'phone_country_code' ? 'Phone code *' : 'Name *'); ?></label>
                        <input wire:model="name" <?php if($group === 'phone_country_code'): ?> placeholder="e.g. +880" inputmode="tel" <?php endif; ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'inquiry_task_status'): ?>
                        <div class="field">
                            <label>Inquiry status auto *</label>
                            <select wire:model="autoInquiryStatus">
                                <option value="To do">To do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="__task_status__">Use task status name</option>
                            </select>
                            <small class="small muted">This value automatically becomes the parent Inquiry status while this task is current.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['autoInquiryStatus'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="field">
                            <label>Attention flag</label>
                            <select wire:model.boolean="requiresAttention">
                                <option value="0">Not needed</option>
                                <option value="1">Requires attention</option>
                            </select>
                            <small class="small muted">When enabled, the task shows an Attention required link and asks for a reason.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['requiresAttention'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_status'): ?>
                        <div class="field">
                            <label>Automatic Order Task Flag</label>
                            <select wire:model="orderTaskFlagId">
                                <option value="">No flag</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orderTaskFlagOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($flag->id); ?>"><?php echo e($flag->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <small class="small muted">When a task uses this status, this flag is applied automatically. An overdue due date overrides this mapping with the system Overdue flag.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderTaskFlagId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_flag'): ?>
                        <div class="field">
                            <label>Parent Order Flag *</label>
                            <select wire:model="orderFlagId">
                                <option value="">Select order flag</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orderFlagOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($flag->id); ?>"><?php echo e($flag->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <small class="small muted">When this task flag is active, the mapped Order Flag is stored on the parent Order.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderFlagId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'product'): ?>
                        <div class="field full ft-master-product-image-field">
                            <label>Product image</label>
                            <div class="ft-master-product-image-editor">
                                <div class="ft-master-product-image-preview">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productImagePreview): ?>
                                        <img src="<?php echo e($productImagePreview); ?>" alt="Product image preview">
                                    <?php else: ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ft-master-product-image-actions">
                                    <label class="ft-master-file-button">
                                        <input type="file" wire:model="productImage" accept="image/png,image/jpeg,image/webp">
                                        <span wire:loading.remove wire:target="productImage"><?php echo e($productImagePreview ? 'Replace image' : 'Upload image'); ?></span><span wire:loading wire:target="productImage">Preparing…</span>
                                    </label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existingProductImageUrl && !$removeProductImage && !$productImage): ?>
                                        <button type="button" class="ft-master-remove-image" wire:click="markProductImageForRemoval">Remove</button>
                                    <?php elseif($removeProductImage): ?>
                                        <button type="button" class="ft-master-remove-image" wire:click="restoreProductImage">Undo remove</button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <small>PNG, JPG or WEBP up to 5 MB.</small>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productImage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'product'): ?>
                        <div class="field">
                            <label>Product category</label>
                            <select wire:model="parentId">
                                <option value="">No category</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['parentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php elseif($group === 'state'): ?>
                        <div class="field">
                            <label>Country *</label>
                            <select wire:model="parentId">
                                <option value="">Select country</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['parentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="field">
                        <label>Status</label>
                        <select wire:model="status"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                    <div class="field">
                        <label>Sort order</label>
                        <input type="number" min="0" wire:model="sortOrder">
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasColor): ?>
                        <div class="field full">
                            <label>Color *</label>
                            <div class="ft-master-color-picker-row" style="<?php echo e(\App\Support\MasterColor::style($color)); ?>">
                                <input class="ft-master-color-picker" type="color" wire:model.live="color" aria-label="Choose <?php echo e($labels[$group]); ?> color">
                                <input type="text" maxlength="7" wire:model.blur="color" placeholder="#2563EB" aria-label="Hex color code">
                                <span class="ft-master-color-preview"><i class="ft-master-color-dot"></i><span>This color will be used for <?php echo e($colorUsageLabel); ?> labels across FlowTrack.</span></span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="field full">
                        <label><?php echo e($group === 'phone_country_code' ? 'Country / label' : 'Description'); ?></label>
                        <textarea wire:model="description" rows="3" <?php if($group === 'phone_country_code'): ?> placeholder="e.g. Bangladesh" <?php endif; ?>></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="ghost" wire:click="close">Cancel</button>
                <button class="primary" wire:click="save" wire:loading.attr="disabled" wire:target="save,productImage">Save <?php echo e(ucfirst($singularLabel)); ?></button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'product_category' && $categoryEditorLevel): ?>
        <?php if (isset($component)) { $__componentOriginal0152ba453f28532ca522a6f84f1ccee6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0152ba453f28532ca522a6f84f1ccee6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-editor','data' => ['level' => $categoryEditorLevel,'editing' => (bool) $categoryEditorId,'readOnly' => $categoryEditorReadOnly,'mainCategories' => $categoryMainCategories,'productCategories' => $categoryProductCategories,'selectedParentId' => $categoryEditorParentId,'nameValue' => $categoryEditorName,'descriptionValue' => $categoryEditorDescription]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorLevel),'editing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $categoryEditorId),'read-only' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorReadOnly),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainCategories),'product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductCategories),'selected-parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorParentId),'name-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorName),'description-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorDescription)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0152ba453f28532ca522a6f84f1ccee6)): ?>
<?php $attributes = $__attributesOriginal0152ba453f28532ca522a6f84f1ccee6; ?>
<?php unset($__attributesOriginal0152ba453f28532ca522a6f84f1ccee6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0152ba453f28532ca522a6f84f1ccee6)): ?>
<?php $component = $__componentOriginal0152ba453f28532ca522a6f84f1ccee6; ?>
<?php unset($__componentOriginal0152ba453f28532ca522a6f84f1ccee6); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/index.blade.php ENDPATH**/ ?>