@props([
    'job',
    'availableDocuments'=>collect(),
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
])
@php
    $required = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $receivedRequired = $required->where('complete', true)->count();
    $missingRequired = $required->where('complete', false)->count();
    $requiredProgress = $required->count() ? (int) round(($receivedRequired / $required->count()) * 100) : 100;
    $unlinkedDocs = $job->documents->whereNull('task_id')->values();
    $canUploadDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','create');
    $canLinkDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','link');
    $canDeleteDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','delete');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $lastUploadedDocument = $lastJobDocumentUploadId
        && (int) $lastJobDocumentTaskId === (int) $jobDocumentTaskId
        ? $job->documents->firstWhere('id', (int) $lastJobDocumentUploadId)
        : null;
    $pendingUploadName = $jobRequiredDocumentUpload && method_exists($jobRequiredDocumentUpload, 'getClientOriginalName')
        ? $jobRequiredDocumentUpload->getClientOriginalName()
        : '';
    $pendingUploadSize = $jobRequiredDocumentUpload && method_exists($jobRequiredDocumentUpload, 'getSize')
        ? (int) $jobRequiredDocumentUpload->getSize()
        : 0;
    $uploadError = $errors->first('jobRequiredDocumentUpload')
        ?: ($pendingUploadName ? $errors->first('jobDocumentTaskId') : '');
    $uploadInitialState = $uploadError ? 'error' : ($lastUploadedDocument ? 'success' : 'idle');
@endphp
<div class="ft-documents-detail-section ft-exact-documents">
    <?php if (session('success')): ?><div class="flash">{{ session('success') }}</div><?php endif; ?>

    <div class="ft-detail-doc-full">
        <main>
            <div class="ft-section-title-row ft-doc-title-row ft-doc-ux-title">
                <div>
                    <h2>Documents</h2>
                    <p>Keep required files, document links, task attachments and existing client documents together.</p>
                </div>
                <div class="ft-doc-completion-copy">
                    <strong>{{ $receivedRequired }}/{{ $required->count() }}</strong>
                    <span>requirements satisfied</span>
                </div>
            </div>

            <div class="ft-doc-summary-strip" aria-label="Document summary">
                <div class="ft-doc-summary-item">
                    <span class="ft-doc-summary-icon blue">▣</span>
                    <div><small>Total files</small><b>{{ $job->documents->count() }}</b></div>
                </div>
                <div class="ft-doc-summary-item">
                    <span class="ft-doc-summary-icon neutral">✓</span>
                    <div><small>Required</small><b>{{ $required->count() }}</b></div>
                </div>
                <div class="ft-doc-summary-item {{ $missingRequired ? 'needs-action' : '' }}">
                    <span class="ft-doc-summary-icon amber">!</span>
                    <div><small>Needs action</small><b>{{ $missingRequired }}</b></div>
                </div>
                <div class="ft-doc-summary-item">
                    <span class="ft-doc-summary-icon green">✓</span>
                    <div><small>Satisfied</small><b>{{ $receivedRequired }}</b></div>
                </div>
            </div>

            <div class="ft-doc-required-progress" aria-label="Required submission progress">
                <div><span>Required submission progress</span><b>{{ $requiredProgress }}%</b></div>
                <span class="ft-doc-progress-track"><i style="width: {{ $requiredProgress }}%"></i></span>
            </div>

            <section id="job-document-upload-panel" class="ft-detail-card ft-upload-panel ft-doc-upload-card ft-prototype-document-uploader">
                <div class="ft-proto-doc-heading">
                    <span class="ft-proto-doc-heading-icon" aria-hidden="true">＋</span>
                    <div>
                        <h3>Add a required document</h3>
                        <p>Choose the document type, then upload a file, select an existing file, or use the task Add link action.</p>
                    </div>
                </div>

                <?php if ($required->isNotEmpty()): ?>
                    <div class="ft-proto-doc-field">
                        <label for="jobDocumentRequirement-{{ $job->id }}">Document type</label>
                        <select id="jobDocumentRequirement-{{ $job->id }}" wire:model.live="jobDocumentTaskId" class="ft-proto-doc-select">
                            <option value="">Select document type</option>
                            @foreach($required as $item)
                                <option value="{{ $item->task->id }}">{{ $item->name }} · {{ $item->phase->name }} · {{ $item->task->title }}</option>
                            @endforeach
                        </select>
                        <p>FlowTrack links the file to the selected phase and task automatically.</p>
                        <?php if ($errors->has('jobDocumentTaskId')): ?><div class="validation-error ft-field-validation" role="alert">{{ $errors->first('jobDocumentTaskId') }}</div><?php endif; ?>
                    </div>

                    <div class="ft-proto-doc-add-label">Add document</div>
                    <div class="ft-proto-doc-mode" role="tablist" aria-label="Document source">
                        <?php if ($canUploadDocument): ?>
                            <button type="button" role="tab" aria-selected="{{ ! $showDocumentPicker ? 'true' : 'false' }}" class="{{ ! $showDocumentPicker ? 'is-active' : '' }}" wire:click="setDocumentUploadMode('upload')">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/></svg>
                                <span>Upload new</span>
                            </button>
                        <?php endif; ?>
                        <?php if ($canLinkDocument): ?>
                            <button type="button" role="tab" aria-selected="{{ ($showDocumentPicker || ! $canUploadDocument) ? 'true' : 'false' }}" class="{{ ($showDocumentPicker || ! $canUploadDocument) ? 'is-active' : '' }}" wire:click="setDocumentUploadMode('existing')">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7zM14 3v6h5"/></svg>
                                <span>Choose existing</span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($canUploadDocument && ! $showDocumentPicker): ?>
                        <div
                            class="ft-proto-upload-shell"
                            wire:key="job-required-upload-{{ $job->id }}-{{ $uploadInitialState }}-{{ (int) ($lastUploadedDocument?->id ?? 0) }}-{{ md5((string) $uploadError) }}"
                            data-file-dropzone
                            x-data="{
                                state: @js($uploadInitialState),
                                progress: 0,
                                fileName: @js($lastUploadedDocument?->name ?: $pendingUploadName),
                                fileSize: @js($lastUploadedDocument?->size ?: $pendingUploadSize),
                                errorText: @js($uploadError ?: 'The connection was interrupted. Your document was not added.'),
                                selectedFile: null,
                                uploadToken: 0,
                                prettySize(bytes) {
                                    const value = Number(bytes || 0);
                                    if (!value) return '';
                                    if (value < 1024 * 1024) return `${Math.max(1, Math.round(value / 1024))} KB`;
                                    return `${(value / (1024 * 1024)).toFixed(1)} MB`;
                                },
                                validateFile(file) {
                                    const extension = String(file?.name || '').split('.').pop().toLowerCase();
                                    const allowed = ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'eps', 'esp'];
                                    if (!allowed.includes(extension)) {
                                        this.errorText = 'Use a PDF, DOCX, XLSX, JPG, PNG, ZIP, EPS or ESP file.';
                                        return false;
                                    }
                                    if (Number(file?.size || 0) > 20 * 1024 * 1024) {
                                        this.errorText = 'The file is too large. The maximum size is 20 MB.';
                                        return false;
                                    }
                                    return true;
                                },
                                captureFile(event) {
                                    const file = event.target.files?.[0];
                                    if (!file) return;
                                    this.startUpload(file);
                                },
                                startUpload(file) {
                                    this.selectedFile = file;
                                    this.fileName = file.name;
                                    this.fileSize = file.size;
                                    this.progress = 0;
                                    if (!this.validateFile(file)) {
                                        this.state = 'error';
                                        return;
                                    }

                                    const token = ++this.uploadToken;
                                    this.errorText = 'The connection was interrupted. Your document was not added.';
                                    this.state = 'uploading';
                                    this.progress = 1;

                                    $wire.upload(
                                        'jobRequiredDocumentUpload',
                                        file,
                                        async () => {
                                            if (token !== this.uploadToken) return;
                                            this.progress = 100;
                                            try {
                                                const result = await $wire.persistJobRequiredDocumentUpload();
                                                if (token !== this.uploadToken) return;
                                                if (result && result.ok === false) {
                                                    this.errorText = result.message || 'The document could not be saved. Please try again.';
                                                    this.state = 'error';
                                                    return;
                                                }
                                                this.state = 'success';
                                            } catch (error) {
                                                if (token !== this.uploadToken) return;
                                                this.errorText = 'The document could not be saved. Please try again.';
                                                this.state = 'error';
                                            }
                                        },
                                        () => {
                                            if (token !== this.uploadToken) return;
                                            this.errorText = 'The connection was interrupted. Your document was not added.';
                                            this.state = 'error';
                                        },
                                        (event) => {
                                            if (token !== this.uploadToken) return;
                                            this.state = 'uploading';
                                            this.progress = Math.max(1, Math.min(99, Number(event?.detail?.progress || 0)));
                                        },
                                        () => {
                                            if (token !== this.uploadToken) return;
                                            this.progress = 0;
                                            this.state = 'idle';
                                        }
                                    );
                                },
                                retry() {
                                    const file = this.selectedFile || this.$refs.input?.files?.[0];
                                    if (!file) {
                                        this.$refs.input?.click();
                                        return;
                                    }
                                    this.startUpload(file);
                                },
                                cancel() {
                                    ++this.uploadToken;
                                    $wire.cancelUpload('jobRequiredDocumentUpload');
                                    this.clearSelection(false);
                                },
                                clearSelection(resetServer = true) {
                                    if (this.$refs.input) this.$refs.input.value = '';
                                    this.selectedFile = null;
                                    this.fileName = '';
                                    this.fileSize = 0;
                                    this.progress = 0;
                                    this.state = 'idle';
                                    if (resetServer) $wire.clearJobRequiredDocumentUpload();
                                }
                            }"
                        >
                            <input
                                x-ref="input"
                                class="ft-proto-file-input"
                                id="jobDocumentUpload-{{ $job->id }}"
                                type="file"
                                accept=".pdf,.docx,.xlsx,.jpg,.jpeg,.png,.zip,.eps,.esp"
                                x-on:change="captureFile($event)"
                            >

                            <div class="ft-proto-upload-state ft-proto-upload-idle" x-show="state === 'idle'" x-cloak>
                                <button type="button" class="ft-proto-idle-target" x-on:click="$refs.input.click()">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V5m0 0L8 9m4-4 4 4M6 14a4 4 0 0 0 1 7h10a4 4 0 0 0 1-7 6 6 0 0 0-11.4-1.8"/></svg>
                                    <span>Drop the file here or <strong>browse</strong> to choose a file.</span>
                                </button>
                            </div>

                            <div class="ft-proto-upload-state ft-proto-uploading" x-show="state === 'uploading'" x-cloak>
                                <div class="ft-proto-file-summary">
                                    <span class="ft-proto-file-icon" x-text="(fileName.split('.').pop() || 'FILE').slice(0,4).toUpperCase()">FILE</span>
                                    <div class="ft-proto-file-copy">
                                        <strong x-text="fileName || 'Preparing file…'"></strong>
                                        <span><span x-text="prettySize(fileSize)"></span><template x-if="fileSize"><span> · </span></template><span x-text="progress >= 100 ? 'Saving & linking…' : 'Uploading…'"></span></span>
                                        <div class="ft-proto-progress-row">
                                            <span class="ft-proto-progress"><i :style="`width:${progress}%`"></i></span>
                                            <b x-text="`${Math.round(progress)}%`"></b>
                                        </div>
                                        <p x-text="progress >= 100 ? 'Upload received. FlowTrack is saving and linking the document…' : 'Keep this window open until the upload finishes.'"></p>
                                    </div>
                                </div>
                                <button type="button" class="ft-proto-outline-action" x-on:click="cancel()">Cancel</button>
                                <div class="ft-proto-drop-again">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17V9m0 0-3 3m3-3 3 3M6 17a4 4 0 0 1 1-7 6 6 0 0 1 11.4 1.8A4 4 0 0 1 18 20H7"/></svg>
                                    <span>Drop another file here or <button type="button" x-on:click="$refs.input.click()">browse</button> after this upload finishes.</span>
                                </div>
                            </div>

                            <div class="ft-proto-upload-state ft-proto-upload-error" x-show="state === 'error'" x-cloak>
                                <div class="ft-proto-file-summary">
                                    <span class="ft-proto-file-icon" x-text="(fileName.split('.').pop() || 'FILE').slice(0,4).toUpperCase()">FILE</span>
                                    <div class="ft-proto-file-copy">
                                        <strong x-text="fileName || 'Selected document'"></strong>
                                        <span><span x-text="prettySize(fileSize)"></span><template x-if="fileSize"><span> · </span></template>Upload failed</span>
                                        <div class="ft-proto-error-title"><span>!</span><b>We couldn’t upload this file</b></div>
                                        <p x-text="errorText"></p>
                                        <p>If this keeps happening, check your connection and try again.</p>
                                    </div>
                                </div>
                                <div class="ft-proto-error-actions">
                                    <button type="button" class="ft-proto-primary-action" x-on:click="retry()">Retry upload</button>
                                    <button type="button" class="ft-proto-outline-action" x-on:click="$refs.input.click()">Choose another file</button>
                                    <button type="button" class="ft-proto-link-action" x-on:click="clearSelection()">Remove</button>
                                </div>
                                <div class="ft-proto-drop-again">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17V9m0 0-3 3m3-3 3 3M6 17a4 4 0 0 1 1-7 6 6 0 0 1 11.4 1.8A4 4 0 0 1 18 20H7"/></svg>
                                    <span>Drop the file here again or <button type="button" x-on:click="$refs.input.click()">browse</button> to choose another file.</span>
                                </div>
                            </div>

                            <?php if ($lastUploadedDocument): ?>
                                <div class="ft-proto-upload-state ft-proto-upload-success" x-show="state === 'success'" x-cloak>
                                    <div class="ft-proto-file-summary">
                                        <span class="ft-proto-file-icon">{{ strtoupper(pathinfo($lastUploadedDocument->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                                        <div class="ft-proto-file-copy">
                                            <strong>{{ $lastUploadedDocument->name }}</strong>
                                            <span>{{ number_format(((int) $lastUploadedDocument->size) / 1048576, 1) }} MB · Uploaded just now</span>
                                            <div class="ft-proto-success-title"><span>✓</span><b>Upload complete</b></div>
                                            <p>Linked to the selected document type.</p>
                                        </div>
                                    </div>
                                    <div class="ft-proto-success-actions">
                                        <a class="ft-proto-outline-action" href="{{ route('documents.open', $lastUploadedDocument) }}" target="_blank" rel="noopener">View document</a>
                                        <?php if ($canDeleteDocument): ?><button type="button" class="ft-proto-link-action" wire:click="removeLastJobDocumentUpload">Remove</button><?php endif; ?>
                                    </div>
                                    <div class="ft-proto-drop-again">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17V9m0 0-3 3m3-3 3 3M6 17a4 4 0 0 1 1-7 6 6 0 0 1 11.4 1.8A4 4 0 0 1 18 20H7"/></svg>
                                        <span>Drop another file here or <button type="button" x-on:click="$refs.input.click()">browse</button> to replace this document.</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="ft-proto-upload-formats">PDF, DOCX, XLSX, JPG, PNG, ZIP, EPS or ESP <span>·</span> Max 20 MB</div>
                    <?php elseif ($canLinkDocument && ($showDocumentPicker || ! $canUploadDocument)): ?>
                        <div class="ft-proto-existing-panel">
                            <div class="ft-proto-existing-copy">
                                <span class="ft-proto-existing-icon" aria-hidden="true">▤</span>
                                <div><b>Choose an existing document</b><p>Select a stored document for this client and FlowTrack will link it to the document type above.</p></div>
                            </div>
                            <?php if ($availableDocuments->isNotEmpty()): ?>
                                <div class="ft-proto-existing-controls">
                                    <select wire:model="existingDocumentId">
                                        <option value="">Select a stored document</option>
                                        @foreach($availableDocuments as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->name }} · {{ $doc->job?->displayOrderNumber() ?? 'Document archive' }}</option>
                                        @endforeach
                                    </select>
                                    <button class="ft-proto-primary-action" type="button" wire:click="attachExistingDocument">Link document</button>
                                </div>
                                <?php if ($errors->has('existingDocumentId')): ?><div class="validation-error ft-field-validation">{{ $errors->first('existingDocumentId') }}</div><?php endif; ?>
                            <?php else: ?>
                                <div class="ft-proto-existing-empty">No stored documents are available for this client yet.</div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="muted small">You have read-only access to Job documents.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="ft-empty-taskpack-docs">No document requirement exists in the Task Packs selected by this Job. Upload requirements are never created outside the Task Packs.</div>
                <?php endif; ?>
            </section>

            <section class="ft-detail-card ft-job-documents-table ft-doc-library-card">
                <div class="ft-doc-library-heading">
                    <div>
                        <h3>Job documents</h3>
                        <p>Organized by workflow phase so you can see what is missing without scanning a table.</p>
                    </div>
                    <span class="ft-soft-pill {{ $missingRequired ? 'amber' : 'green' }}">{{ $missingRequired ? $missingRequired.' required missing' : 'All requirements satisfied' }}</span>
                </div>

                <div class="ft-doc-phase-list">
                    @foreach($job->workflow->phases as $phase)
                        @php
                            $phaseRequirements = $required->filter(fn($item)=>(int)$item->phase->id===(int)$phase->id)->values();
                            $phaseTaskIds = $job->tasks->where('workflow_phase_id',$phase->id)->pluck('id');
                            $phaseDocuments = $job->documents->whereIn('task_id',$phaseTaskIds)->values();
                            $phaseRequiredReceived = $phaseRequirements->where('complete', true)->count();
                            $phaseMissing = $phaseRequirements->where('complete', false)->count();
                            $requiredDocumentIds = $phaseRequirements->flatMap(function($requirement) use ($job) {
                                // task_id is the authoritative requirement link; the
                                // stored category label may be generic on legacy files.
                                return $job->documents
                                    ->where('task_id', $requirement->task->id)
                                    ->pluck('id');
                            })->map(fn($id)=>(int)$id)->unique();
                            $phaseAttachments = $phaseDocuments->reject(fn($doc)=>$requiredDocumentIds->contains((int)$doc->id))->values();
                            $openPhase = (int)($job->workflow_phase_id ?? 0) === (int)$phase->id || ((int)($job->workflow_phase_id ?? 0) === 0 && (int)$phase->sequence === 1);
                        @endphp

                        <details class="ft-doc-phase-group" style="{{ \App\Support\MasterColor::style($phase->color) }}" <?php if ($openPhase): ?>open<?php endif; ?>>
                            <summary class="ft-doc-phase-summary">
                                <span class="ft-doc-phase-chevron">›</span>
                                <b class="ft-doc-phase-number">{{ $phase->sequence }}</b>
                                <span class="ft-doc-phase-copy">
                                    <strong>{{ $phase->name }}</strong>
                                    <small>{{ $phaseRequiredReceived }} of {{ $phaseRequirements->count() }} requirements satisfied · {{ $phaseDocuments->count() }} file{{ $phaseDocuments->count()===1?'':'s' }}</small>
                                </span>
                                <?php if ($phaseMissing): ?>
                                    <span class="ft-soft-pill amber">{{ $phaseMissing }} needs action</span>
                                <?php elseif ($phaseRequirements->isNotEmpty()): ?>
                                    <span class="ft-soft-pill green">Complete</span>
                                <?php else: ?>
                                    <span class="ft-soft-pill gray">No requirements</span>
                                <?php endif; ?>
                            </summary>

                            <div class="ft-doc-phase-body">
                                @forelse($phaseRequirements as $requirement)
                                    @php
                                        $docs = $job->documents->where('task_id',$requirement->task->id)->values();
                                        $links = \App\Support\JobDetailPresenter::taskLinks($job, $requirement->task);
                                    @endphp
                                    <article class="ft-doc-requirement-card {{ $requirement->complete ? 'is-complete' : 'needs-action' }}">
                                        <div class="ft-doc-requirement-main">
                                            <span class="ft-doc-requirement-state">{{ $requirement->complete ? '✓' : '!' }}</span>
                                            <div class="ft-doc-requirement-copy">
                                                <div class="ft-doc-requirement-title-line">
                                                    <b>{{ $requirement->name }}</b>
                                                    <span class="ft-soft-pill {{ $requirement->complete ? 'green' : 'amber' }}">{{ $requirement->complete ? 'Satisfied' : 'Required' }}</span>
                                                </div>
                                                <small>Task: {{ $requirement->task->title }}</small>
                                                <?php if ($docs->isEmpty() && $links->isEmpty()): ?>
                                                    <p>No file or document link has been submitted for this requirement yet.</p>
                                                <?php else: ?>
                                                    <p>
                                                        @if($docs->isNotEmpty()){{ $docs->count() }} file{{ $docs->count()===1?'':'s' }}@endif
                                                        @if($docs->isNotEmpty() && $links->isNotEmpty()) · @endif
                                                        @if($links->isNotEmpty()){{ $links->count() }} link{{ $links->count()===1?'':'s' }}@endif
                                                        submitted for this requirement.
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (! $requirement->complete && $canUploadDocument): ?>
                                                <button class="ft-outline-btn ft-doc-requirement-upload" type="button" x-on:click="await $wire.set('jobDocumentTaskId', {{ $requirement->task->id }}); document.getElementById('jobDocumentUpload-{{ $job->id }}').click()">Upload file</button>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($docs->isNotEmpty()): ?>
                                            <div class="ft-doc-linked-files">
                                                @foreach($docs as $doc)
                                                    @php $extension = strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE'); @endphp
                                                    <div class="ft-doc-linked-file">
                                                        <span class="ft-file-icon {{ str_contains(strtolower($doc->mime_type ?? ''),'pdf') ? 'pdf' : 'sheet' }}">▣</span>
                                                        <div class="ft-doc-linked-file-copy">
                                                            <a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a>
                                                            <small>{{ $extension }} · v{{ $doc->version }} · {{ $doc->uploader?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::isToday($doc->updated_at) ? 'Today '.\App\Support\UserLocalTime::format($doc->updated_at, 'g:i A') : \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y') }}</small>
                                                        </div>
                                                        <span class="ft-soft-pill green">Linked</span>
                                                        <div class="ft-doc-linked-actions">
                                                            <a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>
                                                            <?php if (auth()->user()->canModule('documents','delete')): ?>
                                                                <button class="ft-doc-delete-button" type="button" wire:click="deleteJobDocument({{ $doc->id }})" wire:confirm="Delete this document link?" aria-label="Delete {{ $doc->name }}">×</button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($links->isNotEmpty()): ?>
                                            <div class="ft-doc-linked-files">
                                                @foreach($links as $taskLink)
                                                    <div class="ft-doc-linked-file">
                                                        <span class="ft-file-icon sheet">↗</span>
                                                        <div class="ft-doc-linked-file-copy">
                                                            <a href="{{ $taskLink->url }}" target="_blank" rel="noopener noreferrer">{{ \Illuminate\Support\Str::limit($taskLink->url, 100) }}</a>
                                                            <small>External document link · {{ $taskLink->created_at ? \App\Support\UserLocalTime::format($taskLink->created_at, 'M j, Y, g:i A') : '—' }}</small>
                                                        </div>
                                                        <span class="ft-soft-pill green">Accepted</span>
                                                        <div class="ft-doc-linked-actions">
                                                            <a class="ft-link-blue" href="{{ $taskLink->url }}" target="_blank" rel="noopener noreferrer">Open ↗</a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        <?php endif; ?>
                                    </article>
                                @empty
                                    <div class="ft-doc-empty-phase">No Task Pack document requirements in this phase.</div>
                                @endforelse

                                <?php if ($phaseAttachments->isNotEmpty()): ?>
                                    <div class="ft-doc-attachments-block">
                                        <div class="ft-doc-subsection-label"><span>Attachments</span><small>Files linked to tasks but not counted as required documents</small></div>
                                        @foreach($phaseAttachments as $doc)
                                            @php $extension = strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE'); @endphp
                                            <div class="ft-doc-linked-file is-attachment">
                                                <span class="ft-file-icon">▣</span>
                                                <div class="ft-doc-linked-file-copy">
                                                    <a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a>
                                                    <small>{{ $extension }} · v{{ $doc->version }} · Task: {{ $doc->task?->title }} · {{ \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y') }}</small>
                                                </div>
                                                <span class="ft-soft-pill gray">Attachment</span>
                                                <div class="ft-doc-linked-actions"><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a></div>
                                            </div>
                                        @endforeach
                                    </div>
                                <?php endif; ?>
                            </div>
                        </details>
                    @endforeach

                    <?php if ($unlinkedDocs->isNotEmpty()): ?>
                        <details class="ft-doc-phase-group ft-doc-unlinked-group">
                            <summary class="ft-doc-phase-summary">
                                <span class="ft-doc-phase-chevron">›</span>
                                <b class="ft-doc-phase-number">—</b>
                                <span class="ft-doc-phase-copy"><strong>Existing Job attachments</strong><small>Files not linked to a Task Pack requirement</small></span>
                                <span class="ft-soft-pill gray">{{ $unlinkedDocs->count() }} file{{ $unlinkedDocs->count()===1?'':'s' }}</span>
                            </summary>
                            <div class="ft-doc-phase-body">
                                @foreach($unlinkedDocs as $doc)
                                    @php $extension = strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE'); @endphp
                                    <div class="ft-doc-linked-file is-attachment">
                                        <span class="ft-file-icon">▣</span>
                                        <div class="ft-doc-linked-file-copy">
                                            <a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a>
                                            <small>{{ $extension }} · v{{ $doc->version }} · {{ $doc->uploader?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y') }}</small>
                                        </div>
                                        <span class="ft-soft-pill gray">Attachment</span>
                                        <div class="ft-doc-linked-actions"><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a></div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</div>
