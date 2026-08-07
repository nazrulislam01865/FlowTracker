@props([
    'jobs','jobSummary','clients','phases','users','priorities','healthOptions','jobStatuses',
    'phaseFilter'=>'','healthFilter'=>'','quickFilter'=>'all','showMoreFilters'=>false,'selectedJobIds'=>[],
    'allFilteredJobsSelected'=>false,
])
<div {{ $attributes->class('ft-list-page ft-jobs-list-page ft-exact-jobs-list') }}>
    <div class="ft-list-head">
        <div><h1>Jobs</h1><p>Manage active jobs from request to collection</p></div>
        <div class="ft-list-actions">@if(auth()->user()->canModule('jobs','export'))<button class="ft-outline-btn" type="button">Export</button>@endif<button class="ft-outline-btn" type="button">Columns</button>@if(auth()->user()->canModule('jobs','create'))<button class="ft-new-job-btn" wire:click="openCreate">＋ New Job</button>@endif</div>
    </div>

    <div class="ft-list-view-tabs">
        <button class="ft-list-view-chip red {{ $quickFilter==='attention' ? 'active' : '' }}" wire:click="setQuickFilter('attention')">Needs attention <b>{{ $jobSummary['attention'] ?? 0 }}</b></button>
        <button class="ft-list-view-chip {{ $quickFilter==='due_week' ? 'active' : '' }}" wire:click="setQuickFilter('due_week')">Due this week <b>{{ $jobSummary['week'] ?? 0 }}</b></button>
        <button class="ft-list-view-chip {{ $quickFilter==='waiting' ? 'active' : '' }}" wire:click="setQuickFilter('waiting')">Waiting for client <b>{{ $jobSummary['waiting'] ?? 0 }}</b></button>
        <button class="ft-list-view-chip amber {{ $quickFilter==='invoice' ? 'active' : '' }}" wire:click="setQuickFilter('invoice')">Unpaid invoices <b>{{ $jobSummary['invoice'] ?? 0 }}</b></button>
        <button class="ft-list-view-chip {{ $quickFilter==='completed' ? 'active' : '' }}" wire:click="setQuickFilter('completed')">Completed <b>{{ $jobSummary['completed'] ?? 0 }}</b></button>
    </div>

    <section class="ft-job-table-card">
        <div class="ft-job-filter-grid ft-job-filter-grid-direct">
            <label class="ft-filter-search ft-job-list-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input wire:model.live.debounce.300ms="search" placeholder="Search Job ID, order, client or product"></label>
            <select wire:model.live="phase"><option value="">Phase</option>@foreach($phases as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
            <select wire:model.live="health"><option value="">Health</option>@foreach($healthOptions as $h)<option value="{{ $h }}">{{ $h }}</option>@endforeach</select>
            <select wire:model.live="owner"><option value="">Owner</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select>
            <select wire:model.live="client"><option value="">Client</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
            <select wire:model.live="delivery"><option value="">Delivery</option><option value="week">Due this week</option><option value="overdue">Overdue</option><option value="none">No delivery date</option></select>
            <select wire:model.live="invoice"><option value="">Invoice</option><option value="pending">Quotation pending</option><option value="draft">Draft / value recorded</option></select>
            <select wire:model.live="priorityFilter"><option value="">Priority</option>@foreach($priorities as $p)<option value="{{ $p->name }}">{{ $p->name }}</option>@endforeach</select>
            <select wire:model.live="jobStatusFilter"><option value="">Job status</option>@foreach($jobStatuses as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select>
            <button class="ft-clear-link" wire:click="clearFilters" type="button">× Clear filters</button>
        </div>

        @if(count($selectedJobIds))
            <div class="ft-job-bulk-bar">
                <div><b>{{ count($selectedJobIds) }}</b> Job{{ count($selectedJobIds) === 1 ? '' : 's' }} selected@if($allFilteredJobsSelected) · all filtered Jobs@endif</div>
                <div class="ft-job-bulk-actions">
                    @if(auth()->user()->canModule('jobs','edit'))
                        <button type="button" class="ft-outline-btn" wire:click="bulkUpdateJobs('deactivate')">Deactivate</button>
                        <button type="button" class="ft-outline-btn" wire:click="bulkUpdateJobs('cancel')" wire:confirm="Cancel the selected Jobs?">Cancel Jobs</button>
                    @endif
                    @if(auth()->user()->canModule('jobs','delete'))
                        <button type="button" class="ft-danger-outline-btn" wire:click="bulkUpdateJobs('delete')" wire:confirm="Delete the selected Jobs? This removes them from active FlowTrack views.">Delete</button>
                    @endif
                </div>
            </div>
        @endif

        <div class="ft-job-table-wrap">
            <table class="ft-job-table">
                <thead><tr><th><label class="ft-checkbox-head"><input type="checkbox" wire:click="toggleSelectAllJobs" @checked($allFilteredJobsSelected) @disabled($jobs->total() === 0) aria-label="Select all {{ $jobs->total() }} filtered Jobs"><span>Select all</span></label></th><th>Job / Order</th><th>Client / Brief</th><th>Product / Qty</th><th>Phase</th><th>Next Action</th><th>Health</th><th>Owner</th><th>Delivery ↓</th><th>Progress</th><th>Invoice</th><th>•••</th></tr></thead>
                <tbody>
                @forelse($jobs as $job)
                    @php($next = \App\Support\BoardPresenter::nextTask($job))
                    <tr wire:key="job-row-{{ $job->id }}">
                        <td data-label="Select"><input type="checkbox" wire:model.live="selectedJobIds" value="{{ $job->id }}" aria-label="Select {{ $job->job_number }}"></td>
                        <td data-label="Job / Order"><button class="ft-table-job-link" wire:click="openJob({{ $job->id }})">{{ $job->job_number }}</button><div class="ft-table-sub">{{ $job->order_number ?: 'RFQ-'.str_pad((string)$job->id,5,'0',STR_PAD_LEFT) }}</div></td>
                        <td data-label="Client / Brief"><b>{{ $job->client?->name }}</b><div class="ft-table-sub">{{ \Illuminate\Support\Str::limit($job->title, 36) }}</div></td>
                        <td data-label="Product / Qty"><b>{{ $job->product ?: 'Product' }}</b><div class="ft-table-sub">{{ max(1,(int) $job->items_count) }} product · {{ number_format($job->quantity) }} pcs</div></td>
                        <td data-label="Phase"><span class="ft-soft-pill blue">{{ $job->phase?->short_name ?? '—' }}</span></td>
                        <td data-label="Next Action"><b>{{ $next?->title ?? ($job->next_action ?: 'Review client requirement') }}</b><div class="ft-table-due {{ $next?->due_date?->isPast() ? 'overdue' : '' }}">@if($next?->due_date){{ $next->due_date->isPast() ? 'Overdue '.$next->due_date->format('M j') : 'Due '.$next->due_date->format('M j') }}@else — @endif</div></td>
                        <td data-label="Health"><span class="ft-soft-pill {{ \App\Support\JobDetailPresenter::healthClass($job->needs_attention ? 'Needs Attention' : $job->health) }}">{{ $job->needs_attention ? 'Needs Attention' : $job->health }}</span></td>
                        <td data-label="Owner">
                            <div class="ft-owner-chip ft-inline-owner-editor" x-data="{ editing:false }">
                                <x-ui.avatar :user="$job->owner" :name="$job->owner?->name ?? 'Unassigned'" :size="28"/>
                                <span x-show="!editing" class="ft-inline-owner-name">{{ $job->owner?->name ?? 'Unassigned' }}</span>
                                @if(app(\App\Services\AccessControlService::class)->canAssignVisibleJob(auth()->user()))
                                    <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit Job owner" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.ownerSelect.focus())">✎</button>
                                    <select x-ref="ownerSelect" x-show="editing" aria-label="Edit Job owner"
                                        x-on:keydown.escape="editing=false"
                                        x-on:blur="editing=false"
                                        wire:change="updateJobOwner({{ $job->id }}, $event.target.value)" x-on:change="editing=false">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $u)<option value="{{ $u->id }}" @selected((int)$job->owner_id===(int)$u->id)>{{ $u->name }}</option>@endforeach
                                    </select>
                                @endif
                            </div>
                        </td>
                        <td data-label="Delivery">
                            <div class="ft-date-chip ft-inline-date-editor {{ $job->delivery_date?->isPast() && !$job->completed_at ? 'overdue' : '' }}" x-data="{ editing:false }">
                                <span x-show="!editing" class="ft-inline-date-text">{{ $job->delivery_date?->format('M j') ?? 'Set date' }}</span>
                                @if(app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job))
                                    <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit delivery date" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.deliveryDate.showPicker ? $refs.deliveryDate.showPicker() : $refs.deliveryDate.focus())">✎</button>
                                    <input x-ref="deliveryDate" x-show="editing" type="date" value="{{ $job->delivery_date?->format('Y-m-d') }}" aria-label="Edit delivery date"
                                        x-on:keydown.escape="editing=false"
                                        x-on:blur="editing=false"
                                        wire:change="updateJobDeliveryDate({{ $job->id }}, $event.target.value)">
                                @endif
                            </div>
                        </td>
                        <td data-label="Progress"><div class="ft-table-progress"><span style="width:{{ $job->progress }}%"></span></div><small>{{ $job->progress }}%</small></td>
                        <td data-label="Invoice"><span class="ft-soft-pill {{ $job->commercial_value > 0 ? 'blue' : 'amber' }}">{{ $job->commercial_value > 0 ? 'Draft $'.number_format($job->commercial_value,0) : 'Quotation pending' }}</span></td>
                        <td data-label="Actions"><button class="ft-table-kebab" wire:click="openJob({{ $job->id }})">•••</button></td>
                    </tr>
                @empty<tr><td colspan="12"><div class="empty-state">No Jobs match the selected filters.</div></td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="ft-list-pagination"><span>Showing <b>{{ $jobs->firstItem() ?? 0 }}–{{ $jobs->lastItem() ?? 0 }}</b> of {{ $jobs->total() }} jobs</span><div><span>Show</span><select wire:model.live="perPage"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option></select><span>per page</span></div><div class="ft-page-actions"><button wire:click="previousPage" @disabled($jobs->onFirstPage())>Previous</button><span>Page {{ $jobs->currentPage() }} of {{ $jobs->lastPage() }}</span><button wire:click="nextPage" @disabled(!$jobs->hasMorePages())>Next</button></div></div>
</div>
