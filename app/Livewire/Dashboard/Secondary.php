<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;

class Secondary extends Component
{
    public function placeholder(): string
    {
        return view('livewire.dashboard.secondary-placeholder')->render();
    }

    public function render()
    {
        return view('livewire.dashboard.secondary', app(DashboardService::class)->secondaryData(auth()->user()));
    }
}
