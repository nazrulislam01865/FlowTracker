<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments','purchaseOrderUpload'=>null,
    'priority'=>'Medium','productionUrgencies'=>collect(),'shipmentMethods'=>collect(),'shipmentUrgencies'=>collect(),'productionUrgencyIds'=>[],'shipmentMethodIds'=>[],'shipmentUrgencyIds'=>[],'isRepeatedOrder'=>false,'repeatedOrderNumber'=>'',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'productCategories'=>collect(),'productSearchResults'=>collect(),'productSearchSuppliers'=>collect(),'selectedProductDetails'=>collect(),'selectedProductSuppliers'=>collect(),'createOrderSupplierSkipProductIds'=>[],'activeProductCount'=>0,'productResultTotal'=>0,
    'canUseOrderProductSelector'=>false,'canCreateCatalogProduct'=>false,'canViewProductCategories'=>false,'canCreateProductCategory'=>false,'duplicateProduct'=>null,'newProductCategoryMatches'=>collect(),'newProductSimilarCategories'=>collect(),
    'newProductSimilarProducts'=>collect(),'newProductSelectedCategory'=>null,'newProductHasExactCategory'=>false,'newProductImagePreview'=>null,'newProductSupplierOptions'=>collect(),
    'createProductSearch'=>'','createProductCategoryFilter'=>'','createProductShowAllResults'=>false,'showCreateOrderProductModal'=>false,
    'newProductCode'=>'','newProductCategoryId'=>null,'newProductCategorySearch'=>'','newProductCategoryName'=>'','newProductName'=>'','newProductSupplierId'=>null,
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'workflowSelectorVersion'=>0,'workflowPhaseId'=>null,'mentionUsers'=>collect(),
    'createInquiryFilterOptions'=>collect(),'selectedCreateInquiry'=>null,'selectedCreateInquiries'=>collect(),'createInquirySelectorVersion'=>0,'canLinkInquiryOnCreate'=>false,
    'referenceNumber'=>'',
    'savedShippingAddresses'=>collect(),'savedDeliveryContacts'=>collect(),'showSavedShippingAddressPicker'=>false,'shippingSourceAddressId'=>null,'savedShippingAddressShipmentIndex'=>null,
    'phoneCountryCodeOptions'=>collect(),'shippingPhoneCountryCode'=>'+1','shippingPhone'=>'',
    'createShipmentCountries'=>collect(),'createShipmentStatesByCountry'=>collect(),'createShipmentPhoneCodes'=>collect(),'createShipmentMode'=>'multiple_shipments','createShipments'=>[],
    'shippingContactType'=>'end_customer','shippingContactId'=>null,'shippingContactSelection'=>'','shippingContactName'=>'','shippingSaveContact'=>true,
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
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments','purchaseOrderUpload'=>null,
    'priority'=>'Medium','productionUrgencies'=>collect(),'shipmentMethods'=>collect(),'shipmentUrgencies'=>collect(),'productionUrgencyIds'=>[],'shipmentMethodIds'=>[],'shipmentUrgencyIds'=>[],'isRepeatedOrder'=>false,'repeatedOrderNumber'=>'',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'productCategories'=>collect(),'productSearchResults'=>collect(),'productSearchSuppliers'=>collect(),'selectedProductDetails'=>collect(),'selectedProductSuppliers'=>collect(),'createOrderSupplierSkipProductIds'=>[],'activeProductCount'=>0,'productResultTotal'=>0,
    'canUseOrderProductSelector'=>false,'canCreateCatalogProduct'=>false,'canViewProductCategories'=>false,'canCreateProductCategory'=>false,'duplicateProduct'=>null,'newProductCategoryMatches'=>collect(),'newProductSimilarCategories'=>collect(),
    'newProductSimilarProducts'=>collect(),'newProductSelectedCategory'=>null,'newProductHasExactCategory'=>false,'newProductImagePreview'=>null,'newProductSupplierOptions'=>collect(),
    'createProductSearch'=>'','createProductCategoryFilter'=>'','createProductShowAllResults'=>false,'showCreateOrderProductModal'=>false,
    'newProductCode'=>'','newProductCategoryId'=>null,'newProductCategorySearch'=>'','newProductCategoryName'=>'','newProductName'=>'','newProductSupplierId'=>null,
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'workflowSelectorVersion'=>0,'workflowPhaseId'=>null,'mentionUsers'=>collect(),
    'createInquiryFilterOptions'=>collect(),'selectedCreateInquiry'=>null,'selectedCreateInquiries'=>collect(),'createInquirySelectorVersion'=>0,'canLinkInquiryOnCreate'=>false,
    'referenceNumber'=>'',
    'savedShippingAddresses'=>collect(),'savedDeliveryContacts'=>collect(),'showSavedShippingAddressPicker'=>false,'shippingSourceAddressId'=>null,'savedShippingAddressShipmentIndex'=>null,
    'phoneCountryCodeOptions'=>collect(),'shippingPhoneCountryCode'=>'+1','shippingPhone'=>'',
    'createShipmentCountries'=>collect(),'createShipmentStatesByCountry'=>collect(),'createShipmentPhoneCodes'=>collect(),'createShipmentMode'=>'multiple_shipments','createShipments'=>[],
    'shippingContactType'=>'end_customer','shippingContactId'=>null,'shippingContactSelection'=>'','shippingContactName'=>'','shippingSaveContact'=>true,
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
    $activeWorkflowPhases = $selectedWorkflow?->phases?->where('is_active', true)->sortBy('sequence')->values() ?? collect();
    // Order workflows always start new Orders at the first fixed stage. Keep the
    // stage data dynamic for display, but do not expose a generic start-phase
    // selector because the Order runtime sequence remains fixed.
    $allowedPhases = $activeWorkflowPhases->take(1);
    $taskCount = $activeWorkflowPhases->sum(fn($phase) => $phase->taskPack?->items?->count() ?? 0);
    $workflowStagePreview = $activeWorkflowPhases->map(fn($phase) => [
        'sequence' => (int) $phase->sequence,
        'name' => (string) $phase->name,
        'color' => (string) ($phase->color ?: '#087f73'),
        'task_count' => (int) ($phase->taskPack?->items?->count() ?? 0),
    ])->values();
    $totalUnits = collect($jobItems)->sum(fn($item)=>(int)($item['quantity'] ?? 0));
    $createReady = $catalogReady && $assignmentReady && $workflowReady && $canUseOrderProductSelector && filled($workflowId) && filled($workflowPhaseId);
?>
<div <?php echo e($attributes->class(['ft-create-job-page', 'ft-form-standard', 'ft-form-standard--order'])); ?> data-ft-feedback-scope="form">
    <div class="ft-create-shell">
        <div class="ft-create-breadcrumb">Orders / Create order</div>
        <div class="ft-create-title"><h1>Create new order</h1><p>Set the order scope, products, shipping, ownership and workflow.</p></div>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>1</span><h2>Order basics</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field"><b>Order code</b><div class="ft-locked-input">Generated automatically <span>♙</span></div></label>
                <div class="ft-create-field">
                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-create-remote-select','label' => 'Client *','property' => 'clientId','type' => 'clients','context' => 'create-job','action' => 'setCreateSelector','value' => $clientId,'placeholder' => 'Select client','selectedLabel' => $selectedClient?->name,'initialOptions' => $clientFilterOptions,'clearable' => false,'wire:key' => 'create-client-selector-'.e($clientId ?: 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select','label' => 'Client *','property' => 'clientId','type' => 'clients','context' => 'create-job','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'placeholder' => 'Select client','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedClient?->name),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'create-client-selector-'.e($clientId ?: 'none').'']); ?>
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
                <label class="ft-create-field"><b>Client Reference Number *</b><input wire:model.live.debounce.300ms="referenceNumber" required aria-required="true" placeholder="e.g. FO-333119 or customer PO number"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['referenceNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canLinkInquiryOnCreate): ?>
                    <?php if (isset($component)) { $__componentOriginal533b91716cd67d597c575112997a2909 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal533b91716cd67d597c575112997a2909 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.inquiry-link','data' => ['selectedInquiries' => $selectedCreateInquiries,'initialOptions' => $createInquiryFilterOptions,'clientId' => $clientId,'selectorVersion' => $createInquirySelectorVersion]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.inquiry-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selected-inquiries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedCreateInquiries),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createInquiryFilterOptions),'client-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'selector-version' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createInquirySelectorVersion)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal533b91716cd67d597c575112997a2909)): ?>
<?php $attributes = $__attributesOriginal533b91716cd67d597c575112997a2909; ?>
<?php unset($__attributesOriginal533b91716cd67d597c575112997a2909); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal533b91716cd67d597c575112997a2909)): ?>
<?php $component = $__componentOriginal533b91716cd67d597c575112997a2909; ?>
<?php unset($__componentOriginal533b91716cd67d597c575112997a2909); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        <?php if (isset($component)) { $__componentOriginalfad564098d922e377b755e81752b4e23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfad564098d922e377b755e81752b4e23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-setup','data' => ['shipments' => $createShipments,'mode' => $createShipmentMode,'shipmentMethods' => $shipmentMethods,'shipmentUrgencies' => $shipmentUrgencies,'countries' => $createShipmentCountries,'statesByCountry' => $createShipmentStatesByCountry,'phoneCodes' => $createShipmentPhoneCodes,'savedShippingAddresses' => $savedShippingAddresses,'showSavedShippingAddressPicker' => $showSavedShippingAddressPicker,'savedShippingAddressShipmentIndex' => $savedShippingAddressShipmentIndex,'referenceNumber' => $referenceNumber]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-setup'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shipments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createShipments),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createShipmentMode),'shipment-methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentMethods),'shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencies),'countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createShipmentCountries),'states-by-country' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createShipmentStatesByCountry),'phone-codes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createShipmentPhoneCodes),'saved-shipping-addresses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($savedShippingAddresses),'show-saved-shipping-address-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showSavedShippingAddressPicker),'saved-shipping-address-shipment-index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($savedShippingAddressShipmentIndex),'reference-number' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($referenceNumber)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfad564098d922e377b755e81752b4e23)): ?>
<?php $attributes = $__attributesOriginalfad564098d922e377b755e81752b4e23; ?>
<?php unset($__attributesOriginalfad564098d922e377b755e81752b4e23); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfad564098d922e377b755e81752b4e23)): ?>
<?php $component = $__componentOriginalfad564098d922e377b755e81752b4e23; ?>
<?php unset($__componentOriginalfad564098d922e377b755e81752b4e23); ?>
<?php endif; ?>

        

        <?php echo $__env->make('components.jobs.create-products', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignmentReady): ?>
        <section class="ft-create-section" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-assignment-ready'; ?>wire:key="create-assignment-ready">
            <div class="ft-create-section-title"><span>4</span><h2>Schedule & owner</h2></div>
            <div class="ft-create-fields">
                    
                <label
                    class="ft-create-field ft-clickable-date-field"
                    x-data
                    x-on:click="if (!$event.target.closest('.validation-error')) { $refs.deliveryDate?.showPicker?.(); $refs.deliveryDate?.focus(); }"
                >
                    <b>Order hand date</b>

                    <input
                        x-ref="deliveryDate"
                        type="date"
                        wire:model="deliveryDate"
                    >

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['deliveryDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="validation-error">
                            <?php echo e($message); ?>

                        </small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="ft-create-field">
                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-create-remote-select','label' => 'Order owner *','property' => 'ownerId','type' => 'users','context' => 'create-job','action' => 'setCreateSelector','value' => $ownerId,'placeholder' => 'Select owner','selectedLabel' => $selectedOwnerOption['label'] ?? null,'initialOptions' => $ownerFilterOptions,'clearable' => false,'wire:key' => 'create-owner-selector']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select','label' => 'Order owner *','property' => 'ownerId','type' => 'users','context' => 'create-job','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerId),'placeholder' => 'Select owner','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOwnerOption['label'] ?? null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'create-owner-selector']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create-section-placeholder','data' => ['number' => '4','title' => 'Schedule & owner','section' => 'assignment','rows' => 5]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create-section-placeholder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '4','title' => 'Schedule & owner','section' => 'assignment','rows' => 5]); ?>
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

        <?php if (isset($component)) { $__componentOriginal6f5730ddd9f52a792fb155d096ba09d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f5730ddd9f52a792fb155d096ba09d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.documents','data' => ['purchaseOrderUpload' => $purchaseOrderUpload,'jobAttachments' => $jobAttachments,'canUpload' => auth()->user()->canModule('documents','create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.documents'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['purchase-order-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($purchaseOrderUpload),'job-attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobAttachments),'can-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->canModule('documents','create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f5730ddd9f52a792fb155d096ba09d8)): ?>
<?php $attributes = $__attributesOriginal6f5730ddd9f52a792fb155d096ba09d8; ?>
<?php unset($__attributesOriginal6f5730ddd9f52a792fb155d096ba09d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f5730ddd9f52a792fb155d096ba09d8)): ?>
<?php $component = $__componentOriginal6f5730ddd9f52a792fb155d096ba09d8; ?>
<?php unset($__componentOriginal6f5730ddd9f52a792fb155d096ba09d8); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowReady): ?>
            <?php if (isset($component)) { $__componentOriginaldc75731e81ba1cac015b7a03337954d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc75731e81ba1cac015b7a03337954d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.create-workflow-picker','data' => ['class' => 'ft-create-section ft-order-workflow-create-section','step' => '6','title' => 'What happens next','workflowOptions' => $workflowFilterOptions,'selectedWorkflowId' => $workflowId,'selectedWorkflowName' => $selectedWorkflow?->name ?? 'Select workflow','phaseCount' => $selectedWorkflow?->phases?->where('is_active', true)->count() ?? 0,'taskCount' => $taskCount,'selectionProperty' => 'workflowId','optionFallback' => 'Order workflow','footnote' => 'Tasks are created when you select Create order. Workflow and starting phase are fixed after creation.','previewAllowed' => true,'previewDefaultOpen' => true,'availabilityLabel' => 'workflow available','icon' => 'workflow','stagePreview' => $workflowStagePreview,'kindLabel' => 'Order workflow','sourceLabel' => 'Workflow Setup','stageNoun' => 'stage','optionEmptyMessage' => 'No active, complete Order workflow is available from Workflow Setup. Configure the Order workflow and its Task Packs first, then select it here.','setupUrl' => auth()->user()->canAccess('workflow.view') ? route('workflow.setup', $workflowId ? ['workflow' => $workflowId] : []) : null,'setupLabel' => 'Open Workflow Setup','errorField' => 'workflowId','startPhases' => $allowedPhases,'startPhaseId' => $workflowPhaseId,'startPhaseProperty' => 'workflowPhaseId','startPhaseErrorField' => 'workflowPhaseId','selectable' => true,'wire:key' => 'create-order-workflow-picker-'.e($clientId ?: 'none').'-'.e($workflowSelectorVersion).'-'.e($workflowId ?: 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.create-workflow-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-section ft-order-workflow-create-section','step' => '6','title' => 'What happens next','workflow-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowFilterOptions),'selected-workflow-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowId),'selected-workflow-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedWorkflow?->name ?? 'Select workflow'),'phase-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedWorkflow?->phases?->where('is_active', true)->count() ?? 0),'task-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskCount),'selection-property' => 'workflowId','option-fallback' => 'Order workflow','footnote' => 'Tasks are created when you select Create order. Workflow and starting phase are fixed after creation.','preview-allowed' => true,'preview-default-open' => true,'availability-label' => 'workflow available','icon' => 'workflow','stage-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowStagePreview),'kind-label' => 'Order workflow','source-label' => 'Workflow Setup','stage-noun' => 'stage','option-empty-message' => 'No active, complete Order workflow is available from Workflow Setup. Configure the Order workflow and its Task Packs first, then select it here.','setup-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->canAccess('workflow.view') ? route('workflow.setup', $workflowId ? ['workflow' => $workflowId] : []) : null),'setup-label' => 'Open Workflow Setup','error-field' => 'workflowId','start-phases' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allowedPhases),'start-phase-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowPhaseId),'start-phase-property' => 'workflowPhaseId','start-phase-error-field' => 'workflowPhaseId','selectable' => true,'wire:key' => 'create-order-workflow-picker-'.e($clientId ?: 'none').'-'.e($workflowSelectorVersion).'-'.e($workflowId ?: 'none').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create-section-placeholder','data' => ['number' => '6','title' => 'What happens next','section' => 'workflow','rows' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create-section-placeholder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => '6','title' => 'What happens next','section' => 'workflow','rows' => 2]); ?>
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

        <div class="ft-create-actions">
            <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
            <button type="button" class="ft-create-draft" wire:click="saveDraft" wire:loading.attr="disabled" wire:target="purchaseOrderUpload,jobAttachments,saveDraft" <?php if(!$createReady): echo 'disabled'; endif; ?>>Save draft</button>
            <button type="button" class="ft-create-primary" wire:click="createJob" wire:loading.attr="disabled" wire:target="purchaseOrderUpload,jobAttachments,createJob" <?php if(!$createReady): echo 'disabled'; endif; ?>>Create order</button>
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