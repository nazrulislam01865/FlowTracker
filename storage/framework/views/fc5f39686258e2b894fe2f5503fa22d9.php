<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'task',
    'presentation' => [],
    'editingId' => null,
    'mode' => 'same_address',
    'form' => [],
    'showSavedAddressPicker' => false,
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
    'job',
    'task',
    'presentation' => [],
    'editingId' => null,
    'mode' => 'same_address',
    'form' => [],
    'showSavedAddressPicker' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isEditing = filled($editingId);
    $isSameAddress = $mode === \App\Services\OrderShipmentService::MODE_SAME_ADDRESS;
    $primary = $presentation['primary_shipment'] ?? null;
    $editingRow = $isEditing
        ? collect($presentation['shipments'] ?? [])->firstWhere('id', (int) $editingId)
        : null;
    $isPrimaryEdit = (bool) data_get($editingRow, 'is_primary', false);
    $sequence = $isEditing
        ? data_get($editingRow, 'sequence', '')
        : ($presentation['next_sequence'] ?? 2);
    $phoneCodes = collect($presentation['phone_country_codes'] ?? [])->values();
    $savedAddresses = collect($presentation['saved_shipping_addresses'] ?? [])->values();
    $shipmentMethods = collect($presentation['shipment_methods'] ?? [])->values();
    $shipmentUrgencies = collect($presentation['shipment_urgencies'] ?? [])->values();
    $selectedShipmentMethodId = filled($form['shipment_method_id'] ?? null)
        ? (int) $form['shipment_method_id']
        : null;
    $selectedShipmentUrgencyId = filled($form['shipment_urgency_id'] ?? null)
        ? (int) $form['shipment_urgency_id']
        : null;
    $selectedShipmentMethod = $selectedShipmentMethodId
        ? \App\Support\CreateOrderShippingMethodPresenter::selectedCard(
            $shipmentMethods,
            $shipmentUrgencies,
            [$selectedShipmentMethodId],
            $selectedShipmentUrgencyId ? [$selectedShipmentUrgencyId] : [],
        )
        : null;

    if (! $selectedShipmentMethod
        && $isEditing
        && $selectedShipmentMethodId
        && (int) data_get($editingRow, 'shipment_method_id', 0) === $selectedShipmentMethodId) {
        $selectedShipmentMethod = data_get($editingRow, 'method_card');
    }
?>

<div class="ft-ms-modal-backdrop" role="presentation" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-modal-'.e($task->id).'-'.e($editingId ?: 'new').''; ?>wire:key="shipment-modal-<?php echo e($task->id); ?>-<?php echo e($editingId ?: 'new'); ?>">
    <section
        class="ft-ms-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="shipment-modal-title"
        x-data
        x-on:keydown.escape.window="$wire.closeShipmentModal()"
    >
        <header class="ft-ms-modal__head">
            <div>
                <h2 id="shipment-modal-title"><?php echo e($isEditing ? 'Edit Shipment '.$sequence : 'Add Shipment'); ?></h2>
                <p>
                    <?php echo e($isEditing
                        ? 'Update this shipment\'s delivery address.'
                        : 'Add another shipment delivery address.'); ?>

                </p>
            </div>
            <button type="button" class="ft-ms-modal__close" wire:click="closeShipmentModal" aria-label="Close">×</button>
        </header>

        <div class="ft-ms-modal__body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSameAddress && $primary && ! $isPrimaryEdit): ?>
                <div class="ft-ms-same-address ft-ms-same-address--compact">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10" cy="6.5" r="2.5"/><path d="M5.5 16v-2.2a4.5 4.5 0 0 1 9 0V16"/></svg>
                    <div class="ft-ms-same-address__copy">
                        <strong>Uses Shipment 1 delivery address</strong>
                        <p><?php echo e($primary['recipient'] ?: 'Contact person not set'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($primary['phone']): ?> <span>•</span> <?php echo e($primary['phone']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        <small>
                            <?php echo e($primary['address'] ?: 'Address not set'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($primary['postal_code']): ?> · <?php echo e($primary['postal_code']); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </small>
                    </div>
                    <button type="button" class="ft-ms-use-different-address" wire:click="setShipmentModalAddressMode('multiple_address')">
                        Use different address
                    </button>
                </div>
            <?php else: ?>
                <div class="ft-ms-shipment-address-form ft-form-standard ft-form-standard--order" data-ft-ui-component="shipment-address-fields">
                    <div class="ft-ms-shipment-address-primary-grid ft-create-shipment-primary-grid">
                        <label class="ft-create-field ft-ms-shipment-address-field ft-create-shipment-field">
                            <b>Contact person <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                            <input
                                type="text"
                                wire:model.blur="shipmentForm.recipient"
                                maxlength="255"
                                autocomplete="name"
                                placeholder="e.g. John Smith"
                                aria-required="true"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.recipient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <div class="ft-create-field ft-ms-shipment-address-field ft-create-shipment-field">
                            <b>Phone <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                            <div class="ft-ms-shipment-phone-row ft-create-shipment-phone-row">
                                <div class="ft-ms-shipment-phone-control ft-ms-shipment-phone-code ft-create-shipment-phone-control ft-create-shipment-phone-code">
                                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-ms-shipment-phone-code-select ft-create-shipment-phone-code-select','label' => 'Phone country code for shipment '.e($sequence).'','property' => 'shipmentForm.phone_country_code','value' => $form['phone_country_code'] ?? '','options' => $phoneCodes,'placeholder' => '+Code','selectedLabel' => ($form['phone_country_code'] ?? '') ?: null,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'searchPlaceholder' => 'Search code or country…','wire:key' => 'shipment-modal-phone-code-'.e($task->id).'-'.e($editingId ?: 'new').'-'.e($form['phone_country_code'] ?? 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-ms-shipment-phone-code-select ft-create-shipment-phone-code-select','label' => 'Phone country code for shipment '.e($sequence).'','property' => 'shipmentForm.phone_country_code','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($form['phone_country_code'] ?? ''),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phoneCodes),'placeholder' => '+Code','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($form['phone_country_code'] ?? '') ?: null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'search-placeholder' => 'Search code or country…','wire:key' => 'shipment-modal-phone-code-'.e($task->id).'-'.e($editingId ?: 'new').'-'.e($form['phone_country_code'] ?? 'none').'']); ?>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.phone_country_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ft-ms-shipment-phone-control ft-ms-shipment-phone-number ft-create-shipment-phone-control ft-create-shipment-phone-number">
                                    <input
                                        type="text"
                                        wire:model.blur="shipmentForm.phone"
                                        maxlength="80"
                                        inputmode="tel"
                                        autocomplete="tel"
                                        placeholder="e.g. 555-123-4567"
                                        aria-label="Phone for shipment <?php echo e($sequence); ?>"
                                        aria-required="true"
                                    >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.phone'];
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

                    <div class="ft-ms-shipment-address-block ft-create-shipment-address-block">
                        <div class="ft-create-field ft-ms-shipment-address-field ft-ms-shipment-address-input-field ft-create-shipment-field ft-create-shipment-address-field">
                            <b>Shipping address <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                            <button type="button" class="ft-ms-shipment-saved-address ft-create-shipment-saved-address" wire:click="openShipmentSavedAddressPicker">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5.5 3.5h9v13l-4.5-2.6-4.5 2.6v-13Z"/></svg>
                                Use saved address
                            </button>
                            <input
                                class="ft-ms-shipment-address-input ft-create-shipment-address-input"
                                type="text"
                                wire:model.blur="shipmentForm.address"
                                maxlength="2000"
                                autocomplete="street-address"
                                placeholder="Street address, suite, building, etc."
                                aria-label="Shipping address for shipment <?php echo e($sequence); ?>"
                                aria-required="true"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-ms-shipment-address-error ft-create-shipment-address-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <label class="ft-create-field ft-ms-shipment-address-field ft-ms-shipment-postal-field ft-create-shipment-field ft-create-shipment-postal-field">
                            <b>Postal code <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                            <input
                                type="text"
                                wire:model.blur="shipmentForm.postal_code"
                                maxlength="30"
                                autocomplete="postal-code"
                                placeholder="e.g. 27510-2461"
                                aria-required="true"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.postal_code'];
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

            <div class="ft-form-standard ft-form-standard--order ft-create-field ft-ms-shipment-address-field ft-ms-shipment-method-field ft-create-shipment-field">
                <b>Shipping method <span class="ft-order-required-star" aria-hidden="true">*</span></b>
                <?php if (isset($component)) { $__componentOriginal35e3b281c47117d59d117e40a9d6d494 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35e3b281c47117d59d117e40a9d6d494 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.method-picker','data' => ['selected' => $selectedShipmentMethod,'methods' => $shipmentMethods,'urgencies' => $shipmentUrgencies,'taskId' => $task->id,'shipmentId' => $editingId,'mode' => 'modal','appearance' => 'field']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.method-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedShipmentMethod),'methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentMethods),'urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencies),'task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->id),'shipment-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editingId),'mode' => 'modal','appearance' => 'field']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal35e3b281c47117d59d117e40a9d6d494)): ?>
<?php $attributes = $__attributesOriginal35e3b281c47117d59d117e40a9d6d494; ?>
<?php unset($__attributesOriginal35e3b281c47117d59d117e40a9d6d494); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal35e3b281c47117d59d117e40a9d6d494)): ?>
<?php $component = $__componentOriginal35e3b281c47117d59d117e40a9d6d494; ?>
<?php unset($__componentOriginal35e3b281c47117d59d117e40a9d6d494); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentMethod'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <footer class="ft-ms-modal__footer">
            <button type="button" class="ft-ms-outline-btn" wire:click="closeShipmentModal">Cancel</button>
            <button type="button" class="ft-ms-primary-btn" wire:click="saveShipment" wire:loading.attr="disabled" wire:target="saveShipment">
                <?php echo e($isEditing ? 'Save changes' : 'Add shipment'); ?>

            </button>
        </footer>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSavedAddressPicker): ?>
        <div class="overlay livewire-overlay ft-order-saved-address-overlay ft-ms-saved-address-overlay" wire:click.self="closeShipmentSavedAddressPicker"></div>
        <section
            class="ft-order-saved-address-modal ft-ms-saved-address-modal ft-form-standard ft-form-standard--order"
            role="dialog"
            aria-modal="true"
            aria-labelledby="shipment-saved-address-title"
            x-data
            x-on:keydown.escape.window="$wire.closeShipmentSavedAddressPicker()"
        >
            <div class="ft-order-saved-address-modal-head">
                <div>
                    <h3 id="shipment-saved-address-title">Use saved address</h3>
                    <p>Select a saved client shipping address for Shipment <?php echo e($sequence); ?>.</p>
                </div>
                <button type="button" wire:click="closeShipmentSavedAddressPicker" aria-label="Close saved address picker">&times;</button>
            </div>

            <div class="ft-order-saved-address-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $savedAddresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $savedAddress): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button
                        type="button"
                        class="ft-order-saved-address-card <?php echo e((int) data_get($form, 'shipping_source_address_id', 0) === (int) data_get($savedAddress, 'id', 0) ? 'is-selected' : ''); ?>"
                        wire:click="applyShipmentSavedAddress(<?php echo e((int) data_get($savedAddress, 'id')); ?>)"
                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-modal-saved-address-'.e($task->id).'-'.e((int) data_get($savedAddress, 'id')).''; ?>wire:key="shipment-modal-saved-address-<?php echo e($task->id); ?>-<?php echo e((int) data_get($savedAddress, 'id')); ?>"
                    >
                        <span class="ft-order-saved-address-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4.5h11v15l-5.5-3.3-5.5 3.3v-15Z"/></svg>
                        </span>
                        <span class="ft-order-saved-address-copy">
                            <span class="ft-order-saved-address-label">
                                <strong><?php echo e(data_get($savedAddress, 'label') ?: 'Shipping address'); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($savedAddress, 'is_default')): ?><em>Default</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($savedAddress, 'recipient')): ?><span><?php echo e(data_get($savedAddress, 'recipient')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><?php echo e(data_get($savedAddress, 'address_line1')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($savedAddress, 'suite')): ?>, <?php echo e(data_get($savedAddress, 'suite')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                            <span><?php echo e(collect([data_get($savedAddress, 'city'), data_get($savedAddress, 'state'), data_get($savedAddress, 'zip')])->filter()->implode(', ')); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($savedAddress, 'country')): ?><span><?php echo e(data_get($savedAddress, 'country')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-order-saved-address-use">Use address</span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-order-saved-address-empty">No saved shipping addresses are available for this client.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/shipment/add-modal.blade.php ENDPATH**/ ?>