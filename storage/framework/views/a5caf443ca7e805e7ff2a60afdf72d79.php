<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'workspace' => [],
    'canManage' => false,
    'canEditSuppliers' => false,
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
    'workspace' => [],
    'canManage' => false,
    'canEditSuppliers' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $stats = $workspace['stats'] ?? [];
    $groups = collect($workspace['groups'] ?? []);
    $selectedCount = (int) ($workspace['selected_count'] ?? 0);
    $selectedProductCount = (int) ($workspace['selected_product_count'] ?? 0);
    $filteredProductCount = (int) ($workspace['filtered_product_count'] ?? 0);
    $currentPage = (int) ($workspace['current_page'] ?? 1);
    $lastPage = (int) ($workspace['last_page'] ?? 1);
?>

<section class="ft-rfq-px-workspace" aria-labelledby="rfq-product-workspace-title">
    <header class="ft-rfq-px-head">
        <div>
            <h2 id="rfq-product-workspace-title">Product RFQ invitations</h2>
            <p>Invite suppliers and track quotation responses separately for each product.</p>
        </div>
    </header>

    <div class="ft-rfq-px-stats" aria-label="RFQ summary">
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

    <div class="ft-rfq-px-toolbar" role="search" aria-label="Filter product RFQ invitations">
        <label class="ft-rfq-px-search">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path></svg>
            <input type="search" wire:model.live.debounce.300ms="rfqTableSearch" placeholder="Search products or suppliers" autocomplete="off">
        </label>

        <label class="ft-rfq-px-filter">
            <select wire:model.live="rfqEmailStatusFilter">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($workspace['status_filter_options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 9.5 5 5 5-5"></path></svg>
        </label>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
        <button type="button" class="ft-rfq-px-settings" wire:click="openRfqSettings">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 8.5 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 8.5a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15.5 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.12.36.33.69.6 1 .3.27.68.42 1.09.4H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51.6Z"></path></svg>
            <span>RFQ settings</span>
        </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCount > 0): ?>
        <div class="ft-rfq-px-selection">
            <div>
                <span class="ft-rfq-px-selection-check">✓</span>
                <strong><?php echo e($selectedCount); ?> <?php echo e(\Illuminate\Support\Str::plural('invitation', $selectedCount)); ?> selected across <?php echo e($selectedProductCount); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $selectedProductCount)); ?></strong>
            </div>
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                    <button type="button" wire:click="sendSelectedRfqEmails" wire:loading.attr="disabled" wire:target="sendSelectedRfqEmails">
                        <span wire:loading.remove wire:target="sendSelectedRfqEmails">Send <?php echo e($selectedCount); ?> invitations</span>
                        <span wire:loading wire:target="sendSelectedRfqEmails">Sending…</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" class="is-clear" wire:click="clearRfqSelection">Clear selection</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-rfq-px-products">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalca34d7b7c80d829dce3be290c6152b82 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca34d7b7c80d829dce3be290c6152b82 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-product-group','data' => ['group' => $group,'canManage' => $canManage,'canEditSupplier' => $canEditSuppliers,'selectedKeys' => $workspace['selected_keys'] ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-product-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['group' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group),'can-manage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canManage),'can-edit-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditSuppliers),'selected-keys' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workspace['selected_keys'] ?? [])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca34d7b7c80d829dce3be290c6152b82)): ?>
<?php $attributes = $__attributesOriginalca34d7b7c80d829dce3be290c6152b82; ?>
<?php unset($__attributesOriginalca34d7b7c80d829dce3be290c6152b82); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca34d7b7c80d829dce3be290c6152b82)): ?>
<?php $component = $__componentOriginalca34d7b7c80d829dce3be290c6152b82; ?>
<?php unset($__componentOriginalca34d7b7c80d829dce3be290c6152b82); ?>
<?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-rfq-px-empty">
                <strong>No product RFQ invitations found</strong>
                <span>Try another product, supplier or invitation-status filter.</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <footer class="ft-rfq-px-footer">
        <div class="ft-rfq-px-footer-note">
            <span>i</span>
            <p>Each invitation and supplier quotation is linked to one product. The same supplier can be invited for multiple products independently.</p>
        </div>
        <div class="ft-rfq-px-pagination">
            <span>Showing <?php echo e($filteredProductCount); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $filteredProductCount)); ?></span>
            <button type="button" wire:click="setRfqTablePage(<?php echo e(max(1, $currentPage - 1)); ?>)" <?php if(! ($workspace['has_previous'] ?? false)): echo 'disabled'; endif; ?> aria-label="Previous product page">‹</button>
            <span class="is-current"><?php echo e($currentPage); ?></span>
            <button type="button" wire:click="setRfqTablePage(<?php echo e(min($lastPage, $currentPage + 1)); ?>)" <?php if(! ($workspace['has_next'] ?? false)): echo 'disabled'; endif; ?> aria-label="Next product page">›</button>
        </div>
    </footer>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-product-workspace.blade.php ENDPATH**/ ?>