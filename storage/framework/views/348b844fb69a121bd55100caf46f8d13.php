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
    $taskFlag = static function ($task) use ($today): array {
        if ($task->due_date && $task->due_date->lt($today)) return ['Overdue', 'red'];
        if ($task->status === 'Waiting for Client') return ['Client wait', 'amber'];
        if ($task->status === 'Revision Required') return ['Revision', 'amber'];
        if ($task->status === 'Blocked') return ['Blocked', 'red'];
        if (!$task->assignee_id) return ['Unassigned', 'blue'];
        if ($task->needs_attention) return ['Attention', 'amber'];
        return ['On track', 'green'];
    };
    $jobFlag = static function ($job): array {
        if (in_array($job->health, ['Delayed', 'Blocked'], true)) return [$job->health, 'red'];
        if ($job->needs_attention || in_array($job->health, ['At Risk', 'Needs Attention'], true)) return ['At risk', 'amber'];
        return ['On track', 'green'];
    };
?>

<div class="ft-dashboard-secondary-sections">
    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Assignee performance</h2><div class="ft-panel-note">Ongoing workload before Done, completion and overdue exposure</div></div><a class="ft-link" href="<?php echo e(route('reports')); ?>" wire:navigate>Details</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:29%"><col style="width:16%"><col style="width:18%"><col style="width:19%"><col style="width:18%"></colgroup>
                    <thead><tr><th>Assignee</th><th>Ongoing</th><th>Done</th><th>On time</th><th>Workload</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assigneePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $onTime = $person->done_count > 0 ? (int) round(($person->done_on_time_count / $person->done_count) * 100) : 100;
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
                                <td data-label="Ongoing"><a class="ft-text-link" href="<?php echo e(route('board')); ?>" wire:navigate><?php echo e($person->ongoing_count); ?> ↗</a></td>
                                <td data-label="Done"><?php echo e($person->done_count); ?></td>
                                <td data-label="On time"><?php echo e($onTime); ?>%</td>
                                <td data-label="Workload"><span class="ft-load"><i class="ft-load-track"><span style="width:<?php echo e($workloadPct); ?>%"></span></i><?php echo e($workloadLabel); ?></span></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="5">No active assignee workload.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Needs attention</h2><div class="ft-panel-note">Highest-priority tasks across current jobs</div></div><a class="ft-link" href="<?php echo e(route('board')); ?>" wire:navigate>View all tasks</a></div>
            <div class="ft-risk-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attentionTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        [$flagLabel, $flagTone] = $taskFlag($task);
                    ?>
                    <div class="ft-risk" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-risk-'.e($task->id).''; ?>wire:key="dashboard-risk-<?php echo e($task->id); ?>">
                        <a class="ft-risk-name ft-text-link" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a>
                        <span class="ft-flag <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span>
                        <span class="ft-risk-meta"><?php echo e($task->task_number); ?> · <?php echo e($task->job?->displayOrderNumber() ?? 'Order'); ?> · <?php echo e($task->assignee?->name ?? 'Unassigned'); ?> · <?php echo e($task->due_date ? 'Due '.$task->due_date->format('M j') : 'No due date'); ?></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-panel-empty">No tasks currently need attention.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>

    <div class="ft-grid ft-grid-balanced">
        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Ongoing jobs</h2><div class="ft-panel-note">Current stage, health and exception flags</div></div><a class="ft-link" href="<?php echo e(route('jobs.index')); ?>" wire:navigate>View jobs</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:31%"><col style="width:18%"><col style="width:23%"><col style="width:18%"><col style="width:10%"></colgroup>
                    <thead><tr><th>Job</th><th>Client</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ongoingJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                [$flagLabel, $flagTone] = $jobFlag($job);
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-job-'.e($job->id).''; ?>wire:key="dashboard-job-<?php echo e($job->id); ?>">
                                <td data-label="Job"><a class="ft-text-link ft-cell-clip" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->title); ?></a><span class="ft-ref"><?php echo e($job->displayOrderNumber()); ?></span></td>
                                <td data-label="Client"><span class="ft-cell-clip"><?php echo e($job->client?->name ?? '—'); ?></span></td>
                                <td data-label="Status"><span class="ft-pill <?php echo e($statusTone($job->phase?->short_name)); ?>"><?php echo e($job->phase?->short_name ?? 'Unassigned'); ?></span></td>
                                <td data-label="Flag"><span class="ft-flag <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td data-label="View"><a class="ft-view" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-table-empty-row"><td colspan="5">No ongoing jobs.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Ongoing tasks</h2><div class="ft-panel-note">Tasks before Done with current work status and flags</div></div><a class="ft-link" href="<?php echo e(route('board')); ?>" wire:navigate>Open board</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:29%"><col style="width:13%"><col style="width:17%"><col style="width:20%"><col style="width:13%"><col style="width:8%"></colgroup>
                    <thead><tr><th>Task</th><th>Job</th><th>Assignee</th><th>Status</th><th>Flag</th><th>View</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ongoingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                        [$flagLabel, $flagTone] = $taskFlag($task);
                    ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-task-'.e($task->id).''; ?>wire:key="dashboard-task-<?php echo e($task->id); ?>">
                                <td data-label="Task"><a class="ft-text-link ft-cell-clip" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a><span class="ft-ref"><?php echo e($task->task_number); ?></span></td>
                                <td data-label="Job"><a class="ft-text-link" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id])); ?>" wire:navigate><?php echo e(str($task->job?->displayOrderNumber() ?? '—')->afterLast('-')); ?></a></td>
                                <td data-label="Assignee"><span class="ft-cell-clip"><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></span></td>
                                <td data-label="Status"><span class="ft-pill <?php echo e($statusTone($task->status)); ?>"><?php echo e($task->status); ?></span></td>
                                <td data-label="Flag"><span class="ft-flag <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
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
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Recent activity</h2><div class="ft-panel-note">Latest job, task, inquiry, document and comment events</div></div><a class="ft-link" href="<?php echo e(route('notifications')); ?>" wire:navigate>All activity</a></div>
            <div class="ft-activity-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-activity" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-activity-'.e($notification->id).''; ?>wire:key="dashboard-activity-<?php echo e($notification->id); ?>">
                        <span class="ft-activity-icon"><?php echo e($notification->type === 'mention' ? '@' : '✓'); ?></span>
                        <span><strong><?php echo e($notification->title); ?></strong><span class="ft-activity-copy"><?php echo e($notification->message); ?></span></span>
                        <time class="ft-activity-time"><?php echo e($notification->created_at?->diffForHumans(short: true)); ?></time>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-panel-empty">No recent activity.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <section class="ft-panel">
            <div class="ft-panel-head"><div><h2 class="ft-panel-title">Client portfolio</h2><div class="ft-panel-note">Active work, inquiry volume and delivery health</div></div><a class="ft-link" href="<?php echo e(route('clients.index')); ?>" wire:navigate>All clients</a></div>
            <div class="ft-table-wrap">
                <table class="ft-table responsive">
                    <colgroup><col style="width:28%"><col style="width:15%"><col style="width:18%"><col style="width:19%"><col style="width:20%"></colgroup>
                    <thead><tr><th>Client</th><th>Jobs</th><th>Inquiries</th><th>At risk</th><th>On time</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $clientPortfolio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $onTime = $client->open_tasks_count > 0
                                    ? max(0, (int) round((($client->open_tasks_count - $client->overdue_tasks_count) / $client->open_tasks_count) * 100))
                                    : 100;
                                $riskTone = $client->at_risk_jobs_count > 1 ? 'red' : ($client->at_risk_jobs_count === 1 ? 'amber' : 'green');
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-client-'.e($client->id).''; ?>wire:key="dashboard-client-<?php echo e($client->id); ?>">
                                <td data-label="Client"><a class="ft-text-link ft-cell-clip" href="<?php echo e(route('clients.index')); ?>" wire:navigate><?php echo e($client->name); ?></a></td>
                                <td data-label="Jobs"><a class="ft-text-link" href="<?php echo e(route('jobs.index', ['client' => $client->id])); ?>" wire:navigate><?php echo e($client->active_jobs_count); ?> ↗</a></td>
                                <td data-label="Inquiries">0</td>
                                <td data-label="At risk"><span class="ft-flag <?php echo e($riskTone); ?>"><?php echo e($client->at_risk_jobs_count); ?></span></td>
                                <td data-label="On time"><?php echo e($onTime); ?>%</td>
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