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
        <article class="ft-mgmt-panel ft-mgmt-attention-prototype ft-mgmt-attention-compact ft-mgmt-dashboard-half-panel" data-dashboard-panel="attention">
            <div class="ft-mgmt-panel-head">
                <div><h2>Needs attention</h2><p>Orders and Inquiries ranked by urgency and impact</p></div>
                <a class="ft-mgmt-link" href="<?php echo e($attentionViewAllRoute); ?>" wire:navigate>View all</a>
            </div>
            <div class="ft-mgmt-attention-tabs" role="tablist" aria-label="Needs attention type">
                <button type="button" wire:click="setAttentionTab('all')" class="<?php echo e($attentionTab === 'all' ? 'active' : ''); ?>">All <?php echo e($attentionTotalCount); ?></button>
                <button type="button" wire:click="setAttentionTab('orders')" class="<?php echo e($attentionTab === 'orders' ? 'active' : ''); ?>">Orders <?php echo e($attentionOrderCount); ?></button>
                <button type="button" wire:click="setAttentionTab('inquiries')" class="<?php echo e($attentionTab === 'inquiries' ? 'active' : ''); ?>">Inquiries <?php echo e($attentionInquiryCount); ?></button>
            </div>
            <div class="ft-mgmt-attention-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attentionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attentionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $kind = $attentionItem['kind'];
                        $record = $attentionItem['record'];
                        $isOrder = $kind === 'orders';
                        [$flagLabel, $flagTone] = $isOrder ? $attentionJobFlag($record) : $attentionInquiryFlag($record);
                        $reference = $isOrder ? $record->displayOrderNumber() : $record->inquiry_number;
                        $headline = trim((string) ($isOrder ? ($record->tasks?->first()?->title ?: $record->title) : ($record->currentTask?->title ?: $record->subject)));
                        $ownerName = $isOrder ? ($record->owner?->name ?? 'Unassigned') : ($record->owner?->name ?? 'Unassigned');
                        $reason = trim((string) ($isOrder
                            ? ($record->attention_reason ?: $record->tasks?->first()?->attention_reason ?: $record->flaggedTasks?->first()?->attention_reason ?: 'Attention required')
                            : ($record->currentTask?->attention_reason ?: ($record->needs_attention ? 'Attention required' : $record->currentTask?->status))));
                        $rowRoute = $isOrder
                            ? route('jobs.index', ['open' => $record->id])
                            : route('inquiries.index', ['open' => $record->id]);
                    ?>
                    <a class="ft-mgmt-attention" href="<?php echo e($rowRoute); ?>" wire:navigate <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-attention-'.e($kind).'-'.e($record->id).''; ?>wire:key="mgmt-attention-<?php echo e($kind); ?>-<?php echo e($record->id); ?>">
                        <span class="ft-mgmt-severity <?php echo e($flagTone); ?>"></span>
                        <span class="ft-mgmt-attention-type-icon <?php echo e($isOrder ? 'order' : 'inquiry'); ?>" aria-hidden="true">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOrder): ?>
                                <svg viewBox="0 0 24 24"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 8H7"/><circle cx="10" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24"><path d="M20 15a4 4 0 0 1-4 4H9l-5 3 1.4-4.2A7 7 0 1 1 20 15Z"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-mgmt-attention-kind <?php echo e($isOrder ? 'order' : 'inquiry'); ?>"><?php echo e($isOrder ? 'Order' : 'Inquiry'); ?></span>
                        <span class="ft-mgmt-attention-copy">
                            <strong><?php echo e($reference); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($headline !== ''): ?> · <?php echo e($headline); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong>
                            <small><?php echo e($reason !== '' ? $reason : 'Requires attention'); ?> · Owner <?php echo e($ownerName); ?></small>
                        </span>
                        <span class="ft-mgmt-badge <?php echo e($flagTone); ?>"><?php echo e($flagLabel); ?></span>
                        <span class="ft-mgmt-attention-arrow" aria-hidden="true">›</span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-mgmt-empty">No attention items match the current filters.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/attention-panel.blade.php ENDPATH**/ ?>