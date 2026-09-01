<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'overview' => [],
    'canAdd' => false,
    'showAddForm' => false,
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
    'overview' => [],
    'canAdd' => false,
    'showAddForm' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $stats = $overview['stats'] ?? [];
    $productCount = (int) ($overview['product_count'] ?? 0);
    $totalUnits = (float) ($overview['total_units'] ?? 0);
    $unitDecimals = fmod(abs($totalUnits), 1.0) === 0.0 ? 0 : 2;
?>

<section <?php echo e($attributes->class(['ft-inquiry-product-rfq-overview'])); ?> aria-labelledby="inquiry-product-rfq-overview-title">
    <div class="ft-inquiry-prq-stats" aria-label="Product and RFQ summary">
        <?php if (isset($component)) { $__componentOriginalf134083897eba0c69a3642b5be272ff0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf134083897eba0c69a3642b5be272ff0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.product-rfq-stat','data' => ['label' => 'products','value' => (int) ($stats['products'] ?? 0),'icon' => 'product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.product-rfq-stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'products','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($stats['products'] ?? 0)),'icon' => 'product']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $attributes = $__attributesOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__attributesOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $component = $__componentOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__componentOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalf134083897eba0c69a3642b5be272ff0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf134083897eba0c69a3642b5be272ff0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.product-rfq-stat','data' => ['label' => 'supplier assignments','value' => (int) ($stats['supplier_assignments'] ?? 0),'icon' => 'suppliers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.product-rfq-stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'supplier assignments','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($stats['supplier_assignments'] ?? 0)),'icon' => 'suppliers']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $attributes = $__attributesOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__attributesOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $component = $__componentOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__componentOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalf134083897eba0c69a3642b5be272ff0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf134083897eba0c69a3642b5be272ff0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.product-rfq-stat','data' => ['label' => 'invitations sent','value' => (int) ($stats['invitations_sent'] ?? 0),'icon' => 'sent']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.product-rfq-stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'invitations sent','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($stats['invitations_sent'] ?? 0)),'icon' => 'sent']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $attributes = $__attributesOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__attributesOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $component = $__componentOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__componentOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalf134083897eba0c69a3642b5be272ff0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf134083897eba0c69a3642b5be272ff0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.product-rfq-stat','data' => ['label' => 'quotations received','value' => (int) ($stats['quotations_received'] ?? 0),'icon' => 'quotes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.product-rfq-stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'quotations received','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($stats['quotations_received'] ?? 0)),'icon' => 'quotes']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $attributes = $__attributesOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__attributesOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf134083897eba0c69a3642b5be272ff0)): ?>
<?php $component = $__componentOriginalf134083897eba0c69a3642b5be272ff0; ?>
<?php unset($__componentOriginalf134083897eba0c69a3642b5be272ff0); ?>
<?php endif; ?>
    </div>

    <section class="ft-inquiry-prq-card">
        <header class="ft-inquiry-prq-card__head">
            <div>
                <h2 id="inquiry-product-rfq-overview-title">Products, suppliers &amp; RFQ progress</h2>
                <p>Suppliers and quotation activity are tracked separately for each product.</p>
            </div>
            <div class="ft-inquiry-prq-card__head-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAdd && ! $showAddForm): ?>
                    <button type="button" class="ft-inquiry-prq-add" wire:click="openAddInquiryProductForm" wire:loading.attr="disabled" wire:target="openAddInquiryProductForm">
                        <span aria-hidden="true">＋</span> Add product
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span><?php echo e($productCount); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $productCount)); ?> · <?php echo e(number_format($totalUnits, $unitDecimals)); ?> total units</span>
            </div>
        </header>

        <div class="ft-inquiry-prq-table-wrap">
            <table class="ft-inquiry-prq-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Assigned suppliers</th>
                        <th>RFQ progress</th>
                        <th>Quotations</th>
                        <th>Updated</th>
                        <th aria-label="Actions"></th>
                    </tr>
                </thead>
                <tbody><?php echo e($slot); ?></tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($afterTable)): ?>
            <div class="ft-inquiry-prq-after-table"><?php echo e($afterTable); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <footer class="ft-inquiry-prq-note">
            <span class="ft-inquiry-prq-note__icon" aria-hidden="true">i</span>
            <span>Supplier assignments, invitation delivery and quotation responses are shown against the products they cover.</span>
        </footer>
    </section>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/product-rfq-overview.blade.php ENDPATH**/ ?>