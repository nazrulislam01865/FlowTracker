<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\InquiryTask;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\TaskPack;
use App\Models\TaskPackItem;
use App\Models\TaskPackTask;
use App\Models\WorkflowPhase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TaskPackService
{
    public function workspaceId(): int { return app(SetupContext::class)->workspaceId(); }

    public function all()
    {
        return TaskPack::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_snapshot', false)
            ->select(['id', 'workspace_id', 'code', 'name', 'description', 'is_active'])
            ->with([
                'items' => fn ($query) => $query->select([
                    'id', 'task_pack_id', 'title', 'default_assignee_id',
                    'default_department_id', 'priority_id', 'document_category_id',
                    'is_required', 'sort_order',
                ]),
                'items.defaultAssignee:id,name',
                'items.defaultDepartment:id,name',
                'items.priority:id,name',
                'items.documentCategory:id,name',
            ])
            ->orderBy('name')
            ->get();
    }


    public function resolveLegacyDocumentCategoryId(?int $documentCategoryId, ?string $legacyName = null): ?int
    {
        $workspaceId = $this->workspaceId();
        if ($documentCategoryId) {
            $existing = MasterRecord::query()
                ->where('workspace_id', $workspaceId)
                ->where('type', 'document_category')
                ->find($documentCategoryId);
            if ($existing) return (int) $existing->id;
        }

        $legacyName = trim((string) $legacyName);
        if ($legacyName === '') return null;

        $existingId = MasterRecord::query()
            ->where('workspace_id', $workspaceId)
            ->where('type', 'document_category')
            ->where('name', $legacyName)
            ->value('id');
        if ($existingId) return (int) $existingId;

        // Some legacy workflow requirements (for example "Purchase Order")
        // were never present in master_values. Preserve them by promoting the
        // existing configured name into Master Data, then attach it to the
        // mapped Task Pack item.
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $legacyName), 0, 10)) ?: 'DOC';
        $code = $base;
        $suffix = 1;
        while (MasterRecord::query()->where('workspace_id', $workspaceId)->where('type', 'document_category')->where('code', $code)->exists()) {
            $code = substr($base, 0, 8).'-'.$suffix++;
        }

        return (int) MasterRecord::query()->create([
            'workspace_id' => $workspaceId,
            'parent_id' => null,
            'type' => 'document_category',
            'code' => $code,
            'name' => $legacyName,
            'description' => 'Migrated from an existing FlowTrack workflow document requirement.',
            'metadata' => ['source' => 'legacy_workflow_requirement'],
            'status' => 'active',
            'sort_order' => ((int) MasterRecord::query()->where('workspace_id', $workspaceId)->where('type', 'document_category')->max('sort_order')) + 1,
        ])->id;
    }

    public function nextCode(): string
    {
        $next = ((int) TaskPack::where('workspace_id', $this->workspaceId())->where('is_snapshot', false)->max('id')) + 1;
        do {
            $code = 'TPK-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $next++;
        } while (TaskPack::where('workspace_id', $this->workspaceId())->where('code', $code)->exists());

        return $code;
    }

    public function savePackWithItems(array $packData, array $items, ?int $id = null): TaskPack
    {
        $this->assertAction($id ? 'edit' : 'create');
        return DB::transaction(function () use ($packData, $items, $id) {
            $pack = $this->savePack($packData, $id, false);
            $keepIds = [];

            foreach (array_values($items) as $index => $row) {
                $itemId = !empty($row['id']) ? (int) $row['id'] : null;
                if ($itemId && !TaskPackItem::where('task_pack_id', $pack->id)->whereKey($itemId)->exists()) {
                    throw ValidationException::withMessages(['tasks' => 'A Task Pack item no longer belongs to this Task Pack.']);
                }

                $saved = $this->saveItem($pack, [
                    'title' => $row['title'] ?? '',
                    'description' => $row['description'] ?? null,
                    'default_assignee_id' => $row['default_assignee_id'] ?? null,
                    'default_department_id' => $row['default_department_id'] ?? null,
                    'priority_id' => $row['priority_id'] ?? null,
                    'document_category_id' => $row['document_category_id'] ?? null,
                    'due_offset_days' => $row['due_offset_days'] ?? 1,
                    'is_required' => (bool) ($row['is_required'] ?? true),
                    'sort_order' => $index,
                ], $itemId, false);
                $keepIds[] = $saved->id;
            }

            $removed = $pack->items()->when($keepIds, fn ($q) => $q->whereNotIn('id', $keepIds))->pluck('id');
            if ($removed->isNotEmpty()) {
                $user = auth()->user();
                abort_unless($user && app(AccessControlService::class)->can($user, 'taskpacks', 'delete'), 403);
            }
            foreach ($removed as $removedId) {
                $this->deleteItem((int) $removedId, false);
            }

            $this->normalize($pack->id);
            return $pack->fresh(['items.defaultAssignee','items.defaultDepartment','items.priority','items.documentCategory']);
        });
    }

    public function savePack(array $data, ?int $id = null, bool $authorize = true): TaskPack
    {
        if ($authorize) $this->assertAction($id ? 'edit' : 'create');
        $workspaceId = $this->workspaceId();
        $code = strtoupper(trim($data['code']));
        if (TaskPack::where('workspace_id', $workspaceId)->where('code', $code)->when($id, fn ($q) => $q->whereKeyNot($id))->exists()) {
            throw ValidationException::withMessages(['packCode' => 'This Task Pack code already exists.']);
        }
        $payload = [
            'workspace_id' => $workspaceId,
            'code' => $code,
            'name' => trim($data['name']),
            'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
        if (Schema::hasColumn('task_packs', 'slug')) $payload['slug'] = Str::slug($data['name']).'-'.strtolower($code);

        if ($id) {
            $pack = TaskPack::query()
                ->where('workspace_id', $workspaceId)
                ->where('is_snapshot', false)
                ->findOrFail($id);
            $pack->update($payload);
            return $pack->refresh();
        }

        return TaskPack::query()->create($payload + ['is_snapshot' => false]);
    }

    public function togglePack(int $id): void
    {
        $this->assertAction('edit');
        $pack = TaskPack::where('workspace_id', $this->workspaceId())->where('is_snapshot', false)->findOrFail($id);
        $pack->update(['is_active' => !$pack->is_active]);
    }

    /**
     * Resolve Task Pack dependencies only when the user opens the destructive
     * delete dialog. Normal Task Pack rendering remains unchanged and fast.
     */
    public function packDeleteImpact(int $id): array
    {
        $this->assertAction('delete');
        $pack = TaskPack::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('is_snapshot', false)
            ->findOrFail($id);

        $phaseBase = WorkflowPhase::query()
            ->where('task_pack_id', $id)
            ->whereNotNull('workflow_template_id');

        $mappedPhaseCount = (clone $phaseBase)->count();
        $sourceWorkflowIds = (clone $phaseBase)
            ->select('workflow_template_id')
            ->distinct()
            ->pluck('workflow_template_id')
            ->filter()
            ->map(fn ($workflowId) => (int) $workflowId)
            ->values();

        $mappedPhases = (clone $phaseBase)
            ->with(['workflowTemplate:id,name'])
            ->orderBy('workflow_template_id')
            ->orderBy('sequence')
            ->limit(8)
            ->get(['id', 'workflow_template_id', 'name', 'sequence', 'task_pack_id']);

        $jobsBase = FlowJob::withTrashed()
            ->where(function ($query) use ($sourceWorkflowIds) {
                $query->whereIn('source_workflow_id', $sourceWorkflowIds)
                    ->orWhere(function ($legacy) use ($sourceWorkflowIds) {
                        $legacy->whereNull('source_workflow_id')->whereIn('workflow_id', $sourceWorkflowIds);
                    });
            });

        $jobCount = $sourceWorkflowIds->isEmpty() ? 0 : (clone $jobsBase)->count();
        $taskCount = $sourceWorkflowIds->isEmpty()
            ? 0
            : Task::withTrashed()
                ->whereIn('flow_job_id', (clone $jobsBase)->select('id'))
                ->count();

        $jobs = $sourceWorkflowIds->isEmpty()
            ? collect()
            : (clone $jobsBase)
                ->orderBy('job_number')
                ->limit(8)
                ->get(['id', 'job_number', 'title', 'workflow_id', 'source_workflow_id', 'deleted_at']);

        return [
            'id' => (int) $pack->id,
            'name' => (string) $pack->name,
            'mapped_phase_count' => $mappedPhaseCount,
            'mapped_phases' => $mappedPhases->map(fn (WorkflowPhase $phase) => [
                'id' => (int) $phase->id,
                'name' => (string) $phase->name,
                'sequence' => (int) $phase->sequence,
                'workflow_name' => (string) ($phase->workflowTemplate?->name ?: 'Workflow'),
            ])->all(),
            'generated_task_count' => 0,
            'generated_tasks' => [],
            'job_count' => $jobCount,
            'jobs' => $jobs->map(fn (FlowJob $job) => [
                'id' => (int) $job->id,
                'job_number' => (string) $job->displayOrderNumber(),
                'title' => (string) $job->title,
                'trashed' => $job->deleted_at !== null,
                'already_snapshotted' => $job->source_workflow_id !== null
                    && (int) $job->workflow_id !== (int) $job->source_workflow_id,
            ])->all(),
            'task_count' => $taskCount,
        ];
    }

    public function deletePack(int $id): array
    {
        $this->assertAction('delete');
        $pack = TaskPack::where('workspace_id', $this->workspaceId())->where('is_snapshot', false)->findOrFail($id);

        $mappedPhases = WorkflowPhase::query()
            ->where('task_pack_id', $id)
            ->whereNotNull('workflow_template_id')
            ->get(['id', 'workflow_id', 'workflow_template_id']);
        $sourceWorkflowIds = $mappedPhases
            ->map(fn (WorkflowPhase $phase) => (int) ($phase->workflow_template_id ?: $phase->workflow_id))
            ->filter()->unique()->values();

        $legacyJobIds = $sourceWorkflowIds->isEmpty()
            ? []
            : FlowJob::withTrashed()
                ->whereIn('workflow_id', $sourceWorkflowIds)
                ->pluck('id')
                ->map(fn ($jobId) => (int) $jobId)
                ->all();

        $protectedJobs = 0;
        try {
            DB::transaction(function () use ($pack, $id, $legacyJobIds, &$protectedJobs) {
                if ($legacyJobIds) {
                    $protectedJobs = app(JobWorkflowSnapshotService::class)->snapshotJobs($legacyJobIds);
                }

                // Setup phases keep their structure; deleting a reusable Task
                // Pack only clears the setup mapping. Existing Jobs use their
                // own private copied Task Pack and Tasks.
                WorkflowPhase::query()
                    ->where('task_pack_id', $id)
                    ->whereNotNull('workflow_template_id')
                    ->update(['task_pack_id' => null]);

                TaskPackTask::query()->where('task_pack_id', $id)->delete();
                TaskPackItem::query()->where('task_pack_id', $id)->delete();
                $pack->delete();
            });
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'pack' => 'FlowTrack could not safely detach every linked Job. Nothing was deleted. Refresh and try again.',
                ]);
            }
            throw $exception;
        }

        return [
            'pack_name' => (string) $pack->name,
            'job_count' => $protectedJobs,
            'task_count' => 0,
            'mapped_phase_count' => $mappedPhases->count(),
        ];
    }

    public function saveItem(TaskPack $pack, array $data, ?int $id = null, bool $authorize = true): TaskPackItem
    {
        if ($authorize) $this->assertAction('edit');
        abort_if((bool) $pack->is_snapshot, 404);
        return DB::transaction(function () use ($pack, $data, $id) {
            $existingItem = $id ? TaskPackItem::query()->findOrFail($id) : null;
            $previousDefaultAssigneeId = $existingItem?->default_assignee_id ? (int) $existingItem->default_assignee_id : null;

            $sort = array_key_exists('sort_order', $data)
                ? max(0, (int) $data['sort_order'])
                : ($id ? (int) $existingItem->sort_order : ((int) $pack->items()->max('sort_order') + 1));
            $item = TaskPackItem::query()->updateOrCreate(['id' => $id], [
                'task_pack_id' => $pack->id,
                'title' => trim($data['title']),
                'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
                'default_assignee_id' => $data['default_assignee_id'] ?? null,
                'default_department_id' => $data['default_department_id'] ?? null,
                'priority_id' => $data['priority_id'] ?? null,
                'document_category_id' => $data['document_category_id'] ?? null,
                'due_offset_days' => max(0, (int) ($data['due_offset_days'] ?? 1)),
                'is_required' => (bool) ($data['is_required'] ?? true),
                'sort_order' => $sort,
            ]);
            $this->mirrorLegacyItem($item);

            // Task Pack is the single source of truth for required documents.
            // Keep already generated tasks synchronized when this requirement is
            // added, changed or removed from the Task Pack.
            Task::query()->where('task_pack_task_id', $item->id)->update([
                'document_category_id' => $item->document_category_id ?: null,
                'document_requirement_source' => $item->document_category_id ? 'task_pack' : null,
            ]);

            // The Task Pack is also the source of truth for the initial task
            // assignee. Keep generated tasks in sync when the configured
            // assignee changes, while preserving a deliberate manual
            // reassignment made on an individual Job task.
            $this->syncGeneratedTaskAssignees($item->fresh(), $previousDefaultAssigneeId);

            return $item;
        });
    }

    private function syncGeneratedTaskAssignees(TaskPackItem $item, ?int $previousDefaultAssigneeId = null): void
    {
        if (!Schema::hasTable('tasks') || !Schema::hasColumn('tasks', 'assignee_id')) return;

        $item->loadMissing('defaultDepartment');
        $desiredAssigneeId = $item->default_assignee_id ? (int) $item->default_assignee_id : null;

        // A Task Pack may use a default department instead of a named user.
        // Resolve that exactly as Job generation does so existing and newly
        // generated tasks behave consistently.
        if (!$desiredAssigneeId && $item->defaultDepartment && Schema::hasTable('departments')) {
            $legacyDepartmentId = DB::table('departments')
                ->where('code', $item->defaultDepartment->code)
                ->value('id');
            if ($legacyDepartmentId) {
                $desiredAssigneeId = DB::table('users')
                    ->where('is_active', true)
                    ->where('department_id', $legacyDepartmentId)
                    ->orderBy('id')
                    ->value('id');
                $desiredAssigneeId = $desiredAssigneeId ? (int) $desiredAssigneeId : null;
            }
        }

        Task::query()
            ->where('task_pack_task_id', $item->id)
            ->orderBy('id')
            ->get()
            ->each(function (Task $task) use ($desiredAssigneeId, $previousDefaultAssigneeId): void {
                $storedSetupId = Schema::hasColumn('tasks', 'setup_assignee_id') && $task->setup_assignee_id
                    ? (int) $task->setup_assignee_id
                    : null;

                $followsTaskPack = !$task->assignee_id
                    || ($storedSetupId && (int) $task->assignee_id === $storedSetupId)
                    || ($previousDefaultAssigneeId && (int) $task->assignee_id === $previousDefaultAssigneeId);

                if (!$followsTaskPack) return;

                $changes = ['assignee_id' => $desiredAssigneeId];
                if (Schema::hasColumn('tasks', 'setup_assignee_id')) {
                    $changes['setup_assignee_id'] = $desiredAssigneeId;
                }
                $task->update($changes);

                if ($desiredAssigneeId && Schema::hasTable('flow_job_members')) {
                    DB::table('flow_job_members')->updateOrInsert(
                        ['flow_job_id' => $task->flow_job_id, 'user_id' => $desiredAssigneeId],
                        [
                            'access_level' => 'member',
                            'can_manage_tasks' => false,
                            'can_upload_documents' => true,
                            'can_view_financials' => false,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            });

        // Inquiry taskflows keep the Task Pack assignee as their initial setup
        // value too. Only rows still following that setup are synchronized; a
        // manual reassignment by an Admin or the Inquiry creator is preserved.
        if (Schema::hasTable('inquiry_tasks') && Schema::hasColumn('inquiry_tasks', 'setup_assignee_id')) {
            InquiryTask::query()
                ->where('source_task_pack_item_id', $item->id)
                ->orderBy('id')
                ->get()
                ->each(function (InquiryTask $task) use ($desiredAssigneeId, $previousDefaultAssigneeId): void {
                    $storedSetupId = $task->setup_assignee_id ? (int) $task->setup_assignee_id : null;
                    $followsTaskPack = !$task->assignee_id
                        || ($storedSetupId && (int) $task->assignee_id === $storedSetupId)
                        || ($previousDefaultAssigneeId && (int) $task->assignee_id === $previousDefaultAssigneeId);

                    $changes = ['setup_assignee_id' => $desiredAssigneeId];
                    if ($followsTaskPack) $changes['assignee_id'] = $desiredAssigneeId;
                    $task->update($changes);
                });
        }
    }

    public function deleteItem(int $id, bool $authorize = true): void
    {
        if ($authorize) $this->assertAction('delete');
        $item = TaskPackItem::findOrFail($id);
        if (Task::where('task_pack_task_id', $item->id)->exists()) {
            throw ValidationException::withMessages(['item' => 'This Task Pack item has generated Tasks and cannot be deleted.']);
        }
        DB::transaction(function () use ($item) {
            if (Schema::hasTable('task_pack_tasks')) TaskPackTask::whereKey($item->id)->delete();
            $packId = $item->task_pack_id;
            $item->delete();
            $this->normalize($packId);
        });
    }

    public function moveItem(int $id, int $direction): void
    {
        $this->assertAction('edit');
        DB::transaction(function () use ($id, $direction) {
            $item = TaskPackItem::findOrFail($id);
            $items = TaskPackItem::where('task_pack_id', $item->task_pack_id)->orderBy('sort_order')->orderBy('id')->get()->values();
            $index = $items->search(fn ($row) => $row->id === $item->id);
            $target = $index + $direction;
            if ($index === false || $target < 0 || $target >= $items->count()) return;
            $a = $items[$index]; $b = $items[$target]; $tmp = $a->sort_order;
            $a->update(['sort_order' => 999999]);
            $b->update(['sort_order' => $tmp]);
            $a->update(['sort_order' => $b->sort_order === 999999 ? $target : $target]);
            $this->normalize($item->task_pack_id);
        });
    }

    public function syncLegacy(): void
    {
        if (!Schema::hasTable('task_pack_items')) return;
        app(MasterDataService::class)->syncLegacy();
        $workspaceId = $this->workspaceId();
        foreach (TaskPack::query()->where('workspace_id', $workspaceId)->where('is_snapshot', false)->where(fn ($q) => $q->whereNull('code')->orWhere('code', ''))->get() as $pack) {
            $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) ($pack->slug ?: $pack->name)), 0, 8)) ?: 'PACK';
            $pack->update(['code' => $base.'-'.$pack->id]);
        }
        if (!Schema::hasTable('task_pack_tasks')) return;
        $medium = MasterRecord::where('workspace_id', $workspaceId)->where('type', 'priority')->where('code', 'MED')->value('id');
        foreach (TaskPackTask::query()->orderBy('id')->get() as $legacy) {
            TaskPackItem::firstOrCreate(['id' => $legacy->id], [
                'task_pack_id' => $legacy->task_pack_id,
                'title' => $legacy->title,
                'priority_id' => $medium,
                'due_offset_days' => max(1, (int) $legacy->sequence),
                'is_required' => $legacy->is_required,
                'sort_order' => max(0, (int) $legacy->sequence - 1),
            ]);
        }

        $this->syncLegacyDocumentRequirements();
    }

    private function syncLegacyDocumentRequirements(): void
    {
        if (!Schema::hasTable('task_pack_items')) return;

        // Preserve document requirements already carried by generated tasks.
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'document_category_id')) {
            Task::query()
                ->whereNotNull('task_pack_task_id')
                ->whereNotNull('document_category_id')
                ->when(Schema::hasColumn('tasks', 'document_requirement_source'), fn ($q) => $q->where('document_requirement_source', 'task_pack'))
                ->select(['task_pack_task_id','document_category_id'])
                ->distinct()
                ->get()
                ->each(function ($task) {
                    TaskPackItem::query()
                        ->whereKey($task->task_pack_task_id)
                        ->whereNull('document_category_id')
                        ->update(['document_category_id' => $task->document_category_id]);
                });
        }

        // Legacy workflow phases once carried a required document directly.
        // Migrate it into the mapped Task Pack only when that pack has no
        // explicit document requirement yet. After this, Job logic reads the
        // Task Pack only.
        if (!Schema::hasTable('workflow_phases')) return;
        WorkflowPhase::query()->whereNotNull('task_pack_id')->orderBy('id')->get()->each(function ($phase) {
            $documentId = $this->resolveLegacyDocumentCategoryId(
                $phase->document_category_id ?? null,
                Schema::hasColumn('workflow_phases', 'required_document') ? ($phase->required_document ?? null) : null
            );
            if (!$documentId) return;

            $items = TaskPackItem::query()->where('task_pack_id', $phase->task_pack_id)->orderBy('sort_order')->orderBy('id')->get();
            if ($items->isEmpty() || $items->contains(fn ($item) => filled($item->document_category_id))) return;

            $candidate = $items->first(fn ($item) => preg_match('/upload|document|file|attach|submit|confirmation|quotation|invoice|approval|\bpo\b/i', (string) $item->title))
                ?: $items->firstWhere('is_required', true)
                ?: $items->first();
            if (!$candidate) return;

            $candidate->update(['document_category_id' => $documentId]);
            Task::query()->where('task_pack_task_id', $candidate->id)->update([
                'document_category_id' => $documentId,
                'document_requirement_source' => 'task_pack',
            ]);
        });
    }

    private function normalize(int $packId): void
    {
        if (Schema::hasTable('task_pack_tasks')) {
            TaskPackTask::where('task_pack_id', $packId)->get()->each(fn ($row) => $row->update(['sequence' => 10000 + $row->id]));
        }
        TaskPackItem::where('task_pack_id', $packId)->orderBy('sort_order')->orderBy('id')->get()->values()->each(function ($item, $index) {
            $item->update(['sort_order' => $index]);
            $this->mirrorLegacyItem($item->fresh());
        });
    }

    private function mirrorLegacyItem(TaskPackItem $item): void
    {
        if (!Schema::hasTable('task_pack_tasks')) return;

        $legacyDepartmentId = null;
        if ($item->default_department_id && Schema::hasTable('departments')) {
            $master = MasterRecord::find($item->default_department_id);
            if ($master) $legacyDepartmentId = DB::table('departments')->where('code', $master->code)->value('id');
        }

        $sequence = max(1, (int) $item->sort_order + 1);

        // task_pack_tasks is kept only for backwards compatibility. Older data
        // can contain a row occupying the sequence we are about to mirror. Move
        // that stale row out of the active sequence range first so editing a
        // modern Task Pack can never fail on the legacy unique index.
        $conflict = TaskPackTask::query()
            ->where('task_pack_id', $item->task_pack_id)
            ->where('sequence', $sequence)
            ->whereKeyNot($item->id)
            ->first();

        if ($conflict) {
            $conflict->update(['sequence' => 50000 + (int) $conflict->id]);
        }

        TaskPackTask::query()->updateOrCreate(['id' => $item->id], [
            'task_pack_id' => $item->task_pack_id,
            'title' => $item->title,
            'sequence' => $sequence,
            'is_required' => $item->is_required,
            'default_department_id' => $legacyDepartmentId,
        ]);
    }
    private function taskPackTemplateIds(int $packId): array
    {
        $ids = TaskPackItem::query()->where('task_pack_id', $packId)->pluck('id');

        if (Schema::hasTable('task_pack_tasks')) {
            $ids = $ids->merge(
                TaskPackTask::query()->where('task_pack_id', $packId)->pluck('id')
            );
        }

        return $ids
            ->map(fn ($templateId) => (int) $templateId)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function assertAction(string $action): void
    {
        $user = auth()->user();
        abort_unless($user && app(AccessControlService::class)->can($user, 'taskpacks', $action), 403);
    }

}
