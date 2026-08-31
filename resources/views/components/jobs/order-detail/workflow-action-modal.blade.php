@props(['job', 'task', 'config' => [], 'step' => 'main', 'payload' => [], 'emailFallback' => false, 'emailFallbackMessage' => '', 'emailFallbackAttempts' => 0])
@php
    $variant = (string) ($config['variant'] ?? 'confirm');
    $title = (string) ($config['title'] ?? 'Task action');
    $copy = (string) ($config['copy'] ?? 'Confirm this workflow action.');
    $choices = collect($config['choices'] ?? ['confirm' => 'Confirm']);
    $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
    $automationKey = $workflowActions->automationKey($task);
    $latestArtworkTask = $job->tasks->first(fn($candidate) => $workflowActions->automationKey($candidate) === 'ART_PREPARE_UPLOAD');
    $artworkDocs = $latestArtworkTask
        ? $job->documents->where('task_id', $latestArtworkTask->id)->sortBy('created_at')->values()
        : collect();
    $artworkVersion = max(1, (int) ($artworkDocs->max('version') ?? 0));
    $latestArtworkDocuments = $artworkDocs->where('version', $artworkVersion)->sortBy('id')->values();
    $latestArtwork = $latestArtworkDocuments->last();
    $activeItems = \App\Support\OrderDetailPresenter::activeItems($job);
    $firstItem = $activeItems->first();
    $productName = (string) ($firstItem?->product_name ?: $job->product ?: 'Order product');
    $supplierName = ($firstItem && $firstItem->relationLoaded('supplier'))
        ? (string) ($firstItem->supplier?->name ?: 'Supplier')
        : 'Supplier';
    $orderNumber = $job->displayOrderNumber();
    $clientName = (string) ($job->client?->name ?: 'Client');
    $ownerName = (string) ($job->owner?->name ?: $job->coordinator?->name ?: 'FlowTrack');
    $orderTotal = (float) $activeItems->sum(fn($item) => (float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 0));
    $emailHandoffPreview = in_array($variant, ['purchase_order_email', 'artwork_email'], true)
        ? app(\App\Services\Orders\OrderWorkflowEmailService::class)->preview($task, auth()->user())
        : [];
    $emailServiceEnabled = (bool) ($emailHandoffPreview['email_service_enabled'] ?? true);
    if (! $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)) {
        $title = $variant === 'artwork_email' ? 'Complete Artwork Handoff' : 'Complete Purchase Order Handoff';
        $copy = 'Order email service is disabled by an administrator. Review the handoff details, then continue without sending email.';
    }
    $emailFallbackDocumentId = (int) ($emailHandoffPreview['document_id'] ?? 0);
    $emailFallbackDocument = $emailFallbackDocumentId > 0
        ? $job->documents->firstWhere('id', $emailFallbackDocumentId)
        : null;
    $emailFallbackAttachmentLabel = $variant === 'artwork_email' ? 'Artwork' : 'Purchase Order';

    // Only the main Artwork preview/handoff screens need the large landscape
    // layout. Revision/issue follow-up steps must stay on the normal compact
    // dialog size even when they originate from an Artwork task.
    $isArtworkPreviewModal = $step === 'main'
        && in_array($variant, ['artwork_review', 'artwork_email', 'client_erp'], true);
    // Billing and Payment render validation inside a two-column form. Give
    // those dialogs a stable validation layout so field errors never resize
    // or horizontally shift the popup after submit.
    $usesStableFinanceValidation = $step === 'main'
        && in_array($variant, ['invoice_prepare', 'payment'], true);
    $modalWide = $variant === 'courier_label';

    if ($step === 'sample') {
        $title = 'Is a Sample or Swatch Required?';
        $copy = 'The artwork is approved. Decide whether supplier sample approval is required before Production.';
    } elseif ($step === 'revision') {
        $title = $automationKey === 'ART_INTERNAL_REVIEW' ? 'Request Artwork Revision' : 'Client Revision Request';
        $copy = $automationKey === 'ART_INTERNAL_REVIEW'
            ? 'Add the internal revision instructions before returning the Artwork task to upload.'
            : 'Record the client feedback before restarting the artwork approval cycle.';
    } elseif ($step === 'issue') {
        $title = $automationKey === 'QC_CHECK' ? 'Report QC Issue' : 'Report Production Issue';
        $copy = 'Describe the issue before notifying the supplier and blocking progression.';
    }
@endphp
<div class="ft-order-task-document-modal-backdrop" wire:key="order-workflow-action-modal-{{ $task->id }}-{{ $step }}" wire:click.self="closeOrderWorkflowAction">
    <section class="ft-order-task-document-modal ft-order-workflow-action-modal {{ $isArtworkPreviewModal ? 'ft-order-workflow-action-modal--artwork-preview' : ($modalWide ? 'ft-order-workflow-action-modal--wide' : '') }} {{ $usesStableFinanceValidation ? 'ft-order-workflow-action-modal--stable-finance-validation' : '' }}" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="order-workflow-action-modal-title">
        <header class="ft-order-task-document-modal-head">
            <div><h2 id="order-workflow-action-modal-title">{{ $title }}</h2><p>{{ $copy }}</p></div>
            <button type="button" wire:click="closeOrderWorkflowAction" aria-label="Close">×</button>
        </header>

        <div class="ft-order-task-document-modal-body ft-prototype-action-body">
            @if($step === 'sample')
                <div class="ft-prototype-choice-grid">
                    <button type="button" wire:click="submitOrderWorkflowAction('sample_no')">
                        <span class="ft-prototype-choice-icon">→</span>
                        <strong>No</strong>
                        <small>Go directly to Production</small>
                    </button>
                    <button type="button" wire:click="submitOrderWorkflowAction('sample_yes')">
                        <span class="ft-prototype-choice-icon">✓</span>
                        <strong>Yes</strong>
                        <small>Activate Sample Approval</small>
                    </button>
                </div>
            @elseif($step === 'revision')
                <label class="ft-prototype-field">
                    <span>{{ $automationKey === 'ART_INTERNAL_REVIEW' ? 'Revision instructions' : 'Client feedback' }}</span>
                    <textarea wire:model="orderWorkflowActionComment" rows="5" placeholder="Describe the required artwork changes..."></textarea>
                    @error('orderWorkflowActionComment')<p class="validation-error">{{ $message }}</p>@enderror
                </label>
            @elseif($step === 'issue')
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Issue category</span><select wire:model="orderWorkflowActionPayload.issue_category"><option>Fabric color variance</option><option>Print color mismatch</option><option>Incorrect dimensions</option><option>Damaged items</option><option>Other</option></select>@error('orderWorkflowActionPayload.issue_category')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Supplier</span><input value="{{ $supplierName }}" disabled></label>
                </div>
                <label class="ft-prototype-field"><span>Description</span><textarea wire:model="orderWorkflowActionComment" rows="5" placeholder="Describe the issue and corrective action required..."></textarea>@error('orderWorkflowActionComment')<p class="validation-error">{{ $message }}</p>@enderror</label>
                <div class="ft-prototype-file-placeholder"><strong>Screenshot / photo</strong><span>Supporting images/documents can be added to the task after the issue is recorded.</span></div>
                <div class="ft-prototype-email-preview"><b>Email preview</b><span>The issue notification will be recorded for {{ $supplierName }} with this Order and task.</span></div>
            @elseif($variant === 'purchase_order_email')
                <div class="ft-order-task-document-target">
                    <span class="ft-order-task-document-target-icon">PO</span>
                    <div>
                        <small>ATTACHMENT</small>
                        <strong>{{ $emailHandoffPreview['document_name'] ?? 'No Purchase Order uploaded' }}</strong>
                        <span>{{ filled($emailHandoffPreview['document_version'] ?? null) ? 'Version '.(int) $emailHandoffPreview['document_version'] : 'Upload the Purchase Order in the previous task first' }}</span>
                    </div>
                    <em>{{ $emailHandoffPreview['recipient_count'] ?? 0 }} recipient{{ (int) ($emailHandoffPreview['recipient_count'] ?? 0) === 1 ? '' : 's' }}</em>
                </div>
                <x-email.handoff-preview
                    :preview="$emailHandoffPreview"
                    :defaultSubject="'Purchase Order ready — '.$orderNumber"
                    emptyRecipientText="No active user with a valid email is assigned to this Order's Artwork phase."
                />
                @error('orderWorkflowActionEmail')<p class="validation-error">{{ $message }}</p>@enderror
            @elseif($variant === 'artwork_review' || $variant === 'artwork_email' || $variant === 'client_erp')
                <div class="ft-prototype-artwork-preview">
                    <div class="ft-prototype-artwork-canvas">
                        @if($latestArtwork)
                            @php $extension = strtolower(pathinfo((string) $latestArtwork->name, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($extension, ['jpg','jpeg','png','webp','gif'], true))
                                <img src="{{ route('documents.open', $latestArtwork) }}" alt="Latest artwork preview">
                            @else
                                <div class="ft-prototype-artwork-file"><span>{{ strtoupper($extension ?: 'FILE') }}</span><strong>{{ $latestArtwork->name }} · Version {{ max(1, (int) $latestArtwork->version) }}</strong><a href="{{ route('documents.open', $latestArtwork) }}" target="_blank" rel="noopener">Open artwork</a></div>
                            @endif
                        @else
                            <div class="ft-prototype-artwork-file"><span>ART</span><strong>Artwork file</strong><small>No previewable image available</small></div>
                        @endif
                    </div>
                    <div class="ft-prototype-artwork-meta">
                        <small>LATEST ARTWORK · {{ $latestArtworkDocuments->count() }} FILE{{ $latestArtworkDocuments->count() === 1 ? '' : 'S' }}</small>
                        <h3>{{ $productName }}</h3>
                        <dl>
                            <div><dt>Version</dt><dd>V{{ $artworkVersion }}</dd></div>
                            <div><dt>Files</dt><dd>{{ $latestArtworkDocuments->isNotEmpty() ? $latestArtworkDocuments->pluck('name')->implode(', ') : 'Artwork file' }}</dd></div>
                            <div><dt>Uploaded by</dt><dd>{{ $latestArtwork?->uploader?->name ?: $task->assignee?->name ?: 'FlowTrack' }}</dd></div>
                            <div><dt>Client</dt><dd>{{ $clientName }}</dd></div>
                        </dl>
                        @if($latestArtwork)
                            <div class="ft-prototype-artwork-actions"><a href="{{ route('documents.open', $latestArtwork) }}" target="_blank" rel="noopener">Open</a><a href="{{ route('documents.download', $latestArtwork) }}">Download</a></div>
                        @endif
                        @if($artworkDocs->isNotEmpty())
                            <div class="ft-prototype-version-list">
                                @foreach($artworkDocs->reverse()->values() as $index => $doc)
                                    <div>
                                        <span class="ft-prototype-version-file">
                                            <strong>{{ $doc->name }} · Version {{ max(1, (int) $doc->version) }}</strong>
                                            <small>{{ \App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A') }}</small>
                                        </span>
                                        <span class="ft-prototype-version-status">
                                            <b>{{ (int) $doc->version === $artworkVersion ? 'Latest' : 'Archived' }}</b>
                                            <a href="{{ route('documents.open', $doc) }}" target="_blank" rel="noopener">Open</a>
                                            <a href="{{ route('documents.download', $doc) }}">Download</a>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @if($variant === 'artwork_email')
                    <x-email.handoff-preview
                        :preview="$emailHandoffPreview"
                        :defaultSubject="'Artwork ready — '.$orderNumber"
                        emptyRecipientText="No active user with a valid email has the Order Team role in Users & role assignments."
                    />
                    @error('orderWorkflowActionEmail')<p class="validation-error">{{ $message }}</p>@enderror
                @elseif($variant === 'client_erp')
                    <label class="ft-prototype-field ft-prototype-field--top"><span>Client ERP reference</span><input wire:model="orderWorkflowActionPayload.erp_reference" placeholder="Client ERP reference">@error('orderWorkflowActionPayload.erp_reference')<p class="validation-error">{{ $message }}</p>@enderror</label>
                @endif
            @elseif($variant === 'client_decision')
                <p class="ft-prototype-modal-copy">Choose the decision received from {{ $clientName }} for Artwork V{{ $artworkVersion }}.</p>
                <div class="ft-prototype-choice-grid">
                    <button type="button" wire:click="submitOrderWorkflowAction('revise')"><span class="ft-prototype-choice-icon">↻</span><strong>Client Requested Revision</strong><small>Restart the artwork revision cycle</small></button>
                    <button type="button" wire:click="submitOrderWorkflowAction('approved')"><span class="ft-prototype-choice-icon">✓</span><strong>Client Approved Artwork</strong><small>Continue to sample decision</small></button>
                </div>
            @elseif($variant === 'estimated_delivery')
                <div class="ft-prototype-required-date-panel">
                    <div class="ft-prototype-required-date-icon">*</div>
                    <div>
                        <strong>Required before Production</strong>
                        <span>Set the estimated delivery date to unlock Start Production.</span>
                    </div>
                </div>
                <label class="ft-prototype-field ft-prototype-field--top">
                    <span>Estimated delivery date</span>
                    <input
                        type="date"
                        class="ft-prototype-clickable-date"
                        wire:model="orderWorkflowActionPayload.estimated_delivery_date"
                        onclick="this.focus({ preventScroll: true }); if (typeof this.showPicker === 'function') { try { this.showPicker(); } catch (e) {} }"
                        aria-label="Estimated delivery date. Click anywhere in the field to open the calendar."
                    >
                    @error('orderWorkflowActionPayload.estimated_delivery_date')<p class="validation-error">{{ $message }}</p>@enderror
                </label>
            @elseif($variant === 'production_check')
                <div class="ft-prototype-choice-grid">
                    <button type="button" class="danger-choice" wire:click="submitOrderWorkflowAction('issue')"><span class="ft-prototype-choice-icon">!</span><strong>Report Issue</strong><small>Notify supplier and keep Production open</small></button>
                    <button type="button" wire:click="submitOrderWorkflowAction('confirm')"><span class="ft-prototype-choice-icon">✓</span><strong>No Issue</strong><small>Continue to Production completion</small></button>
                </div>
            @elseif($variant === 'issue_resolution')
                <label class="ft-prototype-field"><span>Resolution</span><textarea wire:model="orderWorkflowActionComment" rows="5" placeholder="Describe the corrective action and resolution..."></textarea>@error('orderWorkflowActionComment')<p class="validation-error">{{ $message }}</p>@enderror</label>
            @elseif($variant === 'qc_check')
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Quantity received</span><input type="number" min="1" wire:model="orderWorkflowActionPayload.qty_received">@error('orderWorkflowActionPayload.qty_received')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Quantity inspected</span><input type="number" min="1" wire:model="orderWorkflowActionPayload.qty_inspected">@error('orderWorkflowActionPayload.qty_inspected')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Accepted</span><input type="number" min="0" wire:model="orderWorkflowActionPayload.qty_accepted">@error('orderWorkflowActionPayload.qty_accepted')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Rejected</span><input type="number" min="0" wire:model="orderWorkflowActionPayload.qty_rejected">@error('orderWorkflowActionPayload.qty_rejected')<p class="validation-error">{{ $message }}</p>@enderror</label>
                </div>
                <label class="ft-prototype-field"><span>QC comments</span><textarea wire:model="orderWorkflowActionPayload.qc_comments" rows="4" placeholder="Record stitching, dimensions, packaging, print registration, or other QC notes..."></textarea>@error('orderWorkflowActionPayload.qc_comments')<p class="validation-error">{{ $message }}</p>@enderror</label>
                <div class="ft-prototype-choice-grid">
                    <button type="button" class="danger-choice" wire:click="submitOrderWorkflowAction('issue')"><span class="ft-prototype-choice-icon">!</span><strong>Report Issue</strong><small>Open a supplier-resolution issue</small></button>
                    <button type="button" wire:click="submitOrderWorkflowAction('pass')"><span class="ft-prototype-choice-icon">✓</span><strong>QC Passed</strong><small>Continue toward Shipment</small></button>
                </div>
            @elseif($variant === 'shipment_info')
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Recipient</span><input wire:model="orderWorkflowActionPayload.recipient">@error('orderWorkflowActionPayload.recipient')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Contact</span><input wire:model="orderWorkflowActionPayload.contact">@error('orderWorkflowActionPayload.contact')<p class="validation-error">{{ $message }}</p>@enderror</label>
                </div>
                <label class="ft-prototype-field"><span>Delivery address</span><textarea wire:model="orderWorkflowActionPayload.address" rows="4"></textarea>@error('orderWorkflowActionPayload.address')<p class="validation-error">{{ $message }}</p>@enderror</label>
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Packages</span><input wire:model="orderWorkflowActionPayload.packages" placeholder="e.g. 24 cartons">@error('orderWorkflowActionPayload.packages')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Total weight</span><input wire:model="orderWorkflowActionPayload.weight" placeholder="e.g. 312 kg">@error('orderWorkflowActionPayload.weight')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Dimensions / carton</span><input wire:model="orderWorkflowActionPayload.dimensions" placeholder="e.g. 60 × 45 × 40 cm">@error('orderWorkflowActionPayload.dimensions')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Declared value</span><input wire:model="orderWorkflowActionPayload.declared_value" placeholder="{{ number_format($orderTotal,2) }}">@error('orderWorkflowActionPayload.declared_value')<p class="validation-error">{{ $message }}</p>@enderror</label>
                </div>
            @elseif($variant === 'courier_label')
                <div class="ft-prototype-label-preview">
                    <div><small>SHIP TO</small><h3>{{ mb_strtoupper($clientName) }}</h3><p>{!! nl2br(e((string) ($payload['address'] ?? $job->shipping_address ?? ''))) !!}</p><div class="ft-prototype-barcode"></div><b>FLOWTRACK · {{ $orderNumber }}</b></div>
                    <div><small>SERVICE</small><h3>Courier<br>Shipping</h3><p><b>{{ $payload['packages'] ?? 'Packages confirmed' }}</b><br>{{ $payload['weight'] ?? '' }}</p><strong class="ft-prototype-label-code">SHIP</strong></div>
                </div>
            @elseif($variant === 'ship_package')
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Shipping provider</span><select wire:model="orderWorkflowActionPayload.carrier"><option>UPS</option><option>FedEx</option><option>DHL</option><option>Other</option></select>@error('orderWorkflowActionPayload.carrier')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Tracking number</span><input wire:model="orderWorkflowActionPayload.tracking_number">@error('orderWorkflowActionPayload.tracking_number')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Shipment date</span><input type="date" wire:model="orderWorkflowActionPayload.shipment_date">@error('orderWorkflowActionPayload.shipment_date')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Estimated delivery</span><input type="date" wire:model="orderWorkflowActionPayload.estimated_delivery_date">@error('orderWorkflowActionPayload.estimated_delivery_date')<p class="validation-error">{{ $message }}</p>@enderror</label>
                </div>
            @elseif($variant === 'invoice_prepare')
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Invoice number</span><input wire:model="orderWorkflowActionPayload.invoice_number">@error('orderWorkflowActionPayload.invoice_number')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Invoice date</span><input type="date" wire:model="orderWorkflowActionPayload.invoice_date">@error('orderWorkflowActionPayload.invoice_date')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Amount</span><input type="number" step="0.01" wire:model="orderWorkflowActionPayload.invoice_amount">@error('orderWorkflowActionPayload.invoice_amount')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Currency</span><select wire:model="orderWorkflowActionPayload.invoice_currency"><option>USD</option><option>GBP</option><option>EUR</option></select>@error('orderWorkflowActionPayload.invoice_currency')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Payment terms</span><select wire:model="orderWorkflowActionPayload.payment_terms"><option>Net 15</option><option>Net 30</option><option>Due on receipt</option></select>@error('orderWorkflowActionPayload.payment_terms')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Due date</span><input type="date" wire:model="orderWorkflowActionPayload.invoice_due_date">@error('orderWorkflowActionPayload.invoice_due_date')<p class="validation-error">{{ $message }}</p>@enderror</label>
                </div>
                <div class="ft-prototype-email-preview"><b>Included order:</b> {{ $orderNumber }}<br><b>Client:</b> {{ $clientName }}<br><b>Total:</b> {{ $payload['invoice_currency'] ?? 'USD' }} {{ number_format((float) ($payload['invoice_amount'] ?? $orderTotal), 2) }}</div>
            @elseif($variant === 'invoice_send')
                <div class="ft-prototype-email-preview"><b>To:</b> Client accounts contact<br><b>Subject:</b> Invoice {{ $payload['invoice_number'] ?: '—' }} — {{ $orderNumber }}<br><br>Hello {{ $clientName }},<br><br>Please find attached the invoice for Order {{ $orderNumber }}.<br><br>Amount due: {{ $payload['invoice_currency'] ?? 'USD' }} {{ number_format((float) ($payload['invoice_amount'] ?? $orderTotal), 2) }}<br>Due date: {{ $payload['invoice_due_date'] ?: 'As agreed' }}<br><br>Regards,<br>{{ $ownerName }}</div>
            @elseif($variant === 'payment')
                <div class="ft-prototype-form-grid">
                    <label class="ft-prototype-field"><span>Outstanding balance</span><input value="{{ number_format((float) ($payload['payment_amount'] ?? $orderTotal), 2) }}" disabled></label>
                    <label class="ft-prototype-field"><span>Payment amount</span><input type="number" min="0.01" step="0.01" wire:model="orderWorkflowActionPayload.payment_amount">@error('orderWorkflowActionPayload.payment_amount')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Payment date</span><input type="date" wire:model="orderWorkflowActionPayload.payment_date">@error('orderWorkflowActionPayload.payment_date')<p class="validation-error">{{ $message }}</p>@enderror</label>
                    <label class="ft-prototype-field"><span>Payment reference</span><input wire:model="orderWorkflowActionPayload.payment_reference">@error('orderWorkflowActionPayload.payment_reference')<p class="validation-error">{{ $message }}</p>@enderror</label>
                </div>
                <label class="ft-prototype-field"><span>Notes</span><textarea wire:model="orderWorkflowActionPayload.payment_notes" rows="4" placeholder="Bank transfer received and matched to invoice..."></textarea>@error('orderWorkflowActionPayload.payment_notes')<p class="validation-error">{{ $message }}</p>@enderror</label>
            @else
                <div class="ft-order-task-document-target"><span class="ft-order-task-document-target-icon">⌘</span><div><small>WORKFLOW TASK</small><strong>{{ $task->title }}</strong><span>{{ $task->phase?->name ?? 'Order workflow' }}</span></div><em>{{ $task->status ?: 'Ready' }}</em></div>
            @endif

            @if($emailFallback && $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true))
                <div class="ft-prototype-email-preview ft-prototype-email-preview--error" role="alert" aria-live="polite">
                    <b>Email delivery failed after {{ max(3, (int) $emailFallbackAttempts) }} attempts</b>
                    <span>{{ $emailFallbackMessage }}</span>
                    @if($emailFallbackDocument)
                        <div class="ft-prototype-artwork-actions">
                            <a href="{{ route('documents.download', $emailFallbackDocument) }}">Download {{ $emailFallbackAttachmentLabel }}</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        @php
            // The three main choice dialogs render their actions inside the body.
            // Once one of those choices opens a nested revision/issue dialog, the
            // normal footer must return so the user can actually submit it.
            $usesInlineWorkflowActions = $step === 'main'
                && in_array($variant, ['client_decision','production_check','qc_check'], true);
        @endphp
        @unless($usesInlineWorkflowActions || $step === 'sample')
            <footer class="ft-order-task-document-modal-actions ft-order-workflow-action-buttons">
                <button type="button" class="secondary" wire:click="closeOrderWorkflowAction">Cancel</button>
                @if($step === 'revision')
                    <button type="button" class="primary" wire:click="submitOrderWorkflowAction('revise')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">{{ $automationKey === 'ART_INTERNAL_REVIEW' ? 'Request Revision' : 'Activate Revision Task' }}</button>
                @elseif($step === 'issue')
                    <button type="button" class="danger" wire:click="submitOrderWorkflowAction('issue')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">Report Issue</button>
                @elseif($emailFallback && $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true))
                    <button type="button" class="secondary" wire:click="submitOrderWorkflowAction('confirm')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">Try email again</button>
                    <button type="button" class="primary" wire:click="completeOrderWorkflowEmailTaskAfterFailure" wire:loading.attr="disabled" wire:target="completeOrderWorkflowEmailTaskAfterFailure">Complete task</button>
                @else
                    @foreach($choices as $decision => $label)
                        @php
                            $actionLabel = ! $emailServiceEnabled && in_array($variant, ['purchase_order_email', 'artwork_email'], true)
                                ? 'Complete without email'
                                : $label;
                        @endphp
                        <button type="button" class="{{ in_array($decision, ['revise','issue'], true) ? 'danger' : 'primary' }}" wire:click="submitOrderWorkflowAction('{{ $decision }}')" wire:loading.attr="disabled" wire:target="submitOrderWorkflowAction">{{ $actionLabel }}</button>
                    @endforeach
                @endif
            </footer>
        @endunless
    </section>
</div>
