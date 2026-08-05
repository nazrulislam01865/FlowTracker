<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'dashboard-attention', lazy: true, always: true);
if (!empty($__islandScope)) {
    extract($__islandScope, EXTR_OVERWRITE);
}

// Extract runtime "with" parameter if provided (overrides everything)
if (isset($__runtimeWith) && is_array($__runtimeWith) && !empty($__runtimeWith)) {
    extract($__runtimeWith, EXTR_OVERWRITE);
}
?>
<?php if (isset($__placeholder)) { echo $__placeholder; return; } ?>


<div class="card section-card">
                    <div class="section-head"><h3>Needs Attention</h3><a class="link-btn" href="{{ route('jobs.index') }}" wire:navigate>View all Jobs</a></div>
                    <div class="attention-list">
                        @forelse($this->attentionJobs as $job)
                            @php($flaggedTask = $job->tasks->first())
                            <a class="attention-item" href="{{ $flaggedTask ? route('jobs.index',['open'=>$job->id,'task'=>$flaggedTask->id]) : route('jobs.index',['open'=>$job->id]) }}" wire:navigate>
                                <span class="signal red"></span>
                                <div>
                                    <div class="item-title">{{ $flaggedTask?->title ?: ($job->next_action ?: $job->title) }}</div>
                                    <div class="item-meta">{{ $job->job_number }} · {{ $job->client?->name ?? 'No client' }} · {{ $flaggedTask ? 'Marked as attention needed' : ($job->phase?->short_name ?? 'Needs attention') }}</div>
                                </div>
                                <x-ui.badge :label="$flaggedTask ? 'Needs Attention' : $job->health" />
                            </a>
                        @empty
                            <div class="empty-state">No Jobs or tasks need attention.</div>
                        @endforelse
                    </div>
                </div>
            