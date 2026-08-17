<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Services\DashboardService;
use Livewire\Attributes\On;
use Livewire\Component;

class Secondary extends Component
{
    use RefreshesFromWorkspace;
    public function placeholder(): string
    {
        return view('livewire.dashboard.secondary-placeholder')->render();
    }

    public function render()
    {
        return view('livewire.dashboard.secondary', app(DashboardService::class)->secondaryData(auth()->user()));
    }
}
