<?php if (isset($component)) { $__componentOriginal25a57b57fd3380b6c9462e30c241e3d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25a57b57fd3380b6c9462e30c241e3d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.table','data' => ['jobs' => $jobs,'searchFilter' => $search,'clearAction' => 'clearSearch','wire:key' => 'orders-list']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jobs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobs),'search-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'clear-action' => 'clearSearch','wire:key' => 'orders-list']); ?>
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