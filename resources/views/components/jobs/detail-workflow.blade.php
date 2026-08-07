@props(['job','users'=>collect(),'healthOptions'=>collect()])
@php
    $blockers = \App\Support\JobDetailPresenter::blockers($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $requiredTasks = $currentTasks->filter(fn($task) => ($task->setupTemplate?->is_required ?? $task->template?->is_required ?? true) !== false)->values();
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $requiredDone = \App\Support\JobDetailPresenter::completedCount($requiredTasks);
    $next = \App\Support\JobDetailPresenter::nextPhase($job);
    $rows = \App\Support\JobDetailPresenter::phaseHistoryRows($job);
    $currentRequired = \App\Support\JobDetailPresenter::phaseRequiredDocuments($job,$job->phase);
    $receivedCurrent = $currentRequired->where('complete',true)->count();
    $missingCurrent = $currentRequired->where('complete',false);
    $blockingTask = $requiredTasks->first(fn($task) => !$task->completed_at && $task->status !== 'Completed');
    $progress = $currentTasks->count() ? round($done/max(1,$currentTasks->count())*100) : 0;
    $tasksReady = $requiredTasks->filter(fn($task) => !$task->completed_at && $task->status !== 'Completed')->isEmpty();
    $documentsReady = $missingCurrent->isEmpty();
    $canEditJob = app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job);
    $canChangeJobStatus = app(\App\Services\AccessControlService::class)->canChangeVisibleJobStatus(auth()->user(), $job);
@endphp
<div class="ft-workflow-detail-section ft-exact-workflow">
    <div class="ft-section-title-row"><div><h2>Workflow</h2><p>{{ $job->workflow->name }} · Version 1</p></div></div>

    @if($blockers->isNotEmpty())
        <div class="ft-warning-banner">
            <span>!</span>
            <div>
                <b>{{ $blockers->count() === 1 ? $blockers->first()->label : $blockers->count().' Task Pack requirements block the next phase' }}</b>
                <p>{{ $blockers->pluck('description')->implode(' ') }}</p>
            </div>
            @if($blockingTask)<button type="button" wire:click="openTask({{ $blockingTask->id }})">View blocking task</button>@else<button type="button" wire:click="setDetailTab('documents')">View documents</button>@endif
        </div>
    @else
        <div class="ft-success-banner"><span>✓</span><div><b>Ready for the next phase</b><p>All required Task Pack tasks and Task Pack documents are complete.</p></div></div>
    @endif

    <section class="ft-workflow-stepper-card"><div class="ft-workflow-stepper">
        @foreach($job->workflow->phases as $phase)
            @php
                $phaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                $phaseDone = \App\Support\JobDetailPresenter::completedCount($phaseTasks);
            @endphp
            <div class="ft-workflow-step {{ $phase->sequence < $job->phase->sequence ? 'done' : ($phase->id === $job->phase->id ? 'current' : '') }}">
                <span>{{ $phase->sequence < $job->phase->sequence ? '✓' : $phase->sequence }}</span>
                <small>{{ $phase->short_name }}</small>
                @if($phase->id === $job->phase->id)<em>Current · {{ $phaseDone }}/{{ $phaseTasks->count() }}</em>@endif
            </div>
        @endforeach
    </div></section>

    <div class="ft-detail-two-col workflow-layout-exact">
        <main>
            <section class="ft-detail-card ft-current-phase-card">
                <div class="ft-card-row-head">
                    <div>
                        <h2>Current phase · {{ $job->phase->name }}</h2>
                        <div class="ft-phase-progress-copy"><span>{{ $done }} of {{ $currentTasks->count() }} tasks complete</span><div class="ft-line-progress"><span style="width:{{ $progress }}%"></span></div><b>{{ $progress }}%</b></div>
                    </div>
                    <span class="ft-phase-count-pill">Phase {{ $job->phase->sequence }} of {{ $job->workflow->phases->count() }}</span>
                </div>
                <div class="ft-phase-owner-row">
                    <x-ui.avatar :user="$job->coordinator" :name="$job->coordinator?->name ?? 'Unassigned'" :size="24"/><b>{{ $job->coordinator?->name ?? 'Unassigned' }}</b><span>·</span>
                    <span>Entered {{ ($job->phaseHistories->firstWhere('workflow_phase_id',$job->phase->id)?->entered_at ?? $job->created_at)?->format('M j, Y') }}</span><span>·</span>
                    <span>Target {{ $job->delivery_date?->format('M j, Y') ?? '—' }}</span><span>·</span><span>{{ \App\Support\BoardPresenter::phaseDays($job) }} days in phase</span>
                    <button class="ft-link-blue" type="button" wire:click="setDetailTab('overview')">View {{ $currentTasks->count() }} phase tasks</button>
                </div>

                <div class="ft-readiness-table ft-taskpack-readiness-only">
                    <div>
                        <span class="{{ $tasksReady ? 'ok' : 'warn' }}">{{ $tasksReady ? '✓' : '!' }}</span><b>1</b><span>Task Pack tasks</span>
                        <strong>{{ $requiredDone }} of {{ $requiredTasks->count() }} required complete</strong>
                        <em class="{{ $tasksReady ? 'complete' : 'remain' }}">{{ $tasksReady ? 'Complete' : max(0,$requiredTasks->count()-$requiredDone).' required remaining' }}</em>
                        @if($blockingTask)<button wire:click="openTask({{ $blockingTask->id }})">Open task</button>@else<button type="button" wire:click="setDetailTab('overview')">View tasks</button>@endif
                    </div>
                    <div>
                        <span class="{{ $documentsReady ? 'ok' : 'warn' }}">{{ $documentsReady ? '✓' : '!' }}</span><b>2</b><span>Task Pack documents</span>
                        <strong>{{ $currentRequired->isEmpty() ? 'Not required' : $receivedCurrent.' of '.$currentRequired->count().' received' }}</strong>
                        <em class="{{ $documentsReady ? 'complete' : 'blocked' }}">{{ $documentsReady ? 'Complete' : 'Review' }}</em>
                        <button wire:click="setDetailTab('documents')">View documents</button>
                    </div>
                </div>

                <div class="ft-next-phase-box">
                    <span>▣</span>
                    <div><b>Next phase: {{ $next?->name ?? 'Completed' }}</b><p>{{ $blockers->isEmpty() ? 'All Task Pack requirements are ready.' : 'Complete the remaining Task Pack requirements.' }}</p></div>
                    @if($blockingTask)<button class="ft-outline-btn" type="button" wire:click="openTask({{ $blockingTask->id }})">Open blocking task</button>@elseif(!$documentsReady)<button class="ft-outline-btn" type="button" wire:click="setDetailTab('documents')">Open documents</button>@else<button class="ft-outline-btn" type="button" wire:click="setDetailTab('overview')">Review</button>@endif
                    @if($canChangeJobStatus)
                        <button class="{{ $blockers->isEmpty() ? 'ft-new-job-btn' : 'ft-disabled-btn' }}" wire:click="completePhase" @disabled($blockers->isNotEmpty())>Move to {{ $next?->name ?? 'Completed' }}</button>
                    @else
                        <span class="ft-permission-note">Only the assigned Job owner can move this Job to another phase.</span>
                    @endif
                    <button class="ft-outline-btn ft-square-action" type="button">•••</button>
                </div>
                @error('phaseCompletion')<div class="ft-warning-banner slim"><span>!</span><p>{{ $message }}</p></div>@enderror
            </section>

            <section class="ft-detail-card ft-history-card">
                <h2>Phase history</h2><p>Each phase is calculated only from its selected Task Pack tasks and Task Pack document requirements.</p>
                <table class="ft-history-table"><thead><tr><th>Phase</th><th>Status</th><th>Entered</th><th>Completed</th><th>Time in phase</th><th>Outcome</th></tr></thead><tbody>
                    @foreach($rows as $row)
                        <tr><td><b>{{ $row->phase->sequence }}</b> &nbsp; {{ $row->phase->short_name }}</td><td><span class="ft-soft-pill {{ $row->status==='Completed'?'green':($row->status==='Current'?'blue':'gray') }}">{{ $row->status }}</span></td><td>{{ $row->entered?->format('M j Y') ?? '—' }}</td><td>{{ $row->completed?->format('M j Y') ?? '—' }}</td><td>{{ $row->time ? $row->time.' day'.($row->time>1?'s':'') : '—' }}</td><td class="{{ $row->outcome==='Passed'?'green-text':($row->outcome==='Blocked'?'warn-text':'') }}">{{ $row->outcome }}</td></tr>
                    @endforeach
                </tbody></table>
            </section>
        </main>
        <aside>
            <section class="ft-detail-card ft-side-panel">
                <h2>Phase controls</h2>
                <div
                    class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-coordinator'), label: 'phase owner', value: @js($job->coordinator_id ?? ''), display: @js($job->coordinator?->name ?? 'Unassigned') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span>Phase owner</span>
                    <b class="ft-planning-value">
                        <span x-show="!editing" class="ft-inline-person-live ft-planning-person-value">
                            <span x-show="String(value) === String(serverValue)"><x-ui.avatar :user="$job->coordinator" :name="$job->coordinator?->name ?? 'Unassigned'" :size="24"/></span>
                            <span x-cloak x-show="String(value) !== String(serverValue)" class="ft-inline-generated-avatar" x-text="initials(display)"></span>
                            <span x-text="display">{{ $job->coordinator?->name ?? 'Unassigned' }}</span>
                        </span>
                        @if($canEditJob)
                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit phase owner" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.phaseOwner.focus())">✎</button>
                            <select x-ref="phaseOwner" x-cloak x-show="editing" x-model="draftValue" class="ft-planning-inline-select"
                                x-on:keydown.escape.prevent="cancelEdit()"
                                x-on:blur="if (editing) cancelEdit()"
                                x-on:change="commit($event.target.value, selectedLabel($event, 'Unassigned'), () => $wire.updateJobCoordinator({{ $job->id }}, draftValue))">
                                <option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                            </select>
                            <x-ui.inline-save-state compact />
                        @endif
                    </b>
                </div>
                <div
                    class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-delivery-date'), label: 'target date', value: @js($job->delivery_date?->format('Y-m-d') ?? ''), display: @js($job->delivery_date?->format('M j, Y') ?? 'Not set') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span>Target date</span>
                    <b class="ft-planning-value">
                        <span x-show="!editing" x-text="display">{{ $job->delivery_date?->format('M j, Y') ?? 'Not set' }}</span>
                        @if($canEditJob)
                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit target date" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.phaseDate.showPicker ? $refs.phaseDate.showPicker() : $refs.phaseDate.focus())">✎</button>
                            <input x-ref="phaseDate" x-cloak x-show="editing" x-model="draftValue" type="date"
                                x-on:keydown.escape.prevent="cancelEdit()"
                                x-on:blur="if (editing) cancelEdit()"
                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateJobDeliveryDate({{ $job->id }}, draftValue))">
                            <x-ui.inline-save-state compact />
                        @endif
                    </b>
                </div>
                <div
                    class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-health'), label: 'job health', value: @js($job->health), display: @js($job->health) })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                >
                    <span>Health</span>
                    <b class="ft-planning-value">
                        <span x-show="!editing" x-text="display">{{ $job->health }}</span>
                        @if($canEditJob)
                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit health" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.healthSelect.focus())">✎</button>
                            <select x-ref="healthSelect" x-cloak x-show="editing" x-model="draftValue"
                                x-on:keydown.escape.prevent="cancelEdit()"
                                x-on:blur="if (editing) cancelEdit()"
                                x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateJobHealth({{ $job->id }}, draftValue))">
                                @foreach($healthOptions as $health)<option value="{{ $health }}">{{ $health }}</option>@endforeach
                            </select>
                            <x-ui.inline-save-state compact />
                        @endif
                    </b>
                </div>
                <div class="ft-side-row"><span>Automation</span><b>{{ $job->phase->auto_advance_on_ready ? 'Automatic' : 'Manual' }}</b></div>
                <hr><h3>Transition policy</h3>
                <p class="{{ $tasksReady ? 'ok-text' : 'warn-text' }}">{{ $tasksReady ? '✓' : '!' }} &nbsp; Required Task Pack tasks complete</p>
                <p class="{{ $documentsReady ? 'ok-text' : 'warn-text' }}">{{ $documentsReady ? '✓' : '!' }} &nbsp; Required Task Pack documents received</p>
                <a class="ft-link-blue" href="{{ route('workflow.setup') }}" wire:navigate>View transition rules</a>
            </section>
            <section class="ft-detail-card ft-side-panel"><h2>Workflow details</h2><div class="ft-side-row"><span>Template</span><b>{{ $job->workflow->name }}</b></div><div class="ft-side-row"><span>Version</span><b>1</b></div><div class="ft-side-row"><span>Started at</span><b>{{ $job->startedFromPhase?->short_name ?? $job->workflow->phases->first()?->short_name }}</b></div><div class="ft-side-row"><span>Started</span><b>{{ $job->created_at?->format('M j Y') }}</b></div><a class="ft-link-blue" href="{{ route('workflow.setup') }}" wire:navigate>Open workflow configuration ↗</a></section>
        </aside>
    </div>
</div>
