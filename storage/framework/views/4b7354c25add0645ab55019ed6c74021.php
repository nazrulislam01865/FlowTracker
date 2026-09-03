<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'jobs',
    'searchFilter' => '',
    'clientFilter' => '',
    'phaseFilter' => '',
    'assigneeFilter' => '',
    'ownerFilter' => null,
    'metrics' => [],
    'metricFilter' => '',
    'dateFrom' => '',
    'dateTo' => '',
    'importFilterId' => 0,
    'importFilterLabel' => '',
    'dateRangeEnabled' => false,
    'clientFilterOptions' => collect(),
    'phaseFilterOptions' => collect(),
    'assigneeFilterOptions' => collect(),
    'ownerFilterOptions' => collect(),
    'clearAction' => 'clearSearch',
    'clearFiltersAction' => null,
    'selectedOrderIds' => [],
    'showBulkDeleteConfirm' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'jobs',
    'searchFilter' => '',
    'clientFilter' => '',
    'phaseFilter' => '',
    'assigneeFilter' => '',
    'ownerFilter' => null,
    'metrics' => [],
    'metricFilter' => '',
    'dateFrom' => '',
    'dateTo' => '',
    'importFilterId' => 0,
    'importFilterLabel' => '',
    'dateRangeEnabled' => false,
    'clientFilterOptions' => collect(),
    'phaseFilterOptions' => collect(),
    'assigneeFilterOptions' => collect(),
    'ownerFilterOptions' => collect(),
    'clearAction' => 'clearSearch',
    'clearFiltersAction' => null,
    'selectedOrderIds' => [],
    'showBulkDeleteConfirm' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $masterData = app(\App\Services\MasterDataService::class);
    $tone = static function (?string $value): string {
        $value = (string) $value;
        if (preg_match('/delayed|issue|overdue|blocked|attention|critical/i', $value)) return 'red';
        if (preg_match('/risk|wait|reply|hold|revision|delay|due|urgent|high/i', $value)) return 'amber';
        if (preg_match('/track|ready|invoice|warehouse|shipping|completed/i', $value)) return 'green';
        if (preg_match('/artwork|sample|client/i', $value)) return 'purple';
        return 'blue';
    };
    $currentPage = $jobs->currentPage();
    $lastPage = $jobs->lastPage();
    $pageNumbers = collect(range(1, max(1, $lastPage)))
        ->filter(fn ($page) => $lastPage <= 7 || $page === 1 || $page === $lastPage || abs($page - $currentPage) <= 1)
        ->values();
    $usesOwnerFilter = $ownerFilter !== null;
    $peopleFilter = $usesOwnerFilter ? (string) $ownerFilter : (string) $assigneeFilter;
    $peopleFilterOptions = $usesOwnerFilter ? $ownerFilterOptions : $assigneeFilterOptions;
    $peopleProperty = $usesOwnerFilter ? 'owner' : 'assignee';
    $peopleContext = $usesOwnerFilter ? 'order-list-owner' : 'order-list';
    $peopleLabel = $usesOwnerFilter ? 'Owner' : 'Assignee';
    $peoplePlaceholder = $usesOwnerFilter ? 'All owner' : 'All assignees';
    $accessControl = app(\App\Services\AccessControlService::class);
    $canViewFinance = $accessControl->can(auth()->user(), 'finance', 'view');
    $canDeleteOrders = auth()->user()->canModule('jobs', 'delete');
    $orderExportQuery = array_filter([
        'search' => filled($searchFilter) ? $searchFilter : null,
        'client' => filled($clientFilter) ? $clientFilter : null,
        'phase' => filled($phaseFilter) ? $phaseFilter : null,
        'owner' => $usesOwnerFilter && filled($peopleFilter) ? $peopleFilter : null,
        'assignee' => ! $usesOwnerFilter && filled($peopleFilter) ? $peopleFilter : null,
        'metric' => filled($metricFilter) ? $metricFilter : null,
        'date_from' => filled($dateFrom) ? $dateFrom : null,
        'date_to' => filled($dateTo) ? $dateTo : null,
        'import' => $importFilterId ? $importFilterId : null,
    ], static fn ($value) => $value !== null && $value !== '');
    $selectedOrderIdSet = collect($selectedOrderIds)->map(fn ($id) => (int) $id)->filter()->unique();
    $visibleOrderIds = collect($jobs->items())->pluck('id')->map(fn ($id) => (int) $id)->values();
    $selectedOrderCount = $selectedOrderIdSet->count();
    $selectedVisibleCount = $visibleOrderIds->filter(fn ($id) => $selectedOrderIdSet->contains($id))->count();
    $allVisibleOrdersSelected = $visibleOrderIds->isNotEmpty() && $selectedVisibleCount === $visibleOrderIds->count();
    $someVisibleOrdersSelected = $selectedVisibleCount > 0 && ! $allVisibleOrdersSelected;
?>

<div id="ft-orders-page" class="ft-orders-prototype">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="ft-list-flash" role="status"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-page-head">
        <div><h1>Orders</h1><p>Fast access to every active and completed order</p></div>
        <div class="ft-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('reports', 'export')): ?>
                <?php if (isset($component)) { $__componentOriginal482bd23a44299291608e7a4e016b33b6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal482bd23a44299291608e7a4e016b33b6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.list-export-period-modal','data' => ['action' => route('orders.export'),'filters' => $orderExportQuery,'buttonClass' => 'ft-button ft-list-export-button','entityLabel' => 'orders']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.list-export-period-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('orders.export')),'filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderExportQuery),'button-class' => 'ft-button ft-list-export-button','entity-label' => 'orders']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal482bd23a44299291608e7a4e016b33b6)): ?>
<?php $attributes = $__attributesOriginal482bd23a44299291608e7a4e016b33b6; ?>
<?php unset($__attributesOriginal482bd23a44299291608e7a4e016b33b6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal482bd23a44299291608e7a4e016b33b6)): ?>
<?php $component = $__componentOriginal482bd23a44299291608e7a4e016b33b6; ?>
<?php unset($__componentOriginal482bd23a44299291608e7a4e016b33b6); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('jobs.create')): ?>
                <a class="ft-button ft-bulk-import-button" href="<?php echo e(route('orders.bulk-import')); ?>">⇧ Bulk Import</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs', 'create')): ?>
                <a class="ft-new-job-btn ft-dashboard-action-match" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate><span class="ft-dashboard-action-match-icon">+</span>New Order</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($metrics)): ?>
        <div class="metrics ft-summary-card-grid ft-order-summary" aria-label="Order summary filters">
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Created Today','value' => $metrics['createdToday'] ?? 0,'icon' => 'created','tone' => 'blue','caption' => 'New orders received','active' => $metricFilter === 'createdToday','wire:click' => 'setMetricFilter(\'createdToday\')','ariaPressed' => ''.e($metricFilter === 'createdToday' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Created Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['createdToday'] ?? 0),'icon' => 'created','tone' => 'blue','caption' => 'New orders received','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'createdToday'),'wire:click' => 'setMetricFilter(\'createdToday\')','aria-pressed' => ''.e($metricFilter === 'createdToday' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Not Started','value' => $metrics['notStarted'] ?? 0,'icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => $metricFilter === 'notStarted','wire:click' => 'setMetricFilter(\'notStarted\')','ariaPressed' => ''.e($metricFilter === 'notStarted' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Not Started','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['notStarted'] ?? 0),'icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'notStarted'),'wire:click' => 'setMetricFilter(\'notStarted\')','aria-pressed' => ''.e($metricFilter === 'notStarted' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'In Progress','value' => $metrics['inProgress'] ?? 0,'icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => $metricFilter === 'inProgress','wire:click' => 'setMetricFilter(\'inProgress\')','ariaPressed' => ''.e($metricFilter === 'inProgress' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['inProgress'] ?? 0),'icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'inProgress'),'wire:click' => 'setMetricFilter(\'inProgress\')','aria-pressed' => ''.e($metricFilter === 'inProgress' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Due This Week','value' => $metrics['dueThisWeek'] ?? 0,'icon' => 'due-week','tone' => 'amber','caption' => 'Required date this week','active' => $metricFilter === 'dueThisWeek','wire:click' => 'setMetricFilter(\'dueThisWeek\')','ariaPressed' => ''.e($metricFilter === 'dueThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['dueThisWeek'] ?? 0),'icon' => 'due-week','tone' => 'amber','caption' => 'Required date this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'dueThisWeek'),'wire:click' => 'setMetricFilter(\'dueThisWeek\')','aria-pressed' => ''.e($metricFilter === 'dueThisWeek' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Completed This Week','value' => $metrics['completedThisWeek'] ?? 0,'icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => $metricFilter === 'completedThisWeek','wire:click' => 'setMetricFilter(\'completedThisWeek\')','ariaPressed' => ''.e($metricFilter === 'completedThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Completed This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['completedThisWeek'] ?? 0),'icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'completedThisWeek'),'wire:click' => 'setMetricFilter(\'completedThisWeek\')','aria-pressed' => ''.e($metricFilter === 'completedThisWeek' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Needs Attention','value' => $metrics['attention'] ?? 0,'icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => $metricFilter === 'attention','wire:click' => 'setMetricFilter(\'attention\')','ariaPressed' => ''.e($metricFilter === 'attention' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['attention'] ?? 0),'icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'attention'),'wire:click' => 'setMetricFilter(\'attention\')','aria-pressed' => ''.e($metricFilter === 'attention' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-list-shell" aria-label="Orders list">
        <?php if (isset($component)) { $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-bar','data' => ['class' => 'ft-search-bar','label' => 'Order filters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-search-bar','label' => 'Order filters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <label class="ft-search">
                <span class="ft-search-icon">⌕</span>
                <input
                    type="search"
                    autocomplete="off"
                    placeholder="Search order, inquiry, client, product, creator or owner"
                    aria-label="Search orders"
                    wire:model.live.debounce.700ms="search"
                >
                <button class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-search-clear','show'=>filled($searchFilter)]); ?>" wire:click="<?php echo e($clearAction); ?>" type="button">Clear</button>
            </label>

            <div class="ft-order-filter-controls">
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-order-list-filter ft-order-list-filter-client','label' => 'Client','property' => 'client','type' => 'clients','context' => 'jobs','value' => $clientFilter,'placeholder' => 'All clients','initialOptions' => $clientFilterOptions,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'order-list-client-filter-'.e(filled($clientFilter) ? $clientFilter : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-list-filter ft-order-list-filter-client','label' => 'Client','property' => 'client','type' => 'clients','context' => 'jobs','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilter),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'order-list-client-filter-'.e(filled($clientFilter) ? $clientFilter : 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-order-list-filter ft-order-list-filter-phase','label' => 'Phase','property' => 'phase','type' => 'phases','context' => 'order-list','value' => $phaseFilter,'placeholder' => 'All phases','initialOptions' => $phaseFilterOptions,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'order-list-phase-filter-'.e(filled($phaseFilter) ? $phaseFilter : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-list-filter ft-order-list-filter-phase','label' => 'Phase','property' => 'phase','type' => 'phases','context' => 'order-list','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilter),'placeholder' => 'All phases','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilterOptions),'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'order-list-phase-filter-'.e(filled($phaseFilter) ? $phaseFilter : 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-order-list-filter ft-order-list-filter-owner','label' => $peopleLabel,'property' => $peopleProperty,'type' => 'users','context' => $peopleContext,'value' => $peopleFilter,'placeholder' => $peoplePlaceholder,'initialOptions' => $peopleFilterOptions,'fixedMenu' => true,'menuWidth' => 260,'wire:key' => 'order-list-people-filter-'.e($peopleProperty).'-'.e(filled($peopleFilter) ? $peopleFilter : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-list-filter ft-order-list-filter-owner','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleLabel),'property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleProperty),'type' => 'users','context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleContext),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleFilter),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peoplePlaceholder),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleFilterOptions),'fixed-menu' => true,'menu-width' => 260,'wire:key' => 'order-list-people-filter-'.e($peopleProperty).'-'.e(filled($peopleFilter) ? $peopleFilter : 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateRangeEnabled): ?>
                    <?php if (isset($component)) { $__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.date-range','data' => ['class' => 'ft-order-date-range','fromProperty' => 'dateFrom','toProperty' => 'dateTo','fromValue' => $dateFrom,'toValue' => $dateTo,'label' => 'Created date','fromLabel' => 'From','toLabel' => 'To']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.date-range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-date-range','from-property' => 'dateFrom','to-property' => 'dateTo','from-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateFrom),'to-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateTo),'label' => 'Created date','from-label' => 'From','to-label' => 'To']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d)): ?>
<?php $attributes = $__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d; ?>
<?php unset($__attributesOriginal6e32424d5df2e7bdda9ad721db0b2c8d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d)): ?>
<?php $component = $__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d; ?>
<?php unset($__componentOriginal6e32424d5df2e7bdda9ad721db0b2c8d); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearFiltersAction): ?>
                    <?php if (isset($component)) { $__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-reset','data' => ['class' => 'ft-order-clear-filters','action' => $clearFiltersAction,'label' => 'Clear filters','disabled' => blank($searchFilter) && blank($clientFilter) && blank($phaseFilter) && blank($peopleFilter) && blank($metricFilter) && blank($dateFrom) && blank($dateTo) && ! $importFilterId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-reset'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-clear-filters','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clearFiltersAction),'label' => 'Clear filters','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(blank($searchFilter) && blank($clientFilter) && blank($phaseFilter) && blank($peopleFilter) && blank($metricFilter) && blank($dateFrom) && blank($dateTo) && ! $importFilterId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266)): ?>
<?php $attributes = $__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266; ?>
<?php unset($__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266)): ?>
<?php $component = $__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266; ?>
<?php unset($__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed)): ?>
<?php $attributes = $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed; ?>
<?php unset($__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed)): ?>
<?php $component = $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed; ?>
<?php unset($__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($importFilterId): ?>
            <div class="ft-import-batch-filter" role="status" aria-live="polite">
                <span class="ft-import-batch-filter-dot" aria-hidden="true"></span>
                <span>Showing <strong><?php echo e(number_format($jobs->total())); ?></strong> <?php echo e(\Illuminate\Support\Str::plural('order', $jobs->total())); ?> imported in <strong><?php echo e($importFilterLabel ?: 'this import'); ?></strong></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearFiltersAction): ?>
                    <button type="button" wire:click="<?php echo e($clearFiltersAction); ?>">Show all orders</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders && $selectedOrderCount > 0): ?>
            <?php if (isset($component)) { $__componentOriginal497829c258fdc4691f65e9caf04f7e61 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal497829c258fdc4691f65e9caf04f7e61 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.bulk-delete-bar','data' => ['count' => $selectedOrderCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.bulk-delete-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOrderCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal497829c258fdc4691f65e9caf04f7e61)): ?>
<?php $attributes = $__attributesOriginal497829c258fdc4691f65e9caf04f7e61; ?>
<?php unset($__attributesOriginal497829c258fdc4691f65e9caf04f7e61); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal497829c258fdc4691f65e9caf04f7e61)): ?>
<?php $component = $__componentOriginal497829c258fdc4691f65e9caf04f7e61; ?>
<?php unset($__componentOriginal497829c258fdc4691f65e9caf04f7e61); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders && $showBulkDeleteConfirm && $selectedOrderCount > 0): ?>
            <?php if (isset($component)) { $__componentOriginald776f0916bb417998a994ba873f50ec0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald776f0916bb417998a994ba873f50ec0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.bulk-delete-confirmation','data' => ['count' => $selectedOrderCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.bulk-delete-confirmation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOrderCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald776f0916bb417998a994ba873f50ec0)): ?>
<?php $attributes = $__attributesOriginald776f0916bb417998a994ba873f50ec0; ?>
<?php unset($__attributesOriginald776f0916bb417998a994ba873f50ec0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald776f0916bb417998a994ba873f50ec0)): ?>
<?php $component = $__componentOriginald776f0916bb417998a994ba873f50ec0; ?>
<?php unset($__componentOriginald776f0916bb417998a994ba873f50ec0); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-order-table-scroll" tabindex="0" aria-label="Orders table. Scroll horizontally to view all columns when needed.">
            <div class="ft-job-head">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders): ?>
                        <span class="ft-order-select-wrap">
                            <input
                                class="ft-order-select"
                                type="checkbox"
                                aria-label="Select all orders on this page"
                                <?php if($allVisibleOrdersSelected): echo 'checked'; endif; ?>
                                x-on:change="$wire.toggleOrderPageSelection(<?php echo \Illuminate\Support\Js::from($visibleOrderIds->all())->toHtml() ?>, $event.target.checked)"
                            >
                            <span>Created by / on</span>
                        </span>
                    <?php else: ?>
                        <span>Created by / on</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span><span>Order</span><span>Inquiry</span><span>Client / Products</span><span>Phase</span><span>Flag</span><span>Owner / Delivery</span><span>Progress</span><span aria-label="Actions"></span>
            </div>

            <div class="ft-job-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $creator = $job->createdActivity?->user ?? $job->owner;
                    $creatorName = $creator?->name ?? 'System';
                    $ownerName = $job->owner?->name ?? 'Unassigned';
                    $ownerInitials = collect(preg_split('/\\s+/', trim($ownerName)))->filter()->map(fn($part)=>mb_substr($part,0,1))->take(2)->implode('');
                    $ownerImage = $job->owner?->profile_image_path && $job->owner?->id
                        ? route('profile-images.show', ['user'=>$job->owner->id,'filename'=>basename($job->owner->profile_image_path)], false)
                        : null;
                    $items = $job->items;
                    $productRows = $items->isNotEmpty()
                        ? $items
                        : collect([(object)['product_name'=>$job->product ?: 'Product','quantity'=>(int)$job->quantity]]);
                    $totalUnits = (int) $productRows->sum(fn($item)=>(int)($item->quantity ?? 0));
                    $productNames = $productRows->pluck('product_name')->filter()->values();
                    $phaseName = $job->phase?->name ?? $job->status ?? '—';
                    // The automatic Order Flag remains stored independently in
                    // flow_jobs.order_flag_id. A manual attention request is also stored
                    // independently and only takes display precedence in the list.
                    $automaticFlag = app(\App\Services\OrderTaskFlagService::class)->labelForOrder($job);
                    $manualAttention = (bool) ($job->attention_requested ?? false);
                    $flag = $manualAttention ? 'Requires attention' : $automaticFlag;
                    $flagColor = !$manualAttention && $automaticFlag ? $masterData->displayColorFor('order_flag', $automaticFlag) : null;
                    $flagReason = $manualAttention ? trim((string) ($job->attention_reason ?? '')) : '';
                    $deliveryOverdue = $job->delivery_date && !$job->completed_at && \App\Support\UserLocalTime::isDatePast($job->delivery_date);
                    $clientCode = strtoupper(trim((string) ($job->client?->code ?? '')));
                    $clientName = strtoupper(trim((string) ($job->client?->name ?? '')));
                    $clientRowTone = ($clientCode === 'IID' || preg_match('/\bIID\b/i', $clientName))
                        ? 'iid'
                        : (($clientCode === 'NEP' || preg_match('/\bNEP\b/i', $clientName)) ? 'nep' : '');
                ?>
                <article class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-job-row', 'ft-client-row-'.$clientRowTone => $clientRowTone, 'is-bulk-selected' => $selectedOrderIdSet->contains((int) $job->id)]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-row-'.e($job->id).''; ?>wire:key="order-row-<?php echo e($job->id); ?>">
                    <div class="ft-cell ft-created-cell" data-label="Created by / on">
                        <span class="ft-order-select-wrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders): ?>
                                <input
                                    class="ft-order-select"
                                    type="checkbox"
                                    aria-label="Select <?php echo e($job->displayOrderNumber()); ?>"
                                    <?php if($selectedOrderIdSet->contains((int) $job->id)): echo 'checked'; endif; ?>
                                    wire:change="toggleOrderSelection(<?php echo e($job->id); ?>)"
                                >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><span class="ft-created-name"><?php echo e($creatorName); ?></span><time class="ft-created-on"><?php echo e($job->created_at ? \App\Support\UserLocalTime::format($job->created_at, 'M j, Y · g:i A') : '—'); ?></time></span>
                        </span>
                    </div>
                    <div class="ft-cell ft-identity" data-label="Order"><a class="ft-id" href="<?php echo e(route('jobs.index',['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a><span class="ft-sub"><?php echo e($job->order_number ?: 'REF-'.str_pad((string)$job->id,5,'0',STR_PAD_LEFT)); ?></span></div>
                    <div class="ft-cell ft-inquiry-cell" data-label="Inquiry">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->sourceInquiry): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('inquiries.view')): ?>
                                <a class="ft-id" href="<?php echo e(route('inquiries.index', ['open' => $job->sourceInquiry->id])); ?>" wire:navigate><?php echo e($job->sourceInquiry->inquiry_number); ?></a>
                            <?php else: ?>
                                <span class="ft-client"><?php echo e($job->sourceInquiry->inquiry_number); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="ft-sub"><?php echo e($job->sourceInquiry->reference_number ?: 'Source inquiry'); ?></span>
                        <?php else: ?>
                            <span class="ft-standard-empty">Not linked</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-cell ft-brief" data-label="Client / products">
                        <span class="ft-job-client-logo-line"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $job->client,'name' => $job->client?->name ?: 'Client','size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client?->name ?: 'Client'),'size' => 22]); ?>
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
<?php endif; ?><span class="ft-client"><?php echo e($job->client?->name ?? '—'); ?></span></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productRows->count() === 1): ?>
                            <span class="ft-product"><?php echo e($productNames->first() ?: 'Product'); ?></span>
                            <span class="ft-product-detail"><?php echo e(number_format($totalUnits)); ?> <?php echo e(\Illuminate\Support\Str::plural('pc', $totalUnits)); ?></span>
                        <?php else: ?>
                            <span class="ft-product"><?php echo e($productRows->count()); ?> ordered products · <?php echo e(number_format($totalUnits)); ?> pcs</span>
                            <span class="ft-product-detail" title="<?php echo e($productNames->implode(' · ')); ?>"><?php echo e($productNames->implode(' · ')); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-cell ft-stage-cell" data-label="Phase"><?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $job->phase,'fallback' => $phaseName,'class' => 'ft-pill']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->phase),'fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseName),'class' => 'ft-pill']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $attributes = $__attributesOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__attributesOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $component = $__componentOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__componentOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?></div>
                    <div class="ft-cell ft-flag-cell" data-label="Flag"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flag): ?><span class="ft-pill <?php echo e($flagColor ? 'ft-master-color' : $tone($flag)); ?>" style="<?php echo e(\App\Support\MasterColor::style($flagColor)); ?>" <?php if($flagReason): ?> title="<?php echo e($flagReason); ?>" <?php endif; ?>><?php echo e($flag); ?></span><?php else: ?><span class="ft-standard-empty">No flag</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="ft-cell ft-owner-cell" data-label="Owner / delivery">
                        <div class="ft-owner">
                            <span class="ft-order-avatar"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ownerImage): ?><img src="<?php echo e($ownerImage); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><?php echo e($ownerInitials ?: 'FT'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                            <span class="ft-owner-copy"><span class="ft-owner-name"><?php echo e($ownerName); ?></span><time class="ft-due <?php echo e($deliveryOverdue ? 'overdue' : ''); ?>"><?php echo e($job->delivery_date ? 'Due '.$job->delivery_date->format('M j') : 'No delivery date'); ?></time></span>
                        </div>
                    </div>
                    <div class="ft-cell ft-progress ft-progress-cell" data-label="Progress"><span class="ft-progress-track"><span class="ft-progress-fill" style="width:<?php echo e(max(0,min(100,(int)$job->progress))); ?>%"></span></span><span><?php echo e((int)$job->progress); ?>%</span></div>
                    <div class="ft-row-actions" data-label="Actions" x-data="{ open: false }">
                            <button
                                class="ft-row-action-trigger"
                                type="button"
                                :aria-expanded="open ? 'true' : 'false'"
                                aria-haspopup="menu"
                                aria-controls="order-actions-<?php echo e($job->id); ?>"
                                aria-label="Actions for <?php echo e($job->displayOrderNumber()); ?>"
                                x-on:click.stop="
                                    const menu = $refs.menu;
                                    if (menu.matches(':popover-open')) { menu.hidePopover(); return; }
                                    const rect = $el.getBoundingClientRect();
                                    const menuWidth = 190;
                                    const menuHeight = <?php echo e(46 + (($canViewFinance ? 1 : 0) + ($canDeleteOrders ? 1 : 0)) * 42); ?>;
                                    const edge = 10;
                                    const gap = 6;
                                    const left = Math.min(window.innerWidth - menuWidth - edge, Math.max(edge, rect.right - menuWidth));
                                    const openAbove = (window.innerHeight - rect.bottom) < (menuHeight + gap + edge) && rect.top > (menuHeight + gap + edge);
                                    const top = openAbove ? rect.top - menuHeight - gap : rect.bottom + gap;
                                    menu.style.left = `${left}px`;
                                    menu.style.top = `${Math.max(edge, top)}px`;
                                    menu.showPopover();
                                "
                            >⋮</button>
                            <div
                                id="order-actions-<?php echo e($job->id); ?>"
                                class="ft-row-action-menu"
                                x-ref="menu"
                                popover="auto"
                                role="menu"
                                x-on:toggle="open = $event.newState === 'open'"
                            >
                                <a
                                    href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>"
                                    role="menuitem"
                                    wire:navigate
                                    x-on:click="$refs.menu.hidePopover()"
                                    aria-label="View details for <?php echo e($job->displayOrderNumber()); ?>"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                    <span>View</span>
                                </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewFinance): ?>
                                    <button
                                        type="button"
                                        role="menuitem"
                                        x-on:click="$refs.menu.hidePopover()"
                                        wire:click="openInvoiceAndPayment(<?php echo e($job->id); ?>)"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3.5h12v17H6z"/><path d="M9 8h6M9 12h6M9 16h3"/></svg>
                                        <span>Invoice and payment</span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders): ?>
                                    <button class="ft-row-action-danger" type="button" role="menuitem" x-on:click="$refs.menu.hidePopover()" wire:click="deleteOrder(<?php echo e($job->id); ?>)" wire:confirm="Delete <?php echo e($job->displayOrderNumber()); ?>? This removes the order from active lists.">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                                        <span>Delete order</span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-empty"><strong>No matching orders</strong>Try another order, inquiry, client, product, creator or owner.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-load-skeleton" wire:loading.delay.grid wire:target="search,gotoPage,previousPage,nextPage" aria-hidden="true"><span class="ft-skeleton-line"></span><span class="ft-skeleton-line"></span></div>
        </div>

        <div class="ft-list-footer">
            <span class="ft-result-count"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jobs->total()): ?> Showing <?php echo e($jobs->firstItem()); ?>–<?php echo e($jobs->lastItem()); ?> of <?php echo e(number_format($jobs->total())); ?> orders <?php else: ?> No orders found <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
            <nav class="ft-page-buttons" aria-label="Orders pagination">
                <button class="ft-page-button" type="button" wire:click="previousPage" <?php if($jobs->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                <span class="ft-page-buttons">
                    <?php $previousRenderedPage = null; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pageNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previousRenderedPage !== null && $pageNumber - $previousRenderedPage > 1): ?><span class="ft-page-ellipsis">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button type="button" class="ft-page-number <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>)" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                        <?php $previousRenderedPage = $pageNumber; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </span>
                <button class="ft-page-button" type="button" wire:click="nextPage" <?php if(!$jobs->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
            </nav>
        </div>
    </section>

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/table.blade.php ENDPATH**/ ?>