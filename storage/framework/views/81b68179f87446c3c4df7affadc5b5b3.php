<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'index',
    'shipment' => [],
    'shipmentCount' => 1,
    'mode' => 'multiple_shipments',
    'shipmentMethods' => collect(),
    'shipmentUrgencies' => collect(),
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
    'shipmentMethods' => collect(),
    'shipmentUrgencies' => collect(),
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
    $country = trim((string) ($shipment['country'] ?? ''));
    $states = collect($statesByCountry)->get($country, []);
    $states = collect($states)->values();
    $sameAddressLocked = $mode === \App\Services\OrderShipmentService::MODE_SAME_ADDRESS && (int) $index > 0;
    $packagePlaceholder = trim((string) $referenceNumber) !== ''
        ? trim((string) $referenceNumber).' | Box '.$shipmentNumber.' of '.$shipmentCount
        : 'e.g. Box '.$shipmentNumber;

    $sharedContact = collect([
        trim((string) ($shipment['contact_name'] ?? '')),
        trim((string) ($shipment['phone_country_code'] ?? '')).' '.trim((string) ($shipment['phone'] ?? '')),
    ])->map(fn ($value) => trim((string) $value))->filter()->implode(' · ');
    $sharedLocality = collect([
        trim((string) ($shipment['city'] ?? '')),
        trim((string) ($shipment['state'] ?? '')),
        trim((string) ($shipment['postal_code'] ?? '')),
    ])->filter()->implode(', ');
    $sharedAddress = collect([
        trim((string) ($shipment['address'] ?? '')),
        $sharedLocality,
        $country,
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
                <small><?php echo e($sameAddressLocked ? 'Uses Shipment 1 delivery details' : 'Enter delivery and shipping details'); ?></small>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $index > 0): ?>
            <button
                type="button"
                class="ft-create-shipment-remove"
                wire:click="removeCreateShipment(<?php echo e($index); ?>)"
                wire:loading.attr="disabled"
                wire:target="removeCreateShipment"
                aria-label="Remove shipment <?php echo e($shipmentNumber); ?>"
            >
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M4.5 6h11M8 3.5h4M6.5 6l.6 10h5.8l.6-10M8.5 8.5v5M11.5 8.5v5"/>
                </svg>
                Remove shipment
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
        <div class="ft-create-shipment-primary-grid">
            <label class="ft-create-shipment-field">
                <span>Contact person</span>
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

            <div class="ft-create-shipment-field">
                <span>Phone</span>
                <div class="ft-create-shipment-phone-row">
                    <div class="ft-create-shipment-phone-control ft-create-shipment-phone-code">
                        <select
                            wire:model.live="createShipments.<?php echo e($index); ?>.phone_country_code"
                            aria-label="Phone country code for shipment <?php echo e($shipmentNumber); ?>"
                        >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phoneCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phoneCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($phoneCode); ?>"><?php echo e($phoneCode); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
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
            <div class="ft-create-shipment-address-toolbar">
                <span>Shipping address</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSavedAddresses): ?>
                    <button type="button" wire:click="openSavedShippingAddressPickerForShipment(<?php echo e($index); ?>)">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5.5 3.5h9v13l-4.5-2.6-4.5 2.6v-13Z"/></svg>
                        Use saved address
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <label class="ft-create-shipment-field ft-create-shipment-address-line">
                <span class="sr-only">Shipping address</span>
                <input
                    type="text"
                    wire:model.blur="createShipments.<?php echo e($index); ?>.address"
                    maxlength="2000"
                    autocomplete="street-address"
                    placeholder="Street address, suite, building, etc."
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.address"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <div class="ft-create-shipment-location-grid">
                <div class="ft-create-shipment-field">
                    <span>Country</span>
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Country','property' => 'createShipments.'.$index.'.country','value' => $country,'placeholder' => 'Select country','options' => $countries,'disabled' => collect($countries)->isEmpty(),'searchPlaceholder' => 'Search country…','menuWidth' => 320,'fixedMenu' => true,'hideLabel' => true,'class' => 'ft-create-shipment-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Country','property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('createShipments.'.$index.'.country'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($country),'placeholder' => 'Select country','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($countries),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($countries)->isEmpty()),'search-placeholder' => 'Search country…','menu-width' => 320,'fixed-menu' => true,'hide-label' => true,'class' => 'ft-create-shipment-select']); ?>
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
                    <small class="validation-error ft-create-shipment-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.country"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </div>

                <div class="ft-create-shipment-field">
                    <span>State</span>
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'State','property' => 'createShipments.'.$index.'.state','value' => $shipment['state'] ?? '','placeholder' => $country === '' ? 'Select country first' : ($states->isEmpty() ? 'No states configured' : 'Select state'),'options' => $states,'disabled' => $country === '' || $states->isEmpty(),'searchPlaceholder' => 'Search state…','menuWidth' => 300,'fixedMenu' => true,'hideLabel' => true,'class' => 'ft-create-shipment-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'State','property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('createShipments.'.$index.'.state'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment['state'] ?? ''),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($country === '' ? 'Select country first' : ($states->isEmpty() ? 'No states configured' : 'Select state')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($states),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($country === '' || $states->isEmpty()),'search-placeholder' => 'Search state…','menu-width' => 300,'fixed-menu' => true,'hide-label' => true,'class' => 'ft-create-shipment-select']); ?>
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
                    <small class="validation-error ft-create-shipment-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.state"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </div>

                <label class="ft-create-shipment-field">
                    <span>City</span>
                    <input
                        type="text"
                        wire:model.blur="createShipments.<?php echo e($index); ?>.city"
                        maxlength="120"
                        autocomplete="address-level2"
                        placeholder="e.g. Miami"
                    >
                    <small class="validation-error ft-create-shipment-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.city"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </label>

                <label class="ft-create-shipment-field">
                    <span>Postal code</span>
                    <input
                        type="text"
                        wire:model.blur="createShipments.<?php echo e($index); ?>.postal_code"
                        maxlength="30"
                        autocomplete="postal-code"
                        placeholder="e.g. 27510-2461"
                    >
                    <small class="validation-error ft-create-shipment-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.postal_code"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </label>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-create-shipment-service-grid">
        <label class="ft-create-shipment-field ft-create-shipment-number-field">
            <span>Shipment no.</span>
            <input type="text" value="<?php echo e($shipmentNumber); ?>" readonly tabindex="-1" aria-label="Shipment number <?php echo e($shipmentNumber); ?>">
        </label>

        <label class="ft-create-shipment-field ft-create-shipment-quantity">
            <span>Quantity <em>Optional</em></span>
            <input
                type="number"
                wire:model.defer="createShipments.<?php echo e($index); ?>.quantity"
                min="1"
                max="2147483647"
                step="1"
                inputmode="numeric"
                placeholder="e.g. 100"
                aria-label="Quantity for shipment <?php echo e($shipmentNumber); ?>"
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.quantity"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>

        <label class="ft-create-shipment-field ft-create-shipment-reference">
            <span>Package / reference <em>Optional</em></span>
            <input
                type="text"
                wire:model.defer="createShipments.<?php echo e($index); ?>.package_reference"
                maxlength="255"
                placeholder="<?php echo e($packagePlaceholder); ?>"
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["createShipments.$index.package_reference"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>

        <div class="ft-create-shipment-field ft-create-shipment-method-cell">
            <span>Shipping method</span>
            <?php if (isset($component)) { $__componentOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-method-picker','data' => ['shipmentMethods' => $shipmentMethods,'shipmentUrgencies' => $shipmentUrgencies,'shipmentIndex' => $index,'selectedMethodId' => $shipment['shipment_method_id'] ?? null,'selectedUrgencyId' => $shipment['shipment_urgency_id'] ?? null,'compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-method-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shipment-methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentMethods),'shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencies),'shipment-index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index),'selected-method-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment['shipment_method_id'] ?? null),'selected-urgency-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment['shipment_urgency_id'] ?? null),'compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9)): ?>
<?php $attributes = $__attributesOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9; ?>
<?php unset($__attributesOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9)): ?>
<?php $component = $__componentOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9; ?>
<?php unset($__componentOriginal2b7eb2cc82005dd6fbbfabb8b5fb6aa9); ?>
<?php endif; ?>
        </div>
    </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/shipping-row.blade.php ENDPATH**/ ?>