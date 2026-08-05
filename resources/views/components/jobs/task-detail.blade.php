@props([
    'task',
    'users',
    'taskProgress',
    'taskStatuses'=>collect(),
    'priorities'=>collect(),
    'availableDocuments'=>collect(),
    'activityTab'=>'all',
    'activityPage'=>1,
    'taskDocumentUploads'=>[],
    'showTaskDocumentPicker'=>false,
])
@php
    $job = $task->job;
    $done = $task->checklistItems->where('is_completed',true)->count();
    $total = $task->checklistItems->count();
    $checkTotal = max(1, $total);
    $previousTask = $job?->tasks?->where('workflow_phase_id',$task->workflow_phase_id)->where('id','<',$task->id)->sortByDesc('id')->first();
    $taskDocumentName = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name;
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditTask = $accessControl->canEditVisibleTask(auth()->user(), $task);
    $canAssignTask = $accessControl->canAssignTask(auth()->user(), $task);
    $canCheck = $canEditTask;
    $canUploadDocument = $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $accessControl->can(auth()->user(), 'documents', 'link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $effectiveDescription = $task->description ?: $task->setupTemplate?->description;
    $effectiveStartDate = $task->start_date ?: $task->created_at;
    $commentEvents = $task->comments->map(fn($comment)=>(object)[
        'kind'=>'comment','event'=>'task.comment','user'=>$comment->user,'body'=>$comment->body,'created_at'=>$comment->created_at,
    ]);
    $activityEvents = $task->activities->reject(fn($activity)=>$activity->event==='task.comment')->map(fn($activity)=>(object)[
        'kind'=>'activity','event'=>$activity->event,'user'=>$activity->user,'body'=>$activity->description,'created_at'=>$activity->created_at,
    ]);
    $timeline = $commentEvents->concat($activityEvents)->sortByDesc('created_at')->values();
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
                <a href="{{ route('my-work') }}" wire:navigate>My Work</a><span>/</span>
                <a class="ft-copyable-id-link" href="{{ route('jobs.index', ['open'=>$task->flow_job_id, 'task'=>$task->id]) }}" wire:navigate>{{ $task->task_number }}</a>
                <button type="button" class="ft-copy-id-btn" title="Copy Task ID" aria-label="Copy {{ $task->task_number }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($task->task_number)); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
            </div>
            @if($job)
                <div class="ft-detail-number ft-detail-linked-number">
                    <a href="{{ route('jobs.index', ['open'=>$job->id]) }}" wire:navigate>{{ $job->job_number }}</a>
                    <button type="button" class="ft-copy-id-btn" title="Copy Job ID" aria-label="Copy {{ $job->job_number }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($job->job_number)); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                </div>
            @endif
            <h1 class="ft-editable-task-title" x-data="{editing:false}">
                <span x-show="!editing">{{ $task->title }}</span>
                @if($canEditTask)
                    <button x-show="!editing" type="button" class="ft-pencil" aria-label="Edit task title" title="Edit task name" x-on:click.stop="editing=true; $nextTick(() => $refs.taskTitle.focus())">✎</button>
                    <input x-ref="taskTitle" x-show="editing" type="text" value="{{ $task->title }}" maxlength="255"
                        x-on:keydown.escape="editing=false"
                        x-on:keydown.enter="$event.target.blur()"
                        x-on:blur="editing=false"
                        wire:change="updateSelectedTaskField('title', $event.target.value)">
                @endif
            </h1>
        </div>
        <div class="ft-detail-actions"><button class="ft-new-job-btn ft-mark-complete" wire:click="markTaskComplete" @disabled($task->status==='Completed' || !$canEditTask)>{{ $task->status==='Completed' ? 'Completed' : 'Mark complete' }}</button><button class="ft-outline-btn ft-square-action" type="button">•••</button><button class="ft-close-page" wire:click="closeTask" type="button" title="Back to job details" aria-label="Back to job details">×</button></div>
    </div>
    @error('taskCompletion')<div class="validation-error ft-task-completion-error">{{ $message }}</div>@enderror

    <div class="ft-task-detail-layout">
        <main>
            <section class="ft-task-property-grid ft-friendly-task-properties">
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Assignee</small>
                    <div class="ft-task-property-display"><x-ui.avatar :name="$task->assignee?->name ?? 'Unassigned'" :size="26"/><b class="ft-property-value">{{ $task->assignee?->name ?? 'Unassigned' }}</b>@if($canAssignTask)<button type="button" title="Edit assignee" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.assignee?.focus())">✎</button>@endif</div>
                    @if($canAssignTask)<div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><select x-ref="assignee" class="ft-task-property-input" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('assignee_id',$event.target.value)"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int)$task->assignee_id===(int)$user->id)>{{ $user->name }}</option>@endforeach</select></div>@endif
                </div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Status</small>
                    <div class="ft-task-property-display"><span class="status-dot blue"></span><b class="ft-property-value">{{ $task->status }}</b>@if($canEditTask)<button type="button" title="Edit status" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.status?.focus())">✎</button>@endif</div>
                    @if($canEditTask)<div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><select x-ref="status" class="ft-task-property-input" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('status',$event.target.value)">@foreach($taskStatuses as $status)<option value="{{ $status }}" @selected($task->status===$status)>{{ $status }}</option>@endforeach</select></div>@endif
                </div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Priority</small>
                    <div class="ft-task-property-display"><span class="status-dot amber"></span><b class="ft-property-value">{{ $task->priority }}</b>@if($canEditTask)<button type="button" title="Edit priority" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.priority?.focus())">✎</button>@endif</div>
                    @if($canEditTask)<div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><select x-ref="priority" class="ft-task-property-input" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('priority',$event.target.value)">@foreach($priorities as $priority)<option value="{{ $priority->name }}" @selected($task->priority===$priority->name)>{{ $priority->name }}</option>@endforeach</select></div>@endif
                </div>
                <div class="ft-task-property"><small>Phase</small><div class="ft-task-property-display"><b class="ft-property-value">{{ $task->phase?->name ?? '—' }}</b></div></div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Start date</small>
                    <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value">{{ $effectiveStartDate?->format('M j, Y') ?? 'Not set' }}</b>@if($canEditTask)<button type="button" title="Edit start date" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.start?.focus())">✎</button>@endif</div>
                    @if($canEditTask)<div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><input x-ref="start" class="ft-task-property-input" type="date" value="{{ $effectiveStartDate?->format('Y-m-d') }}" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('start_date',$event.target.value)"></div>@endif
                </div>
                <div class="ft-task-property" x-data="{editing:false}" :class="{'is-editing':editing}">
                    <small>Due date</small>
                    <div class="ft-task-property-display {{ $task->due_date?->isPast() && !$task->completed_at ? 'danger-text' : '' }}"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value">{{ $task->due_date?->format('M j, Y') ?? 'Not set' }}</b>@if($canEditTask)<button type="button" title="Edit due date" x-on:click.stop="editing=!editing;$nextTick(()=>$refs.due?.focus())">✎</button>@endif</div>
                    @if($canEditTask)<div class="ft-task-property-popover" x-cloak x-show="editing" x-on:click.outside="editing=false"><input x-ref="due" class="ft-task-property-input" type="date" value="{{ $task->due_date?->format('Y-m-d') }}" x-on:keydown.escape="editing=false" x-on:change="editing=false" wire:change="updateSelectedTaskField('due_date',$event.target.value)"></div>@endif
                </div>
            </section>

            <section class="ft-detail-card ft-description-card" x-data="{editing:false}">
                @if($canEditTask)<button x-show="!editing" class="ft-card-edit" type="button" title="Edit description" x-on:click="editing=true;$nextTick(()=>$refs.description.focus())">✎</button>@endif
                <h2>Description</h2>
                <p x-show="!editing">{{ $effectiveDescription ?: 'No description has been provided for this task.' }}</p>
                @if($canEditTask)<div x-show="editing" class="ft-inline-description-editor"><textarea x-ref="description" rows="4">{{ $task->description ?: $task->setupTemplate?->description }}</textarea><div><button type="button" class="ft-outline-btn" x-on:click="editing=false">Cancel</button><button type="button" class="ft-new-job-btn" x-on:click="$wire.updateSelectedTaskField('description',$refs.description.value); editing=false">Save</button></div></div>@endif
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
                        <label class="ft-task-upload-drop ft-livewire-upload-zone" for="taskDocumentUpload-{{ $task->id }}">
                            <input id="taskDocumentUpload-{{ $task->id }}" type="file" wire:model="taskDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                            <span class="ft-paperclip">⌕</span>
                            <div>Drop files here or <strong>browse</strong><small>{{ $taskDocumentName ? 'Required document: '.$taskDocumentName.' · ' : '' }}PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
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
                        <select wire:model="taskExistingDocumentId"><option value="">Select a stored document</option>@foreach($availableDocuments as $stored)<option value="{{ $stored->id }}">{{ $stored->name }} · {{ $stored->job?->job_number ?? 'Archive' }}</option>@endforeach</select>
                        <button class="ft-new-job-btn" type="button" wire:click="attachExistingToSelectedTask">Link document</button>
                        <button class="ft-outline-btn" type="button" wire:click="toggleTaskDocumentPicker">Cancel</button>
                    </div>
                    @error('taskExistingDocumentId')<div class="validation-error">{{ $message }}</div>@enderror
                @endif
                @foreach($task->documents->sortByDesc('created_at') as $doc)<div class="ft-attachment-row"><span class="ft-file-type">{{ strtoupper(pathinfo($doc->name,PATHINFO_EXTENSION) ?: 'FILE') }}</span><b>{{ $doc->name }}</b><small>{{ $doc->created_at?->format('M j, Y, H:i') }}</small><a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>@if($canDeleteDocument)<button type="button" class="ft-doc-delete-button" wire:click="deleteSelectedTaskDocument({{ $doc->id }})" wire:confirm="Delete this document link?">×</button>@endif</div>@endforeach
                <p class="ft-upload-note">Every file uploaded here is linked to this task and appears in Job Documents. A required document is counted only when this Task Pack task defines that document type.</p>
            </section>

            <section class="ft-detail-card ft-task-activity-card ft-friendly-activity">
                <div class="ft-activity-head">
                    <div><h2>Activity</h2><p>Comments and task changes, with who changed what and when.</p></div>
                    <div class="ft-activity-tabs"><button type="button" class="{{ $activityTab==='all'?'active':'' }}" wire:click="setTaskActivityTab('all')">All</button><button type="button" class="{{ $activityTab==='comments'?'active':'' }}" wire:click="setTaskActivityTab('comments')">Comments</button><button type="button" class="{{ $activityTab==='history'?'active':'' }}" wire:click="setTaskActivityTab('history')">History</button></div>
                </div>
                @if($canEditTask)
                    <div class="ft-comment-composer ft-friendly-composer"><x-ui.avatar :name="auth()->user()->name" :size="32"/><input wire:model="taskComment" wire:keydown.enter="addTaskComment" placeholder="Write a comment about this task..."><button class="ft-new-job-btn" type="button" wire:click="addTaskComment">Comment</button></div>
                @endif
                <div class="ft-activity-feed">
                    @forelse($timeline as $entry)
                        @php
                            $eventLabel = $entry->kind === 'comment' ? 'Comment' : \Illuminate\Support\Str::headline(str_replace(['task.','job.'], '', (string) $entry->event));
                            $actorName = $entry->user?->name ?? 'System';
                        @endphp
                        <article class="ft-activity-entry {{ $entry->kind==='comment' ? 'is-comment' : 'is-history' }}">
                            <div class="ft-activity-entry-avatar"><x-ui.avatar :name="$actorName" :size="32"/><span>{{ $entry->kind==='comment' ? '💬' : '↻' }}</span></div>
                            <div class="ft-activity-entry-content">
                                <div class="ft-activity-entry-head"><div><b>{{ $actorName }}</b><span class="ft-activity-kind {{ $entry->kind==='comment' ? 'comment' : 'history' }}">{{ $entry->kind==='comment' ? 'Comment' : 'Change' }}</span></div><time title="{{ $entry->created_at?->format('M j, Y g:i A') }}">{{ $entry->created_at?->diffForHumans() }}</time></div>
                                <p>{{ $entry->body }}</p>
                                <div class="ft-activity-entry-meta"><span>{{ $eventLabel }}</span><span>•</span><span>{{ $entry->created_at?->format('M j, Y · g:i A') }}</span></div>
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
            <section class="ft-detail-card ft-management-card"><h2>Management attention</h2><div class="ft-attention-row"><span>Required evidence</span><b><span class="{{ $taskDocumentName ? 'ft-red-doc-icon' : '' }}">▯</span> {{ $taskDocumentName ?: 'No required evidence' }}</b></div><div class="ft-attention-row"><span>Attention</span><b class="{{ $task->needs_attention ? 'danger-text' : '' }}"><span class="ft-red-flag">⚑</span> {{ $task->needs_attention ? 'Marked as attention needed' : 'Not flagged' }}</b>@if($canEditTask)<button class="ft-link-blue" type="button" wire:click="toggleTaskAttention">{{ $task->needs_attention ? 'Clear flag' : 'Flag task' }}</button>@endif</div></section>

            <section class="ft-detail-card ft-job-context-card"><h2>Job context</h2><button class="ft-link-blue ft-job-context-link" wire:click="closeTask">{{ $job?->job_number }} ↗</button><b>{{ $job?->title }}</b><div><span>Client</span><b>{{ $job?->client?->name }}</b></div><div><span>Job health</span><b class="{{ $job?->needs_attention ? 'danger-text' : '' }}"><span class="{{ $job?->needs_attention ? 'ft-red-dot' : '' }}"></span>{{ $job?->needs_attention ? 'Needs Attention' : $job?->health }}</b></div><div><span>Delivery</span><b>{{ $job?->delivery_date?->format('M j, Y') ?? '—' }}</b></div><div class="ft-context-progress"><span>Job progress</span><b>{{ $job?->progress }}%</b><div class="ft-line-progress"><span style="width:{{ $job?->progress ?? 0 }}%"></span></div></div><button class="ft-link-blue ft-open-job" wire:click="closeTask">Open job details ↗</button></section>

            <section class="ft-detail-card ft-dependency-card"><h2>Dependencies</h2><p class="ok-text"><span class="ft-check-circle">✓</span> No active blockers</p><p><span>Previous task:</span> <b>{{ $previousTask?->title ?? 'None' }}</b></p></section>
            <section class="ft-detail-card ft-task-meta-card">Created {{ $task->created_at?->format('M j, Y') }} <span>·</span> Updated {{ $task->updated_at?->diffForHumans() }}</section>
        </aside>
    </div>
</div>
