<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreate): ?>
        <?php if (isset($component)) { $__componentOriginal7c9b0b4b1633ac324acaf1481e466cc1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c9b0b4b1633ac324acaf1481e466cc1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create','data' => ['clientFilterOptions' => $clientFilterOptions,'ownerFilterOptions' => $ownerFilterOptions,'workflowFilterOptions' => $workflowFilterOptions,'categoryFilterOptions' => $categoryFilterOptions,'clients' => $clients,'workflows' => $workflows,'categories' => $categories,'priorities' => $priorities,'mentionUsers' => $mentionUsers,'clientId' => $clientId,'workflowId' => $workflowId,'ownerId' => $ownerId,'jobItems' => $jobItems,'jobAttachments' => $jobAttachments,'catalogReady' => $createCatalogReady,'assignmentReady' => $createAssignmentReady,'workflowReady' => $createWorkflowReady,'wire:key' => 'job-create']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'owner-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerFilterOptions),'workflow-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowFilterOptions),'category-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryFilterOptions),'clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clients),'workflows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflows),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'client-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'workflow-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowId),'owner-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerId),'job-items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobItems),'job-attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobAttachments),'catalog-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createCatalogReady),'assignment-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createAssignmentReady),'workflow-ready' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createWorkflowReady),'wire:key' => 'job-create']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c9b0b4b1633ac324acaf1481e466cc1)): ?>
<?php $attributes = $__attributesOriginal7c9b0b4b1633ac324acaf1481e466cc1; ?>
<?php unset($__attributesOriginal7c9b0b4b1633ac324acaf1481e466cc1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c9b0b4b1633ac324acaf1481e466cc1)): ?>
<?php $component = $__componentOriginal7c9b0b4b1633ac324acaf1481e466cc1; ?>
<?php unset($__componentOriginal7c9b0b4b1633ac324acaf1481e466cc1); ?>
<?php endif; ?>
    <?php elseif($selectedTask): ?>
        <?php if (isset($component)) { $__componentOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.task-detail','data' => ['task' => $selectedTask,'mentionUsers' => $mentionUsers,'taskProgress' => $taskProgress,'taskStatuses' => $taskStatuses,'priorities' => $priorities,'taskFlags' => $taskFlags,'availableDocuments' => $availableDocuments,'activityTab' => $taskActivityTab,'activityPage' => $taskActivityPage,'focusComment' => $focusComment,'taskDocumentUploads' => $taskDocumentUploads,'showTaskDocumentPicker' => $showTaskDocumentPicker,'editMode' => $taskEditMode,'wire:key' => 'task-detail-'.e($selectedTask->id).'-'.e($taskEditMode ? 'edit' : 'view').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.task-detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedTask),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'task-progress' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskProgress),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'task-flags' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskFlags),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableDocuments),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskActivityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskActivityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'task-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskDocumentUploads),'show-task-document-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showTaskDocumentPicker),'edit-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskEditMode),'wire:key' => 'task-detail-'.e($selectedTask->id).'-'.e($taskEditMode ? 'edit' : 'view').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63)): ?>
<?php $attributes = $__attributesOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63; ?>
<?php unset($__attributesOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63)): ?>
<?php $component = $__componentOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63; ?>
<?php unset($__componentOriginal468fc3d5ca1e9bc99da5a8a8a5d79f63); ?>
<?php endif; ?>
    <?php elseif($selectedJob): ?>
        <?php if (isset($component)) { $__componentOriginal91e787de8877d89678d42f881ad44e86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91e787de8877d89678d42f881ad44e86 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail','data' => ['job' => $selectedJob,'detailTab' => $detailTab,'expandedPhaseIds' => $expandedPhaseIds,'taskStatuses' => $taskStatuses,'users' => $users,'mentionUsers' => $mentionUsers,'priorities' => $priorities,'products' => $products,'categories' => $categories,'availableDocuments' => $availableDocuments,'healthOptions' => $healthOptions,'jobTaskSearch' => $jobTaskSearch,'activityTab' => $jobActivityTab,'activityPage' => $jobActivityPage,'focusComment' => $focusComment,'jobDocumentUploads' => $jobDocumentUploads,'showDocumentPicker' => $showDocumentPicker,'wire:key' => 'job-detail-'.e($selectedJob->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedJob),'detail-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailTab),'expanded-phase-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedPhaseIds),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableDocuments),'health-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($healthOptions),'job-task-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobTaskSearch),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobActivityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobActivityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads),'show-document-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDocumentPicker),'wire:key' => 'job-detail-'.e($selectedJob->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91e787de8877d89678d42f881ad44e86)): ?>
<?php $attributes = $__attributesOriginal91e787de8877d89678d42f881ad44e86; ?>
<?php unset($__attributesOriginal91e787de8877d89678d42f881ad44e86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91e787de8877d89678d42f881ad44e86)): ?>
<?php $component = $__componentOriginal91e787de8877d89678d42f881ad44e86; ?>
<?php unset($__componentOriginal91e787de8877d89678d42f881ad44e86); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal25a57b57fd3380b6c9462e30c241e3d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.table','data' => ['jobs' => $jobs,'searchFilter' => $search,'wire:key' => 'orders-list']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jobs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobs),'search-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'wire:key' => 'orders-list']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7)): ?>
<?php $attributes = $__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7; ?>
<?php unset($__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25a57b57fd3380b6c9462e30c241e3d7)): ?>
<?php $component = $__componentOriginal25a57b57fd3380b6c9462e30c241e3d7; ?>
<?php unset($__componentOriginal25a57b57fd3380b6c9462e30c241e3d7); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/jobs/index.blade.php ENDPATH**/ ?>