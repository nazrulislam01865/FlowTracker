<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\MasterRecord;
use App\Models\NotificationRule;
use App\Models\Role;
use App\Models\RoleModuleAccess;
use App\Models\TaskPack;
use App\Models\User;
use App\Models\WorkspaceMembership;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService
{
    public function summary(): array
    {
        return [
            'users' => User::where('is_active', true)->whereHas('workspaceMemberships', fn ($q) => $q->where('workspace_id', $this->workspaceId())->where('status', 'active'))->count(),
            'roles' => Role::where('workspace_id', $this->workspaceId())->where('is_active', true)->count(),
            'task_packs' => TaskPack::count(),
            'rules' => NotificationRule::where('is_active', true)->count(),
            'access_changes' => Activity::where('event', 'like', 'access.%')->where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    public function users()
    {
        return User::with(['role','department'])
            ->whereHas('workspaceMemberships', fn ($q) => $q->where('workspace_id', $this->workspaceId()))
            ->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q->whereNull('completed_at')])
            ->orderBy('name')->get();
    }

    public function roles()
    {
        return Role::with(['moduleAccess','users'])->where('workspace_id', $this->workspaceId())->orderByDesc('is_system')->orderBy('name')->get();
    }

    public function notificationRules() { return NotificationRule::orderBy('name')->get(); }
    public function taskPacks() { return TaskPack::with('templates')->get(); }

    public function createUser(array $data): User
    {
        $actor = auth()->user();
        $this->assertAdministrator($actor);
        $role = Role::where('workspace_id', $this->workspaceId())->findOrFail((int) $data['role_id']);
        $data['role_id'] = $role->id;
        $user = User::create(array_merge($data, ['password' => Hash::make($data['password']), 'is_active' => true, 'locale' => 'en']));
        $this->syncMembership($user);
        $this->audit($user, 'access.user_created', 'User created and assigned to role '.$user->role?->name, $actor);
        return $user;
    }

    public function saveRole(array $data, ?int $id, User $actor): Role
    {
        $this->assertAdministrator($actor);
        $role = $id ? Role::where('workspace_id', $this->workspaceId())->findOrFail($id) : new Role();
        abort_if($id && in_array($role->slug, ['super-admin','admin','administrator'], true), 422, 'Administrator role identity is locked.');
        $role->fill([
            'workspace_id' => $this->workspaceId(),
            'name' => trim($data['name']),
            'slug' => $id ? $role->slug : Str::slug($data['code'] ?: $data['name']),
            'code' => Str::upper(Str::replace('-', '_', trim($data['code'] ?: $data['name']))),
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'default_scope' => $data['default_scope'] ?? 'assigned_jobs',
            'is_system' => $role->is_system ?? false,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ])->save();

        foreach (AccessControlService::MODULES as $code => $_) {
            RoleModuleAccess::firstOrCreate(
                ['role_id' => $role->id, 'module_code' => $code],
                ['record_scope' => 'none', 'actions' => []],
            );
        }
        $this->audit($role, $id ? 'access.role_updated' : 'access.role_created', ($id ? 'Updated role ' : 'Created role ').$role->name, $actor);
        return $role->refresh();
    }

    public function toggleMatrixAction(Role $role, string $module, string $action, User $actor): void
    {
        $this->assertAdministrator($actor);
        $this->assertRoleWorkspace($role);
        abort_if(in_array($role->slug, ['super-admin','admin','administrator'], true), 422, 'Administrator permissions are always enabled.');
        abort_unless(isset(AccessControlService::MODULES[$module]) && in_array($action, AccessControlService::ACTIONS, true), 422);
        $row = RoleModuleAccess::firstOrCreate(['role_id' => $role->id, 'module_code' => $module], ['record_scope' => 'none', 'actions' => []]);
        $actions = collect($row->actions ?: []);
        $actions = $actions->contains($action) ? $actions->reject(fn ($x) => $x === $action) : $actions->push($action);
        $actions = $actions->unique()->values()->all();
        $row->update(['actions' => $actions, 'record_scope' => $actions && $row->record_scope === 'none' ? ($role->default_scope ?: 'assigned_jobs') : $row->record_scope]);
        $this->audit($role, 'access.permission_changed', ($actions && in_array($action, $actions, true) ? 'Granted ' : 'Removed ').$action.' on '.$module.' for '.$role->name, $actor, compact('module','action'));
    }

    public function setScope(Role $role, string $module, string $scope, User $actor): void
    {
        $this->assertAdministrator($actor);
        $this->assertRoleWorkspace($role);
        abort_if(in_array($role->slug, ['super-admin','admin','administrator'], true), 422, 'Administrator scope is always all records.');
        abort_unless(in_array($scope, ['none','own_records','assigned_jobs','selected_clients','department','all_records'], true), 422);
        $row = RoleModuleAccess::firstOrCreate(['role_id' => $role->id, 'module_code' => $module], ['actions' => [], 'record_scope' => 'none']);
        $row->update(['record_scope' => $scope]);
        $this->audit($role, 'access.scope_changed', 'Changed '.$module.' scope for '.$role->name.' to '.str_replace('_', ' ', $scope), $actor, compact('module','scope'));
    }

    public function assignRole(User $user, Role $role, User $actor): void
    {
        $this->assertAdministrator($actor);
        $this->assertRoleWorkspace($role);
        abort_if($user->isSuperAdmin() && $role->slug !== 'super-admin', 422, 'A Super Admin cannot be downgraded here.');
        $old = $user->role?->name ?: 'No role';
        $user->update(['role_id' => $role->id]);
        $this->syncMembership($user);
        $this->audit($user, 'access.role_assigned', 'Role changed from '.$old.' to '.$role->name, $actor, ['old' => $old, 'new' => $role->name]);
    }

    public function toggleUserActive(User $user, User $actor): void
    {
        $this->assertAdministrator($actor);
        abort_if($user->isSuperAdmin(), 422, 'Super Admin cannot be deactivated.');
        $user->update(['is_active' => !$user->is_active]);
        $this->syncMembership($user);
        $this->audit($user, 'access.user_status_changed', 'User '.($user->is_active ? 'activated' : 'deactivated'), $actor);
    }

    public function auditLog()
    {
        return Activity::with('user')->where('event', 'like', 'access.%')->latest()->limit(100)->get();
    }

    public function securitySettings(): array
    {
        $defaults = [
            'require_mfa_privileged' => ['Require MFA for privileged roles', true],
            'strong_password_policy' => ['Strong password policy', true],
            'automatic_timeout' => ['Automatic timeout', true],
            'restrict_bulk_exports' => ['Restrict bulk exports', true],
            'temporary_access_expiry' => ['Temporary access expiry', true],
            'quarterly_access_review' => ['Quarterly access review', true],
        ];
        $rows = MasterRecord::where('workspace_id', $this->workspaceId())->where('type', 'security_setting')->get()->keyBy('code');
        return collect($defaults)->map(function ($item, $code) use ($rows) {
            $row = $rows->get($code);
            return ['code' => $code, 'label' => $item[0], 'enabled' => $row ? (bool) data_get($row->metadata, 'enabled', true) : $item[1]];
        })->values()->all();
    }

    public function toggleSecurity(string $code, User $actor): void
    {
        $this->assertAdministrator($actor);
        $settings = collect($this->securitySettings())->keyBy('code');
        abort_unless($settings->has($code), 422);
        $current = $settings[$code];
        MasterRecord::updateOrCreate(
            ['workspace_id' => $this->workspaceId(), 'type' => 'security_setting', 'code' => $code],
            ['name' => $current['label'], 'metadata' => ['enabled' => !$current['enabled']], 'status' => 'active', 'sort_order' => 0],
        );
        $this->audit($actor, 'access.security_changed', $current['label'].' '.(!$current['enabled'] ? 'enabled' : 'disabled'), $actor);
    }

    public function toggleRule(int $id): void
    {
        $this->assertAdministrator(auth()->user());
        $r = NotificationRule::findOrFail($id);
        $r->update(['is_active' => !$r->is_active]);
    }


    private function workspaceId(): int
    {
        return app(SetupContext::class)->workspaceId();
    }

    private function assertRoleWorkspace(Role $role): void
    {
        abort_unless((int) ($role->workspace_id ?: $this->workspaceId()) === $this->workspaceId(), 404);
    }

    private function assertAdministrator(?User $actor): void
    {
        abort_unless($actor && app(AccessControlService::class)->isAdministrator($actor), 403);
    }

    private function syncMembership(User $user): void
    {
        if (!$user->role_id) return;
        WorkspaceMembership::updateOrCreate(
            ['workspace_id' => $this->workspaceId(), 'user_id' => $user->id],
            ['role_id' => $user->role_id, 'department_id' => $user->department_id, 'status' => $user->is_active ? 'active' : 'inactive', 'joined_at' => $user->created_at ?: now()],
        );
    }

    private function audit(object $subject, string $event, string $description, ?User $actor = null, array $meta = []): void
    {
        Activity::create([
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'user_id' => ($actor ?: auth()->user())?->id,
            'event' => $event,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);
    }
}
