<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\FlowJob;
use App\Models\FlowNotification;
use App\Models\Document;
use App\Models\Inquiry;
use App\Models\InquiryDocument;
use App\Models\InquiryItem;
use App\Models\InquiryTask;
use App\Models\InquiryTaskComment;
use App\Models\TaskPack;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowTemplate;
use App\Support\UserLocalTime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InquiryService
{
    public const WORKING_STATUSES = ['In Progress', 'Waiting for Client', 'Waiting for Supplier', 'On Hold'];
    public const FINAL_STATUSES = ['Converted', 'Dead'];
    public const AUTO_READY_STATUS = 'Ready';
    public const AUTO_IN_PROGRESS_STATUS = 'In Progress';
    public const AUTO_COMPLETED_STATUS = 'Completed';

    public function workspaceId(): int
    {
        return app(SetupContext::class)->workspaceId();
    }

    /**
     * Inquiry working statuses are workspace Master Data. Draft, Ready for Decision,
     * Converted, and Dead remain system lifecycle states and are not user-managed options.
     */
    public function inquiryStatusOptions(): Collection
    {
        return app(MasterDataService::class)
            ->active('inquiry_status')
            ->pluck('name')
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();
    }

    public function defaultInquiryStatus(): string
    {
        $statuses = $this->inquiryStatusOptions();
        $preferred = $statuses->first(fn (string $status) => strcasecmp($status, 'In Progress') === 0);
        $status = $preferred ?: $statuses->first();

        if (!$status) {
            throw ValidationException::withMessages([
                'status' => 'Add at least one active Inquiry Status in Master Data before creating or activating an Inquiry.',
            ]);
        }

        return (string) $status;
    }

    public function visibleQuery(User $user): Builder
    {
        $access = app(AccessControlService::class);
        $query = Inquiry::query()->where('workspace_id', $this->workspaceId());

        if (!$access->can($user, 'inquiries', 'view')) return $query->whereRaw('1 = 0');

        return match ($access->scope($user, 'inquiries')) {
            'all_records' => $query,
            'none' => $query->whereRaw('1 = 0'),
            'own_records' => $query->where(fn (Builder $scope) => $scope
                ->where('owner_id', $user->id)
                ->orWhereHas('tasks', fn (Builder $task) => $task->where('assignee_id', $user->id))),
            'department' => $user->department_id
                ? $query->where(fn (Builder $scope) => $scope
                    ->whereHas('owner', fn (Builder $owner) => $owner->where('department_id', $user->department_id))
                    ->orWhereHas('tasks.assignee', fn (Builder $assignee) => $assignee->where('department_id', $user->department_id)))
                : $query->whereRaw('1 = 0'),
            default => $query->where(fn (Builder $scope) => $scope
                ->where('owner_id', $user->id)
                ->orWhereHas('tasks', fn (Builder $task) => $task->where('assignee_id', $user->id))),
        };
    }

    public function canEdit(User $user, Inquiry $inquiry): bool
    {
        $access = app(AccessControlService::class);
        if ($access->isAdministrator($user)) return true;
        if (!$access->can($user, 'inquiries', 'edit')) return false;
        if (!$this->visibleQuery($user)->whereKey($inquiry->id)->exists()) return false;
        if ($access->canEditAll($user, 'inquiries')) return true;

        return (int) $inquiry->owner_id === (int) $user->id
            || $inquiry->tasks()->where('assignee_id', $user->id)->exists();
    }

    /**
     * Authorization for an Inquiry already loaded through visibleQuery().
     * Keeping the visibility check out of detail render avoids repeating the
     * same EXISTS scope query on every Livewire refresh.
     */
    public function canEditVisible(User $user, Inquiry $inquiry): bool
    {
        $access = app(AccessControlService::class);
        if ($access->isAdministrator($user)) return true;
        if (!$access->can($user, 'inquiries', 'edit')) return false;
        if ($access->canEditAll($user, 'inquiries')) return true;
        if ((int) $inquiry->owner_id === (int) $user->id) return true;

        if ($inquiry->relationLoaded('tasks')) {
            return $inquiry->tasks->contains(fn (InquiryTask $task) => (int) $task->assignee_id === (int) $user->id);
        }

        return $inquiry->tasks()->where('assignee_id', $user->id)->exists();
    }

    public function paginate(User $user, array $filters, int $perPage = 20, string $pageName = 'inquiryPage'): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $quick = (string) ($filters['quick'] ?? 'all');

        $query = $this->visibleQuery($user)
            ->when($quick === 'active', fn (Builder $q) => $q->whereNull('result')->where('status', '!=', 'Draft'))
            ->when($quick === 'converted', fn (Builder $q) => $q->where('result', 'converted'))
            ->when($quick === 'dead', fn (Builder $q) => $q->where('result', 'dead'))
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $match) use ($search): void {
                    $like = '%'.$search.'%';
                    $match->where('inquiry_number', 'like', $like)
                        ->orWhere('subject', 'like', $like)
                        ->orWhere('reference_number', 'like', $like)
                        ->orWhereHas('creator', fn (Builder $creator) => $creator->where('name', 'like', $like))
                        ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $like))
                        ->orWhereHas('items', fn (Builder $item) => $item->where('item_name', 'like', $like)->orWhere('category', 'like', $like))
                        ->orWhereHas('tasks', fn (Builder $task) => $task
                            ->where('title', 'like', $like)
                            ->orWhereHas('assignee', fn (Builder $assignee) => $assignee->where('name', 'like', $like)));
                });
            });

        $currentTask = InquiryTask::query()
            ->select('title')
            ->whereColumn('inquiry_tasks.inquiry_id', 'inquiries.id')
            ->whereNull('inquiry_tasks.deleted_at')
            ->whereNull('inquiry_tasks.completed_at')
            ->orderBy('sequence')
            ->limit(1);
        $currentTaskAssignee = InquiryTask::query()
            ->select('assignee_id')
            ->whereColumn('inquiry_tasks.inquiry_id', 'inquiries.id')
            ->whereNull('inquiry_tasks.deleted_at')
            ->whereNull('inquiry_tasks.completed_at')
            ->orderBy('sequence')
            ->limit(1);
        $currentTaskDue = InquiryTask::query()
            ->select('due_date')
            ->whereColumn('inquiry_tasks.inquiry_id', 'inquiries.id')
            ->whereNull('inquiry_tasks.deleted_at')
            ->whereNull('inquiry_tasks.completed_at')
            ->orderBy('sequence')
            ->limit(1);
        $inquiryStartedAt = InquiryTask::query()
            ->select('started_at')
            ->whereColumn('inquiry_tasks.inquiry_id', 'inquiries.id')
            ->whereNull('inquiry_tasks.deleted_at')
            ->whereNotNull('started_at')
            ->orderBy('started_at')
            ->limit(1);
        $firstItem = InquiryItem::query()
            ->select('item_name')
            ->whereColumn('inquiry_items.inquiry_id', 'inquiries.id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(1);

        return $query
            ->reorder()
            ->orderByDesc('inquiries.updated_at')
            ->orderByDesc('inquiries.id')
            ->select([
                'inquiries.id', 'inquiries.inquiry_number', 'inquiries.client_id', 'inquiries.owner_id', 'inquiries.created_by',
                'inquiries.subject', 'inquiries.received_date', 'inquiries.status', 'inquiries.result',
                'inquiries.converted_job_id', 'inquiries.created_at', 'inquiries.updated_at',
            ])
            ->selectSub($currentTask, 'current_task_title')
            ->selectSub($currentTaskAssignee, 'current_task_assignee_id')
            ->selectSub($currentTaskDue, 'current_task_due_date')
            ->selectSub($inquiryStartedAt, 'inquiry_started_at')
            ->selectSub($firstItem, 'first_item_name')
            ->with(['client:id,name', 'creator:id,name', 'convertedJob:id,job_number,order_number'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn (Builder $task) => $task->whereNotNull('completed_at'),
            ])
            ->paginate(max(1, min(50, $perPage)), ['*'], $pageName);
    }

    public function listRows(LengthAwarePaginator $paginator, User $user): Collection
    {
        $assigneeIds = collect($paginator->items())
            ->pluck('current_task_assignee_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $assignees = $assigneeIds->isEmpty()
            ? collect()
            : User::query()->whereIn('id', $assigneeIds)->get(['id', 'name', 'profile_image_path'])->keyBy('id');

        return collect($paginator->items())->map(function (Inquiry $inquiry) use ($assignees): array {
            $assignee = $assignees->get((int) $inquiry->current_task_assignee_id);
            $total = (int) $inquiry->tasks_count;
            $done = (int) $inquiry->completed_tasks_count;
            $status = match (true) {
                $inquiry->result === 'converted' => 'Converted',
                $inquiry->result === 'dead' => 'Closed',
                (string) $inquiry->status === 'Draft' => 'Draft',
                $total > 0 && $done === $total => self::AUTO_COMPLETED_STATUS,
                $done > 0 || filled($inquiry->inquiry_started_at) => self::AUTO_IN_PROGRESS_STATUS,
                default => self::AUTO_READY_STATUS,
            };
            $progressPercent = $total > 0 ? max(0, min(100, (int) round(($done / $total) * 100))) : 0;

            return [
                'id' => (int) $inquiry->id,
                'number' => (string) $inquiry->inquiry_number,
                'createdBy' => (string) ($inquiry->creator?->name ?: 'System'),
                'createdDate' => UserLocalTime::format($inquiry->created_at, 'M j, Y'),
                'createdTime' => UserLocalTime::format($inquiry->created_at, 'g:i A'),
                'title' => (string) $inquiry->subject,
                'titlePreview' => Str::words((string) $inquiry->subject, 12, '...'),
                'client' => (string) ($inquiry->client?->name ?: 'No client'),
                'item' => blank($inquiry->first_item_name) ? null : (string) $inquiry->first_item_name,
                'currentTask' => (string) ($inquiry->current_task_title ?: ($done === $total && $total > 0 ? 'Completed' : 'No active task')),
                'taskCaption' => $done === $total && $total > 0 ? 'Workflow tasks finished' : 'Task '.min($total, $done + 1).' of '.$total,
                'progress' => $done,
                'total' => $total,
                'progressPercent' => $progressPercent,
                'assignee' => (string) ($assignee?->name ?: 'Unassigned'),
                'assigneeAvatar' => $assignee?->profile_image_path,
                'due' => $inquiry->current_task_due_date ? date('M j', strtotime((string) $inquiry->current_task_due_date)) : '—',
                'startedDate' => UserLocalTime::format($inquiry->inquiry_started_at, 'M j, Y'),
                'startedTime' => UserLocalTime::format($inquiry->inquiry_started_at, 'g:i A'),
                'status' => $status,
            ];
        })->values();
    }

    public function metrics(User $user): array
    {
        $base = $this->visibleQuery($user);
        $summary = (clone $base)->reorder()->selectRaw("SUM(CASE WHEN result IS NULL AND status <> 'Draft' THEN 1 ELSE 0 END) AS active_count")
            ->selectRaw("SUM(CASE WHEN result = 'converted' THEN 1 ELSE 0 END) AS converted_count")
            ->selectRaw("SUM(CASE WHEN result = 'dead' THEN 1 ELSE 0 END) AS dead_count")
            ->first();

        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $dueToday = InquiryTask::query()
            ->whereIn('inquiry_id', (clone $base)->where('inquiries.status', '!=', 'Draft')->select('inquiries.id'))
            ->whereNull('completed_at')
            ->whereDate('due_date', $today)
            ->count();

        return [
            'active' => (int) ($summary?->active_count ?? 0),
            'converted' => (int) ($summary?->converted_count ?? 0),
            'dead' => (int) ($summary?->dead_count ?? 0),
            'dueToday' => (int) $dueToday,
        ];
    }

    public function findVisible(User $user, int $id, array $with = []): Inquiry
    {
        return $this->visibleQuery($user)->with($with)->findOrFail($id);
    }

    public function create(array $data, User $actor, bool $draft = false): Inquiry
    {
        abort_unless(app(AccessControlService::class)->can($actor, 'inquiries', 'create'), 403);

        return DB::transaction(function () use ($data, $actor, $draft): Inquiry {
            $inquiry = Inquiry::create([
                'workspace_id' => $this->workspaceId(),
                'inquiry_number' => $this->nextNumber(),
                'client_id' => (int) $data['client_id'],
                'owner_id' => (int) (($data['owner_id'] ?? null) ?: $actor->id),
                'created_by' => $actor->id,
                'source_task_pack_id' => ($data['source_task_pack_id'] ?? null) ?: null,
                'source_workflow_template_id' => ($data['source_workflow_template_id'] ?? null) ?: null,
                'reference_number' => blank($data['reference_number'] ?? null) ? null : trim((string) $data['reference_number']),
                'client_contact' => blank($data['client_contact'] ?? null) ? null : trim((string) $data['client_contact']),
                'received_date' => $data['received_date'],
                'request_source' => blank($data['request_source'] ?? null) ? null : $data['request_source'],
                'subject' => trim((string) $data['subject']),
                'requirement_notes' => app(RichTextService::class)->normalize($data['requirement_notes'] ?? null, 10000, 'requirement_notes'),
                'target_price' => filled($data['target_price'] ?? null) ? $data['target_price'] : null,
                'currency' => ($data['currency'] ?? null) ?: 'USD',
                'required_delivery_date' => ($data['required_delivery_date'] ?? null) ?: null,
                'priority' => ($data['priority'] ?? null) ?: 'Medium',
                'initial_follow_up_date' => ($data['initial_follow_up_date'] ?? null) ?: null,
                'status' => $draft ? 'Draft' : self::AUTO_READY_STATUS,
            ]);

            foreach (array_values($data['items'] ?? []) as $index => $item) {
                InquiryItem::create([
                    'inquiry_id' => $inquiry->id,
                    'category' => ($item['category'] ?? null) ?: null,
                    'item_name' => trim((string) $item['name']),
                    'quantity' => $item['quantity'],
                    'unit' => ($item['unit'] ?? null) ?: 'pcs',
                    'notes' => blank($item['notes'] ?? null) ? null : trim((string) $item['notes']),
                    'sort_order' => $index,
                ]);
            }

            foreach (array_values($data['tasks']) as $index => $task) {
                InquiryTask::create([
                    'inquiry_id' => $inquiry->id,
                    'source_task_pack_item_id' => ($task['source_id'] ?? null) ?: null,
                    'assignee_id' => ($task['assignee_id'] ?? null) ?: null,
                    'title' => trim((string) $task['name']),
                    'description' => app(RichTextService::class)->normalize($task['description'] ?? null, 10000, 'description'),
                    'sequence' => $index + 1,
                    'due_date' => ($task['due_date'] ?? null) ?: null,
                    'status' => 'Waiting',
                    'started_at' => null,
                    'requires_submission' => (bool) ($task['requires_submission'] ?? false),
                    'submission_label' => blank($task['submission_label'] ?? null) ? null : trim((string) $task['submission_label']),
                ]);
            }

            $this->activity($inquiry, $actor, $draft ? 'inquiry.draft_saved' : 'inquiry.created', $draft ? 'Inquiry draft saved' : $inquiry->inquiry_number.' created with '.count($data['tasks']).' taskflow tasks.');

            if (!$draft) {
                $first = $inquiry->tasks()->orderBy('sequence')->first();
                if ($first) $this->notifyTaskAssigned($first, $actor);

                if (filled($inquiry->requirement_notes)) {
                    $this->notifyMentions($inquiry, null, (string) $inquiry->requirement_notes, $actor);
                }

                $inquiry->tasks()
                    ->whereNotNull('description')
                    ->get(['id', 'inquiry_id', 'description'])
                    ->each(function (InquiryTask $task) use ($inquiry, $actor): void {
                        if (filled($task->description)) {
                            $this->notifyMentions($inquiry, $task, (string) $task->description, $actor);
                        }
                    });
            }

            return $inquiry->refresh();
        });
    }

    public function workflowSummary(int $workflowId): array
    {
        $workflow = WorkflowTemplate::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_active', true)
            ->with([
                'phases' => fn ($query) => $query->where('is_active', true)->orderBy('sequence'),
                'phases.taskPack' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('is_snapshot', false)
                    ->withCount('items'),
            ])
            ->findOrFail($workflowId);

        $phases = $workflow->phases
            ->filter(fn ($phase) => $phase->taskPack && (int) $phase->taskPack->items_count > 0);

        return [
            'phases' => $phases->count(),
            'tasks' => $phases->sum(fn ($phase) => (int) $phase->taskPack->items_count),
        ];
    }

    public function workflowRows(int $workflowId, ?string $baseDate = null): array
    {
        $workflow = WorkflowTemplate::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_active', true)
            ->with([
                'phases' => fn ($query) => $query->where('is_active', true)->orderBy('sequence'),
                'phases.taskPack' => fn ($query) => $query->where('is_active', true)->where('is_snapshot', false),
                'phases.taskPack.items.defaultAssignee:id,name',
                'phases.taskPack.items.documentCategory:id,name',
            ])
            ->findOrFail($workflowId);

        $base = $baseDate
            ? \Carbon\Carbon::parse($baseDate)
            : app(WorkspaceSettingsService::class)->localToday();

        return $workflow->phases
            ->flatMap(function ($phase) use ($base) {
                $pack = $phase->taskPack;
                if (!$pack) return collect();

                return $pack->items->map(fn ($item) => [
                    'id' => null,
                    'source_id' => (int) $item->id,
                    'phase_id' => (int) $phase->id,
                    'phase_name' => (string) $phase->name,
                    'phase_sequence' => (int) $phase->sequence,
                    'task_pack_id' => (int) $pack->id,
                    'task_pack_name' => (string) $pack->name,
                    'name' => (string) $item->title,
                    'description' => (string) ($item->description ?: ''),
                    'assignee_id' => $item->default_assignee_id ? (int) $item->default_assignee_id : null,
                    'assignee_name' => (string) ($item->defaultAssignee?->name ?: ''),
                    'due_date' => $base->copy()->addDays(max(0, (int) $item->due_offset_days))->toDateString(),
                    'requires_submission' => (bool) $item->document_category_id,
                    'submission_label' => (string) ($item->documentCategory?->name ?: ''),
                    'state' => 'future',
                ]);
            })
            ->values()
            ->all();
    }

    public function taskPackOptions(): Collection
    {
        return TaskPack::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_snapshot', false)
            ->where('is_active', true)
            ->orderByRaw("CASE WHEN LOWER(name) LIKE '%inquiry%' THEN 0 WHEN LOWER(name) LIKE '%quotation%' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name']);
    }

    public function taskPackRows(int $taskPackId, ?string $baseDate, ?int $fallbackAssigneeId): array
    {
        $pack = TaskPack::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_snapshot', false)
            ->where('is_active', true)
            ->with(['items.defaultAssignee:id,name', 'items.documentCategory:id,name'])
            ->findOrFail($taskPackId);

        $base = $baseDate ? \Carbon\Carbon::parse($baseDate) : app(WorkspaceSettingsService::class)->localToday();
        $fallbackAssigneeName = $fallbackAssigneeId
            ? (string) (User::query()->whereKey($fallbackAssigneeId)->value('name') ?: '')
            : '';

        return $pack->items->map(fn ($item) => [
            'id' => null,
            'source_id' => (int) $item->id,
            'name' => (string) $item->title,
            'description' => (string) ($item->description ?: ''),
            'assignee_id' => (int) ($item->default_assignee_id ?: $fallbackAssigneeId ?: 0) ?: null,
            'assignee_name' => (string) ($item->defaultAssignee?->name ?: $fallbackAssigneeName),
            'due_date' => $base->copy()->addDays(max(0, (int) $item->due_offset_days))->toDateString(),
            'requires_submission' => (bool) $item->document_category_id,
            'submission_label' => (string) ($item->documentCategory?->name ?: ''),
            'state' => 'future',
        ])->values()->all();
    }

    public function updateDetailField(Inquiry $inquiry, string $field, mixed $value, User $actor): Inquiry
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);
        abort_if($inquiry->result, 422, 'A completed Inquiry is locked.');

        if ($field === 'status') {
            return $this->updateStatus($inquiry, (string) $value, $actor);
        }

        abort_unless(in_array($field, ['subject', 'owner_id', 'priority', 'requirement_notes'], true), 422, 'Unsupported Inquiry field.');

        $oldDisplay = '';
        $newDisplay = '';
        $update = [];

        if ($field === 'subject') {
            $subject = trim((string) $value);
            if ($subject === '' || mb_strlen($subject) > 255) {
                throw ValidationException::withMessages(['subject' => 'Inquiry title is required and must be 255 characters or fewer.']);
            }
            $oldDisplay = (string) $inquiry->subject;
            $newDisplay = $subject;
            $update['subject'] = $subject;
        } elseif ($field === 'owner_id') {
            $ownerId = (int) $value;
            $owner = User::query()->where('is_active', true)->find($ownerId);
            if (! $owner) {
                throw ValidationException::withMessages(['owner_id' => 'Select an active user.']);
            }
            $oldDisplay = (string) ($inquiry->owner?->name ?: 'Unassigned');
            $newDisplay = (string) $owner->name;
            $update['owner_id'] = $ownerId;
        } elseif ($field === 'priority') {
            $priority = trim((string) $value);
            $allowed = app(MasterDataService::class)->active('priority')->pluck('name')->map(fn ($name) => trim((string) $name));
            if ($priority === '' || ! $allowed->contains($priority)) {
                throw ValidationException::withMessages(['priority' => 'Select a valid active priority.']);
            }
            $oldDisplay = (string) $inquiry->priority;
            $newDisplay = $priority;
            $update['priority'] = $priority;
        } else {
            $description = app(RichTextService::class)->normalize((string) $value, 10000, 'requirement_notes');
            $oldDisplay = (string) ($inquiry->requirement_notes ?? '');
            $newDisplay = (string) ($description ?? '');
            $update['requirement_notes'] = $description;
        }

        if ($oldDisplay === $newDisplay) return $inquiry->refresh();

        $inquiry->update($update);
        $label = match ($field) {
            'subject' => 'title',
            'owner_id' => 'assignee',
            'priority' => 'priority',
            'requirement_notes' => 'description',
            default => 'field',
        };
        if ($field === 'requirement_notes') {
            $this->activity($inquiry, $actor, 'inquiry.field_updated', 'Inquiry description updated.');
            $this->notifyMentions($inquiry->refresh(), null, $newDisplay, $actor);
        } else {
            $oldActivityDisplay = $oldDisplay !== '' ? $oldDisplay : 'empty';
            $newActivityDisplay = $newDisplay !== '' ? $newDisplay : 'empty';
            $this->activity($inquiry, $actor, 'inquiry.field_updated', 'Inquiry '.$label.' changed from '.$oldActivityDisplay.' to '.$newActivityDisplay.'.');
        }

        return $inquiry->refresh();
    }

    public function updateStatus(Inquiry $inquiry, string $status, User $actor): Inquiry
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);
        abort_if(in_array((string) $inquiry->status, self::FINAL_STATUSES, true) || $inquiry->result, 422, 'A completed Inquiry cannot change working status.');

        // Working lifecycle status is task-driven. The only legacy/manual transition
        // retained here is activating a Draft; after that, task progress owns status.
        if ((string) $inquiry->status === 'Draft') {
            $inquiry->update(['status' => self::AUTO_READY_STATUS]);
            $first = $inquiry->tasks()->whereNull('completed_at')->orderBy('sequence')->first();
            if ($first) {
                $first->update(['status' => 'Waiting', 'started_at' => null]);
                $this->notifyTaskAssigned($first, $actor);
            }
            $this->activity($inquiry, $actor, 'inquiry.status_changed', 'Inquiry activated and is Ready to start.');
        }

        return $this->syncAutomaticStatus($inquiry, $actor);
    }

    public function updateTask(InquiryTask $task, array $data, User $actor): InquiryTask
    {
        $task->loadMissing('inquiry');
        abort_unless($this->canEditTask($actor, $task), 403);
        abort_if($task->completed_at, 422, 'Completed tasks are locked.');

        // Open Inquiry tasks may be prepared independently. Completion is allowed
        // whenever that task itself is explicitly moved to In Progress.
        $isActive = $this->isActiveTask($task);
        $allowedStatuses = array_merge(['Waiting'], self::WORKING_STATUSES);
        $nextStatus = in_array((string) ($data['status'] ?? ''), $allowedStatuses, true)
            ? (string) $data['status']
            : ($isActive ? 'In Progress' : 'Waiting');
        $oldAssigneeId = $task->assignee_id ? (int) $task->assignee_id : null;
        $taskUpdate = [
            'assignee_id' => ($data['assignee_id'] ?? null) ?: null,
            'due_date' => ($data['due_date'] ?? null) ?: null,
            'status' => $nextStatus,
        ];
        if (in_array($nextStatus, self::WORKING_STATUSES, true) && !$task->started_at) {
            $taskUpdate['started_at'] = now();
        }
        $task->update($taskUpdate);
        if ((int) ($task->assignee_id ?? 0) !== (int) ($oldAssigneeId ?? 0)) {
            $this->forgetMyTaskShell($oldAssigneeId);
            $this->notifyTaskAssigned($task, $actor);
        }
        $this->activity($task->inquiry, $actor, 'inquiry.task_updated', $task->title.' updated — '.$task->status.'.', ['inquiry_task_id' => $task->id]);
        $this->syncAutomaticStatus($task->inquiry, $actor);
        return $task->refresh();
    }

    public function updateTaskStatus(InquiryTask $task, string $status, User $actor): InquiryTask
    {
        return $this->updateTask($task, [
            'assignee_id' => $task->assignee_id,
            'due_date' => $task->due_date?->toDateString(),
            'status' => $status,
        ], $actor);
    }

    public function updateTaskDueDate(InquiryTask $task, ?string $date, User $actor): InquiryTask
    {
        $task->loadMissing('inquiry');
        abort_unless($this->canEditTask($actor, $task), 403);
        abort_if($task->completed_at, 422, 'Completed tasks are locked.');
        $task->update(['due_date' => $date ?: null]);
        $this->activity($task->inquiry, $actor, 'inquiry.task_due_changed', $task->title.' due date changed'.($date ? ' to '.$date : '').'.', ['inquiry_task_id' => $task->id]);
        return $task->refresh();
    }

    public function completeTask(InquiryTask $task, User $actor): InquiryTask
    {
        $task->loadMissing('inquiry');
        abort_unless($this->canEditTask($actor, $task), 403);
        abort_if($task->completed_at, 422, 'This task is already completed.');
        abort_unless(strcasecmp(trim((string) $task->status), 'In Progress') === 0, 422, 'Task must be In Progress before completion.');
        if ($task->requires_submission && !$task->documents()->exists()) {
            throw ValidationException::withMessages(['task' => 'Required file must be uploaded before completion.']);
        }

        return DB::transaction(function () use ($task, $actor): InquiryTask {
            $task->update(['status' => 'Completed', 'started_at' => $task->started_at ?: now(), 'completed_at' => now()]);
            $this->forgetMyTaskShell($task->assignee_id ? (int) $task->assignee_id : null);
            $this->activity($task->inquiry, $actor, 'inquiry.task_completed', $task->title.' completed.', ['inquiry_task_id' => $task->id]);

            $remaining = $task->inquiry->tasks()->whereNull('completed_at')->exists();
            if (!$remaining) {
                $this->activity($task->inquiry, $actor, 'inquiry.ready_for_decision', 'All Inquiry taskflow tasks are complete. The Inquiry is now Completed and the final decision is available.');
            }

            $this->syncAutomaticStatus($task->inquiry, $actor);
            return $task->refresh();
        });
    }

    public function appendTask(Inquiry $inquiry, array $data, User $actor): InquiryTask
    {
        abort_unless(app(AccessControlService::class)->isAdministrator($actor), 403);
        abort_if($inquiry->result, 422, 'A completed Inquiry cannot receive another task.');

        return DB::transaction(function () use ($inquiry, $data, $actor): InquiryTask {
            $lockedInquiry = Inquiry::query()->whereKey($inquiry->id)->lockForUpdate()->firstOrFail();
            abort_if($lockedInquiry->result, 422, 'A completed Inquiry cannot receive another task.');

            $lastSequence = (int) InquiryTask::query()
                ->where('inquiry_id', $lockedInquiry->id)
                ->max('sequence');
            $hasOpenTask = InquiryTask::query()
                ->where('inquiry_id', $lockedInquiry->id)
                ->whereNull('completed_at')
                ->exists();

            $task = InquiryTask::create([
                'inquiry_id' => $lockedInquiry->id,
                'source_task_pack_item_id' => null,
                'assignee_id' => ($data['assignee_id'] ?? null) ?: null,
                'title' => trim((string) ($data['name'] ?? '')),
                'description' => app(RichTextService::class)->normalize($data['description'] ?? null, 10000, 'description'),
                'sequence' => $lastSequence + 1,
                'due_date' => ($data['due_date'] ?? null) ?: null,
                'status' => 'Waiting',
                'started_at' => null,
                'requires_submission' => (bool) ($data['requires_submission'] ?? false),
                'submission_label' => blank($data['submission_label'] ?? null) ? null : trim((string) $data['submission_label']),
            ]);

            if (!$hasOpenTask) {
                $this->notifyTaskAssigned($task, $actor);
            }

            $this->activity(
                $lockedInquiry,
                $actor,
                'inquiry.task_added',
                $task->title.' added to the Inquiry taskflow.',
                ['inquiry_task_id' => $task->id],
            );
            if (filled($task->description)) {
                $this->notifyMentions($lockedInquiry, $task, (string) $task->description, $actor);
            }
            $this->syncAutomaticStatus($lockedInquiry, $actor);

            return $task->refresh();
        });
    }

    public function saveWorkflow(Inquiry $inquiry, array $rows, User $actor): void
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);
        abort_if($inquiry->result, 422, 'A completed Inquiry taskflow cannot be changed.');
        if ($rows === []) throw ValidationException::withMessages(['workflow' => 'Inquiry taskflow needs at least one task.']);

        DB::transaction(function () use ($inquiry, $rows, $actor): void {
            $existing = $inquiry->tasks()->get()->keyBy('id');
            $completedIds = $existing->filter(fn (InquiryTask $task) => $task->completed_at !== null)->keys()->map(fn ($id) => (int) $id)->all();
            $rowIds = collect($rows)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();
            $active = $existing->filter(fn (InquiryTask $task) => $task->completed_at === null && !$task->trashed())->sortBy('sequence')->first();
            $activeBeforeId = $active?->id ? (int) $active->id : null;
            $activeBeforeAssigneeId = $active?->assignee_id ? (int) $active->assignee_id : null;

            foreach ($completedIds as $completedId) {
                abort_unless(in_array($completedId, $rowIds, true), 422, 'Completed tasks are locked and cannot be removed.');
            }
            if ($active) {
                abort_unless(in_array((int) $active->id, $rowIds, true), 422, 'The active task cannot be removed.');
            }

            // Completed history and the active task cannot be reordered. Only
            // future tasks may move around each other, matching the workflow UI.
            foreach ($existing->filter(fn (InquiryTask $task) => $task->completed_at !== null || ($active && (int) $task->id === (int) $active->id)) as $lockedTask) {
                $submittedIndex = collect($rows)->search(fn (array $row) => (int) ($row['id'] ?? 0) === (int) $lockedTask->id);
                abort_unless($submittedIndex !== false && ((int) $submittedIndex + 1) === (int) $lockedTask->sequence, 422, 'Completed and active tasks cannot be reordered.');
            }

            // Move current sequences away first so the unique constraint never
            // collides while future tasks are reordered.
            $inquiry->tasks()->update(['sequence' => DB::raw('sequence + 10000')]);

            foreach (array_values($rows) as $index => $row) {
                $id = (int) ($row['id'] ?? 0);
                $task = $id ? $existing->get($id) : null;
                if ($task && $task->completed_at) {
                    $task->restore();
                    $task->update(['sequence' => $index + 1]);
                    continue;
                }

                $payload = [
                    'source_task_pack_item_id' => ($row['source_id'] ?? null) ?: null,
                    'assignee_id' => ($row['assignee_id'] ?? null) ?: null,
                    'title' => trim((string) $row['name']),
                    'description' => app(RichTextService::class)->normalize($row['description'] ?? null, 10000, 'description'),
                    'sequence' => $index + 1,
                    'due_date' => ($row['due_date'] ?? null) ?: null,
                    'requires_submission' => (bool) ($row['requires_submission'] ?? false),
                    'submission_label' => blank($row['submission_label'] ?? null) ? null : trim((string) $row['submission_label']),
                ];

                if ($task) {
                    $task->restore();
                    $task->update($payload);
                } else {
                    InquiryTask::create($payload + [
                        'inquiry_id' => $inquiry->id,
                        'status' => 'Waiting',
                    ]);
                }
            }

            $kept = collect($rows)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();
            $inquiry->tasks()->whereNotIn('id', $kept ?: [0])->whereNull('completed_at')->get()->each->forceDelete();
            $this->normalizeTaskStates($inquiry);
            $activeAfter = $inquiry->tasks()->whereNull('completed_at')->orderBy('sequence')->first();
            if ($activeAfter && ((int) $activeAfter->id !== (int) $activeBeforeId || (int) $activeAfter->assignee_id !== (int) $activeBeforeAssigneeId)) {
                $this->forgetMyTaskShell($activeBeforeAssigneeId);
                $this->notifyTaskAssigned($activeAfter, $actor);
            }
            $this->activity($inquiry, $actor, 'inquiry.workflow_updated', 'Inquiry taskflow updated. It now contains '.count($rows).' tasks.');
        });
    }

    public function upload(Inquiry $inquiry, UploadedFile $file, User $actor, ?InquiryTask $task = null, ?string $note = null): InquiryDocument
    {
        abort_unless($this->canEdit($actor, $inquiry) || ($task && $this->canEditTask($actor, $task)), 403);
        if ($task) {
            abort_unless((int) $task->inquiry_id === (int) $inquiry->id, 422);
            abort_if($task->completed_at, 422, 'Completed tasks are locked.');
            // Future open tasks may receive attachments in advance. Completion
            // remains sequential and is still restricted by completeTask().
        }

        $disk = (string) config('flowtrack.document_disk', 'public');
        $path = $file->store('flowtrack/inquiries/'.$inquiry->id, $disk);
        abort_if(!$path, 500, 'The attachment could not be stored.');

        $document = InquiryDocument::create([
            'inquiry_id' => $inquiry->id,
            'inquiry_task_id' => $task?->id,
            'uploaded_by' => $actor->id,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'note' => filled($note) ? trim((string) $note) : null,
        ]);

        $this->activity(
            $inquiry,
            $actor,
            $task ? 'inquiry.task_document_uploaded' : 'inquiry.document_uploaded',
            $document->name.' uploaded'.($task ? ' to '.$task->title : ' directly to the Inquiry').'.',
            ['inquiry_task_id' => $task?->id, 'inquiry_document_id' => $document->id],
        );

        return $document;
    }

    public function linkExistingDocument(Inquiry $inquiry, Document $source, User $actor): InquiryDocument
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);
        abort_unless(app(AccessControlService::class)->can($actor, 'documents', 'link'), 403);
        abort_unless((int) ($source->client_id ?? 0) === (int) $inquiry->client_id, 403, 'The selected document does not belong to this client.');

        $document = InquiryDocument::create([
            'inquiry_id' => $inquiry->id,
            'inquiry_task_id' => null,
            'uploaded_by' => $actor->id,
            'name' => $source->name,
            'path' => $source->path,
            'mime_type' => $source->mime_type,
            'size' => $source->size,
        ]);

        $this->activity($inquiry, $actor, 'inquiry.document_linked', $document->name.' linked from Documents.', [
            'inquiry_document_id' => $document->id,
            'source_document_id' => $source->id,
        ]);

        return $document;
    }

    public function linkExistingDocumentToTask(InquiryTask $task, Document $source, User $actor, ?string $note = null): InquiryDocument
    {
        $task->loadMissing('inquiry');
        abort_unless($this->canEditTask($actor, $task), 403);
        abort_if($task->completed_at, 422, 'Completed tasks are locked.');
        abort_unless(app(AccessControlService::class)->can($actor, 'documents', 'link'), 403);
        abort_unless((int) ($source->client_id ?? 0) === (int) $task->inquiry->client_id, 403, 'The selected document does not belong to this client.');

        $document = InquiryDocument::create([
            'inquiry_id' => $task->inquiry_id,
            'inquiry_task_id' => $task->id,
            'uploaded_by' => $actor->id,
            'name' => $source->name,
            'path' => $source->path,
            'mime_type' => $source->mime_type,
            'size' => $source->size,
            'note' => filled($note) ? trim((string) $note) : null,
        ]);

        $this->activity(
            $task->inquiry,
            $actor,
            'inquiry.task_document_linked',
            $document->name.' linked to '.$task->title.' from Documents.',
            [
                'inquiry_task_id' => $task->id,
                'inquiry_document_id' => $document->id,
                'source_document_id' => $source->id,
            ],
        );

        return $document;
    }

    public function removeDocument(Inquiry $inquiry, int $documentId, User $actor): void
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);

        $document = $inquiry->documents()->whereKey($documentId)->firstOrFail();
        $path = (string) $document->path;
        $name = (string) $document->name;
        $document->delete();

        if ($path !== ''
            && ! Document::query()->where('path', $path)->exists()
            && ! InquiryDocument::query()->where('path', $path)->exists()) {
            Storage::disk((string) config('flowtrack.document_disk', 'public'))->delete($path);
        }

        $this->activity($inquiry, $actor, 'inquiry.document_removed', $name.' removed from the Inquiry.');
    }

    public function removeTaskDocument(InquiryTask $task, int $documentId, User $actor): void
    {
        $task->loadMissing('inquiry');
        abort_unless($this->canEditTask($actor, $task), 403);
        abort_if($task->completed_at, 422, 'Completed tasks are locked.');

        $document = $task->documents()->whereKey($documentId)->firstOrFail();
        $path = (string) $document->path;
        $name = (string) $document->name;
        $document->delete();

        if ($path !== ''
            && ! Document::query()->where('path', $path)->exists()
            && ! InquiryDocument::query()->where('path', $path)->exists()) {
            Storage::disk((string) config('flowtrack.document_disk', 'public'))->delete($path);
        }

        $this->activity(
            $task->inquiry,
            $actor,
            'inquiry.task_document_removed',
            $name.' removed from '.$task->title.'.',
            ['inquiry_task_id' => $task->id, 'inquiry_document_id' => $documentId],
        );
    }

    public function addInquiryComment(Inquiry $inquiry, string $body, User $actor): Activity
    {
        abort_unless($this->visibleQuery($actor)->whereKey($inquiry->id)->exists(), 403);
        $body = app(RichTextService::class)->normalize($body, 5000, 'inquiryComment');
        abort_if(!$body, 422, 'Comment cannot be empty.');
        $activity = $this->activity($inquiry, $actor, 'inquiry.comment', $body, ['comment' => true]);
        $this->notifyMentions($inquiry, null, $body, $actor);
        return $activity;
    }

    public function addTaskComment(InquiryTask $task, string $body, User $actor): InquiryTaskComment
    {
        $body = app(RichTextService::class)->normalize($body, 5000, 'taskComment');
        abort_if(!$body, 422, 'Comment cannot be empty.');
        $task->loadMissing('inquiry');
        abort_unless($this->visibleQuery($actor)->whereKey($task->inquiry_id)->exists(), 403);
        abort_if(!$task->completed_at && !$this->isActiveTask($task), 422, 'Future task comments stay locked until the task starts.');

        $comment = InquiryTaskComment::create([
            'inquiry_task_id' => $task->id,
            'user_id' => $actor->id,
            'body' => $body,
        ]);
        $this->activity($task->inquiry, $actor, 'inquiry.task_comment', $body, ['inquiry_task_id' => $task->id, 'inquiry_task_comment_id' => $comment->id]);
        $this->notifyMentions($task->inquiry, $task, $body, $actor);
        return $comment;
    }

    public function documentsPage(User $user, Inquiry $inquiry, int $perPage = 50): LengthAwarePaginator
    {
        abort_unless($this->visibleQuery($user)->whereKey($inquiry->id)->exists(), 403);
        return InquiryDocument::query()
            ->where('inquiry_id', $inquiry->id)
            ->with(['task:id,title', 'uploader:id,name'])
            ->latest('id')
            ->paginate(max(1, min(100, $perPage)), ['*'], 'inquiryDocumentsPage');
    }

    public function activityPage(User $user, Inquiry $inquiry, int $perPage = 30, string $tab = 'all'): LengthAwarePaginator
    {
        abort_unless($this->visibleQuery($user)->whereKey($inquiry->id)->exists(), 403);
        $query = Activity::query()
            ->where('subject_type', Inquiry::class)
            ->where('subject_id', $inquiry->id)
            ->with('user:id,name,profile_image_path');

        if ($tab === 'comments') $query->where('event', 'inquiry.comment');
        if ($tab === 'history') $query->where('event', '!=', 'inquiry.comment');

        return $query->latest('id')
            ->paginate(max(1, min(60, $perPage)), ['*'], 'inquiryActivityPage');
    }

    public function findVisibleTask(User $user, int $taskId, array $with = []): InquiryTask
    {
        $task = InquiryTask::query()->with($with)->findOrFail($taskId);
        abort_unless($this->visibleQuery($user)->whereKey($task->inquiry_id)->exists(), 403);
        return $task;
    }

    public function taskDetail(User $user, int $taskId): InquiryTask
    {
        return $this->findVisibleTask($user, $taskId, [
            'inquiry:id,inquiry_number,owner_id,status,result',
            'assignee:id,name,profile_image_path',
            'documents' => fn ($q) => $q->with('uploader:id,name')->limit(50),
            'comments' => fn ($q) => $q->with('user:id,name,profile_image_path')->limit(50),
        ]);
    }

    public function convertToOrder(Inquiry $inquiry, User $actor): FlowJob
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);
        abort_unless(app(AccessControlService::class)->can($actor, 'jobs', 'create'), 403, 'You need Order create access to convert this Inquiry.');
        abort_if($inquiry->result, 422, 'This Inquiry already has a final result.');
        abort_if($inquiry->tasks()->whereNull('completed_at')->exists(), 422, 'Complete every Inquiry taskflow task first.');

        $template = WorkflowTemplate::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->firstOrFail();
        $workflow = Workflow::query()->whereKey($template->id)->where('is_snapshot', false)->where('is_active', true)->firstOrFail();
        $phase = $workflow->phases()->where('is_active', true)->where('allow_job_start', true)->orderBy('sequence')->first();
        abort_unless($phase, 422, 'The default Order workflow has no phase that allows a Job start.');

        $inquiry->loadMissing(['items', 'client']);
        $first = $inquiry->items->first();
        $canAssign = app(AccessControlService::class)->can($actor, 'jobs', 'assign');
        $ownerId = $canAssign && $inquiry->owner_id ? $inquiry->owner_id : $actor->id;

        return DB::transaction(function () use ($inquiry, $actor, $workflow, $phase, $first, $ownerId): FlowJob {
            $job = app(JobService::class)->create([
                'title' => $inquiry->subject,
                'product' => $first?->item_name ?: $inquiry->subject,
                'category' => $first?->category,
                'quantity' => (int) round((float) $inquiry->items->sum('quantity')),
                'items' => $inquiry->items->map(fn (InquiryItem $item) => [
                    'product' => $item->item_name,
                    'category' => $item->category,
                    'quantity' => max(1, (int) round((float) $item->quantity)),
                ])->all(),
                'priority' => $inquiry->priority,
                'client_id' => $inquiry->client_id,
                'workflow_id' => $workflow->id,
                'workflow_phase_id' => $phase->id,
                'owner_id' => $ownerId,
                'coordinator_id' => $ownerId,
                'delivery_date' => $inquiry->required_delivery_date?->toDateString(),
                'description' => app(RichTextService::class)->prependText(
                    $inquiry->reference_number ? 'Source Inquiry: '.$inquiry->inquiry_number.' · Reference '.$inquiry->reference_number : 'Source Inquiry: '.$inquiry->inquiry_number,
                    $inquiry->requirement_notes,
                ),
                'draft' => false,
            ], $actor);

            $job->update(['source_inquiry_id' => $inquiry->id, 'currency' => $inquiry->currency ?: 'USD']);
            $inquiry->update([
                'result' => 'converted',
                'status' => 'Converted',
                'converted_job_id' => $job->id,
                'completed_at' => now(),
            ]);
            $this->activity($inquiry, $actor, 'inquiry.converted', $job->displayOrderNumber().' created from this Inquiry.');
            return $job->refresh();
        });
    }

    public function markDead(Inquiry $inquiry, string $reason, ?string $note, User $actor): Inquiry
    {
        abort_unless($this->canEdit($actor, $inquiry), 403);
        abort_if($inquiry->result, 422, 'This Inquiry already has a final result.');
        abort_if($inquiry->tasks()->whereNull('completed_at')->exists(), 422, 'Complete every Inquiry taskflow task first.');

        $inquiry->update([
            'result' => 'dead',
            'status' => 'Dead',
            'dead_reason' => $reason,
            'dead_note' => blank($note) ? null : trim((string) $note),
            'completed_at' => now(),
        ]);
        $this->activity($inquiry, $actor, 'inquiry.dead', 'Inquiry closed. Reason: '.$reason.'.'.(filled($note) ? ' '.trim((string) $note) : ''));
        return $inquiry->refresh();
    }

    public function isActiveTask(InquiryTask $task): bool
    {
        if ($task->completed_at) return false;
        $firstOpenId = InquiryTask::query()
            ->where('inquiry_id', $task->inquiry_id)
            ->whereNull('completed_at')
            ->orderBy('sequence')
            ->value('id');
        return (int) $firstOpenId === (int) $task->id;
    }

    public function canEditTask(User $user, InquiryTask $task): bool
    {
        $task->loadMissing('inquiry');
        if (app(AccessControlService::class)->isAdministrator($user)) return true;
        if ($this->canEdit($user, $task->inquiry)) return true;
        return (int) $task->assignee_id === (int) $user->id
            && app(AccessControlService::class)->can($user, 'inquiries', 'view');
    }


    public function myTaskGroups(User $user, array $filters, int $limit = 3): Collection
    {
        $access = app(AccessControlService::class);
        if (!$access->can($user, 'inquiries', 'view')) return collect();

        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->copy()->addDay()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();
        $quick = (string) ($filters['quick'] ?? 'all');
        $search = trim((string) ($filters['search'] ?? ''));

        $query = InquiryTask::query()
            ->whereNull('inquiry_tasks.completed_at')
            ->whereIn('inquiry_tasks.inquiry_id', $this->visibleQuery($user)->whereNull('result')->where('inquiries.status', '!=', 'Draft')->select('inquiries.id'))
            ->whereNotExists(function ($earlier): void {
                $earlier->selectRaw('1')
                    ->from('inquiry_tasks as earlier_inquiry_tasks')
                    ->whereColumn('earlier_inquiry_tasks.inquiry_id', 'inquiry_tasks.inquiry_id')
                    ->whereColumn('earlier_inquiry_tasks.sequence', '<', 'inquiry_tasks.sequence')
                    ->whereNull('earlier_inquiry_tasks.completed_at')
                    ->whereNull('earlier_inquiry_tasks.deleted_at');
            });

        if (!$access->isAdministrator($user)) $query->where('inquiry_tasks.assignee_id', $user->id);

        if ($search !== '' && mb_strlen($search) >= 2) {
            $like = '%'.$search.'%';
            $query->where(fn (Builder $match) => $match
                ->where('inquiry_tasks.title', 'like', $like)
                ->orWhereHas('assignee', fn (Builder $assignee) => $assignee->where('name', 'like', $like))
                ->orWhereHas('inquiry', fn (Builder $inquiry) => $inquiry
                    ->where('inquiry_number', 'like', $like)
                    ->orWhere('subject', 'like', $like)
                    ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $like))));
        }

        match ($quick) {
            'attention' => $query->whereRaw("LOWER(inquiry_tasks.status) NOT LIKE 'waiting%'")
                ->where(fn (Builder $q) => $q
                    ->where('inquiry_tasks.due_date', '<=', $weekEnd)
                    ->orWhereHas('inquiry', fn (Builder $inquiry) => $inquiry->whereIn('priority', ['High', 'Urgent', 'Critical']))),
            'overdue' => $query->where('inquiry_tasks.due_date', '<', $todayDate),
            'today' => $query->whereDate('inquiry_tasks.due_date', $todayDate),
            'upcoming' => $query->whereBetween('inquiry_tasks.due_date', [$tomorrow, $weekEnd])->whereRaw("LOWER(inquiry_tasks.status) NOT LIKE 'waiting%'"),
            'waiting' => $query->whereRaw("LOWER(inquiry_tasks.status) LIKE 'waiting%'"),
            'mentions' => $query->whereExists(fn ($notification) => $notification
                ->selectRaw('1')->from('flow_notifications')
                ->whereColumn('flow_notifications.inquiry_task_id', 'inquiry_tasks.id')
                ->where('flow_notifications.user_id', $user->id)
                ->where('flow_notifications.type', 'mention')),
            default => null,
        };

        match ((string) ($filters['sort'] ?? 'action')) {
            'due' => $query->orderByRaw('inquiry_tasks.due_date is null')->orderBy('inquiry_tasks.due_date')->orderBy('inquiry_tasks.id'),
            'job' => $query->orderBy('inquiry_tasks.inquiry_id')->orderBy('inquiry_tasks.sequence'),
            default => $query
                ->orderByRaw("CASE WHEN inquiry_tasks.due_date < ? THEN 0 WHEN inquiry_tasks.due_date = ? THEN 1 ELSE 2 END", [$todayDate, $todayDate])
                ->orderByRaw('inquiry_tasks.due_date is null')->orderBy('inquiry_tasks.due_date')->orderBy('inquiry_tasks.id'),
        };

        $tasks = $query
            ->with([
                'assignee:id,name,profile_image_path',
                'inquiry:id,inquiry_number,client_id,subject,status,priority,updated_at',
                'inquiry.client:id,name',
            ])
            ->limit(max(1, min(6, $limit)))
            ->get(['id', 'inquiry_id', 'assignee_id', 'title', 'status', 'due_date', 'sequence', 'updated_at']);

        $inquiryIds = $tasks->pluck('inquiry_id')->unique()->values();
        $counts = $inquiryIds->isEmpty() ? collect() : InquiryTask::query()
            ->whereIn('inquiry_id', $inquiryIds)
            ->select('inquiry_id')
            ->selectRaw('COUNT(*) AS total_count')
            ->selectRaw('SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_count')
            ->groupBy('inquiry_id')
            ->get()->keyBy('inquiry_id');
        $displayTimezone = app(WorkspaceSettingsService::class)->displayTimezone();

        return $tasks->map(function (InquiryTask $task) use ($counts, $user, $displayTimezone, $todayDate): array {
            $inquiry = $task->inquiry;
            $count = $counts->get($task->inquiry_id);
            $total = (int) ($count?->total_count ?? 0);
            $done = (int) ($count?->completed_count ?? 0);
            $dueDate = $task->due_date?->toDateString();
            $dueTone = 'normal';
            $due = $task->due_date?->format('M j') ?: 'No due date';
            if ($dueDate && $dueDate < $todayDate) { $dueTone = 'overdue'; $due = 'Overdue'; }
            elseif ($dueDate === $todayDate) { $dueTone = 'today'; $due = 'Today'; }
            $flag = $dueTone === 'overdue' ? 'Overdue' : ($dueTone === 'today' ? 'Due Today' : 'No flag');
            $updatedAt = $task->updated_at?->copy()->setTimezone($displayTimezone);

            return [
                'id' => (int) $inquiry->id,
                'number' => (string) $inquiry->inquiry_number,
                'title' => (string) $inquiry->subject,
                'client' => (string) ($inquiry->client?->name ?: 'No client'),
                'stage' => 'Inquiry',
                'health' => (string) ($inquiry->status ?: 'In Progress'),
                'healthTone' => $this->statusTone((string) $inquiry->status),
                'progress' => $total ? (int) round($done / $total * 100) : 0,
                'taskCount' => 1,
                'route' => route('inquiries.index', ['open' => $inquiry->id]),
                'tasks' => collect([[
                    'id' => (int) $task->id,
                    'kind' => 'inquiry',
                    'number' => 'INQ-TASK-'.str_pad((string) $task->id, 5, '0', STR_PAD_LEFT),
                    'title' => (string) $task->title,
                    'phase' => 'Inquiry',
                    'assignee' => (string) ($task->assignee?->name ?: 'Unassigned'),
                    'assigneeId' => $task->assignee_id ? (int) $task->assignee_id : null,
                    'assigneeAvatar' => ($task->assignee?->id && $task->assignee?->profile_image_path)
                        ? route('profile-images.show', ['user' => $task->assignee->id, 'filename' => basename($task->assignee->profile_image_path)], false)
                        : null,
                    'due' => $due,
                    'dueValue' => $dueDate ?: '',
                    'dueDisplay' => $task->due_date?->format('M j, Y') ?? 'Set due date',
                    'dueTone' => $dueTone,
                    'status' => (string) $task->status,
                    'flag' => $flag,
                    'flagTone' => $flag === 'Overdue' ? 'red' : ($flag === 'Due Today' ? 'amber' : 'green'),
                    'updated' => $updatedAt?->diffForHumans() ?: '—',
                    'version' => (string) $task->getRawOriginal('updated_at'),
                    'canEdit' => $this->canEditTask($user, $task),
                    'route' => route('inquiries.index', ['open' => $inquiry->id, 'task' => $task->id]),
                ]]),
            ];
        })->values();
    }

    public function myTaskMetrics(User $user): array
    {
        $access = app(AccessControlService::class);
        if (!$access->can($user, 'inquiries', 'view')) return ['attention'=>0,'overdue'=>0,'today'=>0,'upcoming'=>0,'waiting'=>0,'mentions'=>0];
        $today = app(WorkspaceSettingsService::class)->localToday();
        $todayDate = $today->toDateString();
        $tomorrow = $today->copy()->addDay()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();
        $base = InquiryTask::query()
            ->whereNull('inquiry_tasks.completed_at')
            ->whereIn('inquiry_tasks.inquiry_id', $this->visibleQuery($user)->whereNull('result')->where('inquiries.status', '!=', 'Draft')->select('inquiries.id'))
            ->whereNotExists(function ($earlier): void {
                $earlier->selectRaw('1')->from('inquiry_tasks as earlier_inquiry_tasks')
                    ->whereColumn('earlier_inquiry_tasks.inquiry_id', 'inquiry_tasks.inquiry_id')
                    ->whereColumn('earlier_inquiry_tasks.sequence', '<', 'inquiry_tasks.sequence')
                    ->whereNull('earlier_inquiry_tasks.completed_at')->whereNull('earlier_inquiry_tasks.deleted_at');
            });
        if (!$access->isAdministrator($user)) $base->where('inquiry_tasks.assignee_id', $user->id);
        $row = (clone $base)->selectRaw("SUM(CASE WHEN LOWER(status) NOT LIKE 'waiting%' AND (due_date <= ? OR EXISTS (SELECT 1 FROM inquiries i WHERE i.id=inquiry_tasks.inquiry_id AND i.priority IN ('High','Urgent','Critical'))) THEN 1 ELSE 0 END) attention_count", [$weekEnd])
            ->selectRaw('SUM(CASE WHEN due_date < ? THEN 1 ELSE 0 END) overdue_count', [$todayDate])
            ->selectRaw('SUM(CASE WHEN due_date = ? THEN 1 ELSE 0 END) today_count', [$todayDate])
            ->selectRaw("SUM(CASE WHEN due_date BETWEEN ? AND ? AND LOWER(status) NOT LIKE 'waiting%' THEN 1 ELSE 0 END) upcoming_count", [$tomorrow, $weekEnd])
            ->selectRaw("SUM(CASE WHEN LOWER(status) LIKE 'waiting%' THEN 1 ELSE 0 END) waiting_count")
            ->first();
        $mentions = (clone $base)->whereExists(fn ($notification) => $notification
            ->selectRaw('1')->from('flow_notifications')
            ->whereColumn('flow_notifications.inquiry_task_id', 'inquiry_tasks.id')
            ->where('flow_notifications.user_id', $user->id)->where('flow_notifications.type', 'mention'))->count();
        return [
            'attention'=>(int)($row?->attention_count??0), 'overdue'=>(int)($row?->overdue_count??0),
            'today'=>(int)($row?->today_count??0), 'upcoming'=>(int)($row?->upcoming_count??0),
            'waiting'=>(int)($row?->waiting_count??0), 'mentions'=>(int)$mentions,
        ];
    }

    public function openMyTaskCount(User $user): int
    {
        $access = app(AccessControlService::class);
        if (!$access->can($user, 'inquiries', 'view')) return 0;
        $query = InquiryTask::query()
            ->whereNull('inquiry_tasks.completed_at')
            ->whereIn('inquiry_tasks.inquiry_id', $this->visibleQuery($user)->whereNull('result')->where('inquiries.status', '!=', 'Draft')->select('inquiries.id'))
            ->whereNotExists(function ($earlier): void {
                $earlier->selectRaw('1')->from('inquiry_tasks as earlier_inquiry_tasks')
                    ->whereColumn('earlier_inquiry_tasks.inquiry_id', 'inquiry_tasks.inquiry_id')
                    ->whereColumn('earlier_inquiry_tasks.sequence', '<', 'inquiry_tasks.sequence')
                    ->whereNull('earlier_inquiry_tasks.completed_at')->whereNull('earlier_inquiry_tasks.deleted_at');
            });
        if (!$access->isAdministrator($user)) $query->where('inquiry_tasks.assignee_id', $user->id);
        return $query->count();
    }

    private function statusTone(string $value): string
    {
        $value = strtolower($value);
        if (str_contains($value, 'dead') || str_contains($value, 'blocked')) return 'red';
        if (str_contains($value, 'waiting') || str_contains($value, 'hold') || str_contains($value, 'ready')) return 'amber';
        if (str_contains($value, 'converted') || str_contains($value, 'complete')) return 'green';
        return 'blue';
    }

    private function normalizeTaskStates(Inquiry $inquiry): void
    {
        $open = $inquiry->tasks()->whereNull('completed_at')->orderBy('sequence')->get();
        foreach ($open as $index => $task) {
            if ($index === 0) {
                $task->update([
                    'status' => $task->started_at && in_array((string) $task->status, self::WORKING_STATUSES, true)
                        ? $task->status
                        : 'Waiting',
                ]);
                continue;
            }

            // Future tasks stay queued until the sequence reaches them.
            if (!$task->started_at) $task->update(['status' => 'Waiting']);
        }

        $this->syncAutomaticStatus($inquiry);
    }

    public function syncAutomaticStatus(Inquiry $inquiry, ?User $actor = null): Inquiry
    {
        $inquiry->refresh();
        if ($inquiry->result || (string) $inquiry->status === 'Draft') return $inquiry;

        $tasks = $inquiry->tasks()
            ->get(['id', 'status', 'started_at', 'completed_at']);
        $total = $tasks->count();
        $completed = $tasks->whereNotNull('completed_at')->count();
        $hasStarted = $tasks->contains(fn (InquiryTask $task) => $task->started_at !== null || $task->completed_at !== null);

        $nextStatus = match (true) {
            $total > 0 && $completed === $total => self::AUTO_COMPLETED_STATUS,
            $hasStarted => self::AUTO_IN_PROGRESS_STATUS,
            default => self::AUTO_READY_STATUS,
        };

        $update = ['status' => $nextStatus];
        if ($nextStatus === self::AUTO_COMPLETED_STATUS) {
            $update['completed_at'] = $inquiry->completed_at ?: now();
        } elseif ($inquiry->completed_at && !$inquiry->result) {
            $update['completed_at'] = null;
        }

        $statusChanged = (string) $inquiry->status !== $nextStatus;
        $completedChanged = ($update['completed_at'] ?? null) != $inquiry->completed_at;
        if ($statusChanged || $completedChanged) {
            $inquiry->update($update);
            if ($statusChanged && $actor) {
                $this->activity(
                    $inquiry,
                    $actor,
                    'inquiry.status_auto_changed',
                    'Inquiry status automatically changed to '.$nextStatus.' based on Taskflow progress.',
                );
            }
        }

        return $inquiry->refresh();
    }

    private function activity(Inquiry $inquiry, User $actor, string $event, string $description, array $meta = []): Activity
    {
        return $inquiry->activities()->create([
            'user_id' => $actor->id,
            'event' => $event,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);
    }

    private function notifyTaskAssigned(InquiryTask $task, User $actor): void
    {
        if (!$task->assignee_id) return;
        $assigneeId = (int) $task->assignee_id;
        $this->forgetMyTaskShell($assigneeId);
        if ($assigneeId === (int) $actor->id) return;

        $recipient = User::query()->where('is_active', true)->find($assigneeId);
        if (!$recipient) return;
        $this->createNotification($recipient, $task->inquiry, $task, 'Task assigned: '.$task->title, $task->inquiry->inquiry_number.' · '.($task->due_date?->format('M j, Y') ?: 'No due date'), 'assignment');
    }

    private function forgetMyTaskShell(?int $userId): void
    {
        if (!$userId) return;
        app(ShellDataService::class)->forget($userId);
    }

    private function notifyMentions(Inquiry $inquiry, ?InquiryTask $task, string $body, User $actor): void
    {
        $ids = app(MentionService::class)->userIdsFromText($body);
        if ($ids === []) return;

        app(NotificationService::class)->notifyInquiryMentionedUsers(
            $ids,
            $actor->name.' mentioned you in '.$inquiry->inquiry_number,
            $body,
            $inquiry,
            $task,
            $actor,
        );
    }

    private function createNotification(User $recipient, Inquiry $inquiry, ?InquiryTask $task, string $title, string $message, string $type): void
    {
        FlowNotification::create([
            'user_id' => $recipient->id,
            'flow_job_id' => null,
            'flow_task_id' => null,
            'inquiry_id' => $inquiry->id,
            'inquiry_task_id' => $task?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
        app(ShellDataService::class)->forget((int) $recipient->id);
        app(DashboardService::class)->forget((int) $recipient->id);
    }

    private function nextNumber(): string
    {
        $year = app(WorkspaceSettingsService::class)->localNow()->format('Y');
        $last = Inquiry::withTrashed()
            ->where('workspace_id', $this->workspaceId())
            ->where('inquiry_number', 'like', 'INQ-'.$year.'-%')
            ->orderByDesc('id')
            ->value('inquiry_number');
        $next = 1;
        if ($last && preg_match('/(\d+)$/', $last, $match)) $next = ((int) $match[1]) + 1;
        return 'INQ-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
