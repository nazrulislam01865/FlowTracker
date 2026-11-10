<div class="ft-mgmt-dashboard-overview ft-mgmt-skeleton-overview" data-dashboard-skeleton-for="overview" aria-busy="true" role="status" aria-label="Loading Orders by workflow stage">
    <div class="ft-mgmt-dashboard-panel-swap" style="display:contents" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-overview-content'; ?>wire:key="dashboard-overview-content">
    <section class="ft-order-workflow-stage-overview" wire:init="loadDashboardPanel" aria-hidden="true">
        <div class="ft-order-workflow-stage-strip">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 7; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="ft-mgmt-skeleton-stage">
                    <span class="ft-mgmt-skeleton-line ft-mgmt-skeleton-kicker"></span>
                    <span class="ft-mgmt-skeleton-line ft-mgmt-skeleton-stage-title"></span>
                    <div class="ft-mgmt-skeleton-stage-count">
                        <span class="ft-mgmt-skeleton-line ft-mgmt-skeleton-short"></span>
                        <span class="ft-mgmt-skeleton-line ft-mgmt-skeleton-number"></span>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </section>
    <span class="sr-only">Loading workflow stages</span>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/overview-placeholder.blade.php ENDPATH**/ ?>