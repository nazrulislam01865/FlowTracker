<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowPhase;
use App\Models\WorkflowTemplate;
use Illuminate\Support\Collection;

class FilterOptionService
{
    public function options(
        User $user,
        string $type,
        string $context = '',
        string $search = '',
        int|string|null $selectedId = null,
        int $limit = 20,
        array $constraints = [],
    ): Collection {
        $limit = max(1, min(20, $limit));
        $search = trim($search);

        $items = match ($type) {
            'clients' => $this->clients($user, $context, $search, $limit),
            'jobs' => $this->jobs($user, $context, $search, $limit),
            'users' => $this->users($user, $context, $search, $limit),
            'product-categories' => $this->productCategories($user, $context, $search, $limit),
            'products' => $this->products($user, $context, $search, $limit, (string) ($constraints['category'] ?? '')),
            'workflows' => $this->workflows($user, $context, $search, $limit, (int) ($constraints['client_id'] ?? 0) ?: null),
            'priorities' => $this->masterOptions('priority', $search, $limit),
            'task-statuses' => $this->masterOptions('task_status', $search, $limit),
            'document-categories' => $this->masterOptions('document_category', $search, $limit),
            'countries' => $this->countries($user, $context, $search, $limit),
            'job-statuses' => $this->jobStatuses($user, $search, $limit),
            'job-healths' => $this->jobHealths($user, $search, $limit),
            'phases' => $this->phases($search, $limit),
            default => collect(),
        };

        if (filled($selectedId) && !$items->contains(fn ($item) => (string) $item['id'] === (string) $selectedId)) {
            $selected = match ($type) {
                'clients' => $this->clientById($user, $context, $selectedId),
                'jobs' => $this->jobById($user, $context, $selectedId),
                'users' => $this->userById($user, $context, $selectedId),
                'product-categories' => $this->productCategoryByName($user, $context, (string) $selectedId),
                'products' => $this->productByName($user, $context, (string) $selectedId, (string) ($constraints['category'] ?? '')),
                'workflows' => $this->workflowById($user, $context, $selectedId, (int) ($constraints['client_id'] ?? 0) ?: null),
                'priorities' => $this->masterByName('priority', (string) $selectedId),
                'task-statuses' => $this->masterByName('task_status', (string) $selectedId),
                'document-categories' => $this->masterByName('document_category', (string) $selectedId),
                'countries' => $this->countryByName($user, $context, (string) $selectedId),
                'job-statuses' => $this->jobStatusByName($user, (string) $selectedId),
                'job-healths' => $this->jobHealthByName($user, (string) $selectedId),
                'phases' => $this->phaseById($selectedId),
                default => null,
            };
            if ($selected) $items->prepend($selected);
        }

        return $items->unique(fn ($item) => (string) $item['id'])->take($limit)->values();
    }

    private function clients(User $user, string $context, string $search, int $limit): Collection
    {
        $query = $context === 'board-task-pack'
            ? Client::query()->whereIn(
                'clients.id',
                app(BoardTaskPackService::class)->visibleJobQuery($user)->reorder()->select('flow_jobs.client_id'),
            )
            : app(ClientService::class)->visibleQuery($user);

        return $query
            ->where('is_active', true)
            ->when(strlen($search) >= 2, fn ($q) => $q->where(fn ($x) => $x
                ->where('name', 'like', $search.'%')
                ->orWhere('code', 'like', $search.'%')))
            ->when(strlen($search) < 2, fn ($q) => $q->orderByDesc('updated_at'))
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'country'])
            ->map(fn (Client $client) => [
                'id' => (int) $client->id,
                'label' => (string) $client->name,
                'meta' => (string) ($client->country ?: ''),
            ]);
    }

    private function clientById(User $user, string $context, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $query = $context === 'board-task-pack'
            ? Client::query()->whereIn(
                'clients.id',
                app(BoardTaskPackService::class)->visibleJobQuery($user)->reorder()->select('flow_jobs.client_id'),
            )
            : app(ClientService::class)->visibleQuery($user);
        $row = $query->where('is_active', true)->find((int) $id, ['id', 'name', 'country']);
        return $row ? ['id'=>(int)$row->id, 'label'=>(string)$row->name, 'meta'=>(string)($row->country ?: '')] : null;
    }

    private function jobs(User $user, string $context, string $search, int $limit): Collection
    {
        $query = match ($context) {
            'documents' => app(JobService::class)->visibleQuery($user)->whereHas('client', fn ($client) => $client->where('is_active', true)),
            'board-task-pack' => app(BoardTaskPackService::class)->visibleJobQuery($user),
            default => app(JobService::class)->activeQuery($user),
        };

        return $query
            ->when(strlen($search) >= 2, fn ($q) => $q->where(fn ($x) => $x
                ->where('job_number', 'like', $search.'%')
                ->orWhere('title', 'like', '%'.$search.'%')))
            ->with('client:id,name')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['id', 'job_number', 'title', 'client_id', 'updated_at'])
            ->map(fn (FlowJob $job) => [
                'id' => (int) $job->id,
                'label' => trim($job->displayOrderNumber().' — '.$job->title),
                'meta' => (string) ($job->client?->name ?: ''),
            ]);
    }

    private function jobById(User $user, string $context, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $query = match ($context) {
            'documents' => app(JobService::class)->visibleQuery($user),
            'board-task-pack' => app(BoardTaskPackService::class)->visibleJobQuery($user),
            default => app(JobService::class)->activeQuery($user),
        };
        $job = $query->with('client:id,name')->find((int) $id, ['id','job_number','title','client_id']);
        return $job ? ['id'=>(int)$job->id, 'label'=>trim($job->displayOrderNumber().' — '.$job->title), 'meta'=>(string)($job->client?->name ?: '')] : null;
    }

    private function users(User $user, string $context, string $search, int $limit): Collection
    {
        return $this->visibleUsers($user, $context)
            ->with('department:id,name')
            ->when(strlen($search) >= 2, fn ($q) => $q->where('name', 'like', $search.'%'))
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'department_id', 'name', 'profile_image_path'])
            ->map(fn (User $row) => [
                'id' => (int) $row->id,
                'label' => (string) $row->name,
                'meta' => (string) ($row->department?->name ?: ''),
                'avatarUrl' => $row->profileImageUrl(),
            ]);
    }

    private function userById(User $user, string $context, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $row = $this->visibleUsers($user, $context)
            ->with('department:id,name')
            ->find((int) $id, ['id', 'department_id', 'name', 'profile_image_path']);

        return $row ? [
            'id' => (int) $row->id,
            'label' => (string) $row->name,
            'meta' => (string) ($row->department?->name ?: ''),
            'avatarUrl' => $row->profileImageUrl(),
        ] : null;
    }

    private function productCategories(User $user, string $context, string $search, int $limit): Collection
    {
        $this->authorizeCatalogAccess($user, $context);

        return MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType('product_category')
            ->active()
            ->when(strlen($search) >= 2, fn ($q) => $q->where(fn ($x) => $x
                ->where('name', 'like', $search.'%')
                ->orWhere('code', 'like', $search.'%')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'code'])
            ->map(fn (MasterRecord $category) => [
                'id' => (string) $category->name,
                'label' => (string) $category->name,
                'meta' => (string) ($category->code ?: ''),
            ]);
    }

    private function productCategoryByName(User $user, string $context, string $name): ?array
    {
        if ($name === '') return null;
        $this->authorizeCatalogAccess($user, $context);

        $row = MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType('product_category')
            ->active()
            ->where('name', $name)
            ->first(['id', 'name', 'code']);

        return $row ? [
            'id' => (string) $row->name,
            'label' => (string) $row->name,
            'meta' => (string) ($row->code ?: ''),
        ] : null;
    }

    private function products(User $user, string $context, string $search, int $limit, string $category): Collection
    {
        $this->authorizeCatalogAccess($user, $context);

        $workspaceId = app(SetupContext::class)->workspaceId();
        $categoryId = $this->productCategoryId($workspaceId, $category);

        return MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->active()
            ->with('parent:id,name')
            ->when($category !== '', function ($query) use ($category, $categoryId) {
                // New Product records are linked by parent_id. Some older/demo
                // records were imported before that relationship was enforced
                // and only carry the category in their description. Keep that
                // small compatibility fallback so Add Job works immediately
                // while the backfill migration repairs the stored relationship.
                $query->where(function ($scope) use ($category, $categoryId) {
                    if ($categoryId) {
                        $scope->where('parent_id', $categoryId);
                    }

                    $scope->orWhere(function ($legacy) use ($category) {
                        $legacy->whereNull('parent_id')
                            ->where(function ($description) use ($category) {
                                $description->where('description', $category)
                                    ->orWhere('description', 'like', $category.' ·%');
                            });
                    });
                });
            })
            ->when(strlen($search) >= 2, fn ($q) => $q->where(fn ($x) => $x
                ->where('name', 'like', $search.'%')
                ->orWhere('code', 'like', $search.'%')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'parent_id', 'name', 'code', 'description'])
            ->map(fn (MasterRecord $product) => [
                // Job items currently store the product name, so use that as
                // the selection value while keeping the database id internal.
                'id' => (string) $product->name,
                'label' => (string) $product->name,
                'meta' => (string) ($product->parent?->name ?: $this->legacyProductCategory($product->description) ?: $product->code ?: ''),
            ]);
    }

    private function productByName(User $user, string $context, string $name, string $category): ?array
    {
        if ($name === '') return null;
        $this->authorizeCatalogAccess($user, $context);

        $workspaceId = app(SetupContext::class)->workspaceId();
        $categoryId = $this->productCategoryId($workspaceId, $category);

        $row = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->active()
            ->with('parent:id,name')
            ->where('name', $name)
            ->when($category !== '', function ($query) use ($category, $categoryId) {
                $query->where(function ($scope) use ($category, $categoryId) {
                    if ($categoryId) {
                        $scope->where('parent_id', $categoryId);
                    }

                    $scope->orWhere(function ($legacy) use ($category) {
                        $legacy->whereNull('parent_id')
                            ->where(function ($description) use ($category) {
                                $description->where('description', $category)
                                    ->orWhere('description', 'like', $category.' ·%');
                            });
                    });
                });
            })
            ->first(['id', 'parent_id', 'name', 'code', 'description']);

        return $row ? [
            'id' => (string) $row->name,
            'label' => (string) $row->name,
            'meta' => (string) ($row->parent?->name ?: $this->legacyProductCategory($row->description) ?: $row->code ?: ''),
        ] : null;
    }

    private function productCategoryId(int $workspaceId, string $category): ?int
    {
        if ($category === '') return null;

        $id = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_category')
            ->active()
            ->where('name', $category)
            ->value('id');

        return $id ? (int) $id : null;
    }

    private function legacyProductCategory(?string $description): string
    {
        $description = trim((string) $description);
        if ($description === '') return '';

        return trim(explode(' ·', $description, 2)[0]);
    }

    private function authorizeCatalogAccess(User $user, string $context): void
    {
        $canCreate = $user->canAccess('jobs.create');
        $canEditFromJobDetail = $context === 'job-detail' && $user->canAccess('jobs.update');
        $canCreateInquiry = $context === 'create-inquiry' && $user->canModule('inquiries', 'create');
        abort_unless($canCreate || $canEditFromJobDetail || $canCreateInquiry, 403);
    }

    private function workflows(User $user, string $context, string $search, int $limit, ?int $clientId = null): Collection
    {
        $this->authorizeWorkflowOptions($user, $context);
        $workspaceId = app(SetupContext::class)->workspaceId();
        $appliesTo = match ($context) {
            'create-inquiry' => 'inquiries',
            default => null,
        };

        return WorkflowTemplate::query()
            ->where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->when(
                $context === 'create-job',
                fn ($query) => $query->where('applies_to', 'orders')->availableForOrderCreation($clientId),
                fn ($query) => $query->when($appliesTo, fn ($scope) => $scope->availableFor($appliesTo, $clientId)),
            )
            ->when(strlen($search) >= 2, fn ($q) => $q->where('name', 'like', $search.'%'))
            ->withCount(['phases' => fn ($q) => $q->where('is_active', true)])
            ->orderByRaw("CASE WHEN client_availability = 'specific' THEN 0 ELSE 1 END")
            ->when($context !== 'create-job', fn ($query) => $query->orderByRaw("CASE WHEN applies_to = 'inquiries' THEN 0 ELSE 1 END"))
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name'])
            ->map(fn (WorkflowTemplate $workflow) => [
                'id' => (int) $workflow->id,
                'label' => (string) $workflow->name,
                'meta' => $workflow->phases_count.' '.str('phase')->plural($workflow->phases_count),
            ]);
    }

    private function workflowById(User $user, string $context, int|string $id, ?int $clientId = null): ?array
    {
        if (!is_numeric($id)) return null;
        $this->authorizeWorkflowOptions($user, $context);
        $appliesTo = match ($context) {
            'create-inquiry' => 'inquiries',
            default => null,
        };

        $row = WorkflowTemplate::query()
            ->where('workspace_id', app(SetupContext::class)->workspaceId())
            ->where('is_active', true)
            ->when(
                $context === 'create-job',
                fn ($query) => $query->where('applies_to', 'orders')->availableForOrderCreation($clientId),
                fn ($query) => $query->when($appliesTo, fn ($scope) => $scope->availableFor($appliesTo, $clientId)),
            )
            ->withCount(['phases' => fn ($q) => $q->where('is_active', true)])
            ->find((int) $id, ['id', 'name']);

        return $row ? [
            'id' => (int) $row->id,
            'label' => (string) $row->name,
            'meta' => $row->phases_count.' '.str('phase')->plural($row->phases_count),
        ] : null;
    }


    private function masterOptions(string $type, string $search, int $limit): Collection
    {
        return MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType($type)
            ->active()
            ->when(strlen($search) >= 2, fn ($q) => $q->where(fn ($x) => $x
                ->where('name', 'like', $search.'%')
                ->orWhere('code', 'like', $search.'%')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'code'])
            ->map(fn (MasterRecord $record) => [
                'id' => (string) $record->name,
                'label' => (string) $record->name,
                'meta' => '',
            ]);
    }

    private function masterByName(string $type, string $name): ?array
    {
        if ($name === '') return null;

        $record = MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType($type)
            ->active()
            ->where('name', $name)
            ->first(['id', 'name', 'code']);

        return $record ? [
            'id' => (string) $record->name,
            'label' => (string) $record->name,
            'meta' => '',
        ] : null;
    }

    private function countries(User $user, string $context, string $search, int $limit): Collection
    {
        $active = $context !== 'clients-archived';

        return app(ClientService::class)->visibleQuery($user)
            ->where('is_active', $active)
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->when(strlen($search) >= 2, fn ($q) => $q->where('country', 'like', $search.'%'))
            ->distinct()
            ->orderBy('country')
            ->limit($limit)
            ->pluck('country')
            ->map(fn ($country) => ['id' => (string) $country, 'label' => (string) $country, 'meta' => '']);
    }

    private function countryByName(User $user, string $context, string $country): ?array
    {
        if ($country === '') return null;
        $active = $context !== 'clients-archived';
        $exists = app(ClientService::class)->visibleQuery($user)
            ->where('is_active', $active)
            ->where('country', $country)
            ->exists();

        return $exists ? ['id' => $country, 'label' => $country, 'meta' => ''] : null;
    }

    private function jobStatuses(User $user, string $search, int $limit): Collection
    {
        return app(JobService::class)->visibleQuery($user)
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->when(strlen($search) >= 2, fn ($q) => $q->where('status', 'like', $search.'%'))
            ->distinct()
            ->orderBy('status')
            ->limit($limit)
            ->pluck('status')
            ->map(fn ($status) => ['id' => (string) $status, 'label' => (string) $status, 'meta' => '']);
    }

    private function jobStatusByName(User $user, string $status): ?array
    {
        if ($status === '') return null;
        $exists = app(JobService::class)->visibleQuery($user)->where('status', $status)->exists();
        return $exists ? ['id' => $status, 'label' => $status, 'meta' => ''] : null;
    }

    private function jobHealths(User $user, string $search, int $limit): Collection
    {
        return app(JobService::class)->visibleQuery($user)
            ->whereNotNull('health')
            ->where('health', '!=', '')
            ->when(strlen($search) >= 2, fn ($q) => $q->where('health', 'like', $search.'%'))
            ->distinct()
            ->orderBy('health')
            ->limit($limit)
            ->pluck('health')
            ->map(fn ($health) => ['id' => (string) $health, 'label' => (string) $health, 'meta' => '']);
    }

    private function jobHealthByName(User $user, string $health): ?array
    {
        if ($health === '') return null;
        $exists = app(JobService::class)->visibleQuery($user)->where('health', $health)->exists();
        return $exists ? ['id' => $health, 'label' => $health, 'meta' => ''] : null;
    }

    private function phases(string $search, int $limit): Collection
    {
        $workspaceId = app(SetupContext::class)->workspaceId();

        return WorkflowPhase::query()
            ->whereNotNull('workflow_template_id')
            ->where('is_active', true)
            ->whereHas('workflowTemplate', fn ($workflow) => $workflow
                ->where('workspace_id', $workspaceId)
                ->where('is_active', true))
            ->with('workflowTemplate:id,name')
            ->when(strlen($search) >= 2, fn ($q) => $q->where(fn ($x) => $x
                ->where('name', 'like', $search.'%')
                ->orWhere('short_name', 'like', $search.'%')))
            ->orderBy('sequence')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'workflow_template_id', 'name', 'short_name', 'sequence'])
            ->map(fn (WorkflowPhase $phase) => [
                'id' => (int) $phase->id,
                'label' => (string) $phase->name,
                'meta' => (string) ($phase->workflowTemplate?->name ?: ''),
            ]);
    }

    private function phaseById(int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $workspaceId = app(SetupContext::class)->workspaceId();
        $phase = WorkflowPhase::query()
            ->whereNotNull('workflow_template_id')
            ->where('is_active', true)
            ->whereHas('workflowTemplate', fn ($workflow) => $workflow
                ->where('workspace_id', $workspaceId)
                ->where('is_active', true))
            ->with('workflowTemplate:id,name')
            ->find((int) $id, ['id', 'workflow_template_id', 'name']);

        return $phase ? [
            'id' => (int) $phase->id,
            'label' => (string) $phase->name,
            'meta' => (string) ($phase->workflowTemplate?->name ?: ''),
        ] : null;
    }

    private function authorizeWorkflowOptions(User $user, string $context): void
    {
        if ($context === 'board') {
            abort_unless($user->canAccess('tasks.view'), 403);
            return;
        }

        if ($context === 'create-inquiry') {
            abort_unless($user->canModule('inquiries', 'create'), 403);
            return;
        }

        abort_unless($user->canAccess('jobs.create'), 403);
    }

    private function visibleUsers(User $user, string $context)
    {
        if ($context === 'create-job') {
            if ($user->canModule('jobs', 'assign') || $user->canModule('tasks', 'assign')) {
                return User::query()->where('is_active', true);
            }

            return User::query()->where('is_active', true)->whereKey($user->id);
        }

        if ($context === 'create-inquiry') {
            return $user->canModule('inquiries', 'create')
                ? User::query()->where('is_active', true)
                : User::query()->where('is_active', true)->whereKey($user->id);
        }

        if ($context === 'task-assignee') {
            return $user->canModule('tasks', 'assign')
                ? User::query()->where('is_active', true)
                : User::query()->where('is_active', true)->whereKey($user->id);
        }

        if ($context === 'job-owner') {
            return $user->canModule('jobs', 'assign')
                ? User::query()->where('is_active', true)
                : User::query()->where('is_active', true)->whereKey($user->id);
        }

        if ($context === 'board-task-pack') {
            $visibleJobIds = app(BoardTaskPackService::class)
                ->visibleJobQuery($user)
                ->reorder()
                ->select('flow_jobs.id');

            $assigneeIds = Task::query()
                ->whereIn('flow_job_id', $visibleJobIds)
                ->whereNotNull('assignee_id')
                ->select('assignee_id')
                ->distinct();

            return User::query()
                ->where('is_active', true)
                ->whereIn('id', $assigneeIds);
        }

        $access = app(AccessControlService::class);
        $module = $context === 'clients' ? 'clients' : ($context === 'jobs' ? 'jobs' : 'tasks');
        if ($access->scope($user, $module) === 'all_records') {
            return User::query()->where('is_active', true);
        }

        $ids = match ($context) {
            'clients' => app(ClientService::class)->visibleQuery($user)
                ->whereNotNull('account_manager_id')
                ->distinct()
                ->pluck('account_manager_id'),
            'jobs' => app(JobService::class)->activeQuery($user)
                ->whereNotNull('owner_id')
                ->distinct()
                ->pluck('owner_id'),
            default => app(TaskService::class)->visibleQuery($user)
                ->whereNotNull('assignee_id')
                ->whereHas('job', fn ($job) => $job->whereHas('client', fn ($client) => $client->where('is_active', true)))
                ->distinct()
                ->pluck('assignee_id'),
        };

        return User::query()->where('is_active', true)->whereIn('id', $ids->push($user->id)->unique());
    }
}
