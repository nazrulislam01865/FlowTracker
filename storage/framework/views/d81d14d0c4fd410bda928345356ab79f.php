<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'shipments' => [],
    'mode' => 'multiple_shipments',
    'shipmentMethods' => collect(),
    'shipmentUrgencies' => collect(),
    'countries' => collect(),
    'statesByCountry' => collect(),
    'phoneCodes' => collect(),
    'savedShippingAddresses' => collect(),
    'showSavedShippingAddressPicker' => false,
    'savedShippingAddressShipmentIndex' => null,
    'referenceNumber' => '',
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
    'shipments' => [],
    'mode' => 'multiple_shipments',
    'shipmentMethods' => collect(),
    'shipmentUrgencies' => collect(),
    'countries' => collect(),
    'statesByCountry' => collect(),
    'phoneCodes' => collect(),
    'savedShippingAddresses' => collect(),
    'showSavedShippingAddressPicker' => false,
    'savedShippingAddressShipmentIndex' => null,
    'referenceNumber' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rows = collect($shipments)->values();
    $shipmentCount = max(1, $rows->count());
    $addressKeys = $rows->map(function ($shipment) {
        $parts = collect([
            data_get($shipment, 'address'),
            data_get($shipment, 'city'),
            data_get($shipment, 'state'),
            data_get($shipment, 'postal_code'),
            data_get($shipment, 'country'),
        ])->map(fn ($value) => trim((string) $value));

        $hasEnteredAddress = collect([
            data_get($shipment, 'address'),
            data_get($shipment, 'city'),
            data_get($shipment, 'state'),
            data_get($shipment, 'postal_code'),
        ])->contains(fn ($value) => trim((string) $value) !== '');

        return $hasEnteredAddress ? mb_strtolower($parts->implode('|')) : null;
    })->filter()->unique();
    $deliveryAddressCount = $addressKeys->count();
    $hasSavedAddresses = collect($savedShippingAddresses)->isNotEmpty();
    $targetIndex = $savedShippingAddressShipmentIndex ?? 0;
    $targetSourceAddressId = data_get($rows->get($targetIndex, []), 'shipping_source_address_id');

    $modeNote = match ($mode) {
        \App\Services\OrderShipmentService::MODE_SAME_ADDRESS => 'Shipment 1 delivery details are reused by every shipment.',
        \App\Services\OrderShipmentService::MODE_MULTIPLE_ADDRESS => 'Each shipment has its own delivery address.',
        default => 'Each shipment can keep or change the delivery address.',
    };
?>

<section class="ft-create-section ft-create-shipping-setup" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-order-shipping-setup'; ?>wire:key="create-order-shipping-setup">
    <div class="ft-create-shipping-setup-heading">
        <div class="ft-create-section-title">
            <span>2</span>
            <h2>Shipping setup</h2>
        </div>
        <p>Configure one or more shipments for this order.</p>
    </div>

    <div class="ft-create-shipping-modes" role="radiogroup" aria-label="Shipping setup mode">
        <label class="ft-create-shipping-mode <?php echo e($mode === 'multiple_shipments' ? 'is-selected' : ''); ?>">
            <input
                type="radio"
                name="create-shipment-mode"
                value="multiple_shipments"
                <?php if($mode === 'multiple_shipments'): echo 'checked'; endif; ?>
                wire:click="setCreateShipmentMode('multiple_shipments')"
            >
            <span class="ft-create-shipping-mode-copy">
                <strong>Allow multiple shipments</strong>
                <small>Start from the first address and change any shipment if needed.</small>
            </span>
        </label>
        <label class="ft-create-shipping-mode <?php echo e($mode === \App\Services\OrderShipmentService::MODE_SAME_ADDRESS ? 'is-selected' : ''); ?>">
            <input
                type="radio"
                name="create-shipment-mode"
                value="same_address"
                <?php if($mode === \App\Services\OrderShipmentService::MODE_SAME_ADDRESS): echo 'checked'; endif; ?>
                wire:click="setCreateShipmentMode('same_address')"
            >
            <span class="ft-create-shipping-mode-copy">
                <strong>Same address multiple shipment</strong>
                <small>Enter the delivery details once and reuse them for every shipment.</small>
            </span>
        </label>
        <label class="ft-create-shipping-mode <?php echo e($mode === \App\Services\OrderShipmentService::MODE_MULTIPLE_ADDRESS ? 'is-selected' : ''); ?>">
            <input
                type="radio"
                name="create-shipment-mode"
                value="multiple_address"
                <?php if($mode === \App\Services\OrderShipmentService::MODE_MULTIPLE_ADDRESS): echo 'checked'; endif; ?>
                wire:click="setCreateShipmentMode('multiple_address')"
            >
            <span class="ft-create-shipping-mode-copy">
                <strong>Multiple address multiple shipment</strong>
                <small>Set a separate delivery address for each shipment.</small>
            </span>
        </label>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createShipmentMode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-create-shipping-mode-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-create-shipping-summary">
        <div>
            <span class="ft-create-shipping-summary-item">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 7.5h10M6.5 4.5h7l1.5 3v7.5H5V7.5l1.5-3Z"/><path d="M7 15v1.5M13 15v1.5"/></svg>
                <strong><?php echo e($shipmentCount); ?></strong> <?php echo e(\Illuminate\Support\Str::plural('shipment', $shipmentCount)); ?> configured
            </span>
            <span class="ft-create-shipping-summary-item">
                <i></i><strong><?php echo e($deliveryAddressCount); ?></strong> <?php echo e(\Illuminate\Support\Str::plural('delivery address', $deliveryAddressCount)); ?> entered
            </span>
        </div>
        <span class="ft-create-shipping-summary-note">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 9v4M10 6.5h.01"/></svg>
            <?php echo e($modeNote); ?>

        </span>
    </div>

    <div class="ft-create-shipment-workspace">
        <div class="ft-create-shipment-rows">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalaff887bc5cafb648ad544f3e061f8594 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaff887bc5cafb648ad544f3e061f8594 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-row','data' => ['index' => $index,'shipment' => $shipment,'shipmentCount' => $shipmentCount,'mode' => $mode,'shipmentMethods' => $shipmentMethods,'shipmentUrgencies' => $shipmentUrgencies,'countries' => $countries,'statesByCountry' => $statesByCountry,'phoneCodes' => $phoneCodes,'referenceNumber' => $referenceNumber,'hasSavedAddresses' => $hasSavedAddresses]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index),'shipment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment),'shipment-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentCount),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode),'shipment-methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentMethods),'shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencies),'countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($countries),'states-by-country' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statesByCountry),'phone-codes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phoneCodes),'reference-number' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($referenceNumber),'has-saved-addresses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasSavedAddresses)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaff887bc5cafb648ad544f3e061f8594)): ?>
<?php $attributes = $__attributesOriginalaff887bc5cafb648ad544f3e061f8594; ?>
<?php unset($__attributesOriginalaff887bc5cafb648ad544f3e061f8594); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaff887bc5cafb648ad544f3e061f8594)): ?>
<?php $component = $__componentOriginalaff887bc5cafb648ad544f3e061f8594; ?>
<?php unset($__componentOriginalaff887bc5cafb648ad544f3e061f8594); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="ft-create-shipment-add-row">
            <button
                type="button"
                class="ft-create-shipment-add"
                wire:click="addCreateShipment"
                wire:loading.attr="disabled"
                wire:target="addCreateShipment"
                <?php if($rows->count() >= 20): echo 'disabled'; endif; ?>
            >
                <span aria-hidden="true">+</span> Add shipment
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === \App\Services\OrderShipmentService::MODE_SAME_ADDRESS): ?>
                <span class="ft-create-shipment-add-help">New shipments automatically use Shipment 1 contact and address.</span>
            <?php elseif($mode === \App\Services\OrderShipmentService::MODE_MULTIPLE_ADDRESS): ?>
                <span class="ft-create-shipment-add-help">Each new shipment starts with a blank delivery address.</span>
            <?php else: ?>
                <span class="ft-create-shipment-add-help">Add another package and adjust its address or shipping method as needed.</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createShipments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-create-shipping-table-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSavedShippingAddressPicker): ?>
        <div class="overlay livewire-overlay ft-order-saved-address-overlay" wire:click.self="closeSavedShippingAddressPicker"></div>
        <section
            class="modal livewire-modal ft-order-saved-address-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="ft-saved-address-title"
            x-data
            x-on:keydown.escape.window="$wire.closeSavedShippingAddressPicker()"
        >
            <div class="ft-order-saved-address-modal-head">
                <div>
                    <h3 id="ft-saved-address-title">Saved shipping addresses</h3>
                    <p>Choose a saved delivery address for Shipment <?php echo e($targetIndex + 1); ?>.</p>
                </div>
                <button type="button" wire:click="closeSavedShippingAddressPicker" aria-label="Close saved address picker">&times;</button>
            </div>
            <div class="ft-order-saved-address-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $savedShippingAddresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $savedAddress): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button
                        type="button"
                        class="ft-order-saved-address-card <?php echo e((int) $targetSourceAddressId === (int) $savedAddress->id ? 'is-selected' : ''); ?>"
                        wire:click="applySavedShippingAddressToCreateShipment(<?php echo e($savedAddress->id); ?>)"
                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-order-saved-address-'.e($targetIndex).'-'.e($savedAddress->id).''; ?>wire:key="create-order-saved-address-<?php echo e($targetIndex); ?>-<?php echo e($savedAddress->id); ?>"
                    >
                        <span class="ft-order-saved-address-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4.5h11v15l-5.5-3.3-5.5 3.3v-15Z"/></svg>
                        </span>
                        <span class="ft-order-saved-address-copy">
                            <span class="ft-order-saved-address-label">
                                <strong><?php echo e($savedAddress->label ?: 'Shipping address'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($savedAddress->is_default): ?><em>Default</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($savedAddress->recipient): ?><span><?php echo e($savedAddress->recipient); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><?php echo e($savedAddress->address_line1); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($savedAddress->suite): ?>, <?php echo e($savedAddress->suite); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                            <span><?php echo e(collect([$savedAddress->city, $savedAddress->state, $savedAddress->zip])->filter()->implode(', ')); ?></span>
                            <span><?php echo e($savedAddress->country); ?></span>
                        </span>
                        <span class="ft-order-saved-address-use">Use address</span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-order-saved-address-empty">No saved shipping addresses are available for this client.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/shipping-setup.blade.php ENDPATH**/ ?>