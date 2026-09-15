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
    'jobProductSupplierSkipped' => false,
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
    'overviewTaskStagedUploads' => [],
    'overviewTaskStagedRevisionUploads' => [],
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
    'orderWorkflowActionModalPreview' => [],
    'orderWorkflowActionAttachment' => null,
    'orderWorkflowActionRevisionComments' => [],
    'orderWorkflowActionRevisionAttachments' => [],
    'orderWorkflowEmailFallback' => false,
    'orderWorkflowEmailFallbackMessage' => '',
    'orderWorkflowEmailFallbackAttempts' => 0,
    'showShipmentModal' => false,
    'shipmentModalTaskId' => null,
    'shipmentEditingId' => null,
    'shipmentModalMode' => 'same_address',
    'shipmentForm' => [],
    'shipmentInlineTaskId' => null,
    'shipmentInlineEditingId' => null,
    'shipmentInlineAddressMode' => \App\Services\OrderShipmentService::MODE_SAME_ADDRESS,
    'shipmentInlineForm' => [],
    'showShipmentDetailsModal' => false,
    'shipmentDetailsId' => null,
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
    'jobProductSupplierSkipped' => false,
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
    'overviewTaskStagedUploads' => [],
    'overviewTaskStagedRevisionUploads' => [],
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
    'orderWorkflowActionModalPreview' => [],
    'orderWorkflowActionAttachment' => null,
    'orderWorkflowActionRevisionComments' => [],
    'orderWorkflowActionRevisionAttachments' => [],
    'orderWorkflowEmailFallback' => false,
    'orderWorkflowEmailFallbackMessage' => '',
    'orderWorkflowEmailFallbackAttempts' => 0,
    'showShipmentModal' => false,
    'shipmentModalTaskId' => null,
    'shipmentEditingId' => null,
    'shipmentModalMode' => 'same_address',
    'shipmentForm' => [],
    'shipmentInlineTaskId' => null,
    'shipmentInlineEditingId' => null,
    'shipmentInlineAddressMode' => \App\Services\OrderShipmentService::MODE_SAME_ADDRESS,
    'shipmentInlineForm' => [],
    'showShipmentDetailsModal' => false,
    'shipmentDetailsId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    // The parent shell uses the materialized workflow summary only. Full task
    // relations are owned by the isolated Workflow child below.
    $workflowSummary = (array) ($orderDetailContext['workflowSummary'] ?? []);
    $canEditJob = (bool) ($orderDetailContext['canEditJob'] ?? false);
    $canChangeOwner = (bool) ($orderDetailContext['canChangeOwner'] ?? false);
?>
<div class="ft-order-prototype-overview">
    <?php if (isset($component)) { $__componentOriginalbdd3edd2059243a4a4f678d9dd440078 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbdd3edd2059243a4a4f678d9dd440078 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.summary','data' => ['job' => $job,'summary' => $workflowSummary]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.summary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'summary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowSummary)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.planning','data' => ['job' => $job,'canEditJob' => $canEditJob,'canChangeOwner' => $canChangeOwner,'shipmentUrgencyOptions' => $shipmentUrgencyOptions,'context' => $orderDetailContext,'remoteArea' => $orderDetailContext['remoteArea'] ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.planning'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'can-edit-job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditJob),'can-change-owner' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canChangeOwner),'shipment-urgency-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipmentUrgencyOptions),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext),'remote-area' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderDetailContext['remoteArea'] ?? null)]); ?>
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

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('jobs.order-products-section', ['order-id' => $job->id]);

$__keyOuter = $__key ?? null;

$__key = 'order-products-section-'.$job->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1456817479-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>


    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('jobs.order-workflow-section', ['order-id' => $job->id]);

$__keyOuter = $__key ?? null;

$__key = 'order-workflow-section-'.$job->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1456817479-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('jobs.order-attachments-section', ['order-id' => $job->id]);

$__keyOuter = $__key ?? null;

$__key = 'order-attachments-section-'.$job->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1456817479-2', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('jobs.order-activity-section', ['order-id' => $job->id,'focus-comment' => $focusComment]);

$__keyOuter = $__key ?? null;

$__key = 'order-activity-section-'.$job->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1456817479-3', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/detail-overview.blade.php ENDPATH**/ ?>