@props([
    'job',
    'availableDocuments'=>collect(),
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
])
@php
    $required = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $receivedRequired = $required->where('complete', true)->count();
    $unlinkedDocs = $job->documents->whereNull('task_id')->values();
    $canUploadDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','create');
    $canLinkDocument = app(\App\Services\AccessControlService::class)->can(auth()->user(),'documents','link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
@endphp
<div class="ft-documents-detail-section ft-exact-documents">
    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
    @error('jobDocumentTaskId')<div class="validation-error">{{ $message }}</div>@enderror
    @error('jobDocumentUploads')<div class="validation-error">{{ $message }}</div>@enderror
    @error('jobDocumentUploads.*')<div class="validation-error">{{ $message }}</div>@enderror

    <div class="ft-detail-doc-full">
        <main>
            <div class="ft-section-title-row ft-doc-title-row">
                <div><h2>Documents</h2><p>{{ $job->documents->count() }} files · {{ $receivedRequired }} of {{ $required->count() }} required received</p></div>
            </div>
            <div class="ft-doc-filter-chips">
                <button class="active" type="button">All files <b>{{ $job->documents->count() }}</b></button>
                <button type="button">Required <b>{{ $required->count() }}</b></button>
                <button class="amber" type="button">Needs action <b>{{ $required->where('complete',false)->count() }}</b></button>
                <button class="green" type="button">Received <b>{{ $receivedRequired }}</b></button>
                <button type="button">Recently updated <b>{{ $job->documents->filter(fn($doc)=>$doc->updated_at?->gte(now()->subDays(7)))->count() }}</b></button>
            </div>

            <section id="job-document-upload-panel" class="ft-detail-card ft-upload-panel">
                <b>Add documents</b>
                <p>Select the Task Pack document requirement first. The uploaded or chosen file is linked to that exact task automatically.</p>

                @if($required->isNotEmpty())
                    <div class="ft-document-purpose-field">
                        <label for="jobDocumentRequirement-{{ $job->id }}">Document type</label>
                        <select id="jobDocumentRequirement-{{ $job->id }}" wire:model.live="jobDocumentTaskId" class="ft-document-purpose-select">
                            <option value="">Select a Task Pack document requirement</option>
                            @foreach($required as $item)
                                <option value="{{ $item->task->id }}">{{ $item->name }} · {{ $item->phase->name }} · {{ $item->task->title }}</option>
                            @endforeach
                        </select>
                        <small class="ft-document-match-note">Choose the required document first. FlowTrack links the uploaded or selected file to the matching phase and task automatically.</small>
                    </div>

                    <div class="ft-upload-zone compact ft-task-upload-zone ft-job-document-attachment-zone">
                        @if($canUploadDocument)
                            <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone for="jobDocumentUpload-{{ $job->id }}">
                                <input id="jobDocumentUpload-{{ $job->id }}" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                                <span class="ft-paperclip">⌕</span>
                                <div>Drop files here or <strong>browse</strong><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                            </label>
                        @else
                            <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Document upload<small>You have read-only access to Job documents.</small></div></div>
                        @endif
                        @if($canLinkDocument)
                            <button class="ft-outline-btn ft-task-choose-document" type="button" wire:click="toggleDocumentPicker">Choose from Documents</button>
                        @endif
                    </div>
                    @unless($canManageDocuments)<p class="muted small">You have read-only access to Job documents.</p>@endunless

                    @if($canUploadDocument && count($jobDocumentUploads ?? []))
                        <div class="ft-upload-ready-row">
                            <span>{{ count($jobDocumentUploads ?? []) }} file{{ count($jobDocumentUploads ?? [])===1?'':'s' }} ready</span>
                            <button class="ft-new-job-btn" type="button" wire:click="uploadJobDocuments">Upload &amp; link</button>
                        </div>
                    @endif

                    @if($canLinkDocument && ($showDocumentPicker ?? false))
                        <div class="ft-existing-document-picker">
                            <div class="ft-card-row-head"><div><b>Choose from Documents</b><p>Select an existing client document and link it to the selected Task Pack requirement.</p></div><button type="button" class="ft-outline-btn" wire:click="toggleDocumentPicker">Close</button></div>
                            @if($availableDocuments->isNotEmpty())
                                <select wire:model="existingDocumentId">
                                    <option value="">Select a stored document</option>
                                    @foreach($availableDocuments as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }} · {{ $doc->job?->displayOrderNumber() ?? 'Document archive' }}</option>
                                    @endforeach
                                </select>
                                <button class="ft-new-job-btn" type="button" wire:click="attachExistingDocument">Link selected document</button>
                                @error('existingDocumentId')<div class="validation-error">{{ $message }}</div>@enderror
                            @else
                                <p class="muted small">No stored documents are available for this client yet.</p>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="ft-empty-taskpack-docs">No document requirement exists in the Task Packs selected by this Job. Upload requirements are never created outside the Task Packs.</div>
                @endif
            </section>

            <section class="ft-detail-card ft-job-documents-table">
                <h3>Job documents</h3>
                <p>Grouped by workflow phase and related Task Pack task</p>
                <div class="ft-doc-table-wrap">
                    <table class="ft-doc-table">
                        <thead><tr><th>Document / requirement</th><th>Type</th><th>Version</th><th>Owner</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
                        <tbody>
                        @foreach($job->workflow->phases as $phase)
                            @php
                                $phaseRequirements = $required->filter(fn($item)=>(int)$item->phase->id===(int)$phase->id)->values();
                                $phaseTaskIds = $job->tasks->where('workflow_phase_id',$phase->id)->pluck('id');
                                $phaseDocuments = $job->documents->whereIn('task_id',$phaseTaskIds)->values();
                                $requiredDocumentIds = $phaseRequirements->flatMap(function($requirement) use ($job) {
                                    return $job->documents
                                        ->where('task_id', $requirement->task->id)
                                        ->filter(fn($document) => strcasecmp(trim((string)$document->category), trim((string)$requirement->name)) === 0)
                                        ->pluck('id');
                                })->map(fn($id)=>(int)$id)->unique();
                                $phaseAttachments = $phaseDocuments->reject(fn($doc)=>$requiredDocumentIds->contains((int)$doc->id))->values();
                            @endphp
                            <tr class="ft-doc-phase-row"><td colspan="7"><span>⌄</span><b>{{ $phase->sequence }}</b> {{ $phase->name }} <small>{{ $phaseDocuments->count() }} documents · {{ $phaseRequirements->count() }} requirement{{ $phaseRequirements->count()===1?'':'s' }}</small><em>{{ $phaseDocuments->count() }}</em></td></tr>

                            @forelse($phaseRequirements as $requirement)
                                @php
                                    $docs = $job->documents->where('task_id',$requirement->task->id)->filter(fn($document)=>strcasecmp(trim((string)$document->category),trim((string)$requirement->name))===0)->values();
                                @endphp
                                <tr class="ft-required-inline-row"><td colspan="7">
                                    <div><strong>{{ $requirement->task->title }}</strong><span>{{ $docs->count() ? $docs->count().' received' : 'Required document missing' }}</span></div>
                                    <div class="ft-inline-requirement">
                                        <span class="{{ $requirement->complete ? 'ok' : 'warn' }}">{{ $requirement->complete ? '✓' : '!' }}</span>
                                        <div><b>{{ $requirement->name }}</b><small>{{ $phase->name }} · Task: {{ $requirement->task->title }}</small></div>
                                        <span class="ft-soft-pill {{ $requirement->complete ? 'green' : 'amber' }}">{{ $requirement->complete ? 'Received' : 'Needs action' }}</span>
                                        @if(!$requirement->complete && $canUploadDocument)
                                            <button class="ft-outline-btn" type="button" x-on:click="await $wire.set('jobDocumentTaskId', {{ $requirement->task->id }}); document.getElementById('jobDocumentUpload-{{ $job->id }}').click()">Upload</button>
                                        @endif
                                    </div>
                                </td></tr>
                                @foreach($docs as $doc)
                                    <tr>
                                        <td><span class="ft-file-icon {{ str_contains(strtolower($doc->mime_type ?? ''),'pdf') ? 'pdf' : 'sheet' }}">▣</span><a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a><small class="ft-doc-task-caption">{{ $requirement->task->title }}</small></td>
                                        <td>{{ strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE') }}</td><td>v{{ $doc->version }}</td><td>{{ $doc->uploader?->name ?? 'FlowTrack' }}</td>
                                        <td><span class="ft-soft-pill green">Linked</span></td><td>{{ \App\Support\UserLocalTime::isToday($doc->updated_at) ? 'Today '.\App\Support\UserLocalTime::format($doc->updated_at, 'g:i A') : \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y') }}</td>
                                        <td><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>@if(auth()->user()->canModule('documents','delete'))<button class="ft-doc-delete-button" type="button" wire:click="deleteJobDocument({{ $doc->id }})" wire:confirm="Delete this document link?">×</button>@endif</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="7" class="ft-doc-empty-row">No document requirement in this phase's Task Pack.</td></tr>
                            @endforelse

                            @foreach($phaseAttachments as $doc)
                                <tr>
                                    <td><span class="ft-file-icon">▣</span><a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a><small class="ft-doc-task-caption">Task attachment · {{ $doc->task?->title }}</small></td>
                                    <td>{{ strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE') }}</td><td>v{{ $doc->version }}</td><td>{{ $doc->uploader?->name ?? 'FlowTrack' }}</td><td><span class="ft-soft-pill gray">Attachment</span></td><td>{{ \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y') }}</td><td><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a></td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if($unlinkedDocs->isNotEmpty())
                            <tr class="ft-doc-phase-row"><td colspan="7"><span>⌄</span><b>—</b> Existing Job attachments <small>Not counted as Task Pack requirements</small><em>{{ $unlinkedDocs->count() }}</em></td></tr>
                            @foreach($unlinkedDocs as $doc)
                                <tr><td><span class="ft-file-icon">▣</span><a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a></td><td>{{ strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE') }}</td><td>v{{ $doc->version }}</td><td>{{ $doc->uploader?->name ?? 'FlowTrack' }}</td><td><span class="ft-soft-pill gray">Attachment</span></td><td>{{ \App\Support\UserLocalTime::format($doc->updated_at, 'M j, Y') }}</td><td><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a></td></tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

    </div>
</div>
