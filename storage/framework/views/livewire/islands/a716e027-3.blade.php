<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'dashboard-phases', lazy: true, always: true);
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
                    <div class="section-head"><h3>Jobs by Phase</h3><a class="link-btn" href="{{ route('board') }}" wire:navigate>Open board</a></div>
                    <div class="phase-list">
                        @forelse($this->phaseCounts->sortBy(fn($row) => $row->phase?->sequence ?? PHP_INT_MAX)->take(6) as $row)
                            <div class="phase-row"><span>{{ $row->phase?->short_name ?? 'Unassigned' }}</span><x-ui.progress :value="min(100,max(8,$row->total*16))" /><b>{{ $row->total }}</b></div>
                        @empty
                            <div class="empty-state">No active Jobs by phase.</div>
                        @endforelse
                    </div>
                </div>
            