<?php

namespace App\Services;

use App\Models\RoleModuleAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class AccessControlService
{
    private array $accessCache = [];
    public const ACTIONS = ['view','create','edit_own','edit_all','delete','assign','approve','link','export','override','manage'];

    public const MODULES = [
        'dashboard' => ['name' => 'Dashboard', 'group' => 'General'],
        'clients' => ['name' => 'Clients', 'group' => 'Commercial'],
        'jobs' => ['name' => 'Jobs', 'group' => 'Commercial'],
        'tasks' => ['name' => 'Tasks & Checklists', 'group' => 'Operations'],
        'quotation' => ['name' => 'Quotations', 'group' => 'Commercial'],
        'artwork' => ['name' => 'Artwork', 'group' => 'Operations'],
        'sample' => ['name' => 'Swatch / Sample', 'group' => 'Operations'],
        'production' => ['name' => 'Production', 'group' => 'Operations'],
        'shipment' => ['name' => 'Shipment', 'group' => 'Operations'],
        'invoice' => ['name' => 'Invoice & Payment', 'group' => 'Finance'],
        'documents' => ['name' => 'Documents', 'group' => 'Records'],
        'reports' => ['name' => 'Reports', 'group' => 'Reporting'],
        'workflow' => ['name' => 'Workflow Setup', 'group' => 'Administration'],
        'masterdata' => ['name' => 'Master Data', 'group' => 'Administration'],
        'users' => ['name' => 'Users & Access', 'group' => 'Administration'],
        'audit' => ['name' => 'Audit Log', 'group' => 'Administration'],
        'notifications' => ['name' => 'Notifications', 'group' => 'General'],
    ];

    private const LEGACY = [
        'dashboard.view' => ['dashboard','view'],
        'jobs.view' => ['jobs','view'],
        'jobs.create' => ['jobs','create'],
        'jobs.update' => ['jobs','edit'],
        'tasks.view' => ['tasks','view'],
        'tasks.update' => ['tasks','edit'],
        'clients.view' => ['clients','view'],
        'documents.view' => ['documents','view'],
        'reports.view' => ['reports','view'],
        'notifications.view' => ['notifications','view'],
        'workflow.manage' => ['workflow','manage'],
        'master.manage' => ['masterdata','manage'],
        'users.manage' => ['users','manage'],
        'administration.manage' => ['users','manage'],
        'jobs.view-assigned' => ['jobs','view'],
        'jobs.view-all' => ['jobs','view'],
        'jobs.manage-all' => ['jobs','manage'],
        'tasks.view-assigned' => ['tasks','view'],
        'tasks.view-all' => ['tasks','view'],
        'clients.manage' => ['clients','manage'],
        'documents.manage' => ['documents','manage'],
        'financials.view' => ['invoice','view'],
        'workflows.manage' => ['workflow','manage'],
        'master-data.manage' => ['masterdata','manage'],
    ];

    public function isAdministrator(User $user): bool
    {
        return $user->is_super_admin || in_array($user->role?->slug, ['super-admin','admin','administrator'], true);
    }

    public function can(User $user, string $module, string $action = 'view'): bool
    {
        if (!$user->is_active) return false;
        if ($this->isAdministrator($user)) return true;
        if (!$user->role || $user->role->is_active === false) return false;

        $access = $this->access($user, $module);
        if (!$access || $access->record_scope === 'none') return false;
        $actions = $access->actions ?: [];

        if ($action === 'edit') {
            return in_array('edit_all', $actions, true) || in_array('edit_own', $actions, true) || in_array('manage', $actions, true);
        }

        return in_array($action, $actions, true) || in_array('manage', $actions, true);
    }

    public function canPermission(User $user, string $permission): bool
    {
        if (!$user->is_active) return false;
        if ($this->isAdministrator($user)) return true;
        if (isset(self::LEGACY[$permission])) {
            [$module, $action] = self::LEGACY[$permission];
            return $this->can($user, $module, $action);
        }

        if (str_contains($permission, '.')) {
            [$module, $action] = explode('.', $permission, 2);
            $module = match ($module) {
                'master-data' => 'masterdata',
                'workflows' => 'workflow',
                default => $module,
            };
            $action = match ($action) {
                'update' => 'edit',
                default => $action,
            };
            if (isset(self::MODULES[$module])) return $this->can($user, $module, $action);
        }

        return $user->role?->permissions()->where('slug', $permission)->exists() ?? false;
    }

    public function scope(User $user, string $module): string
    {
        if (!$user->is_active) return 'none';
        if ($this->isAdministrator($user)) return 'all_records';
        return $this->access($user, $module)?->record_scope ?: ($user->role?->default_scope ?: 'none');
    }

    public function canEditOwn(User $user, string $module): bool
    {
        if ($this->isAdministrator($user)) return true;
        $actions = $this->access($user, $module)?->actions ?: [];
        return in_array('edit_own', $actions, true) || in_array('edit_all', $actions, true) || in_array('manage', $actions, true);
    }

    public function canEditAll(User $user, string $module): bool
    {
        if ($this->isAdministrator($user)) return true;
        $actions = $this->access($user, $module)?->actions ?: [];
        return in_array('edit_all', $actions, true) || in_array('manage', $actions, true);
    }

    public function applyJobScope(Builder|Relation $query, User $user, string $module = 'jobs'): Builder
    {
        $query = $this->eloquentBuilder($query);
        if (!$this->can($user, $module, 'view')) return $query->whereRaw('1 = 0');
        return $this->constrainJobs($query, $user, $this->scope($user, $module));
    }

    public function applyTaskScope(Builder|Relation $query, User $user): Builder
    {
        $query = $this->eloquentBuilder($query);
        if (!$this->can($user, 'tasks', 'view')) return $query->whereRaw('1 = 0');
        return $this->constrainTasks($query, $user, $this->scope($user, 'tasks'));
    }

    public function applyClientScope(Builder|Relation $query, User $user): Builder
    {
        $query = $this->eloquentBuilder($query);
        if (!$this->can($user, 'clients', 'view')) return $query->whereRaw('1 = 0');
        $scope = $this->scope($user, 'clients');
        if ($scope === 'all_records') return $query;
        if ($scope === 'none') return $query->whereRaw('1 = 0');

        return $query->whereHas('jobs', fn ($jobs) => $this->constrainJobs($jobs, $user, $scope));
    }

    public function applyDocumentScope(Builder|Relation $query, User $user): Builder
    {
        $query = $this->eloquentBuilder($query);
        if (!$this->can($user, 'documents', 'view')) return $query->whereRaw('1 = 0');
        $scope = $this->scope($user, 'documents');
        if ($scope === 'all_records') return $query;
        if ($scope === 'none') return $query->whereRaw('1 = 0');

        return $query->where(function ($q) use ($user, $scope) {
            $q->whereHas('task', fn ($tasks) => $this->constrainTasks($tasks, $user, $scope))
                ->orWhereHas('job', fn ($jobs) => $this->constrainJobs($jobs, $user, $scope));
        });
    }

    public function canEditJob(User $user, object $job): bool
    {
        if ($this->isAdministrator($user)) return true;
        if (!$this->can($user, 'jobs', 'edit')) return false;

        $scope = $this->scope($user, 'jobs');
        if (!$this->jobWithinScope($job, $user, $scope)) return false;
        if ($this->canEditAll($user, 'jobs')) return true;

        return (int) ($job->owner_id ?? 0) === (int) $user->id
            || (int) ($job->coordinator_id ?? 0) === (int) $user->id
            || $this->isJobMember($job, $user);
    }

    public function canEditTask(User $user, object $task): bool
    {
        if ($this->isAdministrator($user)) return true;
        if (!$this->can($user, 'tasks', 'edit')) return false;

        $scope = $this->scope($user, 'tasks');
        if (!$this->taskWithinScope($task, $user, $scope)) return false;
        if ($this->canEditAll($user, 'tasks')) return true;
        return (int) ($task->assignee_id ?? 0) === (int) $user->id;
    }

    public function canAssignJob(User $user, object $job): bool
    {
        if ($this->isAdministrator($user)) return true;
        if (!$this->can($user, 'jobs', 'assign')) return false;
        return $this->jobWithinScope($job, $user, $this->scope($user, 'jobs'));
    }

    public function canAssignTask(User $user, object $task): bool
    {
        if ($this->isAdministrator($user)) return true;
        if (!$this->can($user, 'tasks', 'assign')) return false;

        $scope = $this->scope($user, 'tasks');
        if ($scope === 'all_records') return true;
        if ($scope === 'department') {
            $job = $task->job ?? (empty($task->flow_job_id) ? null : \App\Models\FlowJob::query()->find($task->flow_job_id));
            return $job ? $this->jobWithinScope($job, $user, 'department') : false;
        }

        // Assignment rights under Assigned Jobs apply to tasks in a Job the user is
        // explicitly assigned to, without making every task visible in My Work.
        $job = $task->job ?? (empty($task->flow_job_id) ? null : \App\Models\FlowJob::query()->find($task->flow_job_id));
        return $job ? $this->jobWithinScope($job, $user, 'assigned_jobs') : false;
    }

    /**
     * Eager-load callbacks receive the relation instance (for example HasMany),
     * while normal list queries pass an Eloquent Builder.  Access scoping must
     * support both without changing the relation's underlying query.
     */
    private function eloquentBuilder(Builder|Relation $query): Builder
    {
        return $query instanceof Relation ? $query->getQuery() : $query;
    }

    private function constrainJobs(Builder $query, User $user, string $scope): Builder
    {
        if ($scope === 'all_records') return $query;
        if ($scope === 'none') return $query->whereRaw('1 = 0');

        if ($scope === 'department') {
            if (!$user->department_id) return $query->whereRaw('1 = 0');
            return $query->where(function ($q) use ($user) {
                $q->whereHas('owner', fn ($u) => $u->where('department_id', $user->department_id))
                    ->orWhereHas('coordinator', fn ($u) => $u->where('department_id', $user->department_id))
                    ->orWhereHas('members.user', fn ($u) => $u->where('department_id', $user->department_id))
                    ->orWhereHas('tasks.assignee', fn ($u) => $u->where('department_id', $user->department_id));
            });
        }

        if ($scope === 'own_records') {
            return $query->where(fn ($q) => $q->where('owner_id', $user->id)->orWhere('coordinator_id', $user->id));
        }

        return $query->where(function ($q) use ($user) {
            $q->where('owner_id', $user->id)
                ->orWhere('coordinator_id', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
                ->orWhereHas('tasks', fn ($t) => $t->where('assignee_id', $user->id));
        });
    }

    private function constrainTasks(Builder $query, User $user, string $scope): Builder
    {
        if ($scope === 'all_records') return $query;
        if ($scope === 'none') return $query->whereRaw('1 = 0');
        if ($scope === 'department') {
            if (!$user->department_id) return $query->whereRaw('1 = 0');
            return $query->whereHas('assignee', fn ($u) => $u->where('department_id', $user->department_id));
        }

        // Own records and Assigned Jobs remain assignee-strict on task screens.
        return $query->where('tasks.assignee_id', $user->id);
    }

    private function jobWithinScope(object $job, User $user, string $scope): bool
    {
        if ($scope === 'all_records') return true;
        if ($scope === 'none' || empty($job->id)) return false;
        return $this->constrainJobs(\App\Models\FlowJob::query()->whereKey($job->id), $user, $scope)->exists();
    }

    private function taskWithinScope(object $task, User $user, string $scope): bool
    {
        if ($scope === 'all_records') return true;
        if ($scope === 'none' || empty($task->id)) return false;
        return $this->constrainTasks(\App\Models\Task::query()->whereKey($task->id), $user, $scope)->exists();
    }

    private function isJobMember(object $job, User $user): bool
    {
        return !empty($job->id) && \App\Models\FlowJobMember::query()
            ->where('flow_job_id', $job->id)->where('user_id', $user->id)->exists();
    }

    public function access(User $user, string $module): ?RoleModuleAccess
    {
        if (!$user->role_id) return null;
        $key = $user->role_id.':'.$module;
        if (!array_key_exists($key, $this->accessCache)) {
            $this->accessCache[$key] = RoleModuleAccess::query()
                ->where('role_id', $user->role_id)
                ->where('module_code', $module)
                ->first();
        }
        return $this->accessCache[$key];
    }
}
