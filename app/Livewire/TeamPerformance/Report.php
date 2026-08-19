<?php

namespace App\Livewire\TeamPerformance;

use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\DashboardService;
use App\Services\FilterOptionService;
use Livewire\Attributes\Url;
use Livewire\Component;

class Report extends Component
{
    use RefreshesFromWorkspace;
    use UsesPagePlaceholder;

    private const TEAM_PER_PAGE = 8;

    #[Url(as: 'period', history: true, except: 'this_week')]
    public string $teamPeriod = 'this_week';

    #[Url(as: 'from', history: true, except: '')]
    public string $teamCustomFrom = '';

    #[Url(as: 'to', history: true, except: '')]
    public string $teamCustomTo = '';

    #[Url(as: 'client', history: true, except: '')]
    public string $clientFilter = '';

    #[Url(as: 'department', history: true, except: '')]
    public string $teamFilter = '';

    #[Url(as: 'q', history: true, except: '')]
    public string $search = '';

    #[Url(as: 'sort', history: true, except: 'performance')]
    public string $sort = 'performance';

    public int $teamPage = 1;

    public function mount(): void
    {
        if (!in_array($this->teamPeriod, ['this_week', 'this_month', 'last_30_days', 'custom'], true)) {
            $this->teamPeriod = 'this_week';
        }

        if (!in_array($this->sort, ['performance', 'workload', 'name'], true)) {
            $this->sort = 'performance';
        }
    }

    public function updatedTeamPeriod(string $period): void
    {
        if (!in_array($period, ['this_week', 'this_month', 'last_30_days', 'custom'], true)) {
            $this->teamPeriod = 'this_week';
        }

        if ($this->teamPeriod === 'custom' && ($this->teamCustomFrom === '' || $this->teamCustomTo === '')) {
            $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
            $this->teamCustomFrom = $today->copy()->startOfWeek()->toDateString();
            $this->teamCustomTo = $today->toDateString();
        }

        $this->teamPage = 1;
    }

    public function updatedTeamCustomFrom(): void
    {
        $this->teamPage = 1;
    }

    public function updatedTeamCustomTo(): void
    {
        $this->teamPage = 1;
    }

    public function updatedSearch(): void
    {
        $this->teamPage = 1;
    }

    public function updatedSort(string $sort): void
    {
        if (!in_array($sort, ['performance', 'workload', 'name'], true)) {
            $this->sort = 'performance';
        }

        $this->teamPage = 1;
    }

    public function setReportFilter(string $property, mixed $value): void
    {
        abort_unless(in_array($property, ['clientFilter', 'teamFilter'], true), 422, 'Unsupported report filter.');
        abort_unless(auth()->user()->canAccess('reports.view'), 403);

        $raw = trim((string) $value);
        if ($raw === '') {
            $this->{$property} = '';
            $this->teamPage = 1;
            return;
        }

        abort_unless(ctype_digit($raw), 422, 'Please choose a valid filter option.');
        $id = (int) $raw;
        $type = $property === 'clientFilter' ? 'clients' : 'departments';
        $selected = app(FilterOptionService::class)
            ->options(auth()->user(), $type, 'dashboard', '', $id, 20)
            ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
        abort_unless($selected, 422, 'That filter option is no longer available.');

        $this->{$property} = (string) $id;
        $this->teamPage = 1;
    }

    public function clearFilters(): void
    {
        $this->clientFilter = '';
        $this->teamFilter = '';
        $this->search = '';
        $this->sort = 'performance';
        $this->teamPage = 1;
    }

    public function previousTeamPage(): void
    {
        $this->teamPage = max(1, $this->teamPage - 1);
    }

    public function nextTeamPage(): void
    {
        $this->teamPage++;
    }

    public function render()
    {
        $user = auth()->user();
        $clientId = max(0, (int) $this->clientFilter);
        $departmentId = max(0, (int) $this->teamFilter);
        $query = mb_strtolower(trim($this->search));
        $service = app(DashboardService::class);

        $teamPerformance = $service->assigneePerformance(
            $user,
            $clientId,
            $departmentId,
            $this->teamPeriod,
            $this->teamCustomFrom ?: null,
            $this->teamCustomTo ?: null,
        )
            ->filter(fn ($row) => $departmentId <= 0 || (int) ($row->department_id ?? 0) === $departmentId)
            ->filter(function ($row) use ($query): bool {
                if ($query === '') return true;

                // The Team filter already handles departments. Keep the free
                // text search employee-specific so a search such as "ina"
                // does not also match everyone in "Finance Department".
                return str_contains(mb_strtolower((string) $row->name), $query);
            });

        $teamPerformance = $service->decorateTeamPerformance($teamPerformance);
        $teamPerformance = $service->sortTeamPerformance($teamPerformance, $this->sort);

        $resultCount = $teamPerformance->count();
        $lastPage = max(1, (int) ceil($resultCount / self::TEAM_PER_PAGE));
        $page = min(max(1, $this->teamPage), $lastPage);
        $offset = ($page - 1) * self::TEAM_PER_PAGE;
        $visibleTeamPerformance = $teamPerformance
            ->slice($offset, self::TEAM_PER_PAGE)
            ->values();

        $filterOptions = app(FilterOptionService::class);

        return view('livewire.team-performance.report', [
            'assigneePerformance' => $visibleTeamPerformance,
            'teamReportingPeriod' => $service->teamReportingPeriod(
                $this->teamPeriod,
                $this->teamCustomFrom ?: null,
                $this->teamCustomTo ?: null,
            ),
            'reportClientFilterOptions' => $filterOptions->options($user, 'clients', 'dashboard', '', $clientId ?: null, 6),
            'reportTeamFilterOptions' => $filterOptions->options($user, 'departments', 'dashboard', '', $departmentId ?: null, 6),
            'resultCount' => $resultCount,
            'teamPagination' => [
                'page' => $page,
                'lastPage' => $lastPage,
                'total' => $resultCount,
                'from' => $resultCount > 0 ? $offset + 1 : 0,
                'to' => min($offset + self::TEAM_PER_PAGE, $resultCount),
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $lastPage,
            ],
        ]);
    }
}
