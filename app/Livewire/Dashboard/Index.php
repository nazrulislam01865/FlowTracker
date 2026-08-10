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
        $user = auth()->user();
        $data = app(DashboardService::class)->primaryData($user);
        $data['administratorView'] = app(\App\Services\AccessControlService::class)->isAdministrator($user);

        return view('livewire.dashboard.index', $data);
    }
}
