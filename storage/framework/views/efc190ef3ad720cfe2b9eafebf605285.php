<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'task', 'mode' => 'locked', 'displayCode' => null, 'taskStatuses' => collect(), 'context' => [], 'overviewTaskLinkFormTaskId' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['job', 'task', 'mode' => 'locked', 'displayCode' => null, 'taskStatuses' => collect(), 'context' => [], 'overviewTaskLinkFormTaskId' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $permissions = data_get($context, 'taskPermissions.'.(int) $task->id, []);
    $canEditTask = (bool) data_get($permissions, 'edit', false);
    $canAssignTask = (bool) data_get($permissions, 'assign', false);
    $canDeleteTask = (bool) data_get($permissions, 'delete', false);
    $canUploadDocument = (bool) ($context['canUploadDocument'] ?? false);
    $canLinkDocument = (bool) ($context['canLinkDocument'] ?? false);
    $canDeleteDocument = (bool) ($context['canDeleteDocument'] ?? false);
    $canExportDocument = (bool) ($context['canExportDocument'] ?? false);
    $taskDocuments = $job->documents->where('task_id', $task->id)->sortByDesc('created_at')->values();
    $taskLinks = \App\Support\JobDetailPresenter::taskLinks($job, $task);
    $automationKey = app(\App\Services\OrderWorkflowActionService::class)->automationKey($task);
    $isArtworkUploadTask = $automationKey === 'ART_PREPARE_UPLOAD';
    $isProductionEstimatedDeliveryTask = $automationKey === 'PROD_SET_ESTIMATED_DELIVERY';
    $artworkRevisionNotes = $task->relationLoaded('artworkRevisionNotes') ? $task->artworkRevisionNotes : collect();
    $revisionReferenceDocumentIds = $artworkRevisionNotes
        ->map(function ($revisionNote) {
            $referenceDocument = $revisionNote->relationLoaded('referenceDocument')
                ? $revisionNote->getRelation('referenceDocument')
                : null;

            return (int) ($referenceDocument?->id ?? 0);
        })
        ->filter()
        ->unique()
        ->values();
    // Selective Artwork revision is file-specific: accepted files keep their
    // previous version while only replaced files increment. Use the hydrated
    // current set instead of assuming every current file shares max(version).
    $latestArtworkDocuments = $isArtworkUploadTask
        ? ($task->relationLoaded('currentArtworkDocuments')
            ? collect($task->getRelation('currentArtworkDocuments'))->sortBy('id')->values()
            : app(\App\Services\DocumentService::class)->currentArtworkDocuments($task, $taskDocuments))
        : collect();
    $latestArtworkDocument = $latestArtworkDocuments->last();
    $resourceDocuments = $isArtworkUploadTask
        ? $latestArtworkDocuments
        : $taskDocuments
            ->reject(fn ($document) => $revisionReferenceDocumentIds->contains((int) $document->id))
            ->values();
    $isCurrentPhase = (int) $task->workflow_phase_id === (int) $job->workflow_phase_id;
    $status = $mode === 'locked' && !\App\Support\OrderDetailPresenter::isCompletedTask($task)
        ? 'Locked'
        : trim((string) ($task->status ?: ($mode === 'active' ? 'Ready' : 'Completed')));
    $statusLower = strtolower($status);
    $statusClass = $mode === 'done' ? 'done' : ($mode === 'active' ? ((str_contains($statusLower, 'wait') || str_contains($statusLower, 'issue') || str_contains($statusLower, 'pending')) ? 'wait' : 'active') : '');
    $displayStatus = $isProductionEstimatedDeliveryTask && $mode === 'active' && ! $job->estimated_delivery_date
        ? 'Required'
        : $status;
    if ($isProductionEstimatedDeliveryTask && $mode === 'active' && ! $job->estimated_delivery_date) $statusClass = 'wait';
    $assigneeName = $task->assignee?->name ?: 'Unassigned';
    $assigneeInitials = collect(preg_split('/\s+/', trim($assigneeName)))->filter()->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('');
    $isCancelled = strcasecmp((string) $job->status, 'Cancelled') === 0;
    $documentCategoryName = (string) ($task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name ?: '');
    $requiresDocument = (bool) ($task->document_category_id || $task->setupTemplate?->document_category_id);
    $requiredBeforeCompletion = (bool) ($task->setupTemplate?->document_required_before_completion ?? false);
    $workflowAction = data_get($context, 'taskActions.'.(int) $task->id, []);
    $workflowActionLabel = (string) ($workflowAction['label'] ?? 'Take action');
    $workflowActionType = (string) ($workflowAction['type'] ?? 'workflow');
    $workflowEmailStatus = (array) data_get($context, 'workflowEmailStatuses.'.(int) $task->id, []);
    $workflowInvoice = (array) data_get($context, 'workflowInvoices.'.(int) $task->id, []);
    $workflowInvoiceId = (int) ($workflowInvoice['id'] ?? 0);
    $workflowInvoicePdfName = trim((string) ($workflowInvoice['pdf_name'] ?? ''));
    $emailResendFeedback = (array) data_get($context, 'workflowEmailResendFeedback.'.(int) $task->id, []);
    $emailResendFeedbackType = strtolower(trim((string) ($emailResendFeedback['type'] ?? '')));
    $emailResendFeedbackMessage = trim((string) ($emailResendFeedback['message'] ?? ''));
    $emailResendFeedbackStatus = strtolower(trim((string) ($emailResendFeedback['email_status'] ?? '')));
    $isArtworkEmailTask = $automationKey === 'ART_SEND_ORDER_TEAM';
    $isInvoiceEmailTask = $automationKey === 'BILL_SEND';
    $isTrackedEmailTask = $isArtworkEmailTask || $isInvoiceEmailTask;
    $emailDeliveryStatus = strtolower(trim((string) ($workflowEmailStatus['status'] ?? '')));
    if (in_array($emailResendFeedbackStatus, ['sent', 'failed', 'not_sent'], true)) $emailDeliveryStatus = $emailResendFeedbackStatus;
    // Completed legacy rows may predate delivery tracking. Show an explicit
    // Not Sent state instead of silently hiding email status.
    if ($isTrackedEmailTask && $mode === 'done' && $emailDeliveryStatus === '') $emailDeliveryStatus = 'not_sent';
    $emailDeliveryFailed = $isTrackedEmailTask && $emailDeliveryStatus === 'failed';
    $emailDeliverySent = $isTrackedEmailTask && $emailDeliveryStatus === 'sent';
    $emailDeliveryNotSent = $isTrackedEmailTask && $emailDeliveryStatus === 'not_sent';
    $emailCanResend = $isTrackedEmailTask
        && $mode === 'done'
        && $canEditTask
        && (bool) ($workflowEmailStatus['resendable'] ?? ! empty($workflowEmailStatus['to_emails'] ?? []));
    $emailResourceLabel = $isInvoiceEmailTask ? 'invoice' : 'artwork';
    $taskColor = \App\Support\MasterColor::normalize((string) ($task->setupTemplate?->color ?? $task->template?->color ?? ''))
        ?: \App\Support\MasterColor::normalize((string) ($task->phase?->color ?? ''))
        ?: '#2563EB';
?>
<article id="order-task-<?php echo e($task->id); ?>" class="task ft-order-task-row <?php echo e($mode); ?> <?php echo e($isCancelled ? 'cancelled-task' : ''); ?> <?php echo e($isProductionEstimatedDeliveryTask ? 'ft-order-task-row--estimated-delivery' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($taskColor)); ?>border-left:4px solid var(--ft-master-color,#2563EB)" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-row-'.e($task->id).''; ?>wire:key="order-task-row-<?php echo e($task->id); ?>">
    <div class="task-icon ft-order-task-icon"><?php echo e($mode === 'done' ? '✓' : ($mode === 'active' ? '●' : '⌁')); ?></div>
    <div class="task-copy ft-order-task-copy">
        <div class="task-code">TASK <?php echo e($displayCode ?: ($task->task_number ?: str_pad((string) $task->id, 3, '0', STR_PAD_LEFT))); ?></div>
        <div class="task-title">
            <?php echo e($task->title); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isProductionEstimatedDeliveryTask): ?>
                <span class="ft-order-required-task-badge">Required</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->description || $task->setupTemplate?->description): ?><div class="task-description"><?php echo e(\Illuminate\Support\Str::limit(strip_tags((string) ($task->description ?: $task->setupTemplate?->description)), 105)); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-order-task-assignee-inline ft-inline-edit-shell"
        x-data="window.FlowTrack.ui.inlineEdit({ key:<?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-assignee')->toHtml() ?>, label:'task assignee', value:<?php echo \Illuminate\Support\Js::from($task->assignee_id ?? '')->toHtml() ?>, display:<?php echo \Illuminate\Support\Js::from($assigneeName)->toHtml() ?>, avatarUrl:<?php echo \Illuminate\Support\Js::from($task->assignee?->profileImageUrl() ?? '')->toHtml() ?> })"
        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
        x-on:click.outside="if(editing) cancelEdit()"
        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
        x-on:task-assignee-updated.window="if (Number($event.detail?.taskId) === Number(<?php echo e($task->id); ?>)) syncConfirmed(String($event.detail?.assigneeId ?? ''), String($event.detail?.assigneeName ?? 'Unassigned'), { avatarUrl:String($event.detail?.avatarUrl ?? '') })"
        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeFromJob(<?php echo e($task->id); ?>, draftValue), { avatarUrl:String($event.detail?.avatarUrl ?? '') })">
        <div class="ft-order-inline-display-row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask && !$isCancelled): ?>
                <button
                    x-ref="assigneeAnchor"
                    :disabled="status === 'saving'"
                    type="button"
                    class="ft-order-assignee-display ft-order-inline-name-trigger"
                    :class="{ 'is-open': editing }"
                    title="Edit assignee"
                    aria-label="Edit task assignee"
                    x-on:click.stop="openRemotePicker($refs.assigneeAnchor)"
                >
                    <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 28]); ?>
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
                    <span class="ft-order-assignee-name" x-text="display"><?php echo e($assigneeName); ?></span>
                    <span class="ft-order-inline-trigger-icon" aria-hidden="true">✎</span>
                </button>
            <?php else: ?>
                <div class="ft-order-assignee-display">
                    <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 28]); ?>
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
                    <span class="ft-order-assignee-name" x-text="display"><?php echo e($assigneeName); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask && !$isCancelled): ?>
            <div x-cloak x-show="editing" class="ft-order-assignee-picker">
                <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task->assignee_id ?? '','selectedLabel' => $assigneeName,'parentType' => 'job','parentId' => $job->id,'variant' => 'compact','menuWidth' => 300,'externalTrigger' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assigneeName),'parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'variant' => 'compact','menu-width' => 300,'external-trigger' => true]); ?>
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

    <div class="date ft-order-task-due ft-inline-edit-shell"
        x-data="window.FlowTrack.ui.inlineEdit({ key:<?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-due-date')->toHtml() ?>, label:'task due date', value:<?php echo \Illuminate\Support\Js::from($task->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display:<?php echo \Illuminate\Support\Js::from($task->due_date?->format('M j, Y') ?? 'Set due date')->toHtml() ?> })"
        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
        <div class="ft-order-inline-display-row" x-show="!editing">
            <span class="ft-order-inline-value" x-text="display"><?php echo e($task->due_date?->format('M j, Y') ?? 'Set due date'); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask && !$isCancelled): ?>
                <button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button ft-order-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.orderDue.showPicker ? $refs.orderDue.showPicker() : $refs.orderDue.focus())">✎</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask && !$isCancelled): ?>
            <input x-ref="orderDue" x-cloak x-show="editing" x-model="draftValue" class="ft-order-inline-input" type="date"
                x-on:keydown.escape.prevent="cancelEdit()"
                x-on:blur="if (editing) cancelEdit()"
                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDateFromJob(<?php echo e($task->id); ?>, draftValue))">
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

    <div class="task-state ft-order-task-state">
        <span class="task-status ft-order-task-status <?php echo e($statusClass); ?>"><?php echo e($displayStatus); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTrackedEmailTask && $mode === 'done'): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emailDeliverySent): ?>
                <span class="ft-order-task-email-status is-sent" title="The latest <?php echo e($emailResourceLabel); ?> email was sent successfully.">Email Sent</span>
            <?php elseif($emailDeliveryFailed): ?>
                <span class="ft-order-task-email-status is-failed" title="The <?php echo e($emailResourceLabel); ?> email did not reach the selected recipients. The completed task can still resend it.">Email Failed</span>
            <?php elseif($emailDeliveryNotSent): ?>
                <span class="ft-order-task-email-status is-not-sent" title="The task was completed without a successful <?php echo e($emailResourceLabel); ?> email delivery.">Email Not Sent</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emailResendFeedbackMessage !== ''): ?>
                <div class="ft-order-task-email-feedback <?php echo e($emailResendFeedbackType === 'success' ? 'is-success' : 'is-error'); ?>" role="status" aria-live="polite"><?php echo e($emailResendFeedbackMessage); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowInvoiceId > 0): ?>
            <div class="card-sub ft-order-task-invoice-file">
                <span aria-hidden="true">📎</span>
                <a href="<?php echo e(route('invoices.pdf.open', $workflowInvoiceId)); ?>" target="_blank" rel="noopener"><?php echo e($workflowInvoicePdfName !== '' ? $workflowInvoicePdfName : (($workflowInvoice['invoice_number'] ?? 'Invoice').'.pdf')); ?></a>
                <a href="<?php echo e(route('invoices.pdf.download', $workflowInvoiceId)); ?>" class="ft-order-task-invoice-download">Download</a>
            </div>
        <?php elseif($taskDocuments->isNotEmpty()): ?>
            <?php $latestTaskDocument = $isArtworkUploadTask ? $latestArtworkDocument : $taskDocuments->first(); ?>
            <div class="card-sub">
                📎 <?php echo e($latestTaskDocument->name); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkUploadTask): ?>
                    · Latest
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestArtworkDocuments->count() > 1): ?>
                        · +<?php echo e($latestArtworkDocuments->count() - 1); ?> file<?php echo e($latestArtworkDocuments->count() === 2 ? '' : 's'); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php elseif($taskDocuments->count() > 1): ?>
                    +<?php echo e($taskDocuments->count() - 1); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php elseif($taskLinks->isNotEmpty()): ?>
            <div class="card-sub">↗ <?php echo e($taskLinks->count()); ?> external link<?php echo e($taskLinks->count() === 1 ? '' : 's'); ?></div>
        <?php elseif($requiresDocument): ?>
            <div class="card-sub">📎 <?php echo e($documentCategoryName ?: 'Document'); ?><?php echo e($requiredBeforeCompletion ? ' required' : ''); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="task-actions ft-order-task-actions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCancelled): ?>
            <button type="button" class="btn small" disabled>Blocked</button>
        <?php elseif($mode === 'active' && $isCurrentPhase): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($workflowActionType === 'document' || ($requiresDocument && $requiredBeforeCompletion && $taskDocuments->isEmpty() && $taskLinks->isEmpty())) && ($canUploadDocument || $canLinkDocument)): ?>
                    <button type="button" class="btn small primary" wire:click="openOrderWorkflowAction(<?php echo e($task->id); ?>)"><?php echo e($workflowActionLabel); ?></button>
                <?php else: ?>
                    <button type="button" class="btn small primary" wire:click="openOrderWorkflowAction(<?php echo e($task->id); ?>)"><?php echo e($workflowActionLabel); ?></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php elseif($mode === 'done'): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($automationKey === 'NEW_UPLOAD_PO' && $canEditTask && ($canUploadDocument || $canLinkDocument)): ?>
                <button type="button" class="btn small" wire:click="openOverviewTaskDocumentModal(<?php echo e($task->id); ?>)">Add other documents</button>
            <?php elseif($isTrackedEmailTask && $canEditTask): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emailCanResend): ?>
                    <?php $resendMethod = $isInvoiceEmailTask ? 'resendCompletedInvoiceEmail' : 'resendCompletedArtworkEmail'; ?>
                    <button type="button" class="btn small primary ft-order-task-resend-email" wire:click="<?php echo e($resendMethod); ?>(<?php echo e($task->id); ?>)" wire:loading.attr="disabled" wire:target="<?php echo e($resendMethod); ?>(<?php echo e($task->id); ?>)">
                        <span wire:loading.remove wire:target="<?php echo e($resendMethod); ?>(<?php echo e($task->id); ?>)">Resend</span>
                        <span wire:loading wire:target="<?php echo e($resendMethod); ?>(<?php echo e($task->id); ?>)">Sending...</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" class="btn small" wire:click="viewTask(<?php echo e($task->id); ?>)">View</button>
            <?php else: ?>
                <button type="button" class="btn small" wire:click="viewTask(<?php echo e($task->id); ?>)">View</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</article>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $overviewTaskLinkFormTaskId === (int) $task->id && $canEditTask): ?>
    <form class="task-link-form ft-order-task-link-form" wire:submit.prevent="saveOverviewTaskLink(<?php echo e($task->id); ?>)" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-link-form-'.e($task->id).''; ?>wire:key="order-task-link-form-<?php echo e($task->id); ?>">
        <input type="url" wire:model="overviewTaskLinkUrl" placeholder="Paste external document link..." autocomplete="url">
        <button type="button" class="btn small" wire:click="cancelOverviewTaskLinkForm">Cancel</button><button type="submit" class="btn primary small">Add link</button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskLinkUrl'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="validation-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $artworkRevisionNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revisionNote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal7b9eb72385192b9641f2f32440e72aed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b9eb72385192b9641f2f32440e72aed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.artwork-revision-card','data' => ['revisionNote' => $revisionNote,'canExportDocument' => $canExportDocument]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.artwork-revision-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['revision-note' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($revisionNote),'can-export-document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExportDocument)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b9eb72385192b9641f2f32440e72aed)): ?>
<?php $attributes = $__attributesOriginal7b9eb72385192b9641f2f32440e72aed; ?>
<?php unset($__attributesOriginal7b9eb72385192b9641f2f32440e72aed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b9eb72385192b9641f2f32440e72aed)): ?>
<?php $component = $__componentOriginal7b9eb72385192b9641f2f32440e72aed; ?>
<?php unset($__componentOriginal7b9eb72385192b9641f2f32440e72aed); ?>
<?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resourceDocuments->isNotEmpty() || $taskLinks->isNotEmpty()): ?>
    <div class="task-resources ft-order-task-resources">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resourceDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-document-'.e($document->id).''; ?>wire:key="order-task-document-<?php echo e($document->id); ?>"
                class="ft-order-task-resource-row <?php echo e($isArtworkUploadTask ? 'is-latest-artwork' : ''); ?>"
            >
                <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['name' => $document->name,'class' => 'ft-order-file-icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($document->name),'class' => 'ft-order-file-icon']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $attributes = $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $component = $__componentOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
                <span>
                    <b><?php echo e($document->name); ?></b>
                    <small>
                        <?php echo e($document->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($document->created_at, 'M j, Y, g:i A')); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkUploadTask): ?> · Latest revision <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </small>
                </span>
                <span class="ft-order-task-resource-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isArtworkUploadTask): ?>
                        <em class="ft-order-artwork-version-state is-latest">Latest</em>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="<?php echo e(route('documents.open', $document)); ?>" target="_blank" rel="noopener">Open</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocument): ?><a href="<?php echo e(route('documents.download', $document)); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?><button type="button" wire:click="deleteJobDocument(<?php echo e($document->id); ?>)" wire:confirm="Delete this document link?">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-link-'.e($link->id).''; ?>wire:key="order-task-link-<?php echo e($link->id); ?>"><span class="file-icon link ft-order-file-icon">↗</span><span><b><?php echo e(\Illuminate\Support\Str::limit($link->url, 90)); ?></b><small><?php echo e($link->created_at ? \App\Support\UserLocalTime::format($link->created_at, 'M j, Y, g:i A') : '—'); ?></small></span><span><a href="<?php echo e($link->url); ?>" target="_blank" rel="noopener noreferrer">Open ↗</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" wire:click="deleteOverviewTaskLink(<?php echo e($task->id); ?>, <?php echo e($link->id); ?>)" wire:confirm="Remove this link?">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/task-row.blade.php ENDPATH**/ ?>