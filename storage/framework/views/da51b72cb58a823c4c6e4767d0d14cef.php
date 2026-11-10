    <?php
        $teamReportParams = array_filter([
            // Keep "View all" aligned with the dashboard's global period. The
            // full report still supports its own reporting-period controls after
            // the user leaves the dashboard.
            'period' => 'custom',
            'from' => $teamReportingPeriod['from'] ?? null,
            'to' => $teamReportingPeriod['to'] ?? null,
            'client' => $clientFilter,
            'department' => $teamFilter,
            'q' => $search,
        ], static fn ($value) => $value !== null && $value !== '');
    ?>
    <article class="ft-mgmt-panel ft-mgmt-team-panel ft-mgmt-panel-spaced" data-dashboard-panel="team">
    <div class="ft-mgmt-dashboard-panel-swap" style="display:contents" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-team-content'; ?>wire:key="dashboard-team-content">
        <div class="ft-mgmt-panel-head ft-mgmt-team-panel-head">
            <div>
                <h2>Team performance &amp; workload</h2>
                <p>Top 4 assignees from actual Inquiry and Order task records · <?php echo e($teamReportingPeriod['label'] ?? 'Last 7 days'); ?>.</p>
            </div>
            <a class="ft-mgmt-btn ft-mgmt-team-view-all" href="<?php echo e(route('team-performance.report', $teamReportParams)); ?>" wire:navigate>View all</a>
        </div>
        <div class="ft-mgmt-panel-body">
            <div class="ft-mgmt-team-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assigneePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal09bad63cc66db31fb9cc464e04232869 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09bad63cc66db31fb9cc464e04232869 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.team-performance-card','data' => ['person' => $person,'wire:key' => 'mgmt-person-'.e($person->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard.team-performance-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['person' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($person),'wire:key' => 'mgmt-person-'.e($person->id).'']); ?>
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
                    <div class="ft-mgmt-empty">No team workload matches the current dashboard filters and period.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/team-panel.blade.php ENDPATH**/ ?>