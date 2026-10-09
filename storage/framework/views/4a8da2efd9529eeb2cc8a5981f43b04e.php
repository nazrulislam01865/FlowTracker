<?php $__env->startSection('title', 'Select Your Order — STEP PROMO'); ?>

<?php $__env->startSection('content'); ?>
<div class="ft-lookup-layout" style="max-width:760px;margin:0 auto;display:block;">
    <div class="ft-lookup-card">
        <h1 class="ft-lookup-title">Matching orders found</h1>
        <p class="ft-lookup-intro">Multiple orders matched your search. Select the order you wish to track:</p>

        <div class="ft-disambiguate-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('order.track.show', ['token' => $order->ensureTrackingToken()])); ?>" class="ft-disambiguate-item">
                    <div>
                        <div style="font-weight:750;font-size:15px;color:#0f172a;margin-bottom:2px;">
                            <?php echo e($order->displayOrderNumber() ?: ($order->job_number ?: $order->order_number)); ?>

                        </div>
                        <div style="font-size:12.5px;color:#64748b;">
                            Reference: <?php echo e($order->reference_number ?: ($order->job_number ?: $order->order_number)); ?> · Placed on <?php echo e($order->created_at ? $order->created_at->format('M d, Y') : 'Recent'); ?>

                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;color:#007a64;font-size:13px;font-weight:600;">
                        <span>Track order</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div style="margin-top:20px;text-align:center;">
            <a href="<?php echo e(route('order.track')); ?>" class="ft-track-another-link">&larr; Back to search</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tracking.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/tracking/multiple.blade.php ENDPATH**/ ?>