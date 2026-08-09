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
                    </div>
                    <div class="inquiry-list-body">
                        @forelse($inquiryRows as $row)
                            <article class="row" wire:key="inquiry-list-{{ $row['id'] }}">
                                <div class="cell ft-inquiry-list-identity" data-label="Inquiry">
                                    <a class="id" href="{{ route('inquiries.index', ['open' => $row['id']]) }}" wire:navigate>{{ $row['number'] }}</a>
                                    <span class="sub ft-inquiry-created-by" title="Created by {{ $row['createdBy'] }}">Created by {{ $row['createdBy'] }}</span>
                                    <span class="sub ft-inquiry-created-at">{{ $row['createdDate'] }} · {{ $row['createdTime'] }}</span>
                                </div>
                                <div class="cell ft-inquiry-list-title-cell" data-label="Title">
                                    <span class="title ft-inquiry-title-preview" title="{{ $row['title'] }}">{{ $row['titlePreview'] }}</span>
                                </div>
                                <div class="cell ft-inquiry-list-client-cell" data-label="Client / Item">
                                    <span class="title">{{ $row['client'] }}</span>
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
        <section class="view">
            <div class="formwrap">
                <div class="crumb">Inquiries / New Inquiry</div>
                <div class="formtop"><div><h1>Create Inquiry</h1><p>Capture the client request and create the inquiry taskflow before any order exists.</p></div></div>
                <div class="formcard">
                    <section class="section">
                        <div class="sectiontitle"><span>1</span><h2>Client request</h2></div>
                        <div class="inquiry-create-stack">
                            <div class="field">
                                <x-ui.remote-filter
                                    class="ft-create-remote-select inquiry-create-remote"
                                    label="Client *"
                                    property="clientId"
                                    type="clients"
                                    context="create-inquiry"
                                    action="setCreateSelector"
                                    :value="$clientId"
                                    placeholder="Select client"
                                    :selected-label="$selectedClientLabel ?: null"
                                    :initial-options="$clientFilterOptions"
                                    :clearable="false"
                                    wire:key="inquiry-create-client-selector"
                                />
                                @error('clientId')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                            <label class="field"><b>Client contact</b><input value="{{ $clientContact ?: 'No contact recorded' }}" readonly></label>
                            <label class="field"><b>Reference Number</b><input wire:model="referenceNumber" placeholder="Client / external reference number"></label>
                            <label class="field"><b>Title *</b><input wire:model="subject" placeholder="Enter inquiry title"></label>
                            <label class="field"><b>Description</b><textarea wire:model="requirementNotes" placeholder="Describe the client requirement"></textarea></label>
                        </div>
                    </section>

                    <section class="section">
                        <div class="sectiontitle"><span>2</span><h2>Initial attachments</h2></div>
                        <div
                            class="inquiry-dropzone"
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
                                <strong>Drag & drop client files here</strong>
                                <span>Artwork, specifications, request emails, reference images and supporting documents. Up to 20 MB per file.</span>
                            </div>
                            <button class="secondary inquiry-dropzone-button" type="button" x-on:click.stop="$refs.createAttachmentInput.click()">＋ Choose files</button>
                        </div>
                        <div class="inquiry-upload-state" wire:loading wire:target="createAttachments">Uploading files…</div>
                        @if(count($createAttachments))
                            <div class="inquiry-selected-files">
                                <div class="inquiry-selected-files-title">Selected files <span>{{ count($createAttachments) }}</span></div>
                                <div class="attach-list">
                                    @foreach($createAttachments as $upload)
                                        <span class="attach-chip">□ {{ $upload->getClientOriginalName() }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="section inquiry-workflow-create-section">
                        <div class="sectiontitle"><span>3</span><h2>Inquiry taskflow & tasks</h2></div>

                        <div class="inquiry-workflow-selector-wrap">
                            <x-ui.remote-filter
                                class="ft-create-remote-select inquiry-create-remote inquiry-workflow-selector"
                                label="Workflow *"
                                property="createWorkflowId"
                                type="workflows"
                                context="create-inquiry"
                                action="setCreateSelector"
                                :value="$createWorkflowId"
                                placeholder="Select workflow"
                                :selected-label="$selectedWorkflowLabel ?: null"
                                :initial-options="$workflowFilterOptions"
                                :clearable="false"
                                wire:key="inquiry-create-workflow-selector"
                            />
                            @if($createWorkflowId && $createWorkflowTaskCount === 0)
                                <small class="field-error">This Workflow has no active Task Pack tasks.</small>
                            @else
                                @error('createWorkflowId')<small class="field-error">{{ $message }}</small>@enderror
                            @endif
                        </div>

                        @if($createWorkflowId && $createWorkflowTaskCount > 0)
                            <div class="ft-workflow-summary inquiry-create-workflow-summary">
                                <span>ⓘ {{ $createWorkflowPhaseCount }} {{ \Illuminate\Support\Str::plural('phase', $createWorkflowPhaseCount) }} · {{ $createWorkflowTaskCount }} {{ \Illuminate\Support\Str::plural('task', $createWorkflowTaskCount) }} will be created</span>
                                @if(auth()->user()->canAccess('workflow.manage'))
                                    <a href="{{ route('workflow.setup') }}" wire:navigate>Preview workflow ↗</a>
                                @endif
                            </div>
                        @endif
                    </section>

                    <div class="formactions"><button class="secondary" type="button" wire:click="cancelCreate">Cancel</button><button class="secondary" type="button" wire:click="saveDraft">Save Draft</button><button class="primary" type="button" wire:click="createInquiry">Create</button></div>
                </div>
            </div>
        </section>

    @else
        @php
            $inquiry = $selectedInquiry;
            $totalTasks = (int) $inquiry->tasks_count;
            $completedTasks = (int) $inquiry->completed_tasks_count;
            $readyForDecision = !$inquiry->result && $totalTasks > 0 && $completedTasks === $totalTasks;
            $currentTask = $inquiry->currentTask;
            $detailStatus = $inquiry->result === 'converted' ? 'Converted' : ($inquiry->result === 'dead' ? 'Closed' : $inquiry->status);
            $resultLabel = $inquiry->result === 'converted' ? ($inquiry->convertedJob?->displayOrderNumber() ?: 'Converted') : ($inquiry->result === 'dead' ? 'Closed — '.$inquiry->dead_reason : 'Not decided');
        @endphp
        <section class="view inquiry-detail-view" x-data="{
            deadOpen:false,
            convertOpen:false,
            inquiryStatus:@js($detailStatus),
            statusTone(status){
                if (String(status).includes('Converted') || String(status).includes('Completed')) return 'green';
                if (String(status).includes('Dead') || String(status).includes('Closed')) return 'red';
                if (String(status).includes('Ready') || String(status).includes('On Hold')) return 'amber';
                if (String(status).includes('Waiting')) return 'purple';
                return 'blue';
            },
            async saveInquiryStatus(event){
                const previous=this.inquiryStatus;
                try{
                    const result=await $wire.updateInquiryStatus(event.currentTarget.value);
                    this.inquiryStatus=result?.status || event.currentTarget.value;
                }catch(error){
                    event.currentTarget.value=previous;
                }
            },
            async saveTaskStatus(event, taskId){
                const previous=this.inquiryStatus;
                try{
                    const result=await $wire.updateTaskStatusInline(taskId,event.currentTarget.value);
                    if(result?.inquiryStatus)this.inquiryStatus=result.inquiryStatus;
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
                        <span>/</span><span>{{ $inquiry->inquiry_number }}</span>
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
                </div>
            </div>

            <div class="tabs">
                <button class="tab {{ $detailTab === 'overview' ? 'active' : '' }}" type="button" wire:click="setDetailTab('overview')">Overview</button>
                <button class="tab {{ $detailTab === 'workflow' ? 'active' : '' }}" type="button" wire:click="setDetailTab('workflow')">Taskflow</button>
            </div>

            @if($detailTab !== 'overview')
                <div class="summary">
                    <div class="summarycard"><small>Current task</small><strong>{{ $currentTask?->title ?: ($readyForDecision ? 'Ready for Decision' : 'No active task') }}</strong></div>
                    <div class="summarycard"><small>Taskflow progress</small><strong>{{ $completedTasks }} of {{ $totalTasks }} completed</strong><div class="bar" style="margin-top:7px;width:100%"><i style="width:{{ $totalTasks ? ($completedTasks / $totalTasks * 100) : 0 }}%"></i></div></div>
                    <div class="summarycard"><small>Next due date</small><strong>{{ $currentTask?->due_date?->format('M j, Y') ?: '—' }}</strong></div>
                    <div class="summarycard"><small>Result</small><strong>{{ $resultLabel }}</strong></div>
                </div>
            @endif

            @if($detailTab === 'overview')
                <div class="tabpane ft-task-detail-page ft-exact-task-detail ft-inquiry-task-overview-exact">
                    <section class="ft-task-property-grid ft-friendly-task-properties ft-inquiry-overview-properties">
                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-'.$inquiry->id.'-status'), label: 'Inquiry status', value: @js($detailStatus), display: @js($detailStatus) })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Status</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="status-dot blue"></span><b class="ft-property-value" x-text="display">{{ $detailStatus }}</b>@if($canEditInquiry && !$inquiry->result)<button type="button" :disabled="status === 'saving'" title="Edit status" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryOverviewStatus?.showPicker ? $refs.inquiryOverviewStatus.showPicker() : $refs.inquiryOverviewStatus?.focus())">✎</button>@endif</div>
                            @if($canEditInquiry && !$inquiry->result)
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="inquiryOverviewStatus" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), async () => { const result=await $wire.updateInquiryStatus(draftValue); if(result?.status) inquiryStatus=result.status; return result; })">@unless($inquiryStatusOptions->contains($detailStatus))<option value="{{ $detailStatus }}" disabled>{{ $detailStatus }} (inactive)</option>@endunless @foreach($inquiryStatusOptions as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></div>
                                <x-ui.inline-save-state compact />
                            @endif
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

                        <div class="ft-task-property">
                            <small>Start date</small>
                            <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value">{{ $inquiry->created_at ? \App\Support\UserLocalTime::format($inquiry->created_at, 'M j, Y') : '—' }}</b></div>
                        </div>

                        <div class="ft-task-property ft-task-completed-property">
                            <small>Completed On</small>
                            <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value ft-completed-date-time"><span>{{ $inquiry->completed_at ? \App\Support\UserLocalTime::format($inquiry->completed_at, 'M j, Y') : '—' }}</span>@if($inquiry->completed_at)<span class="ft-completed-time">{{ \App\Support\UserLocalTime::format($inquiry->completed_at, 'g:i A') }}</span>@endif</b></div>
                        </div>
                    </section>

                    <section class="info-card ft-inquiry-information-legacy">
                        <header class="info-head"><h3>Inquiry information</h3><p>Client request and Inquiry details.</p></header>
                        <div class="info-body">
                            <div class="kv ft-inquiry-information-kv">
                                <div><small>Client</small><strong>{{ $inquiry->client?->name ?: '—' }}</strong></div>
                                <div>
                                    <small>Reference Number</small>
                                    @if($inquiry->reference_number)
                                        <div class="ft-inquiry-reference-copy">
                                            <strong class="ft-inquiry-reference-value" title="{{ $inquiry->reference_number }}">{{ $inquiry->reference_number }}</strong>
                                            <button
                                                type="button"
                                                class="ft-copy-id-btn"
                                                title="Copy Reference Number"
                                                aria-label="Copy reference number {{ $inquiry->reference_number }}"
                                                onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($inquiry->reference_number)); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)"
                                            >⧉</button>
                                        </div>
                                    @else
                                        <strong>—</strong>
                                    @endif
                                </div>
                                <div><small>Created by</small><strong>{{ $inquiry->creator?->name ?: 'System' }}</strong></div>
                                <div><small>Created at</small><strong>{{ $inquiry->created_at ? \App\Support\UserLocalTime::format($inquiry->created_at, 'M j, Y · g:i A') : '—' }}</strong></div>
                                <div><small>Order</small><strong>@if($inquiry->convertedJob)<a href="{{ route('jobs.index', ['open' => $inquiry->convertedJob->id]) }}" wire:navigate>{{ $inquiry->convertedJob->displayOrderNumber() }}</a>@else Not created yet @endif</strong></div>
                            </div>
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
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit description" aria-label="Edit Inquiry description" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.inquiryDescription.focus(); $refs.inquiryDescription.setSelectionRange($refs.inquiryDescription.value.length, $refs.inquiryDescription.value.length); })">✎</button>
                            @endif
                        </div>
                        <p x-show="!editing" x-text="display">{{ $inquiry->requirement_notes ?: 'No description has been provided for this Inquiry.' }}</p>
                        @if($canEditInquiry && !$inquiry->result)
                            <div x-cloak x-show="editing" class="ft-inquiry-description-editor">
                                <textarea x-ref="inquiryDescription" x-model="draftValue" maxlength="10000" placeholder="Add the client requirement or Inquiry description..."
                                    x-on:keydown.escape.prevent="cancelEdit()"
                                    x-on:keydown.ctrl.enter.prevent="$event.target.blur()"
                                    x-on:keydown.meta.enter.prevent="$event.target.blur()"
                                    x-on:blur="if (editing) { const clean=draftValue.trim(); commit(clean, clean || 'No description has been provided for this Inquiry.', () => $wire.updateInquiryField('requirement_notes', clean)); }"></textarea>
                                <div class="ft-inquiry-description-editor-foot"><span>Ctrl/⌘ + Enter to save</span><x-ui.inline-save-state compact /></div>
                            </div>
                        @endif
                    </section>

                    @php
                        $overviewTasks = $inquiry->tasks;
                        $overviewTaskTotal = $overviewTasks->count();
                        $overviewTaskCompleted = $overviewTasks->whereNotNull('completed_at')->count();
                        $overviewTaskProgress = $overviewTaskTotal ? round(($overviewTaskCompleted / $overviewTaskTotal) * 100) : 0;
                        $overviewActiveTaskId = $overviewTasks->first(fn ($task) => ! $task->completed_at)?->id;
                    @endphp
                    <section class="ft-detail-card ft-inquiry-overview-task-card" aria-label="Inquiry tasks">
                        <div class="ft-inquiry-overview-task-head">
                            <div>
                                <h2>All tasks</h2>
                                <p>{{ $overviewTaskTotal }} task{{ $overviewTaskTotal === 1 ? '' : 's' }} in this Inquiry</p>
                            </div>
                            <span class="ft-inquiry-view-only-badge">View only</span>
                        </div>
                        <div class="ft-inquiry-overview-task-note">
                            <span>◉ All {{ $overviewTaskTotal }} Taskflow task{{ $overviewTaskTotal === 1 ? '' : 's' }} are shown</span>
                            <span>Current saved status</span>
                        </div>
                        <div class="ft-inquiry-overview-task-group-head">
                            <b>1</b>
                            <strong>Taskflow</strong>
                            <small>{{ $overviewTaskCompleted }} of {{ $overviewTaskTotal }} complete</small>
                            <em style="--inquiry-task-progress:{{ $overviewTaskProgress }}%"></em>
                        </div>
                        <div class="ft-inquiry-overview-task-columns" aria-hidden="true">
                            <span>Task</span>
                            <span>Assignee</span>
                            <span>Due date</span>
                            <span>Started at</span>
                            <span>Status</span>
                        </div>
                        <div class="ft-inquiry-overview-task-rows">
                            @forelse($overviewTasks as $overviewTask)
                                @php
                                    $overviewTaskStatus = strtolower((string) $overviewTask->status);
                                    $overviewTaskTone = match (true) {
                                        $overviewTask->completed_at !== null || str_contains($overviewTaskStatus, 'complete') => 'green',
                                        str_contains($overviewTaskStatus, 'revision') || str_contains($overviewTaskStatus, 'blocked') => 'red',
                                        str_contains($overviewTaskStatus, 'waiting') || str_contains($overviewTaskStatus, 'hold') => 'amber',
                                        default => 'blue',
                                    };
                                @endphp
                                <div class="ft-inquiry-overview-task-row {{ (int) $overviewActiveTaskId === (int) $overviewTask->id ? 'is-current' : '' }}" wire:key="inquiry-overview-task-{{ $overviewTask->id }}">
                                    <div class="ft-inquiry-overview-task-name">
                                        <span>{{ $loop->iteration }}</span>
                                        <strong>{{ $overviewTask->title }}</strong>
                                    </div>
                                    <div class="ft-inquiry-overview-task-assignee">
                                        <x-ui.avatar :user="$overviewTask->assignee" :name="$overviewTask->assignee?->name ?? 'Unassigned'" :size="26"/>
                                        <span>{{ $overviewTask->assignee?->name ?? 'Unassigned' }}</span>
                                    </div>
                                    <span class="ft-inquiry-overview-task-date">{{ $overviewTask->due_date?->format('M j, Y') ?? '—' }}</span>
                                    <span class="ft-inquiry-overview-task-started">
                                        @if($overviewTask->started_at)
                                            <b>{{ \App\Support\UserLocalTime::format($overviewTask->started_at, 'M j, Y') }}</b>
                                            <small>{{ \App\Support\UserLocalTime::format($overviewTask->started_at, 'g:i A') }}</small>
                                        @else
                                            <b>—</b>
                                            <small>Not started</small>
                                        @endif
                                    </span>
                                    <span class="pill {{ $overviewTaskTone }} ft-inquiry-overview-task-status">{{ $overviewTask->status }}</span>
                                </div>
                            @empty
                                <div class="ft-inquiry-overview-task-empty">No Taskflow tasks configured for this Inquiry.</div>
                            @endforelse
                        </div>
                    </section>

                    @include('livewire.inquiries._attachments')
                    @include('livewire.inquiries._activity')
                </div>
            @elseif($detailTab === 'workflow')
                @php
                    $activeTaskId = $inquiry->tasks->first(fn ($task) => !$task->completed_at)?->id;
                @endphp
                <div id="tab-workflow" class="tabpane ft-inquiry-workflow-pane">
                    <section class="panel">
                        <header class="panelhead"><div><h2>Inquiry Taskflow</h2><p>Open tasks can be prepared at any time; completion still follows the taskflow sequence.</p></div><div class="task-control-row"><span class="task-count-pill">{{ $totalTasks }} Tasks</span><span class="manage-badge">Sequential taskflow</span>@if($canAddInquiryTask)<button class="primary" type="button" wire:click="openAddTaskForm" style="min-height:34px">＋ Add Task</button>@endif</div></header>
                        <div class="ft-inquiry-task-grid-head" aria-hidden="true">
                            <span>#</span><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Files</span><span>Action</span>
                        </div>
                        <div class="ft-inquiry-task-list">
                            @forelse($inquiry->tasks as $i => $task)
                                @php
                                    $state = $task->completed_at ? 'done' : ((int)$task->id === (int)$activeTaskId ? 'active' : 'wait');
                                    $fileOk = !$task->requires_submission || (int)$task->documents_count > 0;
                                    $canEditThisTask = $state !== 'done' && !$inquiry->result && ($canEditInquiry || ((int)$task->assignee_id === (int)auth()->id() && auth()->user()->canModule('inquiries', 'view')));
                                    $taskDeepLinked = (int)($selectedTaskId ?? 0) === (int)$task->id;
                                @endphp
                                <div class="ft-inquiry-task-row {{ $state }} {{ $taskDeepLinked ? 'is-highlighted' : '' }}" wire:key="inquiry-task-row-{{ $task->id }}">
                                    <div class="ft-inquiry-task-step"><span>{{ $state === 'done' ? '✓' : $i + 1 }}</span></div>
                                    <div class="ft-inquiry-task-copy">
                                        <strong>{{ $task->title }}</strong>
                                        <p>{{ $task->description ?: 'No instructions added.' }}</p>
                                        @if($task->requires_submission)<span class="reqfile {{ $fileOk ? 'ok' : '' }}">{{ $fileOk ? '✓ File submitted' : '□ Required file' }}</span>@endif
                                    </div>

                                    <div class="ft-inquiry-assignee-inline ft-inline-edit-shell"
                                        x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-task-'.$task->id.'-assignee'), label: 'task assignee', value: @js($task->assignee_id ?? ''), display: @js($task->assignee?->name ?? 'Unassigned'), avatarUrl: @js($task->assignee?->profileImageUrl() ?? '') })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        x-on:click.outside="if (editing) cancelEdit()"
                                        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeInline({{ $task->id }}, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })">
                                        <div class="ft-inquiry-inline-display-row">
                                            <div x-show="!editing" class="ft-inquiry-assignee-display">
                                                <span class="ft-inline-avatar-slot"><x-ui.inline-live-avatar :size="28" /></span>
                                                <span class="ft-inquiry-assignee-name" x-text="display">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                            </div>
                                            @if($canEditThisTask)<button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>@endif
                                        </div>
                                        @if($canEditThisTask)
                                            <div x-cloak x-show="editing" class="ft-inquiry-assignee-picker">
                                                <x-ui.inline-remote-user :value="$task->assignee_id ?? ''" :selected-label="$task->assignee?->name ?? 'Unassigned'" trigger-class="ft-task-inline-input" variant="compact" :menu-width="260" />
                                            </div>
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>

                                    <div class="ft-inquiry-task-date ft-inline-edit-shell"
                                        x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-task-'.$task->id.'-due-date'), label: 'task due date', value: @js($task->due_date?->format('Y-m-d') ?? ''), display: @js($task->due_date?->format('M j, Y') ?? 'Set due date') })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                        <div class="ft-inquiry-inline-display-row" x-show="!editing">
                                            <span class="ft-inquiry-inline-value" x-text="display">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
                                            @if($canEditThisTask)<button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryDue.showPicker ? $refs.inquiryDue.showPicker() : $refs.inquiryDue.focus())">✎</button>@endif
                                        </div>
                                        @if($canEditThisTask)
                                            <input x-ref="inquiryDue" x-cloak x-show="editing" x-model="draftValue" class="ft-inquiry-inline-input" type="date"
                                                x-on:keydown.escape.prevent="cancelEdit()"
                                                x-on:blur="if (editing) cancelEdit()"
                                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueInline({{ $task->id }}, draftValue))">
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>
                                    <div class="task-status-cell">
                                        @if($state === 'done')
                                            <span class="ft-inquiry-status-pill done">Completed</span>
                                        @else
                                            <div class="ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-task-'.$task->id.'-status'), label: 'task status', value: @js($task->status), display: @js($task->status) })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                                <div class="ft-inquiry-inline-display-row" x-show="!editing">
                                                    <span class="pill ft-inquiry-inline-status" x-bind:class="statusTone(display)" x-text="display">{{ $task->status }}</span>
                                                    @if($canEditThisTask)<button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit status" aria-label="Edit task status" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryStatus.focus())">✎</button>@endif
                                                </div>
                                                @if($canEditThisTask)
                                                    <select x-ref="inquiryStatus" x-cloak x-show="editing" class="ft-inquiry-inline-input" x-model="draftValue"
                                                        x-on:keydown.escape.prevent="cancelEdit()"
                                                        x-on:blur="if (editing) cancelEdit()"
                                                        x-on:change="const next=$event.target.value; commit(next, selectedLabel($event), async () => { const result=await $wire.updateTaskStatusInline({{ $task->id }}, draftValue); if(result?.inquiryStatus) inquiryStatus=result.inquiryStatus; return result; })">
                                                        @if($state === 'wait')<option value="Waiting">Waiting</option>@endif
                                                        @foreach(\App\Services\InquiryService::WORKING_STATUSES as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach
                                                    </select>
                                                    <x-ui.inline-save-state compact />
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ft-inquiry-task-files">
                                        @if($canEditThisTask)
                                            <label class="ft-inquiry-attach-button file-label">＋ Attach<input class="file-input" type="file" wire:model="taskQuickUploads.{{ $task->id }}"></label>
                                        @endif
                                        <span><b>{{ $task->documents_count }}</b> file{{ $task->documents_count === 1 ? '' : 's' }}</span>
                                    </div>
                                    <div class="ft-inquiry-task-action">
                                        @if($state === 'done')
                                            <span class="ft-inquiry-complete-state">✓ Completed</span>
                                        @elseif($state === 'wait')
                                            <button class="ft-inquiry-action-button" type="button" disabled>Waiting</button>
                                        @else
                                            <button class="ft-inquiry-action-button primary-action" type="button" wire:click="completeTaskInline({{ $task->id }})" wire:loading.attr="disabled" wire:target="completeTaskInline({{ $task->id }})" @disabled(!$canEditThisTask || !$fileOk)>{{ !$fileOk ? 'File required' : 'Complete' }}</button>
                                        @endif
                                    </div>
                                </div>
                                @if($task->documents->isNotEmpty())
                                    <div class="ft-inquiry-task-document-list" wire:key="inquiry-task-documents-{{ $task->id }}">
                                        @foreach($task->documents as $taskDocument)
                                            <div class="ft-inquiry-task-document-row" wire:key="inquiry-task-document-{{ $taskDocument->id }}">
                                                <span class="ft-inquiry-task-file-type">{{ strtoupper(pathinfo($taskDocument->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                                                <div class="ft-inquiry-task-file-copy">
                                                    <b title="{{ $taskDocument->name }}">{{ $taskDocument->name }}</b>
                                                    <small>{{ $taskDocument->created_at ? \App\Support\UserLocalTime::format($taskDocument->created_at, 'M j, Y, g:i A') : '—' }}</small>
                                                </div>
                                                <div class="ft-inquiry-task-file-actions">
                                                    <a href="{{ route('inquiries.documents.open', $taskDocument) }}" target="_blank" rel="noopener">Open</a>
                                                    @if($canEditThisTask)
                                                        <button type="button" class="ft-inquiry-task-file-remove" wire:click="deleteTaskDocument({{ $task->id }}, {{ $taskDocument->id }})" wire:loading.attr="disabled" wire:target="deleteTaskDocument({{ $task->id }}, {{ $taskDocument->id }})" title="Remove attachment" aria-label="Remove {{ $taskDocument->name }}">×</button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @empty
                                <div class="ft-inquiry-empty-workflow">No taskflow tasks configured.</div>
                            @endforelse
                        </div>

                        @if($showAddTaskForm && $canAddInquiryTask)
                            <div class="ft-inquiry-add-task" wire:key="inquiry-add-task-form">
                                <div class="ft-inquiry-add-task-head">
                                    <div><strong>Add taskflow task</strong><span>The task is appended after the existing taskflow. If the taskflow was already complete, this new task becomes active.</span></div>
                                    <button class="ft-inquiry-add-task-close" type="button" wire:click="cancelAddTask" aria-label="Close add task form">×</button>
                                </div>
                                <div class="ft-inquiry-add-task-grid">
                                    <label class="ft-inquiry-add-task-field ft-inquiry-add-task-field-wide"><span>Task name *</span><input type="text" wire:model="newTaskName" placeholder="Task name"></label>
                                    <label class="ft-inquiry-add-task-field"><span>Assignee</span><select wire:model="newTaskAssigneeId"><option value="">Unassigned</option>@foreach($userOptions as $userOption)<option value="{{ $userOption['id'] }}">{{ $userOption['name'] }}</option>@endforeach</select></label>
                                    <label class="ft-inquiry-add-task-field"><span>Due date</span><input type="date" wire:model="newTaskDueDate" onclick="this.showPicker && this.showPicker()"></label>
                                    <label class="ft-inquiry-add-task-field ft-inquiry-add-task-field-wide"><span>Instructions</span><textarea wire:model="newTaskDescription" placeholder="Describe what must be completed for this task."></textarea></label>
                                    <label class="ft-inquiry-add-task-field"><span>Submission</span><select wire:model.live.boolean="newTaskRequiresSubmission"><option value="0">No required file</option><option value="1">Required file</option></select></label>
                                    @if($newTaskRequiresSubmission)<label class="ft-inquiry-add-task-field"><span>Required file</span><input type="text" wire:model="newTaskSubmissionLabel" placeholder="Submission name"></label>@endif
                                </div>
                                @error('newTaskName')<div class="ft-inquiry-add-task-error">{{ $message }}</div>@enderror
                                <div class="ft-inquiry-add-task-actions"><button class="secondary" type="button" wire:click="cancelAddTask">Cancel</button><button class="primary" type="button" wire:click="addInquiryTask" wire:loading.attr="disabled" wire:target="addInquiryTask">Add Task</button></div>
                            </div>
                        @endif
                    </section>

                    <section class="decision {{ $readyForDecision ? 'ready-decision' : '' }}">
                        <div class="decisiontop"><div><h3>Final Inquiry Decision</h3><p>{{ $readyForDecision ? 'All configured Inquiry tasks and required submissions are complete. Record the client outcome now.' : 'Complete every task currently configured in this Inquiry taskflow. Then choose whether the Inquiry becomes an Order or is closed.' }}</p></div><span class="pill {{ $readyForDecision ? 'amber' : ($inquiry->result === 'converted' ? 'green' : ($inquiry->result === 'dead' ? 'red' : 'amber')) }}">{{ $readyForDecision ? 'Decision Required' : ($inquiry->result ? 'Completed' : 'Locked') }}</span></div>
                        <div class="decisionactions"><button class="primary" type="button" x-on:click="convertOpen=true" @disabled(!$readyForDecision || !$canCreateOrder)>Convert to Order</button><button class="danger" type="button" x-on:click="deadOpen=true" @disabled(!$readyForDecision)>Close Inquiry</button></div>
                        <div class="deadreason" x-bind:class="deadOpen ? 'show' : ''"><select wire:model="deadReason"><option>Price too high</option><option>Client cancelled</option><option>Lost to competitor</option><option>MOQ issue</option><option>Delivery issue</option><option>No response</option><option>Other</option></select><input wire:model="deadNote" placeholder="Optional note"><button class="danger" type="button" wire:click="markDead" x-on:click="deadOpen=false">Confirm Close</button></div>
                        @if($inquiry->result === 'converted')<div class="successbox show"><strong>Converted successfully</strong><span>Order <b>{{ $inquiry->convertedJob?->displayOrderNumber() }}</b> was created from this inquiry. The Order starts its own workflow and keeps this inquiry as the source reference.</span></div>@endif
                        @if($inquiry->result === 'dead')<div class="deadbox show"><strong>Inquiry closed</strong><span>Reason: {{ $inquiry->dead_reason }}{{ $inquiry->dead_note ? '. '.$inquiry->dead_note : '' }}</span></div>@endif
                    </section>
                </div>
            @endif

            <div class="modal" x-bind:class="convertOpen ? 'show' : ''" x-on:click.self="convertOpen=false"><div class="modalcard"><div class="modalhead"><h3>Convert inquiry to order?</h3><button class="close" type="button" x-on:click="convertOpen=false">×</button></div><div class="modalbody"><div class="pair"><div class="pairbox"><small>Inquiry</small><strong>{{ $inquiry->inquiry_number }}</strong></div><div class="arrow">→</div><div class="pairbox"><small>New Order</small><strong>Auto-generated on create</strong></div></div><div class="modalnote">Client, inquiry reference, requirement details and delivery information will carry into the new Order. Inquiry task history remains on the Inquiry.</div></div><div class="modalactions"><button class="secondary" type="button" x-on:click="convertOpen=false">Cancel</button><button class="primary" type="button" wire:click="convertToOrder" x-on:click="convertOpen=false">Create Order</button></div></div></div>
        </section>
    @endif
</div>
