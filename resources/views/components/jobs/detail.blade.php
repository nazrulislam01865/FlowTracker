@props([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'overviewTaskDocumentModalTask'=>null,
    'overviewTaskAvailableDocuments'=>collect(),
    'showOverviewTaskDocumentModal'=>false,
    'showAddOrderTaskForm'=>false,
    'newOrderTaskAssigneeId'=>null,
    'overviewTaskDocumentSource'=>'upload',
    'overviewTaskDocumentUpload'=>null,
    'overviewTaskExistingDocumentId'=>null,
    'overviewTaskLinkFormTaskId'=>null,
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
    'inquiryResults'=>collect(),
    'inquirySearch'=>'',
    'selectedLinkInquiry'=>null,
    'showInquiryLinkConfirm'=>false,
    'showInquiryUnlinkConfirm'=>false,
    'canManageInquiryLink'=>false,
    'linkedInquiryCanOpen'=>false,
    'financeSummary'=>null,
    'financeContacts'=>null,
    'financeUsers'=>null,
    'canCreateFinance'=>false,
    'canEditFinance'=>false,
    'showCreateInvoiceModal'=>false,
    'invoiceType'=>'Final invoice',
    'invoiceCurrency'=>'USD',
    'invoiceIssueDate'=>'',
    'invoicePaymentTerms'=>'15',
    'invoiceDueDate'=>'',
    'invoiceBillingContactId'=>null,
    'invoiceLineItems'=>[],
    'invoicePurchaseOrderReference'=>'',
    'invoiceNotes'=>'',
    'invoiceTaxRate'=>'0',
    'invoiceSupportingDocument'=>null,
    'invoiceEmailAfterCreation'=>true,
    'showRecordPaymentModal'=>false,
    'paymentInvoiceId'=>null,
    'paymentDate'=>'',
    'paymentMethod'=>'Bank transfer',
    'paymentAmount'=>'',
    'paymentReference'=>'',
    'paymentNotes'=>'',
    'showCollectionUpdateModal'=>false,
    'collectionOwnerId'=>null,
    'collectionFollowUpDate'=>'',
    'collectionNextFollowUpDate'=>'',
    'collectionNote'=>'',
])
@php
    $team = \App\Support\JobDetailPresenter::team($job);
    $tabs = ['overview'=>'Overview','inquiry'=>'Inquiry'];
    if (app(\App\Services\AccessControlService::class)->can(auth()->user(), 'finance', 'view')) $tabs['finance'] = 'Invoices & Payments';
    $jobPriorityColor = app(\App\Services\MasterDataService::class)->displayColorFor('priority', (string) $job->priority);
@endphp
<div {{ $attributes->class('ft-job-detail-page ft-exact-job-detail') }}>
    <div class="ft-detail-toolbar ft-exact-job-header">
        <div class="ft-job-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <span>Orders</span><span>/</span>
                <a class="ft-copyable-id-link" href="{{ route('jobs.index', ['open'=>$job->id]) }}" wire:navigate>{{ $job->displayOrderNumber() }}</a>
                <button type="button" class="ft-copy-id-btn" title="Copy Order ID" aria-label="Copy {{ $job->displayOrderNumber() }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($job->displayOrderNumber())); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
            </div>
            <h1
                class="ft-editable-job-title ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-title'), label: 'Order name', value: @js($job->title), display: @js($job->title) })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span x-show="!editing" x-text="display">{{ $job->title }}</span>
                @if(app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job))
                    <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit order title" title="Edit order name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.jobTitle.focus())">✎</button>
                    <input x-ref="jobTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                        x-on:keydown.escape.prevent="cancelEdit()"
                        x-on:keydown.enter.prevent="$event.target.blur()"
                        x-on:blur="if (editing) { draftValue.trim() === value ? cancelEdit() : commit(draftValue.trim(), draftValue.trim(), () => $wire.updateJobTextField({{ $job->id }}, 'title', draftValue.trim())) }">
                    <x-ui.inline-save-state />
                @endif
            </h1>
            <div class="ft-order-header-meta" aria-label="Order information">
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span>
                    <span class="ft-client-inline-identity"><x-ui.client-logo :client="$job->client" :name="$job->client?->name ?: 'Client'" :size="20" /><span>Client <strong>{{ $job->client?->name ?: '—' }}</strong></span></span>
                </span>
                <span class="ft-order-header-meta-separator" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item ft-order-header-reference">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.5h7l4 4V20.5H7z"></path><path d="M14 3.5v4h4"></path></svg></span>
                    <span>Reference <strong>{{ $job->order_number ?: '—' }}</strong></span>
                    @if($job->order_number)
                        <button type="button" class="ft-copy-id-btn ft-order-header-copy" title="Copy Reference Number" aria-label="Copy reference number {{ $job->order_number }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($job->order_number)); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                    @endif
                </span>
                <span class="ft-order-header-meta-separator" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span>
                    <span>Created by <strong>{{ $job->creator?->name ?: 'System' }}</strong></span>
                </span>
                <span class="ft-order-header-meta-separator" aria-hidden="true">•</span>
                <span class="ft-order-header-meta-item">
                    <span class="ft-order-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 10h16"></path></svg></span>
                    <span>Created <strong>{{ $job->created_at ? \App\Support\UserLocalTime::format($job->created_at, 'M j, Y') : '—' }}@if($job->created_at) at {{ \App\Support\UserLocalTime::format($job->created_at, 'g:i A') }}@endif</strong></span>
                </span>
            </div>
            <div class="ft-exact-job-meta ft-order-status-row" aria-label="Order status">
                <span class="ft-soft-pill {{ \App\Support\JobDetailPresenter::healthClass($job->health) }}">{{ $job->health }}</span>
                <span class="ft-soft-pill {{ $jobPriorityColor ? 'ft-master-color' : 'red' }}" style="{{ \App\Support\MasterColor::style($jobPriorityColor) }}">{{ $job->priority }}</span>
                <span class="ft-soft-pill purple">{{ $job->phase?->name ?? $job->status }}</span>
            </div>
        </div>
        <div class="ft-detail-actions ft-exact-job-team" aria-label="Order team">
            <div class="ft-team-stack">
                @foreach($team->take(4) as $member)<x-ui.avatar :user="$member" :name="$member->name" :size="28"/>@endforeach
                @if($team->count()>4)<span class="ft-avatar-more small">+{{ $team->count()-4 }}</span>@endif
            </div>
        </div>
    </div>

    <div class="{{ $detailTab==='finance' ? 'ft-finance-tab-action-row' : '' }}">
        <nav class="ft-detail-tabs ft-exact-tabs">
            @foreach($tabs as $key=>$label)
                <button class="{{ $detailTab===$key ? 'active' : '' }}" wire:click="setDetailTab('{{ $key }}')">
                    {{ $label }}
                    @if($key==='documents')<span>{{ $job->relationLoaded('documents') ? $job->documents->count() : (int) ($job->documents_count ?? 0) }}</span>@endif
                    @if($key==='inquiry')<span>{{ $job->source_inquiry_id ? 1 : 0 }}</span>@endif
                </button>
            @endforeach
        </nav>
        @if($detailTab==='finance' && $canCreateFinance)
            <div class="ft-finance-tab-actions">
                <button type="button" class="ft-finance-btn secondary" wire:click="openRecordPayment">Record Payment</button>
                <button type="button" class="ft-finance-btn primary" wire:click="openCreateInvoice"><span>＋</span> Create Invoice</button>
            </div>
        @endif
    </div>

    @if($detailTab==='overview')
        <x-jobs.detail-overview
            :job="$job"
            :expanded-phase-ids="$expandedPhaseIds"
            :task-statuses="$taskStatuses"
            :users="$users"
            :mention-users="$mentionUsers"
            :priorities="$priorities"
            :products="$products"
            :categories="$categories"
            :job-task-search="$jobTaskSearch"
            :activity-tab="$activityTab"
            :activity-page="$activityPage"
            :focus-comment="$focusComment"
            :job-document-uploads="$jobDocumentUploads"
            :overview-task-document-modal-task="$overviewTaskDocumentModalTask"
            :overview-task-available-documents="$overviewTaskAvailableDocuments"
            :show-overview-task-document-modal="$showOverviewTaskDocumentModal"
            :overview-task-document-source="$overviewTaskDocumentSource"
            :overview-task-document-upload="$overviewTaskDocumentUpload"
            :overview-task-existing-document-id="$overviewTaskExistingDocumentId"
            :overview-task-link-form-task-id="$overviewTaskLinkFormTaskId"
            :show-add-order-task-form="$showAddOrderTaskForm"
            :new-order-task-assignee-id="$newOrderTaskAssigneeId"
        />
    @elseif($detailTab==='inquiry')
        <x-jobs.detail-inquiry
            :job="$job"
            :results="$inquiryResults"
            :search="$inquirySearch"
            :selected-inquiry="$selectedLinkInquiry"
            :show-link-confirm="$showInquiryLinkConfirm"
            :show-unlink-confirm="$showInquiryUnlinkConfirm"
            :can-manage="$canManageInquiryLink"
            :linked-inquiry-can-open="$linkedInquiryCanOpen"
        />
    @elseif($detailTab==='finance')
        <x-jobs.finance.detail
            :job="$job"
            :summary="$financeSummary"
            :contacts="$financeContacts ?? collect()"
            :users="$financeUsers ?? collect()"
            :can-create="$canCreateFinance"
            :can-edit="$canEditFinance"
            :show-create-invoice-modal="$showCreateInvoiceModal"
            :invoice-type="$invoiceType"
            :invoice-currency="$invoiceCurrency"
            :invoice-issue-date="$invoiceIssueDate"
            :invoice-payment-terms="$invoicePaymentTerms"
            :invoice-due-date="$invoiceDueDate"
            :invoice-billing-contact-id="$invoiceBillingContactId"
            :invoice-line-items="$invoiceLineItems"
            :invoice-purchase-order-reference="$invoicePurchaseOrderReference"
            :invoice-notes="$invoiceNotes"
            :invoice-tax-rate="$invoiceTaxRate"
            :invoice-supporting-document="$invoiceSupportingDocument"
            :invoice-email-after-creation="$invoiceEmailAfterCreation"
            :show-record-payment-modal="$showRecordPaymentModal"
            :payment-invoice-id="$paymentInvoiceId"
            :payment-date="$paymentDate"
            :payment-method="$paymentMethod"
            :payment-amount="$paymentAmount"
            :payment-reference="$paymentReference"
            :payment-notes="$paymentNotes"
            :show-collection-update-modal="$showCollectionUpdateModal"
            :collection-owner-id="$collectionOwnerId"
            :collection-follow-up-date="$collectionFollowUpDate"
            :collection-next-follow-up-date="$collectionNextFollowUpDate"
            :collection-note="$collectionNote"
        />
    @endif
</div>
