        <article class="ft-mgmt-panel ft-mgmt-catalogue-side ft-mgmt-catalogue-prototype ft-mgmt-dashboard-half-panel" data-dashboard-panel="catalogue">
    <div class="ft-mgmt-dashboard-panel-swap" style="display:contents" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-catalogue-content'; ?>wire:key="dashboard-catalogue-content">
            <div class="ft-mgmt-catalogue-head">
                <div class="ft-mgmt-catalogue-title">
                    <h2>Catalogue readiness</h2>
                    <p>Product data, classification, availability and document coverage</p>
                </div>
                <div class="ft-mgmt-catalogue-actions">
                    <span class="ft-mgmt-catalogue-ready"><?php echo e((int) ($catalogueReadiness['readyPercent'] ?? 0)); ?>% ready</span>
                    <a class="ft-mgmt-link" href="<?php echo e(route('master-data', ['group' => 'product'])); ?>" wire:navigate>Open catalogue</a>
                </div>
            </div>

            <div class="ft-mgmt-catalogue-body">
                <div class="ft-mgmt-catalogue-summary">
                    <span><strong><?php echo e(number_format((int) ($catalogueReadiness['activeProducts'] ?? 0))); ?></strong> products</span>
                    <i aria-hidden="true"></i>
                    <span><?php echo e(number_format((int) ($catalogueReadiness['mainCategories'] ?? 0))); ?> main categories</span>
                    <i aria-hidden="true"></i>
                    <span><?php echo e(number_format((int) ($catalogueReadiness['activeSuppliers'] ?? 0))); ?> active <?php echo e((int) ($catalogueReadiness['activeSuppliers'] ?? 0) === 1 ? 'supplier' : 'suppliers'); ?></span>
                </div>

                <div class="ft-mgmt-catalogue-rows">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $catalogueReadiness['rows'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $tone = in_array(($row['tone'] ?? ''), ['amber', 'green', 'red', 'blue'], true)
                                ? $row['tone']
                                : 'green';
                        ?>
                        <div class="ft-mgmt-catalogue-row">
                            <div class="ft-mgmt-catalogue-label">
                                <span class="ft-mgmt-catalogue-dot <?php echo e($tone); ?>" aria-hidden="true"></span>
                                <strong><?php echo e($row['label']); ?></strong>
                            </div>
                            <div class="ft-mgmt-catalogue-track" aria-label="<?php echo e($row['label']); ?> <?php echo e((int) $row['value']); ?> percent">
                                <span class="ft-mgmt-catalogue-fill <?php echo e($tone); ?>" style="width:<?php echo e(max(0, min(100, (int) $row['value']))); ?>%"></span>
                            </div>
                            <div class="ft-mgmt-catalogue-value">
                                <strong><?php echo e((int) $row['value']); ?>%</strong>
                                <span><?php echo e($row['detail'] ?? ''); ?></span>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
            </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/catalogue-panel.blade.php ENDPATH**/ ?>