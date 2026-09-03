<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'suppliers' => collect(),
    'selectedSuppliers' => collect(),
    'selectedSupplierIds' => [],
    'supplierSearch' => '',
    'productCount' => 0,
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
    'suppliers' => collect(),
    'selectedSuppliers' => collect(),
    'selectedSupplierIds' => [],
    'supplierSearch' => '',
    'productCount' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $selectedIds = collect($selectedSupplierIds)->map(fn ($id) => (int) $id)->filter()->unique()->values();
    $selectedCount = $selectedIds->count();
    $selectedSupplierRows = collect($selectedSuppliers)
        ->filter(fn ($supplier) => $selectedIds->contains((int) data_get($supplier, 'id')))
        ->values();
?>

<section <?php echo e($attributes->class(['ft-create-rfq-layout'])); ?> aria-labelledby="create-rfq-title">
    <div class="ft-create-rfq-card ft-create-rfq-card--suppliers">
        <header class="ft-create-rfq-head">
            <div class="ft-create-rfq-title-wrap">
                <div class="ft-create-rfq-title-line">
                    <h2 id="create-rfq-title">Invite suppliers to the RFQ</h2>
                    <span class="ft-create-rfq-optional">Optional</span>
                </div>
                <p>You may invite one or two now, or do this later from the RFQ page.</p>
            </div>
            <span class="ft-create-rfq-selected-pill"><?php echo e($selectedCount); ?> selected</span>
        </header>

        <div class="ft-create-rfq-body">
            <label class="ft-create-rfq-search-label" for="create-rfq-supplier-search">Search suppliers</label>
            <div class="ft-create-rfq-search">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="5.5"></circle>
                    <path d="m15.5 15.5 4 4"></path>
                </svg>
                <input
                    id="create-rfq-supplier-search"
                    type="search"
                    wire:model.live.debounce.300ms="createRfqSupplierSearch"
                    placeholder="Search supplier name, category or email"
                    autocomplete="off"
                >
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createRfqSupplierIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-create-rfq-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-create-rfq-supplier-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $supplierId = (int) ($supplier['id'] ?? 0); ?>
                    <?php if (isset($component)) { $__componentOriginalf18b4b3029b3d4a8e7f215ecce0bda37 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf18b4b3029b3d4a8e7f215ecce0bda37 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-supplier-choice','data' => ['supplier' => $supplier,'selected' => $selectedIds->contains($supplierId),'wire:key' => 'create-rfq-supplier-'.e($supplierId).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-supplier-choice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedIds->contains($supplierId)),'wire:key' => 'create-rfq-supplier-'.e($supplierId).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf18b4b3029b3d4a8e7f215ecce0bda37)): ?>
<?php $attributes = $__attributesOriginalf18b4b3029b3d4a8e7f215ecce0bda37; ?>
<?php unset($__attributesOriginalf18b4b3029b3d4a8e7f215ecce0bda37); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf18b4b3029b3d4a8e7f215ecce0bda37)): ?>
<?php $component = $__componentOriginalf18b4b3029b3d4a8e7f215ecce0bda37; ?>
<?php unset($__componentOriginalf18b4b3029b3d4a8e7f215ecce0bda37); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-create-rfq-empty">
                        <?php echo e(trim((string) $supplierSearch) !== '' ? 'No suppliers match your search.' : 'No suppliers are available in the Supplier list.'); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>

    <aside class="ft-create-rfq-card ft-create-rfq-card--settings" aria-labelledby="create-rfq-settings-title">
        <header class="ft-create-rfq-settings-head">
            <h2 id="create-rfq-settings-title">RFQ settings</h2>
        </header>
        <div class="ft-create-rfq-settings-body">
            <label class="ft-create-rfq-field">
                <span>Quotation due</span>
                <input type="date" wire:model="createRfqDueDate">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createRfqDueDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="ft-create-rfq-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <label class="ft-create-rfq-field">
                <span>Message</span>
                <textarea wire:model="createRfqMessage"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createRfqMessage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="ft-create-rfq-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <div class="ft-create-rfq-summary">
                <div><span>Products</span><strong><?php echo e(number_format((int) $productCount)); ?></strong></div>
                <div><span>Invite now</span><strong><?php echo e(number_format($selectedCount)); ?> <?php echo e(\Illuminate\Support\Str::plural('supplier', $selectedCount)); ?></strong></div>
                <div><span>If none<br>selected</span><strong>RFQ remains ready to invite</strong></div>
            </div>
        </div>
    </aside>
</section>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedSupplierRows->isNotEmpty()): ?>
    <section class="ft-create-rfq-selected-section" aria-labelledby="create-rfq-selected-title">
        <div class="ft-create-rfq-selected-head">
            <div>
                <h3 id="create-rfq-selected-title">Selected suppliers</h3>
                <p>These suppliers will be added to this Inquiry RFQ when you create the Inquiry.</p>
            </div>
            <span><?php echo e(number_format($selectedSupplierRows->count())); ?> <?php echo e(\Illuminate\Support\Str::plural('supplier', $selectedSupplierRows->count())); ?></span>
        </div>

        <div class="ft-create-rfq-selected-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedSupplierRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-selected-supplier-card','data' => ['supplier' => $supplier,'wire:key' => 'create-rfq-selected-supplier-'.e((int) data_get($supplier, 'id')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-selected-supplier-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier),'wire:key' => 'create-rfq-selected-supplier-'.e((int) data_get($supplier, 'id')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3)): ?>
<?php $attributes = $__attributesOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3; ?>
<?php unset($__attributesOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3)): ?>
<?php $component = $__componentOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3; ?>
<?php unset($__componentOriginalfa6f5adf4c1b6f60aba2a55514f3e9b3); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/create-rfq.blade.php ENDPATH**/ ?>