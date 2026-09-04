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

     <?php $__env->slot('footer', null, []); ?> Awarded by <?php echo e($awardedBy); ?> · <?php echo e($brand['name'] ?? 'Company'); ?> · <?php echo e(now()->format('M j, Y')); ?> <?php $__env->endSlot(); ?>

    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#11805d;margin-bottom:7px">Supplier award</div>
    <h1 style="margin:0 0 12px;font-size:23px;line-height:1.22;color:#152238;letter-spacing:-.3px">Your quotation has been selected</h1>
    <p style="margin:0 0 14px;color:#44566f;font-size:13px">Hello <?php echo e($contact); ?>,</p>
    <p style="margin:0 0 18px;color:#44566f;font-size:13px">Congratulations. <?php echo e($brand['name'] ?? 'Company'); ?> has selected <strong style="color:#152238"><?php echo e($supplier->name); ?></strong> for <strong style="color:#152238"><?php echo e($inquiry->inquiry_number); ?></strong>.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 18px;background:#eef9f5;border:1px solid #cbe8dc;border-radius:9px"><tr><td style="padding:15px 16px">
        <div style="font-size:13px;font-weight:700;color:#152238"><?php echo e($inquiry->subject); ?></div>
        <div style="margin-top:5px;font-size:11px;color:#5d766e"><?php echo e(number_format((float) $items->sum('quantity'), 0)); ?> units · Awarded total <?php echo e($quote?->currency ?? $inquiry->currency ?? 'USD'); ?> <?php echo e(number_format((float) ($quote?->submitted_total ?? 0), 2)); ?><?php echo e($quote?->lead_time_days ? ' · '.$quote->lead_time_days.'-day lead time' : ''); ?></div>
    </td></tr></table>

    <?php if (isset($component)) { $__componentOriginal48f65b0061ed931e5fc143d540cf7f32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal48f65b0061ed931e5fc143d540cf7f32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.rfq-button','data' => ['href' => $publicUrl ?? '#']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.rfq-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($publicUrl ?? '#')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Review award details <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal48f65b0061ed931e5fc143d540cf7f32)): ?>
<?php $attributes = $__attributesOriginal48f65b0061ed931e5fc143d540cf7f32; ?>
<?php unset($__attributesOriginal48f65b0061ed931e5fc143d540cf7f32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal48f65b0061ed931e5fc143d540cf7f32)): ?>
<?php $component = $__componentOriginal48f65b0061ed931e5fc143d540cf7f32; ?>
<?php unset($__componentOriginal48f65b0061ed931e5fc143d540cf7f32); ?>
<?php endif; ?>
    <p style="margin:0;color:#44566f;font-size:13px">Our team will contact you with the purchase order and next steps.</p>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/emails/rfq/award.blade.php ENDPATH**/ ?>