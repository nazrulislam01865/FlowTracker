<?php

namespace App\Services;

use App\Models\MasterRecord;
use App\Models\MasterValue;
use App\Support\MasterColor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MasterDataService
{
    /** @var array<string,array<string,string>> */
    private array $colorMaps = [];

    public const COLOR_TYPES = ['priority', 'task_status', 'inquiry_status', 'task_flag'];

    public const ACCESS_MODULES = [
        'product' => 'catalog_products',
        'product_category' => 'product_categories',
        'supplier' => 'suppliers',
    ];

    public static function permissionModuleForType(string $type): string
    {
        return self::ACCESS_MODULES[$type] ?? 'masterdata';
    }

    public const LABELS = [
        'department' => 'Departments',
        'product_category' => 'Product Categories',
        'product' => 'Products',
        'supplier' => 'Suppliers',
        'production_unit' => 'Production Units',
        'shipment_method' => 'Shipment Methods',
        'currency' => 'Currencies',
        'country' => 'Countries',
        'state' => 'States',
        'document_category' => 'Document Categories',
        'priority' => 'Priorities',
        'task_status' => 'Task Statuses',
        'inquiry_status' => 'Inquiry Statuses',
        'task_flag' => 'Task Flags',
    ];

    /**
     * Stable prefixes used for automatically generated Master Data codes.
     *
     * Existing records keep their historical codes. New records created from
     * Master Data use the next available PREFIX-### value for their type.
     */
    public const CODE_PREFIXES = [
        'department' => 'DEP',
        'product_category' => 'CAT',
        'product' => 'PRD',
        'supplier' => 'SUP',
        'production_unit' => 'PUN',
        'shipment_method' => 'SHM',
        'currency' => 'CUR',
        'country' => 'CTR',
        'state' => 'STA',
        'document_category' => 'DOC',
        'priority' => 'PRI',
        'task_status' => 'TST',
        'inquiry_status' => 'IST',
        'task_flag' => 'TFL',
    ];

    private const LEGACY_GROUPS = [
        'department' => 'departments',
        'product_category' => 'product_categories',
        'product' => 'products',
        'supplier' => 'suppliers',
        'production_unit' => 'production_units',
        'shipment_method' => 'shipment_methods',
        'currency' => 'currencies',
        'country' => 'countries',
        'state' => 'states',
        'document_category' => 'document_categories',
        'priority' => 'priorities',
        'task_status' => 'task_statuses',
        'inquiry_status' => 'inquiry_statuses',
    ];

    public function workspaceId(): int { return app(SetupContext::class)->workspaceId(); }

    public function query(string $type, string $search = '', array $filters = [])
    {
        $status = trim((string) ($filters['status'] ?? ''));
        $parentId = (int) ($filters['parent_id'] ?? 0);

        return MasterRecord::query()
            ->forWorkspace($this->workspaceId())
            ->ofType($type)
            ->when(in_array($type, ['product', 'state'], true), fn ($q) => $q->with('parent'))
            ->when($type === 'product', fn ($q) => $q->with('creator'))
            ->when($search, fn ($q) => $q->where(fn ($x) => $x
                ->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($q) => $q->where('status', $status))
            ->when($type === 'product' && $parentId > 0, fn ($q) => $q->where('parent_id', $parentId))
            ->orderBy('sort_order')->orderBy('name');
    }

    public function list(string $type, string $search = '', array $filters = [])
    {
        return $this->query($type, $search, $filters)->get();
    }

    public function paginate(string $type, string $search = '', int $perPage = 30, array $filters = [])
    {
        $perPage = max(1, min(100, $perPage));

        return $this->query($type, $search, $filters)->paginate($perPage, ['*'], 'masterPage');
    }

    public function active(string $type)
    {
        abort_unless(array_key_exists($type, self::LABELS), 404);
        $workspaceId = $this->workspaceId();
        $rows = Cache::remember($this->activeCacheKey($workspaceId, $type), now()->addMinutes(5), fn () =>
            MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->active()->orderBy('sort_order')->orderBy('name')->get()
                ->map(fn (MasterRecord $record) => $record->getAttributes())->all()
        );

        return collect($rows)->map(fn (array $attributes) => (new MasterRecord())->newFromBuilder($attributes));
    }

    public function colorFor(string $type, ?string $value): ?string
    {
        if (!in_array($type, self::COLOR_TYPES, true)) return null;

        $value = strtolower(trim((string) $value));
        if ($value === '') return null;

        if (!array_key_exists($type, $this->colorMaps)) {
            $map = [];
            MasterRecord::query()
                ->forWorkspace($this->workspaceId())
                ->ofType($type)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['code', 'name', 'color'])
                ->each(function (MasterRecord $record) use (&$map, $type): void {
                    $color = MasterColor::normalize($record->color) ?: MasterColor::defaultFor($type, $record->name);

                    $name = strtolower(trim((string) $record->name));
                    $code = strtolower(trim((string) $record->code));
                    if ($name !== '') $map[$name] = $color;
                    if ($code !== '') $map[$code] = $color;
                });

            $this->colorMaps[$type] = $map;
        }

        return $this->colorMaps[$type][$value] ?? null;
    }

    public function displayColorFor(string $type, ?string $value): ?string
    {
        if (!in_array($type, self::COLOR_TYPES, true)) return null;
        if (trim((string) $value) === '') return null;

        return $this->colorFor($type, $value) ?: MasterColor::defaultFor($type, $value);
    }

    public function colorStyleFor(string $type, ?string $value): string
    {
        return MasterColor::style($this->displayColorFor($type, $value));
    }

    public function nextCode(string $type): string
    {
        abort_unless(array_key_exists($type, self::LABELS), 404);

        $prefix = self::CODE_PREFIXES[$type];
        $workspaceId = $this->workspaceId();
        $pattern = '/^'.preg_quote($prefix, '/').'-(\d+)$/';

        // Include soft-deleted rows because the database unique index still
        // reserves their code. This prevents a deleted code from being reused.
        $highest = MasterRecord::withTrashed()
            ->forWorkspace($workspaceId)
            ->ofType($type)
            ->where('code', 'like', $prefix.'-%')
            ->pluck('code')
            ->reduce(function (int $max, string $code) use ($pattern): int {
                if (!preg_match($pattern, strtoupper($code), $matches)) return $max;
                return max($max, (int) $matches[1]);
            }, 0);

        $next = $highest + 1;
        do {
            $code = $prefix.'-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $next++;
        } while (MasterRecord::withTrashed()
            ->forWorkspace($workspaceId)
            ->ofType($type)
            ->where('code', $code)
            ->exists());

        return $code;
    }

    public function save(string $type, array $data, ?int $id = null): MasterRecord
    {
        $this->assertAction($type, $id ? 'edit' : 'create');
        abort_unless(array_key_exists($type, self::LABELS), 404);
        $workspaceId = $this->workspaceId();
        $code = strtoupper(trim($data['code']));
        $duplicate = MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->where('code', $code)->when($id, fn ($q) => $q->whereKeyNot($id))->exists();
        if ($duplicate) throw ValidationException::withMessages(['code' => 'This code already exists in the selected master data type.']);

        $parentId = null;
        $parentType = match ($type) {
            'product' => 'product_category',
            'state' => 'country',
            default => null,
        };
        if ($parentType && filled($data['parent_id'] ?? null)) {
            $parentId = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType($parentType)
                ->whereKey((int) $data['parent_id'])
                ->value('id');

            if (!$parentId) {
                $label = $type === 'state' ? 'Country' : 'Product Category';
                throw ValidationException::withMessages(['parentId' => 'Select a valid '.$label.'.']);
            }
        }

        if ($type === 'state' && !$parentId) {
            throw ValidationException::withMessages(['parentId' => 'Select the country this state belongs to.']);
        }

        return DB::transaction(function () use ($type, $data, $id, $workspaceId, $code, $parentId) {
            $record = $id
                ? MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->findOrFail($id)
                : new MasterRecord();

            // Record the original creator once. Edits must never replace the
            // user who originally created the Master Data record.
            if (! $record->exists) {
                $record->created_by = auth()->id();
            }

            $record->fill([
                'workspace_id' => $workspaceId,
                'parent_id' => $parentId,
                'type' => $type,
                'code' => $code,
                'name' => trim($data['name']),
                'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
                'color' => in_array($type, self::COLOR_TYPES, true)
                    ? (MasterColor::normalize($data['color'] ?? null) ?: MasterColor::defaultFor($type, $data['name'] ?? null))
                    : null,
                'metadata' => $data['metadata'] ?? null,
                'status' => ($data['status'] ?? 'active') === 'active' ? 'active' : 'inactive',
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ])->save();

            // Tasks keep a foreign key to Task Flags. Mirror the current Master
            // Data name into attention_reason as a compatibility/search field so
            // every existing list immediately follows Task Flag renames.
            if ($record->type === 'task_flag' && Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'task_flag_id')) {
                DB::table('tasks')
                    ->where('task_flag_id', $record->id)
                    ->update(['attention_reason' => $record->name]);
            }

            $this->mirrorLegacy($record);
            $this->forgetActiveCache($record->type);
            return $record;
        });
    }

    public function setColor(int $id, string $color): MasterRecord
    {
        $record = MasterRecord::query()->forWorkspace($this->workspaceId())->findOrFail($id);
        $this->assertAction($record->type, 'edit');
        abort_unless(in_array($record->type, self::COLOR_TYPES, true), 404);

        $normalized = MasterColor::normalize($color);
        if (!$normalized) {
            throw ValidationException::withMessages(['color' => 'Choose a valid 6-digit hex color.']);
        }

        $record->update(['color' => $normalized]);
        $this->mirrorLegacy($record);
        $this->forgetActiveCache($record->type);

        return $record;
    }

    public function toggle(int $id): MasterRecord
    {
        $record = MasterRecord::query()->forWorkspace($this->workspaceId())->findOrFail($id);
        $this->assertAction($record->type, 'edit');
        $record->update(['status' => $record->status === 'active' ? 'inactive' : 'active']);
        $this->mirrorLegacy($record);
        $this->forgetActiveCache($record->type);
        return $record;
    }

    public function delete(int $id): void
    {
        $record = MasterRecord::query()->forWorkspace($this->workspaceId())->findOrFail($id);
        $this->assertAction($record->type, 'delete');

        // Inquiry Status is intentionally force-removable from the Master Data UI.
        // Inquiries store their status label as text, not as a foreign key, and
        // MasterRecord itself uses SoftDeletes. That means current, historical,
        // or soft-deleted Inquiries must never block removing a status option.
        // Delete the mirrored legacy value too so syncLegacy() cannot restore it.
        if ($record->type === 'inquiry_status') {
            if (Schema::hasTable('master_values')) {
                MasterValue::where('group_key', self::LEGACY_GROUPS['inquiry_status'])
                    ->where('code', $record->code)
                    ->delete();
            }

            $record->delete();
            $this->forgetActiveCache('inquiry_status');
            return;
        }

        if ($record->children()->exists()) throw ValidationException::withMessages(['record' => 'Remove or reassign child records before deleting this record.']);
        if (DB::table('task_pack_items')->where('default_department_id', $id)->orWhere('priority_id', $id)->orWhere('document_category_id', $id)->exists()) {
            throw ValidationException::withMessages(['record' => 'This record is used by a Task Pack and cannot be deleted. Deactivate it instead.']);
        }
        if (Schema::hasColumn('workflow_phases', 'document_category_id') && DB::table('workflow_phases')->where('document_category_id', $id)->exists()) {
            throw ValidationException::withMessages(['record' => 'This record is used by a Workflow phase and cannot be deleted. Deactivate it instead.']);
        }
        if ($record->type === 'task_flag' && Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'task_flag_id') && DB::table('tasks')->where('task_flag_id', $record->id)->exists()) {
            throw ValidationException::withMessages(['record' => 'This Task Flag is already assigned to one or more tasks and cannot be deleted. Deactivate it instead.']);
        }
        if ($record->type === 'product' && data_get($record->metadata, 'product_image_path')) {
            app(ProductImageService::class)->remove($record);
        }

        $legacyGroup = self::LEGACY_GROUPS[$record->type] ?? Str::plural($record->type);
        if (Schema::hasTable('master_values')) MasterValue::where('group_key', $legacyGroup)->where('code', $record->code)->delete();
        $type = $record->type;
        $record->delete();
        $this->forgetActiveCache($type);
    }

    public function syncLegacy(): void
    {
        if (!Schema::hasTable('master_values') || !Schema::hasTable('master_records')) return;
        $workspaceId = $this->workspaceId();
        $syncKey = 'flowtrack:master:legacy-sync:'.$workspaceId;
        if (Cache::get($syncKey)) return;

        foreach (MasterValue::query()->get() as $legacy) {
            $type = array_search($legacy->group_key, self::LEGACY_GROUPS, true) ?: Str::singular($legacy->group_key);
            MasterRecord::query()->firstOrCreate(
                ['workspace_id' => $workspaceId, 'type' => $type, 'code' => $legacy->code],
                [
                    'name' => $legacy->name,
                    'description' => $legacy->description,
                    'color' => in_array($type, self::COLOR_TYPES, true)
                        ? (MasterColor::normalize(data_get($legacy->meta, 'color')) ?: MasterColor::defaultFor($type, $legacy->name))
                        : null,
                    'metadata' => $legacy->meta,
                    'status' => $legacy->is_active ? 'active' : 'inactive',
                    'sort_order' => (int) $legacy->id,
                ]
            );
        }

        // Products and States are hierarchical master-data types. Preserve
        // Product -> Product Category and State -> Country links while keeping
        // all other master-data categories flat.
        MasterRecord::query()->forWorkspace($workspaceId)->whereNotIn('type', ['product', 'state'])->whereNotNull('parent_id')->update(['parent_id' => null]);
        foreach (MasterValue::query()->where('group_key', self::LEGACY_GROUPS['product'])->whereNotNull('parent_id')->get() as $legacyProduct) {
            $legacyCategory = MasterValue::query()->whereKey($legacyProduct->parent_id)->first();
            if (!$legacyCategory || $legacyCategory->group_key !== self::LEGACY_GROUPS['product_category']) continue;

            $categoryId = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_category')
                ->where('code', $legacyCategory->code)
                ->value('id');

            if ($categoryId) {
                MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->where('code', $legacyProduct->code)
                    ->whereNull('parent_id')
                    ->update(['parent_id' => $categoryId]);
            }
        }

        foreach (MasterValue::query()->where('group_key', self::LEGACY_GROUPS['state'])->whereNotNull('parent_id')->get() as $legacyState) {
            $legacyCountry = MasterValue::query()->whereKey($legacyState->parent_id)->first();
            if (!$legacyCountry || $legacyCountry->group_key !== self::LEGACY_GROUPS['country']) continue;

            $countryId = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('country')
                ->where('code', $legacyCountry->code)
                ->value('id');

            if ($countryId) {
                MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('state')
                    ->where('code', $legacyState->code)
                    ->whereNull('parent_id')
                    ->update(['parent_id' => $countryId]);
            }
        }

        // Older demo/legacy Product rows did not always have parent_id set.
        // Their description begins with the Product Category name, e.g.
        // "Backpacks & Bags · Custom". Link only when that prefix exactly
        // matches a real category in this workspace; otherwise leave it alone.
        foreach (MasterRecord::query()->forWorkspace($workspaceId)->ofType('product')->whereNull('parent_id')->get(['id', 'description']) as $product) {
            $categoryName = trim(explode(' ·', trim((string) $product->description), 2)[0]);
            if ($categoryName === '') continue;

            $categoryId = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_category')
                ->where('name', $categoryName)
                ->value('id');

            if ($categoryId) {
                $product->update(['parent_id' => $categoryId]);
            }
        }

        Cache::put($syncKey, true, now()->addMinutes(5));
    }

    private function mirrorLegacy(MasterRecord $record): void
    {
        if (!Schema::hasTable('master_values')) return;
        $group = self::LEGACY_GROUPS[$record->type] ?? Str::plural($record->type);
        $legacyParentId = null;
        $parentType = match ($record->type) {
            'product' => 'product_category',
            'state' => 'country',
            default => null,
        };
        if ($parentType && $record->parent_id) {
            $parent = MasterRecord::query()
                ->forWorkspace($record->workspace_id)
                ->ofType($parentType)
                ->find($record->parent_id);
            if ($parent) {
                $legacyParentId = MasterValue::query()
                    ->where('group_key', self::LEGACY_GROUPS[$parentType])
                    ->where('code', $parent->code)
                    ->value('id');
            }
        }

        $legacyMeta = (array) ($record->metadata ?? []);
        if (in_array($record->type, self::COLOR_TYPES, true) && MasterColor::normalize($record->color)) {
            $legacyMeta['color'] = MasterColor::normalize($record->color);
        }

        MasterValue::query()->updateOrCreate(
            ['group_key' => $group, 'code' => $record->code],
            [
                'name' => $record->name,
                'description' => $record->description,
                'parent_id' => $legacyParentId,
                'is_active' => $record->status === 'active',
                'meta' => $legacyMeta ?: null,
            ]
        );
    }
    private function activeCacheKey(int $workspaceId, string $type): string
    {
        return "flowtrack:master:active:{$workspaceId}:{$type}";
    }

    private function forgetActiveCache(string $type): void
    {
        Cache::forget($this->activeCacheKey($this->workspaceId(), $type));
        unset($this->colorMaps[$type]);
    }

    private function assertAction(string $type, string $action): void
    {
        $user = auth()->user();
        $module = self::permissionModuleForType($type);
        abort_unless($user && app(AccessControlService::class)->can($user, $module, $action), 403);
    }

}
