@php
    $selectedIds = collect($createProductRows)->pluck('product_id')->filter()->map(fn($id) => (int) $id)->all();
    $resultCount = $productSearchResults->count();
    $productSearchValue = trim((string) $createProductSearch);
    $showCreateProductSuggestion = $productSearchValue !== '' && (int) $productResultTotal === 0;
    $categorySearchValue = trim((string) $newProductCategorySearch);
    $categorySuggestions = $categorySearchValue === '' ? $productCategories->take(6) : $newProductCategoryMatches;
    $hasDuplicateCode = (bool) $duplicateProduct;
    $hasSimilarProductName = $newProductSimilarProducts->isNotEmpty();
    $manualProductCode = trim((string) $newProductCode);
    $productCodeFormatValid = $manualProductCode !== ''
        && mb_strlen($manualProductCode) <= 40
        && preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $manualProductCode) === 1;
    $productCodeReady = $productCodeFormatValid && !$hasDuplicateCode;
    $productCategoryReady = $productCodeReady && (bool) $newProductSelectedCategory;
    $productNameReady = $productCategoryReady && trim((string) $newProductName) !== '';
    $totalUnits = collect($createProductRows)->sum(fn($item) => (int) ($item['quantity'] ?? 0));
@endphp

@if($catalogReady)
<section
    class="section ft-inquiry-create-section ft-create-section ft-order-products-prototype"
    wire:key="create-catalog-ready"
    x-data="{
        resultsOpen: false,
        resultsMaxHeight: 420,
        fitProductResults() {
            this.$nextTick(() => {
                const host = this.$refs.productSearchHost;
                if (!host) return;
                const resultsTop = host.getBoundingClientRect().top + 42;
                const available = window.innerHeight - resultsTop - 14;
                this.resultsMaxHeight = Math.max(120, Math.min(520, available));
            });
        },
        openProductResults() {
            this.resultsOpen = true;
            this.fitProductResults();
        }
    }"
    x-on:create-order-product-selected.window="resultsOpen = false"
    x-on:focus-create-order-product-search.window="$nextTick(() => { openProductResults(); $refs.productSearch?.focus(); })"
    x-on:resize.window="if (resultsOpen) fitProductResults()"
>
    <div class="ft-order-products-title-row">
        <div class="ft-create-section-title ft-order-products-title">
            <span>2</span>
            <h2>Products &amp; quantities</h2>
            <em>Optional</em>
        </div>
        <p>Search all {{ number_format((int) $activeProductCount) }} products &mdash; no category selection required.</p>
    </div>

    <div class="ft-order-product-search-label">Search product</div>
    <div class="ft-order-product-search-row">
        <div class="ft-order-product-search-host" x-ref="productSearchHost" x-on:click.outside="resultsOpen = false">
            <div class="ft-order-product-search-input" :class="resultsOpen ? 'is-open' : ''">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input
                    x-ref="productSearch"
                    type="search"
                    wire:model.live.debounce.220ms="createProductSearch"
                    x-on:focus="openProductResults()"
                    x-on:keydown.escape="resultsOpen = false"
                    placeholder="Search by product name or SKU..."
                    autocomplete="off"
                    aria-label="Search product"
                >
                <span class="ft-order-product-shortcut">&#8984; K</span>
            </div>

            <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.origin.top :style="`max-height:${resultsMaxHeight}px`">
                <div class="ft-order-product-results-head">
                    <span>Top matches</span>
                    @if(!$createProductShowAllResults && $productResultTotal > $resultCount)
                        <button type="button" wire:click="showAllCreateProductResults">View all results <span>&nearr;</span></button>
                    @endif
                </div>

                <div class="ft-order-product-result-list">
                    @forelse($productSearchResults as $product)
                        @continue($product->type !== 'product')
                        @php
                            $isSelected = in_array((int) $product->id, $selectedIds, true);
                            $detailText = $product->productCatalogSummary();
                            $imageUrl = $product->productImageUrl();
                        @endphp
                        <button
                            type="button"
                            class="ft-order-product-result {{ $isSelected ? 'is-selected' : '' }}"
                            wire:click="selectCreateProduct({{ $product->id }})"
                            @disabled($isSelected)
                        >
                            <span class="ft-order-product-thumb">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="">
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                @endif
                            </span>
                            <span class="ft-order-product-result-copy">
                                <strong>{{ $product->name }}</strong>
                                <span>Product code: {{ $product->code }}</span>
                                @if(filled($detailText))<small>{{ \Illuminate\Support\Str::limit($detailText, 95) }}</small>@endif
                            </span>
                            @if($isSelected)<span class="ft-order-product-selected-mark">Selected</span>@endif
                        </button>
                    @empty
                        <div class="ft-order-product-no-results">
                            <strong>No products found</strong>
                            <span>Try another product name or SKU.</span>
                        </div>
                    @endforelse
                </div>

                @if($showCreateProductSuggestion)
                    <div class="ft-order-product-create-row">
                        @if($canCreateCatalogProduct)
                            <button type="button" wire:click="openCreateOrderProductModalFromSearch">
                                <span class="ft-order-product-create-icon">+</span>
                                <span><strong>Create Product</strong><small>No matching product found. Add "{{ $productSearchValue }}" to the catalog</small></span>
                            </button>
                            <span class="ft-order-product-permission-ok">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="6" y="10" width="12" height="10" rx="2"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></svg>
                                You have permission
                            </span>
                        @else
                            <button type="button" disabled>
                                <span class="ft-order-product-create-icon">+</span>
                                <span><strong>Create Product</strong><small>No matching product found for "{{ $productSearchValue }}"</small></span>
                            </button>
                            <span class="ft-order-product-permission-required">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="6" y="10" width="12" height="10" rx="2"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></svg>
                                Product create permission required
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($canViewProductCategories)
            <select class="ft-order-product-category-filter" wire:model.live="createProductCategoryFilter" x-on:change="openProductResults()" aria-label="Filter products by category">
                <option value="">All categories</option>
                @foreach($productCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    @if(count($createProductRows))
        <div class="ft-order-selected-products">
            <div class="ft-order-selected-products-title">Selected products ({{ count($createProductRows) }})</div>
            <div class="ft-order-selected-products-table">
                <div class="ft-order-selected-products-head">
                    <span>Product</span><span>Quantity</span><span>Notes</span><span>Action</span>
                </div>
                @foreach($createProductRows as $index => $item)
                    @php
                        $detail = $selectedProductDetails->get((int) ($item['product_id'] ?? 0));
                        $itemImage = $detail?->productImageUrl();
                        $itemCode = (string) ($detail?->code ?? '');
                        $itemCategory = (string) ($detail?->parent?->name ?? ($item['category'] ?? ''));
                        $itemName = (string) ($detail?->name ?? ($item['product'] ?? 'Product'));
                    @endphp
                    <div class="ft-order-selected-product-row" wire:key="selected-order-product-{{ $item['product_id'] ?? $index }}-{{ $index }}">
                        <div class="ft-order-selected-product-info">
                            <span class="ft-order-product-thumb">
                                @if($itemImage)
                                    <img src="{{ $itemImage }}" alt="">
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                @endif
                            </span>
                            <span>
                                <strong>{{ $itemName }}</strong>
                                <small>SKU: {{ $itemCode ?: 'N/A' }} <i>&bull;</i> {{ $itemCategory ?: 'Uncategorized' }}</small>
                            </span>
                        </div>
                        <div class="ft-order-product-quantity-control">
                            <button type="button" wire:click="decrementCreateProductQuantity({{ $index }})" aria-label="Decrease quantity">&minus;</button>
                            <input type="number" min="1" max="999999999" wire:model.live.debounce.300ms="createProductRows.{{ $index }}.quantity" aria-label="Quantity for {{ $itemName }}">
                            <button type="button" wire:click="incrementCreateProductQuantity({{ $index }})" aria-label="Increase quantity">+</button>
                            @error("createProductRows.$index.quantity")<small class="validation-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="ft-order-product-notes">
                            <input type="text" maxlength="2000" wire:model.blur="createProductRows.{{ $index }}.notes" placeholder="Optional notes..." aria-label="Notes for {{ $itemName }}">
                            @error("createProductRows.$index.notes")<small class="validation-error">{{ $message }}</small>@enderror
                            @error("createProductRows.$index.product")<small class="validation-error">{{ $message }}</small>@enderror
                        </div>
                        <button type="button" class="ft-order-selected-product-remove" wire:click="removeCreateProductRow({{ $index }})" aria-label="Remove {{ $itemName }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M8 10v7M12 10v7M16 10v7M6 7l1 14h10l1-14"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="ft-order-products-summary-row">
        <span>{{ count($createProductRows) }} {{ \Illuminate\Support\Str::plural('product', count($createProductRows)) }} <i>&bull;</i> {{ number_format($totalUnits) }} total units</span>

        <div class="ft-order-products-summary-actions">
            @if($canCreateCatalogProduct)
                <button
                    type="button"
                    class="ft-order-products-create-new-button"
                    wire:click="openCreateOrderProductModal"
                    @disabled(count($createProductRows) >= 25)
                    aria-label="Create product and add it to this inquiry"
                >
                    <span class="ft-order-products-create-new-icon" aria-hidden="true">+</span>
                    <span class="ft-order-products-create-new-copy">
                        <strong>Create Product</strong>
                        <small>Can't find it? Add a product to the catalog</small>
                    </span>
                </button>
            @else
                <button
                    type="button"
                    class="ft-order-products-create-new-button"
                    disabled
                    title="Product create permission required"
                    aria-label="Create product unavailable: product create permission required"
                >
                    <span class="ft-order-products-create-new-icon" aria-hidden="true">+</span>
                    <span class="ft-order-products-create-new-copy">
                        <strong>Create Product</strong>
                        <small>Product create permission required</small>
                    </span>
                </button>
            @endif

            <button
                type="button"
                class="ft-order-products-add-another-button"
                wire:click="focusCreateProductSearch"
                @disabled(count($createProductRows) >= 25)
            ><span>+</span> Add Product</button>
        </div>
    </div>
</section>
@else
    <x-jobs.create-section-placeholder number="2" title="Products & quantities" section="catalog" :rows="3" />
@endif

@if($showCreateOrderProductModal)
    <div class="overlay livewire-overlay ft-product-create-overlay" wire:click.self="closeCreateOrderProductModal"></div>
    <div
        class="modal livewire-modal ft-product-create-modal ft-order-create-product-modal"
        x-data="{ categoryOpen: false, creatingCategory: false, dragging: false }"
        x-on:create-order-product-category-selected.window="categoryOpen = false; creatingCategory = false"
        x-on:create-order-product-category-created.window="categoryOpen = false; creatingCategory = false"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ft-order-create-product-title"
    >
        <div class="ft-product-create-head">
            <div>
                <h2 id="ft-order-create-product-title">Create new product</h2>
                <p>Add this product to the catalog and select it for this Inquiry.</p>
            </div>
            <button type="button" class="ft-product-create-close" wire:click="closeCreateOrderProductModal" aria-label="Close">&times;</button>
        </div>

        <div class="ft-product-create-body">
            <div class="ft-product-create-field ft-product-sequence-field is-current-step">
                <label><b class="ft-product-step-number">1</b> SKU / Product code <span>*</span></label>
                <div class="ft-product-create-input-wrap {{ $hasDuplicateCode ? 'is-duplicate' : (($manualProductCode !== '' && !$productCodeFormatValid) ? 'has-warning' : ($productCodeReady ? 'is-valid' : '')) }}">
                    <input
                        type="text"
                        wire:model.live.debounce.220ms="newProductCode"
                        maxlength="40"
                        autocomplete="off"
                        placeholder="Enter product code, e.g. TS-SUB-001"
                        aria-describedby="ft-new-product-code-help"
                    >
                    @if($hasDuplicateCode || ($manualProductCode !== '' && !$productCodeFormatValid))
                        <svg class="ft-order-product-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></svg>
                    @elseif($productCodeReady)
                        <svg class="ft-product-valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4L19 6"/></svg>
                    @endif
                </div>
                @if($hasDuplicateCode)
                    <div class="ft-order-product-duplicate-message">{{ $duplicateProduct->trashed() ? 'This product code is reserved by an archived product.' : 'This product code already exists.' }}</div>
                    <div class="ft-order-product-duplicate-card">
                        <span class="ft-order-product-thumb">
                            @if($duplicateProduct->productImageUrl())
                                <img src="{{ $duplicateProduct->productImageUrl() }}" alt="">
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            @endif
                        </span>
                        <span class="ft-order-product-duplicate-copy">
                            <strong>{{ $duplicateProduct->name }}</strong>
                            <span>SKU: {{ $duplicateProduct->code }} <i>&bull;</i> {{ $duplicateProduct->parent?->name ?? 'Uncategorized' }}</span>
                            <small class="{{ !$duplicateProduct->trashed() && $duplicateProduct->status === 'active' ? 'is-active' : 'is-inactive' }}">{{ $duplicateProduct->trashed() ? 'Archived' : ucfirst($duplicateProduct->status) }}</small>
                        </span>
                        @if(!$duplicateProduct->trashed() && $duplicateProduct->status === 'active')
                            <button type="button" wire:click="selectDuplicateCreateOrderProduct({{ $duplicateProduct->id }})">Select existing</button>
                        @else
                            <span class="ft-order-product-duplicate-inactive">{{ $duplicateProduct->trashed() ? 'Archived product' : 'Inactive product' }}</span>
                        @endif
                    </div>
                @else
                    @if($manualProductCode !== '' && !$productCodeFormatValid)
                        <small id="ft-new-product-code-help" class="ft-product-step-error">Use letters, numbers, dots, dashes or underscores only. Maximum 40 characters.</small>
                    @else
                        <small id="ft-new-product-code-help">Enter the SKU / product code manually. It must be unique. Category selection unlocks after a valid code is entered.</small>
                    @endif
                @endif
                @error('newProductCode')<div class="validation-error">{{ $message }}</div>@enderror
            </div>

            @if($canViewProductCategories)
            <div class="ft-product-create-field ft-product-category-field ft-product-sequence-field {{ !$productCodeReady ? 'is-step-locked' : '' }}" x-on:click.outside="categoryOpen = false">
                <label><b class="ft-product-step-number">2</b> Product category <span>*</span></label>
                <div class="ft-product-category-picker">
                    <div class="ft-product-category-input-wrap" :class="categoryOpen ? 'is-open' : ''">
                        <svg class="ft-product-category-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.220ms="newProductCategorySearch"
                            x-on:focus="categoryOpen = true"
                            x-on:click="categoryOpen = true"
                            x-on:keydown.escape="categoryOpen = false"
                            placeholder="{{ $productCodeReady ? 'Search or create a category' : 'Enter product code first' }}"
                            autocomplete="off"
                            aria-label="Product category"
                            @disabled(!$productCodeReady)
                        >
                        @if($newProductSelectedCategory)
                            <svg class="ft-order-product-category-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4L19 6"/></svg>
                        @endif
                        <svg class="ft-product-category-chevron" :class="categoryOpen ? 'is-open' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 10 5 5 5-5"/></svg>
                    </div>

                    <div class="ft-product-category-menu" x-cloak x-show="categoryOpen && {{ $productCodeReady ? 'true' : 'false' }}" x-transition.origin.top>
                        @if($categorySearchValue !== '' && $newProductCategoryMatches->isEmpty())
                            <div class="ft-product-category-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                <div><strong>No category found</strong><span>No category matches '{{ $categorySearchValue }}'.</span></div>
                            </div>
                        @elseif($categorySuggestions->isNotEmpty())
                            <div class="ft-product-category-list">
                                @foreach($categorySuggestions as $category)
                                    <button type="button" wire:click="selectCreateOrderProductCategory({{ $category->id }})" class="{{ (int) $newProductCategoryId === (int) $category->id ? 'is-selected' : '' }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                        <span>{{ $category->name }}</span>
                                        @if((int) $newProductCategoryId === (int) $category->id)
                                            <svg class="ft-product-category-row-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($productCodeReady && $categorySearchValue !== '' && !$newProductHasExactCategory && $canCreateProductCategory)
                            <button type="button" class="ft-product-category-create-row" wire:click="beginCreateOrderProductCategory" x-on:click="creatingCategory = true; categoryOpen = true">
                                <span class="ft-product-category-plus">+</span>
                                <span class="ft-product-category-create-copy"><strong>Create '{{ $categorySearchValue }}'</strong><small>The category will be created and selected for this product.</small></span>
                                <span class="ft-product-category-permission">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="6"/><path d="M9 7V5.8a3 3 0 0 1 6 0V7M12 11v4"/></svg>
                                    You have permission
                                </span>
                            </button>
                        @endif

                        @if($categorySearchValue !== '' && $newProductSimilarCategories->isNotEmpty())
                            <div class="ft-product-category-similar">
                                <span>Similar categories</span>
                                @foreach($newProductSimilarCategories as $category)
                                    <button type="button" wire:click="selectCreateOrderProductCategory({{ $category->id }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                        <span>{{ $category->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <div class="ft-product-category-create-form" x-cloak x-show="creatingCategory" x-transition>
                            <label>New category name</label>
                            <div>
                                <input type="text" wire:model="newProductCategoryName" maxlength="255" aria-label="New category name">
                                <button type="button" class="ghost" wire:click="cancelCreateOrderProductCategory" x-on:click="creatingCategory = false">Cancel</button>
                                <button type="button" class="primary" wire:click="createCreateOrderProductCategory" wire:loading.attr="disabled" wire:target="createCreateOrderProductCategory">Create category</button>
                            </div>
                            @error('newProductCategoryName')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                @if(!$productCodeReady)
                    <small class="ft-product-step-hint">Complete step 1 with a valid, unused SKU / product code to unlock category selection.</small>
                @elseif(!$newProductSelectedCategory)
                    <small class="ft-product-step-hint">Select an existing category or create a new category before continuing.</small>
                @endif
                @error('newProductCategoryId')<div class="validation-error">{{ $message }}</div>@enderror
            </div>
            @else
            <div class="ft-product-create-field ft-product-category-field ft-product-sequence-field is-step-locked">
                <label><b class="ft-product-step-number">2</b> Product category <span>*</span></label>
                <div class="ft-create-note">Product Categories <b>View</b> permission is required to select a category for a new product.</div>
            </div>
            @endif

            <div class="ft-product-create-field ft-product-sequence-field {{ !$productCategoryReady ? 'is-step-locked' : '' }}">
                <label><b class="ft-product-step-number">3</b> Product name <span>*</span></label>
                <div class="ft-product-create-input-wrap {{ $hasSimilarProductName ? 'has-warning' : (trim($newProductName) !== '' ? 'is-valid' : '') }}">
                    <input
                        type="text"
                        wire:model.live.debounce.220ms="newProductName"
                        maxlength="255"
                        autocomplete="off"
                        placeholder="{{ $productCategoryReady ? 'Enter product name' : 'Select a product category first' }}"
                        @disabled(!$productCategoryReady)
                    >
                    @if($hasSimilarProductName)
                        <svg class="ft-order-product-warning-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/></svg>
                    @elseif(trim($newProductName) !== '')
                        <svg class="ft-product-valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4L19 6"/></svg>
                    @endif
                </div>
                @if($hasSimilarProductName)
                    <div class="ft-order-product-name-warning">
                        <span>A product with this name may already exist.</span>
                        <button type="button" wire:click="viewSimilarCreateProducts">View similar products</button>
                    </div>
                @endif
                @if(!$productCategoryReady)
                    <small class="ft-product-step-hint">Complete step 2 first. Product name becomes available after a category is selected.</small>
                @endif
                @error('newProductName')<div class="validation-error">{{ $message }}</div>@enderror
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
                            x-on:drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { const dt = new DataTransfer(); dt.items.add($event.dataTransfer.files[0]); $refs.orderProductFile.files = dt.files; $refs.orderProductFile.dispatchEvent(new Event('change', { bubbles: true })); }"
                            x-on:click="$refs.orderProductFile.click()"
                            role="button"
                            tabindex="0"
                            x-on:keydown.enter.prevent="$refs.orderProductFile.click()"
                            x-on:keydown.space.prevent="$refs.orderProductFile.click()"
                        @else
                            aria-disabled="true"
                        @endif
                    >
                        @if($productNameReady)
                            <input x-ref="orderProductFile" type="file" wire:model="newProductImage" accept="image/png,image/jpeg,image/webp" tabindex="-1">
                        @endif
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v14H4z"/><path d="m7 16 3.5-4 3 3 2-2 2.5 3"/><circle cx="15.5" cy="9" r="1.4"/></svg>
                        <div>
                            @if($productNameReady)
                                <strong wire:loading.remove wire:target="newProductImage">Drop an image here or <span>browse</span></strong>
                                <strong wire:loading wire:target="newProductImage">Preparing image...</strong>
                                <small>PNG, JPG or WEBP&nbsp;&nbsp;&bull;&nbsp;&nbsp;Max 5 MB</small>
                            @else
                                <strong>Complete product name first</strong>
                                <small>Image upload unlocks after steps 1–3 are complete.</small>
                            @endif
                        </div>
                    </div>
                    <div class="ft-product-create-image-preview">
                        @if($newProductImagePreview)
                            <img src="{{ $newProductImagePreview }}" alt="Product image preview">
                        @else
                            <span aria-hidden="true"></span>
                        @endif
                    </div>
                </div>
                @if(!$productNameReady)
                    <small class="ft-product-step-hint">Complete step 3 to enable the optional product image.</small>
                @endif
                @error('newProductImage')<div class="validation-error">{{ $message }}</div>@enderror
            </div>

            @if($hasDuplicateCode || $hasSimilarProductName)
                <div class="ft-order-product-review-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/></svg>
                    <span>Review the existing product before creating a duplicate.</span>
                </div>
            @endif
        </div>

        <div class="ft-product-create-foot">
            <div class="ft-product-create-permission-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="6"/><path d="M9 7V5.8a3 3 0 0 1 6 0V7M12 11v4"/></svg>
                <span>
                    Product create permission granted.
                    @if($canCreateProductCategory)
                        Product Category create permission granted.
                    @elseif($canViewProductCategories)
                        Product Categories are view-only for this role.
                    @else
                        Product Category view permission is required to finish a new product.
                    @endif
                </span>
            </div>
            <div class="ft-product-create-actions">
                <button type="button" class="ghost" wire:click="closeCreateOrderProductModal">Cancel</button>
                @if($hasDuplicateCode && !$duplicateProduct->trashed() && $duplicateProduct->status === 'active')
                    <button type="button" class="primary ft-order-select-existing-button" wire:click="selectDuplicateCreateOrderProduct({{ $duplicateProduct->id }})">Select existing product</button>
                @endif
                <button
                    type="button"
                    class="primary"
                    wire:click="createAndAddOrderProduct"
                    wire:loading.attr="disabled"
                    wire:target="createAndAddOrderProduct,newProductImage"
                    @disabled(!$productNameReady || $hasDuplicateCode)
                >Create &amp; add product</button>
            </div>
        </div>
    </div>
@endif
