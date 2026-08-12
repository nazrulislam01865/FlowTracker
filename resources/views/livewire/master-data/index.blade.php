<div class="ft-master-page" wire:init="loadMasterRecords">
    {{-- Master category navigation now lives in the application sidebar; counts remain available as $groupCounts[$key] ?? 0 for navigation extensions. --}}
    @php
        $hasParent = in_array($group, ['product', 'state'], true);
        $hasColor = in_array($group, \App\Services\MasterDataService::COLOR_TYPES, true);
        $columnCount = 6 + ($hasParent ? 1 : 0) + ($hasColor ? 1 : 0);
        $colorUsageLabel = match ($group) {
            'task_status' => 'task status',
            'task_flag' => 'task flag',
            'priority' => 'priority',
            'inquiry_status' => 'inquiry status',
            default => 'master data',
        };
        $canCreateMaster = auth()->user()->canModule('masterdata', 'create');
        $canEditMaster = auth()->user()->canModule('masterdata', 'edit');
        $canDeleteMaster = auth()->user()->canModule('masterdata', 'delete');
        $pageTitle = $labels[$group] ?? 'Master Data';
        $singularLabel = match ($group) {
            'product' => 'product',
            'product_category' => 'category',
            'document_category' => 'document category',
            'production_unit' => 'production unit',
            'shipment_method' => 'shipment method',
            'task_status' => 'task status',
            'inquiry_status' => 'inquiry status',
            'task_flag' => 'task flag',
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
            'currency' => 'Maintain currencies available for clients, orders and commercial data.',
            'country' => 'Maintain countries used by client and address records.',
            'state' => 'Maintain states and their parent countries.',
            'document_category' => 'Maintain document categories used across uploads and workflows.',
            'priority' => 'Maintain priority levels and the colours used throughout FlowTrack.',
            'task_status' => 'Maintain task statuses and the colours used throughout task views.',
            'inquiry_status' => 'Maintain Inquiry statuses and their display colours.',
            'task_flag' => 'Maintain task attention flags and their display colours.',
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

    <div class="ft-master-breadcrumb" aria-label="Breadcrumb">
        <span>Master Data</span><i>/</i><strong>{{ $pageTitle }}</strong>
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

    @if($group === 'product')
        <div class="ft-master-single-stat">
            <div class="ft-master-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.3"/></svg>
            </div>
            <div class="ft-master-stat-copy">
                <span>Total products</span>
                <strong>{{ number_format($selectedTotal) }}</strong>
            </div>
            <small>{{ number_format($selectedActive) }} active</small>
        </div>

        <section
            class="ft-master-product-card"
            x-data="{
                columnsOpen: false,
                visible: { image: true, code: true, category: true, name: true, createdBy: true, createdAt: true, status: true, updated: true }
            }"
        >
            <div class="ft-master-product-toolbar">
                <label class="ft-master-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search by product name, SKU or code..." aria-label="Search products">
                </label>

                <select wire:model.live="productCategory" aria-label="Filter products by category">
                    <option value="">All categories</option>
                    @foreach($productCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="productStatus" aria-label="Filter products by status">
                    <option value="">All status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <button type="button" class="ft-master-clear" wire:click="clearProductFilters">Clear</button>

                <div class="ft-master-columns" x-on:click.outside="columnsOpen = false">
                    <button type="button" class="ft-master-columns-button" x-on:click="columnsOpen = !columnsOpen" :aria-expanded="columnsOpen.toString()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v14H4zM9 5v14M15 5v14"/></svg>
                        <span>Columns</span>
                    </button>
                    <div class="ft-master-columns-menu" x-cloak x-show="columnsOpen" x-transition.origin.top.right>
                        <label><input type="checkbox" x-model="visible.image"> Image</label>
                        <label><input type="checkbox" x-model="visible.code"> SKU / Code</label>
                        <label><input type="checkbox" x-model="visible.category"> Product category</label>
                        <label><input type="checkbox" x-model="visible.name"> Product name</label>
                        <label><input type="checkbox" x-model="visible.createdBy"> Created by</label>
                        <label><input type="checkbox" x-model="visible.createdAt"> Created at</label>
                        <label><input type="checkbox" x-model="visible.status"> Status</label>
                        <label><input type="checkbox" x-model="visible.updated"> Updated</label>
                    </div>
                </div>
            </div>

            <div class="ft-master-product-count">
                @if($recordsReady && $rows)
                    Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ number_format($rows->total()) }} products
                @else
                    Loading products…
                @endif
            </div>

            @if(!$recordsReady)
                @include('livewire.shared.table-rows-placeholder', ['columns' => 10, 'rows' => 8])
            @else
                <div class="ft-master-product-table-wrap" wire:key="master-products-table-{{ $productCategory }}-{{ $productStatus }}-{{ $productPerPage }}">
                    <table class="ft-master-product-table">
                        <thead>
                            <tr>
                                <th class="ft-master-check-cell">
                                    <input
                                        type="checkbox"
                                        aria-label="Select all products on this page"
                                        x-on:change="$el.closest('table').querySelectorAll('tbody input[type=checkbox]').forEach((box) => box.checked = $event.target.checked)"
                                    >
                                </th>
                                <th x-show="visible.image">Image</th>
                                <th x-show="visible.code">SKU / Code</th>
                                <th x-show="visible.category">Product category</th>
                                <th x-show="visible.name">Product name</th>
                                <th x-show="visible.createdBy">Created by</th>
                                <th x-show="visible.createdAt">Created at</th>
                                <th x-show="visible.status">Status</th>
                                <th x-show="visible.updated">Updated</th>
                                <th class="ft-master-action-column">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $r)
                            @php
                                $createdAt = $r->created_at?->copy()->timezone($displayTimezone);
                                $createdAtLabel = $createdAt?->format('M j, Y g:i A') ?? '—';
                                $updatedAt = $r->updated_at?->copy()->timezone($displayTimezone);
                                $updatedLabel = !$updatedAt
                                    ? '—'
                                    : ($updatedAt->isToday() ? $updatedAt->diffForHumans() : ($updatedAt->isYesterday() ? 'Yesterday' : $updatedAt->format('M j, Y')));
                            @endphp
                            <tr wire:key="product-row-{{ $r->id }}">
                                <td class="ft-master-check-cell">
                                    <input type="checkbox" value="{{ $r->id }}" aria-label="Select {{ $r->name }}">
                                </td>
                                <td x-show="visible.image" data-label="Image">
                                    <div class="ft-master-product-thumb">
                                        @if($r->productImageUrl())
                                            <img src="{{ $r->productImageUrl() }}" alt="{{ $r->name }}">
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td x-show="visible.code" data-label="SKU / Code"><strong class="ft-master-product-code">{{ $r->code }}</strong></td>
                                <td x-show="visible.category" data-label="Product category">{{ $r->parent?->name ?? '—' }}</td>
                                <td x-show="visible.name" data-label="Product name">{{ $r->name }}</td>
                                <td x-show="visible.createdBy" data-label="Created by"><span class="ft-master-created-by" title="{{ $r->creator?->email ?: ($r->creator?->name ?: 'System') }}">{{ $r->creator?->name ?: 'System' }}</span></td>
                                <td x-show="visible.createdAt" data-label="Created at"><time class="ft-master-created-at" datetime="{{ $createdAt?->toIso8601String() }}" title="{{ $createdAtLabel }} {{ $displayTimezone }}">{{ $createdAtLabel }}</time></td>
                                <td x-show="visible.status" data-label="Status"><x-ui.badge :label="$r->status === 'active' ? 'Active' : 'Inactive'" /></td>
                                <td x-show="visible.updated" data-label="Updated"><span class="ft-master-updated" title="{{ $updatedAt?->format('M j, Y g:i A') }} {{ $displayTimezone }}">{{ $updatedLabel }}</span></td>
                                <td class="ft-master-action-column" data-label="Action">
                                    @if($canEditMaster || $canDeleteMaster)
                                        <div class="ft-master-row-menu" x-data="{ open: false, busy: false }" x-on:click.outside="open = false" wire:key="product-actions-{{ $r->id }}">
                                            <button
                                                type="button"
                                                class="ft-master-menu-trigger"
                                                x-on:click.stop="open = !open"
                                                x-bind:disabled="busy"
                                                aria-label="Product actions"
                                                :aria-expanded="open.toString()"
                                            >
                                                <span></span><span></span><span></span>
                                            </button>
                                            <div class="ft-master-row-menu-panel" x-cloak x-show="open" x-transition.origin.top.right>
                                                @if($canEditMaster)
                                                    <button
                                                        type="button"
                                                        x-bind:disabled="busy"
                                                        x-on:click.prevent.stop="
                                                            if (busy) return;
                                                            busy = true;
                                                            open = false;
                                                            $wire.editProduct({{ $r->id }}).finally(() => busy = false);
                                                        "
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z"/><path d="m14.5 7.5 3 3"/></svg>
                                                        <span>Edit product</span>
                                                    </button>
                                                @endif
                                                @if($canDeleteMaster)
                                                    <button
                                                        type="button"
                                                        class="is-danger"
                                                        x-bind:disabled="busy"
                                                        x-on:click.prevent.stop="
                                                            if (busy) return;
                                                            if (!window.confirm('Delete this product?')) return;
                                                            busy = true;
                                                            open = false;
                                                            $wire.deleteProduct({{ $r->id }}).finally(() => busy = false);
                                                        "
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                                                        <span>Delete product</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="small muted">View only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10"><div class="empty-state">No products found.</div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ft-master-product-pagination">
                    <div class="ft-master-rows-per-page">
                        <span>Rows per page</span>
                        <select wire:model.live="productPerPage" aria-label="Rows per page">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="ft-master-page-nav">
                        <span>Page {{ $rows->currentPage() }} of {{ max(1, $rows->lastPage()) }}</span>
                        <button type="button" wire:click="previousPage('masterPage')" @disabled($rows->onFirstPage())>Previous</button>
                        <button type="button" class="is-next" wire:click="nextPage('masterPage')" @disabled(!$rows->hasMorePages())>
                            <span>Next</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 5 7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            @endif
        </section>
    @else
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

        <section class="ft-master-generic-card">
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
                    <table class="master-table ft-master-generic-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Code</th>
                                <th>Name</th>
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
                                <td data-label="Order">{{ $r->sort_order }}</td>
                                <td data-label="Code"><strong class="ft-master-product-code">{{ $r->code }}</strong></td>
                                <td data-label="Name">{{ $r->name }}</td>
                                @if($hasParent)<td data-label="{{ $group === 'state' ? 'Country' : 'Product Category' }}">{{ $r->parent?->name ?? '—' }}</td>@endif
                                <td data-label="Description / Use">{{ $r->description ?: '—' }}</td>
                                @if($hasColor)
                                    @php
                                        $rowColor = \App\Support\MasterColor::normalize($r->color) ?: \App\Support\MasterColor::defaultFor($group, $r->name);
                                    @endphp
                                    <td data-label="Color">
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
                                <td data-label="Status"><x-ui.badge :label="$r->status === 'active' ? 'Active' : 'Inactive'" /></td>
                                <td data-label="Actions">
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
                            <tr><td colspan="{{ $columnCount }}"><div class="empty-state">No records found.</div></td></tr>
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

    @if($showModal && $group === 'product' && !$editId)
        @php
            $categorySearchValue = trim($productCategorySearch);
            $categorySuggestions = $categorySearchValue === '' ? $parents->take(6) : $categoryMatches;
            $manualProductCode = trim((string) $code);
            $productCodeFormatValid = $manualProductCode !== ''
                && mb_strlen($manualProductCode) <= 40
                && preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $manualProductCode) === 1;
            $productCodeReady = $productCodeFormatValid && !$productCodeDuplicate;
            $productCategoryReady = $productCodeReady && (bool) $parentId;
            $productNameReady = $productCategoryReady && trim((string) $name) !== '';
        @endphp
        <div class="overlay livewire-overlay ft-product-create-overlay" wire:click.self="close"></div>
        <div
            class="modal livewire-modal ft-product-create-modal"
            x-data="{ categoryOpen: false, creatingCategory: false, dragging: false }"
            x-on:product-category-selected.window="categoryOpen = false; creatingCategory = false"
            x-on:product-category-created.window="categoryOpen = false; creatingCategory = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="ft-create-product-title"
        >
            <div class="ft-product-create-head">
                <div>
                    <h2 id="ft-create-product-title">Create new product</h2>
                    <p>Add this product to the catalogue.</p>
                </div>
                <button type="button" class="ft-product-create-close" wire:click="close" aria-label="Close">×</button>
            </div>

            <div class="ft-product-create-body">
                <div class="ft-product-create-field ft-product-sequence-field is-current-step">
                    <label><b class="ft-product-step-number">1</b> SKU / Product code <span>*</span></label>
                    <div class="ft-product-create-input-wrap {{ $productCodeDuplicate ? 'is-duplicate' : (($manualProductCode !== '' && !$productCodeFormatValid) ? 'has-warning' : ($productCodeReady ? 'is-valid' : '')) }}">
                        <input
                            type="text"
                            wire:model.live.debounce.220ms="code"
                            maxlength="40"
                            autocomplete="off"
                            placeholder="Enter product code, e.g. TS-SUB-001"
                            aria-describedby="ft-master-product-code-help"
                        >
                        @if($productCodeDuplicate || ($manualProductCode !== '' && !$productCodeFormatValid))
                            <svg class="ft-product-sequence-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></svg>
                        @elseif($productCodeReady)
                            <svg class="ft-product-valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4L19 6"/></svg>
                        @endif
                    </div>
                    @if($productCodeDuplicate)
                        <small id="ft-master-product-code-help" class="ft-product-step-error">{{ $productCodeDuplicate->trashed() ? 'This code is reserved by an archived product.' : 'This product code already exists.' }}</small>
                    @elseif($manualProductCode !== '' && !$productCodeFormatValid)
                        <small id="ft-master-product-code-help" class="ft-product-step-error">Use letters, numbers, dots, dashes or underscores only. Maximum 40 characters.</small>
                    @else
                        <small id="ft-master-product-code-help">Enter the SKU / product code manually. Category selection unlocks after a valid, unused code is entered.</small>
                    @endif
                    @error('code')<div class="validation-error">{{ $message }}</div>@enderror
                </div>

                <div class="ft-product-create-field ft-product-category-field ft-product-sequence-field {{ !$productCodeReady ? 'is-step-locked' : '' }}" x-on:click.outside="categoryOpen = false">
                    <label><b class="ft-product-step-number">2</b> Product category <span>*</span></label>
                    <div class="ft-product-category-picker">
                        <div class="ft-product-category-input-wrap" :class="categoryOpen ? 'is-open' : ''">
                            <svg class="ft-product-category-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                            <input
                                type="text"
                                wire:model.live.debounce.220ms="productCategorySearch"
                                x-on:focus="categoryOpen = true"
                                x-on:click="categoryOpen = true"
                                x-on:keydown.escape="categoryOpen = false"
                                placeholder="{{ $productCodeReady ? 'Search or create a category' : 'Enter product code first' }}"
                                autocomplete="off"
                                aria-label="Product category"
                                @disabled(!$productCodeReady)
                            >
                            <svg class="ft-product-category-chevron" :class="categoryOpen ? 'is-open' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 10 5 5 5-5"/></svg>
                        </div>

                        <div class="ft-product-category-menu" x-cloak x-show="categoryOpen && {{ $productCodeReady ? 'true' : 'false' }}" x-transition.origin.top>
                            @if($categorySearchValue !== '' && $categoryMatches->isEmpty())
                                <div class="ft-product-category-empty">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                    <div><strong>No category found</strong><span>No category matches ‘{{ $categorySearchValue }}’.</span></div>
                                </div>
                            @elseif($categorySuggestions->isNotEmpty())
                                <div class="ft-product-category-list">
                                    @foreach($categorySuggestions as $category)
                                        <button type="button" wire:click="selectProductCategory({{ $category->id }})" class="{{ (int) $parentId === (int) $category->id ? 'is-selected' : '' }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                            <span>{{ $category->name }}</span>
                                            @if((int) $parentId === (int) $category->id)
                                                <svg class="ft-product-category-row-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="ft-product-category-empty is-initial">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                    <div><strong>No categories available</strong><span>Type a category name to create the first one.</span></div>
                                </div>
                            @endif

                            @if($productCodeReady && $categorySearchValue !== '' && !$hasExactCategory && $canCreateMaster)
                                <button
                                    type="button"
                                    class="ft-product-category-create-row"
                                    wire:click="beginProductCategoryCreation"
                                    x-on:click="creatingCategory = true; categoryOpen = true"
                                >
                                    <span class="ft-product-category-plus">+</span>
                                    <span class="ft-product-category-create-copy">
                                        <strong>Create ‘{{ $categorySearchValue }}’</strong>
                                        <small>The category will be created and selected for this product.</small>
                                    </span>
                                    <span class="ft-product-category-permission">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="6"/><path d="M9 7V5.8a3 3 0 0 1 6 0V7M12 11v4"/></svg>
                                        You have permission
                                    </span>
                                </button>
                            @endif

                            @if($categorySearchValue !== '' && $similarCategories->isNotEmpty())
                                <div class="ft-product-category-similar">
                                    <span>Similar categories</span>
                                    @foreach($similarCategories as $category)
                                        <button type="button" wire:click="selectProductCategory({{ $category->id }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                            <span>{{ $category->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            @if($canCreateMaster)
                                <div class="ft-product-category-create-form" x-cloak x-show="creatingCategory" x-transition>
                                    <label>New category name</label>
                                    <div>
                                        <input type="text" wire:model="newProductCategoryName" maxlength="255" aria-label="New category name">
                                        <button type="button" class="ghost" wire:click="cancelProductCategoryCreation" x-on:click="creatingCategory = false">Cancel</button>
                                        <button type="button" class="primary" wire:click="createProductCategory" wire:loading.attr="disabled" wire:target="createProductCategory">Create category</button>
                                    </div>
                                    @error('newProductCategoryName')<div class="validation-error">{{ $message }}</div>@enderror
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!$productCodeReady)
                        <small class="ft-product-step-hint">Complete step 1 with a valid, unused SKU / product code to unlock category selection.</small>
                    @elseif(!$parentId)
                        <small class="ft-product-step-hint">Select an existing category or create a new category before continuing.</small>
                    @endif
                    @error('parentId')<div class="validation-error">{{ $message }}</div>@enderror
                </div>

                <div class="ft-product-create-field ft-product-sequence-field {{ !$productCategoryReady ? 'is-step-locked' : '' }}">
                    <label><b class="ft-product-step-number">3</b> Product name <span>*</span></label>
                    <div class="ft-product-create-input-wrap {{ trim($name) !== '' ? 'is-valid' : '' }}">
                        <input
                            type="text"
                            wire:model.live.debounce.220ms="name"
                            maxlength="255"
                            autocomplete="off"
                            placeholder="{{ $productCategoryReady ? 'Enter product name' : 'Select a product category first' }}"
                            @disabled(!$productCategoryReady)
                        >
                        @if(trim($name) !== '')
                            <svg class="ft-product-valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4L19 6"/></svg>
                        @endif
                    </div>
                    @if(!$productCategoryReady)
                        <small class="ft-product-step-hint">Complete step 2 first. Product name becomes available after a category is selected.</small>
                    @endif
                    @error('name')<div class="validation-error">{{ $message }}</div>@enderror
                </div>

                <div class="ft-product-create-field ft-product-create-image-field ft-product-sequence-field {{ !$productNameReady ? 'is-step-locked' : '' }}">
                    <label><b class="ft-product-step-number">4</b> Product image <em>Optional</em></label>
                    <div class="ft-product-create-image-row">
                        <div
                            class="ft-product-drop-zone {{ !$productNameReady ? 'is-step-disabled' : '' }}"
                            @if($productNameReady)
                                :class="dragging ? 'is-dragging' : ''"
                                x-on:dragover.prevent="dragging = true"
                                x-on:dragleave.prevent="dragging = false"
                                x-on:drop.prevent="
                                    dragging = false;
                                    if ($event.dataTransfer.files.length) {
                                        const dt = new DataTransfer();
                                        dt.items.add($event.dataTransfer.files[0]);
                                        $refs.productFile.files = dt.files;
                                        $refs.productFile.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                "
                                x-on:click="$refs.productFile.click()"
                                role="button"
                                tabindex="0"
                                x-on:keydown.enter.prevent="$refs.productFile.click()"
                                x-on:keydown.space.prevent="$refs.productFile.click()"
                            @else
                                aria-disabled="true"
                            @endif
                        >
                            @if($productNameReady)
                                <input x-ref="productFile" type="file" wire:model="productImage" accept="image/png,image/jpeg,image/webp" tabindex="-1">
                            @endif
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v14H4z"/><path d="m7 16 3.5-4 3 3 2-2 2.5 3"/><circle cx="15.5" cy="9" r="1.4"/></svg>
                            <div>
                                @if($productNameReady)
                                    <strong wire:loading.remove wire:target="productImage">Drop an image here or <span>browse</span></strong>
                                    <strong wire:loading wire:target="productImage">Preparing image…</strong>
                                    <small>PNG, JPG or WEBP&nbsp;&nbsp;•&nbsp;&nbsp;Max 5 MB</small>
                                @else
                                    <strong>Complete product name first</strong>
                                    <small>Image upload unlocks after steps 1–3 are complete.</small>
                                @endif
                            </div>
                        </div>
                        <div class="ft-product-create-image-preview">
                            @if($productImagePreview)
                                <img src="{{ $productImagePreview }}" alt="Product image preview">
                            @else
                                <span aria-hidden="true"></span>
                            @endif
                        </div>
                    </div>
                    @if(!$productNameReady)
                        <small class="ft-product-step-hint">Complete step 3 to enable the optional product image.</small>
                    @endif
                    @error('productImage')<div class="validation-error">{{ $message }}</div>@enderror
                </div>

                @if(!$productNameReady)
                    <div class="ft-product-create-info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7.2h.01"/></svg>
                        <span>Complete the product in order: SKU / code → category → product name → optional image.</span>
                    </div>
                @endif
            </div>

            <div class="ft-product-create-foot">
                <div class="ft-product-create-permission-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="6"/><path d="M9 7V5.8a3 3 0 0 1 6 0V7M12 11v4"/></svg>
                    <span>You have permission to create products and categories</span>
                </div>
                <div class="ft-product-create-actions">
                    <button type="button" class="ghost" wire:click="close">Cancel</button>
                    <button
                        type="button"
                        class="primary"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save,productImage"
                        @disabled(!$productNameReady)
                    >Create &amp; add product</button>
                </div>
            </div>
        </div>
    @elseif($showModal)
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
                        <label>Name *</label>
                        <input wire:model="name">
                        @error('name')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>

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
                        <label>Description</label>
                        <textarea wire:model="description" rows="3"></textarea>
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
</div>
