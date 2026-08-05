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
        app(DashboardService::class)->forget((int) auth()->id());
    }

    public function render()
    {
        return view('livewire.dashboard.index', app(DashboardService::class)->data(auth()->user()));
    }
}
