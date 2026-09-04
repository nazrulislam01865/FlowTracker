@props([
    'job',
    'task',
    'presentation' => [],
    'editingId' => null,
    'mode' => 'same_address',
    'form' => [],
])

@php
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
@endphp

<div class="ft-ms-modal-backdrop" role="presentation" wire:key="shipment-modal-{{ $task->id }}-{{ $editingId ?: 'new' }}">
    <section class="ft-ms-modal" role="dialog" aria-modal="true" aria-labelledby="shipment-modal-title" x-data x-on:keydown.escape.window="$wire.closeShipmentModal()">
        <header class="ft-ms-modal__head">
            <div>
                <h2 id="shipment-modal-title">{{ $isEditing ? 'Edit Shipment '.$sequence : 'Add Shipment' }}</h2>
                <p>
                    @if($isEditing)
                        Update only this shipment's delivery, method, or reference details.
                    @else
                        Create another shipment and choose whether it uses Shipment 1's address.
                    @endif
                </p>
            </div>
            <button type="button" class="ft-ms-modal__close" wire:click="closeShipmentModal" aria-label="Close">×</button>
        </header>

        @if($canChooseAddressMode)
            <div class="ft-ms-address-mode" role="group" aria-label="Shipment address option">
                <span class="ft-ms-address-mode__label">Delivery address</span>
                <div class="ft-ms-address-mode__options">
                    <label class="ft-ms-address-choice {{ $isSameAddress ? 'is-selected' : '' }}">
                        <input
                            type="radio"
                            name="shipment-modal-address-mode"
                            value="same_address"
                            @checked($mode === 'same_address')
                            wire:click="setShipmentModalAddressMode('same_address')"
                        >
                        <span>
                            <strong>Same as Shipment 1</strong>
                            <small>Reuse the primary delivery address</small>
                        </span>
                    </label>
                    <label class="ft-ms-address-choice {{ ! $isSameAddress ? 'is-selected' : '' }}">
                        <input
                            type="radio"
                            name="shipment-modal-address-mode"
                            value="multiple_address"
                            @checked($mode === 'multiple_address')
                            wire:click="setShipmentModalAddressMode('multiple_address')"
                        >
                        <span>
                            <strong>Different address</strong>
                            <small>Add a separate shipping address</small>
                        </span>
                    </label>
                </div>
            </div>
        @endif

        <div class="ft-ms-modal__body">
            @if($isSameAddress && $primary && ! $isPrimaryEdit)
                <div class="ft-ms-same-address">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10" cy="6.5" r="2.5"/><path d="M5.5 16v-2.2a4.5 4.5 0 0 1 9 0V16"/></svg>
                    <div>
                        <strong>Shipment 1 delivery address</strong>
                        <p>{{ $primary['recipient'] ?: 'Recipient not set' }}@if($primary['phone']) <span>•</span> {{ $primary['phone'] }}@endif</p>
                        <small>
                            {{ $primary['address'] ?: 'Address not set' }}
                            @if($primary['city'] || $primary['state'] || $primary['postal_code'] || $primary['country'])
                                · {{ collect([$primary['city'], $primary['state'], $primary['postal_code'], $primary['country']])->filter()->implode(', ') }}
                            @endif
                        </small>
                    </div>
                </div>
            @endif

            @if(!$isSameAddress || $isPrimaryEdit)
                <div class="ft-ms-form-grid ft-ms-form-grid--two">
                    <label class="ft-ms-field">
                        <span>CONTACT PERSON</span>
                        <input type="text" wire:model.defer="shipmentForm.recipient" maxlength="255" placeholder="e.g. John Smith">
                        <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.recipient'){{ $message }}@enderror</small>
                    </label>
                    <label class="ft-ms-field">
                        <span>PHONE</span>
                        <input type="text" wire:model.defer="shipmentForm.phone" maxlength="80" placeholder="e.g. +1 555-123-4567">
                        <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.phone'){{ $message }}@enderror</small>
                    </label>
                </div>

                <label class="ft-ms-field">
                    <span>SHIPPING ADDRESS</span>
                    <input type="text" wire:model.defer="shipmentForm.address" maxlength="2000" placeholder="e.g. 123 Main St, Apt 4B">
                    <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.address'){{ $message }}@enderror</small>
                </label>

                <div class="ft-ms-form-grid ft-ms-form-grid--address">
                    <div class="ft-ms-field">
                        <span>COUNTRY</span>
                        <x-ui.search-select
                            class="ft-ms-location-select"
                            label="Country"
                            property="shipmentForm.country"
                            :value="$currentCountry"
                            :options="$countries"
                            placeholder="Select country"
                            :selected-label="$currentCountry !== '' ? $currentCountry : null"
                            :clearable="false"
                            :hide-label="true"
                            :fixed-menu="true"
                            :disabled="$countries->isEmpty()"
                            :menu-width="320"
                            search-placeholder="Search country…"
                            wire:key="shipment-country-select-{{ $task->id }}-{{ $editingId ?: 'new' }}"
                        />
                        @if($countries->isEmpty())
                            <small class="ft-ms-field-hint">No active countries are configured in master data.</small>
                        @endif
                        <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.country'){{ $message }}@enderror</small>
                    </div>
                    <div class="ft-ms-field">
                        <span>STATE</span>
                        <x-ui.search-select
                            class="ft-ms-location-select"
                            label="State"
                            property="shipmentForm.state"
                            :value="$currentState"
                            :options="$states"
                            placeholder="Select state"
                            :selected-label="$currentState !== '' ? $currentState : null"
                            :clearable="false"
                            :hide-label="true"
                            :fixed-menu="true"
                            :disabled="$currentCountry === '' || $states->isEmpty()"
                            :menu-width="300"
                            search-placeholder="Search state…"
                            wire:key="shipment-state-select-{{ $task->id }}-{{ \Illuminate\Support\Str::slug($currentCountry ?: 'none') }}-{{ $editingId ?: 'new' }}"
                        />
                        @if($currentCountry !== '' && $states->isEmpty())
                            <small class="ft-ms-field-hint">No active states are configured for this country; state is not required.</small>
                        @endif
                        <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.state'){{ $message }}@enderror</small>
                    </div>
                    <label class="ft-ms-field">
                        <span>CITY</span>
                        <input type="text" wire:model.defer="shipmentForm.city" maxlength="120" placeholder="e.g. Miami">
                        <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.city'){{ $message }}@enderror</small>
                    </label>
                    <label class="ft-ms-field">
                        <span>POSTAL CODE</span>
                        <input type="text" wire:model.defer="shipmentForm.postal_code" maxlength="30" placeholder="e.g. 33101">
                        <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.postal_code'){{ $message }}@enderror</small>
                    </label>
                </div>
            @endif

            <div class="ft-ms-form-grid ft-ms-form-grid--shipping">
                <label class="ft-ms-field">
                    <span>SHIPMENT NO.</span>
                    <input type="text" value="{{ $sequence }}" readonly>
                    <small class="validation-error ft-ms-validation-slot" aria-hidden="true"></small>
                </label>
                <label class="ft-ms-field">
                    <span>QUANTITY (OPTIONAL)</span>
                    <input type="number" wire:model.defer="shipmentForm.quantity" min="1" max="2147483647" step="1" inputmode="numeric" placeholder="e.g. 100">
                    <small class="validation-error ft-ms-validation-slot">@error('shipmentForm.quantity'){{ $message }}@enderror</small>
                </label>
                <label class="ft-ms-field">
                    <span>PACKAGE / REFERENCE (OPTIONAL)</span>
                    <input type="text" wire:model.defer="shipmentForm.package_reference" maxlength="255" placeholder="e.g. PO-45781 | Box 2 of 3">
                    <small class="validation-error ft-ms-validation-slot" aria-hidden="true"></small>
                </label>
                <div class="ft-ms-field">
                    <span>SHIPPING METHOD</span>
                    <x-jobs.order-detail.shipment.method-picker
                        :selected="$selectedCard"
                        :methods="$presentation['shipment_methods'] ?? collect()"
                        :urgencies="$presentation['shipment_urgencies'] ?? collect()"
                        mode="modal"
                    />
                    <small class="validation-error ft-ms-validation-slot">@error('shipmentMethod'){{ $message }}@enderror</small>
                </div>
            </div>
        </div>

        <footer class="ft-ms-modal__footer">
            <button type="button" class="ft-ms-outline-btn" wire:click="closeShipmentModal">Cancel</button>
            <button type="button" class="ft-ms-primary-btn" wire:click="saveShipment" wire:loading.attr="disabled" wire:target="saveShipment">
                {{ $isEditing ? 'Save changes' : 'Add shipment' }}
            </button>
        </footer>
    </section>
</div>
