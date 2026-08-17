<?php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $masterData = app(\App\Services\MasterDataService::class);
    $taskFlagService = app(\App\Services\TaskFlagService::class);
    $inquiryService = app(\App\Services\InquiryService::class);
    $canCreateOrder = auth()->user()->canAccess('jobs.create');
    $canCreateClient = auth()->user()->canModule('clients', 'create');
    $canCreateInquiry = auth()->user()->canModule('inquiries', 'create');

    $badgeTone = static function (?string $value): string {
        $value = mb_strtolower(trim((string) $value));
        if (str_contains($value, 'overdue') || str_contains($value, 'attention') || str_contains($value, 'blocked') || str_contains($value, 'risk')) return 'red';
        if (str_contains($value, 'due') || str_contains($value, 'waiting') || str_contains($value, 'hold') || str_contains($value, 'payment')) return 'amber';
        if (str_contains($value, 'complete') || str_contains($value, 'track') || str_contains($value, 'healthy') || str_contains($value, 'ready')) return 'green';
        if (str_contains($value, 'artwork') || str_contains($value, 'review') || str_contains($value, 'revision')) return 'purple';
        if (str_contains($value, 'unassigned') || str_contains($value, 'no flag')) return 'gray';
        return 'blue';
    };

    $taskFlag = static function ($task) use ($today, $taskFlagService, $badgeTone): array {
        $label = $taskFlagService->labelForTask($task);
        if ($label) return [$label, $badgeTone($label)];
        if ($task->due_date && $task->due_date->lt($today)) return ['Overdue '.$task->due_date->diffInDays($today).'d', 'red'];
        if ($task->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        if ((bool) $task->needs_attention) return ['Needs attention', 'red'];
        return ['On track', 'green'];
    };

    $jobFlag = static function ($job) use ($taskFlagService, $badgeTone): array {
        if ((bool) ($job->attention_requested ?? false) || (bool) ($job->needs_attention ?? false)) return ['Needs attention', 'red'];
        $label = $taskFlagService->labelForOrder($job);
        if ($label) return [$label, $badgeTone($label)];
        $health = trim((string) ($job->health ?? ''));
        if ($health !== '' && !in_array(mb_strtolower($health), ['on track', 'healthy'], true)) return [$health, $badgeTone($health)];
        return ['On track', 'green'];
    };

    $inquiryFlag = static function ($inquiry) use ($today): array {
        $task = $inquiry->currentTask;
        $status = mb_strtolower((string) ($task?->status ?: $inquiry->status));
        if ($task?->needs_attention) return ['Needs attention', 'red'];
        if ($task?->due_date && $task->due_date->lt($today)) return ['Overdue', 'red'];
        if ($task?->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        if (str_contains($status, 'wait')) return ['Waiting', 'amber'];
        return ['On track', 'green'];
    };

    $orderTerminology = static function (?string $value): string {
        return preg_replace_callback('/\bjobs?\b/i', static function (array $match): string {
            return match ($match[0]) {
                'Jobs' => 'Orders', 'jobs' => 'orders', 'JOB' => 'ORDER', 'JOBS' => 'ORDERS',
                default => ctype_upper($match[0][0] ?? '') ? 'Order' : 'order',
            };
        }, (string) $value) ?: (string) $value;
    };

    $flowRows = collect($flowDistribution[$flowTab] ?? []);
    $flowMax = max(1, (int) $flowRows->max('count'));
    $flowRows = $flowRows->map(static function (array $row) use ($flowMax): array {
        $count = (int) ($row['count'] ?? 0);
        $scopeText = trim((string) ($row['scope_text'] ?? ''));

        return [
            'label' => (string) ($row['label'] ?? 'Unassigned'),
            'count' => $count,
            'width' => $count > 0 ? max(2, (int) round(($count / $flowMax) * 100)) : 0,
            'scope_text' => $scopeText,
            'scope_label' => trim((string) ($row['scope_label'] ?? '')),
            'is_mismatch' => (bool) ($row['is_mismatch'] ?? false),
        ];
    })->values();
    $selectedTaskStatusDistribution = $taskStatusDistribution[$taskStatusTab] ?? ['total' => 0, 'rows' => []];
    $statusRows = collect($selectedTaskStatusDistribution['rows'] ?? [])->filter(fn ($row) => (int) ($row['count'] ?? 0) > 0)->values();
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
    $statusOpenRoute = $taskStatusTab === 'orders' ? route('all-tasks') : route('inquiries.index');
?>

<?php if (isset($component)) { $__componentOriginalcfea599f97a0d6266449c21c198d875e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcfea599f97a0d6266449c21c198d875e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.management-theme','data' => ['class' => 'ft-mgmt-dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.management-theme'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-dashboard']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="ft-mgmt-page-head">
        <div>
            <h1>Management Dashboard</h1>
            <p>Live operational overview across inquiries, orders, tasks, clients and product data.</p>
        </div>
        <div class="ft-mgmt-head-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateOrder): ?><a class="ft-mgmt-btn primary" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate>＋ Create Order</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateInquiry): ?><a class="ft-mgmt-btn" href="<?php echo e(route('inquiries.index', ['create' => 1])); ?>" wire:navigate>＋ Create Inquiry</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateClient): ?><a class="ft-mgmt-btn" href="<?php echo e(route('clients.index', ['create' => 1])); ?>" wire:navigate>＋ Add Client</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <section class="ft-mgmt-control-bar" aria-label="Dashboard filters">
        <div class="ft-mgmt-range" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-range-control'; ?>wire:key="dashboard-range-control" wire:loading.class="is-loading" wire:target="setRange">
            <button type="button" wire:click="setRange(1)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="<?php echo e($rangeDays === 1 ? 'true' : 'false'); ?>" class="<?php echo e($rangeDays === 1 ? 'active' : ''); ?>">Today</button>
            <button type="button" wire:click="setRange(7)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="<?php echo e($rangeDays === 7 ? 'true' : 'false'); ?>" class="<?php echo e($rangeDays === 7 ? 'active' : ''); ?>">7 days</button>
            <button type="button" wire:click="setRange(30)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="<?php echo e($rangeDays === 30 ? 'true' : 'false'); ?>" class="<?php echo e($rangeDays === 30 ? 'active' : ''); ?>">30 days</button>
        </div>
        <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-mgmt-remote-filter ft-mgmt-client-filter','label' => 'Client','property' => 'clientFilter','type' => 'clients','context' => 'dashboard','action' => 'setDashboardFilter','value' => $clientFilter,'placeholder' => 'All clients','initialOptions' => $dashboardClientFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'dashboard-client-filter-'.e($clientFilter ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-remote-filter ft-mgmt-client-filter','label' => 'Client','property' => 'clientFilter','type' => 'clients','context' => 'dashboard','action' => 'setDashboardFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilter),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dashboardClientFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'dashboard-client-filter-'.e($clientFilter ?: 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-mgmt-remote-filter ft-mgmt-team-filter','label' => 'Team','property' => 'teamFilter','type' => 'departments','context' => 'dashboard','action' => 'setDashboardFilter','value' => $teamFilter,'placeholder' => 'All teams','initialOptions' => $dashboardTeamFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'dashboard-team-filter-'.e($teamFilter ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-remote-filter ft-mgmt-team-filter','label' => 'Team','property' => 'teamFilter','type' => 'departments','context' => 'dashboard','action' => 'setDashboardFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($teamFilter),'placeholder' => 'All teams','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dashboardTeamFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'dashboard-team-filter-'.e($teamFilter ?: 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
        <span class="ft-mgmt-last-updated"><span class="ft-mgmt-live-dot"></span>Live · updated now</span>
        <input class="ft-mgmt-search" wire:model.live.debounce.300ms="search" type="search" placeholder="Search orders, inquiries or tasks" aria-label="Search dashboard">
    </section>

    <section class="ft-mgmt-kpis" aria-label="Key metrics">
        <a class="ft-mgmt-kpi" href="<?php echo e(route('jobs.index')); ?>" wire:navigate><i class="ft-mgmt-kpi-icon">▣</i><span class="ft-mgmt-kpi-label">Active orders</span><strong class="ft-mgmt-kpi-value"><?php echo e($metrics['activeJobs']); ?></strong><span class="ft-mgmt-kpi-meta">Across active workflow stages</span></a>
        <a class="ft-mgmt-kpi" href="<?php echo e(route('all-tasks')); ?>" wire:navigate><i class="ft-mgmt-kpi-icon">!</i><span class="ft-mgmt-kpi-label">Needs attention</span><strong class="ft-mgmt-kpi-value"><?php echo e($metrics['needsAttention']); ?></strong><span class="ft-mgmt-kpi-meta">Risk, delay or blocker</span></a>
        <a class="ft-mgmt-kpi" href="<?php echo e(route('all-tasks')); ?>" wire:navigate><i class="ft-mgmt-kpi-icon">◷</i><span class="ft-mgmt-kpi-label">Overdue tasks</span><strong class="ft-mgmt-kpi-value"><?php echo e($metrics['overdueTasks']); ?></strong><span class="ft-mgmt-kpi-meta">Require immediate update</span></a>
        <a class="ft-mgmt-kpi" href="<?php echo e(route('inquiries.index')); ?>" wire:navigate><i class="ft-mgmt-kpi-icon">?</i><span class="ft-mgmt-kpi-label">Open inquiries</span><strong class="ft-mgmt-kpi-value"><?php echo e($metrics['openInquiries']); ?></strong><span class="ft-mgmt-kpi-meta">Current open inquiry records</span></a>
        <a class="ft-mgmt-kpi" href="<?php echo e(route('clients.index')); ?>" wire:navigate><i class="ft-mgmt-kpi-icon">△</i><span class="ft-mgmt-kpi-label">Active clients</span><strong class="ft-mgmt-kpi-value"><?php echo e($metrics['activeClients']); ?></strong><span class="ft-mgmt-kpi-meta">Current active client records</span></a>
        <a class="ft-mgmt-kpi" href="<?php echo e(route('master-data', ['group' => 'product'])); ?>" wire:navigate><i class="ft-mgmt-kpi-icon">◇</i><span class="ft-mgmt-kpi-label">Active products</span><strong class="ft-mgmt-kpi-value"><?php echo e(number_format($metrics['activeProducts'] ?? 0)); ?></strong><span class="ft-mgmt-kpi-meta">Available in product catalogue</span></a>
    </section>

    <section class="ft-mgmt-grid">
        <article class="ft-mgmt-panel">
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
                                <span class="ft-mgmt-flow-label"><?php echo e($row['label']); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_mismatch'] && $row['scope_text'] !== ''): ?>
                                    <span class="ft-mgmt-flow-scope"><?php echo e($row['scope_text']); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="ft-mgmt-track"><span class="ft-mgmt-fill <?php echo e($row['is_mismatch'] ? 'amber' : ''); ?>" style="width:<?php echo e($row['width']); ?>%"></span></div>
                            <span class="ft-mgmt-flow-value"><?php echo e($row['count']); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-mgmt-empty">No active workflow data.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flowRows->isNotEmpty()): ?>
                    <div class="ft-mgmt-phase-strip" aria-label="Configured workflow phases">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $flowRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <span
                                class="ft-mgmt-phase <?php echo e($row['count'] > 0 ? 'active' : ''); ?> <?php echo e($row['is_mismatch'] ? 'client-specific' : ''); ?>"
                                title="<?php echo e($row['label']); ?>: <?php echo e($row['count']); ?><?php echo e($row['scope_text'] !== '' ? ' — '.$row['scope_text'] : ''); ?>"
                            ></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                    <div class="ft-mgmt-phase-tip">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $flowRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <span title="<?php echo e($row['label']); ?><?php echo e($row['scope_text'] !== '' ? ' — '.$row['scope_text'] : ''); ?>">
                                <?php echo e(\Illuminate\Support\Str::limit($row['label'], 18)); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_mismatch'] && $row['scope_label'] !== ''): ?><small><?php echo e($row['scope_label']); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Needs attention</h2><p>Exceptions ranked by urgency and business impact</p></div><a class="ft-mgmt-link" href="<?php echo e(route('all-tasks')); ?>" wire:navigate>View all</a></div>
            <div class="ft-mgmt-panel-body">
                <div class="ft-mgmt-attention-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attentionTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            [$flagLabel, $flagTone] = $taskFlag($task);
                        ?>
                        <a class="ft-mgmt-attention" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-attention-'.e($task->id).''; ?>wire:key="mgmt-attention-<?php echo e($task->id); ?>">
                            <span class="ft-mgmt-severity <?php echo e($flagTone === 'red' ? '' : 'amber'); ?>"></span>
                            <span><strong><?php echo e($task->title); ?></strong><small><?php echo e($task->task_number); ?> · <?php echo e($task->job?->displayOrderNumber() ?? 'Order'); ?> · <?php echo e($task->assignee?->name ?? 'Unassigned'); ?></small></span>
                            <span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-mgmt-empty">No attention items match the current filters.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </article>
    </section>

    <section class="ft-mgmt-grid">
        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Task status distribution</h2><p>Current <?php echo e($taskStatusTab === 'orders' ? 'Order' : 'Inquiry'); ?> task status from Master Data</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setTaskStatusTab('orders')" class="ft-mgmt-tab <?php echo e($taskStatusTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                    <button type="button" wire:click="setTaskStatusTab('inquiries')" class="ft-mgmt-tab <?php echo e($taskStatusTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body ft-mgmt-status-layout">
                <div class="ft-mgmt-donut" style="background:<?php echo e($donutBackground); ?>"><div class="ft-mgmt-donut-center"><strong><?php echo e($statusTotal); ?></strong><span>active tasks</span></div></div>
                <div class="ft-mgmt-legend">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $statusRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e($statusOpenRoute); ?>" wire:navigate title="<?php echo e($row['configured'] ? 'Configured in Master Data' : 'Active legacy status'); ?>"><span class="dot" style="background:<?php echo e($row['color']); ?>"></span><?php echo e($row['label']); ?></a><b><?php echo e($row['count']); ?></b>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <span class="ft-mgmt-sub">No active task statuses.</span><b>0</b>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Client portfolio health</h2><p>Active work, inquiry volume and delivery performance</p></div><a class="ft-mgmt-link" href="<?php echo e(route('clients.index')); ?>" wire:navigate>All clients</a></div>
            <div class="ft-mgmt-panel-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $clientPortfolio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolioClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $openTasks = (int) ($portfolioClient->open_tasks_count ?? 0);
                        $overdueTasks = (int) ($portfolioClient->overdue_tasks_count ?? 0);
                        $risk = (int) ($portfolioClient->at_risk_jobs_count ?? 0);
                        $onTime = $openTasks > 0 ? max(0, (int) round((($openTasks - $overdueTasks) / $openTasks) * 100)) : 100;
                    ?>
                    <div class="ft-mgmt-client-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-client-'.e($portfolioClient->id).''; ?>wire:key="mgmt-client-<?php echo e($portfolioClient->id); ?>">
                        <div class="ft-mgmt-client-name"><span class="ft-mgmt-client-logo"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $portfolioClient,'name' => $portfolioClient->name,'size' => 29]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portfolioClient),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portfolioClient->name),'size' => 29]); ?>
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
<?php endif; ?></span><div><?php echo e($portfolioClient->name); ?><div class="ft-mgmt-sub"><?php echo e($risk ? $risk.' attention item'.($risk > 1 ? 's' : '') : 'Healthy portfolio'); ?></div></div></div>
                        <b><?php echo e((int) ($portfolioClient->active_jobs_count ?? 0)); ?></b>
                        <b><?php echo e((int) ($portfolioClient->open_inquiries_count ?? 0)); ?></b>
                        <div><div class="ft-mgmt-health"><span style="width:<?php echo e($onTime); ?>%"></span></div><div class="ft-mgmt-sub"><?php echo e($onTime); ?>% on time</div></div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-mgmt-empty">No client portfolio data matches the current filters.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </article>
    </section>

    

    <section class="ft-mgmt-panel" style="margin-bottom:14px">
        <div class="ft-mgmt-panel-head">
            <div><h2>Priority work</h2><p>Top urgent Orders, Inquiries and Tasks ranked by attention, due date and priority</p></div>
            <div class="ft-mgmt-tabs">
                <button type="button" wire:click="setPriorityTab('orders')" class="ft-mgmt-tab <?php echo e($priorityTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                <button type="button" wire:click="setPriorityTab('inquiries')" class="ft-mgmt-tab <?php echo e($priorityTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                <button type="button" wire:click="setPriorityTab('tasks')" class="ft-mgmt-tab <?php echo e($priorityTab === 'tasks' ? 'active' : ''); ?>">Tasks</button>
            </div>
        </div>
        <div class="ft-mgmt-table-wrap">
            <table class="ft-mgmt-table">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priorityTab === 'orders'): ?>
                    <thead><tr><th>Order</th><th>Client</th><th>Stage</th><th>Progress</th><th>Attention</th><th>Owner</th><th>Delivery</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $priorityJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                [$flagLabel, $flagTone] = $jobFlag($job);
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-priority-job-'.e($job->id).''; ?>wire:key="mgmt-priority-job-<?php echo e($job->id); ?>">
                                <td><a class="ft-mgmt-primary-text" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a><div class="ft-mgmt-sub"><?php echo e($job->title); ?></div></td>
                                <td><?php echo e($job->client?->name ?? '—'); ?></td><td><span class="ft-mgmt-badge <?php echo e($badgeTone($job->phase?->short_name)); ?>"><?php echo e($job->phase?->short_name ?? $job->phase?->name ?? 'Unassigned'); ?></span></td>
                                <td><div class="ft-mgmt-progress-cell"><div class="ft-mgmt-track"><span class="ft-mgmt-fill" style="width:<?php echo e(min(100, max(0, (int) $job->progress))); ?>%"></span></div><b><?php echo e((int) $job->progress); ?>%</b></div></td>
                                <td><span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->owner): ?><span class="ft-mgmt-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $job->owner,'name' => $job->owner->name,'size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner->name),'size' => 27]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php echo e($job->owner->name); ?></span><?php else: ?> Unassigned <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><?php echo e($job->delivery_date?->format('M j') ?? '—'); ?></td><td><a class="ft-mgmt-tiny-action" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="8" class="ft-mgmt-empty">No matching orders found.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                <?php elseif($priorityTab === 'inquiries'): ?>
                    <thead><tr><th>Inquiry</th><th>Client</th><th>Current task</th><th>Status</th><th>Flag</th><th>Owner</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $priorityInquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                [$flagLabel, $flagTone] = $inquiryFlag($inquiry);
                                $statusColor = $inquiryService->inquiryStatusColor($inquiry->status ?: 'To do', (string) ($inquiry->currentTask?->status ?: ''));
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-priority-inquiry-'.e($inquiry->id).''; ?>wire:key="mgmt-priority-inquiry-<?php echo e($inquiry->id); ?>">
                                <td><a class="ft-mgmt-primary-text" href="<?php echo e(route('inquiries.index', ['open' => $inquiry->id])); ?>" wire:navigate><?php echo e($inquiry->inquiry_number); ?></a><div class="ft-mgmt-sub"><?php echo e($inquiry->subject); ?></div></td>
                                <td><?php echo e($inquiry->client?->name ?? '—'); ?></td><td><?php echo e($inquiry->currentTask?->title ?? 'No current task'); ?></td>
                                <td><span class="ft-mgmt-badge <?php echo e($badgeTone($inquiry->status)); ?>"><?php echo e($inquiry->status ?: 'To do'); ?></span></td><td><span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->owner): ?><span class="ft-mgmt-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $inquiry->owner,'name' => $inquiry->owner->name,'size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->owner),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->owner->name),'size' => 27]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php echo e($inquiry->owner->name); ?></span><?php else: ?> Unassigned <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><?php echo e($inquiry->currentTask?->due_date?->format('M j') ?? '—'); ?></td><td><a class="ft-mgmt-tiny-action" href="<?php echo e(route('inquiries.index', ['open' => $inquiry->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="8" class="ft-mgmt-empty">No matching inquiries found.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                <?php else: ?>
                    <thead><tr><th>Task</th><th>Order</th><th>Phase</th><th>Status</th><th>Attention</th><th>Assignee</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $priorityTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                            [$flagLabel, $flagTone] = $taskFlag($task);
                        ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-priority-task-'.e($task->id).''; ?>wire:key="mgmt-priority-task-<?php echo e($task->id); ?>">
                                <td><a class="ft-mgmt-primary-text" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a><div class="ft-mgmt-sub"><?php echo e($task->task_number); ?></div></td>
                                <td><?php echo e($task->job?->displayOrderNumber() ?? '—'); ?></td><td><?php echo e($task->phase?->short_name ?? $task->phase?->name ?? '—'); ?></td>
                                <td><span class="ft-mgmt-badge <?php echo e($badgeTone($task->status)); ?>"><?php echo e($task->status); ?></span></td><td><span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->assignee): ?><span class="ft-mgmt-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $task->assignee,'name' => $task->assignee->name,'size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee->name),'size' => 27]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php echo e($task->assignee->name); ?></span><?php else: ?> Unassigned <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><?php echo e($task->due_date?->format('M j') ?? '—'); ?></td><td><a class="ft-mgmt-tiny-action" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="8" class="ft-mgmt-empty">No matching tasks found.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>
    </section>

    <section class="ft-mgmt-grid">
        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head">
                <div><h2>Recent activity</h2><p>Latest changes from Orders, Inquiries and Tasks</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setActivityTab('all')" class="ft-mgmt-tab <?php echo e($activityTab === 'all' ? 'active' : ''); ?>">All</button>
                    <button type="button" wire:click="setActivityTab('orders')" class="ft-mgmt-tab <?php echo e($activityTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                    <button type="button" wire:click="setActivityTab('inquiries')" class="ft-mgmt-tab <?php echo e($activityTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                    <button type="button" wire:click="setActivityTab('tasks')" class="ft-mgmt-tab <?php echo e($activityTab === 'tasks' ? 'active' : ''); ?>">Tasks</button>
                </div>
            </div>
            <div class="ft-mgmt-panel-body"><div class="ft-mgmt-activity-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-activity" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-activity-'.e($activity->id).''; ?>wire:key="mgmt-activity-<?php echo e($activity->id); ?>">
                        <div class="ft-mgmt-activity-icon"><?php echo e(($activity->dashboard_kind ?? '') === 'tasks' ? '✓' : (($activity->dashboard_kind ?? '') === 'inquiries' ? '?' : '▣')); ?></div>
                        <div><strong><?php echo e($orderTerminology($activity->dashboard_title)); ?></strong><p><?php echo e($orderTerminology($activity->dashboard_detail)); ?></p></div>
                        <time><?php echo e($activity->created_at?->diffForHumans(short: true)); ?></time>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ft-mgmt-empty">No Order, Inquiry or Task changes match the selected period or filters.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></div>
        </article>

        <article class="ft-mgmt-panel">
            <div class="ft-mgmt-panel-head"><div><h2>Catalogue readiness</h2><p>Product, category, supplier and document coverage</p></div><a class="ft-mgmt-link" href="<?php echo e(route('master-data', ['group' => 'product'])); ?>" wire:navigate>Open catalogue</a></div>
            <div class="ft-mgmt-panel-body"><div class="ft-mgmt-flow-bars">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $catalogueReadiness['rows'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-mgmt-flow-row"><span class="ft-mgmt-flow-label"><?php echo e($row['label']); ?></span><div class="ft-mgmt-track"><span class="ft-mgmt-fill <?php echo e($index === 3 ? 'amber' : ''); ?>" style="width:<?php echo e($row['value']); ?>%"></span></div><span class="ft-mgmt-flow-value"><?php echo e($row['value']); ?>%</span></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div></div>
        </article>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcfea599f97a0d6266449c21c198d875e)): ?>
<?php $attributes = $__attributesOriginalcfea599f97a0d6266449c21c198d875e; ?>
<?php unset($__attributesOriginalcfea599f97a0d6266449c21c198d875e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcfea599f97a0d6266449c21c198d875e)): ?>
<?php $component = $__componentOriginalcfea599f97a0d6266449c21c198d875e; ?>
<?php unset($__componentOriginalcfea599f97a0d6266449c21c198d875e); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/dashboard/index.blade.php ENDPATH**/ ?>