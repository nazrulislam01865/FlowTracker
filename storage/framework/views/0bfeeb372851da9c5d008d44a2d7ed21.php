<?php if (isset($component)) { $__componentOriginal3cf0d19ba2447a6865ba0989776a831b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3cf0d19ba2447a6865ba0989776a831b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.rfq-frame','data' => ['brand' => $brand]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.rfq-frame'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['brand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brand)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('footer', null, []); ?> If you cannot quote this request, open the secure request and choose “Decline”. <?php $__env->endSlot(); ?>

    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#b86a00;margin-bottom:7px">Due-date reminder</div>
    <h1 style="margin:0 0 12px;font-size:23px;line-height:1.22;color:#152238;letter-spacing:-.3px">Quotation deadline reminder</h1>
    <p style="margin:0 0 16px;color:#44566f;font-size:13px">Hello <?php echo e($contact); ?>, this is a friendly reminder that your quotation for <strong style="color:#152238"><?php echo e($inquiry->inquiry_number); ?></strong> is due <strong style="color:#152238"><?php echo e($due?->format('M j, Y \a\t g:i A')); ?></strong>.</p>

    <?php if (isset($component)) { $__componentOriginal1745d046c1f1cedbb14586e22cc19674 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1745d046c1f1cedbb14586e22cc19674 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.rfq-detail','data' => ['title' => $inquiry->subject,'meta' => number_format((float) $items->sum('quantity'), 0).' total units · '.($items->count()).' '.($items->count() === 1 ? 'product' : 'products')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.rfq-detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->subject),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((float) $items->sum('quantity'), 0).' total units · '.($items->count()).' '.($items->count() === 1 ? 'product' : 'products'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1745d046c1f1cedbb14586e22cc19674)): ?>
<?php $attributes = $__attributesOriginal1745d046c1f1cedbb14586e22cc19674; ?>
<?php unset($__attributesOriginal1745d046c1f1cedbb14586e22cc19674); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1745d046c1f1cedbb14586e22cc19674)): ?>
<?php $component = $__componentOriginal1745d046c1f1cedbb14586e22cc19674; ?>
<?php unset($__componentOriginal1745d046c1f1cedbb14586e22cc19674); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal48f65b0061ed931e5fc143d540cf7f32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal48f65b0061ed931e5fc143d540cf7f32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.rfq-button','data' => ['href' => $publicUrl]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.rfq-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($publicUrl)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Continue quotation <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal48f65b0061ed931e5fc143d540cf7f32)): ?>
<?php $attributes = $__attributesOriginal48f65b0061ed931e5fc143d540cf7f32; ?>
<?php unset($__attributesOriginal48f65b0061ed931e5fc143d540cf7f32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal48f65b0061ed931e5fc143d540cf7f32)): ?>
<?php $component = $__componentOriginal48f65b0061ed931e5fc143d540cf7f32; ?>
<?php unset($__componentOriginal48f65b0061ed931e5fc143d540cf7f32); ?>
<?php endif; ?>
    <p style="margin:0;color:#718097;font-size:11px;line-height:1.55">If you have already submitted, no further action is required.<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($linkExpiresAt)): ?> This secure link remains valid until <?php echo e($linkExpiresAt->format('M j, Y \a\t g:i A')); ?>.<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3cf0d19ba2447a6865ba0989776a831b)): ?>
<?php $attributes = $__attributesOriginal3cf0d19ba2447a6865ba0989776a831b; ?>
<?php unset($__attributesOriginal3cf0d19ba2447a6865ba0989776a831b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3cf0d19ba2447a6865ba0989776a831b)): ?>
<?php $component = $__componentOriginal3cf0d19ba2447a6865ba0989776a831b; ?>
<?php unset($__componentOriginal3cf0d19ba2447a6865ba0989776a831b); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/emails/rfq/reminder.blade.php ENDPATH**/ ?>