@props([
    'users',
    'clientCode',
    'clientCountries' => [],
    'clientCountryFlags' => [],
    'clientStatesByCountry' => [],
    'clientLanguages' => [],
    'clientCurrencies' => [],
    'paymentTermOptions' => [],
    'accountManagerId' => null,
    'preferredCurrency' => '',
    'clientCountry' => '',
    'billingCountry' => '',
    'billingSameAsOffice' => true,
    'salesTaxStatus' => 'taxable',
    'shippingAddresses' => [],
    'mode' => 'create',
])
@php
    $isEdit = $mode === 'edit';
    $selectedManager = $users->firstWhere('id', (int) $accountManagerId);
    $managerInitials = $selectedManager ? \App\Support\BoardPresenter::initials($selectedManager->name) : 'U';
    $officeStates = $clientStatesByCountry[$clientCountry] ?? [];
    $billingStates = $clientStatesByCountry[$billingCountry] ?? [];
@endphp
<div class="{{ $isEdit ? 'ft-client-inline-edit ft-client-create-prototype' : 'ft-create-client-page ft-client-create-prototype' }}">
    <div class="ft-client-create-shell">
        @unless($isEdit)
            <div class="ft-client-create-top">
                <div>
                    <div class="ft-create-breadcrumb">Add Client</div>
                    <h1>Add Client</h1>
                    <p>Client Information is saved as a full page, not a drawer.</p>
                </div>
                <button type="button" class="ft-back-clients" wire:click="closeCreate">← Back to Clients</button>
            </div>
        @endunless

        <section class="ft-client-prototype-card">
            <header class="ft-client-prototype-head">
                <div>
                    <h2>{{ $isEdit ? 'Edit Client' : 'Create New Client' }}</h2>
                    <p>{{ $isEdit ? 'Update the client business, contact, address and commercial information.' : "Add the client's business, contact and delivery information." }}</p>
                </div>
                <div class="ft-client-required-note"><span>*</span> Required <b>•</b> Optional fields are labeled</div>
            </header>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>1</span><div><h3>Client details</h3></div></div>
                <div class="ft-client-grid ft-client-grid-3">
                    <label class="ft-proto-field">
                        <b>Client code</b>
                        <div class="ft-client-code-lock"><span>{{ $clientCode }}</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10V7a5 5 0 0110 0v3m-9 0h8a2 2 0 012 2v7H6v-7a2 2 0 012-2z" fill="none" stroke="currentColor" stroke-width="1.8"/></svg></div>
                        <small>Generated automatically after the client is created.</small>
                    </label>
                    <label class="ft-proto-field">
                        <b>Client name <em>*</em></b>
                        <input wire:model="clientName" placeholder="Acme Apparel Inc.">
                        @error('clientName')<small class="validation-error">{{ $message }}</small>@enderror
                    </label>
                    <label class="ft-proto-field">
                        <b>Legal business name <span>(Optional)</span></b>
                        <input wire:model="legalBusinessName" placeholder="Acme Apparel Incorporated">
                    </label>
                    <label class="ft-proto-field">
                        <b>Website <span>(Optional)</span></b>
                        <input wire:model="website" placeholder="www.acmeapparel.com">
                    </label>
                    <label class="ft-proto-field">
                        <b>Account manager <em>*</em></b>
                        <div class="ft-manager-select"><span>{{ $managerInitials }}</span><select wire:model.live="accountManagerId"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                        @error('accountManagerId')<small class="validation-error">{{ $message }}</small>@enderror
                    </label>
                    <label class="ft-proto-field">
                        <b>Preferred language <span>(Optional)</span></b>
                        <select wire:model="preferredLanguage">@foreach($clientLanguages as $language)<option value="{{ $language }}">{{ $language }}</option>@endforeach</select>
                    </label>
                    <label class="ft-proto-field ft-currency-field">
                        <b>Preferred currency <em>*</em></b>
                        <select wire:model="preferredCurrency">
                            <option value="">Select currency</option>
                            @foreach($clientCurrencies as $code => $currencyName)
                                <option value="{{ $code }}">{{ $code }} · {{ $currencyName }}</option>
                            @endforeach
                        </select>
                        <small>Available currencies are managed in Master Data.</small>
                        @error('preferredCurrency')<small class="validation-error">{{ $message }}</small>@enderror
                    </label>
                </div>
            </section>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>2</span><div><h3>Primary contact</h3><p>Main person for inquiries, orders and documents.</p></div></div>
                <div class="ft-client-grid ft-client-grid-2">
                    <label class="ft-proto-field"><b>Contact name <em>*</em></b><input wire:model="contactName" placeholder="Sarah Chen">@error('contactName')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label class="ft-proto-field"><b>Job title <span>(Optional)</span></b><input wire:model="contactJobTitle" placeholder="Purchasing Manager"></label>
                    <label class="ft-proto-field"><b>Email <em>*</em></b><input type="email" wire:model="email" placeholder="purchasing@acmeapparel.com">@error('email')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label class="ft-proto-field"><b>Phone <span>(Optional)</span></b><input wire:model="phone" placeholder="+1 (212) 555-0184"></label>
                </div>
            </section>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>3</span><div><h3>Office &amp; billing address</h3><p>Used for correspondence and billing.</p></div></div>
                <div class="ft-client-grid ft-office-address-grid">
                    <label class="ft-proto-field ft-address-line"><b>Address line 1 <em>*</em></b><input wire:model="officeAddressLine1" placeholder="123 W 37th Street">@error('officeAddressLine1')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label class="ft-proto-field"><b>Suite / unit <span>(Optional)</span></b><input wire:model="officeSuite" placeholder="Suite 800"></label>
                    <label class="ft-proto-field"><b>City <em>*</em></b><input wire:model="officeCity" placeholder="New York">@error('officeCity')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label class="ft-proto-field"><b>State <em>*</em></b><select wire:model="officeState" @disabled(empty($officeStates))><option value="">{{ empty($officeStates) ? 'No states configured' : 'Select state' }}</option>@foreach($officeStates as $state)<option value="{{ $state }}">{{ $state }}</option>@endforeach</select>@error('officeState')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label class="ft-proto-field"><b>ZIP code <em>*</em></b><input wire:model="officeZip" placeholder="10018">@error('officeZip')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label class="ft-proto-field ft-country-field"><b>Country <em>*</em></b><div class="ft-country-select"><span>{{ $clientCountryFlags[$clientCountry] ?? '🌐' }}</span><select wire:model.live="clientCountry"><option value="">Select country</option>@foreach($clientCountries as $country)<option value="{{ $country }}">{{ $country }}</option>@endforeach</select></div><small>Countries are managed in Master Data.</small>@error('clientCountry')<small class="validation-error">{{ $message }}</small>@enderror</label>
                </div>
                <div class="ft-billing-choice">
                    <label><input type="checkbox" wire:model.live="billingSameAsOffice"> <span>Use office address as billing address</span></label>
                    @if($billingSameAsOffice)
                        <button type="button" wire:click="showDifferentBillingAddress">Add a different billing address</button>
                    @endif
                </div>
                @if(!$billingSameAsOffice)
                    <div class="ft-client-grid ft-billing-grid">
                        <label class="ft-proto-field ft-address-line"><b>Billing address line 1</b><input wire:model="billingAddressLine1" placeholder="Billing street address">@error('billingAddressLine1')<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <label class="ft-proto-field"><b>Suite / unit <span>(Optional)</span></b><input wire:model="billingSuite"></label>
                        <label class="ft-proto-field"><b>City</b><input wire:model="billingCity">@error('billingCity')<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <label class="ft-proto-field"><b>State</b><select wire:model="billingState" @disabled(empty($billingStates))><option value="">{{ empty($billingStates) ? 'No states configured' : 'Select state' }}</option>@foreach($billingStates as $state)<option value="{{ $state }}">{{ $state }}</option>@endforeach</select>@error('billingState')<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <label class="ft-proto-field"><b>ZIP code</b><input wire:model="billingZip">@error('billingZip')<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <label class="ft-proto-field ft-country-field"><b>Country</b><select wire:model.live="billingCountry"><option value="">Select country</option>@foreach($clientCountries as $country)<option value="{{ $country }}">{{ $country }}</option>@endforeach</select>@error('billingCountry')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    </div>
                @endif
            </section>

            <section class="ft-client-prototype-section ft-shipping-section">
                <div class="ft-client-section-title ft-client-section-title-spread">
                    <div class="ft-section-title-left"><span>4</span><div><h3>Shipping addresses</h3><p>Add one or more delivery locations for orders.</p></div></div>
                    <small class="ft-address-count">{{ count($shippingAddresses) }} {{ count($shippingAddresses) === 1 ? 'address' : 'addresses' }}</small>
                </div>

                <div class="ft-shipping-list">
                    @foreach($shippingAddresses as $index => $address)
                        @php
                            $expanded = (bool)($address['expanded'] ?? false);
                            $displayLabel = trim((string)($address['label'] ?? '')) ?: 'Shipping address '.($index + 1);
                            $displayAddress = collect([$address['address_line1'] ?? '', $address['suite'] ?? '', $address['city'] ?? '', $address['state'] ?? '', $address['zip'] ?? '', $address['country'] ?? ''])->filter()->implode(', ');
                        @endphp
                        @if($expanded)
                            <article class="ft-shipping-card is-expanded" wire:key="shipping-expanded-{{ $index }}">
                                <header>
                                    <div class="ft-shipping-card-title"><b>{{ $displayLabel }}</b>@if($address['is_default'] ?? false)<span>Default shipping</span>@endif</div>
                                    <div class="ft-shipping-card-actions"><button type="button" wire:click="duplicateShippingAddress({{ $index }})">⧉&nbsp; Duplicate</button><button type="button" class="icon-only" aria-label="More options">⋮</button><button type="button" class="icon-only" wire:click="toggleShippingAddress({{ $index }})" aria-label="Collapse">⌃</button></div>
                                </header>
                                <div class="ft-client-grid ft-shipping-grid">
                                    <label class="ft-proto-field"><b>Location label <em>*</em></b><input wire:model="shippingAddresses.{{ $index }}.label" placeholder="New York Warehouse">@error("shippingAddresses.$index.label")<small class="validation-error">{{ $message }}</small>@enderror</label>
                                    <label class="ft-proto-field"><b>Recipient <span>(Optional)</span></b><input wire:model="shippingAddresses.{{ $index }}.recipient" placeholder="Receiving Department"></label>
                                    <label class="ft-proto-field ft-address-line"><b>Address line 1 <em>*</em></b><input wire:model="shippingAddresses.{{ $index }}.address_line1" placeholder="450 10th Avenue">@error("shippingAddresses.$index.address_line1")<small class="validation-error">{{ $message }}</small>@enderror</label>
                                    <label class="ft-proto-field"><b>Suite / unit <span>(Optional)</span></b><input wire:model="shippingAddresses.{{ $index }}.suite" placeholder="Dock 4"></label>
                                    <label class="ft-proto-field"><b>City <em>*</em></b><input wire:model="shippingAddresses.{{ $index }}.city" placeholder="New York">@error("shippingAddresses.$index.city")<small class="validation-error">{{ $message }}</small>@enderror</label>
                                    @php($shippingStates = $clientStatesByCountry[$address['country'] ?? ''] ?? [])
                                    <label class="ft-proto-field"><b>State <em>*</em></b><select wire:model="shippingAddresses.{{ $index }}.state" @disabled(empty($shippingStates))><option value="">{{ empty($shippingStates) ? 'No states configured' : 'Select state' }}</option>@foreach($shippingStates as $state)<option value="{{ $state }}">{{ $state }}</option>@endforeach</select>@error("shippingAddresses.$index.state")<small class="validation-error">{{ $message }}</small>@enderror</label>
                                    <label class="ft-proto-field"><b>ZIP code <em>*</em></b><input wire:model="shippingAddresses.{{ $index }}.zip" placeholder="10001">@error("shippingAddresses.$index.zip")<small class="validation-error">{{ $message }}</small>@enderror</label>
                                    <label class="ft-proto-field ft-country-field"><b>Country <em>*</em></b><div class="ft-country-select"><span>{{ $clientCountryFlags[$address['country'] ?? ''] ?? '🌐' }}</span><select wire:model.live="shippingAddresses.{{ $index }}.country"><option value="">Select country</option>@foreach($clientCountries as $country)<option value="{{ $country }}">{{ $country }}</option>@endforeach</select></div>@error("shippingAddresses.$index.country")<small class="validation-error">{{ $message }}</small>@enderror</label>
                                </div>
                                <label class="ft-default-shipping-check"><input type="checkbox" @checked($address['is_default'] ?? false) wire:click="setDefaultShippingAddress({{ $index }})"> <span>Set as default shipping address</span></label>
                            </article>
                        @else
                            <article class="ft-shipping-card is-collapsed" wire:key="shipping-collapsed-{{ $index }}">
                                <div><b>{{ $displayLabel }}</b><p>{{ $displayAddress ?: 'Address details not added yet' }}</p></div>
                                <div class="ft-shipping-collapsed-actions"><button type="button" wire:click="editShippingAddress({{ $index }})">✎&nbsp; Edit</button><button type="button" class="danger" wire:click="removeShippingAddress({{ $index }})">♙&nbsp; Remove</button><button type="button" class="icon-only" wire:click="toggleShippingAddress({{ $index }})">⌄</button></div>
                            </article>
                        @endif
                    @endforeach
                </div>
                <button type="button" class="ft-add-shipping" wire:click="addShippingAddress"><span>＋</span> Add another shipping address</button>
            </section>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>5</span><div><h3>Business &amp; billing preferences</h3></div></div>
                <div class="ft-business-preferences">
                    <label class="ft-proto-field"><b>EIN / Tax ID <span>(Optional)</span></b><input wire:model="einTaxId" placeholder="XX-XXXXXXX"></label>
                    <div class="ft-proto-field"><b>Sales tax status <em>*</em></b><div class="ft-tax-toggle"><button type="button" class="{{ $salesTaxStatus === 'taxable' ? 'active' : '' }}" wire:click="$set('salesTaxStatus','taxable')">Taxable</button><button type="button" class="{{ $salesTaxStatus === 'tax_exempt' ? 'active' : '' }}" wire:click="$set('salesTaxStatus','tax_exempt')">Tax exempt</button></div></div>
                    <label class="ft-proto-field"><b>Payment terms <span>(Optional)</span></b><select wire:model="paymentTerms"><option value="">Select terms</option>@foreach($paymentTermOptions as $term)<option value="{{ $term }}">{{ $term }}</option>@endforeach</select></label>
                </div>
                <div class="ft-po-row"><span>PO required <small>(Optional)</small></span><label class="ft-switch"><input type="checkbox" wire:model="poRequired"><i></i></label></div>
                <p class="ft-tax-certificate-note">Tax exemption certificates can be added from the client profile after creation.</p>
            </section>

            <section class="ft-client-prototype-section ft-notes-section">
                <div class="ft-client-section-title"><span>6</span><div><h3>Internal notes <small>(Optional)</small></h3></div></div>
                <label class="ft-proto-field"><textarea wire:model="notes" placeholder="Commercial preferences, account instructions or internal notes..."></textarea></label>
            </section>

            <footer class="ft-client-prototype-footer">
                <span>
                    @if($errors->any())
                        <em class="validation-error">Please correct the highlighted fields before saving.</em>
                    @else
                        Required fields are marked with&nbsp; <em>*</em>
                    @endif
                </span>
                <div>
                    @if($isEdit)
                        <button type="button" class="ft-create-cancel" wire:click="cancelEditClient">Cancel</button>
                        <button type="button" class="ft-create-primary" wire:click="updateClient" wire:loading.attr="disabled" wire:target="updateClient">Save Client</button>
                    @else
                        <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
                        <button type="button" class="ft-client-save-draft" wire:click="saveClientDraft" wire:loading.attr="disabled" wire:target="saveClientDraft,createClient">Save as draft</button>
                        <button type="button" class="ft-create-primary" wire:click="createClient" wire:loading.attr="disabled" wire:target="saveClientDraft,createClient">Create client</button>
                    @endif
                </div>
            </footer>
        </section>
    </div>
</div>
