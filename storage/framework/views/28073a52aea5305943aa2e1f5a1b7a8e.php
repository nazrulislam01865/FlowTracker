<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'row',
    'canEdit' => false,
    'canDelete' => false,
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
    'row',
    'canEdit' => false,
    'canDelete' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $suppliers = collect($row['suppliers'] ?? []);
    $visibleSuppliers = $suppliers->take(3);
    $supplierCount = (int) ($row['supplier_count'] ?? $suppliers->count());
    $progress = collect($row['progress'] ?? []);
    $quantity = (float) ($row['quantity'] ?? 0);
    $quantityDecimals = fmod(abs($quantity), 1.0) === 0.0 ? 0 : 2;
    $meta = collect([$row['code'] ?? null, $row['category'] ?? null])->filter()->implode(' · ');
    $updatedAt = $row['updated_at'] ?? null;
?>

<tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-product-rfq-overview-row-'.e((int) ($row['item_id'] ?? 0)).''; ?>wire:key="inquiry-product-rfq-overview-row-<?php echo e((int) ($row['item_id'] ?? 0)); ?>" x-data="{ actionOpen: false }">
    <td data-label="Product">
        <div class="ft-inquiry-prq-product">
            <span class="ft-inquiry-prq-product__image">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($row['image_url'] ?? null)): ?>
                    <img src="<?php echo e($row['image_url']); ?>" alt="<?php echo e($row['name'] ?? 'Product'); ?>">
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2"></rect><circle cx="9" cy="9" r="1.5"></circle><path d="m6.5 17 4-4 2.7 2.5 1.8-1.8 2.5 3.3"></path></svg>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="ft-inquiry-prq-product__copy">
                <strong><?php echo e($row['name'] ?? 'Product'); ?></strong>
                <small><?php echo e($meta !== '' ? $meta : 'Inquiry product'); ?></small>
            </span>
        </div>
    </td>
    <td data-label="Quantity">
        <strong class="ft-inquiry-prq-quantity"><?php echo e(number_format($quantity, $quantityDecimals)); ?> <?php echo e($row['unit'] ?? 'units'); ?></strong>
    </td>
    <td data-label="Assigned suppliers">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierCount > 0): ?>
            <div class="ft-inquiry-prq-suppliers">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $visibleSuppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <span class="ft-inquiry-prq-supplier" title="<?php echo e($supplier['name'] ?? 'Supplier'); ?>">
                        <span class="ft-inquiry-prq-supplier__avatar"><?php echo e($supplier['initials'] ?? '—'); ?></span>
                        <span><?php echo e($supplier['name'] ?? 'Supplier'); ?></span>
                    </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <span class="ft-inquiry-prq-supplier-count"><?php echo e($supplierCount); ?> <?php echo e(\Illuminate\Support\Str::plural('supplier', $supplierCount)); ?></span>
            </div>
        <?php else: ?>
            <span class="ft-inquiry-prq-empty-value">No suppliers</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
    <td data-label="RFQ progress">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($progress->isNotEmpty()): ?>
            <div class="ft-inquiry-prq-progress">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $progress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <span class="ft-inquiry-prq-progress__badge is-<?php echo e($badge['tone'] ?? 'neutral'); ?>"><?php echo e((int) ($badge['count'] ?? 0)); ?> <?php echo e($badge['label'] ?? ''); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php else: ?>
            <span class="ft-inquiry-prq-progress__badge is-neutral">Not started</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
    <td data-label="Quotations">
        <?php ($quotes = (int) ($row['quotation_count'] ?? 0)); ?>
        <span class="ft-inquiry-prq-quote <?php echo e($quotes > 0 ? 'is-received' : ''); ?>"><?php echo e($quotes); ?> received</span>
    </td>
    <td data-label="Updated">
        <span class="ft-inquiry-prq-updated">
            <?php echo e($updatedAt ? (\App\Support\UserLocalTime::localize($updatedAt)?->diffForHumans() ?: '—') : '—'); ?>

        </span>
    </td>
    <td class="ft-inquiry-prq-actions" data-label="Actions">
        <button type="button" class="ft-inquiry-prq-view" wire:click="setDetailTab('rfq')">View details</button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit || $canDelete): ?>
            <?php if (isset($component)) { $__componentOriginal769c4590c1dc590e97b31bc706ef7701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal769c4590c1dc590e97b31bc706ef7701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-actions','data' => ['itemId' => (int) ($row['item_id'] ?? 0),'canEdit' => $canEdit,'editMethod' => 'openEditInquiryProduct','canDelete' => $canDelete,'removeMethod' => 'removeInquiryItem','confirmText' => 'Remove this product from the Inquiry?']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['item-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($row['item_id'] ?? 0)),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEdit),'edit-method' => 'openEditInquiryProduct','can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDelete),'remove-method' => 'removeInquiryItem','confirm-text' => 'Remove this product from the Inquiry?']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal769c4590c1dc590e97b31bc706ef7701)): ?>
<?php $attributes = $__attributesOriginal769c4590c1dc590e97b31bc706ef7701; ?>
<?php unset($__attributesOriginal769c4590c1dc590e97b31bc706ef7701); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal769c4590c1dc590e97b31bc706ef7701)): ?>
<?php $component = $__componentOriginal769c4590c1dc590e97b31bc706ef7701; ?>
<?php unset($__componentOriginal769c4590c1dc590e97b31bc706ef7701); ?>
<?php endif; ?>
        <?php else: ?>
            <span class="ft-inquiry-prq-kebab-placeholder" aria-hidden="true">⋮</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/product-rfq-overview-row.blade.php ENDPATH**/ ?>