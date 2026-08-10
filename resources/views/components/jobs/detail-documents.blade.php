@props([
    'job',
    'availableDocuments'=>collect(),
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
])
@php
    $required = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $receivedRequired = $required->where('complete', true)->count();
    $missingRequired = $required->where('complete', false)->count();
    $requiredProgress = $required->count() ? (int) round(($receivedRequired / $required->count()) * 100) : 100;
    $unlinkedDocs = $job->documents->whereNull('task_id')->values();
    $canUploadDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','create');
    $canLinkDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
@endphp
<div class="ft-documents-detail-section ft-exact-documents">
    <?php if (session('success')): ?><div class="flash">{{ session('success') }}</div><?php endif; ?>

    <div class="ft-detail-doc-full">
        <main>
            <div class="ft-section-title-row ft-doc-title-row ft-doc-ux-title">
                <div>
                    <h2>Documents</h2>
                    <p>Keep required files, task attachments and existing client documents together.</p>
                </div>
                <div class="ft-doc-completion-copy">
                    <strong>{{ $receivedRequired }}/{{ $required->count() }}</strong>
                    <span>required received</span>
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
                    <div><small>Received</small><b>{{ $receivedRequired }}</b></div>
                </div>
            </div>

            <div class="ft-doc-required-progress" aria-label="Required document progress">
                <div><span>Required document progress</span><b>{{ $requiredProgress }}%</b></div>
                <span class="ft-doc-progress-track"><i style="width: {{ $requiredProgress }}%"></i></span>
            </div>

            <section id="job-document-upload-panel" class="ft-detail-card ft-upload-panel ft-doc-upload-card">
                <div class="ft-doc-upload-heading">
                    <span class="ft-doc-upload-heading-icon">＋</span>
                    <div>
                        <b>Add a required document</b>
                        <p>Choose where the document belongs, then upload a new file or link one that already exists.</p>
                    </div>
                </div>

                <?php if ($required->isNotEmpty()): ?>
                    <div class="ft-doc-upload-steps">
                        <div class="ft-doc-upload-step">
                            <span>1</span>
                            <div><b>Choose requirement</b><small>Select the Task Pack requirement this file satisfies.</small></div>
                        </div>
                        <div class="ft-doc-upload-step">
                            <span>2</span>
                            <div><b>Add the file</b><small>Upload from your device or choose an existing document.</small></div>
                        </div>
                    </div>

                    <div class="ft-document-purpose-field ft-doc-purpose-ux">
                        <label for="jobDocumentRequirement-{{ $job->id }}">Required document</label>
                        <select id="jobDocumentRequirement-{{ $job->id }}" wire:model.live="jobDocumentTaskId" class="ft-document-purpose-select">
                            <option value="">Select requirement, phase and task</option>
                            @foreach($required as $item)
                                <option value="{{ $item->task->id }}">{{ $item->name }} · {{ $item->phase->name }} · {{ $item->task->title }}</option>
                            @endforeach
                        </select>
                        <small class="ft-document-match-note">FlowTrack links the file to the selected phase and task automatically.</small>
                        <?php if ($errors->has('jobDocumentTaskId')): ?><div class="validation-error ft-field-validation" role="alert">{{ $errors->first('jobDocumentTaskId') }}</div><?php endif; ?>
                    </div>

                    <div class="ft-upload-zone compact ft-task-upload-zone ft-job-document-attachment-zone ft-doc-upload-actions">
                        <?php if ($canUploadDocument): ?>
                            <label class="ft-task-upload-drop ft-livewire-upload-zone ft-doc-primary-drop {{ $errors->has('jobDocumentUploads') || $errors->has('jobDocumentUploads.*') ? 'has-upload-error' : '' }}" data-file-dropzone for="jobDocumentUpload-{{ $job->id }}">
                                <input id="jobDocumentUpload-{{ $job->id }}" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                                <span class="ft-paperclip">⌕</span>
                                <div><b>Upload from device</b><span>Drop files here or <strong>browse</strong></span><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                            </label>
                        <?php else: ?>
                            <div class="ft-task-upload-drop ft-task-upload-readonly ft-doc-primary-drop"><span class="ft-paperclip">⌕</span><div><b>Document upload</b><span>Read-only access</span><small>You do not have permission to upload Job documents.</small></div></div>
                        <?php endif; ?>
                        <?php if ($canLinkDocument): ?>
                            <button class="ft-outline-btn ft-task-choose-document ft-doc-existing-action" type="button" wire:click="toggleDocumentPicker">
                                <span>▤</span><span>Choose existing</span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if (! $canManageDocuments): ?><p class="muted small">You have read-only access to Job documents.</p><?php endif; ?>

                    <?php if ($canUploadDocument): ?>
                        <?php if ($errors->has('jobDocumentUploads')): ?>
                            <div class="ft-upload-field-error validation-error" role="alert">
                                <span>{{ $errors->first('jobDocumentUploads') }}</span>
                                <button type="button" wire:click="clearJobDocumentUploads">Remove failed file</button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($canUploadDocument && count($jobDocumentUploads ?? [])): ?>
                        <div class="ft-pending-upload-list" aria-label="Files selected for upload">
                            @foreach($jobDocumentUploads as $uploadIndex => $upload)
                                <?php $uploadName = method_exists($upload, 'getClientOriginalName') ? $upload->getClientOriginalName() : ('File '.($uploadIndex + 1)); ?>
                                <div class="ft-pending-upload-item {{ $errors->has('jobDocumentUploads.'.$uploadIndex) ? 'has-error' : '' }}" wire:key="job-doc-pending-{{ $job->id }}-{{ $uploadIndex }}-{{ md5($uploadName) }}">
                                    <div class="ft-pending-upload-copy">
                                        <b>{{ $uploadName }}</b>
                                        <?php if ($errors->has('jobDocumentUploads.'.$uploadIndex)): ?><small class="validation-error" role="alert">{{ $errors->first('jobDocumentUploads.'.$uploadIndex) }}</small><?php endif; ?>
                                    </div>
                                    <button type="button" class="ft-pending-upload-remove" wire:click="removeJobDocumentUpload({{ $uploadIndex }})" aria-label="Remove {{ $uploadName }}">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        <div class="ft-upload-ready-row ft-doc-upload-ready">
                            <span><b>{{ count($jobDocumentUploads ?? []) }}</b> file{{ count($jobDocumentUploads ?? [])===1?'':'s' }} ready to link</span>
                            <button class="ft-new-job-btn" type="button" wire:click="uploadJobDocuments">Upload &amp; link</button>
                        </div>
                    <?php endif; ?>

                    <?php if ($canLinkDocument && ($showDocumentPicker ?? false)): ?>
                        <div class="ft-existing-document-picker ft-doc-existing-picker">
                            <div class="ft-card-row-head">
                                <div><b>Choose an existing document</b><p>Link a stored client document to the requirement selected above.</p></div>
                                <button type="button" class="ft-outline-btn" wire:click="toggleDocumentPicker">Close</button>
                            </div>
                            <?php if ($availableDocuments->isNotEmpty()): ?>
                                <div class="ft-doc-existing-picker-actions">
                                    <select wire:model="existingDocumentId">
                                        <option value="">Select a stored document</option>
                                        @foreach($availableDocuments as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->name }} · {{ $doc->job?->displayOrderNumber() ?? 'Document archive' }}</option>
                                        @endforeach
                                    </select>
                                    <button class="ft-new-job-btn" type="button" wire:click="attachExistingDocument">Link document</button>
                                </div>
                                <?php if ($errors->has('existingDocumentId')): ?><div class="validation-error">{{ $errors->first('existingDocumentId') }}</div><?php endif; ?>
                            <?php else: ?>
                                <p class="muted small">No stored documents are available for this client yet.</p>
                            <?php endif; ?>
                        </div>
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
                    <span class="ft-soft-pill {{ $missingRequired ? 'amber' : 'green' }}">{{ $missingRequired ? $missingRequired.' required missing' : 'All required received' }}</span>
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
                                return $job->documents
                                    ->where('task_id', $requirement->task->id)
                                    ->filter(fn($document) => strcasecmp(trim((string)$document->category), trim((string)$requirement->name)) === 0)
                                    ->pluck('id');
                            })->map(fn($id)=>(int)$id)->unique();
                            $phaseAttachments = $phaseDocuments->reject(fn($doc)=>$requiredDocumentIds->contains((int)$doc->id))->values();
                            $openPhase = (int)($job->workflow_phase_id ?? 0) === (int)$phase->id || ((int)($job->workflow_phase_id ?? 0) === 0 && (int)$phase->sequence === 1);
                        @endphp

                        <details class="ft-doc-phase-group" <?php if ($openPhase): ?>open<?php endif; ?>>
                            <summary class="ft-doc-phase-summary">
                                <span class="ft-doc-phase-chevron">›</span>
                                <b class="ft-doc-phase-number">{{ $phase->sequence }}</b>
                                <span class="ft-doc-phase-copy">
                                    <strong>{{ $phase->name }}</strong>
                                    <small>{{ $phaseRequiredReceived }} of {{ $phaseRequirements->count() }} required received · {{ $phaseDocuments->count() }} file{{ $phaseDocuments->count()===1?'':'s' }}</small>
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
                                        $docs = $job->documents->where('task_id',$requirement->task->id)->filter(fn($document)=>strcasecmp(trim((string)$document->category),trim((string)$requirement->name))===0)->values();
                                    @endphp
                                    <article class="ft-doc-requirement-card {{ $requirement->complete ? 'is-complete' : 'needs-action' }}">
                                        <div class="ft-doc-requirement-main">
                                            <span class="ft-doc-requirement-state">{{ $requirement->complete ? '✓' : '!' }}</span>
                                            <div class="ft-doc-requirement-copy">
                                                <div class="ft-doc-requirement-title-line">
                                                    <b>{{ $requirement->name }}</b>
                                                    <span class="ft-soft-pill {{ $requirement->complete ? 'green' : 'amber' }}">{{ $requirement->complete ? 'Received' : 'Required' }}</span>
                                                </div>
                                                <small>Task: {{ $requirement->task->title }}</small>
                                                <?php if ($docs->isEmpty()): ?>
                                                    <p>No file has been received for this requirement yet.</p>
                                                <?php else: ?>
                                                    <p>{{ $docs->count() }} file{{ $docs->count()===1?'':'s' }} linked to this requirement.</p>
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
