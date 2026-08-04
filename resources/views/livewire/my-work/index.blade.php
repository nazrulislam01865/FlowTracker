<div class="ft-board-page ft-my-work-page" x-data="{ allGroupsOpen:true }">
    <div class="ft-board-sticky-header ft-mywork-sticky-header">
        <div class="ft-board-page-head">
            <div><h1>My Work</h1><p>All visible jobs and tasks across the workspace</p></div>
            <div class="ft-board-head-actions"><a class="ft-new-job-btn" href="{{ route('jobs.index') }}" wire:navigate>Jobs</a></div>
        </div>

        <section class="ft-board-control-card ft-task-controls ft-reference-filter-card">
            <div class="ft-mywork-filter-grid">
                <label class="ft-filter-search ft-mywork-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input wire:model.live.debounce.300ms="search" placeholder="Search jobs, tasks, clients or assignees"></label>
                <select wire:model.live="job"><option value="">Job</option>@foreach($taskJobs as $row)<option value="{{ $row->id }}">{{ $row->job_number }} — {{ $row->title }}</option>@endforeach</select>
                <select wire:model.live="client"><option value="">Client</option>@foreach($clients as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach</select>
                <select wire:model.live="assignee"><option value="">Assignee</option>@foreach($users as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach</select>
                <select wire:model.live="status"><option value="">Status</option>@foreach($taskStatuses as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach</select>
                <select wire:model.live="priority"><option value="">Priority</option>@foreach($priorities as $row)<option value="{{ $row->name }}">{{ $row->name }}</option>@endforeach</select>
                <select wire:model.live="due"><option value="">Due date</option><option value="overdue">Overdue</option><option value="today">Due today</option><option value="week">Due this week</option><option value="month">Next 30 days</option><option value="none">No due date</option></select>
                <button type="button" class="ft-clear-wide" wire:click="clearFilters">Clear</button>
            </div>
            <div class="ft-board-quick-row">
                <span class="ft-quick-label">Quick filters</span>
                <button class="ft-quick-chip {{ $quick==='open'?'active':'' }}" wire:click="setQuick('open')">Open <b>{{ $counts['open'] }}</b></button>
                <button class="ft-quick-chip red {{ $quick==='overdue'?'active':'' }}" wire:click="setQuick('overdue')">Overdue <b>{{ $counts['overdue'] }}</b></button>
                <button class="ft-quick-chip {{ $quick==='week'?'active':'' }}" wire:click="setQuick('week')">Due this week <b>{{ $counts['week'] }}</b></button>
                <button class="ft-quick-chip red {{ $quick==='blocked'?'active':'' }}" wire:click="setQuick('blocked')">Blocked <b>{{ $counts['blocked'] }}</b></button>
                <button class="ft-quick-chip {{ $quick==='waiting'?'active':'' }}" wire:click="setQuick('waiting')">Waiting external <b>{{ $counts['waiting'] }}</b></button>
                <button class="ft-quick-chip {{ $quick==='completed'?'active':'' }}" wire:click="setQuick('completed')">Completed <b>{{ $counts['completed'] }}</b></button>
                <button type="button" class="ft-filter-collapse" x-on:click="allGroupsOpen=!allGroupsOpen; $dispatch(allGroupsOpen ? 'board-expand-all' : 'board-collapse-all')" :title="allGroupsOpen ? 'Collapse all jobs' : 'Expand all jobs'">
                    <svg :class="{'rotated':!allGroupsOpen}" viewBox="0 0 24 24"><path d="m6 15 6-6 6 6"/></svg>
                </button>
            </div>
        </section>
    </div>

    @php($displayStatuses = $taskStatuses->filter(fn($value) => $quick === 'completed' ? $value === 'Completed' : $value !== 'Completed')->values())
    <div class="ft-lane-sticky-header">
        <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="myWorkHeaderScroll" x-on:scroll="$refs.myWorkBodyScroll && ($refs.myWorkBodyScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-task-board-status-header" style="--ft-lane-count: {{ max(1, $displayStatuses->count()) }};">
                @foreach($displayStatuses as $workStatus)
                    <div class="ft-task-status-head"><span>{{ strtoupper($workStatus) }}</span><b>{{ $tasks->where('status',$workStatus)->count() }}</b></div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll" x-ref="myWorkBodyScroll" x-on:scroll="$refs.myWorkHeaderScroll && ($refs.myWorkHeaderScroll.scrollLeft = $event.target.scrollLeft)">
        <x-board.task-job-matrix :tasks="$tasks" :statuses="$displayStatuses" key-prefix="mywork" />
    </div>
</div>
