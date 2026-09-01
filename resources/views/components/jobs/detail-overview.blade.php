@props([
    'job',
    'taskStatuses' => collect(),
    'users' => collect(),
    'mentionUsers' => collect(),
    'priorities' => collect(),
    'shipmentUrgencyOptions' => collect(),
    'overviewPhaseId' => null,
    'orderDetailContext' => [],
    'detailSectionsReady' => [],
    'products' => collect(),
    'categories' => collect(),
    'showAddJobProductForm' => false,
    'jobProductSearch' => '',
    'jobProductSearchResults' => collect(),
    'jobProductSearchSuppliers' => collect(),
    'jobProductResultTotal' => 0,
    'jobProductShowAllResults' => false,
    'jobProductSelectedProduct' => null,
    'jobProductSelectedSupplier' => null,
    'jobProductCategory' => '',
    'jobProductQuantity' => '1000',
    'jobProductUnitPrice' => '0.00',
    'jobProductSupplierId' => null,
    'jobProductSupplierLabel' => '',
    'jobProductSupplierLocked' => false,
    'showEditOrderProductModal' => false, 'editOrderProductItemId' => null, 'editOrderProductName' => '', 'editOrderProductCode' => '',
    'editOrderProductCategory' => '', 'editOrderProductSearch' => '', 'editOrderProductSearchResults' => collect(),
    'editOrderProductSearchSuppliers' => collect(), 'editOrderProductResultTotal' => 0, 'editOrderProductSelectedProduct' => null,
    'editOrderProductSelectedSupplier' => null, 'editOrderProductShowAllResults' => false,
    'editOrderProductSupplierId' => null, 'editOrderProductSupplierLabel' => '', 'editOrderProductQuantity' => '1',
    'editOrderProductUnitPrice' => '0.00', 'editOrderProductNotes' => '',
    'jobTaskSearch' => '',
    'activityTab' => 'all',
    'activityPage' => 1,
    'focusComment' => null,
    'jobDocumentUploads' => [],
    'overviewTaskDocumentModalTask' => null,
    'overviewTaskAvailableDocuments' => collect(),
    'showOverviewTaskDocumentModal' => false,
    'overviewTaskDocumentSource' => 'upload',
    'overviewTaskDocumentUpload' => null,
    'overviewTaskRevisionUpload' => [],
    'overviewTaskExistingDocumentId' => null,
    'overviewTaskArtworkRevision' => [],
    'overviewTaskRevisionDocumentIds' => [],
    'overviewTaskLinkFormTaskId' => null,
    'showAddOrderTaskForm' => false,
    'newOrderTaskAssigneeId' => null,
    'showOrderWorkflowActionModal' => false,
    'orderWorkflowActionTaskId' => null,
    'orderWorkflowActionStep' => 'main',
    'orderWorkflowActionPayload' => [],
    'orderWorkflowActionAttachment' => null,
    'orderWorkflowActionRevisionComments' => [],
    'orderWorkflowActionRevisionAttachments' => [],
    'orderWorkflowEmailFallback' => false,
    'orderWorkflowEmailFallbackMessage' => '',
    'orderWorkflowEmailFallbackAttempts' => 0,
])
@php
    // Presentation only: all relationships were eager-loaded in JobService.
    $currentTasks = \App\Support\OrderDetailPresenter::currentTasks($job);
    $nextTask = \App\Support\OrderDetailPresenter::nextTask($job);
    $canEditJob = (bool) ($orderDetailContext['canEditJob'] ?? false);
    $canChangeOwner = (bool) ($orderDetailContext['canChangeOwner'] ?? false);
@endphp
<div class="ft-order-prototype-overview">
    <x-jobs.order-detail.summary :job="$job" :next-task="$nextTask" :current-tasks="$currentTasks" />

    <div class="overview-grid ft-order-overview-grid">
        <x-jobs.order-detail.overview-card :job="$job" :can-edit-job="$canEditJob" :mention-users="$mentionUsers" />
        <div class="overview-side ft-order-overview-side">
            <x-jobs.order-detail.planning
                :job="$job"
                :can-edit-job="$canEditJob"
                :can-change-owner="$canChangeOwner"
                :shipment-urgency-options="$shipmentUrgencyOptions"
                :context="$orderDetailContext"
            />
            <x-jobs.order-detail.shipping :job="$job" :can-edit-job="$canEditJob" />
        </div>
    </div>

    @if((bool) ($detailSectionsReady['products'] ?? false))
        <x-jobs.order-detail.products
            :job="$job"
            :context="$orderDetailContext"
            :show-add-job-product-form="$showAddJobProductForm"
            :job-product-search="$jobProductSearch"
            :job-product-search-results="$jobProductSearchResults"
            :job-product-search-suppliers="$jobProductSearchSuppliers"
            :job-product-result-total="$jobProductResultTotal"
            :job-product-show-all-results="$jobProductShowAllResults"
            :job-product-selected-product="$jobProductSelectedProduct"
            :job-product-selected-supplier="$jobProductSelectedSupplier"
            :job-product-category="$jobProductCategory"
            :job-product-quantity="$jobProductQuantity"
            :job-product-unit-price="$jobProductUnitPrice"
            :job-product-supplier-id="$jobProductSupplierId"
            :job-product-supplier-label="$jobProductSupplierLabel"
            :job-product-supplier-locked="$jobProductSupplierLocked"
            :show-edit-order-product-modal="$showEditOrderProductModal"
            :edit-order-product-item-id="$editOrderProductItemId"
            :edit-order-product-name="$editOrderProductName"
            :edit-order-product-code="$editOrderProductCode"
            :edit-order-product-category="$editOrderProductCategory"
            :edit-order-product-search="$editOrderProductSearch"
            :edit-order-product-search-results="$editOrderProductSearchResults"
            :edit-order-product-search-suppliers="$editOrderProductSearchSuppliers"
            :edit-order-product-result-total="$editOrderProductResultTotal"
            :edit-order-product-selected-product="$editOrderProductSelectedProduct"
            :edit-order-product-selected-supplier="$editOrderProductSelectedSupplier"
            :edit-order-product-show-all-results="$editOrderProductShowAllResults"
            :edit-order-product-supplier-id="$editOrderProductSupplierId"
            :edit-order-product-supplier-label="$editOrderProductSupplierLabel"
            :edit-order-product-quantity="$editOrderProductQuantity"
            :edit-order-product-unit-price="$editOrderProductUnitPrice"
            :edit-order-product-notes="$editOrderProductNotes"
        />
    @else
        <x-ui.progressive-section-loader
            section="products"
            method="loadDetailSection"
            key-prefix="order-detail"
            context-type="order"
            :context-id="$job->id"
            :rows="4"
            message="Loading order products when needed…"
            root-margin="360px 0px"
        />
    @endif

    @if((bool) ($detailSectionsReady['workflow'] ?? false))
        <x-jobs.order-detail.workflow
            :job="$job"
            :overview-phase-id="$overviewPhaseId"
            :task-statuses="$taskStatuses"
            :context="$orderDetailContext"
            :overview-task-link-form-task-id="$overviewTaskLinkFormTaskId"
        />
    @else
        <x-ui.progressive-section-loader
            section="workflow"
            method="loadDetailSection"
            key-prefix="order-detail"
            context-type="order"
            :context-id="$job->id"
            :rows="5"
            message="Loading workflow and tasks when needed…"
            root-margin="360px 0px"
        />
    @endif

    @if((bool) ($detailSectionsReady['attachments'] ?? false))
        <x-jobs.order-detail.attachments :job="$job" :context="$orderDetailContext" :job-document-uploads="$jobDocumentUploads" />
    @else
        <x-ui.progressive-section-loader
            section="attachments"
            method="loadDetailSection"
            key-prefix="order-detail"
            context-type="order"
            :context-id="$job->id"
            :rows="3"
            message="Loading attachments when needed…"
            root-margin="300px 0px"
        />
    @endif

    @if((bool) ($detailSectionsReady['activity'] ?? false))
        <x-jobs.order-detail.activity
            :job="$job"
            :mention-users="$mentionUsers"
            :activity-tab="$activityTab"
            :activity-page="$activityPage"
            :focus-comment="$focusComment"
            :can-comment="(bool) ($orderDetailContext['canComment'] ?? false)"
        />
    @else
        <x-ui.progressive-section-loader
            section="activity"
            method="loadDetailSection"
            key-prefix="order-detail"
            context-type="order"
            :context-id="$job->id"
            :rows="4"
            message="Loading activity when needed…"
            root-margin="300px 0px"
        />
    @endif

    @if($showOrderWorkflowActionModal && $orderWorkflowActionTaskId)
        @php
            $workflowActionTask = $job->tasks->firstWhere('id', (int) $orderWorkflowActionTaskId);
            $workflowActionModal = data_get($orderDetailContext, 'taskActionModals.'.(int) $orderWorkflowActionTaskId, []);
        @endphp
        @if($workflowActionTask)
            <x-jobs.order-detail.workflow-action-modal
                :job="$job"
                :task="$workflowActionTask"
                :config="$workflowActionModal"
                :step="$orderWorkflowActionStep"
                :payload="$orderWorkflowActionPayload"
                :attachment="$orderWorkflowActionAttachment"
                :revision-comments="$orderWorkflowActionRevisionComments"
                :revision-attachments="$orderWorkflowActionRevisionAttachments"
                :mention-users="$mentionUsers"
                :email-fallback="$orderWorkflowEmailFallback"
                :email-fallback-message="$orderWorkflowEmailFallbackMessage"
                :email-fallback-attempts="$orderWorkflowEmailFallbackAttempts"
            />
        @endif
    @endif

    @if($showOverviewTaskDocumentModal && $overviewTaskDocumentModalTask)
        <x-jobs.order-detail.document-modal
            :job="$job"
            :task="$overviewTaskDocumentModalTask"
            :available-documents="$overviewTaskAvailableDocuments"
            :source="$overviewTaskDocumentSource"
            :upload="$overviewTaskDocumentUpload"
            :revision-upload="$overviewTaskRevisionUpload"
            :existing-document-id="$overviewTaskExistingDocumentId"
            :artwork-revision="$overviewTaskArtworkRevision"
            :revision-document-ids="$overviewTaskRevisionDocumentIds"
            :context="$orderDetailContext"
        />
    @endif
</div>
