<div>
    <x-ui.page-head title="Management Dashboard" :subtitle="now()->format('l, F j').' · Exceptions, workload and delivery health'">
        <x-slot:actions>
            @if(auth()->user()->canModule('reports','export'))
                <a class="ghost" href="{{ route('reports') }}" wire:navigate>Export summary</a>
            @endif
            @if(auth()->user()->canModule('jobs','create'))
                <a class="primary" href="{{ route('jobs.index',['create'=>1]) }}" wire:navigate>＋ New Job</a>
            @endif
        </x-slot:actions>
    </x-ui.page-head>

@php($metrics = $this->metrics)
        <div class="metrics ft-auto-metrics">
            @foreach([
                ['Active Jobs',$metrics['activeJobs'],'Across all active phases'],
                ['Needs Attention',$metrics['riskJobs'],'Risk, delay or blocker'],
                ['Overdue Tasks',$metrics['overdueTasks'],'Require immediate update'],
                ['Pending Approvals',$metrics['pendingApprovals'],'Client or internal'],
                ['Shipping Now',$metrics['shipping'],'Currently in a shipping phase']
            ] as $metric)
                <div class="card metric">
                    <div class="metric-label">{{ $metric[0] }}</div>
                    <div class="metric-value">{{ $metric[1] }}</div>
                    <div class="metric-foot">{{ $metric[2] }}</div>
                    <div class="metric-icon">◎</div>
                </div>
            @endforeach
        </div>

    <div class="grid-2">
        <div class="ft-island-shell">
<div class="card section-card">
                    <div class="section-head"><h3>Needs Attention</h3><a class="link-btn" href="{{ route('jobs.index') }}" wire:navigate>View all Jobs</a></div>
                    <div class="attention-list">
                        @forelse($this->attentionJobs as $job)
                            @php($flaggedTask = $job->tasks->first())
                            <a class="attention-item" href="{{ $flaggedTask ? route('jobs.index',['open'=>$job->id,'task'=>$flaggedTask->id]) : route('jobs.index',['open'=>$job->id]) }}" wire:navigate>
                                <span class="signal red"></span>
                                <div>
                                    <div class="item-title">{{ $flaggedTask?->title ?: ($job->next_action ?: $job->title) }}</div>
                                    <div class="item-meta">{{ $job->job_number }} · {{ $job->client?->name ?? 'No client' }} · {{ $flaggedTask ? 'Marked as attention needed' : ($job->phase?->short_name ?? 'Needs attention') }}</div>
                                </div>
                                <x-ui.badge :label="$flaggedTask ? 'Needs Attention' : $job->health" />
                            </a>
                        @empty
                            <div class="empty-state">No Jobs or tasks need attention.</div>
                        @endforelse
                    </div>
                </div>
        </div>

        <div class="ft-island-shell">
<div class="card section-card">
                    <div class="section-head"><h3>Jobs by Phase</h3><a class="link-btn" href="{{ route('board') }}" wire:navigate>Open board</a></div>
                    <div class="phase-list">
                        @forelse($this->phaseCounts->sortBy(fn($row) => $row->phase?->sequence ?? PHP_INT_MAX)->take(6) as $row)
                            <div class="phase-row"><span>{{ $row->phase?->short_name ?? 'Unassigned' }}</span><x-ui.progress :value="min(100,max(8,$row->total*16))" /><b>{{ $row->total }}</b></div>
                        @empty
                            <div class="empty-state">No active Jobs by phase.</div>
                        @endforelse
                    </div>
                </div>
        </div>
    </div>

    <div class="grid-3">
        <div class="ft-island-shell">
<div class="card section-card">
                    <div class="section-head"><h3>Team Workload</h3><a class="link-btn" href="{{ route('reports') }}" wire:navigate>Details</a></div>
                    @forelse($this->workload as $person)
                        <div class="workload-row"><div class="person"><x-ui.avatar :name="$person->name" :size="27" />{{ $person->name }}</div><x-ui.progress :value="min(100,$person->open_tasks_count*12)"/><b>{{ $person->open_tasks_count }}</b></div>
                    @empty
                        <div class="empty-state">No active team workload.</div>
                    @endforelse
                </div>
        </div>

        <div class="ft-island-shell">
<div class="card section-card">
                    <div class="section-head"><h3>Upcoming Deliveries</h3><a class="link-btn" href="{{ route('jobs.index') }}" wire:navigate>View Jobs</a></div>
                    <table class="mini-table">
                        <thead><tr><th>Job</th><th>Client</th><th>Delivery</th></tr></thead>
                        <tbody>
                            @forelse($this->deliveries as $job)
                                <tr><td><a class="job-link" href="{{ route('jobs.index',['open'=>$job->id]) }}" wire:navigate>{{ str($job->job_number)->afterLast('-') }}</a><div class="muted small truncate" style="max-width:125px">{{ $job->title }}</div></td><td>{{ str($job->client?->name ?? '—')->before(' ') }}</td><td>{{ $job->delivery_date?->format('M j, Y') }}</td></tr>
                            @empty
                                <tr><td colspan="3"><div class="empty-state">No upcoming deliveries.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>

        <div class="ft-island-shell">
<div class="card section-card">
                    <div class="section-head"><h3>Recent Activity</h3><a class="link-btn" href="{{ route('notifications') }}" wire:navigate>All activity</a></div>
                    @forelse($this->activity as $notification)
                        <div class="activity"><x-ui.avatar :name="auth()->user()->name" :size="30"/><div><div class="activity-text"><b>{{ $notification->title }}</b><br>{{ $notification->message }}</div><div class="activity-time">{{ $notification->created_at->diffForHumans() }}</div></div></div>
                    @empty
                        <div class="empty-state">No recent activity.</div>
                    @endforelse
                </div>
        </div>
    </div>
</div>
