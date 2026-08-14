<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'editProduct' => null,
    'parents' => collect(),
    'allProductCategories' => collect(),
    'mainCategories' => collect(),
    'subcategories' => collect(),
    'clients' => collect(),
    'canCreateProductCategory' => false,
    'productImagePreview' => null,
    'clientAvailabilityMode' => 'all',
    'clientIds' => [],
    'certificateUpload' => null,
    'templateUpload' => null,
    'removeCertificate' => false,
    'removeTemplate' => false,
    'categoryCreator' => null,
    'selectedMainCategory' => '',
    'selectedProductCategoryId' => null,
    'selectedSubcategory' => '',
    'newProductCategoryMain' => '',
    'newSubcategoryProductCategoryId' => null,
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
    'editProduct' => null,
    'parents' => collect(),
    'allProductCategories' => collect(),
    'mainCategories' => collect(),
    'subcategories' => collect(),
    'clients' => collect(),
    'canCreateProductCategory' => false,
    'productImagePreview' => null,
    'clientAvailabilityMode' => 'all',
    'clientIds' => [],
    'certificateUpload' => null,
    'templateUpload' => null,
    'removeCertificate' => false,
    'removeTemplate' => false,
    'categoryCreator' => null,
    'selectedMainCategory' => '',
    'selectedProductCategoryId' => null,
    'selectedSubcategory' => '',
    'newProductCategoryMain' => '',
    'newSubcategoryProductCategoryId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $isEdit = (bool) $editProduct;
    $displayCode = $editProduct?->productDisplayCode() ?? 'Generated after creation';
    $selectedClients = collect($clients)->whereIn('id', collect($clientIds)->map(fn($v)=>(int)$v)->all());
    $existingDocs = collect($editProduct?->productDocuments() ?? []);
    $certificateDoc = $removeCertificate ? null : $existingDocs->firstWhere('kind', 'certificate');
    $templateDoc = $removeTemplate ? null : $existingDocs->firstWhere('kind', 'template');
    $productCategoryOptions = collect($parents)->map(fn($category) => [
        'id' => $category->id,
        'label' => $category->name,
        'meta' => $category->code,
    ]);
?>
<div class="ft-product-page ft-product-form-page" x-data="{clientOpen:false, clientSearch:'', dragging:false}">
    <div class="ft-product-page-breadcrumb"><button type="button" wire:click="close">Products</button><span>/</span><strong><?php echo e($isEdit ? 'Edit product' : 'Create product'); ?></strong></div>
    <header class="ft-product-form-header">
        <div><h1><?php echo e($isEdit ? 'Edit product' : 'Create product'); ?></h1><p><?php echo e($isEdit ? 'Update the product information, availability and supporting documents.' : 'Add a product and link its category, image and supporting documents.'); ?></p></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit): ?>
            <div class="ft-product-form-top-actions"><button type="button" class="ft-product-page-btn is-secondary" wire:click="close">Cancel</button><button type="button" class="ft-product-page-btn is-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save,productImage,productCertificateUpload,productTemplateUpload">Save changes</button></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </header>

    <div class="ft-product-form-shell">
        <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '1','title' => 'Product information']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '1','title' => 'Product information']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-product-form-info-grid">
                <div class="ft-product-form-fields">
                    <div class="ft-form-grid ft-form-grid-3">
                        <label class="ft-product-field"><span>Product code</span><div class="ft-product-locked-field"><?php echo e($displayCode); ?> <span>⌑</span></div><small>Generated automatically after the product is created.</small></label>
                        <label class="ft-product-field"><span>Reference product code <em>Optional</em></span><input wire:model.blur="productReferenceCode" placeholder="Client or supplier reference"><small>Client or supplier reference used for search and matching.</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productReferenceCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <label class="ft-product-field"><span>Product name <i>*</i></span><input wire:model.blur="name" placeholder="Enter product name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    </div>
                    <label class="ft-product-field ft-product-size-field"><span>Product size</span><textarea wire:model.blur="productSize" rows="4" placeholder='Add size/specification details. Use a new line for each item, e.g. width, finished length, material, capacity or dimensions.'></textarea><small>Enter multiple size/specification details on separate lines so the information stays easy to read.</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productSize'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <div class="ft-product-client-scope">
                        <span class="ft-product-field-title">Client availability</span>
                        <div class="ft-product-radio-row"><label><input type="radio" value="all" wire:model.live="productClientAvailabilityMode"> All clients</label><label><input type="radio" value="specific" wire:model.live="productClientAvailabilityMode"> Selected clients</label></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientAvailabilityMode === 'specific'): ?>
                            <div class="ft-product-client-picker ft-product-assignee-style-picker" x-on:click.outside="clientOpen=false" x-on:keydown.escape.window="clientOpen=false">
                                <div role="button" tabindex="0" class="ft-product-client-input ft-product-client-trigger" x-on:click="clientOpen=!clientOpen" x-on:keydown.enter.prevent="clientOpen=!clientOpen" x-on:keydown.space.prevent="clientOpen=!clientOpen" :aria-expanded="clientOpen.toString()" aria-haspopup="listbox">
                                    <span class="ft-product-client-trigger-copy">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selectedClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <span class="ft-product-client-chip"><?php echo e($client->name); ?> <button type="button" wire:click.stop="toggleProductClient(<?php echo e($client->id); ?>)" aria-label="Remove <?php echo e($client->name); ?>">×</button></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <span class="ft-product-client-placeholder">Select clients</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                    <b class="ft-filter-chevron">⌄</b>
                                </div>
                                <div class="ft-product-client-menu ft-remote-filter-menu" x-cloak x-show="clientOpen">
                                    <input class="ft-remote-filter-search" type="text" role="searchbox" inputmode="search" x-model="clientSearch" placeholder="Search clients…" autocomplete="off">
                                    <div class="ft-remote-filter-list" role="listbox" aria-multiselectable="true">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php $clientSelected = in_array((int)$client->id, collect($clientIds)->map(fn($v)=>(int)$v)->all(), true); ?>
                                            <button type="button" class="ft-remote-filter-option" :aria-selected="<?php echo \Illuminate\Support\Js::from($clientSelected)->toHtml() ?>.toString()" x-show="!clientSearch || <?php echo \Illuminate\Support\Js::from(mb_strtolower($client->name.' '.$client->code))->toHtml() ?>.includes(clientSearch.toLowerCase())" wire:click="toggleProductClient(<?php echo e($client->id); ?>)">
                                                <span><?php echo e($client->name); ?></span><small><?php echo e($client->code); ?><?php echo e($clientSelected ? ' · Selected' : ''); ?></small>
                                            </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                    <div class="ft-remote-filter-message">Search by client name or code. Multiple clients can be selected.</div>
                                </div>
                            </div>
                            <small class="ft-product-help">Only selected clients can find and use this product.</small>
                            <button type="button" class="ft-product-inline-link" wire:click="selectAllProductClients">Select all clients</button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productClientIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="ft-product-image-column">
                    <span class="ft-product-field-title">Product image <em>Optional</em></span>
                    <div class="ft-product-image-drop" :class="dragging ? 'is-dragging':''" x-on:dragover.prevent="dragging=true" x-on:dragleave.prevent="dragging=false" x-on:drop.prevent="dragging=false;if($event.dataTransfer.files.length){const dt=new DataTransfer();dt.items.add($event.dataTransfer.files[0]);$refs.productImage.files=dt.files;$refs.productImage.dispatchEvent(new Event('change',{bubbles:true}))}" x-on:click="$refs.productImage.click()">
                        <input x-ref="productImage" type="file" wire:model="productImage" accept="image/png,image/jpeg,image/webp">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productImagePreview): ?><img src="<?php echo e($productImagePreview); ?>" alt="Product preview"><?php else: ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v14H4z"/><path d="m7 16 3.5-4 3 3 2-2 2.5 3"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <strong>Drop image or <span>browse</span></strong>
                    </div>
                    <small>PNG, JPG or WEBP · Max 5 MB</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productImage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $attributes = $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $component = $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '2','title' => 'Category hierarchy','subtitle' => 'Select from top to bottom. Each list is filtered by the selection above.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '2','title' => 'Category hierarchy','subtitle' => 'Select from top to bottom. Each list is filtered by the selection above.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-form-grid ft-form-grid-3 ft-category-grid">
                <div class="ft-product-search-select-wrap">
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-search-select','wire:key' => 'product-main-category-filter-'.e($isEdit ? 'edit' : 'create').'','label' => 'Main category','property' => 'productFormMainCategory','action' => 'setProductTaxonomySelection','value' => $selectedMainCategory,'placeholder' => 'Select main category','options' => $mainCategories,'clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 360,'searchPlaceholder' => 'Search main category…','footerMessage' => 'Type to search the available main categories.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','wire:key' => 'product-main-category-filter-'.e($isEdit ? 'edit' : 'create').'','label' => 'Main category','property' => 'productFormMainCategory','action' => 'setProductTaxonomySelection','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedMainCategory),'placeholder' => 'Select main category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mainCategories),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 360,'search-placeholder' => 'Search main category…','footer-message' => 'Type to search the available main categories.']); ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateProductCategory): ?><button type="button" class="ft-product-inline-link" wire:click="openCategoryCreator('main')">+ Create main category</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productFormMainCategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-product-search-select-wrap">
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-search-select','wire:key' => 'product-category-filter-'.e($isEdit ? 'edit' : 'create').'-'.e(md5((string) $selectedMainCategory)).'','label' => 'Product category','property' => 'parentId','action' => 'setProductTaxonomySelection','value' => $selectedProductCategoryId,'placeholder' => trim((string)$selectedMainCategory) === '' ? 'Select main category first' : 'Select product category','options' => $productCategoryOptions,'disabled' => trim((string)$selectedMainCategory) === '','clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search product category…','footerMessage' => 'Type to search product categories in the selected main category.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','wire:key' => 'product-category-filter-'.e($isEdit ? 'edit' : 'create').'-'.e(md5((string) $selectedMainCategory)).'','label' => 'Product category','property' => 'parentId','action' => 'setProductTaxonomySelection','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductCategoryId),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim((string)$selectedMainCategory) === '' ? 'Select main category first' : 'Select product category'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategoryOptions),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim((string)$selectedMainCategory) === ''),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search product category…','footer-message' => 'Type to search product categories in the selected main category.']); ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateProductCategory): ?><button type="button" class="ft-product-inline-link" wire:click="openCategoryCreator('product')">+ Create product category</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['parentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ft-product-search-select-wrap">
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['class' => 'ft-product-search-select','wire:key' => 'product-subcategory-filter-'.e($isEdit ? 'edit' : 'create').'-'.e((int) ($selectedProductCategoryId ?? 0)).'','label' => 'Subcategory','property' => 'productSubcategory','action' => 'setProductTaxonomySelection','value' => $selectedSubcategory,'placeholder' => $selectedProductCategoryId ? 'No subcategory' : 'Select product category first','options' => $subcategories,'disabled' => !$selectedProductCategoryId,'clearable' => true,'optional' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search subcategory…','footerMessage' => 'Subcategory is optional. Type to search available options.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','wire:key' => 'product-subcategory-filter-'.e($isEdit ? 'edit' : 'create').'-'.e((int) ($selectedProductCategoryId ?? 0)).'','label' => 'Subcategory','property' => 'productSubcategory','action' => 'setProductTaxonomySelection','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedSubcategory),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductCategoryId ? 'No subcategory' : 'Select product category first'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subcategories),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$selectedProductCategoryId),'clearable' => true,'optional' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search subcategory…','footer-message' => 'Subcategory is optional. Type to search available options.']); ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateProductCategory): ?><button type="button" class="ft-product-inline-link" wire:click="openCategoryCreator('sub')">+ Create subcategory</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productSubcategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <small class="ft-product-help">Missing a category? Create it here without leaving the product form. Codes are generated automatically.</small>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $attributes = $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $component = $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '3','title' => 'Supporting documents','subtitle' => 'Add the product files now or replace them later while editing.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '3','title' => 'Supporting documents','subtitle' => 'Add the product files now or replace them later while editing.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-product-support-grid is-friendly">
                <label class="ft-product-field ft-certificate-number-field"><span>Test certificate number <em>Optional</em></span><input wire:model.blur="productTestCertificateNumber" placeholder="T-26423684-06-R1"><small>Reference number printed on the test certificate.</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productTestCertificateNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <?php if (isset($component)) { $__componentOriginal34475bacdce0b9e344556f2df7511767 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34475bacdce0b9e344556f2df7511767 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.file-upload','data' => ['model' => 'productCertificateUpload','label' => 'Certificate & Test Report','hint' => 'PDF or DOCX · Max 10 MB','accept' => '.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document','upload' => $certificateUpload,'current' => $certificateDoc,'clearAction' => 'clearProductCertificateUpload','removeCurrentAction' => 'removeProductCertificate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'productCertificateUpload','label' => 'Certificate & Test Report','hint' => 'PDF or DOCX · Max 10 MB','accept' => '.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document','upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certificateUpload),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certificateDoc),'clear-action' => 'clearProductCertificateUpload','remove-current-action' => 'removeProductCertificate']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34475bacdce0b9e344556f2df7511767)): ?>
<?php $attributes = $__attributesOriginal34475bacdce0b9e344556f2df7511767; ?>
<?php unset($__attributesOriginal34475bacdce0b9e344556f2df7511767); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34475bacdce0b9e344556f2df7511767)): ?>
<?php $component = $__componentOriginal34475bacdce0b9e344556f2df7511767; ?>
<?php unset($__componentOriginal34475bacdce0b9e344556f2df7511767); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal34475bacdce0b9e344556f2df7511767 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34475bacdce0b9e344556f2df7511767 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.file-upload','data' => ['model' => 'productTemplateUpload','label' => 'Product template','hint' => 'PDF, AI or EPS · Max 10 MB','accept' => '.pdf,.ai,.eps,application/pdf,application/postscript','upload' => $templateUpload,'current' => $templateDoc,'clearAction' => 'clearProductTemplateUpload','removeCurrentAction' => 'removeProductTemplate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'productTemplateUpload','label' => 'Product template','hint' => 'PDF, AI or EPS · Max 10 MB','accept' => '.pdf,.ai,.eps,application/pdf,application/postscript','upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($templateUpload),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($templateDoc),'clear-action' => 'clearProductTemplateUpload','remove-current-action' => 'removeProductTemplate']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34475bacdce0b9e344556f2df7511767)): ?>
<?php $attributes = $__attributesOriginal34475bacdce0b9e344556f2df7511767; ?>
<?php unset($__attributesOriginal34475bacdce0b9e344556f2df7511767); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34475bacdce0b9e344556f2df7511767)): ?>
<?php $component = $__componentOriginal34475bacdce0b9e344556f2df7511767; ?>
<?php unset($__componentOriginal34475bacdce0b9e344556f2df7511767); ?>
<?php endif; ?>
            </div>
            <div class="ft-product-document-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/></svg>
                <span>These documents stay linked to this product and are available when it is added to an Inquiry or Order.</span>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $attributes = $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $component = $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>

        <footer class="ft-product-form-footer"><span>Required fields are marked <i>*</i></span><div><button type="button" class="ft-product-page-btn is-secondary" wire:click="close">Cancel</button><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEdit): ?><button type="button" class="ft-product-page-btn is-secondary" wire:click="saveProductDraft" wire:loading.attr="disabled">Save as draft</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><button type="button" class="ft-product-page-btn is-primary" wire:click="save" wire:loading.attr="disabled"><?php echo e($isEdit ? 'Save changes' : 'Create product'); ?></button></div></footer>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoryCreator): ?>
        <?php if (isset($component)) { $__componentOriginal987b4960d2f65750b5529cf1563219d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987b4960d2f65750b5529cf1563219d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-creator','data' => ['level' => $categoryCreator,'mainCategories' => $mainCategories,'productCategories' => $allProductCategories,'selectedMainCategory' => $newProductCategoryMain,'selectedProductCategoryId' => $newSubcategoryProductCategoryId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-creator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryCreator),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mainCategories),'product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allProductCategories),'selected-main-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newProductCategoryMain),'selected-product-category-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newSubcategoryProductCategoryId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal987b4960d2f65750b5529cf1563219d8)): ?>
<?php $attributes = $__attributesOriginal987b4960d2f65750b5529cf1563219d8; ?>
<?php unset($__attributesOriginal987b4960d2f65750b5529cf1563219d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal987b4960d2f65750b5529cf1563219d8)): ?>
<?php $component = $__componentOriginal987b4960d2f65750b5529cf1563219d8; ?>
<?php unset($__componentOriginal987b4960d2f65750b5529cf1563219d8); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/product-form.blade.php ENDPATH**/ ?>