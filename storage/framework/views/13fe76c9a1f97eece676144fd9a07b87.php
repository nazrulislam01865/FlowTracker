<?php $__env->startSection('content'); ?>
<style>
    /* Only page-specific layout and the existing native review dialog. Shared
       typography, surfaces, colors, badges, table and buttons come from x-ui. */
    .ft-price-approvals{min-width:0;width:100%;color:var(--ft-text-primary)}
    .ft-price-approvals .ft-price-panel{background:var(--ft-bg-surface);border:1px solid var(--ft-border-default);border-radius:var(--ft-radius-xl);overflow:hidden;box-shadow:var(--ft-shadow-xs)}
    .ft-price-approvals .ft-price-panel-heading{padding:20px 20px 16px;border-bottom:1px solid var(--ft-border-subtle)}
    .ft-price-approvals .ft-price-panel-heading .ft-section-header{margin:0}
    .ft-price-approvals .ft-price-panel-heading .ft-section-header__description{margin:5px 0 0}
    .ft-price-approvals .ft-price-panel .ft-table-shell{border:0;border-radius:0;box-shadow:none}
    .ft-price-approvals .ft-price-panel .ft-table{min-width:780px}
    .ft-price-approvals .ft-price-panel .ft-table th,.ft-price-approvals .ft-price-panel .ft-table td{padding:14px 16px}
    .ft-price-approvals .ft-price-panel .ft-table th:last-child,.ft-price-approvals .ft-price-panel .ft-table td:last-child{text-align:right}
    .ft-price-approvals .ft-product-name{font-weight:700;color:var(--ft-text-primary)}
    .ft-price-approvals .ft-subtext{font-size:var(--ft-font-size-xs);color:var(--ft-text-secondary);margin-top:4px}
    .ft-price-approvals .ft-price-tiers{line-height:1.7;font-variant-numeric:tabular-nums}
    .ft-price-approvals .ft-price-date{white-space:nowrap}
    .ft-price-approvals .ft-price-empty{padding:20px 16px 26px}
    .ft-price-approvals .ft-price-empty .ft-empty-state__icon{width:48px;height:48px}
    .ft-price-approvals .ft-page-links{padding:0 16px}
    .ft-price-approvals .ft-page-links:empty{display:none}
    .ft-price-approvals .ft-review-notice{padding:13px 15px;border:1px solid var(--ft-border-success);background:var(--ft-bg-success);color:var(--ft-text-success);margin-bottom:16px;border-radius:var(--ft-radius-md)}
    .ft-price-approvals .ft-review-notice.warning{border-color:var(--ft-border-warning);background:var(--ft-bg-warning);color:var(--ft-text-warning)}
    .ft-price-approvals dialog.ft-review-modal{border:0;border-radius:var(--ft-radius-xl);padding:0;width:min(690px,calc(100vw - 32px));max-height:calc(100dvh - 48px);overflow-y:auto;color:var(--ft-text-primary);background:var(--ft-bg-surface);box-shadow:0 28px 75px rgba(10,28,50,.23);margin:auto}
    .ft-price-approvals dialog.ft-review-modal::backdrop{background:var(--ft-overlay-backdrop)}
    .ft-price-approvals .ft-modal-header{padding:22px 25px 17px;border-bottom:1px solid var(--ft-border-default);display:flex;justify-content:space-between;gap:14px;align-items:flex-start}
    .ft-price-approvals .ft-modal-header h3{font-size:var(--ft-font-size-xl);font-weight:700;margin:0 0 5px}
    .ft-price-approvals .ft-modal-close{border:1px solid var(--ft-border-default);background:var(--ft-bg-surface);color:var(--ft-text-primary);border-radius:var(--ft-radius-md);padding:6px 11px;font-size:20px;line-height:1;cursor:pointer}
    .ft-price-approvals .ft-modal-body{padding:20px 25px}
    .ft-price-approvals .ft-review-table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
    .ft-price-approvals .ft-review-table th,.ft-price-approvals .ft-review-table td{padding:12px;border-bottom:1px solid var(--ft-border-subtle);text-align:right}
    .ft-price-approvals .ft-review-table th:first-child,.ft-price-approvals .ft-review-table td:first-child{text-align:left}
    .ft-price-approvals .ft-review-table .ft-changed{color:var(--ft-text-success);font-weight:700;background:var(--ft-bg-success)}
    .ft-price-approvals .ft-removed{color:var(--ft-text-danger)}
    .ft-price-approvals .ft-review-label{font-size:var(--ft-font-size-sm);color:var(--ft-text-secondary);font-weight:700;margin:0 0 10px}
    .ft-price-approvals .ft-note-label{display:block;font-size:var(--ft-font-size-sm);font-weight:700;margin:20px 0 7px}
    .ft-price-approvals .ft-review-note{width:100%;padding:11px;border:1px solid var(--ft-border-strong);border-radius:var(--ft-radius-md);font:inherit;font-size:var(--ft-font-size-sm)}
    .ft-price-approvals .ft-modal-footer{display:flex;justify-content:flex-end;flex-wrap:wrap;gap:10px;padding:15px 25px 23px}
    @media(max-width:650px){.ft-price-approvals .ft-price-panel-heading{padding:16px}.ft-price-approvals .ft-modal-body,.ft-price-approvals .ft-modal-header{padding:16px}.ft-price-approvals .ft-modal-footer{padding:14px 16px}}
</style>
<div class="ft-price-approvals">
    <?php if (isset($component)) { $__componentOriginal91a231a9270579fa1ae9246bd51fb785 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91a231a9270579fa1ae9246bd51fb785 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.page-header','data' => ['title' => 'Supplier Price Approvals','subtitle' => 'Review supplier-requested price changes. Approved prices apply only to the selected supplier; customer selling prices stay the same.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Supplier Price Approvals','subtitle' => 'Review supplier-requested price changes. Approved prices apply only to the selected supplier; customer selling prices stay the same.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91a231a9270579fa1ae9246bd51fb785)): ?>
<?php $attributes = $__attributesOriginal91a231a9270579fa1ae9246bd51fb785; ?>
<?php unset($__attributesOriginal91a231a9270579fa1ae9246bd51fb785); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91a231a9270579fa1ae9246bd51fb785)): ?>
<?php $component = $__componentOriginal91a231a9270579fa1ae9246bd51fb785; ?>
<?php unset($__componentOriginal91a231a9270579fa1ae9246bd51fb785); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div role="status" class="ft-review-notice"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('warning')): ?><div role="status" class="ft-review-notice warning"><?php echo e(session('warning')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-price-panel" aria-labelledby="ft-price-approvals-heading">
        <div class="ft-price-panel-heading">
            <?php if (isset($component)) { $__componentOriginal28f065c8f3879b39bd2c46a05497f570 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal28f065c8f3879b39bd2c46a05497f570 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.section-header','data' => ['id' => 'ft-price-approvals-heading','title' => 'Requests awaiting approval','description' => 'Review the proposed quantity tiers before accepting or rejecting a request.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.section-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'ft-price-approvals-heading','title' => 'Requests awaiting approval','description' => 'Review the proposed quantity tiers before accepting or rejecting a request.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('actions', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['label' => number_format($proposals->total()).' pending','variant' => $proposals->total() ? 'warning' : 'neutral','dot' => $proposals->total() > 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($proposals->total()).' pending'),'variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($proposals->total() ? 'warning' : 'neutral'),'dot' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($proposals->total() > 0)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal28f065c8f3879b39bd2c46a05497f570)): ?>
<?php $attributes = $__attributesOriginal28f065c8f3879b39bd2c46a05497f570; ?>
<?php unset($__attributesOriginal28f065c8f3879b39bd2c46a05497f570); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal28f065c8f3879b39bd2c46a05497f570)): ?>
<?php $component = $__componentOriginal28f065c8f3879b39bd2c46a05497f570; ?>
<?php unset($__componentOriginal28f065c8f3879b39bd2c46a05497f570); ?>
<?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proposals->isEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginal3607a477fdef7402bc742abad5df9c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3607a477fdef7402bc742abad5df9c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.empty-state','data' => ['class' => 'ft-price-empty','title' => 'All caught up','description' => 'No supplier price changes are awaiting approval. New requests will appear here when a supplier submits them.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-price-empty','title' => 'All caught up','description' => 'No supplier price changes are awaiting approval. New requests will appear here when a supplier submits them.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('icon', null, []); ?> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg> <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3607a477fdef7402bc742abad5df9c51)): ?>
<?php $attributes = $__attributesOriginal3607a477fdef7402bc742abad5df9c51; ?>
<?php unset($__attributesOriginal3607a477fdef7402bc742abad5df9c51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3607a477fdef7402bc742abad5df9c51)): ?>
<?php $component = $__componentOriginal3607a477fdef7402bc742abad5df9c51; ?>
<?php unset($__componentOriginal3607a477fdef7402bc742abad5df9c51); ?>
<?php endif; ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal793d2b22631f88b8a3d00569a12acf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal793d2b22631f88b8a3d00569a12acf88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.table','data' => ['caption' => 'Supplier price requests waiting for approval']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['caption' => 'Supplier price requests waiting for approval']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('head', null, []); ?> <tr><th scope="col">Product</th><th scope="col">Supplier</th><th scope="col">Proposed tiers</th><th scope="col">Requested</th><th scope="col">Status</th><th scope="col">Action</th></tr> <?php $__env->endSlot(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $proposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proposal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><div class="ft-product-name"><?php echo e($proposal->product_name); ?></div><div class="ft-subtext"><?php echo e($proposal->product_display_code); ?></div></td>
                        <td><?php echo e($proposal->supplier_name); ?></td>
                        <td class="ft-price-tiers">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_slice($proposal->proposed_rows, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div><?php echo e(number_format((int) $tier['quantity'])); ?> units → <?php echo e(rtrim(rtrim(number_format((float) $tier['price'], 6, '.', ','), '0'), '.')); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proposal->new_tier_count > 3): ?><div class="ft-subtext">+<?php echo e($proposal->new_tier_count - 3); ?> more in details</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="ft-price-date"><?php echo e(\Illuminate\Support\Carbon::parse($proposal->created_at)->format('d M Y, h:i A')); ?></td>
                        <td><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['label' => 'Pending','variant' => 'warning','dot' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Pending','variant' => 'warning','dot' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                        <td><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','dataOpenPriceReview' => ''.e($proposal->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','data-open-price-review' => ''.e($proposal->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
View changes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal793d2b22631f88b8a3d00569a12acf88)): ?>
<?php $attributes = $__attributesOriginal793d2b22631f88b8a3d00569a12acf88; ?>
<?php unset($__attributesOriginal793d2b22631f88b8a3d00569a12acf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal793d2b22631f88b8a3d00569a12acf88)): ?>
<?php $component = $__componentOriginal793d2b22631f88b8a3d00569a12acf88; ?>
<?php unset($__componentOriginal793d2b22631f88b8a3d00569a12acf88); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proposals->hasPages()): ?><div class="ft-page-links"><?php echo e($proposals->links()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $proposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proposal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <dialog id="ft-price-review-<?php echo e($proposal->id); ?>" class="ft-review-modal" aria-labelledby="ft-price-review-title-<?php echo e($proposal->id); ?>">
            <div class="ft-modal-header">
                <div>
                    <h3 id="ft-price-review-title-<?php echo e($proposal->id); ?>">Review supplier price change</h3>
                    <div class="ft-product-name"><?php echo e($proposal->product_name); ?></div>
                    <div class="ft-subtext"><?php echo e($proposal->product_display_code); ?> · <?php echo e($proposal->supplier_name); ?></div>
                </div>
                <button type="button" class="ft-modal-close" data-close-price-review aria-label="Close price review">&times;</button>
            </div>
            <form method="post" action="<?php echo e(route('supplier-prices.review', $proposal->id)); ?>">
                <?php echo csrf_field(); ?>
                <div class="ft-modal-body">
                    <p class="ft-review-label">Compare the current approved supplier unit prices with the requested prices at each quantity.</p>
                    <div style="overflow-x:auto">
                        <table class="ft-review-table">
                            <thead><tr><th>Quantity</th><th>Current unit price</th><th>Requested unit price</th></tr></thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $proposal->changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr>
                                        <td><?php echo e(number_format($change['quantity'])); ?></td>
                                        <td><?php echo e($change['old_price'] === null ? '—' : rtrim(rtrim(number_format((float) $change['old_price'], 6, '.', ','), '0'), '.')); ?></td>
                                        <td class="<?php echo e($change['changed'] ? 'ft-changed' : ''); ?> <?php echo e($change['new_price'] === null ? 'ft-removed' : ''); ?>">
                                            <?php echo e($change['new_price'] === null ? 'Removed' : rtrim(rtrim(number_format((float) $change['new_price'], 6, '.', ','), '0'), '.')); ?>

                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <label class="ft-note-label" for="ft-review-note-<?php echo e($proposal->id); ?>">Decision note (optional)</label>
                    <textarea id="ft-review-note-<?php echo e($proposal->id); ?>" class="ft-review-note" name="review_note" rows="2" maxlength="500" placeholder="Reason or instructions for the supplier"></textarea>
                </div>
                <div class="ft-modal-footer">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','type' => 'button','dataClosePriceReview' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','type' => 'button','data-close-price-review' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'danger','type' => 'submit','name' => 'decision','value' => 'rejected']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','type' => 'submit','name' => 'decision','value' => 'rejected']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Reject <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','name' => 'decision','value' => 'approved']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','name' => 'decision','value' => 'approved']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Approve changes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </div>
            </form>
        </dialog>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<script nonce="<?php echo e(request()->attributes->get('csp_nonce')); ?>">
document.addEventListener('click', function (event) {
    var open = event.target.closest('[data-open-price-review]');
    if (open) {
        var dialog = document.getElementById('ft-price-review-' + open.dataset.openPriceReview);
        if (dialog && !dialog.open) dialog.showModal();
        return;
    }
    var close = event.target.closest('[data-close-price-review]');
    if (close) close.closest('dialog')?.close();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/pages/supplier-price-approvals.blade.php ENDPATH**/ ?>