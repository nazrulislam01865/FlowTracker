<?php

namespace App\Livewire\Administration;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\AdminService;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;
    public string $tab = 'dashboard';
    public ?int $selectedRoleId = null;

    public bool $showUserModal = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $position = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public ?int $roleId = null;
    public ?int $departmentId = null;
    public bool $userActive = true;

    public bool $showRoleModal = false;
    public ?int $editingRoleId = null;
    public string $roleName = '';
    public string $roleCode = '';
    public string $roleDescription = '';
    public string $roleDefaultScope = 'assigned_jobs';
    public bool $roleActive = true;

    public function mount(): void
    {
        $this->selectedRoleId = Role::where('slug', 'operations-manager')->value('id') ?: Role::where('slug', '!=', 'super-admin')->value('id') ?: Role::value('id');
    }

    public function setTab(string $tab): void
    {
        $allowed = ['dashboard','roles','matrix','users','audit','security','notification'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'dashboard';
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = Role::findOrFail($roleId)->id;
        $this->tab = 'matrix';
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
            $this->roleId = $user->role_id;
            $this->departmentId = $user->department_id;
            $this->userActive = (bool) $user->is_active;
        } else {
            $this->reset(['name','position','email','roleId','departmentId']);
            $this->userActive = true;
        }

        $this->showUserModal = true;
    }

    public function closeUser(): void
    {
        $this->showUserModal = false;
        $this->editingUserId = null;
        $this->resetValidation();
        $this->reset(['name','position','email','password','passwordConfirmation','roleId','departmentId']);
        $this->userActive = true;
    }

    public function saveUser(): void
    {
        $editing = $this->editingUserId !== null;
        $rules = [
            'name' => ['required','string','max:255'],
            'position' => ['nullable','string','max:120'],
            'email' => ['required','email', $editing ? 'unique:users,email,'.$this->editingUserId : 'unique:users,email'],
            'roleId' => ['required','exists:roles,id'],
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
            'role_id' => $data['roleId'],
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
            $this->roleDefaultScope = (string) $role->default_scope;
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
            'roleDefaultScope' => ['required','in:none,own_records,assigned_jobs,selected_clients,department,all_records'],
            'roleActive' => ['boolean'],
        ]);
        $role = app(AdminService::class)->saveRole([
            'name' => $data['roleName'], 'code' => $data['roleCode'], 'description' => $data['roleDescription'],
            'default_scope' => $data['roleDefaultScope'], 'is_active' => $data['roleActive'],
        ], $this->editingRoleId, auth()->user());
        $this->selectedRoleId = $role->id;
        $this->showRoleModal = false;
    }

    public function toggleMatrixAction(int $roleId, string $module, string $action): void
    {
        app(AdminService::class)->toggleMatrixAction(Role::findOrFail($roleId), $module, $action, auth()->user());
    }

    public function setModuleScope(int $roleId, string $module, string $scope): void
    {
        app(AdminService::class)->setScope(Role::findOrFail($roleId), $module, $scope, auth()->user());
    }

    public function assignRole(int $userId, int $roleId): void
    {
        app(AdminService::class)->assignRole(User::findOrFail($userId), Role::findOrFail($roleId), auth()->user());
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

    public function render()
    {
        $service = app(AdminService::class);

        return view('livewire.administration.index', match ($this->tab) {
            'roles' => $this->rolesPageData($service),
            'matrix' => $this->matrixPageData($service),
            'users' => $this->usersPageData($service),
            'audit' => ['auditLog' => $service->auditLog()],
            'security' => ['securitySettings' => $service->securitySettings()],
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
        $roles = $service->roles();
        $selectedRole = $roles->firstWhere('id', $this->selectedRoleId) ?: $roles->first();

        if ($selectedRole && !$this->selectedRoleId) {
            $this->selectedRoleId = $selectedRole->id;
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
