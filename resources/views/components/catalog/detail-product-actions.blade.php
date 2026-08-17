@props([
    'itemId' => null,
    'canDelete' => false,
    'removeMethod' => '',
    'confirmText' => 'Remove this product?',
    'disabled' => false,
    'disabledTitle' => 'This product cannot be removed.',
])

@if($itemId && $canDelete && $removeMethod !== '')
    @php($target = $removeMethod.'('.(int) $itemId.')')
    <div class="ft-order-product-row-menu" x-on:click.outside="actionOpen = false">
        <button type="button" class="ft-order-product-kebab" x-on:click.stop="actionOpen = !actionOpen" :aria-expanded="actionOpen.toString()" aria-label="Product actions">⋮</button>
        <div class="ft-order-product-menu-popover" x-cloak x-show="actionOpen" x-transition.opacity>
            <button
                type="button"
                wire:click.stop="{{ $target }}"
                wire:confirm="{{ $confirmText }}"
                wire:loading.attr="disabled"
                wire:target="{{ $target }}"
                x-on:click="actionOpen = false"
                :disabled="categorySaving || productSaving || quantitySaving || priceSaving || notesSaving || @js($disabled)"
                @if($disabled) title="{{ $disabledTitle }}" @endif
            >Remove product</button>
        </div>
    </div>
@else
    <span class="ft-order-product-action-placeholder">—</span>
@endif
