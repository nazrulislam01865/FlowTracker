<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\User;
use App\Models\Workflow;
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
            'clients' => $this->clients($user, $search, $limit),
            'jobs' => $this->jobs($user, $context, $search, $limit),
            'users' => $this->users($user, $context, $search, $limit),
            'product-categories' => $this->productCategories($user, $context, $search, $limit),
            'products' => $this->products($user, $context, $search, $limit, (string) ($constraints['category'] ?? '')),
            'workflows' => $this->workflows($user, $search, $limit),
            default => collect(),
        };

        if (filled($selectedId) && !$items->contains(fn ($item) => (string) $item['id'] === (string) $selectedId)) {
            $selected = match ($type) {
                'clients' => $this->clientById($user, $selectedId),
                'jobs' => $this->jobById($user, $context, $selectedId),
                'users' => $this->userById($user, $context, $selectedId),
                'product-categories' => $this->productCategoryByName($user, $context, (string) $selectedId),
                'products' => $this->productByName($user, $context, (string) $selectedId, (string) ($constraints['category'] ?? '')),
                'workflows' => $this->workflowById($user, $selectedId),
                default => null,
            };
            if ($selected) $items->prepend($selected);
        }

        return $items->unique(fn ($item) => (string) $item['id'])->take($limit)->values();
    }

    private function clients(User $user, string $search, int $limit): Collection
    {
        return app(ClientService::class)->visibleQuery($user)
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

    private function clientById(User $user, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $row = app(ClientService::class)->visibleQuery($user)->where('is_active', true)->find((int) $id, ['id', 'name', 'country']);
        return $row ? ['id'=>(int)$row->id, 'label'=>(string)$row->name, 'meta'=>(string)($row->country ?: '')] : null;
    }

    private function jobs(User $user, string $context, string $search, int $limit): Collection
    {
        $query = $context === 'documents'
            ? app(JobService::class)->visibleQuery($user)->whereHas('client', fn ($client) => $client->where('is_active', true))
            : app(JobService::class)->activeQuery($user);

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
                'label' => trim($job->job_number.' — '.$job->title),
                'meta' => (string) ($job->client?->name ?: ''),
            ]);
    }

    private function jobById(User $user, string $context, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $query = $context === 'documents' ? app(JobService::class)->visibleQuery($user) : app(JobService::class)->activeQuery($user);
        $job = $query->with('client:id,name')->find((int) $id, ['id','job_number','title','client_id']);
        return $job ? ['id'=>(int)$job->id, 'label'=>trim($job->job_number.' — '.$job->title), 'meta'=>(string)($job->client?->name ?: '')] : null;
    }

    private function users(User $user, string $context, string $search, int $limit): Collection
    {
        return $this->visibleUsers($user, $context)
            ->with('department:id,name')
            ->when(strlen($search) >= 2, fn ($q) => $q->where('name', 'like', $search.'%'))
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'department_id', 'name'])
            ->map(fn (User $row) => [
                'id' => (int) $row->id,
                'label' => (string) $row->name,
                'meta' => (string) ($row->department?->name ?: ''),
            ]);
    }

    private function userById(User $user, string $context, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        $row = $this->visibleUsers($user, $context)
            ->with('department:id,name')
            ->find((int) $id, ['id', 'department_id', 'name']);

        return $row ? [
            'id' => (int) $row->id,
            'label' => (string) $row->name,
            'meta' => (string) ($row->department?->name ?: ''),
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
        abort_unless($canCreate || $canEditFromJobDetail, 403);
    }

    private function workflows(User $user, string $search, int $limit): Collection
    {
        abort_unless($user->canAccess('jobs.create'), 403);

        return Workflow::query()
            ->select(['id', 'name'])
            ->where('is_snapshot', false)
            ->where('is_active', true)
            ->when(strlen($search) >= 2, fn ($q) => $q->where('name', 'like', $search.'%'))
            ->withCount(['phases' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn (Workflow $workflow) => [
                'id' => (int) $workflow->id,
                'label' => (string) $workflow->name,
                'meta' => $workflow->phases_count.' '.str('phase')->plural($workflow->phases_count),
            ]);
    }

    private function workflowById(User $user, int|string $id): ?array
    {
        if (!is_numeric($id)) return null;
        abort_unless($user->canAccess('jobs.create'), 403);

        $row = Workflow::query()
            ->select(['id', 'name'])
            ->where('is_snapshot', false)
            ->where('is_active', true)
            ->withCount(['phases' => fn ($q) => $q->where('is_active', true)])
            ->find((int) $id);

        return $row ? [
            'id' => (int) $row->id,
            'label' => (string) $row->name,
            'meta' => $row->phases_count.' '.str('phase')->plural($row->phases_count),
        ] : null;
    }

    private function visibleUsers(User $user, string $context)
    {
        if ($context === 'create-job') {
            if ($user->canModule('jobs', 'assign') || $user->canModule('tasks', 'assign')) {
                return User::query()->where('is_active', true);
            }

            return User::query()->where('is_active', true)->whereKey($user->id);
        }

        if ($context === 'task-assignee') {
            return $user->canModule('tasks', 'assign')
                ? User::query()->where('is_active', true)
                : User::query()->where('is_active', true)->whereKey($user->id);
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

        return User::query()->where('is_active', true)->whereIn('id', $ids);
    }
}
