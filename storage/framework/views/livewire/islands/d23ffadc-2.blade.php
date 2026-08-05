<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'report-phases', lazy: true, always: true);
if (!empty($__islandScope)) {
    extract($__islandScope, EXTR_OVERWRITE);
}

// Extract runtime "with" parameter if provided (overrides everything)
if (isset($__runtimeWith) && is_array($__runtimeWith) && !empty($__runtimeWith)) {
    extract($__runtimeWith, EXTR_OVERWRITE);
}
?>
<?php if (isset($__placeholder)) { echo $__placeholder; return; } ?>


@php($phase = $this->phase)
                <div class="card section-card">
                    <div class="section-head"><h3>Active Jobs by Phase</h3><span class="small muted">Current portfolio</span></div>
                    <div class="bars">
                        @php($max = max(1, $phase->max('total') ?? 1))
                        @forelse($phase->sortBy(fn($row) => $row->phase?->sequence ?? PHP_INT_MAX) as $row)
                            <div class="bar-row"><span>{{ $row->phase?->short_name ?? 'Unassigned' }}</span><div class="bar"><span style="width:{{ $row->total/$max*100 }}%"></span></div><b>{{ $row->total }}</b></div>
                        @empty
                            <div class="empty-state">No active Jobs.</div>
                        @endforelse
                    </div>
                </div>
            