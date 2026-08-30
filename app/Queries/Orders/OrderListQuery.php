<?php

namespace App\Queries\Orders;

use App\Models\FlowJob;
use App\Models\User;
use App\Services\Orders\OrderReadService;
use App\Services\OrderListPrototypeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/** Permission-scoped read boundary for the operational Orders list. */
final class OrderListQuery
{
    public function __construct(
        private readonly OrderReadService $jobs,
        private readonly OrderListPrototypeService $prototype,
    ) {
    }

    public function filteredIds(User $actor, array $filters): Collection
    {
        return $this->jobs->filteredIds($actor, $filters);
    }

    public function paginateLegacy(User $actor, string $search, int $perPage, ?int $clientId, ?int $phaseId, ?int $assigneeId): LengthAwarePaginator
    {
        return $this->jobs->paginateOrders($actor, $search, $perPage, $clientId, $phaseId, $assigneeId);
    }

    public function bulkImportNumber(int $importId): ?string
    {
        return $this->jobs->bulkImportNumber($importId);
    }

    public function visible(User $actor, int $orderId): FlowJob
    {
        return $this->jobs->visibleQuery($actor)->findOrFail($orderId);
    }

    public function exists(User $actor, int $orderId): bool
    {
        return $orderId > 0 && $this->jobs->visibleQuery($actor)->whereKey($orderId)->exists();
    }

    public function visibleIds(User $actor, Collection $ids): Collection
    {
        return $this->jobs->visibleQuery($actor)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($value) => (int) $value)
            ->values();
    }

    public function visibleOrders(User $actor, Collection $ids): Collection
    {
        return $this->jobs->visibleQuery($actor)
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();
    }

    public function stages(User $actor): Collection
    {
        return $this->prototype->stages($actor);
    }

    /** @return Collection<int,array{id:int,name:string,short_name:string,sequence:int,color:string,count:int}> */
    public function myTaskStages(User $actor): Collection
    {
        return $this->prototype->stages($actor, true);
    }

    public function urgencyOptions(): Collection
    {
        return $this->prototype->urgencyOptions();
    }

    public function paginate(User $actor, array $filters, Collection $stages, int $perPage): LengthAwarePaginator
    {
        return $this->prototype->paginate($actor, $filters, $stages, $perPage);
    }

    public function paginateMyTasks(User $actor, array $filters, Collection $stages, int $perPage): LengthAwarePaginator
    {
        return $this->prototype->paginate($actor, $filters, $stages, $perPage, true);
    }

    public function rows(LengthAwarePaginator $orders, Collection $urgencies): array
    {
        return $this->prototype->rows($orders, $urgencies);
    }

    public function supplierOptions(): Collection
    {
        return $this->prototype->supplierOptions();
    }
}
