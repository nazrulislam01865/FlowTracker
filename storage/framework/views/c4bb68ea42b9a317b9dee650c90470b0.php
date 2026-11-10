        <article class="ft-mgmt-panel ft-mgmt-client-panel" data-dashboard-panel="portfolio">
            <div class="ft-mgmt-panel-head"><div><h2>Client portfolio</h2><p>Activity and completion in the selected dashboard period</p></div><a class="ft-mgmt-link" href="<?php echo e(route('clients.index')); ?>" wire:navigate>All clients</a></div>
            <div class="ft-mgmt-panel-body ft-mgmt-client-portfolio-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientPortfolio->isNotEmpty()): ?>
                    <div class="ft-mgmt-client-head" aria-hidden="true">
                        <span>Client</span>
                        <span>Inquiries</span>
                        <span>Orders</span>
                        <span>Created vs completed</span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $clientPortfolio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolioClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $inquiries = (int) ($portfolioClient->inquiries_count ?? 0);
                        $orders = (int) ($portfolioClient->orders_count ?? 0);
                        $created = (int) ($portfolioClient->total_records_count ?? ($inquiries + $orders));
                        $completed = (int) ($portfolioClient->completed_records_count ?? 0);
                        $completion = $created > 0 ? min(100, max(0, (int) round(($completed / $created) * 100))) : 0;
                        $attention = (int) ($portfolioClient->attention_items_count ?? 0);
                    ?>
                    <div class="ft-mgmt-client-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-client-'.e($portfolioClient->id).''; ?>wire:key="mgmt-client-<?php echo e($portfolioClient->id); ?>">
                        <div class="ft-mgmt-client-name">
                            <span class="ft-mgmt-client-logo"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $portfolioClient,'name' => $portfolioClient->name,'size' => 38]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portfolioClient),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portfolioClient->name),'size' => 38]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?></span>
                            <div class="ft-mgmt-client-copy">
                                <strong><?php echo e($portfolioClient->name); ?></strong>
                                <span><?php echo e($attention); ?> attention item<?php echo e($attention === 1 ? '' : 's'); ?></span>
                            </div>
                        </div>
                        <b class="ft-mgmt-client-number"><?php echo e($inquiries); ?></b>
                        <b class="ft-mgmt-client-number"><?php echo e($orders); ?></b>
                        <div class="ft-mgmt-client-completion">
                            <div class="ft-mgmt-client-progress"><span style="width:<?php echo e($completion); ?>%"></span></div>
                            <div><?php echo e($completed); ?> of <?php echo e($created); ?> completed · <?php echo e($completion); ?>%</div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-mgmt-empty">No client portfolio data matches the current filters.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/portfolio-panel.blade.php ENDPATH**/ ?>