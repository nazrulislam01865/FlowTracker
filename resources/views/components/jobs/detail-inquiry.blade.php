@props([
    'job',
    'results'=>collect(),
    'search'=>'',
    'selectedInquiry'=>null,
    'showLinkConfirm'=>false,
    'showUnlinkConfirm'=>false,
    'canManage'=>false,
    'linkedInquiryCanOpen'=>false,
])
@php
    $linked = $job->relationLoaded('sourceInquiry') ? $job->sourceInquiry : null;
    $phaseName = $job->phase?->name ?? $job->status;
    $phaseSequence = (int) ($job->phase?->sequence ?? 1);
    $progress = max(0, min(100, (int) ($job->progress ?? 0)));
    $nextAction = trim((string) ($job->next_action ?? '')) ?: 'No action currently required';
    $selectedClientMatch = $selectedInquiry && (int) $selectedInquiry->client_id === (int) $job->client_id;
    $searchLength = mb_strlen(trim((string) $search));
    $masterData = app(\App\Services\MasterDataService::class);
    $inquiryService = app(\App\Services\InquiryService::class);
@endphp

<div class="ft-order-inquiry-link" wire:key="order-inquiry-link-{{ $job->id }}">
    <section class="ft-oil-summary" aria-label="Order summary">
        <div class="ft-oil-summary-card">
            <span class="ft-oil-summary-icon">▣</span>
            <span><small>Current phase</small><strong>{{ $phaseName }} · Phase {{ max(1, $phaseSequence) }}</strong></span>
        </div>
        <div class="ft-oil-summary-card">
            <span class="ft-oil-summary-icon">↗</span>
            <span><small>Overall progress</small><strong>{{ $progress }}%</strong><span class="ft-oil-progress"><i style="width:{{ $progress }}%"></i></span></span>
        </div>
        <div class="ft-oil-summary-card">
            <span class="ft-oil-summary-icon">⌘</span>
            <span><small>Next required action</small><strong>{{ $nextAction }}</strong></span>
        </div>
    </section>

    <section class="ft-oil-panel">
        <header class="ft-oil-panel-head">
            <div><h2>Linked inquiry</h2><p>Connect the source inquiry to this order without duplicating its files or data.</p></div>
            <div class="ft-oil-security-note"><i>⌑</i><span>Inquiry permissions remain unchanged</span></div>
        </header>

        <div class="ft-oil-panel-body">
            <div class="ft-oil-callout">
                <span class="ft-oil-callout-icon">i</span>
                <span><strong>Recommended relationship: one source inquiry per order</strong><span>The link gives traceability from quotation to delivery. Inquiry documents remain visible only to users who already have permission.</span></span>
            </div>

            @error('inquiryLink')
                <div class="ft-oil-error" role="alert">{{ $message }}</div>
            @enderror

            @if(!$linked)
                <div class="ft-oil-link-layout">
                    <section class="ft-oil-search-section" aria-busy="false">
                        <div class="ft-oil-section-head"><h3>Find the source inquiry</h3><p>Search all eligible inquiries after entering at least 2 characters.</p></div>
                        <div class="ft-oil-search-wrap">
                            <span class="ft-oil-search-icon">⌕</span>
                            <input
                                class="ft-oil-search"
                                type="search"
                                autocomplete="off"
                                placeholder="Inquiry number, client, subject, product or offer text"
                                aria-label="Search inquiries"
                                wire:model.live.debounce.400ms="inquirySearch"
                                @disabled(!$canManage)
                            >
                            @if($search !== '')
                                <button class="ft-oil-clear" type="button" wire:click="clearInquirySearch">Clear</button>
                            @endif
                        </div>
                        <div class="ft-oil-search-meta">
                            <span>
                                @if(!$canManage)
                                    You do not have permission to link inquiries to this order
                                @elseif($searchLength < 2)
                                    {{ $searchLength ? 'Enter at least 2 characters' : 'Start with an inquiry number or client name' }}
                                @else
                                    Results for “{{ trim($search) }}”
                                @endif
                            </span>
                            <span class="ft-oil-status-wrap">
                                <i class="ft-oil-spinner" wire:loading wire:target="inquirySearch" aria-hidden="true"></i>
                                @if($searchLength >= 2 && $canManage)<span>{{ $results->count() }} found</span>@endif
                            </span>
                        </div>

                        <div class="ft-oil-results" wire:loading.class="is-updating" wire:target="inquirySearch">
                            @if($searchLength < 2 || !$canManage)
                                <div class="ft-oil-empty"><span><strong>No search yet</strong>Try an inquiry number, client name, subject, or product.</span></div>
                            @elseif($results->isEmpty())
                                <div class="ft-oil-empty"><span><strong>No matching inquiry</strong>Check the number or search with a client, product, or offer term.</span></div>
                            @else
                                @foreach($results as $inquiry)
                                    @php
                                        $linkedOrder = $inquiry->sourceOrder ?: $inquiry->convertedJob;
                                        $eligible = !$linkedOrder && (string) $inquiry->result !== 'dead';
                                        $isSelected = $selectedInquiry && (int) $selectedInquiry->id === (int) $inquiry->id;
                                        $clientMatch = (int) $inquiry->client_id === (int) $job->client_id;
                                        $product = $inquiry->items->pluck('item_name')->filter()->take(2)->join(', ');
                                        $updated = \App\Support\UserLocalTime::format($inquiry->updated_at, 'M j, Y');
                                    @endphp
                                    <button
                                        type="button"
                                        class="ft-oil-result {{ $isSelected ? 'selected' : '' }} {{ !$eligible ? 'disabled' : '' }}"
                                        wire:click="selectInquiryForLink({{ $inquiry->id }})"
                                        wire:key="order-inquiry-result-{{ $inquiry->id }}"
                                        @disabled(!$eligible)
                                    >
                                        <span class="ft-oil-radio"></span>
                                        <span>
                                            <span class="ft-oil-result-title">{{ $inquiry->inquiry_number }}</span>
                                            <span class="ft-oil-result-subject">{{ $inquiry->subject }}</span>
                                            <span class="ft-oil-result-meta">{{ $product ?: ($inquiry->reference_number ?: 'Inquiry') }} · Updated {{ $updated }}</span>
                                        </span>
                                        <span class="ft-oil-result-client"><strong>{{ $inquiry->client?->name ?: 'No client' }}</strong><small>{{ $clientMatch ? 'Client match' : 'Different client' }}</small></span>
                                        @php
                                            $resultInquiryStatusColor = $inquiryService->inquiryStatusColor((string) $inquiry->status);
                                        @endphp
                                        <span class="ft-oil-result-owner"><strong class="ft-master-color-inline-label" style="{{ \App\Support\MasterColor::style($resultInquiryStatusColor) }}">{{ $inquiry->status }}</strong><small>{{ $eligible ? 'Owner: '.($inquiry->owner?->name ?: 'Unassigned') : ($linkedOrder ? 'Linked to '.$linkedOrder->displayOrderNumber() : 'Not eligible') }}</small></span>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </section>

                    <aside class="ft-oil-selection" aria-label="Selected inquiry">
                        <div class="ft-oil-section-head"><h3>Review before linking</h3><p>Verify the client and conversion status.</p></div>
                        <div class="ft-oil-selection-body">
                            @if(!$selectedInquiry)
                                <div class="ft-oil-selection-empty"><span><i>↗</i>Select one eligible inquiry from the results.</span></div>
                            @else
                                <div class="ft-oil-selected-card">
                                    <a class="ft-oil-selected-id" href="{{ route('inquiries.index', ['open'=>$selectedInquiry->id]) }}" wire:navigate>{{ $selectedInquiry->inquiry_number }}</a>
                                    <h4>{{ $selectedInquiry->subject }}</h4>
                                    <div class="ft-oil-checks">
                                        <div class="ft-oil-check"><span>Client</span><b class="{{ $selectedClientMatch ? '' : 'warn' }}">{{ $selectedClientMatch ? 'Match' : 'Different client' }}</b></div>
                                        <div class="ft-oil-check"><span>Inquiry availability</span><b>Eligible</b></div>
                                        <div class="ft-oil-check"><span>Files &amp; permissions</span><b>Unchanged</b></div>
                                    </div>
                                    <button class="ft-oil-primary" type="button" wire:click="openInquiryLinkConfirm">Link selected inquiry</button>
                                    <p class="ft-oil-helper">Linking does not copy quotation files, change inquiry status, or grant additional access.</p>
                                </div>
                            @endif
                        </div>
                    </aside>
                </div>
            @else
                <div class="ft-oil-linked show">
                    <div class="ft-oil-linked-card">
                        <div>
                            <div class="ft-oil-linked-main">
                                <span class="ft-oil-linked-icon">✓</span>
                                <span>
                                    <span class="ft-oil-linked-label">Source inquiry linked</span>
                                    @if($linkedInquiryCanOpen)
                                        <a class="ft-oil-linked-id" href="{{ route('inquiries.index', ['open'=>$linked->id]) }}" wire:navigate>{{ $linked->inquiry_number }}</a>
                                    @else
                                        <span class="ft-oil-linked-id">{{ $linked->inquiry_number }}</span>
                                    @endif
                                    <span class="ft-oil-linked-title">{{ $linked->subject }}</span>
                                    <span class="ft-oil-linked-meta">
                                        <span>{{ $linked->client?->name ?: 'No client' }}</span>
                                        @php
                                            $linkedInquiryStatusColor = $inquiryService->inquiryStatusColor((string) $linked->status);
                                        @endphp
                                        <span class="ft-master-color-inline-label" style="{{ \App\Support\MasterColor::style($linkedInquiryStatusColor) }}">{{ $linked->status }}</span>
                                        <span>Owner: {{ $linked->owner?->name ?: 'Unassigned' }}</span>
                                        <span>Linked to {{ $job->displayOrderNumber() }}</span>
                                    </span>
                                </span>
                            </div>
                            <div class="ft-oil-permission"><span>⌑</span><span><b>Access remains permission-based.</b> Users without Inquiry access can see the linked inquiry number but cannot open restricted Inquiry records or files.</span></div>
                        </div>
                        <div class="ft-oil-linked-actions">
                            @if($linkedInquiryCanOpen)
                                <a class="ft-oil-secondary" href="{{ route('inquiries.index', ['open'=>$linked->id]) }}" wire:navigate>Open inquiry ↗</a>
                            @else
                                <button class="ft-oil-secondary" type="button" disabled>Open inquiry ↗</button>
                            @endif
                            @if($canManage)
                                <button class="ft-oil-danger" type="button" wire:click="openInquiryUnlinkConfirm">Unlink</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if($showLinkConfirm && $selectedInquiry)
        <div class="ft-oil-modal show" role="dialog" aria-modal="true" aria-labelledby="ft-inquiry-link-confirm-title" wire:key="inquiry-link-confirm-{{ $selectedInquiry->id }}">
            <div class="ft-oil-modal-card">
                <header class="ft-oil-modal-head"><h3 id="ft-inquiry-link-confirm-title">Link inquiry to this order?</h3><button class="ft-oil-close" type="button" wire:click="closeInquiryLinkConfirm" aria-label="Close">×</button></header>
                <div class="ft-oil-modal-body">
                    <div class="ft-oil-pair">
                        <div class="ft-oil-pair-card"><small>Inquiry</small><strong>{{ $selectedInquiry->inquiry_number }}</strong></div>
                        <div class="ft-oil-pair-arrow">→</div>
                        <div class="ft-oil-pair-card"><small>Order</small><strong>{{ $job->displayOrderNumber() }}</strong></div>
                    </div>
                    <div class="ft-oil-modal-note">{{ $selectedClientMatch ? 'This creates a traceable relationship only. No files are copied and existing access permissions remain unchanged.' : 'The inquiry belongs to a different client. Confirm the relationship is intentional before linking; this event will be recorded.' }}</div>
                </div>
                <footer class="ft-oil-modal-actions"><button class="ft-oil-cancel" type="button" wire:click="closeInquiryLinkConfirm">Cancel</button><button class="ft-oil-confirm" type="button" wire:click="confirmInquiryLink" wire:loading.attr="disabled" wire:target="confirmInquiryLink">Confirm link</button></footer>
            </div>
        </div>
    @endif

    @if($showUnlinkConfirm && $linked)
        <div class="ft-oil-modal show" role="dialog" aria-modal="true" aria-labelledby="ft-inquiry-unlink-title" wire:key="inquiry-unlink-confirm-{{ $linked->id }}">
            <div class="ft-oil-modal-card">
                <header class="ft-oil-modal-head"><h3 id="ft-inquiry-unlink-title">Unlink this inquiry?</h3><button class="ft-oil-close" type="button" wire:click="closeInquiryUnlinkConfirm" aria-label="Close">×</button></header>
                <div class="ft-oil-modal-body"><div class="ft-oil-modal-note">The inquiry and order will remain unchanged, but their traceability link will be removed. This action is recorded in order activity.</div></div>
                <footer class="ft-oil-modal-actions"><button class="ft-oil-cancel" type="button" wire:click="closeInquiryUnlinkConfirm">Cancel</button><button class="ft-oil-danger" type="button" wire:click="confirmInquiryUnlink" wire:loading.attr="disabled" wire:target="confirmInquiryUnlink">Unlink inquiry</button></footer>
            </div>
        </div>
    @endif
</div>
