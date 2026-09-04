<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'meta',
    'inputId',
    'index' => 0,
    'purchaseOrder' => false,
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
    'meta',
    'inputId',
    'index' => 0,
    'purchaseOrder' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-order-document-file-row">
    <span class="ft-order-document-file-icon <?php echo e($meta['icon_class']); ?>" aria-hidden="true"><?php echo e($meta['type_label']); ?></span>

    <span class="ft-order-document-file-copy">
        <strong title="<?php echo e($meta['name']); ?>"><?php echo e($meta['name']); ?></strong>
        <small><?php echo e($purchaseOrder ? 'Purchase order' : ucfirst($meta['extension'] ?: 'Document')); ?> · <?php echo e($meta['size_label']); ?></small>
    </span>

    <span class="ft-order-document-uploaded-badge">
        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="6.25" fill="currentColor" opacity=".14"/><path d="m5.4 8 1.65 1.65L10.8 5.9" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Uploaded
    </span>

    <span class="ft-order-document-file-actions">
        <button
            type="button"
            class="ft-order-document-file-action"
            data-local-file-action="preview"
            data-input-id="<?php echo e($inputId); ?>"
            data-file-name="<?php echo e($meta['name']); ?>"
            data-file-size="<?php echo e($meta['size']); ?>"
        >Preview</button>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchaseOrder): ?>
            <label class="ft-order-document-file-action" for="<?php echo e($inputId); ?>">Replace</label>
        <?php else: ?>
            <button
                type="button"
                class="ft-order-document-file-action"
                data-local-file-action="download"
                data-input-id="<?php echo e($inputId); ?>"
                data-file-name="<?php echo e($meta['name']); ?>"
                data-file-size="<?php echo e($meta['size']); ?>"
            >Download</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <button
            type="button"
            class="ft-order-document-file-action is-remove"
            <?php if($purchaseOrder): ?>
                wire:click="removeCreatePurchaseOrder"
                wire:target="removeCreatePurchaseOrder"
            <?php else: ?>
                wire:click="removeCreateAttachment(<?php echo e((int) $index); ?>)"
                wire:target="removeCreateAttachment"
            <?php endif; ?>
            wire:loading.attr="disabled"
        >Remove</button>
    </span>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/document-file-row.blade.php ENDPATH**/ ?>