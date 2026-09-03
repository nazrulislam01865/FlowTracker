<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'canEdit' => false, 'canDelete' => false, 'displayTimezone' => 'UTC', 'detailSectionsReady' => []]));

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

foreach (array_filter((['product', 'canEdit' => false, 'canDelete' => false, 'displayTimezone' => 'UTC', 'detailSectionsReady' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $pricingReady = (bool) ($detailSectionsReady['pricing'] ?? false);
    $optionsReady = (bool) ($detailSectionsReady['options'] ?? false);
    $documentsReady = (bool) ($detailSectionsReady['documents'] ?? false);
    $created = $product->created_at?->copy()->timezone($displayTimezone);
    $updated = $product->updated_at?->copy()->timezone($displayTimezone);
    $classification = collect([$product->productMainCategory(), $product->parent?->name, trim((string) data_get($product->metadata, 'sub_category'))])->filter()->values();

    // These helpers can parse legacy metadata and build large arrays. Keep that
    // CPU/HTML work behind viewport boundaries instead of doing it above fold.
    $priceBreakpoints = $pricingReady ? collect($product->productPriceBreakpoints()) : collect();
    $remoteSurchargeBreakpoints = $pricingReady ? collect($product->productRemoteSurchargeBreakpoints())->keyBy('quantity') : collect();
    $productOptions = $optionsReady ? collect($product->productOptions()) : collect();
    $shipmentUrgencyOptions = $optionsReady ? collect($product->productShipmentUrgencyOptions()) : collect();
    $documents = $documentsReady ? collect($product->productDocuments()) : collect();
    $certificate = $documentsReady ? $documents->firstWhere('kind', 'certificate') : null;
    $template = $documentsReady ? $documents->firstWhere('kind', 'template') : null;
?>
<div class="ft-product-page ft-product-view-page">
    <div class="ft-product-page-breadcrumb"><button type="button" wire:click="closeProductView">Products</button><span>/</span><strong><?php echo e($product->productDisplayCode()); ?></strong></div>
    <header class="ft-product-detail-header">
        <div>
            <h1><?php echo e($product->name); ?></h1>
            <div class="ft-product-detail-meta">
                <?php if (isset($component)) { $__componentOriginal18c3afe41550a8e1c941be61b2a6df77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c3afe41550a8e1c941be61b2a6df77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.status','data' => ['active' => $product->status === 'active']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->status === 'active')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c3afe41550a8e1c941be61b2a6df77)): ?>
<?php $attributes = $__attributesOriginal18c3afe41550a8e1c941be61b2a6df77; ?>
<?php unset($__attributesOriginal18c3afe41550a8e1c941be61b2a6df77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c3afe41550a8e1c941be61b2a6df77)): ?>
<?php $component = $__componentOriginal18c3afe41550a8e1c941be61b2a6df77; ?>
<?php unset($__componentOriginal18c3afe41550a8e1c941be61b2a6df77); ?>
<?php endif; ?>
                <span>Product code <?php echo e($product->productDisplayCode()); ?></span>
                <span>Updated <?php echo e($updated?->format('M j, Y') ?? '—'); ?></span>
                <span>Created by <?php echo e($product->creator?->name ?? '—'); ?></span>
            </div>
        </div>
        <div class="ft-product-detail-actions">
            <button type="button" class="ft-product-page-btn is-secondary" wire:click="closeProductView">Back to products</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" class="ft-product-page-btn is-primary" wire:click="editProduct(<?php echo e($product->id); ?>)">Edit product</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal52718073bb91d39800d9980236e22c53 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal52718073bb91d39800d9980236e22c53 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.action-menu','data' => ['productId' => $product->id,'isActive' => $product->status === 'active','canEdit' => $canEdit,'canDelete' => $canDelete]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.action-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->id),'is-active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->status === 'active'),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEdit),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDelete)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal52718073bb91d39800d9980236e22c53)): ?>
<?php $attributes = $__attributesOriginal52718073bb91d39800d9980236e22c53; ?>
<?php unset($__attributesOriginal52718073bb91d39800d9980236e22c53); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal52718073bb91d39800d9980236e22c53)): ?>
<?php $component = $__componentOriginal52718073bb91d39800d9980236e22c53; ?>
<?php unset($__componentOriginal52718073bb91d39800d9980236e22c53); ?>
<?php endif; ?>
        </div>
    </header>

    <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['title' => 'Product details']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Product details']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="ft-product-detail-grid">
            <dl class="ft-product-detail-list">
                <div><dt>Product code</dt><dd><?php echo e($product->productDisplayCode()); ?> <button type="button" class="ft-copy-btn" x-data x-on:click="navigator.clipboard?.writeText('<?php echo e($product->productDisplayCode()); ?>')" aria-label="Copy product code">⧉</button></dd></div>
                <div><dt>Reference product code</dt><dd><?php echo e($product->productReferenceCode() ?: '—'); ?></dd></div>
                <div><dt>Product name</dt><dd><?php echo e($product->name); ?></dd></div>
                <div><dt>Product size</dt><dd class="ft-product-detail-size"><?php echo e($product->productSize() ?: '—'); ?></dd></div>
            </dl>
            <div class="ft-product-detail-image-panel">
                <div class="ft-product-detail-image">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->productImageUrl()): ?><img src="<?php echo e($product->productImageUrl()); ?>" alt="<?php echo e($product->name); ?>"><?php else: ?><span class="ft-product-image-placeholder">No image</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->productImageUrl()): ?><a href="<?php echo e($product->productImageUrl()); ?>" target="_blank" rel="noopener">View full image</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['title' => 'Classification & availability']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Classification & availability']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="ft-product-classification-row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $classification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="ft-product-classification-step"><small><?php echo e(['Main category','Product category','Subcategory'][$index] ?? 'Category'); ?></small><?php if (isset($component)) { $__componentOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-chip','data' => ['label' => $label]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-chip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23)): ?>
<?php $attributes = $__attributesOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23; ?>
<?php unset($__attributesOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23)): ?>
<?php $component = $__componentOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23; ?>
<?php unset($__componentOriginalb7b81a0cde4bd94dcc8a8ff825a4ba23); ?>
<?php endif; ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?><span class="ft-product-classification-arrow">›</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <div class="ft-product-availability-detail">
            <div><strong>Client availability</strong><span><?php echo e($product->hasSpecificProductAvailability() ? 'Selected clients' : 'All clients'); ?></span><small><?php echo e($product->hasSpecificProductAvailability() ? 'Only these clients can find and use this product.' : 'All active clients can find and use this product.'); ?></small></div>
            <div class="ft-product-client-badges">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $product->productAvailabilityLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($label); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->hasSpecificProductAvailability()): ?><em><?php echo e(count($product->productAvailabilityLabels())); ?> clients</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pricingReady): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priceBreakpoints->isNotEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['title' => 'Product pricing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Product pricing']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-product-price-preview-wrap ft-product-detail-price-wrap">
                <table class="ft-product-price-preview">
                    <thead>
                        <tr>
                            <th>Quantity</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priceBreakpoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priceRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <th><?php echo e(number_format((int) $priceRow['quantity'])); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Product price</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priceBreakpoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priceRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td><?php echo e((float) $priceRow['price'] === 0.0 ? '0' : rtrim(rtrim(number_format((float) $priceRow['price'], 6, '.', ''), '0'), '.')); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($remoteSurchargeBreakpoints->isNotEmpty()): ?>
                            <tr>
                                <th>Remote surcharge</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priceBreakpoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priceRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $remotePrice = data_get($remoteSurchargeBreakpoints->get($priceRow['quantity']), 'price');
                                    ?>
                                    <td><?php echo e($remotePrice === null ? '—' : ((float) $remotePrice === 0.0 ? '0' : rtrim(rtrim(number_format((float) $remotePrice, 6, '.', ''), '0'), '.'))); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'pricing','method' => 'loadProductDetailSection','keyPrefix' => 'product-detail','rows' => 3,'message' => 'Loading product pricing when needed…','rootMargin' => '360px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'pricing','method' => 'loadProductDetailSection','key-prefix' => 'product-detail','rows' => 3,'message' => 'Loading product pricing when needed…','root-margin' => '360px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($optionsReady): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productOptions->isNotEmpty() || $shipmentUrgencyOptions->isNotEmpty()): ?>
        <div class="ft-product-options-shipping-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productOptions->isNotEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['title' => 'Product options','class' => 'ft-product-options-section ft-product-options-detail-section']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Product options','class' => 'ft-product-options-section ft-product-options-detail-section']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="ft-product-option-detail-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-product-option-detail-row">
                                <strong><?php echo e($option['label']); ?></strong>
                                <div class="ft-product-option-detail-charge">
                                    <small>Extra charge</small>
                                    <span><?php echo e((float) ($option['extra_charge'] ?? 0) > 0 ? number_format((float) $option['extra_charge'], 2) : '0.00'); ?></span>
                                </div>
                                <div class="ft-product-option-detail-image">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($option['image_url']): ?><img src="<?php echo e($option['image_url']); ?>" alt="<?php echo e($option['label']); ?>"><?php else: ?><span>No image</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shipmentUrgencyOptions->isNotEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['title' => 'Shipping urgencies','class' => 'ft-product-shipping-detail-section']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Shipping urgencies','class' => 'ft-product-shipping-detail-section']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="ft-product-shipping-detail-table-wrap">
                        <table class="ft-product-shipping-detail-table">
                            <thead>
                                <tr>
                                    <th>Shipping urgency</th>
                                    <th>Code</th>
                                    <th>Extra charge</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shipmentUrgencyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipmentUrgencyOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr>
                                        <td><strong><?php echo e($shipmentUrgencyOption['shipment_urgency_name'] ?: $shipmentUrgencyOption['shipment_urgency_code']); ?></strong></td>
                                        <td><?php echo e($shipmentUrgencyOption['shipment_urgency_code'] ?: '—'); ?></td>
                                        <td><?php echo e((float) $shipmentUrgencyOption['extra_charge'] > 0 ? number_format((float) $shipmentUrgencyOption['extra_charge'], 2) : '0.00'); ?></td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'options','method' => 'loadProductDetailSection','keyPrefix' => 'product-detail','rows' => 3,'message' => 'Loading product options and shipping data when needed…','rootMargin' => '320px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'options','method' => 'loadProductDetailSection','key-prefix' => 'product-detail','rows' => 3,'message' => 'Loading product options and shipping data when needed…','root-margin' => '320px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentsReady): ?>
    <?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['title' => 'Certificates & documents','class' => 'ft-product-documents-section']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Certificates & documents','class' => 'ft-product-documents-section']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="ft-product-documents-grid">
            <div class="ft-product-certificate-number"><small>Test certificate number</small><strong><?php echo e(data_get($product->metadata, 'test_certificate_number') ?: '—'); ?></strong></div>
            <?php if (isset($component)) { $__componentOriginal00958161278504347177de5bb665faa1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00958161278504347177de5bb665faa1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-document-card','data' => ['title' => 'Certificate & Test Report','document' => $certificate]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-document-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Certificate & Test Report','document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certificate)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal00958161278504347177de5bb665faa1)): ?>
<?php $attributes = $__attributesOriginal00958161278504347177de5bb665faa1; ?>
<?php unset($__attributesOriginal00958161278504347177de5bb665faa1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal00958161278504347177de5bb665faa1)): ?>
<?php $component = $__componentOriginal00958161278504347177de5bb665faa1; ?>
<?php unset($__componentOriginal00958161278504347177de5bb665faa1); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal00958161278504347177de5bb665faa1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00958161278504347177de5bb665faa1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-document-card','data' => ['title' => 'Product template','document' => $template]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-document-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Product template','document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($template)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal00958161278504347177de5bb665faa1)): ?>
<?php $attributes = $__attributesOriginal00958161278504347177de5bb665faa1; ?>
<?php unset($__attributesOriginal00958161278504347177de5bb665faa1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal00958161278504347177de5bb665faa1)): ?>
<?php $component = $__componentOriginal00958161278504347177de5bb665faa1; ?>
<?php unset($__componentOriginal00958161278504347177de5bb665faa1); ?>
<?php endif; ?>
        </div>
        <footer class="ft-product-audit-row" x-data="{activity:false}">
            <span>Created <?php echo e($created?->format('M j, Y') ?? '—'); ?> by <?php echo e($product->creator?->name ?? '—'); ?> <b>·</b> Last updated <?php echo e($updated?->format('M j, Y') ?? '—'); ?></span>
            <button type="button" x-on:click="activity=!activity">View activity</button>
            <div class="ft-product-activity-popover" x-cloak x-show="activity" x-on:click.outside="activity=false"><strong>Product activity</strong><span>Created <?php echo e($created?->format('M j, Y g:i A') ?? '—'); ?> by <?php echo e($product->creator?->name ?? '—'); ?></span><span>Last updated <?php echo e($updated?->format('M j, Y g:i A') ?? '—'); ?></span></div>
        </footer>
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
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'documents','method' => 'loadProductDetailSection','keyPrefix' => 'product-detail','rows' => 3,'message' => 'Loading certificates and documents when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'documents','method' => 'loadProductDetailSection','key-prefix' => 'product-detail','rows' => 3,'message' => 'Loading certificates and documents when needed…','root-margin' => '300px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/product-view.blade.php ENDPATH**/ ?>