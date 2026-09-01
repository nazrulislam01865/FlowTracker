<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'document' => null, 'emptyLabel' => 'No file']));

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

foreach (array_filter((['title', 'document' => null, 'emptyLabel' => 'No file']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div <?php echo e($attributes->class(['ft-product-document-card'])); ?>>
    <div class="ft-product-document-card-title"><?php echo e($title); ?></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($document): ?>
        <div class="ft-product-document-card-row">
            <span class="ft-product-document-icon <?php echo e(($document['kind'] ?? '') === 'template' ? 'is-template' : ''); ?>"><?php echo e(($document['kind'] ?? '') === 'template' ? 'AI' : 'PDF'); ?></span>
            <div class="ft-product-document-card-copy">
                <strong title="<?php echo e($document['label']); ?>"><?php echo e(\Illuminate\Support\Str::limit($document['label'], 34)); ?></strong>
                <small><?php echo e(strtoupper(pathinfo($document['label'], PATHINFO_EXTENSION) ?: 'FILE')); ?></small>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($document['url'] ?? null): ?><a href="<?php echo e($document['url']); ?>" target="_blank" rel="noopener">Open</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($document['download_url'] ?? null): ?><a href="<?php echo e($document['download_url']); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="ft-product-document-empty"><?php echo e($emptyLabel); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/product-document-card.blade.php ENDPATH**/ ?>