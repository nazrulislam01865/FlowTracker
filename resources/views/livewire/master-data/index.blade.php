<div class="ft-master-page" wire:init="loadMasterRecords">
    {{-- Master category navigation now lives in the application sidebar; counts remain available as $groupCounts[$key] ?? 0 for navigation extensions. --}}
    @php
        $hasParent = in_array($group, ['product', 'state'], true);
        $hasColor = in_array($group, \App\Services\MasterDataService::COLOR_TYPES, true);
        $columnCount = 6 + ($hasParent ? 1 : 0) + ($hasColor ? 1 : 0) + ($group === 'inquiry_task_status' ? 2 : 0) + (in_array($group, ['order_task_status', 'order_task_flag'], true) ? 1 : 0) + ($group === 'task_pack_work_calendar' ? 2 : 0);
        $colorUsageLabel = match ($group) {
            'department' => 'department and team performance',
            'task_status' => 'legacy task status',
            'task_flag' => 'legacy task flag',
            'order_task_status' => 'order task status',
            'order_task_flag' => 'order task flag',
            'order_flag' => 'order flag',
            'priority' => 'priority',
            'inquiry_task_status' => 'inquiry task status',
            default => 'master data',
        };
        $permissionModule = \App\Services\MasterDataService::permissionModuleForType($group);
        $canCreateMaster = auth()->user()->canModule($permissionModule, 'create');
        $canEditMaster = auth()->user()->canModule($permissionModule, 'edit');
        $canDeleteMaster = auth()->user()->canModule($permissionModule, 'delete');
        $canCreateProductCategory = auth()->user()->canModule('product_categories', 'create');
        $catalogueGroup = in_array($group, ['product', 'product_category', 'supplier'], true);
        $financialGroup = in_array($group, \App\Services\MasterDataService::FINANCIAL_TYPES, true);
        $taskPackMasterGroup = in_array($group, \App\Services\MasterDataService::TASK_PACK_MASTER_TYPES, true);
        $masterSectionLabel = $catalogueGroup
            ? 'Catalogue'
            : ($financialGroup ? 'Financial Master Data' : ($taskPackMasterGroup ? 'Task Pack Master Data' : 'Master Data'));
        $pageTitle = $labels[$group] ?? 'Master Data';
        $singularLabel = match ($group) {
            'product' => 'product',
            'product_category' => 'category',
            'document_category' => 'document category',
            'production_unit' => 'production unit',
            'shipment_method' => 'shipment method',
            'task_status' => 'legacy task status',
            'inquiry_task_status' => 'inquiry task status',
            'task_flag' => 'legacy task flag',
            'order_task_status' => 'order task status',
            'order_task_flag' => 'order task flag',
            'order_flag' => 'order flag',
            'task_pack_duration_unit' => 'duration unit',
            'task_pack_timer_start' => 'timer start rule',
            'task_pack_timer_stop' => 'timer stop rule',
            'task_pack_work_calendar' => 'work calendar',
            default => strtolower(\Illuminate\Support\Str::singular($pageTitle)),
        };
        $displayTimezone = app(\App\Services\WorkspaceSettingsService::class)->displayTimezone();
        $pageSubtitle = match ($group) {
            'product' => 'Manage the product catalogue used in Inquiries and Orders.',
            'product_category' => 'Manage the categories used to organise products across FlowTrack.',
            'department' => 'Maintain departments used for assignment, routing and task ownership.',
            'supplier' => 'Maintain supplier values available throughout Order processing.',
            'production_unit' => 'Maintain the production units used by workflows and operations.',
            'shipment_method' => 'Maintain shipment methods available for orders and deliveries.',
            'currency' => 'Maintain currencies available for clients, orders, invoices and payments.',
            'received_account' => 'Maintain the receiving accounts available when recording customer payments.',
            'payment_method' => 'Maintain payment methods available when recording payments.',
            'payment_term' => 'Maintain payment terms available for invoices and client finance settings.',
            'invoice_type' => 'Maintain invoice types available when creating invoices.',
            'country' => 'Maintain countries used by client and address records.',
            'state' => 'Maintain states and their parent countries.',
            'phone_country_code' => 'Maintain searchable international phone codes used on shipping and contact forms.',
            'document_category' => 'Maintain document categories used across uploads and workflows.',
            'priority' => 'Maintain priority levels and the colours used throughout FlowTrack.',
            'task_status' => 'Legacy task statuses retained for compatibility. New Order tasks use Order Task Statuses.',
            'inquiry_task_status' => 'Maintain Inquiry task statuses, their automatic Inquiry status mapping, attention flag rule and display colours.',
            'task_flag' => 'Legacy task flags retained for compatibility. New Order task flags are separate.',
            'order_task_status' => 'Maintain Order task statuses and choose which Order Task Flag each status applies automatically.',
            'order_task_flag' => 'Maintain Order task flags and map each one to the Order Flag that should appear on the parent Order.',
            'order_flag' => 'Maintain the separate Order-level flags shown on Order lists and details.',
            'task_pack_duration_unit' => 'Maintain duration units available in the Task Pack time and efficiency prototype.',
            'task_pack_timer_start' => 'Maintain timer-start choices available in the Task Pack time and efficiency prototype.',
            'task_pack_timer_stop' => 'Maintain timer-stop choices available in the Task Pack time and efficiency prototype.',
            'task_pack_work_calendar' => 'Maintain work-calendar choices available in the Task Pack time and efficiency prototype.',
            default => 'Maintain values used throughout FlowTrack.',
        };
        $productImagePreview = null;
        if ($group === 'product' && $productImage) {
            try { $productImagePreview = $productImage->temporaryUrl(); } catch (\Throwable $exception) { $productImagePreview = null; }
        }
        if (!$productImagePreview && !$removeProductImage && $existingProductImageUrl) {
            $productImagePreview = $existingProductImageUrl;
        }
    @endphp

    @if($group === 'product')
        @if($showProductView && $viewProduct)
            <x-catalog.product-view
                :product="$viewProduct"
                :can-edit="$canEditMaster"
                :can-delete="$canDeleteMaster"
                :display-timezone="$displayTimezone"
            />
        @elseif($showModal)
            <x-catalog.product-form
                :edit-product="$editProduct"
                :parents="$productFormCategories"
                :all-product-categories="$parents"
                :main-categories="$productMainCategories"
                :subcategories="$productSubcategories"
                :clients="$productClients"
                :can-create-product-category="$canCreateProductCategory"
                :product-image-preview="$productImagePreview"
                :client-availability-mode="$productClientAvailabilityMode"
                :client-ids="$productClientIds"
                :certificate-upload="$productCertificateUpload"
                :template-upload="$productTemplateUpload"
                :remove-certificate="$removeProductCertificate"
                :remove-template="$removeProductTemplate"
                :category-creator="$categoryCreator"
                :selected-main-category="$productFormMainCategory"
                :selected-product-category-id="$parentId"
                :selected-subcategory="$productSubcategory"
                :price-preview="$productPricePreview"
                :remote-surcharge-preview="$productRemoteSurchargePreview"
                :product-options="$productOptions"
                :product-option-uploads="$productOptionUploads"
                :shipment-urgencies="$availableProductShipmentUrgencies"
                :product-shipment-urgencies="$productShipmentUrgencies"
                :shipment-urgency-picker-open="$productShipmentUrgencyPickerOpen"
                :shipment-urgency-picker-selection="$productShipmentUrgencyPickerSelection"
                :new-product-category-main="$newProductCategoryMain"
                :new-subcategory-product-category-id="$newSubcategoryProductCategoryId"
            />
        @else
        <div class="ft-product-list-head">
            <div>
                <h1>Products</h1>
                <p>Manage the product catalog, client availability and supporting documents.</p>
            </div>
            <div class="ft-product-list-head-actions">
                @if($canCreateMaster)
                    <button type="button" class="ft-product-add-button" wire:click="open">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Add product</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))<div class="flash success ft-master-flash">{{ session('success') }}</div>@endif
        @error('record')<div class="flash error ft-master-flash">{{ $message }}</div>@enderror

        <section class="ft-product-list-shell" x-data="{}">
            <div class="ft-product-filter-card">
                <label class="ft-product-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search product name, product code or reference code" aria-label="Search products">
                </label>

                @php
                    $productMainCategoryListOptions = collect($productMainCategoryFilterOptions)->map(fn ($mainCategory) => [
                        'id' => (string) $mainCategory->name,
                        'label' => (string) $mainCategory->name,
                        'meta' => (string) ($mainCategory->code ?? ''),
                    ])->values();
                    $productCategoryListOptions = collect($productCategories)->map(fn ($category) => [
                        'id' => (string) $category->id,
                        'label' => (string) $category->name,
                        'meta' => (string) ($category->code ?? ''),
                    ])->values();
                    $productClientAvailabilityListOptions = collect([
                        ['id' => 'all', 'label' => 'All clients'],
                        ['id' => 'specific', 'label' => 'Specific clients'],
                    ]);
                    $productStatusListOptions = collect([
                        ['id' => 'active', 'label' => 'Active'],
                        ['id' => 'inactive', 'label' => 'Inactive'],
                    ]);
                @endphp

                <x-ui.select-filter
                    class="ft-product-list-filter"
                    label="Main category"
                    property="productMainCategory"
                    :value="$productMainCategory"
                    placeholder="All main categories"
                    :options="$productMainCategoryListOptions"
                    :hide-label="true"
                    :fixed-menu="true"
                    :menu-width="300"
                    search-placeholder="Search main category…"
                    footer-message="Options shown instantly. Type to search."
                />

                <x-ui.select-filter
                    class="ft-product-list-filter"
                    label="Product category"
                    property="productCategory"
                    :value="$productCategory"
                    placeholder="All product categories"
                    :options="$productCategoryListOptions"
                    :hide-label="true"
                    :fixed-menu="true"
                    :menu-width="300"
                    search-placeholder="Search product category…"
                    footer-message="Options shown instantly. Type to search."
                />

                <x-ui.select-filter
                    class="ft-product-list-filter"
                    label="Client availability"
                    property="productClientAvailability"
                    :value="$productClientAvailability"
                    placeholder="All client availability"
                    :options="$productClientAvailabilityListOptions"
                    :hide-label="true"
                    :fixed-menu="true"
                    :menu-width="280"
                    search-placeholder="Search client availability…"
                />

                <x-ui.select-filter
                    class="ft-product-list-filter"
                    label="Status"
                    property="productStatus"
                    :value="$productStatus"
                    placeholder="All statuses"
                    :options="$productStatusListOptions"
                    :hide-label="true"
                    :fixed-menu="true"
                    :menu-width="240"
                    search-placeholder="Search status…"
                />

                <button type="button" class="ft-product-clear" wire:click="clearProductFilters">Clear</button>
            </div>

            @if(!$recordsReady)
                @include('livewire.shared.table-rows-placeholder', ['columns' => 9, 'rows' => 8])
            @else
                @php
                    $pageProductIds = $rows->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
                    $selectedProductIdSet = collect($selectedProductIds)->map(fn ($id) => (int) $id);
                    $excludedProductIdSet = collect($excludedProductIds)->map(fn ($id) => (int) $id);
                    $allPageProductsSelected = count($pageProductIds) > 0 && collect($pageProductIds)->every(
                        fn ($id) => $selectAllMatchingProducts ? !$excludedProductIdSet->contains($id) : $selectedProductIdSet->contains($id)
                    );
                @endphp

                @if($productSelectionCount > 0)
                    <x-catalog.bulk-actions
                        :count="$productSelectionCount"
                        :matching-total="$rows->total()"
                        :all-matching-selected="$selectAllMatchingProducts && empty($excludedProductIds)"
                        :can-edit="$canEditMaster"
                        :can-delete="$canDeleteMaster"
                    />
                @endif

                <div class="ft-product-table-card" wire:key="product-catalog-{{ $productMainCategory }}-{{ $productCategory }}-{{ $productClientAvailability }}-{{ $productStatus }}-{{ $productPerPage }}">
                    <div class="ft-product-table-scroll">
                        <table class="ft-product-list-table">
                            <thead>
                                <tr>
                                    <th class="ft-product-checkbox-cell">
                                        <input
                                            type="checkbox"
                                            aria-label="Select all products on this page"
                                            @checked($allPageProductsSelected)
                                            x-on:change="$wire.toggleProductPageSelection(@js($pageProductIds), $event.target.checked)"
                                        >
                                    </th>
                                    <th>Product</th>
                                    <th>Product code</th>
                                    <th>Classification</th>
                                    <th>Size</th>
                                    <th>Availability</th>
                                    <th>Documents</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th class="ft-product-actions-heading" aria-label="Actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($rows as $r)
                                @php
                                    $updatedAt = $r->updated_at?->copy()->timezone($displayTimezone);
                                    $updatedLabel = !$updatedAt
                                        ? '—'
                                        : ($updatedAt->isToday() ? $updatedAt->diffForHumans() : ($updatedAt->isYesterday() ? '1 day ago' : $updatedAt->diffForHumans()));
                                    $documents = $r->productDocuments();
                                    $classificationPath = $r->productClassificationPath();
                                    $isProductSelected = $selectAllMatchingProducts
                                        ? !$excludedProductIdSet->contains((int) $r->id)
                                        : $selectedProductIdSet->contains((int) $r->id);
                                @endphp
                                <tr wire:key="product-list-row-{{ $r->id }}" @class(['is-selected' => $isProductSelected])>
                                    <td class="ft-product-checkbox-cell"><input type="checkbox" value="{{ $r->id }}" aria-label="Select {{ $r->name }}" @checked($isProductSelected) wire:change="toggleProductSelection({{ $r->id }})"></td>
                                    <td>
                                        <div class="ft-product-name-cell">
                                            <div class="ft-product-list-thumb">
                                                @if($r->productImageUrl())
                                                    <img src="{{ $r->productImageUrl() }}" alt="{{ $r->name }}">
                                                @else
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                                @endif
                                            </div>
                                            <a
                                                class="ft-product-name-link"
                                                href="{{ route('master-data', ['group' => 'product', 'open' => $r->id]) }}"
                                                wire:navigate
                                                title="Open {{ $r->name }} details"
                                            >{{ $r->name }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ft-product-code-cell">
                                            <strong>{{ $r->productDisplayCode() }}</strong>
                                            <span>Ref: {{ $r->productReferenceCode() ?: '—' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ft-product-classification">
                                            <strong>{{ $r->productMainCategory() ?: '—' }}</strong>
                                            <span>{{ $classificationPath ?: '—' }}</span>
                                        </div>
                                    </td>
                                    <td><x-catalog.product-size :value="$r->productSize()" /></td>
                                    <td><x-catalog.availability :labels="$r->productAvailabilityLabels()" /></td>
                                    <td>
                                        <div class="ft-product-documents">
                                            @if(count($documents))
                                                <div class="ft-product-document-count">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M8.5 13h7M8.5 17h5"/></svg>
                                                    <span>{{ count($documents) }} {{ \Illuminate\Support\Str::plural('file', count($documents)) }}</span>
                                                </div>
                                                <small title="{{ $documents[0]['label'] }}">{{ \Illuminate\Support\Str::limit($documents[0]['label'], 18) }}</small>
                                            @else
                                                <span class="ft-product-documents-empty">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><x-catalog.status :active="$r->status === 'active'" /></td>
                                    <td><span class="ft-product-updated" title="{{ $updatedAt?->format('M j, Y g:i A') }} {{ $displayTimezone }}">{{ $updatedLabel }}</span></td>
                                    <td class="ft-product-actions-cell">
                                        <x-catalog.action-menu
                                            :product-id="$r->id"
                                            :is-active="$r->status === 'active'"
                                            :can-edit="$canEditMaster"
                                            :can-delete="$canDeleteMaster"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10"><div class="empty-state">No products found.</div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @php
                        $lastPage = max(1, $rows->lastPage());
                        $currentPage = $rows->currentPage();
                        $pageStart = max(1, min($currentPage - 1, max(1, $lastPage - 2)));
                        $pageEnd = min($lastPage, $pageStart + 2);
                    @endphp
                    <div class="ft-product-pagination">
                        <div class="ft-product-pagination-left">
                            <div class="ft-product-result-count ft-product-result-count-footer">
                                Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ number_format($rows->total()) }} products
                            </div>
                            <div class="ft-product-rows-per-page">
                                <span>Rows per page</span>
                                <x-catalog.filter-select model="productPerPage" label="Rows per page">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </x-catalog.filter-select>
                            </div>
                        </div>
                        <div class="ft-product-page-position">Page {{ $currentPage }} of {{ $lastPage }}</div>
                        <div class="ft-product-page-buttons">
                            <button type="button" wire:click="previousPage('masterPage')" @disabled($rows->onFirstPage())>Previous</button>
                            @for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++)
                                <button
                                    type="button"
                                    @class(['is-current' => $pageNumber === $currentPage])
                                    wire:click="gotoPage({{ $pageNumber }}, 'masterPage')"
                                    aria-label="Go to page {{ $pageNumber }}"
                                    @if($pageNumber === $currentPage) aria-current="page" @endif
                                >{{ $pageNumber }}</button>
                            @endfor
                            <button type="button" wire:click="nextPage('masterPage')" @disabled(!$rows->hasMorePages())>Next</button>
                        </div>
                    </div>
                </div>

                @if($bulkProductPanel === 'clients')
                    <x-catalog.bulk-modal
                        title="Assign clients"
                        subtitle="Choose who can find and use the selected products."
                        save-label="Assign clients"
                        save-action="applyBulkProductClients"
                    >
                        <div class="ft-product-bulk-radio-row">
                            <label><input type="radio" wire:model.live="bulkProductClientMode" value="all"> <span>All clients</span></label>
                            <label><input type="radio" wire:model.live="bulkProductClientMode" value="specific"> <span>Selected clients</span></label>
                        </div>
                        @if($bulkProductClientMode === 'specific')
                            <div class="ft-product-bulk-client-picker" x-data="{ q: '' }">
                                <label>Available clients</label>
                                <input type="search" x-model="q" placeholder="Search clients…" autocomplete="off">
                                <div class="ft-product-bulk-client-list">
                                    @foreach($productClients as $client)
                                        @php $bulkClientSelected = in_array((int)$client->id, collect($bulkProductClientIds)->map(fn($v)=>(int)$v)->all(), true); @endphp
                                        <button type="button"
                                            x-show="!q || @js(mb_strtolower($client->name.' '.$client->code)).includes(q.toLowerCase())"
                                            wire:click="toggleBulkProductClient({{ $client->id }})"
                                            @class(['is-selected' => $bulkClientSelected])>
                                            <span>{{ $client->name }}</span><small>{{ $client->code }}</small><b>{{ $bulkClientSelected ? '✓' : '' }}</b>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('bulkProductClientIds')<b class="validation-error">{{ $message }}</b>@enderror
                        @else
                            <div class="ft-product-bulk-note">All clients will be able to find and use these products.</div>
                        @endif
                    </x-catalog.bulk-modal>
                @elseif($bulkProductPanel === 'category')
                    <x-catalog.bulk-modal
                        title="Change category"
                        subtitle="Move the selected products to a new category hierarchy."
                        save-label="Change category"
                        save-action="applyBulkProductCategory"
                    >
                        <div class="ft-product-bulk-category-grid">
                            <x-ui.select-filter
                                label="Main category"
                                property="bulkProductMainCategory"
                                :value="$bulkProductMainCategory"
                                placeholder="Select main category"
                                :options="$productMainCategories"
                                :clearable="false"
                                :required="true"
                                :fixed-menu="true"
                                :menu-width="360"
                                search-placeholder="Search main category…"
                            />
                            <x-ui.select-filter
                                label="Product category"
                                property="bulkProductCategoryId"
                                :value="$bulkProductCategoryId"
                                :placeholder="trim($bulkProductMainCategory) === '' ? 'Select main category first' : 'Select product category'"
                                :options="$bulkProductCategories"
                                :disabled="trim($bulkProductMainCategory) === ''"
                                :clearable="false"
                                :required="true"
                                :fixed-menu="true"
                                :menu-width="380"
                                search-placeholder="Search product category…"
                            />
                            <x-ui.select-filter
                                label="Subcategory"
                                property="bulkProductSubcategory"
                                :value="$bulkProductSubcategory"
                                :placeholder="$bulkProductCategoryId ? 'No subcategory' : 'Select product category first'"
                                :options="$bulkProductSubcategories"
                                :disabled="!$bulkProductCategoryId"
                                :clearable="true"
                                :optional="true"
                                :fixed-menu="true"
                                :menu-width="380"
                                search-placeholder="Search subcategory…"
                            />
                        </div>
                        @error('bulkProductMainCategory')<b class="validation-error">{{ $message }}</b>@enderror
                        @error('bulkProductCategoryId')<b class="validation-error">{{ $message }}</b>@enderror
                    </x-catalog.bulk-modal>
                @endif
            @endif
        </section>

        @endif
    @elseif($group === 'product_category')
        @if(!$recordsReady)
            @include('livewire.shared.table-rows-placeholder', ['columns' => 9, 'rows' => 8])
        @else
            <x-catalog.category-list
                :main-page="$categoryMainPage"
                :product-children="$categoryProductChildren"
                :subcategory-children="$categorySubcategoryChildren"
                :main-categories="$categoryMainCategories"
                :product-categories="$categoryProductCategories"
                :parent-options="$categoryParentOptions"
                :counts="$categoryCounts"
                :product-counts="$categoryProductCounts"
                :main-product-counts="$categoryMainProductCounts"
                :subcategory-product-counts="$categorySubcategoryProductCounts"
                :product-child-totals="$categoryProductChildTotals"
                :subcategory-child-totals="$categorySubcategoryChildTotals"
                :expanded-main-ids="$expandedMainCategoryIds"
                :expanded-product-ids="$expandedProductCategoryIds"
                :can-create="$canCreateMaster"
                :can-edit="$canEditMaster"
                :can-delete="$canDeleteMaster"
                :display-timezone="$displayTimezone"
                :search="$search"
                :level-filter="$categoryLevelFilter"
                :parent-filter="$categoryParentFilter"
                :status-filter="$categoryStatusFilter"
                :per-page="$categoryPerPage"
                :selected-category-keys="$selectedCategoryKeys"
                :selection-count="$categorySelectionCount"
            />

            @if($showCategoryDeleteConfirm)
                <x-catalog.category-delete-modal :preview="$categoryDeletePreview" />
            @endif
        @endif
    @else
        <div class="ft-master-breadcrumb" aria-label="Breadcrumb">
            <span>{{ $masterSectionLabel }}</span><i>/</i><strong>{{ $pageTitle }}</strong>
        </div>

        <div class="ft-master-page-head">
            <div>
                <h1>{{ $pageTitle }}</h1>
                <p>{{ $pageSubtitle }}</p>
            </div>
            @if($canCreateMaster)
                <button type="button" class="primary ft-master-add-button" wire:click="open">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Add {{ $singularLabel }}</span>
                </button>
            @endif
        </div>

        @if(session('success'))<div class="flash success ft-master-flash">{{ session('success') }}</div>@endif
        @error('record')<div class="flash error ft-master-flash">{{ $message }}</div>@enderror

        <div class="ft-master-single-stat ft-master-generic-stat">
            <div class="ft-master-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v14H5zM8 9h8M8 13h8M8 17h5"/></svg>
            </div>
            <div class="ft-master-stat-copy">
                <span>Total {{ strtolower($pageTitle) }}</span>
                <strong>{{ number_format($selectedTotal) }}</strong>
            </div>
            <small>{{ number_format($selectedActive) }} active</small>
        </div>

        <section @class(['ft-master-generic-card', 'ft-master-supplier-card' => $group === 'supplier'])>
            <div class="ft-master-generic-toolbar">
                <label class="ft-master-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search {{ strtolower($pageTitle) }}..." aria-label="Search {{ strtolower($pageTitle) }}">
                </label>
            </div>

            <div class="ft-master-product-count">
                @if($recordsReady && $rows)
                    Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ number_format($rows->total()) }} records
                @else
                    Loading records…
                @endif
            </div>

            @if(!$recordsReady)
                @include('livewire.shared.table-rows-placeholder', ['columns' => $columnCount, 'rows' => 8])
            @else
                <div class="table-wrap ft-master-generic-table-wrap" wire:key="master-records-{{ $group }}">
                    <table @class(['master-table', 'ft-master-generic-table', 'ft-master-supplier-table' => $group === 'supplier'])>
                        <thead>
                            <tr>
                                <th>Sort order</th>
                                <th>Code</th>
                                <th>{{ $group === 'phone_country_code' ? 'Phone code' : 'Name' }}</th>
                                @if($group === 'task_pack_work_calendar')<th>Days</th><th>Working hours</th>@endif
                                @if($group === 'inquiry_task_status')<th>Inquiry status auto</th><th>Flag</th>@endif
                                @if($group === 'order_task_status')<th>Automatic task flag</th>@endif
                                @if($group === 'order_task_flag')<th>Order flag</th>@endif
                                @if($hasParent)<th>{{ $group === 'state' ? 'Country' : 'Product Category' }}</th>@endif
                                <th>Description / Use</th>
                                @if($hasColor)<th>Color</th>@endif
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $r)
                            <tr>
                                <td class="ft-master-mobile-sort" data-label="Sort order">{{ $r->sort_order }}</td>
                                <td class="ft-master-mobile-code" data-label="Code"><strong class="ft-master-product-code">{{ $r->code }}</strong></td>
                                <td class="ft-master-mobile-name" data-label="{{ $group === 'phone_country_code' ? 'Phone code' : 'Name' }}">{{ $r->name }}</td>
                                @if($group === 'task_pack_work_calendar')
                                    <td data-label="Days"><strong class="ft-work-calendar-table-value">{{ $r->taskPackWorkCalendarDayRange() }}</strong></td>
                                    <td data-label="Working hours"><strong class="ft-work-calendar-table-value">{{ $r->taskPackWorkCalendarTimeRange() }}</strong></td>
                                @endif
                                @if($group === 'inquiry_task_status')
                                    <td class="ft-master-mobile-auto-status" data-label="Inquiry status auto"><strong>{{ $r->inquiryAutoStatus() }}</strong></td>
                                    <td class="ft-master-mobile-flag" data-label="Flag">
                                        @if($r->requiresAttention())
                                            <span class="ft-inquiry-status-rule-flag is-attention">Requires attention</span>
                                        @else
                                            <span class="ft-inquiry-status-rule-flag">Not needed</span>
                                        @endif
                                    </td>
                                @endif
                                @if($group === 'order_task_status')
                                    @php $mappedTaskFlag = $orderTaskFlagOptions->firstWhere('id', $r->orderTaskFlagId()); @endphp
                                    <td class="ft-master-mobile-flag" data-label="Automatic task flag">
                                        @if($mappedTaskFlag)
                                            <span class="ft-inquiry-status-rule-flag is-attention" style="{{ \App\Support\MasterColor::style($mappedTaskFlag->color) }}">{{ $mappedTaskFlag->name }}</span>
                                        @else
                                            <span class="ft-inquiry-status-rule-flag">No flag</span>
                                        @endif
                                    </td>
                                @endif
                                @if($group === 'order_task_flag')
                                    @php $mappedOrderFlag = $orderFlagOptions->firstWhere('id', $r->orderFlagId()); @endphp
                                    <td class="ft-master-mobile-flag" data-label="Order flag">
                                        <strong>{{ $mappedOrderFlag?->name ?? 'Not mapped' }}</strong>
                                    </td>
                                @endif
                                @if($hasParent)<td class="ft-master-mobile-parent" data-label="{{ $group === 'state' ? 'Country' : 'Product Category' }}">{{ $r->parent?->name ?? '—' }}</td>@endif
                                <td class="ft-master-mobile-description" data-label="Description / Use">{{ $r->description ?: '—' }}</td>
                                @if($hasColor)
                                    @php
                                        $rowColor = \App\Support\MasterColor::normalize($r->color) ?: \App\Support\MasterColor::defaultFor($group, $r->name);
                                    @endphp
                                    <td class="ft-master-mobile-color" data-label="Color">
                                        <label class="ft-master-color-chip" style="{{ \App\Support\MasterColor::style($rowColor) }}" title="Choose color for {{ $r->name }}">
                                            <input
                                                class="ft-master-inline-color"
                                                type="color"
                                                value="{{ $rowColor }}"
                                                wire:change="updateColor({{ $r->id }}, $event.target.value)"
                                                wire:loading.attr="disabled"
                                                @disabled(!$canEditMaster)
                                                aria-label="Choose color for {{ $r->name }}"
                                            >
                                            <span>{{ $rowColor }}</span>
                                        </label>
                                    </td>
                                @endif
                                <td class="ft-master-mobile-status" data-label="Status"><x-ui.badge :label="$r->status === 'active' ? 'Active' : 'Inactive'" /></td>
                                <td class="ft-master-mobile-actions" data-label="Actions">
                                    <div class="row-actions">
                                        @if($canEditMaster)
                                            <button class="mini-btn" wire:click="open({{ $r->id }})">Edit</button>
                                            <button class="mini-btn" wire:click="toggle({{ $r->id }})">{{ $r->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                                        @endif
                                        @if($canDeleteMaster)
                                            <button class="mini-btn" wire:click="deleteRecord({{ $r->id }})" wire:confirm="Delete this master record?">Delete</button>
                                        @endif
                                        @if(!$canEditMaster && !$canDeleteMaster)
                                            <span class="small muted">View only</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="ft-master-empty-row"><td colspan="{{ $columnCount }}"><div class="empty-state">No records found.</div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rows->total() > 30)
                    <div class="ft-list-pagination ft-master-pagination">
                        <span>Showing <b>{{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }}</b> of {{ $rows->total() }} records</span>
                        <div class="ft-page-actions">
                            <button type="button" wire:click="previousPage('masterPage')" @disabled($rows->onFirstPage())>Previous</button>
                            <span>Page {{ $rows->currentPage() }} of {{ $rows->lastPage() }}</span>
                            <button type="button" wire:click="nextPage('masterPage')" @disabled(!$rows->hasMorePages())>Next</button>
                        </div>
                    </div>
                @endif
            @endif
        </section>
    @endif

    @if($showModal && !in_array($group, ['product', 'product_category'], true))
        <div class="overlay livewire-overlay" wire:click.self="close"></div>
        <div class="modal livewire-modal ft-master-modal">
            <div class="modal-head">
                <div>
                    <h2>{{ $editId ? 'Edit' : 'Add' }} {{ ucfirst($singularLabel) }}</h2>
                    <p>{{ $editId ? 'Update this master data record.' : 'Create a new '.$singularLabel.' for FlowTrack.' }}</p>
                </div>
                <button class="close-btn" wire:click="close" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Code</label>
                        <div class="ft-admin-locked">{{ $code }}</div>
                        <small class="small muted">{{ $editId ? 'System code is permanently locked.' : 'Automatically generated and permanently locked.' }}</small>
                        @error('code')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>{{ $group === 'phone_country_code' ? 'Phone code *' : ($group === 'task_pack_work_calendar' ? 'Calendar name *' : 'Name *') }}</label>
                        <input wire:model="name" @if($group === 'phone_country_code') placeholder="e.g. +880" inputmode="tel" @elseif($group === 'task_pack_work_calendar') placeholder="e.g. Workspace hours" @endif>
                        @error('name')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>

                    @if($group === 'task_pack_work_calendar')
                        @php
                            $workCalendarDays = [
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ];
                        @endphp
                        <section class="field full ft-work-calendar-editor" aria-label="Work calendar schedule">
                            <div class="ft-work-calendar-editor-head">
                                <div class="ft-work-calendar-editor-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                                </div>
                                <div>
                                    <strong>Working schedule</strong>
                                    <small>Set the working-day range and daily working hours used by this calendar.</small>
                                </div>
                            </div>

                            <div class="ft-work-calendar-grid">
                                <div class="ft-work-calendar-field">
                                    <label>Day from *</label>
                                    <select wire:model.live="workCalendarDayFrom">
                                        @foreach($workCalendarDays as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                                    </select>
                                    @error('workCalendarDayFrom')<div class="validation-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="ft-work-calendar-field">
                                    <label>Day to *</label>
                                    <select wire:model.live="workCalendarDayTo">
                                        @foreach($workCalendarDays as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                                    </select>
                                    @error('workCalendarDayTo')<div class="validation-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="ft-work-calendar-field">
                                    <label>Time from *</label>
                                    <input type="time" wire:model.live="workCalendarTimeFrom">
                                    @error('workCalendarTimeFrom')<div class="validation-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="ft-work-calendar-field">
                                    <label>Time to *</label>
                                    <input type="time" wire:model.live="workCalendarTimeTo">
                                    @error('workCalendarTimeTo')<div class="validation-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="ft-work-calendar-preview">
                                <span>Calendar preview</span>
                                <strong>{{ $workCalendarDays[$workCalendarDayFrom] ?? ucfirst($workCalendarDayFrom) }} → {{ $workCalendarDays[$workCalendarDayTo] ?? ucfirst($workCalendarDayTo) }}</strong>
                                <i></i>
                                <strong>{{ $workCalendarTimeFrom ?: '—' }} → {{ $workCalendarTimeTo ?: '—' }}</strong>
                            </div>
                        </section>
                    @endif

                    @if($group === 'inquiry_task_status')
                        <div class="field">
                            <label>Inquiry status auto *</label>
                            <select wire:model="autoInquiryStatus">
                                <option value="To do">To do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="__task_status__">Use task status name</option>
                            </select>
                            <small class="small muted">This value automatically becomes the parent Inquiry status while this task is current.</small>
                            @error('autoInquiryStatus')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Attention flag</label>
                            <select wire:model.boolean="requiresAttention">
                                <option value="0">Not needed</option>
                                <option value="1">Requires attention</option>
                            </select>
                            <small class="small muted">When enabled, the task shows an Attention required link and asks for a reason.</small>
                            @error('requiresAttention')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if($group === 'order_task_status')
                        <div class="field">
                            <label>Automatic Order Task Flag</label>
                            <select wire:model="orderTaskFlagId">
                                <option value="">No flag</option>
                                @foreach($orderTaskFlagOptions as $flag)
                                    <option value="{{ $flag->id }}">{{ $flag->name }}</option>
                                @endforeach
                            </select>
                            <small class="small muted">When a task uses this status, this flag is applied automatically. An overdue due date overrides this mapping with the system Overdue flag.</small>
                            @error('orderTaskFlagId')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if($group === 'order_task_flag')
                        <div class="field">
                            <label>Parent Order Flag *</label>
                            <select wire:model="orderFlagId">
                                <option value="">Select order flag</option>
                                @foreach($orderFlagOptions as $flag)
                                    <option value="{{ $flag->id }}">{{ $flag->name }}</option>
                                @endforeach
                            </select>
                            <small class="small muted">When this task flag is active, the mapped Order Flag is stored on the parent Order.</small>
                            @error('orderFlagId')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if($group === 'product')
                        <div class="field full ft-master-product-image-field">
                            <label>Product image</label>
                            <div class="ft-master-product-image-editor">
                                <div class="ft-master-product-image-preview">
                                    @if($productImagePreview)
                                        <img src="{{ $productImagePreview }}" alt="Product image preview">
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                    @endif
                                </div>
                                <div class="ft-master-product-image-actions">
                                    <label class="ft-master-file-button">
                                        <input type="file" wire:model="productImage" accept="image/png,image/jpeg,image/webp">
                                        <span wire:loading.remove wire:target="productImage">{{ $productImagePreview ? 'Replace image' : 'Upload image' }}</span><span wire:loading wire:target="productImage">Preparing…</span>
                                    </label>
                                    @if($existingProductImageUrl && !$removeProductImage && !$productImage)
                                        <button type="button" class="ft-master-remove-image" wire:click="markProductImageForRemoval">Remove</button>
                                    @elseif($removeProductImage)
                                        <button type="button" class="ft-master-remove-image" wire:click="restoreProductImage">Undo remove</button>
                                    @endif
                                    <small>PNG, JPG or WEBP up to 5 MB.</small>
                                </div>
                            </div>
                            @error('productImage')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if($group === 'product')
                        <div class="field">
                            <label>Product category</label>
                            <select wire:model="parentId">
                                <option value="">No category</option>
                                @foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                            </select>
                            @error('parentId')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @elseif($group === 'state')
                        <div class="field">
                            <label>Country *</label>
                            <select wire:model="parentId">
                                <option value="">Select country</option>
                                @foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                            </select>
                            @error('parentId')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    <div class="field">
                        <label>Status</label>
                        <select wire:model="status"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                    <div class="field">
                        <label>Sort order</label>
                        <input type="number" min="0" wire:model="sortOrder">
                    </div>

                    @if($hasColor)
                        <div class="field full">
                            <label>Color *</label>
                            <div class="ft-master-color-picker-row" style="{{ \App\Support\MasterColor::style($color) }}">
                                <input class="ft-master-color-picker" type="color" wire:model.live="color" aria-label="Choose {{ $labels[$group] }} color">
                                <input type="text" maxlength="7" wire:model.blur="color" placeholder="#2563EB" aria-label="Hex color code">
                                <span class="ft-master-color-preview"><i class="ft-master-color-dot"></i><span>This color will be used for {{ $colorUsageLabel }} labels across FlowTrack.</span></span>
                            </div>
                            @error('color')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    <div class="field full">
                        <label>{{ $group === 'phone_country_code' ? 'Country / label' : 'Description' }}</label>
                        <textarea wire:model="description" rows="3" @if($group === 'phone_country_code') placeholder="e.g. Bangladesh" @endif></textarea>
                        @error('description')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="ghost" wire:click="close">Cancel</button>
                <button class="primary" wire:click="save" wire:loading.attr="disabled" wire:target="save,productImage">Save {{ ucfirst($singularLabel) }}</button>
            </div>
        </div>
    @endif

    @if($group === 'product_category' && $categoryEditorLevel)
        <x-catalog.category-editor
            :level="$categoryEditorLevel"
            :editing="(bool) $categoryEditorId"
            :read-only="$categoryEditorReadOnly"
            :main-categories="$categoryMainCategories"
            :product-categories="$categoryProductCategories"
            :selected-parent-id="$categoryEditorParentId"
            :name-value="$categoryEditorName"
            :description-value="$categoryEditorDescription"
        />
    @endif
</div>
