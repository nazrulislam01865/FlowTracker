<?php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $masterData = app(\App\Services\MasterDataService::class);
    $canCreateOrder = auth()->user()->canAccess('jobs.create');
    $canCreateClient = auth()->user()->canModule('clients', 'create');
    $canCreateInquiry = auth()->user()->canModule('inquiries', 'create');
    $inquiryFlag = static function ($inquiry) use ($today): array {
        $task = $inquiry->currentTask;
        $status = strtolower((string) ($task?->status ?: $inquiry->status));
        $due = $task?->due_date;

        if ($due && $due->lt($today)) return ['Overdue', 'red'];
        if ($due && $due->isSameDay($today)) return ['Due today', 'amber'];
        if (str_contains($status, 'waiting for client')) return ['Client wait', 'amber'];
        if (str_contains($status, 'waiting for supplier')) return ['Supplier wait', 'amber'];
        if (str_contains($status, 'hold')) return ['On hold', 'amber'];
        return ['On track', 'green'];
    };
    $inquiryStatusTone = static function (?string $status): string {
        $value = strtolower((string) $status);
        if (str_contains($value, 'wait') || str_contains($value, 'hold')) return 'amber';
        if (str_contains($value, 'progress')) return 'blue';
        if (str_contains($value, 'ready')) return 'green';
        return '';
    };
?>

<div class="ft-dashboard-prototype">
    <div class="ft-heading">
        <div class="ft-heading-copy">
            <h1>Management Dashboard</h1>
            <p><?php echo e($today->format('l, F j')); ?></p>
        </div>
        <nav class="ft-quick-actions" aria-label="Quick actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateOrder): ?>
                <a class="ft-action primary" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate><span class="ft-action-icon">+</span>Create Order</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateInquiry): ?>
                <a class="ft-action" href="<?php echo e(route('inquiries.index', ['create' => 1])); ?>" wire:navigate><span class="ft-action-icon">+</span>Create Inquiry</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateClient): ?>
                <a class="ft-action" href="<?php echo e(route('clients.index', ['create' => 1])); ?>" wire:navigate><span class="ft-action-icon">+</span>Add Client</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </div>

    <nav class="ft-page-tabs" aria-label="Dashboard views">
        <span class="ft-page-tab active">Dashboard</span>
    </nav>

    <section class="ft-kpis" aria-label="Key metrics">
        <a class="ft-kpi" href="<?php echo e(route('jobs.index')); ?>" wire:navigate><span class="ft-kpi-label">Active Orders <i class="ft-kpi-icon">◎</i></span><strong class="ft-kpi-value"><?php echo e($metrics['activeJobs']); ?></strong><span class="ft-kpi-foot">Across all active phases</span></a>
        <a class="ft-kpi" href="<?php echo e(route('all-tasks')); ?>" wire:navigate><span class="ft-kpi-label">Needs Attention <i class="ft-kpi-icon">!</i></span><strong class="ft-kpi-value"><?php echo e($metrics['needsAttention']); ?></strong><span class="ft-kpi-foot">Risk, delay or blocker</span></a>
        <a class="ft-kpi" href="<?php echo e(route('all-tasks')); ?>" wire:navigate><span class="ft-kpi-label">Overdue Tasks <i class="ft-kpi-icon">◷</i></span><strong class="ft-kpi-value"><?php echo e($metrics['overdueTasks']); ?></strong><span class="ft-kpi-foot">Require immediate update</span></a>
        <a class="ft-kpi" href="<?php echo e(route('clients.index')); ?>" wire:navigate><span class="ft-kpi-label">Active Clients <i class="ft-kpi-icon">♙</i></span><strong class="ft-kpi-value"><?php echo e($metrics['activeClients']); ?></strong><span class="ft-kpi-foot">Current active client records</span></a>
        <a class="ft-kpi" href="<?php echo e(route('inquiries.index')); ?>" wire:navigate aria-label="Open Enquiries"><span class="ft-kpi-label">Open Enquiries <i class="ft-kpi-icon">?</i></span><strong class="ft-kpi-value"><?php echo e($metrics['openInquiries']); ?></strong><span class="ft-kpi-foot">Current open inquiry records</span></a>
        <a class="ft-kpi" href="<?php echo e(route('notifications')); ?>" wire:navigate><span class="ft-kpi-label">Tagged Comments <i class="ft-kpi-icon">@</i></span><strong class="ft-kpi-value"><?php echo e($metrics['taggedComments']); ?></strong><span class="ft-kpi-foot"><?php echo e($administratorView ? 'Unread tagged comments across FlowTrack' : 'Unread mentions for you'); ?></span></a>
    </section>

    <div class="ft-grid">
        <section class="ft-panel" id="inquiries">
            <div class="ft-panel-head">
                <div><h2 class="ft-panel-title">Open enquiries</h2><div class="ft-panel-note">Pre-job opportunities, ownership, quotation progress and follow-up flags</div></div>
                <a class="ft-link" href="<?php echo e(route('inquiries.index')); ?>" wire:navigate>View all enquiries</a>
            </div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:17%"><col style="width:25%"><col style="width:20%"><col style="width:18%"><col style="width:12%"><col style="width:8%"></colgroup>
                    <thead><tr><th>Inquiry ID</th><th>Client</th><th>Assignee Name</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentInquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                // The dashboard Assignee column represents the Inquiry's own assignee/owner.
                                // Task assignees are deliberately not used here because they can differ per task.
                                $assignee = $inquiry->owner;
                                [$flagLabel, $flagTone] = $inquiryFlag($inquiry);
                                $displayStatus = $inquiry->status;
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-inquiry-'.e($inquiry->id).''; ?>wire:key="dashboard-inquiry-<?php echo e($inquiry->id); ?>">
                                <td data-label="Inquiry ID"><a class="ft-text-link" href="<?php echo e(route('inquiries.index', ['open' => $inquiry->id])); ?>" wire:navigate><?php echo e($inquiry->inquiry_number); ?></a><span class="ft-ref ft-cell-clip"><?php echo e($inquiry->subject); ?></span></td>
                                <td data-label="Client"><span class="ft-client-name-with-logo"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $inquiry->client,'name' => $inquiry->client?->name ?: 'Client','size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->client?->name ?: 'Client'),'size' => 22]); ?>
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
<?php endif; ?><span class="ft-cell-clip"><?php echo e($inquiry->client?->name ?? 'No client'); ?></span></span></td>
                                <td data-label="Assignee Name">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignee): ?>
                                        <span class="ft-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $assignee,'name' => $assignee->name,'size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assignee),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assignee->name),'size' => 22]); ?>
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
<?php endif; ?><span class="ft-cell-clip"><?php echo e($assignee->name); ?></span></span>
                                    <?php else: ?>
                                        <span class="ft-cell-clip">Unassigned</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <?php
                                    $displayStatusColor = $masterData->displayColorFor('inquiry_status', $displayStatus ?: 'Ready');
                                ?>
                                <td data-label="Status"><span class="ft-pill <?php echo e($displayStatusColor ? 'ft-master-color' : $inquiryStatusTone($displayStatus)); ?>" style="<?php echo e(\App\Support\MasterColor::style($displayStatusColor)); ?>"><?php echo e($displayStatus ?: 'Ready'); ?></span></td>
                                <td data-label="Flag"><span class="ft-flag <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td data-label="View"><a class="ft-view" href="<?php echo e(route('inquiries.index', ['open' => $inquiry->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="6">No open inquiries.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-primary">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.tagged-comments', ['lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1781049492-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Operational health</h2><div class="ft-panel-note">Current job health and task distribution based on task flags</div></div></div>
            <div class="ft-analytics">
                <div class="ft-health">
                    <div class="ft-health-content">
                        <div class="ft-donut" style="background:conic-gradient(#2eb67d 0 <?php echo e($operationalHealth['healthyPct']); ?>%, #f2b84b <?php echo e($operationalHealth['healthyPct']); ?>% <?php echo e($operationalHealth['riskStart']); ?>%, #ed5b5b <?php echo e($operationalHealth['riskStart']); ?>% 100%)"><div class="ft-donut-value"><?php echo e($operationalHealth['totalJobs']); ?><small>active jobs</small></div></div>
                        <div class="ft-health-list">
                            <div class="ft-health-row"><span><i></i>Healthy</span><b><?php echo e($operationalHealth['healthy']); ?></b></div>
                            <div class="ft-health-row"><span><i></i>Watch</span><b><?php echo e($operationalHealth['watch']); ?></b></div>
                            <div class="ft-health-row"><span><i></i>At risk</span><b><?php echo e($operationalHealth['atRisk']); ?></b></div>
                        </div>
                    </div>
                </div>
                <div class="ft-flag-mix">
                    <div class="ft-mix-summary"><span>Task mix by flag</span><span><strong><?php echo e($operationalHealth['flaggedTotal']); ?></strong> flagged tasks</span></div>
                    <div class="ft-mix-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $operationalHealth['flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-mix-row"><a href="<?php echo e(route('all-tasks')); ?>" wire:navigate><?php echo e($flag['label']); ?></a><i class="ft-mix-track"><span class="ft-mix-fill <?php echo e($flag['tone']); ?>" style="width:<?php echo e($flag['width']); ?>%"></span></i><b><?php echo e($flag['count']); ?></b></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.secondary', ['lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1781049492-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/dashboard/index.blade.php ENDPATH**/ ?>