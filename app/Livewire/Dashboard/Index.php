<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\DashboardService;
use Livewire\Attributes\Computed;
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

    #[Computed]
    public function metrics(): array
    {
        return app(DashboardService::class)->metrics(auth()->user());
    }

    #[Computed]
    public function attentionJobs()
    {
        return app(DashboardService::class)->attentionJobs(auth()->user());
    }

    #[Computed]
    public function phaseCounts()
    {
        return app(DashboardService::class)->phaseCounts(auth()->user());
    }

    #[Computed]
    public function workload()
    {
        return app(DashboardService::class)->workload(auth()->user());
    }

    #[Computed]
    public function deliveries()
    {
        return app(DashboardService::class)->deliveries(auth()->user());
    }

    #[Computed]
    public function activity()
    {
        return app(DashboardService::class)->activity(auth()->user());
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
