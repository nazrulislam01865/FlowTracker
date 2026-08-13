@props([
    'editProduct' => null,
    'parents' => collect(),
    'allProductCategories' => collect(),
    'mainCategories' => collect(),
    'subcategories' => collect(),
    'clients' => collect(),
    'canCreateProductCategory' => false,
    'productImagePreview' => null,
    'clientAvailabilityMode' => 'all',
    'clientIds' => [],
    'certificateUpload' => null,
    'templateUpload' => null,
    'removeCertificate' => false,
    'removeTemplate' => false,
    'categoryCreator' => null,
    'selectedMainCategory' => '',
    'selectedProductCategoryId' => null,
    'selectedSubcategory' => '',
    'newProductCategoryMain' => '',
    'newSubcategoryProductCategoryId' => null,
])
@php
    $isEdit = (bool) $editProduct;
    $displayCode = $editProduct?->productDisplayCode() ?? 'Generated after creation';
    $selectedClients = collect($clients)->whereIn('id', collect($clientIds)->map(fn($v)=>(int)$v)->all());
    $existingDocs = collect($editProduct?->productDocuments() ?? []);
    $certificateDoc = $removeCertificate ? null : $existingDocs->firstWhere('kind', 'certificate');
    $templateDoc = $removeTemplate ? null : $existingDocs->firstWhere('kind', 'template');
    $productCategoryOptions = collect($parents)->map(fn($category) => [
        'id' => $category->id,
        'label' => $category->name,
        'meta' => $category->code,
    ]);
@endphp
<div class="ft-product-page ft-product-form-page" x-data="{clientOpen:false, clientSearch:'', dragging:false}">
    <div class="ft-product-page-breadcrumb"><button type="button" wire:click="close">Products</button><span>/</span><strong>{{ $isEdit ? 'Edit product' : 'Create product' }}</strong></div>
    <header class="ft-product-form-header">
        <div><h1>{{ $isEdit ? 'Edit product' : 'Create product' }}</h1><p>{{ $isEdit ? 'Update the product information, availability and supporting documents.' : 'Add a product and link its category, image and supporting documents.' }}</p></div>
        @if($isEdit)
            <div class="ft-product-form-top-actions"><button type="button" class="ft-product-page-btn is-secondary" wire:click="close">Cancel</button><button type="button" class="ft-product-page-btn is-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save,productImage,productCertificateUpload,productTemplateUpload">Save changes</button></div>
        @endif
    </header>

    <div class="ft-product-form-shell">
        <x-catalog.product-section number="1" title="Product information">
            <div class="ft-product-form-info-grid">
                <div class="ft-product-form-fields">
                    <div class="ft-form-grid ft-form-grid-3">
                        <label class="ft-product-field"><span>Product code</span><div class="ft-product-locked-field">{{ $displayCode }} <span>⌑</span></div><small>Generated automatically after the product is created.</small></label>
                        <label class="ft-product-field"><span>Reference product code <em>Optional</em></span><input wire:model.blur="productReferenceCode" placeholder="Client or supplier reference"><small>Client or supplier reference used for search and matching.</small>@error('productReferenceCode')<b class="validation-error">{{ $message }}</b>@enderror</label>
                        <label class="ft-product-field"><span>Product name <i>*</i></span><input wire:model.blur="name" placeholder="Enter product name">@error('name')<b class="validation-error">{{ $message }}</b>@enderror</label>
                    </div>
                    <label class="ft-product-field ft-product-size-field"><span>Product size</span><textarea wire:model.blur="productSize" rows="4" placeholder='Add size/specification details. Use a new line for each item, e.g. width, finished length, material, capacity or dimensions.'></textarea><small>Enter multiple size/specification details on separate lines so the information stays easy to read.</small>@error('productSize')<b class="validation-error">{{ $message }}</b>@enderror</label>
                    <div class="ft-product-client-scope">
                        <span class="ft-product-field-title">Client availability</span>
                        <div class="ft-product-radio-row"><label><input type="radio" value="all" wire:model.live="productClientAvailabilityMode"> All clients</label><label><input type="radio" value="specific" wire:model.live="productClientAvailabilityMode"> Selected clients</label></div>
                        @if($clientAvailabilityMode === 'specific')
                            <div class="ft-product-client-picker ft-product-assignee-style-picker" x-on:click.outside="clientOpen=false" x-on:keydown.escape.window="clientOpen=false">
                                <div role="button" tabindex="0" class="ft-product-client-input ft-product-client-trigger" x-on:click="clientOpen=!clientOpen" x-on:keydown.enter.prevent="clientOpen=!clientOpen" x-on:keydown.space.prevent="clientOpen=!clientOpen" :aria-expanded="clientOpen.toString()" aria-haspopup="listbox">
                                    <span class="ft-product-client-trigger-copy">
                                        @forelse($selectedClients as $client)
                                            <span class="ft-product-client-chip">{{ $client->name }} <button type="button" wire:click.stop="toggleProductClient({{ $client->id }})" aria-label="Remove {{ $client->name }}">×</button></span>
                                        @empty
                                            <span class="ft-product-client-placeholder">Select clients</span>
                                        @endforelse
                                    </span>
                                    <b class="ft-filter-chevron">⌄</b>
                                </div>
                                <div class="ft-product-client-menu ft-remote-filter-menu" x-cloak x-show="clientOpen">
                                    <input class="ft-remote-filter-search" type="text" role="searchbox" inputmode="search" x-model="clientSearch" placeholder="Search clients…" autocomplete="off">
                                    <div class="ft-remote-filter-list" role="listbox" aria-multiselectable="true">
                                        @foreach($clients as $client)
                                            @php $clientSelected = in_array((int)$client->id, collect($clientIds)->map(fn($v)=>(int)$v)->all(), true); @endphp
                                            <button type="button" class="ft-remote-filter-option" :aria-selected="@js($clientSelected).toString()" x-show="!clientSearch || @js(mb_strtolower($client->name.' '.$client->code)).includes(clientSearch.toLowerCase())" wire:click="toggleProductClient({{ $client->id }})">
                                                <span>{{ $client->name }}</span><small>{{ $client->code }}{{ $clientSelected ? ' · Selected' : '' }}</small>
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="ft-remote-filter-message">Search by client name or code. Multiple clients can be selected.</div>
                                </div>
                            </div>
                            <small class="ft-product-help">Only selected clients can find and use this product.</small>
                            <button type="button" class="ft-product-inline-link" wire:click="selectAllProductClients">Select all clients</button>
                            @error('productClientIds')<b class="validation-error">{{ $message }}</b>@enderror
                        @endif
                    </div>
                </div>
                <div class="ft-product-image-column">
                    <span class="ft-product-field-title">Product image <em>Optional</em></span>
                    <div class="ft-product-image-drop" :class="dragging ? 'is-dragging':''" x-on:dragover.prevent="dragging=true" x-on:dragleave.prevent="dragging=false" x-on:drop.prevent="dragging=false;if($event.dataTransfer.files.length){const dt=new DataTransfer();dt.items.add($event.dataTransfer.files[0]);$refs.productImage.files=dt.files;$refs.productImage.dispatchEvent(new Event('change',{bubbles:true}))}" x-on:click="$refs.productImage.click()">
                        <input x-ref="productImage" type="file" wire:model="productImage" accept="image/png,image/jpeg,image/webp">
                        @if($productImagePreview)<img src="{{ $productImagePreview }}" alt="Product preview">@else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v14H4z"/><path d="m7 16 3.5-4 3 3 2-2 2.5 3"/></svg>@endif
                        <strong>Drop image or <span>browse</span></strong>
                    </div>
                    <small>PNG, JPG or WEBP · Max 5 MB</small>@error('productImage')<b class="validation-error">{{ $message }}</b>@enderror
                </div>
            </div>
        </x-catalog.product-section>

        <x-catalog.product-section number="2" title="Category hierarchy" subtitle="Select from top to bottom. Each list is filtered by the selection above.">
            <div class="ft-form-grid ft-form-grid-3 ft-category-grid">
                <div class="ft-product-search-select-wrap">
                    <x-ui.select-filter
                        class="ft-product-search-select"
                        wire:key="product-main-category-filter-{{ $isEdit ? 'edit' : 'create' }}"
                        label="Main category"
                        property="productFormMainCategory"
                        action="setProductTaxonomySelection"
                        :value="$selectedMainCategory"
                        placeholder="Select main category"
                        :options="$mainCategories"
                        :clearable="false"
                        :required="true"
                        :fixed-menu="true"
                        :menu-width="360"
                        search-placeholder="Search main category…"
                        footer-message="Type to search the available main categories."
                    />
                    @if($canCreateProductCategory)<button type="button" class="ft-product-inline-link" wire:click="openCategoryCreator('main')">+ Create main category</button>@endif
                    @error('productFormMainCategory')<b class="validation-error">{{ $message }}</b>@enderror
                </div>

                <div class="ft-product-search-select-wrap">
                    <x-ui.select-filter
                        class="ft-product-search-select"
                        wire:key="product-category-filter-{{ $isEdit ? 'edit' : 'create' }}-{{ md5((string) $selectedMainCategory) }}"
                        label="Product category"
                        property="parentId"
                        action="setProductTaxonomySelection"
                        :value="$selectedProductCategoryId"
                        :placeholder="trim((string)$selectedMainCategory) === '' ? 'Select main category first' : 'Select product category'"
                        :options="$productCategoryOptions"
                        :disabled="trim((string)$selectedMainCategory) === ''"
                        :clearable="false"
                        :required="true"
                        :fixed-menu="true"
                        :menu-width="380"
                        search-placeholder="Search product category…"
                        footer-message="Type to search product categories in the selected main category."
                    />
                    @if($canCreateProductCategory)<button type="button" class="ft-product-inline-link" wire:click="openCategoryCreator('product')">+ Create product category</button>@endif
                    @error('parentId')<b class="validation-error">{{ $message }}</b>@enderror
                </div>

                <div class="ft-product-search-select-wrap">
                    <x-ui.select-filter
                        class="ft-product-search-select"
                        wire:key="product-subcategory-filter-{{ $isEdit ? 'edit' : 'create' }}-{{ (int) ($selectedProductCategoryId ?? 0) }}"
                        label="Subcategory"
                        property="productSubcategory"
                        action="setProductTaxonomySelection"
                        :value="$selectedSubcategory"
                        :placeholder="$selectedProductCategoryId ? 'No subcategory' : 'Select product category first'"
                        :options="$subcategories"
                        :disabled="!$selectedProductCategoryId"
                        :clearable="true"
                        :optional="true"
                        :fixed-menu="true"
                        :menu-width="380"
                        search-placeholder="Search subcategory…"
                        footer-message="Subcategory is optional. Type to search available options."
                    />
                    @if($canCreateProductCategory)<button type="button" class="ft-product-inline-link" wire:click="openCategoryCreator('sub')">+ Create subcategory</button>@endif
                    @error('productSubcategory')<b class="validation-error">{{ $message }}</b>@enderror
                </div>
            </div>
            <small class="ft-product-help">Missing a category? Create it here without leaving the product form. Codes are generated automatically.</small>
        </x-catalog.product-section>

        <x-catalog.product-section number="3" title="Supporting documents" subtitle="Add the product files now or replace them later while editing.">
            <div class="ft-product-support-grid is-friendly">
                <label class="ft-product-field ft-certificate-number-field"><span>Test certificate number <em>Optional</em></span><input wire:model.blur="productTestCertificateNumber" placeholder="T-26423684-06-R1"><small>Reference number printed on the test certificate.</small>@error('productTestCertificateNumber')<b class="validation-error">{{ $message }}</b>@enderror</label>
                <x-catalog.file-upload
                    model="productCertificateUpload"
                    label="Certificate & Test Report"
                    hint="PDF or DOCX · Max 10 MB"
                    accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    :upload="$certificateUpload"
                    :current="$certificateDoc"
                    clear-action="clearProductCertificateUpload"
                    remove-current-action="removeProductCertificate"
                />
                <x-catalog.file-upload
                    model="productTemplateUpload"
                    label="Product template"
                    hint="PDF, AI or EPS · Max 10 MB"
                    accept=".pdf,.ai,.eps,application/pdf,application/postscript"
                    :upload="$templateUpload"
                    :current="$templateDoc"
                    clear-action="clearProductTemplateUpload"
                    remove-current-action="removeProductTemplate"
                />
            </div>
            <div class="ft-product-document-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/></svg>
                <span>These documents stay linked to this product and are available when it is added to an Inquiry or Order.</span>
            </div>
        </x-catalog.product-section>

        <footer class="ft-product-form-footer"><span>Required fields are marked <i>*</i></span><div><button type="button" class="ft-product-page-btn is-secondary" wire:click="close">Cancel</button>@if(!$isEdit)<button type="button" class="ft-product-page-btn is-secondary" wire:click="saveProductDraft" wire:loading.attr="disabled">Save as draft</button>@endif<button type="button" class="ft-product-page-btn is-primary" wire:click="save" wire:loading.attr="disabled">{{ $isEdit ? 'Save changes' : 'Create product' }}</button></div></footer>
    </div>

    @if($categoryCreator)
        <x-catalog.category-creator
            :level="$categoryCreator"
            :main-categories="$mainCategories"
            :product-categories="$allProductCategories"
            :selected-main-category="$newProductCategoryMain"
            :selected-product-category-id="$newSubcategoryProductCategoryId"
        />
    @endif
</div>
