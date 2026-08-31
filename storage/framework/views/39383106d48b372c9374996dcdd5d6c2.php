<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
    'overviewTaskExistingDocumentId' => null,
    'overviewTaskLinkFormTaskId' => null,
    'showAddOrderTaskForm' => false,
    'newOrderTaskAssigneeId' => null,
    'showOrderWorkflowActionModal' => false,
    'orderWorkflowActionTaskId' => null,
    'orderWorkflowActionStep' => 'main',
    'orderWorkflowActionPayload' => [],
    'orderWorkflowEmailFallback' => false,
    'orderWorkflowEmailFallbackMessage' => '',
    'orderWorkflowEmailFallbackAttempts' => 0,
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
    'overviewTaskExistingDocumentId' => null,
    'overviewTaskLinkFormTaskId' => null,
    'showAddOrderTaskForm' => false,
    'newOrderTaskAssigneeId' => null,
    'showOrderWorkflowActionModal' => false,
    'orderWorkflowActionTaskId' => null,
    'orderWorkflowActionStep' => 'main',
    'orderWorkflowActionPayload' => [],
    'orderWorkflowEmailFallback' => false,
    'orderWorkflowEmailFallbackMessage' => '',
    'orderWorkflowEmailFallbackAttempts' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    // Presentation only: all relationships were eager-loaded in JobService.
    $currentTasks = \App\Support\OrderDetailPresenter::currentTasks($job);
    $nextTask = \App\Support\OrderDetailPresenter::nextTask($job);
    $canEditJob = (bool) ($orderDetailContext['canEditJob'] ?? false);
    $canChangeOwner = (bool) ($orderDetailContext['canChangeOwner'] ?? false);
?>
<div class="ft-order-prototype-overview">
    <?php if (isset($component)) { $__componentOriginalbdd3edd2059243a4a4f678d9dd440078 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbdd3edd2059243a4a4f678d9dd440078 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.summary','data' => ['job' => $job,'nextTask' => $nextTask,'currentTasks' => $currentTasks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.summary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'next-task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nextTask),'current-tasks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentTasks)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbdd3edd2059243a4a4f678d9dd440078)): ?>
<?php $attributes = $__attributesOriginalbdd3edd2059243a4a4f678d9dd440078; ?>
<?php unset($__attributesOriginalbdd3edd2059243a4a4f678d9dd440078); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbdd3edd2059243a4a4f678d9dd440078)): ?>
<?php $component = $__componentOriginalbdd3edd2059243a4a4f678d9dd440078; ?>
<?php unset($__componentOriginalbdd3edd2059243a4a4f678d9dd440078); ?>
<?php endif; ?>

    <div class="overview-grid ft-order-overview-grid">
        <?php if (isset($component)) { $__componentOriginalc6c37d149b8b1298e7c846ea184bcae2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc6c37d149b8b1298e7c846ea184bcae2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.overview-card','data' => ['job' => $job,'canEditJob' => $canEditJob,'mentionUsers' => $mentionUsers]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.overview-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'can-edit-job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditJob),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc6c37d149b8b1298e7c846ea184bcae2)): ?>
<?php $attributes = $__attributesOriginalc6c37d149b8b1298e7c846ea184bcae2; ?>
<?php unset($__attributesOriginalc6c37d149b8b1298e7c846ea184bcae2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc6c37d149b8b1298e7c846ea184bcae2)): ?>
<?php $component = $__componentOriginalc6c37d149b8b1298e7c846ea184bcae2; ?>
<?php unset($__componentOriginalc6c37d149b8b1298e7c846ea184bcae2); ?>
<?php endif; ?>
        <div class="overview-side ft-order-overview-side">
            <?php if (isset($component)) { $__componentOriginal89b3f0d2b6e3055ded0e4d12dece9e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b3f0d2b6e3055ded0e4d12dece9e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.planning','data' => ['job' => $job,'canEditJob' => $canEditJob,'canChangeOwner' => $canChangeOwner,'shipmentUrgencyOptions' => $shipmentUrgencyOptions,'context' => $orderDetailContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.planning'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'can-edit-job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditJob),'can-change-owner' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canChangeOwner),'shipment-urgency-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyOptions),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b3f0d2b6e3055ded0e4d12dece9e5b)): ?>
<?php $attributes = $__attributesOriginal89b3f0d2b6e3055ded0e4d12dece9e5b; ?>
<?php unset($__attributesOriginal89b3f0d2b6e3055ded0e4d12dece9e5b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b3f0d2b6e3055ded0e4d12dece9e5b)): ?>
<?php $component = $__componentOriginal89b3f0d2b6e3055ded0e4d12dece9e5b; ?>
<?php unset($__componentOriginal89b3f0d2b6e3055ded0e4d12dece9e5b); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalc103ef29f7e23a0c1f8ee41b24bf49db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc103ef29f7e23a0c1f8ee41b24bf49db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.shipping','data' => ['job' => $job,'canEditJob' => $canEditJob]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.shipping'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'can-edit-job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditJob)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc103ef29f7e23a0c1f8ee41b24bf49db)): ?>
<?php $attributes = $__attributesOriginalc103ef29f7e23a0c1f8ee41b24bf49db; ?>
<?php unset($__attributesOriginalc103ef29f7e23a0c1f8ee41b24bf49db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc103ef29f7e23a0c1f8ee41b24bf49db)): ?>
<?php $component = $__componentOriginalc103ef29f7e23a0c1f8ee41b24bf49db; ?>
<?php unset($__componentOriginalc103ef29f7e23a0c1f8ee41b24bf49db); ?>
<?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($detailSectionsReady['products'] ?? false)): ?>
        <?php if (isset($component)) { $__componentOriginalecfe6bb0ec1e143001ce80be73d172d2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.products','data' => ['job' => $job,'context' => $orderDetailContext,'showAddJobProductForm' => $showAddJobProductForm,'jobProductSearch' => $jobProductSearch,'jobProductSearchResults' => $jobProductSearchResults,'jobProductSearchSuppliers' => $jobProductSearchSuppliers,'jobProductResultTotal' => $jobProductResultTotal,'jobProductShowAllResults' => $jobProductShowAllResults,'jobProductSelectedProduct' => $jobProductSelectedProduct,'jobProductSelectedSupplier' => $jobProductSelectedSupplier,'jobProductCategory' => $jobProductCategory,'jobProductQuantity' => $jobProductQuantity,'jobProductUnitPrice' => $jobProductUnitPrice,'jobProductSupplierId' => $jobProductSupplierId,'jobProductSupplierLabel' => $jobProductSupplierLabel,'jobProductSupplierLocked' => $jobProductSupplierLocked,'showEditOrderProductModal' => $showEditOrderProductModal,'editOrderProductItemId' => $editOrderProductItemId,'editOrderProductName' => $editOrderProductName,'editOrderProductCode' => $editOrderProductCode,'editOrderProductCategory' => $editOrderProductCategory,'editOrderProductSearch' => $editOrderProductSearch,'editOrderProductSearchResults' => $editOrderProductSearchResults,'editOrderProductSearchSuppliers' => $editOrderProductSearchSuppliers,'editOrderProductResultTotal' => $editOrderProductResultTotal,'editOrderProductSelectedProduct' => $editOrderProductSelectedProduct,'editOrderProductSelectedSupplier' => $editOrderProductSelectedSupplier,'editOrderProductShowAllResults' => $editOrderProductShowAllResults,'editOrderProductSupplierId' => $editOrderProductSupplierId,'editOrderProductSupplierLabel' => $editOrderProductSupplierLabel,'editOrderProductQuantity' => $editOrderProductQuantity,'editOrderProductUnitPrice' => $editOrderProductUnitPrice,'editOrderProductNotes' => $editOrderProductNotes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.products'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext),'show-add-job-product-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showAddJobProductForm),'job-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearch),'job-product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchResults),'job-product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSearchSuppliers),'job-product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductResultTotal),'job-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductShowAllResults),'job-product-selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedProduct),'job-product-selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSelectedSupplier),'job-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductCategory),'job-product-quantity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductQuantity),'job-product-unit-price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductUnitPrice),'job-product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierId),'job-product-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLabel),'job-product-supplier-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobProductSupplierLocked),'show-edit-order-product-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showEditOrderProductModal),'edit-order-product-item-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductItemId),'edit-order-product-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductName),'edit-order-product-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCode),'edit-order-product-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductCategory),'edit-order-product-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearch),'edit-order-product-search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchResults),'edit-order-product-search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSearchSuppliers),'edit-order-product-result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductResultTotal),'edit-order-product-selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedProduct),'edit-order-product-selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSelectedSupplier),'edit-order-product-show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductShowAllResults),'edit-order-product-supplier-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierId),'edit-order-product-supplier-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductSupplierLabel),'edit-order-product-quantity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductQuantity),'edit-order-product-unit-price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductUnitPrice),'edit-order-product-notes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editOrderProductNotes)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2)): ?>
<?php $attributes = $__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2; ?>
<?php unset($__attributesOriginalecfe6bb0ec1e143001ce80be73d172d2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalecfe6bb0ec1e143001ce80be73d172d2)): ?>
<?php $component = $__componentOriginalecfe6bb0ec1e143001ce80be73d172d2; ?>
<?php unset($__componentOriginalecfe6bb0ec1e143001ce80be73d172d2); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'products','method' => 'loadDetailSection','keyPrefix' => 'order-detail','contextType' => 'order','contextId' => $job->id,'rows' => 4,'message' => 'Loading order products when needed…','rootMargin' => '360px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'products','method' => 'loadDetailSection','key-prefix' => 'order-detail','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'rows' => 4,'message' => 'Loading order products when needed…','root-margin' => '360px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($detailSectionsReady['workflow'] ?? false)): ?>
        <?php if (isset($component)) { $__componentOriginalacd6c8d39c322d451ed4aa64b3000636 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalacd6c8d39c322d451ed4aa64b3000636 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.workflow','data' => ['job' => $job,'overviewPhaseId' => $overviewPhaseId,'taskStatuses' => $taskStatuses,'context' => $orderDetailContext,'overviewTaskLinkFormTaskId' => $overviewTaskLinkFormTaskId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.workflow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'overview-phase-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewPhaseId),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext),'overview-task-link-form-task-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskLinkFormTaskId)]); ?>
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
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'workflow','method' => 'loadDetailSection','keyPrefix' => 'order-detail','contextType' => 'order','contextId' => $job->id,'rows' => 5,'message' => 'Loading workflow and tasks when needed…','rootMargin' => '360px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'workflow','method' => 'loadDetailSection','key-prefix' => 'order-detail','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'rows' => 5,'message' => 'Loading workflow and tasks when needed…','root-margin' => '360px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($detailSectionsReady['attachments'] ?? false)): ?>
        <?php if (isset($component)) { $__componentOriginalc381fed9822a6501599c1e870652f2cd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc381fed9822a6501599c1e870652f2cd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.attachments','data' => ['job' => $job,'context' => $orderDetailContext,'jobDocumentUploads' => $jobDocumentUploads]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.attachments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc381fed9822a6501599c1e870652f2cd)): ?>
<?php $attributes = $__attributesOriginalc381fed9822a6501599c1e870652f2cd; ?>
<?php unset($__attributesOriginalc381fed9822a6501599c1e870652f2cd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc381fed9822a6501599c1e870652f2cd)): ?>
<?php $component = $__componentOriginalc381fed9822a6501599c1e870652f2cd; ?>
<?php unset($__componentOriginalc381fed9822a6501599c1e870652f2cd); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'attachments','method' => 'loadDetailSection','keyPrefix' => 'order-detail','contextType' => 'order','contextId' => $job->id,'rows' => 3,'message' => 'Loading attachments when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'attachments','method' => 'loadDetailSection','key-prefix' => 'order-detail','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'rows' => 3,'message' => 'Loading attachments when needed…','root-margin' => '300px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($detailSectionsReady['activity'] ?? false)): ?>
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
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'activity','method' => 'loadDetailSection','keyPrefix' => 'order-detail','contextType' => 'order','contextId' => $job->id,'rows' => 4,'message' => 'Loading activity when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'activity','method' => 'loadDetailSection','key-prefix' => 'order-detail','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'rows' => 4,'message' => 'Loading activity when needed…','root-margin' => '300px 0px']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOrderWorkflowActionModal && $orderWorkflowActionTaskId): ?>
        <?php
            $workflowActionTask = $job->tasks->firstWhere('id', (int) $orderWorkflowActionTaskId);
            $workflowActionModal = data_get($orderDetailContext, 'taskActionModals.'.(int) $orderWorkflowActionTaskId, []);
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowActionTask): ?>
            <?php if (isset($component)) { $__componentOriginal8e43f3521a8e6328e588de4039a01fc1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e43f3521a8e6328e588de4039a01fc1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.workflow-action-modal','data' => ['job' => $job,'task' => $workflowActionTask,'config' => $workflowActionModal,'step' => $orderWorkflowActionStep,'payload' => $orderWorkflowActionPayload,'emailFallback' => $orderWorkflowEmailFallback,'emailFallbackMessage' => $orderWorkflowEmailFallbackMessage,'emailFallbackAttempts' => $orderWorkflowEmailFallbackAttempts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.workflow-action-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowActionTask),'config' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowActionModal),'step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionStep),'payload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionPayload),'email-fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallback),'email-fallback-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackMessage),'email-fallback-attempts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowEmailFallbackAttempts)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.document-modal','data' => ['job' => $job,'task' => $overviewTaskDocumentModalTask,'availableDocuments' => $overviewTaskAvailableDocuments,'source' => $overviewTaskDocumentSource,'upload' => $overviewTaskDocumentUpload,'existingDocumentId' => $overviewTaskExistingDocumentId,'context' => $orderDetailContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.document-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentModalTask),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskAvailableDocuments),'source' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentSource),'upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskDocumentUpload),'existing-document-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewTaskExistingDocumentId),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext)]); ?>
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
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-overview.blade.php ENDPATH**/ ?>