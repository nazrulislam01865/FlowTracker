<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['suppliers' => collect(), 'productCounts' => collect(), 'selectedSupplierId' => null, 'selectionCount' => 0]));

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

foreach (array_filter((['suppliers' => collect(), 'productCounts' => collect(), 'selectedSupplierId' => null, 'selectionCount' => 0]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php if (isset($component)) { $__componentOriginal5835968f22246c90d00dd620e450bc3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5835968f22246c90d00dd620e450bc3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.bulk-modal','data' => ['title' => 'Assign supplier','subtitle' => 'Choose one supplier for '.number_format($selectionCount).' selected '.\Illuminate\Support\Str::plural('product', $selectionCount).'. Existing supplier links will be kept.','saveLabel' => 'Assign supplier','saveAction' => 'applyBulkProductSupplier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.bulk-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Assign supplier','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Choose one supplier for '.number_format($selectionCount).' selected '.\Illuminate\Support\Str::plural('product', $selectionCount).'. Existing supplier links will be kept.'),'save-label' => 'Assign supplier','save-action' => 'applyBulkProductSupplier']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="ft-supplier-assign-picker" x-data="{ q: '' }">
        <label class="ft-supplier-assign-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" x-model="q" placeholder="Search suppliers" autocomplete="off" aria-label="Search suppliers">
        </label>

        <div class="ft-supplier-assign-options">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $selected = (int) $selectedSupplierId === (int) $supplier->id;
                    $name = trim((string) $supplier->name);
                    $initials = collect(preg_split('/\s+/', $name) ?: [])->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                    $contact = trim((string) data_get($supplier->metadata, 'contact_person'));
                    $count = (int) ($productCounts[(int) $supplier->id] ?? 0);
                    $searchText = mb_strtolower($name.' '.$contact.' '.(string) $supplier->code);
                ?>
                <button
                    type="button"
                    class="ft-supplier-assign-option <?php echo e($selected ? 'is-selected' : ''); ?>"
                    x-show="!q || <?php echo \Illuminate\Support\Js::from($searchText)->toHtml() ?>.includes(q.toLowerCase())"
                    wire:click="chooseBulkProductSupplier(<?php echo e($supplier->id); ?>)"
                    aria-pressed="<?php echo e($selected ? 'true' : 'false'); ?>"
                >
                    <span class="ft-supplier-assign-radio" aria-hidden="true"><i></i></span>
                    <span class="ft-supplier-assign-logo"><?php echo e($initials ?: 'S'); ?></span>
                    <span class="ft-supplier-assign-copy">
                        <b><?php echo e($supplier->name); ?></b>
                        <small><?php echo e($contact !== '' ? $contact.' · ' : ''); ?><?php echo e(number_format($count)); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $count)); ?></small>
                    </span>
                    <span class="ft-supplier-assign-status">Active</span>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-supplier-assign-empty"><strong>No active suppliers found</strong><span>Create or activate a supplier first.</span></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <div class="ft-supplier-assign-note">This adds the supplier to each selected product. Existing supplier links and each product's current default supplier are preserved.</div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bulkProductSupplierId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5835968f22246c90d00dd620e450bc3f)): ?>
<?php $attributes = $__attributesOriginal5835968f22246c90d00dd620e450bc3f; ?>
<?php unset($__attributesOriginal5835968f22246c90d00dd620e450bc3f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5835968f22246c90d00dd620e450bc3f)): ?>
<?php $component = $__componentOriginal5835968f22246c90d00dd620e450bc3f; ?>
<?php unset($__componentOriginal5835968f22246c90d00dd620e450bc3f); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/suppliers/assign-products-modal.blade.php ENDPATH**/ ?>