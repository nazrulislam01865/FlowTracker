<?php if (isset($component)) { $__componentOriginal25a57b57fd3380b6c9462e30c241e3d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.table','data' => ['jobs' => $jobs,'searchFilter' => $search,'clientFilter' => $client,'phaseFilter' => $phase,'ownerFilter' => $owner,'metrics' => $metrics,'metricFilter' => $metricFilter,'dateFrom' => $dateFrom,'dateTo' => $dateTo,'importFilterId' => $importBatchId,'importFilterLabel' => $importBatchLabel,'dateRangeEnabled' => true,'clientFilterOptions' => $clientFilterOptions,'phaseFilterOptions' => $phaseFilterOptions,'ownerFilterOptions' => $ownerFilterOptions,'selectedOrderIds' => $selectedOrderIds,'showBulkDeleteConfirm' => $showBulkDeleteConfirm,'clearAction' => 'clearSearch','clearFiltersAction' => 'clearFilters','wire:key' => 'orders-list']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jobs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobs),'search-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'client-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client),'phase-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phase),'owner-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($owner),'metrics' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics),'metric-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter),'date-from' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateFrom),'date-to' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateTo),'import-filter-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($importBatchId),'import-filter-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($importBatchLabel),'date-range-enabled' => true,'client-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'phase-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilterOptions),'owner-filter-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerFilterOptions),'selected-order-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOrderIds),'show-bulk-delete-confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showBulkDeleteConfirm),'clear-action' => 'clearSearch','clear-filters-action' => 'clearFilters','wire:key' => 'orders-list']); ?>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/orders/index.blade.php ENDPATH**/ ?>