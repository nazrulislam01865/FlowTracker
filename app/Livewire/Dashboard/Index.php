<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\DashboardService;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render the KPI row so Tagged Comments updates at the same time as
        // the dedicated tagged-comments component.
    }

    public function render()
    {
        return view('livewire.dashboard.index', app(DashboardService::class)->primaryData(auth()->user()));
    }
}
