<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'rows' => [],
    'rowsProperty' => 'createProductRows',
    'removeMethod' => 'removeCreateProductRow',
    'required' => false,
    'activeProductCount' => 0,
    'productSearchResults' => collect(),
    'productSearchSuppliers' => collect(),
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductShowAllResults' => false,
    'selectedProductDetails' => collect(),
    'selectedProductSuppliers' => collect(),
    'supplierSkippedProductIds' => [],
    'canCreateCatalogProduct' => false,
    'wireKey' => 'shared-create-products',
    'rowKeyPrefix' => 'shared-selected-product',
    'requiredErrorField' => null,
    'step' => 2,
    'supplierRequired' => false,
    'context' => 'order',
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
    'rows' => [],
    'rowsProperty' => 'createProductRows',
    'removeMethod' => 'removeCreateProductRow',
    'required' => false,
    'activeProductCount' => 0,
    'productSearchResults' => collect(),
    'productSearchSuppliers' => collect(),
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductShowAllResults' => false,
    'selectedProductDetails' => collect(),
    'selectedProductSuppliers' => collect(),
    'supplierSkippedProductIds' => [],
    'canCreateCatalogProduct' => false,
    'wireKey' => 'shared-create-products',
    'rowKeyPrefix' => 'shared-selected-product',
    'requiredErrorField' => null,
    'step' => 2,
    'supplierRequired' => false,
    'context' => 'order',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $selectedIds = collect($rows)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->all();
    $resultCount = $productSearchResults->count();
    $productSearchValue = trim((string) $createProductSearch);
    $totalUnits = collect($rows)->sum(fn ($item) => (int) ($item['quantity'] ?? 0));
    $sectionError = $requiredErrorField ? $errors->first($requiredErrorField) : null;
    $showCreateProductSuggestion = $productSearchValue !== '' && (int) $productResultTotal === 0;
?>

<section
    class="section ft-inquiry-create-section ft-create-section ft-order-products-prototype ft-inquiry-prototype ft-product-quantity-prototype-match ft-product-quantity-prototype-exact"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($wireKey).''; ?>wire:key="<?php echo e($wireKey); ?>"
    x-data="{
        resultsOpen: <?php echo \Illuminate\Support\Js::from($productSearchValue !== '')->toHtml() ?>,
        openProductResults() { this.resultsOpen = true },
        closeProductResults() { if (!<?php echo \Illuminate\Support\Js::from($productSearchValue !== '')->toHtml() ?>) this.resultsOpen = false }
    }"
    x-on:create-order-product-selected.window="resultsOpen = true"
    x-on:focus-create-order-product-search.window="$nextTick(() => { openProductResults(); $refs.productSearch?.focus(); })"
    x-on:open-create-order-product-results.window="$nextTick(() => openProductResults())"
>
    <div class="ft-order-products-title-row">
        <div class="ft-create-section-title ft-order-products-title">
            <span><?php echo e($step); ?></span>
            <h2>Products &amp; quantities</h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                <em class="is-required">Required</em>
            <?php else: ?>
                <em>Optional</em>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <p>Search all <?php echo e(number_format((int) $activeProductCount)); ?> products &mdash; the default supplier is shown before selection.</p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sectionError): ?>
            <div class="validation-error ft-order-products-section-error"><?php echo e($sectionError); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <label class="ft-order-product-search-label" for="<?php echo e($wireKey); ?>-search">Search product</label>
    <div class="ft-order-product-search-host" x-on:click.outside="closeProductResults()">
        <div class="ft-order-product-search-input" :class="resultsOpen ? 'is-open' : ''">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input
                id="<?php echo e($wireKey); ?>-search"
                x-ref="productSearch"
                type="search"
                wire:model.live.debounce.220ms="createProductSearch"
                x-on:focus="openProductResults()"
                x-on:input="openProductResults()"
                x-on:keydown.escape="resultsOpen = false"
                placeholder="Search product name, product code or reference code"
                autocomplete="off"
                aria-label="Search product"
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productSearchValue !== ''): ?>
                <button type="button" class="ft-order-product-search-clear" wire:click="$set('createProductSearch', '')" x-on:click="resultsOpen = false" aria-label="Clear product search">&times;</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.opacity.duration.120ms>
            <div class="ft-order-product-results-head">
                <span>Top matches <i>&middot;</i> <?php echo e(number_format((int) $resultCount)); ?> <?php echo e(\Illuminate\Support\Str::plural('result', (int) $resultCount)); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$createProductShowAllResults && $productResultTotal > 0): ?>
                    <button type="button" wire:click="showAllCreateProductResults">View all results <span>&nearr;</span></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-order-product-result-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productSearchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if($product->type !== 'product'): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?>
                    <?php
                        $resultSupplier = $productSearchSuppliers->get((int) $product->id);
                        $supplierWords = preg_split('/\s+/', trim((string) ($resultSupplier?->name ?? ''))) ?: [];
                        $supplierInitials = strtoupper(substr(implode('', array_map(fn ($word) => substr($word, 0, 1), array_filter($supplierWords))), 0, 2));
                        $previewPrice = $product->productPriceForQuantity(1000);
                        $leadDays = (int) (data_get($product->metadata, 'lead_time_days') ?: data_get($product->metadata, 'supplier_lead_time_days') ?: 0);
                        $isPreferred = (bool) (data_get($product->metadata, 'supplier_preferred') ?: data_get($product->metadata, 'preferred_supplier'));
                        $referenceCode = $product->productReferenceCode();
                        $displayCode = $product->productDisplayCode();
                        $imageUrl = $product->productImageUrl();
                    ?>
                    <div class="ft-order-product-result">
                        <span class="ft-order-product-thumb">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl): ?>
                                <img src="<?php echo e($imageUrl); ?>" alt="">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-order-product-result-copy">
                            <strong><?php echo e($product->name); ?></strong>
                            <span class="ft-order-product-code-line">Product code <?php echo e($displayCode); ?> <i>&middot;</i> Ref <?php echo e($referenceCode ?: '—'); ?></span>
                            <small class="ft-order-product-result-supplier">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resultSupplier): ?>
                                    <b class="ft-order-product-result-supplier-badge"><?php echo e($supplierInitials ?: 'S'); ?></b>
                                    <strong><?php echo e($resultSupplier->name); ?></strong>
                                    <span>Default</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leadDays > 0): ?><i>&middot;</i><span><?php echo e(number_format($leadDays)); ?> days</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPreferred): ?><i>&middot;</i><span>Preferred</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previewPrice !== null): ?><i>&middot;</i><span>$<?php echo e(number_format((float) $previewPrice, 2)); ?> last price</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <b class="ft-order-product-result-supplier-badge is-empty">—</b>
                                    <strong>No default supplier</strong>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </small>
                        </span>
                        <button type="button" class="ft-order-product-select-button <?php echo e(in_array((int) $product->id, $selectedIds, true) ? 'is-selected' : ''); ?>" wire:click="selectCreateProduct(<?php echo e($product->id); ?>)">Select</button>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-order-product-no-results <?php echo e($showCreateProductSuggestion && $canCreateCatalogProduct ? 'has-create-action' : ''); ?>">
                        <span class="ft-order-product-empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/><path d="M11 8v6M8 11h6"/></svg>
                        </span>
                        <strong><?php echo e($showCreateProductSuggestion ? 'No matching product found' : 'No products found'); ?></strong>
                        <span><?php echo e($showCreateProductSuggestion ? 'Nothing in the catalog matches “'.$productSearchValue.'”.' : 'Try another product name, product code or reference code.'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreateProductSuggestion && $canCreateCatalogProduct): ?>
                            <button type="button" class="ft-order-product-create-cta" wire:click="openCreateOrderProductModalFromSearch">
                                <span class="ft-order-product-create-cta-icon" aria-hidden="true">+</span>
                                <span class="ft-order-product-create-cta-copy">
                                    <strong>Create “<?php echo e($productSearchValue); ?>”</strong>
                                    <small>Add it to the catalog and this Order</small>
                                </span>
                                <svg class="ft-order-product-create-cta-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($rows)): ?>
        <div class="ft-order-selected-products ft-order-selected-products--cards">
            <div class="ft-order-selected-product-cards">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $productId = (int) ($item['product_id'] ?? 0);
                        $detail = $selectedProductDetails->get($productId);
                        $supplier = $selectedProductSuppliers->get($productId);
                        $supplierSkipped = in_array($productId, array_map('intval', (array) $supplierSkippedProductIds), true);
                    ?>
                    <?php if (isset($component)) { $__componentOriginal052d61524ffa5e68b67e3e1a50ffce55 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal052d61524ffa5e68b67e3e1a50ffce55 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.create-product-card','data' => ['item' => $item,'detail' => $detail,'supplier' => $supplier,'supplierSkipped' => $supplierSkipped,'supplierRequired' => $supplierRequired,'context' => $context,'index' => $index,'rowsProperty' => $rowsProperty,'removeMethod' => $removeMethod,'rowKeyPrefix' => $rowKeyPrefix]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.create-product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['item' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item),'detail' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detail),'supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier),'supplier-skipped' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierSkipped),'supplier-required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierRequired),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index),'rows-property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rowsProperty),'remove-method' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removeMethod),'row-key-prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rowKeyPrefix)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal052d61524ffa5e68b67e3e1a50ffce55)): ?>
<?php $attributes = $__attributesOriginal052d61524ffa5e68b67e3e1a50ffce55; ?>
<?php unset($__attributesOriginal052d61524ffa5e68b67e3e1a50ffce55); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal052d61524ffa5e68b67e3e1a50ffce55)): ?>
<?php $component = $__componentOriginal052d61524ffa5e68b67e3e1a50ffce55; ?>
<?php unset($__componentOriginal052d61524ffa5e68b67e3e1a50ffce55); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-order-product-bottom-actions">
        <button type="button" wire:click="focusCreateProductSearch" <?php if(count($rows) >= 25): echo 'disabled'; endif; ?>><span>+</span> Add Product</button>
    </div>

    <div class="ft-order-products-summary-row">
        <span><?php echo e(count($rows)); ?> <?php echo e(\Illuminate\Support\Str::plural('product', count($rows))); ?> <i>&bull;</i> <?php echo e(number_format($totalUnits)); ?> total units</span>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/create-product-quantity.blade.php ENDPATH**/ ?>