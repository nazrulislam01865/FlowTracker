<div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-activity-workspace-'.e($orderId).''; ?>wire:key="order-activity-workspace-<?php echo e($orderId); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'activity','method' => 'loadActivitySection','keyPrefix' => 'order-activity-isolated','contextType' => 'order','contextId' => $orderId,'queueGroup' => 'order-detail-'.e($orderId).'','queuePriority' => 40,'settleDelay' => 180,'rows' => 4,'message' => 'Loading activity when needed…','rootMargin' => '40px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'activity','method' => 'loadActivitySection','key-prefix' => 'order-activity-isolated','context-type' => 'order','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderId),'queue-group' => 'order-detail-'.e($orderId).'','queue-priority' => 40,'settle-delay' => 180,'rows' => 4,'message' => 'Loading activity when needed…','root-margin' => '40px 0px']); ?>
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
        <?php if (isset($component)) { $__componentOriginale9bd4c7bc89f1675cde7d2af9804ef4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9bd4c7bc89f1675cde7d2af9804ef4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.activity','data' => ['job' => $job,'mentionUsers' => $mentionUsers,'activityTab' => $jobActivityTab,'activityPage' => $jobActivityPage,'focusComment' => $focusComment,'canComment' => $canComment]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.activity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobActivityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobActivityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'can-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canComment)]); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/jobs/order-activity-section.blade.php ENDPATH**/ ?>