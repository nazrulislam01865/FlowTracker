<div>
@if($showCreate)
    <x-clients.create :users="$users" :client-code="$clientCode" />
@else
@php
    $selected = $detail['client'] ?? null;
    $selectedJobs = $detail['jobs'] ?? collect();
    $activeJobs = $detail['active'] ?? collect();
    $attentionTasks = $detail['tasks'] ?? collect();
    $selectedHealth = $detail['health'] ?? 'On Track';
@endphp
<div class="ft-clients-reference">
    <div class="ft-clients-page-head">
        <div>
            <h1>Clients</h1>
            <p>Monitor client Jobs, task delivery, account health and outstanding balances.</p>
        </div>
        @if(auth()->user()->canModule('clients','create'))
            <button class="ft-clients-new" type="button" wire:click="openCreate">＋ New Client</button>
        @endif
    </div>

    <div class="ft-clients-layout">
        <section class="ft-clients-main">
            <div class="ft-clients-metrics">
                <button type="button" wire:click="setQuick('all')" class="ft-client-metric {{ $quick==='all'?'is-active':'' }}">
                    <span class="ft-client-metric-icon ft-client-metric-blue">♙</span><span><small>Total clients</small><b>{{ number_format($summary['clients']) }}</b></span>
                </button>
                <button type="button" wire:click="setQuick('active_jobs')" class="ft-client-metric {{ $quick==='active_jobs'?'is-active':'' }}">
                    <span class="ft-client-metric-icon ft-client-metric-green">▣</span><span><small>Active Jobs</small><b>{{ number_format($summary['active_jobs']) }}</b></span>
                </button>
                <button type="button" wire:click="setQuick('attention')" class="ft-client-metric {{ $quick==='attention'?'is-active':'' }}">
                    <span class="ft-client-metric-icon ft-client-metric-amber">△</span><span><small>Needs attention</small><b>{{ number_format($summary['attention']) }}</b></span>
                </button>
                <button type="button" wire:click="setQuick('outstanding')" class="ft-client-metric {{ $quick==='outstanding'?'is-active':'' }}">
                    <span class="ft-client-metric-icon ft-client-metric-purple">$</span><span><small>Outstanding</small><b>${{ number_format($summary['outstanding'],0) }}</b></span>
                </button>
            </div>

            <div class="ft-client-filter-card">
                <div class="ft-client-filter-search"><span>⌕</span><input wire:model.live.debounce.300ms="search" placeholder="Search client, Job ID, country or manager"></div>
                <select wire:model.live="manager"><option value="">Account manager</option>@foreach($managers as $managerOption)<option value="{{ $managerOption->id }}">{{ $managerOption->name }}</option>@endforeach</select>
                <select wire:model.live="country"><option value="">Country</option>@foreach($countries as $countryOption)<option value="{{ $countryOption }}">{{ $countryOption }}</option>@endforeach</select>
                <select wire:model.live="jobHealth"><option value="">Job health</option>@foreach($healthOptions as $healthOption)<option value="{{ $healthOption }}">{{ $healthOption }}</option>@endforeach</select>
                <select wire:model.live="outstanding"><option value="">Outstanding</option><option value="positive">Has balance</option><option value="high">$10,000+</option><option value="zero">No balance</option></select>
                <button type="button" wire:click="clearFilters">Clear</button>
            </div>

            <div class="ft-client-quick-row">
                <button type="button" wire:click="setQuick('all')" class="{{ $quick==='all'?'active':'' }}">All clients <span>{{ $summary['clients'] }}</span></button>
                <button type="button" wire:click="setQuick('active_jobs')" class="{{ $quick==='active_jobs'?'active':'' }}">Active Jobs <span>{{ $summary['clients_active'] }}</span></button>
                <button type="button" wire:click="setQuick('attention')" class="{{ $quick==='attention'?'active':'' }}">Needs attention <span>{{ $summary['clients_attention'] }}</span></button>
                <button type="button" wire:click="setQuick('outstanding')" class="{{ $quick==='outstanding'?'active':'' }}">Outstanding balance <span>{{ $summary['clients_outstanding'] }}</span></button>
            </div>

            <div class="ft-client-list-card">
                <div class="ft-client-table-scroll">
                    <table class="ft-client-table">
                        <thead><tr><th>Client</th><th>Account manager</th><th>Jobs</th><th>Tasks</th><th>Health</th><th>Next delivery</th><th>Outstanding</th><th>Updated</th><th>Actions</th></tr></thead>
                        <tbody>
                        @forelse($clients as $clientRow)
                            @php
                                $rowHealth = $clientRow->attention_jobs_count > 0 ? 'Needs Attention' : ($clientRow->overdue_tasks_count > 0 ? 'At Risk' : 'On Track');
                                $healthClass = $rowHealth === 'On Track' ? 'green' : ($rowHealth === 'At Risk' ? 'amber' : 'red');
                                $rowInitials = collect(preg_split('/\s+/', trim($clientRow->name)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part,0,1)))->implode('');
                            @endphp
                            <tr wire:key="client-row-{{ $clientRow->id }}" class="{{ (int)$selectedClientId === (int)$clientRow->id ? 'selected' : '' }}" wire:click="openClient({{ $clientRow->id }})">
                                <td><div class="ft-client-identity"><span class="ft-client-logo">{{ $rowInitials ?: 'CL' }}</span><span><b>{{ $clientRow->name }}</b><small>{{ $clientRow->country ?: '—' }}</small></span></div></td>
                                <td>@if($clientRow->accountManager)<div class="ft-client-person"><x-ui.avatar :name="$clientRow->accountManager->name" :size="26" /><span>{{ $clientRow->accountManager->name }}</span></div>@else<span class="muted">Unassigned</span>@endif</td>
                                <td><b>{{ $clientRow->active_jobs_count }} / {{ $clientRow->total_jobs_count }}</b> active<div class="ft-mini-progress"><span style="width:{{ $clientRow->total_jobs_count ? min(100,round(($clientRow->active_jobs_count/$clientRow->total_jobs_count)*100)) : 0 }}%"></span></div></td>
                                <td>
                                    <b>{{ $clientRow->open_tasks_count }}</b> open
                                    @if ((int) $clientRow->overdue_tasks_count > 0)
                                        <small class="ft-text-red">{{ $clientRow->overdue_tasks_count }} overdue</small>
                                    @elseif ((int) $clientRow->blocked_tasks_count > 0)
                                        <small class="ft-text-purple">{{ $clientRow->blocked_tasks_count }} blocked</small>
                                    @else
                                        <small class="ft-text-green">0 overdue</small>
                                    @endif
                                </td>
                                <td><span class="ft-client-health {{ $healthClass }}">{{ $rowHealth }}</span></td>
                                <td>{{ $clientRow->next_delivery_at ? \Carbon\Carbon::parse($clientRow->next_delivery_at)->format('M j') : '—' }}</td>
                                <td><b>${{ number_format($clientRow->outstanding_balance,0) }}</b></td>
                                <td>{{ $clientRow->updated_at?->diffForHumans(short:true) }}</td>
                                <td><button type="button" class="ft-client-more" wire:click.stop="openClient({{ $clientRow->id }})">⋮</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="ft-client-empty">No clients match the selected filters.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="ft-client-pagination">
                    <span>Showing {{ $clients->firstItem() ?? 0 }}–{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients</span>
                    <div><label>Rows per page:</label><select wire:model.live="perPage"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option></select><button type="button" wire:click="previousPage" @disabled($clients->onFirstPage())>Previous</button><span>Page {{ $clients->currentPage() }} of {{ max(1,$clients->lastPage()) }}</span><button type="button" wire:click="nextPage" @disabled(!$clients->hasMorePages())>Next →</button></div>
                </div>
            </div>
        </section>

        <aside class="ft-client-detail-card">
        @if($selected)
            @php
                $detailHealthClass = $selectedHealth === 'On Track' ? 'green' : ($selectedHealth === 'At Risk' ? 'amber' : 'red');
                $selectedInitials = collect(preg_split('/\s+/', trim($selected->name)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part,0,1)))->implode('');
            @endphp
            <div class="ft-client-detail-head">
                <span class="ft-client-detail-logo">{{ $selectedInitials ?: 'CL' }}</span>
                <div><h2>{{ $selected->name }}</h2><p>{{ $selected->country ?: '—' }} <span class="ft-client-health {{ $detailHealthClass }}">{{ $selectedHealth }}</span></p></div>
                <a class="ft-open-client" href="{{ route('jobs.index',['search'=>$selected->name]) }}" wire:navigate>Open client</a>
                <button type="button" class="ft-client-more">⋮</button>
            </div>
            <div class="ft-client-detail-contact">
                <div><small>Account manager</small>@if($selected->accountManager)<div class="ft-client-person"><x-ui.avatar :name="$selected->accountManager->name" :size="28" /><b>{{ $selected->accountManager->name }}</b></div>@else<b>Unassigned</b>@endif</div>
                <div><small>Contact</small><a href="mailto:{{ $selected->email }}">{{ $selected->email ?: ($selected->contact_name ?: 'No contact recorded') }}</a>@if($selected->phone)<span>{{ $selected->phone }}</span>@endif</div>
            </div>
            <div class="ft-client-detail-stats">
                <div><b>{{ $activeJobs->count() }}</b><small>Active Jobs</small></div><div><b>{{ $detail['openTasks'] }}</b><small>Open Tasks</small></div><div><b class="ft-text-red">{{ $detail['overdue'] }}</b><small>Overdue</small></div><div><b>${{ number_format($selected->outstanding_balance,0) }}</b><small>Outstanding</small></div>
            </div>

            <div class="ft-client-detail-section">
                <div class="ft-client-detail-section-head"><h3>Active Jobs</h3><a href="{{ route('jobs.index',['search'=>$selected->name]) }}" wire:navigate>View all jobs</a></div>
                <table class="ft-client-mini-table"><thead><tr><th>Job ID</th><th>Job name</th><th>Phase</th><th>Progress</th><th>Next delivery</th><th>Health</th></tr></thead><tbody>
                @forelse($activeJobs->take(3) as $job)
                    <tr><td><a href="{{ route('jobs.index',['open'=>$job->id]) }}" wire:navigate>{{ $job->job_number }}</a></td><td>{{ $job->title }}</td><td>{{ $job->phase?->name ?? '—' }}</td><td><b>{{ $job->progress }}%</b><div class="ft-mini-progress"><span style="width:{{ $job->progress }}%"></span></div></td><td>{{ $job->delivery_date?->format('M j') ?? '—' }}</td><td><span class="ft-health-dot {{ in_array($job->health,['Needs Attention','Blocked','Delayed'])?'red':($job->health==='At Risk'?'amber':'green') }}"></span></td></tr>
                @empty<tr><td colspan="6" class="ft-client-empty">No active Jobs.</td></tr>@endforelse
                </tbody></table>
            </div>

            <div class="ft-client-detail-section ft-client-attention-section">
                <div class="ft-client-detail-section-head"><h3>Tasks needing attention</h3><a href="{{ route('my-work') }}" wire:navigate>View all tasks</a></div>
                <table class="ft-client-mini-table"><thead><tr><th>Task</th><th>Due</th><th>Status</th><th>Assignee</th></tr></thead><tbody>
                @forelse($attentionTasks->take(3) as $task)
                    <tr><td><a href="{{ route('jobs.index',['open'=>$task->flow_job_id,'task'=>$task->id]) }}" wire:navigate>{{ $task->title }}</a></td><td class="{{ $task->due_date?->isPast()?'ft-text-red':'' }}">{{ $task->due_date?->isPast() ? 'Overdue '.$task->due_date->diffInDays(today()).'d' : ($task->due_date?->format('M j') ?? '—') }}</td><td><span class="ft-client-health {{ $task->needs_attention||$task->status==='Blocked'?'red':'amber' }}">{{ $task->needs_attention?'Needs Attention':$task->status }}</span></td><td>@if($task->assignee)<div class="ft-client-person"><x-ui.avatar :name="$task->assignee->name" :size="25" /><span>{{ $task->assignee->name }}</span></div>@else<span class="muted">Unassigned</span>@endif</td></tr>
                @empty<tr><td colspan="4" class="ft-client-empty">No tasks need attention.</td></tr>@endforelse
                </tbody></table>
            </div>
            <a class="ft-view-client-work" href="{{ route('jobs.index',['search'=>$selected->name]) }}" wire:navigate>View all client work&nbsp; →</a>
        @else
            <div class="ft-client-empty-detail">Select a client to see its Jobs and Tasks.</div>
        @endif
        </aside>
    </div>
</div>
@endif
</div>
