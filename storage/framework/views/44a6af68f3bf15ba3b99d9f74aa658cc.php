<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments',
    'priority'=>'Medium','productionUrgencies'=>collect(),'shipmentUrgencies'=>collect(),'productionUrgencyIds'=>[],'shipmentUrgencyIds'=>[],'isRepeatedOrder'=>false,'repeatedOrderNumber'=>'',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'productCategories'=>collect(),'productSearchResults'=>collect(),'selectedProductDetails'=>collect(),'activeProductCount'=>0,'productResultTotal'=>0,
    'canUseOrderProductSelector'=>false,'canCreateCatalogProduct'=>false,'canViewProductCategories'=>false,'canCreateProductCategory'=>false,'duplicateProduct'=>null,'newProductCategoryMatches'=>collect(),'newProductSimilarCategories'=>collect(),
    'newProductSimilarProducts'=>collect(),'newProductSelectedCategory'=>null,'newProductHasExactCategory'=>false,'newProductImagePreview'=>null,
    'createProductSearch'=>'','createProductCategoryFilter'=>'','createProductShowAllResults'=>false,'showCreateOrderProductModal'=>false,
    'newProductCode'=>'','newProductCategoryId'=>null,'newProductCategorySearch'=>'','newProductCategoryName'=>'','newProductName'=>'',
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'workflowSelectorVersion'=>0,'workflowPhaseId'=>null,'mentionUsers'=>collect(),
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
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments',
    'priority'=>'Medium','productionUrgencies'=>collect(),'shipmentUrgencies'=>collect(),'productionUrgencyIds'=>[],'shipmentUrgencyIds'=>[],'isRepeatedOrder'=>false,'repeatedOrderNumber'=>'',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'productCategories'=>collect(),'productSearchResults'=>collect(),'selectedProductDetails'=>collect(),'activeProductCount'=>0,'productResultTotal'=>0,
    'canUseOrderProductSelector'=>false,'canCreateCatalogProduct'=>false,'canViewProductCategories'=>false,'canCreateProductCategory'=>false,'duplicateProduct'=>null,'newProductCategoryMatches'=>collect(),'newProductSimilarCategories'=>collect(),
    'newProductSimilarProducts'=>collect(),'newProductSelectedCategory'=>null,'newProductHasExactCategory'=>false,'newProductImagePreview'=>null,
    'createProductSearch'=>'','createProductCategoryFilter'=>'','createProductShowAllResults'=>false,'showCreateOrderProductModal'=>false,
    'newProductCode'=>'','newProductCategoryId'=>null,'newProductCategorySearch'=>'','newProductCategoryName'=>'','newProductName'=>'',
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'workflowSelectorVersion'=>0,'workflowPhaseId'=>null,'mentionUsers'=>collect(),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $selectedClient = $clients->firstWhere('id', (int)$clientId);
    $selectedWorkflow = $workflows->firstWhere('id', (int)$workflowId);
    $selectedOwnerOption = collect($ownerFilterOptions)->first(fn($item) => (int)($item['id'] ?? 0) === (int)($ownerId ?? 0));
    $allowedPhases = $selectedWorkflow?->phases?->where('is_active', true)->where('allow_job_start', true) ?? collect();
    $taskCount = $selectedWorkflow?->phases?->sum(fn($phase) => $phase->taskPack?->templates?->count() ?? 0) ?? 0;
    $totalUnits = collect($jobItems)->sum(fn($item)=>(int)($item['quantity'] ?? 0));
    $createReady = $catalogReady && $assignmentReady && $workflowReady && $canUseOrderProductSelector;
?>
<div <?php echo e($attributes->class('ft-create-job-page')); ?>>
    <div class="ft-create-shell">
        <div class="ft-create-breadcrumb">Orders / Create order</div>
        <div class="ft-create-title"><h1>Create new order</h1><p>Set the order scope, products, ownership and workflow.</p></div>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>1</span><h2>Order basics</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field"><b>Order code</b><div class="ft-locked-input">Generated automatically <span>♙</span></div></label>
                <div class="ft-create-field">
                    <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-create-remote-select','label' => 'Client *','property' => 'clientId','type' => 'clients','context' => 'create-job','action' => 'setCreateSelector','value' => $clientId,'placeholder' => 'Select client','selectedLabel' => $selectedClient?->name,'initialOptions' => $clientFilterOptions,'clearable' => false,'wire:key' => 'create-client-selector-'.e($clientId ?: 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select','label' => 'Client *','property' => 'clientId','type' => 'clients','context' => 'create-job','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'placeholder' => 'Select client','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedClient?->name),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'create-client-selector-'.e($clientId ?: 'none').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <label class="ft-create-field"><b>Client contact</b><input value="<?php echo e($selectedClient?->contact_name ?? 'No contact recorded'); ?>" readonly></label>
                <label class="ft-create-field"><b>Reference number</b><input wire:model="referenceNumber" placeholder="e.g. REF-00028 or customer PO number"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['referenceNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-create-field ft-repeat-order-option">
                    <b>Repeated order</b>
                    <label class="ft-repeat-order-check">
                        <input type="checkbox" wire:model.live="isRepeatedOrder">
                        <span>Is this a repeated order?</span>
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['isRepeatedOrder'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isRepeatedOrder): ?>
                    <label class="ft-create-field" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'repeated-order-number-field'; ?>wire:key="repeated-order-number-field">
                        <b>Previous reference number *</b>
                        <input wire:model="repeatedOrderNumber" placeholder="Enter the previous order reference number">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['repeatedOrderNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <label class="ft-create-field"><b>Order title *</b><input wire:model="jobTitle" placeholder="e.g. Conference merchandise order"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-create-field ft-mention-host"><b>Request description</b><textarea class="ft-mention-input" data-rich-text wire:model="description" rows="4" autocomplete="off" data-mention-users="<?php echo e($mentionUsers->toJson()); ?>" placeholder="Type @ to mention a user. Add specifications, target price or customization requirements..."></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            </div>
        </section>

        <?php echo $__env->make('components.jobs.create-products', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignmentReady): ?>
        <section class="ft-create-section" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-assignment-ready'; ?>wire:key="create-assignment-ready">
            <div class="ft-create-section-title"><span>3</span><h2>Schedule & owner</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.deliveryDate?.showPicker?.(); $refs.deliveryDate?.focus(); }"><b>Customer required delivery date</b><input x-ref="deliveryDate" type="date" wire:model="deliveryDate"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['deliveryDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.estimatedDeliveryDate?.showPicker?.(); $refs.estimatedDeliveryDate?.focus(); }"><b>Estimated Delivery date</b><input x-ref="estimatedDeliveryDate" type="date" wire:model="estimatedDeliveryDate"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['estimatedDeliveryDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <div class="ft-create-urgency-grid">
                    <div class="ft-create-field ft-create-urgency-field">
                        <b>Select order production urgency</b>
                        <div class="ft-create-urgency-control" role="group" aria-label="Select order production urgency">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productionUrgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urgency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <label class="ft-create-urgency-check" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'production-urgency-'.e($urgency->id).''; ?>wire:key="production-urgency-<?php echo e($urgency->id); ?>">
                                    <input type="checkbox" value="<?php echo e($urgency->id); ?>" wire:model="productionUrgencyIds">
                                    <span><?php echo e($urgency->name); ?></span>
                                </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <small>No active Production Urgency options in Master Data.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productionUrgencyIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productionUrgencyIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-create-field ft-create-urgency-field">
                        <b>Select order shipment urgency</b>
                        <div class="ft-create-urgency-control" role="group" aria-label="Select order shipment urgency">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $shipmentUrgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urgency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <label class="ft-create-urgency-check" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-urgency-'.e($urgency->id).''; ?>wire:key="shipment-urgency-<?php echo e($urgency->id); ?>">
                                    <input type="checkbox" value="<?php echo e($urgency->id); ?>" wire:model="shipmentUrgencyIds">
                                    <span><?php echo e($urgency->name); ?></span>
                                </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <small>No active Shipment Urgency options in Master Data.</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentUrgencyIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentUrgencyIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="ft-create-field">
                    <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-create-remote-select','label' => 'Order owner *','property' => 'ownerId','type' => 'users','context' => 'create-job','action' => 'setCreateSelector','value' => $ownerId,'placeholder' => 'Select owner','selectedLabel' => $selectedOwnerOption['label'] ?? null,'initialOptions' => $ownerFilterOptions,'clearable' => false,'wire:key' => 'create-owner-selector']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select','label' => 'Order owner *','property' => 'ownerId','type' => 'users','context' => 'create-job','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerId),'placeholder' => 'Select owner','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOwnerOption['label'] ?? null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'create-owner-selector']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
                    <small>Accountable for overall delivery.</small>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ownerId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal732a8e3f5371418be0dfaaa000db0561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal732a8e3f5371418be0dfaaa000db0561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create-section-placeholder','data' => ['number' => '3','title' => 'Schedule & owner','section' => 'assignment','rows' => 5]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create-section-placeholder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '3','title' => 'Schedule & owner','section' => 'assignment','rows' => 5]); ?>
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

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowReady): ?>
            <?php if (isset($component)) { $__componentOriginaldc75731e81ba1cac015b7a03337954d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc75731e81ba1cac015b7a03337954d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.create-workflow-picker','data' => ['class' => 'ft-create-section','step' => '4','title' => 'What happens next','workflowOptions' => $workflowFilterOptions,'selectedWorkflowId' => $workflowId,'selectedWorkflowName' => $selectedWorkflow?->name ?? 'Select workflow','phaseCount' => $selectedWorkflow?->phases?->where('is_active', true)->count() ?? 0,'taskCount' => $taskCount,'selectionProperty' => 'workflowId','optionFallback' => 'Order workflow','footnote' => 'Tasks are created when you select Create order. Workflow and starting phase are fixed after creation.','previewAllowed' => auth()->user()->canAccess('workflow.view'),'errorField' => 'workflowId','startPhases' => $allowedPhases,'startPhaseId' => $workflowPhaseId,'startPhaseProperty' => 'workflowPhaseId','startPhaseErrorField' => 'workflowPhaseId','wire:key' => 'create-order-workflow-picker-'.e($clientId ?: 'none').'-'.e($workflowSelectorVersion).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.create-workflow-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-section','step' => '4','title' => 'What happens next','workflow-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowFilterOptions),'selected-workflow-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowId),'selected-workflow-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedWorkflow?->name ?? 'Select workflow'),'phase-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedWorkflow?->phases?->where('is_active', true)->count() ?? 0),'task-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskCount),'selection-property' => 'workflowId','option-fallback' => 'Order workflow','footnote' => 'Tasks are created when you select Create order. Workflow and starting phase are fixed after creation.','preview-allowed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->canAccess('workflow.view')),'error-field' => 'workflowId','start-phases' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allowedPhases),'start-phase-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowPhaseId),'start-phase-property' => 'workflowPhaseId','start-phase-error-field' => 'workflowPhaseId','wire:key' => 'create-order-workflow-picker-'.e($clientId ?: 'none').'-'.e($workflowSelectorVersion).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc75731e81ba1cac015b7a03337954d0)): ?>
<?php $attributes = $__attributesOriginaldc75731e81ba1cac015b7a03337954d0; ?>
<?php unset($__attributesOriginaldc75731e81ba1cac015b7a03337954d0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc75731e81ba1cac015b7a03337954d0)): ?>
<?php $component = $__componentOriginaldc75731e81ba1cac015b7a03337954d0; ?>
<?php unset($__componentOriginaldc75731e81ba1cac015b7a03337954d0); ?>
<?php endif; ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal732a8e3f5371418be0dfaaa000db0561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal732a8e3f5371418be0dfaaa000db0561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create-section-placeholder','data' => ['number' => '4','title' => 'What happens next','section' => 'workflow','rows' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create-section-placeholder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '4','title' => 'What happens next','section' => 'workflow','rows' => 2]); ?>
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

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>5</span><h2>Attachments</h2></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','create')): ?>
                <div class="ft-create-upload-wrap">
                <div class="ft-create-upload ft-livewire-upload-zone" data-file-dropzone>
                    <span class="ft-create-paperclip">⌕</span>
                    <div><b>Drop files here or <label for="job-create-files">browse</label></b><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','view')): ?><a href="<?php echo e(route('documents.index')); ?>" wire:navigate>Open Documents</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <input id="job-create-files" type="file" wire:model="jobAttachments" multiple hidden accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                </div>
                </div>
            <?php else: ?>
                <div class="ft-create-note">Your role does not allow document uploads during Order creation.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($jobAttachments)): ?><div class="ft-create-upload-list"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($file->getClientOriginalName()); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobAttachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <div class="ft-create-actions">
            <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
            <button type="button" class="ft-create-draft" wire:click="saveDraft" <?php if(!$createReady): echo 'disabled'; endif; ?>>Save draft</button>
            <button type="button" class="ft-create-primary" wire:click="createJob" <?php if(!$createReady): echo 'disabled'; endif; ?>>Create order</button>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createLoading'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create.blade.php ENDPATH**/ ?>