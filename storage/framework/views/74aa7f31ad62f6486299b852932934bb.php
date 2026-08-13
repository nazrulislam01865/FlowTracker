<?php
    $categorySearchValue = trim((string) $newProductCategorySearch);
    $categorySuggestions = $categorySearchValue === '' ? $productCategories->take(6) : $newProductCategoryMatches;
    $hasDuplicateCode = (bool) $duplicateProduct;
    $hasSimilarProductName = $newProductSimilarProducts->isNotEmpty();
    $manualProductCode = trim((string) $newProductCode);
    $productCodeFormatValid = $manualProductCode !== ''
        && mb_strlen($manualProductCode) <= 40
        && preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $manualProductCode) === 1;
    $productCodeReady = $productCodeFormatValid && !$hasDuplicateCode;
    $productCategoryReady = $productCodeReady && (bool) $newProductSelectedCategory;
    $productNameReady = $productCategoryReady && trim((string) $newProductName) !== '';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($catalogReady): ?>
    <?php if (isset($component)) { $__componentOriginal39599d37e505322c8766761a0aaccbf0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal39599d37e505322c8766761a0aaccbf0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.create-product-quantity','data' => ['rows' => $createProductRows,'rowsProperty' => 'createProductRows','removeMethod' => 'removeCreateProductRow','required' => false,'activeProductCount' => $activeProductCount,'productSearchResults' => $productSearchResults,'productResultTotal' => $productResultTotal,'createProductSearch' => $createProductSearch,'createProductCategoryFilter' => $createProductCategoryFilter,'createProductShowAllResults' => $createProductShowAllResults,'productCategories' => $productCategories,'selectedProductDetails' => $selectedProductDetails,'canViewProductCategories' => $canViewProductCategories,'canCreateCatalogProduct' => $canCreateCatalogProduct,'wireKey' => 'create-inquiry-products-ready','rowKeyPrefix' => 'selected-inquiry-product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.create-product-quantity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductRows),'rows-property' => 'createProductRows','remove-method' => 'removeCreateProductRow','required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'active-product-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeProductCount),'product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSearchResults),'product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productResultTotal),'create-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductSearch),'create-product-category-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductCategoryFilter),'create-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createProductShowAllResults),'product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategories),'selected-product-details' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductDetails),'can-view-product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canViewProductCategories),'can-create-catalog-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateCatalogProduct),'wire-key' => 'create-inquiry-products-ready','row-key-prefix' => 'selected-inquiry-product']); ?>
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
<?php else: ?>
    <?php if (isset($component)) { $__componentOriginal732a8e3f5371418be0dfaaa000db0561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal732a8e3f5371418be0dfaaa000db0561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create-section-placeholder','data' => ['number' => '2','title' => 'Products & quantities','section' => 'catalog','rows' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create-section-placeholder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '2','title' => 'Products & quantities','section' => 'catalog','rows' => 3]); ?>
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
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreateOrderProductModal): ?>
    <div class="overlay livewire-overlay ft-product-create-overlay" wire:click.self="closeCreateOrderProductModal"></div>
    <div
        class="modal livewire-modal ft-product-create-modal ft-order-create-product-modal"
        x-data="{ categoryOpen: false, creatingCategory: false, dragging: false }"
        x-on:create-order-product-category-selected.window="categoryOpen = false; creatingCategory = false"
        x-on:create-order-product-category-created.window="categoryOpen = false; creatingCategory = false"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ft-order-create-product-title"
    >
        <div class="ft-product-create-head">
            <div>
                <h2 id="ft-order-create-product-title">Create new product</h2>
                <p>Add this product to the catalog and select it for this Inquiry.</p>
            </div>
            <button type="button" class="ft-product-create-close" wire:click="closeCreateOrderProductModal" aria-label="Close">&times;</button>
        </div>

        <div class="ft-product-create-body">
            <div class="ft-product-create-field ft-product-sequence-field is-current-step">
                <label><b class="ft-product-step-number">1</b> SKU / Product code <span>*</span></label>
                <div class="ft-product-create-input-wrap <?php echo e($hasDuplicateCode ? 'is-duplicate' : (($manualProductCode !== '' && !$productCodeFormatValid) ? 'has-warning' : ($productCodeReady ? 'is-valid' : ''))); ?>">
                    <input
                        type="text"
                        wire:model.live.debounce.220ms="newProductCode"
                        maxlength="40"
                        autocomplete="off"
                        placeholder="Enter product code, e.g. TS-SUB-001"
                        aria-describedby="ft-new-product-code-help"
                    >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDuplicateCode || ($manualProductCode !== '' && !$productCodeFormatValid)): ?>
                        <svg class="ft-order-product-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></svg>
                    <?php elseif($productCodeReady): ?>
                        <svg class="ft-product-valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4L19 6"/></svg>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDuplicateCode): ?>
                    <div class="ft-order-product-duplicate-message"><?php echo e($duplicateProduct->trashed() ? 'This product code is reserved by an archived product.' : 'This product code already exists.'); ?></div>
                    <div class="ft-order-product-duplicate-card">
                        <span class="ft-order-product-thumb">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($duplicateProduct->productImageUrl()): ?>
                                <img src="<?php echo e($duplicateProduct->productImageUrl()); ?>" alt="">
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-order-product-duplicate-copy">
                            <strong><?php echo e($duplicateProduct->name); ?></strong>
                            <span>SKU: <?php echo e($duplicateProduct->code); ?> <i>&bull;</i> <?php echo e($duplicateProduct->parent?->name ?? 'Uncategorized'); ?></span>
                            <small class="<?php echo e(!$duplicateProduct->trashed() && $duplicateProduct->status === 'active' ? 'is-active' : 'is-inactive'); ?>"><?php echo e($duplicateProduct->trashed() ? 'Archived' : ucfirst($duplicateProduct->status)); ?></small>
                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$duplicateProduct->trashed() && $duplicateProduct->status === 'active'): ?>
                            <button type="button" wire:click="selectDuplicateCreateOrderProduct(<?php echo e($duplicateProduct->id); ?>)">Select existing</button>
                        <?php else: ?>
                            <span class="ft-order-product-duplicate-inactive"><?php echo e($duplicateProduct->trashed() ? 'Archived product' : 'Inactive product'); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($manualProductCode !== '' && !$productCodeFormatValid): ?>
                        <small id="ft-new-product-code-help" class="ft-product-step-error">Use letters, numbers, dots, dashes or underscores only. Maximum 40 characters.</small>
                    <?php else: ?>
                        <small id="ft-new-product-code-help">Enter the SKU / product code manually. It must be unique. Category selection unlocks after a valid code is entered.</small>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newProductCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewProductCategories): ?>
            <div class="ft-product-create-field ft-product-category-field ft-product-sequence-field <?php echo e(!$productCodeReady ? 'is-step-locked' : ''); ?>" x-on:click.outside="categoryOpen = false">
                <label><b class="ft-product-step-number">2</b> Product category <span>*</span></label>
                <div class="ft-product-category-picker">
                    <div class="ft-product-category-input-wrap" :class="categoryOpen ? 'is-open' : ''">
                        <svg class="ft-product-category-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.220ms="newProductCategorySearch"
                            x-on:focus="categoryOpen = true"
                            x-on:click="categoryOpen = true"
                            x-on:keydown.escape="categoryOpen = false"
                            placeholder="<?php echo e($productCodeReady ? 'Search or create a category' : 'Enter product code first'); ?>"
                            autocomplete="off"
                            aria-label="Product category"
                            <?php if(!$productCodeReady): echo 'disabled'; endif; ?>
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newProductSelectedCategory): ?>
                            <svg class="ft-order-product-category-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4L19 6"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <svg class="ft-product-category-chevron" :class="categoryOpen ? 'is-open' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 10 5 5 5-5"/></svg>
                    </div>

                    <div class="ft-product-category-menu" x-cloak x-show="categoryOpen && <?php echo e($productCodeReady ? 'true' : 'false'); ?>" x-transition.origin.top>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categorySearchValue !== '' && $newProductCategoryMatches->isEmpty()): ?>
                            <div class="ft-product-category-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                <div><strong>No category found</strong><span>No category matches '<?php echo e($categorySearchValue); ?>'.</span></div>
                            </div>
                        <?php elseif($categorySuggestions->isNotEmpty()): ?>
                            <div class="ft-product-category-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categorySuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button type="button" wire:click="selectCreateOrderProductCategory(<?php echo e($category->id); ?>)" class="<?php echo e((int) $newProductCategoryId === (int) $category->id ? 'is-selected' : ''); ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                        <span><?php echo e($category->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $newProductCategoryId === (int) $category->id): ?>
                                            <svg class="ft-product-category-row-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productCodeReady && $categorySearchValue !== '' && !$newProductHasExactCategory && $canCreateProductCategory): ?>
                            <button type="button" class="ft-product-category-create-row" wire:click="beginCreateOrderProductCategory" x-on:click="creatingCategory = true; categoryOpen = true">
                                <span class="ft-product-category-plus">+</span>
                                <span class="ft-product-category-create-copy"><strong>Create '<?php echo e($categorySearchValue); ?>'</strong><small>The category will be created and selected for this product.</small></span>
                                <span class="ft-product-category-permission">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="6"/><path d="M9 7V5.8a3 3 0 0 1 6 0V7M12 11v4"/></svg>
                                    You have permission
                                </span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categorySearchValue !== '' && $newProductSimilarCategories->isNotEmpty()): ?>
                            <div class="ft-product-category-similar">
                                <span>Similar categories</span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $newProductSimilarCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button type="button" wire:click="selectCreateOrderProductCategory(<?php echo e($category->id); ?>)">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                        <span><?php echo e($category->name); ?></span>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="ft-product-category-create-form" x-cloak x-show="creatingCategory" x-transition>
                            <label>New category name</label>
                            <div>
                                <input type="text" wire:model="newProductCategoryName" maxlength="255" aria-label="New category name">
                                <button type="button" class="ghost" wire:click="cancelCreateOrderProductCategory" x-on:click="creatingCategory = false">Cancel</button>
                                <button type="button" class="primary" wire:click="createCreateOrderProductCategory" wire:loading.attr="disabled" wire:target="createCreateOrderProductCategory">Create category</button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newProductCategoryName'];
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$productCodeReady): ?>
                    <small class="ft-product-step-hint">Complete step 1 with a valid, unused SKU / product code to unlock category selection.</small>
                <?php elseif(!$newProductSelectedCategory): ?>
                    <small class="ft-product-step-hint">Select an existing category or create a new category before continuing.</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newProductCategoryId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php else: ?>
            <div class="ft-product-create-field ft-product-category-field ft-product-sequence-field is-step-locked">
                <label><b class="ft-product-step-number">2</b> Product category <span>*</span></label>
                <div class="ft-create-note">Product Categories <b>View</b> permission is required to select a category for a new product.</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-product-create-field ft-product-sequence-field <?php echo e(!$productCategoryReady ? 'is-step-locked' : ''); ?>">
                <label><b class="ft-product-step-number">3</b> Product name <span>*</span></label>
                <div class="ft-product-create-input-wrap <?php echo e($hasSimilarProductName ? 'has-warning' : (trim($newProductName) !== '' ? 'is-valid' : '')); ?>">
                    <input
                        type="text"
                        wire:model.live.debounce.220ms="newProductName"
                        maxlength="255"
                        autocomplete="off"
                        placeholder="<?php echo e($productCategoryReady ? 'Enter product name' : 'Select a product category first'); ?>"
                        <?php if(!$productCategoryReady): echo 'disabled'; endif; ?>
                    >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSimilarProductName): ?>
                        <svg class="ft-order-product-warning-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/></svg>
                    <?php elseif(trim($newProductName) !== ''): ?>
                        <svg class="ft-product-valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4L19 6"/></svg>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSimilarProductName): ?>
                    <div class="ft-order-product-name-warning">
                        <span>A product with this name may already exist.</span>
                        <button type="button" wire:click="viewSimilarCreateProducts">View similar products</button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$productCategoryReady): ?>
                    <small class="ft-product-step-hint">Complete step 2 first. Product name becomes available after a category is selected.</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newProductName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-product-create-field ft-product-create-image-field ft-product-sequence-field <?php echo e(!$productNameReady ? 'is-step-locked' : ''); ?>">
                <label><b class="ft-product-step-number">4</b> Product image <em>Optional</em></label>
                <div class="ft-product-create-image-row">
                    <div
                        class="ft-product-drop-zone <?php echo e(!$productNameReady ? 'is-step-disabled' : ''); ?>"
                        <?php if($productNameReady): ?>
                            :class="dragging ? 'is-dragging' : ''"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { const dt = new DataTransfer(); dt.items.add($event.dataTransfer.files[0]); $refs.orderProductFile.files = dt.files; $refs.orderProductFile.dispatchEvent(new Event('change', { bubbles: true })); }"
                            x-on:click="$refs.orderProductFile.click()"
                            role="button"
                            tabindex="0"
                            x-on:keydown.enter.prevent="$refs.orderProductFile.click()"
                            x-on:keydown.space.prevent="$refs.orderProductFile.click()"
                        <?php else: ?>
                            aria-disabled="true"
                        <?php endif; ?>
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productNameReady): ?>
                            <input x-ref="orderProductFile" type="file" wire:model="newProductImage" accept="image/png,image/jpeg,image/webp" tabindex="-1">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v14H4z"/><path d="m7 16 3.5-4 3 3 2-2 2.5 3"/><circle cx="15.5" cy="9" r="1.4"/></svg>
                        <div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productNameReady): ?>
                                <strong wire:loading.remove wire:target="newProductImage">Drop an image here or <span>browse</span></strong>
                                <strong wire:loading wire:target="newProductImage">Preparing image...</strong>
                                <small>PNG, JPG or WEBP&nbsp;&nbsp;&bull;&nbsp;&nbsp;Max 5 MB</small>
                            <?php else: ?>
                                <strong>Complete product name first</strong>
                                <small>Image upload unlocks after steps 1–3 are complete.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="ft-product-create-image-preview">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newProductImagePreview): ?>
                            <img src="<?php echo e($newProductImagePreview); ?>" alt="Product image preview">
                        <?php else: ?>
                            <span aria-hidden="true"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$productNameReady): ?>
                    <small class="ft-product-step-hint">Complete step 3 to enable the optional product image.</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newProductImage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDuplicateCode || $hasSimilarProductName): ?>
                <div class="ft-order-product-review-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/></svg>
                    <span>Review the existing product before creating a duplicate.</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-product-create-foot">
            <div class="ft-product-create-permission-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="6"/><path d="M9 7V5.8a3 3 0 0 1 6 0V7M12 11v4"/></svg>
                <span>
                    Product create permission granted.
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateProductCategory): ?>
                        Product Category create permission granted.
                    <?php elseif($canViewProductCategories): ?>
                        Product Categories are view-only for this role.
                    <?php else: ?>
                        Product Category view permission is required to finish a new product.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
            <div class="ft-product-create-actions">
                <button type="button" class="ghost" wire:click="closeCreateOrderProductModal">Cancel</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDuplicateCode && !$duplicateProduct->trashed() && $duplicateProduct->status === 'active'): ?>
                    <button type="button" class="primary ft-order-select-existing-button" wire:click="selectDuplicateCreateOrderProduct(<?php echo e($duplicateProduct->id); ?>)">Select existing product</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button
                    type="button"
                    class="primary"
                    wire:click="createAndAddOrderProduct"
                    wire:loading.attr="disabled"
                    wire:target="createAndAddOrderProduct,newProductImage"
                    <?php if(!$productNameReady || $hasDuplicateCode): echo 'disabled'; endif; ?>
                >Create &amp; add product</button>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/create-products.blade.php ENDPATH**/ ?>