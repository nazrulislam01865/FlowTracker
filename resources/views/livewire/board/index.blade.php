<div wire:init="loadBoardCards" class="ft-board-page ft-operations-board" x-data="{ draggedTask:null, draggedJob:null, allGroupsOpen:true, phaseClosed:{} }">
    <div class="ft-board-sticky-header">
        <div class="ft-board-page-head">
            <div><h1>Operations Board</h1><p>Track work across all active Jobs</p></div>
            <div class="ft-board-head-actions">@if(auth()->user()->canModule('jobs','create'))<a class="ft-new-job-btn" href="{{ route('jobs.index', ['create'=>1]) }}" wire:navigate>＋ New Job</a>@endif</div>
        </div>

        @if($message)<div class="flash">{{ $message }}</div>@endif

        <div class="ft-board-tabs ft-reference-tabs">
            <button type="button" class="{{ $mode === 'jobs' ? 'active' : '' }}" wire:click="setMode('jobs')">Job Board</button>
            <button type="button" class="{{ $mode === 'tasks' ? 'active' : '' }}" wire:click="setMode('tasks')">Task Board</button>
        </div>

        <section class="ft-board-control-card ft-reference-filter-card ft-list-filter-shell">
            <div class="ft-list-filter-grid">
                <x-ui.list-search property="search" :value="$search" placeholder="Job ID, task, client or assignee…" />
                <x-ui.remote-filter label="Job" property="job" type="jobs" context="board" :value="$job" placeholder="All jobs" :initial-options="$jobFilterOptions" />
                <x-ui.remote-filter label="Client" property="client" type="clients" context="board" :value="$client" placeholder="All clients" :initial-options="$clientFilterOptions" />
                <x-ui.remote-filter label="Assignee" property="assignee" type="users" context="board" :value="$assignee" placeholder="Anyone" :initial-options="$assigneeFilterOptions" />
                <x-ui.select-filter label="Status" property="status" :value="$status" placeholder="All statuses" :options="($mode === 'jobs' ? $jobStatuses : $taskStatuses)->map(fn($value) => ['id' => $value, 'label' => $value])" />
                <x-ui.select-filter label="Due" property="due" :value="$due" placeholder="Any date" :options="collect([['id'=>'overdue','label'=>'Overdue'],['id'=>'today','label'=>'Due today'],['id'=>'week','label'=>'Due this week'],['id'=>'month','label'=>'Next 30 days'],['id'=>'none','label'=>'No due date']])" />
                @if($mode === 'jobs')
                    <x-ui.select-filter label="Workflow" property="workflow" :value="$workflow" placeholder="Select workflow" :clearable="false" :options="$workflows->map(fn($flow) => ['id' => (string)$flow->id, 'label' => $flow->name])" />
                    <x-ui.select-filter label="Sort" property="sort" :value="$sort" placeholder="Delivery date" :clearable="false" :options="collect([['id'=>'delivery','label'=>'Delivery date'],['id'=>'updated','label'=>'Recently updated'],['id'=>'priority','label'=>'Priority']])" />
                @endif
            </div>
            @php
                $chips = collect();
                if($search) $chips->push(['key'=>'search','label'=>'Search: '.$search]);
                if($job) $chips->push(['key'=>'job','label'=>'Job: '.(collect($jobFilterOptions)->firstWhere('id',(int)$job)['label'] ?? 'Selected')]);
                if($client) $chips->push(['key'=>'client','label'=>'Client: '.(collect($clientFilterOptions)->firstWhere('id',(int)$client)['label'] ?? 'Selected')]);
                if($assignee) $chips->push(['key'=>'assignee','label'=>'Assignee: '.(collect($assigneeFilterOptions)->firstWhere('id',(int)$assignee)['label'] ?? 'Selected')]);
                if($status) $chips->push(['key'=>'status','label'=>'Status: '.$status]);
                if($due) $chips->push(['key'=>'due','label'=>'Due: '.(['overdue'=>'Overdue','today'=>'Due today','week'=>'Due this week','month'=>'Next 30 days','none'=>'No due date'][$due] ?? $due)]);
            @endphp
            <div class="ft-list-active-row">
                <div class="ft-list-filter-chips">@forelse($chips as $chip)<span class="ft-list-filter-chip">{{ $chip['label'] }}<button type="button" wire:click="clearFilter('{{ $chip['key'] }}')">×</button></span>@empty<span>No filters applied</span>@endforelse</div>
                <div class="ft-list-filter-actions">
                    @if($chips->isNotEmpty())<button type="button" class="ft-list-clear-all" wire:click="clearFilters">Clear all filters</button>@endif
                    @if($mode === 'jobs')
                        <span class="ft-board-group-controls" aria-label="Job group controls"><button type="button" class="ft-filter-collapse" wire:click="expandVisibleJobs('{{ $jobs->pluck('id')->implode(',') }}')" title="Expand all job cards"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button><button type="button" class="ft-filter-collapse" wire:click="collapseAll" title="Collapse all job cards"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button></span>
                    @else
                        <span class="ft-board-group-controls" aria-label="Task job group controls"><button type="button" class="ft-filter-collapse" wire:click="expandAllTaskGroups" title="Expand all jobs"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button><button type="button" class="ft-filter-collapse" wire:click="collapseAllTaskGroups" title="Collapse all jobs"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button></span>
                    @endif
                </div>
            </div>
        </section>

    </div>

    @if($mode === 'jobs')


        @if(!$cardsReady)
            @include('livewire.shared.board-cards-placeholder', ['columns' => max(3, $phases->count())])
        @else
        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="jobHeaderScroll" x-on:scroll="$refs.jobBodyScroll && ($refs.jobBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-job-board-header-grid">
                    @foreach($phases as $phase)
                        @php($phaseJobs = $jobs->filter(fn($job) => (int)($job->source_workflow_phase_id ?: $job->workflow_phase_id) === (int)$phase->id))
                        @if($hideEmptyPhases && $phaseJobs->isEmpty()) @continue @endif
                        <button type="button" class="ft-board-column-head ft-external-lane-head" x-on:click="phaseClosed[{{ $phase->id }}]=!phaseClosed[{{ $phase->id }}]">
                            <span>{{ strtoupper($phase->short_name) }}</span><b>{{ $phaseJobs->count() }}</b>
                            <svg :class="{'rotated':phaseClosed[{{ $phase->id }}]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,job,client,assignee,status,due,workflow,sort" x-ref="jobBodyScroll" x-on:scroll="$refs.jobHeaderScroll && ($refs.jobHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-job-board-grid ft-job-board-body-grid">
                @foreach($phases as $phase)
                    @php($phaseJobs = $jobs->filter(fn($job) => (int)($job->source_workflow_phase_id ?: $job->workflow_phase_id) === (int)$phase->id))
                    @if($hideEmptyPhases && $phaseJobs->isEmpty()) @continue @endif
                    <section class="ft-board-column ft-job-column ft-board-column-nohead" wire:key="job-phase-{{ $phase->id }}">
                        <button type="button" class="ft-mobile-phase-head" x-on:click="phaseClosed[{{ $phase->id }}]=!phaseClosed[{{ $phase->id }}]">
                            <span>{{ $phase->short_name }}</span><b>{{ $phaseJobs->count() }}</b>
                            <svg :class="{'rotated':phaseClosed[{{ $phase->id }}]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="ft-board-column-list" x-show="!phaseClosed[{{ $phase->id }}]" x-on:dragover.prevent x-on:drop.prevent="if(draggedJob){$wire.moveJob(draggedJob,{{ $phase->id }});draggedJob=null}">
                            @forelse($phaseJobs as $jobRow)
                                @php($canMoveJob = app(\App\Services\AccessControlService::class)->canChangeVisibleJobStatus(auth()->user(), $jobRow))
                                @if($canMoveJob)
                                    <x-board.job-card :job="$jobRow" :expanded="in_array($jobRow->id,$expandedJobs,true)" draggable="true" x-on:dragstart="draggedJob={{ $jobRow->id }}" x-on:dragend="draggedJob=null" wire:key="job-card-{{ $jobRow->id }}" />
                                @else
                                    <x-board.job-card :job="$jobRow" :expanded="in_array($jobRow->id,$expandedJobs,true)" draggable="false" wire:key="job-card-{{ $jobRow->id }}" />
                                @endif
                            @empty
                                <div class="ft-board-empty-column">No Jobs</div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
        @endif
    @else
        @if(!$cardsReady)
            @include('livewire.shared.board-cards-placeholder', ['columns' => max(3, $taskStatuses->count())])
        @else
        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="taskHeaderScroll" x-on:scroll="$refs.taskBodyScroll && ($refs.taskBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-task-board-status-header" style="--ft-lane-count: {{ max(1, $taskStatuses->count()) }};">
                    @foreach($taskStatuses as $taskStatus)
                        <div class="ft-task-status-head"><span>{{ strtoupper($taskStatus) }}</span><b>{{ $tasks->filter(fn($task) => \App\Support\BoardLaneResolver::taskStatusMatches($task->status, $taskStatus))->count() }}</b></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,job,client,assignee,status,due,workflow,sort" x-ref="taskBodyScroll" x-on:scroll="$refs.taskHeaderScroll && ($refs.taskHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <x-board.task-job-matrix :tasks="$tasks" :statuses="$taskStatuses" :draggable="true" :all-groups-expanded="$taskGroupsExpanded" :group-state-key="$taskGroupsExpanded ? 'open' : 'closed'" key-prefix="board" />
        </div>
        @endif
    @endif

    @if($cardsReady && $hasMoreCards)
        <div class="ft-board-load-more">
            <button type="button" class="ft-outline-btn" wire:click="loadMore">Load 60 more</button>
        </div>
    @endif
</div>
