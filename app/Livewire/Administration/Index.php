<?php

namespace App\Livewire\Administration;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\AdminService;
use Livewire\Component;

class Index extends Component
{
    public string $tab = 'dashboard';
    public ?int $selectedRoleId = null;

    public bool $showUserModal = false;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public ?int $roleId = null;
    public ?int $departmentId = null;

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

    public function openUser(): void
    {
        $this->showUserModal = true;
        $this->resetValidation();
    }

    public function closeUser(): void { $this->showUserModal = false; }

    public function createUser(): void
    {
        $data = $this->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','string','min:10'],
            'roleId' => ['required','exists:roles,id'],
            'departmentId' => ['nullable','exists:departments,id'],
        ]);
        app(AdminService::class)->createUser([
            'name' => $data['name'], 'email' => $data['email'], 'password' => $data['password'],
            'role_id' => $data['roleId'], 'department_id' => $data['departmentId'],
        ]);
        $this->showUserModal = false;
        $this->reset(['name','email','password','roleId','departmentId']);
    }

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
        $roles = $service->roles();
        $selectedRole = $roles->firstWhere('id', $this->selectedRoleId) ?: $roles->first();
        if ($selectedRole && !$this->selectedRoleId) $this->selectedRoleId = $selectedRole->id;

        return view('livewire.administration.index', [
            'summary' => $service->summary(),
            'users' => $service->users(),
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'modules' => AccessControlService::MODULES,
            'actions' => AccessControlService::ACTIONS,
            'auditLog' => $service->auditLog(),
            'securitySettings' => $service->securitySettings(),
            'rules' => $service->notificationRules(),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
