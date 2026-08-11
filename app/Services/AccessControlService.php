<?php

namespace App\Services;

use App\Models\InquiryTask;
use App\Models\RoleModuleAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class AccessControlService
{
    private array $accessCache = [];
    public const ACTIONS = ['view','create','edit_own','edit_all','delete','assign','link','export','manage'];

    /** Modules that are actually implemented and enforced by FlowTrack today. */
    public const MODULES = [
        'dashboard' => ['name' => 'Dashboard', 'group' => 'General'],
        'notifications' => ['name' => 'Notifications', 'group' => 'General'],
        'clients' => ['name' => 'Clients', 'group' => 'Commercial'],
        'inquiries' => ['name' => 'Inquiries', 'group' => 'Commercial'],
        'jobs' => ['name' => 'Orders', 'group' => 'Commercial'],
        'tasks' => ['name' => 'Tasks & Checklists', 'group' => 'Operations'],
        'documents' => ['name' => 'Documents', 'group' => 'Records'],
        'workflow' => ['name' => 'Workflow Setup', 'group' => 'Administration'],
        'taskpacks' => ['name' => 'Task Pack Setup', 'group' => 'Administration'],
        'masterdata' => ['name' => 'Master Data', 'group' => 'Administration'],
    ];

    /**
     * Every matrix action is selectable for every FlowTrack module.
     *
     * The matrix is the role's authoritative capability store: administrators
     * can grant any action without the UI silently disabling a cell. Existing
     * screens/services continue to enforce the actions they actually perform,
     * while additional granted capabilities remain available to any current or
     * future operation that checks that module/action pair.
     */
    public const SUPPORTED_ACTIONS = [
        'dashboard' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'notifications' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'clients' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'inquiries' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'jobs' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'tasks' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'documents' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'workflow' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'taskpacks' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
        'masterdata' => ['view','create','edit_own','edit_all','delete','assign','link','export','manage'],
    ];

    /** These modules do not have per-record ownership scope. */
    public const UNIVERSAL_RECORD_MODULES = ['dashboard', 'notifications', 'clients', 'workflow', 'taskpacks', 'masterdata'];

    public const SCOPED_MODULES = ['inquiries', 'jobs', 'tasks', 'documents'];

    public static function supportedActions(string $module): array
    {
        return self::SUPPORTED_ACTIONS[$module] ?? [];
    }

    public static function supportsAction(string $module, string $action): bool
    {
        if ($action === 'edit') {
            return in_array('edit_own', self::supportedActions($module), true)
                || in_array('edit_all', self::supportedActions($module), true);
        }

        return in_array($action, self::supportedActions($module), true);
    }

    public static function supportsScope(string $module): bool
    {
        return in_array($module, self::SCOPED_MODULES, true);
    }

    private const LEGACY = [
        'dashboard.view' => ['dashboard','view'],
        'jobs.view' => ['jobs','view'],
        'inquiries.view' => ['inquiries','view'],
        'inquiries.create' => ['inquiries','create'],
        'inquiries.update' => ['inquiries','edit'],
        'jobs.create' => ['jobs','create'],
        'jobs.update' => ['jobs','edit'],
        'tasks.view' => ['tasks','view'],
        'tasks.update' => ['tasks','edit'],
        'clients.view' => ['clients','view'],
        'documents.view' => ['documents','view'],
        'reports.view' => ['reports','view'],
        'notifications.view' => ['notifications','view'],
        'workflow.view' => ['workflow','view'],
        'workflow.create' => ['workflow','create'],
        'workflow.update' => ['workflow','edit'],
        'workflow.delete' => ['workflow','delete'],
        'workflow.manage' => ['workflow','manage'],
        'taskpacks.view' => ['taskpacks','view'],
        'taskpacks.create' => ['taskpacks','create'],
        'taskpacks.update' => ['taskpacks','edit'],
        'taskpacks.delete' => ['taskpacks','delete'],
        'taskpacks.manage' => ['taskpacks','manage'],
        'task-pack.manage' => ['taskpacks','manage'],
        'master.view' => ['masterdata','view'],
        'master.create' => ['masterdata','create'],
        'master.update' => ['masterdata','edit'],
        'master.delete' => ['masterdata','delete'],
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
        if (!isset(self::MODULES[$module]) || !self::supportsAction($module, $action)) return false;
        if (!$user->role || $user->role->is_active === false) return false;

        $access = $this->access($user, $module);
        if (!$access) return false;
        if (!self::isUniversalRecordModule($module) && $access->record_scope === 'none') return false;
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

        $access = $this->access($user, $module);
        if (self::isUniversalRecordModule($module)) {
            return $access && !empty($access->actions) ? 'all_records' : 'none';
        }

        return $access?->record_scope ?: ($user->role?->default_scope ?: 'none');
    }

    public static function isUniversalRecordModule(string $module): bool
    {
        return in_array($module, self::UNIVERSAL_RECORD_MODULES, true);
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
        if (!$this->can($user, 'tasks', 'view')) {
            return $query->whereHas('job', fn ($job) => $job->where('created_by', $user->id));
        }
        return $this->constrainTasks($query, $user, $this->scope($user, 'tasks'));
    }

    public function applyClientScope(Builder|Relation $query, User $user): Builder
    {
        $query = $this->eloquentBuilder($query);
        if (!$this->can($user, 'clients', 'view')) return $query->whereRaw('1 = 0');

        // Clients are workspace-wide reference records. Restricting the client
        // directory through Job ownership makes newly-created Clients vanish
        // until an Order exists, and prevents legitimate Order creators from
        // selecting shared Clients. Operational Jobs/Tasks remain scoped by
        // their own modules; only the Client directory itself is universal.
        return $query;
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

    public function applyInquiryTaskScope(Builder|Relation $query, User $user): Builder
    {
        $query = $this->eloquentBuilder($query);
        if (!$this->can($user, 'inquiries', 'view')) return $query->whereRaw('1 = 0');

        $query->whereIn(
            'inquiry_tasks.inquiry_id',
            app(InquiryService::class)->visibleQuery($user)->select('inquiries.id')
        );

        // The creator of an Inquiry always keeps access to its complete taskflow.
        // The standalone Tasks module can still be hidden by the role matrix; this
        // exception exists so the creator never sees a partial Inquiry they created.
        if (!$this->can($user, 'tasks', 'view')) {
            return $query->whereHas('inquiry', fn ($inquiry) => $inquiry->where('created_by', $user->id));
        }

        $scope = $this->scope($user, 'tasks');
        if ($scope === 'all_records') return $query;
        if ($scope === 'none') {
            return $query->whereHas('inquiry', fn ($inquiry) => $inquiry->where('created_by', $user->id));
        }
        if ($scope === 'department') {
            return $query->where(function ($scopeQuery) use ($user) {
                $scopeQuery->whereHas('inquiry', fn ($inquiry) => $inquiry->where('created_by', $user->id));
                if ($user->department_id) {
                    $scopeQuery->orWhereHas('assignee', fn ($assignee) => $assignee->where('department_id', $user->department_id));
                }
            });
        }

        return $query->where(function ($scopeQuery) use ($user) {
            $scopeQuery->where('inquiry_tasks.assignee_id', $user->id)
                ->orWhereHas('inquiry', fn ($inquiry) => $inquiry->where('created_by', $user->id));
        });
    }

    public function isJobCreator(User $user, object $job): bool
    {
        if (empty($job->id)) return false;
        if (method_exists($job, 'getAttributes') && array_key_exists('created_by', $job->getAttributes())) {
            return (int) ($job->created_by ?? 0) === (int) $user->id;
        }
        return (int) \App\Models\FlowJob::query()->whereKey($job->id)->value('created_by') === (int) $user->id;
    }

    public function isInquiryCreator(User $user, object $inquiry): bool
    {
        if (empty($inquiry->id)) return false;
        if (method_exists($inquiry, 'getAttributes') && array_key_exists('created_by', $inquiry->getAttributes())) {
            return (int) ($inquiry->created_by ?? 0) === (int) $user->id;
        }
        return (int) \App\Models\Inquiry::query()->whereKey($inquiry->id)->value('created_by') === (int) $user->id;
    }

    public function isTaskParentCreator(User $user, object $task): bool
    {
        if (empty($task->flow_job_id)) return false;
        return (int) \App\Models\FlowJob::query()->whereKey($task->flow_job_id)->value('created_by') === (int) $user->id;
    }

    public function isInquiryTaskParentCreator(User $user, InquiryTask $task): bool
    {
        if (empty($task->inquiry_id)) return false;
        return (int) \App\Models\Inquiry::query()->whereKey($task->inquiry_id)->value('created_by') === (int) $user->id;
    }

    public function canEditInquiryTask(User $user, InquiryTask $task): bool
    {
        if ($this->isAdministrator($user) || $this->isInquiryTaskParentCreator($user, $task)) return true;
        if (!$this->can($user, 'tasks', 'edit')) return false;
        if (!$this->applyInquiryTaskScope(InquiryTask::query()->whereKey($task->id), $user)->exists()) return false;
        if ($this->canEditAll($user, 'tasks')) return true;

        return (int) ($task->assignee_id ?? 0) === (int) $user->id;
    }

    public function canAssignInquiryTask(User $user, InquiryTask $task): bool
    {
        if ($this->isAdministrator($user) || $this->isInquiryTaskParentCreator($user, $task)) return true;
        if (!$this->can($user, 'tasks', 'assign')) return false;

        return $this->applyInquiryTaskScope(InquiryTask::query()->whereKey($task->id), $user)->exists();
    }

    public function canCreateInquiryTask(User $user, object $inquiry): bool
    {
        if ($this->isAdministrator($user) || $this->isInquiryCreator($user, $inquiry)) return true;
        if (!$this->can($user, 'tasks', 'create') || empty($inquiry->id)) return false;

        return app(InquiryService::class)->visibleQuery($user)->whereKey($inquiry->id)->exists();
    }

    public function canDeleteInquiryTask(User $user, InquiryTask $task): bool
    {
        if ($this->isAdministrator($user)) return true;
        if (!$this->can($user, 'tasks', 'delete')) return false;

        return $this->applyInquiryTaskScope(InquiryTask::query()->whereKey($task->id), $user)->exists();
    }

    public function canEditJob(User $user, object $job): bool
    {
        if ($this->isAdministrator($user) || $this->isJobCreator($user, $job)) return true;
        if (!$this->can($user, 'jobs', 'edit')) return false;

        $scope = $this->scope($user, 'jobs');
        if (!$this->jobWithinScope($job, $user, $scope)) return false;
        if ($this->canEditAll($user, 'jobs')) return true;

        return (int) ($job->owner_id ?? 0) === (int) $user->id
            || (int) ($job->coordinator_id ?? 0) === (int) $user->id;
    }

    /**
     * Authorization for a Job that was already loaded through visibleQuery().
     * This avoids repeating an EXISTS scope query for every rendered row/card.
     */
    public function canEditVisibleJob(User $user, object $job): bool
    {
        if ($this->isAdministrator($user) || $this->isJobCreator($user, $job)) return true;
        if (!$this->can($user, 'jobs', 'edit')) return false;
        if ($this->canEditAll($user, 'jobs')) return true;

        return (int) ($job->owner_id ?? 0) === (int) $user->id
            || (int) ($job->coordinator_id ?? 0) === (int) $user->id;
    }

    public function canEditTask(User $user, object $task): bool
    {
        if ($this->isAdministrator($user) || $this->isTaskParentCreator($user, $task)) return true;
        if (!$this->can($user, 'tasks', 'edit')) return false;

        $scope = $this->scope($user, 'tasks');
        if (!$this->taskWithinScope($task, $user, $scope)) return false;
        if ($this->canEditAll($user, 'tasks')) return true;
        return (int) ($task->assignee_id ?? 0) === (int) $user->id;
    }

    /** Authorization for a Task already loaded through visibleQuery(). */
    public function canEditVisibleTask(User $user, object $task): bool
    {
        if ($this->isAdministrator($user) || $this->isTaskParentCreator($user, $task)) return true;
        if (!$this->can($user, 'tasks', 'edit')) return false;
        if ($this->canEditAll($user, 'tasks')) return true;

        return (int) ($task->assignee_id ?? 0) === (int) $user->id;
    }

    public function canAssignJob(User $user, object $job): bool
    {
        if ($this->isAdministrator($user) || $this->isJobCreator($user, $job)) return true;
        if (!$this->can($user, 'jobs', 'assign')) return false;
        return $this->jobWithinScope($job, $user, $this->scope($user, 'jobs'));
    }

    /** Assignment authorization for a Job already loaded through visibleQuery(). */
    public function canAssignVisibleJob(User $user): bool
    {
        return $this->isAdministrator($user) || $this->can($user, 'jobs', 'assign');
    }

    /**
     * Job workflow/status transitions are intentionally stricter than normal
     * inline Job editing. For non-administrators, the role must allow Job
     * editing and the user must be the Job's assigned owner. Older Jobs that
     * do not have an owner fall back to the coordinator so they remain usable.
     */
    public function canChangeJobStatus(User $user, object $job): bool
    {
        if ($this->isAdministrator($user) || $this->isJobCreator($user, $job)) return true;
        if (!$this->can($user, 'jobs', 'edit')) return false;
        if (!$this->jobWithinScope($job, $user, $this->scope($user, 'jobs'))) return false;

        return $this->canEditJob($user, $job);
    }

    /** Status authorization for a Job already loaded through visibleQuery(). */
    public function canChangeVisibleJobStatus(User $user, object $job): bool
    {
        if ($this->isAdministrator($user) || $this->isJobCreator($user, $job)) return true;
        if (!$this->can($user, 'jobs', 'edit')) return false;
        if ($this->canEditAll($user, 'jobs')) return true;

        return (int) ($job->owner_id ?? 0) === (int) $user->id
            || (int) ($job->coordinator_id ?? 0) === (int) $user->id;
    }

    public function canAssignTask(User $user, object $task): bool
    {
        if ($this->isAdministrator($user) || $this->isTaskParentCreator($user, $task)) return true;
        if (!$this->can($user, 'tasks', 'assign')) return false;

        return $this->taskWithinScope($task, $user, $this->scope($user, 'tasks'));
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
        if ($scope === 'none') return $query->where('created_by', $user->id);

        if ($scope === 'department') {
            return $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id);
                if ($user->department_id) {
                    $q->orWhereHas('owner', fn ($u) => $u->where('department_id', $user->department_id))
                        ->orWhereHas('coordinator', fn ($u) => $u->where('department_id', $user->department_id))
                        ->orWhereHas('members.user', fn ($u) => $u->where('department_id', $user->department_id))
                        ->orWhereHas('tasks.assignee', fn ($u) => $u->where('department_id', $user->department_id));
                }
            });
        }

        if ($scope === 'own_records') {
            return $query->where(fn ($q) => $q->where('created_by', $user->id)
                ->orWhere('owner_id', $user->id)
                ->orWhere('coordinator_id', $user->id));
        }

        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhere('owner_id', $user->id)
                ->orWhere('coordinator_id', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
                ->orWhereHas('tasks', fn ($t) => $t->where('assignee_id', $user->id));
        });
    }

    private function constrainTasks(Builder $query, User $user, string $scope): Builder
    {
        if ($scope === 'all_records') return $query;
        if ($scope === 'none') {
            return $query->whereHas('job', fn ($job) => $job->where('created_by', $user->id));
        }
        if ($scope === 'department') {
            return $query->where(function ($scopeQuery) use ($user) {
                $scopeQuery->whereHas('job', fn ($job) => $job->where('created_by', $user->id));
                if ($user->department_id) {
                    $scopeQuery->orWhereHas('assignee', fn ($u) => $u->where('department_id', $user->department_id));
                }
            });
        }

        // Assigned/own task screens stay assignee-strict except that the creator
        // of the parent Order always sees every task generated for that Order.
        return $query->where(function ($scopeQuery) use ($user) {
            $scopeQuery->where('tasks.assignee_id', $user->id)
                ->orWhereHas('job', fn ($job) => $job->where('created_by', $user->id));
        });
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

    public function forgetRole(int $roleId): void
    {
        $prefix = $roleId.':';
        foreach (array_keys($this->accessCache) as $key) {
            if (str_starts_with($key, $prefix)) unset($this->accessCache[$key]);
        }
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
