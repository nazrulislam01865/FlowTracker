@props([
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments',
    'priority'=>'Medium','productionUrgencies'=>collect(),'shipmentUrgencies'=>collect(),'productionUrgencyIds'=>[],'shipmentUrgencyIds'=>[],'isRepeatedOrder'=>false,'repeatedOrderNumber'=>'',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'productCategories'=>collect(),'productSearchResults'=>collect(),'selectedProductDetails'=>collect(),'activeProductCount'=>0,'productResultTotal'=>0,
    'canUseOrderProductSelector'=>false,'canCreateCatalogProduct'=>false,'canViewProductCategories'=>false,'canCreateProductCategory'=>false,'duplicateProduct'=>null,'newProductCategoryMatches'=>collect(),'newProductSimilarCategories'=>collect(),
    'newProductSimilarProducts'=>collect(),'newProductSelectedCategory'=>null,'newProductHasExactCategory'=>false,'newProductImagePreview'=>null,
    'createProductSearch'=>'','createProductCategoryFilter'=>'','createProductShowAllResults'=>false,'showCreateOrderProductModal'=>false,
    'newProductCode'=>'','newProductCategoryId'=>null,'newProductCategorySearch'=>'','newProductCategoryName'=>'','newProductName'=>'',
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'workflowSelectorVersion'=>0,'workflowPhaseId'=>null,'mentionUsers'=>collect(),
    'savedShippingAddresses'=>collect(),'showSavedShippingAddressPicker'=>false,'shippingSourceAddressId'=>null,
    'phoneCountryCodeOptions'=>collect(),'shippingPhoneCountryCode'=>'',
])
@php
    $selectedClient = $clients->firstWhere('id', (int)$clientId);
    $selectedWorkflow = $workflows->firstWhere('id', (int)$workflowId);
    $selectedOwnerOption = collect($ownerFilterOptions)->first(fn($item) => (int)($item['id'] ?? 0) === (int)($ownerId ?? 0));
    $allowedPhases = $selectedWorkflow?->phases?->where('is_active', true)->where('allow_job_start', true) ?? collect();
    $taskCount = $selectedWorkflow?->phases?->sum(fn($phase) => $phase->taskPack?->templates?->count() ?? 0) ?? 0;
    $totalUnits = collect($jobItems)->sum(fn($item)=>(int)($item['quantity'] ?? 0));
    $createReady = $catalogReady && $assignmentReady && $workflowReady && $canUseOrderProductSelector;
@endphp
<div {{ $attributes->class('ft-create-job-page') }}>
    <div class="ft-create-shell">
        <div class="ft-create-breadcrumb">Orders / Create order</div>
        <div class="ft-create-title"><h1>Create new order</h1><p>Set the order scope, products, shipping, ownership and workflow.</p></div>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>1</span><h2>Order basics</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field"><b>Order code</b><div class="ft-locked-input">Generated automatically <span>♙</span></div></label>
                <div class="ft-create-field">
                    <x-ui.remote-filter
                        class="ft-create-remote-select"
                        label="Client *"
                        property="clientId"
                        type="clients"
                        context="create-job"
                        action="setCreateSelector"
                        :value="$clientId"
                        placeholder="Select client"
                        :selected-label="$selectedClient?->name"
                        :initial-options="$clientFilterOptions"
                        :clearable="false"
                        wire:key="create-client-selector-{{ $clientId ?: 'none' }}"
                    />
                    @error('clientId')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
                <label class="ft-create-field"><b>Client contact</b><input value="{{ $selectedClient?->contact_name ?? 'No contact recorded' }}" readonly></label>
                <label class="ft-create-field"><b>Reference number</b><input wire:model="referenceNumber" placeholder="e.g. REF-00028 or customer PO number">@error('referenceNumber')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-create-field ft-repeat-order-option">
                    <b>Repeated order</b>
                    <label class="ft-repeat-order-check">
                        <input type="checkbox" wire:model.live="isRepeatedOrder">
                        <span>Is this a repeated order?</span>
                    </label>
                    @error('isRepeatedOrder')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
                @if($isRepeatedOrder)
                    <label class="ft-create-field" wire:key="repeated-order-number-field">
                        <b>Previous reference number *</b>
                        <input wire:model="repeatedOrderNumber" placeholder="Enter the previous order reference number">
                        @error('repeatedOrderNumber')<small class="validation-error">{{ $message }}</small>@enderror
                    </label>
                @endif
                <label class="ft-create-field"><b>Order title *</b><input wire:model="jobTitle" placeholder="e.g. Conference merchandise order">@error('jobTitle')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-create-field ft-mention-host"><b>Request description</b><textarea class="ft-mention-input" data-rich-text wire:model="description" rows="4" autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}" placeholder="Type @ to mention a user. Add specifications, target price or customization requirements..."></textarea>@error('description')<small class="validation-error">{{ $message }}</small>@enderror</div>
            </div>
        </section>

        <section class="ft-create-section ft-order-shipping-section" wire:key="create-order-shipping-address">
            <div class="ft-order-shipping-head">
                <div class="ft-order-shipping-heading">
                    <div class="ft-create-section-title ft-order-shipping-title">
                        <span>2</span>
                        <h2>Shipping address</h2>
                        <em class="ft-order-optional-badge">Optional</em>
                    </div>
                    <p>Add the delivery address for this Order if needed.</p>
                </div>
                <button
                    type="button"
                    class="ft-order-saved-address-button"
                    wire:click="openSavedShippingAddressPicker"
                    @disabled(!$clientId || $savedShippingAddresses->isEmpty())
                    title="{{ !$clientId ? 'Select a client first' : ($savedShippingAddresses->isEmpty() ? 'This client has no saved shipping addresses' : 'Choose from this client\'s saved shipping addresses') }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4.5h11v15l-5.5-3.3-5.5 3.3v-15Z"/></svg>
                    <span>Use saved address</span>
                </button>
            </div>

            <label class="ft-create-field ft-order-shipping-address-field">
                <b>Shipping Address</b>
                <textarea wire:model="shippingAddress" rows="5" placeholder="Recipient name&#10;Street address&#10;City, State, Country"></textarea>
                @error('shippingAddress')<small class="validation-error">{{ $message }}</small>@enderror
            </label>

            <div class="ft-order-shipping-contact-grid">
                <div class="ft-create-field">
                    <b>Phone Number</b>
                    <div class="ft-order-phone-control">
                        <x-ui.remote-filter
                            class="ft-order-phone-code-filter"
                            label="Phone country code"
                            property="shippingPhoneCountryCode"
                            type="phone-country-codes"
                            context="create-job"
                            :value="$shippingPhoneCountryCode"
                            placeholder="Code"
                            :selected-label="$shippingPhoneCountryCode ?: null"
                            :initial-options="$phoneCountryCodeOptions"
                            :clearable="true"
                            :fixed-menu="true"
                            :menu-width="300"
                            wire:key="create-order-phone-country-code-{{ $shippingPhoneCountryCode ?: 'none' }}"
                        />
                        <input wire:model="shippingPhone" inputmode="tel" autocomplete="tel" placeholder="Enter phone number">
                    </div>
                    @error('shippingPhoneCountryCode')<small class="validation-error">{{ $message }}</small>@enderror
                    @error('shippingPhone')<small class="validation-error">{{ $message }}</small>@enderror
                </div>

                <label class="ft-create-field">
                    <b>Postal Code</b>
                    <input wire:model="shippingPostalCode" autocomplete="postal-code" placeholder="Enter postal code">
                    @error('shippingPostalCode')<small class="validation-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </section>

        @if($showSavedShippingAddressPicker)
            <div class="overlay livewire-overlay ft-order-saved-address-overlay" wire:click.self="closeSavedShippingAddressPicker"></div>
            <section class="modal livewire-modal ft-order-saved-address-modal" role="dialog" aria-modal="true" aria-labelledby="ft-saved-address-title" x-data x-on:keydown.escape.window="$wire.closeSavedShippingAddressPicker()">
                <div class="ft-order-saved-address-modal-head">
                    <div>
                        <h3 id="ft-saved-address-title">Saved shipping addresses</h3>
                        <p>Choose a saved delivery address for {{ $selectedClient?->name ?? 'this client' }}.</p>
                    </div>
                    <button type="button" wire:click="closeSavedShippingAddressPicker" aria-label="Close saved address picker">&times;</button>
                </div>
                <div class="ft-order-saved-address-list">
                    @forelse($savedShippingAddresses as $savedAddress)
                        <button
                            type="button"
                            class="ft-order-saved-address-card {{ (int) $shippingSourceAddressId === (int) $savedAddress->id ? 'is-selected' : '' }}"
                            wire:click="useSavedShippingAddress({{ $savedAddress->id }})"
                            wire:key="create-order-saved-address-{{ $savedAddress->id }}"
                        >
                            <span class="ft-order-saved-address-card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4.5h11v15l-5.5-3.3-5.5 3.3v-15Z"/></svg>
                            </span>
                            <span class="ft-order-saved-address-copy">
                                <span class="ft-order-saved-address-label">
                                    <strong>{{ $savedAddress->label ?: 'Shipping address' }}</strong>
                                    @if($savedAddress->is_default)<em>Default</em>@endif
                                </span>
                                @if($savedAddress->recipient)<span>{{ $savedAddress->recipient }}</span>@endif
                                <span>{{ $savedAddress->address_line1 }}@if($savedAddress->suite), {{ $savedAddress->suite }}@endif</span>
                                <span>{{ collect([$savedAddress->city, $savedAddress->state, $savedAddress->zip])->filter()->implode(', ') }}</span>
                                <span>{{ $savedAddress->country }}</span>
                            </span>
                            <span class="ft-order-saved-address-use">Use address</span>
                        </button>
                    @empty
                        <div class="ft-order-saved-address-empty">No saved shipping addresses are available for this client.</div>
                    @endforelse
                </div>
            </section>
        @endif

        @include('components.jobs.create-products')

        @if($assignmentReady)
        <section class="ft-create-section" wire:key="create-assignment-ready">
            <div class="ft-create-section-title"><span>4</span><h2>Schedule & owner</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.deliveryDate?.showPicker?.(); $refs.deliveryDate?.focus(); }"><b>Customer required delivery date</b><input x-ref="deliveryDate" type="date" wire:model="deliveryDate">@error('deliveryDate')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.estimatedDeliveryDate?.showPicker?.(); $refs.estimatedDeliveryDate?.focus(); }"><b>Estimated Delivery date</b><input x-ref="estimatedDeliveryDate" type="date" wire:model="estimatedDeliveryDate">@error('estimatedDeliveryDate')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-create-urgency-grid">
                    <div class="ft-create-field ft-create-urgency-field">
                        <b>Select order production urgency</b>
                        <div class="ft-create-urgency-control" role="radiogroup" aria-label="Select order production urgency">
                            @forelse($productionUrgencies as $urgency)
                                <label class="ft-create-urgency-check" wire:key="production-urgency-{{ $urgency->id }}">
                                    <input
                                        type="radio"
                                        name="create-production-urgency"
                                        value="{{ $urgency->id }}"
                                        @checked((int) ($productionUrgencyIds[0] ?? 0) === (int) $urgency->id)
                                        wire:click="selectCreateProductionUrgency({{ $urgency->id }})"
                                    >
                                    <span>{{ $urgency->name }}</span>
                                </label>
                            @empty
                                <small>No active Production Urgency options in Master Data.</small>
                            @endforelse
                        </div>
                        @error('productionUrgencyIds')<small class="validation-error">{{ $message }}</small>@enderror
                        @error('productionUrgencyIds.*')<small class="validation-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="ft-create-field ft-create-urgency-field">
                        <b>Select order shipment urgency</b>
                        <div class="ft-create-urgency-control" role="radiogroup" aria-label="Select order shipment urgency">
                            @forelse($shipmentUrgencies as $urgency)
                                <label class="ft-create-urgency-check" wire:key="shipment-urgency-{{ $urgency->id }}">
                                    <input
                                        type="radio"
                                        name="create-shipment-urgency"
                                        value="{{ $urgency->id }}"
                                        @checked((int) ($shipmentUrgencyIds[0] ?? 0) === (int) $urgency->id)
                                        wire:click="selectCreateShipmentUrgency({{ $urgency->id }})"
                                    >
                                    <span>{{ $urgency->name }}</span>
                                </label>
                            @empty
                                <small>No active Shipment Urgency options in Master Data.</small>
                            @endforelse
                        </div>
                        @error('shipmentUrgencyIds')<small class="validation-error">{{ $message }}</small>@enderror
                        @error('shipmentUrgencyIds.*')<small class="validation-error">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="ft-create-field">
                    <x-ui.remote-filter
                        class="ft-create-remote-select"
                        label="Order owner *"
                        property="ownerId"
                        type="users"
                        context="create-job"
                        action="setCreateSelector"
                        :value="$ownerId"
                        placeholder="Select owner"
                        :selected-label="$selectedOwnerOption['label'] ?? null"
                        :initial-options="$ownerFilterOptions"
                        :clearable="false"
                        wire:key="create-owner-selector"
                    />
                    <small>Accountable for overall delivery.</small>
                    @error('ownerId')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
            </div>
        </section>
        @else
            <x-jobs.create-section-placeholder number="4" title="Schedule & owner" section="assignment" :rows="5" />
        @endif

        @if($workflowReady)
            <x-ui.create-workflow-picker
                class="ft-create-section"
                step="5"
                title="What happens next"
                :workflow-options="$workflowFilterOptions"
                :selected-workflow-id="$workflowId"
                :selected-workflow-name="$selectedWorkflow?->name ?? 'Select workflow'"
                :phase-count="$selectedWorkflow?->phases?->where('is_active', true)->count() ?? 0"
                :task-count="$taskCount"
                selection-property="workflowId"
                option-fallback="Order workflow"
                footnote="Tasks are created when you select Create order. Workflow and starting phase are fixed after creation."
                :preview-allowed="auth()->user()->canAccess('workflow.view')"
                error-field="workflowId"
                :start-phases="$allowedPhases"
                :start-phase-id="$workflowPhaseId"
                start-phase-property="workflowPhaseId"
                start-phase-error-field="workflowPhaseId"
                wire:key="create-order-workflow-picker-{{ $clientId ?: 'none' }}-{{ $workflowSelectorVersion }}"
            />
        @else
            <x-jobs.create-section-placeholder number="5" title="What happens next" section="workflow" :rows="2" />
        @endif

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>6</span><h2>Attachments</h2></div>
            @if(auth()->user()->canModule('documents','create'))
                <div class="ft-create-upload-wrap">
                <div class="ft-create-upload ft-livewire-upload-zone" data-file-dropzone>
                    <span class="ft-create-paperclip">⌕</span>
                    <div><b>Drop files here or <label for="job-create-files">browse</label></b><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    @if(auth()->user()->canModule('document_archive','view'))<a href="{{ route('documents.index') }}" wire:navigate>Open Documents</a>@endif
                    <input id="job-create-files" type="file" wire:model="jobAttachments" multiple hidden accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                </div>
                </div>
            @else
                <div class="ft-create-note">Your role does not allow document uploads during Order creation.</div>
            @endif
            @if(count($jobAttachments))<div class="ft-create-upload-list">@foreach($jobAttachments as $file)<span>{{ $file->getClientOriginalName() }}</span>@endforeach</div>@endif
            @error('jobAttachments.*')<small class="validation-error">{{ $message }}</small>@enderror
        </section>

        <div class="ft-create-actions">
            <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
            <button type="button" class="ft-create-draft" wire:click="saveDraft" @disabled(!$createReady)>Save draft</button>
            <button type="button" class="ft-create-primary" wire:click="createJob" @disabled(!$createReady)>Create order</button>
        </div>
        @error('createLoading')<div class="validation-error">{{ $message }}</div>@enderror
    </div>
</div>
