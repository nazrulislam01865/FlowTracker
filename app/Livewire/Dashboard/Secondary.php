<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Attributes\On;
use Livewire\Component;

class Secondary extends Component
{
    public function placeholder(): string
    {
        return view('livewire.dashboard.secondary-placeholder')->render();
    }

    #[On('flowtrack-refresh')]
    public function refreshWorkspace(): void
    {
        // Workspace data versioning invalidates shared dashboard caches.
    }

    public function render()
    {
        return view('livewire.dashboard.secondary', app(DashboardService::class)->secondaryData(auth()->user()));
    }
}
