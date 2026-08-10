@props([
    'task',
    'mentionUsers'=>collect(),
    'taskProgress',
    'taskStatuses'=>collect(),
    'priorities'=>collect(),
    'taskFlags'=>collect(),
    'displayTimezone'=>'UTC',
    'availableDocuments'=>collect(),
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'taskDocumentUploads'=>[],
    'showTaskDocumentPicker'=>false,
    'editMode'=>false,
])
@php
    $job = $task->job;
    $done = $task->checklistItems->where('is_completed',true)->count();
    $total = $task->checklistItems->count();
    $checkTotal = max(1, $total);
    $taskDocumentName = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name;
    $accessControl = app(\App\Services\AccessControlService::class);
    $mayEditTask = $accessControl->canEditVisibleTask(auth()->user(), $task);
    $canEditTask = $editMode && $mayEditTask;
    $canAssignTask = $editMode && $accessControl->canAssignTask(auth()->user(), $task);
    $canCheck = $canEditTask;
    $canUploadDocument = $editMode && $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $editMode && $accessControl->can(auth()->user(), 'documents', 'link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $canDeleteDocument = $editMode && $accessControl->can(auth()->user(), 'documents', 'delete');
    $effectiveDescription = $task->description ?: $task->setupTemplate?->description;
    $effectiveStartDate = $task->start_date ?: \App\Support\UserLocalTime::localize($task->created_at);
    $completedOn = $task->completed_at?->copy()->timezone($displayTimezone);
    $currentTaskFlag = $task->needs_attention ? (app(\App\Services\TaskFlagService::class)->labelForTask($task) ?: 'Management attention') : '';
    $taskFlagNames = $taskFlags->pluck('name')->map(fn($name)=>trim((string)$name))->filter()->values();
    $commentEvents = $task->comments->map(fn($comment)=>(object)[
        'id'=>(int)$comment->id,'kind'=>'comment','event'=>'task.comment','user'=>$comment->user,'body'=>$comment->body,'created_at'=>$comment->created_at,
    ]);
    $activityEvents = $task->activities->reject(fn($activity)=>$activity->event==='task.comment')->map(fn($activity)=>(object)[
        'id'=>(int)$activity->id,'kind'=>'activity','event'=>$activity->event,'user'=>$activity->user,'body'=>$activity->description,'created_at'=>$activity->created_at,
    ]);
    $timeline = $commentEvents->concat($activityEvents)->sortByDesc(fn($entry) => sprintf('%020d-%020d', $entry->created_at?->getTimestamp() ?? 0, $entry->id ?? 0))->values();
    if($activityTab==='comments') $timeline = $timeline->where('kind','comment')->values();
    if($activityTab==='history') $timeline = $timeline->where('kind','activity')->values();
    $activityPerPage = 30;
    $timelineTotal = $timeline->count();
    $timelinePages = max(1, (int) ceil($timelineTotal / $activityPerPage));
    $timelineCurrentPage = min(max(1, (int) $activityPage), $timelinePages);
    $timeline = $timeline->forPage($timelineCurrentPage, $activityPerPage)->values();
@endphp
<div {{ $attributes->class('ft-task-detail-page ft-exact-task-detail') }}>
    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
    <div class="ft-detail-toolbar task-toolbar ft-exact-task-header">
        <div class="ft-task-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <a href="{{ route('my-work') }}" wire:navigate>My Work</a>
                @if($job)
                    <span>/</span><a href="{{ route('jobs.index', ['open'=>$job->id]) }}" wire:navigate>{{ $job->displayOrderNumber() }}</a>
                @endif
                <span>/</span><span>{{ $task->task_number }}</span>
            </div>
            <div class="ft-task-title-line">
                <h1
                    class="ft-editable-task-title ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-title'), label: 'task title', value: @js($task->title), display: @js($task->title) })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span x-show="!editing" x-text="display">{{ $task->title }}</span>
                    @if($canEditTask)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit task title" title="Edit task name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskTitle.focus())">✎</button>
                        <input x-ref="taskTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:keydown.enter.prevent="$event.target.blur()"
                            x-on:blur="if (editing) commit(draftValue.trim(), draftValue.trim(), () => $wire.updateSelectedTaskField('title', draftValue.trim()))">
                        <x-ui.inline-save-state />
                    @endif
                </h1>
                @if($task->phase?->name)<span class="ft-task-title-phase">· {{ $task->phase->name }}</span>@endif
            </div>
        </div>
        <div class="ft-detail-actions">@if($canEditTask)<button class="ft-new-job-btn ft-mark-complete" wire:click="markTaskComplete" @disabled($task->status==='Completed')>{{ $task->status==='Completed' ? 'Completed' : 'Mark complete' }}</button>@endif<button class="ft-close-page" wire:click="closeTask" type="button" title="Back to order details" aria-label="Back to order details">×</button></div>
    </div>
    @error('taskCompletion')<div class="validation-error ft-task-completion-error">{{ $message }}</div>@enderror

    <div class="ft-task-detail-layout">
        <main>
            <section class="ft-task-property-grid ft-friendly-task-properties">
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-assignee'), label: 'task assignee', value: @js($task->assignee_id ?? ''), display: @js($task->assignee?->name ?? 'Unassigned'), avatarUrl: @js($task->assignee?->profileImageUrl() ?? '') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateSelectedTaskField('assignee_id', draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
                >
                    <small>Assignee</small>
                    <div x-show="!editing" class="ft-task-property-display ft-inline-person-live">
                        <x-ui.inline-live-avatar :size="26" />
                        <b class="ft-property-value" x-text="display">{{ $task->assignee?->name ?? 'Unassigned' }}</b>
                        @if($canAssignTask)<button type="button" :disabled="status === 'saving'" title="Edit assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>@endif
                    </div>
                    @if($canAssignTask)
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor ft-task-property-assignee-editor">
                            <x-ui.inline-remote-user
                                :value="$task->assignee_id ?? ''"
                                :selected-label="$task->assignee?->name ?? 'Unassigned'"
                                trigger-class="ft-task-property-inline-input"
                                variant="compact"
                                :menu-width="300"
                            />
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-status'), label: 'task status', value: @js($task->status), display: @js($task->status) })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Status</small>
                    <div x-show="!editing" class="ft-task-property-display"><span class="status-dot blue"></span><b class="ft-property-value" x-text="display">{{ $task->status }}</b>@if($canEditTask)<button type="button" :disabled="status === 'saving'" title="Edit status" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.status?.showPicker ? $refs.status.showPicker() : $refs.status?.focus())">✎</button>@endif</div>
                    @if($canEditTask)
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="status" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateSelectedTaskField('status', draftValue))">@foreach($taskStatuses as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></div>
                        <x-ui.inline-save-state compact />
                    @endif
                </div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-priority'), label: 'task priority', value: @js($task->priority), display: @js($task->priority) })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Priority</small>
                    <div x-show="!editing" class="ft-task-property-display"><span class="status-dot amber"></span><b class="ft-property-value" x-text="display">{{ $task->priority }}</b>@if($canEditTask)<button type="button" :disabled="status === 'saving'" title="Edit priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.priority?.showPicker ? $refs.priority.showPicker() : $refs.priority?.focus())">✎</button>@endif</div>
                    @if($canEditTask)
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="priority" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateSelectedTaskField('priority', draftValue))">@foreach($priorities as $priority)<option value="{{ $priority->name }}">{{ $priority->name }}</option>@endforeach</select></div>
                        <x-ui.inline-save-state compact />
                    @endif
                </div>
                <div class="ft-task-property"><small>Phase</small><div class="ft-task-property-display"><b class="ft-property-value">{{ $task->phase?->name ?? '—' }}</b></div></div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-start-date'), label: 'task start date', value: @js($effectiveStartDate?->format('Y-m-d') ?? ''), display: @js($effectiveStartDate?->format('M j, Y') ?? 'Not set') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Start date</small>
                    <div x-show="!editing" class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value" x-text="display">{{ $effectiveStartDate?->format('M j, Y') ?? 'Not set' }}</b>@if($canEditTask)<button type="button" :disabled="status === 'saving'" title="Edit start date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.start?.showPicker ? $refs.start.showPicker() : $refs.start?.focus())">✎</button>@endif</div>
                    @if($canEditTask)
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><input x-ref="start" x-model="draftValue" class="ft-task-property-inline-input" type="date" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateSelectedTaskField('start_date', draftValue))"></div>
                        <x-ui.inline-save-state compact />
                    @endif
                </div>
                <div
                    class="ft-task-property ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-due-date'), label: 'task due date', value: @js($task->due_date?->format('Y-m-d') ?? ''), display: @js($task->due_date?->format('M j, Y') ?? 'Not set') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                >
                    <small>Due date</small>
                    <div x-show="!editing" class="ft-task-property-display {{ ($task->due_date && \App\Support\UserLocalTime::isDatePast($task->due_date)) && !$task->completed_at ? 'danger-text' : '' }}"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value" x-text="display">{{ $task->due_date?->format('M j, Y') ?? 'Not set' }}</b>@if($canEditTask)<button type="button" :disabled="status === 'saving'" title="Edit due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.due?.showPicker ? $refs.due.showPicker() : $refs.due?.focus())">✎</button>@endif</div>
                    @if($canEditTask)
                        <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><input x-ref="due" x-model="draftValue" class="ft-task-property-inline-input" type="date" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateSelectedTaskField('due_date', draftValue))"></div>
                        <x-ui.inline-save-state compact />
                    @endif
                </div>
                <div class="ft-task-property ft-task-completed-property"
                    x-data="{ completedDate: @js($completedOn?->format('M j, Y') ?? '—'), completedTime: @js($completedOn?->format('g:i A') ?? '') }"
                    x-on:task-completion-updated.window="completedDate = $event.detail.completedDate || '—'; completedTime = $event.detail.completedTime || ''">
                    <small>Completed On</small>
                    <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value ft-completed-date-time"><span x-text="completedDate">{{ $completedOn?->format('M j, Y') ?? '—' }}</span><span class="ft-completed-time" x-show="completedTime" x-text="completedTime">{{ $completedOn?->format('g:i A') ?? '' }}</span></b></div>
                </div>
            </section>

            <section
                class="ft-detail-card ft-description-card ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-description'), label: 'task description', value: @js($effectiveDescription ?? ''), display: @js($effectiveDescription ?: 'No description has been provided for this task.') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                @if($canEditTask)<button x-show="!editing" :disabled="status === 'saving'" class="ft-card-edit" type="button" title="Edit description" x-on:click="beginRichTextEdit($refs.description)">✎</button>@endif
                <h2>Description</h2>
                <div x-show="!editing" class="ft-rich-text-content">
                    <div x-show="!hasRichTextOverride">@if($effectiveDescription)<x-ui.mention-text :text="$effectiveDescription" />@else No description has been provided for this task. @endif</div>
                    <div x-cloak x-show="hasRichTextOverride" x-html="richTextOverrideHtml"></div>
                </div>
                @if($canEditTask)
                    <div x-cloak x-show="editing" class="ft-inline-description-editor"><textarea x-ref="description" class="ft-mention-input" data-rich-text rows="4" autocomplete="off" data-mention-users='@json($mentionUsers->values())'>{{ $effectiveDescription ?? '' }}</textarea><div class="ft-inline-description-actions"><button type="button" class="ft-outline-btn" x-on:click="cancelRichTextEdit($refs.description)">Cancel</button><button type="button" class="ft-new-job-btn" data-rich-text-submit :disabled="status === 'saving'" x-on:click="saveRichText($refs.description, 'No description has been provided for this task.', (clean) => $wire.updateSelectedTaskField('description', clean))">Save</button></div></div>
                    <x-ui.inline-save-state />
                @endif
            </section>

            <section class="ft-detail-card ft-checklist-card" x-data="{adding:false}">
                <div class="ft-card-row-head"><div class="ft-check-title"><h2>Checklist</h2><span>{{ $done }} of {{ $total }} complete</span><div class="ft-small-progress"><span style="width:{{ $done / $checkTotal * 100 }}%"></span></div></div>@if($canEditTask)<button class="ft-link-blue" type="button" x-on:click="adding=!adding">＋ Add item</button>@endif</div>
                @if($canEditTask)<div class="ft-checklist-add-row" x-show="adding"><input wire:model="newChecklistItem" wire:keydown.enter="addTaskChecklistItem" placeholder="Checklist item"><button type="button" class="ft-new-job-btn" wire:click="addTaskChecklistItem" x-on:click="adding=false">Add</button><button type="button" class="ft-outline-btn" x-on:click="adding=false">Cancel</button></div>@error('newChecklistItem')<div class="validation-error ft-checklist-validation">{{ $message }}</div>@enderror@endif
                @forelse($task->checklistItems->sortBy('sort_order') as $item)
                    <div class="ft-checklist-row">
                        <input type="checkbox" @checked($item->is_completed) @disabled(!$canCheck) wire:change="toggleTaskChecklistItem({{ $item->id }}, $event.target.checked)">
                        <span class="{{ $item->is_completed ? 'completed' : '' }}">{{ $item->label }}</span>
                        @if($canEditTask)<button type="button" class="ft-checklist-delete" title="Delete checklist item" wire:click="deleteTaskChecklistItem({{ $item->id }})" wire:confirm="Delete this checklist item?">×</button>@else<span></span>@endif
                    </div>
                @empty<div class="empty-state">No checklist items configured.</div>@endforelse
                @unless($canCheck)<p class="ft-checklist-permission-note">Only the assigned person can check or uncheck checklist items.</p>@endunless
            </section>

            <section class="ft-detail-card ft-attachment-card">
                <h2>Attachments <span>{{ $task->documents->count() }}</span></h2>
                <div class="ft-upload-zone compact ft-task-upload-zone">
                    @if($canUploadDocument)
                        <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone for="taskDocumentUpload-{{ $task->id }}">
                            <input id="taskDocumentUpload-{{ $task->id }}" type="file" wire:model="taskDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                            <span class="ft-paperclip">⌕</span>
                            <div>Drop files here or <strong>browse</strong><small data-drop-status>{{ $taskDocumentName ? 'Required document: '.$taskDocumentName.' · ' : '' }}PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                        </label>
                    @else
                        <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to task attachments.</small></div></div>
                    @endif
                    @if($canLinkDocument)<button class="ft-outline-btn ft-task-choose-document" type="button" wire:click="toggleTaskDocumentPicker">Choose from Documents</button>@endif
                </div>
                @if(count($taskDocumentUploads ?? []))
                    <div class="ft-upload-ready-row"><span>{{ count($taskDocumentUploads ?? []) }} file{{ count($taskDocumentUploads ?? [])===1?'':'s' }} ready</span><button class="ft-new-job-btn" type="button" wire:click="uploadSelectedTaskDocuments">Upload &amp; link</button></div>
                @endif
                @error('taskDocumentUploads')<div class="validation-error">{{ $message }}</div>@enderror
                @error('taskDocumentUploads.*')<div class="validation-error">{{ $message }}</div>@enderror
                @if($canLinkDocument && $showTaskDocumentPicker)
                    <div class="ft-existing-document-picker ft-task-document-picker">
                        <select wire:model="taskExistingDocumentId"><option value="">Select a stored document</option>@foreach($availableDocuments as $stored)<option value="{{ $stored->id }}">{{ $stored->name }} · {{ $stored->job?->displayOrderNumber() ?? 'Archive' }}</option>@endforeach</select>
                        <button class="ft-new-job-btn" type="button" wire:click="attachExistingToSelectedTask">Link document</button>
                        <button class="ft-outline-btn" type="button" wire:click="toggleTaskDocumentPicker">Cancel</button>
                    </div>
                    @error('taskExistingDocumentId')<div class="validation-error">{{ $message }}</div>@enderror
                @endif
                @foreach($task->documents->sortByDesc('created_at') as $doc)<div class="ft-attachment-row"><span class="ft-file-type">{{ strtoupper(pathinfo($doc->name,PATHINFO_EXTENSION) ?: 'FILE') }}</span><b>{{ $doc->name }}</b><small>{{ \App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A') }}</small><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>@if($canDeleteDocument)<button type="button" class="ft-doc-delete-button" wire:click="deleteSelectedTaskDocument({{ $doc->id }})" wire:confirm="Delete this document link?">×</button>@endif</div>@endforeach
                <p class="ft-upload-note">Every file uploaded here is linked to this task and appears in Order Documents. A required document is counted only when this Task Pack task defines that document type.</p>
            </section>

            <section class="ft-detail-card ft-task-activity-card ft-friendly-activity">
                <div class="ft-activity-head">
                    <div><h2>Activity</h2><p>Comments and task changes, with who changed what and when.</p></div>
                    <div class="ft-activity-tabs"><button type="button" class="{{ $activityTab==='all'?'active':'' }}" wire:click="setTaskActivityTab('all')">All</button><button type="button" class="{{ $activityTab==='comments'?'active':'' }}" wire:click="setTaskActivityTab('comments')">Comments</button><button type="button" class="{{ $activityTab==='history'?'active':'' }}" wire:click="setTaskActivityTab('history')">History</button></div>
                </div>
                @if($canEditTask)
                    <div class="ft-comment-composer ft-friendly-composer ft-rich-comment-composer"><x-ui.avatar :user="auth()->user()" :name="auth()->user()->name" :size="32"/><textarea class="ft-mention-input" data-rich-text data-rich-text-compact wire:model="taskComment" rows="2" autocomplete="off" data-mention-users='@json($mentionUsers->values())' placeholder="Write a comment. Type @ to mention someone or paste a screenshot..."></textarea><button class="ft-new-job-btn" data-rich-text-submit type="button" wire:click="addTaskComment" wire:loading.attr="disabled" wire:target="addTaskComment">Comment</button></div>
                @endif
                <div class="ft-activity-feed">
                    @forelse($timeline as $entry)
                        @php
                            $eventLabel = $entry->kind === 'comment' ? 'Comment' : \Illuminate\Support\Str::headline(str_replace(['task.','job.'], '', (string) $entry->event));
                            $actorName = $entry->user?->name ?? 'System';
                            $entryLocalTime = $entry->created_at?->copy()->timezone($displayTimezone);
                        @endphp
                        @php
                            $entryFocusKey = $entry->kind === 'comment' ? 'task-'.$entry->id : null;
                            $entryAnchor = $entry->kind === 'comment' ? 'task-comment-'.$entry->id : null;
                            $isFocusedComment = $entryFocusKey !== null && $focusComment === $entryFocusKey;
                        @endphp
                        <article @if($entryAnchor) id="{{ $entryAnchor }}" @endif class="ft-activity-entry {{ $entry->kind==='comment' ? 'is-comment' : 'is-history' }} {{ $isFocusedComment ? 'is-focused-comment' : '' }}" @if($isFocusedComment) x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' }))" @endif>
                            <div class="ft-activity-entry-avatar"><x-ui.avatar :user="$entry->user" :name="$actorName" :size="32"/><span>{{ $entry->kind==='comment' ? '💬' : '↻' }}</span></div>
                            <div class="ft-activity-entry-content">
                                <div class="ft-activity-entry-head"><div><b>{{ $actorName }}</b><span class="ft-activity-kind {{ $entry->kind==='comment' ? 'comment' : 'history' }}">{{ $entry->kind==='comment' ? 'Comment' : 'Change' }}</span></div><time title="{{ $entryLocalTime?->format('M j, Y g:i A') }} {{ $displayTimezone }}">{{ $entry->created_at?->diffForHumans() }}</time></div>
                                <div class="ft-rich-text-content"><x-ui.mention-text :text="$entry->body" /></div>
                                <div class="ft-activity-entry-meta"><span>{{ $eventLabel }}</span><span>•</span><span>{{ $entryLocalTime?->format('M j, Y · g:i A') }}</span></div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">No {{ $activityTab==='comments' ? 'comments' : ($activityTab==='history' ? 'changes' : 'activity') }} yet.</div>
                    @endforelse
                </div>
                @if($timelineTotal > $activityPerPage)
                    <div class="ft-activity-pagination">
                        <span>Showing {{ (($timelineCurrentPage - 1) * $activityPerPage) + 1 }}–{{ min($timelineCurrentPage * $activityPerPage, $timelineTotal) }} of {{ $timelineTotal }}</span>
                        <div>
                            <button type="button" wire:click="setTaskActivityPage({{ $timelineCurrentPage - 1 }})" @disabled($timelineCurrentPage <= 1)>Previous</button>
                            <span>Page {{ $timelineCurrentPage }} of {{ $timelinePages }}</span>
                            <button type="button" wire:click="setTaskActivityPage({{ $timelineCurrentPage + 1 }})" @disabled($timelineCurrentPage >= $timelinePages)>Next</button>
                        </div>
                    </div>
                @endif
            </section>
        </main>
        <aside>
            <section class="ft-detail-card ft-management-card">
                <h2>Management attention</h2>
                <div class="ft-attention-row"><span>Required evidence</span><b><span class="{{ $taskDocumentName ? 'ft-red-doc-icon' : '' }}">▯</span> {{ $taskDocumentName ?: 'No required evidence' }}</b></div>
                <div class="ft-attention-row ft-task-flag-row">
                    <span>Attention</span>
                    @if($canEditTask)
                        <select class="ft-task-flag-select {{ $task->needs_attention ? 'is-flagged' : '' }}" wire:change="setTaskFlag($event.target.value)">
                            <option value="" @selected($currentTaskFlag==='')>Not flagged</option>
                            @if($currentTaskFlag!=='' && !$taskFlagNames->contains($currentTaskFlag))
                                <option value="{{ $currentTaskFlag }}" selected>{{ $currentTaskFlag }}</option>
                            @endif
                            @foreach($taskFlags as $flag)
                                <option value="{{ $flag->name }}" @selected($currentTaskFlag===$flag->name)>{{ $flag->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <b class="{{ $task->needs_attention ? 'danger-text' : '' }}"><span class="ft-red-flag">⚑</span> {{ $currentTaskFlag ?: 'Not flagged' }}</b>
                    @endif
                </div>
                @if($canEditTask && $taskFlags->isEmpty() && $currentTaskFlag==='')
                    <small class="ft-task-flag-help">Add Task Flags in Master Data to make them available here.</small>
                @endif
            </section>

            <section class="ft-detail-card ft-job-context-card"><h2>Order context</h2><b>{{ $job?->title }}</b><div><span>Client</span><b>{{ $job?->client?->name }}</b></div><div><span>Order health</span><b class="{{ $job?->needs_attention ? 'danger-text' : '' }}"><span class="{{ $job?->needs_attention ? 'ft-red-dot' : '' }}"></span>{{ $job?->needs_attention ? 'Needs Attention' : $job?->health }}</b></div><div><span>Delivery</span><b>{{ $job?->delivery_date?->format('M j, Y') ?? '—' }}</b></div><div class="ft-context-progress"><span>Order progress</span><b>{{ $job?->progress }}%</b><div class="ft-line-progress"><span style="width:{{ $job?->progress ?? 0 }}%"></span></div></div><button class="ft-link-blue ft-open-job" wire:click="closeTask">Open order details ↗</button></section>

        </aside>
    </div>
</div>
