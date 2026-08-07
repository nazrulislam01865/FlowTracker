@props(['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'mentionUsers'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'jobTaskSearch'=>'','activityTab'=>'all','activityPage'=>1])
@php
    $productRows = \App\Support\JobDetailPresenter::products($job);
    $nextTask = \App\Support\JobDetailPresenter::nextTask($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditJob = $accessControl->canEditVisibleJob(auth()->user(), $job);
    $canAssignJob = $accessControl->canAssignVisibleJob(auth()->user());
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $configuredTasks = $job->workflow->phases->flatMap(fn($phase) => \App\Support\JobDetailPresenter::phaseTasks($job,$phase))->values();
@endphp
<div class="ft-job-overview-section ft-exact-overview">
    <div class="ft-overview-metrics">
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">▣</span><div><small>Current phase</small><b>{{ $job->phase?->name }} · Phase {{ $job->phase?->sequence }} of {{ $job->workflow->phases->count() }}</b><p>{{ $currentTasks->count() }} tasks · {{ $done }} of {{ $currentTasks->count() }} complete</p></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">↗</span><div><small>Overall progress</small><b>{{ $job->progress }}%</b><div class="ft-line-progress"><span style="width:{{ $job->progress }}%"></span></div></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">⌘</span><div><small>Next required action</small><b>{{ $nextTask?->title ?? ($job->next_action ?: 'Review client requirement') }}</b><p>{{ $nextTask?->assignee?->name ?? $job->coordinator?->name ?? 'Unassigned' }}</p></div></div>
    </div>

    <div class="ft-overview-top-grid">
        <section class="ft-detail-card ft-overview-card">
            <h2>Job overview</h2>
            <div
                class="ft-editable-copy ft-editable-description ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-description'), label: 'Job description', value: @js($job->description ?? ''), display: @js($job->description ?: 'No job description recorded.') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <div class="ft-edit-display-row" x-show="!editing">
                    <span>
                        <span x-show="String(value) === String(serverValue)">@if($job->description)<x-ui.mention-text :text="$job->description" />@else No job description recorded. @endif</span>
                        <span x-cloak x-show="String(value) !== String(serverValue)" x-text="display"></span>
                    </span>
                    @if($canEditJob)
                        <button type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit job description" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.descriptionEditor.focus())">✎</button>
                    @endif
                </div>
                @if($canEditJob)
                    <div x-cloak x-show="editing" class="ft-inline-description-editor">
                        <textarea x-ref="descriptionEditor" x-model="draftValue" rows="3" class="ft-mention-input" autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}" x-on:keydown.escape.prevent="cancelEdit()"></textarea>
                        <div>
                            <button type="button" class="ft-outline-btn" x-on:click="cancelEdit()">Cancel</button>
                            <button type="button" class="ft-new-job-btn" x-on:click="commit(draftValue.trim(), draftValue.trim() || 'No job description recorded.', () => $wire.updateJobTextField({{ $job->id }}, 'description', draftValue))">Save</button>
                        </div>
                    </div>
                    <x-ui.inline-save-state />
                @endif
            </div>
            <div class="ft-card-section-head"><b>Products &amp; quantities</b><span>{{ $productRows->count() }} product · {{ number_format($productRows->sum('quantity')) }} total units</span></div>
            <table class="ft-mini-grid-table ft-inline-product-table">
                <thead><tr><th>Category</th><th>Product</th><th>Quantity</th></tr></thead>
                <tbody>
                @foreach($productRows as $item)
                    <tr wire:key="job-item-{{ $item->id ?? $loop->index }}">
                        <td>
                            @if($item->id)
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-category'), label: 'product category', value: @js($item->category_name ?? ''), display: @js($item->category_name ?: 'Uncategorised') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display">{{ $item->category_name ?: 'Uncategorised' }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.categorySelect.focus())">✎</button>
                                        <select x-ref="categorySelect" x-cloak x-show="editing" x-model="draftValue" class="ft-inline-cell-input product"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateJobItem({{ $item->id }}, 'category_name', draftValue))">
                                            @foreach($categories as $category)<option value="{{ $category->name }}">{{ $category->name }}</option>@endforeach
                                        </select>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </div>
                            @else
                                {{ $item->category_name }}
                            @endif
                        </td>
                        <td>
                            @if($item->id)
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-product'), label: 'product', value: @js($item->product_name), display: @js($item->product_name) })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display">{{ $item->product_name }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.productInput.focus())">✎</button>
                                        <select x-ref="productInput" x-cloak x-show="editing" x-model="draftValue" class="ft-inline-cell-input product"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateJobItem({{ $item->id }}, 'product_name', draftValue))">
                                            @foreach($products as $product)<option value="{{ $product->name }}">{{ $product->name }}</option>@endforeach
                                        </select>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </div>
                            @else
                                {{ $item->product_name }}
                            @endif
                        </td>
                        <td>
                            @if($item->id)
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-quantity'), label: 'quantity', value: @js((string) $item->quantity), display: @js(number_format((int)$item->quantity)) })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display">{{ number_format((int)$item->quantity) }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit quantity" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.quantityInput.focus())">✎</button>
                                        <input x-ref="quantityInput" x-cloak x-show="editing" x-model="draftValue" class="ft-inline-cell-input quantity" type="number" min="1"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:keydown.enter.prevent="$event.target.blur()"
                                            x-on:blur="if (editing) { const next = positiveInteger(draftValue); next === value ? cancelEdit() : commit(next, numberLabel(next), () => $wire.updateJobItem({{ $item->id }}, 'quantity', next)) }">
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </div>
                            @else
                                {{ number_format((int)$item->quantity) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($canEditJob)
                <div class="ft-product-actions"><button class="ft-link-blue ft-add-product-inline" type="button" wire:click="addJobItem({{ $job->id }})">＋ Add product</button></div>
            @endif
        </section>

        <aside class="ft-detail-card ft-side-panel ft-planning-panel">
            <h2>Planning &amp; ownership</h2>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-delivery-date'), label: 'delivery date', value: @js($job->delivery_date?->format('Y-m-d') ?? ''), display: @js($job->delivery_date?->format('M j, Y') ?? 'Not set') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Required delivery</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" x-text="display">{{ $job->delivery_date?->format('M j, Y') ?? 'Not set' }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit required delivery" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.deliveryInput.showPicker ? $refs.deliveryInput.showPicker() : $refs.deliveryInput.focus())">✎</button>
                        <input x-ref="deliveryInput" x-cloak x-show="editing" x-model="draftValue" type="date"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:blur="if (editing) cancelEdit()"
                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateJobDeliveryDate({{ $job->id }}, draftValue))">
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-priority'), label: 'priority', value: @js($job->priority), display: @js($job->priority) })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Priority</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" x-text="display">{{ $job->priority }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit priority" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.prioritySelect.focus())">✎</button>
                        <select x-ref="prioritySelect" x-cloak x-show="editing" x-model="draftValue" x-on:keydown.escape.prevent="cancelEdit()" x-on:blur="if (editing) cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateJobPriority({{ $job->id }}, draftValue))">
                            @foreach($priorities as $priority)<option value="{{ $priority->name }}">{{ $priority->name }}</option>@endforeach
                        </select>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-owner'), label: 'Job owner', value: @js($job->owner_id ?? ''), display: @js($job->owner?->name ?? 'Unassigned') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Job owner</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-inline-person-live ft-planning-person-value">
                        <span x-show="String(value) === String(serverValue)"><x-ui.avatar :user="$job->owner" :name="$job->owner?->name ?? 'Unassigned'" :size="24"/></span>
                        <span x-cloak x-show="String(value) !== String(serverValue)" class="ft-inline-generated-avatar" x-text="initials(display)"></span>
                        <span x-text="display">{{ $job->owner?->name ?? 'Unassigned' }}</span>
                    </span>
                    @if($canAssignJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit job owner" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.ownerSelect.focus())">✎</button>
                        <select x-ref="ownerSelect" x-cloak x-show="editing" x-model="draftValue" class="ft-planning-inline-select" x-on:keydown.escape.prevent="cancelEdit()" x-on:blur="if (editing) cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event, 'Unassigned'), () => $wire.updateJobOwner({{ $job->id }}, draftValue))">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                        </select>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-coordinator'), label: 'Job coordinator', value: @js($job->coordinator_id ?? ''), display: @js($job->coordinator?->name ?? 'Unassigned') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Coordinator</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-inline-person-live ft-planning-person-value">
                        <span x-show="String(value) === String(serverValue)"><x-ui.avatar :user="$job->coordinator" :name="$job->coordinator?->name ?? 'Unassigned'" :size="24"/></span>
                        <span x-cloak x-show="String(value) !== String(serverValue)" class="ft-inline-generated-avatar" x-text="initials(display)"></span>
                        <span x-text="display">{{ $job->coordinator?->name ?? 'Unassigned' }}</span>
                    </span>
                    @if($canAssignJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit coordinator" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.coordinatorSelect.focus())">✎</button>
                        <select x-ref="coordinatorSelect" x-cloak x-show="editing" x-model="draftValue" class="ft-planning-inline-select" x-on:keydown.escape.prevent="cancelEdit()" x-on:blur="if (editing) cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event, 'Unassigned'), () => $wire.updateJobCoordinator({{ $job->id }}, draftValue))">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                        </select>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div class="ft-side-row"><span>Workflow</span><b>▣ {{ $job->workflow?->name }}</b></div>
            <div class="ft-side-row"><span>Created</span><b>{{ $job->created_at?->format('M j, Y, H:i') }}</b></div>
        </aside>
    </div>

    <section class="ft-workflow-mini-line ft-overview-workflow-line">
        @foreach($job->workflow->phases as $phase)
            <button type="button" class="{{ $phase->sequence < $job->phase->sequence ? 'done' : ($phase->id === $job->phase->id ? 'current' : '') }}" wire:click="setDetailTab('workflow')">
                <span>{{ $phase->sequence < $job->phase->sequence ? '✓' : $phase->sequence }}</span><small>{{ $phase->short_name }}</small>
            </button>
        @endforeach
    </section>

    <section class="ft-detail-card ft-phase-table-card ft-overview-task-card">
        <div class="ft-card-row-head ft-task-card-heading">
            <div><h2>All phase tasks</h2><p>{{ $configuredTasks->count() }} tasks across {{ $job->workflow->phases->count() }} phases</p></div>
            <div class="ft-row-actions">
                <label class="ft-inline-search"><span>⌕</span><input wire:model.live.debounce.250ms="jobTaskSearch" placeholder="Search tasks"></label>
                <button class="ft-outline-btn" type="button" wire:click="collapseAllJobPhases">⌃ Collapse all</button>
                <button class="ft-new-job-btn" type="button" wire:click="expandAllJobPhases">↗ Expand all</button>
            </div>
        </div>
        <div class="ft-phase-load-note"><span>◉ All {{ $configuredTasks->count() }} configured Task Pack tasks are loaded</span><span>{{ count($expandedPhaseIds) }} phase{{ count($expandedPhaseIds)===1?'':'s' }} expanded</span><span>Task status changes save automatically</span></div>
        <div class="ft-phase-task-table">
            @foreach($job->workflow->phases as $phase)
                @php
                    $allPhaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                    $completed = \App\Support\JobDetailPresenter::completedCount($allPhaseTasks);
                    $phaseProgress = $allPhaseTasks->count() ? round($completed/max(1,$allPhaseTasks->count())*100) : 0;
                    $phaseTasks = $allPhaseTasks;
                    if(trim($jobTaskSearch)!=='') {
                        $needle = \Illuminate\Support\Str::lower(trim($jobTaskSearch));
                        $phaseTasks = $phaseTasks->filter(fn($task) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($task->title.' '.($task->assignee?->name ?? '')), $needle))->values();
                    }
                    $expanded = in_array((int)$phase->id, array_map('intval',$expandedPhaseIds), true);
                @endphp
                <div class="ft-phase-group {{ $expanded ? 'open' : '' }}" wire:key="job-phase-{{ $phase->id }}">
                    <button type="button" class="ft-phase-group-head ft-phase-toggle" wire:click="toggleJobPhase({{ $phase->id }})" aria-expanded="{{ $expanded?'true':'false' }}">
                        <span>{{ $expanded ? '⌄' : '›' }}</span>
                        <b class="{{ $phase->id === $job->phase->id ? 'current-number' : '' }}">{{ $phase->sequence }}</b>
                        <strong>{{ $phase->name }}</strong>
                        <small>{{ $completed }} of {{ $allPhaseTasks->count() }} complete</small>
                        <em style="--phase-progress:{{ $phaseProgress }}%"></em>
                        <i>{{ $expanded ? '−' : '+' }}</i>
                    </button>
                    @if($expanded)
                        <div class="ft-phase-task-columns"><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Action</span></div>
                        @forelse($phaseTasks as $task)
                            @php($taskAccess = app(\App\Services\AccessControlService::class))
                            @php($canEditTask = $taskAccess->canEditVisibleTask(auth()->user(), $task))
                            @php($canAssignTask = $taskAccess->canAssignTask(auth()->user(), $task))
                            <div class="ft-phase-task-line ft-editable-task-line" wire:key="job-task-{{ $task->id }}">
                                <span>{{ $phase->sequence }}.{{ $loop->iteration }}</span>
                                <button class="ft-inline-task-link" type="button" wire:click="openTask({{ $task->id }})">{{ $task->title }}</button>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-assignee'), label: 'task assignee', value: @js($task->assignee_id ?? ''), display: @js($task->assignee?->name ?? 'Unassigned') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span x-show="!editing" class="ft-task-inline-display ft-inline-person-live">
                                        <span x-show="String(value) === String(serverValue)"><x-ui.avatar :user="$task->assignee" :name="$task->assignee?->name ?? 'Unassigned'" :size="24"/></span>
                                        <span x-cloak x-show="String(value) !== String(serverValue)" class="ft-inline-generated-avatar" x-text="initials(display)"></span>
                                        <span x-text="display">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                    </span>
                                    @if($canAssignTask)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskAssignee.focus())">✎</button>
                                        <select x-ref="taskAssignee" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, selectedLabel($event, 'Unassigned'), () => $wire.updateTaskAssigneeFromJob({{ $task->id }}, draftValue))">
                                            <option value="">Unassigned</option>
                                            @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                                        </select>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </span>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-due-date'), label: 'task due date', value: @js($task->due_date?->format('Y-m-d') ?? ''), display: @js($task->due_date?->format('M j, Y') ?? 'Set due date') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span x-show="!editing" x-text="display" class="ft-task-inline-display {{ $task->due_date?->isPast() && !$task->completed_at ? 'danger-text' : '' }}">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
                                    @if($canEditTask)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskDue.showPicker ? $refs.taskDue.showPicker() : $refs.taskDue.focus())">✎</button>
                                        <input x-ref="taskDue" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input" type="date"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDateFromJob({{ $task->id }}, draftValue))">
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </span>
                                <span
                                    class="ft-task-inline-status-shell ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-status'), label: 'task status', value: @js($task->status), display: @js($task->status) })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <select class="ft-inline-task-status {{ \App\Support\JobDetailPresenter::taskStatusClass($task->status) }}" x-model="draftValue"
                                        x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateTaskStatusFromJob({{ $task->id }}, draftValue))"
                                        :disabled="status === 'saving'" @disabled(!$canEditTask)>
                                        @foreach($taskStatuses as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach
                                    </select>
                                    @if($canEditTask)<x-ui.inline-save-state compact />@endif
                                </span>
                                <button class="ft-table-kebab" type="button" wire:click="openTask({{ $task->id }})">•••</button>
                            </div>
                        @empty
                            <div class="ft-phase-empty-row">No configured tasks match this phase/search.</div>
                        @endforelse
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="ft-detail-card ft-attachment-card ft-job-overview-attachments">
        <h2>Attachments <span>{{ $job->documents->count() }}</span></h2>
        <div class="ft-upload-zone compact"><span class="ft-paperclip">⌕</span><div>Upload documents against the Task Pack requirement that needs them.<small>Documents are linked to the selected task and stored permanently.</small></div><button class="ft-outline-btn" type="button" wire:click="setDetailTab('documents')">Add / choose document</button></div>
        @foreach($job->documents as $doc)<div class="ft-job-file-row"><span class="ft-file-type">{{ strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span><div><b>{{ $doc->name }}</b><small>{{ $doc->task?->title ?: 'Job document' }} · {{ $doc->uploader?->name ?? 'FlowTrack' }} · {{ $doc->created_at?->format('M j, Y, H:i') }}</small></div><a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>@if($canDeleteDocument)<button type="button" wire:click="deleteJobDocument({{ $doc->id }})" wire:confirm="Delete this document link?">Delete</button>@endif</div>@endforeach
    </section>

    <x-jobs.detail-activity :job="$job" :mention-users="$mentionUsers" compact="true" :activity-tab="$activityTab" :activity-page="$activityPage" />
</div>
