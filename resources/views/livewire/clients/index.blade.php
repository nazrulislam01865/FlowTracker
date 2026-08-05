<div>
@if($showCreate)
    <x-clients.create :users="$users" :client-code="$clientCode" />
@elseif($showDetail && $detail)
    <x-clients.detail :detail="$detail" :users="$users" :editing="$showEdit" />
@else
@php
    $selected = $detail['client'] ?? null;
    $activeJobs = $detail['active'] ?? collect();
    $attentionTasks = $detail['tasks'] ?? collect();
    $selectedHealth = $detail['health'] ?? 'On Track';
@endphp
<div class="ft-clients-reference">
    <div class="ft-clients-page-head">
        <div>
            <h1>{{ $showArchived ? 'Archived Clients' : 'Clients' }}</h1>
            <p>{{ $showArchived ? 'Review inactive clients and restore them when needed.' : 'Monitor client Jobs, task delivery, account health and outstanding balances.' }}</p>
        </div>
        @if(auth()->user()->canModule('clients','create'))
            <button class="ft-clients-new" type="button" wire:click="openCreate">＋ New Client</button>
        @endif
    </div>

    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif

    <div class="ft-client-list-modes" role="tablist" aria-label="Client status">
        <button type="button" wire:click="showActiveClients" class="{{ !$showArchived ? 'active' : '' }}">Active Clients <span>{{ $summary['clients'] }}</span></button>
        <button type="button" wire:click="showArchivedClients" class="{{ $showArchived ? 'active' : '' }}">Archived Clients <span>{{ $summary['archived'] }}</span></button>
    </div>

    <div class="ft-clients-layout ft-clients-layout-full">
        <section class="ft-clients-main">
            @if(!$showArchived)
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
            @endif

            <div class="ft-client-filter-card {{ $showArchived ? 'is-archived' : '' }}">
                <div class="ft-client-filter-search"><span>⌕</span><input wire:model.live.debounce.300ms="search" placeholder="Search client, Job ID, country or manager"></div>
                <select wire:model.live="manager"><option value="">Account manager</option>@foreach($managers as $managerOption)<option value="{{ $managerOption->id }}">{{ $managerOption->name }}</option>@endforeach</select>
                <select wire:model.live="country"><option value="">Country</option>@foreach($countries as $countryOption)<option value="{{ $countryOption }}">{{ $countryOption }}</option>@endforeach</select>
                @if(!$showArchived)<select wire:model.live="jobHealth"><option value="">Job health</option>@foreach($healthOptions as $healthOption)<option value="{{ $healthOption }}">{{ $healthOption }}</option>@endforeach</select>@endif
                <select wire:model.live="outstanding"><option value="">Outstanding</option><option value="positive">Has balance</option><option value="high">$10,000+</option><option value="zero">No balance</option></select>
                <button type="button" wire:click="clearFilters">Clear</button>
            </div>

            @if(!$showArchived)
            <div class="ft-client-quick-row">
                <button type="button" wire:click="setQuick('all')" class="{{ $quick==='all'?'active':'' }}">All clients <span>{{ $summary['clients'] }}</span></button>
                <button type="button" wire:click="setQuick('active_jobs')" class="{{ $quick==='active_jobs'?'active':'' }}">Active Jobs <span>{{ $summary['clients_active'] }}</span></button>
                <button type="button" wire:click="setQuick('attention')" class="{{ $quick==='attention'?'active':'' }}">Needs attention <span>{{ $summary['clients_attention'] }}</span></button>
                <button type="button" wire:click="setQuick('outstanding')" class="{{ $quick==='outstanding'?'active':'' }}">Outstanding balance <span>{{ $summary['clients_outstanding'] }}</span></button>
            </div>
            @endif

            <div class="ft-client-list-card">
                <div class="ft-client-table-scroll">
                    @if($showArchived)
                    <table class="ft-client-table ft-archived-client-table">
                        <thead><tr><th>Archived client</th><th>Account manager</th><th>Job history</th><th>Outstanding</th><th>Archived</th><th>Actions</th></tr></thead>
                        <tbody>
                        @forelse($clients as $clientRow)
                            <tr wire:key="archived-client-row-{{ $clientRow->id }}">
                                <td data-label="Archived client">
                                    <div class="ft-client-identity"><span class="ft-client-logo is-archived">{{ \App\Support\BoardPresenter::initials($clientRow->name) }}</span><span><b>{{ $clientRow->name }}</b><small>{{ $clientRow->code }} · {{ $clientRow->country ?: 'No country' }}</small></span></div>
                                </td>
                                <td data-label="Account manager">@if($clientRow->accountManager)<div class="ft-client-person"><x-ui.avatar :name="$clientRow->accountManager->name" :size="26" /><span>{{ $clientRow->accountManager->name }}</span></div>@else<span class="muted">Unassigned</span>@endif</td>
                                <td data-label="Job history"><b>{{ $clientRow->total_jobs_count }}</b> {{ \Illuminate\Support\Str::plural('Job', $clientRow->total_jobs_count) }} preserved</td>
                                <td data-label="Outstanding"><b>${{ number_format($clientRow->outstanding_balance,0) }}</b></td>
                                <td data-label="Archived"><span class="ft-archived-status">Archived</span><small>{{ $clientRow->updated_at?->diffForHumans(short:true) }}</small></td>
                                <td data-label="Actions">
                                    <div class="ft-archived-actions">
                                        <button type="button" class="ft-archive-view" wire:click="viewClient({{ $clientRow->id }})">View history</button>
                                        @if(auth()->user()->canModule('clients','delete'))<button type="button" class="ft-archive-restore" wire:click="restoreClient({{ $clientRow->id }})" wire:confirm="Restore this client to the active client list?">Restore</button>@endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="ft-client-empty">No archived clients match the selected filters.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    @else
                    <table class="ft-client-table">
                        <thead><tr><th>Client</th><th>Account manager</th><th>Jobs</th><th>Tasks</th><th>Health</th><th>Next delivery</th><th>Outstanding</th><th>Updated</th><th>Actions</th></tr></thead>
                        <tbody>
                        @forelse($clients as $clientRow)
                            @php
                                $rowHealth = $clientRow->attention_jobs_count > 0 ? 'Needs Attention' : ($clientRow->overdue_tasks_count > 0 ? 'At Risk' : 'On Track');
                                $healthClass = $rowHealth === 'On Track' ? 'green' : ($rowHealth === 'At Risk' ? 'amber' : 'red');
                            @endphp
                            <tr
                                wire:key="client-row-{{ $clientRow->id }}"
                                class="{{ $showClientPreview && (int)$selectedClientId === (int)$clientRow->id ? 'selected' : '' }}"
                                wire:click="openClient({{ $clientRow->id }})"
                                wire:keydown.enter="openClient({{ $clientRow->id }})"
                                wire:keydown.space.prevent="openClient({{ $clientRow->id }})"
                                tabindex="0"
                                aria-label="Preview client {{ $clientRow->name }}"
                            >
                                <td data-label="Client"><div class="ft-client-identity"><span class="ft-client-logo">{{ \App\Support\BoardPresenter::initials($clientRow->name) }}</span><span><b>{{ $clientRow->name }}</b><small>{{ $clientRow->country ?: '—' }}</small></span></div></td>
                                <td data-label="Account manager">@if($clientRow->accountManager)<div class="ft-client-person"><x-ui.avatar :name="$clientRow->accountManager->name" :size="26" /><span>{{ $clientRow->accountManager->name }}</span></div>@else<span class="muted">Unassigned</span>@endif</td>
                                <td data-label="Jobs"><b>{{ $clientRow->active_jobs_count }} / {{ $clientRow->total_jobs_count }}</b> active<div class="ft-mini-progress"><span style="width:{{ $clientRow->total_jobs_count ? min(100,round(($clientRow->active_jobs_count/$clientRow->total_jobs_count)*100)) : 0 }}%"></span></div></td>
                                <td data-label="Tasks">
                                    <b>{{ $clientRow->open_tasks_count }}</b> open
                                    @if ((int) $clientRow->overdue_tasks_count > 0)
                                        <small class="ft-text-red">{{ $clientRow->overdue_tasks_count }} overdue</small>
                                    @elseif ((int) $clientRow->blocked_tasks_count > 0)
                                        <small class="ft-text-purple">{{ $clientRow->blocked_tasks_count }} blocked</small>
                                    @else
                                        <small class="ft-text-green">0 overdue</small>
                                    @endif
                                </td>
                                <td data-label="Health"><span class="ft-client-health {{ $healthClass }}">{{ $rowHealth }}</span></td>
                                <td data-label="Next delivery">{{ $clientRow->next_delivery_at ? \Carbon\Carbon::parse($clientRow->next_delivery_at)->format('M j') : '—' }}</td>
                                <td data-label="Outstanding"><b>${{ number_format($clientRow->outstanding_balance,0) }}</b></td>
                                <td data-label="Updated">{{ $clientRow->updated_at?->diffForHumans(short:true) }}</td>
                                <td data-label="Actions" class="ft-client-action-cell">
                                    <button type="button" class="ft-client-more" wire:click.stop="toggleClientMenu({{ $clientRow->id }})" aria-label="Client actions">⋮</button>
                                    @if($actionMenuClientId === (int)$clientRow->id)
                                        <div class="ft-client-action-menu" x-on:click.stop>
                                            <button type="button" wire:click.stop="viewClient({{ $clientRow->id }})">View client</button>
                                            @php
                                                $access = app(\App\Services\AccessControlService::class);
                                                $rowCanEdit = $access->isAdministrator(auth()->user()) || $access->canEditAll(auth()->user(),'clients') || ($access->canEditOwn(auth()->user(),'clients') && (int)$clientRow->account_manager_id === (int)auth()->id());
                                            @endphp
                                            @if(!$showArchived && $rowCanEdit)<button type="button" wire:click.stop="editClient({{ $clientRow->id }})">Edit client</button>@endif
                                            @if(auth()->user()->canModule('clients','delete'))
                                                @if($showArchived)
                                                    <button type="button" wire:click.stop="restoreClient({{ $clientRow->id }})" wire:confirm="Restore this client to the active client list?">Restore client</button>
                                                @else
                                                    <button type="button" class="danger" wire:click.stop="deleteClient({{ $clientRow->id }})" wire:confirm="Archive this client? Existing history will be preserved and the client can be restored later.">Archive client</button>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="ft-client-empty">{{ $showArchived ? 'No archived clients match the selected filters.' : 'No clients match the selected filters.' }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    @endif
                </div>
                <div class="ft-client-pagination">
                    <span>Showing {{ $clients->firstItem() ?? 0 }}–{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} {{ $showArchived ? 'archived ' : '' }}clients</span>
                    <div><label>Rows per page:</label><select wire:model.live="perPage"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option></select><button type="button" wire:click="previousPage" @disabled($clients->onFirstPage())>Previous</button><span>Page {{ $clients->currentPage() }} of {{ max(1,$clients->lastPage()) }}</span><button type="button" wire:click="nextPage" @disabled(!$clients->hasMorePages())>Next →</button></div>
                </div>
            </div>
        </section>

    </div>

    @if($showClientPreview && $selected)
        <div
            class="ft-client-preview-backdrop"
            wire:key="client-preview-{{ $selected->id }}"
            wire:click.self="closeClientPreview"
            x-data
            x-on:keydown.escape.window="$wire.closeClientPreview()"
            x-init="$nextTick(() => $refs.dialog.focus())"
        >
        <aside
            class="ft-client-detail-card ft-client-preview-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="client-preview-title-{{ $selected->id }}"
            tabindex="-1"
            x-ref="dialog"
        >
            @php
                $detailHealthClass = $selectedHealth === 'On Track' ? 'green' : ($selectedHealth === 'At Risk' ? 'amber' : 'red');
                $selectedInitials = collect(preg_split('/\s+/', trim($selected->name)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part,0,1)))->implode('');
            @endphp
            <button class="ft-client-preview-close" type="button" wire:click="closeClientPreview" aria-label="Close client preview">×</button>
            <div class="ft-client-detail-head">
                <span class="ft-client-detail-logo">{{ $selectedInitials ?: 'CL' }}</span>
                <div><h2 id="client-preview-title-{{ $selected->id }}">{{ $selected->name }}</h2><p>{{ $selected->country ?: '—' }} <span class="ft-client-health {{ $detailHealthClass }}">{{ $selectedHealth }}</span></p></div>
                <button class="ft-open-client" type="button" wire:click="viewClient({{ $selected->id }})">Open client</button>
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
        </aside>
        </div>
    @endif
</div>
@endif
</div>
