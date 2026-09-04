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
    'productSupplierId' => null,
    'certificateUpload' => null,
    'templateUpload' => null,
    'removeCertificate' => false,
    'removeTemplate' => false,
    'categoryCreator' => null,
    'selectedMainCategory' => '',
    'selectedProductCategoryId' => null,
    'selectedSubcategory' => '',
    'pricePreview' => [],
    'remoteSurchargePreview' => [],
    'productOptions' => [],
    'productOptionUploads' => [],
    'shipmentUrgencies' => collect(),
    'productShipmentUrgencies' => [],
    'shipmentUrgencyPickerOpen' => false,
    'shipmentUrgencyPickerSelection' => [],
    'newProductCategoryMain' => '',
    'newSubcategoryProductCategoryId' => null,
    'taxonomyReady' => true,
    'shipmentOptionsReady' => true,
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
    'productSupplierId' => null,
    'certificateUpload' => null,
    'templateUpload' => null,
    'removeCertificate' => false,
    'removeTemplate' => false,
    'categoryCreator' => null,
    'selectedMainCategory' => '',
    'selectedProductCategoryId' => null,
    'selectedSubcategory' => '',
    'pricePreview' => [],
    'remoteSurchargePreview' => [],
    'productOptions' => [],
    'productOptionUploads' => [],
    'shipmentUrgencies' => collect(),
    'productShipmentUrgencies' => [],
    'shipmentUrgencyPickerOpen' => false,
    'shipmentUrgencyPickerSelection' => [],
    'newProductCategoryMain' => '',
    'newSubcategoryProductCategoryId' => null,
    'taxonomyReady' => true,
    'shipmentOptionsReady' => true,
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
    $existingDocs = collect($editProduct?->productDocuments() ?? []);
    $certificateDoc = $removeCertificate ? null : $existingDocs->firstWhere('kind', 'certificate');
    $templateDoc = $removeTemplate ? null : $existingDocs->firstWhere('kind', 'template');
    $productCategoryOptions = collect($parents)->map(fn($category) => [
        'id' => $category->id,
        'label' => $category->name,
        'meta' => $category->code,
    ]);
?>
<div class="ft-product-page ft-product-form-page ft-form-standard ft-form-standard--product" data-ft-feedback-scope="form" x-data="{dragging:false}">
    <div class="ft-product-page-breadcrumb"><button type="button" wire:click="close">Products</button><span>/</span><strong><?php echo e($isEdit ? 'Edit product' : 'Create product'); ?></strong></div>
    <header class="ft-product-form-header">
        <div><h1><?php echo e($isEdit ? 'Edit product' : 'Create product'); ?></h1><p><?php echo e($isEdit ? 'Update the product information, default supplier, availability and supporting documents.' : 'Add a product and link its category, default supplier, image and supporting documents.'); ?></p></div>
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
                    <div class="ft-product-search-select-wrap ft-product-default-supplier">
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-search-select','label' => 'Default supplier','property' => 'productSupplierId','type' => 'suppliers','context' => 'master-product','value' => $productSupplierId,'placeholder' => 'No default supplier','clearable' => true,'optional' => true,'fixedMenu' => true,'menuWidth' => 360,'searchPlaceholder' => 'Search supplier…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','label' => 'Default supplier','property' => 'productSupplierId','type' => 'suppliers','context' => 'master-product','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productSupplierId),'placeholder' => 'No default supplier','clearable' => true,'optional' => true,'fixed-menu' => true,'menu-width' => 360,'search-placeholder' => 'Search supplier…']); ?>
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
                        <small class="ft-product-help">When set, Create Order automatically uses this supplier for the product.</small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productSupplierId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                            <?php if (isset($component)) { $__componentOriginalb73b05fb764f63a65626f13f8ab62da9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb73b05fb764f63a65626f13f8ab62da9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.multi-select','data' => ['label' => 'Select clients','property' => 'productClientIds','type' => 'clients','context' => 'master-product','values' => $clientIds,'initialOptions' => $clients,'placeholder' => 'Search and select clients','fixedMenu' => true,'menuWidth' => 380,'maxSelected' => 100,'class' => 'ft-product-client-multi-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.multi-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Select clients','property' => 'productClientIds','type' => 'clients','context' => 'master-product','values' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientIds),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clients),'placeholder' => 'Search and select clients','fixed-menu' => true,'menu-width' => 380,'max-selected' => 100,'class' => 'ft-product-client-multi-select']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb73b05fb764f63a65626f13f8ab62da9)): ?>
<?php $attributes = $__attributesOriginalb73b05fb764f63a65626f13f8ab62da9; ?>
<?php unset($__attributesOriginalb73b05fb764f63a65626f13f8ab62da9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb73b05fb764f63a65626f13f8ab62da9)): ?>
<?php $component = $__componentOriginalb73b05fb764f63a65626f13f8ab62da9; ?>
<?php unset($__componentOriginalb73b05fb764f63a65626f13f8ab62da9); ?>
<?php endif; ?>
                            <small class="ft-product-help">Only selected clients can find and use this product. Results are loaded in bounded pages.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productClientIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php if (isset($component)) { $__componentOriginalce11a07acd8b47e338d25689bef957cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce11a07acd8b47e338d25689bef957cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.validation-message','data' => ['message' => $message]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.validation-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $attributes = $__attributesOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__attributesOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $component = $__componentOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__componentOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productClientIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php if (isset($component)) { $__componentOriginalce11a07acd8b47e338d25689bef957cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce11a07acd8b47e338d25689bef957cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.validation-message','data' => ['message' => $message]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.validation-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $attributes = $__attributesOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__attributesOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $component = $__componentOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__componentOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?><?php unset($message);
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

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxonomyReady): ?>
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
                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-search-select','wire:key' => 'product-main-category-filter-'.e($isEdit ? 'edit' : 'create').'','label' => 'Main category','property' => 'productFormMainCategory','action' => 'setProductTaxonomySelection','value' => $selectedMainCategory,'placeholder' => 'Select main category','options' => $mainCategories,'clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 360,'searchPlaceholder' => 'Search main category…','footerMessage' => 'Type to search the available main categories.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','wire:key' => 'product-main-category-filter-'.e($isEdit ? 'edit' : 'create').'','label' => 'Main category','property' => 'productFormMainCategory','action' => 'setProductTaxonomySelection','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedMainCategory),'placeholder' => 'Select main category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mainCategories),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 360,'search-placeholder' => 'Search main category…','footer-message' => 'Type to search the available main categories.']); ?>
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
                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-search-select','wire:key' => 'product-category-filter-'.e($isEdit ? 'edit' : 'create').'-'.e(md5((string) $selectedMainCategory)).'','label' => 'Product category','property' => 'parentId','action' => 'setProductTaxonomySelection','value' => $selectedProductCategoryId,'placeholder' => trim((string)$selectedMainCategory) === '' ? 'Select main category first' : 'Select product category','options' => $productCategoryOptions,'disabled' => trim((string)$selectedMainCategory) === '','clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search product category…','footerMessage' => 'Type to search product categories in the selected main category.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','wire:key' => 'product-category-filter-'.e($isEdit ? 'edit' : 'create').'-'.e(md5((string) $selectedMainCategory)).'','label' => 'Product category','property' => 'parentId','action' => 'setProductTaxonomySelection','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductCategoryId),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim((string)$selectedMainCategory) === '' ? 'Select main category first' : 'Select product category'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCategoryOptions),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim((string)$selectedMainCategory) === ''),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search product category…','footer-message' => 'Type to search product categories in the selected main category.']); ?>
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
                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-search-select','wire:key' => 'product-subcategory-filter-'.e($isEdit ? 'edit' : 'create').'-'.e((int) ($selectedProductCategoryId ?? 0)).'','label' => 'Subcategory','property' => 'productSubcategory','action' => 'setProductTaxonomySelection','value' => $selectedSubcategory,'placeholder' => $selectedProductCategoryId ? 'No subcategory' : 'Select product category first','options' => $subcategories,'disabled' => !$selectedProductCategoryId,'clearable' => true,'optional' => true,'fixedMenu' => true,'menuWidth' => 380,'searchPlaceholder' => 'Search subcategory…','footerMessage' => 'Subcategory is optional. Type to search available options.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select','wire:key' => 'product-subcategory-filter-'.e($isEdit ? 'edit' : 'create').'-'.e((int) ($selectedProductCategoryId ?? 0)).'','label' => 'Subcategory','property' => 'productSubcategory','action' => 'setProductTaxonomySelection','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedSubcategory),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedProductCategoryId ? 'No subcategory' : 'Select product category first'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subcategories),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$selectedProductCategoryId),'clearable' => true,'optional' => true,'fixed-menu' => true,'menu-width' => 380,'search-placeholder' => 'Search subcategory…','footer-message' => 'Subcategory is optional. Type to search available options.']); ?>
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

        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '2','title' => 'Category hierarchy','subtitle' => 'Category options load only when this section is needed.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '2','title' => 'Category hierarchy','subtitle' => 'Category options load only when this section is needed.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'product-taxonomy','rows' => 4]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'product-taxonomy','rows' => 4]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '3','title' => 'Product pricing','subtitle' => 'Paste the complete Quantity and Product price table directly from Excel.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '3','title' => 'Product pricing','subtitle' => 'Paste the complete Quantity and Product price table directly from Excel.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <label class="ft-product-field ft-product-price-table-field">
                <span>Price table <em>Optional</em></span>
                <textarea
                    wire:model.change="productPriceTable"
                    rows="6"
                    spellcheck="false"
                    placeholder="Paste the Excel price table here"
                ></textarea>
                <small>Excel price tables are detected automatically, including supplier tables with quantity columns.</small>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productPriceTable'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($pricePreview)): ?>
                <div class="ft-product-price-preview-wrap">
                    <table class="ft-product-price-preview">
                        <thead>
                            <tr>
                                <th>Quantity</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pricePreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priceRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <th><?php echo e(number_format((int) $priceRow['quantity'])); ?></th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Product price</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pricePreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priceRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <td><?php echo e((float) $priceRow['price'] === 0.0 ? '0' : rtrim(rtrim(number_format((float) $priceRow['price'], 6, '.', ''), '0'), '.')); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($remoteSurchargePreview)): ?>
                                <?php
                                    $remoteSurchargeByQuantity = collect($remoteSurchargePreview)->keyBy('quantity');
                                ?>
                                <tr>
                                    <th>Remote surcharge</th>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pricePreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priceRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php
                                            $remotePrice = data_get($remoteSurchargeByQuantity->get($priceRow['quantity']), 'price');
                                        ?>
                                        <td><?php echo e($remotePrice === null ? '—' : ((float) $remotePrice === 0.0 ? '0' : rtrim(rtrim(number_format((float) $remotePrice, 6, '.', ''), '0'), '.'))); ?></td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '4','title' => 'Product options','subtitle' => 'Add optional choices such as color, size or material. An option can also add an extra unit charge.','class' => 'ft-product-options-section']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '4','title' => 'Product options','subtitle' => 'Add optional choices such as color, size or material. An option can also add an extra unit charge.','class' => 'ft-product-options-section']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-product-options-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $optionUpload = $productOptionUploads[$optionIndex] ?? null;
                        $optionPreview = null;
                        if ($optionUpload && method_exists($optionUpload, 'temporaryUrl')) {
                            try { $optionPreview = $optionUpload->temporaryUrl(); } catch (\Throwable $e) { $optionPreview = null; }
                        }
                        $optionPreview ??= data_get($option, 'image_url');
                    ?>
                    <div class="ft-product-option-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-option-'.e(data_get($option, 'key', $optionIndex)).''; ?>wire:key="product-option-<?php echo e(data_get($option, 'key', $optionIndex)); ?>">
                        <label class="ft-product-field ft-product-option-label-field">
                            <span>Label <i>*</i></span>
                            <input wire:model.blur="productOptions.<?php echo e($optionIndex); ?>.label" placeholder="e.g. Red, Large, Cotton">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productOptions.'.$optionIndex.'.label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                        <label class="ft-product-field ft-product-option-charge-field">
                            <span>Extra charge <em>Optional</em></span>
                            <input type="number" min="0" step="0.01" wire:model.blur="productOptions.<?php echo e($optionIndex); ?>.extra_charge" placeholder="0.00" inputmode="decimal">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productOptions.'.$optionIndex.'.extra_charge'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                        <label class="ft-product-field ft-product-option-image-field">
                            <span>Image <em>Optional</em></span>
                            <span class="ft-product-option-image-input">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($optionPreview): ?><img src="<?php echo e($optionPreview); ?>" alt=""><?php else: ?><span class="ft-product-option-image-placeholder">No image</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="ft-product-option-image-button"><?php echo e($optionPreview ? 'Change image' : 'Choose image'); ?></span>
                                <input type="file" wire:model="productOptionUploads.<?php echo e($optionIndex); ?>" accept="image/png,image/jpeg,image/webp">
                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productOptionUploads.'.$optionIndex];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                        <button type="button" class="ft-product-option-remove" wire:click="removeProductOption(<?php echo e($optionIndex); ?>)" aria-label="Remove option">Remove</button>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <button type="button" class="ft-product-option-add" wire:click="addProductOption">+ Add option</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productOptions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipmentOptionsReady): ?>
            <?php if (isset($component)) { $__componentOriginal3fa9dea266082291e83bd227523e1837 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fa9dea266082291e83bd227523e1837 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-shipment-urgencies','data' => ['number' => '5','shipmentUrgencies' => $shipmentUrgencies,'selectedUrgencies' => $productShipmentUrgencies,'pickerOpen' => $shipmentUrgencyPickerOpen,'pickerSelection' => $shipmentUrgencyPickerSelection]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-shipment-urgencies'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '5','shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencies),'selected-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productShipmentUrgencies),'picker-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyPickerOpen),'picker-selection' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyPickerSelection)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fa9dea266082291e83bd227523e1837)): ?>
<?php $attributes = $__attributesOriginal3fa9dea266082291e83bd227523e1837; ?>
<?php unset($__attributesOriginal3fa9dea266082291e83bd227523e1837); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fa9dea266082291e83bd227523e1837)): ?>
<?php $component = $__componentOriginal3fa9dea266082291e83bd227523e1837; ?>
<?php unset($__componentOriginal3fa9dea266082291e83bd227523e1837); ?>
<?php endif; ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '5','title' => 'Shipping urgencies','subtitle' => 'Shipping urgency master data loads only when this section is needed.','class' => 'ft-product-shipping-section']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '5','title' => 'Shipping urgencies','subtitle' => 'Shipping urgency master data loads only when this section is needed.','class' => 'ft-product-shipping-section']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'product-shipping-urgencies','rows' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'product-shipping-urgencies','rows' => 3]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => '6','title' => 'Supporting documents','subtitle' => 'Add the product files now or replace them later while editing.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '6','title' => 'Supporting documents','subtitle' => 'Add the product files now or replace them later while editing.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.file-upload','data' => ['model' => 'productCertificateUpload','label' => 'Certificate & Test Report','hint' => \App\Support\AttachmentUpload::helperText(10),'accept' => \App\Support\AttachmentUpload::accept(),'upload' => $certificateUpload,'current' => $certificateDoc,'clearAction' => 'clearProductCertificateUpload','removeCurrentAction' => 'removeProductCertificate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'productCertificateUpload','label' => 'Certificate & Test Report','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::helperText(10)),'accept' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::accept()),'upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certificateUpload),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certificateDoc),'clear-action' => 'clearProductCertificateUpload','remove-current-action' => 'removeProductCertificate']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.file-upload','data' => ['model' => 'productTemplateUpload','label' => 'Product template','hint' => \App\Support\AttachmentUpload::helperText(10),'accept' => \App\Support\AttachmentUpload::accept(),'upload' => $templateUpload,'current' => $templateDoc,'clearAction' => 'clearProductTemplateUpload','removeCurrentAction' => 'removeProductTemplate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'productTemplateUpload','label' => 'Product template','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::helperText(10)),'accept' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::accept()),'upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($templateUpload),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($templateDoc),'clear-action' => 'clearProductTemplateUpload','remove-current-action' => 'removeProductTemplate']); ?>
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