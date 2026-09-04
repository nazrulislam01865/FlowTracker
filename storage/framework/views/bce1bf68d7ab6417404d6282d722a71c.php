<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'count' => 0,
    'matchingTotal' => 0,
    'allMatchingSelected' => false,
    'canEdit' => false,
    'canDelete' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'count' => 0,
    'matchingTotal' => 0,
    'allMatchingSelected' => false,
    'canEdit' => false,
    'canDelete' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-product-bulk-bar" x-data="{ statusOpen: false, moreOpen: false }">
    <div class="ft-product-bulk-summary">
        <strong><?php echo e(number_format($count)); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $count)); ?> selected</strong>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$allMatchingSelected && $matchingTotal > $count): ?>
            <button type="button" wire:click="selectAllFilteredProducts">Select all <?php echo e(number_format($matchingTotal)); ?> products</button>
        <?php elseif($allMatchingSelected): ?>
            <span>All matching products selected</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-product-bulk-actions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
            <div class="ft-product-bulk-menu-wrap">
                <button type="button" class="ft-product-bulk-btn" x-on:click="statusOpen = !statusOpen; moreOpen = false" :aria-expanded="statusOpen.toString()">
                    Set status
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
                </button>
                <div class="ft-product-bulk-menu" x-cloak x-show="statusOpen" x-on:click.outside="statusOpen=false">
                    <button type="button" wire:click="bulkSetProductStatus('active')" x-on:click="statusOpen=false"><span class="ft-bulk-dot is-active"></span>Active</button>
                    <button type="button" wire:click="bulkSetProductStatus('inactive')" x-on:click="statusOpen=false"><span class="ft-bulk-dot is-inactive"></span>Inactive</button>
                </div>
            </div>
            <button type="button" class="ft-product-bulk-btn is-bulk-secondary is-supplier" wire:click="openProductSupplierAssignment">Assign supplier</button>
            <button type="button" class="ft-product-bulk-btn is-bulk-secondary" wire:click="openProductBulkPanel('clients')">Assign clients</button>
            <button type="button" class="ft-product-bulk-btn is-bulk-secondary" wire:click="openProductBulkPanel('category')">Change category</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <button type="button" class="ft-product-bulk-btn is-bulk-secondary" wire:click="exportSelectedProducts">Export</button>

        <div class="ft-product-bulk-menu-wrap">
            <button type="button" class="ft-product-bulk-btn" x-on:click="moreOpen = !moreOpen; statusOpen = false" :aria-expanded="moreOpen.toString()">
                More
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
            </button>
            <div class="ft-product-bulk-menu ft-product-bulk-more-menu" x-cloak x-show="moreOpen" x-on:click.outside="moreOpen=false">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
                    <button type="button" class="ft-product-bulk-mobile-only" wire:click="openProductSupplierAssignment" x-on:click="moreOpen=false">Assign supplier</button>
                    <button type="button" class="ft-product-bulk-mobile-only" wire:click="openProductBulkPanel('clients')" x-on:click="moreOpen=false">Assign clients</button>
                    <button type="button" class="ft-product-bulk-mobile-only" wire:click="openProductBulkPanel('category')" x-on:click="moreOpen=false">Change category</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" class="ft-product-bulk-mobile-only" wire:click="exportSelectedProducts" x-on:click="moreOpen=false">Export</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                    <button type="button" class="is-danger" wire:click="bulkDeleteProducts" wire:confirm="Delete the selected products? This cannot be undone." x-on:click="moreOpen=false">Delete products</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <button type="button" class="ft-product-bulk-clear" wire:click="clearProductSelection">× Clear selection</button>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/bulk-actions.blade.php ENDPATH**/ ?>