<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kind === 'distributionpanel'): ?>
    <section class="ft-mgmt-dashboard-distribution-shell ft-mgmt-dashboard-skeleton" data-dashboard-skeleton-for="distribution" role="status" aria-busy="true" aria-label="Loading workflow and task status distributions">
    <div class="ft-mgmt-grid ft-mgmt-dashboard-pair-grid ft-mgmt-dashboard-panel-swap" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-distribution-content'; ?>wire:key="dashboard-distribution-content">
        <article class="ft-mgmt-panel ft-mgmt-flow-prototype" wire:intersect.once="loadDashboardPanel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Work moving through FlowTrack</h2></div>
                <div class="ft-mgmt-skeleton-tabs" aria-hidden="true"><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span></div>
            </div>
            <div class="ft-mgmt-panel-body ft-mgmt-skeleton-body" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 6; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-skeleton-chart-row"><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </article>
        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Task status distribution</h2></div></div>
            <div class="ft-mgmt-panel-body ft-mgmt-skeleton-distribution" aria-hidden="true">
                <div class="ft-mgmt-skeleton-donut"></div>
                <div class="ft-mgmt-skeleton-legend">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <span class="ft-mgmt-skeleton-line"></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </article>
        <span class="sr-only">Loading dashboard distributions</span>
        </div>
</section>
<?php elseif($kind === 'cataloguepanel'): ?>
    <article class="ft-mgmt-panel ft-mgmt-catalogue-side ft-mgmt-catalogue-prototype ft-mgmt-dashboard-half-panel ft-mgmt-dashboard-skeleton" data-dashboard-skeleton-for="catalogue" role="status" aria-busy="true" aria-label="Loading Catalogue readiness">
    <div class="ft-mgmt-dashboard-panel-swap" style="display:contents" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-catalogue-content'; ?>wire:key="dashboard-catalogue-content">
        <div class="ft-mgmt-catalogue-head" wire:intersect.once="loadDashboardPanel"><div class="ft-mgmt-catalogue-title"><h2><?php echo e($heading); ?></h2></div></div>
        <div class="ft-mgmt-catalogue-body ft-mgmt-skeleton-body" aria-hidden="true">
            <div class="ft-mgmt-skeleton-catalogue-summary">
                <span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="ft-mgmt-skeleton-chart-row"><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <span class="sr-only">Loading Catalogue readiness</span>
        </div>
</article>
<?php else: ?>
    <article class="ft-mgmt-panel ft-mgmt-dashboard-skeleton <?php echo e(match($kind) {
        'prioritypanel' => 'ft-mgmt-panel-spaced',
        'attentionpanel' => 'ft-mgmt-attention-prototype ft-mgmt-attention-compact ft-mgmt-dashboard-half-panel',
        'teampanel' => 'ft-mgmt-team-panel ft-mgmt-panel-spaced',
        'portfoliopanel' => 'ft-mgmt-client-panel',
        'activitypanel' => 'ft-mgmt-recent-prototype',
        default => '',
    }); ?>" data-dashboard-skeleton-for="<?php echo e(str_replace('panel', '', $kind)); ?>" style="min-height:<?php echo e($height); ?>px" role="status" aria-busy="true" aria-label="Loading <?php echo e($heading); ?>">
    <div class="ft-mgmt-dashboard-panel-swap" style="display:contents" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-'.e(str_replace('panel', '', $kind)).'-content'; ?>wire:key="dashboard-<?php echo e(str_replace('panel', '', $kind)); ?>-content">
        <div class="ft-mgmt-panel-head" wire:intersect.once="loadDashboardPanel">
            <div><h2><?php echo e($heading); ?></h2></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($kind, ['prioritypanel', 'attentionpanel', 'activitypanel'], true)): ?>
                <div class="ft-mgmt-skeleton-tabs" aria-hidden="true">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < ($kind === 'attentionpanel' ? 2 : ($kind === 'activitypanel' ? 4 : 3)); $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <span class="ft-mgmt-skeleton-line"></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kind === 'prioritypanel'): ?>
            <div class="ft-mgmt-skeleton-table" aria-hidden="true">
                <div class="ft-mgmt-skeleton-table-row ft-mgmt-skeleton-table-head">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 7; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span class="ft-mgmt-skeleton-line"></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($row = 0; $row < 5; $row++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-skeleton-table-row">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 7; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span class="ft-mgmt-skeleton-line"></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php elseif($kind === 'teampanel'): ?>
            <div class="ft-mgmt-panel-body ft-mgmt-skeleton-team" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-skeleton-person">
                        <div class="ft-mgmt-skeleton-person-head"><span class="ft-mgmt-skeleton-circle"></span><span class="ft-mgmt-skeleton-line"></span></div>
                        <span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span>
                        <span class="ft-mgmt-skeleton-line ft-mgmt-skeleton-progress"></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php elseif($kind === 'portfoliopanel'): ?>
            <div class="ft-mgmt-panel-body ft-mgmt-skeleton-body" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-skeleton-portfolio-row"><span class="ft-mgmt-skeleton-circle"></span><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="ft-mgmt-panel-body ft-mgmt-skeleton-body" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < ($kind === 'activitypanel' ? 5 : 4); $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-skeleton-feed-row">
                        <span class="ft-mgmt-skeleton-circle"></span>
                        <div class="ft-mgmt-skeleton-feed-copy"><span class="ft-mgmt-skeleton-line"></span><span class="ft-mgmt-skeleton-line"></span></div>
                        <span class="ft-mgmt-skeleton-line ft-mgmt-skeleton-time"></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="sr-only">Loading <?php echo e($heading); ?></span>
        </div>
</article>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/panel-placeholder.blade.php ENDPATH**/ ?>