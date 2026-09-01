<?php if (isset($component)) { $__componentOriginalcfea599f97a0d6266449c21c198d875e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcfea599f97a0d6266449c21c198d875e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.management-theme','data' => ['class' => 'ft-mgmt-dashboard ft-mgmt-team-report-page']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.management-theme'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-dashboard ft-mgmt-team-report-page']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="ft-mgmt-page-head ft-mgmt-team-report-head">
        <div>
            <a class="ft-mgmt-team-report-back" href="<?php echo e(route('dashboard')); ?>" wire:navigate>← Dashboard</a>
            <h1>Team Performance Report</h1>
            <p>All user performance from actual Inquiry and Order task records in the selected reporting period.</p>
        </div>
    </div>

    <section class="ft-mgmt-panel ft-mgmt-team-report-filter-panel" aria-label="Team performance report filters">
        <div class="ft-mgmt-panel-body">
            <div class="ft-mgmt-team-report-filters">
                <label class="ft-mgmt-team-period">
                    <span>Reporting period</span>
                    <select wire:model.live="teamPeriod" aria-label="Team performance reporting period">
                        <option value="this_week">This week</option>
                        <option value="this_month">This month</option>
                        <option value="last_30_days">Last 30 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                </label>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teamPeriod === 'custom'): ?>
                    <div class="ft-mgmt-team-custom-range">
                        <label><span>From</span><input type="date" wire:model.live="teamCustomFrom" aria-label="Custom reporting period start"></label>
                        <label><span>To</span><input type="date" wire:model.live="teamCustomTo" aria-label="Custom reporting period end"></label>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-mgmt-remote-filter ft-mgmt-team-report-remote-filter','label' => 'Client','property' => 'clientFilter','type' => 'clients','context' => 'dashboard','action' => 'setReportFilter','value' => $clientFilter,'placeholder' => 'All clients','initialOptions' => $reportClientFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'team-report-client-filter-'.e($clientFilter ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-remote-filter ft-mgmt-team-report-remote-filter','label' => 'Client','property' => 'clientFilter','type' => 'clients','context' => 'dashboard','action' => 'setReportFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilter),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reportClientFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'team-report-client-filter-'.e($clientFilter ?: 'all').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-mgmt-remote-filter ft-mgmt-team-report-remote-filter','label' => 'Team','property' => 'teamFilter','type' => 'departments','context' => 'dashboard','action' => 'setReportFilter','value' => $teamFilter,'placeholder' => 'All teams','initialOptions' => $reportTeamFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'team-report-team-filter-'.e($teamFilter ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-remote-filter ft-mgmt-team-report-remote-filter','label' => 'Team','property' => 'teamFilter','type' => 'departments','context' => 'dashboard','action' => 'setReportFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($teamFilter),'placeholder' => 'All teams','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reportTeamFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'team-report-team-filter-'.e($teamFilter ?: 'all').'']); ?>
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

                <label class="ft-mgmt-team-report-sort">
                    <span>Sort by</span>
                    <select wire:model.live="sort" aria-label="Sort team performance">
                        <option value="performance">Top performance</option>
                        <option value="workload">Workload</option>
                        <option value="name">Name</option>
                    </select>
                </label>

                <label class="ft-mgmt-team-report-search">
                    <span>Search</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search employee" aria-label="Search employee performance">
                </label>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientFilter !== '' || $teamFilter !== '' || $search !== '' || $sort !== 'performance'): ?>
                    <button type="button" class="ft-mgmt-btn ft-mgmt-team-report-clear" wire:click="clearFilters">Clear filters</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="ft-mgmt-panel ft-mgmt-team-panel ft-mgmt-team-report-results">
        <div class="ft-mgmt-panel-head ft-mgmt-team-report-results-head">
            <div>
                <h2>All team performance</h2>
                <p><?php echo e($resultCount); ?> <?php echo e($resultCount === 1 ? 'user' : 'users'); ?> in the current report.</p>
            </div>
            <div class="ft-mgmt-team-report-period-summary">
                <strong><?php echo e($teamReportingPeriod['label'] ?? 'This week'); ?></strong>
                <span>Live task totals · current assignee · cancelled/deleted tasks excluded</span>
            </div>
        </div>
        <div class="ft-mgmt-panel-body">
            <div class="ft-mgmt-team-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assigneePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal09bad63cc66db31fb9cc464e04232869 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09bad63cc66db31fb9cc464e04232869 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.team-performance-card','data' => ['person' => $person,'wire:key' => 'team-report-person-'.e($person->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard.team-performance-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['person' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($person),'wire:key' => 'team-report-person-'.e($person->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09bad63cc66db31fb9cc464e04232869)): ?>
<?php $attributes = $__attributesOriginal09bad63cc66db31fb9cc464e04232869; ?>
<?php unset($__attributesOriginal09bad63cc66db31fb9cc464e04232869); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09bad63cc66db31fb9cc464e04232869)): ?>
<?php $component = $__componentOriginal09bad63cc66db31fb9cc464e04232869; ?>
<?php unset($__componentOriginal09bad63cc66db31fb9cc464e04232869); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-mgmt-empty ft-mgmt-team-report-empty">No team performance matches the selected filters and reporting period.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resultCount > 0): ?>
            <div class="ft-mgmt-team-report-lazy" aria-label="Team performance lazy loading">
                <span class="ft-mgmt-priority-page-status">
                    Showing <?php echo e($visibleCount); ?> of <?php echo e($resultCount); ?> <?php echo e($resultCount === 1 ? 'user' : 'users'); ?>

                </span>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreTeamPerformance): ?>
                    <button
                        type="button"
                        class="ft-mgmt-team-report-load-more"
                        wire:click="loadMoreTeamPerformance"
                        wire:loading.attr="disabled"
                        wire:target="loadMoreTeamPerformance"
                    >
                        <span wire:loading.remove wire:target="loadMoreTeamPerformance">Load <?php echo e($nextTeamBatchCount); ?> more</span>
                        <span wire:loading wire:target="loadMoreTeamPerformance">Loading…</span>
                    </button>
                <?php else: ?>
                    <span class="ft-mgmt-team-report-all-loaded">All users loaded</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/team-performance/report.blade.php ENDPATH**/ ?>