<div
    id="my-work-app"
    x-data="{ metrics: <?php echo \Illuminate\Support\Js::from($metrics)->toHtml() ?>, groupsExpanded: true }"
    x-on:my-work-metrics.window="metrics = $event.detail"
>


    <div class="page-head">
        <div>
            <h1>My Tasks</h1>
            <p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sourceFilter === 'inquiries' && $statusFilter !== ''): ?>
                    Inquiry tasks matching the selected dashboard status filter.
                <?php else: ?>
                    <?php echo e($administratorView ? 'All Order tasks, grouped by Order and ranked by what needs action first.' : 'Tasks assigned to you or from Orders you created, grouped by Order and ranked by what needs action first.'); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
    </div>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sourceFilter === 'inquiries' && $statusFilter !== ''): ?>
    <section class="work-view" aria-busy="false">
        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search filtered Inquiry tasks" aria-label="Search filtered Inquiry tasks">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="quick-filters" aria-label="Active dashboard task filter">
                    <span class="chip active">Inquiry tasks</span>
                    <span class="chip active">Status: <?php echo e($statusFilter); ?></span>
                </div>
            </div>
            <div class="toolbar-secondary">
                <select class="sort" wire:model.live="sort" aria-label="Sort filtered Inquiry tasks">
                    <option value="action">Sort: Action priority</option>
                    <option value="due">Sort: Due soon</option>
                    <option value="job">Sort: Inquiry number</option>
                </select>
                <button type="button" class="chip clear-filters" wire:click="clearStatusFilter">Clear status filter</button>
            </div>
        </div>

        <div class="load-state">
            <span><strong><?php echo e($statusFilter); ?></strong> Inquiry tasks</span>
            <span class="loading-copy">
                <span wire:loading.delay.long wire:target="search,sort,clearSearch,clearStatusFilter"><i class="spinner"></i> Updating tasks…</span>
            </span>
        </div>

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,sort,clearSearch,clearStatusFilter" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My Inquiry Tasks filtered by status" wire:loading.class="is-refreshing" wire:target="search,sort,clearSearch,clearStatusFilter">
            <div class="task-table-scroll">
                <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>
                <div>
                    <?php echo $__env->make('livewire.my-work._inquiry-groups', ['inquiryGroups' => $inquiryGroups], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryGroups->isEmpty()): ?>
                        <div class="empty"><strong>No matching Inquiry tasks</strong>No My Task records currently use the selected status.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <footer class="footer">
                <span><?php echo e($inquiryVisibleTaskCount); ?> <?php echo e($inquiryVisibleTaskCount === 1 ? 'task' : 'tasks'); ?> with status “<?php echo e($statusFilter); ?>”</span>
            </footer>
        </section>
    </section>
    <?php else: ?>

    <section class="work-view" aria-busy="false">
        <div class="metrics ft-summary-card-grid" aria-label="My Task summary filters">
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Created Today','value' => $metrics['createdToday'] ?? 0,'valueExpression' => 'metrics.createdToday ?? \'—\'','icon' => 'created','tone' => 'blue','caption' => 'Tasks created today','active' => $quick === 'createdToday','wire:click' => 'setMetricFilter(\'createdToday\')','ariaPressed' => ''.e($quick === 'createdToday' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Created Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['createdToday'] ?? 0),'value-expression' => 'metrics.createdToday ?? \'—\'','icon' => 'created','tone' => 'blue','caption' => 'Tasks created today','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quick === 'createdToday'),'wire:click' => 'setMetricFilter(\'createdToday\')','aria-pressed' => ''.e($quick === 'createdToday' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Not Started','value' => $metrics['notStarted'] ?? 0,'valueExpression' => 'metrics.notStarted ?? \'—\'','icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => $quick === 'notStarted','wire:click' => 'setMetricFilter(\'notStarted\')','ariaPressed' => ''.e($quick === 'notStarted' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Not Started','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['notStarted'] ?? 0),'value-expression' => 'metrics.notStarted ?? \'—\'','icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quick === 'notStarted'),'wire:click' => 'setMetricFilter(\'notStarted\')','aria-pressed' => ''.e($quick === 'notStarted' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'In Progress','value' => $metrics['inProgress'] ?? 0,'valueExpression' => 'metrics.inProgress ?? \'—\'','icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => $quick === 'inProgress','wire:click' => 'setMetricFilter(\'inProgress\')','ariaPressed' => ''.e($quick === 'inProgress' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['inProgress'] ?? 0),'value-expression' => 'metrics.inProgress ?? \'—\'','icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quick === 'inProgress'),'wire:click' => 'setMetricFilter(\'inProgress\')','aria-pressed' => ''.e($quick === 'inProgress' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Due This Week','value' => $metrics['dueThisWeek'] ?? 0,'valueExpression' => 'metrics.dueThisWeek ?? \'—\'','icon' => 'due-week','tone' => 'amber','caption' => 'Tasks due this week','active' => $quick === 'dueThisWeek','wire:click' => 'setMetricFilter(\'dueThisWeek\')','ariaPressed' => ''.e($quick === 'dueThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['dueThisWeek'] ?? 0),'value-expression' => 'metrics.dueThisWeek ?? \'—\'','icon' => 'due-week','tone' => 'amber','caption' => 'Tasks due this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quick === 'dueThisWeek'),'wire:click' => 'setMetricFilter(\'dueThisWeek\')','aria-pressed' => ''.e($quick === 'dueThisWeek' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Completed This Week','value' => $metrics['completedThisWeek'] ?? 0,'valueExpression' => 'metrics.completedThisWeek ?? \'—\'','icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => $quick === 'completedThisWeek','wire:click' => 'setMetricFilter(\'completedThisWeek\')','ariaPressed' => ''.e($quick === 'completedThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Completed This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['completedThisWeek'] ?? 0),'value-expression' => 'metrics.completedThisWeek ?? \'—\'','icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quick === 'completedThisWeek'),'wire:click' => 'setMetricFilter(\'completedThisWeek\')','aria-pressed' => ''.e($quick === 'completedThisWeek' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Needs Attention','value' => $metrics['attention'] ?? 0,'valueExpression' => 'metrics.attention ?? \'—\'','icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => $quick === 'attention','wire:click' => 'setMetricFilter(\'attention\')','ariaPressed' => ''.e($quick === 'attention' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['attention'] ?? 0),'value-expression' => 'metrics.attention ?? \'—\'','icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quick === 'attention'),'wire:click' => 'setMetricFilter(\'attention\')','aria-pressed' => ''.e($quick === 'attention' ? 'true' : 'false').'']); ?>
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

        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search tasks, Orders, clients or flags" aria-label="Search my work">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="phase-filters" aria-label="Filter by Order workflow phase">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phaseOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phaseOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button
                            type="button"
                            class="phase-toggle <?php echo e($phaseFilter === $phaseOption ? 'active' : ''); ?>"
                            wire:click="setPhaseFilter(<?php echo e(\Illuminate\Support\Js::from($phaseOption)); ?>)"
                            aria-pressed="<?php echo e($phaseFilter === $phaseOption ? 'true' : 'false'); ?>"
                            title="<?php echo e($phaseOption); ?>"
                        >
                            <span class="phase-check" aria-hidden="true">✓</span>
                            <span><?php echo e($phaseOption); ?></span>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
            <div class="toolbar-secondary">
                <div class="quick-filters">
                    <button type="button" class="chip <?php echo e($quick === 'mentions' ? 'active' : ''); ?>" wire:click="setQuick('<?php echo e($quick === 'mentions' ? 'my_tasks' : 'mentions'); ?>')">Mentions (<span x-text="metrics.mentions ?? '—'"><?php echo e($metrics['mentions'] ?? '—'); ?></span>)</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusFilter !== ''): ?>
                        <button type="button" class="chip active" wire:click="clearStatusFilter" title="Clear dashboard status filter">Status: <?php echo e($statusFilter); ?> ×</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <label class="completed-toggle <?php echo e($hideCompleted ? 'active' : ''); ?>">
                    <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed tasks">
                    <span class="completed-check" aria-hidden="true">✓</span>
                    <span>Hide completed</span>
                </label>
                <select class="sort" wire:model.live="sort" aria-label="Sort work">
                    <option value="action">Sort: Action priority</option>
                    <option value="due">Sort: Due soon</option>
                    <option value="job">Sort: Order number</option>
                </select>
                <button type="button" class="chip clear-filters" wire:click="clearFilters" <?php if($search === '' && $phaseFilter === '' && $statusFilter === '' && $quick === 'my_tasks' && !$hideCompleted): echo 'disabled'; endif; ?>>Clear filters</button>
            </div>
        </div>

        <div class="load-state">
            <span></span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.delay.long wire:target="search,phaseFilter,quick,sort,hideCompleted,setMetricFilter,setPhaseFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Updating tasks…</span>
                </span>
                <span class="group-controls" aria-label="Order group controls">
                    <button type="button" class="group-control" x-on:click="groupsExpanded = true" title="Expand all Orders" aria-label="Expand all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 6 5 5 5-5M5 11l5 5 5-5"/></svg>
                    </button>
                    <button type="button" class="group-control" x-on:click="groupsExpanded = false" title="Collapse all Orders" aria-label="Collapse all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 14 5-5 5 5M5 9l5-5 5 5"/></svg>
                    </button>
                </span>
            </span>
        </div>

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,phaseFilter,sort,hideCompleted,setMetricFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My Tasks grouped by Order" wire:loading.class="is-refreshing" wire:target="search,phaseFilter,sort,hideCompleted,setMetricFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage">
            <div class="task-table-scroll">
                <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>

                <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $workGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <article class="order-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-order-'.e($group['id']).''; ?>wire:key="my-work-order-<?php echo e($group['id']); ?>" x-data="{ open: true }" x-effect="open = groupsExpanded">
                        <header class="order-head">
                            <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Collapse <?php echo e($group['number']); ?>"><span x-text="open ? '⌄' : '›'">⌄</span></button>
                            <span class="order-identity">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?><a class="order-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a><?php else: ?><span class="order-id"><?php echo e($group['number']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="order-title"><?php echo e($group['title']); ?></span>
                            </span>
                            <span class="order-client"><?php echo e($group['client']); ?></span>
                            <span class="order-stage"><?php echo e($group['stage']); ?></span>
                            <span class="health <?php echo e($group['healthTone']); ?>"><?php echo e($group['health']); ?></span>
                            <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                            <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                        </header>

                        <div class="task-rows" x-show="open">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div
                                    class="task-row"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-task-'.e($task['id']).''; ?>wire:key="my-work-task-<?php echo e($task['id']); ?>"
                                    x-data="{
                                        saving:false,
                                        version:<?php echo \Illuminate\Support\Js::from($task['version'])->toHtml() ?>,
                                        currentStatus:<?php echo \Illuminate\Support\Js::from($task['status'])->toHtml() ?>,
                                        async saveStatus(event){
                                            const select=event.currentTarget;
                                            const previous=this.currentStatus;
                                            const next=select.value;
                                            if(next===previous||this.saving)return;
                                            this.saving=true;
                                            select.disabled=true;
                                            try{
                                                const result=await $wire.updateTaskStatus(<?php echo e($task['id']); ?>,next,this.version);
                                                if(!result?.ok){select.value=previous;window.FlowTrackMasterColor?.applySelect(select);return;}
                                                this.currentStatus=result.status||next;
                                                this.version=result.version||this.version;
                                                // Keep the renderless status update, but re-query once when
                                                // completion changes list membership. This removes the task now,
                                                // and removes its Order group too if it was the final visible task.
                                                if(result.completed && <?php echo \Illuminate\Support\Js::from($hideCompleted)->toHtml() ?>)await $wire.$refresh();
                                            }catch(error){select.value=previous;window.FlowTrackMasterColor?.applySelect(select);}
                                            finally{this.saving=false;select.disabled=false;}
                                        }
                                    }"
                                    x-bind:class="{ 'saving': saving }"
                                    x-on:my-work-task-version.stop="if ($event.detail?.version) version = String($event.detail.version)"
                                >
                                    <div class="task-main">
                                        <a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a>
                                        <span class="task-ref"><?php echo e($task['number']); ?></span>
                                    </div>
                                    <span class="phase ft-phase-color-label" data-label="Phase" style="<?php echo e(\App\Support\MasterColor::style($task['phaseColor'] ?? null)); ?>"><?php echo e($task['phase']); ?></span>
                                    <div
                                        class="assignee assignee-editor ft-inline-edit-shell"
                                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-task-'.e($task['id']).'-assignee-'.e($task['assigneeId'] ?: 0).''; ?>wire:key="my-work-task-<?php echo e($task['id']); ?>-assignee-<?php echo e($task['assigneeId'] ?: 0); ?>"
                                        data-label="Assignee"
                                        title="<?php echo e($task['assignee']); ?>"
                                        x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-assignee')->toHtml() ?>, label: 'task assignee', value: <?php echo \Illuminate\Support\Js::from($task['assigneeId'] ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['assignee'])->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($task['assigneeAvatar'] ?? '')->toHtml() ?> })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        x-on:click.outside="if (editing) cancelEdit()"
                                        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssignee(<?php echo e($task['id']); ?>, draftValue, version), { avatarUrl: String($event.detail?.avatarUrl ?? '') }).then(async (ok) => { if (!ok) return; if (lastResponse?.version) $dispatch('my-work-task-version', { version: lastResponse.version }); if (lastResponse?.refresh) await $wire.$refresh(); })"
                                    >
                                        <div class="assignee-display" x-show="!editing">
                                            <span class="assignee-avatar"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 22]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?></span>
                                            <span class="assignee-name" x-text="display"><?php echo e($task['assignee']); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canAssign']): ?>
                                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact assignee-edit-button" title="Edit assignee" aria-label="Edit assignee for <?php echo e($task['title']); ?>" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canAssign']): ?>
                                            <div x-cloak x-show="editing" class="assignee-picker">
                                                <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task['assigneeId'] ?? '','selectedLabel' => $task['assignee'],'parentType' => 'job','parentId' => $group['id'],'triggerClass' => 'assignee-picker-trigger','variant' => 'compact','menuWidth' => 280]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeId'] ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group['id']),'trigger-class' => 'assignee-picker-trigger','variant' => 'compact','menu-width' => 280]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
                                            </div>
                                            <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <span
                                        class="due-editor ft-inline-edit-shell <?php echo e($task['dueTone']); ?>" data-label="Due"
                                        x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task['dueValue'])->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['dueDisplay'])->toHtml() ?> })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    >
                                        <span x-show="!editing" x-text="display" class="ft-task-inline-display"><?php echo e($task['dueDisplay']); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canEdit']): ?>
                                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact" title="Edit due date" aria-label="Edit due date for <?php echo e($task['title']); ?>" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.myWorkDue.showPicker ? $refs.myWorkDue.showPicker() : $refs.myWorkDue.focus())">✎</button>
                                            <input x-ref="myWorkDue" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input" type="date"
                                                x-on:keydown.escape.prevent="cancelEdit()"
                                                x-on:blur="if (editing) cancelEdit()"
                                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDate(<?php echo e($task['id']); ?>, draftValue))">
                                            <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                    <span class="status-wrap" data-label="Status">
                                        <select data-master-color-select class="status-select <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event); window.FlowTrackMasterColor?.applySelect($event.currentTarget)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $statusOptions, true)): ?><option value="<?php echo e($task['status']); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status'])); ?>" selected><?php echo e($task['status']); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </span>
                                    <span class="flag <?php echo e($task['flagColor'] ? 'ft-master-color' : $task['flagTone']); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['flagColor'])); ?>" data-label="Flag"><?php echo e($task['flag']); ?></span>
                                    <span class="updated" data-label="Updated"><?php echo e($task['updated']); ?></span>
                                    <a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate><span class="row-action-desktop">Open</span><span class="row-action-mobile">Details</span><span class="row-action-arrow" aria-hidden="true">→</span></a>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workGroups->isEmpty()): ?>
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, or flag.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <footer class="footer">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workPaginator->total()): ?>
                        Orders <?php echo e($workPaginator->firstItem()); ?>–<?php echo e($workPaginator->lastItem()); ?> of <?php echo e($workPaginator->total()); ?> · <?php echo e($visibleTaskCount); ?> tasks on this page
                    <?php else: ?>
                        My Orders and tasks
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php
                    $currentPage = $workPaginator->currentPage();
                    $lastPage = max(1, $workPaginator->lastPage());
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                ?>
                <nav class="pages" aria-label="Pagination">
                    <button type="button" class="page-button" wire:click="previousPage('workPage')" <?php if($workPaginator->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="page-button <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'workPage')" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" class="page-button" wire:click="nextPage('workPage')" <?php if(!$workPaginator->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                </nav>
            </footer>
        </section>
    </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/my-work/index.blade.php ENDPATH**/ ?>