@php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $statusTone = static function (?string $status): string {
        $value = strtolower((string) $status);
        if (str_contains($value, 'wait') || str_contains($value, 'risk')) return 'amber';
        if (str_contains($value, 'revision') || str_contains($value, 'artwork')) return 'purple';
        if (str_contains($value, 'not started')) return 'gray';
        if (str_contains($value, 'progress') || str_contains($value, 'production') || str_contains($value, 'request')) return 'blue';
        return '';
    };
    $taskFlag = static function ($task) use ($today): array {
        if ($task->due_date && $task->due_date->lt($today)) return ['Overdue', 'red'];
        if ($task->status === 'Waiting for Client') return ['Client wait', 'amber'];
        if ($task->status === 'Revision Required') return ['Revision', 'amber'];
        if ($task->status === 'Blocked') return ['Blocked', 'red'];
        if (!$task->assignee_id) return ['Unassigned', 'blue'];
        if ($task->needs_attention) return ['Attention', 'amber'];
        return ['On track', 'green'];
    };
    $jobFlag = static function ($job): array {
        if (in_array($job->health, ['Delayed', 'Blocked'], true)) return [$job->health, 'red'];
        if ($job->needs_attention || in_array($job->health, ['At Risk', 'Needs Attention'], true)) return ['At risk', 'amber'];
        return ['On track', 'green'];
    };
@endphp

<div class="ft-dashboard-secondary-sections">
    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel ft-dashboard-assignee-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Assignee performance</h2><div class="ft-panel-note">Ongoing workload before Done, completion and overdue exposure</div></div>{{-- Reports Details link disabled with the Reports page. --}}</div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-assignee-table">
                    <colgroup><col style="width:29%"><col style="width:16%"><col style="width:18%"><col style="width:19%"><col style="width:18%"></colgroup>
                    <thead><tr><th>Assignee</th><th>Ongoing</th><th>Done</th><th>On time</th><th>Workload</th></tr></thead>
                    <tbody>
                        @forelse($assigneePerformance as $person)
                            @php
                                $onTime = $person->done_count > 0 ? (int) round(($person->done_on_time_count / $person->done_count) * 100) : 100;
                                $workloadPct = min(100, max(8, (int) $person->ongoing_count * 12));
                                $workloadLabel = $person->ongoing_count >= 8 ? 'High' : ($person->ongoing_count >= 5 ? 'Med' : 'Good');
                            @endphp
                            <tr wire:key="dashboard-assignee-{{ $person->id }}">
                                <td data-label="Assignee"><span class="ft-person"><x-ui.avatar :user="$person" :name="$person->name" :size="22" /><span class="ft-cell-clip">{{ $person->name }}</span></span></td>
                                <td data-label="Ongoing"><a class="ft-text-link" href="{{ route('all-tasks', ['assignee' => $person->id]) }}" wire:navigate>{{ $person->ongoing_count }} ↗</a></td>
                                <td data-label="Done">{{ $person->done_count }}</td>
                                <td data-label="On time">{{ $onTime }}%</td>
                                <td data-label="Workload"><span class="ft-load"><i class="ft-load-track"><span style="width:{{ $workloadPct }}%"></span></i>{{ $workloadLabel }}</span></td>
                            </tr>
                        @empty
                            <tr class="ft-table-empty-row"><td colspan="5">No active assignee workload.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-panel ft-dashboard-attention-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Needs attention</h2><div class="ft-panel-note">Highest-priority tasks across current jobs</div></div><a class="ft-link" href="{{ route('all-tasks') }}" wire:navigate>View all tasks</a></div>
            <div class="ft-risk-list">
                @forelse($attentionTasks as $task)
                    @php
                        [$flagLabel, $flagTone] = $taskFlag($task);
                    @endphp
                    <div class="ft-risk" wire:key="dashboard-risk-{{ $task->id }}">
                        <a class="ft-risk-name ft-text-link" href="{{ route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) }}" wire:navigate>{{ $task->title }}</a>
                        <span class="ft-flag {{ $flagTone }}">{{ $flagLabel }}</span>
                        <span class="ft-risk-meta">{{ $task->task_number }} · {{ $task->job?->displayOrderNumber() ?? 'Order' }} · {{ $task->assignee?->name ?? 'Unassigned' }} · {{ $task->due_date ? 'Due '.$task->due_date->format('M j') : 'No due date' }}</span>
                    </div>
                @empty
                    <div class="ft-panel-empty">No tasks currently need attention.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel ft-dashboard-jobs-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Ongoing jobs</h2><div class="ft-panel-note">Current stage, health and exception flags</div></div><a class="ft-link" href="{{ route('jobs.index') }}" wire:navigate>View jobs</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-jobs-table">
                    <colgroup><col style="width:31%"><col style="width:18%"><col style="width:23%"><col style="width:18%"><col style="width:10%"></colgroup>
                    <thead><tr><th>Job</th><th>Client</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        @forelse($ongoingJobs as $job)
                            @php
                                [$flagLabel, $flagTone] = $jobFlag($job);
                            @endphp
                            <tr wire:key="dashboard-job-{{ $job->id }}">
                                <td data-label="Job"><a class="ft-text-link ft-cell-clip" href="{{ route('jobs.index', ['open' => $job->id]) }}" wire:navigate>{{ $job->title }}</a><span class="ft-ref">{{ $job->displayOrderNumber() }}</span></td>
                                <td data-label="Client"><span class="ft-cell-clip">{{ $job->client?->name ?? '—' }}</span></td>
                                <td data-label="Status"><span class="ft-pill {{ $statusTone($job->phase?->short_name) }}">{{ $job->phase?->short_name ?? 'Unassigned' }}</span></td>
                                <td data-label="Flag"><span class="ft-flag {{ $flagTone }}">{{ $flagLabel }}</span></td>
                                <td data-label="View"><a class="ft-view" href="{{ route('jobs.index', ['open' => $job->id]) }}" wire:navigate>View</a></td>
                            </tr>
                        @empty
                            <tr class="ft-table-empty-row"><td colspan="5">No ongoing jobs.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-panel ft-dashboard-tasks-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Ongoing tasks</h2><div class="ft-panel-note">Tasks before Done with current work status and flags</div></div><a class="ft-link" href="{{ route('all-tasks') }}" wire:navigate>Open all tasks</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-tasks-table">
                    <colgroup><col style="width:29%"><col style="width:13%"><col style="width:17%"><col style="width:20%"><col style="width:13%"><col style="width:8%"></colgroup>
                    <thead><tr><th>Task</th><th>Job</th><th>Assignee</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        @forelse($ongoingTasks as $task)
                            @php
                        [$flagLabel, $flagTone] = $taskFlag($task);
                    @endphp
                            <tr wire:key="dashboard-task-{{ $task->id }}">
                                <td data-label="Task"><a class="ft-text-link ft-cell-clip" href="{{ route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) }}" wire:navigate>{{ $task->title }}</a><span class="ft-ref">{{ $task->task_number }}</span></td>
                                <td data-label="Job"><a class="ft-text-link" href="{{ route('jobs.index', ['open' => $task->flow_job_id]) }}" wire:navigate>{{ str($task->job?->displayOrderNumber() ?? '—')->afterLast('-') }}</a></td>
                                <td data-label="Assignee"><span class="ft-cell-clip">{{ $task->assignee?->name ?? 'Unassigned' }}</span></td>
                                <td data-label="Status"><span class="ft-pill {{ $statusTone($task->status) }}">{{ $task->status }}</span></td>
                                <td data-label="Flag"><span class="ft-flag {{ $flagTone }}">{{ $flagLabel }}</span></td>
                                <td data-label="View"><a class="ft-view" href="{{ route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id]) }}" wire:navigate>View</a></td>
                            </tr>
                        @empty
                            <tr class="ft-table-empty-row"><td colspan="6">No ongoing tasks.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Recent activity</h2><div class="ft-panel-note">Latest job, task, inquiry, document and comment events</div></div><a class="ft-link" href="{{ route('notifications') }}" wire:navigate>All activity</a></div>
            <div class="ft-activity-list">
                @forelse($recentActivity as $notification)
                    <div class="ft-activity" wire:key="dashboard-activity-{{ $notification->id }}">
                        <span class="ft-activity-icon">{{ $notification->type === 'mention' ? '@' : '✓' }}</span>
                        <span><strong>{{ $notification->title }}</strong><span class="ft-activity-copy">{{ app(\App\Services\RichTextService::class)->plainText($notification->message) }}</span></span>
                        <time class="ft-activity-time">{{ $notification->created_at?->diffForHumans(short: true) }}</time>
                    </div>
                @empty
                    <div class="ft-panel-empty">No recent activity.</div>
                @endforelse
            </div>
        </section>

        <section class="ft-panel ft-dashboard-clients-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Client portfolio</h2><div class="ft-panel-note">Active work, inquiry volume and delivery health</div></div><a class="ft-link" href="{{ route('clients.index') }}" wire:navigate>All clients</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-clients-table">
                    <colgroup><col style="width:28%"><col style="width:15%"><col style="width:18%"><col style="width:19%"><col style="width:20%"></colgroup>
                    <thead><tr><th>Client</th><th>Jobs</th><th>Inquiries</th><th>At risk</th><th>On time</th></tr></thead>
                    <tbody>
                        @forelse($clientPortfolio as $client)
                            @php
                                $onTime = $client->open_tasks_count > 0
                                    ? max(0, (int) round((($client->open_tasks_count - $client->overdue_tasks_count) / $client->open_tasks_count) * 100))
                                    : 100;
                                $riskTone = $client->at_risk_jobs_count > 1 ? 'red' : ($client->at_risk_jobs_count === 1 ? 'amber' : 'green');
                            @endphp
                            <tr wire:key="dashboard-client-{{ $client->id }}">
                                <td data-label="Client"><a class="ft-text-link ft-cell-clip" href="{{ route('clients.index') }}" wire:navigate>{{ $client->name }}</a></td>
                                <td data-label="Jobs"><a class="ft-text-link" href="{{ route('jobs.index', ['client' => $client->id]) }}" wire:navigate>{{ $client->active_jobs_count }} ↗</a></td>
                                <td data-label="Inquiries">0</td>
                                <td data-label="At risk"><span class="ft-flag {{ $riskTone }}">{{ $client->at_risk_jobs_count }}</span></td>
                                <td data-label="On time">{{ $onTime }}%</td>
                            </tr>
                        @empty
                            <tr class="ft-table-empty-row"><td colspan="5">No active clients.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
