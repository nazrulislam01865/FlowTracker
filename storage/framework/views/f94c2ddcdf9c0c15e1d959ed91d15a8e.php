<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'wireKey',
    'searchModel',
    'searchValue' => '',
    'searchResults' => collect(),
    'searchSuppliers' => collect(),
    'resultTotal' => 0,
    'showAllResults' => false,
    'showAllMethod',
    'selectMethod',
    'selectedProduct' => null,
    'selectedSupplier' => null,
    'categoryValue' => '',
    'quantityModel',
    'quantityValue' => 1,
    'unitPriceModel',
    'unitPriceValue' => '0.00',
    'supplierModel' => null,
    'supplierValue' => null,
    'supplierLabel' => '',
    'supplierLocked' => false,
    'supplierRequired' => false,
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
    'supplierErrorKey' => null,
    'showHeader' => true,
    'recordLabel' => 'Order',
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
    'searchSuppliers' => collect(),
    'resultTotal' => 0,
    'showAllResults' => false,
    'showAllMethod',
    'selectMethod',
    'selectedProduct' => null,
    'selectedSupplier' => null,
    'categoryValue' => '',
    'quantityModel',
    'quantityValue' => 1,
    'unitPriceModel',
    'unitPriceValue' => '0.00',
    'supplierModel' => null,
    'supplierValue' => null,
    'supplierLabel' => '',
    'supplierLocked' => false,
    'supplierRequired' => false,
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
    'supplierErrorKey' => null,
    'showHeader' => true,
    'recordLabel' => 'Order',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $searchValue = trim((string) $searchValue);
    $selectedImage = $selectedProduct?->productImageUrl();
    $selectedCode = (string) ($selectedProduct?->productDisplayCode() ?? '');
    $selectedName = (string) ($selectedProduct?->name ?? 'Product');
    $selectedSupplierName = $supplierRequired
        ? trim((string) $supplierLabel)
        : trim((string) ($selectedSupplier?->name ?? ''));
    $selectedSupplierExists = filled($selectedSupplierName) || filled($supplierValue);
    $selectedSupplierIsDefault = $supplierRequired
        && $selectedSupplier
        && (int) $supplierValue === (int) $selectedSupplier->id;
?>

<div
    class="ft-detail-add-product ft-detail-add-product--create-parity ft-product-quantity-prototype-match ft-product-quantity-prototype-exact"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($wireKey).''; ?>wire:key="<?php echo e($wireKey); ?>"
    x-data="{ resultsOpen: false, changingSupplier: false }"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showHeader): ?>
        <div class="ft-detail-add-product__parity-head">
            <div>
                <strong>Add product</strong>
                <span>Search the Product master and add the product using the same layout as Create <?php echo e($recordLabel); ?>.</span>
            </div>
            <button type="button" class="ft-detail-add-product__parity-close" wire:click="<?php echo e($closeMethod); ?>" aria-label="Close add product">&times;</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <label class="ft-order-product-search-label">Search product</label>
    <div class="ft-order-product-search-host" x-on:click.outside="resultsOpen = false">
        <div class="ft-order-product-search-input" :class="resultsOpen ? 'is-open' : ''">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input
                type="search"
                wire:model.live.debounce.220ms="<?php echo e($searchModel); ?>"
                x-on:focus="resultsOpen = true"
                x-on:input="resultsOpen = true"
                x-on:keydown.escape="resultsOpen = false"
                placeholder="Search product name, product code or reference code"
                autocomplete="off"
                aria-label="Search product"
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchValue !== ''): ?>
                <button type="button" class="ft-order-product-search-clear" wire:click="$set('<?php echo e($searchModel); ?>', '')" x-on:click="resultsOpen = false" aria-label="Clear product search">&times;</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.opacity.duration.120ms>
            <div class="ft-order-product-results-head">
                <span>Top matches <i>&middot;</i> <?php echo e(number_format((int) $searchResults->count())); ?> <?php echo e(\Illuminate\Support\Str::plural('result', (int) $searchResults->count())); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$showAllResults && $resultTotal > 0): ?>
                    <button type="button" wire:click="<?php echo e($showAllMethod); ?>">View all results <span>&nearr;</span></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-order-product-result-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if($product->type !== 'product'): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?>
                    <?php
                        $isSelected = (int) ($selectedProduct?->id ?? 0) === (int) $product->id;
                        $resultSupplier = $searchSuppliers->get((int) $product->id);
                        $supplierWords = preg_split('/\s+/', trim((string) ($resultSupplier?->name ?? ''))) ?: [];
                        $supplierInitials = strtoupper(substr(implode('', array_map(fn ($word) => substr($word, 0, 1), array_filter($supplierWords))), 0, 2));
                        $previewPrice = $product->productPriceForQuantity(1000);
                        $leadDays = (int) (data_get($product->metadata, 'lead_time_days') ?: data_get($product->metadata, 'supplier_lead_time_days') ?: 0);
                        $isPreferred = (bool) (data_get($product->metadata, 'supplier_preferred') ?: data_get($product->metadata, 'preferred_supplier'));
                        $resultReferenceCode = $product->productReferenceCode();
                        $resultDisplayCode = $product->productDisplayCode();
                        $resultImageUrl = $product->productImageUrl();
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
                            <span class="ft-order-product-code-line">Product code <?php echo e($resultDisplayCode); ?> <i>&middot;</i> Ref <?php echo e($resultReferenceCode ?: '—'); ?></span>
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
                        <button type="button" class="ft-order-product-select-button <?php echo e($isSelected ? 'is-selected' : ''); ?>" wire:click="<?php echo e($selectMethod); ?>(<?php echo e($product->id); ?>)" x-on:click="resultsOpen = false">Select</button>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-order-product-no-results">
                        <strong>No products found</strong>
                        <span>Try another product name, product code or reference code.</span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct): ?>
        <div class="ft-order-selected-products ft-order-selected-products--cards">
            <div class="ft-order-selected-product-cards">
                <article class="ft-order-selected-product-card ft-create-product-card ft-product-quantity-selected-row">
                    <div class="ft-pq-selected-product">
                        <span class="ft-order-product-thumb">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedImage): ?>
                                <img src="<?php echo e($selectedImage); ?>" alt="">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-pq-selected-product-copy">
                            <strong title="<?php echo e($selectedName); ?>"><?php echo e($selectedName); ?></strong>
                            <small><?php echo e($selectedCode ?: 'N/A'); ?> <i>&middot;</i> <?php echo e($categoryValue ?: 'Uncategorized'); ?></small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($selectedErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($selectedErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>

                    <label class="ft-pq-selected-field ft-pq-selected-quantity">
                        <span>Quantity</span>
                        <input type="number" min="1" max="999999999" step="1" wire:model.live.debounce.300ms="<?php echo e($quantityModel); ?>" aria-label="Quantity for <?php echo e($selectedName); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($quantityErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($quantityErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>

                    <div class="ft-pq-selected-field ft-pq-selected-supplier" x-on:create-order-product-supplier-selected.window="changingSupplier = false">
                        <span>Supplier for this <?php echo e(strtolower($recordLabel)); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierRequired): ?>
                            <div x-show="!changingSupplier" class="ft-pq-supplier-box <?php echo e(!$selectedSupplierExists ? 'is-missing' : ''); ?>">
                                <strong><?php echo e($selectedSupplierName ?: 'No default supplier linked'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedSupplierExists): ?><i>&middot;</i><b><?php echo e($selectedSupplierIsDefault ? 'Default' : 'Selected'); ?></b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button type="button" x-on:click="changingSupplier = true">Change</button>
                            </div>
                            <div x-cloak x-show="changingSupplier" class="ft-pq-supplier-picker">
                                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Supplier','type' => 'suppliers','context' => 'job-detail','property' => ''.e($supplierModel).'','value' => $supplierValue,'selectedLabel' => $supplierLabel ?: null,'placeholder' => 'Select supplier','searchPlaceholder' => 'Search supplier','clearable' => false,'action' => 'updateAddJobProductSupplierFromSelector','menuWidth' => 360,'hideLabel' => true,'fixedMenu' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier','type' => 'suppliers','context' => 'job-detail','property' => ''.e($supplierModel).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierValue),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierLabel ?: null),'placeholder' => 'Select supplier','search-placeholder' => 'Search supplier','clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'action' => 'updateAddJobProductSupplierFromSelector','menu-width' => 360,'hide-label' => true,'fixed-menu' => true]); ?>
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
                                <button type="button" class="ft-pq-supplier-cancel" x-on:click="changingSupplier = false" aria-label="Cancel supplier change">&times;</button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierErrorKey && $errors->has($supplierErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($supplierErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="ft-pq-supplier-box <?php echo e(!$selectedSupplierExists ? 'is-missing' : ''); ?>">
                                <strong><?php echo e($selectedSupplierName ?: 'No default supplier linked'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedSupplierExists): ?><i>&middot;</i><b>Default</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <label class="ft-pq-selected-field ft-pq-selected-price">
                        <span>Unit price</span>
                        <div class="ft-pq-price-input is-readonly">
                            <b><?php echo e($currencySymbol); ?></b>
                            <input
                                type="number"
                                min="0"
                                max="999999999999.99"
                                step="0.01"
                                value="<?php echo e(filled($unitPriceValue) ? number_format((float) $unitPriceValue, 2, '.', '') : ''); ?>"
                                placeholder="0.00"
                                aria-label="Unit price for <?php echo e($selectedName); ?>"
                                readonly
                            >
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($unitPriceErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($unitPriceErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>

                    <button type="button" class="ft-pq-remove-product" wire:click="<?php echo e($closeMethod); ?>">Remove</button>
                </article>
            </div>
        </div>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($selectedErrorKey)): ?><div class="validation-error ft-detail-add-product__selection-error"><?php echo e($errors->first($selectedErrorKey)); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-detail-add-product__parity-actions">
        <button type="button" class="ft-detail-add-product__cancel" wire:click="<?php echo e($closeMethod); ?>">Cancel</button>
        <button type="button" class="ft-detail-add-product__submit" wire:click="<?php echo e($saveMethod); ?>" wire:loading.attr="disabled" wire:target="<?php echo e($saveMethod); ?>" <?php if(!$selectedProduct || ($supplierRequired && !$supplierValue)): echo 'disabled'; endif; ?>>
            <span wire:loading.remove wire:target="<?php echo e($saveMethod); ?>">Add product</span>
            <span wire:loading wire:target="<?php echo e($saveMethod); ?>">Adding…</span>
        </button>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-add-product.blade.php ENDPATH**/ ?>