<div id="inquiry-intelligence-app" class="ii" wire:loading.class="ii-is-loading" wire:target="search,period,status,priority,assigneeId,resetFilters,setTab,setTaskTab,employeeFocus">
<?php
    $report = $this->report;
    $portfolio = $report['portfolio'];
    $pk = $portfolio['kpis'];
    $people = $report['people'];
    $peopleKpis = $people['kpis'];
    $products = $report['products'];
    $productKpis = $products['kpis'];
    $badgeClass = fn (string $tone) => in_array($tone, ['green','amber','red','blue'], true) ? $tone : 'blue';
    $periodFilterOptions = collect([
        ['id' => 'month', 'label' => app(\App\Services\WorkspaceSettingsService::class)->localNow()->format('F Y')],
        ['id' => '30d', 'label' => 'Last 30 days'],
        ['id' => 'qtd', 'label' => 'Quarter to date'],
        ['id' => 'ytd', 'label' => 'Year to date'],
    ]);
    $statusFilterOptions = collect($report['filters']['statuses'])->map(fn ($option) => ['id' => $option, 'label' => $option]);
    $priorityFilterOptions = collect($report['filters']['priorities'])->map(fn ($option) => ['id' => $option, 'label' => $option]);
    $assigneeFilterOptions = collect([['id' => '0', 'label' => 'All assignees']])
        ->concat(collect($report['filters']['assignees'])->map(fn ($option) => ['id' => (string) $option['id'], 'label' => $option['name']]));
    // Focus Employee must always come from the current workspace user roster,
    // not from the performance-ranking result set. This keeps every active
    // workspace user selectable even when they have no task rows in the
    // current reporting period.
    $employeeFocusOptions = collect([['id' => 'all', 'label' => 'All active employees']])
        ->concat(collect($report['filters']['assignees'])->map(fn ($option) => [
            'id' => (string) $option['id'],
            'label' => $option['name'],
        ]));
?>

    <div class="ii-crumb"><b>Analytics</b> &nbsp;/&nbsp; Inquiry Intelligence</div>
    <header class="ii-head">
        <div>
            <h1>Inquiry Intelligence</h1>
            <p>Portfolio-wide visibility into inquiry volume, execution, assignee performance and improvement opportunities.</p>
        </div>
        <div class="ii-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('reports','export')): ?>
                <button type="button" class="ii-btn" wire:click="exportVisible" wire:loading.attr="disabled" wire:target="exportVisible">↓ Export visible inquiries</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="ii-btn ii-primary" onclick="window.print()">⤓ Download / Print PDF</button>
        </div>
    </header>

    <?php if (isset($component)) { $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-bar','data' => ['as' => 'section','class' => 'ii-filters','label' => 'Dashboard filters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['as' => 'section','class' => 'ii-filters','label' => 'Dashboard filters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if (isset($component)) { $__componentOriginalf6ee3670073e124e2f361de392ee6597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf6ee3670073e124e2f361de392ee6597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-input','data' => ['class' => 'ii-field ii-search','property' => 'search','value' => $search,'label' => 'Search','placeholder' => 'Reference, title, product or assignee','debounce' => 400]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-field ii-search','property' => 'search','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'label' => 'Search','placeholder' => 'Reference, title, product or assignee','debounce' => 400]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf6ee3670073e124e2f361de392ee6597)): ?>
<?php $attributes = $__attributesOriginalf6ee3670073e124e2f361de392ee6597; ?>
<?php unset($__attributesOriginalf6ee3670073e124e2f361de392ee6597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf6ee3670073e124e2f361de392ee6597)): ?>
<?php $component = $__componentOriginalf6ee3670073e124e2f361de392ee6597; ?>
<?php unset($__componentOriginalf6ee3670073e124e2f361de392ee6597); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ii-field ii-searchable-filter','label' => 'Period','property' => 'period','value' => $period,'options' => $periodFilterOptions,'clearable' => false,'searchPlaceholder' => 'Search period…','fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'inquiry-intelligence-period-'.e($period).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-field ii-searchable-filter','label' => 'Period','property' => 'period','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($period),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($periodFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'search-placeholder' => 'Search period…','fixed-menu' => true,'menu-width' => 280,'wire:key' => 'inquiry-intelligence-period-'.e($period).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ii-field ii-searchable-filter','label' => 'Status','property' => 'status','value' => $status,'placeholder' => 'All statuses','options' => $statusFilterOptions,'searchPlaceholder' => 'Search status…','fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'inquiry-intelligence-status-'.e($status ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-field ii-searchable-filter','label' => 'Status','property' => 'status','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status),'placeholder' => 'All statuses','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusFilterOptions),'search-placeholder' => 'Search status…','fixed-menu' => true,'menu-width' => 280,'wire:key' => 'inquiry-intelligence-status-'.e($status ?: 'all').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ii-field ii-searchable-filter','label' => 'Priority','property' => 'priority','value' => $priority,'placeholder' => 'All priorities','options' => $priorityFilterOptions,'searchPlaceholder' => 'Search priority…','fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'inquiry-intelligence-priority-'.e($priority ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-field ii-searchable-filter','label' => 'Priority','property' => 'priority','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priority),'placeholder' => 'All priorities','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorityFilterOptions),'search-placeholder' => 'Search priority…','fixed-menu' => true,'menu-width' => 280,'wire:key' => 'inquiry-intelligence-priority-'.e($priority ?: 'all').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ii-field ii-searchable-filter','label' => 'Assignee','property' => 'assigneeId','value' => (string) $assigneeId,'options' => $assigneeFilterOptions,'clearable' => false,'searchPlaceholder' => 'Search assignee…','fixedMenu' => true,'menuWidth' => 300,'wire:key' => 'inquiry-intelligence-assignee-'.e($assigneeId).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-field ii-searchable-filter','label' => 'Assignee','property' => 'assigneeId','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $assigneeId),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assigneeFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'search-placeholder' => 'Search assignee…','fixed-menu' => true,'menu-width' => 300,'wire:key' => 'inquiry-intelligence-assignee-'.e($assigneeId).'']); ?>
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

        <?php if (isset($component)) { $__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-reset','data' => ['class' => 'ii-btn ii-filter-reset','action' => 'resetFilters','label' => 'Reset']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-reset'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-btn ii-filter-reset','action' => 'resetFilters','label' => 'Reset']); ?>
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

    <nav class="ii-tabs" aria-label="Inquiry intelligence sections">
        <button type="button" class="ii-tab <?php echo e($activeTab === 'portfolio' ? 'active' : ''); ?>" wire:click="setTab('portfolio')">Portfolio overview</button>
        <button type="button" class="ii-tab <?php echo e($activeTab === 'people' ? 'active' : ''); ?>" wire:click="setTab('people')">Assignee performance</button>
        <button type="button" class="ii-tab <?php echo e($activeTab === 'products' ? 'active' : ''); ?>" wire:click="setTab('products')">Product performance</button>
    </nav>

    <section class="ii-panel <?php echo e($activeTab === 'portfolio' ? 'active' : ''); ?>" <?php if($activeTab !== 'portfolio'): ?> hidden <?php endif; ?>>
        <div class="ii-sect"><div><h2>Inquiry performance at a glance</h2><p>Management indicators recalculated from the active filters</p></div><small><?php echo e($report['period']['label']); ?></small></div>
        <div class="ii-grid6">
            <article class="ii-card ii-kpi"><div class="ii-label">Total inquiries</div><div class="ii-big"><?php echo e(number_format($pk['total'])); ?></div><div class="ii-trend">Visible in the selected period</div><div class="ii-track"><i class="ft-report-track-full"></i></div></article>
            <article class="ii-card ii-kpi ii-warn"><div class="ii-label">Open inquiries</div><div class="ii-big"><?php echo e(number_format($pk['open'])); ?></div><div class="ii-trend">Still moving through inquiry workflow</div><div class="ii-track"><i style="width:<?php echo e($pk['total'] ? round($pk['open']/$pk['total']*100) : 0); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-good"><div class="ii-label">Completed inquiries</div><div class="ii-big"><?php echo e(number_format($pk['completed'])); ?></div><div class="ii-trend">Workflow completed or final result recorded</div><div class="ii-track"><i style="width:<?php echo e($pk['total'] ? round($pk['completed']/$pk['total']*100) : 0); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-good"><div class="ii-label">Task completion</div><div class="ii-big"><?php echo e(number_format($pk['task_completion'],1)); ?><small>%</small></div><div class="ii-trend"><?php echo e(number_format($pk['task_done'])); ?> of <?php echo e(number_format($pk['task_total'])); ?> workflow tasks</div><div class="ii-track"><i style="width:<?php echo e(min(100,$pk['task_completion'])); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-good"><div class="ii-label">File compliance</div><div class="ii-big"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pk['file_compliance'] !== null): ?><?php echo e(number_format($pk['file_compliance'],1)); ?><small>%</small><?php else: ?>—<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="ii-trend"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pk['evidence_total'] > 0): ?><?php echo e($pk['evidenced']); ?> of <?php echo e($pk['evidence_total']); ?> completed required-file tasks evidenced <?php else: ?> No completed required-file tasks in this selection <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="ii-track"><i style="width:<?php echo e($pk['file_compliance'] ?? 0); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-warn"><div class="ii-label">Structured products</div><div class="ii-big"><?php echo e($pk['structured_products']); ?><small>%</small></div><div class="ii-trend"><?php echo e($pk['structured_count']); ?> inquiries contain structured product lines</div><div class="ii-track"><i style="width:<?php echo e($pk['structured_products']); ?>%"></i></div></article>
        </div>

        <div class="ii-sect"><div><h2>Volume and outcome</h2><p>Inquiry creation and workflow result across the selected period</p></div></div>
        <div class="ii-layout">
            <article class="ii-card">
                <div class="ii-cardhead"><h3>Inquiry activity</h3><div class="ii-legend"><span><i></i>Created</span><span><i class="green"></i>Completed</span></div></div>
                <div class="ii-chartbody ii-trendchart">
                    <div class="ii-plot">
                        <svg viewBox="0 0 700 180" preserveAspectRatio="none" aria-label="Inquiry trend chart">
                            <polygon points="<?php echo e($portfolio['trend']['created_fill_points']); ?>" fill="rgba(18,104,245,.11)"></polygon>
                            <polyline points="<?php echo e($portfolio['trend']['created_points']); ?>" fill="none" stroke="#1268f5" stroke-width="3"></polyline>
                            <polyline points="<?php echo e($portfolio['trend']['completed_points']); ?>" fill="none" stroke="#168451" stroke-width="3" stroke-dasharray="6 5"></polyline>
                        </svg>
                    </div>
                    <div class="ii-xaxis"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $portfolio['trend']['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($label); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div>
                </div>
            </article>
            <div class="ii-stack">
                <article class="ii-card">
                    <div class="ii-cardhead"><h3>Status mix</h3><span><?php echo e(number_format($pk['total'])); ?> inquiries</span></div>
                    <div class="ii-donuts">
                        <?php $openPct = $pk['total'] ? round($pk['open']/$pk['total']*100) : 0; $completedPct = $pk['total'] ? round($pk['completed']/$pk['total']*100) : 0; ?>
                        <div class="ii-donutbox"><div class="ii-donut" style="--p:<?php echo e($openPct); ?>"><div><b><?php echo e($openPct); ?>%</b><small>Open</small></div></div><small><?php echo e($pk['open']); ?> inquiries</small></div>
                        <div class="ii-donutbox"><div class="ii-donut" style="--p:<?php echo e($completedPct); ?>;--c:var(--ii-green)"><div><b><?php echo e($completedPct); ?>%</b><small>Completed</small></div></div><small><?php echo e($pk['completed']); ?> inquiries</small></div>
                    </div>
                </article>
            </div>
        </div>

        <div class="ii-sect"><div><h2>Management attention</h2><p>Exceptions and decision signals recalculated from the active filters</p></div><small><?php echo e(number_format($portfolio['row_count'])); ?> filtered inquiries</small></div>
        <div class="ii-layout">
            <article class="ii-card"><div class="ii-attention">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $portfolio['attention']['signals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $signal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ii-alert <?php echo e($signal['tone'] === 'amber' ? 'ii-amber' : ($signal['tone'] === 'red' ? 'ii-red' : '')); ?>">
                        <b><?php echo e(number_format($signal['count'])); ?> <?php echo e($signal['label']); ?></b>
                        <p><?php echo e($signal['description']); ?></p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div></article>
            <article class="ii-card"><div class="ii-cardhead"><h3>Priority mix</h3><span><?php echo e(number_format($portfolio['row_count'])); ?> filtered inquiries</span></div><div class="ii-bars">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $portfolio['priority_mix']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priorityRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ii-barrow"><span><?php echo e($priorityRow['name']); ?></span><div class="ii-bar"><i class="ii-master-fill" style="width:<?php echo e($priorityRow['width']); ?>%;<?php echo e(\App\Support\MasterColor::style($priorityRow['color'] ?? null)); ?>"></i></div><b><?php echo e($priorityRow['count']); ?><small><?php echo e(number_format($priorityRow['share'], 1)); ?>%</small></b></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ii-empty-inline">No priority data matches the active filters.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></article>
        </div>

        <div class="ii-sect">
            <div><h2>All inquiries</h2><p>Recent inquiry preview from the same active filters</p></div>
            <div class="ii-sect-actions">
                <small>Showing <?php echo e(number_format($portfolio['preview_count'])); ?> of <?php echo e(number_format($portfolio['row_count'])); ?></small>
                <a class="ii-btn ii-section-link" href="<?php echo e($portfolio['view_all_url']); ?>" wire:navigate>View all inquiries</a>
            </div>
        </div>
        <article class="ii-card ii-tablewrap">
            <table>
                <thead><tr><th>Reference / inquiry</th><th>Product signal</th><th>Created</th><th>Priority</th><th>Lead assignee</th><th>Progress</th><th>Status</th><th>Attention</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $portfolio['preview_rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><a class="ii-record-link" href="<?php echo e($row['url']); ?>" wire:navigate><b><?php echo e($row['reference']); ?></b><small><?php echo e($row['subject']); ?></small></a></td>
                        <td><?php echo e($row['product']); ?></td>
                        <td><?php echo e($row['created']); ?></td>
                        <td><span class="ii-badge ii-master" style="<?php echo e(\App\Support\MasterColor::style($row['priority_color'] ?? null)); ?>"><?php echo e($row['priority']); ?></span></td>
                        <td><?php echo e($row['assignee']); ?></td>
                        <td><b><?php echo e($row['progress']); ?>%</b><small><?php echo e($row['progress_text']); ?></small></td>
                        <td><span class="ii-status"><i class="ii-dot ii-master-dot" style="<?php echo e(\App\Support\MasterColor::style($row['status_color'] ?? null)); ?>"></i><?php echo e($row['status']); ?></span></td>
                        <td><span class="ii-badge ii-<?php echo e($badgeClass($row['attention_tone'])); ?>"><?php echo e($row['attention']); ?></span></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="8"><div class="ii-empty-inline">No inquiries match the selected filters.</div></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </article>
    </section>

    <section class="ii-panel <?php echo e($activeTab === 'people' ? 'active' : ''); ?>" <?php if($activeTab !== 'people'): ?> hidden <?php endif; ?>>
        <div class="ii-sect"><div><h2>Assignee performance center</h2><p>Completion, turnaround time, due-date reliability and reopen activity across the inquiry team</p></div><small><?php echo e($report['period']['label']); ?> · live data</small></div>
        <div class="ii-demo-banner"><div><b>Live task performance</b>Completion uses assigned vs completed tasks. Average hours use only the task's recorded Started/In Progress timestamp through completion. On-time uses completed tasks with due dates, and every completed-to-open status change is counted as a reopen.</div><span class="ii-badge ii-blue">Live data</span></div>

        <div class="ii-sect"><div><h2>Team summary</h2><p>Headline capacity and execution indicators</p></div></div>
        <div class="ii-people-kpis">
            <article class="ii-card ii-smallkpi"><div class="ii-label">Employee roster</div><div class="ii-big"><?php echo e(number_format($peopleKpis['roster'])); ?></div><div class="ii-trend">Active users in this workspace</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Active this period</div><div class="ii-big"><?php echo e(number_format($peopleKpis['active'])); ?></div><div class="ii-trend">Assignees with inquiry tasks</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Tasks assigned</div><div class="ii-big"><?php echo e(number_format($peopleKpis['assigned'])); ?></div><div class="ii-trend">Across filtered inquiries</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Tasks completed</div><div class="ii-big"><?php echo e(number_format($peopleKpis['completed'])); ?></div><div class="ii-trend"><?php echo e($peopleKpis['completion_rate']); ?>% completion rate</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Avg completion time</div><div class="ii-big"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peopleKpis['avg_hours'] !== null): ?><?php echo e(number_format($peopleKpis['avg_hours'],1)); ?><small>h</small><?php else: ?>—<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="ii-trend"><?php echo e($peopleKpis['avg_hours_samples']); ?> completed tasks with a recorded start time</div></article>
            <article class="ii-card ii-smallkpi ii-warn"><div class="ii-label">On-time completion</div><div class="ii-big"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peopleKpis['on_time'] !== null): ?><?php echo e(number_format($peopleKpis['on_time'],1)); ?><small>%</small><?php else: ?>—<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="ii-trend"><?php echo e($peopleKpis['on_time_samples']); ?> completed tasks with due dates</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Reopen events</div><div class="ii-big"><?php echo e(number_format($peopleKpis['reopen_events'])); ?></div><div class="ii-trend">Completed tasks moved back to any non-completed status</div></article>
        </div>

        <div class="ii-sect"><div><h2>Performance highlights</h2><p>Live highlights based on completion, on-time delivery, average task hours and completed-task throughput</p></div></div>
        <div class="ii-rankgrid">
            <?php
                $highlightCards = [
                    ['key'=>'best','class'=>'','eyebrow'=>'Best overall performer'],
                    ['key'=>'throughput','class'=>'ii-watch','eyebrow'=>'Highest throughput'],
                    ['key'=>'coaching','class'=>'ii-attn','eyebrow'=>'Coaching priority'],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $highlightCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php $person = $people['highlights'][$card['key']] ?? null; ?>
                <article class="ii-card ii-rankcard <?php echo e($card['class']); ?>">
                    <div class="ii-rankeyebrow"><?php echo e($card['eyebrow']); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($person): ?>
                        <div class="ii-rankperson"><span class="ii-face"><?php echo e($person['initials']); ?></span><div><h3><?php echo e($person['name']); ?></h3><p><?php echo e($person['completed']); ?> completed tasks · <?php echo e($person['reopen_count']); ?> <?php echo e($person['reopen_count'] === 1 ? 'reopen' : 'reopens'); ?></p></div></div>
                        <div class="ii-rankstats"><div><b><?php echo e($person['avg_hours'] !== null ? number_format($person['avg_hours'],1).'h' : '—'); ?></b><span>Avg hours</span></div><div><b><?php echo e($person['on_time'] !== null ? number_format($person['on_time'],0).'%' : '—'); ?></b><span>On time</span></div><div><b><?php echo e(number_format($person['completion'],0)); ?>%</b><span>Completion</span></div></div>
                    <?php else: ?>
                        <div class="ii-empty-inline">No assignee data in this period.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="ii-sect"><div><h2>Employee performance ranking</h2><p>Completion rate remains the primary ranking measure; on-time rate, completed volume, lower average hours and fewer reopens resolve ties</p></div><small>Live task metrics</small></div>
        <article class="ii-card ii-tablewrap"><table><thead><tr><th>Rank</th><th>Assignee</th><th>Assigned</th><th>Completed</th><th>Completion</th><th>Avg hours</th><th>On time</th><th>Reopen count</th></tr></thead><tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $people['ranking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td class="ii-rankno"><?php echo e(str_pad((string)$row['rank'],2,'0',STR_PAD_LEFT)); ?></td>
                    <td><div class="ii-emp-name"><span class="ii-face"><?php echo e($row['initials']); ?></span><b><?php echo e($row['name']); ?></b></div></td>
                    <td><?php echo e($row['assigned']); ?></td><td><?php echo e($row['completed']); ?></td><td><?php echo e(number_format($row['completion'],1)); ?>%</td>
                    <td class="ii-hours <?php echo e($row['avg_hours'] !== null && $row['avg_hours'] > 8 ? 'slow' : ($row['avg_hours'] !== null && $row['avg_hours'] <= 2 ? 'fast' : '')); ?>"><?php echo e($row['avg_hours'] !== null ? number_format($row['avg_hours'],1).'h' : '—'); ?></td>
                    <td><?php echo e($row['on_time'] !== null ? number_format($row['on_time'],1).'%' : '—'); ?></td>
                    <td><b><?php echo e($row['reopen_count']); ?></b></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="8"><div class="ii-empty-inline">No assignee activity matches the selected filters.</div></td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody></table></article>

        <div class="ii-sect"><div><h2>Assignee inquiry-to-order conversion</h2><p>The 5 most recent Inquiry records converted into linked FlowTrack Orders, attributed to the inquiry owner or latest task assignee</p></div><small>Current filtered period</small></div>
        <article class="ii-card ii-tablewrap">
            <table>
                <thead><tr><th>Assignee</th><th>Inquiry</th><th>Converted order</th><th>Completed inquiries</th><th>Orders converted</th><th>Conversion rate</th><th>Converted at</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $people['conversion']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td><b><?php echo e($row['assignee']); ?></b></td>
                            <td><a class="ii-record-link ii-inline-link" href="<?php echo e($row['inquiry_url']); ?>" wire:navigate><?php echo e($row['inquiry']); ?></a></td>
                            <td><a class="ii-record-link ii-inline-link" href="<?php echo e($row['order_url']); ?>" wire:navigate><?php echo e($row['order']); ?></a></td>
                            <td><?php echo e($row['completed_inquiries']); ?></td>
                            <td><b><?php echo e($row['orders_converted']); ?></b></td>
                            <td class="ii-conversion"><?php echo e(number_format($row['conversion_rate'], 1)); ?>%</td>
                            <td class="ii-timecell"><?php echo e($row['converted_at']); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="7"><div class="ii-empty-inline">No Inquiry-to-Order conversions match the selected period and filters.</div></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </article>

        <div class="ii-sect"><div><h2>Task start-to-completion detail</h2><p>Shows real task start/completion timing, Inquiry SLA target and every Completed → non-completed reopen event</p></div><small>10 rows per page</small></div>
        <div class="ii-focusbar">
            <div class="ii-subtabs">
                <button type="button" class="ii-subtab <?php echo e($taskTab === 'recent' ? 'active' : ''); ?>" wire:click="setTaskTab('recent')">Recent completions</button>
                <button type="button" class="ii-subtab <?php echo e($taskTab === 'longest' ? 'active' : ''); ?>" wire:click="setTaskTab('longest')">Longest tasks</button>
                <button type="button" class="ii-subtab <?php echo e($taskTab === 'reopened' ? 'active' : ''); ?>" wire:click="setTaskTab('reopened')">Reopened tasks</button>
            </div>
            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ii-field ii-searchable-filter ii-focus-employee-filter','label' => 'Focus employee','property' => 'employeeFocus','value' => $employeeFocus,'options' => $employeeFocusOptions,'clearable' => false,'searchPlaceholder' => 'Search employee…','fixedMenu' => true,'menuWidth' => 320,'wire:key' => 'inquiry-intelligence-focus-'.e($employeeFocus).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ii-field ii-searchable-filter ii-focus-employee-filter','label' => 'Focus employee','property' => 'employeeFocus','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employeeFocus),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employeeFocusOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'search-placeholder' => 'Search employee…','fixed-menu' => true,'menu-width' => 320,'wire:key' => 'inquiry-intelligence-focus-'.e($employeeFocus).'']); ?>
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
        </div>
        <article class="ii-card ii-task-detail-card">
            <div class="ii-tablewrap ii-task-table-scroll">
            <table class="ii-task-detail-table">
                <thead><tr><th>Assignee</th><th>Inquiry</th><th>Task</th><th>Status</th><th>Started</th><th>Completed</th><th>Hours taken</th><th>SLA target</th><th>SLA</th><th>Reopen count</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->taskRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td><b><?php echo e($row['assignee']); ?></b></td>
                            <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['inquiry_url']): ?><a class="ii-record-link ii-inline-link" href="<?php echo e($row['inquiry_url']); ?>" wire:navigate><?php echo e($row['inquiry']); ?></a><?php else: ?><?php echo e($row['inquiry']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                            <td><b class="ii-task-name"><?php echo e($row['task']); ?></b></td>
                            <td><span class="ii-status-text"><?php echo e($row['status']); ?></span></td>
                            <td class="ii-timecell"><?php echo e($row['started']); ?></td>
                            <td class="ii-timecell"><?php echo e($row['completed']); ?></td>
                            <td class="ii-hours <?php echo e($row['hours_value'] !== null && $row['hours_value'] > 8 ? 'slow' : ($row['hours_value'] !== null && $row['hours_value'] <= 2 ? 'fast' : '')); ?>"><?php echo e($row['hours']); ?></td>
                            <td class="ii-sla-target"><b><?php echo e($row['sla_target']); ?></b><small><?php echo e($row['sla_source']); ?></small></td>
                            <td><span class="ii-badge ii-<?php echo e($badgeClass($row['sla_tone'])); ?>"><?php echo e($row['sla']); ?></span></td>
                            <td><b><?php echo e($row['reopen_count']); ?></b></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="10"><div class="ii-empty-inline">No task events match this focus.</div></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
            </div>
            <?php ($taskPager = $this->taskPagination); ?>
            <div class="ii-task-pagination">
                <span>Showing <?php echo e($taskPager['from']); ?>–<?php echo e($taskPager['to']); ?> of <?php echo e($taskPager['total']); ?></span>
                <div class="ii-task-page-actions">
                    <button type="button" wire:click="previousTaskPage" <?php if($taskPager['page'] <= 1): echo 'disabled'; endif; ?>>Previous</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPager['numbers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="<?php echo e($taskPager['page'] === $pageNumber ? 'active' : ''); ?>" wire:click="gotoTaskPage(<?php echo e($pageNumber); ?>)"><?php echo e($pageNumber); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" wire:click="nextTaskPage" <?php if($taskPager['page'] >= $taskPager['pages']): echo 'disabled'; endif; ?>>Next</button>
                </div>
            </div>
        </article>



    </section>

    <section class="ii-panel <?php echo e($activeTab === 'products' ? 'active' : ''); ?>" <?php if($activeTab !== 'products'): ?> hidden <?php endif; ?>>
        <div class="ii-sect"><div><h2>Product performance center</h2><p>Demand, inquiry workload, turnaround, conversion and recurring client questions</p></div><small><?php echo e($report['period']['label']); ?> · live data</small></div>
        <div class="ii-demo-banner"><div><b>Product analytics uses Inquiry Product &amp; Quantity lines</b>Category and quantity metrics are based only on structured inquiry items. Requests kept exclusively in descriptions or attachments remain visible in coverage gaps instead of being guessed.</div><span class="ii-badge ii-blue">Live data</span></div>

        <div class="ii-sect"><div><h2>Product portfolio summary</h2><p>Commercial and operational indicators for inquiry-led demand</p></div></div>
        <div class="ii-product-kpis">
            <article class="ii-card ii-smallkpi"><div class="ii-label">Product inquiries</div><div class="ii-big"><?php echo e(number_format($productKpis['product_inquiries'])); ?></div><div class="ii-trend">Inquiries with structured product lines</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Completed workflows</div><div class="ii-big"><?php echo e(number_format($productKpis['completed'])); ?></div><div class="ii-trend">Product inquiries completed</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Converted to order</div><div class="ii-big"><?php echo e(number_format($productKpis['converted'])); ?></div><div class="ii-trend"><?php echo e($productKpis['conversion'] !== null ? number_format($productKpis['conversion'],1).'%' : '—'); ?> of completed product inquiries</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Avg quote workflow time</div><div class="ii-big"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productKpis['avg_quote_hours'] !== null): ?><?php echo e(number_format($productKpis['avg_quote_hours'],1)); ?><small>h</small><?php else: ?>—<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="ii-trend">Inquiry start/create to workflow completion</div></article>
            <article class="ii-card ii-smallkpi ii-warn"><div class="ii-label">Product data coverage</div><div class="ii-big"><?php echo e(number_format($productKpis['data_coverage'],0)); ?><small>%</small></div><div class="ii-trend"><?php echo e(number_format(100-$productKpis['data_coverage'],0)); ?>% needs structured product lines</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Top demand category</div><div class="ii-big ii-category-big"><?php echo e($productKpis['top_category']); ?></div><div class="ii-trend"><?php echo e($productKpis['top_category_share']); ?>% of structured product inquiries</div></article>
        </div>

        <div class="ii-sect"><div><h2>Category performance</h2><p>Compare demand with workflow speed and conversion, not inquiry count alone</p></div></div>
        <div class="ii-product-layout">
            <article class="ii-card ii-tablewrap"><table><thead><tr><th>Product category</th><th>Inquiries</th><th>Demand share</th><th>Avg workflow time</th><th>Completed</th><th>Conversion</th><th>Avg quantity</th><th>Signal</th></tr></thead><tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><b><?php echo e($row['category']); ?></b><small><?php echo e($row['sample_product'] ?: 'Structured inquiry products'); ?></small></td><td><?php echo e($row['inquiries']); ?></td><td><?php echo e($row['share']); ?>%</td><td class="ii-hours <?php echo e($row['avg_hours'] !== null && $row['avg_hours'] > 24 ? 'slow' : ($row['avg_hours'] !== null && $row['avg_hours'] <= 4 ? 'fast' : '')); ?>"><?php echo e($row['avg_hours'] !== null ? number_format($row['avg_hours'],1).'h' : '—'); ?></td><td><?php echo e($row['completed']); ?></td><td><b><?php echo e($row['conversion'] !== null ? number_format($row['conversion'],1).'%' : '—'); ?></b></td><td><?php echo e($row['avg_quantity'] !== null ? number_format($row['avg_quantity']).' pcs' : '—'); ?></td><td><span class="ii-badge ii-<?php echo e($badgeClass($row['signal']['tone'])); ?>"><?php echo e($row['signal']['label']); ?></span></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="8"><div class="ii-empty-inline">No structured product data matches the selected filters.</div></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody></table></article>
            <div class="ii-stack">
                <article class="ii-card"><div class="ii-cardhead"><h3>Inquiry demand share</h3><span><?php echo e(number_format($productKpis['product_inquiries'])); ?> total</span></div><div class="ii-bars">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products['demand_bars']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ii-barrow"><span><?php echo e($row['category']); ?></span><div class="ii-bar"><i style="width:<?php echo e($row['width']); ?>%"></i></div><b><?php echo e($row['inquiries']); ?></b></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ii-empty-inline">No demand data.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div></article>
                <article class="ii-card ii-product-insight"><h3>Management reading</h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products['insights']['top']): ?><p><?php echo e($products['insights']['top']['category']); ?> currently creates the most structured inquiry demand. Compare its workflow time with conversion before prioritizing templates or supplier changes.</p><?php else: ?><p>Structured product data is not yet available for the selected period.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products['insights']['slowest']): ?><div class="ii-alert ii-amber"><b><?php echo e($products['insights']['slowest']['category']); ?> has the longest average workflow time</b><p>Review sourcing, task wait states and product complexity before attributing delay to an employee.</p></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            </div>
        </div>

        <div class="ii-sect"><div><h2>Recurring product-related queries</h2><p>Common themes detected from inquiry subject and requirement text</p></div></div>
        <div class="ii-product-layout">
            <article class="ii-card"><div class="ii-cardhead"><h3>Top query themes</h3><span>Mentions can overlap</span></div><div class="ii-querycloud">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products['query_themes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span class="ii-querytag"><?php echo e($row['label']); ?> <b><?php echo e($row['count']); ?></b></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><span class="ii-querytag">No recurring themes detected</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></article>
            <article class="ii-card ii-product-insight"><h3>Suggested knowledge content</h3><p>Use recurring question themes to build reusable product briefs, quantity guidance, decoration rules, freight assumptions and lead-time ranges.</p><div class="ii-alert"><b>Recommended product KPI</b><p>Track clarification messages per inquiry once message/event classification is available.</p></div></article>
        </div>

        <div class="ii-sect"><div><h2>Product KPI framework</h2><p>Measures management can use from the records already linked in FlowTrack</p></div></div>
        <article class="ii-card ii-tablewrap"><table><thead><tr><th>KPI</th><th>Definition</th><th>Suggested target</th><th>Decision supported</th></tr></thead><tbody>
            <tr><td><b>Inquiry demand</b></td><td>Unique inquiries by structured product category and period</td><td>Trend, no fixed target</td><td>Catalog and sales focus</td></tr>
            <tr><td><b>Workflow turnaround</b></td><td>Inquiry start/create → completed inquiry workflow, by category</td><td>Company policy</td><td>Templates, staffing and supplier panels</td></tr>
            <tr><td><b>Workflow completion rate</b></td><td>Completed product inquiries ÷ structured product inquiries</td><td>Upward trend</td><td>Identify abandoned or blocked demand</td></tr>
            <tr><td><b>Order conversion</b></td><td>Linked orders ÷ completed product inquiries</td><td>By category</td><td>Pricing and assortment quality</td></tr>
            <tr><td><b>Average quantity</b></td><td>Mean quantity across structured inquiry items</td><td>Trend, no fixed target</td><td>Supplier MOQs and price tiers</td></tr>
            <tr><td><b>Data completeness</b></td><td>Inquiries with at least one structured product line</td><td>100%</td><td>Trustworthy analytics</td></tr>
            <tr><td><b>Attention load</b></td><td>Open product inquiries with configured attention states</td><td>Downward trend</td><td>Operational bottleneck review</td></tr>
            <tr><td><b>Query theme frequency</b></td><td>Keyword themes detected from request text</td><td>Downward after content fixes</td><td>Product content quality</td></tr>
        </tbody></table></article>

        <div class="ii-sect"><div><h2>Product improvement actions</h2><p>Recommended response to the current report signals</p></div></div>
        <div class="ii-recommend">
            <article class="ii-card ii-rec"><div class="ii-num">01 · Top demand</div><h3><?php echo e($products['insights']['top']['category'] ?? 'Product categories'); ?></h3><p>Build repeatable quote and workflow templates around the highest-demand structured category.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">02 · Product master</div><h3>Require structured product selection</h3><p><?php echo e(number_format($products['insights']['coverage_gap'],0)); ?>% of filtered inquiries currently lack structured product lines and cannot be categorized reliably.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">03 · Best conversion</div><h3><?php echo e($products['insights']['best_conversion']['category'] ?? 'Conversion playbook'); ?></h3><p>Document the workflow, product and handoff practices behind the strongest observed conversion rate.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">04 · Slow categories</div><h3><?php echo e($products['insights']['slowest']['category'] ?? 'Separate complexity from delay'); ?></h3><p>Compare task wait states, sourcing complexity and employee handling time before setting category expectations.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">05 · Commercial linkage</div><h3>Keep inquiries linked to orders</h3><p>Conversion depends on valid Inquiry → Order linkage; maintain that relationship during imports and manual creation.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">06 · Query reduction</div><h3>Answer common questions upfront</h3><p>Use the recurring themes above to improve product request forms and reduce repeated clarification work.</p></article>
        </div>
    </section>

    <footer class="ii-pagefoot"><span>StepPromo · Inquiry Intelligence</span><span>Management analytics · <?php echo e($report['period']['label']); ?></span></footer>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/reports/index.blade.php ENDPATH**/ ?>