<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'colspan' => 1,
    'name',
    'meta' => '',
    'openUrl',
    'downloadUrl',
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
    'colspan' => 1,
    'name',
    'meta' => '',
    'openUrl',
    'downloadUrl',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $extension = strtoupper((string) pathinfo((string) $name, PATHINFO_EXTENSION));
    $extension = $extension !== '' ? $extension : 'FILE';
?>
<tr class="ft-invoice-document-row">
    <td colspan="<?php echo e($colspan); ?>">
        <div class="ft-invoice-document">
            <span class="ft-invoice-document-type"><?php echo e(\Illuminate\Support\Str::limit($extension, 4, '')); ?></span>
            <div class="ft-invoice-document-copy">
                <strong><?php echo e($name); ?></strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($meta !== ''): ?>
                    <small><?php echo e($meta); ?></small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-invoice-document-actions">
                <a href="<?php echo e($openUrl); ?>" target="_blank" rel="noopener">Open</a>
                <a href="<?php echo e($downloadUrl); ?>">Download</a>
            </div>
        </div>
    </td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/finance/attachment-row.blade.php ENDPATH**/ ?>