<div>
    <?php if (isset($component)) { $__componentOriginal8f6938ac62d0a39f318af1c1674a1814 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.page-head','data' => ['title' => 'Reports','subtitle' => 'Operational performance, delivery, workload and task completion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reports','subtitle' => 'Operational performance, delivery, workload and task completion']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('reports','export')): ?>
                <button class="ghost">Export PDF</button>
                <button class="primary">Export Excel</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $attributes = $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $component = $__componentOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>

<?php
    $kpis = $this->kpis;
?>
        <div class="metrics ft-auto-metrics">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                ['Active Jobs',$kpis['active_jobs'],'Current portfolio'],
                ['On-time Jobs',$kpis['on_time'].'%','Completed by delivery date'],
                ['Task Completion',$kpis['task_completion'].'%',$kpis['task_done'].' completed tasks'],
                ['Avg. Artwork Cycle',number_format($kpis['avg_artwork_cycle'],1).'d','Completed artwork phases'],
                ['Shipment On Time',$kpis['shipment_on_time'].'%','Completed shipment phases'],
                ['Overdue Tasks',$kpis['overdue_tasks'],'Open tasks past due']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="card metric">
                    <div class="metric-label"><?php echo e($metric[0]); ?></div>
                    <div class="metric-value"><?php echo e($metric[1]); ?></div>
                    <div class="metric-foot"><?php echo e($metric[2]); ?></div>
                    <div class="metric-icon">◎</div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

    <div class="report-grid">
        <div class="ft-island-shell">
<?php
    $phase = $this->phase;
?>
                <div class="card section-card">
                    <div class="section-head"><h3>Active Jobs by Phase</h3><span class="small muted">Current portfolio</span></div>
                    <div class="bars">
                        <?php
                            $max = max(1, $phase->max('total') ?? 1);
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phase->sortBy(fn($row) => $row->phase?->sequence ?? PHP_INT_MAX); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="bar-row"><span><?php echo e($row->phase?->short_name ?? 'Unassigned'); ?></span><div class="bar"><span style="width:<?php echo e($row->total/$max*100); ?>%"></span></div><b><?php echo e($row->total); ?></b></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="empty-state">No active Jobs.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
        </div>

        <div class="ft-island-shell">
<?php
    $workload = $this->workload;
?>
                <div class="card section-card">
                    <div class="section-head"><h3>Team Workload</h3><span class="small muted">Open tasks</span></div>
                    <div class="bars">
                        <?php
                            $workloadMax = max(1, $workload->max('open_tasks_count') ?? 1);
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $workload; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="bar-row"><span><?php echo e($person->name); ?></span><div class="bar"><span style="width:<?php echo e($person->open_tasks_count/$workloadMax*100); ?>%"></span></div><b><?php echo e($person->open_tasks_count); ?></b></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="empty-state">No open assigned tasks.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
        </div>

        <div class="ft-island-shell ft-report-performance">
                <div class="card section-card">
                    <div class="section-head"><h3>Delivery Performance</h3><span class="small muted">Calculated from current Job, task and phase-history records</span></div>
                    <div class="kpi-list ft-report-kpis">
                        <div class="kpi"><b><?php echo e($kpis['on_time']); ?>%</b><span>Jobs delivered on time</span></div>
                        <div class="kpi"><b><?php echo e($kpis['completed_jobs']); ?></b><span>Completed Jobs</span></div>
                        <div class="kpi"><b><?php echo e($kpis['task_done']); ?></b><span>Completed tasks</span></div>
                        <div class="kpi"><b><?php echo e($kpis['shipment_on_time']); ?>%</b><span>Shipment phases on time</span></div>
                        <div class="kpi"><b><?php echo e(number_format($kpis['avg_artwork_cycle'],1)); ?>d</b><span>Average artwork cycle</span></div>
                        <div class="kpi"><b><?php echo e($kpis['overdue_tasks']); ?></b><span>Overdue open tasks</span></div>
                    </div>
                </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/reports/index.blade.php ENDPATH**/ ?>