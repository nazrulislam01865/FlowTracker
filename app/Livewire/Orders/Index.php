<?php

namespace App\Livewire\Orders;

use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\FilterOptionService;
use App\Services\JobService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use RefreshesFromWorkspace;
    use WithPagination;

    public string $search = '';
    public string $client = '';
    public string $phase = '';
    public string $owner = '';
    public string $metricFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    #[Url(as: 'import', history: true, except: 0)]
    public int $importBatchId = 0;
    public string $importBatchLabel = '';
    public int $perPage = 25;
    public array $selectedOrderIds = [];
    public bool $showBulkDeleteConfirm = false;

    public function mount(): void
    {
        $this->search = trim((string) request('search', ''));
        $this->client = $this->numericFilterFromRequest('client');
        $this->phase = $this->numericFilterFromRequest('phase');
        $this->owner = $this->numericFilterFromRequest('owner');
        $this->dateFrom = $this->normalizeDateFilter((string) request('date_from', ''));
        $this->dateTo = $this->normalizeDateFilter((string) request('date_to', ''));
        $this->normalizeDateRange('from');

        $this->importBatchId = max(0, (int) request('import', $this->importBatchId));
        if ($this->importBatchId > 0) {
            $this->importBatchLabel = app(JobService::class)->bulkImportNumber($this->importBatchId) ?? '';
            if ($this->importBatchLabel === '') {
                $this->importBatchId = 0;
            }
        }

        if ($this->importBatchId > 0) {
            // The import batch behaves as one dedicated filter. It takes
            // precedence over any stale query-string filters on entry.
            $this->clearListFiltersExcept('importBatch');
        } elseif ($this->dateFrom !== '' || $this->dateTo !== '') {
            $this->clearListFiltersExcept('dateRange');
        }
    }

    public function updatedSearch(): void
    {
        $this->clearListFiltersExcept('search');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedClient(): void
    {
        $this->client = $this->normalizeNumericFilter($this->client);
        $this->clearListFiltersExcept('client');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedPhase(): void
    {
        $this->phase = $this->normalizeNumericFilter($this->phase);
        $this->clearListFiltersExcept('phase');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedOwner(): void
    {
        $this->owner = $this->normalizeNumericFilter($this->owner);
        $this->clearListFiltersExcept('owner');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->dateFrom = $this->normalizeDateFilter($this->dateFrom);
        $this->clearListFiltersExcept('dateRange');
        $this->normalizeDateRange('from');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->dateTo = $this->normalizeDateFilter($this->dateTo);
        $this->clearListFiltersExcept('dateRange');
        $this->normalizeDateRange('to');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        if ($this->search === '') {
            return;
        }

        $this->search = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function setMetricFilter(string $metric): void
    {
        if (! in_array($metric, ['createdToday', 'notStarted', 'inProgress', 'dueThisWeek', 'completedThisWeek', 'attention'], true)) {
            return;
        }

        $nextMetric = $this->metricFilter === $metric ? '' : $metric;

        // Summary cards and toolbar filters are mutually exclusive. Selecting
        // a card clears the search/dropdowns so only one Order list filter is
        // active and the visible rows always correspond to the selected card.
        $this->clearToolbarFilters();
        $this->metricFilter = $nextMetric;
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->client = '';
        $this->phase = '';
        $this->owner = '';
        $this->metricFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->importBatchId = 0;
        $this->importBatchLabel = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function openInvoiceAndPayment(int $id): void
    {
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'view'), 403);

        // Confirm the Order is inside the current user's visible Order scope
        // before navigating to its finance section.
        $job = app(JobService::class)->visibleQuery($user)->findOrFail($id);

        $this->redirectRoute('jobs.index', [
            'open' => $job->id,
            'tab' => 'finance',
        ], navigate: true);
    }

    public function deleteOrder(int $id): void
    {
        $service = app(JobService::class);
        $job = $service->visibleQuery(auth()->user())->findOrFail($id);

        $service->delete($job, auth()->user());
        $this->selectedOrderIds = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->reject(fn ($value) => $value === $id)
            ->values()
            ->all();
        $this->resetPage();

        session()->flash('success', $job->displayOrderNumber().' deleted successfully.');
    }

    public function toggleOrderSelection(int $id): void
    {
        $id = (int) $id;
        if ($id < 1 || ! app(JobService::class)->visibleQuery(auth()->user())->whereKey($id)->exists()) {
            return;
        }

        $selected = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique();

        $this->selectedOrderIds = $selected->contains($id)
            ? $selected->reject(fn ($value) => $value === $id)->values()->all()
            : $selected->push($id)->unique()->values()->all();
    }

    public function toggleOrderPageSelection(array $ids, bool $checked): void
    {
        $ids = collect($ids)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showBulkDeleteConfirm = false;
            return;
        }

        $visibleIds = app(JobService::class)
            ->visibleQuery(auth()->user())
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($value) => (int) $value)
            ->values();

        $selected = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique();

        $this->selectedOrderIds = $checked
            ? $selected->concat($visibleIds)->unique()->values()->all()
            : $selected->reject(fn ($value) => $visibleIds->contains($value))->values()->all();
    }

    public function clearOrderSelection(): void
    {
        $this->resetOrderSelection();
    }

    public function openBulkDeleteConfirmation(): void
    {
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'jobs', 'delete'), 403);

        $ids = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showBulkDeleteConfirm = false;
            return;
        }

        // Keep only Orders that are still inside the user's current visible scope.
        $visibleIds = app(JobService::class)
            ->visibleQuery($user)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($value) => (int) $value)
            ->values();

        $this->selectedOrderIds = $visibleIds->all();
        $this->showBulkDeleteConfirm = $visibleIds->isNotEmpty();
    }

    public function closeBulkDeleteConfirmation(): void
    {
        $this->showBulkDeleteConfirm = false;
    }

    public function bulkDeleteOrders(): void
    {
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'jobs', 'delete'), 403);

        $ids = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showBulkDeleteConfirm = false;
            return;
        }

        $service = app(JobService::class);
        $orders = $service->visibleQuery($user)
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            $this->resetOrderSelection();
            $this->showBulkDeleteConfirm = false;
            return;
        }

        foreach ($orders as $order) {
            $service->delete($order, $user);
        }

        $deletedCount = $orders->count();
        $this->showBulkDeleteConfirm = false;
        $this->resetOrderSelection();
        $this->resetPage();

        session()->flash(
            'success',
            $deletedCount.' '.\Illuminate\Support\Str::plural('order', $deletedCount).' deleted successfully.'
        );
    }

    public function render()
    {
        $user = auth()->user();
        $options = app(FilterOptionService::class);

        $service = app(JobService::class);

        return view('livewire.orders.index', [
            'jobs' => $service->paginateOrders(
                $user,
                $this->search,
                $this->perPage,
                $this->filterId($this->client),
                $this->filterId($this->phase),
                null,
                $this->filterId($this->owner),
                $this->metricFilter,
                $this->dateFrom,
                $this->dateTo,
                $this->importBatchId > 0 ? $this->importBatchId : null,
            ),
            'metrics' => $service->summaryCounts($user),
            'clientFilterOptions' => $this->selectedFilterOptions($options, $user, 'clients', 'jobs', $this->client),
            'phaseFilterOptions' => $this->selectedFilterOptions($options, $user, 'phases', 'order-list', $this->phase),
            'ownerFilterOptions' => $this->selectedFilterOptions($options, $user, 'users', 'order-list-owner', $this->owner),
        ]);
    }

    private function clearListFiltersExcept(string $except): void
    {
        if ($except !== 'search') {
            $this->search = '';
        }
        if ($except !== 'client') {
            $this->client = '';
        }
        if ($except !== 'phase') {
            $this->phase = '';
        }
        if ($except !== 'owner') {
            $this->owner = '';
        }
        if ($except !== 'dateRange') {
            $this->dateFrom = '';
            $this->dateTo = '';
        }
        if ($except !== 'importBatch') {
            $this->importBatchId = 0;
            $this->importBatchLabel = '';
        }

        $this->metricFilter = '';
    }

    private function clearToolbarFilters(): void
    {
        $this->search = '';
        $this->client = '';
        $this->phase = '';
        $this->owner = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->importBatchId = 0;
        $this->importBatchLabel = '';
    }

    private function normalizeDateFilter(string $value): string
    {
        $value = trim($value);
        if ($value === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }

        try {
            $date = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $value, 'UTC');
        } catch (\Throwable) {
            return '';
        }

        return $date && $date->format('Y-m-d') === $value ? $value : '';
    }

    private function normalizeDateRange(string $changed): void
    {
        if ($this->dateFrom === '' || $this->dateTo === '' || $this->dateFrom <= $this->dateTo) {
            return;
        }

        if ($changed === 'to') {
            $this->dateFrom = $this->dateTo;
            return;
        }

        $this->dateTo = $this->dateFrom;
    }

    private function selectedFilterOptions(
        FilterOptionService $options,
        User $user,
        string $type,
        string $context,
        string $value,
    ): Collection {
        $id = $this->filterId($value);

        return $id
            ? $options->options($user, $type, $context, '', $id, 5)
            : collect();
    }

    private function resetOrderSelection(): void
    {
        $this->selectedOrderIds = [];
        $this->showBulkDeleteConfirm = false;
    }

    private function numericFilterFromRequest(string $key): string
    {
        return $this->normalizeNumericFilter((string) request($key, ''));
    }

    private function normalizeNumericFilter(string $value): string
    {
        $value = trim($value);

        return $value !== '' && ctype_digit($value) && (int) $value > 0
            ? (string) ((int) $value)
            : '';
    }

    private function filterId(string $value): ?int
    {
        return $value !== '' && ctype_digit($value) && (int) $value > 0
            ? (int) $value
            : null;
    }
}
