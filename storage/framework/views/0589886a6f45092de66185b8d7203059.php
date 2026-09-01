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

     <?php $__env->slot('footer', null, []); ?> Submission reference Q-<?php echo e(str_pad((string) ($quote?->id ?? 0), 6, '0', STR_PAD_LEFT)); ?> <?php $__env->endSlot(); ?>

    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#11805d;margin-bottom:7px">Quotation received</div>
    <h1 style="margin:0 0 12px;font-size:23px;line-height:1.22;color:#152238;letter-spacing:-.3px">Thank you — we received your quotation</h1>
    <p style="margin:0 0 18px;color:#44566f;font-size:13px">Hello <?php echo e($contact); ?>. Your quotation for <strong style="color:#152238"><?php echo e($inquiry->inquiry_number); ?></strong> was received<?php echo e($quote?->updated_at ? ' on '.$quote->updated_at->format('M j, Y \a\t g:i A') : ''); ?>.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 18px;background:#eef9f5;border:1px solid #cbe8dc;border-radius:9px"><tr><td style="padding:15px 16px">
        <div style="font-size:10px;color:#4d7468;text-transform:uppercase;letter-spacing:.05em">Submitted total</div>
        <div style="margin-top:3px;font-size:20px;font-weight:800;color:#11654c"><?php echo e($quote?->currency ?? $inquiry->currency ?? 'USD'); ?> <?php echo e(number_format((float) ($quote?->submitted_total ?? 0), 2)); ?></div>
        <div style="margin-top:5px;font-size:11px;color:#5d766e"><?php echo e(number_format((float) $items->sum('quantity'), 0)); ?> units<?php echo e($quote?->lead_time_days ? ' · '.$quote->lead_time_days.'-day lead time' : ''); ?> · Freight <?php echo e($quote?->currency ?? $inquiry->currency ?? 'USD'); ?> <?php echo e(number_format((float) ($quote?->freight ?? 0), 2)); ?></div>
    </td></tr></table>

    <p style="margin:0;color:#44566f;font-size:13px"><?php echo e($brand['name'] ?? 'Company'); ?> will contact you when a decision is made. Please keep this message for your records.</p>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/emails/rfq/quote-received.blade.php ENDPATH**/ ?>