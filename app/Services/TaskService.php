<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\FlowJobMember;
use App\Models\FlowTaskChecklistItem;
use App\Models\FlowTaskComment;
use App\Models\Task;
use App\Models\TaskLink;
use App\Models\User;
use App\Support\BoardLaneResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function visibleQuery(User $user): Builder
    {
        return app(AccessControlService::class)->applyTaskScope(Task::query(), $user);
    }

    public function list(User $user, array $filters = [])
    {
        return $this->visibleQuery($user)
            ->with(['job.client', 'assignee', 'phase'])
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($x) => $x->whereLike('title', "%{$s}%")->orWhereHas('job', fn ($j) => $j->whereLike('job_number', "%{$s}%"))))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['assignee'] ?? null, fn ($q, $a) => $q->where('assignee_id', $a))
            ->whereNull('completed_at')
            ->orderByRaw('due_date is null, due_date asc')
            ->limit(60)
            ->get();
    }

    public function metrics(User $user): array
    {
        $q = $this->visibleQuery($user)->whereNull('completed_at');

        return [
            'today' => (clone $q)->where('due_date', app(WorkspaceSettingsService::class)->localToday()->toDateString())->count(),
            'overdue' => (clone $q)->where('due_date', '<', app(WorkspaceSettingsService::class)->localToday()->toDateString())->count(),
            'attention' => (clone $q)->where('needs_attention', true)->count(),
            'approval' => (clone $q)->whereIn('status', ['Waiting for Client', 'Waiting for Internal Approval'])->count(),
            'upcoming' => (clone $q)->where('due_date', '>', app(WorkspaceSettingsService::class)->localToday()->toDateString())->count(),
            'completed_week' => $this->visibleQuery($user)->whereBetween('completed_at', app(WorkspaceSettingsService::class)->localWeekUtcBounds())->count(),
        ];
    }

    public function moveStatus(Task $task, string $status, User $actor): Task
    {
        // Order workflow status changes are serialized at the database level.
        // This prevents two browser sessions from completing the same task and
        // unlocking/advancing the same phase twice. The Livewire component only
        // submits an intent; the service remains authoritative.
        return DB::transaction(function () use ($task, $status, $actor): Task {
            FlowJob::query()->whereKey($task->flow_job_id)->lockForUpdate()->firstOrFail();
            $lockedTask = Task::query()->whereKey($task->id)->lockForUpdate()->firstOrFail();

            return $this->update($lockedTask, [
                'status' => $status,
                'assignee_id' => $lockedTask->assignee_id,
                'progress' => BoardLaneResolver::isCompleted($status) ? 100 : (BoardLaneResolver::isNotStarted($status) ? 0 : max($lockedTask->progress, 35)),
                'needs_attention' => $lockedTask->needs_attention,
                'attention_reason' => $lockedTask->attention_reason,
            ], $actor);
        }, 3);
    }


    public function addExternalLink(Task $task, string $url, User $actor): TaskLink
    {
        $this->assertEditable($task, $actor);
        $url = trim($url);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = (string) parse_url($url, PHP_URL_HOST);
        if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw ValidationException::withMessages(['overviewTaskLinkUrl' => 'Enter a valid http:// or https:// link.']);
        }

        // Create through the task relation so the foreign key always belongs
        // to the exact Order task that was authorized above. This also keeps the
        // persistence path identical to the relation used when the taskflow is
        // re-hydrated after Livewire closes the Add link form.
        $link = $task->links()->create([
            'created_by' => $actor->id,
            'url' => $url,
        ]);

        $this->record($task, $actor, 'task.link_added', 'External link added.', [
            'task_link_id' => $link->id,
            'url' => $url,
        ]);

        // The link is already persisted and the next render reads document
        // evidence directly from task_links. Do not run the parent Order/phase
        // lifecycle from inside this resource-save request: doing so can change
        // the visible taskflow before Livewire renders the newly saved link.
        // Task status/completion updates remain responsible for parent progress
        // and phase advancement.
        return $link->refresh();
    }

    public function removeExternalLink(Task $task, int $linkId, User $actor): void
    {
        $this->assertEditable($task, $actor);
        $link = $task->links()->whereKey($linkId)->firstOrFail();
        $link->delete();

        $this->record($task, $actor, 'task.link_removed', 'External link removed.', [
            'task_link_id' => $linkId,
        ]);
    }

    public function updateDueDate(Task $task, ?string $dueDate, User $actor): Task
    {
        $this->assertEditable($task, $actor);
        $old = $task->due_date?->format('Y-m-d');
        $new = $dueDate ?: null;
        $task->update(['due_date' => $new]);
        $this->record($task, $actor, 'task.due_date_updated', $this->changeDescription('Due date', $old, $new));

        return app(OrderTaskFlagService::class)->syncTask($task->refresh());
    }

    public function updateDetailField(Task $task, string $field, mixed $value, User $actor): Task
    {
        $allowed = ['title', 'assignee_id', 'status', 'priority', 'start_date', 'due_date', 'description'];
        abort_unless(in_array($field, $allowed, true), 422, 'This task field cannot be edited.');

        if ($field === 'assignee_id') {
            abort_unless(app(AccessControlService::class)->canAssignTask($actor, $task), 403);
        } else {
            $this->assertEditable($task, $actor);
        }

        if ($field === 'due_date') {
            return $this->updateDueDate($task, filled($value) ? (string) $value : null, $actor);
        }

        $old = $task->{$field};
        if ($old instanceof \DateTimeInterface) $old = $old->format('Y-m-d');

        $new = $value;
        if ($field === 'assignee_id') {
            $new = filled($value) ? (int) $value : null;
            if ($new) User::where('is_active', true)->findOrFail($new);
        } elseif ($field === 'start_date') {
            $new = filled($value) ? (string) $value : null;
        } elseif ($field === 'description') {
            $new = app(RichTextService::class)->normalize((string) $value, 10000, 'description');
        } elseif ($field === 'title') {
            $new = trim((string) $value);
            abort_if($new === '', 422, 'Task name is required.');
        } else {
            $new = trim((string) $value);
            abort_if($new === '', 422, ucfirst(str_replace('_', ' ', $field)).' is required.');
        }

        if ($field === 'status') app(OrderTaskSequenceService::class)->assertStatusActionable($task);

        $updates = [$field => $new];
        if ($field === 'status' && BoardLaneResolver::isCompleted($new)) $this->ensureCompletionRequirements($task);
        if ($field === 'status') {
            $updates['progress'] = BoardLaneResolver::isCompleted($new) ? 100 : (BoardLaneResolver::isNotStarted($new) ? 0 : max(1, min(99, (int) $task->progress)));
            $updates['completed_at'] = BoardLaneResolver::isCompleted($new) ? ($task->completed_at ?: now()) : null;
        }
        $task->update($updates);

        if ($field === 'assignee_id' && $new) {
            FlowJobMember::firstOrCreate(
                ['flow_job_id' => $task->flow_job_id, 'user_id' => $new],
                ['access_level' => 'member', 'can_manage_tasks' => false, 'can_upload_documents' => true, 'can_view_financials' => false],
            );
        }

        $labels = [
            'title' => 'Task name', 'assignee_id' => 'Assignee', 'status' => 'Status', 'priority' => 'Priority',
            'start_date' => 'Start date', 'description' => 'Description',
        ];
        $oldDisplay = $field === 'assignee_id' ? (User::find($old)?->name ?? 'Unassigned') : $old;
        $newDisplay = $field === 'assignee_id' ? (User::find($new)?->name ?? 'Unassigned') : $new;
        if ($field === 'description') {
            $oldDisplay = app(RichTextService::class)->plainText((string) $oldDisplay);
            $newDisplay = app(RichTextService::class)->plainText((string) $newDisplay);
        }
        $mentionIds = $field === 'description'
            ? app(MentionService::class)->userIdsFromText((string) $new)
            : [];

        $this->record($task, $actor, 'task.field_updated', $this->changeDescription($labels[$field] ?? $field, $oldDisplay, $newDisplay), [
            'field' => $field,
            'old' => $oldDisplay,
            'new' => $newDisplay,
            'mention_user_ids' => $mentionIds,
            'mention_text' => $field === 'description' ? (string) $new : null,
        ]);

        $task = app(OrderTaskFlagService::class)->syncTask($task->refresh());
        $this->refreshJobState($task, $actor);
        return $task->refresh();
    }

    public function update(Task $task, array $data, User $actor): Task
    {
        $assignmentChanged = array_key_exists('assignee_id', $data) && (int) ($data['assignee_id'] ?: 0) !== (int) ($task->assignee_id ?: 0);
        if ($assignmentChanged) abort_unless(app(AccessControlService::class)->canAssignTask($actor, $task), 403);
        $this->assertEditable($task, $actor);

        $before = $task->only(['status','assignee_id','progress','needs_attention','attention_reason']);
        $assigneeId = array_key_exists('assignee_id', $data) ? ($data['assignee_id'] ?: null) : $task->assignee_id;
        $status = trim((string) ($data['status'] ?? $task->status));
        abort_if($status === '', 422, 'Task status is required.');

        $statusRecord = app(OrderTaskFlagService::class)->statusRecord($status, false);
        if ($statusRecord) $status = (string) $statusRecord->name;

        app(OrderTaskSequenceService::class)->assertStatusActionable($task);
        if (BoardLaneResolver::isCompleted($status)) $this->ensureCompletionRequirements($task);

        $updates = [
            'status' => $status,
            'order_task_status_id' => $statusRecord?->id,
            'assignee_id' => $assigneeId,
            'progress' => BoardLaneResolver::isCompleted($status) ? 100 : (BoardLaneResolver::isNotStarted($status) ? 0 : (int) ($data['progress'] ?? $task->progress)),
            'completed_at' => BoardLaneResolver::isCompleted($status) ? ($task->completed_at ?: now()) : null,
        ];

        // attention_reason is an explanation only. The flag itself is never
        // chosen manually; it is derived from Order Task Status / due date.
        if (array_key_exists('attention_reason', $data)) {
            $reason = trim((string) $data['attention_reason']);
            $updates['attention_reason'] = $reason !== '' ? $reason : null;
        }

        $task->update($updates);

        if ($assigneeId) {
            FlowJobMember::firstOrCreate(
                ['flow_job_id' => $task->flow_job_id, 'user_id' => $assigneeId],
                ['access_level' => 'member', 'can_manage_tasks' => false, 'can_upload_documents' => true, 'can_view_financials' => false],
            );
        }

        $task = app(OrderTaskFlagService::class)->syncTask($task->refresh());
        $after = $task->only(['status','assignee_id','progress','needs_attention','attention_reason']);
        $changes = [];
        foreach ($after as $key => $value) {
            if (($before[$key] ?? null) != $value) $changes[$key] = ['old' => $before[$key] ?? null, 'new' => $value];
        }

        if ($changes) {
            $parts = [];
            foreach ($changes as $key => $change) {
                $label = ucfirst(str_replace('_', ' ', $key));
                if ($key === 'assignee_id') {
                    $change['old'] = User::find($change['old'])?->name ?? 'Unassigned';
                    $change['new'] = User::find($change['new'])?->name ?? 'Unassigned';
                }
                if ($key === 'needs_attention') {
                    $label = 'Automatic flag';
                    $change['old'] = $change['old'] ? 'Flagged' : 'Not flagged';
                    $change['new'] = $change['new'] ? 'Flagged' : 'Not flagged';
                }
                $parts[] = $this->changeDescription($label, $change['old'], $change['new']);
            }
            $this->record($task, $actor, 'task.updated', implode(' · ', $parts), ['changes' => $changes]);
        }

        $this->refreshJobState($task, $actor);

        return $task->refresh();
    }

    public function addChecklistItem(Task $task, string $label, User $actor): FlowTaskChecklistItem
    {
        $this->assertEditable($task, $actor);
        $label = trim($label);
        abort_if($label === '', 422, 'Checklist item is required.');
        $item = $task->checklistItems()->create([
            'label' => $label,
            'is_completed' => false,
            'sort_order' => ((int) $task->checklistItems()->max('sort_order')) + 1,
        ]);
        $this->record($task, $actor, 'task.checklist_added', 'Checklist item added: '.$label);
        $this->syncChecklistProgress($task);
        return $item;
    }

    public function toggleChecklistItem(Task $task, FlowTaskChecklistItem $item, bool $completed, User $actor): FlowTaskChecklistItem
    {
        $this->assertEditable($task, $actor);
        abort_unless((int) $item->flow_task_id === (int) $task->id, 422);
        $item->update(['is_completed' => $completed]);
        $this->record($task, $actor, 'task.checklist_updated', ($completed ? 'Completed checklist item: ' : 'Reopened checklist item: ').$item->label);
        $this->syncChecklistProgress($task);
        return $item->refresh();
    }

    public function deleteChecklistItem(Task $task, FlowTaskChecklistItem $item, User $actor): void
    {
        $this->assertEditable($task, $actor);
        abort_unless((int) $item->flow_task_id === (int) $task->id, 422);
        $label = $item->label;
        $item->delete();
        $this->record($task, $actor, 'task.checklist_deleted', 'Checklist item deleted: '.$label);
        $this->syncChecklistProgress($task);
    }

    public function addComment(Task $task, string $body, User $actor): FlowTaskComment
    {
        abort_unless(app(AccessControlService::class)->canEditTask($actor, $task), 403);
        $body = app(RichTextService::class)->normalize($body, 5000, 'comment');
        abort_if(!$body, 422, 'Comment cannot be empty.');
        $mentionIds = app(MentionService::class)->userIdsFromText($body);
        $comment = $task->comments()->create(['user_id' => $actor->id, 'body' => $body]);
        $activityBody = app(RichTextService::class)->plainText($body);
        $this->record($task, $actor, 'task.comment', 'Comment: '.$activityBody, [
            'comment_id' => $comment->id,
            'body' => $body,
            'mention_user_ids' => $mentionIds,
            'mention_text' => $body,
        ]);
        return $comment;
    }

    public function setAttentionFlag(Task $task, ?string $flag, User $actor): Task
    {
        $this->assertEditable($task, $actor);

        // Order Task Flags are automatic. Keep this compatibility method so
        // stale Livewire requests cannot corrupt the new status-driven mapping.
        return app(OrderTaskFlagService::class)->syncTask($task->refresh());
    }

    public function syncJobAttention(Task $task): void
    {
        app(OrderTaskFlagService::class)->syncJob($task->job()->first());
    }



    private function assertEditable(Task $task, User $actor): void
    {
        abort_unless(app(AccessControlService::class)->canEditTask($actor, $task), 403);
    }

    private function ensureCompletionRequirements(Task $task): void
    {
        $task->loadMissing(['documentCategory','setupTemplate.documentCategory']);
        $hasRequiredDocument = (bool) ($task->setupTemplate?->document_category_id ?: $task->document_category_id);
        $mustUploadBeforeCompletion = $task->setupTemplate
            ? (bool) ($task->setupTemplate->document_required_before_completion ?? true)
            : true;
        if (! $hasRequiredDocument || ! $mustUploadBeforeCompletion) return;

        // Courier labels and invoices are generated by their dedicated workflow
        // actions. Requiring a separately uploaded file here would make the
        // prototype action impossible to complete and prevent stage progression.
        // The generated action is audit-logged by OrderWorkflowActionService.
        $automationKey = app(OrderWorkflowActionService::class)->automationKey($task);
        if (in_array($automationKey, ['SHIP_LABEL', 'BILL_PREPARE'], true)) return;

        // The requirement belongs to the task itself. A file-backed Document or an
        // external TaskLink is valid submission evidence. This lets users provide a
        // cloud/document URL instead of uploading a duplicate file while keeping the
        // Task Pack requirement attached to the same task. Fresh exists() queries are
        // intentional so a previously-loaded empty relation cannot make newly-added
        // evidence appear missing during the next inline status update.
        if ($task->documents()->exists() || $task->links()->exists()) return;

        $name = $task->setupTemplate?->documentCategory?->name
            ?: $task->documentCategory?->name
            ?: 'required document';

        throw ValidationException::withMessages([
            'taskCompletion' => 'Upload or link '.$name.' before completing this task.',
        ]);
    }

    private function syncChecklistProgress(Task $task): void
    {
        $total = $task->checklistItems()->count();
        if ($total <= 0 || $task->status === 'Completed') return;
        $done = $task->checklistItems()->where('is_completed', true)->count();
        $progress = (int) round(($done / $total) * 100);
        $task->update(['progress' => min(99, $progress)]);
    }

    private function refreshJobState(Task $task, User $actor): void
    {
        $task = $task->refresh();
        $job = $task->job()->first();
        if (!$job) return;
        if ((int) $task->workflow_phase_id === (int) $job->workflow_phase_id) {
            app(OrderTaskSequenceService::class)->synchronizeCurrentPhase($job, $actor);
        }
        app(JobService::class)->recalculateProgress($job->refresh());
        if ((int) $task->workflow_phase_id === (int) $job->workflow_phase_id) {
            app(JobService::class)->maybeAutoAdvance($job->refresh(), $actor);
        }
    }

    private function record(Task $task, User $actor, string $event, string $description, array $meta = []): void
    {
        $task->activities()->create([
            'user_id' => $actor->id,
            'event' => $event,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);

        $job = $task->job()->first();
        if ($job) {
            $job->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.task_activity',
                'description' => $task->title.': '.$description,
                'meta' => array_merge(['task_id' => $task->id, 'task_number' => $task->task_number, 'task_event' => $event], $meta),
            ]);
        }

        $fresh = $task->refresh();
        $isAssignment = ($meta['field'] ?? null) === 'assignee_id' || isset($meta['changes']['assignee_id']);
        $type = ($fresh->needs_attention || str_contains($event, 'attention')) ? 'risk' : ($isAssignment ? 'assignment' : ($event === 'task.comment' ? 'comment' : 'update'));
        $mentionIds = collect($meta['mention_user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        app(NotificationService::class)->notifyTaskParticipants(
            $fresh,
            $isAssignment ? 'Task assigned: '.$fresh->title : $this->notificationTitle($fresh, $event),
            $description,
            $type,
            $actor,
            [],
            $mentionIds,
        );

        if ($mentionIds) {
            $mentionText = trim((string) ($meta['mention_text'] ?? $description));
            app(NotificationService::class)->notifyMentionedUsers(
                $mentionIds,
                $actor->name.' mentioned you in '.$fresh->title,
                $mentionText,
                $fresh->job()->first(),
                $fresh,
                $actor,
            );
        }
    }

    private function notificationTitle(Task $task, string $event): string
    {
        return match ($event) {
            'task.comment' => 'New comment on '.$task->title,
            'task.checklist_added', 'task.checklist_updated', 'task.checklist_deleted' => 'Checklist updated: '.$task->title,
            'task.document_uploaded', 'task.document_linked', 'task.document_deleted' => 'Task document updated: '.$task->title,
            default => 'Task updated: '.$task->title,
        };
    }

    private function changeDescription(string $label, mixed $old, mixed $new): string
    {
        $format = static fn ($value) => $value === null || $value === '' ? '—' : (is_bool($value) ? ($value ? 'Yes' : 'No') : (string) $value);
        return $label.' changed from '.$format($old).' to '.$format($new);
    }
}
