<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'shipmentMethods' => collect(),
    'shipmentUrgencies' => collect(),
    'selectedMethodIds' => [],
    'selectedUrgencyIds' => [],
    'shipmentIndex' => null,
    'selectedMethodId' => null,
    'selectedUrgencyId' => null,
    'compact' => false,
]));

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

foreach (array_filter(([
    'shipmentMethods' => collect(),
    'shipmentUrgencies' => collect(),
    'selectedMethodIds' => [],
    'selectedUrgencyIds' => [],
    'shipmentIndex' => null,
    'selectedMethodId' => null,
    'selectedUrgencyId' => null,
    'compact' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $shippingPresenter = \App\Support\CreateOrderShippingMethodPresenter::class;
    $methods = collect($shipmentMethods)->values();
    $urgencies = collect($shipmentUrgencies)->values();
    $directMethods = $shippingPresenter::directMethods($methods);
    $expressMethod = $shippingPresenter::expressMethod($methods);
    $expressUrgencies = $shippingPresenter::expressUrgencies($urgencies);
    $methodIds = $shipmentIndex !== null
        ? (filled($selectedMethodId) ? [(int) $selectedMethodId] : [])
        : (array) $selectedMethodIds;
    $urgencyIds = $shipmentIndex !== null
        ? (filled($selectedUrgencyId) ? [(int) $selectedUrgencyId] : [])
        : (array) $selectedUrgencyIds;
    $selectedCard = $shippingPresenter::selectedCard($methods, $urgencies, $methodIds, $urgencyIds);
    $hasOptions = $directMethods->isNotEmpty() || $expressMethod;
    $validationPrefix = $shipmentIndex !== null ? 'createShipments.'.$shipmentIndex : null;
?>

<div
    class="ft-create-field ft-create-shipping-method-field <?php echo e($compact ? 'ft-create-shipping-method-field--compact' : ''); ?>"
    x-data="{ open: false }"
    x-on:click.outside="open = false"
    x-on:keydown.escape.window="open = false"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($compact)): ?><b>Shipping method</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOptions): ?>
        <div class="ft-create-shipping-picker">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCard): ?>
                <button
                    type="button"
                    class="ft-create-shipping-selected-card"
                    :class="open ? 'is-open' : ''"
                    x-on:click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-haspopup="listbox"
                    aria-label="Change <?php echo e($selectedCard['title']); ?>"
                >
                    <span class="ft-create-shipping-option-icon">
                        <?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal937251c6395c013b7e12535197664182 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-method-icon','data' => ['type' => $selectedCard['kind']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-method-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedCard['kind'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal937251c6395c013b7e12535197664182)): ?>
<?php $attributes = $__attributesOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__attributesOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal937251c6395c013b7e12535197664182)): ?>
<?php $component = $__componentOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__componentOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?>
                    </span>
                    <span class="ft-create-shipping-selected-copy"><strong><?php echo e($selectedCard['title']); ?></strong></span>
                    <svg class="ft-create-shipping-chevron" :class="open ? 'is-open' : ''" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
                </button>
            <?php else: ?>
                <button
                    type="button"
                    class="ft-create-shipping-trigger"
                    :class="open ? 'is-open' : ''"
                    x-on:click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-haspopup="listbox"
                >
                    <span>Select shipping method</span>
                    <svg class="ft-create-shipping-chevron" :class="open ? 'is-open' : ''" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-create-shipping-menu" x-cloak x-show="open" x-transition.opacity.duration.120ms role="listbox" aria-label="Shipping methods">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $directMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $kind = $shippingPresenter::methodKind($method);
                        $label = $shippingPresenter::methodLabel($method);
                    ?>
                    <button
                        type="button"
                        class="ft-create-shipping-option"
                        role="option"
                        <?php if($shipmentIndex !== null): ?>
                            wire:click="selectCreateShipmentMethod(<?php echo e((int) $shipmentIndex); ?>, <?php echo e((int) $method->id); ?>, null)"
                        <?php else: ?>
                            wire:click="selectCreateShippingMethod(<?php echo e((int) $method->id); ?>, null)"
                        <?php endif; ?>
                        x-on:click="open = false"
                        aria-selected="<?php echo e((int) ($selectedCard['method_id'] ?? 0) === (int) $method->id ? 'true' : 'false'); ?>"
                    >
                        <span class="ft-create-shipping-option-icon"><?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal937251c6395c013b7e12535197664182 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-method-icon','data' => ['type' => $kind]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-method-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kind)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal937251c6395c013b7e12535197664182)): ?>
<?php $attributes = $__attributesOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__attributesOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal937251c6395c013b7e12535197664182)): ?>
<?php $component = $__componentOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__componentOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?></span>
                        <span class="ft-create-shipping-option-copy"><strong><?php echo e($label); ?></strong></span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expressMethod): ?>
                    <div class="ft-create-shipping-express-group">
                        <div class="ft-create-shipping-express-heading" role="presentation">
                            <span>STANDARD EXPRESS SHIPPING</span>
                            <span class="ft-create-shipping-info" title="Choose a service level for Standard Express Shipping." aria-label="Standard express shipping information">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10" cy="10" r="7.2"/><path d="M10 8.7v4.4M10 6.2h.01"/></svg>
                            </span>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $expressUrgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expressUrgency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button
                                type="button"
                                class="ft-create-shipping-option ft-create-shipping-option--express"
                                role="option"
                                <?php if($shipmentIndex !== null): ?>
                                    wire:click="selectCreateShipmentMethod(<?php echo e((int) $shipmentIndex); ?>, <?php echo e((int) $expressMethod->id); ?>, <?php echo e($expressUrgency['id'] === null ? 'null' : (int) $expressUrgency['id']); ?>)"
                                <?php else: ?>
                                    wire:click="selectCreateShippingMethod(<?php echo e((int) $expressMethod->id); ?>, <?php echo e($expressUrgency['id'] === null ? 'null' : (int) $expressUrgency['id']); ?>)"
                                <?php endif; ?>
                                x-on:click="open = false"
                                aria-selected="<?php echo e((int) ($selectedCard['method_id'] ?? 0) === (int) $expressMethod->id && ($selectedCard['urgency_id'] ?? null) === $expressUrgency['id'] ? 'true' : 'false'); ?>"
                            >
                                <span class="ft-create-shipping-option-icon"><?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal937251c6395c013b7e12535197664182 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-method-icon','data' => ['type' => 'express']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-method-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'express']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal937251c6395c013b7e12535197664182)): ?>
<?php $attributes = $__attributesOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__attributesOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal937251c6395c013b7e12535197664182)): ?>
<?php $component = $__componentOriginal937251c6395c013b7e12535197664182; ?>
<?php unset($__componentOriginal937251c6395c013b7e12535197664182); ?>
<?php endif; ?></span>
                                <span class="ft-create-shipping-option-copy ft-create-shipping-option-copy--inline"><strong><?php echo e($expressUrgency['name']); ?></strong></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <small>No active Shipment Methods are available in Master Data.</small>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($validationPrefix): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$validationPrefix.'.shipment_method_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$validationPrefix.'.shipment_urgency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentMethodIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentMethodIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentUrgencyIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipmentUrgencyIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/shipping-method-picker.blade.php ENDPATH**/ ?>