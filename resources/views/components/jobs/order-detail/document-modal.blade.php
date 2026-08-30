@props(['job', 'task', 'availableDocuments' => collect(), 'source' => 'upload', 'upload' => null, 'existingDocumentId' => null, 'context' => []])
@php
    $canUpload = (bool) ($context['canUploadDocument'] ?? false);
    $canLink = (bool) ($context['canLinkDocument'] ?? false);
    $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
    $automationKey = $workflowActions->automationKey($task);
    $prototypeUpload = in_array($automationKey, ['NEW_UPLOAD_PO', 'ART_PREPARE_UPLOAD', 'ART_SAMPLE_APPROVAL'], true);
    $hasExistingEvidence = $task->relationLoaded('documents') ? $task->documents->isNotEmpty() : false;

    $prototypeConfig = match ($automationKey) {
        'NEW_UPLOAD_PO' => [
            'title' => 'Upload Purchase Order',
            'label' => 'Purchase order file',
            'copy' => 'Upload the client purchase order to begin processing this Order.',
            'hint' => 'PDF, Office files, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB',
            'button' => 'Upload Purchase Order',
        ],
        'ART_PREPARE_UPLOAD' => [
            'title' => $hasExistingEvidence ? 'Upload Revised Artwork' : 'Upload Artwork',
            'label' => $hasExistingEvidence ? 'Revised artwork proof' : 'Artwork proof',
            'copy' => $hasExistingEvidence
                ? 'Upload the corrected artwork version. The previous version remains in Order history.'
                : 'Upload the artwork proof for internal review.',
            'hint' => 'PDF, AI, EPS, ESP, JPG or PNG · Max 20 MB',
            'button' => $hasExistingEvidence ? 'Upload Revised Artwork' : 'Upload Artwork',
        ],
        'ART_SAMPLE_APPROVAL' => [
            'title' => 'Upload Sample Approval',
            'label' => 'Signed sample approval',
            'copy' => 'Attach the client sample/swatch approval to continue to Production.',
            'hint' => 'PDF, Office files, JPG or PNG · Max 20 MB',
            'button' => 'Upload Sample Approval',
        ],
        default => [
            'title' => 'Add document to task',
            'label' => 'Task document',
            'copy' => 'Upload a new file or link an existing client document.',
            'hint' => 'PDF, Office, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB',
            'button' => 'Add document',
        ],
    };
    $selectedUploadName = $upload?->getClientOriginalName();
    $selectedUploadType = $selectedUploadName
        ? (strtoupper((string) pathinfo($selectedUploadName, PATHINFO_EXTENSION)) ?: 'FILE')
        : null;
    $selectedUploadSize = $upload
        ? ($upload->getSize() >= 1048576
            ? number_format($upload->getSize() / 1048576, 1).' MB'
            : number_format(max(1, (int) ceil($upload->getSize() / 1024))).' KB')
        : null;
@endphp
<div class="ft-order-task-document-modal-backdrop" wire:key="order-task-document-modal-{{ $task->id }}" wire:click.self="closeOverviewTaskDocumentModal">
    <section class="ft-order-task-document-modal ft-order-attachment-upload-modal {{ $prototypeUpload ? 'ft-order-prototype-upload-modal' : '' }}" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="order-task-document-modal-title">
        <header class="ft-order-task-document-modal-head">
            <div>
                <h2 id="order-task-document-modal-title">{{ $prototypeConfig['title'] }}</h2>
                <p>{{ $prototypeConfig['copy'] }}</p>
            </div>
            <button type="button" wire:click="closeOverviewTaskDocumentModal" aria-label="Close">×</button>
        </header>
        <div class="ft-order-task-document-modal-body">
            @unless($prototypeUpload)
                <div class="ft-order-task-document-target"><span class="ft-order-task-document-target-icon">▣</span><div><small>ATTACHING TO</small><strong>{{ $task->title }}</strong><span>{{ $task->task_number ?: 'TASK-'.str_pad((string) $task->id, 5, '0', STR_PAD_LEFT) }} · {{ $task->phase?->name ?? 'Order Taskflow' }}</span><span><b>Order Reference:</b> {{ $job->order_number ?: '—' }}</span></div><em>Task selected</em></div>
            @endunless

            @if(!$prototypeUpload || !$canUpload)
                <div class="ft-order-task-document-source-tabs">
                    <button type="button" class="{{ $source === 'upload' ? 'active' : '' }}" wire:click="setOverviewTaskDocumentSource('upload')" @disabled(!$canUpload)>Upload new</button>
                    <button type="button" class="{{ $source === 'existing' ? 'active' : '' }}" wire:click="setOverviewTaskDocumentSource('existing')" @disabled(!$canLink)>Choose existing</button>
                </div>
            @endif

            @if($source === 'upload' && $canUpload)
                {{-- CHANGED 2026-08-24: local Livewire upload progress feedback. --}}
                <div
                    class="ft-order-popup-field"
                    x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true; progress = 0"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    x-on:livewire-upload-finish="progress = 100; window.setTimeout(() => { uploading = false; progress = 0 }, 250)"
                    x-on:livewire-upload-error="uploading = false; progress = 0"
                    x-on:livewire-upload-cancel="uploading = false; progress = 0"
                >
                    @if($upload)
                        <div class="ft-order-attachment-selected-count">1 file selected</div>
                        <div class="ft-order-attachment-selected-file">
                            <span class="ft-order-attachment-selected-check" aria-hidden="true">✓</span>
                            <span class="ft-order-attachment-selected-copy">
                                <strong class="{{ $prototypeUpload ? 'ft-prototype-selected-file-name' : '' }}" title="{{ $selectedUploadName }}">{{ $selectedUploadName }}</strong>
                                <small>{{ $selectedUploadType }} · {{ $selectedUploadSize }} · Ready to upload</small>
                            </span>
                            <button type="button" wire:click="$set('overviewTaskDocumentUpload', null)" wire:loading.attr="disabled" wire:target="overviewTaskDocumentUpload">Remove</button>
                        </div>
                    @else
                        <div class="{{ $prototypeUpload ? 'ft-prototype-upload-label' : 'ft-order-attachment-field-label' }}">{{ $prototypeUpload ? $prototypeConfig['label'] : 'File attachment' }}</div>
                    @endif

                    <label class="ft-order-task-document-dropzone ft-order-attachment-dropzone {{ $upload ? 'is-compact' : '' }}">
                        <input type="file" wire:model="overviewTaskDocumentUpload" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai,.eps,.esp" aria-label="{{ $upload ? 'Add another file' : 'Choose a file to upload' }}" title="Choose file">
                        <svg class="ft-order-attachment-upload-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 16l-4-4-4 4M12 12v9M20.4 17.5A5 5 0 0 0 18 8.2 7 7 0 0 0 4.3 10.8 4.5 4.5 0 0 0 5.5 19H7"/></svg>
                        @if($upload)
                            <strong>Add another file</strong>
                            <b>Drag &amp; drop or <span>browse</span></b>
                        @else
                            <strong>Drag &amp; drop a file here</strong>
                            <b>or choose from your computer</b>
                            <span class="ft-order-attachment-browse">Browse files</span>
                        @endif
                        <small>{{ $prototypeConfig['hint'] }}</small>
                    </label>

                    {{-- CHANGED: shown only while Livewire is transferring the selected file. --}}
                    <div
                        class="ft-prototype-upload-progress"
                        x-cloak
                        x-show="uploading"
                        x-transition.opacity.duration.120ms
                    >
                        <div class="ft-prototype-upload-progress-meta">
                            <span>Uploading file...</span>

                            <b x-text="`${progress}%`">
                                0%
                            </b>
                        </div>

                        <div
                            class="ft-prototype-upload-progress-track"
                            role="progressbar"
                            aria-label="File upload progress"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            x-bind:aria-valuenow="progress"
                        >
                            <span
                                x-bind:style="`width: ${progress}%`"
                            ></span>
                        </div>
                    </div>

                    @error('overviewTaskDocumentUpload')
                        <p class="validation-error">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            @endif

            @unless($prototypeUpload)
                <label class="ft-order-task-document-note"><span>Document note (optional)</span><input type="text" wire:model="overviewTaskDocumentNote" placeholder="Add a short note...">@error('overviewTaskDocumentNote')<p class="validation-error">{{ $message }}</p>@enderror</label>
                <div class="ft-order-task-document-info">This document remains linked to this task and is available from the task and Documents archive.</div>
            @else
                <div class="ft-prototype-upload-meta"><span>Task</span><b>{{ $task->title }}</b><span>Order</span><b>{{ $job->displayOrderNumber() }}</b></div>
            @endunless
        </div>
        <footer class="ft-order-task-document-modal-actions">
            <button type="button" class="secondary" wire:click="closeOverviewTaskDocumentModal">Cancel</button>
            <button
                type="button"
                class="primary"
                wire:click="saveOverviewTaskDocument"
                wire:loading.attr="disabled"
                wire:target="saveOverviewTaskDocument,overviewTaskDocumentUpload"
                @disabled($source === 'upload' ? !$upload : !$existingDocumentId)
            >
                <span
                    wire:loading.remove
                    wire:target="saveOverviewTaskDocument"
                >
                    {{
                        $prototypeUpload
                            ? $prototypeConfig['button']
                            : ($upload ? 'Add 1 document' : 'Add document')
                    }}
                </span>

                <span
                    wire:loading
                    wire:target="saveOverviewTaskDocument"
                >
                    Uploading...
                </span>
            </button>
        </footer>
    </section>
</div>
