@php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $masterData = app(\App\Services\MasterDataService::class);
    $taskFlagService = app(\App\Services\TaskFlagService::class);
    $inquiryService = app(\App\Services\InquiryService::class);
    $canCreateOrder = auth()->user()->canAccess('jobs.create');
    $canCreateClient = auth()->user()->canModule('clients', 'create');
    $canCreateInquiry = auth()->user()->canModule('inquiries', 'create');

    $badgeTone = static function (?string $value): string {
        $value = mb_strtolower(trim((string) $value));
        if (str_contains($value, 'overdue') || str_contains($value, 'attention') || str_contains($value, 'blocked') || str_contains($value, 'risk')) return 'red';
        if (str_contains($value, 'due') || str_contains($value, 'waiting') || str_contains($value, 'hold') || str_contains($value, 'payment')) return 'amber';
        if (str_contains($value, 'complete') || str_contains($value, 'track') || str_contains($value, 'healthy') || str_contains($value, 'ready')) return 'green';
        if (str_contains($value, 'artwork') || str_contains($value, 'review') || str_contains($value, 'revision')) return 'purple';
        if (str_contains($value, 'unassigned') || str_contains($value, 'no flag')) return 'gray';
        return 'blue';
    };

    $taskFlag = static function ($task) use ($today, $taskFlagService, $badgeTone): array {
        $label = $taskFlagService->labelForTask($task);
        if ($label) return [$label, $badgeTone($label)];
        if ($task->due_date && $task->due_date->lt($today)) return ['Overdue '.$task->due_date->diffInDays($today).'d', 'red'];
        if ($task->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        if ((bool) $task->needs_attention) return ['Needs attention', 'red'];
        return ['On track', 'green'];
    };

    $jobFlag = static function ($job) use ($taskFlagService, $badgeTone): array {
        if ((bool) ($job->attention_requested ?? false) || (bool) ($job->needs_attention ?? false)) return ['Needs attention', 'red'];
        $label = $taskFlagService->labelForOrder($job);
        if ($label) return [$label, $badgeTone($label)];
        $health = trim((string) ($job->health ?? ''));
        if ($health !== '' && !in_array(mb_strtolower($health), ['on track', 'healthy'], true)) return [$health, $badgeTone($health)];
        return ['On track', 'green'];
    };

    $inquiryFlag = static function ($inquiry) use ($today): array {
        $task = $inquiry->currentTask;
        $status = mb_strtolower((string) ($task?->status ?: $inquiry->status));
        if ($task?->needs_attention) return ['Needs attention', 'red'];
        if ($task?->due_date && $task->due_date->lt($today)) return ['Overdue', 'red'];
        if ($task?->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        if (str_contains($status, 'wait')) return ['Waiting', 'amber'];
        return ['On track', 'green'];
    };

    $orderTerminology = static function (?string $value): string {
        return preg_replace_callback('/\bjobs?\b/i', static function (array $match): string {
            return match ($match[0]) {
                'Jobs' => 'Orders', 'jobs' => 'orders', 'JOB' => 'ORDER', 'JOBS' => 'ORDERS',
                default => ctype_upper($match[0][0] ?? '') ? 'Order' : 'order',
            };
        }, (string) $value) ?: (string) $value;
    };

    $flowRows = collect($flowDistribution[$flowTab] ?? []);
    $flowMax = max(1, (int) $flowRows->max('count'));
    $flowRows = $flowRows->map(static function (array $row) use ($flowMax): array {
        $count = (int) ($row['count'] ?? 0);
        $scopeText = trim((string) ($row['scope_text'] ?? ''));

        return [
            'label' => (string) ($row['label'] ?? 'Unassigned'),
            'count' => $count,
            'width' => $count > 0 ? max(2, (int) round(($count / $flowMax) * 100)) : 0,
            'scope_text' => $scopeText,
            'scope_label' => trim((string) ($row['scope_label'] ?? '')),
            'is_mismatch' => (bool) ($row['is_mismatch'] ?? false),
        ];
    })->values();
    $selectedTaskStatusDistribution = $taskStatusDistribution[$taskStatusTab] ?? ['total' => 0, 'rows' => []];
    $statusRows = collect($selectedTaskStatusDistribution['rows'] ?? [])->filter(fn ($row) => (int) ($row['count'] ?? 0) > 0)->values();
    $statusTotal = max(0, (int) ($selectedTaskStatusDistribution['total'] ?? 0));
    $cursor = 0.0;
    $gradientSegments = [];
    foreach ($statusRows as $row) {
        $count = max(0, (int) ($row['count'] ?? 0));
        if ($count <= 0 || $statusTotal <= 0) continue;
        $start = $cursor;
        $cursor += ($count / $statusTotal) * 100;
        $color = (string) ($row['color'] ?? '#64748B');
        $gradientSegments[] = $color.' '.$start.'% '.$cursor.'%';
    }
    if ($cursor < 100) $gradientSegments[] = '#edf2f5 '.$cursor.'% 100%';
    $donutBackground = $statusTotal > 0 ? 'conic-gradient('.implode(',', $gradientSegments).')' : '#edf2f5';
    $statusOpenRoute = $taskStatusTab === 'orders' ? route('all-tasks') : route('inquiries.index');
@endphp

<x-ui.management-theme class="ft-mgmt-dashboard">
    <div class="ft-mgmt-page-head">
        <div>
            <h1>Management Dashboard</h1>
            <p>Live operational overview across inquiries, orders, tasks, clients and product data.</p>
        </div>
        <div class="ft-mgmt-head-actions">
            @if($canCreateOrder)<a class="ft-mgmt-btn primary" href="{{ route('jobs.index', ['create' => 1]) }}" wire:navigate>＋ Create Order</a>@endif
            @if($canCreateInquiry)<a class="ft-mgmt-btn" href="{{ route('inquiries.index', ['create' => 1]) }}" wire:navigate>＋ Create Inquiry</a>@endif
            @if($canCreateClient)<a class="ft-mgmt-btn" href="{{ route('clients.index', ['create' => 1]) }}" wire:navigate>＋ Add Client</a>@endif
        </div>
    </div>

    <section class="ft-mgmt-control-bar" aria-label="Dashboard filters">
        <div class="ft-mgmt-range" wire:key="dashboard-range-control" wire:loading.class="is-loading" wire:target="setRange">
            <button type="button" wire:click="setRange(1)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="{{ $rangeDays === 1 ? 'true' : 'false' }}" class="{{ $rangeDays === 1 ? 'active' : '' }}">Today</button>
            <button type="button" wire:click="setRange(7)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="{{ $rangeDays === 7 ? 'true' : 'false' }}" class="{{ $rangeDays === 7 ? 'active' : '' }}">7 days</button>
            <button type="button" wire:click="setRange(30)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="{{ $rangeDays === 30 ? 'true' : 'false' }}" class="{{ $rangeDays === 30 ? 'active' : '' }}">30 days</button>
        </div>
        <x-ui.remote-filter
            class="ft-mgmt-remote-filter ft-mgmt-client-filter"
            label="Client"
            property="clientFilter"
            type="clients"
            context="dashboard"
            action="setDashboardFilter"
            :value="$clientFilter"
            placeholder="All clients"
            :initial-options="$dashboardClientFilterOptions"
            :menu-width="300"
            :fixed-menu="true"
            wire:key="dashboard-client-filter-{{ $clientFilter ?: 'all' }}"
        />
        <x-ui.remote-filter
            class="ft-mgmt-remote-filter ft-mgmt-team-filter"
            label="Team"
            property="teamFilter"
            type="departments"
            context="dashboard"
            action="setDashboardFilter"
            :value="$teamFilter"
            placeholder="All teams"
            :initial-options="$dashboardTeamFilterOptions"
            :menu-width="300"
            :fixed-menu="true"
            wire:key="dashboard-team-filter-{{ $teamFilter ?: 'all' }}"
        />
        <span class="ft-mgmt-last-updated"><span class="ft-mgmt-live-dot"></span>Live · updated now</span>
        <input class="ft-mgmt-search" wire:model.live.debounce.300ms="search" type="search" placeholder="Search orders, inquiries or tasks" aria-label="Search dashboard">
    </section>

    <section class="ft-mgmt-kpis" aria-label="Key metrics">
        <a class="ft-mgmt-kpi" href="{{ route('jobs.index') }}" wire:navigate><i class="ft-mgmt-kpi-icon">▣</i><span class="ft-mgmt-kpi-label">Active orders</span><strong class="ft-mgmt-kpi-value">{{ $metrics['activeJobs'] }}</strong><span class="ft-mgmt-kpi-meta">Across active workflow stages</span></a>
        <a class="ft-mgmt-kpi" href="{{ route('all-tasks') }}" wire:navigate><i class="ft-mgmt-kpi-icon">!</i><span class="ft-mgmt-kpi-label">Needs attention</span><strong class="ft-mgmt-kpi-value">{{ $metrics['needsAttention'] }}</strong><span class="ft-mgmt-kpi-meta">Risk, delay or blocker</span></a>
        <a class="ft-mgmt-kpi" href="{{ route('all-tasks') }}" wire:navigate><i class="ft-mgmt-kpi-icon">◷</i><span class="ft-mgmt-kpi-label">Overdue tasks</span><strong class="ft-mgmt-kpi-value">{{ $metrics['overdueTasks'] }}</strong><span class="ft-mgmt-kpi-meta">Require immediate update</span></a>
        <a class="ft-mgmt-kpi" href="{{ route('inquiries.index') }}" wire:navigate><i class="ft-mgmt-kpi-icon">?</i><span class="ft-mgmt-kpi-label">Open inquiries</span><strong class="ft-mgmt-kpi-value">{{ $metrics['openInquiries'] }}</strong><span class="ft-mgmt-kpi-meta">Current open inquiry records</span></a>
        <a class="ft-mgmt-kpi" href="{{ route('clients.index') }}" wire:navigate><i class="ft-mgmt-kpi-icon">△</i><span class="ft-mgmt-kpi-label">Active clients</span><strong class="ft-mgmt-kpi-value">{{ $metrics['activeClients'] }}</strong><span class="ft-mgmt-kpi-meta">Current active client records</span></a>
        <a class="ft-mgmt-kpi" href="{{ route('master-data', ['group' => 'product']) }}" wire:navigate><i class="ft-mgmt-kpi-icon">◇</i><span class="ft-mgmt-kpi-label">Active products</span><strong class="ft-mgmt-kpi-value">{{ number_format($metrics['activeProducts'] ?? 0) }}</strong><span class="ft-mgmt-kpi-meta">Available in product catalogue</span></a>
    </section>

    <section class="ft-mgmt-grid">
        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Work moving through FlowTrack</h2><p>Active records grouped by configured workflow phase. Client-specific phase differences are labelled.</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setFlowTab('orders')" class="ft-mgmt-tab {{ $flowTab === 'orders' ? 'active' : '' }}">Orders</button>
                    <button type="button" wire:click="setFlowTab('inquiries')" class="ft-mgmt-tab {{ $flowTab === 'inquiries' ? 'active' : '' }}">Inquiries</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body">
                <div class="ft-mgmt-flow-bars">
                    @forelse($flowRows as $row)
                        <div class="ft-mgmt-flow-row">
                            <div class="ft-mgmt-flow-label-wrap" title="{{ $row['label'] }}{{ $row['scope_text'] !== '' ? ' — '.$row['scope_text'] : '' }}">
                                <span class="ft-mgmt-flow-label">{{ $row['label'] }}</span>
                                @if($row['is_mismatch'] && $row['scope_text'] !== '')
                                    <span class="ft-mgmt-flow-scope">{{ $row['scope_text'] }}</span>
                                @endif
                            </div>
                            <div class="ft-mgmt-track"><span class="ft-mgmt-fill {{ $row['is_mismatch'] ? 'amber' : '' }}" style="width:{{ $row['width'] }}%"></span></div>
                            <span class="ft-mgmt-flow-value">{{ $row['count'] }}</span>
                        </div>
                    @empty
                        <div class="ft-mgmt-empty">No active workflow data.</div>
                    @endforelse
                </div>
                @if($flowRows->isNotEmpty())
                    <div class="ft-mgmt-phase-strip" aria-label="Configured workflow phases">
                        @foreach($flowRows as $row)
                            <span
                                class="ft-mgmt-phase {{ $row['count'] > 0 ? 'active' : '' }} {{ $row['is_mismatch'] ? 'client-specific' : '' }}"
                                title="{{ $row['label'] }}: {{ $row['count'] }}{{ $row['scope_text'] !== '' ? ' — '.$row['scope_text'] : '' }}"
                            ></span>
                        @endforeach
                    </div>
                    <div class="ft-mgmt-phase-tip">
                        @foreach($flowRows as $row)
                            <span title="{{ $row['label'] }}{{ $row['scope_text'] !== '' ? ' — '.$row['scope_text'] : '' }}">
                                {{ \Illuminate\Support\Str::limit($row['label'], 18) }}
                                @if($row['is_mismatch'] && $row['scope_label'] !== '')<small>{{ $row['scope_label'] }}</small>@endif
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Needs attention</h2><p>Exceptions ranked by urgency and business impact</p></div><a class="ft-mgmt-link" href="{{ route('all-tasks') }}" wire:navigate>View all</a></div>
            <div class="ft-mgmt-panel-body">
                <div class="ft-mgmt-attention-list">
                    @forelse($attentionTasks as $task)
                        @php
                            [$flagLabel, $flagTone] = $taskFlag($task);
                        @endphp
                        <a class="ft-mgmt-attention" href="{{ route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) }}" wire:navigate wire:key="mgmt-attention-{{ $task->id }}">
                            <span class="ft-mgmt-severity {{ $flagTone === 'red' ? '' : 'amber' }}"></span>
                            <span><strong>{{ $task->title }}</strong><small>{{ $task->task_number }} · {{ $task->job?->displayOrderNumber() ?? 'Order' }} · {{ $task->assignee?->name ?? 'Unassigned' }}</small></span>
                            <span class="ft-mgmt-badge {{ $flagTone }}">{{ $flagLabel }}</span>
                        </a>
                    @empty
                        <div class="ft-mgmt-empty">No attention items match the current filters.</div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>

    <section class="ft-mgmt-grid">
        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Task status distribution</h2><p>Current {{ $taskStatusTab === 'orders' ? 'Order' : 'Inquiry' }} task status from Master Data</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setTaskStatusTab('orders')" class="ft-mgmt-tab {{ $taskStatusTab === 'orders' ? 'active' : '' }}">Orders</button>
                    <button type="button" wire:click="setTaskStatusTab('inquiries')" class="ft-mgmt-tab {{ $taskStatusTab === 'inquiries' ? 'active' : '' }}">Inquiries</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body ft-mgmt-status-layout">
                <div class="ft-mgmt-donut" style="background:{{ $donutBackground }}"><div class="ft-mgmt-donut-center"><strong>{{ $statusTotal }}</strong><span>active tasks</span></div></div>
                <div class="ft-mgmt-legend">
                    @forelse($statusRows as $row)
                        <a href="{{ $statusOpenRoute }}" wire:navigate title="{{ $row['configured'] ? 'Configured in Master Data' : 'Active legacy status' }}"><span class="dot" style="background:{{ $row['color'] }}"></span>{{ $row['label'] }}</a><b>{{ $row['count'] }}</b>
                    @empty
                        <span class="ft-mgmt-sub">No active task statuses.</span><b>0</b>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Client portfolio health</h2><p>Active work, inquiry volume and delivery performance</p></div><a class="ft-mgmt-link" href="{{ route('clients.index') }}" wire:navigate>All clients</a></div>
            <div class="ft-mgmt-panel-body">
                @forelse($clientPortfolio as $portfolioClient)
                    @php
                        $openTasks = (int) ($portfolioClient->open_tasks_count ?? 0);
                        $overdueTasks = (int) ($portfolioClient->overdue_tasks_count ?? 0);
                        $risk = (int) ($portfolioClient->at_risk_jobs_count ?? 0);
                        $onTime = $openTasks > 0 ? max(0, (int) round((($openTasks - $overdueTasks) / $openTasks) * 100)) : 100;
                    @endphp
                    <div class="ft-mgmt-client-row" wire:key="mgmt-client-{{ $portfolioClient->id }}">
                        <div class="ft-mgmt-client-name"><span class="ft-mgmt-client-logo"><x-ui.client-logo :client="$portfolioClient" :name="$portfolioClient->name" :size="29" /></span><div>{{ $portfolioClient->name }}<div class="ft-mgmt-sub">{{ $risk ? $risk.' attention item'.($risk > 1 ? 's' : '') : 'Healthy portfolio' }}</div></div></div>
                        <b>{{ (int) ($portfolioClient->active_jobs_count ?? 0) }}</b>
                        <b>{{ (int) ($portfolioClient->open_inquiries_count ?? 0) }}</b>
                        <div><div class="ft-mgmt-health"><span style="width:{{ $onTime }}%"></span></div><div class="ft-mgmt-sub">{{ $onTime }}% on time</div></div>
                    </div>
                @empty
                    <div class="ft-mgmt-empty">No client portfolio data matches the current filters.</div>
                @endforelse
            </div>
        </article>
    </section>

    {{-- TEMPORARILY DISABLED: Team performance & workload. Remove this Blade comment wrapper to restore.
    <section class="ft-mgmt-panel" style="margin-bottom:14px">
        <div class="ft-mgmt-panel-head"><div><h2>Team performance &amp; workload</h2><p>Capacity, completion pace, on-time rate and attention exposure</p></div><button type="button" class="ft-mgmt-link" wire:click="toggleTeamSort">{{ $teamSortByWorkload ? 'Restore default' : 'Sort by workload' }}</button></div>
        <div class="ft-mgmt-panel-body">
            <div class="ft-mgmt-team-grid">
                @forelse($assigneePerformance as $person)
                    @php
                        $ongoing = (int) $person->ongoing_count;
                        $completed = (int) $person->done_count;
                        $onTime = $completed > 0 ? (int) round(((int) $person->done_on_time_count / $completed) * 100) : 100;
                        $load = $ongoing > 0 ? min(100, max(8, (int) round(($ongoing / max(1, (int) $teamMaxOngoing)) * 100))) : 0;
                        $highThreshold = max(8.0, ((float) $teamAverageOngoing) * 1.25);
                        $availableThreshold = max(1.0, ((float) $teamAverageOngoing) * .60);
                        $workloadLabel = $ongoing <= 0 ? 'Available' : ($ongoing >= $highThreshold ? 'High' : ($ongoing <= $availableThreshold ? 'Available' : 'Balanced'));
                        $score = max(0, min(100, (int) round(($onTime * .7) + ((100 - max(0, $load - 65)) * .3))));
                    @endphp
                    <a class="ft-mgmt-team-card" href="{{ route('all-tasks', ['assignee' => $person->id]) }}" wire:navigate wire:key="mgmt-person-{{ $person->id }}" style="text-decoration:none;color:inherit">
                        <div class="ft-mgmt-person"><x-ui.avatar :user="$person" :name="$person->name" :size="27" /><div style="min-width:0">{{ $person->name }}<div class="ft-mgmt-sub">{{ $person->department?->name ?? 'Team member' }}</div></div><span style="margin-left:auto"><span class="ft-mgmt-score">{{ $score }}</span><span class="ft-mgmt-capacity"> / 100</span></span></div>
                        <div class="ft-mgmt-team-stat"><span>Ongoing</span><b>{{ $ongoing }}</b></div>
                        <div class="ft-mgmt-team-stat"><span>Completed</span><b>{{ $completed }}</b></div>
                        <div class="ft-mgmt-team-stat"><span>On time</span><b>{{ $onTime }}%</b></div>
                        <div class="ft-mgmt-loadbar"><span style="width:{{ $load }}%;{{ $workloadLabel === 'High' ? 'background:#e8a526' : '' }}"></span></div>
                        <div class="ft-mgmt-team-stat"><span>Workload</span><b>{{ $workloadLabel }}</b></div>
                    </a>
                @empty
                    <div class="ft-mgmt-empty">No team workload matches the current filters.</div>
                @endforelse
            </div>
            @if($teamHiddenCount > 0 || $teamExpanded)
                <div class="ft-mgmt-team-more">
                    <button type="button" class="ft-mgmt-btn" wire:click="toggleTeamExpanded">
                        {{ $teamExpanded ? 'Show fewer users' : 'See more users ('.$teamHiddenCount.')' }}
                    </button>
                </div>
            @endif
        </div>
    </section>
    --}}

    <section class="ft-mgmt-panel" style="margin-bottom:14px">
        <div class="ft-mgmt-panel-head">
            <div><h2>Priority work</h2><p>Top urgent Orders, Inquiries and Tasks ranked by attention, due date and priority</p></div>
            <div class="ft-mgmt-tabs">
                <button type="button" wire:click="setPriorityTab('orders')" class="ft-mgmt-tab {{ $priorityTab === 'orders' ? 'active' : '' }}">Orders</button>
                <button type="button" wire:click="setPriorityTab('inquiries')" class="ft-mgmt-tab {{ $priorityTab === 'inquiries' ? 'active' : '' }}">Inquiries</button>
                <button type="button" wire:click="setPriorityTab('tasks')" class="ft-mgmt-tab {{ $priorityTab === 'tasks' ? 'active' : '' }}">Tasks</button>
            </div>
        </div>
        <div class="ft-mgmt-table-wrap">
            <table class="ft-mgmt-table">
                @if($priorityTab === 'orders')
                    <thead><tr><th>Order</th><th>Client</th><th>Stage</th><th>Progress</th><th>Attention</th><th>Owner</th><th>Delivery</th><th></th></tr></thead>
                    <tbody>
                        @forelse($priorityJobs as $job)
                            @php
                                [$flagLabel, $flagTone] = $jobFlag($job);
                            @endphp
                            <tr wire:key="mgmt-priority-job-{{ $job->id }}">
                                <td><a class="ft-mgmt-primary-text" href="{{ route('jobs.index', ['open' => $job->id]) }}" wire:navigate>{{ $job->displayOrderNumber() }}</a><div class="ft-mgmt-sub">{{ $job->title }}</div></td>
                                <td>{{ $job->client?->name ?? '—' }}</td><td><span class="ft-mgmt-badge {{ $badgeTone($job->phase?->short_name) }}">{{ $job->phase?->short_name ?? $job->phase?->name ?? 'Unassigned' }}</span></td>
                                <td><div class="ft-mgmt-progress-cell"><div class="ft-mgmt-track"><span class="ft-mgmt-fill" style="width:{{ min(100, max(0, (int) $job->progress)) }}%"></span></div><b>{{ (int) $job->progress }}%</b></div></td>
                                <td><span class="ft-mgmt-badge {{ $flagTone }}">{{ $flagLabel }}</span></td>
                                <td>@if($job->owner)<span class="ft-mgmt-person"><x-ui.avatar :user="$job->owner" :name="$job->owner->name" :size="27" />{{ $job->owner->name }}</span>@else Unassigned @endif</td>
                                <td>{{ $job->delivery_date?->format('M j') ?? '—' }}</td><td><a class="ft-mgmt-tiny-action" href="{{ route('jobs.index', ['open' => $job->id]) }}" wire:navigate>View</a></td>
                            </tr>
                        @empty<tr><td colspan="8" class="ft-mgmt-empty">No matching orders found.</td></tr>@endforelse
                    </tbody>
                @elseif($priorityTab === 'inquiries')
                    <thead><tr><th>Inquiry</th><th>Client</th><th>Current task</th><th>Status</th><th>Flag</th><th>Owner</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        @forelse($priorityInquiries as $inquiry)
                            @php
                                [$flagLabel, $flagTone] = $inquiryFlag($inquiry);
                                $statusColor = $inquiryService->inquiryStatusColor($inquiry->status ?: 'To do', (string) ($inquiry->currentTask?->status ?: ''));
                            @endphp
                            <tr wire:key="mgmt-priority-inquiry-{{ $inquiry->id }}">
                                <td><a class="ft-mgmt-primary-text" href="{{ route('inquiries.index', ['open' => $inquiry->id]) }}" wire:navigate>{{ $inquiry->inquiry_number }}</a><div class="ft-mgmt-sub">{{ $inquiry->subject }}</div></td>
                                <td>{{ $inquiry->client?->name ?? '—' }}</td><td>{{ $inquiry->currentTask?->title ?? 'No current task' }}</td>
                                <td><span class="ft-mgmt-badge {{ $badgeTone($inquiry->status) }}">{{ $inquiry->status ?: 'To do' }}</span></td><td><span class="ft-mgmt-badge {{ $flagTone }}">{{ $flagLabel }}</span></td>
                                <td>@if($inquiry->owner)<span class="ft-mgmt-person"><x-ui.avatar :user="$inquiry->owner" :name="$inquiry->owner->name" :size="27" />{{ $inquiry->owner->name }}</span>@else Unassigned @endif</td>
                                <td>{{ $inquiry->currentTask?->due_date?->format('M j') ?? '—' }}</td><td><a class="ft-mgmt-tiny-action" href="{{ route('inquiries.index', ['open' => $inquiry->id]) }}" wire:navigate>View</a></td>
                            </tr>
                        @empty<tr><td colspan="8" class="ft-mgmt-empty">No matching inquiries found.</td></tr>@endforelse
                    </tbody>
                @else
                    <thead><tr><th>Task</th><th>Order</th><th>Phase</th><th>Status</th><th>Attention</th><th>Assignee</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        @forelse($priorityTasks as $task)
                            @php
                            [$flagLabel, $flagTone] = $taskFlag($task);
                        @endphp
                            <tr wire:key="mgmt-priority-task-{{ $task->id }}">
                                <td><a class="ft-mgmt-primary-text" href="{{ route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) }}" wire:navigate>{{ $task->title }}</a><div class="ft-mgmt-sub">{{ $task->task_number }}</div></td>
                                <td>{{ $task->job?->displayOrderNumber() ?? '—' }}</td><td>{{ $task->phase?->short_name ?? $task->phase?->name ?? '—' }}</td>
                                <td><span class="ft-mgmt-badge {{ $badgeTone($task->status) }}">{{ $task->status }}</span></td><td><span class="ft-mgmt-badge {{ $flagTone }}">{{ $flagLabel }}</span></td>
                                <td>@if($task->assignee)<span class="ft-mgmt-person"><x-ui.avatar :user="$task->assignee" :name="$task->assignee->name" :size="27" />{{ $task->assignee->name }}</span>@else Unassigned @endif</td>
                                <td>{{ $task->due_date?->format('M j') ?? '—' }}</td><td><a class="ft-mgmt-tiny-action" href="{{ route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) }}" wire:navigate>View</a></td>
                            </tr>
                        @empty<tr><td colspan="8" class="ft-mgmt-empty">No matching tasks found.</td></tr>@endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </section>

    <section class="ft-mgmt-grid">
        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Recent activity</h2><p>Latest changes from Orders, Inquiries and Tasks</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setActivityTab('all')" class="ft-mgmt-tab {{ $activityTab === 'all' ? 'active' : '' }}">All</button>
                    <button type="button" wire:click="setActivityTab('orders')" class="ft-mgmt-tab {{ $activityTab === 'orders' ? 'active' : '' }}">Orders</button>
                    <button type="button" wire:click="setActivityTab('inquiries')" class="ft-mgmt-tab {{ $activityTab === 'inquiries' ? 'active' : '' }}">Inquiries</button>
                    <button type="button" wire:click="setActivityTab('tasks')" class="ft-mgmt-tab {{ $activityTab === 'tasks' ? 'active' : '' }}">Tasks</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body"><div class="ft-mgmt-activity-list">
                @forelse($recentActivity as $activity)
                    <div class="ft-mgmt-activity" wire:key="mgmt-activity-{{ $activity->id }}">
                        <div class="ft-mgmt-activity-icon">{{ ($activity->dashboard_kind ?? '') === 'tasks' ? '✓' : (($activity->dashboard_kind ?? '') === 'inquiries' ? '?' : '▣') }}</div>
                        <div><strong>{{ $orderTerminology($activity->dashboard_title) }}</strong><p>{{ $orderTerminology($activity->dashboard_detail) }}</p></div>
                        <time>{{ $activity->created_at?->diffForHumans(short: true) }}</time>
                    </div>
                @empty<div class="ft-mgmt-empty">No Order, Inquiry or Task changes match the selected period or filters.</div>@endforelse
            </div></div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Catalogue readiness</h2><p>Product, category, supplier and document coverage</p></div><a class="ft-mgmt-link" href="{{ route('master-data', ['group' => 'product']) }}" wire:navigate>Open catalogue</a></div>
            <div class="ft-mgmt-panel-body"><div class="ft-mgmt-flow-bars">
                @foreach($catalogueReadiness['rows'] ?? [] as $index => $row)
                    <div class="ft-mgmt-flow-row"><span class="ft-mgmt-flow-label">{{ $row['label'] }}</span><div class="ft-mgmt-track"><span class="ft-mgmt-fill {{ $index === 3 ? 'amber' : '' }}" style="width:{{ $row['value'] }}%"></span></div><span class="ft-mgmt-flow-value">{{ $row['value'] }}%</span></div>
                @endforeach
            </div></div>
        </article>
    </section>
</x-ui.management-theme>
