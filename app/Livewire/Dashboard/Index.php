<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\DashboardService;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;

    public bool $secondaryReady = false;

    public function loadSecondaryDashboard(): void
    {
        $this->secondaryReady = true;
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        app(DashboardService::class)->forget((int) auth()->id());
    }

    public function render()
    {
        $service = app(DashboardService::class);
        $user = auth()->user();
        $data = $service->initialData($user);

        if ($this->secondaryReady) {
            $data += $service->secondaryData($user);
        }

        return view('livewire.dashboard.index', $data);
    }
}
