<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'candidates',
    'products' => [],
    'selectedProductId' => null,
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
    'candidates',
    'products' => [],
    'selectedProductId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $products = collect($products);
    $selectedProduct = $selectedProductId ? $products->firstWhere('id', (int) $selectedProductId) : null;
?>

<div class="ft-rfq-add-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rfq-add-supplier-modal'; ?>wire:key="rfq-add-supplier-modal" wire:click.self="closeRfqSupplierPicker" x-data x-on:keydown.escape.window="$wire.closeRfqSupplierPicker()">
    <section class="ft-rfq-add-modal" role="dialog" aria-modal="true" aria-labelledby="rfq-add-supplier-title">
        <header class="ft-rfq-add-modal-head">
            <div>
                <h2 id="rfq-add-supplier-title">Add supplier</h2>
                <p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct): ?>
                        Add an active supplier to <?php echo e($selectedProduct['name']); ?>. Sending the invitation remains a separate action.
                    <?php else: ?>
                        Choose a product, then add an active supplier to its RFQ.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
            <button type="button" wire:click="closeRfqSupplierPicker" aria-label="Close add supplier">×</button>
        </header>

        <div class="ft-rfq-add-modal-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedProduct): ?>
                <label class="ft-rfq-add-modal-product">
                    <span>Product</span>
                    <select wire:model.live.number="rfqSupplierProductId">
                        <option value="">Select product</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e((int) $product['id']); ?>">
                                <?php echo e($product['name']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($product['code'] ?? null)): ?> · <?php echo e($product['code']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </label>
            <?php else: ?>
                <div class="ft-rfq-add-modal-product-summary">
                    <span>Product</span>
                    <strong><?php echo e($selectedProduct['name']); ?></strong>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($selectedProduct['code'] ?? null)): ?><small><?php echo e($selectedProduct['code']); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <label class="ft-rfq-add-modal-search <?php echo e(! $selectedProductId ? 'is-disabled' : ''); ?>">
                <span class="sr-only">Search supplier directory</span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path></svg>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="rfqSupplierSearch"
                    placeholder="Search supplier name, category or email"
                    autocomplete="off"
                    <?php if(! $selectedProductId): echo 'disabled'; endif; ?>
                    <?php if($selectedProductId): ?> autofocus <?php endif; ?>
                >
            </label>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqSupplierSearch'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-rfq-add-modal-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-rfq-add-modal-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedProductId): ?>
                    <div class="ft-rfq-add-modal-empty">Select a product to view available suppliers.</div>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $name = (string) ($candidate['name'] ?? 'Supplier');
                            $parts = preg_split('/\s+/u', trim($name)) ?: [];
                            $initials = collect($parts)->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('') ?: '—';
                            $email = trim((string) ($candidate['email'] ?? ''));
                            $isActive = (bool) ($candidate['invitable'] ?? false);
                        ?>
                        <div class="ft-rfq-add-modal-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rfq-add-candidate-'.e($selectedProductId).'-'.e($candidate['id']).''; ?>wire:key="rfq-add-candidate-<?php echo e($selectedProductId); ?>-<?php echo e($candidate['id']); ?>">
                            <span class="ft-rfq-management-avatar"><?php echo e($initials); ?></span>
                            <div class="ft-rfq-add-modal-copy">
                                <strong><?php echo e($name); ?></strong>
                                <span><?php echo e($candidate['category'] ?? 'General supplier'); ?> · <?php echo e($email !== '' ? $email : 'No email configured'); ?></span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActive): ?>
                                <button type="button" wire:click="addRfqSupplier(<?php echo e((int) $candidate['id']); ?>)" wire:loading.attr="disabled" wire:target="addRfqSupplier(<?php echo e((int) $candidate['id']); ?>)">
                                    <span wire:loading.remove wire:target="addRfqSupplier(<?php echo e((int) $candidate['id']); ?>)">Add</span>
                                    <span wire:loading wire:target="addRfqSupplier(<?php echo e((int) $candidate['id']); ?>)">Adding…</span>
                                </button>
                            <?php else: ?>
                                <span class="ft-rfq-add-modal-unavailable"><?php echo e($candidate['unavailable_reason'] ?? 'Inactive'); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-rfq-add-modal-empty">No available suppliers match your search.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-add-supplier-modal.blade.php ENDPATH**/ ?>