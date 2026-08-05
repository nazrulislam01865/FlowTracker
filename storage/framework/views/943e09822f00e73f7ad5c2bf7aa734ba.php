<div class="ft-lazy-dashboard" aria-busy="true" aria-label="Loading <?php echo e($title); ?>">
    <div class="ft-lazy-head">
        <div>
            <h1><?php echo e($title); ?></h1>
            <p>Loading the latest workspace data</p>
        </div>
    </div>

    <div class="ft-lazy-metrics">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="ft-lazy-card ft-lazy-metric-card">
                <span class="ft-lazy-circle"></span>
                <span class="ft-lazy-copy">
                    <i class="ft-lazy-line short"></i>
                    <i class="ft-lazy-line medium"></i>
                </span>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <div class="ft-lazy-dashboard-grid">
        <div class="ft-lazy-card ft-lazy-chart-card">
            <div class="ft-lazy-section-head"><span class="ft-lazy-line medium"></span><span class="ft-lazy-line tiny"></span></div>
            <div class="ft-lazy-chart">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [45,72,51,60,36,69,40]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $height): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <span style="height:<?php echo e($height); ?>%"></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
        <div class="ft-lazy-card ft-lazy-attention-card">
            <div class="ft-lazy-section-head"><span class="ft-lazy-line medium"></span><span class="ft-lazy-line tiny"></span></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="ft-lazy-list-row"><span class="ft-lazy-circle small"></span><span class="ft-lazy-copy"><i class="ft-lazy-line medium"></i><i class="ft-lazy-line long"></i></span><i class="ft-lazy-line tiny"></i></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <div class="ft-lazy-card ft-lazy-table-card">
        <div class="ft-lazy-section-head"><span class="ft-lazy-line medium"></span></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="ft-lazy-table-row">
                <span class="ft-lazy-circle small"></span>
                <span class="ft-lazy-line medium"></span>
                <span class="ft-lazy-line short"></span>
                <span class="ft-lazy-line short"></span>
                <span class="ft-lazy-line medium"></span>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <div class="ft-workspace-loader" role="status" aria-live="polite">
        <div class="ft-workspace-loader-title"><span class="ft-lazy-spinner"></span><strong>Preparing <?php echo e($title); ?></strong></div>
        <div class="ft-workspace-step done"><span>✓</span><b>Account and permissions ready</b></div>
        <div class="ft-workspace-step active"><span class="ft-lazy-spinner small"></span><b>Loading current records</b></div>
        <div class="ft-workspace-step"><span class="dot"></span><b>Preparing page controls</b></div>
        <div class="ft-workspace-progress"><span></span></div>
        <small>This usually takes only a few seconds.</small>
        <div class="ft-workspace-tip">FlowTrack loads each page only when it is opened.</div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/shared/page-placeholder.blade.php ENDPATH**/ ?>