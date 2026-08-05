<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'report-kpis', defer: true, always: true);
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
        <div class="metrics ft-auto-metrics">
            @foreach([
                ['Active Jobs',$kpis['active_jobs'],'Current portfolio'],
                ['On-time Jobs',$kpis['on_time'].'%','Completed by delivery date'],
                ['Task Completion',$kpis['task_completion'].'%',$kpis['task_done'].' completed tasks'],
                ['Avg. Artwork Cycle',number_format($kpis['avg_artwork_cycle'],1).'d','Completed artwork phases'],
                ['Shipment On Time',$kpis['shipment_on_time'].'%','Completed shipment phases'],
                ['Overdue Tasks',$kpis['overdue_tasks'],'Open tasks past due']
            ] as $metric)
                <div class="card metric">
                    <div class="metric-label">{{ $metric[0] }}</div>
                    <div class="metric-value">{{ $metric[1] }}</div>
                    <div class="metric-foot">{{ $metric[2] }}</div>
                    <div class="metric-icon">◎</div>
                </div>
            @endforeach
        </div>
    