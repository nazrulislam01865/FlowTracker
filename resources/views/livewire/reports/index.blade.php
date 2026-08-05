<div wire:init="loadSecondaryReports">
    <x-ui.page-head title="Reports" subtitle="Operational performance, delivery, workload and task completion">
        <x-slot:actions>
            @if(auth()->user()->canModule('reports','export'))
                <button class="ghost">Export PDF</button>
                <button class="primary">Export Excel</button>
            @endif
        </x-slot:actions>
    </x-ui.page-head>

        <div class="metrics ft-auto-metrics">
            @foreach([
                ['Active Jobs',$kpis['active_jobs'],'Current portfolio'],
                ['On-time Jobs',$kpis['on_time'].'%','Completed by delivery date'],
                ['Task Completion',$kpis['task_completion'].'%',$kpis['task_done'].' completed tasks'],
                ['Avg. Artwork Cycle',number_format($kpis['avg_artwork_cycle'],1).'d','Completed artwork phases'],
                ['Shipment On Time',$kpis['shipment_on_time'].'%','Completed shipment phases'],
                ['Overdue Tasks',$kpis['overdue_tasks'],'Open tasks past due']
            ] as $metric)
                <div class="card metric">
                    <div class="metric-label">{{ $metric[0] }}</div>
                    <div class="metric-value">{{ $metric[1] }}</div>
                    <div class="metric-foot">{{ $metric[2] }}</div>
                    <div class="metric-icon">◎</div>
                </div>
            @endforeach
        </div>

    <div class="report-grid">
        <div class="ft-island-shell">
            @if($secondaryReady)
                <div class="card section-card">
                    <div class="section-head"><h3>Active Jobs by Phase</h3><span class="small muted">Current portfolio</span></div>
                    <div class="bars ft-report-scroll-bars">
                        @php($max = max(1, $phase->max('total') ?? 1))
                        @forelse($phase->sortBy(fn($row) => $row->phase?->sequence ?? PHP_INT_MAX) as $row)
                            <div class="bar-row"><span>{{ $row->phase?->short_name ?? 'Unassigned' }}</span><div class="bar"><span style="width:{{ $row->total/$max*100 }}%"></span></div><b>{{ $row->total }}</b></div>
                        @empty
                            <div class="empty-state">No active Jobs.</div>
                        @endforelse
                    </div>
                </div>
            @else
                @include('livewire.dashboard.secondary-placeholder', ['title' => 'Active Jobs by Phase', 'rows' => 6])
            @endif
        </div>

        <div class="ft-island-shell">
            @if($secondaryReady)
                <div class="card section-card">
                    <div class="section-head"><h3>Team Workload</h3><span class="small muted">Open tasks</span></div>
                    <div class="bars ft-report-scroll-bars">
                        @php($workloadMax = max(1, $workload->max('open_tasks_count') ?? 1))
                        @forelse($workload as $person)
                            <div class="bar-row"><span>{{ $person->name }}</span><div class="bar"><span style="width:{{ $person->open_tasks_count/$workloadMax*100 }}%"></span></div><b>{{ $person->open_tasks_count }}</b></div>
                        @empty
                            <div class="empty-state">No open assigned tasks.</div>
                        @endforelse
                    </div>
                </div>
            @else
                @include('livewire.dashboard.secondary-placeholder', ['title' => 'Team Workload', 'rows' => 6])
            @endif
        </div>

        <div class="ft-island-shell ft-report-performance">
                <div class="card section-card">
                    <div class="section-head"><h3>Delivery Performance</h3><span class="small muted">Calculated from current Job, task and phase-history records</span></div>
                    <div class="kpi-list ft-report-kpis">
                        <div class="kpi"><b>{{ $kpis['on_time'] }}%</b><span>Jobs delivered on time</span></div>
                        <div class="kpi"><b>{{ $kpis['completed_jobs'] }}</b><span>Completed Jobs</span></div>
                        <div class="kpi"><b>{{ $kpis['task_done'] }}</b><span>Completed tasks</span></div>
                        <div class="kpi"><b>{{ $kpis['shipment_on_time'] }}%</b><span>Shipment phases on time</span></div>
                        <div class="kpi"><b>{{ number_format($kpis['avg_artwork_cycle'],1) }}d</b><span>Average artwork cycle</span></div>
                        <div class="kpi"><b>{{ $kpis['overdue_tasks'] }}</b><span>Overdue open tasks</span></div>
                    </div>
                </div>
        </div>
    </div>
</div>
