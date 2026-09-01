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

     <?php $__env->slot('footer', null, []); ?> Quotation request <?php echo e($inquiry->inquiry_number); ?> · Sent automatically from <?php echo e($brand['name'] ?? 'Company'); ?> <?php $__env->endSlot(); ?>

    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#007d70;margin-bottom:7px">Request for quotation</div>
    <h1 style="margin:0 0 12px;font-size:23px;line-height:1.22;color:#152238;letter-spacing:-.3px">You're invited to submit a quotation</h1>
    <p style="margin:0 0 14px;color:#44566f;font-size:13px">Hello <?php echo e($contact); ?>,</p>
    <p style="margin:0 0 20px;color:#44566f;font-size:13px"><?php echo e($brand['name'] ?? 'Company'); ?> is requesting a quotation from <strong style="color:#152238"><?php echo e($supplier->name); ?></strong> for <?php echo e($items->count() === 1 ? 'the product below' : 'the products below'); ?>.</p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($requestMessage ?? null)): ?>
        <div style="margin:0 0 14px;padding:12px 14px;border-left:3px solid #0b8f80;background:#f1faf8;border-radius:7px;color:#385d58;font-size:12px;line-height:1.55">
            <div style="margin-bottom:4px;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#08776c">Special note from buyer</div>
            <?php echo nl2br(e($requestMessage)); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($supplierDetails ?? null)): ?>
        <div style="margin:0 0 18px;padding:12px 14px;background:#f7f9fc;border:1px solid #e1e7ef;border-radius:7px;color:#44566f;font-size:12px;line-height:1.55">
            <div style="margin-bottom:4px;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#718097">Inquiry &amp; product details</div>
            <?php echo nl2br(e($supplierDetails)); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 16px;background:#f8fafc;border:1px solid #e1e7ef;border-radius:9px"><tr>
        <td style="padding:12px 14px"><div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Inquiry</div><div style="margin-top:3px;font-size:13px;font-weight:700;color:#152238"><?php echo e($inquiry->inquiry_number); ?></div></td>
        <td style="padding:12px 14px;border-left:1px solid #e1e7ef"><div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Quotation due</div><div style="margin-top:3px;font-size:13px;font-weight:700;color:#152238"><?php echo e($due?->format('M j, Y') ?? 'No due date'); ?></div></td>
    </tr></table>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal1745d046c1f1cedbb14586e22cc19674 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1745d046c1f1cedbb14586e22cc19674 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.rfq-detail','data' => ['title' => $item->item_name,'meta' => number_format((float) $item->quantity, 0).' '.($item->unit ?: 'units').' · '.($item->category ?: 'Product')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.rfq-detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->item_name),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((float) $item->quantity, 0).' '.($item->unit ?: 'units').' · '.($item->category ?: 'Product'))]); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

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
View request &amp; submit quotation <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal48f65b0061ed931e5fc143d540cf7f32)): ?>
<?php $attributes = $__attributesOriginal48f65b0061ed931e5fc143d540cf7f32; ?>
<?php unset($__attributesOriginal48f65b0061ed931e5fc143d540cf7f32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal48f65b0061ed931e5fc143d540cf7f32)): ?>
<?php $component = $__componentOriginal48f65b0061ed931e5fc143d540cf7f32; ?>
<?php unset($__componentOriginal48f65b0061ed931e5fc143d540cf7f32); ?>
<?php endif; ?>
    <p style="margin:0;color:#718097;font-size:11px;line-height:1.55">
        This secure link is unique to your company. No account is required.
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($linkExpiresAt)): ?> It remains valid until <strong style="color:#52647c"><?php echo e($linkExpiresAt->format('M j, Y 	 g:i A')); ?></strong>. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </p>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/emails/rfq/invitation.blade.php ENDPATH**/ ?>