<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'payload' => []]));

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

foreach (array_filter((['job', 'payload' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-shipment-modal-form">
    <div class="ft-shipment-modal-notice">
        <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 9v4M10 6.5h.01"/></svg>
        <p>These changes apply to this shipment. The original order and client profile will not change unless you choose to update the saved contact.</p>
    </div>

    <section class="ft-shipment-modal-section">
        <h3>Recipient</h3>
        <div class="ft-shipment-modal-grid ft-shipment-modal-grid--two">
            <label class="ft-shipment-modal-field">
                <span>Client name <b>*</b></span>
                <input wire:model="orderWorkflowActionPayload.client_name">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.client_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
            <label class="ft-shipment-modal-field">
                <span>Contact person <b>*</b></span>
                <select wire:model="orderWorkflowActionPayload.contact_selection" wire:change="selectShipmentContact($event.target.value)">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($payload['contact_options'] ?? [])): ?>
                        <option value="current"><?php echo e(trim((string) ($payload['contact_name'] ?? $payload['recipient'] ?? '')) ?: 'Current shipment contact'); ?></option>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($payload['contact_options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($option['value']); ?>"><?php echo e($option['label']); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
        </div>

        <div class="ft-shipment-modal-grid ft-shipment-modal-grid--phone">
            <label class="ft-shipment-modal-field">
                <span>Phone source <b>*</b></span>
                <select wire:model="orderWorkflowActionPayload.contact_type">
                    <option value="middle_client">Middle client contact</option>
                    <option value="end_customer">End customer</option>
                    <option value="other_contact">Other contact</option>
                </select>
            </label>
            <div class="ft-shipment-modal-field">
                <span>Country code <b>*</b></span>
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-shipment-modal-search-select','label' => 'Country code','property' => 'orderWorkflowActionPayload.phone_country_code','value' => $payload['phone_country_code'] ?? '','options' => $payload['phone_country_code_options'] ?? [],'placeholder' => '+Code','selectedLabel' => ($payload['phone_country_code'] ?? '') ?: null,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 280,'searchPlaceholder' => 'Search country code…','wire:key' => 'shipment-phone-country-code-'.e($job->id).'-'.e($payload['phone_country_code'] ?? 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-shipment-modal-search-select','label' => 'Country code','property' => 'orderWorkflowActionPayload.phone_country_code','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payload['phone_country_code'] ?? ''),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payload['phone_country_code_options'] ?? []),'placeholder' => '+Code','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($payload['phone_country_code'] ?? '') ?: null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'menu-width' => 280,'search-placeholder' => 'Search country code…','wire:key' => 'shipment-phone-country-code-'.e($job->id).'-'.e($payload['phone_country_code'] ?? 'none').'']); ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.phone_country_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <label class="ft-shipment-modal-field">
                <span>Phone number <b>*</b></span>
                <input wire:model="orderWorkflowActionPayload.phone_number" placeholder="1712 345678">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
        </div>
        <div class="ft-shipment-saved-indicator">
            <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m6.5 10 2.2 2.2 4.8-4.8"/></svg>
            Saved contact
        </div>
    </section>

    <section class="ft-shipment-modal-section ft-shipment-modal-section--address">
        <div class="ft-shipment-modal-section__title">
            <h3>Delivery address</h3>
            <button type="button" wire:click="useShipmentSavedAddress('<?php echo e((string) ($payload['address_selection'] ?? '')); ?>')">Use saved address</button>
        </div>
        <label class="ft-shipment-modal-field">
            <span>Shipping address <b>*</b></span>
            <textarea rows="3" wire:model="orderWorkflowActionPayload.address"></textarea>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>
        <div class="ft-shipment-modal-grid ft-shipment-modal-grid--two">
            <label class="ft-shipment-modal-field"><span>City</span><input wire:model="orderWorkflowActionPayload.city"></label>
            <label class="ft-shipment-modal-field"><span>State / region</span><input wire:model="orderWorkflowActionPayload.state"></label>
            <div class="ft-shipment-modal-field">
                <span>Country <b>*</b></span>
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-shipment-modal-search-select','label' => 'Country','property' => 'orderWorkflowActionPayload.country','value' => $payload['country'] ?? '','options' => $payload['country_options'] ?? [],'placeholder' => 'Select country','selectedLabel' => ($payload['country'] ?? '') ?: null,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 320,'searchPlaceholder' => 'Search country…','wire:key' => 'shipment-country-'.e($job->id).'-'.e(\Illuminate\Support\Str::slug((string) ($payload['country'] ?? 'none'))).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-shipment-modal-search-select','label' => 'Country','property' => 'orderWorkflowActionPayload.country','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payload['country'] ?? ''),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payload['country_options'] ?? []),'placeholder' => 'Select country','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($payload['country'] ?? '') ?: null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'menu-width' => 320,'search-placeholder' => 'Search country…','wire:key' => 'shipment-country-'.e($job->id).'-'.e(\Illuminate\Support\Str::slug((string) ($payload['country'] ?? 'none'))).'']); ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <label class="ft-shipment-modal-field"><span>Postal code <b>*</b></span><input wire:model="orderWorkflowActionPayload.postal_code"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
        </div>
        <label class="ft-shipment-update-contact-check">
            <input type="checkbox" wire:model="orderWorkflowActionPayload.update_saved_contact">
            <span>Also update <?php echo e(trim((string) ($payload['contact_name'] ?? $payload['recipient'] ?? '')) ?: 'this contact'); ?>'s saved contact information.<small>Leave unchecked to use these changes for this shipment only.</small></span>
        </label>
        <div class="ft-shipment-contact-summary">
            <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="7" r="3"/><path d="M4.5 16c.7-3 2.5-4.5 5.5-4.5s4.8 1.5 5.5 4.5"/></svg>
            <span>Shipment contact: <strong><?php echo e(trim((string) ($payload['contact_name'] ?? $payload['recipient'] ?? '')) ?: '—'); ?></strong> · <?php echo e(trim((string) (($payload['phone_country_code'] ?? '').' '.($payload['phone_number'] ?? ''))) ?: '—'); ?></span>
        </div>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/update-details-form.blade.php ENDPATH**/ ?>