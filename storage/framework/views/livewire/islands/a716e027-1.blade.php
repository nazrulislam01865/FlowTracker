<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'dashboard-metrics', defer: true, always: true);
if (!empty($__islandScope)) {
    extract($__islandScope, EXTR_OVERWRITE);
}

// Extract runtime "with" parameter if provided (overrides everything)
if (isset($__runtimeWith) && is_array($__runtimeWith) && !empty($__runtimeWith)) {
    extract($__runtimeWith, EXTR_OVERWRITE);
}
?>
<?php if (isset($__placeholder)) { echo $__placeholder; return; } ?>


@php($metrics = $this->metrics)
        <div class="metrics ft-auto-metrics">
            @foreach([
                ['Active Jobs',$metrics['activeJobs'],'Across all active phases'],
                ['Needs Attention',$metrics['riskJobs'],'Risk, delay or blocker'],
                ['Overdue Tasks',$metrics['overdueTasks'],'Require immediate update'],
                ['Pending Approvals',$metrics['pendingApprovals'],'Client or internal'],
                ['Shipping Now',$metrics['shipping'],'Currently in a shipping phase']
            ] as $metric)
                <div class="card metric">
                    <div class="metric-label">{{ $metric[0] }}</div>
                    <div class="metric-value">{{ $metric[1] }}</div>
                    <div class="metric-foot">{{ $metric[2] }}</div>
                    <div class="metric-icon">◎</div>
                </div>
            @endforeach
        </div>
    