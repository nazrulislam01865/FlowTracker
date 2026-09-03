<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'inputId',
    'model',
    'multiple' => false,
    'headline' => 'Drop files here',
    'browseText' => 'browse files',
    'helper' => null,
    'accept' => null,
    'progressLabel' => 'Uploading files...',
    'progressAriaLabel' => 'Attachment upload progress',
    'variant' => 'default',
    'browseButton' => null,
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
    'inputId',
    'model',
    'multiple' => false,
    'headline' => 'Drop files here',
    'browseText' => 'browse files',
    'helper' => null,
    'accept' => null,
    'progressLabel' => 'Uploading files...',
    'progressAriaLabel' => 'Attachment upload progress',
    'variant' => 'default',
    'browseButton' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $accept = $accept ?: \App\Support\AttachmentUpload::accept();
    $helper = $helper ?: \App\Support\AttachmentUpload::helperText(20);
?>

<div
    <?php echo e($attributes->class(['ft-create-attachment-uploader'])); ?>

    x-data="{
        uploading: false,
        progress: 0,
        hideTimer: null,
        startUpload() {
            if (this.hideTimer) window.clearTimeout(this.hideTimer);
            this.uploading = true;
            this.progress = 0;
        },
        updateUpload(event) {
            this.uploading = true;
            this.progress = Math.max(0, Math.min(100, Number(event.detail?.progress) || 0));
        },
        finishUpload() {
            this.progress = 100;
            this.hideTimer = window.setTimeout(() => {
                this.uploading = false;
                this.progress = 0;
            }, 350);
        },
        resetUpload() {
            if (this.hideTimer) window.clearTimeout(this.hideTimer);
            this.uploading = false;
            this.progress = 0;
        }
    }"
    x-on:livewire-upload-start="startUpload()"
    x-on:livewire-upload-progress="updateUpload($event)"
    x-on:livewire-upload-finish="finishUpload()"
    x-on:livewire-upload-error="resetUpload()"
    x-on:livewire-upload-cancel="resetUpload()"
>
    <label
        class="ft-create-attachment-dropzone ft-livewire-upload-zone <?php echo e($variant === 'order-document' ? 'ft-create-attachment-dropzone--order-document' : ''); ?>"
        data-file-dropzone
        for="<?php echo e($inputId); ?>"
    >
        <span class="ft-create-attachment-dropzone-icon" aria-hidden="true">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'order-document'): ?>
                <svg viewBox="0 0 24 24" fill="none"><path d="M7.7 18.5H6.5a4 4 0 0 1-.65-7.95A6.5 6.5 0 0 1 18.4 8.9a4.75 4.75 0 0 1-.9 9.6h-1.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M12 18V10.5m0 0-3 3m3-3 3 3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?php else: ?>
                ⇧
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
        <span class="ft-create-attachment-dropzone-copy">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'order-document'): ?>
                <strong><?php echo e($headline); ?> <span>or <b><?php echo e($browseText); ?></b></span></strong>
                <small data-drop-status><?php echo e($helper); ?></small>
            <?php else: ?>
                <strong><?php echo e($headline); ?></strong>
                <span class="ft-create-attachment-drop-or">or <b><?php echo e($browseText); ?></b></span>
                <small data-drop-status><?php echo e($helper); ?></small>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'order-document' && filled($browseButton)): ?>
            <span class="ft-create-attachment-browse-button" aria-hidden="true"><?php echo e($browseButton); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <input
            id="<?php echo e($inputId); ?>"
            type="file"
            wire:model="<?php echo e($model); ?>"
            <?php if($multiple): ?> multiple <?php endif; ?>
            accept="<?php echo e($accept); ?>"
            hidden
        >
    </label>

    <div class="ft-create-attachment-progress" x-cloak x-show="uploading" x-transition.opacity.duration.120ms>
        <div class="ft-create-attachment-progress-meta">
            <span><?php echo e($progressLabel); ?></span>
            <b x-text="`${Math.round(progress)}%`">0%</b>
        </div>
        <div
            class="ft-create-attachment-progress-track"
            role="progressbar"
            aria-label="<?php echo e($progressAriaLabel); ?>"
            aria-valuemin="0"
            aria-valuemax="100"
            x-bind:aria-valuenow="Math.round(progress)"
        >
            <span x-bind:style="`width: ${progress}%`"></span>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/create-attachment-dropzone.blade.php ENDPATH**/ ?>