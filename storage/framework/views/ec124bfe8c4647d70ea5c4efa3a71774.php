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


<?php ($kpis = $this->kpis); ?>
        <div class="metrics ft-auto-metrics">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                ['Active Jobs',$kpis['active_jobs'],'Current portfolio'],
                ['On-time Jobs',$kpis['on_time'].'%','Completed by delivery date'],
                ['Task Completion',$kpis['task_completion'].'%',$kpis['task_done'].' completed tasks'],
                ['Avg. Artwork Cycle',number_format($kpis['avg_artwork_cycle'],1).'d','Completed artwork phases'],
                ['Shipment On Time',$kpis['shipment_on_time'].'%','Completed shipment phases'],
                ['Overdue Tasks',$kpis['overdue_tasks'],'Open tasks past due']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="card metric">
                    <div class="metric-label"><?php echo e($metric[0]); ?></div>
                    <div class="metric-value"><?php echo e($metric[1]); ?></div>
                    <div class="metric-foot"><?php echo e($metric[2]); ?></div>
                    <div class="metric-icon">◎</div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/storage/framework/views/livewire/islands/d23ffadc-1.blade.php ENDPATH**/ ?>