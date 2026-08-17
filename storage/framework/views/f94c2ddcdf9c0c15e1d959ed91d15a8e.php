<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'wireKey',
    'searchModel',
    'searchValue' => '',
    'searchResults' => collect(),
    'resultTotal' => 0,
    'showAllMethod',
    'selectMethod',
    'selectedProduct' => null,
    'categoryValue' => '',
    'quantityModel',
    'unitPriceModel',
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
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
    'wireKey',
    'searchModel',
    'searchValue' => '',
    'searchResults' => collect(),
    'resultTotal' => 0,
    'showAllMethod',
    'selectMethod',
    'selectedProduct' => null,
    'categoryValue' => '',
    'quantityModel',
    'unitPriceModel',
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-order-detail-add-product" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($wireKey).''; ?>wire:key="<?php echo e($wireKey); ?>" x-data="{ resultsOpen: true }">
    <div class="ft-order-detail-add-product-head">
        <div>
            <strong>Add product</strong>
            <span>Search the Product master, select a product, then enter quantity and unit price.</span>
        </div>
        <button type="button" class="ft-order-detail-add-product-close" wire:click="<?php echo e($closeMethod); ?>" aria-label="Close add product">×</button>
    </div>

    <div class="ft-order-product-search-label">Search product</div>
    <div class="ft-order-product-search-host ft-order-detail-product-search" x-on:click.outside="resultsOpen = false">
        <div class="ft-order-product-search-input" :class="resultsOpen ? 'is-open' : ''">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input
                type="search"
                wire:model.live.debounce.220ms="<?php echo e($searchModel); ?>"
                x-on:focus="resultsOpen = true"
                x-on:keydown.escape="resultsOpen = false"
                placeholder="Search product name, product code or reference code"
                autocomplete="off"
                aria-label="Search product"
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $searchValue) !== ''): ?>
                <span class="ft-order-product-search-tools">
                    <button type="button" class="ft-order-product-search-clear" wire:click="$set('<?php echo e($searchModel); ?>', '')" x-on:click="resultsOpen = true" aria-label="Clear product search">&times;</button>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.origin.top>
            <div class="ft-order-product-results-head">
                <span>Top matches <b><?php echo e(number_format((int) $resultTotal)); ?> <?php echo e(\Illuminate\Support\Str::plural('result', (int) $resultTotal)); ?></b></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resultTotal > $searchResults->count()): ?>
                    <button type="button" wire:click="<?php echo e($showAllMethod); ?>">View all results <span>&nearr;</span></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-order-product-result-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isSelected = (int) ($selectedProduct?->id ?? 0) === (int) $product->id;
                        $resultImageUrl = $product->productImageUrl();
                        $resultReferenceCode = $product->productReferenceCode();
                        $resultDisplayCode = $product->productDisplayCode();
                        $resultMainCategory = $product->productMainCategory();
                        $resultProductCategory = trim((string) ($product->parent?->name ?? ''));
                        $resultSubCategory = trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category') ?: $product->productCatalogSummary()));
                        $resultClassification = collect([$resultMainCategory, $resultProductCategory, $resultSubCategory])->filter()->unique()->values();
                    ?>
                    <div class="ft-order-product-result <?php echo e($isSelected ? 'is-selected' : ''); ?>">
                        <span class="ft-order-product-thumb">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resultImageUrl): ?>
                                <img src="<?php echo e($resultImageUrl); ?>" alt="">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-order-product-result-copy">
                            <strong><?php echo e($product->name); ?></strong>
                            <span class="ft-order-product-code-line">Product code: <?php echo e($resultDisplayCode); ?> <i>&bull;</i> Ref: <?php echo e($resultReferenceCode ?: '—'); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resultClassification->isNotEmpty()): ?>
                                <small class="ft-order-product-classification">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resultClassification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($part); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?><i>&rsaquo;</i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <button type="button" class="ft-order-product-select-button <?php echo e($isSelected ? 'is-selected' : ''); ?>" wire:click="<?php echo e($selectMethod); ?>(<?php echo e($product->id); ?>)" x-on:click="resultsOpen = false"><?php echo e($isSelected ? 'Selected' : 'Select'); ?></button>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-order-product-no-results"><strong>No products found</strong><span>Try another product name, product code or reference code.</span></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct): ?>
        <div class="ft-order-detail-product-fields">
            <div class="ft-order-detail-product-field is-product">
                <label>Selected product</label>
                <div class="ft-order-detail-selected-product">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct->productImageUrl()): ?>
                        <img src="<?php echo e($selectedProduct->productImageUrl()); ?>" alt="">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span><strong><?php echo e($selectedProduct->name); ?></strong><small><?php echo e($selectedProduct->productDisplayCode()); ?></small></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($selectedErrorKey)): ?><span class="validation-error"><?php echo e($errors->first($selectedErrorKey)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-order-detail-product-field">
                <label>Product category</label>
                <input type="text" value="<?php echo e($categoryValue); ?>" readonly>
            </div>
            <div class="ft-order-detail-product-field">
                <label>Quantity *</label>
                <input type="number" min="1" step="1" wire:model="<?php echo e($quantityModel); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($quantityErrorKey)): ?><span class="validation-error"><?php echo e($errors->first($quantityErrorKey)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-order-detail-product-field">
                <label>Unit price *</label>
                <div class="ft-order-detail-price-field"><span><?php echo e($currencySymbol); ?></span><input type="number" min="0" step="0.01" wire:model="<?php echo e($unitPriceModel); ?>"></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($unitPriceErrorKey)): ?><span class="validation-error"><?php echo e($errors->first($unitPriceErrorKey)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($selectedErrorKey)): ?><div class="validation-error ft-order-detail-product-selection-error"><?php echo e($errors->first($selectedErrorKey)); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-order-detail-add-product-actions">
        <button type="button" class="ft-outline-btn" wire:click="<?php echo e($closeMethod); ?>">Cancel</button>
        <button type="button" class="ft-new-job-btn" wire:click="<?php echo e($saveMethod); ?>" wire:loading.attr="disabled" wire:target="<?php echo e($saveMethod); ?>" <?php if(!$selectedProduct): echo 'disabled'; endif; ?>>Add product</button>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-add-product.blade.php ENDPATH**/ ?>