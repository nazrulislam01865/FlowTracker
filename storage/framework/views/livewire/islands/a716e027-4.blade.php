<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'dashboard-workload', lazy: true, always: true);
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
                    <div class="section-head"><h3>Team Workload</h3><a class="link-btn" href="{{ route('reports') }}" wire:navigate>Details</a></div>
                    @forelse($this->workload as $person)
                        <div class="workload-row"><div class="person"><x-ui.avatar :name="$person->name" :size="27" />{{ $person->name }}</div><x-ui.progress :value="min(100,$person->open_tasks_count*12)"/><b>{{ $person->open_tasks_count }}</b></div>
                    @empty
                        <div class="empty-state">No active team workload.</div>
                    @endforelse
                </div>
            