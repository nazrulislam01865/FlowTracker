@props(['job', 'task', 'mode' => 'locked', 'displayCode' => null, 'taskStatuses' => collect(), 'context' => [], 'overviewTaskLinkFormTaskId' => null])
@php
    $permissions = data_get($context, 'taskPermissions.'.(int) $task->id, []);
    $canEditTask = (bool) data_get($permissions, 'edit', false);
    $canAssignTask = (bool) data_get($permissions, 'assign', false);
    $canDeleteTask = (bool) data_get($permissions, 'delete', false);
    $canUploadDocument = (bool) ($context['canUploadDocument'] ?? false);
    $canLinkDocument = (bool) ($context['canLinkDocument'] ?? false);
    $canDeleteDocument = (bool) ($context['canDeleteDocument'] ?? false);
    $canExportDocument = (bool) ($context['canExportDocument'] ?? false);
    $taskDocuments = $job->documents->where('task_id', $task->id)->sortByDesc('created_at')->values();
    $taskLinks = \App\Support\JobDetailPresenter::taskLinks($job, $task);
    $automationKey = app(\App\Services\OrderWorkflowActionService::class)->automationKey($task);
    $isArtworkUploadTask = $automationKey === 'ART_PREPARE_UPLOAD';
    $isProductionEstimatedDeliveryTask = $automationKey === 'PROD_SET_ESTIMATED_DELIVERY';
    $artworkRevisionNotes = $task->relationLoaded('artworkRevisionNotes') ? $task->artworkRevisionNotes : collect();
    $revisionReferenceDocumentIds = $artworkRevisionNotes
        ->map(function ($revisionNote) {
            $referenceDocument = $revisionNote->relationLoaded('referenceDocument')
                ? $revisionNote->getRelation('referenceDocument')
                : null;

            return (int) ($referenceDocument?->id ?? 0);
        })
        ->filter()
        ->unique()
        ->values();
    // Artwork task: only the latest revision belongs in the live Order Details view.
    // Older artwork versions remain stored and available from document/version history,
    // but are intentionally hidden from the Artwork stage task row.
    $artworkVersionDocuments = $isArtworkUploadTask
        ? $taskDocuments
            ->sortBy(function ($document) {
                $version = (int) ($document->version ?? 0);

                return [
                    $version > 0 ? $version : 999999,
                    optional($document->created_at)->timestamp ?? 0,
                    (int) $document->id,
                ];
            })
            ->values()
        : collect();
    $latestArtworkDocument = $artworkVersionDocuments->last();
    $latestArtworkDocumentId = (int) ($latestArtworkDocument?->id ?? 0);
    $resourceDocuments = $isArtworkUploadTask
        ? collect([$latestArtworkDocument])->filter()->values()
        : $taskDocuments
            ->reject(fn ($document) => $revisionReferenceDocumentIds->contains((int) $document->id))
            ->values();
    $isCurrentPhase = (int) $task->workflow_phase_id === (int) $job->workflow_phase_id;
    $status = $mode === 'locked' && !\App\Support\OrderDetailPresenter::isCompletedTask($task)
        ? 'Locked'
        : trim((string) ($task->status ?: ($mode === 'active' ? 'Ready' : 'Completed')));
    $statusLower = strtolower($status);
    $statusClass = $mode === 'done' ? 'done' : ($mode === 'active' ? ((str_contains($statusLower, 'wait') || str_contains($statusLower, 'issue') || str_contains($statusLower, 'pending')) ? 'wait' : 'active') : '');
    $displayStatus = $isProductionEstimatedDeliveryTask && $mode === 'active' && ! $job->estimated_delivery_date
        ? 'Required'
        : $status;
    if ($isProductionEstimatedDeliveryTask && $mode === 'active' && ! $job->estimated_delivery_date) $statusClass = 'wait';
    $assigneeName = $task->assignee?->name ?: 'Unassigned';
    $assigneeInitials = collect(preg_split('/\s+/', trim($assigneeName)))->filter()->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('');
    $isCancelled = strcasecmp((string) $job->status, 'Cancelled') === 0;
    $documentCategoryName = (string) ($task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name ?: '');
    $requiresDocument = (bool) ($task->document_category_id || $task->setupTemplate?->document_category_id);
    $requiredBeforeCompletion = (bool) ($task->setupTemplate?->document_required_before_completion ?? false);
    $workflowAction = data_get($context, 'taskActions.'.(int) $task->id, []);
    $workflowActionLabel = (string) ($workflowAction['label'] ?? 'Take action');
    $workflowActionType = (string) ($workflowAction['type'] ?? 'workflow');
    $taskColor = \App\Support\MasterColor::normalize((string) ($task->setupTemplate?->color ?? $task->template?->color ?? ''))
        ?: \App\Support\MasterColor::normalize((string) ($task->phase?->color ?? ''))
        ?: '#2563EB';
@endphp
<article id="order-task-{{ $task->id }}" class="task ft-order-task-row {{ $mode }} {{ $isCancelled ? 'cancelled-task' : '' }} {{ $isProductionEstimatedDeliveryTask ? 'ft-order-task-row--estimated-delivery' : '' }}" style="{{ \App\Support\MasterColor::style($taskColor) }}border-left:4px solid var(--ft-master-color,#2563EB)" wire:key="order-task-row-{{ $task->id }}">
    <div class="task-icon ft-order-task-icon">{{ $mode === 'done' ? '✓' : ($mode === 'active' ? '●' : '⌁') }}</div>
    <div class="task-copy ft-order-task-copy">
        <div class="task-code">TASK {{ $displayCode ?: ($task->task_number ?: str_pad((string) $task->id, 3, '0', STR_PAD_LEFT)) }}</div>
        <div class="task-title">
            {{ $task->title }}
            @if($isProductionEstimatedDeliveryTask)
                <span class="ft-order-required-task-badge">Required</span>
            @endif
        </div>
        @if($task->description || $task->setupTemplate?->description)<div class="task-description">{{ \Illuminate\Support\Str::limit(strip_tags((string) ($task->description ?: $task->setupTemplate?->description)), 105) }}</div>@endif
    </div>

    <div class="ft-order-task-assignee-inline ft-inline-edit-shell"
        x-data="window.FlowTrack.ui.inlineEdit({ key:@js('task-'.$task->id.'-assignee'), label:'task assignee', value:@js($task->assignee_id ?? ''), display:@js($assigneeName), avatarUrl:@js($task->assignee?->profileImageUrl() ?? '') })"
        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
        x-on:click.outside="if(editing) cancelEdit()"
        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeFromJob({{ $task->id }}, draftValue), { avatarUrl:String($event.detail?.avatarUrl ?? '') })">
        <div class="ft-order-inline-display-row">
            @if($canAssignTask && !$isCancelled)
                <button
                    x-ref="assigneeAnchor"
                    :disabled="status === 'saving'"
                    type="button"
                    class="ft-order-assignee-display ft-order-inline-name-trigger"
                    :class="{ 'is-open': editing }"
                    title="Edit assignee"
                    aria-label="Edit task assignee"
                    x-on:click.stop="openRemotePicker($refs.assigneeAnchor)"
                >
                    <span class="ft-inline-avatar-slot"><x-ui.inline-live-avatar :size="28" /></span>
                    <span class="ft-order-assignee-name" x-text="display">{{ $assigneeName }}</span>
                    <span class="ft-order-inline-trigger-icon" aria-hidden="true">✎</span>
                </button>
            @else
                <div class="ft-order-assignee-display">
                    <span class="ft-inline-avatar-slot"><x-ui.inline-live-avatar :size="28" /></span>
                    <span class="ft-order-assignee-name" x-text="display">{{ $assigneeName }}</span>
                </div>
            @endif
        </div>
        @if($canAssignTask && !$isCancelled)
            <div x-cloak x-show="editing" class="ft-order-assignee-picker">
                <x-ui.inline-remote-user :value="$task->assignee_id ?? ''" :selected-label="$assigneeName" parent-type="job" :parent-id="$job->id" variant="compact" :menu-width="300" external-trigger />
            </div>
            <x-ui.inline-save-state compact />
        @endif
    </div>

    <div class="date ft-order-task-due ft-inline-edit-shell"
        x-data="window.FlowTrack.ui.inlineEdit({ key:@js('task-'.$task->id.'-due-date'), label:'task due date', value:@js($task->due_date?->format('Y-m-d') ?? ''), display:@js($task->due_date?->format('M j, Y') ?? 'Set due date') })"
        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
        <div class="ft-order-inline-display-row" x-show="!editing">
            <span class="ft-order-inline-value" x-text="display">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
            @if($canEditTask && !$isCancelled)
                <button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button ft-order-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.orderDue.showPicker ? $refs.orderDue.showPicker() : $refs.orderDue.focus())">✎</button>
            @endif
        </div>
        @if($canEditTask && !$isCancelled)
            <input x-ref="orderDue" x-cloak x-show="editing" x-model="draftValue" class="ft-order-inline-input" type="date"
                x-on:keydown.escape.prevent="cancelEdit()"
                x-on:blur="if (editing) cancelEdit()"
                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDateFromJob({{ $task->id }}, draftValue))">
            <x-ui.inline-save-state compact />
        @endif
    </div>

    <div class="task-state ft-order-task-state">
        <span class="task-status ft-order-task-status {{ $statusClass }}">{{ $displayStatus }}</span>
        @if($taskDocuments->isNotEmpty())
            @php $latestTaskDocument = $isArtworkUploadTask ? $latestArtworkDocument : $taskDocuments->first(); @endphp
            <div class="card-sub">
                📎 {{ $latestTaskDocument->name }}
                @if($isArtworkUploadTask)
                    · Version {{ max(1, (int) $latestTaskDocument->version) }} · Latest
                @elseif($taskDocuments->count() > 1)
                    +{{ $taskDocuments->count() - 1 }}
                @endif
            </div>
        @elseif($taskLinks->isNotEmpty())
            <div class="card-sub">↗ {{ $taskLinks->count() }} external link{{ $taskLinks->count() === 1 ? '' : 's' }}</div>
        @elseif($requiresDocument)
            <div class="card-sub">📎 {{ $documentCategoryName ?: 'Document' }}{{ $requiredBeforeCompletion ? ' required' : '' }}</div>
        @endif
    </div>

    <div class="task-actions ft-order-task-actions">
        @if($isCancelled)
            <button type="button" class="btn small" disabled>Blocked</button>
        @elseif($mode === 'active' && $isCurrentPhase)
            @if($canEditTask)
                @if(($workflowActionType === 'document' || ($requiresDocument && $requiredBeforeCompletion && $taskDocuments->isEmpty() && $taskLinks->isEmpty())) && ($canUploadDocument || $canLinkDocument))
                    <button type="button" class="btn small primary" wire:click="openOrderWorkflowAction({{ $task->id }})">{{ $workflowActionLabel }}</button>
                @else
                    <button type="button" class="btn small primary" wire:click="openOrderWorkflowAction({{ $task->id }})">{{ $workflowActionLabel }}</button>
                @endif
            @endif
        @elseif($mode === 'done')
            <button type="button" class="btn small" wire:click="viewTask({{ $task->id }})">View</button>
        @endif
    </div>
</article>

@if((int) $overviewTaskLinkFormTaskId === (int) $task->id && $canEditTask)
    <form class="task-link-form ft-order-task-link-form" wire:submit.prevent="saveOverviewTaskLink({{ $task->id }})" wire:key="order-task-link-form-{{ $task->id }}">
        <input type="url" wire:model="overviewTaskLinkUrl" placeholder="Paste external document link..." autocomplete="url">
        <button type="button" class="btn small" wire:click="cancelOverviewTaskLinkForm">Cancel</button><button type="submit" class="btn primary small">Add link</button>
        @error('overviewTaskLinkUrl')<span class="validation-error">{{ $message }}</span>@enderror
    </form>
@endif

@foreach($artworkRevisionNotes as $revisionNote)
    <x-jobs.order-detail.artwork-revision-card
        :revision-note="$revisionNote"
        :can-export-document="$canExportDocument"
    />
@endforeach

@if($resourceDocuments->isNotEmpty() || $taskLinks->isNotEmpty())
    <div class="task-resources ft-order-task-resources">
        @foreach($resourceDocuments as $document)
            <div
                wire:key="order-task-document-{{ $document->id }}"
                class="ft-order-task-resource-row {{ $isArtworkUploadTask ? 'is-latest-artwork' : '' }}"
            >
                <span class="file-icon ft-order-file-icon">{{ strtoupper(pathinfo($document->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                <span>
                    <b>
                        {{ $document->name }}
                        @if($isArtworkUploadTask)
                            · Version {{ max(1, (int) $document->version) }}
                        @endif
                    </b>
                    <small>
                        {{ $document->uploader?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::format($document->created_at, 'M j, Y, g:i A') }}
                        @if($isArtworkUploadTask)
                            · Latest
                        @endif
                    </small>
                </span>
                <span class="ft-order-task-resource-actions">
                    @if($isArtworkUploadTask)
                        <em class="ft-order-artwork-version-state is-latest">Latest</em>
                    @endif
                    <a href="{{ route('documents.open', $document) }}" target="_blank" rel="noopener">Open</a>
                    @if($canExportDocument)<a href="{{ route('documents.download', $document) }}">Download</a>@endif
                    @if($canDeleteDocument)<button type="button" wire:click="deleteJobDocument({{ $document->id }})" wire:confirm="Delete this document link?">×</button>@endif
                </span>
            </div>
        @endforeach
        @foreach($taskLinks as $link)
            <div wire:key="order-task-link-{{ $link->id }}"><span class="file-icon link ft-order-file-icon">↗</span><span><b>{{ \Illuminate\Support\Str::limit($link->url, 90) }}</b><small>{{ $link->created_at ? \App\Support\UserLocalTime::format($link->created_at, 'M j, Y, g:i A') : '—' }}</small></span><span><a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">Open ↗</a>@if($canEditTask)<button type="button" wire:click="deleteOverviewTaskLink({{ $task->id }}, {{ $link->id }})" wire:confirm="Remove this link?">×</button>@endif</span></div>
        @endforeach
    </div>
@endif
