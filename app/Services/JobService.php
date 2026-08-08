<?php

namespace App\Services;

use App\Models\Department;
use App\Models\FlowJob;
use App\Models\FlowJobItem;
use App\Models\FlowJobMember;
use App\Models\FlowJobPhaseHistory;
use App\Models\FlowTaskChecklistItem;
use App\Models\FlowTaskComment;
use App\Models\MasterRecord;
use App\Models\TaskPackItem;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Support\JobDetailPresenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobService
{
    public const INACTIVE_STATUSES = ['Inactive', 'Cancelled'];

    public function visibleQuery(User $user): Builder
    {
        return app(AccessControlService::class)->applyJobScope(FlowJob::query(), $user, 'jobs');
    }

    public function activeQuery(User $user): Builder
    {
        return $this->visibleQuery($user)
            ->whereHas('client', fn ($client) => $client->where('is_active', true))
            ->whereNull('completed_at')
            ->whereNotIn('status', self::INACTIVE_STATUSES);
    }

    public function filteredQuery(User $user, array $filters): Builder
    {
        $quick = (string) ($filters['quick'] ?? 'all');

        return $this->visibleQuery($user)
            ->whereHas('client', fn ($client) => $client->where('is_active', true))
            ->when($quick !== 'completed', fn ($q) => $q->whereNull('completed_at'))
            ->when($quick !== 'completed' && empty($filters['status']), fn ($q) => $q->whereNotIn('status', self::INACTIVE_STATUSES))
            ->when($quick === 'completed', fn ($q) => $q->whereNotNull('completed_at'))
            ->when($quick === 'attention', fn ($q) => $q->where(fn ($x) => $x->where('needs_attention', true)->orWhereIn('health', ['Needs Attention','At Risk','Delayed','Blocked'])))
            ->when($quick === 'due_week', fn ($q) => $q->whereBetween('delivery_date', [app(WorkspaceSettingsService::class)->localToday(), app(WorkspaceSettingsService::class)->localToday()->addDays(7)]))
            ->when($quick === 'waiting', fn ($q) => $q->whereHas('tasks', fn ($t) => $t->where('status', 'like', 'Waiting%')->whereNull('completed_at')))
            ->when($quick === 'invoice', fn ($q) => $q->where(fn ($x) => $x->where('commercial_value', '<=', 0)->orWhereHas('phase', fn ($p) => $p->where('short_name', 'Invoice'))))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($x) use ($search) {
                    $x->where('job_number', 'like', "%{$search}%")
                        ->orWhere('order_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('product', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['phase'] ?? null, fn ($q, $v) => $q->where(function ($phaseQuery) use ($v) {
                $phaseQuery->where('source_workflow_phase_id', $v)
                    ->orWhere(function ($legacy) use ($v) {
                        $legacy->whereNull('source_workflow_phase_id')->where('workflow_phase_id', $v);
                    });
            }))
            ->when($filters['health'] ?? null, fn ($q, $v) => $q->where('health', $v))
            ->when($filters['client'] ?? null, fn ($q, $v) => $q->where('client_id', $v))
            ->when($filters['owner'] ?? null, fn ($q, $v) => $q->where('owner_id', $v))
            ->when($filters['priority'] ?? null, fn ($q, $v) => $q->where('priority', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when(($filters['delivery'] ?? null) === 'week', fn ($q) => $q->whereBetween('delivery_date', [app(WorkspaceSettingsService::class)->localToday(), app(WorkspaceSettingsService::class)->localToday()->addDays(7)]))
            ->when(($filters['delivery'] ?? null) === 'overdue', fn ($q) => $q->where('delivery_date', '<', app(WorkspaceSettingsService::class)->localToday()->toDateString())->whereNull('completed_at'))
            ->when(($filters['delivery'] ?? null) === 'none', fn ($q) => $q->whereNull('delivery_date'))
            ->when(($filters['invoice'] ?? null) === 'pending', fn ($q) => $q->where('commercial_value', '<=', 0))
            ->when(($filters['invoice'] ?? null) === 'draft', fn ($q) => $q->where('commercial_value', '>', 0))
            ->latest('id');
    }

    public function filteredIds(User $user, array $filters): Collection
    {
        return $this->filteredQuery($user, $filters)
            ->reorder('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();
    }

    public function paginate(User $user, array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->filteredQuery($user, $filters)->reorder();
        match ($filters['sort'] ?? 'updated_desc') {
            'due_asc' => $query->orderByRaw('delivery_date is null, delivery_date asc')->orderByDesc('id'),
            'priority_desc' => $query->orderByRaw("case priority when 'Urgent' then 4 when 'High' then 3 when 'Medium' then 2 when 'Normal' then 2 when 'Low' then 1 else 0 end desc")->orderByDesc('updated_at'),
            default => $query->orderByDesc('updated_at')->orderByDesc('id'),
        };

        return $query
            ->select([
                'flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.order_number', 'flow_jobs.client_id',
                'flow_jobs.workflow_phase_id', 'flow_jobs.owner_id', 'flow_jobs.coordinator_id',
                'flow_jobs.title', 'flow_jobs.product', 'flow_jobs.quantity', 'flow_jobs.next_action',
                'flow_jobs.status', 'flow_jobs.health', 'flow_jobs.priority', 'flow_jobs.progress',
                'flow_jobs.delivery_date', 'flow_jobs.commercial_value', 'flow_jobs.currency',
                'flow_jobs.needs_attention', 'flow_jobs.completed_at', 'flow_jobs.updated_at',
            ])
            ->with([
                'client:id,name',
                'phase:id,name,short_name,sequence',
                'owner:id,name,profile_image_path',
                'coordinator:id,name,profile_image_path',
                'members:id,flow_job_id,user_id',
                'tasks' => fn ($query) => app(AccessControlService::class)
                    ->applyTaskScope($query, $user)
                    ->select(['tasks.id', 'tasks.flow_job_id', 'tasks.workflow_phase_id', 'tasks.assignee_id', 'tasks.title', 'tasks.status', 'tasks.due_date', 'tasks.completed_at'])
                    ->whereNull('completed_at')
                    ->where('status', '!=', 'Completed')
                    ->whereHas('job', fn ($job) => $job->whereColumn('flow_jobs.workflow_phase_id', 'tasks.workflow_phase_id'))
                    ->with(['assignee:id,name,profile_image_path', 'phase:id,name,sequence'])
                    ->orderByRaw('due_date is null, due_date asc'),
            ])
            ->withCount('items')
            ->paginate($perPage);
    }


    /**
     * Performance-oriented Orders list query used by the prototype-faithful
     * list page. It renders the first page on the server, selects only fields
     * visible in the list and eager-loads only the compact relations needed by
     * each row. Completed Orders remain visible; cancelled/inactive records do
     * not appear in the operational Orders list.
     */
    public function paginateOrders(User $user, string $search = '', int $perPage = 25): LengthAwarePaginator
    {
        $search = trim($search);
        $tokens = collect(preg_split('/\s+/', $search) ?: [])
            ->filter()
            ->take(6)
            ->values();

        $query = $this->visibleQuery($user)
            ->whereNotIn('flow_jobs.status', self::INACTIVE_STATUSES);

        foreach ($tokens as $token) {
            $token = (string) $token;
            $legacyToken = preg_replace('/^ORDER-/i', 'JOB-', $token) ?: $token;
            $like = '%'.$token.'%';
            $legacyLike = '%'.$legacyToken.'%';

            $query->where(function (Builder $match) use ($like, $legacyLike) {
                $match->where('flow_jobs.job_number', 'like', $like)
                    ->orWhere('flow_jobs.job_number', 'like', $legacyLike)
                    ->orWhere('flow_jobs.order_number', 'like', $like)
                    ->orWhere('flow_jobs.title', 'like', $like)
                    ->orWhere('flow_jobs.product', 'like', $like)
                    ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $like))
                    ->orWhereHas('owner', fn (Builder $owner) => $owner->where('name', 'like', $like))
                    ->orWhereHas('items', fn (Builder $item) => $item
                        ->where('product_name', 'like', $like)
                        ->orWhere('category_name', 'like', $like))
                    ->orWhereHas('sourceInquiry', fn (Builder $inquiry) => $inquiry
                        ->where('inquiry_number', 'like', $like)
                        ->orWhere('reference_number', 'like', $like)
                        ->orWhere('subject', 'like', $like))
                    ->orWhereHas('createdActivity.user', fn (Builder $creator) => $creator->where('name', 'like', $like));
            });
        }

        return $query
            ->reorder()
            ->orderByDesc('flow_jobs.created_at')
            ->orderByDesc('flow_jobs.id')
            ->select([
                'flow_jobs.id', 'flow_jobs.job_number', 'flow_jobs.order_number',
                'flow_jobs.client_id', 'flow_jobs.workflow_phase_id', 'flow_jobs.owner_id', 'flow_jobs.source_inquiry_id',
                'flow_jobs.title', 'flow_jobs.product', 'flow_jobs.quantity',
                'flow_jobs.status', 'flow_jobs.health', 'flow_jobs.priority',
                'flow_jobs.progress', 'flow_jobs.delivery_date', 'flow_jobs.needs_attention',
                'flow_jobs.completed_at', 'flow_jobs.created_at',
            ])
            ->with([
                'client:id,name',
                'sourceInquiry:id,inquiry_number,reference_number',
                'phase:id,name,short_name,sequence',
                'owner:id,name,profile_image_path',
                'items:id,flow_job_id,product_name,category_name,quantity,sort_order',
                'createdActivity' => fn ($activity) => $activity->select([
                    'activities.id',
                    'activities.subject_type',
                    'activities.subject_id',
                    'activities.user_id',
                    'activities.created_at',
                ]),
                'createdActivity.user:id,name,profile_image_path',
            ])
            ->paginate(max(1, min($perPage, 50)));
    }

    public function summaryCounts(User $user): array
    {
        $today = app(WorkspaceSettingsService::class)->localToday()->format('Y-m-d');
        $weekEnd = app(WorkspaceSettingsService::class)->localToday()->copy()->addDays(7)->format('Y-m-d');
        $inactive = self::INACTIVE_STATUSES;

        $row = $this->visibleQuery($user)
            ->reorder()
            ->selectRaw("sum(case when flow_jobs.completed_at is null and flow_jobs.status not in (?, ?) then 1 else 0 end) as active_count", $inactive)
            ->selectRaw("sum(case when flow_jobs.completed_at is null and flow_jobs.status not in (?, ?) and (flow_jobs.needs_attention = 1 or flow_jobs.health in ('Needs Attention','At Risk','Delayed','Blocked')) then 1 else 0 end) as attention_count", $inactive)
            ->selectRaw("sum(case when flow_jobs.completed_at is null and flow_jobs.status not in (?, ?) and flow_jobs.delivery_date between ? and ? then 1 else 0 end) as week_count", [...$inactive, $today, $weekEnd])
            ->selectRaw("sum(case when flow_jobs.completed_at is null and flow_jobs.status not in (?, ?) and exists (select 1 from tasks where tasks.flow_job_id = flow_jobs.id and tasks.status like 'Waiting%' and tasks.completed_at is null and tasks.deleted_at is null) then 1 else 0 end) as waiting_count", $inactive)
            ->selectRaw("sum(case when flow_jobs.completed_at is null and flow_jobs.status not in (?, ?) and (flow_jobs.commercial_value <= 0 or exists (select 1 from workflow_phases where workflow_phases.id = flow_jobs.workflow_phase_id and workflow_phases.short_name = 'Invoice')) then 1 else 0 end) as invoice_count", $inactive)
            ->selectRaw("sum(case when flow_jobs.completed_at is not null then 1 else 0 end) as completed_count")
            ->first();

        return [
            'all' => (int) ($row?->active_count ?? 0),
            'attention' => (int) ($row?->attention_count ?? 0),
            'week' => (int) ($row?->week_count ?? 0),
            'waiting' => (int) ($row?->waiting_count ?? 0),
            'invoice' => (int) ($row?->invoice_count ?? 0),
            'completed' => (int) ($row?->completed_count ?? 0),
        ];
    }

    public function findVisible(User $user, int $id): FlowJob
    {
        return $this->visibleQuery($user)->with([
            'client',
            'workflow.phases.taskPack.items.defaultAssignee',
            'workflow.phases.taskPack.items.defaultDepartment',
            'workflow.phases.taskPack.items.priority',
            'workflow.phases.taskPack.items.documentCategory',
            'phase.taskPack.items.documentCategory',
            'phase.documentCategory',
            'startedFromPhase','owner','coordinator','items','members.user',
            'phaseHistories.phase','phaseHistories.actor',
            'tasks' => fn ($q) => app(AccessControlService::class)->applyTaskScope($q, $user)->with(['assignee','phase','template','documentCategory','setupTemplate.documentCategory','checklistItems','comments.user','documents']),
            'documents.uploader','activities.user',
        ])->findOrFail($id);
    }

    public function create(array $data, User $actor): FlowJob
    {
        abort_unless(app(AccessControlService::class)->can($actor, 'jobs', 'create'), 403);
        $canAssign = app(AccessControlService::class)->can($actor, 'jobs', 'assign');
        if (!$canAssign) {
            abort_unless((int) ($data['owner_id'] ?? $actor->id) === (int) $actor->id, 403);
            abort_unless((int) ($data['coordinator_id'] ?? $actor->id) === (int) $actor->id, 403);
        }

        return DB::transaction(function () use ($data, $actor) {
            $workflow = Workflow::query()
                ->where('is_snapshot', false)
                ->where('is_active', true)
                ->with('phases.taskPack.items.defaultAssignee', 'phases.taskPack.items.defaultDepartment', 'phases.taskPack.items.priority', 'phases.taskPack.items.documentCategory')
                ->findOrFail($data['workflow_id']);
            $phase = $workflow->phases->firstWhere('id', (int) $data['workflow_phase_id']);
            abort_unless($phase && $phase->is_active && $phase->allow_job_start, 422, 'Selected starting phase is not allowed.');

            $next = (int) FlowJob::withTrashed()->max('id') + 126;
            $draft = (bool) ($data['draft'] ?? false);
            $job = FlowJob::create([
                'job_number' => 'ORDER-'.app(WorkspaceSettingsService::class)->localNow()->format('Y').'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT),
                'client_id' => $data['client_id'],
                'workflow_id' => $workflow->id,
                'source_workflow_id' => $workflow->id,
                'workflow_phase_id' => $phase->id,
                'source_workflow_phase_id' => $phase->id,
                'started_from_phase_id' => $phase->id,
                'owner_id' => $data['owner_id'] ?: $actor->id,
                'coordinator_id' => $data['coordinator_id'] ?: $actor->id,
                'title' => $data['title'],
                'product' => $data['product'],
                'category' => $data['category'] ?? null,
                'quantity' => $data['quantity'] ?? 0,
                'delivery_date' => $data['delivery_date'] ?? null,
                'priority' => $data['priority'] ?? 'Medium',
                'description' => $data['description'] ?? null,
                'status' => $draft ? 'Draft' : 'New',
                'health' => 'On Track',
                'progress' => 0,
                'next_action' => $draft ? 'Complete draft and activate workflow' : ($phase->entry_condition ?: $phase->entry_rule),
                'start_handling' => $data['start_handling'] ?? 'Normal start',
                'start_reason' => $data['start_reason'] ?? null,
            ]);

            $items = collect($data['items'] ?? [])->filter(fn ($item) => filled($item['product'] ?? null))->values();
            if ($items->isEmpty()) {
                $items = collect([['product' => $job->product, 'category' => $job->category, 'quantity' => $job->quantity]]);
            }
            foreach ($items as $sort => $item) {
                FlowJobItem::create([
                    'flow_job_id' => $job->id,
                    'product_name' => $item['product'] ?? null,
                    'category_name' => $item['category'] ?? null,
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'sort_order' => $sort,
                ]);
            }

            $this->ensureMembers($job);

            // Freeze the selected Workflow, all phases and Task Packs for this
            // Job before any operational history/tasks are created. From this
            // point the Job no longer depends on editable setup records.
            $job = app(JobWorkflowSnapshotService::class)->snapshot($job, $workflow->id);
            $snapshotPhase = $job->phase()->firstOrFail();

            FlowJobPhaseHistory::create([
                'flow_job_id' => $job->id,
                'workflow_phase_id' => $snapshotPhase->id,
                'changed_by' => $actor->id,
                'phase_owner_id' => $job->coordinator_id,
                'target_date' => $job->delivery_date,
                'health_override' => $job->health,
                'status' => $draft ? 'planned' : 'active',
                'entered_at' => $draft ? null : now(),
            ]);

            // Create every Task from every snapshotted phase immediately,
            // including future phases. Draft Jobs keep those Tasks in a
            // Not Started state until the Job is activated.
            $this->syncWorkflowTasks($job, $draft ? null : $actor, true);
            if (!$draft) {
                $this->recalculateProgress($job);
            }

            $job->activities()->create([
                'user_id' => $actor->id,
                'event' => $draft ? 'job.draft_saved' : 'job.created',
                'description' => $draft ? 'Job saved as draft' : 'Job created at '.$snapshotPhase->name,
            ]);

            $mentionIds = app(MentionService::class)->userIdsFromText((string) $job->description);
            if (!$draft || $mentionIds) {
                $jobId = $job->id;
                DB::afterCommit(function () use ($jobId, $actor, $draft, $mentionIds) {
                    $fresh = FlowJob::with(['client','phase','members','tasks'])->find($jobId);
                    if (!$fresh) return;

                    if (!$draft) app(NotificationService::class)->notifyJobAssigned($fresh, $actor, [], $mentionIds);

                    if ($mentionIds) {
                        app(NotificationService::class)->notifyMentionedUsers(
                            $mentionIds,
                            $actor->name.' mentioned you in '.$fresh->job_number,
                            (string) $fresh->description,
                            $fresh,
                            null,
                            $actor,
                        );
                    }
                });
            }

            return $job->refresh();
        });
    }

    public function updateDeliveryDate(FlowJob $job, ?string $deliveryDate, User $actor): FlowJob
    {
        $this->assertEditable($job, $actor);
        $job->update(['delivery_date' => $deliveryDate ?: null]);
        $job->phaseHistories()->whereNull('completed_at')->update(['target_date' => $deliveryDate ?: null]);
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.delivery_date_updated',
            'description' => $deliveryDate ? 'Delivery date changed to '.$deliveryDate : 'Delivery date cleared',
        ]);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order delivery date updated', $job->displayOrderNumber().' · '.($deliveryDate ? 'Delivery '.$deliveryDate : 'Delivery date cleared'), 'update', $actor);
        return $job->refresh();
    }

    public function updateOwner(FlowJob $job, ?int $ownerId, User $actor): FlowJob
    {
        abort_unless(app(AccessControlService::class)->canAssignJob($actor, $job), 403);
        $job->update(['owner_id' => $ownerId]);
        $this->ensureMembers($job);
        $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.owner_updated', 'description' => 'Order owner updated']);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order owner updated', $job->job_number.' · Owner is now '.($job->owner?->name ?? 'Unassigned'), 'assignment', $actor, array_filter([$ownerId]));
        return $job->refresh();
    }

    public function updateCoordinator(FlowJob $job, ?int $coordinatorId, User $actor): FlowJob
    {
        abort_unless(app(AccessControlService::class)->canAssignJob($actor, $job), 403);
        $job->update(['coordinator_id' => $coordinatorId]);
        $this->ensureMembers($job);
        $job->phaseHistories()->whereNull('completed_at')->update(['phase_owner_id' => $coordinatorId]);
        $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.coordinator_updated', 'description' => 'Order coordinator updated']);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order coordinator updated', $job->job_number.' · Coordinator is now '.($job->coordinator?->name ?? 'Unassigned'), 'assignment', $actor, array_filter([$coordinatorId]));
        return $job->refresh();
    }

    public function updatePriority(FlowJob $job, string $priority, User $actor): FlowJob
    {
        $this->assertEditable($job, $actor);
        $job->update(['priority' => $priority]);
        $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.priority_updated', 'description' => 'Job priority changed to '.$priority]);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order priority updated', $job->job_number.' · Priority changed to '.$priority, 'update', $actor);
        return $job->refresh();
    }

    public function updateHealth(FlowJob $job, string $health, User $actor): FlowJob
    {
        $this->assertEditable($job, $actor);
        $job->update(['health' => $health]);
        $job->phaseHistories()->whereNull('completed_at')->update(['health_override' => $health]);
        $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.health_updated', 'description' => 'Job health changed to '.$health]);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order health updated', $job->job_number.' · Health changed to '.$health, in_array($health, ['Needs Attention','At Risk','Delayed','Blocked'], true) ? 'risk' : 'update', $actor);
        return $job->refresh();
    }

    public function deactivate(FlowJob $job, User $actor): FlowJob
    {
        $this->assertStatusEditable($job, $actor);
        $old = $job->status;
        $job->update(['status' => 'Inactive']);
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.deactivated',
            'description' => 'Order deactivated'.($old && $old !== 'Inactive' ? ' from '.$old : ''),
        ]);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order deactivated', $job->displayOrderNumber().' · '.$job->title, 'update', $actor);
        return $job->refresh();
    }

    public function cancel(FlowJob $job, User $actor): FlowJob
    {
        $this->assertStatusEditable($job, $actor);
        DB::transaction(function () use ($job, $actor) {
            $job->tasks()->whereNull('completed_at')->update(['status' => 'Cancelled']);
            $job->update(['status' => 'Cancelled']);
            $job->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.cancelled',
                'description' => 'Order cancelled',
            ]);
        });
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order cancelled', $job->displayOrderNumber().' · '.$job->title, 'update', $actor);
        return $job->refresh();
    }

    public function delete(FlowJob $job, User $actor): void
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($actor, 'jobs', 'delete'), 403);
        abort_unless($this->visibleQuery($actor)->whereKey($job->id)->exists(), 404);

        // Notify participants before the soft delete so record-scope checks can
        // still resolve the Job for each recipient.
        app(NotificationService::class)->notifyJobParticipants($job->fresh(), 'Order deleted', $job->displayOrderNumber().' · '.$job->title, 'update', $actor);
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.deleted',
            'description' => 'Order deleted',
        ]);
        $job->delete();
        app(DashboardService::class)->forget($actor->id);
        app(ReportService::class)->forget($actor->id);
    }

    public function updateTextField(FlowJob $job, string $field, ?string $value, User $actor): FlowJob
    {
        $this->assertEditable($job, $actor);
        abort_unless(in_array($field, ['title', 'description'], true), 422, 'This Job field cannot be edited inline.');

        $value = trim((string) $value);
        if ($field === 'title') {
            abort_if($value === '', 422, 'Job title is required.');
            abort_if(mb_strlen($value) > 255, 422, 'Job title is too long.');
        }

        $storedValue = $field === 'description' && $value === '' ? null : $value;
        $mentionIds = $field === 'description'
            ? app(MentionService::class)->userIdsFromText($storedValue)
            : [];

        $job->update([$field => $storedValue]);
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.'.$field.'_updated',
            'description' => $field === 'title' ? 'Job title updated' : 'Job description updated',
            'meta' => $mentionIds ? ['mention_user_ids' => $mentionIds] : null,
        ]);

        $fresh = $job->refresh();
        app(NotificationService::class)->notifyJobParticipants(
            $fresh,
            'Job details updated',
            $fresh->job_number.' · '.($field === 'title' ? 'Job name changed' : 'Description updated'),
            'update',
            $actor,
            [],
            $mentionIds,
        );

        if ($mentionIds) {
            app(NotificationService::class)->notifyMentionedUsers(
                $mentionIds,
                $actor->name.' mentioned you in '.$fresh->job_number,
                (string) $fresh->description,
                $fresh,
                null,
                $actor,
            );
        }

        return $fresh;
    }

    public function updateItem(FlowJob $job, FlowJobItem $item, string $field, mixed $value, User $actor): FlowJobItem
    {
        $this->assertEditable($job, $actor);
        abort_unless((int) $item->flow_job_id === (int) $job->id, 404);
        abort_unless(in_array($field, ['category_name', 'product_name', 'quantity'], true), 422, 'This Job item field cannot be edited inline.');

        $wasDraft = blank($item->product_name);
        $originalCategory = (string) ($item->category_name ?? '');

        if ($field === 'quantity') {
            $value = max(1, (int) $value);
        } else {
            $value = trim((string) $value);
            abort_if($value === '', 422, $field === 'category_name' ? 'Product category is required.' : 'Product name is required.');
        }

        $item->update([$field => $value]);

        // Category and product are a dependent pair. A real category change
        // always clears the previous product so the user explicitly chooses a
        // product from the newly selected category. This keeps the inline UI
        // deterministic and prevents stale category/product combinations.
        if ($field === 'category_name' && (string) $value !== $originalCategory && filled($item->product_name)) {
            $item->update(['product_name' => null]);
        }

        $this->syncItemSummary($job);
        $item = $item->refresh();

        // A newly inserted blank row is only a UI draft. Do not generate a
        // product-update activity/notification until the user actually chooses
        // a product; the Notifications page still receives the normal event
        // once the new row becomes a real product line.
        if ($wasDraft && blank($item->product_name)) {
            return $item;
        }

        if ($wasDraft && $field === 'product_name') {
            $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.product_added', 'description' => 'Product added to Order']);
            app(NotificationService::class)->notifyJobParticipants(
                $job->refresh(),
                'Product added to Order',
                $job->displayOrderNumber().' · '.$item->product_name.' · '.number_format($item->quantity).' units',
                'update',
                $actor,
            );
            return $item;
        }

        $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.product_updated', 'description' => 'Product and quantity details updated']);
        app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Order product updated', $job->job_number.' · Product/category/quantity changed', 'update', $actor);
        return $item;
    }

    public function addItem(FlowJob $job, string $category, string $product, int $quantity, User $actor): FlowJobItem
    {
        $this->assertEditable($job, $actor);
        $category = trim($category);
        $product = trim($product);

        // Older Jobs can still rely on the summary columns without a persisted
        // flow_job_items row. Preserve that existing line before inserting the
        // new blank draft so Add product never erases legacy product data.
        if (!$job->items()->exists() && filled($job->product)) {
            FlowJobItem::create([
                'flow_job_id' => $job->id,
                'category_name' => $job->category,
                'product_name' => $job->product,
                'quantity' => max(1, (int) $job->quantity),
                'sort_order' => 0,
            ]);
        }

        $item = FlowJobItem::create([
            'flow_job_id' => $job->id,
            'category_name' => $category !== '' ? $category : null,
            'product_name' => $product !== '' ? $product : null,
            'quantity' => max(1, $quantity),
            'sort_order' => ((int) $job->items()->max('sort_order')) + 1,
        ]);

        $this->syncItemSummary($job);

        // Blank rows are drafts opened by “Add product”; wait until a product
        // is selected before recording the normal Product added notification.
        if ($product !== '') {
            $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.product_added', 'description' => 'Product added to Order']);
            app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Product added to Order', $job->displayOrderNumber().' · '.$item->product_name.' · '.number_format($item->quantity).' units', 'update', $actor);
        }

        return $item->refresh();
    }

    public function removeItem(FlowJob $job, FlowJobItem $item, User $actor): void
    {
        $this->assertEditable($job, $actor);
        abort_unless((int) $item->flow_job_id === (int) $job->id, 404);
        abort_if($job->items()->count() <= 1, 422, 'A Job must keep at least one product.');
        $wasDraft = blank($item->product_name);
        $item->delete();
        $this->syncItemSummary($job);

        if (!$wasDraft) {
            $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.product_removed', 'description' => 'Product removed from Order']);
            app(NotificationService::class)->notifyJobParticipants($job->refresh(), 'Product removed from Order', $job->job_number.' · Product list updated', 'update', $actor);
        }
    }

    private function syncItemSummary(FlowJob $job): void
    {
        $items = $job->items()->orderBy('sort_order')->get();
        $completeItems = $items->filter(fn (FlowJobItem $row) => filled($row->product_name))->values();
        $first = $completeItems->first();

        $job->update([
            'product' => $first?->product_name,
            'category' => $first?->category_name,
            'quantity' => (int) $completeItems->sum('quantity'),
        ]);
    }

    public function syncWorkflowTasks(FlowJob $job, ?User $actor = null, bool $includeDraft = false): void
    {
        $job->loadMissing([
            'workflow.phases.taskPack.items.defaultAssignee',
            'workflow.phases.taskPack.items.defaultDepartment',
            'workflow.phases.taskPack.items.priority',
            'workflow.phases.taskPack.items.documentCategory',
            'phaseHistories','phase','items',
        ]);

        if (!$job->items()->exists() && filled($job->product)) {
            FlowJobItem::create([
                'flow_job_id' => $job->id,
                'product_name' => $job->product,
                'category_name' => $job->category,
                'quantity' => max(1, (int) $job->quantity),
                'sort_order' => 0,
            ]);
        }

        if ($job->status === 'Draft' && !$includeDraft) {
            return;
        }

        foreach ($job->workflow->phases as $phase) {
            $this->syncPhaseTaskPack($job, $phase, false, $actor);
        }
    }

    public function recalculateProgress(FlowJob $job): int
    {
        $job->loadMissing('workflow.phases.taskPack.items', 'tasks.setupTemplate', 'tasks.template');
        if ($job->completed_at || $job->status === 'Completed') {
            if ((int) $job->progress !== 100) $job->update(['progress' => 100]);
            return 100;
        }

        $phases = $job->workflow->phases->sortBy('sequence')->values();
        if ($phases->isEmpty()) {
            $job->update(['progress' => 0]);
            return 0;
        }

        $currentSequence = (int) ($job->phase?->sequence ?? 1);
        $score = 0.0;
        foreach ($phases as $phase) {
            if ((int) $phase->sequence < $currentSequence) {
                $score += 100;
                continue;
            }

            $tasks = JobDetailPresenter::phaseTasks($job, $phase);
            if ($tasks->isEmpty()) continue;
            $completed = $tasks->filter(fn ($task) => $task->completed_at || $task->status === 'Completed')->count();
            $score += ($completed / max(1, $tasks->count())) * 100;
        }

        $progress = max(0, min(99, (int) round($score / $phases->count())));
        if ((int) $job->progress !== $progress) $job->update(['progress' => $progress]);
        return $progress;
    }

    public function maybeAutoAdvance(FlowJob $job, User $actor): void
    {
        $job = $this->findVisible($actor, $job->id);
        if (!$job->phase?->auto_advance_on_ready) return;
        if (JobDetailPresenter::blockers($job)->isEmpty()) {
            $this->completePhase($job, $actor, true);
        }
    }

    public function completePhase(FlowJob $job, User $actor, bool $automatic = false): FlowJob
    {
        if (!$automatic) $this->assertStatusEditable($job, $actor);
        return DB::transaction(function () use ($job, $actor) {
            $this->syncWorkflowTasks($job, $actor);
            $job = $this->findVisible($actor, $job->id);
            $current = $job->phase;
            abort_unless($current, 422, 'The Job does not have a current workflow phase.');

            $blockers = JobDetailPresenter::blockers($job);
            if ($blockers->isNotEmpty()) {
                $firstBlocker = $blockers->first();
                abort(422, data_get($firstBlocker, 'description')
                    ?: data_get($firstBlocker, 'label')
                    ?: 'Complete the required Task Pack work before moving to the next phase.');
            }

            $next = $job->workflow->phases->firstWhere('sequence', $current->sequence + 1);
            FlowJobPhaseHistory::where('flow_job_id', $job->id)
                ->where('workflow_phase_id', $current->id)
                ->whereNull('completed_at')
                ->update(['status' => 'completed', 'completed_at' => now()]);

            if (!$next) {
                $job->update(['status' => 'Completed', 'health' => 'Completed', 'progress' => 100, 'next_action' => null, 'completed_at' => now()]);
                $job->activities()->create(['user_id' => $actor->id, 'event' => 'job.completed', 'description' => 'Workflow completed']);
                $jobId = $job->id;
                DB::afterCommit(function () use ($jobId, $actor) {
                    $fresh = FlowJob::with(['members','tasks'])->find($jobId);
                    if ($fresh) app(NotificationService::class)->notifyJobParticipants($fresh, 'Job completed', $fresh->job_number.' · Workflow completed', 'update', $actor);
                });
                return $job->refresh();
            }

            $isCompletedPhase = $next->short_name === 'Completed';
            $job->update([
                'workflow_phase_id' => $next->id,
                'source_workflow_phase_id' => $next->source_workflow_phase_id ?: $next->id,
                'status' => $isCompletedPhase ? 'Completed' : 'In Progress',
                'health' => $isCompletedPhase ? 'Completed' : 'On Track',
                'next_action' => $next->entry_condition ?: $next->entry_rule,
                'completed_at' => $isCompletedPhase ? now() : null,
            ]);

            $this->activateTaskPack($job, $next, $actor);
            FlowJobPhaseHistory::updateOrCreate(
                ['flow_job_id' => $job->id, 'workflow_phase_id' => $next->id],
                ['changed_by' => $actor->id, 'phase_owner_id' => $job->coordinator_id, 'target_date' => $job->delivery_date, 'health_override' => $job->health, 'status' => $isCompletedPhase ? 'completed' : 'active', 'entered_at' => now(), 'completed_at' => $isCompletedPhase ? now() : null]
            );

            $job->activities()->create(['user_id' => $actor->id, 'event' => 'phase.activated', 'description' => $next->name.' activated']);
            $jobId = $job->id;
            $nextName = $next->name;
            DB::afterCommit(function () use ($jobId, $nextName, $actor) {
                $fresh = FlowJob::with(['members','tasks'])->find($jobId);
                if ($fresh) app(NotificationService::class)->notifyJobParticipants($fresh, 'Job moved to '.$nextName, $fresh->job_number.' · Current phase is now '.$nextName, 'update', $actor);
            });
            $this->recalculateProgress($job->refresh());
            return $job->refresh();
        });
    }

    public function moveToPhase(FlowJob $job, int $phaseId, User $actor): FlowJob
    {
        $this->assertStatusEditable($job, $actor);
        $job->load('phase', 'workflow.phases');
        $target = $job->workflow->phases->firstWhere('id', $phaseId)
            ?: $job->workflow->phases->firstWhere('source_workflow_phase_id', $phaseId);
        abort_unless($target, 422, 'Invalid workflow phase.');
        if ($target->sequence === $job->phase->sequence + 1) return $this->completePhase($job, $actor);
        abort_if($target->sequence > $job->phase->sequence + 1, 422, 'Complete required phase controls before skipping ahead.');
        $job->update([
            'workflow_phase_id' => $target->id,
            'source_workflow_phase_id' => $target->source_workflow_phase_id ?: $target->id,
            'status' => $target->short_name === 'Completed' ? 'Completed' : 'In Progress',
        ]);
        $this->activateTaskPack($job, $target, $actor);
        $this->recalculateProgress($job->refresh());
        return $job->refresh();
    }

    private function activateTaskPack(FlowJob $job, WorkflowPhase $phase, User $actor): void
    {
        $this->syncPhaseTaskPack($job, $phase, true, $actor);
    }

    private function syncPhaseTaskPack(FlowJob $job, WorkflowPhase $phase, bool $activate = false, ?User $actor = null): void
    {
        $phase->loadMissing('taskPack.items.defaultAssignee', 'taskPack.items.defaultDepartment', 'taskPack.items.priority', 'taskPack.items.documentCategory');
        if (!$phase->taskPack) return;

        // Older FlowTrack data stored a phase-level required document before
        // Task Pack items became the single source of truth. If a mapped Task
        // Pack has no document requirement at all, migrate that legacy value
        // onto the most likely Task Pack item once, then all runtime logic
        // continues to read only from Task Pack/task data.
        if ($this->restoreLegacyTaskPackDocumentRequirement($phase)) {
            $phase->unsetRelation('taskPack');
            $phase->load('taskPack.items.defaultAssignee', 'taskPack.items.defaultDepartment', 'taskPack.items.priority', 'taskPack.items.documentCategory');
        }

        $currentSequence = (int) ($job->phase?->sequence ?? $job->workflow_phase_id);
        $isDraft = $job->status === 'Draft';
        $isCurrent = !$isDraft && ((int) $phase->id === (int) $job->workflow_phase_id || $activate);
        $isPast = !$isDraft && (int) $phase->sequence < $currentSequence;

        foreach ($phase->taskPack->items as $template) {
            $assignee = $template->defaultAssignee;
            if (!$assignee && $template->defaultDepartment) {
                $legacyDepartmentId = Department::where('code', $template->defaultDepartment->code)->value('id');
                if ($legacyDepartmentId) {
                    $assignee = User::query()->where('is_active', true)->where('department_id', $legacyDepartmentId)->orderBy('id')->first();
                }
            }

            $assigneeId = $assignee?->id;
            $priority = $template->priority?->name ?: $job->priority;
            $dueDate = null;
            if ($isCurrent) {
                $dueDate = app(WorkspaceSettingsService::class)->localToday()->addDays(max(0, (int) $template->due_offset_days));
                if ($job->delivery_date && $dueDate->gt($job->delivery_date)) $dueDate = $job->delivery_date->copy();
            }

            $defaultStatus = $isPast ? 'Completed' : ($isCurrent ? 'Ready' : 'Not Started');
            $task = Task::firstOrCreate([
                'flow_job_id' => $job->id,
                'workflow_phase_id' => $phase->id,
                'task_pack_task_id' => $template->id,
            ], [
                'task_number' => 'TSK-'.str_pad((string) ((int) Task::withTrashed()->max('id') + 301), 5, '0', STR_PAD_LEFT),
                'assignee_id' => $assigneeId,
                'setup_assignee_id' => $assigneeId,
                'document_category_id' => $template->document_category_id,
                'document_requirement_source' => $template->document_category_id ? 'task_pack' : null,
                'title' => $template->title,
                'description' => $template->description,
                'status' => $defaultStatus,
                'priority' => $priority,
                'progress' => $isPast ? 100 : 0,
                'start_date' => $isPast || $isCurrent ? app(WorkspaceSettingsService::class)->localToday() : null,
                'due_date' => $dueDate,
                'completed_at' => $isPast ? now() : null,
            ]);

            $becameActive = $task->wasRecentlyCreated && $isCurrent;
            $changes = [];
            if ($isCurrent && $task->status === 'Not Started') {
                $becameActive = true;
                $changes['status'] = 'Ready';
                $changes['due_date'] = $task->due_date ?: $dueDate;
            }
            // Task Pack assignment is the source of truth for generated tasks.
            // Existing tasks created by the old coordinator fallback are corrected
            // when the Task Pack now has an explicit default assignee. A manual
            // reassignment is preserved because its assignee differs from the
            // stored setup_assignee_id.
            // Store the resolved Task Pack assignee (explicit user first,
            // otherwise the Task Pack's default department resolution) so the
            // same initial owner is shown everywhere in the system.
            $templateAssigneeId = $assigneeId ? (int) $assigneeId : null;
            $previousSetupAssigneeId = $task->setup_assignee_id ? (int) $task->setup_assignee_id : null;
            $followsSetup = !$task->assignee_id
                || ($previousSetupAssigneeId && (int) $task->assignee_id === $previousSetupAssigneeId)
                || (!$previousSetupAssigneeId && (int) $task->assignee_id === (int) ($job->coordinator_id ?: 0));

            if ($followsSetup && (int) ($task->assignee_id ?: 0) !== (int) ($templateAssigneeId ?: 0)) {
                $changes['assignee_id'] = $templateAssigneeId;
            }
            if ($previousSetupAssigneeId !== $templateAssigneeId) {
                $changes['setup_assignee_id'] = $templateAssigneeId;
            }
            if ((int) ($task->document_category_id ?: 0) !== (int) ($template->document_category_id ?: 0)) {
                $changes['document_category_id'] = $template->document_category_id ?: null;
                $changes['document_requirement_source'] = $template->document_category_id ? 'task_pack' : null;
            }
            if (!$task->description && $template->description) $changes['description'] = $template->description;
            if ($isCurrent && !$task->start_date) $changes['start_date'] = app(WorkspaceSettingsService::class)->localToday();
            if ($changes) $task->update($changes);

            FlowTaskComment::firstOrCreate(['flow_task_id' => $task->id, 'body' => 'Task created from the configured phase Task Pack.'], ['user_id' => $job->coordinator_id]);
            if ($task->assignee_id) {
                FlowJobMember::firstOrCreate(['flow_job_id' => $job->id, 'user_id' => $task->assignee_id], ['access_level' => 'member', 'can_manage_tasks' => false, 'can_upload_documents' => true, 'can_view_financials' => false]);
            }
            if ($actor && $becameActive && $task->assignee_id) {
                $taskId = $task->id;
                DB::afterCommit(function () use ($taskId, $actor) {
                    $freshTask = Task::with(['job','phase','assignee'])->find($taskId);
                    if ($freshTask) app(NotificationService::class)->notifyTaskAssigned($freshTask, $actor);
                });
            }
        }
    }


    private function restoreLegacyTaskPackDocumentRequirement(WorkflowPhase $phase): bool
    {
        if (!$phase->task_pack_id || !Schema::hasTable('task_pack_items')) return false;

        $items = TaskPackItem::query()
            ->where('task_pack_id', $phase->task_pack_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        if ($items->isEmpty() || $items->contains(fn ($item) => filled($item->document_category_id))) return false;

        $documentCategoryId = app(TaskPackService::class)->resolveLegacyDocumentCategoryId(
            $phase->document_category_id ?? null,
            Schema::hasColumn('workflow_phases', 'required_document') ? ($phase->required_document ?? null) : null
        );
        if (!$documentCategoryId) return false;

        $document = MasterRecord::query()
            ->whereKey($documentCategoryId)
            ->where('type', 'document_category')
            ->first();
        if (!$document) return false;

        $keywords = collect(preg_split('/[^a-z0-9]+/i', strtolower($document->name)))
            ->filter(fn ($word) => strlen($word) >= 3 && !in_array($word, ['document','file','required'], true))
            ->values();

        $ranked = $items
            ->map(function ($item) use ($keywords) {
                $title = strtolower((string) $item->title);
                $score = $item->is_required ? 2 : 0;
                foreach ($keywords as $word) if (str_contains($title, $word)) $score += 8;
                foreach (['upload','document','file','attach','submit','quotation','invoice','approval','confirmation','order','po'] as $hint) {
                    if (str_contains($title, $hint)) $score += 2;
                }
                return ['item' => $item, 'score' => $score];
            })
            ->sortByDesc(fn ($row) => $row['score']);
        $candidate = data_get($ranked->first(), 'item') ?: $items->first();

        if (!$candidate) return false;

        $candidate->update(['document_category_id' => $document->id]);
        Task::query()->where('task_pack_task_id', $candidate->id)->update([
            'document_category_id' => $document->id,
            'document_requirement_source' => 'task_pack',
        ]);

        return true;
    }

    private function assertEditable(FlowJob $job, User $actor): void
    {
        abort_unless(app(AccessControlService::class)->canEditJob($actor, $job), 403);
    }

    private function assertStatusEditable(FlowJob $job, User $actor): void
    {
        abort_unless(
            app(AccessControlService::class)->canChangeJobStatus($actor, $job),
            403,
            'Only the assigned Job owner or an Admin/Super Admin can change the Job status.'
        );
    }

    private function ensureMembers(FlowJob $job): void
    {
        foreach (collect([$job->owner_id, $job->coordinator_id])->filter()->unique() as $memberId) {
            FlowJobMember::updateOrCreate(
                ['flow_job_id' => $job->id, 'user_id' => $memberId],
                ['access_level' => $memberId === $job->owner_id ? 'lead' : 'member', 'can_manage_tasks' => $memberId === $job->owner_id, 'can_upload_documents' => true, 'can_view_financials' => $memberId === $job->owner_id]
            );
        }
    }
}
