@php
    $tone = static function (string $status): string {
        return match (true) {
            str_contains($status, 'Converted'), str_contains($status, 'Completed') => 'green',
            str_contains($status, 'Dead'), str_contains($status, 'Closed') => 'red',
            str_contains($status, 'Ready'), str_contains($status, 'On Hold') => 'amber',
            str_contains($status, 'Waiting') => 'purple',
            default => 'blue',
        };
    };
    $initials = static function (?string $name): string {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        return strtoupper(substr(implode('', array_map(fn ($part) => substr($part, 0, 1), $parts)), 0, 2)) ?: '—';
    };
    $mentionText = static function (?string $text): string {
        $escaped = e((string) $text);
        return preg_replace('/(?<![\pL\pN._-])@([\pL\pN][\pL\pN._-]*)/u', '<span class="mention">@$1</span>', $escaped) ?? $escaped;
    };
@endphp

<div class="ft-inquiry-prototype">
    @if(session('success'))<div class="flash-inline">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="error-inline">{{ $errors->first() }}</div>@endif

    @if($mode === 'list')
        <section class="view">
            <div class="pagehead">
                <div><h1>Inquiries</h1><p>Manage client requests from first inquiry through tasks, conversion, or closure.</p></div>
                <div class="actions">
                    @if(auth()->user()->canModule('inquiries','create'))<button class="primary" type="button" wire:click="openCreate">＋ New Inquiry</button>@endif
                </div>
            </div>

            <div class="metrics">
                <div class="metric"><i>?</i><span><small>Active inquiries</small><strong>{{ $metrics['active'] }}</strong></span></div>
                <div class="metric"><i>✓</i><span><small>Converted to order</small><strong>{{ $metrics['converted'] }}</strong></span></div>
                <div class="metric"><i>×</i><span><small>Closed inquiries</small><strong>{{ $metrics['dead'] }}</strong></span></div>
                <div class="metric"><i>⌁</i><span><small>Tasks due today</small><strong>{{ $metrics['dueToday'] }}</strong></span></div>
            </div>

            <div class="shell inquiry-list-v2">
                <div class="toolbar">
                    <div class="search"><span>⌕</span><input wire:model.live.debounce.350ms="search" placeholder="Search inquiry, title, client, task or assignee"></div>
                    <div class="filters">
                        <button class="chip {{ $quick === 'all' ? 'active' : '' }}" type="button" wire:click="setQuick('all')">All</button>
                        <button class="chip {{ $quick === 'active' ? 'active' : '' }}" type="button" wire:click="setQuick('active')">Active</button>
                        <button class="chip {{ $quick === 'converted' ? 'active' : '' }}" type="button" wire:click="setQuick('converted')">Converted</button>
                        <button class="chip {{ $quick === 'dead' ? 'active' : '' }}" type="button" wire:click="setQuick('dead')">Closed</button>
                    </div>
                </div>
                <div class="inquiry-list-table" role="region" aria-label="Inquiry list" tabindex="0">
                    <div class="listhead">
                        <div>Inquiry</div>
                        <div>Title</div>
                        <div>Client / Item</div>
                        <div>Current Task</div>
                        <div>Progress</div>
                        <div>Assignee</div>
                        <div>Due Date</div>
                        <div>Started At</div>
                        <div>Status</div>
                        <div>View</div>
                        <div aria-label="Actions"></div>
                    </div>
                    <div class="inquiry-list-body">
                        @forelse($inquiryRows as $row)
                            <article class="row" wire:key="inquiry-list-{{ $row['id'] }}">
                                <div class="cell ft-inquiry-list-identity" data-label="Inquiry">
                                    <span class="ft-copyable-id-wrap ft-inquiry-list-code-wrap">
                                        <a class="id" href="{{ route('inquiries.index', ['open' => $row['id']]) }}" wire:navigate>{{ $row['number'] }}</a>
                                        <button type="button" class="ft-copy-id-btn" title="Copy Inquiry ID" aria-label="Copy {{ $row['number'] }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($row['number'])); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                                    </span>
                                    <span class="sub ft-inquiry-created-by" title="Created by {{ $row['createdBy'] }}">Created by {{ $row['createdBy'] }}</span>
                                    <span class="sub ft-inquiry-created-at">{{ $row['createdDate'] }} · {{ $row['createdTime'] }}</span>
                                </div>
                                <div class="cell ft-inquiry-list-title-cell" data-label="Title">
                                    <span class="title ft-inquiry-title-preview" title="{{ $row['title'] }}">{{ $row['titlePreview'] }}</span>
                                </div>
                                <div class="cell ft-inquiry-list-client-cell" data-label="Client / Item">
                                    <span class="ft-client-name-with-logo"><x-ui.client-logo :name="$row['client']" :src="$row['clientLogoUrl'] ?? null" :size="24" /><span class="title">{{ $row['client'] }}</span></span>
                                    @if($row['item'])<span class="sub">{{ $row['item'] }}</span>@endif
                                </div>
                                <div class="cell ft-inquiry-list-task-cell" data-label="Current Task"><span class="title">{{ $row['currentTask'] }}</span><span class="sub">{{ $row['taskCaption'] }}</span></div>
                                <div class="cell ft-inquiry-list-progress-cell" data-label="Progress">
                                    <div class="ft-inquiry-list-progress">
                                        <div class="ft-inquiry-list-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $row['progressPercent'] }}" aria-label="{{ $row['progress'] }} of {{ $row['total'] }} tasks completed"><span style="width:{{ $row['progressPercent'] }}%"></span></div>
                                        <b>{{ $row['progress'] }}/{{ $row['total'] }}</b>
                                    </div>
                                </div>
                                <div class="cell ft-inquiry-list-assignee-cell" data-label="Assignee"><div class="ownerline"><span class="avatar">{{ $initials($row['assignee']) }}</span><span class="title">{{ $row['assignee'] }}</span></div></div>
                                <div class="cell ft-inquiry-list-due-cell" data-label="Due Date"><span class="title">{{ $row['due'] }}</span></div>
                                <div class="cell ft-inquiry-list-started-cell" data-label="Started At"><span class="title">{{ $row['startedDate'] }}</span><span class="sub">{{ $row['startedTime'] }}</span></div>
                                <div class="cell ft-inquiry-list-status-cell" data-label="Status"><span class="pill {{ $tone($row['status']) }}">{{ $row['status'] }}</span></div>
                                <div class="cell ft-inquiry-list-view-cell" data-label="View"><a class="openbtn openbtn-link" href="{{ route('inquiries.index', ['open' => $row['id']]) }}" aria-label="View {{ $row['number'] }}" wire:navigate>View <span aria-hidden="true">→</span></a></div>
                                @if(auth()->user()->canModule('inquiries', 'delete'))
                                    <div class="cell ft-inquiry-list-actions-cell" data-label="Actions" x-data="{ open: false }">
                                        <button
                                            class="ft-inquiry-row-action-trigger"
                                            type="button"
                                            :aria-expanded="open ? 'true' : 'false'"
                                            aria-haspopup="menu"
                                            aria-controls="inquiry-actions-{{ $row['id'] }}"
                                            aria-label="Actions for {{ $row['number'] }}"
                                            x-on:click.stop="
                                                const menu = $refs.menu;
                                                if (menu.matches(':popover-open')) { menu.hidePopover(); return; }
                                                const rect = $el.getBoundingClientRect();
                                                const menuWidth = 166;
                                                const menuHeight = 46;
                                                const edge = 10;
                                                const gap = 6;
                                                const left = Math.min(window.innerWidth - menuWidth - edge, Math.max(edge, rect.right - menuWidth));
                                                const openAbove = (window.innerHeight - rect.bottom) < (menuHeight + gap + edge) && rect.top > (menuHeight + gap + edge);
                                                const top = openAbove ? rect.top - menuHeight - gap : rect.bottom + gap;
                                                menu.style.left = `${left}px`;
                                                menu.style.top = `${Math.max(edge, top)}px`;
                                                menu.showPopover();
                                            "
                                        >⋮</button>
                                        <div
                                            id="inquiry-actions-{{ $row['id'] }}"
                                            class="ft-inquiry-row-action-menu"
                                            x-ref="menu"
                                            popover="auto"
                                            role="menu"
                                            x-on:toggle="open = $event.newState === 'open'"
                                        >
                                            <button type="button" role="menuitem" wire:click="deleteInquiry({{ $row['id'] }})" wire:confirm="Delete {{ $row['number'] }}? This removes the inquiry from active lists. Any converted order remains available.">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                                                <span>Delete inquiry</span>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="cell ft-inquiry-list-actions-cell" aria-hidden="true"></div>
                                @endif
                            </article>
                        @empty
                            <div class="ft-inquiry-list-empty">No matching inquiries.</div>
                        @endforelse
                    </div>
                </div>
                <div class="footer">
                    <span>Showing {{ $inquiryPaginator->firstItem() ?? 0 }}–{{ $inquiryPaginator->lastItem() ?? 0 }} of {{ $inquiryPaginator->total() }} inquiries</span>
                    <span>
                        @if($inquiryPaginator->lastPage() > 1)
                            <button class="chip" type="button" wire:click="previousPage('inquiryPage')" @disabled($inquiryPaginator->onFirstPage())>←</button>
                            Page {{ $inquiryPaginator->currentPage() }} of {{ $inquiryPaginator->lastPage() }}
                            <button class="chip" type="button" wire:click="nextPage('inquiryPage')" @disabled(!$inquiryPaginator->hasMorePages())>→</button>
                        @else
                            Page 1 of 1
                        @endif
                    </span>
                </div>
            </div>
        </section>

    @elseif($mode === 'create')
        @php
            $selectedWorkflow = collect($workflowFilterOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $createWorkflowId);
            $selectedWorkflowName = (string) ($selectedWorkflow['label'] ?? $selectedWorkflowLabel ?: 'Select workflow');
            $workflowOptionCount = count($workflowFilterOptions);
        @endphp
        <section class="view ft-inquiry-create-v3" x-on:keydown.meta.enter.window="$wire.createInquiry()" x-on:keydown.ctrl.enter.window="$wire.createInquiry()">
            <div class="formwrap ft-inquiry-create-shell">
                <div class="crumb">Inquiries / New Inquiry</div>
                <div class="formtop ft-inquiry-create-heading">
                    <div>
                        <h1>Create Inquiry</h1>
                        <p>Capture a new client request from email or phone. The inquiry workflow starts automatically.</p>
                    </div>
                </div>

                <div class="formcard ft-inquiry-create-card">
                    <section class="section ft-inquiry-create-section ft-inquiry-create-details">
                        <div class="sectiontitle ft-inquiry-step-title"><span>1</span><h2>Inquiry details</h2></div>

                        <div class="ft-inquiry-create-grid ft-inquiry-create-grid-top">
                            <div class="ft-inquiry-create-field">
                                <label>How was this inquiry received? *</label>
                                <div class="ft-inquiry-source-switch" role="group" aria-label="How was this inquiry received?">
                                    @foreach(['Email' => '✉', 'Phone' => '☎', 'Other' => '•••'] as $source => $icon)
                                        <button type="button" class="{{ $requestSource === $source ? 'is-active' : '' }}" wire:click="$set('requestSource', '{{ $source }}')">
                                            <span aria-hidden="true">{{ $icon }}</span>{{ $source }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('requestSource')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="ft-inquiry-create-field">
                                <label for="inquiry-received-date">Received *</label>
                                <div class="ft-inquiry-received-control">
                                    <input id="inquiry-received-date" type="date" wire:model="createReceivedDate" aria-describedby="inquiry-received-help">
                                </div>
                                <small id="inquiry-received-help" class="ft-inquiry-field-help">Defaults to today. Change it when the inquiry was received on another date.</small>
                                @error('createReceivedDate')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid ft-inquiry-create-grid-client">
                            <div class="ft-inquiry-create-field">
                                <label>Client *</label>
                                <div class="ft-inquiry-client-control-row">
                                    <x-ui.remote-filter
                                        class="ft-create-remote-select inquiry-create-remote ft-inquiry-client-selector"
                                        label="Client"
                                        property="clientId"
                                        type="clients"
                                        context="create-inquiry"
                                        action="setCreateSelector"
                                        :value="$clientId"
                                        placeholder="Search or select client..."
                                        :selected-label="$selectedClientLabel ?: null"
                                        :initial-options="$clientFilterOptions"
                                        :clearable="false"
                                        wire:key="inquiry-create-client-selector-{{ $clientId ?: 'none' }}-{{ substr(md5($selectedClientLabel ?: 'none'), 0, 8) }}"
                                    />
                                    @if(auth()->user()->canModule('clients','create'))
                                        <button class="secondary ft-inquiry-new-client-btn" type="button" wire:click="openCreateClientModal">＋ New client</button>
                                    @endif
                                </div>
                                @error('clientId')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="ft-inquiry-create-field">
                                <label>Client contact</label>
                                <div class="ft-inquiry-client-control-row">
                                    <div class="ft-inquiry-contact-select-wrap">
                                        <select wire:model="clientContact" @disabled(!$clientId || !$clientContact)>
                                            @if($clientId && $clientContact)
                                                <option value="{{ $clientContact }}">{{ $clientContact }}</option>
                                            @else
                                                <option value="">{{ $clientId ? 'No contact recorded' : 'Select a client first' }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <button class="secondary ft-inquiry-new-contact-btn" type="button" wire:click="openCreateContactModal" @disabled(!$clientId)>＋ New contact</button>
                                </div>
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid">
                            <label class="ft-inquiry-create-field">
                                <span>Reference number</span>
                                <input wire:model="referenceNumber" placeholder="Email or client reference (optional)">
                            </label>

                            <div class="ft-inquiry-create-field">
                                <label>Assigned to *</label>
                                <x-ui.remote-filter
                                    class="ft-create-remote-select inquiry-create-remote ft-inquiry-owner-selector"
                                    label="Assigned to"
                                    property="createOwnerId"
                                    type="users"
                                    context="create-inquiry"
                                    action="setCreateSelector"
                                    :value="$createOwnerId"
                                    placeholder="Search or select assignee..."
                                    :selected-label="$selectedOwnerLabel ?: null"
                                    :initial-options="$ownerFilterOptions"
                                    :clearable="false"
                                    wire:key="inquiry-create-owner-selector-{{ $createOwnerId ?: 'none' }}-{{ substr(md5($selectedOwnerLabel ?: 'none'), 0, 8) }}"
                                />
                                @error('createOwnerId')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <label class="ft-inquiry-create-field ft-inquiry-create-field-full">
                            <span>Inquiry title *</span>
                            <input wire:model="subject" placeholder="e.g. 5,000 embroidered polo shirts for September">
                            @error('subject')<small class="field-error">{{ $message }}</small>@enderror
                        </label>

                        <div class="ft-inquiry-create-field ft-inquiry-create-field-full ft-inquiry-request-details">
                            <label>Request details</label>
                            <textarea data-rich-text wire:model="requirementNotes" placeholder="Paste or summarize the client's request, including quantities, specifications, target date and any special instructions..."></textarea>
                            <small class="ft-inquiry-field-tip"><b>Tip:</b> Include quantity, product, deadline and delivery location.</small>
                        </div>
                    </section>

                    <section class="section ft-inquiry-create-section ft-inquiry-attachments-section">
                        <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                            <span>2</span><h2>Attachments</h2><p>Add emails, specifications, artwork or reference images.</p>
                        </div>
                        <div
                            class="inquiry-dropzone ft-inquiry-prototype-dropzone"
                            x-data="{ dragging: false }"
                            x-bind:class="{ 'is-dragging': dragging }"
                            x-on:dragenter.prevent="dragging = true"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="if (!$el.contains($event.relatedTarget)) dragging = false"
                            x-on:drop.prevent="dragging = false; const files = $event.dataTransfer.files; if (files.length) { $refs.createAttachmentInput.files = files; $refs.createAttachmentInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                            x-on:click="$refs.createAttachmentInput.click()"
                            role="button"
                            tabindex="0"
                            x-on:keydown.enter.prevent="$refs.createAttachmentInput.click()"
                            x-on:keydown.space.prevent="$refs.createAttachmentInput.click()"
                        >
                            <input x-ref="createAttachmentInput" class="file-input" type="file" wire:model="createAttachments" multiple>
                            <div class="inquiry-dropzone-icon" aria-hidden="true">⇧</div>
                            <div class="inquiry-dropzone-copy">
                                <strong>Drop client files here</strong>
                                <span class="ft-inquiry-drop-or">or <b>browse files</b></span>
                                <small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB per file</small>
                            </div>
                            <button class="secondary inquiry-dropzone-button" type="button" x-on:click.stop="$refs.createAttachmentInput.click()">Choose files</button>
                        </div>
                        <div class="inquiry-upload-state" wire:loading wire:target="createAttachments">Uploading files…</div>
                        @if(count($createAttachments))
                            <div class="inquiry-selected-files ft-inquiry-selected-files">
                                <div class="inquiry-selected-files-title">Selected files <span>{{ count($createAttachments) }}</span></div>
                                <div class="ft-inquiry-selected-file-grid">
                                    @foreach($createAttachments as $upload)
                                        @php
                                            $attachmentName = (string) $upload->getClientOriginalName();
                                            $attachmentExtension = strtolower((string) pathinfo($attachmentName, PATHINFO_EXTENSION));
                                            $attachmentMime = method_exists($upload, 'getMimeType') ? (string) $upload->getMimeType() : '';
                                            $attachmentIsImage = str_starts_with($attachmentMime, 'image/')
                                                || in_array($attachmentExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                                            $attachmentPreviewUrl = $attachmentIsImage && method_exists($upload, 'temporaryUrl')
                                                ? $upload->temporaryUrl()
                                                : null;
                                            $attachmentSize = method_exists($upload, 'getSize') ? (int) $upload->getSize() : 0;
                                            $attachmentSizeLabel = $attachmentSize >= 1048576
                                                ? number_format($attachmentSize / 1048576, 1).' MB'
                                                : ($attachmentSize > 0 ? max(1, (int) round($attachmentSize / 1024)).' KB' : 'Selected file');
                                        @endphp
                                        <article class="ft-inquiry-selected-file-card" wire:key="create-attachment-{{ $loop->index }}-{{ md5($attachmentName) }}">
                                            @if($attachmentPreviewUrl)
                                                <a
                                                    class="ft-inquiry-selected-file-preview"
                                                    href="{{ $attachmentPreviewUrl }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Open image preview"
                                                    aria-label="Open preview of {{ $attachmentName }}"
                                                >
                                                    <img src="{{ $attachmentPreviewUrl }}" alt="Preview of {{ $attachmentName }}">
                                                    <span>Preview</span>
                                                </a>
                                            @else
                                                <div class="ft-inquiry-selected-file-type" aria-hidden="true">
                                                    <span>▤</span>
                                                    <b>{{ $attachmentExtension !== '' ? strtoupper($attachmentExtension) : 'FILE' }}</b>
                                                </div>
                                            @endif
                                            <div class="ft-inquiry-selected-file-meta">
                                                <strong title="{{ $attachmentName }}">{{ $attachmentName }}</strong>
                                                <span>{{ $attachmentExtension !== '' ? strtoupper($attachmentExtension) : 'FILE' }} · {{ $attachmentSizeLabel }}</span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="section ft-inquiry-create-section ft-inquiry-next-section" x-data="{ workflowOpen: false }">
                        <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                            <span>3</span><h2>What happens next</h2>
                            @if($workflowOptionCount > 0)<em>{{ $workflowOptionCount }} {{ \Illuminate\Support\Str::plural('workflow', $workflowOptionCount) }} available</em>@endif
                        </div>

                        <div class="ft-inquiry-workflow-card" :class="{ 'is-open': workflowOpen }">
                            <div class="ft-inquiry-workflow-selected" role="button" tabindex="0" x-on:click="workflowOpen = !workflowOpen" x-on:keydown.enter.prevent="workflowOpen = !workflowOpen" x-on:keydown.space.prevent="workflowOpen = !workflowOpen" :aria-expanded="workflowOpen.toString()">
                                <span class="ft-inquiry-workflow-icon">✓</span>
                                <span class="ft-inquiry-workflow-copy">
                                    <small>Default workflow</small>
                                    <strong>{{ $selectedWorkflowName }}</strong>
                                    <span>{{ $createWorkflowPhaseCount }} {{ \Illuminate\Support\Str::plural('phase', $createWorkflowPhaseCount) }} · {{ $createWorkflowTaskCount }} {{ \Illuminate\Support\Str::plural('task', $createWorkflowTaskCount) }} will be created</span>
                                </span>
                                @if(auth()->user()->canAccess('workflow.manage'))
                                    <a href="{{ route('workflow.setup') }}" wire:navigate x-on:click.stop>Preview workflow ↗</a>
                                @endif
                                <span class="ft-inquiry-workflow-chevron" aria-hidden="true">⌄</span>
                            </div>

                            <div class="ft-inquiry-workflow-options" x-cloak x-show="workflowOpen">
                                @foreach($workflowFilterOptions as $workflowOption)
                                    <button type="button" class="ft-inquiry-workflow-option {{ (int) ($workflowOption['id'] ?? 0) === (int) $createWorkflowId ? 'is-selected' : '' }}"
                                        wire:click="setCreateSelector('createWorkflowId', '{{ $workflowOption['id'] }}')"
                                        x-on:click="workflowOpen = false">
                                        <span class="ft-inquiry-workflow-radio"></span>
                                        <span><strong>{{ $workflowOption['label'] }}</strong><small>{{ $workflowOption['meta'] ?: 'Inquiry workflow' }}</small></span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @if($createWorkflowId && $createWorkflowTaskCount === 0)
                            <small class="field-error">This Workflow has no active Task Pack tasks.</small>
                        @else
                            @error('createWorkflowId')<small class="field-error">{{ $message }}</small>@enderror
                        @endif
                        <p class="ft-inquiry-workflow-footnote">Tasks are created when you select Create inquiry.</p>
                    </section>

                    <div class="formactions ft-inquiry-create-actions">
                        <span>Required fields are marked with *</span>
                        <div>
                            <button class="secondary" type="button" wire:click="cancelCreate">Cancel</button>
                            <button class="secondary" type="button" wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft">Save draft</button>
                            <button class="primary" type="button" wire:click="createInquiry" wire:loading.attr="disabled" wire:target="createInquiry">Create inquiry <kbd>⌘ Enter</kbd></button>
                        </div>
                    </div>
                </div>
            </div>

            @if($showCreateClientModal)
                <div class="ft-inquiry-modal-backdrop" wire:key="inquiry-quick-client-modal" wire:click.self="closeCreateClientModal">
                    <section class="ft-inquiry-quick-client-modal" role="dialog" aria-modal="true" aria-labelledby="quick-client-title">
                        <header>
                            <div><h2 id="quick-client-title">Add new client</h2><p>Create the client with minimum information. You can complete the profile later.</p></div>
                            <button type="button" wire:click="closeCreateClientModal" aria-label="Close">×</button>
                        </header>
                        <div class="ft-inquiry-quick-client-body">
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Client name *</span><input wire:model="newClientName" placeholder="Company or client name">@error('newClientName')<small class="field-error">{{ $message }}</small>@enderror<small>This is the only required field.</small></label>

                            <div class="ft-inquiry-modal-divider"></div>
                            <div class="ft-inquiry-modal-subhead"><strong>Primary contact (optional)</strong><span>Add contact details if they were provided with the inquiry.</span></div>
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Contact name</span><input wire:model="newClientContactName" placeholder="Full name"></label>
                            <div class="ft-inquiry-modal-grid">
                                <label class="ft-inquiry-modal-field"><span>Email</span><input type="email" wire:model="newClientEmail" placeholder="name@company.com">@error('newClientEmail')<small class="field-error">{{ $message }}</small>@enderror</label>
                                <label class="ft-inquiry-modal-field"><span>Phone</span><input wire:model="newClientPhone" placeholder="Phone number"></label>
                            </div>
                            <label class="ft-inquiry-contact-checkbox"><input type="checkbox" wire:model="useNewClientContactForInquiry"><span>Use this person as the inquiry contact</span></label>
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Country / region</span><input list="ft-country-regions" wire:model="newClientCountry" placeholder="Select country or region"><datalist id="ft-country-regions"><option value="Bangladesh"><option value="China"><option value="Hong Kong"><option value="India"><option value="United Kingdom"><option value="United States"><option value="Vietnam"><option value="Cambodia"><option value="Pakistan"><option value="Sri Lanka"><option value="United Arab Emirates"></datalist></label>
                            <div class="ft-inquiry-client-info">ⓘ <span>The new client will be selected automatically in this inquiry.</span></div>
                        </div>
                        <footer>
                            <span>Required fields are marked with *</span>
                            <div><button type="button" class="secondary" wire:click="closeCreateClientModal">Cancel</button><button type="button" class="primary" wire:click="createClientAndSelect" wire:loading.attr="disabled" wire:target="createClientAndSelect">Add &amp; select client</button></div>
                        </footer>
                    </section>
                </div>
            @endif

            @if($showCreateContactModal)
                <div class="ft-inquiry-modal-backdrop" wire:key="inquiry-quick-contact-modal" wire:click.self="closeCreateContactModal">
                    <section class="ft-inquiry-quick-client-modal ft-inquiry-quick-contact-modal" role="dialog" aria-modal="true" aria-labelledby="quick-contact-title">
                        <header><div><h2 id="quick-contact-title">Add client contact</h2><p>Add the primary contact for {{ $selectedClientLabel ?: 'this client' }} and use it in this inquiry.</p></div><button type="button" wire:click="closeCreateContactModal" aria-label="Close">×</button></header>
                        <div class="ft-inquiry-quick-client-body">
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Contact name *</span><input wire:model="newContactName" placeholder="Full name">@error('newContactName')<small class="field-error">{{ $message }}</small>@enderror</label>
                            <div class="ft-inquiry-modal-grid">
                                <label class="ft-inquiry-modal-field"><span>Email</span><input type="email" wire:model="newContactEmail" placeholder="name@company.com">@error('newContactEmail')<small class="field-error">{{ $message }}</small>@enderror</label>
                                <label class="ft-inquiry-modal-field"><span>Phone</span><input wire:model="newContactPhone" placeholder="Phone number"></label>
                            </div>
                        </div>
                        <footer><span></span><div><button type="button" class="secondary" wire:click="closeCreateContactModal">Cancel</button><button type="button" class="primary" wire:click="saveCreateContact" wire:loading.attr="disabled" wire:target="saveCreateContact">Add contact</button></div></footer>
                    </section>
                </div>
            @endif
        </section>

    @else
        @php
            $inquiry = $selectedInquiry;
            $totalTasks = (int) $inquiry->tasks_count;
            $completedTasks = (int) $inquiry->completed_tasks_count;
            $readyForDecision = !$inquiry->result && $totalTasks > 0 && $completedTasks === $totalTasks;
            $currentTask = $inquiry->currentTask;
            $firstStartedTask = $inquiry->tasks->whereNotNull('started_at')->sortBy('started_at')->first();
            $lastCompletedTask = $inquiry->tasks->whereNotNull('completed_at')->sortByDesc('completed_at')->first();
            $inquiryStartAt = $inquiry->started_at ?: $firstStartedTask?->started_at;
            $inquiryStartLocal = \App\Support\UserLocalTime::localize($inquiryStartAt);
            $inquiryCompletedAt = $inquiry->completed_at ?: ($readyForDecision ? $lastCompletedTask?->completed_at : null);
            $detailStatus = match (true) {
                $inquiry->result === 'converted' => 'Converted',
                $inquiry->result === 'dead' => 'Closed',
                (string) $inquiry->status === 'Draft' => 'Draft',
                $readyForDecision => \App\Services\InquiryService::AUTO_COMPLETED_STATUS,
                $completedTasks > 0 || $inquiryStartAt !== null => \App\Services\InquiryService::AUTO_IN_PROGRESS_STATUS,
                default => \App\Services\InquiryService::AUTO_READY_STATUS,
            };
        @endphp
        <section class="view inquiry-detail-view" x-data="{
            inquiryStatus:@js($detailStatus),
            inquiryStartValue:@js($inquiryStartLocal?->format('Y-m-d\TH:i') ?? ''),
            inquiryStartDisplay:@js($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—'),
            statusTone(status){
                if (String(status).includes('Converted') || String(status).includes('Completed')) return 'green';
                if (String(status).includes('Dead') || String(status).includes('Closed')) return 'red';
                if (String(status).includes('Ready') || String(status).includes('On Hold')) return 'amber';
                if (String(status).includes('Waiting')) return 'purple';
                return 'blue';
            },
            async saveTaskStatus(event, taskId){
                const previous=this.inquiryStatus;
                try{
                    const result=await $wire.updateTaskStatusInline(taskId,event.currentTarget.value);
                    if(result?.inquiryStatus)this.inquiryStatus=result.inquiryStatus;
                    if(result && Object.prototype.hasOwnProperty.call(result,'inquiryStartValue')){
                        this.inquiryStartValue=result.inquiryStartValue || '';
                        this.inquiryStartDisplay=result.inquiryStartDisplay || '—';
                        window.dispatchEvent(new CustomEvent('flowtrack-inquiry-started',{detail:{value:this.inquiryStartValue,display:this.inquiryStartDisplay}}));
                    }
                }catch(error){
                    this.inquiryStatus=previous;
                    window.location.reload();
                }
            }
        }">
            <div class="ft-detail-toolbar task-toolbar ft-exact-task-header ft-inquiry-exact-header">
                <div class="ft-task-heading-copy">
                    <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                        <a href="{{ route('inquiries.index') }}" wire:navigate>Inquiries</a>
                        <span>/</span>
                        <span class="ft-copyable-id-wrap ft-inquiry-detail-code-wrap">
                            <span>{{ $inquiry->inquiry_number }}</span>
                            <button type="button" class="ft-copy-id-btn" title="Copy Inquiry ID" aria-label="Copy {{ $inquiry->inquiry_number }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($inquiry->inquiry_number)); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                        </span>
                    </div>
                    <div class="ft-task-title-line">
                        <h1
                            class="ft-editable-task-title ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-title'), label: 'Inquiry title', value: @js($inquiry->subject), display: @js($inquiry->subject) })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                        >
                            <span x-show="!editing" x-text="display">{{ $inquiry->subject }}</span>
                            @if($canEditInquiry && !$inquiry->result)
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit Inquiry title" title="Edit Inquiry title" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryTitle.focus())">✎</button>
                                <input x-ref="inquiryTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                                    x-on:keydown.escape.prevent="cancelEdit()"
                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                    x-on:blur="if (editing) commit(draftValue.trim(), draftValue.trim(), () => $wire.updateInquiryField('subject', draftValue.trim()))">
                                <x-ui.inline-save-state />
                            @endif
                        </h1>
                    </div>
                    <div class="ft-inquiry-header-meta" aria-label="Inquiry information">
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span class="ft-client-inline-identity"><x-ui.client-logo :client="$inquiry->client" :name="$inquiry->client?->name ?: 'Client'" :size="20" /><span>Client <strong>{{ $inquiry->client?->name ?: '—' }}</strong></span></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item ft-inquiry-header-reference">
                            <span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.5h7l4 4V20.5H7z"></path><path d="M14 3.5v4h4"></path></svg></span>
                            <span>Reference <strong>{{ $inquiry->reference_number ?: '—' }}</strong></span>
                            @if($inquiry->reference_number)
                                <button type="button" class="ft-copy-id-btn ft-inquiry-header-copy" title="Copy Reference Number" aria-label="Copy reference number {{ $inquiry->reference_number }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($inquiry->reference_number)); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                            @endif
                        </span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span>Created by <strong>{{ $inquiry->creator?->name ?: 'System' }}</strong></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 10h16"></path></svg></span><span>Created <strong>{{ $inquiry->created_at ? \App\Support\UserLocalTime::format($inquiry->created_at, 'M j, Y') : '—' }}@if($inquiry->created_at) at {{ \App\Support\UserLocalTime::format($inquiry->created_at, 'g:i A') }}@endif</strong></span></span>
                    </div>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" type="button">Overview</button>
            </div>

            @if($detailTab === 'overview')
                <div class="tabpane ft-task-detail-page ft-exact-task-detail ft-inquiry-task-overview-exact">
                    <section class="ft-task-property-grid ft-friendly-task-properties ft-inquiry-overview-properties">
                        <div class="ft-task-property ft-inquiry-auto-status-property">
                            <small>Status</small>
                            <div class="ft-task-property-display">
                                <span class="status-dot" x-bind:class="statusTone(inquiryStatus)"></span>
                                <b class="ft-property-value" x-text="inquiryStatus">{{ $detailStatus }}</b>
                            </div>
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-priority'), label: 'Inquiry priority', value: @js($inquiry->priority), display: @js($inquiry->priority) })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Priority</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="status-dot amber"></span><b class="ft-property-value" x-text="display">{{ $inquiry->priority }}</b>@if($canEditInquiry && !$inquiry->result)<button type="button" :disabled="status === 'saving'" title="Edit priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryPriority?.showPicker ? $refs.inquiryPriority.showPicker() : $refs.inquiryPriority?.focus())">✎</button>@endif</div>
                            @if($canEditInquiry && !$inquiry->result)
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="inquiryPriority" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateInquiryField('priority', draftValue))">@unless($inquiryPriorities->contains(fn($priority) => (string) $priority->name === (string) $inquiry->priority))<option value="{{ $inquiry->priority }}">{{ $inquiry->priority }}</option>@endunless @foreach($inquiryPriorities as $priority)<option value="{{ $priority->name }}">{{ $priority->name }}</option>@endforeach</select></div>
                                <x-ui.inline-save-state compact />
                            @endif
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-due-date'), label: 'Inquiry next due date', value: @js($currentTask?->due_date?->format('Y-m-d') ?? ''), display: @js($currentTask?->due_date?->format('M j, Y') ?? '—') })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Due date</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value" x-text="display">{{ $currentTask?->due_date?->format('M j, Y') ?? '—' }}</b>@if($currentTask && $canEditActiveTask)<button type="button" :disabled="status === 'saving'" title="Edit due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryOverviewDue?.showPicker ? $refs.inquiryOverviewDue.showPicker() : $refs.inquiryOverviewDue?.focus())">✎</button>@endif</div>
                            @if($currentTask && $canEditActiveTask)
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><input x-ref="inquiryOverviewDue" x-model="draftValue" class="ft-task-property-inline-input" type="date" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueInline({{ $currentTask->id }}, draftValue))"></div>
                                <x-ui.inline-save-state compact />
                            @endif
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-start-at'), label: 'Inquiry start date', value: @js($inquiryStartLocal?->format('Y-m-d\TH:i') ?? ''), display: @js($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—') })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                            x-on:flowtrack-inquiry-started.window="const v=String($event.detail?.value ?? ''); const d=String($event.detail?.display ?? '—'); serverValue=v; value=v; savedValue=v; draftValue=v; display=d; savedDisplay=d;"
                        >
                            <small>Start date</small>
                            <div x-show="!editing" class="ft-task-property-display">
                                <span class="ft-calendar-glyph">▣</span>
                                <b class="ft-property-value" x-text="display">{{ $inquiryStartLocal?->format('M j, Y · g:i A') ?? '—' }}</b>
                                @if($canEditInquiry && !$inquiry->result)
                                    <button type="button" :disabled="status === 'saving'" title="Edit start date and time" aria-label="Edit Inquiry start date and time" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryStartAt?.showPicker ? $refs.inquiryStartAt.showPicker() : $refs.inquiryStartAt?.focus())">✎</button>
                                @endif
                            </div>
                            @if($canEditInquiry && !$inquiry->result)
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor">
                                    <input x-ref="inquiryStartAt" x-model="draftValue" class="ft-task-property-inline-input" type="datetime-local" step="60"
                                        x-on:keydown.escape.prevent="cancelEdit()"
                                        x-on:change="commit($event.target.value, formatDateTime($event.target.value), () => $wire.updateInquiryStartInline(draftValue))">
                                </div>
                                <x-ui.inline-save-state compact />
                            @endif
                        </div>

                        <div class="ft-task-property ft-task-completed-property">
                            <small>Completed On</small>
                            <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value ft-completed-date-time"><span>{{ $inquiryCompletedAt ? \App\Support\UserLocalTime::format($inquiryCompletedAt, 'M j, Y') : '—' }}</span>@if($inquiryCompletedAt)<span class="ft-completed-time">{{ \App\Support\UserLocalTime::format($inquiryCompletedAt, 'g:i A') }}</span>@endif</b></div>
                        </div>
                    </section>

                    <section
                        class="ft-detail-card ft-inquiry-description-card ft-inline-edit-shell"
                        x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-description'), label: 'Inquiry description', value: @js($inquiry->requirement_notes ?? ''), display: @js($inquiry->requirement_notes ?: 'No description has been provided for this Inquiry.') })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <div class="ft-inquiry-description-head">
                            <h2>Description</h2>
                            @if($canEditInquiry && !$inquiry->result)
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit description" aria-label="Edit Inquiry description" x-on:click.stop="beginRichTextEdit($refs.inquiryDescription)">✎</button>
                            @endif
                        </div>
                        <div x-show="!editing" class="ft-rich-text-content ft-inquiry-description-content">
                            <div x-show="!hasRichTextOverride">@if($inquiry->requirement_notes)<x-ui.mention-text :text="$inquiry->requirement_notes" />@else No description has been provided for this Inquiry. @endif</div>
                            <div x-cloak x-show="hasRichTextOverride" x-html="richTextOverrideHtml"></div>
                        </div>
                        @if($canEditInquiry && !$inquiry->result)
                            <div x-cloak x-show="editing" class="ft-inquiry-description-editor ft-inline-description-editor">
                                <textarea x-ref="inquiryDescription" data-rich-text placeholder="Add the client requirement or Inquiry description, or paste screenshots here...">{{ $inquiry->requirement_notes ?? '' }}</textarea>
                                <div class="ft-inquiry-description-editor-actions">
                                    <button type="button" class="secondary" x-on:click="cancelRichTextEdit($refs.inquiryDescription)">Cancel</button>
                                    <button type="button" class="primary" data-rich-text-submit :disabled="status === 'saving'" x-on:click="saveRichText($refs.inquiryDescription, 'No description has been provided for this Inquiry.', (clean) => $wire.updateInquiryField('requirement_notes', clean))">Save</button>
                                    <x-ui.inline-save-state compact />
                                </div>
                            </div>
                        @endif
                    </section>

                    <div id="tab-workflow" class="ft-inquiry-overview-taskflow ft-inquiry-workflow-pane">
                        @include('livewire.inquiries._taskflow')
                    </div>

                    @include('livewire.inquiries._attachments')
                    @include('livewire.inquiries._activity')
                </div>
            @endif

            @if($showTaskDocumentModal && $taskDocumentModalTask)
                <div class="ft-inquiry-task-document-modal-backdrop" wire:key="inquiry-task-document-modal" wire:click.self="closeTaskDocumentModal">
                    <section class="ft-inquiry-task-document-modal" role="dialog" aria-modal="true" aria-labelledby="task-document-modal-title">
                        <header class="ft-inquiry-task-document-modal-head">
                            <div>
                                <h2 id="task-document-modal-title">Add new document to task</h2>
                                <p>Upload a new file or choose a document that already exists.</p>
                            </div>
                            <button type="button" class="ft-inquiry-task-document-modal-close" wire:click="closeTaskDocumentModal" aria-label="Close">×</button>
                        </header>

                        <div class="ft-inquiry-task-document-modal-body">
                            <div class="ft-inquiry-task-document-target">
                                <span class="ft-inquiry-task-document-target-icon">▣</span>
                                <div>
                                    <small>ATTACHING TO</small>
                                    <strong>{{ $taskDocumentModalTask->title }}</strong>
                                    <span>INQ-TASK-{{ str_pad((string) $taskDocumentModalTask->id, 5, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp; {{ $inquiry->sourceWorkflow?->name ?? 'Inquiry Taskflow' }}</span>
                                </div>
                                <span class="ft-inquiry-task-document-target-lock">▣&nbsp; Task selected</span>
                            </div>

                            <div class="ft-inquiry-task-document-source-label">Document source</div>
                            <div class="ft-inquiry-task-document-source-tabs">
                                <button type="button" class="{{ $taskDocumentSource === 'upload' ? 'active' : '' }}" wire:click="setTaskDocumentSource('upload')">
                                    <span>↥</span> Upload new
                                </button>
                                <button type="button" class="{{ $taskDocumentSource === 'existing' ? 'active' : '' }}" wire:click="setTaskDocumentSource('existing')" @disabled(!$canLinkDocuments)>
                                    <span>▤</span> Choose existing
                                </button>
                            </div>

                            @if($taskDocumentSource === 'upload')
                                <label class="ft-inquiry-task-document-dropzone">
                                    <input type="file" wire:model="taskDocumentUpload" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai">
                                    <span class="ft-inquiry-task-document-upload-icon">⇧</span>
                                    @if($taskDocumentUpload)
                                        <strong>{{ $taskDocumentUpload->getClientOriginalName() }}</strong>
                                        <b>File selected — choose another file</b>
                                        <small>{{ number_format(max(1, (int) ceil($taskDocumentUpload->getSize() / 1024))) }} KB · ready to add</small>
                                    @else
                                        <strong>Drop a file here</strong>
                                        <b>or browse files</b>
                                        <small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small>
                                    @endif
                                </label>
                                @error('taskDocumentUpload')<p class="ft-inquiry-task-document-error">{{ $message }}</p>@enderror
                            @else
                                <div class="ft-inquiry-task-document-existing">
                                    @if($availableTaskDocuments->isEmpty())
                                        <div class="ft-inquiry-task-document-existing-empty">No existing client documents are available.</div>
                                    @else
                                        <label>
                                            <span>Choose an existing document</span>
                                            <select wire:model="taskExistingDocumentId">
                                                <option value="">Select a document...</option>
                                                @foreach($availableTaskDocuments as $sourceDocument)
                                                    <option value="{{ $sourceDocument->id }}">{{ $sourceDocument->name }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endif
                                </div>
                                @error('taskExistingDocumentId')<p class="ft-inquiry-task-document-error">{{ $message }}</p>@enderror
                            @endif

                            <label class="ft-inquiry-task-document-note">
                                <span>Document note (optional)</span>
                                <input type="text" wire:model="taskDocumentNote" placeholder="Add a short note about this document...">
                            </label>
                            @error('taskDocumentNote')<p class="ft-inquiry-task-document-error">{{ $message }}</p>@enderror

                            <div class="ft-inquiry-task-document-info">
                                <span>ⓘ</span>
                                <p>This document will appear directly under <strong>{{ $taskDocumentModalTask->title }}</strong>.</p>
                            </div>
                        </div>

                        <footer class="ft-inquiry-task-document-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskDocumentModal">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskDocument" wire:loading.attr="disabled" wire:target="saveTaskDocument,taskDocumentUpload"
                                @disabled($taskDocumentSource === 'upload' ? !$taskDocumentUpload : !$taskExistingDocumentId)>
                                <span wire:loading.remove wire:target="saveTaskDocument">Add document</span>
                                <span wire:loading wire:target="saveTaskDocument">Adding...</span>
                            </button>
                        </footer>
                    </section>
                </div>
            @endif

        </section>
    @endif
</div>
