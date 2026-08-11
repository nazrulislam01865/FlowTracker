<div
    id="my-work-app"
    x-data="{ metrics: @js($metrics), groupsExpanded: true }"
    x-on:my-work-metrics.window="metrics = $event.detail"
>


    <div class="page-head">
        <div>
            <h1>My Work</h1>
            <p>Your assigned tasks, grouped by Order and ranked by what needs action first.</p>
        </div>
        <a class="row-action" style="width:auto;padding:0 10px" href="{{ route('all-tasks') }}" wire:navigate>All Tasks</a>
    </div>


    <section class="work-view" aria-busy="false">
        <div class="metrics" aria-label="Personal work summary">
            <button type="button" class="metric amber {{ $quick === 'attention' ? 'active' : '' }}" wire:click="setQuick('attention')"><span><small>Needs my action</small><strong x-text="metrics.attention ?? '—'">{{ $metrics['attention'] ?? '—' }}</strong></span><i>⚑</i></button>
            <button type="button" class="metric red {{ $quick === 'overdue' ? 'active' : '' }}" wire:click="setQuick('overdue')"><span><small>Overdue</small><strong x-text="metrics.overdue ?? '—'">{{ $metrics['overdue'] ?? '—' }}</strong></span><i>!</i></button>
            <button type="button" class="metric amber {{ $quick === 'today' ? 'active' : '' }}" wire:click="setQuick('today')"><span><small>Due today</small><strong x-text="metrics.today ?? '—'">{{ $metrics['today'] ?? '—' }}</strong></span><i>◷</i></button>
            <button type="button" class="metric {{ $quick === 'upcoming' ? 'active' : '' }}" wire:click="setQuick('upcoming')"><span><small>Upcoming</small><strong x-text="metrics.upcoming ?? '—'">{{ $metrics['upcoming'] ?? '—' }}</strong></span><i>→</i></button>
            <button type="button" class="metric {{ $quick === 'waiting' ? 'active' : '' }}" wire:click="setQuick('waiting')"><span><small>Waiting</small><strong x-text="metrics.waiting ?? '—'">{{ $metrics['waiting'] ?? '—' }}</strong></span><i>⌛</i></button>
        </div>

        <div class="toolbar">
            <label class="search-wrap">
                <span class="search-icon">⌕</span>
                <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search my tasks, Orders, clients or flags" aria-label="Search my work">
                @if($search !== '')<button class="clear" type="button" wire:click="clearSearch">Clear</button>@endif
            </label>
            <div class="quick-filters">
                <button type="button" class="chip {{ $quick === 'attention' ? 'active' : '' }}" wire:click="setQuick('attention')">Needs action</button>
                <button type="button" class="chip {{ $quick === 'all' ? 'active' : '' }}" wire:click="setQuick('all')">All my tasks</button>
                <button type="button" class="chip {{ $quick === 'mentions' ? 'active' : '' }}" wire:click="setQuick('mentions')">Mentions (<span x-text="metrics.mentions ?? '—'">{{ $metrics['mentions'] ?? '—' }}</span>)</button>
            </div>
            <label class="completed-toggle {{ $hideCompleted ? 'active' : '' }}">
                <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed tasks">
                <span class="completed-check" aria-hidden="true">✓</span>
                <span>Hide completed</span>
            </label>
            <select class="sort" wire:model.live="sort" aria-label="Sort work">
                <option value="action">Sort: Action priority</option>
                <option value="due">Sort: Due soon</option>
                <option value="job">Sort: Order number</option>
            </select>
        </div>

        <div class="load-state">
            <span>
                @if($searchNeedsMoreCharacters)
                    Type 3 characters to search broadly. Order and task reference prefixes can be searched sooner.
                @elseif($workPaginator->total())
                    Showing {{ $workGroups->count() }} of {{ $workPaginator->total() }} matching Orders · {{ $visibleTaskCount }} visible tasks
                @else
                    Showing personal work only
                @endif
            </span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.remove wire:target="search,quick,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage">Results update after 650 ms</span>
                    <span wire:loading.delay.long wire:target="search,quick,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Searching all visible work…</span>
                </span>
                <span class="group-controls" aria-label="Order group controls">
                    <button type="button" class="group-control" x-on:click="groupsExpanded = true" title="Expand all Orders" aria-label="Expand all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 6 5 5 5-5M5 11l5 5 5-5"/></svg>
                    </button>
                    <button type="button" class="group-control" x-on:click="groupsExpanded = false" title="Collapse all Orders" aria-label="Collapse all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 14 5-5 5 5M5 9l5-5 5 5"/></svg>
                    </button>
                </span>
            </span>
        </div>

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My tasks grouped by Order" wire:loading.class="is-refreshing" wire:target="search,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage">
            <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>

            <div>
                @foreach($workGroups as $group)
                    <article class="order-group" wire:key="my-work-order-{{ $group['id'] }}" x-data="{ open: true }" x-effect="open = groupsExpanded">
                        <header class="order-head">
                            <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Collapse {{ $group['number'] }}"><span x-text="open ? '⌄' : '›'">⌄</span></button>
                            <span class="order-identity">
                                @if($group['route'])<a class="order-id" href="{{ $group['route'] }}" wire:navigate>{{ $group['number'] }}</a>@else<span class="order-id">{{ $group['number'] }}</span>@endif
                                <span class="order-title">{{ $group['title'] }}</span>
                            </span>
                            <span class="order-client">{{ $group['client'] }}</span>
                            <span class="order-stage">{{ $group['stage'] }}</span>
                            <span class="health {{ $group['healthTone'] }}">{{ $group['health'] }}</span>
                            <span class="order-progress"><i class="progress-track"><i style="width:{{ $group['progress'] }}%"></i></i>{{ $group['progress'] }}%</span>
                            <span class="task-count">{{ $group['taskCount'] }} {{ $group['taskCount'] === 1 ? 'task' : 'tasks' }}</span>
                        </header>

                        <div class="task-rows" x-show="open">
                            @foreach($group['tasks'] as $task)
                                <div
                                    class="task-row"
                                    wire:key="my-work-task-{{ $task['id'] }}"
                                    x-data="{
                                        saving:false,
                                        version:@js($task['version']),
                                        currentStatus:@js($task['status']),
                                        async saveStatus(event){
                                            const select=event.currentTarget;
                                            const previous=this.currentStatus;
                                            const next=select.value;
                                            if(next===previous||this.saving)return;
                                            this.saving=true;
                                            select.disabled=true;
                                            try{
                                                const result=await $wire.updateTaskStatus({{ $task['id'] }},next,this.version);
                                                if(!result?.ok){select.value=previous;window.FlowTrackMasterColor?.applySelect(select);return;}
                                                this.currentStatus=result.status||next;
                                                this.version=result.version||this.version;
                                                // Keep the renderless status update, but re-query once when
                                                // completion changes list membership. This removes the task now,
                                                // and removes its Order group too if it was the final visible task.
                                                if(result.completed && @js($hideCompleted))await $wire.$refresh();
                                            }catch(error){select.value=previous;window.FlowTrackMasterColor?.applySelect(select);}
                                            finally{this.saving=false;select.disabled=false;}
                                        }
                                    }"
                                    x-bind:class="{ 'saving': saving }"
                                >
                                    <div class="task-main">
                                        <a class="task-link" href="{{ $task['route'] }}" wire:navigate>{{ $task['title'] }}</a>
                                        <span class="task-ref">{{ $task['number'] }}</span>
                                    </div>
                                    <span class="phase" data-label="Phase">{{ $task['phase'] }}</span>
                                    <span class="assignee" data-label="Assignee" title="{{ $task['assignee'] }}">
                                        <x-ui.avatar :name="$task['assignee']" :src="$task['assigneeAvatar']" :size="22" />
                                        <span class="assignee-name">{{ $task['assignee'] }}</span>
                                    </span>
                                    <span
                                        class="due-editor ft-inline-edit-shell {{ $task['dueTone'] }}" data-label="Due"
                                        x-data="window.FlowTrackInlineEdit({ key: @js('my-work-task-'.$task['id'].'-due-date'), label: 'task due date', value: @js($task['dueValue']), display: @js($task['dueDisplay']) })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    >
                                        <span x-show="!editing" x-text="display" class="ft-task-inline-display">{{ $task['dueDisplay'] }}</span>
                                        @if($task['canEdit'])
                                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact" title="Edit due date" aria-label="Edit due date for {{ $task['title'] }}" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.myWorkDue.showPicker ? $refs.myWorkDue.showPicker() : $refs.myWorkDue.focus())">✎</button>
                                            <input x-ref="myWorkDue" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input" type="date"
                                                x-on:keydown.escape.prevent="cancelEdit()"
                                                x-on:blur="if (editing) cancelEdit()"
                                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDate({{ $task['id'] }}, draftValue))">
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </span>
                                    <span class="status-wrap" data-label="Status">
                                        <select data-master-color-select class="status-select {{ $task['statusColor'] ? 'ft-master-color' : '' }}" style="{{ \App\Support\MasterColor::style($task['statusColor']) }}" @if($task['canEdit']) x-on:change="saveStatus($event); window.FlowTrackMasterColor?.applySelect($event.currentTarget)" @else disabled @endif aria-label="Status for {{ $task['title'] }}">
                                            @if(!in_array($task['status'], $statusOptions, true))<option value="{{ $task['status'] }}" data-color="{{ app(\App\Services\MasterDataService::class)->colorFor('task_status', $task['status']) }}" selected>{{ $task['status'] }}</option>@endif
                                            @foreach($statusOptions as $statusOption)<option value="{{ $statusOption }}" data-color="{{ app(\App\Services\MasterDataService::class)->colorFor('task_status', $statusOption) }}" @selected($statusOption === $task['status'])>{{ $statusOption }}</option>@endforeach
                                        </select>
                                    </span>
                                    <span class="flag {{ $task['flagColor'] ? 'ft-master-color' : $task['flagTone'] }}" style="{{ \App\Support\MasterColor::style($task['flagColor']) }}" data-label="Flag">{{ $task['flag'] }}</span>
                                    <span class="updated" data-label="Updated">{{ $task['updated'] }}</span>
                                    <a class="row-action" href="{{ $task['route'] }}" wire:navigate>Open</a>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach

                @if($workGroups->isEmpty())
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, or flag.</div>
                @endif
            </div>

            <footer class="footer">
                <span>
                    @if($workPaginator->total())
                        Orders {{ $workPaginator->firstItem() }}–{{ $workPaginator->lastItem() }} of {{ $workPaginator->total() }} · {{ $visibleTaskCount }} tasks on this page
                    @else
                        My Orders and tasks
                    @endif
                </span>
                @php
                    $currentPage = $workPaginator->currentPage();
                    $lastPage = max(1, $workPaginator->lastPage());
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                @endphp
                <nav class="pages" aria-label="Pagination">
                    <button type="button" class="page-button" wire:click="previousPage('workPage')" @disabled($workPaginator->onFirstPage())>Previous</button>
                    @for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++)
                        <button type="button" class="page-button {{ $pageNumber === $currentPage ? 'active' : '' }}" wire:click="gotoPage({{ $pageNumber }}, 'workPage')" @if($pageNumber === $currentPage) aria-current="page" @endif>{{ $pageNumber }}</button>
                    @endfor
                    <button type="button" class="page-button" wire:click="nextPage('workPage')" @disabled(!$workPaginator->hasMorePages())>Next</button>
                </nav>
            </footer>
        </section>
    </section>

</div>
