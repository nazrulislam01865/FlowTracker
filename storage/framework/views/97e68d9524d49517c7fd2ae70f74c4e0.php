<?php if (isset($component)) { $__componentOriginalda5f06f4db1f587dcaec2bd4c168b718 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda5f06f4db1f587dcaec2bd4c168b718 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.email.order-frame','data' => ['brand' => $brand,'label' => 'Order workflow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('email.order-frame'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['brand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brand),'label' => 'Order workflow']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#007d70;margin-bottom:7px"><?php echo e($handoffType === 'purchase_order' ? 'Purchase Order handoff' : 'Artwork handoff'); ?></div>
    <h1 style="margin:0 0 12px;font-size:23px;line-height:1.22;color:#152238;letter-spacing:-.3px"><?php echo e($handoffType === 'purchase_order' ? 'Purchase Order ready for Artwork' : 'Artwork ready for Order Team'); ?></h1>
    <p style="margin:0 0 14px;color:#44566f;font-size:13px">Hello <?php echo e($team); ?>,</p>
    <p style="margin:0 0 20px;color:#44566f;font-size:13px">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($handoffType === 'purchase_order'): ?>
            The Purchase Order for <strong style="color:#152238"><?php echo e($orderNumber); ?></strong> has been uploaded and is attached for the Artwork Team to continue the order workflow.
        <?php else: ?>
            The latest confirmed artwork for <strong style="color:#152238"><?php echo e($orderNumber); ?></strong> is attached for the Order Team to continue with the client ERP / approval step.
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 16px;background:#f8fafc;border:1px solid #e1e7ef;border-radius:9px">
        <tr>
            <td style="padding:12px 14px"><div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Order</div><div style="margin-top:3px;font-size:13px;font-weight:700;color:#152238"><?php echo e($orderNumber); ?></div></td>
            <td style="padding:12px 14px;border-left:1px solid #e1e7ef"><div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Client</div><div style="margin-top:3px;font-size:13px;font-weight:700;color:#152238"><?php echo e($job->client?->name ?: 'Client'); ?></div></td>
        </tr>
        <tr>
            <td style="padding:12px 14px;border-top:1px solid #e1e7ef"><div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Products</div><div style="margin-top:3px;font-size:13px;font-weight:700;color:#152238"><?php echo e($productSummary); ?></div></td>
            <td style="padding:12px 14px;border-left:1px solid #e1e7ef;border-top:1px solid #e1e7ef"><div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Sent by</div><div style="margin-top:3px;font-size:13px;font-weight:700;color:#152238"><?php echo e($sentBy->name); ?></div></td>
        </tr>
    </table>

    <div style="padding:14px 16px;border:1px solid #dce5ee;border-radius:9px;background:#fff">
        <div style="font-size:10px;color:#718097;text-transform:uppercase;letter-spacing:.05em">Attached file<?php echo e($documents->count() === 1 ? '' : 's'); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachedDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div style="margin-top:4px;font-size:13px;font-weight:700;color:#152238"><?php echo e($attachedDocument->name); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) ($document->version ?? 0) > 0): ?><div style="margin-top:2px;font-size:11px;color:#718097">Artwork version <?php echo e($document->version); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <p style="margin:20px 0 0;color:#718097;font-size:11px;line-height:1.55">Reply to this email if you need clarification from <?php echo e($sentBy->name); ?>.</p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda5f06f4db1f587dcaec2bd4c168b718)): ?>
<?php $attributes = $__attributesOriginalda5f06f4db1f587dcaec2bd4c168b718; ?>
<?php unset($__attributesOriginalda5f06f4db1f587dcaec2bd4c168b718); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda5f06f4db1f587dcaec2bd4c168b718)): ?>
<?php $component = $__componentOriginalda5f06f4db1f587dcaec2bd4c168b718; ?>
<?php unset($__componentOriginalda5f06f4db1f587dcaec2bd4c168b718); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/emails/orders/workflow-handoff.blade.php ENDPATH**/ ?>