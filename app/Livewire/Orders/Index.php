<?php

namespace App\Livewire\Orders;

use App\Models\User;
use App\Services\FilterOptionService;
use App\Services\JobService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $client = '';
    public string $phase = '';
    public string $owner = '';
    public int $perPage = 25;

    public function mount(): void
    {
        $this->search = trim((string) request('search', ''));
        $this->client = $this->numericFilterFromRequest('client');
        $this->phase = $this->numericFilterFromRequest('phase');
        $this->owner = $this->numericFilterFromRequest('owner');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedClient(): void
    {
        $this->client = $this->normalizeNumericFilter($this->client);
        $this->resetPage();
    }

    public function updatedPhase(): void
    {
        $this->phase = $this->normalizeNumericFilter($this->phase);
        $this->resetPage();
    }

    public function updatedOwner(): void
    {
        $this->owner = $this->normalizeNumericFilter($this->owner);
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        if ($this->search === '') {
            return;
        }

        $this->search = '';
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->client = '';
        $this->phase = '';
        $this->owner = '';
        $this->resetPage();
    }

    public function deleteOrder(int $id): void
    {
        $service = app(JobService::class);
        $job = $service->visibleQuery(auth()->user())->findOrFail($id);

        $service->delete($job, auth()->user());
        $this->resetPage();

        session()->flash('success', $job->displayOrderNumber().' deleted successfully.');
    }

    public function render()
    {
        $user = auth()->user();
        $options = app(FilterOptionService::class);

        return view('livewire.orders.index', [
            'jobs' => app(JobService::class)->paginateOrders(
                $user,
                $this->search,
                $this->perPage,
                $this->filterId($this->client),
                $this->filterId($this->phase),
                null,
                $this->filterId($this->owner),
            ),
            'clientFilterOptions' => $this->selectedFilterOptions($options, $user, 'clients', 'jobs', $this->client),
            'phaseFilterOptions' => $this->selectedFilterOptions($options, $user, 'phases', 'order-list', $this->phase),
            'ownerFilterOptions' => $this->selectedFilterOptions($options, $user, 'users', 'order-list-owner', $this->owner),
        ]);
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
