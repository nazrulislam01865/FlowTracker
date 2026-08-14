<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\InquiryIntelligenceService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;

    public string $search = '';
    public string $period = 'month';
    public string $status = '';
    public string $priority = '';
    public int $assigneeId = 0;
    public string $activeTab = 'portfolio';
    public string $employeeFocus = 'all';
    public string $taskTab = 'recent';

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        unset($this->report);
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['portfolio','people','products'], true)) $this->activeTab = $tab;
    }

    public function setTaskTab(string $tab): void
    {
        if (in_array($tab, ['recent','longest','reopened'], true)) $this->taskTab = $tab;
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'status', 'priority', 'assigneeId');
        $this->period = 'month';
        unset($this->report);
    }

    #[Computed]
    public function report(): array
    {
        return app(InquiryIntelligenceService::class)->data(auth()->user(), $this->filters());
    }

    public function exportVisible()
    {
        $rows = app(InquiryIntelligenceService::class)->exportRows(auth()->user(), $this->filters());
        $filename = 'StepPromo-inquiry-intelligence-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Reference','Inquiry','Product','Created','Priority','Lead assignee','Progress','Status','Attention']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['reference'], $row['subject'], $row['product'], $row['created'], $row['priority'], $row['assignee'],
                    $row['progress'].'% '.$row['progress_text'], $row['status'], $row['attention'],
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function getTaskRowsProperty(): array
    {
        $rows = collect($this->report['people']['task_details'] ?? []);
        if ($this->employeeFocus !== 'all') $rows = $rows->where('assignee', $this->employeeFocus);

        return match ($this->taskTab) {
            'longest' => $rows->sortByDesc(fn (array $row) => $row['hours_value'] ?? -1)->values()->all(),
            'reopened' => $rows->where('reopened', true)->values()->all(),
            default => $rows->values()->all(),
        };
    }

    private function filters(): array
    {
        return [
            'search' => $this->search,
            'period' => $this->period,
            'status' => $this->status,
            'priority' => $this->priority,
            'assignee_id' => $this->assigneeId,
        ];
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
