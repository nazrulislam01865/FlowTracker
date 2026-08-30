@props([
    'job',
    'overviewPhaseId' => null,
    'taskStatuses' => collect(),
    'context' => [],
    'overviewTaskLinkFormTaskId' => null,
])
@php
    /*
     * Workflow Setup is the workflow-definition source for both Inquiry and Order
     * templates. Active Orders stay attached to the Order workflow selected at
     * creation, so this view renders its saved seven-stage definition and real
     * tasks without hard-coded stage/task definitions in Blade.
     *
     * All relations used here are hydrated by JobService::loadVisibleDetailTab(),
     * so this component does not issue database queries (N+1 safe).
     */
    $phases = \App\Support\OrderDetailPresenter::phases($job);
    $selectedPhase = \App\Support\OrderDetailPresenter::selectedPhase($job, $overviewPhaseId);
    $selectedTasks = $selectedPhase
        ? \App\Support\OrderDetailPresenter::displayTasksForPhase($job, $selectedPhase)
        : collect();
    $selectedState = $selectedPhase
        ? \App\Support\OrderDetailPresenter::phaseState($job, $selectedPhase)
        : 'locked';
    $completedTasks = \App\Support\OrderDetailPresenter::completedCount($selectedTasks);
    $applicableTaskCount = $selectedTasks->count();
    $stageCount = $phases->count();

    $taskPackSub = match ($selectedState) {
        'completed' => 'This stage is complete',
        'active' => 'Complete the active task to continue the workflow',
        'cancelled' => 'Order cancelled — workflow actions are blocked',
        default => 'This stage is locked',
    };
@endphp

<section class="section-card integrated-process ft-order-section-card ft-order-workflow-card" id="workflowSection" wire:key="order-detail-workflow-{{ $job->id }}">
    <div class="process-head">
        <div>
            <h2>Order process &amp; tasks</h2>
            <div class="card-sub">
                @if($stageCount)
                    {{ $stageCount }} stages · status changes save automatically · conditional tasks appear only when required
                @else
                    No Order workflow is configured
                @endif
            </div>
        </div>
    </div>

    @if($phases->isEmpty())
        <div class="empty-stage">
            <b>No Order workflow stages are available.</b><br>
            Configure and save an Order workflow in Workflow Setup before creating Orders.
        </div>
    @else
        <section class="workflow" aria-label="Order workflow stages">
            @foreach($phases as $phase)
                <x-jobs.order-detail.stage-card
                    :job="$job"
                    :phase="$phase"
                    :selected="(int) ($selectedPhase?->id ?? 0) === (int) $phase->id"
                    :context="$context"
                />
            @endforeach
        </section>

        <section class="grid ft-order-workflow-layout ft-order-workflow-layout--full">
            <div class="card ft-order-task-panel">
                <div class="card-head">
                    <div>
                        <div class="card-title">{{ $selectedPhase?->name ?? 'Workflow' }} tasks</div>
                        <div class="card-sub">{{ $taskPackSub }}</div>
                    </div>
                    <div class="completion">{{ $completedTasks }} of {{ $applicableTaskCount }} complete</div>
                </div>

                <div class="task-columns ft-order-task-columns">
                    <span></span><span>Task</span><span>Assignee</span><span>Due date</span><span>Status / files</span><span>Action</span>
                </div>

                <div>
                    @forelse($selectedTasks as $index => $task)
                        @php
                            $mode = \App\Support\OrderDetailPresenter::taskMode($job, $task);
                            if ($selectedState === 'completed' && $mode !== 'done') $mode = 'locked';
                            $displayCode = \App\Support\OrderDetailPresenter::taskDisplayCode($selectedPhase, $task, $index);
                        @endphp
                        <x-jobs.order-detail.task-row
                            :job="$job"
                            :task="$task"
                            :mode="$mode"
                            :display-code="$displayCode"
                            :task-statuses="$taskStatuses"
                            :context="$context"
                            :overview-task-link-form-task-id="$overviewTaskLinkFormTaskId"
                        />
                    @empty
                        <div class="empty-stage">No tasks are configured for this stage.</div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif
</section>
