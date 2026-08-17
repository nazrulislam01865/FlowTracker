<?php

namespace App\Livewire\Administration;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Livewire\Concerns\RefreshesFromWorkspace;

use App\Models\Department;
use App\Models\Role;
use App\Models\RoleModuleAccess;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\AdminService;
use App\Services\BrandingService;
use App\Services\SetupContext;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use RefreshesFromWorkspace;
    use UsesPagePlaceholder;
    use HandlesInlineEdits;
    use WithFileUploads;
    public string $tab = 'dashboard';
    public ?int $selectedRoleId = null;

    public bool $showUserModal = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $position = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public array $roleIds = [];
    public ?int $departmentId = null;
    public bool $userActive = true;

    public $logoUpload = null;
    public $faviconUpload = null;

    public bool $showRoleModal = false;
    public ?int $editingRoleId = null;
    public string $roleName = '';
    public string $roleCode = '';
    public string $roleDescription = '';
    public string $roleDefaultScope = 'assigned_jobs';
    public bool $roleActive = true;

    public function mount(): void
    {
        $requestedTab = (string) request()->query('tab', '');
        if (in_array($requestedTab, ['dashboard','roles','matrix','users','audit','security','settings','branding'], true)) {
            $this->tab = $requestedTab;
        }


        if ($this->tab !== 'branding') {
            $this->selectedRoleId = Role::where('slug', 'operations-manager')->value('id') ?: Role::where('slug', '!=', 'super-admin')->value('id') ?: Role::value('id');
        }
    }

    public function setTab(string $tab): void
    {
        $allowed = ['dashboard','roles','matrix','users','audit','security','settings','branding'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'dashboard';
    }

    public function selectRole(int $roleId): void
    {
        $role = Role::query()
            ->where('workspace_id', app(SetupContext::class)->workspaceId())
            ->findOrFail($roleId);

        $this->selectedRoleId = $role->id;
        $this->tab = 'matrix';
    }

    public function selectMatrixRole(int $roleId): void
    {
        $role = Role::query()
            ->where('workspace_id', app(SetupContext::class)->workspaceId())
            ->findOrFail($roleId);

        $this->selectedRoleId = $role->id;
    }

    public function openUser(?int $id = null): void
    {
        $this->tab = 'users';
        $this->resetValidation();
        $this->editingUserId = $id;
        $this->password = '';
        $this->passwordConfirmation = '';

        if ($id) {
            $user = User::findOrFail($id);
            $this->name = $user->name;
            $this->position = app(AdminService::class)->positionFor($user) ?? '';
            $this->email = $user->email;
            $this->roleIds = $user->assignedRoleIds();
            $this->departmentId = $user->department_id;
            $this->userActive = (bool) $user->is_active;
        } else {
            $this->reset(['name','position','email','roleIds','departmentId']);
            $this->userActive = true;
        }

        $this->showUserModal = true;
    }

    public function closeUser(): void
    {
        $this->showUserModal = false;
        $this->editingUserId = null;
        $this->resetValidation();
        $this->reset(['name','position','email','password','passwordConfirmation','roleIds','departmentId']);
        $this->userActive = true;
    }

    public function saveUser(): void
    {
        $editing = $this->editingUserId !== null;
        $rules = [
            'name' => ['required','string','max:255'],
            'position' => ['nullable','string','max:120'],
            'email' => ['required','email', $editing ? 'unique:users,email,'.$this->editingUserId : 'unique:users,email'],
            'roleIds' => ['required','array','min:1'],
            'roleIds.*' => ['distinct', Rule::exists('roles', 'id')->where('workspace_id', app(SetupContext::class)->workspaceId())],
            'departmentId' => ['nullable','exists:departments,id'],
            'userActive' => ['boolean'],
            'password' => $editing ? ['nullable','string','min:10'] : ['required','string','min:10'],
            'passwordConfirmation' => $editing ? ['required_with:password','same:password'] : ['required','same:password'],
        ];
        $data = $this->validate($rules, [
            'passwordConfirmation.same' => 'The password confirmation does not match.',
        ]);

        $payload = [
            'name' => $data['name'],
            'position' => filled(trim((string) ($data['position'] ?? ''))) ? trim($data['position']) : null,
            'email' => $data['email'],
            'role_ids' => array_values($data['roleIds']),
            'department_id' => $data['departmentId'],
            'is_active' => $data['userActive'],
        ];
        if (filled($data['password'] ?? null)) $payload['password'] = $data['password'];

        if ($editing) {
            app(AdminService::class)->updateUser(User::findOrFail($this->editingUserId), $payload, auth()->user());
        } else {
            app(AdminService::class)->createUser($payload);
        }

        session()->flash('success', $editing ? 'User updated.' : 'User created.');
        $this->closeUser();
    }

    public function deleteUser(int $userId): void
    {
        app(AdminService::class)->deleteUser(User::findOrFail($userId), auth()->user());
        session()->flash('success', 'User deleted.');
    }

    public function createUser(): void { $this->saveUser(); }

    public function openRole(?int $id = null): void
    {
        $this->editingRoleId = $id;
        if ($id) {
            $role = Role::findOrFail($id);
            $this->roleName = $role->name;
            $this->roleCode = (string) $role->code;
            $this->roleDescription = (string) $role->description;
            $this->roleDefaultScope = (string) ($role->default_scope === 'selected_clients' ? 'assigned_jobs' : $role->default_scope);
            $this->roleActive = (bool) $role->is_active;
        } else {
            $this->reset(['roleName','roleCode','roleDescription']);
            $this->roleDefaultScope = 'assigned_jobs';
            $this->roleActive = true;
        }
        $this->showRoleModal = true;
    }

    public function closeRole(): void { $this->showRoleModal = false; }

    public function saveRole(): void
    {
        $data = $this->validate([
            'roleName' => ['required','string','max:255'],
            'roleCode' => ['nullable','string','max:80'],
            'roleDescription' => ['nullable','string','max:2000'],
            'roleDefaultScope' => ['required','in:none,own_records,assigned_jobs,department,all_records'],
            'roleActive' => ['boolean'],
        ]);
        $role = app(AdminService::class)->saveRole([
            'name' => $data['roleName'], 'code' => $data['roleCode'], 'description' => $data['roleDescription'],
            'default_scope' => $data['roleDefaultScope'], 'is_active' => $data['roleActive'],
        ], $this->editingRoleId, auth()->user());
        $this->selectedRoleId = $role->id;
        $this->showRoleModal = false;
    }

    public function deleteRole(int $roleId): void
    {
        $workspaceId = app(SetupContext::class)->workspaceId();
        $role = Role::query()->where('workspace_id', $workspaceId)->findOrFail($roleId);
        $roleName = $role->name;

        $affectedUsers = app(AdminService::class)->deleteRole($role, auth()->user());

        if ((int) $this->selectedRoleId === $roleId) {
            $this->selectedRoleId = Role::query()
                ->where('workspace_id', $workspaceId)
                ->orderByDesc('is_system')
                ->orderBy('name')
                ->value('id');
        }

        if ((int) $this->editingRoleId === $roleId) {
            $this->closeRole();
            $this->editingRoleId = null;
        }

        session()->flash(
            'success',
            $roleName.' was permanently deleted. '.($affectedUsers === 1
                ? '1 user role assignment was removed.'
                : $affectedUsers.' user role assignments were removed.'),
        );
    }

    public function toggleMatrixAction(int $roleId, string $module, string $action): void
    {
        app(AdminService::class)->toggleMatrixAction(Role::findOrFail($roleId), $module, $action, auth()->user());
    }

    #[Renderless]
    public function setMatrixAction(int $roleId, string $module, string $action, bool $enabled): array
    {
        $result = $this->persistInlineEdit('permission', function () use ($roleId, $module, $action, $enabled) {
            app(AdminService::class)->setMatrixAction(Role::findOrFail($roleId), $module, $action, $enabled, auth()->user());
        });

        return $this->withCanonicalMatrixState($result, $roleId, $module);
    }

    #[Renderless]
    public function setModuleScope(int $roleId, string $module, string $scope): array
    {
        $result = $this->persistInlineEdit('record scope', function () use ($roleId, $module, $scope) {
            app(AdminService::class)->setScope(Role::findOrFail($roleId), $module, $scope, auth()->user());
        });

        return $this->withCanonicalMatrixState($result, $roleId, $module);
    }

    private function withCanonicalMatrixState(array $result, int $roleId, string $module): array
    {
        if (($result['ok'] ?? false) !== true) return $result;

        $row = RoleModuleAccess::query()
            ->where('role_id', $roleId)
            ->where('module_code', $module)
            ->first(['actions', 'record_scope']);

        return array_merge($result, [
            'roleId' => $roleId,
            'module' => $module,
            'actions' => array_values($row?->actions ?: []),
            'recordScope' => (string) ($row?->record_scope ?: 'none'),
        ]);
    }

    #[Renderless]
    public function assignRole(int $userId, int $roleId): array
    {
        return $this->persistInlineEdit('user role', function () use ($userId, $roleId) {
            app(AdminService::class)->assignRole(User::findOrFail($userId), Role::findOrFail($roleId), auth()->user());
        });
    }

    public function toggleUserActive(int $userId): void
    {
        app(AdminService::class)->toggleUserActive(User::findOrFail($userId), auth()->user());
    }

    public function toggleSecurity(string $code): void
    {
        app(AdminService::class)->toggleSecurity($code, auth()->user());
    }

    public function toggleRule(int $id): void { app(AdminService::class)->toggleRule($id); }

    public function saveLogo()
    {
        $this->validate([
            'logoUpload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        app(BrandingService::class)->saveLogo($this->logoUpload, auth()->user());
        $this->reset('logoUpload');
        session()->flash('success', 'System logo updated.');

        return $this->redirectRoute('administration', ['tab' => 'branding']);
    }

    public function saveFavicon()
    {
        $this->validate([
            'faviconUpload' => ['required', 'file', 'mimes:ico,png,jpg,jpeg,webp', 'max:1024'],
        ]);

        app(BrandingService::class)->saveFavicon($this->faviconUpload, auth()->user());
        $this->reset('faviconUpload');
        session()->flash('success', 'Favicon updated.');

        return $this->redirectRoute('administration', ['tab' => 'branding']);
    }

    public function removeLogo()
    {
        app(BrandingService::class)->removeLogo(auth()->user());
        $this->reset('logoUpload');
        session()->flash('success', 'System logo removed.');

        return $this->redirectRoute('administration', ['tab' => 'branding']);
    }

    public function removeFavicon()
    {
        app(BrandingService::class)->removeFavicon(auth()->user());
        $this->reset('faviconUpload');
        session()->flash('success', 'Favicon removed.');

        return $this->redirectRoute('administration', ['tab' => 'branding']);
    }

    public function render()
    {
        $service = app(AdminService::class);

        return view('livewire.administration.index', match ($this->tab) {
            'roles' => $this->rolesPageData($service),
            'matrix' => $this->matrixPageData($service),
            'users' => $this->usersPageData($service),
            'audit' => ['auditLog' => $service->auditLog()],
            'security' => ['securitySettings' => $service->securitySettings()],
            'settings' => [],
            'branding' => ['branding' => app(BrandingService::class)->current()],
            default => $this->dashboardPageData($service),
        });
    }

    private function dashboardPageData(AdminService $service): array
    {
        return [
            'summary' => $service->summary(),
            'roles' => $service->roles(),
        ];
    }

    private function rolesPageData(AdminService $service): array
    {
        return ['roles' => $service->roles()];
    }

    private function matrixPageData(AdminService $service): array
    {
        // Keep role switching cheap: load the lightweight role selector once,
        // then fetch permission rows only for the selected role. The previous
        // implementation loaded every role's users + full matrix on each change.
        $roles = $service->roleOptions();
        $selectedRole = $roles->firstWhere('id', $this->selectedRoleId) ?: $roles->first();

        if ($selectedRole) {
            $selectedRole->load(['moduleAccess' => fn ($q) => $q
                ->select(['id', 'role_id', 'module_code', 'record_scope', 'actions'])]);

            if ((int) $this->selectedRoleId !== (int) $selectedRole->id) {
                $this->selectedRoleId = $selectedRole->id;
            }
        }

        return [
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'modules' => AccessControlService::MODULES,
            'actions' => AccessControlService::ACTIONS,
        ];
    }

    private function usersPageData(AdminService $service): array
    {
        return [
            'users' => $service->users(),
            'roles' => $service->roleOptions(),
            'departments' => Department::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }
}
