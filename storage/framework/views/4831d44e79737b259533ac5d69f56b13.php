<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tracking']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['tracking']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $variant = $tracking['screen_variant'] ?? 'new-order';
    $status = $tracking['current_status'];
    $next = $tracking['next'];
    $timeline = $tracking['timeline'] ?? [];
    $shipment = $tracking['shipment'] ?? [];
    $paymentDetail = $tracking['payment_detail'] ?? null;
    $stageVariant = $variant === 'artwork' ? 'detailed' : 'compact';
?>

<div class="ft-tracking-stage-screen is-<?php echo e($variant); ?>">
    <section class="ft-tracking-result-card" aria-labelledby="tracking-result-title">
        <header class="ft-tracking-result-head">
            <div class="ft-tracking-result-title-row">
                <h2 id="tracking-result-title">Order <?php echo e($tracking['order_number']); ?></h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tracking['sample_order']): ?>
                    <span class="ft-tracking-sample-badge">Sample order</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <p>
                Reference: <?php echo e($tracking['reference_number'] !== '' ? $tracking['reference_number'] : '—'); ?>

                <b aria-hidden="true">|</b>
                Last updated: <?php echo e($tracking['last_updated']); ?>

            </p>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'artwork'): ?>
            <div class="ft-tracking-status-callout is-<?php echo e($status['tone']); ?> <?php echo e(($status['action'] ?? null) === 'review-artwork' ? '' : 'is-passive'); ?>">
                <span class="ft-tracking-callout-icon"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => $status['icon'],'size' => 26]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status['icon']),'size' => 26]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                <div class="ft-tracking-callout-copy">
                    <strong><?php echo e($status['title']); ?></strong>
                    <p><?php echo e($status['message']); ?></p>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($status['action'] ?? null) === 'review-artwork'): ?>
                    <div class="ft-tracking-callout-action-copy">Your approval is needed</div>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('login')).'','class' => 'ft-tracking-action-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('login')).'','class' => 'ft-tracking-action-button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Sign in to review artwork
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
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal17f1cfd0cd6c7000c297c952b8bdcddc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal17f1cfd0cd6c7000c297c952b8bdcddc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.overview','data' => ['status' => $status,'summary' => $tracking['summary']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status),'summary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tracking['summary'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal17f1cfd0cd6c7000c297c952b8bdcddc)): ?>
<?php $attributes = $__attributesOriginal17f1cfd0cd6c7000c297c952b8bdcddc; ?>
<?php unset($__attributesOriginal17f1cfd0cd6c7000c297c952b8bdcddc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal17f1cfd0cd6c7000c297c952b8bdcddc)): ?>
<?php $component = $__componentOriginal17f1cfd0cd6c7000c297c952b8bdcddc; ?>
<?php unset($__componentOriginal17f1cfd0cd6c7000c297c952b8bdcddc); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-tracking-stages" aria-label="Order stages">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tracking['stages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginale74ea6e0cfaebc847d8bfe14ff901d1f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale74ea6e0cfaebc847d8bfe14ff901d1f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.stage-card','data' => ['stage' => $stage,'variant' => $stageVariant]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.stage-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stage),'variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stageVariant)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale74ea6e0cfaebc847d8bfe14ff901d1f)): ?>
<?php $attributes = $__attributesOriginale74ea6e0cfaebc847d8bfe14ff901d1f; ?>
<?php unset($__attributesOriginale74ea6e0cfaebc847d8bfe14ff901d1f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale74ea6e0cfaebc847d8bfe14ff901d1f)): ?>
<?php $component = $__componentOriginale74ea6e0cfaebc847d8bfe14ff901d1f; ?>
<?php unset($__componentOriginale74ea6e0cfaebc847d8bfe14ff901d1f); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($variant):
            case ('new-order'): ?>
                <div class="ft-tracking-new-order-grid">
                    <section class="ft-tracking-status-card">
                        <header>
                            <h3>Order status</h3>
                            <p>Live updates for your order.</p>
                        </header>
                        <div class="ft-tracking-current-status-row">
                            <span class="ft-tracking-current-marker" aria-hidden="true"></span>
                            <div>
                                <strong><?php echo e($status['title']); ?></strong>
                                <p><?php echo e($status['message']); ?></p>
                            </div>
                        </div>
                    </section>

                    <section class="ft-tracking-next-card">
                        <h3>What happens next?</h3>
                        <div class="ft-tracking-next-message">
                            <span class="ft-tracking-next-icon"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'document','size' => 31]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'document','size' => 31]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                            <p><?php echo e($next['message']); ?></p>
                        </div>
                    </section>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>

            <?php case ('artwork'): ?>
                <div class="ft-tracking-artwork-grid">
                    <?php if (isset($component)) { $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.progress-list','data' => ['title' => 'Order progress','subtitle' => 'Track the progress of your order.','items' => $timeline,'tone' => 'purple']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.progress-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order progress','subtitle' => 'Track the progress of your order.','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timeline),'tone' => 'purple']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $attributes = $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $component = $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>

                    <div class="ft-tracking-artwork-side">
                        <section class="ft-tracking-next-card ft-tracking-next-card--artwork">
                            <h3>What happens next?</h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($next['action'] ?? null) === 'review-artwork'): ?>
                                <p class="ft-tracking-next-intro"><?php echo e($next['intro']); ?></p>
                                <div class="ft-tracking-next-action-box is-warning">
                                    <span class="ft-tracking-next-alert"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'alert','size' => 25]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 25]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                                    <div>
                                        <strong><?php echo e($next['title']); ?></strong>
                                        <p><?php echo e($next['message']); ?></p>
                                    </div>
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('login')).'','class' => 'ft-tracking-action-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('login')).'','class' => 'ft-tracking-action-button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <?php echo e($next['action_label']); ?>

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
                            <?php else: ?>
                                <div class="ft-tracking-next-message ft-tracking-next-message--artwork">
                                    <span class="ft-tracking-next-icon"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => $status['icon'],'size' => 31]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status['icon']),'size' => 31]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                                    <p><?php echo e($next['message']); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </section>

                        <section class="ft-tracking-delivery-card">
                            <header>
                                <h3>Delivery, billing and payment</h3>
                                <p>These steps will be completed after production.</p>
                            </header>
                            <div class="ft-tracking-summary ft-tracking-summary--workflow">
                                <?php if (isset($component)) { $__componentOriginal1d01c42a53fe56489990480be3a473e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d01c42a53fe56489990480be3a473e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.summary-item','data' => ['title' => 'Delivery','data' => $tracking['summary']['delivery'],'icon' => 'truck']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.summary-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Delivery','data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tracking['summary']['delivery']),'icon' => 'truck']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $attributes = $__attributesOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__attributesOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $component = $__componentOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__componentOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal1d01c42a53fe56489990480be3a473e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d01c42a53fe56489990480be3a473e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.summary-item','data' => ['title' => 'Billing','data' => $tracking['summary']['billing'],'icon' => 'billing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.summary-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Billing','data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tracking['summary']['billing']),'icon' => 'billing']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $attributes = $__attributesOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__attributesOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $component = $__componentOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__componentOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal1d01c42a53fe56489990480be3a473e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d01c42a53fe56489990480be3a473e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.summary-item','data' => ['title' => 'Payment','data' => $tracking['summary']['payment'],'icon' => 'payment']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.summary-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Payment','data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tracking['summary']['payment']),'icon' => 'payment']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $attributes = $__attributesOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__attributesOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d01c42a53fe56489990480be3a473e4)): ?>
<?php $component = $__componentOriginal1d01c42a53fe56489990480be3a473e4; ?>
<?php unset($__componentOriginal1d01c42a53fe56489990480be3a473e4); ?>
<?php endif; ?>
                            </div>
                        </section>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>

            <?php case ('production'): ?>
                <div class="ft-tracking-production-grid">
                    <?php if (isset($component)) { $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.progress-list','data' => ['title' => 'Order progress','subtitle' => 'See the latest status of your order.','items' => $timeline,'tone' => 'orange']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.progress-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order progress','subtitle' => 'See the latest status of your order.','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timeline),'tone' => 'orange']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $attributes = $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $component = $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>

                    <section class="ft-tracking-next-card ft-tracking-next-card--production">
                        <h3>What happens next?</h3>
                        <p class="ft-tracking-next-intro"><?php echo e($next['intro']); ?></p>
                        <div class="ft-tracking-next-step-box">
                            <span class="ft-tracking-next-step-icon"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'search','size' => 29]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => 29]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                            <div>
                                <strong><?php echo e($next['title']); ?></strong>
                                <p><?php echo e($next['message']); ?></p>
                            </div>
                        </div>
                        <div class="ft-tracking-info-box">
                            <span><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'info','size' => 25]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 25]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                            <div>
                                <strong><?php echo e($next['note_title']); ?></strong>
                                <p><?php echo e($next['note_message']); ?></p>
                            </div>
                        </div>
                    </section>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>

            <?php case ('qc'): ?>
                <div class="ft-tracking-qc-grid">
                    <?php if (isset($component)) { $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.progress-list','data' => ['title' => 'Order updates','subtitle' => 'Live updates for your order.','items' => $timeline,'tone' => 'teal','class' => 'ft-tracking-qc-progress']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.progress-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order updates','subtitle' => 'Live updates for your order.','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timeline),'tone' => 'teal','class' => 'ft-tracking-qc-progress']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $attributes = $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $component = $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>

                    <section class="ft-tracking-next-card ft-tracking-next-card--qc">
                        <header class="ft-tracking-inline-heading">
                            <span class="ft-tracking-card-heading-icon is-info"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'info','size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 24]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                            <h3>Next step</h3>
                        </header>
                        <div class="ft-tracking-next-copy">
                            <strong><?php echo e($next['title']); ?></strong>
                            <p><?php echo e($next['message']); ?></p>
                        </div>
                    </section>

                    <?php if (isset($component)) { $__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.shipment-details','data' => ['shipment' => $shipment,'subtitle' => 'Your order will be dispatched once quality checking is complete.','showButton' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.shipment-details'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shipment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment),'subtitle' => 'Your order will be dispatched once quality checking is complete.','show-button' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2)): ?>
<?php $attributes = $__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2; ?>
<?php unset($__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2)): ?>
<?php $component = $__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2; ?>
<?php unset($__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal9431f2673f2526681982e1d68c9b86f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9431f2673f2526681982e1d68c9b86f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.status-strip','data' => ['title' => 'Billing','message' => 'Upcoming.','icon' => 'billing','tone' => 'pink','class' => 'ft-tracking-qc-billing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.status-strip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Billing','message' => 'Upcoming.','icon' => 'billing','tone' => 'pink','class' => 'ft-tracking-qc-billing']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $attributes = $__attributesOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $component = $__componentOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__componentOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal9431f2673f2526681982e1d68c9b86f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9431f2673f2526681982e1d68c9b86f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.status-strip','data' => ['title' => 'Payment','message' => 'Upcoming.','icon' => 'payment','tone' => 'neutral','class' => 'ft-tracking-qc-payment']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.status-strip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Payment','message' => 'Upcoming.','icon' => 'payment','tone' => 'neutral','class' => 'ft-tracking-qc-payment']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $attributes = $__attributesOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $component = $__componentOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__componentOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>

            <?php case ('shipment'): ?>
                <div class="ft-tracking-shipment-grid">
                    <?php if (isset($component)) { $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.progress-list','data' => ['title' => 'Order updates','subtitle' => 'Follow the progress of your order.','items' => $timeline,'tone' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.progress-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order updates','subtitle' => 'Follow the progress of your order.','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timeline),'tone' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $attributes = $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $component = $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.shipment-details','data' => ['shipment' => $shipment,'shipments' => $tracking['shipment_rows'] ?? [],'subtitle' => 'Track your shipment with the courier.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.shipment-details'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shipment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipment),'shipments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tracking['shipment_rows'] ?? []),'subtitle' => 'Track your shipment with the courier.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2)): ?>
<?php $attributes = $__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2; ?>
<?php unset($__attributesOriginal9d36a619fcde4418ef6f7e9bde4e77f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2)): ?>
<?php $component = $__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2; ?>
<?php unset($__componentOriginal9d36a619fcde4418ef6f7e9bde4e77f2); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal9431f2673f2526681982e1d68c9b86f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9431f2673f2526681982e1d68c9b86f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.status-strip','data' => ['title' => ($tracking['summary']['billing']['state'] ?? 'upcoming') === 'active' ? 'Billing in progress' : 'Billing','message' => ($tracking['summary']['billing']['state'] ?? 'upcoming') === 'active' ? 'We are preparing your invoice. No action is needed right now.' : 'Upcoming.','icon' => 'billing','tone' => 'pink','class' => 'ft-tracking-shipment-billing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.status-strip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($tracking['summary']['billing']['state'] ?? 'upcoming') === 'active' ? 'Billing in progress' : 'Billing'),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($tracking['summary']['billing']['state'] ?? 'upcoming') === 'active' ? 'We are preparing your invoice. No action is needed right now.' : 'Upcoming.'),'icon' => 'billing','tone' => 'pink','class' => 'ft-tracking-shipment-billing']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $attributes = $__attributesOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $component = $__componentOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__componentOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>

            <?php case ('billing'): ?>
                <div class="ft-tracking-billing-grid">
                    <?php if (isset($component)) { $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.progress-list','data' => ['title' => 'Order progress','subtitle' => 'A summary of completed stages for your order.','items' => $timeline,'tone' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.progress-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order progress','subtitle' => 'A summary of completed stages for your order.','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timeline),'tone' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $attributes = $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $component = $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>

                    <section class="ft-tracking-next-card ft-tracking-next-card--billing">
                        <h3>What happens next?</h3>
                        <div class="ft-tracking-billing-current">
                            <span class="ft-tracking-billing-current-icon"><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => $status['icon'],'size' => 29]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status['icon']),'size' => 29]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                            <div>
                                <strong><?php echo e($next['title']); ?></strong>
                                <p><?php echo e($next['message']); ?></p>
                            </div>
                        </div>
                        <div class="ft-tracking-info-box">
                            <span><?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'info','size' => 23]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 23]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?></span>
                            <div>
                                <strong><?php echo e($next['note_title']); ?></strong>
                                <p><?php echo e($next['note_message']); ?></p>
                            </div>
                        </div>
                    </section>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>

            <?php case ('payment'): ?>
            <?php default: ?>
                <div class="ft-tracking-payment-grid">
                    <?php if (isset($component)) { $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.progress-list','data' => ['title' => 'Order updates','subtitle' => ($tracking['public_order_completed'] ?? false) ? 'Your order has been completed.' : 'See the latest status of your order.','items' => $timeline,'tone' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.progress-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order updates','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($tracking['public_order_completed'] ?? false) ? 'Your order has been completed.' : 'See the latest status of your order.'),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timeline),'tone' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $attributes = $__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__attributesOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec)): ?>
<?php $component = $__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec; ?>
<?php unset($__componentOriginalce2eb97a3915d4cd4d50a9c2def045ec); ?>
<?php endif; ?>

                    <div class="ft-tracking-payment-side">
                        <section class="ft-tracking-payment-card">
                            <header>
                                <h3>Payment details</h3>
                                <p><?php echo e(($tracking['public_order_completed'] ?? false) ? 'Your payment has been received and the order is complete.' : 'See the latest payment status for your order.'); ?></p>
                            </header>
                            <?php if (isset($component)) { $__componentOriginal9431f2673f2526681982e1d68c9b86f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9431f2673f2526681982e1d68c9b86f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.status-strip','data' => ['title' => $paymentDetail['title'],'message' => $paymentDetail['message'],'icon' => $paymentDetail['icon'],'tone' => $paymentDetail['tone']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.status-strip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentDetail['title']),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentDetail['message']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentDetail['icon']),'tone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentDetail['tone'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $attributes = $__attributesOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__attributesOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9431f2673f2526681982e1d68c9b86f2)): ?>
<?php $component = $__componentOriginal9431f2673f2526681982e1d68c9b86f2; ?>
<?php unset($__componentOriginal9431f2673f2526681982e1d68c9b86f2); ?>
<?php endif; ?>
                        </section>

                        <section class="ft-tracking-next-card ft-tracking-next-card--payment">
                            <h3>Next steps</h3>
                            <div class="ft-tracking-payment-next">
                                <span class="ft-tracking-payment-next-icon">
                                    <?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => ($tracking['public_order_completed'] ?? false) ? 'check' : ($status['icon'] ?? 'payment'),'size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($tracking['public_order_completed'] ?? false) ? 'check' : ($status['icon'] ?? 'payment')),'size' => 28]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
                                </span>
                                <div>
                                    <strong><?php echo e($next['title']); ?></strong>
                                    <p><?php echo e($next['message']); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($next['action'] ?? null) === 'payment-instructions'): ?>
                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('login')).'','class' => 'ft-tracking-payment-login']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('login')).'','class' => 'ft-tracking-payment-login']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            <?php echo e($next['action_label'] ?? 'Sign in'); ?>

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
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-tracking-another-wrap">
            <a href="<?php echo e(route('order.track').'#tracking-search-page-title'); ?>" class="ft-tracking-another">
                <?php if (isset($component)) { $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.icon','data' => ['name' => 'refresh','size' => 18]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'refresh','size' => 18]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $attributes = $__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__attributesOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5)): ?>
<?php $component = $__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5; ?>
<?php unset($__componentOriginal0d78ef242c7848a0ad8a9198097ba1d5); ?>
<?php endif; ?> Track another order
            </a>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($tracking['design_preview'] ?? false) && !empty($tracking['status_variants'])): ?>
        <?php if (isset($component)) { $__componentOriginala3b20152a98f0df7fa85e6b46b3ec10c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3b20152a98f0df7fa85e6b46b3ec10c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-tracking.status-variants','data' => ['section' => $tracking['status_variants'],'currentStatusKey' => $status['key'] ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-tracking.status-variants'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tracking['status_variants']),'current-status-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status['key'] ?? null)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3b20152a98f0df7fa85e6b46b3ec10c)): ?>
<?php $attributes = $__attributesOriginala3b20152a98f0df7fa85e6b46b3ec10c; ?>
<?php unset($__attributesOriginala3b20152a98f0df7fa85e6b46b3ec10c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3b20152a98f0df7fa85e6b46b3ec10c)): ?>
<?php $component = $__componentOriginala3b20152a98f0df7fa85e6b46b3ec10c; ?>
<?php unset($__componentOriginala3b20152a98f0df7fa85e6b46b3ec10c); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/order-tracking/result-card.blade.php ENDPATH**/ ?>