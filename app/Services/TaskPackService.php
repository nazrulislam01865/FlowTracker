<?php

namespace App\Services;

use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\TaskPack;
use App\Models\TaskPackItem;
use App\Models\TaskPackTask;
use App\Models\WorkflowPhase;
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
        $next = ((int) TaskPack::where('workspace_id', $this->workspaceId())->max('id')) + 1;
        do {
            $code = 'TPK-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $next++;
        } while (TaskPack::where('workspace_id', $this->workspaceId())->where('code', $code)->exists());

        return $code;
    }

    public function savePackWithItems(array $packData, array $items, ?int $id = null): TaskPack
    {
        $this->assertManage();
        return DB::transaction(function () use ($packData, $items, $id) {
            $pack = $this->savePack($packData, $id);
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
                ], $itemId);
                $keepIds[] = $saved->id;
            }

            $removed = $pack->items()->when($keepIds, fn ($q) => $q->whereNotIn('id', $keepIds))->pluck('id');
            foreach ($removed as $removedId) {
                $this->deleteItem((int) $removedId);
            }

            $this->normalize($pack->id);
            return $pack->fresh(['items.defaultAssignee','items.defaultDepartment','items.priority','items.documentCategory']);
        });
    }

    public function savePack(array $data, ?int $id = null): TaskPack
    {
        $this->assertManage();
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
        return TaskPack::query()->updateOrCreate(['id' => $id], $payload);
    }

    public function togglePack(int $id): void
    {
        $this->assertManage();
        $pack = TaskPack::where('workspace_id', $this->workspaceId())->findOrFail($id);
        $pack->update(['is_active' => !$pack->is_active]);
    }

    public function deletePack(int $id): void
    {
        $this->assertManage();
        $pack = TaskPack::where('workspace_id', $this->workspaceId())->findOrFail($id);
        if (DB::table('workflow_phases')->where('task_pack_id', $id)->exists()) {
            throw ValidationException::withMessages(['pack' => 'This Task Pack is used by a Workflow phase. Remove that mapping first.']);
        }
        $itemIds = $pack->items()->pluck('id');
        if ($itemIds->isNotEmpty() && Task::whereIn('task_pack_task_id', $itemIds)->exists()) {
            throw ValidationException::withMessages(['pack' => 'Tasks have already been generated from this Task Pack. Deactivate it instead of deleting it.']);
        }
        DB::transaction(function () use ($pack, $itemIds) {
            if (Schema::hasTable('task_pack_tasks')) TaskPackTask::whereIn('id', $itemIds)->delete();
            $pack->items()->delete();
            $pack->delete();
        });
    }

    public function saveItem(TaskPack $pack, array $data, ?int $id = null): TaskPackItem
    {
        $this->assertManage();
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
    }

    public function deleteItem(int $id): void
    {
        $this->assertManage();
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
        $this->assertManage();
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
        foreach (TaskPack::query()->where('workspace_id', $workspaceId)->where(fn ($q) => $q->whereNull('code')->orWhere('code', ''))->get() as $pack) {
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
    private function assertManage(): void
    {
        $user = auth()->user();
        abort_unless($user && app(AccessControlService::class)->can($user, 'workflow', 'manage'), 403);
    }

}
