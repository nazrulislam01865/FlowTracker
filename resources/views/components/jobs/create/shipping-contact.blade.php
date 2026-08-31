@props([
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
])

@php
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
    $contactTypeLabel = match ($shippingContactType) {
        'end_customer' => 'end-customer',
        'middle_client' => 'middle-client',
        default => 'other',
    };
    $contactOptions = $shippingContactType === 'middle_client'
        ? $middleContacts->map(function ($contact): array {
            $meta = collect([$contact->job_title, $contact->phone])->filter()->implode(' · ');
            return ['id' => (string) $contact->id, 'label' => (string) $contact->name, 'meta' => $meta];
        })->values()
        : $manualSavedContacts->map(function ($contact): array {
            $phone = trim(collect([$contact->phone_country_code, $contact->phone])->filter()->implode(' '));
            return ['id' => (string) $contact->id, 'label' => (string) $contact->name, 'meta' => $phone];
        })->values();
    $isCustomContact = str_starts_with((string) $shippingContactSelection, 'custom:');
@endphp

<div class="ft-order-delivery-contact" data-ft-ui-component="order-delivery-contact">
    <div class="ft-order-delivery-contact-heading">
        <b>Delivery contact phone</b>
        <small>Choose whose phone number should be used for delivery coordination.</small>
    </div>

    <div class="ft-order-contact-source-tabs" role="radiogroup" aria-label="Delivery contact source">
        <button
            type="button"
            class="ft-order-contact-source {{ $shippingContactType === 'end_customer' ? 'is-active' : '' }}"
            wire:click="selectShippingContactType('end_customer')"
            aria-pressed="{{ $shippingContactType === 'end_customer' ? 'true' : 'false' }}"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20c.8-4 3.3-6 7.5-6s6.7 2 7.5 6"/></svg>
            <span>
                <strong>End customer</strong>
                <small>{{ $endCustomerSavedCount > 0 ? $endCustomerSavedCount.' saved' : 'Enter contact details' }}</small>
            </span>
        </button>

        <button
            type="button"
            class="ft-order-contact-source {{ $shippingContactType === 'middle_client' ? 'is-active' : '' }}"
            wire:click="selectShippingContactType('middle_client')"
            aria-pressed="{{ $shippingContactType === 'middle_client' ? 'true' : 'false' }}"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20c.8-4 3.3-6 7.5-6s6.7 2 7.5 6"/></svg>
            <span>
                <strong>Middle client</strong>
                <small>{{ $selectedClient?->name ?? 'Select client first' }}</small>
            </span>
        </button>

        <button
            type="button"
            class="ft-order-contact-source {{ $shippingContactType === 'other_contact' ? 'is-active' : '' }}"
            wire:click="selectShippingContactType('other_contact')"
            aria-pressed="{{ $shippingContactType === 'other_contact' ? 'true' : 'false' }}"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20c.8-4 3.3-6 7.5-6s6.7 2 7.5 6"/></svg>
            <span>
                <strong>Other contact</strong>
                <small>{{ $otherSavedCount > 0 ? $otherSavedCount.' saved' : 'Enter contact details' }}</small>
            </span>
        </button>
    </div>

    @if($shippingContactType === 'middle_client' && $middleContacts->isEmpty())
        <div class="ft-order-contact-warning" role="status">
            <span aria-hidden="true">!</span>
            <p>No saved client contacts yet. Type a new contact person below and it will be saved to {{ $selectedClient?->name ?? 'the selected client' }} when the Order is created.</p>
        </div>
    @endif

    <div class="ft-order-delivery-contact-grid">
        <div class="ft-create-field ft-order-contact-person-field">
            <b>Contact person *</b>
            <x-jobs.create.contact-person-combobox
                :type="$shippingContactType"
                :value="$shippingContactName"
                :selected-id="$shippingContactSelection"
                :options="$contactOptions"
                :placeholder="$shippingContactType === 'end_customer' ? 'Enter end customer name' : ($shippingContactType === 'middle_client' ? 'Search or enter client contact' : 'Enter contact name')"
                wire:key="create-order-contact-person-{{ $shippingContactType }}-{{ $selectedClient?->id ?? 'none' }}"
            />
            @error('shippingContactId')<small class="validation-error">{{ $message }}</small>@enderror
            @error('shippingContactName')<small class="validation-error">{{ $message }}</small>@enderror
        </div>

        <div class="ft-create-field ft-order-country-code-field">
            <b>Country code *</b>
            <x-ui.search-select
                class="ft-order-delivery-phone-code-filter"
                label="Country code"
                property="shippingPhoneCountryCode"
                type="phone-country-codes"
                context="create-job"
                :value="$shippingPhoneCountryCode"
                placeholder="+Code"
                :selected-label="$shippingPhoneCountryCode ?: null"
                :initial-options="$phoneCountryCodeOptions"
                :clearable="false"
                :hide-label="true"
                :fixed-menu="true"
                :menu-width="280"
                wire:key="create-order-delivery-phone-code-{{ $shippingContactType }}-{{ $shippingPhoneCountryCode ?: 'none' }}"
            />
            @error('shippingPhoneCountryCode')<small class="validation-error">{{ $message }}</small>@enderror
        </div>

        <label class="ft-create-field ft-order-delivery-phone-field">
            <b>Phone number *</b>
            <input wire:model="shippingPhone" inputmode="tel" autocomplete="tel" placeholder="Enter phone number">
            @error('shippingPhone')<small class="validation-error">{{ $message }}</small>@enderror
        </label>
    </div>

    @if(in_array($shippingContactType, ['end_customer', 'middle_client', 'other_contact'], true))
        <div
            class="ft-order-contact-save-row"
            wire:key="create-order-contact-save-row-{{ $shippingContactType }}"
            data-contact-type="{{ $shippingContactType }}"
        >
            <label class="ft-order-contact-save-check" for="create-order-save-contact-{{ $shippingContactType }}">
                @if($shippingContactType === 'middle_client')
                    <input id="create-order-save-contact-{{ $shippingContactType }}" type="checkbox" wire:model="shippingSaveContact" @disabled($isCustomContact)>
                    <span>
                        {{ $isCustomContact
                            ? 'New contact will be saved to '.($selectedClient?->name ?? 'the selected client').' for future orders'
                            : ($selectedMiddleContact ? 'Save phone changes to '.$selectedMiddleContact->name.'\'s contact profile' : 'Save this contact to the selected client for future orders') }}
                    </span>
                @else
                    @if($isCustomContact)
                        <input id="create-order-save-contact-{{ $shippingContactType }}" type="checkbox" checked disabled aria-label="New contact will be saved for future orders">
                        <span>New contact will be saved for future orders</span>
                    @else
                        <input id="create-order-save-contact-{{ $shippingContactType }}" type="checkbox" wire:model="shippingSaveContact">
                        <span>Save this contact for future orders</span>
                    @endif
                @endif
            </label>

            @if($shippingContactType === 'middle_client' && $selectedMiddleContact)
                <span class="ft-order-saved-contact-badge">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5.5 10.2 2.8 2.8 6.2-6.2"/></svg>
                    Client contact
                </span>
            @elseif($shippingContactType !== 'middle_client' && $matchedSavedContact)
                <span class="ft-order-saved-contact-badge">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5.5 10.2 2.8 2.8 6.2-6.2"/></svg>
                    Saved contact
                </span>
            @endif
        </div>
    @endif

    @if($shippingContactName !== '' && $currentPhone !== '')
        <div class="ft-order-contact-success" role="status">
            <span aria-hidden="true">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="m5.5 10.2 2.8 2.8 6.2-6.2"/></svg>
            </span>
            <p>Using {{ $contactTypeLabel }} contact: <strong>{{ $shippingContactName }}</strong> · {{ $currentPhone }}</p>
        </div>
    @endif

    <label class="ft-create-field ft-order-postal-row" for="create-order-postal-code">
        <b>Postal Code *</b>
        <input id="create-order-postal-code" wire:model="shippingPostalCode" autocomplete="postal-code" required aria-required="true" placeholder="Enter postal code">
        @error('shippingPostalCode')<small class="validation-error">{{ $message }}</small>@enderror
    </label>
</div>
