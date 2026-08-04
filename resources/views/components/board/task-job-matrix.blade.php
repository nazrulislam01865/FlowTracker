@props(['tasks', 'statuses', 'draggable' => false, 'keyPrefix' => 'task-matrix'])
@php
    $matrixTasks = collect($tasks);
    $laneStatuses = collect($statuses)->values();
    $groupedJobs = $matrixTasks->groupBy('flow_job_id');
@endphp

<div class="ft-task-job-matrix" style="--ft-lane-count: {{ max(1, $laneStatuses->count()) }};">
    @forelse($groupedJobs as $jobId => $jobTasks)
        @php
            $job = $jobTasks->first()?->job;
            $resolvedJobId = $job?->id ?? ('unassigned-'.$loop->index);
        @endphp
        <section
            class="ft-task-job-matrix-group"
            x-data="{ open: true }"
            x-on:board-expand-all.window="open = true"
            x-on:board-collapse-all.window="open = false"
            wire:key="{{ $keyPrefix }}-job-{{ $resolvedJobId }}"
        >
            <div class="ft-task-job-row-head">
                <button type="button" class="ft-task-job-row-toggle" x-on:click="open = !open" :title="open ? 'Collapse job tasks' : 'Expand job tasks'" :aria-expanded="open.toString()">
                    <svg :class="{'rotated': !open}" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </button>

                @if($job)
                    <a class="ft-task-job-row-number" href="{{ route('jobs.index', ['open' => $job->id]) }}" wire:navigate>{{ $job->job_number }}</a>
                    <span class="ft-task-job-copy" aria-hidden="true">▣</span>
                    <span class="ft-task-job-dot">·</span>
                    <a class="ft-task-job-row-title" href="{{ route('jobs.index', ['open' => $job->id]) }}" wire:navigate>{{ $job->title }}</a>
                    <span class="ft-task-job-client-pill">{{ $job->client?->name ?? 'No client' }}</span>
                @else
                    <span class="ft-task-job-row-number">No job</span>
                @endif
                <span class="ft-task-job-row-total">{{ $jobTasks->count() }} {{ \Illuminate\Support\Str::plural('task', $jobTasks->count()) }}</span>
            </div>

            <div class="ft-task-job-row-grid" x-show="open">
                @foreach($laneStatuses as $laneStatus)
                    @php($laneTasks = $jobTasks->where('status', $laneStatus))
                    <div
                        class="ft-task-job-status-cell"
                        @if($draggable)
                            x-on:dragover.prevent
                            x-on:drop.prevent="if(draggedTask){ $wire.moveTask(draggedTask, {{ \Illuminate\Support\Js::from($laneStatus) }}); draggedTask=null }"
                        @endif
                    >
                        @forelse($laneTasks as $taskRow)
                            @if($draggable)
                                <x-board.task-card :task="$taskRow" draggable="true" x-on:dragstart="draggedTask={{ $taskRow->id }}" wire:key="{{ $keyPrefix }}-{{ str($laneStatus)->slug() }}-task-{{ $taskRow->id }}" />
                            @else
                                <x-board.task-card :task="$taskRow" wire:key="{{ $keyPrefix }}-{{ str($laneStatus)->slug() }}-task-{{ $taskRow->id }}" />
                            @endif
                        @empty
                            <div class="ft-task-job-empty-cell">No tasks</div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="ft-task-job-matrix-empty">No tasks match the current filters.</div>
    @endforelse
</div>
