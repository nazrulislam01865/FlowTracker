<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public function placeholder()
    {
        return view('livewire.dashboard.placeholder');
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Pusher/Livewire event forces a fresh render for visible dashboard data.
    }

    public function render()
    {
        return view('livewire.dashboard.index', app(DashboardService::class)->data(auth()->user()));
    }
}
