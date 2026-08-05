<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'dashboard-deliveries', lazy: true, always: true);
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
                    <div class="section-head"><h3>Upcoming Deliveries</h3><a class="link-btn" href="{{ route('jobs.index') }}" wire:navigate>View Jobs</a></div>
                    <table class="mini-table">
                        <thead><tr><th>Job</th><th>Client</th><th>Delivery</th></tr></thead>
                        <tbody>
                            @forelse($this->deliveries as $job)
                                <tr><td><a class="job-link" href="{{ route('jobs.index',['open'=>$job->id]) }}" wire:navigate>{{ str($job->job_number)->afterLast('-') }}</a><div class="muted small truncate" style="max-width:125px">{{ $job->title }}</div></td><td>{{ str($job->client?->name ?? '—')->before(' ') }}</td><td>{{ $job->delivery_date?->format('M j, Y') }}</td></tr>
                            @empty
                                <tr><td colspan="3"><div class="empty-state">No upcoming deliveries.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            