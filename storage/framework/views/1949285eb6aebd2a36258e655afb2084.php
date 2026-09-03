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

     <?php $__env->slot('footer', null, []); ?> No pricing or selected-supplier details are disclosed in this message. <?php $__env->endSlot(); ?>

    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#61738d;margin-bottom:7px">Quotation update</div>
    <h1 style="margin:0 0 12px;font-size:23px;line-height:1.22;color:#152238;letter-spacing:-.3px">Thank you for your quotation</h1>
    <p style="margin:0 0 14px;color:#44566f;font-size:13px">Hello <?php echo e($contact); ?>,</p>
    <p style="margin:0 0 16px;color:#44566f;font-size:13px">Thank you for the time you invested in <strong style="color:#152238"><?php echo e($inquiry->inquiry_number); ?></strong>. <?php echo e($brand['name'] ?? 'Company'); ?> has selected another quotation for this inquiry.</p>
    <?php if (isset($component)) { $__componentOriginal1745d046c1f1cedbb14586e22cc19674 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1745d046c1f1cedbb14586e22cc19674 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.rfq-detail','data' => ['title' => $inquiry->subject,'meta' => number_format((float) $items->sum('quantity'), 0).' total units']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.rfq-detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->subject),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((float) $items->sum('quantity'), 0).' total units')]); ?>
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
    <p style="margin:18px 0 0;color:#44566f;font-size:13px">We appreciate your support and look forward to inviting <?php echo e($supplier->name); ?> to future opportunities.</p>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/emails/rfq/not-selected.blade.php ENDPATH**/ ?>