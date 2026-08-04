<?php

namespace App\Services;

use App\Models\MasterRecord;
use App\Models\MasterValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MasterDataService
{
    public const LABELS = [
        'department' => 'Departments',
        'product_category' => 'Product Categories',
        'product' => 'Products',
        'supplier' => 'Suppliers',
        'production_unit' => 'Production Units',
        'shipment_method' => 'Shipment Methods',
        'currency' => 'Currencies',
        'document_category' => 'Document Categories',
        'priority' => 'Priorities',
        'task_status' => 'Task Statuses',
    ];

    private const LEGACY_GROUPS = [
        'department' => 'departments',
        'product_category' => 'product_categories',
        'product' => 'products',
        'supplier' => 'suppliers',
        'production_unit' => 'production_units',
        'shipment_method' => 'shipment_methods',
        'currency' => 'currencies',
        'document_category' => 'document_categories',
        'priority' => 'priorities',
        'task_status' => 'task_statuses',
    ];

    public function workspaceId(): int { return app(SetupContext::class)->workspaceId(); }

    public function query(string $type, string $search = '')
    {
        $this->syncLegacy();

        return MasterRecord::query()
            ->forWorkspace($this->workspaceId())
            ->ofType($type)
            ->with('parent')
            ->when($search, fn ($q) => $q->where(fn ($x) => $x
                ->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")))
            ->orderBy('sort_order')->orderBy('name');
    }

    public function list(string $type, string $search = '')
    {
        return $this->query($type, $search)->get();
    }

    public function paginate(string $type, string $search = '', int $perPage = 30)
    {
        return $this->query($type, $search)->paginate($perPage, ['*'], 'masterPage');
    }

    public function active(string $type)
    {
        $this->syncLegacy();
        return MasterRecord::query()->forWorkspace($this->workspaceId())->ofType($type)->active()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function save(string $type, array $data, ?int $id = null): MasterRecord
    {
        $this->assertManage();
        abort_unless(array_key_exists($type, self::LABELS), 404);
        $workspaceId = $this->workspaceId();
        $code = strtoupper(trim($data['code']));
        $duplicate = MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->where('code', $code)->when($id, fn ($q) => $q->whereKeyNot($id))->exists();
        if ($duplicate) throw ValidationException::withMessages(['code' => 'This code already exists in the selected master data type.']);

        return DB::transaction(function () use ($type, $data, $id, $workspaceId, $code) {
            $record = MasterRecord::query()->updateOrCreate(['id' => $id], [
                'workspace_id' => $workspaceId,
                'parent_id' => $data['parent_id'] ?? null,
                'type' => $type,
                'code' => $code,
                'name' => trim($data['name']),
                'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
                'metadata' => $data['metadata'] ?? null,
                'status' => ($data['status'] ?? 'active') === 'active' ? 'active' : 'inactive',
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);
            $this->mirrorLegacy($record);
            return $record;
        });
    }

    public function toggle(int $id): MasterRecord
    {
        $this->assertManage();
        $record = MasterRecord::query()->forWorkspace($this->workspaceId())->findOrFail($id);
        $record->update(['status' => $record->status === 'active' ? 'inactive' : 'active']);
        $this->mirrorLegacy($record);
        return $record;
    }

    public function delete(int $id): void
    {
        $this->assertManage();
        $record = MasterRecord::query()->forWorkspace($this->workspaceId())->findOrFail($id);
        if ($record->children()->exists()) throw ValidationException::withMessages(['record' => 'Remove or reassign child records before deleting this record.']);
        if (DB::table('task_pack_items')->where('default_department_id', $id)->orWhere('priority_id', $id)->orWhere('document_category_id', $id)->exists()) {
            throw ValidationException::withMessages(['record' => 'This record is used by a Task Pack and cannot be deleted. Deactivate it instead.']);
        }
        if (Schema::hasColumn('workflow_phases', 'document_category_id') && DB::table('workflow_phases')->where('document_category_id', $id)->exists()) {
            throw ValidationException::withMessages(['record' => 'This record is used by a Workflow phase and cannot be deleted. Deactivate it instead.']);
        }
        $legacyGroup = self::LEGACY_GROUPS[$record->type] ?? Str::plural($record->type);
        if (Schema::hasTable('master_values')) MasterValue::where('group_key', $legacyGroup)->where('code', $record->code)->delete();
        $record->delete();
    }

    public function syncLegacy(): void
    {
        if (!Schema::hasTable('master_values') || !Schema::hasTable('master_records')) return;
        $workspaceId = $this->workspaceId();
        foreach (MasterValue::query()->get() as $legacy) {
            $type = array_search($legacy->group_key, self::LEGACY_GROUPS, true) ?: Str::singular($legacy->group_key);
            MasterRecord::query()->firstOrCreate(
                ['workspace_id' => $workspaceId, 'type' => $type, 'code' => $legacy->code],
                [
                    'name' => $legacy->name,
                    'description' => $legacy->description,
                    'metadata' => $legacy->meta,
                    'status' => $legacy->is_active ? 'active' : 'inactive',
                    'sort_order' => (int) $legacy->id,
                ]
            );
        }
    }

    private function mirrorLegacy(MasterRecord $record): void
    {
        if (!Schema::hasTable('master_values')) return;
        $group = self::LEGACY_GROUPS[$record->type] ?? Str::plural($record->type);
        MasterValue::query()->updateOrCreate(
            ['group_key' => $group, 'code' => $record->code],
            [
                'name' => $record->name,
                'description' => $record->description,
                'parent_id' => null,
                'is_active' => $record->status === 'active',
                'meta' => $record->metadata,
            ]
        );
    }
    private function assertManage(): void
    {
        $user = auth()->user();
        abort_unless($user && app(AccessControlService::class)->can($user, 'masterdata', 'manage'), 403);
    }

}
