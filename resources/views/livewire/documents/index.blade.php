<div class="ft-documents-page">
    <div class="ft-doc-page-head"><div><h1>Documents</h1><p>Find, upload and manage files across every Job, phase and task.</p></div>@if(auth()->user()->canModule('documents','create'))<button class="primary" wire:click="openUpload">⇧ Upload document</button>@endif</div>
    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif

    <div class="ft-doc-layout">
        <div class="ft-doc-main">
            <div class="ft-doc-metrics">
                <button class="card" wire:click="$set('status','')"><span>All files</span><b>{{ $metrics['all'] }}</b><i>▤</i></button>
                <button class="card danger" wire:click="$set('status','needs_action')"><span>Needs attention</span><b>{{ $metrics['attention'] }}</b><i>△</i></button>
                <button class="card purple" wire:click="$set('status','awaiting_approval')"><span>Awaiting approval</span><b>{{ $metrics['approval'] }}</b><i>◷</i></button>
                <button class="card" wire:click="$set('status','recent')"><span>Recently updated</span><b>{{ $metrics['recent'] }}</b><i>◴</i></button>
            </div>

            <div class="ft-doc-filterbar">
                <div class="ft-doc-search"><span>⌕</span><input wire:model.live.debounce.300ms="search" placeholder="Search file name, Job ID, client or task"></div>
                <select wire:model.live="job"><option value="">Job</option>@foreach($jobs as $j)<option value="{{ $j->id }}">{{ $j->job_number }}</option>@endforeach</select>
                <select wire:model.live="client"><option value="">Client</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
                <select wire:model.live="phase"><option value="">Phase</option>@foreach($phases as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
                <select wire:model.live="category"><option value="">Document type</option>@foreach($categories as $cat)<option>{{ $cat->name }}</option>@endforeach</select>
                <select wire:model.live="status"><option value="">Status</option><option value="current">Current</option><option value="approved">Approved</option><option value="needs_action">Needs attention</option><option value="awaiting_approval">Awaiting approval</option><option value="recent">Recently updated</option></select>
                <button class="ft-doc-clear" wire:click="clearFilters">Clear</button>
            </div>
            @if($search || $job || $client || $phase || $category || $status)<div class="ft-doc-active-filter"><span>Filtered files</span><button wire:click="clearFilters">×</button></div>@endif

            <div class="ft-doc-expand-row">
                <div></div>
                <div class="ft-group-toggle-actions" aria-label="Document group controls">
                    <button type="button" class="ft-double-chevron-btn" wire:click="expandAll" title="Expand all" aria-label="Expand all">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg>
                    </button>
                    <button type="button" class="ft-double-chevron-btn" wire:click="collapseAll" title="Collapse all" aria-label="Collapse all">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg>
                    </button>
                </div>
            </div>

            <div class="card ft-doc-table-card">
                @forelse($grouped as $jobId=>$docs)
                    @php($first=$docs->first())
                    @php($expanded=$jobId==0 || in_array((int)$jobId,$expandedJobs,true))
                    <div class="ft-doc-job-group">
                        <button class="ft-doc-job-head" @if($jobId) wire:click="toggleJob({{ $jobId }})" @endif>
                            <span class="ft-doc-chevron">{{ $expanded?'⌄':'›' }}</span>
                            <b>{{ $first->job?->job_number ?? 'General documents' }} @if($first->job) · {{ $first->job->title }} @endif</b>
                            <span class="ft-doc-job-meta">{{ $first->job?->client?->name ?? 'No client' }} @if($first->job) <em>|</em> Phase: {{ $first->job?->phase?->sequence ?? '—' }} <em>·</em> Tasks: {{ $first->job?->tasks?->count() ?? 0 }} <em>·</em> Documents: {{ $docs->count() }} @endif</span>
                        </button>
                        @if($expanded)
                            <div class="ft-doc-table-scroll"><table class="ft-doc-table"><thead><tr><th>Document</th><th>Linked to</th><th>Type</th><th>Version</th><th>Owner</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead><tbody>
                            @foreach($docs as $doc)
                                @php($docStatus=$doc->is_final?'Approved':($doc->task?->needs_attention?'Needs attention':'Current'))
                                <tr class="{{ $selected?->id===$doc->id?'selected':'' }}" wire:click="selectDocument({{ $doc->id }})">
                                    <td data-label="Document"><div class="ft-doc-file"><span class="ft-file-badge {{ strtolower(pathinfo($doc->name,PATHINFO_EXTENSION)) }}">{{ strtoupper(pathinfo($doc->name,PATHINFO_EXTENSION) ?: 'FILE') }}</span><b>{{ $doc->name }}</b></div></td>
                                    <td data-label="Linked to"><b>{{ $doc->task?->phase?->name ?? $doc->job?->phase?->name ?? 'Job' }}</b><span>{{ $doc->task?->title ?? $doc->job?->title ?? 'General' }}</span></td>
                                    <td data-label="Type">{{ $doc->category ?: 'Other' }}</td><td data-label="Version">v{{ $doc->version }}</td>
                                    <td data-label="Owner"><div class="person"><x-ui.avatar :name="$doc->uploader?->name ?? 'System'"/><span>{{ $doc->uploader?->name ?? 'System' }}</span></div></td>
                                    <td data-label="Status"><span class="ft-doc-status {{ $docStatus==='Approved'?'green':($docStatus==='Needs attention'?'amber':'blue') }}">{{ $docStatus }}</span></td>
                                    <td data-label="Updated">{{ $doc->updated_at?->format('M j') }}</td>
                                    <td data-label="Actions"><div class="ft-doc-row-actions"><a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener" wire:click.stop>Open</a>@if(auth()->user()->canModule('documents','delete'))<button wire:click.stop="deleteDocument({{ $doc->id }})" wire:confirm="Delete this document?">⋮</button>@else<span>⋮</span>@endif</div></td>
                                </tr>
                            @endforeach
                            </tbody></table></div>
                        @endif
                    </div>
                @empty<div class="empty">No documents found.</div>@endforelse

                @if($documents->hasPages() || $documents->total())<div class="ft-doc-pagination"><span>Showing {{ $documents->firstItem() ?? 0 }}–{{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} documents</span><div><select wire:model.live="perPage"><option value="10">10 per page</option><option value="25">25 per page</option><option value="50">50 per page</option></select><button wire:click="previousPage" @disabled($documents->onFirstPage())>Previous</button><span>Page {{ $documents->currentPage() }} of {{ $documents->lastPage() }}</span><button wire:click="nextPage" @disabled(!$documents->hasMorePages())>Next</button></div></div>@endif
            </div>
        </div>

        <aside class="card ft-doc-detail-panel">
            @if($selected)
                <div class="ft-doc-detail-title"><span class="ft-file-large">{{ strtoupper(pathinfo($selected->name,PATHINFO_EXTENSION) ?: 'FILE') }}</span><div><h3>{{ $selected->name }}</h3><div><span class="ft-doc-status blue">{{ $selected->is_final?'Approved':'Current' }}</span> <span class="tag">v{{ $selected->version }}</span> · {{ number_format($selected->size/1024) }} KB</div></div></div>
                <div class="ft-doc-side-list">
                    <div><span>Job</span><a href="{{ $selected->job ? route('jobs.index',['open'=>$selected->flow_job_id]) : '#' }}" wire:navigate>{{ $selected->job?->job_number ?? '—' }}</a></div>
                    <div><span>Client</span><b>{{ $selected->job?->client?->name ?? $selected->client?->name ?? '—' }}</b></div>
                    <div><span>Phase</span><b>{{ $selected->task?->phase?->name ?? $selected->job?->phase?->name ?? '—' }}</b></div>
                    <div><span>Task</span><b>{{ $selected->task?->title ?? '—' }}</b></div>
                    <div><span>Type</span><b>{{ $selected->category ?: 'Other' }}</b></div>
                    <div><span>Owner</span><b>{{ $selected->uploader?->name ?? 'System' }}</b></div>
                    <div><span>Uploaded</span><b>{{ $selected->created_at?->format('M j, Y g:i A') }}</b></div>
                </div>
                <div class="ft-doc-side-actions"><a class="primary" href="{{ route('documents.open',$selected) }}" target="_blank" rel="noopener">Open ↗</a>@if(auth()->user()->canModule('documents','export'))<a class="ghost" href="{{ route('documents.download',$selected) }}">⇩ Download</a>@endif</div>
                <div class="ft-doc-versions"><h4>Versions</h4>@forelse($versions as $version)<button wire:click="selectDocument({{ $version->id }})"><b>v{{ $version->version }}</b><span>{{ $version->uploader?->name ?? 'System' }} · {{ $version->created_at?->format('M j, Y g:i A') }} · {{ number_format($version->size/1024) }} KB</span>@if($version->id===$selected->id)<em>Current</em>@endif</button>@empty<div class="small muted">No previous versions.</div>@endforelse</div>
            @else<div class="empty">Select a document to view details.</div>@endif
        </aside>
    </div>

    @if($showUpload)
        <div class="overlay livewire-overlay" wire:click.self="closeUpload"></div>
        <div class="modal livewire-modal ft-doc-upload-modal" wire:key="document-upload-modal">
            <div class="modal-head">
                <div>
                    <h2>Upload document</h2>
                    <div class="small muted">Link the file to a visible Job and optionally to one of your assigned tasks.</div>
                </div>
                <button type="button" class="close-btn" wire:click="closeUpload">×</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Job *</label>
                        <select wire:model.live="uploadJobId">
                            <option value="">Select Job</option>
                            @foreach($jobs as $j)
                                <option value="{{ $j->id }}">{{ $j->job_number }} · {{ $j->title }}</option>
                            @endforeach
                        </select>
                        @error('uploadJobId')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label>Task</label>
                        <select wire:model="uploadTaskId">
                            <option value="">Job-level document</option>
                            @foreach($uploadTasks as $task)
                                <option value="{{ $task->id }}">{{ $task->phase?->short_name }} · {{ $task->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field full">
                        <label>Document type *</label>
                        <select wire:model="uploadCategory">
                            @foreach($categories as $cat)<option>{{ $cat->name }}</option>@endforeach
                        </select>
                        @error('uploadCategory')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field full">
                        <label
                            class="upload-zone ft-livewire-upload-zone"
                            data-file-dropzone
                            for="document-page-upload-input"
                        >
                            <input
                                id="document-page-upload-input"
                                type="file"
                                wire:model="documentUploads"
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv"
                            >
                            <b>Drop files here or browse</b>
                            <div class="small muted" data-drop-status>PDF, DOCX, XLSX, JPG, PNG, ZIP, TXT or CSV · Max 20 MB each</div>
                        </label>

                        <div class="ft-file-upload-progress" wire:loading wire:target="documentUploads">
                            Preparing selected files…
                        </div>

                        @if(count($documentUploads))
                            <div class="ft-upload-ready-list">
                                @foreach($documentUploads as $file)
                                    <span>{{ $file->getClientOriginalName() }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @error('documentUploads')<div class="validation-error">{{ $message }}</div>@enderror
                @error('documentUploads.*')<div class="validation-error">{{ $message }}</div>@enderror
            </div>

            <div class="modal-foot">
                <button type="button" class="ghost" wire:click="closeUpload">Cancel</button>
                <button
                    type="button"
                    class="primary"
                    wire:click="storeDocuments"
                    wire:loading.attr="disabled"
                    wire:target="documentUploads,storeDocuments"
                    @disabled(count($documentUploads) === 0)
                >
                    <span wire:loading.remove wire:target="storeDocuments">Upload document</span>
                    <span wire:loading wire:target="storeDocuments">Uploading…</span>
                </button>
            </div>
        </div>
    @endif
</div>
