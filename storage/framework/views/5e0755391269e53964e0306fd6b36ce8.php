<div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-workflow-workspace-'.e($orderId).''; ?>wire:key="order-workflow-workspace-<?php echo e($orderId); ?>">
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
        <?php if (isset($component)) { $__componentOriginalacd6c8d39c322d451ed4aa64b3000636 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalacd6c8d39c322d451ed4aa64b3000636 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.workflow','data' => ['job' => $job,'overviewPhaseId' => $overviewPhaseId,'taskStatuses' => $taskStatuses,'context' => $context,'overviewTaskLinkFormTaskId' => $overviewTaskLinkFormTaskId,'showShipmentModal' => $showShipmentModal,'shipmentModalTaskId' => $shipmentModalTaskId,'shipmentEditingId' => $shipmentEditingId,'shipmentModalMode' => $shipmentModalMode,'shipmentForm' => $shipmentForm,'shipmentInlineTaskId' => $shipmentInlineTaskId,'shipmentInlineEditingId' => $shipmentInlineEditingId,'shipmentInlineAddressMode' => $shipmentInlineAddressMode,'shipmentInlineForm' => $shipmentInlineForm,'showShipmentDetailsModal' => $showShipmentDetailsModal,'shipmentDetailsId' => $shipmentDetailsId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.workflow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'overview-phase-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewPhaseId),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'overview-task-link-form-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskLinkFormTaskId),'show-shipment-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentModal),'shipment-modal-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalTaskId),'shipment-editing-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentEditingId),'shipment-modal-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalMode),'shipment-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentForm),'shipment-inline-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineTaskId),'shipment-inline-editing-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineEditingId),'shipment-inline-address-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineAddressMode),'shipment-inline-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentInlineForm),'show-shipment-details-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentDetailsModal),'shipment-details-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentDetailsId)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.workflow-action-modal','data' => ['job' => $job,'task' => $workflowActionTask,'config' => $workflowActionModal,'step' => $orderWorkflowActionStep,'payload' => $orderWorkflowActionPayload,'modalPreview' => $orderWorkflowActionModalPreview,'attachment' => $orderWorkflowActionAttachment,'revisionComments' => $orderWorkflowActionRevisionComments,'revisionAttachments' => $orderWorkflowActionRevisionAttachments,'mentionUsers' => $mentionUsers,'emailFallback' => $orderWorkflowEmailFallback,'emailFallbackMessage' => $orderWorkflowEmailFallbackMessage,'emailFallbackAttempts' => $orderWorkflowEmailFallbackAttempts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.workflow-action-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowActionTask),'config' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowActionModal),'step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionStep),'payload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionPayload),'modal-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionModalPreview),'attachment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionAttachment),'revision-comments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionRevisionComments),'revision-attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionRevisionAttachments),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'email-fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallback),'email-fallback-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackMessage),'email-fallback-attempts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackAttempts)]); ?>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/jobs/order-workflow-section.blade.php ENDPATH**/ ?>