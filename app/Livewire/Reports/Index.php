<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Services\InquiryIntelligenceService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use RefreshesFromWorkspace;
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
        $this->employeeFocus = 'all';
        $this->taskTab = 'recent';
        unset($this->report);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'period', 'status', 'priority', 'assigneeId'], true)) {
            $this->employeeFocus = 'all';
            $this->taskTab = 'recent';
            unset($this->report);
        }
    }

    public function updatedPeriod(): void
    {
        $this->status = '';
        $this->priority = '';
        $this->assigneeId = 0;
        $this->employeeFocus = 'all';
        $this->taskTab = 'recent';
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

        if ($this->employeeFocus !== 'all') {
            $focusId = (int) $this->employeeFocus;
            $rows = $focusId > 0 ? $rows->where('assignee_id', $focusId) : collect();
        }

        return match ($this->taskTab) {
            'longest' => $rows
                ->where('is_completed', true)
                ->filter(fn (array $row) => $row['hours_value'] !== null)
                ->sortByDesc(fn (array $row) => $row['hours_value'])
                ->values()
                ->all(),
            'reopened' => $rows
                ->where('reopened', true)
                ->sortByDesc(fn (array $row) => $row['updated_timestamp'] ?? 0)
                ->values()
                ->all(),
            default => $rows
                ->where('is_completed', true)
                ->sortByDesc(fn (array $row) => $row['completed_timestamp'] ?? 0)
                ->values()
                ->all(),
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

    protected function prepareForWorkspaceRefresh(): void
    {
        unset($this->report);
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
