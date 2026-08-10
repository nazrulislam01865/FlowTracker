@php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $canCreateOrder = auth()->user()->canAccess('jobs.create');
    $canCreateClient = auth()->user()->canModule('clients', 'create');
    $canCreateInquiry = auth()->user()->canModule('inquiries', 'create');
    $inquiryFlag = static function ($inquiry) use ($today): array {
        $task = $inquiry->currentTask;
        $status = strtolower((string) ($task?->status ?: $inquiry->status));
        $due = $task?->due_date;

        if ($due && $due->lt($today)) return ['Overdue', 'red'];
        if ($due && $due->isSameDay($today)) return ['Due today', 'amber'];
        if (str_contains($status, 'waiting for client')) return ['Client wait', 'amber'];
        if (str_contains($status, 'waiting for supplier')) return ['Supplier wait', 'amber'];
        if (str_contains($status, 'hold')) return ['On hold', 'amber'];
        return ['On track', 'green'];
    };
    $inquiryStatusTone = static function (?string $status): string {
        $value = strtolower((string) $status);
        if (str_contains($value, 'wait') || str_contains($value, 'hold')) return 'amber';
        if (str_contains($value, 'progress')) return 'blue';
        if (str_contains($value, 'ready')) return 'green';
        return '';
    };
@endphp

<div class="ft-dashboard-prototype">
    <div class="ft-heading">
        <div class="ft-heading-copy">
            <h1>Management Dashboard</h1>
            <p>{{ $today->format('l, F j') }} · Exceptions, workload, inquiries and delivery health</p>
        </div>
        <nav class="ft-quick-actions" aria-label="Quick actions">
            @if($canCreateOrder)
                <a class="ft-action primary" href="{{ route('jobs.index', ['create' => 1]) }}" wire:navigate><span class="ft-action-icon">+</span>Create Order</a>
            @endif
            @if($canCreateInquiry)
                <a class="ft-action" href="{{ route('inquiries.index', ['create' => 1]) }}" wire:navigate><span class="ft-action-icon">+</span>Create Inquiry</a>
            @endif
            @if($canCreateClient)
                <a class="ft-action" href="{{ route('clients.index', ['create' => 1]) }}" wire:navigate><span class="ft-action-icon">+</span>Add Client</a>
            @endif
        </nav>
    </div>

    <nav class="ft-page-tabs" aria-label="Dashboard views">
        <span class="ft-page-tab active">Dashboard</span>
    </nav>

    <section class="ft-kpis" aria-label="Key metrics">
        <a class="ft-kpi" href="{{ route('jobs.index') }}" wire:navigate><span class="ft-kpi-label">Active Jobs <i class="ft-kpi-icon">◎</i></span><strong class="ft-kpi-value">{{ $metrics['activeJobs'] }}</strong><span class="ft-kpi-foot">Across all active phases</span></a>
        <a class="ft-kpi" href="{{ route('all-tasks') }}" wire:navigate><span class="ft-kpi-label">Needs Attention <i class="ft-kpi-icon">!</i></span><strong class="ft-kpi-value">{{ $metrics['needsAttention'] }}</strong><span class="ft-kpi-foot">Risk, delay or blocker</span></a>
        <a class="ft-kpi" href="{{ route('all-tasks') }}" wire:navigate><span class="ft-kpi-label">Overdue Tasks <i class="ft-kpi-icon">◷</i></span><strong class="ft-kpi-value">{{ $metrics['overdueTasks'] }}</strong><span class="ft-kpi-foot">Require immediate update</span></a>
        <a class="ft-kpi" href="{{ route('clients.index') }}" wire:navigate><span class="ft-kpi-label">Active Clients <i class="ft-kpi-icon">♙</i></span><strong class="ft-kpi-value">{{ $metrics['activeClients'] }}</strong><span class="ft-kpi-foot">Current active client records</span></a>
        <a class="ft-kpi" href="{{ route('inquiries.index') }}" wire:navigate aria-label="Open Enquiries"><span class="ft-kpi-label">Open Enquiries <i class="ft-kpi-icon">?</i></span><strong class="ft-kpi-value">{{ $metrics['openInquiries'] }}</strong><span class="ft-kpi-foot">Current open inquiry records</span></a>
        <a class="ft-kpi" href="{{ route('notifications') }}" wire:navigate><span class="ft-kpi-label">Tagged Comments <i class="ft-kpi-icon">@</i></span><strong class="ft-kpi-value">{{ $metrics['taggedComments'] }}</strong><span class="ft-kpi-foot">Unread mentions for you</span></a>
    </section>

    <div class="ft-grid">
        <section class="ft-panel" id="inquiries">
            <div class="ft-panel-head">
                <div><h2 class="ft-panel-title">Open enquiries</h2><div class="ft-panel-note">Pre-job opportunities, ownership, quotation progress and follow-up flags</div></div>
                <a class="ft-link" href="{{ route('inquiries.index') }}" wire:navigate>View all enquiries</a>
            </div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:17%"><col style="width:25%"><col style="width:20%"><col style="width:18%"><col style="width:12%"><col style="width:8%"></colgroup>
                    <thead><tr><th>Inquiry ID</th><th>Client</th><th>Assignee Name</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        @forelse($recentInquiries as $inquiry)
                            @php
                                $assignee = $inquiry->currentTask?->assignee ?: $inquiry->owner;
                                [$flagLabel, $flagTone] = $inquiryFlag($inquiry);
                                $displayStatus = $inquiry->status;
                            @endphp
                            <tr wire:key="dashboard-inquiry-{{ $inquiry->id }}">
                                <td data-label="Inquiry ID"><a class="ft-text-link" href="{{ route('inquiries.index', ['open' => $inquiry->id]) }}" wire:navigate>{{ $inquiry->inquiry_number }}</a><span class="ft-ref ft-cell-clip">{{ $inquiry->subject }}</span></td>
                                <td data-label="Client"><span class="ft-cell-clip">{{ $inquiry->client?->name ?? 'No client' }}</span></td>
                                <td data-label="Assignee Name">
                                    @if($assignee)
                                        <span class="ft-person"><x-ui.avatar :user="$assignee" :name="$assignee->name" :size="22" /><span class="ft-cell-clip">{{ $assignee->name }}</span></span>
                                    @else
                                        <span class="ft-cell-clip">Unassigned</span>
                                    @endif
                                </td>
                                <td data-label="Status"><span class="ft-pill {{ $inquiryStatusTone($displayStatus) }}">{{ $displayStatus ?: 'Ready' }}</span></td>
                                <td data-label="Flag"><span class="ft-flag {{ $flagTone }}">{{ $flagLabel }}</span></td>
                                <td data-label="View"><a class="ft-view" href="{{ route('inquiries.index', ['open' => $inquiry->id]) }}" wire:navigate>View</a></td>
                            </tr>
                        @empty
                            <tr class="ft-table-empty-row"><td colspan="6">No open inquiries.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-primary">
        <livewire:dashboard.tagged-comments lazy />

        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Operational health</h2><div class="ft-panel-note">Current job health and task distribution based on task flags</div></div>{{-- Reports Details link disabled with the Reports page. --}}</div>
            <div class="ft-analytics">
                <div class="ft-health">
                    <div class="ft-health-content">
                        <div class="ft-donut" style="background:conic-gradient(#2eb67d 0 {{ $operationalHealth['healthyPct'] }}%, #f2b84b {{ $operationalHealth['healthyPct'] }}% {{ $operationalHealth['riskStart'] }}%, #ed5b5b {{ $operationalHealth['riskStart'] }}% 100%)"><div class="ft-donut-value">{{ $operationalHealth['totalJobs'] }}<small>active jobs</small></div></div>
                        <div class="ft-health-list">
                            <div class="ft-health-row"><span><i></i>Healthy</span><b>{{ $operationalHealth['healthy'] }}</b></div>
                            <div class="ft-health-row"><span><i></i>Watch</span><b>{{ $operationalHealth['watch'] }}</b></div>
                            <div class="ft-health-row"><span><i></i>At risk</span><b>{{ $operationalHealth['atRisk'] }}</b></div>
                        </div>
                    </div>
                </div>
                <div class="ft-flag-mix">
                    <div class="ft-mix-summary"><span>Task mix by flag</span><span><strong>{{ $operationalHealth['flaggedTotal'] }}</strong> flagged tasks</span></div>
                    <div class="ft-mix-list">
                        @foreach($operationalHealth['flags'] as $flag)
                            <div class="ft-mix-row"><a href="{{ route('all-tasks') }}" wire:navigate>{{ $flag['label'] }}</a><i class="ft-mix-track"><span class="ft-mix-fill {{ $flag['tone'] }}" style="width:{{ $flag['width'] }}%"></span></i><b>{{ $flag['count'] }}</b></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>

    <livewire:dashboard.secondary lazy />
</div>
