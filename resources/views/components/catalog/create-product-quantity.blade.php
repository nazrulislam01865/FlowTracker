@props([
    'rows' => [],
    'rowsProperty' => 'createProductRows',
    'removeMethod' => 'removeCreateProductRow',
    'required' => false,
    'activeProductCount' => 0,
    'productSearchResults' => collect(),
    'productResultTotal' => 0,
    'createProductSearch' => '',
    'createProductCategoryFilter' => '',
    'createProductShowAllResults' => false,
    'productCategories' => collect(),
    'selectedProductDetails' => collect(),
    'canViewProductCategories' => false,
    'canCreateCatalogProduct' => false,
    'wireKey' => 'shared-create-products',
    'rowKeyPrefix' => 'shared-selected-product',
    'requiredErrorField' => null,
])

@php
    $selectedIds = collect($rows)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->all();
    $resultCount = $productSearchResults->count();
    $productSearchValue = trim((string) $createProductSearch);
    $totalUnits = collect($rows)->sum(fn ($item) => (int) ($item['quantity'] ?? 0));
    $createProductUrl = route('master-data', ['group' => 'product', 'create' => 1]);
    $sectionError = $requiredErrorField ? $errors->first($requiredErrorField) : null;
@endphp

<section
    class="section ft-inquiry-create-section ft-create-section ft-order-products-prototype ft-inquiry-prototype"
    wire:key="{{ $wireKey }}"
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
    x-on:open-create-order-product-results.window="$nextTick(() => openProductResults())"
    x-on:resize.window="if (resultsOpen) fitProductResults()"
>
    <div class="ft-order-products-title-row">
        <div class="ft-create-section-title ft-order-products-title">
            <span>2</span>
            <h2>Products &amp; quantities</h2>
            @if($required)
                <em class="is-required">Required</em>
            @else
                <em>Optional</em>
            @endif
        </div>
        <p>
            Search all {{ number_format((int) $activeProductCount) }} products &mdash;
            {{ $required ? 'at least one product is required.' : 'no category selection required.' }}
        </p>
        @if($sectionError)
            <div class="validation-error ft-order-products-section-error">{{ $sectionError }}</div>
        @endif
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
                    placeholder="Search product name, product code or reference code"
                    autocomplete="off"
                    aria-label="Search product"
                >
                <span class="ft-order-product-search-tools">
                    @if($productSearchValue !== '')
                        <button type="button" class="ft-order-product-search-clear" wire:click="$set('createProductSearch', '')" x-on:click="openProductResults()" aria-label="Clear product search">&times;</button>
                    @endif
                    <span class="ft-order-product-shortcut">&#8984; K</span>
                </span>
            </div>

            <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.origin.top :style="`max-height:${resultsMaxHeight}px`">
                <div class="ft-order-product-results-head">
                    <span>Top matches <b>{{ number_format((int) $productResultTotal) }} {{ \Illuminate\Support\Str::plural('result', (int) $productResultTotal) }}</b></span>
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
                            $referenceCode = $product->productReferenceCode();
                            $displayCode = $product->productDisplayCode();
                            $mainCategory = $product->productMainCategory();
                            $productCategory = trim((string) ($product->parent?->name ?? ''));
                            $subCategory = trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category') ?: $detailText));
                            $classification = collect([$mainCategory, $productCategory, $subCategory])->filter()->unique()->values();
                        @endphp
                        <div class="ft-order-product-result {{ $isSelected ? 'is-selected' : '' }}">
                            <span class="ft-order-product-thumb">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="">
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                @endif
                            </span>
                            <span class="ft-order-product-result-copy">
                                <strong>{{ $product->name }}</strong>
                                <span class="ft-order-product-code-line">Product code: {{ $displayCode }} <i>&bull;</i> Ref: {{ $referenceCode ?: '—' }}</span>
                                @if($classification->isNotEmpty())
                                    <small class="ft-order-product-classification">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                        @foreach($classification as $part)<span>{{ $part }}</span>@if(!$loop->last)<i>&rsaquo;</i>@endif @endforeach
                                    </small>
                                @endif
                            </span>
                            <button type="button" class="ft-order-product-select-button {{ $isSelected ? 'is-selected' : '' }}" wire:click="selectCreateProduct({{ $product->id }})" @disabled($isSelected)>{{ $isSelected ? 'Selected' : 'Select' }}</button>
                        </div>
                    @empty
                        <div class="ft-order-product-no-results">
                            <strong>No products found</strong>
                            <span>Try another product name or SKU.</span>
                        </div>
                    @endforelse
                </div>

                <div class="ft-order-product-results-footer">
                    <span>Use &uarr; &darr; to navigate &nbsp;&middot;&nbsp; Enter to select</span>
                    @if($canCreateCatalogProduct)
                        <a href="{{ $createProductUrl }}">Can't find it? <b>Create new product</b></a>
                    @endif
                </div>
            </div>
        </div>

        @if($canViewProductCategories)
            <x-ui.select-filter
                class="ft-order-product-category-search-filter"
                label="Product category"
                property="createProductCategoryFilter"
                :value="$createProductCategoryFilter"
                placeholder="All categories"
                :options="$productCategories"
                :clearable="true"
                :hide-label="true"
                :fixed-menu="true"
                :menu-width="320"
                search-placeholder="Search product category…"
            />
        @endif

        <div class="ft-order-product-top-actions">
            @if($canCreateCatalogProduct)
                <a href="{{ $createProductUrl }}" class="ft-order-product-action-link"><span>+</span> Create Product</a>
            @endif
        </div>
    </div>

    @if(count($rows))
        <div class="ft-order-selected-products">
            <div class="ft-order-selected-products-title">Selected products ({{ count($rows) }})</div>
            <div class="ft-order-selected-products-table">
                <div class="ft-order-selected-products-head">
                    <span>Product</span><span>Quantity</span><span>Notes</span><span>Action</span>
                </div>
                @foreach($rows as $index => $item)
                    @php
                        $detail = $selectedProductDetails->get((int) ($item['product_id'] ?? 0));
                        $itemImage = $detail?->productImageUrl();
                        $itemCode = (string) ($detail?->code ?? '');
                        $itemCategory = (string) ($detail?->parent?->name ?? ($item['category'] ?? ''));
                        $itemName = (string) ($detail?->name ?? ($item['product'] ?? 'Product'));
                        $quantityError = $errors->first("{$rowsProperty}.{$index}.quantity");
                        $notesError = $errors->first("{$rowsProperty}.{$index}.notes");
                        $productError = $errors->first("{$rowsProperty}.{$index}.product");
                    @endphp
                    <div class="ft-order-selected-product-row" wire:key="{{ $rowKeyPrefix }}-{{ $item['product_id'] ?? $index }}-{{ $index }}">
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
                            <input type="number" min="1" max="999999999" wire:model.live.debounce.300ms="{{ $rowsProperty }}.{{ $index }}.quantity" aria-label="Quantity for {{ $itemName }}">
                            <button type="button" wire:click="incrementCreateProductQuantity({{ $index }})" aria-label="Increase quantity">+</button>
                            @if($quantityError)<small class="validation-error">{{ $quantityError }}</small>@endif
                        </div>
                        <div class="ft-order-product-notes">
                            <input type="text" maxlength="2000" wire:model.blur="{{ $rowsProperty }}.{{ $index }}.notes" placeholder="Optional notes..." aria-label="Notes for {{ $itemName }}">
                            @if($notesError)<small class="validation-error">{{ $notesError }}</small>@endif
                            @if($productError)<small class="validation-error">{{ $productError }}</small>@endif
                        </div>
                        <button type="button" class="ft-order-selected-product-remove" wire:click="{{ $removeMethod }}({{ $index }})" aria-label="Remove {{ $itemName }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M8 10v7M12 10v7M16 10v7M6 7l1 14h10l1-14"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="ft-order-product-bottom-actions">
        <button type="button" wire:click="focusCreateProductSearch" @disabled(count($rows) >= 25)><span>+</span> Add Product</button>
    </div>

    <div class="ft-order-products-summary-row">
        <span>{{ count($rows) }} {{ \Illuminate\Support\Str::plural('product', count($rows)) }} <i>&bull;</i> {{ number_format($totalUnits) }} total units</span>
    </div>
</section>
