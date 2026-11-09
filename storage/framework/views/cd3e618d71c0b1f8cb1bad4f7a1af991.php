<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['task' => null, 'payload' => [], 'preview' => [], 'standalone' => false]));

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

foreach (array_filter((['task' => null, 'payload' => [], 'preview' => [], 'standalone' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $poRows = (array) ($preview['rows'] ?? []);
    $poProducts = (array) ($preview['products'] ?? []);
    $generated = count($poRows) > 0;
    $mode = $payload['supplier_po_mode'] ?? 'single';
    $selectedItemIds = array_map('intval', (array) ($payload['supplier_po_item_ids'] ?? []));
    $selectedPoId = (int) ($payload['supplier_po_selected_id'] ?? ($poRows[0]['id'] ?? 0));
    $selectedPo = collect($poRows)->firstWhere('id', $selectedPoId) ?: ($poRows[0] ?? null);
?>
<div class="ft-supplier-po" aria-label="Supplier purchase orders">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($generated)): ?>
        <p class="ft-prototype-modal-copy">Choose how you want to create the Purchase Order for this order.</p>
        <div class="ft-prototype-choice-grid ft-supplier-po-choices" role="group" aria-label="Purchase Order type">
            <button type="button" class="ft-supplier-po-option <?php echo e($mode === 'single' ? 'is-selected' : ''); ?>" wire:click="chooseSupplierPurchaseOrderMode('single')" aria-pressed="<?php echo e($mode === 'single' ? 'true' : 'false'); ?>">
                <span class="ft-supplier-po-option-symbol" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h8"/></svg>
                </span>
                <span class="ft-supplier-po-option-label"><strong>Single Purchase Order</strong><small>One PDF with all <?php echo e(count($poProducts)); ?> <?php echo e(count($poProducts) === 1 ? 'product' : 'products'); ?></small></span>
                <span class="ft-supplier-po-option-check" aria-hidden="true">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'single'): ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="m5 12 4 4L19 6"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </button>
            <button type="button" class="ft-supplier-po-option <?php echo e($mode === 'split' ? 'is-selected' : ''); ?>" wire:click="chooseSupplierPurchaseOrderMode('split')" aria-pressed="<?php echo e($mode === 'split' ? 'true' : 'false'); ?>" <?php if(count($poProducts) < 2): ?> disabled title="At least two products are needed to split a PO" <?php endif; ?>>
                <span class="ft-supplier-po-option-symbol" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="11" height="15" rx="1.5"/><path d="M8 8h4M8 12h4M17 8h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2v-1"/></svg>
                </span>
                <span class="ft-supplier-po-option-label"><strong>Split Purchase Orders</strong><small>Separate PDFs for selected products</small></span>
                <span class="ft-supplier-po-option-check" aria-hidden="true">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'split'): ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="m5 12 4 4L19 6"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </button>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'split'): ?>
            <section class="ft-supplier-po-product-section" aria-label="Select products to split">
                <div class="ft-supplier-po-section-heading">
                    <strong>Which products need separate POs?</strong>
                    <span><?php echo e(count($selectedItemIds)); ?> of <?php echo e(count($poProducts)); ?> selected</span>
                </div>
                <p>Select the products that should each have their own PO. Other products will be grouped by supplier automatically.</p>
                <div class="ft-supplier-po-product-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $poProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'supplier-po-select-'.e($product['id']).''; ?>wire:key="supplier-po-select-<?php echo e($product['id']); ?>" class="ft-supplier-po-product <?php echo e(in_array((int) $product['id'], $selectedItemIds, true) ? 'is-selected' : ''); ?>">
                            <input type="checkbox" value="<?php echo e($product['id']); ?>" wire:model.live="orderWorkflowActionPayload.supplier_po_item_ids">
                            <span class="ft-supplier-po-product-name"><b><?php echo e($product['name']); ?></b><small><?php echo e(number_format($product['quantity'])); ?> pcs · <?php echo e($product['supplier']); ?></small></span>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.supplier_po_item_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <details class="ft-supplier-po-extra">
            <summary><span><strong>Carton marks &amp; payment details</strong><small>Optional — leave anything unknown blank</small></span><span class="ft-supplier-po-chevron" aria-hidden="true">⌄</span></summary>
            <div class="ft-supplier-po-extra-body">
                <div class="ft-supplier-po-extra-heading"><strong>4. Carton marks requirement</strong><small>These values appear in section 4 of the supplied Excel template.</small></div>
                <div class="ft-supplier-po-extra-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['carton_buyer' => 'Carton buyer', 'carton_style' => 'Style number', 'carton_colour' => 'Colour', 'carton_size' => 'Size', 'carton_gross_weight' => 'Gross weight (kg)', 'carton_net_weight' => 'Net weight (kg)', 'carton_measurement' => 'Carton measurement (cm)', 'carton_other' => 'Other carton notes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="ft-supplier-po-extra-field"><span><?php echo e($label); ?></span><input type="text" maxlength="255" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.<?php echo e($field); ?>" placeholder="Optional"></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <label class="ft-supplier-po-extra-field"><span>Carton mark quantity (PCS)</span><input type="text" maxlength="20" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.carton_qty" placeholder="Leave blank if unknown"></label>
                    <label class="ft-supplier-po-extra-field"><span>Mark type</span><select wire:model="orderWorkflowActionPayload.supplier_po_template.carton_mark_type"><option value="">Not specified</option><option value="side">Side mark only</option><option value="two">Two sides</option><option value="four">Four sides</option><option value="barcode">Barcode mark</option><option value="fragile">Fragile / this side up</option></select></label>
                    <label class="ft-supplier-po-extra-field"><span>Outer carton strength</span><select wire:model="orderWorkflowActionPayload.supplier_po_template.carton_strength"><option value="">Not specified</option><option value="5ply">5-ply</option><option value="3ply">3-ply</option><option value="export">Strong export carton</option></select></label>
                    <label class="ft-supplier-po-extra-field"><span>Inner packing</span><select wire:model="orderWorkflowActionPayload.supplier_po_template.inner_packing"><option value="">Not specified</option><option value="polybag">Polybag per piece</option><option value="tissue">Tissue paper</option><option value="desiccant">Desiccant</option></select></label>
                    <label class="ft-supplier-po-extra-field"><span>Pieces per inner carton</span><input type="text" maxlength="20" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.pieces_per_inner" placeholder="Optional"></label>
                    <label class="ft-supplier-po-extra-field"><span>Outer carton other</span><input type="text" maxlength="100" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.carton_strength_other" placeholder="Optional"></label>
                    <label class="ft-supplier-po-extra-field"><span>Inner packing other</span><input type="text" maxlength="100" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.inner_packing_other" placeholder="Optional"></label>
                </div>
                <div class="ft-supplier-po-extra-heading"><strong>5. Payment method</strong><small>Currency is taken from the Order. Leave unspecified terms and bank details blank.</small></div>
                <div class="ft-supplier-po-extra-grid">
                    <label class="ft-supplier-po-extra-field"><span>Payment method</span><select wire:model.live="orderWorkflowActionPayload.supplier_po_template.payment_method"><option value="">Not specified</option><option value="tt">T/T (bank transfer)</option><option value="lc">L/C at sight</option><option value="dp">D/P</option><option value="other">Other</option></select></label>
                    <label class="ft-supplier-po-extra-field"><span>Payment terms</span><select wire:model="orderWorkflowActionPayload.supplier_po_template.payment_term"><option value="">Not specified</option><option value="30_70">30% deposit / 70% before shipment</option><option value="50_50">50% / 50%</option><option value="net30">Net 30 days</option><option value="net60">Net 60 days</option></select></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($payload,'supplier_po_template.payment_method') === 'tt'): ?>
                        <label class="ft-supplier-po-extra-field"><span>Deposit (%)</span><input type="text" maxlength="5" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.deposit_percent" placeholder="Optional"></label>
                        <label class="ft-supplier-po-extra-field"><span>Balance (%)</span><input type="text" maxlength="5" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.balance_percent" placeholder="Optional"></label>
                    <?php elseif(data_get($payload,'supplier_po_template.payment_method') === 'other'): ?>
                        <label class="ft-supplier-po-extra-field"><span>Other payment method</span><input type="text" maxlength="100" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.payment_other" placeholder="Specify method"></label>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['beneficiary' => 'Beneficiary', 'bank_name' => 'Bank name', 'bank_address' => 'Bank address', 'account_number' => 'Account number', 'swift_code' => 'SWIFT code']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="ft-supplier-po-extra-field"><span><?php echo e($label); ?></span><input type="text" maxlength="255" wire:model.blur="orderWorkflowActionPayload.supplier_po_template.<?php echo e($field); ?>" placeholder="Optional"></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <p class="ft-supplier-po-extra-hint">The supplied PO template stays unchanged. Unknown carton, payment, and bank details remain blank in the PDF.</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.supplier_po_template'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </details>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.supplier_po_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <footer class="ft-supplier-po-actions">
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','size' => 'sm','wire:click' => 'closeOrderWorkflowAction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 'sm','wire:click' => 'closeOrderWorkflowAction']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'primary','size' => 'sm','wire:click' => 'createSupplierPurchaseOrders','wire:loading.attr' => 'disabled','wire:target' => 'createSupplierPurchaseOrders','disabled' => $mode === 'split' && count($selectedItemIds) === 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','size' => 'sm','wire:click' => 'createSupplierPurchaseOrders','wire:loading.attr' => 'disabled','wire:target' => 'createSupplierPurchaseOrders','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'split' && count($selectedItemIds) === 0)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <span wire:loading.remove wire:target="createSupplierPurchaseOrders">Generate <?php echo e($mode === 'split' ? 'Split POs' : 'Single PO'); ?></span>
                <span wire:loading wire:target="createSupplierPurchaseOrders">Generating...</span>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
        </footer>
    <?php else: ?>
        <div class="ft-supplier-po-ready"><span aria-hidden="true">✓</span><div><strong><?php echo e(count($poRows)); ?> Purchase Order<?php echo e(count($poRows) === 1 ? '' : 's'); ?> ready</strong><small>PDF<?php echo e(count($poRows) === 1 ? '' : 's'); ?> saved under this order. Email is optional; you can finish without sending.</small></div></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($poRows) > 1): ?>
            <nav class="ft-supplier-po-files" aria-label="Generated Purchase Orders">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $poRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button type="button" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'supplier-po-tab-'.e($po['id']).''; ?>wire:key="supplier-po-tab-<?php echo e($po['id']); ?>" wire:click="selectSupplierPurchaseOrder(<?php echo e($po['id']); ?>)" class="ft-supplier-po-file <?php echo e((int) $selectedPo['id'] === (int) $po['id'] ? 'is-active' : ''); ?>" aria-current="<?php echo e((int) $selectedPo['id'] === (int) $po['id'] ? 'true' : 'false'); ?>">
                        <span class="ft-supplier-po-file-icon" aria-hidden="true">PDF</span><span><b><?php echo e($po['number']); ?></b><small><?php echo e($po['supplier']); ?></small></span>
                        <span class="ft-supplier-po-dot <?php echo e($po['status'] === 'sent' ? 'is-sent' : ($po['status'] === 'failed' ? 'is-failed' : '')); ?>" aria-label="<?php echo e($po['status'] === 'sent' ? 'Sent' : ($po['status'] === 'failed' ? 'Failed' : 'Not sent')); ?>"></span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPo): ?>
            <div class="ft-supplier-po-send" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'supplier-po-email-'.e($selectedPo['id']).''; ?>wire:key="supplier-po-email-<?php echo e($selectedPo['id']); ?>">
                <div class="ft-supplier-po-document">
                    <div class="ft-supplier-po-pdf-preview"><span class="ft-supplier-po-pdf-badge">PDF</span><strong><?php echo e($selectedPo['pdf_name']); ?></strong><small>Purchase Order · <?php echo e($selectedPo['number']); ?></small><span class="ft-supplier-po-pdf-caption"><?php echo e($selectedPo['status'] === 'sent' ? 'Email sent with this PDF attached.' : 'This PDF is attached automatically if you choose Send.'); ?></span></div>
                    <div class="ft-supplier-po-downloads">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPo['document_id']): ?>
                            <a href="<?php echo e(route('documents.open', $selectedPo['document_id'])); ?>" target="_blank" rel="noopener">Open PDF ↗</a>
                            <a href="<?php echo e(route('documents.download', $selectedPo['document_id'])); ?>">Download</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="ft-supplier-po-email-details">
                    <div class="ft-supplier-po-meta"><div><span>SUPPLIER</span><strong><?php echo e($selectedPo['supplier']); ?></strong></div><div><span>STATUS</span><strong class="<?php echo e($selectedPo['status'] === 'sent' ? 'ft-supplier-po-sent-text' : ''); ?>"><?php echo e($selectedPo['status'] === 'sent' ? 'Sent' : ($selectedPo['status'] === 'failed' ? 'Failed - retry' : 'Not sent')); ?></strong></div></div>
                    <div class="ft-supplier-po-meta ft-supplier-po-items"><div><span>PRODUCTS</span><strong><?php echo e($selectedPo['items']); ?></strong></div></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPo['status'] !== 'sent' && ($standalone || !$task->completed_at || $selectedPo['status'] === 'failed')): ?>
                    <section class="ft-supplier-po-recipient-section" aria-label="Email recipients">
                        <strong>To <small>(optional)</small></strong>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = (array) ($payload['supplier_po_recipients'][$selectedPo['id']] ?? ['']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipientKey => $recipientEmail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-supplier-po-recipient" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'supplier-po-recipient-'.e($selectedPo['id']).'-'.e($recipientKey).''; ?>wire:key="supplier-po-recipient-<?php echo e($selectedPo['id']); ?>-<?php echo e($recipientKey); ?>">
                                <input type="email" inputmode="email" maxlength="254" wire:model.blur="orderWorkflowActionPayload.supplier_po_recipients.<?php echo e($selectedPo['id']); ?>.<?php echo e($recipientKey); ?>" value="<?php echo e($recipientEmail); ?>" placeholder="Enter email address" aria-label="Recipient <?php echo e($loop->iteration); ?>" autocomplete="off" autocapitalize="off" spellcheck="false">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($loop->first)): ?><button type="button" wire:click="removeSupplierPoRecipient(<?php echo e($selectedPo['id']); ?>, <?php echo e($recipientKey); ?>)" aria-label="Remove recipient">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <button type="button" class="ft-supplier-po-add-recipient" wire:click="addSupplierPoRecipient(<?php echo e($selectedPo['id']); ?>)" <?php if(count((array) ($payload['supplier_po_recipients'][$selectedPo['id']] ?? [])) >= 20): ?> disabled <?php endif; ?>>+ Add recipient</button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderWorkflowActionPayload.supplier_po_emails.'.$selectedPo['id']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>
                    <?php elseif($selectedPo['status'] === 'sent'): ?>
                        <p class="ft-supplier-po-delivered" role="status">Email sent successfully<?php echo e($selectedPo['sent_at'] ? ' · '.$selectedPo['sent_at'] : ''); ?>. No resend is needed.</p>
                    <?php else: ?>
                        <p class="ft-supplier-po-delivered">This PO PDF is available above.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ft-supplier-po-message"><span>SUBJECT</span><p><?php echo e($selectedPo['subject']); ?></p><span>EMAIL BODY</span><div class="ft-supplier-po-body"><?php echo e($selectedPo['body']); ?></div></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPo['status'] !== 'sent' && ($standalone || !$task->completed_at || $selectedPo['status'] === 'failed')): ?>
                        <div class="ft-supplier-po-send-action">
                            <small><?php echo e($selectedPo['status'] === 'failed' ? 'Delivery failed. You can retry or finish without email.' : 'Add a recipient to send, or finish without email.'); ?></small>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'primary','wire:click' => 'sendSupplierPurchaseOrder('.e($selectedPo['id']).')','wire:loading.attr' => 'disabled','wire:target' => 'sendSupplierPurchaseOrder('.e($selectedPo['id']).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','wire:click' => 'sendSupplierPurchaseOrder('.e($selectedPo['id']).')','wire:loading.attr' => 'disabled','wire:target' => 'sendSupplierPurchaseOrder('.e($selectedPo['id']).')']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <span wire:loading.remove wire:target="sendSupplierPurchaseOrder(<?php echo e($selectedPo['id']); ?>)"><?php echo e($selectedPo['status'] === 'failed' ? 'Resend PO' : 'Send PO'); ?></span>
                                <span wire:loading wire:target="sendSupplierPurchaseOrder(<?php echo e($selectedPo['id']); ?>)">Sending...</span>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <footer class="ft-supplier-po-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($standalone || !$task->completed_at): ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','wire:click' => 'closeOrderWorkflowAction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','wire:click' => 'closeOrderWorkflowAction']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Close <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($poRows) > 1 && collect($poRows)->contains(fn (array $po) => in_array($po['status'], ['not_sent', 'failed'], true))): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','wire:click' => 'sendAllSupplierPurchaseOrders','wire:loading.attr' => 'disabled','wire:target' => 'sendAllSupplierPurchaseOrders']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','wire:click' => 'sendAllSupplierPurchaseOrders','wire:loading.attr' => 'disabled','wire:target' => 'sendAllSupplierPurchaseOrders']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Send all POs <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'primary','wire:click' => 'completeSupplierPurchaseOrdersWithoutEmail','wire:loading.attr' => 'disabled','wire:target' => 'completeSupplierPurchaseOrdersWithoutEmail']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','wire:click' => 'completeSupplierPurchaseOrdersWithoutEmail','wire:loading.attr' => 'disabled','wire:target' => 'completeSupplierPurchaseOrdersWithoutEmail']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <span wire:loading.remove wire:target="completeSupplierPurchaseOrdersWithoutEmail"><?php echo e($standalone ? 'Finish without email' : (collect($poRows)->contains(fn (array $po) => $po['status'] === 'sent') ? 'Complete (skip remaining emails)' : 'Complete without email')); ?></span>
                    <span wire:loading wire:target="completeSupplierPurchaseOrdersWithoutEmail">Finishing...</span>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','wire:click' => 'closeOrderWorkflowAction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','wire:click' => 'closeOrderWorkflowAction']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Done <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </footer>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/supplier-purchase-order-panel.blade.php ENDPATH**/ ?>