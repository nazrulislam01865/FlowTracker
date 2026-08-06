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
            <div class="ft-editable-copy ft-editable-description" x-data="{ editing:false }">
                <div class="ft-edit-display-row" x-show="!editing">
                    <span>@if($job->description)<x-ui.mention-text :text="$job->description" />@else No job description recorded. @endif</span>
                    @if($canEditJob)
                        <button type="button" class="ft-inline-edit-button" aria-label="Edit job description" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.descriptionEditor.focus())">✎</button>
                    @endif
                </div>
                @if($canEditJob)
                    <textarea x-ref="descriptionEditor" x-show="editing" rows="3" class="ft-mention-input" autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}"
                        x-on:keydown.escape="editing=false"
                        x-on:blur="editing=false"
                        wire:change="updateJobTextField({{ $job->id }}, 'description', $event.target.value)">{{ $job->description }}</textarea>
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
                                <div class="ft-inline-field-editor" x-data="{ editing:false }">
                                    <span class="ft-inline-field-value" x-show="!editing">{{ $item->category_name ?: 'Uncategorised' }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="editing=true; $nextTick(() => $refs.categorySelect.focus())">✎</button>
                                        <select x-ref="categorySelect" x-show="editing" class="ft-inline-cell-input product"
                                            x-on:keydown.escape="editing=false"
                                            x-on:blur="editing=false"
                                            x-on:change="editing=false"
                                            wire:change="updateJobItem({{ $item->id }}, 'category_name', $event.target.value)">
                                            @foreach($categories as $category)<option value="{{ $category->name }}" @selected($item->category_name===$category->name)>{{ $category->name }}</option>@endforeach
                                        </select>
                                    @endif
                                </div>
                            @else
                                {{ $item->category_name }}
                            @endif
                        </td>
                        <td>
                            @if($item->id)
                                <div class="ft-inline-field-editor" x-data="{ editing:false }">
                                    <span class="ft-inline-field-value" x-show="!editing">{{ $item->product_name }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.productInput.focus())">✎</button>
                                        <select x-ref="productInput" x-show="editing" class="ft-inline-cell-input product"
                                            x-on:keydown.escape="editing=false"
                                            x-on:blur="editing=false"
                                            x-on:change="editing=false"
                                            wire:change="updateJobItem({{ $item->id }}, 'product_name', $event.target.value)">
                                            @foreach($products as $product)<option value="{{ $product->name }}" @selected($item->product_name===$product->name)>{{ $product->name }}</option>@endforeach
                                        </select>
                                    @endif
                                </div>
                            @else
                                {{ $item->product_name }}
                            @endif
                        </td>
                        <td>
                            @if($item->id)
                                <div class="ft-inline-field-editor" x-data="{ editing:false }">
                                    <span class="ft-inline-field-value" x-show="!editing">{{ number_format((int)$item->quantity) }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit quantity" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.quantityInput.focus())">✎</button>
                                        <input x-ref="quantityInput" x-show="editing" class="ft-inline-cell-input quantity" type="number" min="1" value="{{ (int)$item->quantity }}"
                                            x-on:keydown.escape="editing=false"
                                            x-on:keydown.enter="$event.target.blur()"
                                            x-on:blur="editing=false"
                                            wire:change="updateJobItem({{ $item->id }}, 'quantity', $event.target.value)">
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
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Required delivery</span>
                <b class="ft-planning-value">
                    <span x-show="!editing">{{ $job->delivery_date?->format('M j, Y') ?? 'Not set' }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit required delivery" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.deliveryInput.showPicker ? $refs.deliveryInput.showPicker() : $refs.deliveryInput.focus())">✎</button>
                        <input x-ref="deliveryInput" x-show="editing" type="date" value="{{ $job->delivery_date?->format('Y-m-d') }}"
                            x-on:keydown.escape="editing=false"
                            x-on:blur="editing=false"
                            wire:change="updateJobDeliveryDate({{ $job->id }}, $event.target.value)">
                    @endif
                </b>
            </div>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Priority</span>
                <b class="ft-planning-value">
                    <span x-show="!editing">{{ $job->priority }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit priority" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.prioritySelect.focus())">✎</button>
                        <select x-ref="prioritySelect" x-show="editing" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateJobPriority({{ $job->id }}, $event.target.value)" x-on:change="editing=false">
                            @foreach($priorities as $priority)<option value="{{ $priority->name }}" @selected($job->priority===$priority->name)>{{ $priority->name }}</option>@endforeach
                        </select>
                    @endif
                </b>
            </div>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Job owner</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-planning-person"><x-ui.avatar :name="$job->owner?->name ?? 'Unassigned'" :size="24"/>{{ $job->owner?->name ?? 'Unassigned' }}</span>
                    @if($canAssignJob)
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit job owner" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.ownerSelect.focus())">✎</button>
                        <select x-ref="ownerSelect" x-show="editing" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateJobOwner({{ $job->id }}, $event.target.value)" x-on:change="editing=false">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)<option value="{{ $user->id }}" @selected((int)$job->owner_id===(int)$user->id)>{{ $user->name }}</option>@endforeach
                        </select>
                    @endif
                </b>
            </div>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Coordinator</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-planning-person"><x-ui.avatar :name="$job->coordinator?->name ?? 'Unassigned'" :size="24"/>{{ $job->coordinator?->name ?? 'Unassigned' }}</span>
                    @if($canAssignJob)
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit coordinator" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.coordinatorSelect.focus())">✎</button>
                        <select x-ref="coordinatorSelect" x-show="editing" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateJobCoordinator({{ $job->id }}, $event.target.value)" x-on:change="editing=false">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)<option value="{{ $user->id }}" @selected((int)$job->coordinator_id===(int)$user->id)>{{ $user->name }}</option>@endforeach
                        </select>
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
                                <span class="ft-task-inline-editor" x-data="{ editing:false }">
                                    <span x-show="!editing" class="ft-task-inline-display"><x-ui.avatar :name="$task->assignee?->name ?? 'Unassigned'" :size="24"/>{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                    @if($canAssignTask)
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="editing=true; $nextTick(() => $refs.taskAssignee.focus())">✎</button>
                                        <select x-ref="taskAssignee" x-show="editing" class="ft-task-inline-input" x-on:keydown.escape="editing=false" x-on:blur="editing=false" x-on:change="editing=false" wire:change="updateTaskAssigneeFromJob({{ $task->id }}, $event.target.value)">
                                            @foreach($users as $user)<option value="{{ $user->id }}" @selected((int)$task->assignee_id===(int)$user->id)>{{ $user->name }}</option>@endforeach
                                        </select>
                                    @endif
                                </span>
                                <span class="ft-task-inline-editor" x-data="{ editing:false }">
                                    <span x-show="!editing" class="ft-task-inline-display {{ $task->due_date?->isPast() && !$task->completed_at ? 'danger-text' : '' }}">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
                                    @if($canEditTask)
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="editing=true; $nextTick(() => $refs.taskDue.showPicker ? $refs.taskDue.showPicker() : $refs.taskDue.focus())">✎</button>
                                        <input x-ref="taskDue" x-show="editing" class="ft-task-inline-input" type="date" value="{{ $task->due_date?->format('Y-m-d') }}" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateTaskDueDateFromJob({{ $task->id }}, $event.target.value)">
                                    @endif
                                </span>
                                <select class="ft-inline-task-status {{ \App\Support\JobDetailPresenter::taskStatusClass($task->status) }}" wire:change="updateTaskStatusFromJob({{ $task->id }}, $event.target.value)" @disabled(!$canEditTask)>
                                    @foreach($taskStatuses as $status)<option value="{{ $status }}" @selected($task->status===$status)>{{ $status }}</option>@endforeach
                                </select>
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
