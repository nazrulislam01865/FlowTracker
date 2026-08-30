<?php

namespace App\Livewire\Inquiries\Concerns;

use App\Models\Inquiry;
use App\Models\Client;
use Throwable;

trait ManagesInquiryList
{
    public function updatedSearch(): void
    {
        // Inquiry list filters are mutually exclusive. Typing a search becomes
        // the only active filter and clears every other filter/card shortcut.
        if (trim($this->search) !== '') {
            $this->clearListFiltersExcept('search');
        } else {
            $this->clearMetricFilter();
        }

        $this->resetPage('inquiryPage');
    }

    public function updatedListClient(): void
    {
        if ($this->listClient !== '') {
            $this->clearListFiltersExcept('client');
        } else {
            $this->clearMetricFilter();
        }

        $this->resetPage('inquiryPage');
    }

    public function updatedListStatus(): void
    {
        $allowedStatuses = app(\App\Queries\Inquiries\InquiryListQuery::class)->taskStatusOptions();
        abort_unless(
            $this->listStatus === '' || $allowedStatuses->contains($this->listStatus),
            422,
            'Invalid task status filter.'
        );

        if ($this->listStatus !== '') {
            $this->clearListFiltersExcept('status');
        } else {
            $this->clearMetricFilter();
        }

        $this->resetPage('inquiryPage');
    }

    public function updatedDateFrom(): void
    {
        $this->dateFrom = $this->normalizeDateFilter($this->dateFrom);
        $this->clearListFiltersExcept('dateRange');
        $this->normalizeDateRange('from');
        $this->resetPage('inquiryPage');
    }

    public function updatedDateTo(): void
    {
        $this->dateTo = $this->normalizeDateFilter($this->dateTo);
        $this->clearListFiltersExcept('dateRange');
        $this->normalizeDateRange('to');
        $this->resetPage('inquiryPage');
    }

    public function setInquiryListFilter(string $property, mixed $value): void
    {
        abort_unless($property === 'listClient', 422, 'Unsupported Inquiry filter.');
        abort_unless(auth()->user()->canModule('inquiries', 'view'), 403);

        $raw = trim((string) $value);
        if ($raw === '') {
            $this->listClient = '';
            $this->listClientLabel = '';
            $this->clearMetricFilter();
            $this->resetPage('inquiryPage');
            return;
        }

        abort_unless(ctype_digit($raw), 422, 'Please choose a valid client.');
        $id = (int) $raw;
        $selected = app(\App\Services\FilterOptionService::class)
            ->options(auth()->user(), 'clients', 'inquiries', '', $id, 20)
            ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
        abort_unless($selected, 422, 'That client is no longer available.');

        // Client becomes the only active Inquiry list filter.
        $this->clearListFiltersExcept('client');
        $this->listClient = (string) $id;
        $this->listClientLabel = (string) ($selected['label'] ?? '');
        $this->resetPage('inquiryPage');
    }

    public function updatedHideCompleted(): void
    {
        if ($this->hideCompleted) {
            $this->clearListFiltersExcept('hideCompleted');
        } else {
            $this->clearMetricFilter();
        }

        $this->resetPage('inquiryPage');
    }

    public function setQuick(string $quick): void
    {
        abort_unless(in_array($quick, ['all', 'attention'], true), 422);
        abort_unless(auth()->user()->canModule('inquiries', 'view'), 403);

        if ($quick === 'all') {
            $this->clearToolbarFilters();
            $this->metricFilter = '';
        } else {
            // Attention becomes the only active Inquiry list filter.
            $this->clearListFiltersExcept('quick');
            $this->quick = $quick;
        }

        $this->resetPage('inquiryPage');
    }

    public function setMetricFilter(string $metric): void
    {
        abort_unless(in_array($metric, ['createdToday', 'notStarted', 'inProgress', 'dueThisWeek', 'completedThisWeek', 'attention'], true), 422);
        abort_unless(auth()->user()->canModule('inquiries', 'view'), 403);

        $nextMetric = $this->metricFilter === $metric ? '' : $metric;

        // Summary cards are also exclusive filters. Selecting one clears the
        // search and every toolbar filter so the card count always matches.
        $this->clearToolbarFilters();
        $this->metricFilter = $nextMetric;
        $this->resetPage('inquiryPage');
    }

    public function clearFilters(): void
    {
        abort_unless(auth()->user()->canModule('inquiries', 'view'), 403);

        $this->clearToolbarFilters();
        $this->metricFilter = '';
        $this->resetPage('inquiryPage');
    }

    private function clearMetricFilter(): void
    {
        $this->metricFilter = '';
    }

    private function clearListFiltersExcept(string $except): void
    {
        if ($except !== 'search') {
            $this->search = '';
        }
        if ($except !== 'quick') {
            $this->quick = 'all';
        }
        if ($except !== 'client') {
            $this->listClient = '';
            $this->listClientLabel = '';
        }
        if ($except !== 'status') {
            $this->listStatus = '';
        }
        if ($except !== 'hideCompleted') {
            $this->hideCompleted = false;
        }
        if ($except !== 'dateRange') {
            $this->dateFrom = '';
            $this->dateTo = '';
        }

        $this->metricFilter = '';
    }

    private function clearToolbarFilters(): void
    {
        $this->search = '';
        $this->quick = 'all';
        $this->listClient = '';
        $this->listClientLabel = '';
        $this->listStatus = '';
        $this->hideCompleted = false;
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    private function normalizeDateFilter(string $value): string
    {
        $value = trim($value);
        if ($value === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }

        try {
            $date = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $value, 'UTC');
        } catch (\Throwable) {
            return '';
        }

        return $date && $date->format('Y-m-d') === $value ? $value : '';
    }

    private function normalizeDateRange(string $changed): void
    {
        if ($this->dateFrom === '' || $this->dateTo === '' || $this->dateFrom <= $this->dateTo) {
            return;
        }

        if ($changed === 'to') {
            $this->dateFrom = $this->dateTo;
            return;
        }

        $this->dateTo = $this->dateFrom;
    }

    public function deleteInquiry(int $id): void
    {
        $inquiry = app(\App\Queries\Inquiries\InquiryDetailQuery::class)->find(auth()->user(), $id);
        $number = (string) $inquiry->inquiry_number;

        app(\App\Actions\Inquiries\DeleteInquiry::class)->handle($inquiry, auth()->user());
        $this->metrics = app(\App\Queries\Inquiries\InquiryListQuery::class)->metrics(auth()->user());
        $this->resetPage('inquiryPage');

        session()->flash('success', $number.' deleted successfully.');
    }
}
