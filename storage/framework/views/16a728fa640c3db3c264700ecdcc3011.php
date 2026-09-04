<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'file' => null,
    'removeAction' => null,
    'title' => 'Selected file',
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
    'file' => null,
    'removeAction' => null,
    'title' => 'Selected file',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($file): ?>
    <?php
        $name = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'Selected file';
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $mime = '';
        $size = null;
        try { $mime = (string) $file->getMimeType(); } catch (\Throwable $e) { $mime = ''; }
        try { $size = (int) $file->getSize(); } catch (\Throwable $e) { $size = null; }
        $isImage = str_starts_with($mime, 'image/') || in_array($extension, ['jpg','jpeg','png','gif','webp','bmp'], true);
        $previewUrl = null;
        if ($isImage && method_exists($file, 'temporaryUrl')) {
            try { $previewUrl = $file->temporaryUrl(); } catch (\Throwable $e) { $previewUrl = null; }
        }
        $sizeLabel = $size !== null
            ? ($size >= 1048576 ? number_format($size / 1048576, 1).' MB' : number_format(max(1, $size) / 1024, 0).' KB')
            : null;
        $typeLabel = $extension !== '' ? strtoupper($extension) : 'FILE';
    ?>

    <div class="ft-finance-upload-preview">
        <div class="ft-finance-upload-thumb <?php echo e($previewUrl ? 'has-image' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previewUrl): ?>
                <img src="<?php echo e($previewUrl); ?>" alt="Preview of <?php echo e($name); ?>">
            <?php else: ?>
                <span><?php echo e($typeLabel); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="ft-finance-upload-meta">
            <strong><?php echo e($title); ?></strong>
            <span title="<?php echo e($name); ?>"><?php echo e($name); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sizeLabel): ?><small><?php echo e($typeLabel); ?> · <?php echo e($sizeLabel); ?></small><?php else: ?><small><?php echo e($typeLabel); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($removeAction): ?>
            <button type="button" class="ft-finance-upload-remove" wire:click="<?php echo e($removeAction); ?>" aria-label="Remove selected file">×</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/finance/upload-preview.blade.php ENDPATH**/ ?>