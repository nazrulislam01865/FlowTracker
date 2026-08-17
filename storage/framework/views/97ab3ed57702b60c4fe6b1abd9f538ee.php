<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['preview' => []]));

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

foreach (array_filter((['preview' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $mainCount = (int) ($preview['main_categories'] ?? 0);
    $productCategoryCount = (int) ($preview['product_categories'] ?? 0);
    $subcategoryCount = (int) ($preview['subcategories'] ?? 0);
    $productCount = (int) ($preview['products'] ?? 0);
    $totalCategoryCount = (int) ($preview['total_categories'] ?? 0);
    $selectedLabels = collect($preview['selected_labels'] ?? []);
    $moreSelected = (int) ($preview['selected_labels_more'] ?? 0);
?>
<div
    class="ft-category-delete-layer"
    role="dialog"
    aria-modal="true"
    aria-labelledby="ft-category-delete-title"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'category-delete-confirmation'; ?>wire:key="category-delete-confirmation"
    wire:keydown.escape="closeCategoryDeleteConfirmation"
>
    <button type="button" class="ft-category-delete-backdrop" wire:click="closeCategoryDeleteConfirmation" aria-label="Close deletion confirmation"></button>

    <div class="ft-category-delete-card">
        <header>
            <div class="ft-category-delete-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4m0 4h.01M10.3 4.6 2.8 18a2 2 0 0 0 1.75 3h14.9a2 2 0 0 0 1.75-3L13.7 4.6a2 2 0 0 0-3.4 0Z"/></svg>
            </div>
            <div>
                <h2 id="ft-category-delete-title">Permanently delete categories?</h2>
                <p>This is a hard delete and cannot be undone. All child categories will also be permanently deleted.</p>
            </div>
            <button type="button" class="ft-category-delete-close" wire:click="closeCategoryDeleteConfirmation" aria-label="Close">×</button>
        </header>

        <div class="ft-category-delete-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedLabels->isNotEmpty()): ?>
                <div class="ft-category-delete-selection">
                    <span>You selected</span>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><b><?php echo e($label); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($moreSelected > 0): ?><b>+<?php echo e(number_format($moreSelected)); ?> more</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-category-delete-impact-title">Affected rows</div>
            <div class="ft-category-delete-impact-grid">
                <div><span>Main categories</span><strong><?php echo e(number_format($mainCount)); ?></strong></div>
                <div><span>Product categories</span><strong><?php echo e(number_format($productCategoryCount)); ?></strong></div>
                <div><span>Subcategories</span><strong><?php echo e(number_format($subcategoryCount)); ?></strong></div>
                <div class="is-products"><span>Products to unassign</span><strong><?php echo e(number_format($productCount)); ?></strong></div>
            </div>

            <div class="ft-category-delete-summary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 10v6m0-9h.01"/></svg>
                <p>
                    <strong><?php echo e(number_format($totalCategoryCount)); ?> <?php echo e(\Illuminate\Support\Str::plural('category row', $totalCategoryCount)); ?></strong> will be permanently removed.
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productCount > 0): ?>
                        The <?php echo e(number_format($productCount)); ?> affected <?php echo e(\Illuminate\Support\Str::plural('product', $productCount)); ?> will be kept, but their main category, product category and subcategory assignments will be cleared.
                    <?php else: ?>
                        No products will be changed.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        </div>

        <footer>
            <button type="button" class="ft-category-delete-cancel" wire:click="closeCategoryDeleteConfirmation" wire:loading.attr="disabled" wire:target="confirmCategoryHardDelete">Cancel</button>
            <button type="button" class="ft-category-delete-confirm" wire:click="confirmCategoryHardDelete" wire:loading.attr="disabled" wire:target="confirmCategoryHardDelete">
                <span wire:loading.remove wire:target="confirmCategoryHardDelete">Delete permanently</span>
                <span wire:loading wire:target="confirmCategoryHardDelete">Deleting…</span>
            </button>
        </footer>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/category-delete-modal.blade.php ENDPATH**/ ?>