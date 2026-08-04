<div>
<x-ui.page-head title="Management Dashboard" :subtitle="now()->format('l, F j').' · Exceptions, workload and delivery health'">
    <x-slot:actions>@if(auth()->user()->canModule('reports','export'))<a class="ghost" href="{{ route('reports') }}" wire:navigate>Export summary</a>@endif @if(auth()->user()->canModule('jobs','create'))<a class="primary" href="{{ route('jobs.index',['create'=>1]) }}" wire:navigate>＋ New Job</a>@endif</x-slot:actions>
</x-ui.page-head>
<div class="metrics">
@foreach([
    ['Active Jobs',$metrics['activeJobs'],'Across all phases'],['Needs Attention',$metrics['riskJobs'],'Risk, delay or blocker'],
    ['Overdue Tasks',$metrics['overdueTasks'],'Require immediate update'],['Pending Approvals',$metrics['pendingApprovals'],'Client or internal'],
    ['Shipping Now',$metrics['shipping'],'Ready or in transit'],['Outstanding','$'.number_format($metrics['outstanding'],0),'Open client balance']
] as $m)
<div class="card metric"><div class="metric-label">{{ $m[0] }}</div><div class="metric-value">{{ $m[1] }}</div><div class="metric-foot">{{ $m[2] }}</div><div class="metric-icon">◎</div></div>
@endforeach
</div>
<div class="grid-2">
<div class="card section-card"><div class="section-head"><h3>Needs Attention</h3><a class="link-btn" href="{{ route('jobs.index') }}" wire:navigate>View all Jobs</a></div><div class="attention-list">
@forelse($attentionJobs as $job)@php($flaggedTask=$job->tasks->first())<a class="attention-item" href="{{ $flaggedTask ? route('jobs.index',['open'=>$job->id,'task'=>$flaggedTask->id]) : route('jobs.index',['open'=>$job->id]) }}" wire:navigate><span class="signal red"></span><div><div class="item-title">{{ $flaggedTask?->title ?: ($job->next_action ?: $job->title) }}</div><div class="item-meta">{{ $job->job_number }} · {{ $job->client->name }} · {{ $flaggedTask ? 'Marked as attention needed' : ($job->phase?->short_name ?? 'Needs attention') }}</div></div><x-ui.badge :label="$flaggedTask ? 'Needs Attention' : $job->health" /></a>@empty<div class="empty-state">No Jobs or tasks need attention.</div>@endforelse
</div></div>
<div class="card section-card"><div class="section-head"><h3>Jobs by Phase</h3><a class="link-btn" href="{{ route('board') }}" wire:navigate>Open board</a></div><div class="phase-list">
@foreach($phaseCounts->take(6) as $row)<div class="phase-row"><span>{{ $row->phase?->short_name }}</span><x-ui.progress :value="min(100,max(8,$row->total*16))" /><b>{{ $row->total }}</b></div>@endforeach
</div></div>
</div>
<div class="grid-3">
<div class="card section-card"><div class="section-head"><h3>Team Workload</h3><a class="link-btn" href="{{ route('reports') }}" wire:navigate>Details</a></div>@foreach($workload as $person)<div class="workload-row"><div class="person"><x-ui.avatar :name="$person->name" :size="27" />{{ $person->name }}</div><x-ui.progress :value="min(100,$person->open_tasks_count*12)"/><b>{{ $person->open_tasks_count }}</b></div>@endforeach</div>
<div class="card section-card"><div class="section-head"><h3>Upcoming Deliveries</h3><a class="link-btn" href="{{ route('jobs.index') }}" wire:navigate>View calendar</a></div><table class="mini-table"><thead><tr><th>Job</th><th>Client</th><th>Delivery</th></tr></thead><tbody>@foreach($deliveries as $job)<tr><td><a class="job-link" href="{{ route('jobs.index',['open'=>$job->id]) }}" wire:navigate>{{ str($job->job_number)->afterLast('-') }}</a><div class="muted small truncate" style="max-width:125px">{{ $job->title }}</div></td><td>{{ str($job->client->name)->before(' ') }}</td><td class="{{ $job->delivery_date?->isPast()?'due overdue':'' }}">{{ $job->delivery_date?->format('M j, Y') }}</td></tr>@endforeach</tbody></table></div>
<div class="card section-card"><div class="section-head"><h3>Recent Activity</h3><a class="link-btn" href="{{ route('notifications') }}" wire:navigate>All activity</a></div>@forelse($activity as $n)<div class="activity"><x-ui.avatar :name="auth()->user()->name" :size="30"/><div><div class="activity-text"><b>{{ $n->title }}</b><br>{{ $n->message }}</div><div class="activity-time">{{ $n->created_at->diffForHumans() }}</div></div></div>@empty<div class="empty-state">No recent activity.</div>@endforelse</div>
</div>
</div>
