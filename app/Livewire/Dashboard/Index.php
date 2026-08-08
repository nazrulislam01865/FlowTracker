<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\DashboardService;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;

    public function render()
    {
        return view('livewire.dashboard.index', app(DashboardService::class)->primaryData(auth()->user()));
    }
}
