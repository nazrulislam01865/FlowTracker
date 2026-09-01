<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'wireKey',
    'variant' => 'order',
    'recordLabel' => 'Order',
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
    'unitPriceValue' => '0.00',
    'notesModel',
    'notesValue' => '',
    'supplierModel' => null,
    'supplierValue' => null,
    'supplierLabel' => '',
    'supplierEditable' => false,
    'supplierAction' => 'updateEditOrderProductSupplierFromSelector',
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
    'notesErrorKey',
    'supplierErrorKey' => null,
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
    'variant' => 'order',
    'recordLabel' => 'Order',
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
    'unitPriceValue' => '0.00',
    'notesModel',
    'notesValue' => '',
    'supplierModel' => null,
    'supplierValue' => null,
    'supplierLabel' => '',
    'supplierEditable' => false,
    'supplierAction' => 'updateEditOrderProductSupplierFromSelector',
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
    'notesErrorKey',
    'supplierErrorKey' => null,
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
    $selectedReference = (string) ($selectedProduct?->productReferenceCode() ?? '');
    $selectedName = (string) ($selectedProduct?->name ?? '');
    $selectedSupplierName = trim((string) ($supplierLabel ?: $selectedSupplier?->name ?: ''));
    $selectedSupplierExists = filled($selectedSupplierName) || filled($supplierValue);
?>

<?php if (isset($component)) { $__componentOriginal94b0001281a0d54388965f0be299fa5a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal94b0001281a0d54388965f0be299fa5a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-inline-editor','data' => ['variant' => $variant,'title' => 'Edit product','productName' => $selectedName,'meta' => 'Search a product first. Select one to load its category, supplier and quantity-based base price from Product Master.','class' => 'ft-detail-product-inline-editor--product-first','wire:key' => ''.e($wireKey).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-inline-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($variant),'title' => 'Edit product','product-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedName),'meta' => 'Search a product first. Select one to load its category, supplier and quantity-based base price from Product Master.','class' => 'ft-detail-product-inline-editor--product-first','wire:key' => ''.e($wireKey).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('close', null, []); ?> 
        <button type="button" class="ft-detail-product-inline-editor__close" wire:click="<?php echo e($closeMethod); ?>" aria-label="Cancel editing">&times;</button>
     <?php $__env->endSlot(); ?>

    <div
        class="ft-detail-product-edit-flow"
        x-data="{ resultsOpen: false, changingSupplier: false }"
        x-on:detail-product-edit-selected.window="resultsOpen = false; changingSupplier = false"
        x-on:detail-product-edit-supplier-selected.window="changingSupplier = false"
    >
        <div class="ft-detail-product-edit-flow__search">
            <label class="ft-order-product-search-label">Search product <b>*</b></label>
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
                        <button type="button" class="ft-order-product-search-clear" wire:click="$set('<?php echo e($searchModel); ?>', '')" x-on:click="resultsOpen = true" aria-label="Clear product search">&times;</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-order-product-results ft-detail-product-edit-flow__results" x-cloak x-show="resultsOpen" x-transition.opacity.duration.120ms>
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
                                $previewPrice = $product->productPriceForQuantity(max(1, (int) $quantityValue));
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
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previewPrice !== null): ?><i>&middot;</i><span><?php echo e($currencySymbol); ?><?php echo e(number_format((float) $previewPrice, 2)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php else: ?>
                                            <b class="ft-order-product-result-supplier-badge is-empty">—</b>
                                            <strong>No default supplier</strong>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </small>
                                </span>
                                <button type="button" class="ft-order-product-select-button <?php echo e($isSelected ? 'is-selected' : ''); ?>" wire:click="<?php echo e($selectMethod); ?>(<?php echo e($product->id); ?>)" x-on:click="resultsOpen = false"><?php echo e($isSelected ? 'Selected' : 'Select'); ?></button>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($selectedErrorKey)): ?>
                <div class="validation-error ft-detail-product-edit-flow__selection-error"><?php echo e($errors->first($selectedErrorKey)); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct): ?>
            <div class="ft-detail-product-edit-flow__selected">
                <div class="ft-detail-product-edit-flow__selected-head">
                    <span class="ft-order-product-thumb">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedImage): ?>
                            <img src="<?php echo e($selectedImage); ?>" alt="">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <span class="ft-detail-product-edit-flow__selected-copy">
                        <small>Selected product</small>
                        <strong><?php echo e($selectedName); ?></strong>
                        <span>Product code <?php echo e($selectedCode ?: '—'); ?> <i>&middot;</i> Ref <?php echo e($selectedReference ?: '—'); ?></span>
                    </span>
                </div>

                <div class="ft-detail-product-edit-flow__details">
                    <div class="ft-detail-product-edit-flow__readonly-field">
                        <span>Category</span>
                        <strong><?php echo e($categoryValue ?: 'Uncategorized'); ?></strong>
                    </div>

                    <div class="ft-detail-product-edit-flow__supplier" x-data="{ changingSupplier: false }" x-on:detail-product-edit-supplier-selected.window="changingSupplier = false">
                        <span>Supplier</span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierEditable): ?>
                            <div x-show="!changingSupplier" class="ft-pq-supplier-box <?php echo e(!$selectedSupplierExists ? 'is-missing' : ''); ?>">
                                <strong><?php echo e($selectedSupplierName ?: 'No default supplier linked'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedSupplierExists): ?><i>&middot;</i><b>Selected</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button type="button" x-on:click="changingSupplier = true">Change</button>
                            </div>
                            <div x-cloak x-show="changingSupplier" class="ft-pq-supplier-picker">
                                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Supplier','type' => 'suppliers','context' => 'order-detail-product-edit','property' => ''.e($supplierModel).'','value' => $supplierValue,'selectedLabel' => $supplierLabel ?: null,'placeholder' => 'Select supplier','searchPlaceholder' => 'Search supplier','clearable' => false,'action' => ''.e($supplierAction).'','menuWidth' => 360,'hideLabel' => true,'fixedMenu' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier','type' => 'suppliers','context' => 'order-detail-product-edit','property' => ''.e($supplierModel).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierValue),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierLabel ?: null),'placeholder' => 'Select supplier','search-placeholder' => 'Search supplier','clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'action' => ''.e($supplierAction).'','menu-width' => 360,'hide-label' => true,'fixed-menu' => true]); ?>
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
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierErrorKey && $errors->has($supplierErrorKey)): ?>
                                <small class="validation-error"><?php echo e($errors->first($supplierErrorKey)); ?></small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="ft-detail-product-edit-flow__readonly-value <?php echo e(!$selectedSupplierExists ? 'is-missing' : ''); ?>">
                                <strong><?php echo e($selectedSupplierName ?: 'No default supplier linked'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedSupplierExists): ?><span>Product Master default</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <label class="ft-detail-product-edit-flow__input-field">
                        <span>Quantity <b>*</b></span>
                        <input type="number" min="1" max="999999999" step="1" wire:model.live.debounce.300ms="<?php echo e($quantityModel); ?>" inputmode="numeric">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($quantityErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($quantityErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>

                    <label class="ft-detail-product-edit-flow__input-field">
                        <span>Unit price</span>
                        <div class="ft-detail-product-inline-editor__price is-readonly">
                            <i><?php echo e($currencySymbol); ?></i>
                            <input type="number" value="<?php echo e(filled($unitPriceValue) ? number_format((float) $unitPriceValue, 2, '.', '') : '0.00'); ?>" readonly tabindex="-1" aria-label="Unit price">
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($unitPriceErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($unitPriceErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                </div>

                <label class="ft-detail-product-edit-flow__notes">
                    <span>Notes</span>
                    <input type="text" maxlength="2000" wire:model="<?php echo e($notesModel); ?>" placeholder="Add product notes">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($notesErrorKey)): ?><small class="validation-error"><?php echo e($errors->first($notesErrorKey)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
            </div>
        <?php else: ?>
            <div class="ft-detail-product-edit-flow__empty">
                <strong>Select a product to continue</strong>
                <span>Category, supplier and unit price will appear automatically after you select a Product Master record.</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

     <?php $__env->slot('actions', null, []); ?> 
        <button type="button" class="ft-detail-product-inline-editor__cancel" wire:click="<?php echo e($closeMethod); ?>" wire:loading.attr="disabled" wire:target="<?php echo e($saveMethod); ?>">Cancel</button>
        <button
            type="button"
            class="ft-detail-product-inline-editor__save"
            wire:click="<?php echo e($saveMethod); ?>"
            wire:loading.attr="disabled"
            wire:target="<?php echo e($saveMethod); ?>"
            <?php if(!$selectedProduct || ($supplierEditable && !$supplierValue)): echo 'disabled'; endif; ?>
        >
            <span aria-hidden="true">&#10003;</span>
            <span wire:loading.remove wire:target="<?php echo e($saveMethod); ?>">Save changes</span>
            <span wire:loading wire:target="<?php echo e($saveMethod); ?>">Saving...</span>
        </button>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal94b0001281a0d54388965f0be299fa5a)): ?>
<?php $attributes = $__attributesOriginal94b0001281a0d54388965f0be299fa5a; ?>
<?php unset($__attributesOriginal94b0001281a0d54388965f0be299fa5a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal94b0001281a0d54388965f0be299fa5a)): ?>
<?php $component = $__componentOriginal94b0001281a0d54388965f0be299fa5a; ?>
<?php unset($__componentOriginal94b0001281a0d54388965f0be299fa5a); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-product-edit.blade.php ENDPATH**/ ?>