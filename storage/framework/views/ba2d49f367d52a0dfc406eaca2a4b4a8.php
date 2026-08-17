<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'rows' => [],
    'rowsProperty' => 'createProductRows',
    'removeMethod' => 'removeCreateProductRow',
    'required' => false,
    'activeProductCount' => 0,
    'productSearchResults' => collect(),
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductCategoryFilter' => '',
    'createProductShowAllResults' => false,
    'productCategories' => collect(),
    'selectedProductDetails' => collect(),
    'canViewProductCategories' => false,
    'canCreateCatalogProduct' => false,
    'wireKey' => 'shared-create-products',
    'rowKeyPrefix' => 'shared-selected-product',
    'requiredErrorField' => null,
    'step' => 2,
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
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductCategoryFilter' => '',
    'createProductShowAllResults' => false,
    'productCategories' => collect(),
    'selectedProductDetails' => collect(),
    'canViewProductCategories' => false,
    'canCreateCatalogProduct' => false,
    'wireKey' => 'shared-create-products',
    'rowKeyPrefix' => 'shared-selected-product',
    'requiredErrorField' => null,
    'step' => 2,
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
    $createProductUrl = route('master-data', ['group' => 'product', 'create' => 1]);
    $sectionError = $requiredErrorField ? $errors->first($requiredErrorField) : null;
?>

<section
    class="section ft-inquiry-create-section ft-create-section ft-order-products-prototype ft-inquiry-prototype"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($wireKey).''; ?>wire:key="<?php echo e($wireKey); ?>"
    x-data="{
        resultsOpen: false,
        resultsMaxHeight: 420,
        fitProductResults() {
            this.$nextTick(() => {
                const host = this.$refs.productSearchHost;
                if (!host) return;
                const resultsTop = host.getBoundingClientRect().top + 42;
                const available = window.innerHeight - resultsTop - 14;
                this.resultsMaxHeight = Math.max(120, Math.min(520, available));
            });
        },
        openProductResults() {
            this.resultsOpen = true;
            this.fitProductResults();
        }
    }"
    x-on:create-order-product-selected.window="resultsOpen = false"
    x-on:focus-create-order-product-search.window="$nextTick(() => { openProductResults(); $refs.productSearch?.focus(); })"
    x-on:open-create-order-product-results.window="$nextTick(() => openProductResults())"
    x-on:resize.window="if (resultsOpen) fitProductResults()"
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
        <p>
            Search all <?php echo e(number_format((int) $activeProductCount)); ?> products &mdash;
            <?php echo e($required ? 'at least one product is required.' : 'no category selection required.'); ?>

        </p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sectionError): ?>
            <div class="validation-error ft-order-products-section-error"><?php echo e($sectionError); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-order-product-search-label">Search product</div>
    <div class="ft-order-product-search-row">
        <div class="ft-order-product-search-host" x-ref="productSearchHost" x-on:click.outside="resultsOpen = false">
            <div class="ft-order-product-search-input" :class="resultsOpen ? 'is-open' : ''">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input
                    x-ref="productSearch"
                    type="search"
                    wire:model.live.debounce.220ms="createProductSearch"
                    x-on:focus="openProductResults()"
                    x-on:keydown.escape="resultsOpen = false"
                    placeholder="Search product name, product code or reference code"
                    autocomplete="off"
                    aria-label="Search product"
                >
                <span class="ft-order-product-search-tools">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productSearchValue !== ''): ?>
                        <button type="button" class="ft-order-product-search-clear" wire:click="$set('createProductSearch', '')" x-on:click="openProductResults()" aria-label="Clear product search">&times;</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="ft-order-product-shortcut">&#8984; K</span>
                </span>
            </div>

            <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.origin.top :style="`max-height:${resultsMaxHeight}px`">
                <div class="ft-order-product-results-head">
                    <span>Top matches <b><?php echo e(number_format((int) $productResultTotal)); ?> <?php echo e(\Illuminate\Support\Str::plural('result', (int) $productResultTotal)); ?></b></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$createProductShowAllResults && $productResultTotal > $resultCount): ?>
                        <button type="button" wire:click="showAllCreateProductResults">View all results <span>&nearr;</span></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-order-product-result-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productSearchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if($product->type !== 'product'): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?>
                        <?php
                            $isSelected = in_array((int) $product->id, $selectedIds, true);
                            $detailText = $product->productCatalogSummary();
                            $imageUrl = $product->productImageUrl();
                            $referenceCode = $product->productReferenceCode();
                            $displayCode = $product->productDisplayCode();
                            $mainCategory = $product->productMainCategory();
                            $productCategory = trim((string) ($product->parent?->name ?? ''));
                            $subCategory = trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category') ?: $detailText));
                            $classification = collect([$mainCategory, $productCategory, $subCategory])->filter()->unique()->values();
                        ?>
                        <div class="ft-order-product-result <?php echo e($isSelected ? 'is-selected' : ''); ?>">
                            <span class="ft-order-product-thumb">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl): ?>
                                    <img src="<?php echo e($imageUrl); ?>" alt="">
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span class="ft-order-product-result-copy">
                                <strong><?php echo e($product->name); ?></strong>
                                <span class="ft-order-product-code-line">Product code: <?php echo e($displayCode); ?> <i>&bull;</i> Ref: <?php echo e($referenceCode ?: '—'); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($classification->isNotEmpty()): ?>
                                    <small class="ft-order-product-classification">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $classification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($part); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?><i>&rsaquo;</i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <button type="button" class="ft-order-product-select-button <?php echo e($isSelected ? 'is-selected' : ''); ?>" wire:click="selectCreateProduct(<?php echo e($product->id); ?>)" <?php if($isSelected): echo 'disabled'; endif; ?>><?php echo e($isSelected ? 'Selected' : 'Select'); ?></button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-order-product-no-results">
                            <strong>No products found</strong>
                            <span>Try another product name or SKU.</span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-order-product-results-footer">
                    <span>Use &uarr; &darr; to navigate &nbsp;&middot;&nbsp; Enter to select</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateCatalogProduct): ?>
                        <a href="<?php echo e($createProductUrl); ?>">Can't find it? <b>Create new product</b></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewProductCategories): ?>
            <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-order-product-category-search-filter','label' => 'Product category','property' => 'createProductCategoryFilter','value' => $createProductCategoryFilter,'placeholder' => 'All categories','options' => $productCategories,'clearable' => true,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 320,'searchPlaceholder' => 'Search product category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-product-category-search-filter','label' => 'Product category','property' => 'createProductCategoryFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductCategoryFilter),'placeholder' => 'All categories','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategories),'clearable' => true,'hide-label' => true,'fixed-menu' => true,'menu-width' => 320,'search-placeholder' => 'Search product category…']); ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-order-product-top-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateCatalogProduct): ?>
                <a href="<?php echo e($createProductUrl); ?>" class="ft-order-product-action-link"><span>+</span> Create Product</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($rows)): ?>
        <div class="ft-order-selected-products">
            <div class="ft-order-selected-products-title">Selected products (<?php echo e(count($rows)); ?>)</div>
            <div class="ft-order-selected-products-table">
                <div class="ft-order-selected-products-head">
                    <span>Product</span><span>Quantity</span><span>Unit price <small>(optional)</small></span><span>Notes</span><span>Action</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $detail = $selectedProductDetails->get((int) ($item['product_id'] ?? 0));
                        $itemImage = $detail?->productImageUrl();
                        $itemCode = (string) ($detail?->code ?? '');
                        $itemCategory = (string) ($detail?->parent?->name ?? ($item['category'] ?? ''));
                        $itemName = (string) ($detail?->name ?? ($item['product'] ?? 'Product'));
                        $quantityError = $errors->first("{$rowsProperty}.{$index}.quantity");
                        $unitPriceError = $errors->first("{$rowsProperty}.{$index}.unit_price");
                        $notesError = $errors->first("{$rowsProperty}.{$index}.notes");
                        $productError = $errors->first("{$rowsProperty}.{$index}.product");
                    ?>
                    <div class="ft-order-selected-product-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($rowKeyPrefix).'-'.e($item['product_id'] ?? $index).'-'.e($index).''; ?>wire:key="<?php echo e($rowKeyPrefix); ?>-<?php echo e($item['product_id'] ?? $index); ?>-<?php echo e($index); ?>">
                        <div class="ft-order-selected-product-info">
                            <span class="ft-order-product-thumb">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemImage): ?>
                                    <img src="<?php echo e($itemImage); ?>" alt="">
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span>
                                <strong><?php echo e($itemName); ?></strong>
                                <small>SKU: <?php echo e($itemCode ?: 'N/A'); ?> <i>&bull;</i> <?php echo e($itemCategory ?: 'Uncategorized'); ?></small>
                            </span>
                        </div>
                        <div class="ft-order-product-quantity-control">
                            <button type="button" wire:click="decrementCreateProductQuantity(<?php echo e($index); ?>)" aria-label="Decrease quantity">&minus;</button>
                            <input type="number" min="1" max="999999999" wire:model.live.debounce.300ms="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.quantity" aria-label="Quantity for <?php echo e($itemName); ?>">
                            <button type="button" wire:click="incrementCreateProductQuantity(<?php echo e($index); ?>)" aria-label="Increase quantity">+</button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantityError): ?><small class="validation-error"><?php echo e($quantityError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ft-order-product-unit-price">
                            <input type="number" min="0" max="999999999999.99" step="0.01" wire:model.blur="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.unit_price" placeholder="0.00" aria-label="Unit price for <?php echo e($itemName); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unitPriceError): ?><small class="validation-error"><?php echo e($unitPriceError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ft-order-product-notes">
                            <input type="text" maxlength="2000" wire:model.blur="<?php echo e($rowsProperty); ?>.<?php echo e($index); ?>.notes" placeholder="Optional notes..." aria-label="Notes for <?php echo e($itemName); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notesError): ?><small class="validation-error"><?php echo e($notesError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productError): ?><small class="validation-error"><?php echo e($productError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button type="button" class="ft-order-selected-product-remove" wire:click="<?php echo e($removeMethod); ?>(<?php echo e($index); ?>)" aria-label="Remove <?php echo e($itemName); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M8 10v7M12 10v7M16 10v7M6 7l1 14h10l1-14"/></svg>
                        </button>
                    </div>
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