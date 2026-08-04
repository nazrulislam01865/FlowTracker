<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreate): ?>
        <?php if (isset($component)) { $__componentOriginal7c9b0b4b1633ac324acaf1481e466cc1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c9b0b4b1633ac324acaf1481e466cc1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create','data' => ['clients' => $clients,'workflows' => $workflows,'users' => $users,'categories' => $categories,'products' => $products,'priorities' => $priorities,'clientId' => $clientId,'workflowId' => $workflowId,'jobItems' => $jobItems,'jobAttachments' => $jobAttachments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clients),'workflows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflows),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'client-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'workflow-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowId),'job-items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobItems),'job-attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobAttachments)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.task-detail','data' => ['task' => $selectedTask,'users' => $users,'taskProgress' => $taskProgress,'taskStatuses' => $taskStatuses,'priorities' => $priorities,'availableDocuments' => $availableDocuments,'activityTab' => $taskActivityTab,'activityPage' => $taskActivityPage,'taskDocumentUploads' => $taskDocumentUploads,'showTaskDocumentPicker' => $showTaskDocumentPicker]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.task-detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedTask),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'task-progress' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskProgress),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableDocuments),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskActivityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskActivityPage),'task-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskDocumentUploads),'show-task-document-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showTaskDocumentPicker)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail','data' => ['job' => $selectedJob,'detailTab' => $detailTab,'expandedPhaseIds' => $expandedPhaseIds,'taskStatuses' => $taskStatuses,'users' => $users,'priorities' => $priorities,'products' => $products,'categories' => $categories,'availableDocuments' => $availableDocuments,'healthOptions' => $healthOptions,'jobTaskSearch' => $jobTaskSearch,'activityTab' => $jobActivityTab,'activityPage' => $jobActivityPage,'jobDocumentUploads' => $jobDocumentUploads,'showDocumentPicker' => $showDocumentPicker]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedJob),'detail-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailTab),'expanded-phase-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedPhaseIds),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableDocuments),'health-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($healthOptions),'job-task-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobTaskSearch),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobActivityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobActivityPage),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads),'show-document-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDocumentPicker)]); ?>
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
        <span class="ft-jobs-list-poll" wire:poll.20s.visible aria-hidden="true"></span>
        <?php if (isset($component)) { $__componentOriginal25a57b57fd3380b6c9462e30c241e3d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.table','data' => ['jobs' => $jobs,'jobSummary' => $jobSummary,'clients' => $clients,'phases' => $phases,'users' => $users,'priorities' => $priorities,'healthOptions' => $healthOptions,'jobStatuses' => $jobStatuses,'phaseFilter' => $phase,'healthFilter' => $health,'quickFilter' => $quickFilter,'showMoreFilters' => $showMoreFilters,'selectedJobIds' => $selectedJobIds]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jobs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobs),'job-summary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobSummary),'clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clients),'phases' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phases),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'health-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($healthOptions),'job-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobStatuses),'phase-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phase),'health-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($health),'quick-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($quickFilter),'show-more-filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showMoreFilters),'selected-job-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedJobIds)]); ?>
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