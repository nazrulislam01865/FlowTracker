<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'selectedClient' => null,
    'savedDeliveryContacts' => collect(),
    'phoneCountryCodeOptions' => collect(),
    'shippingPhoneCountryCode' => '+1',
    'shippingPhone' => '',
    'shippingContactType' => 'end_customer',
    'shippingContactId' => null,
    'shippingContactSelection' => '',
    'shippingContactName' => '',
    'shippingSaveContact' => true,
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
    'selectedClient' => null,
    'savedDeliveryContacts' => collect(),
    'phoneCountryCodeOptions' => collect(),
    'shippingPhoneCountryCode' => '+1',
    'shippingPhone' => '',
    'shippingContactType' => 'end_customer',
    'shippingContactId' => null,
    'shippingContactSelection' => '',
    'shippingContactName' => '',
    'shippingSaveContact' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $middleContacts = $selectedClient?->contacts ?? collect();
    $selectedMiddleContact = $shippingContactId
        ? $middleContacts->firstWhere('id', (int) $shippingContactId)
        : null;

    $manualSavedContacts = collect($savedDeliveryContacts)
        ->where('contact_type', $shippingContactType)
        ->values();
    $endCustomerSavedCount = collect($savedDeliveryContacts)->where('contact_type', 'end_customer')->count();
    $otherSavedCount = collect($savedDeliveryContacts)->where('contact_type', 'other_contact')->count();
    $currentPhone = trim(collect([$shippingPhoneCountryCode, $shippingPhone])->filter()->implode(' '));
    $matchedSavedContact = $manualSavedContacts->first(function ($contact) use ($shippingContactName, $shippingPhoneCountryCode, $shippingPhone): bool {
        return mb_strtolower(trim((string) $contact->name)) === mb_strtolower(trim($shippingContactName))
            && trim((string) ($contact->phone_country_code ?? '')) === trim($shippingPhoneCountryCode)
            && trim((string) $contact->phone) === trim($shippingPhone);
    });
    $contactOptions = $shippingContactType === 'middle_client'
        ? $middleContacts->map(function ($contact): array {
            $meta = collect([$contact->job_title, $contact->phone])->filter()->implode(' · ');
            return ['id' => (string) $contact->id, 'label' => (string) $contact->name, 'meta' => $meta];
        })->values()
        : $manualSavedContacts->map(function ($contact): array {
            $phone = trim(collect([$contact->phone_country_code, $contact->phone])->filter()->implode(' '));
            return ['id' => (string) $contact->id, 'label' => (string) $contact->name, 'meta' => $phone];
        })->values();
?>

<div
    class="ft-order-delivery-contact"
    data-ft-ui-component="order-delivery-contact"
    x-data="{
        contactType: <?php echo \Illuminate\Support\Js::from((string) $shippingContactType)->toHtml() ?>,
        contactId: <?php echo \Illuminate\Support\Js::from($shippingContactId)->toHtml() ?>,
        selection: <?php echo \Illuminate\Support\Js::from((string) $shippingContactSelection)->toHtml() ?>,
        contactName: <?php echo \Illuminate\Support\Js::from((string) $shippingContactName)->toHtml() ?>,
        countryCode: <?php echo \Illuminate\Support\Js::from((string) $shippingPhoneCountryCode)->toHtml() ?>,
        phone: <?php echo \Illuminate\Support\Js::from((string) $shippingPhone)->toHtml() ?>,
        saveContact: <?php echo \Illuminate\Support\Js::from((bool) $shippingSaveContact)->toHtml() ?>,
        switching: false,
        selectedClientName: <?php echo \Illuminate\Support\Js::from((string) ($selectedClient?->name ?? 'the selected client'))->toHtml() ?>,
        placeholderFor(type) {
            if (type === 'end_customer') return 'Enter end customer name';
            if (type === 'middle_client') return 'Search or enter client contact';
            return 'Enter contact name';
        },
        async switchContactType(type) {
            type = String(type || '');
            if (!type || this.switching || type === this.contactType) return;

            const previous = this.contactType;
            this.contactType = type;
            this.switching = true;

            try {
                const payload = await $wire.call('selectShippingContactType', type);
                this.applyPayload(payload);
            } catch (error) {
                this.contactType = previous;
                throw error;
            } finally {
                this.switching = false;
            }
        },
        applyPayload(payload) {
            if (!payload || typeof payload !== 'object') return;

            this.contactType = String(payload.type || this.contactType);
            this.contactId = payload.contactId ?? null;
            this.selection = String(payload.selection || '');
            this.contactName = String(payload.name || '');
            this.countryCode = String(payload.countryCode || '+1');
            this.phone = String(payload.phone || '');
            this.saveContact = Boolean(payload.saveContact);

            const detail = {
                ...payload,
                placeholder: this.placeholderFor(this.contactType),
            };

            this.$nextTick(() => {
                if (this.$el) {
                    this.$el.querySelectorAll('.validation-error').forEach((element) => element.remove());
                }
                window.dispatchEvent(new CustomEvent('flowtrack-shipping-contact-switched', { detail }));
                window.dispatchEvent(new CustomEvent('flowtrack-search-select-sync', {
                    detail: {
                        property: 'shippingPhoneCountryCode',
                        value: this.countryCode,
                        label: this.countryCode,
                    },
                }));
            });
        },
        get isCustomContact() {
            return String(this.selection || '').startsWith('custom:');
        },
        get isSavedSelection() {
            return /^\\d+$/.test(String(this.selection || ''));
        },
        get saveLabel() {
            if (this.contactType === 'middle_client') {
                if (this.isCustomContact) {
                    return `New contact will be saved to ${this.selectedClientName} for future orders`;
                }
                if (this.isSavedSelection && this.contactName) {
                    return `Save phone changes to ${this.contactName}'s contact profile`;
                }
                return 'Save this contact to the selected client for future orders';
            }

            return this.isCustomContact
                ? 'New contact will be saved for future orders'
                : 'Save this contact for future orders';
        },
        get badgeLabel() {
            if (!this.isSavedSelection) return '';
            return this.contactType === 'middle_client' ? 'Client contact' : 'Saved contact';
        },
        get contactTypeLabel() {
            if (this.contactType === 'end_customer') return 'end-customer';
            if (this.contactType === 'middle_client') return 'middle-client';
            return 'other';
        },
        get currentPhone() {
            return [this.countryCode, this.phone].map((value) => String(value || '').trim()).filter(Boolean).join(' ');
        },
    }"
    x-on:flowtrack-shipping-contact-payload.window="applyPayload($event.detail)"
    x-on:flowtrack-shipping-contact-name-input="contactName = String($event.detail?.name || '')"
    x-on:flowtrack-selection-changed="if ($event.detail?.property === 'shippingPhoneCountryCode') countryCode = String($event.detail?.value || '')"
>
    <div class="ft-order-delivery-contact-heading">
        <b>Delivery contact phone</b>
        <small>Choose whose phone number should be used for delivery coordination.</small>
    </div>

    <div class="ft-order-contact-source-tabs" role="radiogroup" aria-label="Delivery contact source">
        <button
            type="button"
            class="ft-order-contact-source"
            x-bind:class="{ 'is-active': contactType === 'end_customer' }"
            x-on:click="switchContactType('end_customer')"
            x-bind:aria-pressed="(contactType === 'end_customer').toString()"
            x-bind:disabled="switching"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20c.8-4 3.3-6 7.5-6s6.7 2 7.5 6"/></svg>
            <span>
                <strong>End customer</strong>
                <small><?php echo e($endCustomerSavedCount > 0 ? $endCustomerSavedCount.' saved' : 'Enter contact details'); ?></small>
            </span>
        </button>

        <button
            type="button"
            class="ft-order-contact-source"
            x-bind:class="{ 'is-active': contactType === 'middle_client' }"
            x-on:click="switchContactType('middle_client')"
            x-bind:aria-pressed="(contactType === 'middle_client').toString()"
            x-bind:disabled="switching"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20c.8-4 3.3-6 7.5-6s6.7 2 7.5 6"/></svg>
            <span>
                <strong>Middle client</strong>
                <small><?php echo e($selectedClient?->name ?? 'Select client first'); ?></small>
            </span>
        </button>

        <button
            type="button"
            class="ft-order-contact-source"
            x-bind:class="{ 'is-active': contactType === 'other_contact' }"
            x-on:click="switchContactType('other_contact')"
            x-bind:aria-pressed="(contactType === 'other_contact').toString()"
            x-bind:disabled="switching"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20c.8-4 3.3-6 7.5-6s6.7 2 7.5 6"/></svg>
            <span>
                <strong>Other contact</strong>
                <small><?php echo e($otherSavedCount > 0 ? $otherSavedCount.' saved' : 'Enter contact details'); ?></small>
            </span>
        </button>
    </div>

    <div
        class="ft-order-contact-warning"
        role="status"
        x-cloak
        x-show="contactType === 'middle_client' && <?php echo \Illuminate\Support\Js::from($middleContacts->isEmpty())->toHtml() ?>"
    >
        <span aria-hidden="true">!</span>
        <p>No saved client contacts yet. Type a new contact person below and it will be saved to <?php echo e($selectedClient?->name ?? 'the selected client'); ?> when the Order is created.</p>
    </div>

    <div class="ft-order-delivery-contact-grid">
        <div class="ft-create-field ft-order-contact-person-field">
            <b>Contact person *</b>
            <?php if (isset($component)) { $__componentOriginal9c2db6c3c9a4f7b7d0a388360a85f557 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c2db6c3c9a4f7b7d0a388360a85f557 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.contact-person-combobox','data' => ['type' => $shippingContactType,'value' => $shippingContactName,'selectedId' => $shippingContactSelection,'options' => $contactOptions,'placeholder' => $shippingContactType === 'end_customer' ? 'Enter end customer name' : ($shippingContactType === 'middle_client' ? 'Search or enter client contact' : 'Enter contact name'),'wire:key' => 'create-order-contact-person-'.e($selectedClient?->id ?? 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.contact-person-combobox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingContactType),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingContactName),'selected-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingContactSelection),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contactOptions),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingContactType === 'end_customer' ? 'Enter end customer name' : ($shippingContactType === 'middle_client' ? 'Search or enter client contact' : 'Enter contact name')),'wire:key' => 'create-order-contact-person-'.e($selectedClient?->id ?? 'none').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c2db6c3c9a4f7b7d0a388360a85f557)): ?>
<?php $attributes = $__attributesOriginal9c2db6c3c9a4f7b7d0a388360a85f557; ?>
<?php unset($__attributesOriginal9c2db6c3c9a4f7b7d0a388360a85f557); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c2db6c3c9a4f7b7d0a388360a85f557)): ?>
<?php $component = $__componentOriginal9c2db6c3c9a4f7b7d0a388360a85f557; ?>
<?php unset($__componentOriginal9c2db6c3c9a4f7b7d0a388360a85f557); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shippingContactId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shippingContactName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-create-field ft-order-country-code-field">
            <b>Country code *</b>
            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-order-delivery-phone-code-filter','label' => 'Country code','property' => 'shippingPhoneCountryCode','type' => 'phone-country-codes','context' => 'create-job','action' => 'setCreateShippingPhoneCountryCode','value' => $shippingPhoneCountryCode,'placeholder' => '+Code','selectedLabel' => $shippingPhoneCountryCode ?: null,'initialOptions' => $phoneCountryCodeOptions,'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'create-order-delivery-phone-code']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-delivery-phone-code-filter','label' => 'Country code','property' => 'shippingPhoneCountryCode','type' => 'phone-country-codes','context' => 'create-job','action' => 'setCreateShippingPhoneCountryCode','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingPhoneCountryCode),'placeholder' => '+Code','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingPhoneCountryCode ?: null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phoneCountryCodeOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'create-order-delivery-phone-code']); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shippingPhoneCountryCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <label class="ft-create-field ft-order-delivery-phone-field">
            <b>Phone number *</b>
            <input wire:model="shippingPhone" x-model="phone" inputmode="tel" autocomplete="tel" placeholder="Enter phone number">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shippingPhone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>
    </div>

    <div class="ft-order-contact-save-row">
        <label class="ft-order-contact-save-check" for="create-order-save-contact-fast">
            <input
                id="create-order-save-contact-fast"
                type="checkbox"
                wire:model="shippingSaveContact"
                x-model="saveContact"
                x-bind:disabled="isCustomContact"
            >
            <span x-text="saveLabel"></span>
        </label>

        <span class="ft-order-saved-contact-badge" x-cloak x-show="badgeLabel !== ''">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5.5 10.2 2.8 2.8 6.2-6.2"/></svg>
            <span x-text="badgeLabel"></span>
        </span>
    </div>

    <div class="ft-order-contact-success" role="status" x-cloak x-show="contactName !== '' && currentPhone !== ''">
        <span aria-hidden="true">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="m5.5 10.2 2.8 2.8 6.2-6.2"/></svg>
        </span>
        <p>Using <span x-text="contactTypeLabel"></span> contact: <strong x-text="contactName"></strong> · <span x-text="currentPhone"></span></p>
    </div>

    <label class="ft-create-field ft-order-postal-row" for="create-order-postal-code">
        <b>Postal Code *</b>
        <input id="create-order-postal-code" wire:model="shippingPostalCode" autocomplete="postal-code" required aria-required="true" placeholder="Enter postal code">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shippingPostalCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </label>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/shipping-contact.blade.php ENDPATH**/ ?>