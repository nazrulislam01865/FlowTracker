@php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $canCreateOrder = auth()->user()->canAccess('jobs.create');
    $canCreateClient = auth()->user()->canModule('clients', 'create');
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
            <span class="ft-action" aria-disabled="true" title="Inquiry management is not available in this FlowTrack build"><span class="ft-action-icon">+</span>Create Inquiry</span>
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
        <a class="ft-kpi" href="{{ route('board') }}" wire:navigate><span class="ft-kpi-label">Needs Attention <i class="ft-kpi-icon">!</i></span><strong class="ft-kpi-value">{{ $metrics['needsAttention'] }}</strong><span class="ft-kpi-foot">Risk, delay or blocker</span></a>
        <a class="ft-kpi" href="{{ route('board') }}" wire:navigate><span class="ft-kpi-label">Overdue Tasks <i class="ft-kpi-icon">◷</i></span><strong class="ft-kpi-value">{{ $metrics['overdueTasks'] }}</strong><span class="ft-kpi-foot">Require immediate update</span></a>
        <a class="ft-kpi" href="{{ route('clients.index') }}" wire:navigate><span class="ft-kpi-label">Active Clients <i class="ft-kpi-icon">♙</i></span><strong class="ft-kpi-value">{{ $metrics['activeClients'] }}</strong><span class="ft-kpi-foot">Current active client records</span></a>
        <div class="ft-kpi" aria-label="Open Enquiries"><span class="ft-kpi-label">Open Enquiries <i class="ft-kpi-icon">?</i></span><strong class="ft-kpi-value">{{ $metrics['openInquiries'] }}</strong><span class="ft-kpi-foot">Inquiry module not configured</span></div>
        <a class="ft-kpi" href="{{ route('notifications') }}" wire:navigate><span class="ft-kpi-label">Tagged Comments <i class="ft-kpi-icon">@</i></span><strong class="ft-kpi-value">{{ $metrics['taggedComments'] }}</strong><span class="ft-kpi-foot">Unread mentions for you</span></a>
    </section>

    <div class="ft-grid">
        <section class="ft-panel" id="inquiries">
            <div class="ft-panel-head">
                <div><h2 class="ft-panel-title">Open enquiries</h2><div class="ft-panel-note">Pre-job opportunities, ownership, quotation progress and follow-up flags</div></div>
                <span class="ft-link" aria-disabled="true">View all enquiries</span>
            </div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:17%"><col style="width:25%"><col style="width:20%"><col style="width:18%"><col style="width:12%"><col style="width:8%"></colgroup>
                    <thead><tr><th>Inquiry ID</th><th>Client</th><th>Assignee Name</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        <tr class="ft-table-empty-row"><td colspan="6">No inquiry records are available in this FlowTrack build.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-primary">
        <livewire:dashboard.tagged-comments />

        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Operational health</h2><div class="ft-panel-note">Current job health and task distribution based on task flags</div></div><a class="ft-link" href="{{ route('reports') }}" wire:navigate>Details</a></div>
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
                            <div class="ft-mix-row"><a href="{{ route('board') }}" wire:navigate>{{ $flag['label'] }}</a><i class="ft-mix-track"><span class="ft-mix-fill {{ $flag['tone'] }}" style="width:{{ $flag['width'] }}%"></span></i><b>{{ $flag['count'] }}</b></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>

    <livewire:dashboard.secondary lazy />
</div>
