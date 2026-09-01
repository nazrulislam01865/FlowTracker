<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'group',
    'canManage' => false,
    'canEditSupplier' => false,
    'selectedKeys' => [],
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
    'group',
    'canManage' => false,
    'canEditSupplier' => false,
    'selectedKeys' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rows = collect($group['supplier_rows'] ?? []);
    $productId = (int) ($group['product_id'] ?? 0);
    $quantity = (float) ($group['quantity'] ?? 0);
    $quantityDecimals = fmod(abs($quantity), 1.0) === 0.0 ? 0 : 2;
    $supplierCount = (int) ($group['supplier_count'] ?? $rows->count());
    $sentCount = (int) ($group['sent_count'] ?? 0);
    $failedCount = (int) ($group['failed_count'] ?? 0);
    $queuedCount = (int) ($group['queued_count'] ?? 0);
    $quotationCount = (int) ($group['quotation_count'] ?? 0);
    $headerAction = $group['header_action'] ?? ['type' => 'view', 'label' => 'View suppliers', 'tone' => 'secondary', 'supplier_ids' => []];
    $selectableKeys = $group['selectable_keys'] ?? [];
?>

<article
    class="ft-rfq-px-product <?php echo e($failedCount > 0 ? 'has-failure' : ''); ?>"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rfq-product-group-'.e((int) ($group['item_id'] ?? 0)).''; ?>wire:key="rfq-product-group-<?php echo e((int) ($group['item_id'] ?? 0)); ?>"
    x-data="{ expanded: <?php echo \Illuminate\Support\Js::from((bool) ($group['expanded_default'] ?? false))->toHtml() ?> }"
>
    <header class="ft-rfq-px-product-head">
        <button type="button" class="ft-rfq-px-expand" x-on:click="expanded = !expanded" :aria-expanded="expanded.toString()" aria-label="Toggle supplier details">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" :class="{ 'is-open': expanded }"><path d="m8 10 4 4 4-4"></path></svg>
        </button>

        <span class="ft-rfq-px-index"><?php echo e((int) ($group['index'] ?? 1)); ?></span>

        <div class="ft-rfq-px-product-copy">
            <strong><?php echo e($group['name'] ?? 'Product'); ?></strong>
            <span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($group['code'] ?? null)): ?><?php echo e($group['code']); ?> <b>·</b> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e(number_format($quantity, $quantityDecimals)); ?> <?php echo e($group['unit'] ?? 'units'); ?>

            </span>
        </div>

        <div class="ft-rfq-px-product-badges">
            <span class="ft-rfq-px-badge is-neutral"><?php echo e($supplierCount); ?> <?php echo e(\Illuminate\Support\Str::plural('supplier', $supplierCount)); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sentCount > 0): ?><span class="ft-rfq-px-badge is-success"><?php echo e($sentCount); ?> sent</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($failedCount > 0): ?><span class="ft-rfq-px-badge is-danger"><?php echo e($failedCount); ?> failed</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($queuedCount > 0): ?><span class="ft-rfq-px-badge is-neutral"><?php echo e($queuedCount); ?> queued</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="ft-rfq-px-badge <?php echo e($quotationCount > 0 ? 'is-success' : 'is-neutral'); ?>"><?php echo e($quotationCount); ?> <?php echo e(\Illuminate\Support\Str::plural('quotation', $quotationCount)); ?></span>
        </div>

        <div class="ft-rfq-px-product-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage && $productId > 0): ?>
                <button type="button" class="ft-rfq-px-add-supplier" wire:click="openRfqSupplierPicker(<?php echo e($productId); ?>)" wire:loading.attr="disabled" wire:target="openRfqSupplierPicker(<?php echo e($productId); ?>)">Add supplier</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($headerAction['type'] ?? '') === 'send' && $canManage): ?>
                <button
                    type="button"
                    class="ft-rfq-px-product-action is-<?php echo e($headerAction['tone'] ?? 'primary'); ?>"
                    wire:click='sendRfqProductEmails(<?php echo json_encode($headerAction['supplier_ids'] ?? [], 15, 512) ?>)'
                    wire:loading.attr="disabled"
                    wire:target="sendRfqProductEmails"
                ><?php echo e($headerAction['label'] ?? 'Send invitation'); ?></button>
            <?php else: ?>
                <button type="button" class="ft-rfq-px-product-action is-secondary" x-on:click="expanded = true"><?php echo e($headerAction['label'] ?? 'View suppliers'); ?></button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <button type="button" class="ft-rfq-px-collapse" x-on:click="expanded = !expanded" aria-label="Toggle supplier details">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" :class="{ 'is-open': expanded }"><path d="m8 10 4 4 4-4"></path></svg>
        </button>
    </header>

    <div class="ft-rfq-px-product-body" x-cloak x-show="expanded" x-transition.opacity.duration.120ms>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($failedCount > 0): ?>
            <div class="ft-rfq-px-product-alert">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5 21 19H3z"></path><path d="M12 9v4M12 16h.01"></path></svg>
                <span><?php echo e($failedCount); ?> <?php echo e(\Illuminate\Support\Str::plural('invitation', $failedCount)); ?> failed for this product. Review the error and retry.</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rows->isNotEmpty()): ?>
            <div class="ft-rfq-px-table-panel">
                <div class="ft-rfq-px-table-wrap">
                    <table class="ft-rfq-px-table">
                    <thead>
                        <tr>
                            <th class="ft-rfq-px-check-col">
                                <button
                                    type="button"
                                    class="ft-rfq-px-master-check <?php echo e(($group['all_selectable_selected'] ?? false) ? 'is-checked' : ''); ?>"
                                    wire:click='toggleRfqProductSelection(<?php echo json_encode($selectableKeys, 15, 512) ?>)'
                                    <?php if($selectableKeys === []): echo 'disabled'; endif; ?>
                                    aria-label="Select suppliers for <?php echo e($group['name'] ?? 'product'); ?>"
                                >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['all_selectable_selected'] ?? false): ?><span>✓</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </button>
                            </th>
                            <th>Supplier</th>
                            <th>Email</th>
                            <th>Invitation status</th>
                            <th>Quotation</th>
                            <th>Last activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal5477f3d0f718b8b49128c86af5f48189 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5477f3d0f718b8b49128c86af5f48189 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-product-supplier-row','data' => ['row' => $row,'canManage' => $canManage,'canEditSupplier' => $canEditSupplier,'selectedKeys' => $selectedKeys]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-product-supplier-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row),'can-manage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canManage),'can-edit-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditSupplier),'selected-keys' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedKeys)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5477f3d0f718b8b49128c86af5f48189)): ?>
<?php $attributes = $__attributesOriginal5477f3d0f718b8b49128c86af5f48189; ?>
<?php unset($__attributesOriginal5477f3d0f718b8b49128c86af5f48189); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5477f3d0f718b8b49128c86af5f48189)): ?>
<?php $component = $__componentOriginal5477f3d0f718b8b49128c86af5f48189; ?>
<?php unset($__componentOriginal5477f3d0f718b8b49128c86af5f48189); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                    </table>
                </div>
                <footer class="ft-rfq-px-product-note">These invitations request pricing only for <?php echo e($group['name'] ?? 'this product'); ?>.</footer>
            </div>
        <?php else: ?>
            <div class="ft-rfq-px-product-empty">
                <span>No suppliers match the current filters for this product.</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage && $productId > 0): ?>
                    <button type="button" wire:click="openRfqSupplierPicker(<?php echo e($productId); ?>)">Add supplier</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-product-group.blade.php ENDPATH**/ ?>