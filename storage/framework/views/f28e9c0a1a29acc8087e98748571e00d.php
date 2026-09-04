<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'selected' => null,
    'methods' => collect(),
    'urgencies' => collect(),
    'taskId' => null,
    'shipmentId' => null,
    'mode' => 'row',
    'disabled' => false,
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
    'selected' => null,
    'methods' => collect(),
    'urgencies' => collect(),
    'taskId' => null,
    'shipmentId' => null,
    'mode' => 'row',
    'disabled' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $presenter = \App\Support\CreateOrderShippingMethodPresenter::class;
    $methods = collect($methods)->values();
    $urgencies = collect($urgencies)->values();
    $directMethods = $presenter::directMethods($methods);
    $expressMethod = $presenter::expressMethod($methods);
    $expressUrgencies = $presenter::expressUrgencies($urgencies);
    $hardDisabled = (bool) $disabled;
    $clientDisabledExpression = $hardDisabled ? 'true' : 'false';
    $action = $mode === 'modal' ? 'selectShipmentModalMethod' : 'selectOrderShipmentMethod';
?>

<div
    class="ft-ms-method"
    x-data="{
        ...window.FlowTrack.ui.floatingActionMenu(),
        menuZIndex: 2450,
        open: false,
        toggleMenu() {
            if (<?php echo $clientDisabledExpression; ?>) return;
            if (this.open) { this.closeMenu(); return; }
            this.menuStyle = 'visibility:hidden!important;pointer-events:none!important';
            this.open = true;
            this.$nextTick(() => this.positionMenu());
        },
        closeMenu() {
            this.open = false;
            this.menuStyle = '';
        }
    }"
    x-effect="if (<?php echo $clientDisabledExpression; ?> && open) closeMenu()"
    x-on:keydown.escape.window="closeMenu()"
    x-on:resize.window="open && positionMenu()"
    x-on:scroll.window="open && positionMenu()"
>
    <button
        x-ref="trigger"
        type="button"
        class="ft-ms-method__trigger"
        :class="open ? 'is-open' : ''"
        x-on:click.stop="toggleMenu()"
        x-bind:disabled="<?php echo $clientDisabledExpression; ?>"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
            <span class="ft-ms-method__icon"><?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal937251c6395c013b7e12535197664182 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.shipping-method-icon','data' => ['type' => $selected['kind']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.shipping-method-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selected['kind'])]); ?>
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
            <span class="ft-ms-method__copy">
                <strong><?php echo e($selected['title']); ?></strong>
            </span>
        <?php else: ?>
            <span class="ft-ms-method__copy"><strong>Select shipping method</strong></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <svg
            class="ft-ms-method__chevron"
            x-cloak
            x-show="!(<?php echo $clientDisabledExpression; ?>)"
            :class="open ? 'is-open' : ''"
            viewBox="0 0 20 20"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            aria-hidden="true"
        ><path d="m6 8 4 4 4-4"/></svg>
    </button>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($hardDisabled)): ?>
        <template x-teleport="body">
            <div
                x-ref="menu"
                class="ft-ms-method__menu ft-ms-method-portal"
                x-cloak
                x-show="open"
                x-bind:style="menuStyle + (open ? ';display:block!important;' : ';display:none!important;')"
                x-on:click.outside="closeMenu()"
                role="listbox"
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $directMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $kind = $presenter::methodKind($method);
                        $label = $presenter::methodLabel($method);
                        $actionArgs = $mode === 'modal'
                            ? ((int) $method->id).', null'
                            : ((int) $taskId).', '.((int) $shipmentId).', '.((int) $method->id).', null';
                    ?>
                    <button
                        type="button"
                        class="ft-ms-method__option"
                        x-on:click.stop="closeMenu(); $wire.call(<?php echo \Illuminate\Support\Js::from($action)->toHtml() ?>, <?php echo $actionArgs; ?>)"
                    >
                        <span class="ft-ms-method__icon"><?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
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
                        <span class="ft-ms-method__copy">
                            <strong><?php echo e($label); ?></strong>
                        </span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expressMethod): ?>
                    <div class="ft-ms-method__group-title">
                        <span>STANDARD EXPRESS SHIPPING</span>
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10" cy="10" r="7.2"/><path d="M10 8.7v4.4M10 6.2h.01"/></svg>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $expressUrgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urgency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $urgencyId = $urgency['id'];
                            $urgencyArg = $urgencyId === null ? 'null' : (string) ((int) $urgencyId);
                            $actionArgs = $mode === 'modal'
                                ? ((int) $expressMethod->id).', '.$urgencyArg
                                : ((int) $taskId).', '.((int) $shipmentId).', '.((int) $expressMethod->id).', '.$urgencyArg;
                        ?>
                        <button
                            type="button"
                            class="ft-ms-method__option ft-ms-method__option--express"
                            x-on:click.stop="closeMenu(); $wire.call(<?php echo \Illuminate\Support\Js::from($action)->toHtml() ?>, <?php echo $actionArgs; ?>)"
                        >
                            <span class="ft-ms-method__icon"><?php if (isset($component)) { $__componentOriginal937251c6395c013b7e12535197664182 = $component; } ?>
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
                            <span class="ft-ms-method__copy ft-ms-method__copy--inline">
                                <strong><?php echo e($urgency['name']); ?></strong>
                            </span>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </template>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment/method-picker.blade.php ENDPATH**/ ?>