<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'shipmentUrgencyOptions'=>collect(),
    'overviewPhaseId'=>null,
    'orderDetailContext'=>[],
    'orderDetailSectionsReady'=>[],
    'orderRedoContext'=>[],
    'orderRedoForm'=>[],
    'products'=>collect(),
    'categories'=>collect(),
    'showAddJobProductForm'=>false,
    'jobProductSearch'=>'',
    'jobProductSearchResults'=>collect(),
    'jobProductSearchSuppliers'=>collect(),
    'jobProductResultTotal'=>0,
    'jobProductShowAllResults'=>false,
    'jobProductSelectedProduct'=>null,
    'jobProductSelectedSupplier'=>null,
    'jobProductCategory'=>'',
    'jobProductQuantity'=>'1000',
    'jobProductUnitPrice'=>'0.00',
    'jobProductSupplierId'=>null,
    'jobProductSupplierLabel'=>'',
    'jobProductSupplierSkipped'=>false,
    'jobProductSupplierLocked'=>false,
    'showEditOrderProductModal'=>false, 'editOrderProductItemId'=>null, 'editOrderProductName'=>'', 'editOrderProductCode'=>'',
    'editOrderProductCategory'=>'', 'editOrderProductSearch'=>'', 'editOrderProductSearchResults'=>collect(),
    'editOrderProductSearchSuppliers'=>collect(), 'editOrderProductResultTotal'=>0, 'editOrderProductSelectedProduct'=>null,
    'editOrderProductSelectedSupplier'=>null, 'editOrderProductShowAllResults'=>false,
    'editOrderProductSupplierId'=>null, 'editOrderProductSupplierLabel'=>'', 'editOrderProductQuantity'=>'1',
    'editOrderProductUnitPrice'=>'0.00', 'editOrderProductNotes'=>'',
    'availableDocuments'=>collect(),
    'overviewTaskDocumentModalTask'=>null,
    'overviewTaskAvailableDocuments'=>collect(),
    'showOverviewTaskDocumentModal'=>false,
    'showAddOrderTaskForm'=>false,
    'newOrderTaskAssigneeId'=>null,
    'overviewTaskDocumentSource'=>'upload',
    'overviewTaskDocumentUpload'=>null,
    'overviewTaskRevisionUpload'=>[],
    'overviewTaskStagedUploads'=>[],
    'overviewTaskStagedRevisionUploads'=>[],
    'overviewTaskExistingDocumentId'=>null,
    'overviewTaskArtworkRevision'=>[],
    'overviewTaskRevisionDocumentIds'=>[],
    'overviewTaskLinkFormTaskId'=>null,
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'showOrderAttentionModal'=>false,
    'orderAttentionReason'=>'',
    'showOrderCancelModal'=>false,
    'orderCancellationReason'=>'',
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
    'inquiryResults'=>collect(),
    'inquirySearch'=>'',
    'selectedLinkInquiry'=>null,
    'showInquiryLinkConfirm'=>false,
    'showInquiryUnlinkConfirm'=>false,
    'unlinkInquiryId'=>null,
    'canManageInquiryLink'=>false,
    'linkedInquiryCanOpen'=>false,
    'canViewLinkedInquiryDocuments'=>false,
    'canExportLinkedInquiryDocuments'=>false,
    'financeSummary'=>null,
    'financeContacts'=>null,
    'financeUsers'=>null,
    'financeInvoiceTypes'=>collect(),
    'financeCurrencies'=>collect(),
    'financePaymentTerms'=>collect(),
    'financePaymentMethods'=>collect(),
    'financeReceivedAccounts'=>collect(),
    'canCreateFinance'=>false,
    'canEditFinance'=>false,
    'canViewFinance'=>false,
    'showCreateInvoiceModal'=>false,
    'invoiceType'=>'Final invoice',
    'invoiceCurrency'=>'USD',
    'invoiceIssueDate'=>'',
    'invoicePaymentTerms'=>'Net 15 days',
    'invoiceDueDate'=>'',
    'invoiceBillingContactId'=>null,
    'invoiceLineItems'=>[],
    'invoicePurchaseOrderReference'=>'',
    'invoiceNotes'=>'',
    'invoiceTaxRate'=>'0',
    'invoiceRemoteAreaCharge'=>0,
    'invoiceRemoteAreaName'=>'',
    'invoiceRemoteAreaPostalCode'=>'',
    'invoiceSupportingDocument'=>null,
    'invoiceEmailAfterCreation'=>false,
    'showRecordPaymentModal'=>false,
    'paymentInvoiceId'=>null,
    'paymentDate'=>'',
    'paymentMethod'=>'Bank transfer',
    'paymentAmount'=>'',
    'paymentReference'=>'',
    'paymentNotes'=>'',
    'paymentReceipt'=>null,
    'showCollectionUpdateModal'=>false,
    'collectionOwnerId'=>null,
    'collectionFollowUpDate'=>'',
    'collectionNextFollowUpDate'=>'',
    'collectionNote'=>'',
    'showOrderWorkflowActionModal'=>false,
    'orderWorkflowActionTaskId'=>null,
    'orderWorkflowActionStep'=>'main',
    'orderWorkflowActionPayload'=>[],
    'orderWorkflowActionAttachment'=>null,
    'orderWorkflowActionRevisionComments'=>[],
    'orderWorkflowActionRevisionAttachments'=>[],
    'orderWorkflowEmailFallback'=>false,
    'orderWorkflowEmailFallbackMessage'=>'',
    'orderWorkflowEmailFallbackAttempts'=>0,
    'showShipmentModal'=>false,
    'shipmentModalTaskId'=>null,
    'shipmentEditingId'=>null,
    'shipmentModalMode'=>'same_address',
    'shipmentForm'=>[],
    'showShipmentDetailsModal'=>false,
    'shipmentDetailsId'=>null,
]));

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

foreach (array_filter(([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'shipmentUrgencyOptions'=>collect(),
    'overviewPhaseId'=>null,
    'orderDetailContext'=>[],
    'orderDetailSectionsReady'=>[],
    'orderRedoContext'=>[],
    'orderRedoForm'=>[],
    'products'=>collect(),
    'categories'=>collect(),
    'showAddJobProductForm'=>false,
    'jobProductSearch'=>'',
    'jobProductSearchResults'=>collect(),
    'jobProductSearchSuppliers'=>collect(),
    'jobProductResultTotal'=>0,
    'jobProductShowAllResults'=>false,
    'jobProductSelectedProduct'=>null,
    'jobProductSelectedSupplier'=>null,
    'jobProductCategory'=>'',
    'jobProductQuantity'=>'1000',
    'jobProductUnitPrice'=>'0.00',
    'jobProductSupplierId'=>null,
    'jobProductSupplierLabel'=>'',
    'jobProductSupplierSkipped'=>false,
    'jobProductSupplierLocked'=>false,
    'showEditOrderProductModal'=>false, 'editOrderProductItemId'=>null, 'editOrderProductName'=>'', 'editOrderProductCode'=>'',
    'editOrderProductCategory'=>'', 'editOrderProductSearch'=>'', 'editOrderProductSearchResults'=>collect(),
    'editOrderProductSearchSuppliers'=>collect(), 'editOrderProductResultTotal'=>0, 'editOrderProductSelectedProduct'=>null,
    'editOrderProductSelectedSupplier'=>null, 'editOrderProductShowAllResults'=>false,
    'editOrderProductSupplierId'=>null, 'editOrderProductSupplierLabel'=>'', 'editOrderProductQuantity'=>'1',
    'editOrderProductUnitPrice'=>'0.00', 'editOrderProductNotes'=>'',
    'availableDocuments'=>collect(),
    'overviewTaskDocumentModalTask'=>null,
    'overviewTaskAvailableDocuments'=>collect(),
    'showOverviewTaskDocumentModal'=>false,
    'showAddOrderTaskForm'=>false,
    'newOrderTaskAssigneeId'=>null,
    'overviewTaskDocumentSource'=>'upload',
    'overviewTaskDocumentUpload'=>null,
    'overviewTaskRevisionUpload'=>[],
    'overviewTaskStagedUploads'=>[],
    'overviewTaskStagedRevisionUploads'=>[],
    'overviewTaskExistingDocumentId'=>null,
    'overviewTaskArtworkRevision'=>[],
    'overviewTaskRevisionDocumentIds'=>[],
    'overviewTaskLinkFormTaskId'=>null,
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'showOrderAttentionModal'=>false,
    'orderAttentionReason'=>'',
    'showOrderCancelModal'=>false,
    'orderCancellationReason'=>'',
    'jobDocumentUploads'=>[],
    'jobRequiredDocumentUpload'=>null,
    'jobDocumentTaskId'=>null,
    'showDocumentPicker'=>false,
    'lastJobDocumentUploadId'=>null,
    'lastJobDocumentTaskId'=>null,
    'inquiryResults'=>collect(),
    'inquirySearch'=>'',
    'selectedLinkInquiry'=>null,
    'showInquiryLinkConfirm'=>false,
    'showInquiryUnlinkConfirm'=>false,
    'unlinkInquiryId'=>null,
    'canManageInquiryLink'=>false,
    'linkedInquiryCanOpen'=>false,
    'canViewLinkedInquiryDocuments'=>false,
    'canExportLinkedInquiryDocuments'=>false,
    'financeSummary'=>null,
    'financeContacts'=>null,
    'financeUsers'=>null,
    'financeInvoiceTypes'=>collect(),
    'financeCurrencies'=>collect(),
    'financePaymentTerms'=>collect(),
    'financePaymentMethods'=>collect(),
    'financeReceivedAccounts'=>collect(),
    'canCreateFinance'=>false,
    'canEditFinance'=>false,
    'canViewFinance'=>false,
    'showCreateInvoiceModal'=>false,
    'invoiceType'=>'Final invoice',
    'invoiceCurrency'=>'USD',
    'invoiceIssueDate'=>'',
    'invoicePaymentTerms'=>'Net 15 days',
    'invoiceDueDate'=>'',
    'invoiceBillingContactId'=>null,
    'invoiceLineItems'=>[],
    'invoicePurchaseOrderReference'=>'',
    'invoiceNotes'=>'',
    'invoiceTaxRate'=>'0',
    'invoiceRemoteAreaCharge'=>0,
    'invoiceRemoteAreaName'=>'',
    'invoiceRemoteAreaPostalCode'=>'',
    'invoiceSupportingDocument'=>null,
    'invoiceEmailAfterCreation'=>false,
    'showRecordPaymentModal'=>false,
    'paymentInvoiceId'=>null,
    'paymentDate'=>'',
    'paymentMethod'=>'Bank transfer',
    'paymentAmount'=>'',
    'paymentReference'=>'',
    'paymentNotes'=>'',
    'paymentReceipt'=>null,
    'showCollectionUpdateModal'=>false,
    'collectionOwnerId'=>null,
    'collectionFollowUpDate'=>'',
    'collectionNextFollowUpDate'=>'',
    'collectionNote'=>'',
    'showOrderWorkflowActionModal'=>false,
    'orderWorkflowActionTaskId'=>null,
    'orderWorkflowActionStep'=>'main',
    'orderWorkflowActionPayload'=>[],
    'orderWorkflowActionAttachment'=>null,
    'orderWorkflowActionRevisionComments'=>[],
    'orderWorkflowActionRevisionAttachments'=>[],
    'orderWorkflowEmailFallback'=>false,
    'orderWorkflowEmailFallbackMessage'=>'',
    'orderWorkflowEmailFallbackAttempts'=>0,
    'showShipmentModal'=>false,
    'shipmentModalTaskId'=>null,
    'shipmentEditingId'=>null,
    'shipmentModalMode'=>'same_address',
    'shipmentForm'=>[],
    'showShipmentDetailsModal'=>false,
    'shipmentDetailsId'=>null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $manualAttention = (bool) ($job->attention_requested ?? false);
?>
<div
    <?php echo e($attributes->class('ft-job-detail-page ft-order-prototype-detail ft-detail-products-scope')); ?>

    x-data="{ redoNotice: '', redoNoticeOpen: false, showRedoNotice(message) { this.redoNotice = message; this.redoNoticeOpen = true; clearTimeout(this.__redoNoticeTimer); this.__redoNoticeTimer = setTimeout(() => this.redoNoticeOpen = false, 2600); } }"
    x-on:order-redo-notice.window="showRedoNotice($event.detail.message ?? 'Redo update saved.')"
>
    <?php if (isset($component)) { $__componentOriginal7d0d13f77e0bbde4ee23e564f6eba885 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7d0d13f77e0bbde4ee23e564f6eba885 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.header','data' => ['job' => $job,'context' => $orderDetailContext,'shipmentUrgencyOptions' => $shipmentUrgencyOptions,'redoContext' => $orderRedoContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext),'shipment-urgency-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyOptions),'redo-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7d0d13f77e0bbde4ee23e564f6eba885)): ?>
<?php $attributes = $__attributesOriginal7d0d13f77e0bbde4ee23e564f6eba885; ?>
<?php unset($__attributesOriginal7d0d13f77e0bbde4ee23e564f6eba885); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7d0d13f77e0bbde4ee23e564f6eba885)): ?>
<?php $component = $__componentOriginal7d0d13f77e0bbde4ee23e564f6eba885; ?>
<?php unset($__componentOriginal7d0d13f77e0bbde4ee23e564f6eba885); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal1df8a78b7363ad7d457b2eb92c82d1f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1df8a78b7363ad7d457b2eb92c82d1f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.tabs','data' => ['job' => $job,'detailTab' => $detailTab,'canViewFinance' => $canViewFinance,'canCreateFinance' => $canCreateFinance,'redoContext' => $orderRedoContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'detail-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailTab),'can-view-finance' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canViewFinance),'can-create-finance' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateFinance),'redo-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1df8a78b7363ad7d457b2eb92c82d1f7)): ?>
<?php $attributes = $__attributesOriginal1df8a78b7363ad7d457b2eb92c82d1f7; ?>
<?php unset($__attributesOriginal1df8a78b7363ad7d457b2eb92c82d1f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1df8a78b7363ad7d457b2eb92c82d1f7)): ?>
<?php $component = $__componentOriginal1df8a78b7363ad7d457b2eb92c82d1f7; ?>
<?php unset($__componentOriginal1df8a78b7363ad7d457b2eb92c82d1f7); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal27d979a54b82658ca881e2facfd9d0c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27d979a54b82658ca881e2facfd9d0c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.redo-banner','data' => ['job' => $job,'context' => $orderRedoContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.redo-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27d979a54b82658ca881e2facfd9d0c7)): ?>
<?php $attributes = $__attributesOriginal27d979a54b82658ca881e2facfd9d0c7; ?>
<?php unset($__attributesOriginal27d979a54b82658ca881e2facfd9d0c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27d979a54b82658ca881e2facfd9d0c7)): ?>
<?php $component = $__componentOriginal27d979a54b82658ca881e2facfd9d0c7; ?>
<?php unset($__componentOriginal27d979a54b82658ca881e2facfd9d0c7); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab==='overview'): ?>
        <?php if (isset($component)) { $__componentOriginaldad3229fa826ba1f935ba3112a62f4a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-overview','data' => ['job' => $job,'expandedPhaseIds' => $expandedPhaseIds,'taskStatuses' => $taskStatuses,'users' => $users,'mentionUsers' => $mentionUsers,'priorities' => $priorities,'shipmentUrgencyOptions' => $shipmentUrgencyOptions,'overviewPhaseId' => $overviewPhaseId,'orderDetailContext' => $orderDetailContext,'detailSectionsReady' => $orderDetailSectionsReady,'products' => $products,'categories' => $categories,'showAddJobProductForm' => $showAddJobProductForm,'jobProductSearch' => $jobProductSearch,'jobProductSearchResults' => $jobProductSearchResults,'jobProductSearchSuppliers' => $jobProductSearchSuppliers,'jobProductResultTotal' => $jobProductResultTotal,'jobProductShowAllResults' => $jobProductShowAllResults,'jobProductSelectedProduct' => $jobProductSelectedProduct,'jobProductSelectedSupplier' => $jobProductSelectedSupplier,'jobProductCategory' => $jobProductCategory,'jobProductQuantity' => $jobProductQuantity,'jobProductUnitPrice' => $jobProductUnitPrice,'jobProductSupplierId' => $jobProductSupplierId,'jobProductSupplierLabel' => $jobProductSupplierLabel,'jobProductSupplierSkipped' => $jobProductSupplierSkipped,'jobProductSupplierLocked' => $jobProductSupplierLocked,'showEditOrderProductModal' => $showEditOrderProductModal,'editOrderProductItemId' => $editOrderProductItemId,'editOrderProductName' => $editOrderProductName,'editOrderProductCode' => $editOrderProductCode,'editOrderProductCategory' => $editOrderProductCategory,'editOrderProductSearch' => $editOrderProductSearch,'editOrderProductSearchResults' => $editOrderProductSearchResults,'editOrderProductSearchSuppliers' => $editOrderProductSearchSuppliers,'editOrderProductResultTotal' => $editOrderProductResultTotal,'editOrderProductSelectedProduct' => $editOrderProductSelectedProduct,'editOrderProductSelectedSupplier' => $editOrderProductSelectedSupplier,'editOrderProductShowAllResults' => $editOrderProductShowAllResults,'editOrderProductSupplierId' => $editOrderProductSupplierId,'editOrderProductSupplierLabel' => $editOrderProductSupplierLabel,'editOrderProductQuantity' => $editOrderProductQuantity,'editOrderProductUnitPrice' => $editOrderProductUnitPrice,'editOrderProductNotes' => $editOrderProductNotes,'jobTaskSearch' => $jobTaskSearch,'activityTab' => $activityTab,'activityPage' => $activityPage,'focusComment' => $focusComment,'jobDocumentUploads' => $jobDocumentUploads,'overviewTaskDocumentModalTask' => $overviewTaskDocumentModalTask,'overviewTaskAvailableDocuments' => $overviewTaskAvailableDocuments,'showOverviewTaskDocumentModal' => $showOverviewTaskDocumentModal,'overviewTaskDocumentSource' => $overviewTaskDocumentSource,'overviewTaskDocumentUpload' => $overviewTaskDocumentUpload,'overviewTaskRevisionUpload' => $overviewTaskRevisionUpload,'overviewTaskStagedUploads' => $overviewTaskStagedUploads,'overviewTaskStagedRevisionUploads' => $overviewTaskStagedRevisionUploads,'overviewTaskExistingDocumentId' => $overviewTaskExistingDocumentId,'overviewTaskArtworkRevision' => $overviewTaskArtworkRevision,'overviewTaskRevisionDocumentIds' => $overviewTaskRevisionDocumentIds,'overviewTaskLinkFormTaskId' => $overviewTaskLinkFormTaskId,'showAddOrderTaskForm' => $showAddOrderTaskForm,'newOrderTaskAssigneeId' => $newOrderTaskAssigneeId,'showOrderWorkflowActionModal' => $showOrderWorkflowActionModal,'orderWorkflowActionTaskId' => $orderWorkflowActionTaskId,'orderWorkflowActionStep' => $orderWorkflowActionStep,'orderWorkflowActionPayload' => $orderWorkflowActionPayload,'orderWorkflowActionAttachment' => $orderWorkflowActionAttachment,'orderWorkflowActionRevisionComments' => $orderWorkflowActionRevisionComments,'orderWorkflowActionRevisionAttachments' => $orderWorkflowActionRevisionAttachments,'orderWorkflowEmailFallback' => $orderWorkflowEmailFallback,'orderWorkflowEmailFallbackMessage' => $orderWorkflowEmailFallbackMessage,'orderWorkflowEmailFallbackAttempts' => $orderWorkflowEmailFallbackAttempts,'showShipmentModal' => $showShipmentModal,'shipmentModalTaskId' => $shipmentModalTaskId,'shipmentEditingId' => $shipmentEditingId,'shipmentModalMode' => $shipmentModalMode,'shipmentForm' => $shipmentForm,'showShipmentDetailsModal' => $showShipmentDetailsModal,'shipmentDetailsId' => $shipmentDetailsId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'expanded-phase-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedPhaseIds),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'shipment-urgency-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyOptions),'overview-phase-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewPhaseId),'order-detail-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext),'detail-sections-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailSectionsReady),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'show-add-job-product-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showAddJobProductForm),'job-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearch),'job-product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchResults),'job-product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchSuppliers),'job-product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductResultTotal),'job-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductShowAllResults),'job-product-selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedProduct),'job-product-selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedSupplier),'job-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductCategory),'job-product-quantity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductQuantity),'job-product-unit-price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductUnitPrice),'job-product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierId),'job-product-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLabel),'job-product-supplier-skipped' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierSkipped),'job-product-supplier-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLocked),'show-edit-order-product-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showEditOrderProductModal),'edit-order-product-item-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductItemId),'edit-order-product-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductName),'edit-order-product-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCode),'edit-order-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCategory),'edit-order-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearch),'edit-order-product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchResults),'edit-order-product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchSuppliers),'edit-order-product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductResultTotal),'edit-order-product-selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedProduct),'edit-order-product-selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedSupplier),'edit-order-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductShowAllResults),'edit-order-product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierId),'edit-order-product-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierLabel),'edit-order-product-quantity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductQuantity),'edit-order-product-unit-price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductUnitPrice),'edit-order-product-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductNotes),'job-task-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobTaskSearch),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads),'overview-task-document-modal-task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentModalTask),'overview-task-available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskAvailableDocuments),'show-overview-task-document-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showOverviewTaskDocumentModal),'overview-task-document-source' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentSource),'overview-task-document-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentUpload),'overview-task-revision-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskRevisionUpload),'overview-task-staged-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskStagedUploads),'overview-task-staged-revision-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskStagedRevisionUploads),'overview-task-existing-document-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskExistingDocumentId),'overview-task-artwork-revision' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskArtworkRevision),'overview-task-revision-document-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskRevisionDocumentIds),'overview-task-link-form-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskLinkFormTaskId),'show-add-order-task-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showAddOrderTaskForm),'new-order-task-assignee-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newOrderTaskAssigneeId),'show-order-workflow-action-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showOrderWorkflowActionModal),'order-workflow-action-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionTaskId),'order-workflow-action-step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionStep),'order-workflow-action-payload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionPayload),'order-workflow-action-attachment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionAttachment),'order-workflow-action-revision-comments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionRevisionComments),'order-workflow-action-revision-attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionRevisionAttachments),'order-workflow-email-fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallback),'order-workflow-email-fallback-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackMessage),'order-workflow-email-fallback-attempts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackAttempts),'show-shipment-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentModal),'shipment-modal-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalTaskId),'shipment-editing-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentEditingId),'shipment-modal-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentModalMode),'shipment-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentForm),'show-shipment-details-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showShipmentDetailsModal),'shipment-details-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentDetailsId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3)): ?>
<?php $attributes = $__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3; ?>
<?php unset($__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldad3229fa826ba1f935ba3112a62f4a3)): ?>
<?php $component = $__componentOriginaldad3229fa826ba1f935ba3112a62f4a3; ?>
<?php unset($__componentOriginaldad3229fa826ba1f935ba3112a62f4a3); ?>
<?php endif; ?>
    <?php elseif($detailTab==='inquiry'): ?>
        <?php if (isset($component)) { $__componentOriginal4fcfaf8252741a09c01f4d7850ec4125 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-inquiry','data' => ['job' => $job,'results' => $inquiryResults,'search' => $inquirySearch,'selectedInquiry' => $selectedLinkInquiry,'showLinkConfirm' => $showInquiryLinkConfirm,'showUnlinkConfirm' => $showInquiryUnlinkConfirm,'unlinkInquiryId' => $unlinkInquiryId,'canManage' => $canManageInquiryLink,'linkedInquiryCanOpen' => $linkedInquiryCanOpen,'canViewLinkedInquiryDocuments' => $canViewLinkedInquiryDocuments,'canExportLinkedInquiryDocuments' => $canExportLinkedInquiryDocuments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-inquiry'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryResults),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquirySearch),'selected-inquiry' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedLinkInquiry),'show-link-confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showInquiryLinkConfirm),'show-unlink-confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showInquiryUnlinkConfirm),'unlink-inquiry-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($unlinkInquiryId),'can-manage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canManageInquiryLink),'linked-inquiry-can-open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($linkedInquiryCanOpen),'can-view-linked-inquiry-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canViewLinkedInquiryDocuments),'can-export-linked-inquiry-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExportLinkedInquiryDocuments)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125)): ?>
<?php $attributes = $__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125; ?>
<?php unset($__attributesOriginal4fcfaf8252741a09c01f4d7850ec4125); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fcfaf8252741a09c01f4d7850ec4125)): ?>
<?php $component = $__componentOriginal4fcfaf8252741a09c01f4d7850ec4125; ?>
<?php unset($__componentOriginal4fcfaf8252741a09c01f4d7850ec4125); ?>
<?php endif; ?>
    <?php elseif($detailTab==='redo' && (bool) ($orderRedoContext['hasRedo'] ?? false)): ?>
        <?php if (isset($component)) { $__componentOriginal33bb12f013b848e1248475b3ed077d50 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33bb12f013b848e1248475b3ed077d50 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.redo-panel','data' => ['job' => $job,'context' => $orderRedoContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.redo-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33bb12f013b848e1248475b3ed077d50)): ?>
<?php $attributes = $__attributesOriginal33bb12f013b848e1248475b3ed077d50; ?>
<?php unset($__attributesOriginal33bb12f013b848e1248475b3ed077d50); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33bb12f013b848e1248475b3ed077d50)): ?>
<?php $component = $__componentOriginal33bb12f013b848e1248475b3ed077d50; ?>
<?php unset($__componentOriginal33bb12f013b848e1248475b3ed077d50); ?>
<?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginale9bd4c7bc89f1675cde7d2af9804ef4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9bd4c7bc89f1675cde7d2af9804ef4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.activity','data' => ['job' => $job,'mentionUsers' => $mentionUsers,'activityTab' => $activityTab,'activityPage' => $activityPage,'focusComment' => $focusComment,'canComment' => (bool) ($orderDetailContext['canComment'] ?? false)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.activity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'can-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) ($orderDetailContext['canComment'] ?? false))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale9bd4c7bc89f1675cde7d2af9804ef4e)): ?>
<?php $attributes = $__attributesOriginale9bd4c7bc89f1675cde7d2af9804ef4e; ?>
<?php unset($__attributesOriginale9bd4c7bc89f1675cde7d2af9804ef4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale9bd4c7bc89f1675cde7d2af9804ef4e)): ?>
<?php $component = $__componentOriginale9bd4c7bc89f1675cde7d2af9804ef4e; ?>
<?php unset($__componentOriginale9bd4c7bc89f1675cde7d2af9804ef4e); ?>
<?php endif; ?>
    <?php elseif($detailTab==='finance'): ?>
        <?php if (isset($component)) { $__componentOriginalc5092f9572675e4d09a4c5d853dd912c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5092f9572675e4d09a4c5d853dd912c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.detail','data' => ['job' => $job,'summary' => $financeSummary,'redoContext' => $orderRedoContext,'contacts' => $financeContacts ?? collect(),'users' => $financeUsers ?? collect(),'invoiceTypes' => $financeInvoiceTypes ?? collect(),'currencies' => $financeCurrencies ?? collect(),'paymentTerms' => $financePaymentTerms ?? collect(),'paymentMethods' => $financePaymentMethods ?? collect(),'receivedAccounts' => $financeReceivedAccounts ?? collect(),'canCreate' => $canCreateFinance,'canEdit' => $canEditFinance,'showCreateInvoiceModal' => $showCreateInvoiceModal,'invoiceType' => $invoiceType,'invoiceCurrency' => $invoiceCurrency,'invoiceIssueDate' => $invoiceIssueDate,'invoicePaymentTerms' => $invoicePaymentTerms,'invoiceDueDate' => $invoiceDueDate,'invoiceBillingContactId' => $invoiceBillingContactId,'invoiceLineItems' => $invoiceLineItems,'invoicePurchaseOrderReference' => $invoicePurchaseOrderReference,'invoiceNotes' => $invoiceNotes,'invoiceTaxRate' => $invoiceTaxRate,'invoiceRemoteAreaCharge' => $invoiceRemoteAreaCharge,'invoiceRemoteAreaName' => $invoiceRemoteAreaName,'invoiceRemoteAreaPostalCode' => $invoiceRemoteAreaPostalCode,'invoiceSupportingDocument' => $invoiceSupportingDocument,'invoiceEmailAfterCreation' => $invoiceEmailAfterCreation,'showRecordPaymentModal' => $showRecordPaymentModal,'paymentInvoiceId' => $paymentInvoiceId,'paymentDate' => $paymentDate,'paymentMethod' => $paymentMethod,'paymentAmount' => $paymentAmount,'paymentReference' => $paymentReference,'paymentNotes' => $paymentNotes,'paymentReceipt' => $paymentReceipt,'showCollectionUpdateModal' => $showCollectionUpdateModal,'collectionOwnerId' => $collectionOwnerId,'collectionFollowUpDate' => $collectionFollowUpDate,'collectionNextFollowUpDate' => $collectionNextFollowUpDate,'collectionNote' => $collectionNote]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'summary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeSummary),'redo-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoContext),'contacts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeContacts ?? collect()),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeUsers ?? collect()),'invoice-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeInvoiceTypes ?? collect()),'currencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeCurrencies ?? collect()),'payment-terms' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financePaymentTerms ?? collect()),'payment-methods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financePaymentMethods ?? collect()),'received-accounts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($financeReceivedAccounts ?? collect()),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateFinance),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditFinance),'show-create-invoice-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showCreateInvoiceModal),'invoice-type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceType),'invoice-currency' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceCurrency),'invoice-issue-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceIssueDate),'invoice-payment-terms' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoicePaymentTerms),'invoice-due-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceDueDate),'invoice-billing-contact-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceBillingContactId),'invoice-line-items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceLineItems),'invoice-purchase-order-reference' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoicePurchaseOrderReference),'invoice-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceNotes),'invoice-tax-rate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceTaxRate),'invoice-remote-area-charge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceRemoteAreaCharge),'invoice-remote-area-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceRemoteAreaName),'invoice-remote-area-postal-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceRemoteAreaPostalCode),'invoice-supporting-document' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceSupportingDocument),'invoice-email-after-creation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceEmailAfterCreation),'show-record-payment-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showRecordPaymentModal),'payment-invoice-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentInvoiceId),'payment-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentDate),'payment-method' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentMethod),'payment-amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentAmount),'payment-reference' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentReference),'payment-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentNotes),'payment-receipt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentReceipt),'show-collection-update-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showCollectionUpdateModal),'collection-owner-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionOwnerId),'collection-follow-up-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionFollowUpDate),'collection-next-follow-up-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionNextFollowUpDate),'collection-note' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collectionNote)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5092f9572675e4d09a4c5d853dd912c)): ?>
<?php $attributes = $__attributesOriginalc5092f9572675e4d09a4c5d853dd912c; ?>
<?php unset($__attributesOriginalc5092f9572675e4d09a4c5d853dd912c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5092f9572675e4d09a4c5d853dd912c)): ?>
<?php $component = $__componentOriginalc5092f9572675e4d09a4c5d853dd912c; ?>
<?php unset($__componentOriginalc5092f9572675e4d09a4c5d853dd912c); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    <?php if (isset($component)) { $__componentOriginaldae41b4154cad483d147bb1f895c9b1e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae41b4154cad483d147bb1f895c9b1e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.redo-modal','data' => ['job' => $job,'context' => $orderRedoContext,'form' => $orderRedoForm,'mentionUsers' => $mentionUsers]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.redo-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoContext),'form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderRedoForm),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae41b4154cad483d147bb1f895c9b1e)): ?>
<?php $attributes = $__attributesOriginaldae41b4154cad483d147bb1f895c9b1e; ?>
<?php unset($__attributesOriginaldae41b4154cad483d147bb1f895c9b1e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae41b4154cad483d147bb1f895c9b1e)): ?>
<?php $component = $__componentOriginaldae41b4154cad483d147bb1f895c9b1e; ?>
<?php unset($__componentOriginaldae41b4154cad483d147bb1f895c9b1e); ?>
<?php endif; ?>

    <div class="ft-redo-toast" x-cloak x-show="redoNoticeOpen" x-transition x-text="redoNotice" role="status" aria-live="polite"></div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOrderCancelModal): ?>
        <div
            class="ft-order-modal-backdrop"
            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-cancel-modal'; ?>wire:key="order-cancel-modal"
            wire:click.self="closeOrderCancelModal"
        >
            <section
                class="ft-order-modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="order-cancel-modal-title"
                x-data="{ cancelling: false }"
            >
                <header>
                    <div>
                        <h2 id="order-cancel-modal-title">Cancel Order</h2>

                        <p>
                            <?php echo e($job->displayOrderNumber()); ?>

                            · cancellation is available through the QC stage.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeOrderCancelModal"
                        aria-label="Close"
                    >
                        ×
                    </button>
                </header>

                <div class="ft-order-modal-body ft-mention-host">
                    <div class="ft-order-critical-note">
                        <strong>This stops workflow progression.</strong>

                        <span>
                            Open tasks are marked cancelled.
                            Existing documents, products, history,
                            and audit records are retained.
                        </span>
                    </div>

                    <label for="order-cancellation-reason">
                        Cancellation reason *
                    </label>

                    <textarea
                        id="order-cancellation-reason"
                        x-ref="cancelReason"
                        class="ft-mention-input"
                        data-rich-text
                        wire:model="orderCancellationReason"
                        rows="5"
                        autocomplete="off"
                        data-mention-users="<?php echo e(json_encode(
                            collect($mentionUsers)->values()->all(),
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        )); ?>"
                        placeholder="Explain why this Order is being cancelled. Type @ to mention someone, paste text, or paste an image..."
                    ></textarea>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderCancellationReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="validation-error">
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <footer>
                    <button
                        type="button"
                        class="secondary"
                        wire:click="closeOrderCancelModal"
                        x-bind:disabled="cancelling"
                    >
                        Keep Order
                    </button>

                    <button
                        type="button"
                        class="danger"
                        x-bind:disabled="cancelling"
                        x-on:click="
                            if (cancelling) return;

                            cancelling = true;

                            (async () => {
                                try {
                                    const input = $refs.cancelReason;

                                    const value =
                                        input?.__flowtrackRichTextValueAsync
                                            ? await input.__flowtrackRichTextValueAsync()
                                            : (input?.value ?? '');

                                    await $wire.confirmOrderCancellation(value);
                                } finally {
                                    cancelling = false;
                                }
                            })();
                        "
                    >
                        <span x-show="!cancelling">
                            Cancel Order
                        </span>

                        <span
                            x-cloak
                            x-show="cancelling"
                        >
                            Cancelling...
                        </span>
                    </button>
                </footer>
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOrderAttentionModal): ?>
        <div class="ft-inquiry-attention-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-attention-modal'; ?>wire:key="order-attention-modal" wire:click.self="closeOrderAttentionReason">
            <section class="ft-inquiry-attention-modal" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="order-attention-modal-title">
                <header class="ft-inquiry-attention-modal-head">
                    <div>
                        <h2 id="order-attention-modal-title">Request attention</h2>
                        <p><?php echo e($job->displayOrderNumber()); ?> · Admin, Super Admin and the Order creator will be notified.</p>
                    </div>
                    <button type="button" class="ft-inquiry-attention-modal-close" wire:click="closeOrderAttentionReason" aria-label="Close">×</button>
                </header>
                <div class="ft-inquiry-attention-modal-body ft-mention-host">
                    <label for="order-attention-reason">Reason for flag *</label>
                    <textarea id="order-attention-reason" class="ft-mention-input" wire:model="orderAttentionReason" rows="5" maxlength="2000" autocomplete="off" data-mention-users="<?php echo e(json_encode(collect($mentionUsers)->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>" placeholder="Explain what needs attention. Type @ to mention a user..."></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderAttentionReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-attention-modal-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <p class="ft-inquiry-attention-modal-help">The reason is added to Order comments. Use <b>@</b> to mention specific users in addition to the automatic Admin/Super Admin/creator notification.</p>
                </div>
                <footer class="ft-inquiry-attention-modal-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($manualAttention): ?><button type="button" class="ft-inquiry-attention-clear" wire:click="clearOrderAttention" wire:loading.attr="disabled" wire:target="clearOrderAttention">Clear flag</button><?php else: ?><span></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div>
                        <button type="button" class="secondary" wire:click="closeOrderAttentionReason">Cancel</button>
                        <button type="button" class="primary" wire:click="saveOrderAttentionReason" wire:loading.attr="disabled" wire:target="saveOrderAttentionReason">
                            <span wire:loading.remove wire:target="saveOrderAttentionReason">Request attention</span>
                            <span wire:loading wire:target="saveOrderAttentionReason">Saving...</span>
                        </button>
                    </div>
                </footer>
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail.blade.php ENDPATH**/ ?>