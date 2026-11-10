<?php
    $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
    $masterData = app(\App\Services\MasterDataService::class);
    $taskFlagService = app(\App\Services\TaskFlagService::class);
    $inquiryService = app(\App\Services\InquiryService::class);

    $badgeTone = static function (?string $value): string {
        $value = mb_strtolower(trim((string) $value));
        if (str_contains($value, 'overdue') || str_contains($value, 'attention') || str_contains($value, 'blocked') || str_contains($value, 'risk')) return 'red';
        if (str_contains($value, 'due') || str_contains($value, 'waiting') || str_contains($value, 'hold') || str_contains($value, 'payment')) return 'amber';
        if (str_contains($value, 'complete') || str_contains($value, 'track') || str_contains($value, 'healthy') || str_contains($value, 'ready')) return 'green';
        if (str_contains($value, 'artwork') || str_contains($value, 'review') || str_contains($value, 'revision')) return 'purple';
        if (str_contains($value, 'unassigned') || str_contains($value, 'no flag')) return 'gray';
        return 'blue';
    };

    $taskFlag = static function ($task) use ($today, $taskFlagService, $badgeTone): array {
        $label = $taskFlagService->labelForTask($task);
        if ($label) return [$label, $badgeTone($label)];
        if ($task->due_date && $task->due_date->lt($today)) return ['Overdue '.$task->due_date->diffInDays($today).'d', 'red'];
        if ($task->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        if ((bool) $task->needs_attention) return ['Needs attention', 'red'];
        return ['On track', 'green'];
    };

    $jobFlag = static function ($job) use ($taskFlagService, $badgeTone): array {
        if ((bool) ($job->attention_requested ?? false) || (bool) ($job->needs_attention ?? false)) return ['Needs attention', 'red'];
        $label = $taskFlagService->labelForOrder($job);
        if ($label) return [$label, $badgeTone($label)];
        return ['No flag', 'gray'];
    };

    $inquiryFlag = static function ($inquiry) use ($today): array {
        $task = $inquiry->currentTask;
        $status = mb_strtolower((string) ($task?->status ?: $inquiry->status));
        if ($task?->needs_attention) return ['Needs attention', 'red'];
        if ($task?->due_date && $task->due_date->lt($today)) return ['Overdue '.$task->due_date->diffInDays($today).'d', 'red'];
        if ($task?->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        if (str_contains($status, 'block')) return ['Blocked', 'red'];
        if (str_contains($status, 'revision')) return ['Revision required', 'red'];
        if (str_contains($status, 'wait')) return ['Waiting', 'amber'];
        if (!$inquiry->owner_id) return ['Unassigned', 'gray'];
        return ['On track', 'green'];
    };

    $attentionOverdueLabel = static function ($dueDate) use ($today): string {
        $overdueDays = (float) $dueDate->diffInDays($today);

        return 'Overdue '.number_format($overdueDays, 2, '.', '').'d';
    };

    $attentionInquiryFlag = static function ($inquiry) use ($today, $inquiryFlag, $attentionOverdueLabel): array {
        $task = $inquiry->currentTask;
        if ($task?->due_date && $task->due_date->lt($today)) {
            return [$attentionOverdueLabel($task->due_date), 'red'];
        }

        return $inquiryFlag($inquiry);
    };

    $attentionJobFlag = static function ($job) use ($today, $jobFlag, $attentionOverdueLabel): array {
        $task = $job->tasks?->first();
        if ($task?->due_date && $task->due_date->lt($today)) {
            return [$attentionOverdueLabel($task->due_date), 'red'];
        }
        if ($task?->due_date && $task->due_date->isSameDay($today)) return ['Due today', 'amber'];
        $taskStatus = mb_strtolower(trim((string) ($task?->status ?? '')));
        if (str_contains($taskStatus, 'block')) return ['Blocked', 'red'];
        if ($task?->needs_attention) return ['Needs attention', 'red'];
        if ($job->delivery_date && $job->delivery_date->lt($today)) {
            return [$attentionOverdueLabel($job->delivery_date), 'red'];
        }
        if ($job->delivery_date && $job->delivery_date->isSameDay($today)) return ['Due today', 'amber'];
        if (!$job->owner_id) return ['Unassigned', 'gray'];
        return $jobFlag($job);
    };

    $attentionViewAllRoute = match ($attentionTab ?? 'all') {
        'inquiries' => route('inquiries.index', ['metric' => 'attention']),
        default => route('jobs.index', ['metric' => 'dashboardAttention']),
    };

    $orderTerminology = static function (?string $value): string {
        return preg_replace_callback('/\bjobs?\b/i', static function (array $match): string {
            return match ($match[0]) {
                'Jobs' => 'Orders', 'jobs' => 'orders', 'JOB' => 'ORDER', 'JOBS' => 'ORDERS',
                default => ctype_upper($match[0][0] ?? '') ? 'Order' : 'order',
            };
        }, (string) $value) ?: (string) $value;
    };

?>
    <section class="ft-mgmt-panel ft-mgmt-panel-spaced" data-dashboard-panel="priority">
        <div class="ft-mgmt-panel-head">
            <div><h2>Priority work</h2><p>Top urgent Orders, Inquiries and Tasks ranked by attention, due date and priority</p></div>
            <div class="ft-mgmt-tabs">
                <button type="button" wire:click="setPriorityTab('orders')" class="ft-mgmt-tab <?php echo e($priorityTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                <button type="button" wire:click="setPriorityTab('inquiries')" class="ft-mgmt-tab <?php echo e($priorityTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                <button type="button" wire:click="setPriorityTab('tasks')" class="ft-mgmt-tab <?php echo e($priorityTab === 'tasks' ? 'active' : ''); ?>">Tasks</button>
            </div>
        </div>
        <div class="ft-mgmt-table-wrap">
            <table class="ft-mgmt-table">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priorityTab === 'orders'): ?>
                    <thead><tr><th>Order</th><th>Client</th><th>Stage</th><th>Progress</th><th>Attention</th><th>Owner</th><th>Delivery</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $priorityJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                [$flagLabel, $flagTone] = $jobFlag($job);
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-priority-job-'.e($job->id).''; ?>wire:key="mgmt-priority-job-<?php echo e($job->id); ?>">
                                <td><a class="ft-mgmt-primary-text" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a><div class="ft-mgmt-sub"><?php echo e($job->title); ?></div></td>
                                <td><?php echo e($job->client?->name ?? '—'); ?></td><td><?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $job->phase,'short' => true,'fallback' => 'Unassigned','class' => 'ft-mgmt-badge']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->phase),'short' => true,'fallback' => 'Unassigned','class' => 'ft-mgmt-badge']); ?>
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
                                <td><div class="ft-mgmt-progress-cell"><div class="ft-mgmt-track"><span class="ft-mgmt-fill" style="width:<?php echo e(min(100, max(0, (int) $job->progress))); ?>%"></span></div><b><?php echo e((int) $job->progress); ?>%</b></div></td>
                                <td><span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->owner): ?><span class="ft-mgmt-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $job->owner,'name' => $job->owner->name,'size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner->name),'size' => 27]); ?>
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
<?php endif; ?><?php echo e($job->owner->name); ?></span><?php else: ?> Unassigned <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><?php echo e($job->delivery_date?->format('M j') ?? '—'); ?></td><td><a class="ft-mgmt-tiny-action" href="<?php echo e(route('jobs.index', ['open' => $job->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="8" class="ft-mgmt-empty">No matching orders found.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                <?php elseif($priorityTab === 'inquiries'): ?>
                    <thead><tr><th>Inquiry</th><th>Client</th><th>Current task</th><th>Status</th><th>Flag</th><th>Owner</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $priorityInquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                [$flagLabel, $flagTone] = $inquiryFlag($inquiry);
                                $statusColor = $inquiryService->inquiryStatusColor($inquiry->status ?: 'To do', (string) ($inquiry->currentTask?->status ?: ''));
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-priority-inquiry-'.e($inquiry->id).''; ?>wire:key="mgmt-priority-inquiry-<?php echo e($inquiry->id); ?>">
                                <td><a class="ft-mgmt-primary-text" href="<?php echo e(route('inquiries.index', ['open' => $inquiry->id])); ?>" wire:navigate><?php echo e($inquiry->inquiry_number); ?></a><div class="ft-mgmt-sub"><?php echo e($inquiry->subject); ?></div></td>
                                <td><?php echo e($inquiry->client?->name ?? '—'); ?></td><td><?php echo e($inquiry->currentTask?->title ?? 'No current task'); ?></td>
                                <td><span class="ft-mgmt-badge <?php echo e($badgeTone($inquiry->status)); ?>"><?php echo e($inquiry->status ?: 'To do'); ?></span></td><td><span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->owner): ?><span class="ft-mgmt-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $inquiry->owner,'name' => $inquiry->owner->name,'size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->owner),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->owner->name),'size' => 27]); ?>
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
<?php endif; ?><?php echo e($inquiry->owner->name); ?></span><?php else: ?> Unassigned <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><?php echo e($inquiry->currentTask?->due_date?->format('M j') ?? '—'); ?></td><td><a class="ft-mgmt-tiny-action" href="<?php echo e(route('inquiries.index', ['open' => $inquiry->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="8" class="ft-mgmt-empty">No matching inquiries found.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                <?php else: ?>
                    <thead><tr><th>Task</th><th>Order</th><th>Phase</th><th>Status</th><th>Attention</th><th>Assignee</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $priorityTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                            [$flagLabel, $flagTone] = $taskFlag($task);
                        ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-priority-task-'.e($task->id).''; ?>wire:key="mgmt-priority-task-<?php echo e($task->id); ?>">
                                <td><a class="ft-mgmt-primary-text" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a><div class="ft-mgmt-sub"><?php echo e($task->task_number); ?></div></td>
                                <td><?php echo e($task->job?->displayOrderNumber() ?? '—'); ?></td><td><?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $task->phase,'short' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->phase),'short' => true]); ?>
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
                                <td><span class="ft-mgmt-badge <?php echo e($badgeTone($task->status)); ?>"><?php echo e($task->status); ?></span></td><td><span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->assignee): ?><span class="ft-mgmt-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $task->assignee,'name' => $task->assignee->name,'size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee->name),'size' => 27]); ?>
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
<?php endif; ?><?php echo e($task->assignee->name); ?></span><?php else: ?> Unassigned <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><?php echo e($task->due_date?->format('M j') ?? '—'); ?></td><td><a class="ft-mgmt-tiny-action" href="<?php echo e(route('jobs.index', ['open' => $task->flow_job_id, 'task' => $task->id])); ?>" wire:navigate>View</a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="8" class="ft-mgmt-empty">No matching tasks found.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>
        <div class="ft-mgmt-priority-pagination" aria-label="Priority work pagination">
            <span class="ft-mgmt-priority-page-status">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($priorityPagination['total'] ?? 0) > 0): ?>
                    <?php echo e($priorityPagination['from']); ?>–<?php echo e($priorityPagination['to']); ?> of <?php echo e($priorityPagination['total']); ?>

                <?php else: ?>
                    0 items
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <button
                type="button"
                class="ft-mgmt-priority-page-btn"
                wire:click="previousPriorityPage"
                wire:loading.attr="disabled"
                wire:target="previousPriorityPage,nextPriorityPage"
                <?php if(!($priorityPagination['hasPrevious'] ?? false)): echo 'disabled'; endif; ?>
                aria-label="Previous priority work page"
                title="Previous page"
            >←</button>
            <button
                type="button"
                class="ft-mgmt-priority-page-btn"
                wire:click="nextPriorityPage"
                wire:loading.attr="disabled"
                wire:target="previousPriorityPage,nextPriorityPage"
                <?php if(!($priorityPagination['hasNext'] ?? false)): echo 'disabled'; endif; ?>
                aria-label="Next priority work page"
                title="Next page"
            >→</button>
        </div>
    </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/priority-panel.blade.php ENDPATH**/ ?>