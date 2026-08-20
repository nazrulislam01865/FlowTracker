<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'model',
    'label',
    'hint',
    'accept' => null,
    'upload' => null,
    'current' => null,
    'clearAction' => null,
    'removeCurrentAction' => null,
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
    'model',
    'label',
    'hint',
    'accept' => null,
    'upload' => null,
    'current' => null,
    'clearAction' => null,
    'removeCurrentAction' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $inputId = 'ft-product-upload-'.str_replace(['.', '[', ']'], '-', $model);
    $selectedName = $upload?->getClientOriginalName();
    $currentName = data_get($current, 'label');
    $displayName = $selectedName ?: $currentName;
    $hasFile = filled($displayName);
?>
<div
    class="ft-friendly-upload"
    x-data="{
        dragging: false,
        previewUrl: null,
        localName: <?php echo \Illuminate\Support\Js::from($selectedName ?: '')->toHtml() ?>,
        setFile(file) {
            if (!file) return;
            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = URL.createObjectURL(file);
            this.localName = file.name || '';
        },
        clearLocal() {
            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = null;
            this.localName = '';
            if (this.$refs.input) this.$refs.input.value = '';
        },
        previewLocal() {
            if (!this.previewUrl) return;
            window.open(this.previewUrl, '_blank', 'noopener,noreferrer');
        }
    }"
>
    <div class="ft-friendly-upload-heading">
        <span><?php echo e($label); ?></span><em>Optional</em>
    </div>
    <label for="<?php echo e($inputId); ?>" class="ft-friendly-upload-drop" :class="dragging ? 'is-dragging' : ''"
        x-on:dragover.prevent="dragging=true"
        x-on:dragleave.prevent="dragging=false"
        x-on:drop.prevent="dragging=false; if($event.dataTransfer.files.length){ const dt=new DataTransfer(); dt.items.add($event.dataTransfer.files[0]); $refs.input.files=dt.files; setFile(dt.files[0]); $refs.input.dispatchEvent(new Event('change',{bubbles:true})); }">
        <input
            x-ref="input"
            id="<?php echo e($inputId); ?>"
            type="file"
            wire:model="<?php echo e($model); ?>"
            <?php if($accept): ?> accept="<?php echo e($accept); ?>" <?php endif; ?>
            x-on:change="setFile($event.target.files?.[0])"
        >
        <span class="ft-friendly-upload-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 16V4m0 0L8 8m4-4 4 4"/><path d="M5 14v5h14v-5"/></svg>
        </span>
        <span class="ft-friendly-upload-copy">
            <b x-text="localName || <?php echo \Illuminate\Support\Js::from($displayName ?: 'Drop file here or browse')->toHtml() ?>"><?php echo e($displayName ?: 'Drop file here or browse'); ?></b>
            <small><?php echo e($hint); ?></small>
        </span>
        <span class="ft-friendly-upload-button"><?php echo e($hasFile ? 'Replace' : 'Choose file'); ?></span>
    </label>

    <div class="ft-friendly-upload-meta">
        <span wire:loading wire:target="<?php echo e($model); ?>" class="ft-upload-progress">Uploading…</span>

        <template x-if="previewUrl">
            <button type="button" class="ft-upload-meta-action" x-on:click="previewLocal()">Preview</button>
        </template>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedName && $clearAction): ?>
            <button type="button" wire:click="<?php echo e($clearAction); ?>" x-on:click="clearLocal()">Remove</button>
        <?php elseif($currentName): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($current, 'url')): ?><a href="<?php echo e(data_get($current, 'url')); ?>" target="_blank" rel="noopener">Preview</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($current, 'download_url')): ?><a href="<?php echo e(data_get($current, 'download_url')); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($removeCurrentAction): ?><button type="button" class="is-danger" wire:click="<?php echo e($removeCurrentAction); ?>">Remove</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <span x-show="!localName">No file selected</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearAction): ?><button type="button" x-show="localName" wire:click="<?php echo e($clearAction); ?>" x-on:click="clearLocal()">Remove</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has($model)): ?>
        <b class="validation-error"><?php echo e($errors->first($model)); ?></b>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/file-upload.blade.php ENDPATH**/ ?>