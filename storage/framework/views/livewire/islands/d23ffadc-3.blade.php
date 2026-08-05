<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'report-workload', lazy: true, always: true);
if (!empty($__islandScope)) {
    extract($__islandScope, EXTR_OVERWRITE);
}

// Extract runtime "with" parameter if provided (overrides everything)
if (isset($__runtimeWith) && is_array($__runtimeWith) && !empty($__runtimeWith)) {
    extract($__runtimeWith, EXTR_OVERWRITE);
}
?>
<?php if (isset($__placeholder)) { echo $__placeholder; return; } ?>


@php($workload = $this->workload)
                <div class="card section-card">
                    <div class="section-head"><h3>Team Workload</h3><span class="small muted">Open tasks</span></div>
                    <div class="bars">
                        @php($workloadMax = max(1, $workload->max('open_tasks_count') ?? 1))
                        @forelse($workload as $person)
                            <div class="bar-row"><span>{{ $person->name }}</span><div class="bar"><span style="width:{{ $person->open_tasks_count/$workloadMax*100 }}%"></span></div><b>{{ $person->open_tasks_count }}</b></div>
                        @empty
                            <div class="empty-state">No open assigned tasks.</div>
                        @endforelse
                    </div>
                </div>
            