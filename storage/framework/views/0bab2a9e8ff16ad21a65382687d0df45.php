<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'rows' => [],
    'rfqRows' => [],
    'activeProductCount' => 0,
    'productSearchResults' => collect(),
    'productSearchSuppliers' => collect(),
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductShowAllResults' => false,
    'selectedProductDetails' => collect(),
    'selectedProductSuppliers' => collect(),
    'selectedRfqSuppliers' => collect(),
    'canCreateCatalogProduct' => false,
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
    'rfqRows' => [],
    'activeProductCount' => 0,
    'productSearchResults' => collect(),
    'productSearchSuppliers' => collect(),
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductShowAllResults' => false,
    'selectedProductDetails' => collect(),
    'selectedProductSuppliers' => collect(),
    'selectedRfqSuppliers' => collect(),
    'canCreateCatalogProduct' => false,
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
    $resultCount = collect($productSearchResults)->count();
    $productSearchValue = trim((string) $createProductSearch);
    $showCreateProductSuggestion = $productSearchValue !== '' && (int) $productResultTotal === 0;
    $hasProducts = count($rows) > 0;
?>

<section
    class="section ft-inquiry-create-section ft-inquiry-product-rfq-prototype"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-inquiry-product-rfq-prototype'; ?>wire:key="create-inquiry-product-rfq-prototype"
    x-data="{
        resultsOpen: false,
        focusSearch(openResults = true) {
            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    const search = this.$refs.productSearch;
                    search?.focus({ preventScroll: true });
                    search?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (openResults) this.resultsOpen = true;
                });
            });
        },
        openResults() {
            this.resultsOpen = true;
        },
        closeResults() {
            this.resultsOpen = false;
        },
    }"
    x-on:focus-create-order-product-search.window="focusSearch(true)"
    x-on:open-create-order-product-results.window="openResults()"
    x-on:create-order-product-selected.window="closeResults()"
    x-on:keydown.escape.window="closeResults()"
>
    <header class="ft-ipr-section-head">
        <div class="ft-ipr-section-heading">
            <span class="ft-ipr-step">2</span>
            <div>
                <div class="ft-ipr-title-line">
                    <h2>Products, suppliers &amp; RFQ invitations</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasProducts): ?>
                        <span class="ft-ipr-count-badge"><?php echo e(count($rows)); ?> <?php echo e(\Illuminate\Support\Str::plural('product', count($rows))); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <p>Add one or more products, then choose suppliers and invitation settings for each product.</p>
            </div>
        </div>

    </header>

    <div class="ft-ipr-search-shell">
        <label class="ft-ipr-product-search-label" for="create-inquiry-product-search">Search product</label>

        <div class="ft-ipr-product-picker" x-on:click.outside="closeResults()">
            <div class="ft-ipr-product-search" :class="resultsOpen ? 'is-open' : ''">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input
                    id="create-inquiry-product-search"
                    x-ref="productSearch"
                    type="search"
                    wire:model.live.debounce.220ms="createProductSearch"
                    x-on:focus="openResults()"
                    x-on:input="openResults()"
                    x-on:keydown.escape.stop="closeResults()"
                    placeholder="Search product name, product code or reference code"
                    autocomplete="off"
                    aria-label="Search product"
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productSearchValue !== ''): ?>
                    <button
                        type="button"
                        wire:click="$set('createProductSearch', '')"
                        x-on:click="closeResults()"
                        aria-label="Clear product search"
                    >&times;</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-ipr-product-results" x-cloak x-show="resultsOpen" x-transition.opacity.duration.120ms>
                <div class="ft-ipr-results-head">
                    <span>Top matches <i>&middot;</i> <?php echo e(number_format($resultCount)); ?> <?php echo e(\Illuminate\Support\Str::plural('result', $resultCount)); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$createProductShowAllResults && $productResultTotal > 0): ?>
                        <button type="button" wire:click="showAllCreateProductResults">View all results <span>&nearr;</span></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productSearchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if($product->type !== 'product'): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?>
                    <?php
                        $resultSupplier = $productSearchSuppliers->get((int) $product->id);
                        $imageUrl = $product->productImageUrl();
                        $displayCode = $product->productDisplayCode();
                        $referenceCode = $product->productReferenceCode();
                    ?>
                    <div class="ft-ipr-product-result">
                        <span class="ft-ipr-product-result-image">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl): ?>
                                <img src="<?php echo e($imageUrl); ?>" alt="" loading="lazy" decoding="async" data-ft-image-fallback="icon">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-ipr-product-result-copy">
                            <strong><?php echo e($product->name); ?></strong>
                            <small>Product code <?php echo e($displayCode ?: '—'); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($referenceCode): ?><i>&middot;</i> Ref <?php echo e($referenceCode); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                            <span><?php echo e($resultSupplier?->name ?: 'No default supplier'); ?></span>
                        </span>
                        <button type="button" class="<?php echo e(in_array((int) $product->id, $selectedIds, true) ? 'is-selected' : ''); ?>" wire:click="selectCreateProduct(<?php echo e($product->id); ?>)">
                            <?php echo e(in_array((int) $product->id, $selectedIds, true) ? 'Selected' : 'Select'); ?>

                        </button>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-ipr-empty-search">
                        <strong><?php echo e($showCreateProductSuggestion ? 'No matching product found.' : 'No products found'); ?></strong>
                        <span><?php echo e($showCreateProductSuggestion ? 'You can create a new product from this search.' : 'Search by product name, product code or reference code.'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreateProductSuggestion && $canCreateCatalogProduct): ?>
                            <button type="button" wire:click="openCreateOrderProductModalFromSearch">Create new product</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="ft-ipr-initial-add-action">
            <button type="button" x-on:click.stop="focusSearch(true)" <?php if(count($rows) >= 25): echo 'disabled'; endif; ?>>
                <span aria-hidden="true">+</span> Add Product
            </button>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasProducts): ?>
        <div class="ft-ipr-card-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $productId = (int) ($item['product_id'] ?? 0);
                    $detail = $selectedProductDetails->get($productId);
                    $defaultSupplier = $selectedProductSuppliers->get($productId);
                    $rfqState = is_array($rfqRows[$index] ?? null) ? $rfqRows[$index] : [];
                    $rfqSuppliers = collect($selectedRfqSuppliers->get($index, collect()));
                ?>
                <?php if (isset($component)) { $__componentOriginal45e701988cf5e99ecba124fecab2d979 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45e701988cf5e99ecba124fecab2d979 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.create-product-rfq-card','data' => ['item' => $item,'index' => $index,'detail' => $detail,'defaultSupplier' => $defaultSupplier,'rfqState' => $rfqState,'rfqSuppliers' => $rfqSuppliers,'wire:key' => 'create-inquiry-product-rfq-card-'.e($item['row_key'] ?? ($productId.'-'.$index)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.create-product-rfq-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['item' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item),'index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index),'detail' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detail),'default-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($defaultSupplier),'rfq-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqState),'rfq-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqSuppliers),'wire:key' => 'create-inquiry-product-rfq-card-'.e($item['row_key'] ?? ($productId.'-'.$index)).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45e701988cf5e99ecba124fecab2d979)): ?>
<?php $attributes = $__attributesOriginal45e701988cf5e99ecba124fecab2d979; ?>
<?php unset($__attributesOriginal45e701988cf5e99ecba124fecab2d979); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45e701988cf5e99ecba124fecab2d979)): ?>
<?php $component = $__componentOriginal45e701988cf5e99ecba124fecab2d979; ?>
<?php unset($__componentOriginal45e701988cf5e99ecba124fecab2d979); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <button type="button" class="ft-ipr-add-another" x-on:click.stop="focusSearch(true)" <?php if(count($rows) >= 25): echo 'disabled'; endif; ?>>
            <span aria-hidden="true">+</span> Add another product
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-ipr-info-strip">
        <span aria-hidden="true">i</span>
        <p>Suppliers and RFQ responses are tracked separately for each product.</p>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/create-product-rfq-section.blade.php ENDPATH**/ ?>