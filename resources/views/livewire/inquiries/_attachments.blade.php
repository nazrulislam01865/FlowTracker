<section class="ft-detail-card ft-attachment-card">
    <h2>Attachments <span>{{ $inquiry->documents_count }}</span></h2>
    <div class="ft-upload-zone compact ft-task-upload-zone">
        @if($canEditInquiry)
            <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone for="inquiryOverviewUpload-{{ $inquiry->id }}">
                <input id="inquiryOverviewUpload-{{ $inquiry->id }}" type="file" wire:model="inquiryUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai">
                <span class="ft-paperclip">⌕</span>
                <div>Drop files here or <strong>browse</strong><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
            </label>
        @else
            <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to Inquiry attachments.</small></div></div>
        @endif
        @if(auth()->user()->canModule('documents','link'))
            <button class="ft-outline-btn ft-task-choose-document" type="button" wire:click="toggleInquiryDocumentPicker">Choose from Documents</button>
        @endif
    </div>

    @if(count($inquiryUploads ?? []))
        <div class="ft-upload-ready-row">
            <span>{{ count($inquiryUploads ?? []) }} file{{ count($inquiryUploads ?? []) === 1 ? '' : 's' }} ready</span>
            <button class="ft-new-job-btn" type="button" wire:click="uploadInquiryFiles">Upload &amp; link</button>
        </div>
    @endif
    @error('inquiryUploads')<div class="validation-error">{{ $message }}</div>@enderror
    @error('inquiryUploads.*')<div class="validation-error">{{ $message }}</div>@enderror

    @if($showInquiryDocumentPicker)
        <div class="ft-existing-document-picker ft-task-document-picker">
            <select wire:model="inquiryExistingDocumentId">
                <option value="">Select a stored client document</option>
                @foreach($availableInquiryDocuments as $stored)
                    <option value="{{ $stored->id }}">{{ $stored->name }}</option>
                @endforeach
            </select>
            <button class="ft-new-job-btn" type="button" wire:click="attachExistingInquiryDocument">Link document</button>
            <button class="ft-outline-btn" type="button" wire:click="toggleInquiryDocumentPicker">Cancel</button>
        </div>
        @error('inquiryExistingDocumentId')<div class="validation-error">{{ $message }}</div>@enderror
    @endif

    @if($inquiryDocuments)
        @foreach($inquiryDocuments as $document)
            <div class="ft-attachment-row" wire:key="inquiry-overview-doc-{{ $document->id }}">
                <span class="ft-file-type">{{ strtoupper(pathinfo($document->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                <b>{{ $document->name }}</b>
                <small>{{ $document->created_at ? \App\Support\UserLocalTime::format($document->created_at, 'M j, Y, g:i A') : '—' }}</small>
                <a class="ft-link-blue" href="{{ route('inquiries.documents.open', $document) }}" target="_blank" rel="noopener">Open</a>
                @if($canEditInquiry)
                    <button type="button" class="ft-doc-delete-button" wire:click="deleteInquiryDocument({{ $document->id }})" wire:confirm="Remove this attachment from the Inquiry?">×</button>
                @endif
            </div>
        @endforeach
        @if($inquiryDocuments->isEmpty())<div class="empty-state">No Inquiry attachments yet.</div>@endif
        @if($inquiryDocuments->lastPage() > 1)
            <div class="ft-activity-pagination">
                <span>Showing {{ $inquiryDocuments->firstItem() ?? 0 }}–{{ $inquiryDocuments->lastItem() ?? 0 }} of {{ $inquiryDocuments->total() }}</span>
                <div><button type="button" wire:click="previousPage('inquiryDocumentsPage')" @disabled($inquiryDocuments->onFirstPage())>Previous</button><span>Page {{ $inquiryDocuments->currentPage() }} of {{ $inquiryDocuments->lastPage() }}</span><button type="button" wire:click="nextPage('inquiryDocumentsPage')" @disabled(!$inquiryDocuments->hasMorePages())>Next</button></div>
            </div>
        @endif
    @endif
    <p class="ft-upload-note">Every file uploaded here is linked to this Inquiry and remains available from Inquiry Documents.</p>
</section>
