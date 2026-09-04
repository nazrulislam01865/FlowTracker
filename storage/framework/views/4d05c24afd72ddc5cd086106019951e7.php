<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'task',
    'presentation' => [],
    'editingId' => null,
    'mode' => 'same_address',
    'form' => [],
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
    $canChooseAddressMode = ! $isPrimaryEdit;
    $sequence = $isEditing
        ? data_get($editingRow, 'sequence', '')
        : ($presentation['next_sequence'] ?? 2);
    $selectedCard = \App\Support\CreateOrderShippingMethodPresenter::selectedCard(
        collect($presentation['shipment_methods'] ?? []),
        collect($presentation['shipment_urgencies'] ?? []),
        filled($form['shipment_method_id'] ?? null) ? [(int) $form['shipment_method_id']] : [],
        filled($form['shipment_urgency_id'] ?? null) ? [(int) $form['shipment_urgency_id']] : [],
    );
    $countries = collect($presentation['countries'] ?? [])->filter()->values();
    $states = collect($presentation['states'] ?? [])->filter()->values();
    $currentCountry = (string) ($form['country'] ?? '');
    $currentState = (string) ($form['state'] ?? '');
?>

<div class="ft-ms-modal-backdrop" role="presentation" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipment-modal-'.e($task->id).'-'.e($editingId ?: 'new').''; ?>wire:key="shipment-modal-<?php echo e($task->id); ?>-<?php echo e($editingId ?: 'new'); ?>">
    <section class="ft-ms-modal" role="dialog" aria-modal="true" aria-labelledby="shipment-modal-title" x-data x-on:keydown.escape.window="$wire.closeShipmentModal()">
        <header class="ft-ms-modal__head">
            <div>
                <h2 id="shipment-modal-title"><?php echo e($isEditing ? 'Edit Shipment '.$sequence : 'Add Shipment'); ?></h2>
                <p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEditing): ?>
                        Update only this shipment's delivery, method, or reference details.
                    <?php else: ?>
                        Create another shipment and choose whether it uses Shipment 1's address.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
            <button type="button" class="ft-ms-modal__close" wire:click="closeShipmentModal" aria-label="Close">×</button>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChooseAddressMode): ?>
            <div class="ft-ms-address-mode" role="group" aria-label="Shipment address option">
                <span class="ft-ms-address-mode__label">Delivery address</span>
                <div class="ft-ms-address-mode__options">
                    <label class="ft-ms-address-choice <?php echo e($isSameAddress ? 'is-selected' : ''); ?>">
                        <input
                            type="radio"
                            name="shipment-modal-address-mode"
                            value="same_address"
                            <?php if($mode === 'same_address'): echo 'checked'; endif; ?>
                            wire:click="setShipmentModalAddressMode('same_address')"
                        >
                        <span>
                            <strong>Same as Shipment 1</strong>
                            <small>Reuse the primary delivery address</small>
                        </span>
                    </label>
                    <label class="ft-ms-address-choice <?php echo e(! $isSameAddress ? 'is-selected' : ''); ?>">
                        <input
                            type="radio"
                            name="shipment-modal-address-mode"
                            value="multiple_address"
                            <?php if($mode === 'multiple_address'): echo 'checked'; endif; ?>
                            wire:click="setShipmentModalAddressMode('multiple_address')"
                        >
                        <span>
                            <strong>Different address</strong>
                            <small>Add a separate shipping address</small>
                        </span>
                    </label>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-ms-modal__body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSameAddress && $primary && ! $isPrimaryEdit): ?>
                <div class="ft-ms-same-address">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10" cy="6.5" r="2.5"/><path d="M5.5 16v-2.2a4.5 4.5 0 0 1 9 0V16"/></svg>
                    <div>
                        <strong>Shipment 1 delivery address</strong>
                        <p><?php echo e($primary['recipient'] ?: 'Recipient not set'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($primary['phone']): ?> <span>•</span> <?php echo e($primary['phone']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        <small>
                            <?php echo e($primary['address'] ?: 'Address not set'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($primary['city'] || $primary['state'] || $primary['postal_code'] || $primary['country']): ?>
                                · <?php echo e(collect([$primary['city'], $primary['state'], $primary['postal_code'], $primary['country']])->filter()->implode(', ')); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isSameAddress || $isPrimaryEdit): ?>
                <div class="ft-ms-form-grid ft-ms-form-grid--two">
                    <label class="ft-ms-field">
                        <span>CONTACT PERSON</span>
                        <input type="text" wire:model.defer="shipmentForm.recipient" maxlength="255" placeholder="e.g. John Smith">
                        <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.recipient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                    </label>
                    <label class="ft-ms-field">
                        <span>PHONE</span>
                        <input type="text" wire:model.defer="shipmentForm.phone" maxlength="80" placeholder="e.g. +1 555-123-4567">
                        <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                    </label>
                </div>

                <label class="ft-ms-field">
                    <span>SHIPPING ADDRESS</span>
                    <input type="text" wire:model.defer="shipmentForm.address" maxlength="2000" placeholder="e.g. 123 Main St, Apt 4B">
                    <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </label>

                <div class="ft-ms-form-grid ft-ms-form-grid--address">
                    <div class="ft-ms-field">
                        <span>COUNTRY</span>
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-ms-location-select','label' => 'Country','property' => 'shipmentForm.country','value' => $currentCountry,'options' => $countries,'placeholder' => 'Select country','selectedLabel' => $currentCountry !== '' ? $currentCountry : null,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'disabled' => $countries->isEmpty(),'menuWidth' => 320,'searchPlaceholder' => 'Search country…','wire:key' => 'shipment-country-select-'.e($task->id).'-'.e($editingId ?: 'new').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-ms-location-select','label' => 'Country','property' => 'shipmentForm.country','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentCountry),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($countries),'placeholder' => 'Select country','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentCountry !== '' ? $currentCountry : null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($countries->isEmpty()),'menu-width' => 320,'search-placeholder' => 'Search country…','wire:key' => 'shipment-country-select-'.e($task->id).'-'.e($editingId ?: 'new').'']); ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($countries->isEmpty()): ?>
                            <small class="ft-ms-field-hint">No active countries are configured in master data.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                    </div>
                    <div class="ft-ms-field">
                        <span>STATE</span>
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-ms-location-select','label' => 'State','property' => 'shipmentForm.state','value' => $currentState,'options' => $states,'placeholder' => 'Select state','selectedLabel' => $currentState !== '' ? $currentState : null,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'disabled' => $currentCountry === '' || $states->isEmpty(),'menuWidth' => 300,'searchPlaceholder' => 'Search state…','wire:key' => 'shipment-state-select-'.e($task->id).'-'.e(\Illuminate\Support\Str::slug($currentCountry ?: 'none')).'-'.e($editingId ?: 'new').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-ms-location-select','label' => 'State','property' => 'shipmentForm.state','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentState),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($states),'placeholder' => 'Select state','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentState !== '' ? $currentState : null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentCountry === '' || $states->isEmpty()),'menu-width' => 300,'search-placeholder' => 'Search state…','wire:key' => 'shipment-state-select-'.e($task->id).'-'.e(\Illuminate\Support\Str::slug($currentCountry ?: 'none')).'-'.e($editingId ?: 'new').'']); ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentCountry !== '' && $states->isEmpty()): ?>
                            <small class="ft-ms-field-hint">No active states are configured for this country; state is not required.</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                    </div>
                    <label class="ft-ms-field">
                        <span>CITY</span>
                        <input type="text" wire:model.defer="shipmentForm.city" maxlength="120" placeholder="e.g. Miami">
                        <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                    </label>
                    <label class="ft-ms-field">
                        <span>POSTAL CODE</span>
                        <input type="text" wire:model.defer="shipmentForm.postal_code" maxlength="30" placeholder="e.g. 33101">
                        <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                    </label>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-ms-form-grid ft-ms-form-grid--shipping">
                <label class="ft-ms-field">
                    <span>SHIPMENT NO.</span>
                    <input type="text" value="<?php echo e($sequence); ?>" readonly>
                    <small class="validation-error ft-ms-validation-slot" aria-hidden="true"></small>
                </label>
                <label class="ft-ms-field">
                    <span>QUANTITY (OPTIONAL)</span>
                    <input type="number" wire:model.defer="shipmentForm.quantity" min="1" max="2147483647" step="1" inputmode="numeric" placeholder="e.g. 100">
                    <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentForm.quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </label>
                <label class="ft-ms-field">
                    <span>PACKAGE / REFERENCE (OPTIONAL)</span>
                    <input type="text" wire:model.defer="shipmentForm.package_reference" maxlength="255" placeholder="e.g. PO-45781 | Box 2 of 3">
                    <small class="validation-error ft-ms-validation-slot" aria-hidden="true"></small>
                </label>
                <div class="ft-ms-field">
                    <span>SHIPPING METHOD</span>
                    <?php if (isset($component)) { $__componentOriginal35e3b281c47117d59d117e40a9d6d494 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35e3b281c47117d59d117e40a9d6d494 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipment.method-picker','data' => ['selected' => $selectedCard,'methods' => $presentation['shipment_methods'] ?? collect(),'urgencies' => $presentation['shipment_urgencies'] ?? collect(),'mode' => 'modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipment.method-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedCard),'methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($presentation['shipment_methods'] ?? collect()),'urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($presentation['shipment_urgencies'] ?? collect()),'mode' => 'modal']); ?>
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
                    <small class="validation-error ft-ms-validation-slot"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentMethod'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small>
                </div>
            </div>
        </div>

        <footer class="ft-ms-modal__footer">
            <button type="button" class="ft-ms-outline-btn" wire:click="closeShipmentModal">Cancel</button>
            <button type="button" class="ft-ms-primary-btn" wire:click="saveShipment" wire:loading.attr="disabled" wire:target="saveShipment">
                <?php echo e($isEditing ? 'Save changes' : 'Add shipment'); ?>

            </button>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/add-modal.blade.php ENDPATH**/ ?>