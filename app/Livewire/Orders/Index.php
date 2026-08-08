<?php

namespace App\Livewire\Orders;

use App\Services\JobService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 25;

    public function mount(): void
    {
        $this->search = trim((string) request('search', ''));
    }

    public function updatedSearch(): void
    {
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

    public function render()
    {
        return view('livewire.orders.index', [
            'jobs' => app(JobService::class)->paginateOrders(
                auth()->user(),
                $this->search,
                $this->perPage,
            ),
        ]);
    }
}
