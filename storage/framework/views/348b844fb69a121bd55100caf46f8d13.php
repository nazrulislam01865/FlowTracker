<?php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $statusTone = static function (?string $status): string {
        $value = strtolower((string) $status);
        if (str_contains($value, 'wait') || str_contains($value, 'risk')) return 'amber';
        if (str_contains($value, 'revision') || str_contains($value, 'artwork')) return 'purple';
        if (str_contains($value, 'not started')) return 'gray';
        if (str_contains($value, 'progress') || str_contains($value, 'production') || str_contains($value, 'request')) return 'blue';
        return '';
    };
    $masterData = app(\App\Services\MasterDataService::class);
    $taskFlagService = app(\App\Services\TaskFlagService::class);
    $administrator = app(\App\Services\AccessControlService::class)->isAdministrator(auth()->user());
    $orderTerminology = static function (?string $value): string {
        return preg_replace_callback('/\bjobs?\b/i', static function (array $match): string {
            return match ($match[0]) {
                'Jobs' => 'Orders',
                'jobs' => 'orders',
                'JOB' => 'ORDER',
                'JOBS' => 'ORDERS',
                default => ctype_upper($match[0][0] ?? '') ? 'Order' : 'order',
            };
        }, (string) $value) ?: (string) $value;
    };
    $taskFlag = static function ($task) use ($masterData, $taskFlagService): array {
        $label = $taskFlagService->labelForTask($task);
        if ($label) return [$label, 'amber', $masterData->displayColorFor('order_task_flag', $label)];
        return ['No flag', 'green', null];
    };
    $jobFlag = static function ($job) use ($masterData, $taskFlagService): array {
        if ((bool) ($job->attention_requested ?? false)) return ['Requires attention', 'red', null];
        $label = $taskFlagService->labelForOrder($job);
        if ($label) return [$label, 'amber', $masterData->displayColorFor('order_flag', $label)];
        return ['No flag', 'green', null];
    };
?>

<div class="ft-dashboard-secondary-sections">
    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel ft-dashboard-assignee-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Assignee performance</h2><div class="ft-panel-note">Open and completed Inquiry + Order tasks for the selected reporting cohort</div></div></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-assignee-table">
                    <colgroup><col class="ft-dashboard-col--29"><col class="ft-dashboard-col--16"><col class="ft-dashboard-col--18"><col class="ft-dashboard-col--19"><col class="ft-dashboard-col--18"></colgroup>
                    <thead><tr><th>Assignee</th><th>Open</th><th>Completed</th><th>On time</th><th>Workload</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assigneePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $onTime = $person->on_time_rate;
                                $workloadPct = min(100, max(8, (int) $person->ongoing_count * 12));
                                $workloadLabel = $person->ongoing_count >= 8 ? 'High' : ($person->ongoing_count >= 5 ? 'Med' : 'Good');
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-assignee-'.e($person->id).''; ?>wire:key="dashboard-assignee-<?php echo e($person->id); ?>">
                                <td data-label="Assignee"><span class="ft-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $person,'name' => $person->name,'size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($person),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($person->name),'size' => 22]); ?>
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
<?php endif; ?><span class="ft-cell-clip"><?php echo e($person->name); ?></span></span></td>
                                <td data-label="Open">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($administrator): ?>
                                        <a class="ft-text-link" href="<?php echo e(route('all-tasks', ['assignee' => $person->id])); ?>" wire:navigate><?php echo e($person->ongoing_count); ?> ↗</a>
                                    <?php else: ?>
                                        <?php echo e($person->ongoing_count); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td data-label="Completed"><?php echo e($person->done_count); ?></td>
                                <td data-label="On time"><?php echo e($onTime === null ? '—' : $onTime.'%'); ?></td>
                                <td data-label="Workload"><span class="ft-load"><i class="ft-load-track"><span style="width:<?php echo e($workloadPct); ?>%"></span></i><?php echo e($workloadLabel); ?></span></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="5">No active assignee workload.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-panel ft-dashboard-attention-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Needs attention</h2><div class="ft-panel-note">Highest-priority tasks across current orders</div></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($administrator): ?><a class="ft-link" href="<?php echo e(route('all-tasks')); ?>" wire:navigate>View all tasks</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            <div class="ft-risk-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attentionTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        [$flagLabel, $flagTone, $flagColor] = $taskFlag($task);
                    ?>
                    <div class="ft-risk" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-risk-'.e($task->id).''; ?>wire:key="dashboard-risk-<?php echo e($task->id); ?>">
                        <a class="ft-risk-name ft-text-link" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a>
                        <span class="ft-flag <?php echo e($flagColor ? 'ft-master-color' : $flagTone); ?>" style="<?php echo e(\App\Support\MasterColor::style($flagColor)); ?>"><?php echo e($flagLabel); ?></span>
                        <span class="ft-risk-meta"><?php echo e($task->task_number); ?> · <?php echo e($task->job?->displayOrderNumber() ?? 'Order'); ?> · <?php echo e($task->assignee?->name ?? 'Unassigned'); ?> · <?php echo e($task->due_date ? 'Due '.$task->due_date->format('M j') : 'No due date'); ?></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-panel-empty">No tasks currently need attention.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel ft-dashboard-jobs-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Ongoing Orders</h2><div class="ft-panel-note">Current stage and exception flags</div></div><a class="ft-link" href="<?php echo e(route('jobs.index')); ?>" wire:navigate>View orders</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-jobs-table">
                    <colgroup><col class="ft-dashboard-col--31"><col class="ft-dashboard-col--18"><col class="ft-dashboard-col--23"><col class="ft-dashboard-col--18"><col class="ft-dashboard-col--10"></colgroup>
                    <thead><tr><th>Order</th><th>Client</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ongoingJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                [$flagLabel, $flagTone, $flagColor] = $jobFlag($job);
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-job-'.e($job->id).''; ?>wire:key="dashboard-job-<?php echo e($job->id); ?>">
                                <td data-label="Order"><a class="ft-text-link ft-cell-clip" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->title); ?></a><span class="ft-ref"><?php echo e($job->displayOrderNumber()); ?></span></td>
                                <td data-label="Client"><span class="ft-client-name-with-logo"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
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
<?php endif; ?><span class="ft-cell-clip"><?php echo e($job->client?->name ?? '—'); ?></span></span></td>
                                <td data-label="Status"><?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $job->phase,'short' => true,'fallback' => 'Unassigned','class' => 'ft-pill']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->phase),'short' => true,'fallback' => 'Unassigned','class' => 'ft-pill']); ?>
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
<?php endif; ?></td>
                                <td data-label="Flag"><span class="ft-flag <?php echo e($flagColor ? 'ft-master-color' : $flagTone); ?>" style="<?php echo e(\App\Support\MasterColor::style($flagColor)); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td data-label="View"><a class="ft-view" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="5">No ongoing orders.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-panel ft-dashboard-tasks-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Ongoing tasks</h2><div class="ft-panel-note">Tasks before Done with current work status and flags</div></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($administrator): ?><a class="ft-link" href="<?php echo e(route('all-tasks')); ?>" wire:navigate>Open all tasks</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-tasks-table">
                    <colgroup><col class="ft-dashboard-col--29"><col class="ft-dashboard-col--13"><col class="ft-dashboard-col--17"><col class="ft-dashboard-col--20"><col class="ft-dashboard-col--13"><col class="ft-dashboard-col--8"></colgroup>
                    <thead><tr><th>Task</th><th>Order</th><th>Assignee</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ongoingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                        [$flagLabel, $flagTone, $flagColor] = $taskFlag($task);
                    ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-task-'.e($task->id).''; ?>wire:key="dashboard-task-<?php echo e($task->id); ?>">
                                <td data-label="Task"><a class="ft-text-link ft-cell-clip" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a><span class="ft-ref"><?php echo e($task->task_number); ?></span></td>
                                <td data-label="Order"><a class="ft-text-link" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id])); ?>" wire:navigate><?php echo e(str($task->job?->displayOrderNumber() ?? '—')->afterLast('-')); ?></a></td>
                                <td data-label="Assignee"><span class="ft-cell-clip"><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></span></td>
                                <td data-label="Status"><?php $taskStatusColor = $masterData->colorFor('order_task_status', (string) $task->status); ?><span class="ft-pill <?php echo e($taskStatusColor ? 'ft-master-color' : $statusTone($task->status)); ?>" style="<?php echo e(\App\Support\MasterColor::style($taskStatusColor)); ?>"><?php echo e($task->status); ?></span></td>
                                <td data-label="Flag"><span class="ft-flag <?php echo e($flagColor ? 'ft-master-color' : $flagTone); ?>" style="<?php echo e(\App\Support\MasterColor::style($flagColor)); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td data-label="View"><a class="ft-view" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="6">No ongoing tasks.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Recent activity</h2><div class="ft-panel-note">Latest order, task, inquiry, document and comment events</div></div><a class="ft-link" href="<?php echo e(route('notifications')); ?>" wire:navigate>All activity</a></div>
            <div class="ft-activity-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-activity" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-activity-'.e($notification->id).''; ?>wire:key="dashboard-activity-<?php echo e($notification->id); ?>">
                        <span class="ft-activity-icon"><?php echo e(in_array($notification->type, ['mention', 'mention_admin'], true) ? '@' : '✓'); ?></span>
                        <span><strong><?php echo e($orderTerminology($notification->title)); ?></strong><span class="ft-activity-copy"><?php echo e(app(\App\Services\MentionService::class)->displayText($notification->message)); ?></span></span>
                        <time class="ft-activity-time"><?php echo e($notification->created_at?->diffForHumans(short: true)); ?></time>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-panel-empty">No recent activity.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <section class="ft-panel ft-dashboard-clients-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Client portfolio</h2><div class="ft-panel-note">Active work, inquiry volume and delivery performance</div></div><a class="ft-link" href="<?php echo e(route('clients.index')); ?>" wire:navigate>All clients</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive ft-dashboard-clients-table">
                    <colgroup><col class="ft-dashboard-col--28"><col class="ft-dashboard-col--15"><col class="ft-dashboard-col--18"><col class="ft-dashboard-col--19"><col class="ft-dashboard-col--20"></colgroup>
                    <thead><tr><th>Client</th><th>Orders</th><th>Inquiries</th><th>Attention</th><th>On time</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $clientPortfolio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolioClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $portfolioOpenTasks = (int) ($portfolioClient->open_tasks_count ?? 0);
                                $portfolioOverdueTasks = (int) ($portfolioClient->overdue_tasks_count ?? 0);
                                $portfolioAttentionJobs = (int) ($portfolioClient->attention_jobs_count ?? 0);
                                $portfolioOnTime = $portfolioOpenTasks > 0
                                    ? max(0, (int) round((($portfolioOpenTasks - $portfolioOverdueTasks) / $portfolioOpenTasks) * 100))
                                    : 100;
                                $portfolioAttentionTone = $portfolioAttentionJobs > 1 ? 'red' : ($portfolioAttentionJobs === 1 ? 'amber' : 'green');
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-client-portfolio-'.e($portfolioClient->id).''; ?>wire:key="dashboard-client-portfolio-<?php echo e($portfolioClient->id); ?>">
                                <td data-label="Client"><a class="ft-text-link ft-dashboard-client-logo-link" href="<?php echo e(route('clients.index')); ?>" wire:navigate><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $portfolioClient,'name' => $portfolioClient->name,'size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portfolioClient),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portfolioClient->name),'size' => 24]); ?>
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
<?php endif; ?><span><?php echo e($portfolioClient->name); ?></span></a></td>
                                <td data-label="Orders"><a class="ft-text-link" href="<?php echo e(route('jobs.index', ['client' => $portfolioClient->id])); ?>" wire:navigate><?php echo e((int) ($portfolioClient->active_jobs_count ?? 0)); ?> ↗</a></td>
                                <td data-label="Inquiries"><a class="ft-text-link" href="<?php echo e(route('inquiries.index')); ?>" wire:navigate><?php echo e((int) ($portfolioClient->open_inquiries_count ?? 0)); ?> ↗</a></td>
                                <td data-label="Attention"><span class="ft-flag <?php echo e($portfolioAttentionTone); ?>"><?php echo e($portfolioAttentionJobs); ?></span></td>
                                <td data-label="On time"><?php echo e($portfolioOnTime); ?>%</td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="5">No active clients.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/dashboard/secondary.blade.php ENDPATH**/ ?>