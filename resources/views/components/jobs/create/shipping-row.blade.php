@props([
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
])

@php
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
@endphp

<article
    class="ft-create-shipment-card {{ $sameAddressLocked ? 'is-shared-address' : '' }}"
    wire:key="create-shipment-row-{{ $index }}"
    data-ft-ui-component="create-order-shipment-card"
>
    <header class="ft-create-shipment-card-head">
        <div class="ft-create-shipment-card-title">
            <div>
                <strong>Shipment {{ $shipmentNumber }}</strong>
                <small>{{ $sameAddressLocked ? 'Uses Shipment 1 delivery details' : 'Enter delivery and shipping details' }}</small>
            </div>
        </div>

        @if((int) $index > 0)
            <button
                type="button"
                class="ft-create-shipment-remove"
                wire:click="removeCreateShipment({{ $index }})"
                wire:loading.attr="disabled"
                wire:target="removeCreateShipment"
                aria-label="Remove shipment {{ $shipmentNumber }}"
            >
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M4.5 6h11M8 3.5h4M6.5 6l.6 10h5.8l.6-10M8.5 8.5v5M11.5 8.5v5"/>
                </svg>
                Remove shipment
            </button>
        @endif
    </header>

    @if($sameAddressLocked)
        <div class="ft-create-shipment-shared-address" aria-label="Shipment {{ $shipmentNumber }} uses the same delivery address as Shipment 1">
            <span class="ft-create-shipment-shared-icon" aria-hidden="true">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4.5 8.5 10 4l5.5 4.5V16H4.5V8.5Z"/><path d="M7.5 16v-4h5v4"/></svg>
            </span>
            <span class="ft-create-shipment-shared-copy">
                <strong>Same delivery address as Shipment 1</strong>
                <span>{{ $sharedContact !== '' ? $sharedContact : 'Contact details come from Shipment 1.' }}</span>
                <span>{{ $sharedAddress !== '' ? $sharedAddress : 'Complete Shipment 1 delivery address above.' }}</span>
            </span>
        </div>
    @else
        <div class="ft-create-shipment-primary-grid">
            <label class="ft-create-shipment-field">
                <span>Contact person</span>
                <input
                    type="text"
                    wire:model.blur="createShipments.{{ $index }}.contact_name"
                    maxlength="255"
                    autocomplete="name"
                    placeholder="e.g. John Smith"
                >
                @error("createShipments.$index.contact_name")<small class="validation-error">{{ $message }}</small>@enderror
            </label>

            <div class="ft-create-shipment-field">
                <span>Phone</span>
                <div class="ft-create-shipment-phone-row">
                    <div class="ft-create-shipment-phone-control ft-create-shipment-phone-code">
                        <select
                            wire:model.live="createShipments.{{ $index }}.phone_country_code"
                            aria-label="Phone country code for shipment {{ $shipmentNumber }}"
                        >
                            @foreach($phoneCodes as $phoneCode)
                                <option value="{{ $phoneCode }}">{{ $phoneCode }}</option>
                            @endforeach
                        </select>
                        @error("createShipments.$index.phone_country_code")<small class="validation-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="ft-create-shipment-phone-control ft-create-shipment-phone-number">
                        <input
                            type="text"
                            wire:model.blur="createShipments.{{ $index }}.phone"
                            maxlength="60"
                            inputmode="tel"
                            autocomplete="tel"
                            placeholder="e.g. 555-123-4567"
                            aria-label="Phone for shipment {{ $shipmentNumber }}"
                        >
                        @error("createShipments.$index.phone")<small class="validation-error">{{ $message }}</small>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ft-create-shipment-address-block">
            <div class="ft-create-shipment-address-toolbar">
                <span>Shipping address</span>
                @if($hasSavedAddresses)
                    <button type="button" wire:click="openSavedShippingAddressPickerForShipment({{ $index }})">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5.5 3.5h9v13l-4.5-2.6-4.5 2.6v-13Z"/></svg>
                        Use saved address
                    </button>
                @endif
            </div>

            <label class="ft-create-shipment-field ft-create-shipment-address-line">
                <span class="sr-only">Shipping address</span>
                <input
                    type="text"
                    wire:model.blur="createShipments.{{ $index }}.address"
                    maxlength="2000"
                    autocomplete="street-address"
                    placeholder="Street address, suite, building, etc."
                >
                @error("createShipments.$index.address")<small class="validation-error">{{ $message }}</small>@enderror
            </label>

            <div class="ft-create-shipment-location-grid">
                <div class="ft-create-shipment-field">
                    <span>Country</span>
                    <x-ui.select-filter
                        label="Country"
                        :property="'createShipments.'.$index.'.country'"
                        :value="$country"
                        placeholder="Select country"
                        :options="$countries"
                        :disabled="collect($countries)->isEmpty()"
                        search-placeholder="Search country…"
                        :menu-width="320"
                        :fixed-menu="true"
                        :hide-label="true"
                        class="ft-create-shipment-select"
                    />
                    <small class="validation-error ft-create-shipment-validation-slot">@error("createShipments.$index.country"){{ $message }}@enderror</small>
                </div>

                <div class="ft-create-shipment-field">
                    <span>State</span>
                    <x-ui.select-filter
                        label="State"
                        :property="'createShipments.'.$index.'.state'"
                        :value="$shipment['state'] ?? ''"
                        :placeholder="$country === '' ? 'Select country first' : ($states->isEmpty() ? 'No states configured' : 'Select state')"
                        :options="$states"
                        :disabled="$country === '' || $states->isEmpty()"
                        search-placeholder="Search state…"
                        :menu-width="300"
                        :fixed-menu="true"
                        :hide-label="true"
                        class="ft-create-shipment-select"
                    />
                    <small class="validation-error ft-create-shipment-validation-slot">@error("createShipments.$index.state"){{ $message }}@enderror</small>
                </div>

                <label class="ft-create-shipment-field">
                    <span>City</span>
                    <input
                        type="text"
                        wire:model.blur="createShipments.{{ $index }}.city"
                        maxlength="120"
                        autocomplete="address-level2"
                        placeholder="e.g. Miami"
                    >
                    <small class="validation-error ft-create-shipment-validation-slot">@error("createShipments.$index.city"){{ $message }}@enderror</small>
                </label>

                <label class="ft-create-shipment-field">
                    <span>Postal code</span>
                    <input
                        type="text"
                        wire:model.blur="createShipments.{{ $index }}.postal_code"
                        maxlength="30"
                        autocomplete="postal-code"
                        placeholder="e.g. 27510-2461"
                    >
                    <small class="validation-error ft-create-shipment-validation-slot">@error("createShipments.$index.postal_code"){{ $message }}@enderror</small>
                </label>
            </div>
        </div>
    @endif

    <div class="ft-create-shipment-service-grid">
        <label class="ft-create-shipment-field ft-create-shipment-number-field">
            <span>Shipment no.</span>
            <input type="text" value="{{ $shipmentNumber }}" readonly tabindex="-1" aria-label="Shipment number {{ $shipmentNumber }}">
        </label>

        <label class="ft-create-shipment-field ft-create-shipment-quantity">
            <span>Quantity <em>Optional</em></span>
            <input
                type="number"
                wire:model.defer="createShipments.{{ $index }}.quantity"
                min="1"
                max="2147483647"
                step="1"
                inputmode="numeric"
                placeholder="e.g. 100"
                aria-label="Quantity for shipment {{ $shipmentNumber }}"
            >
            @error("createShipments.$index.quantity")<small class="validation-error">{{ $message }}</small>@enderror
        </label>

        <label class="ft-create-shipment-field ft-create-shipment-reference">
            <span>Package / reference <em>Optional</em></span>
            <input
                type="text"
                wire:model.defer="createShipments.{{ $index }}.package_reference"
                maxlength="255"
                placeholder="{{ $packagePlaceholder }}"
            >
            @error("createShipments.$index.package_reference")<small class="validation-error">{{ $message }}</small>@enderror
        </label>

        <div class="ft-create-shipment-field ft-create-shipment-method-cell">
            <span>Shipping method</span>
            <x-jobs.create.shipping-method-picker
                :shipment-methods="$shipmentMethods"
                :shipment-urgencies="$shipmentUrgencies"
                :shipment-index="$index"
                :selected-method-id="$shipment['shipment_method_id'] ?? null"
                :selected-urgency-id="$shipment['shipment_urgency_id'] ?? null"
                :compact="true"
            />
        </div>
    </div>
</article>
