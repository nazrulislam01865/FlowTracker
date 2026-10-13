<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'index',
    'shipment' => [],
    'shipmentCount' => 1,
    'mode' => 'multiple_shipments',
    'countries' => collect(),
    'statesByCountry' => collect(),
    'phoneCodes' => collect(),
    'referenceNumber' => '',
    'hasSavedAddresses' => false,
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
    'index',
    'shipment' => [],
    'shipmentCount' => 1,
    'mode' => 'multiple_shipments',
    'countries' => collect(),
    'statesByCountry' => collect(),
    'phoneCodes' => collect(),
    'referenceNumber' => '',
    'hasSavedAddresses' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $shipmentNumber = (int) $index + 1;
    $sameAddressLocked = $mode === \App\Services\OrderShipmentService::MODE_SAME_ADDRESS && (int) $index > 0;

    $sharedContact = collect([
        trim((string) ($shipment['contact_name'] ?? '')),
        trim((string) ($shipment['phone_country_code'] ?? '')).' '.trim((string) ($shipment['phone'] ?? '')),
    ])->map(fn ($value) => trim((string) $value))->filter()->implode(' · ');
    $sharedAddress = collect([
        trim((string) ($shipment['address'] ?? '')),
        trim((string) ($shipment['postal_code'] ?? '')),
    ])->filter()->implode(' · ');
?>

<article
    class="ft-create-shipment-card <?php echo e($sameAddressLocked ? 'is-shared-address' : ''); ?>"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-shipment-row-'.e($index).''; ?>wire:key="create-shipment-row-<?php echo e($index); ?>"
    data-ft-ui-component="create-order-shipment-card"
>
    <header class="ft-create-shipment-card-head">
        <div class="ft-create-shipment-card-title">
            <div>
                <strong>Shipment <?php echo e($shipmentNumber); ?></strong>
                <small><?php echo e($sameAddressLocked ? 'Uses Shipment 1 delivery details' : 'Enter delivery details'); ?></small>
            </div>
        </div>

        <div class="ft-create-shipment-card-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $index === 0): ?>
                <span class="ft-create-shipment-primary-badge">Primary</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button
                type="button"
                class="ft-create-shipment-remove <?php echo e((int) $index === 0 ? 'is-disabled' : ''); ?>"
                <?php if((int) $index > 0): ?>
                    wire:click="removeCreateShipment(<?php echo e($index); ?>)"
                    wire:loading.attr="disabled"
                    wire:target="removeCreateShipment"
                <?php else: ?>
                    disabled
                <?php endif; ?>
                aria-label="<?php echo e((int) $index === 0 ? 'Primary shipment cannot be removed' : 'Remove shipment '.$shipmentNumber); ?>"
                title="<?php echo e((int) $index === 0 ? 'Primary shipment cannot be removed' : 'Remove shipment'); ?>"
            >
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M4.5 6h11M8 3.5h4M6.5 6l.6 10h5.8l.6-10M8.5 8.5v5M11.5 8.5v5"/>
                </svg>
            </button>
        </div>
    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sameAddressLocked): ?>
        <div class="ft-create-shipment-shared-address" aria-label="Shipment <?php echo e($shipmentNumber); ?> uses the same delivery address as Shipment 1">
            <span class="ft-create-shipment-shared-icon" aria-hidden="true">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4.5 8.5 10 4l5.5 4.5V16H4.5V8.5Z"/><path d="M7.5 16v-4h5v4"/></svg>
            </span>
            <span class="ft-create-shipment-shared-copy">
                <strong>Same delivery address as Shipment 1</strong>
                <span><?php echo e($sharedContact !== '' ? $sharedContact : 'Contact details come from Shipment 1.'); ?></span>
                <span><?php echo e($sharedAddress !== '' ? $sharedAddress : 'Complete Shipment 1 delivery address above.'); ?></span>
            </span>
        </div>
    <?php else: ?>
        <div class="ft-create-shipment-body">
            <div class="ft-create-shipment-primary-grid">
                <label class="ft-create-field ft-create-shipment-field">
                    <b>Contact person <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                    <input
                        type="text"
                        wire:model.blur="createShipments.<?php echo e($index); ?>.contact_name"
                        maxlength="255"
                        autocomplete="name"
                        placeholder="e.g. John Smith"
                    >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.contact_name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <div class="ft-create-field ft-create-shipment-field">
                    <b>Phone <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                    <div class="ft-create-shipment-phone-row">
                        <div class="ft-create-shipment-phone-control ft-create-shipment-phone-code">
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-create-shipment-phone-code-select','label' => 'Phone country code for shipment '.e($shipmentNumber).'','property' => 'createShipments.'.e($index).'.phone_country_code','value' => $shipment['phone_country_code'] ?? '','options' => $phoneCodes,'placeholder' => '+Code','selectedLabel' => ($shipment['phone_country_code'] ?? '') ?: null,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'searchPlaceholder' => 'Search code or country…','wire:key' => 'create-shipment-phone-code-'.e($index).'-'.e($shipment['phone_country_code'] ?? 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-shipment-phone-code-select','label' => 'Phone country code for shipment '.e($shipmentNumber).'','property' => 'createShipments.'.e($index).'.phone_country_code','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment['phone_country_code'] ?? ''),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phoneCodes),'placeholder' => '+Code','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($shipment['phone_country_code'] ?? '') ?: null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'search-placeholder' => 'Search code or country…','wire:key' => 'create-shipment-phone-code-'.e($index).'-'.e($shipment['phone_country_code'] ?? 'none').'']); ?>
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
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.phone_country_code"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ft-create-shipment-phone-control ft-create-shipment-phone-number">
                            <input
                                type="text"
                                wire:model.blur="createShipments.<?php echo e($index); ?>.phone"
                                maxlength="60"
                                inputmode="tel"
                                autocomplete="tel"
                                placeholder="e.g. 555-123-4567"
                                aria-label="Phone for shipment <?php echo e($shipmentNumber); ?>"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.phone"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ft-create-shipment-address-block">
                <div class="ft-create-field ft-create-shipment-field ft-create-shipment-address-field">
                    <b>Shipping address <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                    <button type="button" class="ft-create-shipment-saved-address" wire:click="openSavedShippingAddressPickerForShipment(<?php echo e($index); ?>)">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5.5 3.5h9v13l-4.5-2.6-4.5 2.6v-13Z"/></svg>
                        Use saved address
                    </button>
                    <input
                        class="ft-create-shipment-address-input"
                        type="text"
                        wire:model.blur="createShipments.<?php echo e($index); ?>.address"
                        maxlength="2000"
                        autocomplete="street-address"
                        placeholder="Street address, suite, building, etc."
                        aria-label="Shipping address for shipment <?php echo e($shipmentNumber); ?>"
                    >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.address"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-create-shipment-address-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <label class="ft-create-field ft-create-shipment-field ft-create-shipment-postal-field">
                    <b>Postal code <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                    <input
                        type="text"
                        wire:model.blur="createShipments.<?php echo e($index); ?>.postal_code"
                        maxlength="30"
                        autocomplete="postal-code"
                        placeholder="e.g. 27510-2461"
                    >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.postal_code"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/create/shipping-row.blade.php ENDPATH**/ ?>