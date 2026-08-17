@props([
    'wireKey',
    'searchModel',
    'searchValue' => '',
    'searchResults' => collect(),
    'resultTotal' => 0,
    'showAllMethod',
    'selectMethod',
    'selectedProduct' => null,
    'categoryValue' => '',
    'quantityModel',
    'unitPriceModel',
    'currencySymbol' => '$',
    'closeMethod',
    'saveMethod',
    'selectedErrorKey',
    'quantityErrorKey',
    'unitPriceErrorKey',
])

<div class="ft-order-detail-add-product" wire:key="{{ $wireKey }}" x-data="{ resultsOpen: true }">
    <div class="ft-order-detail-add-product-head">
        <div>
            <strong>Add product</strong>
            <span>Search the Product master, select a product, then enter quantity and unit price.</span>
        </div>
        <button type="button" class="ft-order-detail-add-product-close" wire:click="{{ $closeMethod }}" aria-label="Close add product">×</button>
    </div>

    <div class="ft-order-product-search-label">Search product</div>
    <div class="ft-order-product-search-host ft-order-detail-product-search" x-on:click.outside="resultsOpen = false">
        <div class="ft-order-product-search-input" :class="resultsOpen ? 'is-open' : ''">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input
                type="search"
                wire:model.live.debounce.220ms="{{ $searchModel }}"
                x-on:focus="resultsOpen = true"
                x-on:keydown.escape="resultsOpen = false"
                placeholder="Search product name, product code or reference code"
                autocomplete="off"
                aria-label="Search product"
            >
            @if(trim((string) $searchValue) !== '')
                <span class="ft-order-product-search-tools">
                    <button type="button" class="ft-order-product-search-clear" wire:click="$set('{{ $searchModel }}', '')" x-on:click="resultsOpen = true" aria-label="Clear product search">&times;</button>
                </span>
            @endif
        </div>

        <div class="ft-order-product-results" x-cloak x-show="resultsOpen" x-transition.origin.top>
            <div class="ft-order-product-results-head">
                <span>Top matches <b>{{ number_format((int) $resultTotal) }} {{ \Illuminate\Support\Str::plural('result', (int) $resultTotal) }}</b></span>
                @if($resultTotal > $searchResults->count())
                    <button type="button" wire:click="{{ $showAllMethod }}">View all results <span>&nearr;</span></button>
                @endif
            </div>
            <div class="ft-order-product-result-list">
                @forelse($searchResults as $product)
                    @php
                        $isSelected = (int) ($selectedProduct?->id ?? 0) === (int) $product->id;
                        $resultImageUrl = $product->productImageUrl();
                        $resultReferenceCode = $product->productReferenceCode();
                        $resultDisplayCode = $product->productDisplayCode();
                        $resultMainCategory = $product->productMainCategory();
                        $resultProductCategory = trim((string) ($product->parent?->name ?? ''));
                        $resultSubCategory = trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category') ?: $product->productCatalogSummary()));
                        $resultClassification = collect([$resultMainCategory, $resultProductCategory, $resultSubCategory])->filter()->unique()->values();
                    @endphp
                    <div class="ft-order-product-result {{ $isSelected ? 'is-selected' : '' }}">
                        <span class="ft-order-product-thumb">
                            @if($resultImageUrl)
                                <img src="{{ $resultImageUrl }}" alt="">
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                            @endif
                        </span>
                        <span class="ft-order-product-result-copy">
                            <strong>{{ $product->name }}</strong>
                            <span class="ft-order-product-code-line">Product code: {{ $resultDisplayCode }} <i>&bull;</i> Ref: {{ $resultReferenceCode ?: '—' }}</span>
                            @if($resultClassification->isNotEmpty())
                                <small class="ft-order-product-classification">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                                    @foreach($resultClassification as $part)<span>{{ $part }}</span>@if(!$loop->last)<i>&rsaquo;</i>@endif @endforeach
                                </small>
                            @endif
                        </span>
                        <button type="button" class="ft-order-product-select-button {{ $isSelected ? 'is-selected' : '' }}" wire:click="{{ $selectMethod }}({{ $product->id }})" x-on:click="resultsOpen = false">{{ $isSelected ? 'Selected' : 'Select' }}</button>
                    </div>
                @empty
                    <div class="ft-order-product-no-results"><strong>No products found</strong><span>Try another product name, product code or reference code.</span></div>
                @endforelse
            </div>
        </div>
    </div>

    @if($selectedProduct)
        <div class="ft-order-detail-product-fields">
            <div class="ft-order-detail-product-field is-product">
                <label>Selected product</label>
                <div class="ft-order-detail-selected-product">
                    @if($selectedProduct->productImageUrl())
                        <img src="{{ $selectedProduct->productImageUrl() }}" alt="">
                    @endif
                    <span><strong>{{ $selectedProduct->name }}</strong><small>{{ $selectedProduct->productDisplayCode() }}</small></span>
                </div>
                @if($errors->has($selectedErrorKey))<span class="validation-error">{{ $errors->first($selectedErrorKey) }}</span>@endif
            </div>
            <div class="ft-order-detail-product-field">
                <label>Product category</label>
                <input type="text" value="{{ $categoryValue }}" readonly>
            </div>
            <div class="ft-order-detail-product-field">
                <label>Quantity *</label>
                <input type="number" min="1" step="1" wire:model="{{ $quantityModel }}">
                @if($errors->has($quantityErrorKey))<span class="validation-error">{{ $errors->first($quantityErrorKey) }}</span>@endif
            </div>
            <div class="ft-order-detail-product-field">
                <label>Unit price *</label>
                <div class="ft-order-detail-price-field"><span>{{ $currencySymbol }}</span><input type="number" min="0" step="0.01" wire:model="{{ $unitPriceModel }}"></div>
                @if($errors->has($unitPriceErrorKey))<span class="validation-error">{{ $errors->first($unitPriceErrorKey) }}</span>@endif
            </div>
        </div>
    @else
        @if($errors->has($selectedErrorKey))<div class="validation-error ft-order-detail-product-selection-error">{{ $errors->first($selectedErrorKey) }}</div>@endif
    @endif

    <div class="ft-order-detail-add-product-actions">
        <button type="button" class="ft-outline-btn" wire:click="{{ $closeMethod }}">Cancel</button>
        <button type="button" class="ft-new-job-btn" wire:click="{{ $saveMethod }}" wire:loading.attr="disabled" wire:target="{{ $saveMethod }}" @disabled(!$selectedProduct)>Add product</button>
    </div>
</div>
