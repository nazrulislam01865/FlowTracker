<?php
    $flowRows = collect($flowDistribution[$flowTab] ?? []);
    $flowMax = max(1, (int) $flowRows->max('count'));
    $flowRows = $flowRows->map(static function (array $row) use ($flowMax): array {
        $count = (int) ($row['count'] ?? 0);
        $scopeText = trim((string) ($row['scope_text'] ?? ''));

        return [
            'label' => (string) ($row['label'] ?? 'Unassigned'),
            'short_label' => (string) ($row['short_label'] ?? $row['label'] ?? 'Unassigned'),
            'color' => $row['color'] ?? null,
            'count' => $count,
            'width' => $count > 0 ? max(2, (int) round(($count / $flowMax) * 100)) : 0,
            'scope_text' => $scopeText,
            'scope_label' => trim((string) ($row['scope_label'] ?? '')),
            'is_mismatch' => (bool) ($row['is_mismatch'] ?? false),
        ];
    })->values();
    $selectedTaskStatusDistribution = $taskStatusDistribution[$taskStatusTab] ?? ['total' => 0, 'rows' => []];
    $statusRows = collect($selectedTaskStatusDistribution['rows'] ?? [])->values();
    $statusTotal = max(0, (int) ($selectedTaskStatusDistribution['total'] ?? 0));
    $cursor = 0.0;
    $gradientSegments = [];
    foreach ($statusRows as $row) {
        $count = max(0, (int) ($row['count'] ?? 0));
        if ($count <= 0 || $statusTotal <= 0) continue;
        $start = $cursor;
        $cursor += ($count / $statusTotal) * 100;
        $color = (string) ($row['color'] ?? '#64748B');
        $gradientSegments[] = $color.' '.$start.'% '.$cursor.'%';
    }
    if ($cursor < 100) $gradientSegments[] = '#edf2f5 '.$cursor.'% 100%';
    $donutBackground = $statusTotal > 0 ? 'conic-gradient('.implode(',', $gradientSegments).')' : '#edf2f5';
?>
    <section class="ft-mgmt-grid ft-mgmt-dashboard-pair-grid" data-dashboard-panel="distribution">
        <article class="ft-mgmt-panel ft-mgmt-flow-prototype">
            <div class="ft-mgmt-panel-head">
                <div><h2>Work moving through FlowTrack</h2><p>Active records grouped by configured workflow phase. Client-specific phase differences are labelled.</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setFlowTab('orders')" class="ft-mgmt-tab <?php echo e($flowTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                    <button type="button" wire:click="setFlowTab('inquiries')" class="ft-mgmt-tab <?php echo e($flowTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body">
                <div class="ft-mgmt-flow-bars">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $flowRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-mgmt-flow-row">
                            <div class="ft-mgmt-flow-label-wrap" title="<?php echo e($row['label']); ?><?php echo e($row['scope_text'] !== '' ? ' — '.$row['scope_text'] : ''); ?>">
                                <span class="ft-mgmt-flow-label"><?php echo e($row['short_label'] ?? $row['label']); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_mismatch'] && $row['scope_text'] !== ''): ?>
                                    <span class="ft-mgmt-flow-scope"><?php echo e($row['scope_text']); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="ft-mgmt-track">
                                <span class="ft-mgmt-fill ft-phase-fill <?php echo e((int) $row['count'] === 0 ? 'is-empty' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($row['color'] ?? null)); ?>width:<?php echo e($row['width']); ?>%"></span>
                            </div>
                            <span class="ft-mgmt-flow-value"><?php echo e($row['count']); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-mgmt-empty">No active workflow data.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Task status distribution</h2><p>Current <?php echo e($taskStatusTab === 'orders' ? 'Order' : 'Inquiry'); ?> task status from Master Data</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setTaskStatusTab('orders')" class="ft-mgmt-tab <?php echo e($taskStatusTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                    <button type="button" wire:click="setTaskStatusTab('inquiries')" class="ft-mgmt-tab <?php echo e($taskStatusTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body ft-mgmt-status-layout">
                <div class="ft-mgmt-donut" style="background:<?php echo e($donutBackground); ?>"><div class="ft-mgmt-donut-center"><strong><?php echo e($statusTotal); ?></strong><span>tasks</span></div></div>
                <div class="ft-mgmt-legend">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $statusRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('my-work', ['source' => $taskStatusTab, 'status' => $row['label']])); ?>" wire:navigate title="View My Tasks filtered by <?php echo e($row['label']); ?>"><span class="dot" style="background:<?php echo e($row['color']); ?>"></span><?php echo e($row['label']); ?></a><b><?php echo e($row['count']); ?></b>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <span class="ft-mgmt-sub">No active task statuses.</span><b>0</b>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </article>
    </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/distribution-panel.blade.php ENDPATH**/ ?>