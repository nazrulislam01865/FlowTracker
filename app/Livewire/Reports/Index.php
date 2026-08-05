<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Services\ReportService;
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

    public function render()
    {
        return view('livewire.reports.index', app(ReportService::class)->data(auth()->user()));
    }
}
