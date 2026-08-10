<div>
@if($showCreate)
    <x-clients.create
        :users="$users"
        :client-code="$clientCode"
        :client-countries="$clientCountries"
        :client-country-flags="$clientCountryFlags"
        :client-states-by-country="$clientStatesByCountry"
        :client-languages="$clientLanguages"
        :client-currencies="$clientCurrencies"
        :payment-term-options="$paymentTermOptions"
        :account-manager-id="$accountManagerId"
        :preferred-currency="$preferredCurrency"
        :client-country="$clientCountry"
        :billing-country="$billingCountry"
        :billing-same-as-office="$billingSameAsOffice"
        :sales-tax-status="$salesTaxStatus"
        :shipping-addresses="$shippingAddresses"
    />
@elseif($showDetail && $detail)
    <x-clients.detail
        :detail="$detail"
        :users="$users"
        :editing="$showEdit"
        :tab="$clientDetailTab"
        :orders="$clientOrders"
        :documents="$clientDocuments"
        :activities="$clientActivities"
        :order-status-options="$clientOrderStatusOptions"
        :order-owner-options="$clientOrderOwnerOptions"
        :document-count="$clientDocumentCount"
        :order-metrics="$clientOrderMetrics"
        :client-code="$clientCode"
        :client-countries="$clientCountries ?? []"
        :client-country-flags="$clientCountryFlags ?? []"
        :client-states-by-country="$clientStatesByCountry ?? []"
        :client-languages="$clientLanguages ?? []"
        :client-currencies="$clientCurrencies ?? []"
        :payment-term-options="$paymentTermOptions ?? []"
        :account-manager-id="$accountManagerId"
        :preferred-currency="$preferredCurrency"
        :client-country="$clientCountry"
        :billing-country="$billingCountry"
        :billing-same-as-office="$billingSameAsOffice"
        :sales-tax-status="$salesTaxStatus"
        :edit-shipping-addresses="$shippingAddresses"
    />
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
            <button class="ft-clients-new ft-dashboard-action-match" type="button" wire:click="openCreate"><span class="ft-dashboard-action-match-icon">+</span>New Client</button>
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

            <div class="ft-list-filter-shell {{ $showArchived ? 'is-archived' : '' }}">
                <div class="ft-list-filter-grid ft-client-filter-grid">
                    <x-ui.list-search property="search" :value="$search" placeholder="Client, Job ID, country or manager…" />
                    <x-ui.remote-filter label="Account manager" property="manager" type="users" context="clients" :value="$manager" placeholder="Anyone" :initial-options="$managerFilterOptions" />
                    <x-ui.remote-filter label="Country" property="country" type="countries" :context="$showArchived ? 'clients-archived' : 'clients'" :value="$country" placeholder="All countries" :initial-options="$countryFilterOptions" />
                    @if(!$showArchived)<x-ui.select-filter label="Job health" property="jobHealth" :value="$jobHealth" placeholder="All health" :options="$healthOptions->map(fn($healthOption) => ['id'=>$healthOption,'label'=>$healthOption])" />@endif
                    <x-ui.select-filter label="Outstanding" property="outstanding" :value="$outstanding" placeholder="Any balance" :options="collect([['id'=>'positive','label'=>'Has balance'],['id'=>'high','label'=>'$10,000+'],['id'=>'zero','label'=>'No balance']])" />
                </div>
                @php
                    $chips = collect();
                    if($search) $chips->push(['key'=>'search','label'=>'Search: '.$search]);
                    if($manager) $chips->push(['key'=>'manager','label'=>'Manager: '.(collect($managerFilterOptions)->firstWhere('id',(int)$manager)['label'] ?? 'Selected')]);
                    if($country) $chips->push(['key'=>'country','label'=>'Country: '.$country]);
                    if($jobHealth) $chips->push(['key'=>'jobHealth','label'=>'Health: '.$jobHealth]);
                    if($outstanding) $chips->push(['key'=>'outstanding','label'=>'Outstanding: '.(['positive'=>'Has balance','high'=>'$10,000+','zero'=>'No balance'][$outstanding] ?? $outstanding)]);
                @endphp
                @if($chips->isNotEmpty() || $quick !== 'all')
                <div class="ft-list-active-row">
                    <div class="ft-list-filter-chips">@foreach($chips as $chip)<span class="ft-list-filter-chip">{{ $chip['label'] }}<button type="button" wire:click="clearFilter('{{ $chip['key'] }}')">×</button></span>@endforeach</div>
                    <button type="button" class="ft-list-clear-all" wire:click="clearFilters">Clear all filters</button>
                </div>
                @endif
            </div>


            <div class="ft-client-list-card">
                <div class="ft-client-table-scroll ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,manager,country,jobHealth,outstanding,quick">
                    @if($showArchived)
                    <table class="ft-client-table ft-archived-client-table">
                        <thead><tr><th>Archived client</th><th>Account manager</th><th>Job history</th><th>Outstanding</th><th>Archived</th><th>Actions</th></tr></thead>
                        <tbody>
                        @forelse($clients as $clientRow)
                            <tr wire:key="archived-client-row-{{ $clientRow->id }}">
                                <td data-label="Archived client">
                                    <div class="ft-client-identity"><span class="ft-client-logo is-archived">{{ \App\Support\BoardPresenter::initials($clientRow->name) }}</span><span><b>{{ $clientRow->name }}</b><small>{{ $clientRow->code }} · {{ $clientRow->country ?: 'No country' }}</small></span></div>
                                </td>
                                <td data-label="Account manager">@if($clientRow->accountManager)<div class="ft-client-person"><x-ui.avatar :user="$clientRow->accountManager" :name="$clientRow->accountManager->name" :size="26" /><span>{{ $clientRow->accountManager->name }}</span></div>@else<span class="muted">Unassigned</span>@endif</td>
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
                                wire:click="viewClient({{ $clientRow->id }})"
                                wire:keydown.enter="viewClient({{ $clientRow->id }})"
                                wire:keydown.space.prevent="viewClient({{ $clientRow->id }})"
                                tabindex="0"
                                aria-label="Open client {{ $clientRow->name }}"
                            >
                                <td data-label="Client"><div class="ft-client-identity"><span class="ft-client-logo">{{ \App\Support\BoardPresenter::initials($clientRow->name) }}</span><span><b>{{ $clientRow->name }}</b><small>{{ $clientRow->country ?: '—' }}</small></span></div></td>
                                <td data-label="Account manager">@if($clientRow->accountManager)<div class="ft-client-person"><x-ui.avatar :user="$clientRow->accountManager" :name="$clientRow->accountManager->name" :size="26" /><span>{{ $clientRow->accountManager->name }}</span></div>@else<span class="muted">Unassigned</span>@endif</td>
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
                                <td
                                    data-label="Actions"
                                    class="ft-client-action-cell"
                                    x-data="window.FlowTrackFloatingActionMenu()"
                                    x-on:resize.window="positionMenu()"
                                    x-on:scroll.window="positionMenu()"
                                >
                                    <button x-ref="trigger" type="button" class="ft-client-more" wire:click.stop="toggleClientMenu({{ $clientRow->id }})" aria-label="Client actions">⋮</button>
                                    @if($actionMenuClientId === (int)$clientRow->id)
                                        <div
                                            x-ref="menu"
                                            x-cloak
                                            x-show="menuStyle !== ''"
                                            x-init="$nextTick(() => positionMenu())"
                                            x-bind:style="menuStyle"
                                            class="ft-client-action-menu"
                                            x-on:click.stop
                                        >
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


</div>
@endif
</div>
