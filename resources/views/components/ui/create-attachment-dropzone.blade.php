@props([
    'inputId',
    'model',
    'multiple' => false,
    'headline' => 'Drop files here',
    'browseText' => 'browse files',
    'helper' => 'PDF, Office files, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB per file',
    'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai,.eps,.esp',
    'progressLabel' => 'Uploading files...',
    'progressAriaLabel' => 'Attachment upload progress',
])

<div
    {{ $attributes->class(['ft-create-attachment-uploader']) }}
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
    <label class="ft-create-attachment-dropzone ft-livewire-upload-zone" data-file-dropzone for="{{ $inputId }}">
        <span class="ft-create-attachment-dropzone-icon" aria-hidden="true">⇧</span>
        <span class="ft-create-attachment-dropzone-copy">
            <strong>{{ $headline }}</strong>
            <span class="ft-create-attachment-drop-or">or <b>{{ $browseText }}</b></span>
            <small data-drop-status>{{ $helper }}</small>
        </span>
        <input
            id="{{ $inputId }}"
            type="file"
            wire:model="{{ $model }}"
            @if($multiple) multiple @endif
            accept="{{ $accept }}"
            hidden
        >
    </label>

    <div class="ft-create-attachment-progress" x-cloak x-show="uploading" x-transition.opacity.duration.120ms>
        <div class="ft-create-attachment-progress-meta">
            <span>{{ $progressLabel }}</span>
            <b x-text="`${Math.round(progress)}%`">0%</b>
        </div>
        <div
            class="ft-create-attachment-progress-track"
            role="progressbar"
            aria-label="{{ $progressAriaLabel }}"
            aria-valuemin="0"
            aria-valuemax="100"
            x-bind:aria-valuenow="Math.round(progress)"
        >
            <span x-bind:style="`width: ${progress}%`"></span>
        </div>
    </div>
</div>
