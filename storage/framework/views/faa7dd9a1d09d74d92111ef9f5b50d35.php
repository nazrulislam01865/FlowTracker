<div class="ft-po-page ft-order-prototype-detail ft-order-list-v5">
    <header class="ft-po-page-header">
        <div>
            <div class="breadcrumbs">Order / Purchase Orders</div>
            <h1>Purchase Orders</h1>
            <p>Generate, review and email supplier purchase orders for existing orders.</p>
        </div>
        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','size' => 'sm','href' => route('jobs.index'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 'sm','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('jobs.index')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Back to Orders
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="ft-order-list-flash" role="status"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <p class="validation-error" role="alert"><?php echo e(session('error')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedOrder): ?>
        <?php
            $products = (array) data_get($orderWorkflowActionModalPreview, 'supplier_po.products', []);
            $poRows = (array) data_get($orderWorkflowActionModalPreview, 'supplier_po.rows', []);
            $units = array_sum(array_map(static fn ($product) => (int) ($product['quantity'] ?? 0), $products));
            $reference = $selectedOrder->order_number && $selectedOrder->order_number !== $selectedOrder->job_number ? $selectedOrder->order_number : null;
        ?>
        <section class="ft-po-editor" aria-label="Purchase order generator">
            <header class="ft-po-editor-header">
                <div class="ft-po-editor-identity">
                    <span class="ft-po-eyebrow">ORDER PURCHASE ORDER</span>
                    <h2><?php echo e($selectedOrder->displayOrderNumber()); ?></h2>
                    <p><?php echo e($selectedOrder->title); ?></p>
                </div>
                <div class="ft-po-editor-header-actions">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','size' => 'sm','href' => route('jobs.index', ['open' => $selectedOrder->id]),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 'sm','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('jobs.index', ['open' => $selectedOrder->id])),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M13 3h8v8M21 3l-10 10"/></svg>
                        Open order details
                     <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','size' => 'sm','wire:click' => 'closeOrderWorkflowAction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 'sm','wire:click' => 'closeOrderWorkflowAction']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        Back to PO list
                     <?php echo $__env->renderComponent(); ?>
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
            </header>

            <div class="ft-po-order-summary" aria-label="Selected order details">
                <div class="ft-po-summary-cell ft-po-summary-client">
                    <span class="ft-po-summary-label">CLIENT</span>
                    <div class="ft-po-summary-client-row"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $selectedOrder->client,'size' => 34]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOrder->client),'size' => 34]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?><strong><?php echo e($selectedOrder->client?->name ?: 'Unassigned client'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedOrder->client?->code): ?> <small class="ft-po-client-code"><?php echo e($selectedOrder->client->code); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong></div>
                </div>
                <div class="ft-po-summary-cell"><span class="ft-po-summary-label">REFERENCE</span><strong><?php echo e($reference ?: '—'); ?></strong></div>
                <div class="ft-po-summary-cell"><span class="ft-po-summary-label">WORKFLOW STAGE</span><strong><?php echo e($selectedOrder->phase?->name ?: 'Not started'); ?></strong></div>
                <div class="ft-po-summary-cell"><span class="ft-po-summary-label">ORDER STATUS</span><strong class="ft-po-actual-order-status"><?php echo e($selectedOrder->status ?: '—'); ?></strong></div>
                <div class="ft-po-summary-cell"><span class="ft-po-summary-label">CREATED</span><strong><?php echo e($selectedOrder->created_at ? \App\Support\UserLocalTime::format($selectedOrder->created_at, 'M j, Y') : '—'); ?></strong></div>
                <div class="ft-po-summary-cell"><span class="ft-po-summary-label">DELIVERY DATE</span><strong><?php echo e($selectedOrder->delivery_date ? \App\Support\UserLocalTime::format($selectedOrder->delivery_date, 'M j, Y') : 'Not set'); ?></strong></div>
            </div>

            <div class="ft-po-order-products">
                <div class="ft-po-products-heading">
                    <div><h3>Order products</h3><p>Confirm the products and supplier before generating the PO.</p></div>
                    <span class="ft-po-products-count"><?php echo e(count($products)); ?> <?php echo e(count($products) === 1 ? 'product' : 'products'); ?> <span aria-hidden="true">·</span> <?php echo e(number_format($units)); ?> units</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products): ?>
                    <div class="ft-po-products-list" role="list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_slice($products, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-po-product-item" role="listitem" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'ft-po-product-summary-'.e($product['id']).''; ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'ft-po-product-summary-'.e($product['id']).''; ?>wire:key="ft-po-product-summary-<?php echo e($product['id']); ?>">
                                <span class="ft-po-product-index" aria-hidden="true"><?php echo e($loop->iteration); ?></span>
                                <span class="ft-po-product-name"><strong title="<?php echo e($product['name']); ?>"><?php echo e($product['name']); ?></strong><small>Supplier: <?php echo e($product['supplier']); ?></small></span>
                                <span class="ft-po-product-quantity"><?php echo e(number_format((int) $product['quantity'])); ?> <small>units</small></span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($products) > 3): ?>
                            <details class="ft-po-products-more">
                                <summary>Show <?php echo e(count($products) - 3); ?> more <?php echo e(count($products) - 3 === 1 ? 'product' : 'products'); ?></summary>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_slice($products, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="ft-po-product-item" role="listitem" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'ft-po-product-summary-'.e($product['id']).''; ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'ft-po-product-summary-'.e($product['id']).''; ?>wire:key="ft-po-product-summary-<?php echo e($product['id']); ?>">
                                        <span class="ft-po-product-index" aria-hidden="true"><?php echo e($loop->iteration + 3); ?></span>
                                        <span class="ft-po-product-name"><strong title="<?php echo e($product['name']); ?>"><?php echo e($product['name']); ?></strong><small>Supplier: <?php echo e($product['supplier']); ?></small></span>
                                        <span class="ft-po-product-quantity"><?php echo e(number_format((int) $product['quantity'])); ?> <small>units</small></span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </details>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="ft-po-no-products">No active products on this order.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-po-workspace-heading">
                <div><h3><?php echo e($poRows ? 'Generated purchase orders' : 'Create supplier purchase order'); ?></h3><p><?php echo e($poRows ? 'Review, download or email the PDFs associated with this order.' : 'Choose a PO format and generate a PDF from the order products above.'); ?></p></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($poRows): ?><span class="ft-po-status is-ready"><?php echo e(count($poRows)); ?> <?php echo e(count($poRows) === 1 ? 'PO' : 'POs'); ?> generated</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditSelectedOrder || strcasecmp((string) $selectedOrder->status, 'Cancelled') === 0): ?>
                <p class="ft-po-readonly-note">You can view generated purchase orders for this order, but you don't have permission to create or send them.</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $poRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-po-readonly-row">
                        <span><strong><?php echo e($po['number']); ?></strong><small><?php echo e($po['supplier']); ?> · <?php echo e(ucfirst(str_replace('_', ' ', $po['status']))); ?></small></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($po['document_id']): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','size' => 'sm','href' => route('documents.open', $po['document_id']),'target' => '_blank','rel' => 'noopener']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 'sm','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('documents.open', $po['document_id'])),'target' => '_blank','rel' => 'noopener']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Open PDF <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal65929a320cfb8b435b3e69c42cef87b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65929a320cfb8b435b3e69c42cef87b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.order-detail.supplier-purchase-order-panel','data' => ['standalone' => true,'payload' => $orderWorkflowActionPayload,'preview' => $orderWorkflowActionModalPreview['supplier_po'] ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.order-detail.supplier-purchase-order-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['standalone' => true,'payload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionPayload),'preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderWorkflowActionModalPreview['supplier_po'] ?? [])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65929a320cfb8b435b3e69c42cef87b4)): ?>
<?php $attributes = $__attributesOriginal65929a320cfb8b435b3e69c42cef87b4; ?>
<?php unset($__attributesOriginal65929a320cfb8b435b3e69c42cef87b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65929a320cfb8b435b3e69c42cef87b4)): ?>
<?php $component = $__componentOriginal65929a320cfb8b435b3e69c42cef87b4; ?>
<?php unset($__componentOriginal65929a320cfb8b435b3e69c42cef87b4); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    <?php else: ?>
        <section class="ft-po-list" aria-label="Orders available for purchase orders">
            <div class="ft-po-list-heading">
                <div><h2>Order purchase orders</h2><p>Find the correct order, then generate or manage its supplier PO.</p></div>
                <span class="ft-po-list-hint">15 orders per page</span>
            </div>
            <div class="ft-po-filter-toolbar">
                <label class="ft-po-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m16 16 4.4 4.4"/></svg>
                    <input type="search" wire:model.live.debounce.600ms="search" placeholder="Search order, reference, client or product" aria-label="Search orders" autocomplete="off" />
                </label>
                <div class="ft-po-filter-controls" role="group" aria-label="Filter by purchase order status">
                    <button type="button" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-po-filter-chip', 'is-active' => $poFilter === 'all']); ?>" wire:click="setPoFilter('all')" aria-pressed="<?php echo e($poFilter === 'all' ? 'true' : 'false'); ?>">All orders</button>
                    <button type="button" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-po-filter-chip', 'is-active' => $poFilter === 'pending']); ?>" wire:click="setPoFilter('pending')" aria-pressed="<?php echo e($poFilter === 'pending' ? 'true' : 'false'); ?>">To generate</button>
                    <button type="button" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-po-filter-chip', 'is-active' => $poFilter === 'generated']); ?>" wire:click="setPoFilter('generated')" aria-pressed="<?php echo e($poFilter === 'generated' ? 'true' : 'false'); ?>">Generated</button>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($search) || $poFilter !== 'all'): ?>
                    <button type="button" class="ft-po-clear" wire:click="clearPoFilters">Clear filters</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-po-table-summary">
                <span><strong><?php echo e($orders->count()); ?></strong> <?php echo e($orders->count() === 1 ? 'order' : 'orders'); ?> on this page</span>
                <span><?php echo e($poFilter === 'pending' ? 'Awaiting PO generation' : ($poFilter === 'generated' ? 'Generated supplier POs' : 'All PO statuses')); ?></span>
            </div>
            <div class="ft-po-table-scroll" wire:loading.class="is-loading" wire:target="search,setPoFilter,gotoPage,previousPage,nextPage">
                <table class="ft-po-table">
                    <thead><tr><th>Order</th><th>Client &amp; product</th><th>Current stage</th><th>Order status</th><th>PO status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $reference = $order->order_number && $order->order_number !== $order->job_number ? $order->order_number : null;
                                $productName = $order->first_po_product ?: $order->product ?: $order->title;
                            ?>
                            <tr class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-po-order-row', 'has-po' => $order->has_active_supplier_po]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'purchase-order-list-'.e($order->id).''; ?>wire:key="purchase-order-list-<?php echo e($order->id); ?>">
                                <td data-label="Order" class="ft-po-order-identity">
                                    <a class="ft-po-order-id" href="<?php echo e(route('jobs.index', ['open' => $order->id])); ?>" wire:navigate><?php echo e($order->displayOrderNumber()); ?></a>
                                    <small><?php echo e($reference ?: 'No reference'); ?> <span aria-hidden="true">·</span> <?php echo e($order->created_at ? \App\Support\UserLocalTime::format($order->created_at, 'M j, Y') : '—'); ?></small>
                                </td>
                                <td data-label="Client &amp; product" class="ft-po-client-product">
                                    <div class="ft-po-client-product-row">
                                        <?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $order->client,'size' => 34]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order->client),'size' => 34]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
                                        <span><strong title="<?php echo e($order->client?->name ?: 'Client'); ?>"><?php echo e($order->client?->name ?: 'Client'); ?></strong><small title="<?php echo e($productName); ?>"><?php echo e($productName ?: 'No product'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->po_product_count > 1): ?> <span class="ft-po-more-count">+<?php echo e($order->po_product_count - 1); ?> more</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small></span>
                                    </div>
                                </td>
                                <td data-label="Current stage"><span class="ft-po-stage-chip" <?php if($order->phase?->color): ?> style="--po-stage-color: <?php echo e(\App\Support\MasterColor::normalize($order->phase->color) ?: '#64748b'); ?>" <?php endif; ?>><?php echo e($order->phase?->name ?: 'Not started'); ?></span></td>
                                <td data-label="Order status"><?php if (isset($component)) { $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-badge','data' => ['label' => $order->status ?: '—','variant' => stripos((string) $order->status, 'Waiting') !== false ? 'warning' : 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order->status ?: '—'),'variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(stripos((string) $order->status, 'Waiting') !== false ? 'warning' : 'success')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $attributes = $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $component = $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?></td>
                                <td data-label="PO status"><span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-po-status', 'is-ready' => $order->has_active_supplier_po]); ?>"><span class="ft-po-status-dot" aria-hidden="true"></span><?php echo e($order->has_active_supplier_po ? 'Generated' : 'Not generated'); ?></span></td>
                                <td data-label="Action"><?php if (isset($component)) { $__componentOriginalc2677fea324d3c45ab0be143b454c82f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2677fea324d3c45ab0be143b454c82f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.orders.po-action','data' => ['orderId' => $order->id,'generated' => (bool) $order->has_active_supplier_po,'select' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('orders.po-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order->id),'generated' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $order->has_active_supplier_po),'select' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2677fea324d3c45ab0be143b454c82f)): ?>
<?php $attributes = $__attributesOriginalc2677fea324d3c45ab0be143b454c82f; ?>
<?php unset($__attributesOriginalc2677fea324d3c45ab0be143b454c82f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2677fea324d3c45ab0be143b454c82f)): ?>
<?php $component = $__componentOriginalc2677fea324d3c45ab0be143b454c82f; ?>
<?php unset($__componentOriginalc2677fea324d3c45ab0be143b454c82f); ?>
<?php endif; ?></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="6" class="ft-po-empty"><strong>No orders found</strong><span>Try another search or choose a different PO status.</span></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="ft-po-pagination"><?php echo e($orders->links()); ?></div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/orders/purchase-orders.blade.php ENDPATH**/ ?>