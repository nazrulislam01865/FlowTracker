<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'report-performance', lazy: true, always: true);
if (!empty($__islandScope)) {
    extract($__islandScope, EXTR_OVERWRITE);
}

// Extract runtime "with" parameter if provided (overrides everything)
if (isset($__runtimeWith) && is_array($__runtimeWith) && !empty($__runtimeWith)) {
    extract($__runtimeWith, EXTR_OVERWRITE);
}
?>
<?php if (isset($__placeholder)) { echo $__placeholder; return; } ?>


@php($kpis = $this->kpis)
                <div class="card section-card">
                    <div class="section-head"><h3>Delivery Performance</h3><span class="small muted">Calculated from current Job, task and phase-history records</span></div>
                    <div class="kpi-list ft-report-kpis">
                        <div class="kpi"><b>{{ $kpis['on_time'] }}%</b><span>Jobs delivered on time</span></div>
                        <div class="kpi"><b>{{ $kpis['completed_jobs'] }}</b><span>Completed Jobs</span></div>
                        <div class="kpi"><b>{{ $kpis['task_done'] }}</b><span>Completed tasks</span></div>
                        <div class="kpi"><b>{{ $kpis['shipment_on_time'] }}%</b><span>Shipment phases on time</span></div>
                        <div class="kpi"><b>{{ number_format($kpis['avg_artwork_cycle'],1) }}d</b><span>Average artwork cycle</span></div>
                        <div class="kpi"><b>{{ $kpis['overdue_tasks'] }}</b><span>Overdue open tasks</span></div>
                    </div>
                </div>
            