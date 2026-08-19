@php
    $masterData = app(\App\Services\MasterDataService::class);
    $inquiryService = app(\App\Services\InquiryService::class);
    $tone = static function (string $status): string {
        return match (true) {
            str_contains($status, 'Converted'), str_contains($status, 'Completed') => 'green',
            str_contains($status, 'Dead'), str_contains($status, 'Closed') => 'red',
            str_contains($status, 'Ready'), str_contains($status, 'On Hold') => 'amber',
            str_contains($status, 'Waiting') => 'purple',
            default => 'blue',
        };
    };
    $priorityTone = static function (string $priority): string {
        return match (strtolower(trim($priority))) {
            'critical', 'urgent' => 'red',
            'high' => 'amber',
            'low' => 'green',
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
    $inquiryToolbarIsClear = trim((string) $search) === ''
        && $quick === 'all'
        && $listStatus === ''
        && $listClient === ''
        && $dateFrom === ''
        && $dateTo === ''
        && ! $hideCompleted;
    $inquiryAnyFilterActive = $metricFilter !== '' || ! $inquiryToolbarIsClear;
@endphp

<div class="ft-inquiry-prototype">
    @if(session('success'))<div class="flash-inline">{{ session('success') }}</div>@endif
    @if($mode !== 'create' && $errors->any())<div class="error-inline">{{ $errors->first() }}</div>@endif

    @if($mode === 'list')
        <section class="view">
            <div class="pagehead">
                <div><h1>Inquiries</h1><p>Manage client requests from first inquiry through tasks, conversion, or closure.</p></div>
                <div class="actions">
                    @if(auth()->user()->canModule('inquiries','create'))<button class="primary" type="button" wire:click="openCreate">＋ New Inquiry</button>@endif
                </div>
            </div>

            <div class="metrics ft-summary-card-grid" aria-label="Inquiry summary filters">
                <x-ui.summary-card label="Created Today" :value="$metrics['createdToday'] ?? 0" icon="created" tone="blue" caption="New inquiries received" :active="$metricFilter === 'createdToday'" wire:click="setMetricFilter('createdToday')" aria-pressed="{{ $metricFilter === 'createdToday' ? 'true' : 'false' }}" />
                <x-ui.summary-card label="Not Started" :value="$metrics['notStarted'] ?? 0" icon="not-started" tone="slate" caption="Waiting for first action" :active="$metricFilter === 'notStarted'" wire:click="setMetricFilter('notStarted')" aria-pressed="{{ $metricFilter === 'notStarted' ? 'true' : 'false' }}" />
                <x-ui.summary-card label="In Progress" :value="$metrics['inProgress'] ?? 0" icon="in-progress" tone="blue" caption="Work currently underway" :active="$metricFilter === 'inProgress'" wire:click="setMetricFilter('inProgress')" aria-pressed="{{ $metricFilter === 'inProgress' ? 'true' : 'false' }}" />
                <x-ui.summary-card label="Due This Week" :value="$metrics['dueThisWeek'] ?? 0" icon="due-week" tone="amber" caption="Required date this week" :active="$metricFilter === 'dueThisWeek'" wire:click="setMetricFilter('dueThisWeek')" aria-pressed="{{ $metricFilter === 'dueThisWeek' ? 'true' : 'false' }}" />
                <x-ui.summary-card label="Completed This Week" :value="$metrics['completedThisWeek'] ?? 0" icon="completed" tone="green" caption="Finished this week" :active="$metricFilter === 'completedThisWeek'" wire:click="setMetricFilter('completedThisWeek')" aria-pressed="{{ $metricFilter === 'completedThisWeek' ? 'true' : 'false' }}" />
                <x-ui.summary-card label="Needs Attention" :value="$metrics['attention'] ?? 0" icon="attention" tone="red" caption="Blocked, overdue or unassigned" :active="$metricFilter === 'attention'" wire:click="setMetricFilter('attention')" aria-pressed="{{ $metricFilter === 'attention' ? 'true' : 'false' }}" />
            </div>

            <div class="shell inquiry-list-v2">
                <div class="toolbar">
                    <div class="search"><span>⌕</span><input wire:model.live.debounce.350ms="search" placeholder="Search inquiry, title, client, task or assignee"></div>
                    <div class="filters inquiry-filter-controls">
                        <button class="chip {{ $metricFilter === '' && $inquiryToolbarIsClear ? 'active' : '' }}" type="button" wire:click="setQuick('all')" aria-pressed="{{ $metricFilter === '' && $inquiryToolbarIsClear ? 'true' : 'false' }}">All</button>
                        <button class="chip ft-inquiry-attention-filter {{ $quick === 'attention' ? 'active' : '' }}" type="button" wire:click="setQuick('attention')" aria-pressed="{{ $quick === 'attention' ? 'true' : 'false' }}">
                            <span aria-hidden="true">⚠</span> Attention needed
                        </button>
                        <label class="ft-inquiry-status-filter">
                            <select wire:model.live="listStatus" aria-label="Filter inquiries by task status">
                                <option value="">All task statuses</option>
                                @foreach($listStatusOptions as $statusOption)
                                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                                @endforeach
                            </select>
                            <span class="ft-inquiry-status-filter-chevron" aria-hidden="true">⌄</span>
                        </label>
                        <x-ui.remote-filter
                            class="ft-inquiry-list-client-filter"
                            label="Client"
                            property="listClient"
                            type="clients"
                            context="inquiries"
                            action="setInquiryListFilter"
                            :value="$listClient"
                            placeholder="All clients"
                            :selected-label="$listClientLabel ?: null"
                            :initial-options="$listClientFilterOptions"
                            :menu-width="300"
                            :fixed-menu="true"
                            wire:key="inquiry-list-client-filter-{{ $listClient ?: 'all' }}-{{ substr(md5($listClientLabel ?: 'all'), 0, 8) }}"
                        />
                        <label class="completed-toggle {{ $hideCompleted ? 'active' : '' }}">
                            <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed inquiries">
                            <span class="completed-check" aria-hidden="true">✓</span>
                            <span>Hide completed</span>
                        </label>
                        <x-ui.date-range-filter
                            class="ft-inquiry-date-range"
                            from-property="dateFrom"
                            to-property="dateTo"
                            :from-value="$dateFrom"
                            :to-value="$dateTo"
                            label="Created date"
                            from-label="From"
                            to-label="To"
                        />
                        <button
                            class="chip ft-inquiry-clear-filter"
                            type="button"
                            wire:click="clearFilters"
                            @disabled(! $inquiryAnyFilterActive)
                            aria-label="Clear active inquiry filter"
                        >
                            <span aria-hidden="true">×</span> Clear filter
                        </button>
                    </div>
                </div>
                <div class="inquiry-list-table" role="region" aria-label="Inquiry list" tabindex="0">
                    <div class="listhead">
                        <div>Inquiry</div>
                        <div>Title</div>
                        <div>Client / Item</div>
                        <div>Priority</div>
                        <div>Due Date</div>
                        <div>Status</div>
                        <div>Flag</div>
                        <div>Current Task</div>
                        <div>Assignee</div>
                        <div>Task Status</div>
                        <div>Started At</div>
                        <div>Progress</div>
                        <div>Updated At</div>
                        <div>View</div>
                        <div aria-label="Actions"></div>
                    </div>
                    <div class="inquiry-list-body">
                        @forelse($inquiryRows as $row)
                            @php
                                $clientCode = strtoupper(trim((string) ($row['clientCode'] ?? '')));
                                $clientName = strtoupper(trim((string) ($row['client'] ?? '')));
                                $clientRowTone = ($clientCode === 'IID' || preg_match('/\bIID\b/i', $clientName))
                                    ? 'iid'
                                    : (($clientCode === 'NEP' || preg_match('/\bNEP\b/i', $clientName)) ? 'nep' : '');
                            @endphp
                            <article class="row {{ $clientRowTone ? 'ft-client-row-'.$clientRowTone : '' }}" wire:key="inquiry-list-{{ $row['id'] }}">
                                <div class="cell ft-inquiry-list-identity" data-label="Inquiry">
                                    <span class="ft-copyable-id-wrap ft-inquiry-list-code-wrap">
                                        <a class="id" href="{{ route('inquiries.index', ['open' => $row['id']]) }}" wire:navigate>{{ $row['number'] }}</a>
                                        <button type="button" class="ft-copy-id-btn" title="Copy Inquiry ID" aria-label="Copy {{ $row['number'] }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($row['number'])); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                                    </span>
                                    <span class="sub ft-inquiry-created-by" title="Created by {{ $row['createdBy'] }}">Created by {{ $row['createdBy'] }}</span>
                                    <span class="sub ft-inquiry-created-at">{{ $row['createdDate'] }} · {{ $row['createdTime'] }}</span>
                                </div>
                                <div class="cell ft-inquiry-list-title-cell" data-label="Title">
                                    <span class="title ft-inquiry-title-preview ft-inquiry-title-desktop" title="{{ $row['title'] }}">{{ $row['titlePreview'] }}</span>
                                    <span class="title ft-inquiry-title-mobile" title="{{ $row['title'] }}">{{ $row['title'] }}</span>
                                    <span class="sub ft-inquiry-mobile-created">Created by {{ $row['createdBy'] }} · {{ $row['createdDate'] }} · {{ $row['createdTime'] }}</span>
                                </div>
                                <div class="ft-inquiry-mobile-separator ft-inquiry-mobile-separator-before-task" aria-hidden="true"></div>
                                <div class="cell ft-inquiry-list-client-cell" data-label="Client / Item">
                                    <span class="ft-client-name-with-logo"><x-ui.client-logo :name="$row['client']" :src="$row['clientLogoUrl'] ?? null" :size="24" /><span class="title">{{ $row['client'] }}</span></span>
                                    <span class="sub">Contact: {{ $row['clientContact'] ?: '—' }}</span>
                                    @if($row['item'])<span class="sub">{{ $row['item'] }}</span>@endif
                                </div>
                                @php
                                    $rowTaskStatusColor = $masterData->displayColorFor('inquiry_task_status', $row['taskStatus']);
                                    $rowTaskFlagTone = match ($row['flag']) {
                                        'Requires attention', 'Overdue' => 'red',
                                        'Due Today' => 'amber',
                                        'No flag' => 'green',
                                        default => 'blue',
                                    };
                                    $rowInquiryPriorityColor = $masterData->displayColorFor('priority', $row['priority']);
                                    $rowInquiryStatusColor = $inquiryService->inquiryStatusColor($row['status'], $row['taskStatus']);
                                @endphp
                                <div class="cell ft-inquiry-list-priority-cell" data-label="Priority"><span class="pill {{ $rowInquiryPriorityColor ? 'ft-master-color' : $priorityTone($row['priority']) }}" style="{{ \App\Support\MasterColor::style($rowInquiryPriorityColor) }}">{{ $row['priority'] }}</span></div>
                                <div class="cell ft-inquiry-list-due-cell" data-label="Due Date"><span class="title">{{ $row['due'] }}</span></div>
                                <div class="cell ft-inquiry-list-status-cell" data-label="Status"><span class="pill {{ $rowInquiryStatusColor ? 'ft-master-color' : $tone($row['status']) }}" style="{{ \App\Support\MasterColor::style($rowInquiryStatusColor) }}">{{ $row['status'] }}</span></div>
                                <div class="cell ft-inquiry-list-flag-cell" data-label="Flag">
                                    @if($row['flag'] === 'No flag')
                                        <span class="ft-inquiry-no-flag">No flag</span>
                                    @else
                                        <span class="pill {{ $rowTaskFlagTone }}">{{ $row['flag'] }}</span>
                                    @endif
                                </div>
                                <div class="cell ft-inquiry-list-task-cell" data-label="Current Task"><span class="title">{{ $row['currentTask'] }}</span><span class="sub">{{ $row['taskCaption'] }}</span></div>
                                <div class="cell ft-inquiry-list-assignee-cell" data-label="Assignee">
                                    <div class="ownerline">
                                        <x-ui.avatar
                                            class="ft-inquiry-assignee-avatar"
                                            :name="$row['assignee']"
                                            :src="$row['assigneeAvatar'] ?? null"
                                            :size="34"
                                        />
                                        <span class="title" title="{{ $row['assignee'] }}">{{ $row['assignee'] }}</span>
                                    </div>
                                </div>
                                <div class="cell ft-inquiry-list-task-status-cell" data-label="Task Status"><span class="pill {{ $rowTaskStatusColor ? 'ft-master-color' : $tone($row['taskStatus']) }}" style="{{ \App\Support\MasterColor::style($rowTaskStatusColor) }}">{{ $row['taskStatus'] }}</span></div>
                                <div class="ft-inquiry-mobile-separator ft-inquiry-mobile-separator-after-task" aria-hidden="true"></div>
                                <div class="cell ft-inquiry-list-started-cell" data-label="Started At">
                                    @if($row['hasStarted'])
                                        <span class="title">{{ $row['startedDate'] }}</span>
                                        <span class="sub">{{ $row['startedTime'] }}</span>
                                    @else
                                        <span class="title ft-inquiry-not-started">Not Started</span>
                                    @endif
                                </div>
                                <div class="cell ft-inquiry-list-progress-cell" data-label="Progress">
                                    <div class="ft-inquiry-list-progress">
                                        <div class="ft-inquiry-list-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $row['progressPercent'] }}" aria-label="{{ $row['progress'] }} of {{ $row['total'] }} tasks completed"><span style="width:{{ $row['progressPercent'] }}%"></span></div>
                                        <b>{{ $row['progress'] }}/{{ $row['total'] }}</b>
                                    </div>
                                </div>
                                <div class="cell ft-inquiry-list-updated-cell" data-label="Updated At">
                                    <span class="title">{{ $row['updatedDate'] }}</span>
                                    <span class="sub">{{ $row['updatedTime'] }}</span>
                                </div>
                                <div class="ft-inquiry-mobile-separator ft-inquiry-mobile-separator-before-footer" aria-hidden="true"></div>
                                <div class="cell ft-inquiry-list-view-cell" data-label="View"><a class="openbtn openbtn-link" href="{{ route('inquiries.index', ['open' => $row['id']]) }}" aria-label="View details for {{ $row['number'] }}" wire:navigate><span class="ft-inquiry-view-label-desktop">View</span><span class="ft-inquiry-view-label-mobile">Details</span><span aria-hidden="true">→</span></a></div>
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
                                </div>
                                @error('clientId')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="ft-inquiry-create-field">
                                <label>Client contact *</label>
                                <div class="ft-inquiry-client-control-row">
                                    <div class="ft-inquiry-contact-select-wrap">
                                        <select wire:model="clientContact" @disabled(!$clientId || empty($clientContactOptions)) aria-required="true">
                                            @if(!$clientId)
                                                <option value="">Select a client first</option>
                                            @elseif(empty($clientContactOptions))
                                                <option value="">No contact recorded</option>
                                            @else
                                                @foreach($clientContactOptions as $contactOption)
                                                    <option value="{{ $contactOption['value'] }}">{{ $contactOption['label'] }}{{ $contactOption['primary'] ? ' · Primary' : '' }}{{ $contactOption['meta'] ? ' · '.$contactOption['meta'] : '' }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @if($clientId && empty($clientContactOptions))
                                    <small class="ft-inquiry-field-help">This client has no contact. Add a contact from Clients before creating the Inquiry.</small>
                                @endif
                                @error('clientContact')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid">
                            <label class="ft-inquiry-create-field">
                                <span>Reference number</span>
                                <input wire:model="referenceNumber" placeholder="Enter the client-provided ES or NEQ number">
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

                        @php
                            $createPriorityColor = optional($createPriorityOptions->first(
                                fn ($priority) => (string) $priority->name === (string) $createPriority
                            ))->color;
                        @endphp
                        <div class="ft-inquiry-create-field ft-inquiry-create-field-full">
                            <label>Priority *</label>
                            <select
                                data-master-color-select
                                wire:model="createPriority"
                                class="{{ $createPriorityColor ? 'ft-master-color' : '' }}"
                                style="{{ \App\Support\MasterColor::style($createPriorityColor) }}"
                                aria-label="Inquiry priority"
                            >
                                @forelse($createPriorityOptions as $priority)
                                    <option value="{{ $priority->name }}" data-color="{{ $priority->color }}">{{ $priority->name }}</option>
                                @empty
                                    <option value="">No active priorities</option>
                                @endforelse
                            </select>
                            @error('createPriority')<small class="field-error">{{ $message }}</small>@enderror
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

                    @if($canUseInquiryProductSelector)
                        @include('components.inquiries.create-products')
                    @endif

                    <section class="section ft-inquiry-create-section ft-inquiry-attachments-section">
                        <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                            <span>{{ $canUseInquiryProductSelector ? 3 : 2 }}</span><h2>Attachments</h2><p>Add emails, specifications, artwork or reference images.</p>
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
                            <input x-ref="createAttachmentInput" class="file-input" type="file" wire:model="createAttachments" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai,.eps,.esp">
                            <div class="inquiry-dropzone-icon" aria-hidden="true">⇧</div>
                            <div class="inquiry-dropzone-copy">
                                <strong>Drop client files here</strong>
                                <span class="ft-inquiry-drop-or">or <b>browse files</b></span>
                                <small>PDF, Office files, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB per file</small>
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

                    <x-ui.create-workflow-picker
                        class="section ft-inquiry-create-section ft-inquiry-next-section"
                        :step="$canUseInquiryProductSelector ? 4 : 3"
                        title="What happens next"
                        :workflow-options="$workflowFilterOptions"
                        :selected-workflow-id="$createWorkflowId"
                        :selected-workflow-name="$selectedWorkflowName"
                        :phase-count="$createWorkflowPhaseCount"
                        :task-count="$createWorkflowTaskCount"
                        selection-property="createWorkflowId"
                        option-fallback="Inquiry workflow"
                        footnote="Tasks are created when you select Create inquiry."
                        :preview-allowed="auth()->user()->canAccess('workflow.view')"
                        :empty-message="$createWorkflowId && $createWorkflowTaskCount === 0 ? 'This Workflow has no active Task Pack tasks.' : null"
                        error-field="createWorkflowId"
                        wire:key="create-inquiry-workflow-picker"
                    />

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
                default => (string) ($inquiry->status ?: \App\Services\InquiryService::AUTO_READY_STATUS),
            };
            $detailStatusColor = $inquiryService->inquiryStatusColor($detailStatus, (string) ($currentTask?->status ?: ''));
            $detailPriorityColor = $masterData->displayColorFor('priority', (string) $inquiry->priority);
            $headerFlagTask = $currentTask?->needs_attention ? $currentTask : $inquiry->tasks->first(fn ($task) => (bool) $task->needs_attention);
            $headerFlagLabel = $inquiry->needs_attention
                ? 'Requires attention'
                : ($headerFlagTask ? 'Requires attention' : '');
            $headerFlagReason = $inquiry->needs_attention
                ? (string) ($inquiry->attention_reason ?? '')
                : (string) ($headerFlagTask?->attention_reason ?? '');
        @endphp
        <section class="view inquiry-detail-view ft-detail-products-scope" x-data="{
            inquiryStatus:@js($detailStatus),
            inquiryStatusColor:@js($detailStatusColor),
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
                    if(result?.inquiryColor)this.inquiryStatusColor=result.inquiryColor;
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
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span>Client contact <strong>{{ $inquiry->client_contact ?: '—' }}</strong></span></span>
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
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item ft-inquiry-header-action" title="{{ $headerFlagReason ?: 'Request attention from the Inquiry creator and administrators' }}">
                            <span>Action:</span>
                            <button type="button" class="ft-inquiry-header-flag-button {{ $headerFlagLabel !== '' ? 'is-flagged' : '' }}" wire:click="openInquiryAttentionReason" @disabled($inquiry->result) aria-label="Request attention" title="{{ $headerFlagLabel !== '' ? 'View or update attention request' : 'Request attention' }}">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 21V4"></path><path d="M7 5h10l-2 4 2 4H7"></path></svg>
                            </button>
                            @if($headerFlagLabel !== '')<strong class="ft-inquiry-header-flag-label">{{ $headerFlagLabel }}</strong>@endif
                        </span>
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
                                <span class="status-dot {{ $detailStatusColor ? 'ft-master-color-dot' : '' }}" style="{{ \App\Support\MasterColor::style($detailStatusColor) }}" x-bind:class="inquiryStatusColor ? 'ft-master-color-dot' : statusTone(inquiryStatus)" x-bind:style="inquiryStatusColor ? '--ft-master-color:'+inquiryStatusColor : ''"></span>
                                <b class="ft-property-value" x-text="inquiryStatus">{{ $detailStatus }}</b>
                            </div>
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="{ ...window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-priority'), label: 'Inquiry priority', value: @js($inquiry->priority), display: @js($inquiry->priority) }), priorityColor: @js($detailPriorityColor) }"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Priority</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="status-dot ft-master-color-dot" style="{{ \App\Support\MasterColor::style($detailPriorityColor) }}" x-bind:style="priorityColor ? '--ft-master-color:'+priorityColor : ''"></span><b class="ft-property-value" x-text="display">{{ $inquiry->priority }}</b>@if($canEditInquiry && !$inquiry->result)<button type="button" :disabled="status === 'saving'" title="Edit priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryPriority?.showPicker ? $refs.inquiryPriority.showPicker() : $refs.inquiryPriority?.focus())">✎</button>@endif</div>
                            @if($canEditInquiry && !$inquiry->result)
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select data-master-color-select x-ref="inquiryPriority" x-model="draftValue" class="ft-task-property-inline-input ft-master-color" style="{{ \App\Support\MasterColor::style($detailPriorityColor) }}" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="const nextColor=String($event.target.selectedOptions[0]?.dataset?.color || ''); window.FlowTrackMasterColor?.applySelect($event.target); commit($event.target.value, selectedLabel($event), () => $wire.updateInquiryField('priority', draftValue)).then(ok => { if(ok) priorityColor=nextColor; });">@unless($inquiryPriorities->contains(fn($priority) => (string) $priority->name === (string) $inquiry->priority))<option value="{{ $inquiry->priority }}" data-color="{{ $masterData->displayColorFor('priority', (string) $inquiry->priority) }}">{{ $inquiry->priority }}</option>@endunless @foreach($inquiryPriorities as $priority)<option value="{{ $priority->name }}" data-color="{{ $masterData->displayColorFor('priority', $priority->name) }}">{{ $priority->name }}</option>@endforeach</select></div>
                                <x-ui.inline-save-state compact />
                            @endif
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-assignee'), label: 'Inquiry assignee', value: @js($inquiry->owner_id ?? ''), display: @js($inquiry->owner?->name ?? 'Unassigned'), avatarUrl: @js($inquiry->owner?->profileImageUrl() ?? '') })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                            x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                            x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateInquiryField('owner_id', draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
                        >
                            <small>Assignee</small>
                            <div x-show="!editing" class="ft-task-property-display ft-inline-person-live">
                                <x-ui.inline-live-avatar :size="26" />
                                <b class="ft-property-value" x-text="display">{{ $inquiry->owner?->name ?? 'Unassigned' }}</b>
                                @if($canEditInquiry && $canAssignInquiry && !$inquiry->result)<button type="button" :disabled="status === 'saving'" title="Edit assignee" aria-label="Edit Inquiry assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>@endif
                            </div>
                            @if($canEditInquiry && $canAssignInquiry && !$inquiry->result)
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor ft-task-property-assignee-editor">
                                    <x-ui.inline-remote-user
                                        :value="$inquiry->owner_id ?? ''"
                                        :selected-label="$inquiry->owner?->name ?? 'Unassigned'"
                                        context="inquiry-owner"
                                        parent-type="inquiry"
                                        :parent-id="$inquiry->id"
                                        search-placeholder="Search assignee…"
                                        trigger-class="ft-task-property-inline-input"
                                        variant="compact"
                                        :menu-width="300"
                            :fixed-menu="true"
                                    />
                                </div>
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

                    @if($canViewInquiryProducts)
                    @php
                        // Only persisted products belong in the details table. The shared
                        // Add Product panel owns the temporary selection state, exactly as
                        // it does on Order Details, so unfinished legacy draft rows stay out.
                        $inquiryItemRows = collect($inquiry->items ?? collect())
                            ->filter(fn ($item) => filled($item->item_name))
                            ->values();
                        $inquiryItemCount = $inquiryItemRows->count();
                        $inquiryItemUnits = (float) $inquiryItemRows->sum('quantity');
                    @endphp
                    <x-catalog.detail-products-card
                        id="inquiry-products-card"
                        variant="inquiry"
                        :count="$inquiryItemCount"
                        :total-units="$inquiryItemUnits"
                    >
                        @if($inquiryItemRows->isEmpty())
                            <tr class="ft-order-product-empty-row"><td colspan="7">No products have been added to this Inquiry yet.</td></tr>
                        @else
                            @foreach($inquiryItemRows as $item)
                                @php
                                    $isDraftInquiryItem = blank($item->item_name);
                                    $categoryNeedsSelection = filled($item->id) && blank($item->category);
                                    $productNeedsSelection = filled($item->id) && filled($item->category) && blank($item->item_name);
                                    $categoryLabel = $item->category ?: 'Select category';
                                    $productLabel = $item->item_name ?: (blank($item->category) ? 'Select category first' : 'Select product');
                                    $productPickerKey = 'inquiry-item-'.$item->id.'-product-'.md5((string) ($item->category ?? '').'|'.(string) ($item->item_name ?? ''));
                                    $productMaster = $inquiryProductMasters->get(mb_strtolower(trim((string) ($item->item_name ?? ''))));
                                    $productImageUrl = $productMaster?->productImageUrl();
                                    $productCode = $productMaster?->productDisplayCode();
                                    $productReference = $productMaster?->productReferenceCode();
                                    $classificationParts = collect([
                                        $productMaster?->productMainCategory(),
                                        ...array_filter(array_map('trim', preg_split('/\s*>\s*/', (string) ($productMaster?->productClassificationPath() ?? '')) ?: [])),
                                    ])->filter()->unique()->values();
                                    if ($classificationParts->isEmpty() && filled($item->category)) $classificationParts = collect([$item->category]);
                                    $categoryDisplay = $classificationParts->implode(' › ') ?: $categoryLabel;
                                    $unitPrice = $item->unit_price !== null ? (float) $item->unit_price : null;
                                    $unitPriceValue = $unitPrice !== null ? number_format($unitPrice, 2, '.', '') : '';
                                    $unitPriceDisplay = $unitPrice !== null ? $inquiryCurrencySymbol.number_format($unitPrice, 2) : '—';
                                    $updatedDate = $item->updated_at ? \App\Support\UserLocalTime::format($item->updated_at, 'M j, Y') : '—';
                                    $updatedTime = $item->updated_at ? \App\Support\UserLocalTime::format($item->updated_at, 'g:i A') : null;
                                @endphp
                                <tr
                                    wire:key="inquiry-product-detail-{{ $item->id }}"
                                    x-data="{ categorySaving: false, productSaving: false, quantitySaving: false, priceSaving: false, notesSaving: false, actionOpen: false, draftProductReady: @js(filled($item->item_name)) }"
                                    @class(['ft-order-product-draft-row' => $isDraftInquiryItem])
                                >
                                    <td data-label="Product">
                                        <x-catalog.detail-product-identity
                                            :image-url="$productImageUrl"
                                            :alt="$item->item_name ?? ''"
                                            :code="$productCode"
                                            :reference="$productReference"
                                            fallback-meta="Inquiry product"
                                        >
                                            <div
                                                class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor ft-order-product-name-editor"
                                                wire:key="{{ $productPickerKey }}"
                                                x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-item-'.$item->id.'-product'), label: 'product', value: @js($item->item_name ?? ''), display: @js($productLabel) })"
                                                x-init="if (@js($canEditInquiryProducts && $productNeedsSelection)) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                                x-on:click.outside="if (editing && !@js($productNeedsSelection)) cancelEdit()"
                                                x-on:ft-inline-remote-cancel.stop="if (!@js($productNeedsSelection)) cancelEdit()"
                                                x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select product'); productSaving = true; commit(nextValue, nextLabel, () => $wire.updateInquiryItem({{ $item->id }}, 'item_name', nextValue)).then(async (ok) => { productSaving = false; if (ok) { draftProductReady = true; await $wire.$refresh(); } })"
                                            >
                                                <span class="ft-order-product-name" x-show="!editing" x-text="display">{{ $productLabel }}</span>
                                                @if($canEditInquiryProducts)
                                                    <button x-show="!editing" :disabled="status === 'saving' || categorySaving || quantitySaving || priceSaving || notesSaving || @js(blank($item->category))" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="{{ blank($item->category) ? 'Select a category first' : 'Edit product' }}" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                                    <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                                        <x-ui.inline-remote-catalog
                                                            type="products"
                                                            context="inquiry-detail"
                                                            :value="$item->item_name ?? ''"
                                                            :selected-label="$productLabel"
                                                            :placeholder="blank($item->category) ? 'Select category first' : 'Select product'"
                                                            search-label="product"
                                                            :params="['category' => (string) ($item->category ?? '')]"
                                                            :disabled="blank($item->category)"
                                                            :menu-width="360"
                                                            :fixed-menu="true"
                                                        />
                                                    </div>
                                                    <x-ui.inline-save-state compact />
                                                @endif
                                            </div>
                                        </x-catalog.detail-product-identity>
                                    </td>
                                    <td data-label="Category">
                                        <div
                                            class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor ft-order-product-category-editor"
                                            wire:key="inquiry-item-{{ $item->id }}-category-{{ md5((string) ($item->category ?? '')) }}"
                                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-item-'.$item->id.'-category'), label: 'product category', value: @js($item->category ?? ''), display: @js($categoryDisplay) })"
                                            x-init="if (@js($canEditInquiryProducts && $categoryNeedsSelection)) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                            x-on:click.outside="if (editing && !@js($categoryNeedsSelection)) cancelEdit()"
                                            x-on:ft-inline-remote-cancel.stop="if (!@js($categoryNeedsSelection)) cancelEdit()"
                                            x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select category'); const changed = nextValue !== savedValue; categorySaving = true; commit(nextValue, nextLabel, () => $wire.updateInquiryItem({{ $item->id }}, 'category', nextValue)).then(async (ok) => { if (ok && changed) await $wire.$refresh(); categorySaving = false })"
                                        >
                                            <span class="ft-order-product-category-path" x-show="!editing" x-text="display">{{ $categoryDisplay }}</span>
                                            @if($canEditInquiryProducts)
                                                <button x-show="!editing" :disabled="status === 'saving' || productSaving || quantitySaving || priceSaving || notesSaving" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                                <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                                    <x-ui.inline-remote-catalog
                                                        type="product-categories"
                                                        context="inquiry-detail"
                                                        :value="$item->category ?? ''"
                                                        :selected-label="$categoryLabel"
                                                        placeholder="Select category"
                                                        search-label="product category"
                                                        :menu-width="340"
                                                        :fixed-menu="true"
                                                    />
                                                </div>
                                                <x-ui.inline-save-state compact />
                                            @endif
                                        </div>
                                    </td>
                                    <td class="ft-order-product-quantity" data-label="Quantity">
                                        <div
                                            class="ft-inline-field-editor ft-inline-edit-shell"
                                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-item-'.$item->id.'-quantity'), label: 'quantity', value: @js((string) max(1, (int) $item->quantity)), display: @js(number_format((int) max(1, (int) $item->quantity)).' units') })"
                                            x-init="if (@js($canEditInquiryProducts && $isDraftInquiryItem)) editing = true"
                                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        >
                                            <span x-show="!editing" class="ft-order-product-edit-value" x-text="display">{{ number_format((int) max(1, (int) $item->quantity)) }} units</span>
                                            @if($canEditInquiryProducts)
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || priceSaving || notesSaving" type="button" class="ft-inline-edit-button" title="Edit quantity" aria-label="Edit product quantity" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.quantityInput.focus(); $refs.quantityInput.select(); })">✎</button>
                                                <input x-ref="quantityInput" data-inquiry-item-quantity x-cloak x-show="editing" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-number-input" type="number" min="1" max="999999999" step="1" :disabled="categorySaving || productSaving"
                                                    x-on:keydown.escape.prevent="cancelEdit()"
                                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                                    x-on:blur="if (editing && !categorySaving && !productSaving && !quantitySaving) { const next = positiveInteger(draftValue); quantitySaving = true; commit(next, Number(next).toLocaleString() + ' units', () => $wire.updateInquiryItem({{ $item->id }}, 'quantity', next)).then(async (ok) => { quantitySaving = false; if (ok && @js($isDraftInquiryItem)) await $wire.$refresh(); else if (!ok) editing = true; }) }"
                                                >
                                                <x-ui.inline-save-state compact />
                                            @endif
                                        </div>
                                    </td>
                                    <td class="ft-order-product-price" data-label="Unit price">
                                        <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-item-'.$item->id.'-unit-price'), label: 'unit price', value: @js($unitPriceValue), display: @js($unitPriceDisplay) })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                            <span x-show="!editing" class="ft-order-product-edit-value" x-text="display">{{ $unitPriceDisplay }}</span>
                                            @if($canEditInquiryProducts)
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || quantitySaving || notesSaving" type="button" class="ft-inline-edit-button" title="Edit unit price" aria-label="Edit unit price" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.priceInput.focus(); $refs.priceInput.select(); })">✎</button>
                                                <div x-cloak x-show="editing" class="ft-order-product-price-input-wrap">
                                                    <span>{{ $inquiryCurrencySymbol }}</span>
                                                    <input x-ref="priceInput" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-number-input" type="number" min="0" step="0.01"
                                                        x-on:keydown.escape.prevent="cancelEdit()"
                                                        x-on:keydown.enter.prevent="$event.target.blur()"
                                                        x-on:blur="if (editing && !priceSaving) { const raw = String(draftValue ?? '').trim(); const parsed = raw === '' ? '' : Number(raw); const next = raw === '' ? '' : (Number.isFinite(parsed) ? Math.max(0, parsed).toFixed(2) : ''); priceSaving = true; commit(next, next === '' ? '—' : @js($inquiryCurrencySymbol) + Number(next).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}), () => $wire.updateInquiryItem({{ $item->id }}, 'unit_price', next)).then((ok) => { priceSaving = false; if (!ok) editing = true; }) }"
                                                    >
                                                </div>
                                                <x-ui.inline-save-state compact />
                                            @endif
                                        </div>
                                    </td>
                                    <td class="ft-order-product-notes" data-label="Notes">
                                        <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-item-'.$item->id.'-notes'), label: 'product notes', value: @js($item->notes ?? ''), display: @js($item->notes ?: 'Add notes') })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                            <span x-show="!editing" class="ft-order-product-note-value" :class="{ 'is-empty': !value }" x-text="display">{{ $item->notes ?: 'Add notes' }}</span>
                                            @if($canEditInquiryProducts)
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || quantitySaving || priceSaving" type="button" class="ft-inline-edit-button" title="Edit notes" aria-label="Edit product notes" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.notesInput.focus(); $refs.notesInput.select(); })">✎</button>
                                                <input x-ref="notesInput" x-cloak x-show="editing" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-notes-input" type="text" maxlength="2000" placeholder="Product notes"
                                                    x-on:keydown.escape.prevent="cancelEdit()"
                                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                                    x-on:blur="if (editing && !notesSaving) { const next = String(draftValue || '').trim(); notesSaving = true; commit(next, next || 'Add notes', () => $wire.updateInquiryItem({{ $item->id }}, 'notes', next)).then((ok) => { notesSaving = false; if (!ok) editing = true; }) }"
                                                >
                                                <x-ui.inline-save-state compact />
                                            @endif
                                        </div>
                                    </td>
                                    <x-catalog.detail-product-updated
                                        :primary="$updatedDate"
                                        :secondary="$updatedTime"
                                    />
                                    <td class="ft-order-product-actions-cell" data-label="Actions">
                                        <x-catalog.detail-product-actions
                                            :item-id="$item->id"
                                            :can-delete="$canDeleteInquiryProducts"
                                            remove-method="removeInquiryItem"
                                            confirm-text="Remove this product from the Inquiry?"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        <x-slot:afterTable>
                            @if($showAddInquiryProductForm && $canCreateInquiryProducts)
                                <x-catalog.detail-add-product
                                    :wire-key="'inquiry-detail-add-product-'.$inquiry->id"
                                    search-model="inquiryProductSearch"
                                    :search-value="$inquiryProductSearch"
                                    :search-results="$inquiryProductSearchResults"
                                    :result-total="$inquiryProductResultTotal"
                                    show-all-method="showAllInquiryProductResults"
                                    select-method="selectInquiryProduct"
                                    :selected-product="$inquiryProductSelectedProduct"
                                    :category-value="$inquiryProductCategory"
                                    quantity-model="inquiryProductQuantity"
                                    unit-price-model="inquiryProductUnitPrice"
                                    :currency-symbol="$inquiryCurrencySymbol"
                                    close-method="closeAddInquiryProductForm"
                                    save-method="saveInquiryProduct"
                                    selected-error-key="inquiryProductSelectedId"
                                    quantity-error-key="inquiryProductQuantity"
                                    unit-price-error-key="inquiryProductUnitPrice"
                                />
                            @endif
                        </x-slot:afterTable>

                        <x-slot:footer>
                            <span>Product and quantity changes are recorded in inquiry activity.</span>
                            @if($canCreateInquiryProducts && !$showAddInquiryProductForm)
                                <button type="button" class="ft-outline-btn ft-order-product-add-another" wire:click="openAddInquiryProductForm" wire:loading.attr="disabled" wire:target="openAddInquiryProductForm">＋ Add another product</button>
                            @endif
                        </x-slot:footer>
                    </x-catalog.detail-products-card>
                    @endif

                    <div id="tab-workflow" class="ft-inquiry-overview-taskflow ft-inquiry-workflow-pane">
                        @if(auth()->user()->canModule('tasks', 'view'))
                            @include('livewire.inquiries._taskflow')
                        @else
                            <section class="panel"><div class="ft-inquiry-empty-workflow">Task access is not enabled for your role.</div></section>
                        @endif
                    </div>

                    @if(auth()->user()->canModule('documents', 'view'))@include('livewire.inquiries._attachments')@endif
                    @include('livewire.inquiries._activity')
                </div>
            @endif

            @if($showTaskDocumentModal && $taskDocumentModalTask)
                @php
                    $completeAfterTaskDocument = (int) ($pendingCompletionTaskId ?? 0) === (int) $taskDocumentModalTask->id;
                @endphp
                <div class="ft-inquiry-task-document-modal-backdrop" wire:key="inquiry-task-document-modal" wire:click.self="closeTaskDocumentModal">
                    <section class="ft-inquiry-task-document-modal" role="dialog" aria-modal="true" aria-labelledby="task-document-modal-title">
                        <header class="ft-inquiry-task-document-modal-head">
                            <div>
                                <h2 id="task-document-modal-title">{{ $completeAfterTaskDocument ? 'Required file needed to complete task' : 'Add new document to task' }}</h2>
                                <p>{{ $completeAfterTaskDocument ? 'Add the required file now. The task will be completed automatically after the document is saved.' : 'Upload a new file or choose a document that already exists.' }}</p>
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
                                    <span class="ft-inquiry-task-document-reference"><b>Inquiry Reference:</b> {{ $inquiry->reference_number ?: '—' }}</span>
                                </div>
                                <span class="ft-inquiry-task-document-target-lock">▣&nbsp; Task selected</span>
                            </div>

                            <div class="ft-inquiry-task-document-source-label">Document source</div>
                            <div class="ft-inquiry-task-document-source-tabs">
                                <button type="button" class="{{ $taskDocumentSource === 'upload' ? 'active' : '' }}" wire:click="setTaskDocumentSource('upload')" @disabled(!$canCreateDocuments)>
                                    <span>↥</span> Upload new
                                </button>
                                <button type="button" class="{{ $taskDocumentSource === 'existing' ? 'active' : '' }}" wire:click="setTaskDocumentSource('existing')" @disabled(!$canLinkDocuments)>
                                    <span>▤</span> Choose existing
                                </button>
                            </div>

                            @if($taskDocumentSource === 'upload' && $canCreateDocuments)
                                <label class="ft-inquiry-task-document-dropzone">
                                    <input type="file" wire:model="taskDocumentUpload" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai,.eps,.esp">
                                    <span class="ft-inquiry-task-document-upload-icon">⇧</span>
                                    @if($taskDocumentUpload)
                                        <strong>{{ $taskDocumentUpload->getClientOriginalName() }}</strong>
                                        <b>File selected — choose another file</b>
                                        <small>{{ number_format(max(1, (int) ceil($taskDocumentUpload->getSize() / 1024))) }} KB · ready to add</small>
                                    @else
                                        <strong>Drop a file here</strong>
                                        <b>or browse files</b>
                                        <small>PDF, Office files, JPG, PNG, ZIP, AI, EPS or ESP · Max 20 MB</small>
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
                                <p>
                                    This document will appear directly under <strong>{{ $taskDocumentModalTask->title }}</strong>.
                                    @if($completeAfterTaskDocument) Saving it will also mark the task as Completed. @elseif($taskDocumentModalTask->completed_at) Adding a document will not reopen or change the completed task. @endif
                                </p>
                            </div>
                        </div>

                        <footer class="ft-inquiry-task-document-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskDocumentModal">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskDocument" wire:loading.attr="disabled" wire:target="saveTaskDocument,taskDocumentUpload"
                                @disabled($taskDocumentSource === 'upload' ? !$taskDocumentUpload : !$taskExistingDocumentId)>
                                <span wire:loading.remove wire:target="saveTaskDocument">{{ $completeAfterTaskDocument ? 'Add file & complete' : 'Add document' }}</span>
                                <span wire:loading wire:target="saveTaskDocument">{{ $completeAfterTaskDocument ? 'Adding & completing...' : 'Adding...' }}</span>
                            </button>
                        </footer>
                    </section>
                </div>
            @endif

            @if($showInquiryAttentionModal)
                <div class="ft-inquiry-attention-modal-backdrop" wire:key="inquiry-attention-modal" wire:click.self="closeInquiryAttentionReason">
                    <section class="ft-inquiry-attention-modal" role="dialog" aria-modal="true" aria-labelledby="inquiry-attention-modal-title">
                        <header class="ft-inquiry-attention-modal-head">
                            <div>
                                <h2 id="inquiry-attention-modal-title">Request attention</h2>
                                <p>{{ $inquiry->inquiry_number }} · Admin, Super Admin and the Inquiry creator will be notified.</p>
                            </div>
                            <button type="button" class="ft-inquiry-attention-modal-close" wire:click="closeInquiryAttentionReason" aria-label="Close">×</button>
                        </header>
                        <div class="ft-inquiry-attention-modal-body ft-mention-host">
                            <label for="inquiry-attention-reason">Reason for flag *</label>
                            <textarea id="inquiry-attention-reason" class="ft-mention-input" wire:model="inquiryAttentionReason" rows="5" maxlength="2000" autocomplete="off" data-mention-users="{{ json_encode($inquiryMentionUsers->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" placeholder="Explain what needs attention. Type @ to mention a user..."></textarea>
                            @error('inquiryAttentionReason')<p class="ft-inquiry-attention-modal-error">{{ $message }}</p>@enderror
                            <p class="ft-inquiry-attention-modal-help">The reason is added to Inquiry comments. Use <b>@</b> to mention specific users in addition to the automatic Admin/Super Admin/creator notification.</p>
                        </div>
                        <footer class="ft-inquiry-attention-modal-actions">
                            @if($inquiry->needs_attention)<button type="button" class="ft-inquiry-attention-clear" wire:click="clearInquiryAttention" wire:loading.attr="disabled" wire:target="clearInquiryAttention">Clear flag</button>@else<span></span>@endif
                            <div>
                                <button type="button" class="secondary" wire:click="closeInquiryAttentionReason">Cancel</button>
                                <button type="button" class="primary" wire:click="saveInquiryAttentionReason" wire:loading.attr="disabled" wire:target="saveInquiryAttentionReason">
                                    <span wire:loading.remove wire:target="saveInquiryAttentionReason">Request attention</span>
                                    <span wire:loading wire:target="saveInquiryAttentionReason">Saving...</span>
                                </button>
                            </div>
                        </footer>
                    </section>
                </div>
            @endif

            @if($showTaskAttentionModal && $taskAttentionTaskId)
                @php
                    $attentionTask = $inquiry->tasks->firstWhere('id', (int) $taskAttentionTaskId);
                @endphp
                <div class="ft-inquiry-attention-modal-backdrop" wire:key="inquiry-task-attention-modal" wire:click.self="closeTaskAttentionReason">
                    <section class="ft-inquiry-attention-modal" role="dialog" aria-modal="true" aria-labelledby="task-attention-modal-title">
                        <header class="ft-inquiry-attention-modal-head">
                            <div>
                                <h2 id="task-attention-modal-title">Why is attention required?</h2>
                                <p>{{ $attentionTask?->title ?: 'Inquiry task' }} · {{ $attentionTask?->status ?: 'Attention required' }}</p>
                            </div>
                            <button type="button" class="ft-inquiry-attention-modal-close" wire:click="closeTaskAttentionReason" aria-label="Close">×</button>
                        </header>
                        <div class="ft-inquiry-attention-modal-body ft-mention-host">
                            <label for="inquiry-task-attention-reason">Reason for flag *</label>
                            <textarea id="inquiry-task-attention-reason" class="ft-mention-input" wire:model="taskAttentionReason" rows="5" maxlength="2000" autocomplete="off" data-mention-users="{{ json_encode($inquiryMentionUsers->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" placeholder="Explain what is blocking the task or what needs attention. Type @ to mention a user..."></textarea>
                            @error('taskAttentionReason')<p class="ft-inquiry-attention-modal-error">{{ $message }}</p>@enderror
                            <p class="ft-inquiry-attention-modal-help">Saving this reason adds it to Inquiry comments, notifies Admin/Super Admin/the Inquiry creator, and supports <b>@mentions</b>.</p>
                        </div>
                        <footer class="ft-inquiry-attention-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskAttentionReason">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskAttentionReason" wire:loading.attr="disabled" wire:target="saveTaskAttentionReason">
                                <span wire:loading.remove wire:target="saveTaskAttentionReason">Save reason</span>
                                <span wire:loading wire:target="saveTaskAttentionReason">Saving...</span>
                            </button>
                        </footer>
                    </section>
                </div>
            @endif

        </section>
    @endif
</div>
