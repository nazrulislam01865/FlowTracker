<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job', 'context' => [], 'showAddJobProductForm' => false, 'jobProductSearch' => '',
    'jobProductSearchResults' => collect(), 'jobProductSearchSuppliers' => collect(), 'jobProductResultTotal' => 0,
    'jobProductShowAllResults' => false, 'jobProductSelectedProduct' => null, 'jobProductSelectedSupplier' => null,
    'jobProductCategory' => '', 'jobProductQuantity' => '1000', 'jobProductUnitPrice' => '0.00', 'jobProductSupplierId' => null,
    'jobProductSupplierLabel' => '', 'jobProductSupplierLocked' => false,
    'showEditOrderProductModal' => false, 'editOrderProductItemId' => null, 'editOrderProductName' => '', 'editOrderProductCode' => '',
    'editOrderProductCategory' => '', 'editOrderProductSearch' => '', 'editOrderProductSearchResults' => collect(),
    'editOrderProductSearchSuppliers' => collect(), 'editOrderProductResultTotal' => 0, 'editOrderProductSelectedProduct' => null,
    'editOrderProductSelectedSupplier' => null, 'editOrderProductShowAllResults' => false,
    'editOrderProductSupplierId' => null, 'editOrderProductSupplierLabel' => '', 'editOrderProductQuantity' => '1',
    'editOrderProductUnitPrice' => '0.00', 'editOrderProductNotes' => '',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'job', 'context' => [], 'showAddJobProductForm' => false, 'jobProductSearch' => '',
    'jobProductSearchResults' => collect(), 'jobProductSearchSuppliers' => collect(), 'jobProductResultTotal' => 0,
    'jobProductShowAllResults' => false, 'jobProductSelectedProduct' => null, 'jobProductSelectedSupplier' => null,
    'jobProductCategory' => '', 'jobProductQuantity' => '1000', 'jobProductUnitPrice' => '0.00', 'jobProductSupplierId' => null,
    'jobProductSupplierLabel' => '', 'jobProductSupplierLocked' => false,
    'showEditOrderProductModal' => false, 'editOrderProductItemId' => null, 'editOrderProductName' => '', 'editOrderProductCode' => '',
    'editOrderProductCategory' => '', 'editOrderProductSearch' => '', 'editOrderProductSearchResults' => collect(),
    'editOrderProductSearchSuppliers' => collect(), 'editOrderProductResultTotal' => 0, 'editOrderProductSelectedProduct' => null,
    'editOrderProductSelectedSupplier' => null, 'editOrderProductShowAllResults' => false,
    'editOrderProductSupplierId' => null, 'editOrderProductSupplierLabel' => '', 'editOrderProductQuantity' => '1',
    'editOrderProductUnitPrice' => '0.00', 'editOrderProductNotes' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Presentation only. OrderReadService eager-loads product, supplier and audit
    // relations before this component renders, so this view never performs N+1 queries.
    $items = \App\Support\JobDetailPresenter::products($job);
    $activeItems = $items->filter(fn ($item) => !($item->is_removed ?? false))->values();
    $canView = (bool) ($context['canViewProducts'] ?? false);
    $canEdit = (bool) ($context['canEditProducts'] ?? false);
    $canCreate = (bool) ($context['canCreateProducts'] ?? false);
    $canDelete = (bool) ($context['canDeleteProducts'] ?? false);
    $currency = strtoupper((string) ($job->currency ?: 'USD'));
    $symbol = match ($currency) { 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CNY', 'RMB' => '¥', default => $currency.' ' };
    $activeUnits = \App\Support\OrderDetailPresenter::totalActiveUnits($job);
?>

<?php if (isset($component)) { $__componentOriginalba811f0c8eda75848d52d470099ca258 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalba811f0c8eda75848d52d470099ca258 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-products-card','data' => ['id' => 'productsSection','variant' => 'order','count' => $activeItems->count(),'totalUnits' => $activeUnits]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-products-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'productsSection','variant' => 'order','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeItems->count()),'total-units' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeUnits)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canView): ?>
        <tr class="ft-order-product-empty-row">
            <td colspan="8">Product details are unavailable for your role.</td>
        </tr>
    <?php elseif($items->isEmpty()): ?>
        <tr class="ft-order-product-empty-row">
            <td colspan="8">No products have been added to this Order yet.</td>
        </tr>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $removed = (bool) ($item->is_removed ?? false);
                $catalog = $item->catalogProduct;
                $imageUrl = $catalog && method_exists($catalog, 'productImageUrl') ? $catalog->productImageUrl() : null;
                $displayCode = $catalog && method_exists($catalog, 'productDisplayCode') ? $catalog->productDisplayCode() : null;
                $referenceCode = $catalog && method_exists($catalog, 'productReferenceCode') ? $catalog->productReferenceCode() : null;
                // Keep Order Details category rendering in parity with Inquiry Details:
                // Main category > Product category > Subcategory. Prefer Product Master
                // classification and only fall back to the legacy stored category text.
                $classificationParts = collect([
                    $catalog && method_exists($catalog, 'productMainCategory') ? $catalog->productMainCategory() : null,
                    ...array_filter(array_map('trim', preg_split('/\s*>\s*/', (string) ($catalog && method_exists($catalog, 'productClassificationPath') ? $catalog->productClassificationPath() : '')) ?: [])),
                ])->filter()->unique()->values();
                if ($classificationParts->isEmpty() && filled($item->category_name)) {
                    $classificationParts = collect(preg_split('/\s*>\s*/', trim((string) $item->category_name)) ?: [])
                        ->map(fn ($part) => trim((string) $part))
                        ->filter()
                        ->unique()
                        ->values();
                }
                $categoryDisplay = $classificationParts->implode(' › ') ?: '—';
                $supplier = $item->relationLoaded('supplier') ? $item->supplier : null;
                $supplierName = $supplier?->name ?: \App\Support\OrderDetailPresenter::itemSupplierName($item, $job);
                $defaultSupplierId = $catalog && method_exists($catalog, 'productSupplierId') ? $catalog->productSupplierId() : null;
                $isDefaultSupplier = $supplier && $defaultSupplierId && (int) $supplier->id === (int) $defaultSupplierId;
                $leadDays = (int) (data_get($catalog?->metadata, 'lead_time_days') ?: data_get($catalog?->metadata, 'supplier_lead_time_days') ?: 0);
                $isPreferred = (bool) (data_get($catalog?->metadata, 'supplier_preferred') ?: data_get($catalog?->metadata, 'preferred_supplier'));
                $supplierMeta = collect([
                    $supplierName !== 'Not linked' ? ($isDefaultSupplier ? 'Default supplier' : 'Order supplier') : null,
                    $leadDays > 0 ? number_format($leadDays).'-day lead time' : null,
                    $isPreferred ? 'Preferred' : null,
                ])->filter()->implode(' · ');
                $quantity = max(0, (int) ($item->quantity ?? 0));
                $unitPrice = max(0, (float) ($item->unit_price ?? 0));
                $updatedBy = data_get($item, 'updatedBy.name') ?: data_get($item, 'removedBy.name') ?: $job->owner?->name ?: 'FlowTrack';
                $updatedRelative = $item->updated_at
                    ? (\App\Support\UserLocalTime::localize($item->updated_at)?->diffForHumans() ?: '—')
                    : '—';
            ?>

            <tr
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-product-detail-'.e($item->id ?? 'legacy').''; ?>wire:key="order-product-detail-<?php echo e($item->id ?? 'legacy'); ?>"
                x-data="{ actionOpen: false }"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-removed removed-product-row' => $removed, 'is-editing' => !$removed && $canEdit && $showEditOrderProductModal && (int) $editOrderProductItemId === (int) $item->id]); ?>"
            >
                <td data-label="Product">
                    <?php if (isset($component)) { $__componentOriginale5d0c9e6668574836a4427e7246d2066 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5d0c9e6668574836a4427e7246d2066 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-identity','data' => ['imageUrl' => $imageUrl,'alt' => $item->product_name ?? '','code' => $displayCode,'reference' => $referenceCode,'fallbackMeta' => 'Order product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-identity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['image-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($imageUrl),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->product_name ?? ''),'code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayCode),'reference' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($referenceCode),'fallback-meta' => 'Order product']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <span class="ft-order-product-name"><?php echo e($item->product_name ?: 'Unnamed product'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($removed): ?><span class="ft-detail-product-state">Removed</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5d0c9e6668574836a4427e7246d2066)): ?>
<?php $attributes = $__attributesOriginale5d0c9e6668574836a4427e7246d2066; ?>
<?php unset($__attributesOriginale5d0c9e6668574836a4427e7246d2066); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5d0c9e6668574836a4427e7246d2066)): ?>
<?php $component = $__componentOriginale5d0c9e6668574836a4427e7246d2066; ?>
<?php unset($__componentOriginale5d0c9e6668574836a4427e7246d2066); ?>
<?php endif; ?>
                </td>
                <td data-label="Category">
                    <span class="ft-order-product-category-path"><?php echo e($categoryDisplay); ?></span>
                </td>
                <td data-label="Supplier">
                    <?php if (isset($component)) { $__componentOriginal36bb00d8dbbab30ad4c0674c5a974b30 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36bb00d8dbbab30ad4c0674c5a974b30 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-supplier','data' => ['supplier' => $supplier,'name' => $supplierName !== 'Not linked' ? $supplierName : null,'meta' => $supplierMeta,'fallback' => 'Not linked']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-supplier'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierName !== 'Not linked' ? $supplierName : null),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierMeta),'fallback' => 'Not linked']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36bb00d8dbbab30ad4c0674c5a974b30)): ?>
<?php $attributes = $__attributesOriginal36bb00d8dbbab30ad4c0674c5a974b30; ?>
<?php unset($__attributesOriginal36bb00d8dbbab30ad4c0674c5a974b30); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36bb00d8dbbab30ad4c0674c5a974b30)): ?>
<?php $component = $__componentOriginal36bb00d8dbbab30ad4c0674c5a974b30; ?>
<?php unset($__componentOriginal36bb00d8dbbab30ad4c0674c5a974b30); ?>
<?php endif; ?>
                </td>
                <td class="ft-order-product-quantity" data-label="Quantity">
                    <strong class="ft-order-product-static-value"><?php echo e(number_format($quantity)); ?> units</strong>
                </td>
                <td class="ft-order-product-price" data-label="Unit price">
                    <strong class="ft-order-product-static-value"><?php echo e($symbol); ?><?php echo e(number_format($unitPrice, 2)); ?></strong>
                </td>
                <td class="ft-order-product-notes" data-label="Notes">
                    <span class="ft-order-product-note-value <?php echo e(filled($item->notes) ? '' : 'is-empty'); ?>"><?php echo e($item->notes ?: 'Add notes'); ?></span>
                </td>
                <?php if (isset($component)) { $__componentOriginalf8e22e549d64313bce97b5ba6b14d89a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-updated','data' => ['primary' => $updatedBy,'secondary' => $updatedRelative]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-updated'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['primary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($updatedBy),'secondary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($updatedRelative)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a)): ?>
<?php $attributes = $__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a; ?>
<?php unset($__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8e22e549d64313bce97b5ba6b14d89a)): ?>
<?php $component = $__componentOriginalf8e22e549d64313bce97b5ba6b14d89a; ?>
<?php unset($__componentOriginalf8e22e549d64313bce97b5ba6b14d89a); ?>
<?php endif; ?>
                <td class="ft-order-product-actions-cell" data-label="Actions">
                    <?php if (isset($component)) { $__componentOriginal769c4590c1dc590e97b31bc706ef7701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal769c4590c1dc590e97b31bc706ef7701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-actions','data' => ['itemId' => $item->id,'canEdit' => !$removed && $canEdit,'editMethod' => 'openEditOrderProductModal','canDelete' => !$removed && $canDelete,'removeMethod' => 'removeJobItem','confirmText' => 'Remove this product from the active Order? It will remain available in audit history.','canRestore' => $removed && $canEdit,'restoreMethod' => 'restoreJobItem']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['item-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->id),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$removed && $canEdit),'edit-method' => 'openEditOrderProductModal','can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$removed && $canDelete),'remove-method' => 'removeJobItem','confirm-text' => 'Remove this product from the active Order? It will remain available in audit history.','can-restore' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removed && $canEdit),'restore-method' => 'restoreJobItem']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal769c4590c1dc590e97b31bc706ef7701)): ?>
<?php $attributes = $__attributesOriginal769c4590c1dc590e97b31bc706ef7701; ?>
<?php unset($__attributesOriginal769c4590c1dc590e97b31bc706ef7701); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal769c4590c1dc590e97b31bc706ef7701)): ?>
<?php $component = $__componentOriginal769c4590c1dc590e97b31bc706ef7701; ?>
<?php unset($__componentOriginal769c4590c1dc590e97b31bc706ef7701); ?>
<?php endif; ?>
                </td>
            </tr>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$removed && $canEdit && $showEditOrderProductModal && (int) $editOrderProductItemId === (int) $item->id): ?>
                <tr class="ft-detail-product-editor-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-product-inline-editor-'.e($item->id).''; ?>wire:key="order-product-inline-editor-<?php echo e($item->id); ?>">
                    <td colspan="8">
                        <?php if (isset($component)) { $__componentOriginal0413a3f88fce063db5626ac70d24ba80 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0413a3f88fce063db5626ac70d24ba80 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-edit','data' => ['wireKey' => 'order-product-edit-'.$item->id,'variant' => 'order','recordLabel' => 'Order','searchModel' => 'editOrderProductSearch','searchValue' => $editOrderProductSearch,'searchResults' => $editOrderProductSearchResults,'searchSuppliers' => $editOrderProductSearchSuppliers,'resultTotal' => $editOrderProductResultTotal,'showAllResults' => $editOrderProductShowAllResults,'showAllMethod' => 'showAllEditOrderProductResults','selectMethod' => 'selectEditOrderProduct','selectedProduct' => $editOrderProductSelectedProduct,'selectedSupplier' => $editOrderProductSelectedSupplier,'categoryValue' => $editOrderProductCategory,'quantityModel' => 'editOrderProductQuantity','quantityValue' => $editOrderProductQuantity,'unitPriceValue' => $editOrderProductUnitPrice,'notesModel' => 'editOrderProductNotes','notesValue' => $editOrderProductNotes,'supplierModel' => 'editOrderProductSupplierId','supplierValue' => $editOrderProductSupplierId,'supplierLabel' => $editOrderProductSupplierLabel,'supplierEditable' => true,'supplierAction' => 'updateEditOrderProductSupplierFromSelector','currencySymbol' => $symbol,'closeMethod' => 'closeEditOrderProductModal','saveMethod' => 'saveEditOrderProductModal','selectedErrorKey' => 'editOrderProductSelectedId','quantityErrorKey' => 'editOrderProductQuantity','unitPriceErrorKey' => 'editOrderProductUnitPrice','notesErrorKey' => 'editOrderProductNotes','supplierErrorKey' => 'editOrderProductSupplierId']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-edit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('order-product-edit-'.$item->id),'variant' => 'order','record-label' => 'Order','search-model' => 'editOrderProductSearch','search-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearch),'search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchResults),'search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchSuppliers),'result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductResultTotal),'show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductShowAllResults),'show-all-method' => 'showAllEditOrderProductResults','select-method' => 'selectEditOrderProduct','selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedProduct),'selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedSupplier),'category-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCategory),'quantity-model' => 'editOrderProductQuantity','quantity-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductQuantity),'unit-price-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductUnitPrice),'notes-model' => 'editOrderProductNotes','notes-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductNotes),'supplier-model' => 'editOrderProductSupplierId','supplier-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierId),'supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierLabel),'supplier-editable' => true,'supplier-action' => 'updateEditOrderProductSupplierFromSelector','currency-symbol' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($symbol),'close-method' => 'closeEditOrderProductModal','save-method' => 'saveEditOrderProductModal','selected-error-key' => 'editOrderProductSelectedId','quantity-error-key' => 'editOrderProductQuantity','unit-price-error-key' => 'editOrderProductUnitPrice','notes-error-key' => 'editOrderProductNotes','supplier-error-key' => 'editOrderProductSupplierId']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0413a3f88fce063db5626ac70d24ba80)): ?>
<?php $attributes = $__attributesOriginal0413a3f88fce063db5626ac70d24ba80; ?>
<?php unset($__attributesOriginal0413a3f88fce063db5626ac70d24ba80); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0413a3f88fce063db5626ac70d24ba80)): ?>
<?php $component = $__componentOriginal0413a3f88fce063db5626ac70d24ba80; ?>
<?php unset($__componentOriginal0413a3f88fce063db5626ac70d24ba80); ?>
<?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

     <?php $__env->slot('afterTable', null, []); ?> 
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddJobProductForm && $canCreate): ?>
            <div class="ft-detail-products-inline-add" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-add-product-inline-'.e($job->id).''; ?>wire:key="order-add-product-inline-<?php echo e($job->id); ?>" x-data x-on:keydown.escape.window="$wire.closeAddJobProductForm()">
                <?php if (isset($component)) { $__componentOriginal5e4da558653258c1bfe993ad392b6247 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e4da558653258c1bfe993ad392b6247 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-add-product','data' => ['wireKey' => 'job-detail-add-product-'.$job->id,'searchModel' => 'jobProductSearch','searchValue' => $jobProductSearch,'searchResults' => $jobProductSearchResults,'searchSuppliers' => $jobProductSearchSuppliers,'resultTotal' => $jobProductResultTotal,'showAllResults' => $jobProductShowAllResults,'showAllMethod' => 'showAllJobProductResults','selectMethod' => 'selectJobProduct','selectedProduct' => $jobProductSelectedProduct,'selectedSupplier' => $jobProductSelectedSupplier,'categoryValue' => $jobProductCategory,'quantityModel' => 'jobProductQuantity','quantityValue' => $jobProductQuantity,'unitPriceModel' => 'jobProductUnitPrice','unitPriceValue' => $jobProductUnitPrice,'supplierModel' => 'jobProductSupplierId','supplierValue' => $jobProductSupplierId,'supplierLabel' => $jobProductSupplierLabel,'supplierLocked' => $jobProductSupplierLocked,'supplierRequired' => true,'currencySymbol' => $symbol,'closeMethod' => 'closeAddJobProductForm','saveMethod' => 'saveJobProduct('.e($job->id).')','selectedErrorKey' => 'jobProductSelectedId','quantityErrorKey' => 'jobProductQuantity','unitPriceErrorKey' => 'jobProductUnitPrice','supplierErrorKey' => 'jobProductSupplierId']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-add-product'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('job-detail-add-product-'.$job->id),'search-model' => 'jobProductSearch','search-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearch),'search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchResults),'search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchSuppliers),'result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductResultTotal),'show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductShowAllResults),'show-all-method' => 'showAllJobProductResults','select-method' => 'selectJobProduct','selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedProduct),'selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedSupplier),'category-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductCategory),'quantity-model' => 'jobProductQuantity','quantity-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductQuantity),'unit-price-model' => 'jobProductUnitPrice','unit-price-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductUnitPrice),'supplier-model' => 'jobProductSupplierId','supplier-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierId),'supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLabel),'supplier-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLocked),'supplier-required' => true,'currency-symbol' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($symbol),'close-method' => 'closeAddJobProductForm','save-method' => 'saveJobProduct('.e($job->id).')','selected-error-key' => 'jobProductSelectedId','quantity-error-key' => 'jobProductQuantity','unit-price-error-key' => 'jobProductUnitPrice','supplier-error-key' => 'jobProductSupplierId']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e4da558653258c1bfe993ad392b6247)): ?>
<?php $attributes = $__attributesOriginal5e4da558653258c1bfe993ad392b6247; ?>
<?php unset($__attributesOriginal5e4da558653258c1bfe993ad392b6247); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e4da558653258c1bfe993ad392b6247)): ?>
<?php $component = $__componentOriginal5e4da558653258c1bfe993ad392b6247; ?>
<?php unset($__componentOriginal5e4da558653258c1bfe993ad392b6247); ?>
<?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php $__env->endSlot(); ?>

     <?php $__env->slot('footer', null, []); ?> 
        <span>Product, quantity, price and supplier changes are recorded in order activity.</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canView && $canCreate && !$showAddJobProductForm): ?>
            <button type="button" class="ft-order-product-add-another" wire:click="openAddJobProductForm(<?php echo e($job->id); ?>)" wire:loading.attr="disabled" wire:target="openAddJobProductForm">
                <span aria-hidden="true">+</span> Add another product
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalba811f0c8eda75848d52d470099ca258)): ?>
<?php $attributes = $__attributesOriginalba811f0c8eda75848d52d470099ca258; ?>
<?php unset($__attributesOriginalba811f0c8eda75848d52d470099ca258); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalba811f0c8eda75848d52d470099ca258)): ?>
<?php $component = $__componentOriginalba811f0c8eda75848d52d470099ca258; ?>
<?php unset($__componentOriginalba811f0c8eda75848d52d470099ca258); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/products.blade.php ENDPATH**/ ?>