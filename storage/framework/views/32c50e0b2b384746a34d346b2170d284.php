<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null, 'saveLabel' => 'Apply', 'saveAction']));

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

foreach (array_filter((['title', 'subtitle' => null, 'saveLabel' => 'Apply', 'saveAction']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="ft-product-bulk-modal-layer" role="dialog" aria-modal="true" aria-label="<?php echo e($title); ?>">
    <button type="button" class="ft-product-bulk-modal-backdrop" wire:click="closeProductBulkPanel" aria-label="Close"></button>
    <div class="ft-product-bulk-modal-card">
        <header>
            <div>
                <h2><?php echo e($title); ?></h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subtitle): ?><p><?php echo e($subtitle); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button type="button" class="ft-product-bulk-modal-close" wire:click="closeProductBulkPanel" aria-label="Close">×</button>
        </header>
        <div class="ft-product-bulk-modal-body"><?php echo e($slot); ?></div>
        <footer>
            <button type="button" class="ft-product-page-btn is-secondary" wire:click="closeProductBulkPanel">Cancel</button>
            <button type="button" class="ft-product-page-btn is-primary" wire:click="<?php echo e($saveAction); ?>" wire:loading.attr="disabled"><?php echo e($saveLabel); ?></button>
        </footer>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/bulk-modal.blade.php ENDPATH**/ ?>