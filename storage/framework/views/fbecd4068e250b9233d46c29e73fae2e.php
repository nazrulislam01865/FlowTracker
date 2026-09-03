<div>
<?php if (isset($component)) { $__componentOriginal8f6938ac62d0a39f318af1c1674a1814 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.page-head','data' => ['title' => 'Notifications','subtitle' => 'Assignments, changes and attention alerts delivered according to your role and record access']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Notifications','subtitle' => 'Assignments, changes and attention alerts delivered according to your role and record access']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('actions', null, []); ?> <button class="ghost" wire:click="markAllRead">Mark all read</button> <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $attributes = $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $component = $__componentOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>
<div class="card section-card"><div class="attention-list">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $url = app(\App\Services\NotificationService::class)->urlFor($n);
    ?>
    <div class="attention-item" style="<?php echo e($n->read_at?'opacity:.62':''); ?>">
        <span class="signal <?php echo e($n->type==='risk'?'red':($n->type==='assignment'?'purple':($n->type==='approval'?'amber':'purple'))); ?>"></span>
        <div><div class="item-title"><?php echo e($n->title); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$n->read_at): ?><span class="badge b-blue">New</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="item-meta"><?php echo e(app(\App\Services\MentionService::class)->displayText($n->message)); ?> · <?php echo e($n->created_at->diffForHumans()); ?></div></div>
        <a class="mini-btn" href="<?php echo e($url); ?>">Open</a>
    </div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div class="empty-state">No notifications.</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifications->total() > 30): ?>
    <div class="ft-list-pagination ft-notification-pagination">
        <span>Showing <b><?php echo e($notifications->firstItem() ?? 0); ?>–<?php echo e($notifications->lastItem() ?? 0); ?></b> of <?php echo e($notifications->total()); ?> notifications</span>
        <div class="ft-page-actions">
            <button type="button" wire:click="previousPage('notificationsPage')" <?php if($notifications->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
            <span>Page <?php echo e($notifications->currentPage()); ?> of <?php echo e($notifications->lastPage()); ?></span>
            <button type="button" wire:click="nextPage('notificationsPage')" <?php if(!$notifications->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/notifications/index.blade.php ENDPATH**/ ?>