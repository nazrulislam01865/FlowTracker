<div class="ft-board-page ft-operations-board" x-data="{ draggedTask:null, draggedJob:null, allGroupsOpen:true, phaseClosed:{} }">
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

        <section class="ft-board-control-card ft-reference-filter-card">
            <div class="ft-board-reference-filter-grid">
                <label class="ft-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input wire:model.live.debounce.300ms="search" placeholder="Search job ID, task or client"></label>
                <select wire:model.live="job"><option value="">Job</option>@foreach($taskJobs as $row)<option value="{{ $row->id }}">{{ $row->job_number }} — {{ $row->title }}</option>@endforeach</select>
                <select wire:model.live="client"><option value="">Client</option>@foreach($clients as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach</select>
                <select wire:model.live="assignee"><option value="">Assignee</option>@foreach($users as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach</select>
                @if($mode === 'jobs')
                    <select wire:model.live="status"><option value="">Status</option>@foreach($jobStatuses as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach</select>
                @else
                    <select wire:model.live="status"><option value="">Status</option>@foreach($taskStatuses as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach</select>
                @endif
                <select wire:model.live="due"><option value="">Due date</option><option value="overdue">Overdue</option><option value="today">Due today</option><option value="week">Due this week</option><option value="month">Next 30 days</option><option value="none">No due date</option></select>
                <button type="button" class="ft-clear-wide" wire:click="clearFilters">Clear</button>
            </div>

            <div class="ft-board-quick-row">
                <span class="ft-quick-label">Quick filters</span>
                @if($mode === 'jobs')
                    <button class="ft-quick-chip {{ $quick==='mine'?'active':'' }}" wire:click="setQuick('mine')">My job <b>{{ $jobCounts['mine'] }}</b></button>
                    <button class="ft-quick-chip red {{ $quick==='overdue'?'active':'' }}" wire:click="setQuick('overdue')">Overdue <b>{{ $jobCounts['overdue'] }}</b></button>
                    <button class="ft-quick-chip {{ $quick==='week'?'active':'' }}" wire:click="setQuick('week')">Due this week <b>{{ $jobCounts['week'] }}</b></button>
                    <button class="ft-quick-chip red {{ $quick==='blocked'?'active':'' }}" wire:click="setQuick('blocked')">Blocked <b>{{ $jobCounts['blocked'] }}</b></button>
                    <button class="ft-quick-chip {{ $quick==='waiting'?'active':'' }}" wire:click="setQuick('waiting')">Waiting external <b>{{ $jobCounts['waiting'] }}</b></button>
                    <button class="ft-quick-chip amber {{ $quick==='unassigned'?'active':'' }}" wire:click="setQuick('unassigned')">Unassigned <b>{{ $jobCounts['unassigned'] }}</b></button>
                    <button type="button" class="ft-filter-collapse" wire:click="{{ count($expandedJobs) ? 'collapseAll' : 'expandAll' }}" title="{{ count($expandedJobs) ? 'Collapse all job cards' : 'Expand all job cards' }}">
                        <svg class="{{ count($expandedJobs) ? '' : 'rotated' }}" viewBox="0 0 24 24"><path d="m6 15 6-6 6 6"/></svg>
                    </button>
                @else
                    <button class="ft-quick-chip {{ $quick==='mine'?'active':'' }}" wire:click="setQuick('mine')">My task <b>{{ $taskCounts['mine'] }}</b></button>
                    <button class="ft-quick-chip red {{ $quick==='overdue'?'active':'' }}" wire:click="setQuick('overdue')">Overdue <b>{{ $taskCounts['overdue'] }}</b></button>
                    <button class="ft-quick-chip {{ $quick==='week'?'active':'' }}" wire:click="setQuick('week')">Due this week <b>{{ $taskCounts['week'] }}</b></button>
                    <button class="ft-quick-chip red {{ $quick==='blocked'?'active':'' }}" wire:click="setQuick('blocked')">Blocked <b>{{ $taskCounts['blocked'] }}</b></button>
                    <button class="ft-quick-chip {{ $quick==='waiting'?'active':'' }}" wire:click="setQuick('waiting')">Waiting external <b>{{ $taskCounts['waiting'] }}</b></button>
                    <button class="ft-quick-chip amber {{ $quick==='unassigned'?'active':'' }}" wire:click="setQuick('unassigned')">Unassigned <b>{{ $taskCounts['unassigned'] }}</b></button>
                    <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=!allGroupsOpen; $dispatch(allGroupsOpen ? 'board-expand-all' : 'board-collapse-all')" :title="allGroupsOpen ? 'Collapse all jobs' : 'Expand all jobs'">
                        <svg :class="{'rotated':!allGroupsOpen}" viewBox="0 0 24 24"><path d="m6 15 6-6 6 6"/></svg>
                    </button>
                @endif
            </div>
        </section>

        <div class="ft-board-summary-row ft-reference-summary">
            @if($mode === 'jobs')
                <span>Showing <b>{{ $jobs->count() }}</b> Jobs across <b>{{ $jobs->pluck('workflow_id')->unique()->count() }}</b> {{ \Illuminate\Support\Str::plural('workflow', $jobs->pluck('workflow_id')->unique()->count()) }}</span>
            @else
                <span>Showing <b>{{ $tasks->count() }}</b> of <b>{{ $taskCounts['open'] + $taskCounts['completed'] }}</b> tasks across <b>{{ $tasks->pluck('flow_job_id')->unique()->count() }}</b> {{ \Illuminate\Support\Str::plural('job', $tasks->pluck('flow_job_id')->unique()->count()) }}</span>
            @endif
        </div>
    </div>

    @if($mode === 'jobs')
        <section class="ft-workflow-reference-card">
            <label>Workflow</label>
            <select wire:model.live="workflow">@foreach($workflows as $flow)<option value="{{ $flow->id }}">{{ $flow->name }}</option>@endforeach</select>
            <p>Job cards show current phase progress, next action and expandable phase tasks.</p>
        </section>

        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="jobHeaderScroll" x-on:scroll="$refs.jobBodyScroll && ($refs.jobBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-job-board-header-grid">
                    @foreach($phases as $phase)
                        @php($phaseJobs = $jobs->where('workflow_phase_id', $phase->id))
                        @if($hideEmptyPhases && $phaseJobs->isEmpty()) @continue @endif
                        <button type="button" class="ft-board-column-head ft-external-lane-head" x-on:click="phaseClosed[{{ $phase->id }}]=!phaseClosed[{{ $phase->id }}]">
                            <span>{{ strtoupper($phase->short_name) }}</span><b>{{ $phaseJobs->count() }}</b>
                            <svg :class="{'rotated':phaseClosed[{{ $phase->id }}]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll" x-ref="jobBodyScroll" x-on:scroll="$refs.jobHeaderScroll && ($refs.jobHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-job-board-grid ft-job-board-body-grid">
                @foreach($phases as $phase)
                    @php($phaseJobs = $jobs->where('workflow_phase_id', $phase->id))
                    @if($hideEmptyPhases && $phaseJobs->isEmpty()) @continue @endif
                    <section class="ft-board-column ft-job-column ft-board-column-nohead" wire:key="job-phase-{{ $phase->id }}">
                        <div class="ft-board-column-list" x-show="!phaseClosed[{{ $phase->id }}]" x-on:dragover.prevent x-on:drop.prevent="if(draggedJob){$wire.moveJob(draggedJob,{{ $phase->id }});draggedJob=null}">
                            @forelse($phaseJobs as $jobRow)
                                <x-board.job-card :job="$jobRow" :expanded="in_array($jobRow->id,$expandedJobs,true)" draggable="true" x-on:dragstart="draggedJob={{ $jobRow->id }}" wire:key="job-card-{{ $jobRow->id }}" />
                            @empty
                                <div class="ft-board-empty-column">No Jobs</div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    @else
        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="taskHeaderScroll" x-on:scroll="$refs.taskBodyScroll && ($refs.taskBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-task-board-status-header" style="--ft-lane-count: {{ max(1, $taskStatuses->count()) }};">
                    @foreach($taskStatuses as $taskStatus)
                        <div class="ft-task-status-head"><span>{{ strtoupper($taskStatus) }}</span><b>{{ $tasks->where('status',$taskStatus)->count() }}</b></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll" x-ref="taskBodyScroll" x-on:scroll="$refs.taskHeaderScroll && ($refs.taskHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <x-board.task-job-matrix :tasks="$tasks" :statuses="$taskStatuses" :draggable="true" key-prefix="board" />
        </div>
    @endif
</div>
