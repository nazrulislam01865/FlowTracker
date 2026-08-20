<div
    id="my-work-app"
    x-data="{ metrics: @js($metrics), groupsExpanded: true }"
    x-on:my-work-metrics.window="metrics = $event.detail"
>


    <div class="page-head">
        <div>
            <h1>My Tasks</h1>
            <p>
                @if($sourceFilter === 'inquiries' && $statusFilter !== '')
                    Inquiry tasks matching the selected dashboard status filter.
                @else
                    {{ $administratorView ? 'All Order tasks, grouped by Order and ranked by what needs action first.' : 'Tasks assigned to you or from Orders you created, grouped by Order and ranked by what needs action first.' }}
                @endif
            </p>
        </div>
    </div>


    @if($sourceFilter === 'inquiries' && $statusFilter !== '')
    <section class="work-view" aria-busy="false">
        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search filtered Inquiry tasks" aria-label="Search filtered Inquiry tasks">
                    @if($search !== '')<button class="clear" type="button" wire:click="clearSearch">Clear</button>@endif
                </label>
                <div class="quick-filters" aria-label="Active dashboard task filter">
                    <span class="chip active">Inquiry tasks</span>
                    <span class="chip active">Status: {{ $statusFilter }}</span>
                </div>
            </div>
            <div class="toolbar-secondary">
                <select class="sort" wire:model.live="sort" aria-label="Sort filtered Inquiry tasks">
                    <option value="action">Sort: Action priority</option>
                    <option value="due">Sort: Due soon</option>
                    <option value="job">Sort: Inquiry number</option>
                </select>
                <button type="button" class="chip clear-filters" wire:click="clearStatusFilter">Clear status filter</button>
            </div>
        </div>

        <div class="load-state">
            <span><strong>{{ $statusFilter }}</strong> Inquiry tasks</span>
            <span class="loading-copy">
                <span wire:loading.delay.long wire:target="search,sort,clearSearch,clearStatusFilter"><i class="spinner"></i> Updating tasks…</span>
            </span>
        </div>

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,sort,clearSearch,clearStatusFilter" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My Inquiry Tasks filtered by status" wire:loading.class="is-refreshing" wire:target="search,sort,clearSearch,clearStatusFilter">
            <div class="task-table-scroll">
                <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>
                <div>
                    @include('livewire.my-work._inquiry-groups', ['inquiryGroups' => $inquiryGroups])
                    @if($inquiryGroups->isEmpty())
                        <div class="empty"><strong>No matching Inquiry tasks</strong>No My Task records currently use the selected status.</div>
                    @endif
                </div>
            </div>
            <footer class="footer">
                <span>{{ $inquiryVisibleTaskCount }} {{ $inquiryVisibleTaskCount === 1 ? 'task' : 'tasks' }} with status “{{ $statusFilter }}”</span>
            </footer>
        </section>
    </section>
    @else

    <section class="work-view" aria-busy="false">
        <div class="metrics ft-summary-card-grid" aria-label="My Task summary filters">
            <x-ui.summary-card label="Created Today" :value="$metrics['createdToday'] ?? 0" value-expression="metrics.createdToday ?? '—'" icon="created" tone="blue" caption="Tasks created today" :active="$quick === 'createdToday'" wire:click="setMetricFilter('createdToday')" aria-pressed="{{ $quick === 'createdToday' ? 'true' : 'false' }}" />
            <x-ui.summary-card label="Not Started" :value="$metrics['notStarted'] ?? 0" value-expression="metrics.notStarted ?? '—'" icon="not-started" tone="slate" caption="Waiting for first action" :active="$quick === 'notStarted'" wire:click="setMetricFilter('notStarted')" aria-pressed="{{ $quick === 'notStarted' ? 'true' : 'false' }}" />
            <x-ui.summary-card label="In Progress" :value="$metrics['inProgress'] ?? 0" value-expression="metrics.inProgress ?? '—'" icon="in-progress" tone="blue" caption="Work currently underway" :active="$quick === 'inProgress'" wire:click="setMetricFilter('inProgress')" aria-pressed="{{ $quick === 'inProgress' ? 'true' : 'false' }}" />
            <x-ui.summary-card label="Due This Week" :value="$metrics['dueThisWeek'] ?? 0" value-expression="metrics.dueThisWeek ?? '—'" icon="due-week" tone="amber" caption="Tasks due this week" :active="$quick === 'dueThisWeek'" wire:click="setMetricFilter('dueThisWeek')" aria-pressed="{{ $quick === 'dueThisWeek' ? 'true' : 'false' }}" />
            <x-ui.summary-card label="Completed This Week" :value="$metrics['completedThisWeek'] ?? 0" value-expression="metrics.completedThisWeek ?? '—'" icon="completed" tone="green" caption="Finished this week" :active="$quick === 'completedThisWeek'" wire:click="setMetricFilter('completedThisWeek')" aria-pressed="{{ $quick === 'completedThisWeek' ? 'true' : 'false' }}" />
            <x-ui.summary-card label="Needs Attention" :value="$metrics['attention'] ?? 0" value-expression="metrics.attention ?? '—'" icon="attention" tone="red" caption="Blocked, overdue or unassigned" :active="$quick === 'attention'" wire:click="setMetricFilter('attention')" aria-pressed="{{ $quick === 'attention' ? 'true' : 'false' }}" />
        </div>

        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search tasks, Orders, clients or flags" aria-label="Search my work">
                    @if($search !== '')<button class="clear" type="button" wire:click="clearSearch">Clear</button>@endif
                </label>
                <div class="phase-filters" aria-label="Filter by Order workflow phase">
                    @foreach($phaseOptions as $phaseOption)
                        <button
                            type="button"
                            class="phase-toggle {{ $phaseFilter === $phaseOption ? 'active' : '' }}"
                            wire:click="setPhaseFilter({{ \Illuminate\Support\Js::from($phaseOption) }})"
                            aria-pressed="{{ $phaseFilter === $phaseOption ? 'true' : 'false' }}"
                            title="{{ $phaseOption }}"
                        >
                            <span class="phase-check" aria-hidden="true">✓</span>
                            <span>{{ $phaseOption }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="toolbar-secondary">
                <div class="quick-filters">
                    <button type="button" class="chip {{ $quick === 'mentions' ? 'active' : '' }}" wire:click="setQuick('{{ $quick === 'mentions' ? 'my_tasks' : 'mentions' }}')">Mentions (<span x-text="metrics.mentions ?? '—'">{{ $metrics['mentions'] ?? '—' }}</span>)</button>
                    @if($statusFilter !== '')
                        <button type="button" class="chip active" wire:click="clearStatusFilter" title="Clear dashboard status filter">Status: {{ $statusFilter }} ×</button>
                    @endif
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
                <button type="button" class="chip clear-filters" wire:click="clearFilters" @disabled($search === '' && $phaseFilter === '' && $statusFilter === '' && $quick === 'my_tasks' && !$hideCompleted)>Clear filters</button>
            </div>
        </div>

        <div class="load-state">
            <span></span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.delay.long wire:target="search,phaseFilter,quick,sort,hideCompleted,setMetricFilter,setPhaseFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Updating tasks…</span>
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

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,phaseFilter,sort,hideCompleted,setMetricFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My Tasks grouped by Order" wire:loading.class="is-refreshing" wire:target="search,phaseFilter,sort,hideCompleted,setMetricFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage">
            <div class="task-table-scroll">
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
                                    x-on:my-work-task-version.stop="if ($event.detail?.version) version = String($event.detail.version)"
                                >
                                    <div class="task-main">
                                        <a class="task-link" href="{{ $task['route'] }}" wire:navigate>{{ $task['title'] }}</a>
                                        <span class="task-ref">{{ $task['number'] }}</span>
                                    </div>
                                    <span class="phase ft-phase-color-label" data-label="Phase" style="{{ \App\Support\MasterColor::style($task['phaseColor'] ?? null) }}">{{ $task['phase'] }}</span>
                                    <div
                                        class="assignee assignee-editor ft-inline-edit-shell"
                                        wire:key="my-work-task-{{ $task['id'] }}-assignee-{{ $task['assigneeId'] ?: 0 }}"
                                        data-label="Assignee"
                                        title="{{ $task['assignee'] }}"
                                        x-data="window.FlowTrackInlineEdit({ key: @js('my-work-task-'.$task['id'].'-assignee'), label: 'task assignee', value: @js($task['assigneeId'] ?? ''), display: @js($task['assignee']), avatarUrl: @js($task['assigneeAvatar'] ?? '') })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        x-on:click.outside="if (editing) cancelEdit()"
                                        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssignee({{ $task['id'] }}, draftValue, version), { avatarUrl: String($event.detail?.avatarUrl ?? '') }).then(async (ok) => { if (!ok) return; if (lastResponse?.version) $dispatch('my-work-task-version', { version: lastResponse.version }); if (lastResponse?.refresh) await $wire.$refresh(); })"
                                    >
                                        <div class="assignee-display" x-show="!editing">
                                            <span class="assignee-avatar"><x-ui.inline-live-avatar :size="22" /></span>
                                            <span class="assignee-name" x-text="display">{{ $task['assignee'] }}</span>
                                            @if($task['canAssign'])
                                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact assignee-edit-button" title="Edit assignee" aria-label="Edit assignee for {{ $task['title'] }}" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                            @endif
                                        </div>
                                        @if($task['canAssign'])
                                            <div x-cloak x-show="editing" class="assignee-picker">
                                                <x-ui.inline-remote-user
                                                    :value="$task['assigneeId'] ?? ''"
                                                    :selected-label="$task['assignee']"
                                                    parent-type="job"
                                                    :parent-id="$group['id']"
                                                    trigger-class="assignee-picker-trigger"
                                                    variant="compact"
                                                    :menu-width="280"
                                                />
                                            </div>
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>
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
                                            @if(!in_array($task['status'], $statusOptions, true))<option value="{{ $task['status'] }}" data-color="{{ app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status']) }}" selected>{{ $task['status'] }}</option>@endif
                                            @foreach($statusOptions as $statusOption)<option value="{{ $statusOption }}" data-color="{{ app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption) }}" @selected($statusOption === $task['status'])>{{ $statusOption }}</option>@endforeach
                                        </select>
                                    </span>
                                    <span class="flag {{ $task['flagColor'] ? 'ft-master-color' : $task['flagTone'] }}" style="{{ \App\Support\MasterColor::style($task['flagColor']) }}" data-label="Flag">{{ $task['flag'] }}</span>
                                    <span class="updated" data-label="Updated">{{ $task['updated'] }}</span>
                                    <a class="row-action" href="{{ $task['route'] }}" wire:navigate><span class="row-action-desktop">Open</span><span class="row-action-mobile">Details</span><span class="row-action-arrow" aria-hidden="true">→</span></a>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach

                @if($workGroups->isEmpty())
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, or flag.</div>
                @endif
                </div>
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
    @endif

</div>
