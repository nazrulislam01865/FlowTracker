<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\ReportService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        app(ReportService::class)->forget((int) auth()->id());
    }

    #[Computed]
    public function kpis(): array
    {
        return app(ReportService::class)->kpis(auth()->user());
    }

    #[Computed]
    public function phase()
    {
        return app(ReportService::class)->phase(auth()->user());
    }

    #[Computed]
    public function workload()
    {
        return app(ReportService::class)->workload(auth()->user());
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
