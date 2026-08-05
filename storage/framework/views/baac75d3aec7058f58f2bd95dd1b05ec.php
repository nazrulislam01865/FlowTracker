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


<?php ($metrics = $this->metrics); ?>
        <div class="metrics ft-auto-metrics">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                ['Active Jobs',$metrics['activeJobs'],'Across all active phases'],
                ['Needs Attention',$metrics['riskJobs'],'Risk, delay or blocker'],
                ['Overdue Tasks',$metrics['overdueTasks'],'Require immediate update'],
                ['Pending Approvals',$metrics['pendingApprovals'],'Client or internal'],
                ['Shipping Now',$metrics['shipping'],'Currently in a shipping phase']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="card metric">
                    <div class="metric-label"><?php echo e($metric[0]); ?></div>
                    <div class="metric-value"><?php echo e($metric[1]); ?></div>
                    <div class="metric-foot"><?php echo e($metric[2]); ?></div>
                    <div class="metric-icon">◎</div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/storage/framework/views/livewire/islands/a716e027-1.blade.php ENDPATH**/ ?>