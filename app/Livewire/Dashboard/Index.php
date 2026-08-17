<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Services\AccessControlService;
use App\Services\DashboardService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use RefreshesFromWorkspace;
    use UsesPagePlaceholder;

    public int $rangeDays = 7;
    public string $clientFilter = '';
    public string $teamFilter = '';
    public string $search = '';
    public string $flowTab = 'orders';
    public string $priorityTab = 'orders';
    public string $taskStatusTab = 'orders';
    public string $activityTab = 'all';
    public bool $teamSortByWorkload = false;
    public bool $teamExpanded = false;

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render the management dashboard when realtime state changes.
    }

    public function setRange(int $days): void
    {
        abort_unless(in_array($days, [1, 7, 30], true), 422);
        $this->rangeDays = $days;
        $this->teamExpanded = false;
    }

    public function updatedClientFilter(): void
    {
        $this->teamExpanded = false;
    }

    public function updatedTeamFilter(): void
    {
        $this->teamExpanded = false;
    }

    public function updatedSearch(): void
    {
        $this->teamExpanded = false;
    }

    public function setDashboardFilter(string $property, mixed $value): void
    {
        abort_unless(in_array($property, ['clientFilter', 'teamFilter'], true), 422, 'Unsupported dashboard filter.');
        abort_unless(auth()->user()->canAccess('dashboard.view'), 403);

        $raw = trim((string) $value);
        if ($raw === '') {
            if ($property === 'clientFilter') {
                $this->clientFilter = '';
            } else {
                $this->teamFilter = '';
            }
            $this->teamExpanded = false;
            return;
        }

        abort_unless(ctype_digit($raw), 422, 'Please choose a valid filter option.');
        $id = (int) $raw;
        $type = $property === 'clientFilter' ? 'clients' : 'departments';
        $selected = app(\App\Services\FilterOptionService::class)
            ->options(auth()->user(), $type, 'dashboard', '', $id, 20)
            ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
        abort_unless($selected, 422, 'That filter option is no longer available.');

        if ($property === 'clientFilter') {
            $this->clientFilter = (string) $id;
        } else {
            $this->teamFilter = (string) $id;
        }
        $this->teamExpanded = false;
    }

    public function setFlowTab(string $tab): void
    {
        abort_unless(in_array($tab, ['orders', 'inquiries'], true), 422);
        $this->flowTab = $tab;
    }

    public function setPriorityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['orders', 'inquiries', 'tasks'], true), 422);
        $this->priorityTab = $tab;
    }

    public function setTaskStatusTab(string $tab): void
    {
        abort_unless(in_array($tab, ['orders', 'inquiries'], true), 422);
        $this->taskStatusTab = $tab;
    }

    public function setActivityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['all', 'orders', 'inquiries', 'tasks'], true), 422);
        $this->activityTab = $tab;
    }

    public function toggleTeamSort(): void
    {
        $this->teamSortByWorkload = ! $this->teamSortByWorkload;
    }

    public function toggleTeamExpanded(): void
    {
        $this->teamExpanded = ! $this->teamExpanded;
    }

    public function render()
    {
        $user = auth()->user();
        $clientId = max(0, (int) $this->clientFilter);
        $departmentId = max(0, (int) $this->teamFilter);
        $query = mb_strtolower(trim($this->search));
        $data = app(DashboardService::class)->primaryData($user, $clientId, $departmentId, $this->rangeDays);
        $filterOptions = app(\App\Services\FilterOptionService::class);
        $data['dashboardClientFilterOptions'] = $filterOptions->options($user, 'clients', 'dashboard', '', $clientId ?: null, 6);
        $data['dashboardTeamFilterOptions'] = $filterOptions->options($user, 'departments', 'dashboard', '', $departmentId ?: null, 6);
        $data['administratorView'] = app(AccessControlService::class)->isAdministrator($user);
        $cutoff = app(\App\Services\WorkspaceSettingsService::class)
            ->localToday()
            ->copy()
            ->subDays(max(0, $this->rangeDays - 1))
            ->startOfDay();

        $data['priorityInquiries'] = $this->filterCollection(
            $data['priorityInquiries'],
            $clientId,
            $departmentId,
            $query,
            fn ($row): array => [
                $row->inquiry_number, $row->subject, $row->status, $row->priority,
                $row->client?->name, $row->owner?->name, $row->currentTask?->title,
                $row->currentTask?->status, $row->currentTask?->assignee?->name,
            ],
            fn ($row): ?int => $row->client_id ? (int) $row->client_id : null,
            fn ($row): ?int => $row->currentTask?->assignee?->department_id
                ? (int) $row->currentTask->assignee->department_id
                : ($row->owner?->department_id ? (int) $row->owner->department_id : null),
        )->take(5)->values();

        $data['attentionTasks'] = $this->filterCollection(
            $data['attentionTasks'],
            $clientId,
            $departmentId,
            $query,
            fn ($row): array => [
                $row->task_number, $row->title, $row->status,
                $row->job?->job_number, $row->job?->title,
                $row->job?->client?->name, $row->assignee?->name,
            ],
            fn ($row): ?int => $row->job?->client_id ? (int) $row->job->client_id : null,
            fn ($row): ?int => $row->assignee?->department_id ? (int) $row->assignee->department_id : null,
        )->take(4)->values();

        $data['priorityJobs'] = $this->filterCollection(
            $data['priorityJobs'],
            $clientId,
            $departmentId,
            $query,
            fn ($row): array => [
                $row->job_number, $row->title, $row->health, $row->priority,
                $row->client?->name, $row->phase?->short_name, $row->phase?->name,
                $row->owner?->name,
            ],
            fn ($row): ?int => $row->client_id ? (int) $row->client_id : null,
            fn ($row): ?int => $row->owner?->department_id ? (int) $row->owner->department_id : null,
        )->take(5)->values();

        $data['priorityTasks'] = $this->filterCollection(
            $data['priorityTasks'],
            $clientId,
            $departmentId,
            $query,
            fn ($row): array => [
                $row->task_number, $row->title, $row->status, $row->priority,
                $row->job?->job_number, $row->job?->title, $row->job?->client?->name,
                $row->phase?->short_name, $row->phase?->name, $row->assignee?->name,
            ],
            fn ($row): ?int => $row->job?->client_id ? (int) $row->job->client_id : null,
            fn ($row): ?int => $row->assignee?->department_id ? (int) $row->assignee->department_id : null,
        )->take(5)->values();

        $data['clientPortfolio'] = collect($data['clientPortfolio'])
            ->filter(fn ($row) => $clientId <= 0 || (int) $row->id === $clientId)
            ->filter(fn ($row) => $query === '' || str_contains(mb_strtolower((string) $row->name), $query))
            ->take(4)
            ->values();

        $teamPerformance = collect($data['assigneePerformance'])
            ->filter(fn ($row) => $departmentId <= 0 || (int) ($row->department_id ?? 0) === $departmentId)
            ->filter(function ($row) use ($query): bool {
                if ($query === '') return true;
                return str_contains(mb_strtolower(implode(' ', array_filter([
                    $row->name, $row->department?->name,
                ]))), $query);
            });

        $teamPerformance = $this->teamSortByWorkload
            ? $teamPerformance->sort(function ($left, $right): int {
                $ongoing = (int) $right->ongoing_count <=> (int) $left->ongoing_count;
                if ($ongoing !== 0) return $ongoing;

                $completed = (int) $right->done_count <=> (int) $left->done_count;
                if ($completed !== 0) return $completed;

                return strcasecmp((string) $left->name, (string) $right->name);
            })
            : $teamPerformance->sortBy(fn ($row) => mb_strtolower((string) $row->name));

        $teamPerformance = $teamPerformance->values();
        $data['teamUserTotal'] = $teamPerformance->count();
        $data['teamHiddenCount'] = max(0, $data['teamUserTotal'] - 5);
        $data['teamMaxOngoing'] = max(1, (int) ($teamPerformance->max(fn ($row) => (int) $row->ongoing_count) ?? 0));
        $data['teamAverageOngoing'] = $teamPerformance->isNotEmpty()
            ? (float) $teamPerformance->avg(fn ($row) => (int) $row->ongoing_count)
            : 0.0;
        $data['assigneePerformance'] = ($this->teamExpanded ? $teamPerformance : $teamPerformance->take(5))->values();

        $data['recentActivity'] = collect($data['recentActivity'])
            ->filter(fn ($row) => !$row->created_at || $row->created_at->gte($cutoff))
            ->filter(function ($row): bool {
                return $this->activityTab === 'all' || (string) ($row->dashboard_kind ?? '') === $this->activityTab;
            })
            ->filter(fn ($row) => $clientId <= 0 || (int) ($row->dashboard_client_id ?? 0) === $clientId)
            ->filter(fn ($row) => $departmentId <= 0 || (int) ($row->dashboard_department_id ?? 0) === $departmentId)
            ->filter(function ($row) use ($query): bool {
                if ($query === '') return true;
                $haystack = mb_strtolower(trim(implode(' ', array_filter([
                    (string) ($row->dashboard_title ?? ''),
                    (string) ($row->dashboard_detail ?? ''),
                    (string) ($row->event ?? ''),
                ]))));
                return str_contains($haystack, $query);
            })
            ->take(6)
            ->values();

        return view('livewire.dashboard.index', $data);
    }

    private function filterCollection(
        Collection $rows,
        int $clientId,
        int $departmentId,
        string $query,
        callable $searchFields,
        callable $clientResolver,
        callable $departmentResolver,
    ): Collection {
        return $rows
            ->filter(fn ($row) => $clientId <= 0 || (int) ($clientResolver($row) ?? 0) === $clientId)
            ->filter(fn ($row) => $departmentId <= 0 || (int) ($departmentResolver($row) ?? 0) === $departmentId)
            ->filter(function ($row) use ($query, $searchFields): bool {
                if ($query === '') return true;
                $haystack = mb_strtolower(implode(' ', array_filter(array_map(
                    static fn ($value) => trim((string) $value),
                    $searchFields($row)
                ))));
                return str_contains($haystack, $query);
            })
            ->values();
    }
}
