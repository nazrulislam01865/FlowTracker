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
    'taskDetailSectionsReady'=>[],
])
@php
    $job = $task->job;
    $checklistReady = (bool) ($taskDetailSectionsReady['checklist'] ?? false);
    $attachmentsReady = (bool) ($taskDetailSectionsReady['attachments'] ?? false);
    $activityReady = (bool) ($taskDetailSectionsReady['activity'] ?? false);
    $done = $checklistReady && $task->relationLoaded('checklistItems') ? $task->checklistItems->where('is_completed',true)->count() : 0;
    $total = $checklistReady && $task->relationLoaded('checklistItems') ? $task->checklistItems->count() : 0;
    $checkTotal = max(1, $total);
    $taskDocumentName = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name;
    $taskDocumentInstructions = trim((string) ($task->setupTemplate?->document_instructions ?? ''));
    $accessControl = app(\App\Services\AccessControlService::class);
    $canModerateTaskActivity = $accessControl->isAdministrator(auth()->user());
    $mayEditTask = $accessControl->canEditVisibleTask(auth()->user(), $task);
    // Inline task editing is permission-driven on the Task Details page.
    // Opening the page through a generic View action must not hide inline edit
    // controls from a user who is authorized to edit this task.
    $canEditTask = $mayEditTask;
    $canAssignTask = $accessControl->canAssignTask(auth()->user(), $task);
    $canCheck = $canEditTask;
    // Task Details permissions must be driven by the actual task authorization,
    // not by how the page was opened. A normal View action can still be an
    // editable task for its assignee/creator/admin, so attachment upload must
    // remain available in that case.
    $canUploadDocument = $canEditTask && $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $canEditTask && $accessControl->can(auth()->user(), 'documents', 'link');
    $canManageDocuments = $canUploadDocument || $canLinkDocument;
    $canDeleteDocument = $canEditTask && $accessControl->can(auth()->user(), 'documents', 'delete');
    $effectiveDescription = $task->description ?: $task->setupTemplate?->description;
    $effectiveStartDate = $task->start_date ?: \App\Support\UserLocalTime::localize($task->created_at);
    $completedOn = $task->completed_at?->copy()->timezone($displayTimezone);
    $masterData = app(\App\Services\MasterDataService::class);
    $currentStatusColor = $masterData->colorFor('order_task_status', (string) $task->status);
    $currentPriorityColor = $masterData->displayColorFor('priority', (string) $task->priority);
    $currentTaskFlag = app(\App\Services\OrderTaskFlagService::class)->labelForTask($task) ?: '';
    $currentTaskFlagColor = $currentTaskFlag !== '' ? $masterData->colorFor('order_task_flag', $currentTaskFlag) : null;
    $currentOrderFlag = $job ? (app(\App\Services\OrderTaskFlagService::class)->labelForOrder($job) ?: '') : '';
    $currentOrderFlagColor = $currentOrderFlag !== '' ? $masterData->colorFor('order_flag', $currentOrderFlag) : null;
    $timeline = collect();
    $activityPerPage = 30;
    $timelineTotal = 0;
    $timelinePages = 1;
    $timelineCurrentPage = 1;
    if ($activityReady && $task->relationLoaded('comments') && $task->relationLoaded('activities')) {
        $commentEvents = $task->comments->map(fn($comment)=>(object)[
            'id'=>(int)$comment->id,'kind'=>'comment','event'=>'task.comment','user'=>$comment->user,'body'=>$comment->body,'created_at'=>$comment->created_at,
        ]);
        $activityEvents = $task->activities->reject(fn($activity)=>$activity->event==='task.comment')->map(fn($activity)=>(object)[
            'id'=>(int)$activity->id,'kind'=>'activity','event'=>$activity->event,'user'=>$activity->user,'body'=>$activity->description,'created_at'=>$activity->created_at,
        ]);
        $timeline = $commentEvents->concat($activityEvents)->sortByDesc(fn($entry) => sprintf('%020d-%020d', $entry->created_at?->getTimestamp() ?? 0, $entry->id ?? 0))->values();
        if($activityTab==='comments') $timeline = $timeline->where('kind','comment')->values();
        if($activityTab==='history') $timeline = $timeline->where('kind','activity')->values();
        $timelineTotal = $timeline->count();
        $timelinePages = max(1, (int) ceil($timelineTotal / $activityPerPage));
        $timelineCurrentPage = min(max(1, (int) $activityPage), $timelinePages);
        $timeline = $timeline->forPage($timelineCurrentPage, $activityPerPage)->values();
    }
@endphp
<div {{ $attributes->class('ft-task-detail-page ft-exact-task-detail') }}>
    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
    <div class="ft-detail-toolbar task-toolbar ft-exact-task-header">
        <div class="ft-task-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <a href="{{ route('my-work') }}" wire:navigate>My Tasks</a>
                @if($job)
                    <span>/</span><a href="{{ route('jobs.index', ['open'=>$job->id]) }}" wire:navigate>{{ $job->displayOrderNumber() }}</a>
                @endif
                <span>/</span><span>{{ $task->task_number }}</span>
            </div>
            <div class="ft-task-title-line">
                <h1
                    class="ft-editable-task-title ft-inline-edit-shell"
                    x-data="window.FlowTrack.ui.inlineEdit({ key: @js('task-'.$task->id.'-title'), label: 'task title', value: @js($task->title), display: @js($task->title) })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span x-show="!editing" x-text="display">{{ $task->title }}</span>
                    @if($canEditTask)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil ft-detail-edit-button" aria-label="Edit task title" title="Edit task name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskTitle.focus())"><x-ui.detail-icon name="edit" /></button>
                        <input x-ref="taskTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:keydown.enter.prevent="$event.target.blur()"
                            x-on:blur="if (editing) commit(draftValue.trim(), draftValue.trim(), () => $wire.updateSelectedTaskField('title', draftValue.trim()))">
                        <x-ui.inline-save-state />
                    @endif
                </h1>
                @if($task->phase?->name)<span class="ft-task-title-phase">· <x-ui.phase-label :phase="$task->phase" /></span>@endif
            </div>
        </div>
        <div class="ft-detail-actions">@if($canEditTask)<button class="ft-new-job-btn ft-mark-complete" wire:click="markTaskComplete" @disabled($task->status==='Completed')>{{ $task->status==='Completed' ? 'Completed' : 'Mark complete' }}</button>@endif<button class="ft-close-page" wire:click="closeTask" type="button" title="Back to order details" aria-label="Back to order details">×</button></div>
    </div>
    @error('taskCompletion')<div class="validation-error ft-task-completion-error">{{ $message }}</div>@enderror

    <div class="ft-task-detail-layout">
        <main>
            @include('components.jobs.task-detail.properties')

            @include('components.jobs.task-detail.description')

            @if($checklistReady)
                @include('components.jobs.task-detail.checklist')
            @else
                <x-ui.progressive-section-loader section="checklist" method="loadDetailSection" key-prefix="task-detail" context-type="task" :context-id="$task->id" :rows="3" message="Loading checklist when needed…" root-margin="320px 0px" />
            @endif

            @if($attachmentsReady)
                @include('components.jobs.task-detail.attachments')
            @else
                <x-ui.progressive-section-loader section="attachments" method="loadDetailSection" key-prefix="task-detail" context-type="task" :context-id="$task->id" :rows="3" message="Loading task attachments when needed…" root-margin="300px 0px" />
            @endif

            @if($activityReady)
                @include('components.jobs.task-detail.activity')
            @else
                <x-ui.progressive-section-loader section="activity" method="loadDetailSection" key-prefix="task-detail" context-type="task" :context-id="$task->id" :rows="4" message="Loading task activity when needed…" root-margin="300px 0px" />
            @endif
        </main>
        <aside>
            @include('components.jobs.task-detail.sidebar')

        </aside>
    </div>
</div>
