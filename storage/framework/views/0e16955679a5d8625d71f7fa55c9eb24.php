<div
    id="my-work-app"
    x-data="{ metrics: <?php echo \Illuminate\Support\Js::from($taskPackMetrics)->toHtml() ?>, groupsExpanded: true }"
    x-on:board-task-metrics.window="metrics = $event.detail"
>
<div class="page-head">
        <div>
            <h1>All Tasks</h1>
            <p><?php echo e($taskPackAdministratorView
                ? 'All active Job tasks, grouped by Order and ranked by what needs action first.'
                : 'Tasks from Jobs associated with your assigned work, grouped by Order and ranked by what needs action first.'); ?></p>
        </div>
    </div>

    <section class="work-view" aria-busy="false">
        <div class="metrics ft-summary-card-grid" aria-label="All Tasks summary filters">
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Created Today','value' => $taskPackMetrics['createdToday'] ?? 0,'valueExpression' => 'metrics.createdToday ?? \'—\'','icon' => 'created','tone' => 'blue','caption' => 'Tasks created today','active' => $taskQuick === 'createdToday','wire:click' => 'setTaskMetricFilter(\'createdToday\')','ariaPressed' => ''.e($taskQuick === 'createdToday' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Created Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['createdToday'] ?? 0),'value-expression' => 'metrics.createdToday ?? \'—\'','icon' => 'created','tone' => 'blue','caption' => 'Tasks created today','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'createdToday'),'wire:click' => 'setTaskMetricFilter(\'createdToday\')','aria-pressed' => ''.e($taskQuick === 'createdToday' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Not Started','value' => $taskPackMetrics['notStarted'] ?? 0,'valueExpression' => 'metrics.notStarted ?? \'—\'','icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => $taskQuick === 'notStarted','wire:click' => 'setTaskMetricFilter(\'notStarted\')','ariaPressed' => ''.e($taskQuick === 'notStarted' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Not Started','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['notStarted'] ?? 0),'value-expression' => 'metrics.notStarted ?? \'—\'','icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'notStarted'),'wire:click' => 'setTaskMetricFilter(\'notStarted\')','aria-pressed' => ''.e($taskQuick === 'notStarted' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'In Progress','value' => $taskPackMetrics['inProgress'] ?? 0,'valueExpression' => 'metrics.inProgress ?? \'—\'','icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => $taskQuick === 'inProgress','wire:click' => 'setTaskMetricFilter(\'inProgress\')','ariaPressed' => ''.e($taskQuick === 'inProgress' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['inProgress'] ?? 0),'value-expression' => 'metrics.inProgress ?? \'—\'','icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'inProgress'),'wire:click' => 'setTaskMetricFilter(\'inProgress\')','aria-pressed' => ''.e($taskQuick === 'inProgress' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Due This Week','value' => $taskPackMetrics['dueThisWeek'] ?? 0,'valueExpression' => 'metrics.dueThisWeek ?? \'—\'','icon' => 'due-week','tone' => 'amber','caption' => 'Tasks due this week','active' => $taskQuick === 'dueThisWeek','wire:click' => 'setTaskMetricFilter(\'dueThisWeek\')','ariaPressed' => ''.e($taskQuick === 'dueThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['dueThisWeek'] ?? 0),'value-expression' => 'metrics.dueThisWeek ?? \'—\'','icon' => 'due-week','tone' => 'amber','caption' => 'Tasks due this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'dueThisWeek'),'wire:click' => 'setTaskMetricFilter(\'dueThisWeek\')','aria-pressed' => ''.e($taskQuick === 'dueThisWeek' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Completed This Week','value' => $taskPackMetrics['completedThisWeek'] ?? 0,'valueExpression' => 'metrics.completedThisWeek ?? \'—\'','icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => $taskQuick === 'completedThisWeek','wire:click' => 'setTaskMetricFilter(\'completedThisWeek\')','ariaPressed' => ''.e($taskQuick === 'completedThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Completed This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['completedThisWeek'] ?? 0),'value-expression' => 'metrics.completedThisWeek ?? \'—\'','icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'completedThisWeek'),'wire:click' => 'setTaskMetricFilter(\'completedThisWeek\')','aria-pressed' => ''.e($taskQuick === 'completedThisWeek' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Needs Attention','value' => $taskPackMetrics['attention'] ?? 0,'valueExpression' => 'metrics.attention ?? \'—\'','icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => $taskQuick === 'attention','wire:click' => 'setTaskMetricFilter(\'attention\')','ariaPressed' => ''.e($taskQuick === 'attention' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['attention'] ?? 0),'value-expression' => 'metrics.attention ?? \'—\'','icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'attention'),'wire:click' => 'setTaskMetricFilter(\'attention\')','aria-pressed' => ''.e($taskQuick === 'attention' ? 'true' : 'false').'']); ?>
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

        <?php
            $allTaskStageColors = collect(\App\Services\OrderWorkflowSetupService::fixedStages())
                ->mapWithKeys(fn (array $stage) => [
                    mb_strtolower(trim((string) ($stage['name'] ?? ''))) => (string) ($stage['color'] ?? '#0F8F7C'),
                ]);
        ?>
        <div class="toolbar ft-list-filter-bar all-tasks-filter-bar">
            <label class="search-wrap all-tasks-search-row">
                <span class="search-icon">⌕</span>
                <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search tasks, Orders, clients, assignees or flags" aria-label="Search All Tasks">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearTaskSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
            <div class="phase-filters all-tasks-stage-filters" aria-label="Filter by Order workflow phase">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPackPhaseOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phaseOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $phaseColor = $allTaskStageColors->get(mb_strtolower(trim((string) $phaseOption)), '#0F8F7C');
                    ?>
                    <button
                        type="button"
                        class="phase-toggle <?php echo e($taskPhaseFilter === $phaseOption ? 'active' : ''); ?>"
                        style="--phase-color: <?php echo e($phaseColor); ?>"
                        wire:click="setTaskPhaseFilter(<?php echo e(\Illuminate\Support\Js::from($phaseOption)); ?>)"
                        aria-pressed="<?php echo e($taskPhaseFilter === $phaseOption ? 'true' : 'false'); ?>"
                        title="<?php echo e($phaseOption); ?>"
                    >
                        <span class="phase-check" aria-hidden="true">✓</span>
                        <span><?php echo e($phaseOption); ?></span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <div class="toolbar-secondary all-tasks-secondary-filters">
                <div class="quick-filters">
                    <button type="button" class="chip <?php echo e($taskQuick === 'mentions' ? 'active' : ''); ?>" wire:click="setTaskQuick('<?php echo e($taskQuick === 'mentions' ? 'all' : 'mentions'); ?>')">Mentions (<span x-text="metrics.mentions ?? '—'"><?php echo e($taskPackMetrics['mentions'] ?? '—'); ?></span>)</button>
                </div>
                <label class="completed-toggle <?php echo e($hideCompleted ? 'active' : ''); ?>">
                    <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed tasks">
                    <span class="completed-check" aria-hidden="true">✓</span>
                    <span>Hide completed</span>
                </label>
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'assignee-filter','label' => 'Assignee','property' => 'assignee','value' => $assignee,'placeholder' => 'Assignee','options' => collect([['id' => 'unassigned', 'label' => 'Unassigned']])->concat(
                        $assigneeFilterOptions->map(fn ($option) => [
                            'id' => (string) $option->id,
                            'label' => $option->name,
                        ])
                    ),'searchPlaceholder' => 'Search assignee…','hideLabel' => true,'fixedMenu' => true,'menuWidth' => 300,'wire:key' => 'all-tasks-assignee-filter-'.e($assignee ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'assignee-filter','label' => 'Assignee','property' => 'assignee','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assignee),'placeholder' => 'Assignee','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect([['id' => 'unassigned', 'label' => 'Unassigned']])->concat(
                        $assigneeFilterOptions->map(fn ($option) => [
                            'id' => (string) $option->id,
                            'label' => $option->name,
                        ])
                    )),'search-placeholder' => 'Search assignee…','hide-label' => true,'fixed-menu' => true,'menu-width' => 300,'wire:key' => 'all-tasks-assignee-filter-'.e($assignee ?: 'all').'']); ?>
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
                <select class="sort" wire:model.live="taskSort" aria-label="Sort All Tasks">
                    <option value="action">Sort: Action priority</option>
                    <option value="due">Sort: Due soon</option>
                    <option value="job">Sort: Order number</option>
                </select>
                <button
                    type="button"
                    class="clear-filters all-tasks-clear-filters"
                    wire:click="clearFilters"
                    title="Clear filters"
                    aria-label="Clear filters"
                    <?php if($search === '' && $taskPhaseFilter === '' && $taskQuick === 'all' && $hideCompleted && $assignee === '' && $job === '' && $client === '' && $status === '' && $due === ''): echo 'disabled'; endif; ?>
                >
                    <span class="clear-filter-icon" aria-hidden="true">×</span>
                    <span>Clear filters</span>
                </button>
            </div>
        </div>

        <div class="load-state">
            <span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskPackPaginator && $taskPackPaginator->total()): ?>
                    Showing <?php echo e($taskPackGroups->count()); ?> of <?php echo e($taskPackPaginator->total()); ?> matching Orders · <?php echo e($taskPackTaskCount); ?> visible tasks
                <?php elseif($taskPackAdministratorView): ?>
                    Showing all active Job Task Packs
                <?php else: ?>
                    Showing associated Job Task Packs only
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.remove wire:target="search,assignee,taskPhaseFilter,taskQuick,taskSort,hideCompleted,setTaskMetricFilter,setTaskPhaseFilter,setTaskQuick,clearFilters,clearTaskSearch,gotoPage,previousPage,nextPage">Results update after 650 ms</span>
                    <span wire:loading.delay.long wire:target="search,assignee,taskPhaseFilter,taskQuick,taskSort,hideCompleted,setTaskMetricFilter,setTaskPhaseFilter,setTaskQuick,clearFilters,clearTaskSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Searching all visible work…</span>
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

        <section class="list-shell" aria-label="All Tasks grouped by Order">
            <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>

            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $taskPackGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <article class="order-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-order-'.e($group['id']).''; ?>wire:key="board-task-order-<?php echo e($group['id']); ?>" x-data="{ open: true }" x-effect="open = groupsExpanded">
                        <header class="order-head">
                            <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Collapse <?php echo e($group['number']); ?>"><span x-text="open ? '⌄' : '›'">⌄</span></button>
                            <span class="order-identity">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?><a class="order-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a><?php else: ?><span class="order-id"><?php echo e($group['number']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="order-title"><?php echo e($group['title']); ?></span>
                            </span>
                            <span class="order-client"><?php echo e($group['client']); ?></span>
                            <span class="order-stage"><?php echo e($group['stage']); ?></span>
                            <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                            <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                        </header>

                        <div class="task-rows" x-show="open">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div
                                    class="task-row"
                                    style="<?php echo e(\App\Support\MasterColor::style($task['taskColor'] ?? null)); ?>border-left:4px solid var(--ft-master-color,#2563EB)"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-'.e($task['id']).''; ?>wire:key="board-task-<?php echo e($task['id']); ?>"
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
                                                if(!result?.ok){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);return;}
                                                this.currentStatus=result.status||next;
                                                this.version=result.version||this.version;
                                                if(result.metrics)window.dispatchEvent(new CustomEvent('board-task-metrics',{detail:result.metrics}));
                                                // Status saves are normally renderless for speed. When a task is
                                                // completed while Hide completed is active, refresh the grouped
                                                // list once so the completed row disappears immediately and the
                                                // Order disappears too when it no longer has any visible tasks.
                                                if(result.completed && <?php echo \Illuminate\Support\Js::from($hideCompleted)->toHtml() ?>)await $wire.$refresh();
                                            }catch(error){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);}
                                            finally{this.saving=false;select.disabled=false;}
                                        }
                                    }"
                                    x-bind:class="{ 'saving': saving }"
                                >
                                    <div class="task-main">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?><a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a><?php else: ?><span class="task-link"><?php echo e($task['title']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="task-ref"><?php echo e($task['number']); ?></span>
                                    </div>
                                    <span class="phase ft-phase-color-label" style="<?php echo e(\App\Support\MasterColor::style($task['phaseColor'] ?? null)); ?>"><?php echo e($task['phase']); ?></span>
                                    <span class="assignee" title="<?php echo e($task['assignee']); ?>">
                                        <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task['assignee'],'src' => $task['assigneeImage'],'size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeImage']),'size' => 22]); ?>
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
<?php endif; ?>
                                        <span class="assignee-name"><?php echo e($task['assignee']); ?></span>
                                    </span>
                                    <time class="due <?php echo e($task['dueTone']); ?>"><?php echo e($task['due']); ?></time>
                                    <select data-master-color-select class="status-select <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event); window.FlowTrack.ui.masterColor?.applySelect($event.currentTarget)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $taskPackStatusOptions, true)): ?><option value="<?php echo e($task['status']); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status'])); ?>" selected><?php echo e($task['status']); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPackStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <span class="flag <?php echo e($task['flagColor'] ? 'ft-master-color' : $task['flagTone']); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['flagColor'])); ?>"><?php echo e($task['flag']); ?></span>
                                    <span class="updated"><?php echo e($task['updated']); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?><a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a><?php else: ?><span class="row-action" aria-disabled="true">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, assignee, or flag.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <footer class="footer">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskPackPaginator && $taskPackPaginator->total()): ?>
                        Orders <?php echo e($taskPackPaginator->firstItem()); ?>–<?php echo e($taskPackPaginator->lastItem()); ?> of <?php echo e($taskPackPaginator->total()); ?> · <?php echo e($taskPackTaskCount); ?> tasks on this page
                    <?php elseif($taskPackAdministratorView): ?>
                        All active Job tasks
                    <?php else: ?>
                        Associated Job tasks
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php
                    $currentPage = $taskPackPaginator?->currentPage() ?? 1;
                    $lastPage = max(1, $taskPackPaginator?->lastPage() ?? 1);
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                ?>
                <nav class="pages" aria-label="Pagination">
                    <button type="button" class="page-button" wire:click="previousPage('taskPackPage')" <?php if(!$taskPackPaginator || $taskPackPaginator->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="page-button <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'taskPackPage')" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" class="page-button" wire:click="nextPage('taskPackPage')" <?php if(!$taskPackPaginator || !$taskPackPaginator->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                </nav>
            </footer>
        </section>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/board/index.blade.php ENDPATH**/ ?>