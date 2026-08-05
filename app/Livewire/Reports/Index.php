<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\ReportService;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;

    public bool $secondaryReady = false;

    public function loadSecondaryReports(): void
    {
        $this->secondaryReady = true;
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        app(ReportService::class)->forget((int) auth()->id());
    }

    public function render()
    {
        $service = app(ReportService::class);
        $data = $service->initialData(auth()->user());

        if ($this->secondaryReady) {
            $data += $service->secondaryData(auth()->user());
        }

        return view('livewire.reports.index', $data);
    }
}
