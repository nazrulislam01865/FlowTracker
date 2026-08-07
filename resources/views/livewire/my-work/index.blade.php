<div wire:init="loadMyWorkTasks" class="ft-board-page ft-my-work-page" x-data="{ allGroupsOpen:true, draggedTask:null }">
    <div class="ft-board-sticky-header ft-mywork-sticky-header">
        <div class="ft-board-page-head">
            <div><h1>My Work</h1><p>All visible jobs and tasks across the workspace</p></div>
            <div class="ft-board-head-actions"><a class="ft-new-job-btn" href="{{ route('jobs.index') }}" wire:navigate>Jobs</a></div>
        </div>

        <section class="ft-board-control-card ft-task-controls ft-reference-filter-card ft-list-filter-shell">
            <div class="ft-list-filter-grid">
                <x-ui.list-search property="search" :value="$search" placeholder="Jobs, tasks, clients or assignees…" />
                <x-ui.remote-filter label="Job" property="job" type="jobs" context="my-work" :value="$job" placeholder="All jobs" :initial-options="$jobFilterOptions" />
                <x-ui.remote-filter label="Client" property="client" type="clients" context="my-work" :value="$client" placeholder="All clients" :initial-options="$clientFilterOptions" />
                <x-ui.remote-filter label="Assignee" property="assignee" type="users" context="my-work" :value="$assignee" placeholder="Anyone" :initial-options="$assigneeFilterOptions" />
                <x-ui.remote-filter label="Status" property="status" type="task-statuses" context="my-work" :value="$status" placeholder="All statuses" :initial-options="$statusFilterOptions" />
                <x-ui.remote-filter label="Priority" property="priority" type="priorities" context="my-work" :value="$priority" placeholder="All priorities" :initial-options="$priorityFilterOptions" />
                <x-ui.select-filter label="Due" property="due" :value="$due" placeholder="Any date" :options="collect([['id'=>'overdue','label'=>'Overdue'],['id'=>'today','label'=>'Due today'],['id'=>'week','label'=>'Due this week'],['id'=>'month','label'=>'Next 30 days'],['id'=>'none','label'=>'No due date']])" />
            </div>
            @php
                $chips = collect();
                if($search) $chips->push(['key'=>'search','label'=>'Search: '.$search]);
                if($job) $chips->push(['key'=>'job','label'=>'Job: '.(collect($jobFilterOptions)->firstWhere('id',(int)$job)['label'] ?? 'Selected')]);
                if($client) $chips->push(['key'=>'client','label'=>'Client: '.(collect($clientFilterOptions)->firstWhere('id',(int)$client)['label'] ?? 'Selected')]);
                if($assignee) $chips->push(['key'=>'assignee','label'=>'Assignee: '.(collect($assigneeFilterOptions)->firstWhere('id',(int)$assignee)['label'] ?? 'Selected')]);
                if($status) $chips->push(['key'=>'status','label'=>'Status: '.$status]);
                if($priority) $chips->push(['key'=>'priority','label'=>'Priority: '.$priority]);
                if($due) $chips->push(['key'=>'due','label'=>'Due: '.(['overdue'=>'Overdue','today'=>'Due today','week'=>'Due this week','month'=>'Next 30 days','none'=>'No due date'][$due] ?? $due)]);
            @endphp
            <div class="ft-list-active-row">
                <div class="ft-list-filter-chips">@forelse($chips as $chip)<span class="ft-list-filter-chip">{{ $chip['label'] }}<button type="button" wire:click="clearFilter('{{ $chip['key'] }}')">×</button></span>@empty<span>No filters applied</span>@endforelse</div>
                <div class="ft-list-filter-actions">
                    @if($chips->isNotEmpty())<button type="button" class="ft-list-clear-all" wire:click="clearFilters">Clear all filters</button>@endif
                    <span class="ft-board-group-controls" aria-label="Job group controls">
                        <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=true; $dispatch('board-expand-all')" title="Expand all jobs" aria-label="Expand all jobs"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button>
                        <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=false; $dispatch('board-collapse-all')" title="Collapse all jobs" aria-label="Collapse all jobs"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button>
                    </span>
                </div>
            </div>
        </section>
    </div>

    @php
        $displayStatuses = $taskStatuses->filter(fn($value) => $status !== '' && \App\Support\BoardLaneResolver::isCompleted($status) ? \App\Support\BoardLaneResolver::isCompleted($value) : ! \App\Support\BoardLaneResolver::isCompleted($value))->values();
    @endphp
    <div class="ft-lane-sticky-header">
        <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="myWorkHeaderScroll" x-on:scroll="$refs.myWorkBodyScroll && ($refs.myWorkBodyScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-task-board-status-header" style="--ft-lane-count: {{ max(1, $displayStatuses->count()) }};">
                @foreach($displayStatuses as $workStatus)
                    <div class="ft-task-status-head"><span>{{ strtoupper($workStatus) }}</span><b>{{ $tasksReady ? $tasks->filter(fn($task) => \App\Support\BoardLaneResolver::taskStatusMatches($task->status, $workStatus))->count() : '…' }}</b></div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,job,client,assignee,status,priority,due" x-ref="myWorkBodyScroll" x-on:scroll="$refs.myWorkHeaderScroll && ($refs.myWorkHeaderScroll.scrollLeft = $event.target.scrollLeft)">
        @if($tasksReady)
            <x-board.task-job-matrix :tasks="$tasks" :statuses="$displayStatuses" :draggable="true" key-prefix="mywork" />
        @else
            @include('livewire.shared.board-cards-placeholder', ['columns' => max(3, $displayStatuses->count())])
        @endif
    </div>
    @if($tasksReady && $hasMoreCards)
        <div class="ft-board-load-more">
            <button type="button" class="ft-outline-btn" wire:click="loadMore">Load 60 more</button>
        </div>
    @endif
</div>
