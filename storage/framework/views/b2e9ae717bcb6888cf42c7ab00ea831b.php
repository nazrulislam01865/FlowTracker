<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'item' => [],
    'index' => 0,
    'detail' => null,
    'defaultSupplier' => null,
    'rfqState' => [],
    'rfqSuppliers' => collect(),
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
    'item' => [],
    'index' => 0,
    'detail' => null,
    'defaultSupplier' => null,
    'rfqState' => [],
    'rfqSuppliers' => collect(),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $name = (string) ($detail?->name ?? ($item['product'] ?? 'Product'));
    $code = (string) ($detail?->productDisplayCode() ?? ($detail?->code ?? ''));
    $imageUrl = $detail?->productImageUrl();
    $supplierCount = collect($rfqSuppliers)->count();
    $sendOnCreate = (bool) ($rfqState['send_on_create'] ?? true);
    $quantityError = $errors->first("createProductRows.$index.quantity");
    $dueError = $errors->first("createProductRfqRows.$index.due_date");
    $messageError = $errors->first("createProductRfqRows.$index.message");
    $supplierError = $errors->first("createProductRfqRows.$index.supplier_ids");
?>

<article class="ft-ipr-card">
    <div class="ft-ipr-card-head">
        <div class="ft-ipr-product-main">
            <span class="ft-ipr-product-image">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl): ?>
                    <img src="<?php echo e($imageUrl); ?>" alt="" loading="lazy" decoding="async" data-ft-image-fallback="icon">
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="ft-ipr-product-copy">
                <strong><?php echo e($name); ?></strong>
                <small>Product code <?php echo e($code ?: '—'); ?></small>
            </span>
        </div>

        <div class="ft-ipr-quantity-wrap">
            <label for="inquiry-product-qty-<?php echo e($index); ?>">Quantity</label>
            <div class="ft-ipr-quantity-controls">
                <input id="inquiry-product-qty-<?php echo e($index); ?>" type="number" min="1" max="999999999" wire:model.live.debounce.300ms="createProductRows.<?php echo e($index); ?>.quantity" aria-label="Quantity for <?php echo e($name); ?>">
                <select wire:model.live="createProductRows.<?php echo e($index); ?>.unit" aria-label="Unit for <?php echo e($name); ?>">
                    <option value="units">Units</option>
                    <option value="pcs">Pieces</option>
                    <option value="sets">Sets</option>
                    <option value="pairs">Pairs</option>
                </select>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantityError): ?><small class="validation-error"><?php echo e($quantityError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-ipr-card-status">
            <span class="ft-ipr-supplier-count"><?php echo e($supplierCount); ?> <?php echo e(\Illuminate\Support\Str::plural('supplier', $supplierCount)); ?></span>
            <span class="ft-ipr-send-badge <?php echo e($sendOnCreate ? 'is-send' : 'is-draft'); ?>"><?php echo e($sendOnCreate ? 'Invite on create' : 'Draft only'); ?></span>
        </div>

        <div class="ft-ipr-card-actions">
            <button type="button" class="ft-ipr-duplicate" wire:click="duplicateCreateProductRow(<?php echo e($index); ?>)" aria-label="Duplicate <?php echo e($name); ?>">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="6" y="6" width="9" height="9" rx="1.5"/><path d="M4 12H3.5A1.5 1.5 0 0 1 2 10.5v-7A1.5 1.5 0 0 1 3.5 2h7A1.5 1.5 0 0 1 12 3.5V4"/></svg>
                <span>Duplicate</span>
            </button>
            <button type="button" class="ft-ipr-delete" wire:click="removeCreateProductRow(<?php echo e($index); ?>)" aria-label="Remove <?php echo e($name); ?>">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3.5 5.5h13M8 8.5v5M12 8.5v5M6 5.5l.6 10h6.8l.6-10M7.5 5.5l.7-2h3.6l.7 2"/></svg>
            </button>
        </div>
    </div>

    <div class="ft-ipr-card-body">
        <section class="ft-ipr-supplier-pane">
            <h3>Suppliers for this product</h3>
            <div class="ft-ipr-supplier-picker" x-data>
                <div x-ref="supplierPicker">
                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-ipr-supplier-search-select','label' => 'Supplier','type' => 'suppliers','context' => 'create-inquiry','property' => 'create-product-rfq-supplier:'.e($index).'','value' => '','placeholder' => 'Search and add suppliers','searchPlaceholder' => 'Search supplier name or email','selectedLabel' => null,'clearable' => false,'action' => 'addCreateProductRfqSupplierFromSelector','hideLabel' => true,'fixedMenu' => true,'menuWidth' => 420,'wire:key' => 'create-product-rfq-picker-'.e($index).'-'.e($supplierCount).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-ipr-supplier-search-select','label' => 'Supplier','type' => 'suppliers','context' => 'create-inquiry','property' => 'create-product-rfq-supplier:'.e($index).'','value' => '','placeholder' => 'Search and add suppliers','search-placeholder' => 'Search supplier name or email','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'action' => 'addCreateProductRfqSupplierFromSelector','hide-label' => true,'fixed-menu' => true,'menu-width' => 420,'wire:key' => 'create-product-rfq-picker-'.e($index).'-'.e($supplierCount).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                </div>

                <div class="ft-ipr-supplier-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rfqSuppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $supplierName = (string) data_get($supplier, 'name', 'Supplier');
                            $supplierEmail = trim((string) data_get($supplier, 'email', ''));
                            $words = preg_split('/\s+/u', trim($supplierName)) ?: [];
                            $initials = strtoupper(mb_substr(implode('', array_map(fn ($word) => mb_substr($word, 0, 1), $words)), 0, 2)) ?: 'S';
                            $emailReady = (bool) data_get($supplier, 'email_ready', false);
                        ?>
                        <div class="ft-ipr-supplier-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-product-rfq-selected-'.e($index).'-'.e((int) data_get($supplier, 'id')).''; ?>wire:key="create-product-rfq-selected-<?php echo e($index); ?>-<?php echo e((int) data_get($supplier, 'id')); ?>">
                            <span class="ft-ipr-supplier-avatar"><?php echo e($initials); ?></span>
                            <span class="ft-ipr-supplier-copy">
                                <strong><?php echo e($supplierName); ?></strong>
                                <small><?php echo e($supplierEmail !== '' ? $supplierEmail : 'No email configured'); ?></small>
                            </span>
                            <span class="ft-ipr-email-badge <?php echo e($emailReady ? '' : 'is-muted'); ?>"><?php echo e($emailReady ? 'Email ready' : 'No email'); ?></span>
                            <button type="button" wire:click="removeCreateProductRfqSupplier(<?php echo e($index); ?>, <?php echo e((int) data_get($supplier, 'id')); ?>)">Remove</button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-ipr-no-supplier">No supplier selected yet.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <button type="button" class="ft-ipr-add-supplier" x-on:click="$refs.supplierPicker?.querySelector('[aria-haspopup=listbox]')?.click()">
                    <span aria-hidden="true">+</span> Add supplier
                </button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierError): ?><small class="validation-error"><?php echo e($supplierError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <p class="ft-ipr-product-supplier-note">These suppliers will receive an RFQ only for <?php echo e($name); ?>.</p>
        </section>

        <section class="ft-ipr-rfq-pane">
            <h3>Product RFQ settings</h3>
            <label class="ft-ipr-rfq-field">
                <span>Quotation due</span>
                <input type="date" wire:model.live="createProductRfqRows.<?php echo e($index); ?>.due_date">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dueError): ?><small class="validation-error"><?php echo e($dueError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
            <label class="ft-ipr-rfq-field">
                <span>Message</span>
                <textarea rows="3" wire:model.live.debounce.350ms="createProductRfqRows.<?php echo e($index); ?>.message"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messageError): ?><small class="validation-error"><?php echo e($messageError); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <label class="ft-ipr-toggle-row">
                <input type="checkbox" wire:model.live="createProductRfqRows.<?php echo e($index); ?>.send_on_create">
                <span class="ft-ipr-toggle" aria-hidden="true"><i></i></span>
                <b>Send invitations when inquiry is created</b>
            </label>
            <p class="ft-ipr-send-summary">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sendOnCreate): ?>
                    <strong><?php echo e($supplierCount); ?></strong> <?php echo e(\Illuminate\Support\Str::plural('invitation', $supplierCount)); ?> will be sent
                <?php else: ?>
                    Invitation saved as draft
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </section>
    </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/create-product-rfq-card.blade.php ENDPATH**/ ?>