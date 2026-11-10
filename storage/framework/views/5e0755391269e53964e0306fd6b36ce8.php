<div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-workflow-workspace-'.e($orderId).''; ?>wire:key="order-workflow-workspace-<?php echo e($orderId); ?>" data-flowtrack-order-section="workflow" data-flowtrack-section-ready="<?php echo e($ready ? '1' : '0'); ?>">
    <?php if (isset($component)) { $__componentOriginalb332b6fecacc481ca3fbe2a522c86dbf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb332b6fecacc481ca3fbe2a522c86dbf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.artwork-address-book-notice','data' => ['status' => $artworkAddressBookNotice]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.artwork-address-book-notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($artworkAddressBookNotice)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb332b6fecacc481ca3fbe2a522c86dbf)): ?>
<?php $attributes = $__attributesOriginalb332b6fecacc481ca3fbe2a522c86dbf; ?>
<?php unset($__attributesOriginalb332b6fecacc481ca3fbe2a522c86dbf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb332b6fecacc481ca3fbe2a522c86dbf)): ?>
<?php $component = $__componentOriginalb332b6fecacc481ca3fbe2a522c86dbf; ?>
<?php unset($__componentOriginalb332b6fecacc481ca3fbe2a522c86dbf); ?>
<?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'workflow','method' => 'loadWorkflowSection','keyPrefix' => 'order-workflow-isolated','contextType' => 'order','contextId' => $orderId,'queueGroup' => 'order-detail-'.e($orderId).'','queuePriority' => 20,'settleDelay' => 180,'rows' => 5,'message' => 'Loading workflow and tasks when needed…','rootMargin' => '120px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'workflow','method' => 'loadWorkflowSection','key-prefix' => 'order-workflow-isolated','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderId),'queue-group' => 'order-detail-'.e($orderId).'','queue-priority' => 20,'settle-delay' => 180,'rows' => 5,'message' => 'Loading workflow and tasks when needed…','root-margin' => '120px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) config('flowtrack.workflow_display_query_enabled', false)
            && (bool) config('flowtrack.order_three_level_loading_enabled', false)): ?>
            <span hidden aria-hidden="true"
                data-workflow-stage-prefetch
                data-order-id="<?php echo e($orderId); ?>"
                data-prefetch-url="<?php echo e(route('orders.workflow.stage-prefetch', ['orderId' => $orderId])); ?>"
                data-active-phase="<?php echo e((int) ($overviewPhaseId ?: $job->workflow_phase_id)); ?>"></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalacd6c8d39c322d451ed4aa64b3000636 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalacd6c8d39c322d451ed4aa64b3000636 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.workflow','data' => ['job' => $job,'overviewPhaseId' => $overviewPhaseId,'taskStatuses' => $taskStatuses,'context' => $context,'overviewTaskLinkFormTaskId' => $overviewTaskLinkFormTaskId,'showShipmentModal' => $showShipmentModal,'showShipmentSavedAddressPicker' => $showShipmentSavedAddressPicker,'shipmentModalTaskId' => $shipmentModalTaskId,'shipmentEditingId' => $shipmentEditingId,'shipmentModalMode' => $shipmentModalMode,'shipmentForm' => $shipmentForm,'shipmentInlineTaskId' => $shipmentInlineTaskId,'shipmentInlineEditingId' => $shipmentInlineEditingId,'shipmentInlineAddressMode' => $shipmentInlineAddressMode,'shipmentInlineForm' => $shipmentInlineForm,'showShipmentDetailsModal' => $showShipmentDetailsModal,'shipmentDetailsId' => $shipmentDetailsId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.workflow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'overview-phase-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewPhaseId),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'overview-task-link-form-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskLinkFormTaskId),'show-shipment-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentModal),'show-shipment-saved-address-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentSavedAddressPicker),'shipment-modal-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalTaskId),'shipment-editing-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentEditingId),'shipment-modal-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalMode),'shipment-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentForm),'shipment-inline-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineTaskId),'shipment-inline-editing-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineEditingId),'shipment-inline-address-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineAddressMode),'shipment-inline-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineForm),'show-shipment-details-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentDetailsModal),'shipment-details-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentDetailsId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalacd6c8d39c322d451ed4aa64b3000636)): ?>
<?php $attributes = $__attributesOriginalacd6c8d39c322d451ed4aa64b3000636; ?>
<?php unset($__attributesOriginalacd6c8d39c322d451ed4aa64b3000636); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalacd6c8d39c322d451ed4aa64b3000636)): ?>
<?php $component = $__componentOriginalacd6c8d39c322d451ed4aa64b3000636; ?>
<?php unset($__componentOriginalacd6c8d39c322d451ed4aa64b3000636); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOrderWorkflowActionModal && $orderWorkflowActionTaskId): ?>
            <?php
                $workflowActionTask = $job->tasks->firstWhere('id', (int) $orderWorkflowActionTaskId);
                $workflowActionModal = data_get($context, 'taskActionModals.'.(int) $orderWorkflowActionTaskId, []);
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowActionTask): ?>
                <?php if (isset($component)) { $__componentOriginal8e43f3521a8e6328e588de4039a01fc1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e43f3521a8e6328e588de4039a01fc1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.workflow-action-modal','data' => ['job' => $job,'task' => $workflowActionTask,'config' => $workflowActionModal,'step' => $orderWorkflowActionStep,'payload' => $orderWorkflowActionPayload,'shipmentMethods' => $context['shipmentMethods'] ?? collect(),'shipmentUrgencies' => $context['shipmentUrgencies'] ?? collect(),'artworkRecipientEmails' => $artworkRecipientEmails,'artworkSavedRecipientSuggestions' => $artworkSavedRecipientSuggestions,'modalPreview' => $orderWorkflowActionModalPreview,'attachment' => $orderWorkflowActionAttachment,'revisionComments' => $orderWorkflowActionRevisionComments,'revisionAttachments' => $orderWorkflowActionRevisionAttachments,'mentionUsers' => $mentionUsers,'emailFallback' => $orderWorkflowEmailFallback,'emailFallbackMessage' => $orderWorkflowEmailFallbackMessage,'emailFallbackAttempts' => $orderWorkflowEmailFallbackAttempts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.workflow-action-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowActionTask),'config' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowActionModal),'step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionStep),'payload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionPayload),'shipment-methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context['shipmentMethods'] ?? collect()),'shipment-urgencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context['shipmentUrgencies'] ?? collect()),'artwork-recipient-emails' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($artworkRecipientEmails),'artwork-saved-recipient-suggestions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($artworkSavedRecipientSuggestions),'modal-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionModalPreview),'attachment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionAttachment),'revision-comments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionRevisionComments),'revision-attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionRevisionAttachments),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'email-fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallback),'email-fallback-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackMessage),'email-fallback-attempts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackAttempts)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8e43f3521a8e6328e588de4039a01fc1)): ?>
<?php $attributes = $__attributesOriginal8e43f3521a8e6328e588de4039a01fc1; ?>
<?php unset($__attributesOriginal8e43f3521a8e6328e588de4039a01fc1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e43f3521a8e6328e588de4039a01fc1)): ?>
<?php $component = $__componentOriginal8e43f3521a8e6328e588de4039a01fc1; ?>
<?php unset($__componentOriginal8e43f3521a8e6328e588de4039a01fc1); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOverviewTaskDocumentModal && $overviewTaskDocumentModalTask): ?>
            <?php if (isset($component)) { $__componentOriginal75144a7262080e4edb332b7df7a76a92 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal75144a7262080e4edb332b7df7a76a92 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.document-modal','data' => ['job' => $job,'task' => $overviewTaskDocumentModalTask,'availableDocuments' => $overviewTaskAvailableDocuments,'source' => $overviewTaskDocumentSource,'upload' => $overviewTaskDocumentUpload,'revisionUpload' => $overviewTaskRevisionUpload,'stagedUploads' => $overviewTaskStagedUploads,'stagedRevisionUploads' => $overviewTaskStagedRevisionUploads,'existingDocumentId' => $overviewTaskExistingDocumentId,'artworkRevision' => $overviewTaskArtworkRevision,'revisionDocumentIds' => $overviewTaskRevisionDocumentIds,'context' => $context]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.document-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentModalTask),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskAvailableDocuments),'source' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentSource),'upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentUpload),'revision-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskRevisionUpload),'staged-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskStagedUploads),'staged-revision-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskStagedRevisionUploads),'existing-document-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskExistingDocumentId),'artwork-revision' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskArtworkRevision),'revision-document-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskRevisionDocumentIds),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal75144a7262080e4edb332b7df7a76a92)): ?>
<?php $attributes = $__attributesOriginal75144a7262080e4edb332b7df7a76a92; ?>
<?php unset($__attributesOriginal75144a7262080e4edb332b7df7a76a92); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal75144a7262080e4edb332b7df7a76a92)): ?>
<?php $component = $__componentOriginal75144a7262080e4edb332b7df7a76a92; ?>
<?php unset($__componentOriginal75144a7262080e4edb332b7df7a76a92); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


    <?php
        $__scriptKey = '2389369084-0';
        ob_start();
    ?>
<script>
(() => {
    const root = $wire.$el;
    const marker = root?.querySelector('[data-workflow-stage-prefetch]');
    if (!marker || !window.fetch) return;

    const orderId = Number(marker.dataset.orderId);
    const activePhase = Number(marker.dataset.activePhase);
    const endpoint = new URL(marker.dataset.prefetchUrl, window.location.origin);
    endpoint.searchParams.set('active_phase_id', String(activePhase));
    const controller = new AbortController();
    let started = false;
    const start = () => {
        if (started || !root.isConnected || document.visibilityState === 'hidden') return;
        started = true;
        fetch(endpoint.toString(), {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'Accept': 'application/json' },
            signal: controller.signal,
        }).then(response => response.ok ? response.json() : null)
          .then(data => {
              if (!data || !root.isConnected || Number(data.order_id) !== orderId) return;
              // A passive, bounded read model. Interactive task rows always
              // re-read authoritative records on phase selection/action.
              root.__flowtrackPrefetchedStages = data.phases;
              root.dispatchEvent(new CustomEvent('flowtrack-stages-prefetched', {
                  detail: { orderId, phaseCount: data.phases.length },
              }));
          }).catch(() => { /* Prefetch failure cannot block current work. */ });
    };

    if (typeof window.requestIdleCallback === 'function') {
        window.requestIdleCallback(start, { timeout: 1700 });
    } else {
        window.setTimeout(start, 450);
    }
    document.addEventListener('visibilitychange', start, { once: true });
    // A navigation destroys the old workspace: never retain task data from
    // another order or render a response into a different Livewire instance.
    root.addEventListener('livewire:navigating', () => controller.abort(), { once: true });
})();
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/jobs/order-workflow-section.blade.php ENDPATH**/ ?>