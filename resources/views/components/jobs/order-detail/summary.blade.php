@props(['job', 'nextTask' => null, 'currentTasks' => collect()])
@php
    $phases = \App\Support\OrderDetailPresenter::phases($job);
    $stageCount = max(1, $phases->count());
    $currentPhaseNumber = \App\Support\OrderDetailPresenter::currentPhaseNumber($job);
    $completedTasks = \App\Support\OrderDetailPresenter::completedCount($currentTasks);
    $applicableTasks = $currentTasks->reject(fn($task) => \App\Support\OrderDetailPresenter::isSkippedTask($task))->values();
    $applicableCount = $applicableTasks->count();
    $progress = max(0, min(100, (int) ($job->progress ?? 0)));
    $nextOwner = $nextTask?->assignee?->name ?: $job->owner?->name ?: 'Unassigned';
    $dependency = $currentPhaseNumber <= 1 ? 'No dependency' : 'Previous stage complete';
@endphp
<section class="summary-grid ft-order-summary-grid" aria-label="Order workflow summary">
    <div class="summary-card ft-order-summary-card">
        <div class="summary-ic">▣</div>
        <div>
            <div class="summary-label">Current stage</div>
            <div class="summary-value">
                <span>{{ $job->phase?->name ?: 'Not configured' }}</span>
                · Stage <span>{{ $currentPhaseNumber }}</span> of {{ $stageCount }}
            </div>
            <div class="summary-sub">{{ $completedTasks }} of {{ $applicableCount }} applicable tasks complete</div>
        </div>
    </div>

    <div class="summary-card ft-order-summary-card">
        <div class="summary-ic">↗</div>
        <div>
            <div class="summary-label">Overall progress</div>
            <div class="summary-value">{{ $progress }}%</div>
            <div class="overall-progress ft-order-overall-progress"><i style="width:{{ $progress }}%"></i></div>
            <div class="summary-sub"><span class="status-pill">{{ $job->status ?: 'New' }}</span></div>
        </div>
    </div>

    <div class="summary-card next-summary ft-order-summary-card next">
        <div class="summary-ic">⌘</div>
        <div>
            <div class="summary-label"><span class="help" title="The next unlocked task from this Order's saved workflow setup.">Next required action</span></div>
            <div class="summary-value">{{ $nextTask?->title ?: ($job->completed_at ? 'Order completed' : 'No action available') }}</div>
            <div class="summary-sub"><span>{{ $nextOwner }}</span> · <span>{{ $dependency }}</span></div>
            @if($nextTask)
                <button type="button" class="btn primary small summary-cta" wire:click="openTask({{ (int) $nextTask->id }})">Take action</button>
            @endif
        </div>
    </div>
</section>
