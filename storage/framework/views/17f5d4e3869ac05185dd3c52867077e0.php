<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'invitation', 'token', 'quote', 'products', 'currency', 'contact', 'rfqReference', 'clientName',
    'documents' => [], 'locked' => false,
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
    'invitation', 'token', 'quote', 'products', 'currency', 'contact', 'rfqReference', 'clientName',
    'documents' => [], 'locked' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $inquiry = $invitation->inquiry;
    $quoteItems = collect($quote?->items ?? [])->keyBy('inquiry_item_id');
    $documentCollection = collect($documents);
    $dueLabel = $invitation->due_at?->format('M j, Y · g:i A') ?? 'No due date';
?>

<div class="ft-rfq-pricing-prototype">
    <form method="post" action="<?php echo e(route('rfq.public.respond', ['token' => $token])); ?>" id="rfq-pricing-form" class="ft-rfq-pricing-prototype__form" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <section class="ft-rfq-portal-card ft-rfq-prototype-card ft-rfq-prototype-details" aria-labelledby="rfq-pricing-details-title">
            <div class="ft-rfq-prototype-section-head">
                <span class="ft-rfq-prototype-section-number">1</span>
                <h2 id="rfq-pricing-details-title">RFQ and supplier details</h2>
            </div>

            <div class="ft-rfq-prototype-details-grid is-reference-row">
                <label class="ft-rfq-prototype-field is-readonly">
                    <span>Inquiry reference</span>
                    <span class="ft-rfq-prototype-control-with-icon">
                        <input type="text" value="<?php echo e($inquiry->inquiry_number); ?>" readonly tabindex="-1">
                        <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'lock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
                    </span>
                </label>
                <label class="ft-rfq-prototype-field is-readonly">
                    <span>RFQ reference</span>
                    <span class="ft-rfq-prototype-control-with-icon">
                        <input type="text" value="<?php echo e($rfqReference); ?>" readonly tabindex="-1">
                        <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'lock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
                    </span>
                </label>
                <label class="ft-rfq-prototype-field is-readonly">
                    <span>Requested by</span>
                    <span class="ft-rfq-prototype-control-with-icon">
                        <input type="text" value="<?php echo e($clientName); ?>" readonly tabindex="-1">
                        <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'lock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
                    </span>
                </label>
                <label class="ft-rfq-prototype-field is-readonly">
                    <span>Quotation due</span>
                    <span class="ft-rfq-prototype-control-with-icon">
                        <input type="text" value="<?php echo e($dueLabel); ?>" readonly tabindex="-1">
                        <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'lock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
                    </span>
                </label>
            </div>

            <div class="ft-rfq-prototype-details-grid is-contact-row">
                <label class="ft-rfq-prototype-field">
                    <span>Supplier company <b>*</b></span>
                    <input type="text" value="<?php echo e($invitation->supplier?->name ?: '—'); ?>" readonly tabindex="-1">
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Contact person <b>*</b></span>
                    <input type="text" name="supplier_contact_name" value="<?php echo e(old('supplier_contact_name', $contact['name'] ?? '')); ?>" required <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Email <b>*</b></span>
                    <input type="email" name="supplier_contact_email" value="<?php echo e(old('supplier_contact_email', $contact['email'] ?? '')); ?>" required <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <div class="ft-rfq-prototype-phone-cell">
                    <label class="ft-rfq-prototype-field">
                        <span>Phone <b>*</b></span>
                        <input type="text" name="supplier_contact_phone" value="<?php echo e(old('supplier_contact_phone', $contact['phone'] ?? '')); ?>" <?php if($locked): echo 'disabled'; endif; ?>>
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?>
                        <button type="button" class="ft-rfq-edit-contact-link" data-rfq-edit-contact>
                            <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'pencil']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pencil']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?> Edit contact details
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if (isset($component)) { $__componentOriginalc717d07ccd4458ff48b6c7281e618771 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc717d07ccd4458ff48b6c7281e618771 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.product-to-quote','data' => ['invitation' => $invitation,'token' => $token,'products' => $products]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.product-to-quote'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['invitation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invitation),'token' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($token),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc717d07ccd4458ff48b6c7281e618771)): ?>
<?php $attributes = $__attributesOriginalc717d07ccd4458ff48b6c7281e618771; ?>
<?php unset($__attributesOriginalc717d07ccd4458ff48b6c7281e618771); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc717d07ccd4458ff48b6c7281e618771)): ?>
<?php $component = $__componentOriginalc717d07ccd4458ff48b6c7281e618771; ?>
<?php unset($__componentOriginalc717d07ccd4458ff48b6c7281e618771); ?>
<?php endif; ?>

        <section class="ft-rfq-portal-card ft-rfq-prototype-card ft-rfq-prototype-pricing" aria-labelledby="rfq-pricing-section-title">
            <div class="ft-rfq-prototype-section-head is-inline-copy">
                <span class="ft-rfq-prototype-section-number">3</span>
                <h2 id="rfq-pricing-section-title">Pricing</h2>
                <p>Enter pricing for the requested quantity. You may add volume price breaks.</p>
            </div>

            <div class="ft-rfq-prototype-price-lines">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $item = $quoteItems->get($product['item_id']);
                        $initialPrice = old('prices.'.$product['item_id'], $item?->unit_price);
                        $initialSubtotal = (float) $product['quantity'] * (float) ($initialPrice ?? 0);
                    ?>
                    <div class="ft-rfq-prototype-price-line" data-rfq-price-row data-quantity="<?php echo e((float) $product['quantity']); ?>">
                        <label class="ft-rfq-prototype-field">
                            <span>Quantity</span>
                            <input type="text" value="<?php echo e(number_format((float) $product['quantity'], fmod((float) $product['quantity'], 1.0) === 0.0 ? 0 : 2)); ?>" readonly tabindex="-1">
                        </label>
                        <label class="ft-rfq-prototype-field">
                            <span>Currency</span>
                            <select name="currency" data-rfq-currency <?php if($locked): echo 'disabled'; endif; ?>>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'CNY' => 'CNY']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($code); ?>" <?php if(old('currency', $currency) === $code): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </label>
                        <label class="ft-rfq-prototype-field">
                            <span>Unit price (<span data-rfq-currency-label><?php echo e($currency); ?></span>) <b>*</b></span>
                            <input type="number" name="prices[<?php echo e($product['item_id']); ?>]" value="<?php echo e($initialPrice); ?>" min="0" step="0.0001" required data-rfq-price <?php if($locked): echo 'disabled'; endif; ?>>
                        </label>
                        <label class="ft-rfq-prototype-field">
                            <span>MOQ <b>*</b></span>
                            <input type="number" name="moqs[<?php echo e($product['item_id']); ?>]" value="<?php echo e(old('moqs.'.$product['item_id'], $item?->moq)); ?>" min="0" step="1" <?php if($locked): echo 'disabled'; endif; ?>>
                        </label>
                        <div class="ft-rfq-prototype-subtotal">
                            <span>Subtotal (<span data-rfq-currency-label><?php echo e($currency); ?></span>)</span>
                            <strong data-rfq-line-subtotal><?php echo e($currency); ?> <?php echo e(number_format($initialSubtotal, 2)); ?></strong>
                            <small>Calculated automatically</small>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loop->first && !$locked): ?>
                            <button type="button" class="ft-rfq-add-price-break-btn" data-rfq-add-price-break title="Add an optional quantity tier with a different unit price" aria-label="Add an optional volume price break">
                                <span>+</span>
                                <span class="ft-rfq-add-price-break-copy">
                                    <strong>Add price break</strong>
                                    <small>Optional quantity tier</small>
                                </span>
                            </button>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="ft-rfq-prototype-costs-heading">
                <strong>Additional costs &amp; tax</strong>
                <span>Add only the charges that apply to this quotation.</span>
            </div>

            <div class="ft-rfq-prototype-cost-row">
                <label class="ft-rfq-prototype-field">
                    <span>Tooling / setup cost (<span data-rfq-currency-label><?php echo e($currency); ?></span>)</span>
                    <input type="number" name="tooling_cost" value="<?php echo e(old('tooling_cost', $quote?->tooling_cost ?? 0)); ?>" min="0" step="0.01" <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Sample cost (<span data-rfq-currency-label><?php echo e($currency); ?></span>)</span>
                    <input type="number" name="sample_cost" value="<?php echo e(old('sample_cost', $quote?->sample_cost ?? 0)); ?>" min="0" step="0.01" <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Discount (<span data-rfq-currency-label><?php echo e($currency); ?></span>)</span>
                    <input type="number" name="discount" value="<?php echo e(old('discount', $quote?->discount ?? 0)); ?>" min="0" step="0.01" <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Tax <b>*</b></span>
                    <select name="tax_status" <?php if($locked): echo 'disabled'; endif; ?>>
                        <option value="excluded" <?php if(old('tax_status', $quote?->tax_status ?? 'excluded') === 'excluded'): echo 'selected'; endif; ?>>Excluded</option>
                        <option value="included" <?php if(old('tax_status', $quote?->tax_status) === 'included'): echo 'selected'; endif; ?>>Included</option>
                    </select>
                </label>
                <input type="hidden" name="freight" value="<?php echo e(old('freight', $quote?->freight ?? 0)); ?>">
            </div>
        </section>

        <section class="ft-rfq-portal-card ft-rfq-prototype-card ft-rfq-prototype-production" aria-labelledby="rfq-production-title">
            <div class="ft-rfq-prototype-section-head">
                <span class="ft-rfq-prototype-section-number">4</span>
                <h2 id="rfq-production-title">Production and delivery</h2>
            </div>

            <div class="ft-rfq-prototype-production-grid">
                <label class="ft-rfq-prototype-field">
                    <span>Production lead time <b>*</b></span>
                    <span class="ft-rfq-prototype-suffix-control"><input type="number" name="lead_time_days" value="<?php echo e(old('lead_time_days', $quote?->lead_time_days)); ?>" min="0" <?php if($locked): echo 'disabled'; endif; ?>><em>days</em></span>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Sample lead time <b>*</b></span>
                    <span class="ft-rfq-prototype-suffix-control"><input type="number" name="sample_lead_time_days" value="<?php echo e(old('sample_lead_time_days', $quote?->sample_lead_time_days)); ?>" min="0" <?php if($locked): echo 'disabled'; endif; ?>><em>days</em></span>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Incoterm <b>*</b></span>
                    <select name="incoterm" <?php if($locked): echo 'disabled'; endif; ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['FOB', 'EXW', 'FCA', 'CIF', 'CFR', 'DAP', 'DDP']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incoterm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($incoterm); ?>" <?php if(old('incoterm', $quote?->incoterm ?: 'FOB') === $incoterm): echo 'selected'; endif; ?>><?php echo e($incoterm); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Shipping port <b>*</b></span>
                    <input type="text" name="shipping_port" value="<?php echo e(old('shipping_port', $quote?->shipping_port)); ?>" placeholder="Shanghai" <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <label class="ft-rfq-prototype-field is-deviations">
                    <span>Deviations or alternatives (optional)</span>
                    <textarea rows="4" name="notes" placeholder="Describe any differences from the requested specification..." <?php if($locked): echo 'disabled'; endif; ?>><?php echo e(old('notes', $quote?->notes)); ?></textarea>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Estimated delivery date <b>*</b></span>
                    <input type="date" name="estimated_delivery_date" value="<?php echo e(old('estimated_delivery_date', $quote?->estimated_delivery_date?->format('Y-m-d'))); ?>" <?php if($locked): echo 'disabled'; endif; ?>>
                </label>
                <label class="ft-rfq-prototype-field">
                    <span>Quote validity <b>*</b></span>
                    <span class="ft-rfq-prototype-suffix-control"><input type="number" name="validity_days" value="<?php echo e(old('validity_days', $quote?->validity_days ?? 30)); ?>" min="0" <?php if($locked): echo 'disabled'; endif; ?>><em>days</em></span>
                </label>
                <label class="ft-rfq-prototype-field is-specification">
                    <span>Can meet requested specification? <b>*</b></span>
                    <select name="specification_compliance" <?php if($locked): echo 'disabled'; endif; ?>>
                        <option value="">Select</option>
                        <option value="yes" <?php if(old('specification_compliance', $quote?->specification_compliance) === 'yes'): echo 'selected'; endif; ?>>Yes, fully compliant</option>
                        <option value="partial" <?php if(old('specification_compliance', $quote?->specification_compliance) === 'partial'): echo 'selected'; endif; ?>>Partially compliant</option>
                        <option value="no" <?php if(old('specification_compliance', $quote?->specification_compliance) === 'no'): echo 'selected'; endif; ?>>Not compliant</option>
                    </select>
                </label>
            </div>
        </section>

        <section class="ft-rfq-portal-card ft-rfq-prototype-card ft-rfq-prototype-supporting" aria-labelledby="rfq-supporting-title">
            <div class="ft-rfq-prototype-section-head">
                <span class="ft-rfq-prototype-section-number">5</span>
                <h2 id="rfq-supporting-title">Supporting documents</h2>
            </div>

            <div class="ft-rfq-prototype-supporting-grid">
                <label class="ft-rfq-prototype-dropzone" data-rfq-dropzone>
                    <input
                        type="file"
                        name="documents[]"
                        multiple
                        accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>"
                        data-rfq-pricing-file-input
                        <?php if($locked): echo 'disabled'; endif; ?>
                    >
                    <span class="ft-rfq-prototype-upload-icon"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'upload-cloud']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'upload-cloud']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?></span>
                    <span>
                        <strong>Drop quotation files here or <b>browse</b></strong>
                        <small><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></small>
                    </span>
                </label>

                <div class="ft-rfq-prototype-supporting-right">
                    <div class="ft-rfq-prototype-uploaded-files">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $documentCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $extension = strtolower(pathinfo((string) $document->name, PATHINFO_EXTENSION));
                                $size = (int) $document->size;
                                $sizeLabel = $size >= 1048576 ? number_format($size / 1048576, 1).' MB' : number_format(max(1, (int) ceil($size / 1024)), 0).' KB';
                            ?>
                            <div class="ft-rfq-prototype-file-row">
                                <span class="ft-rfq-file-icon is-<?php echo e($extension); ?>"><?php echo e(strtoupper(substr($extension ?: 'FILE', 0, 4))); ?></span>
                                <span class="ft-rfq-prototype-file-name"><?php echo e($document->name); ?></span>
                                <span class="ft-rfq-prototype-file-size">·&nbsp; <?php echo e($sizeLabel); ?></span>
                                <span class="ft-rfq-prototype-file-ready">✓</span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?>
                                    <button type="submit" form="rfq-pricing-remove-doc-<?php echo e($document->id); ?>" class="ft-rfq-prototype-remove-file">Remove</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="ft-rfq-prototype-empty-file">No quotation file uploaded yet.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <label class="ft-rfq-prototype-field ft-rfq-prototype-supplier-notes">
                        <span>Supplier notes (optional)</span>
                        <input type="text" name="document_notes" value="<?php echo e(old('document_notes', $quote?->document_notes)); ?>" placeholder="Pricing includes standard export packaging. Samples available within 5 days." <?php if($locked): echo 'disabled'; endif; ?>>
                    </label>
                </div>
            </div>
        </section>

        <section class="ft-rfq-prototype-confirmation" aria-label="Quotation confirmation">
            <label>
                <input type="checkbox" name="pricing_confirmation" value="1" data-rfq-pricing-confirmation <?php if($locked): echo 'disabled'; endif; ?>>
                <span>I confirm that the information provided is accurate and that this quotation is valid for the stated period. <b>*</b></span>
            </label>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?><p><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'alert']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?> Required before submission</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <small>You can save a draft and return using the same secure link before the deadline.</small>
        </section>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <form method="post" action="<?php echo e(route('rfq.public.documents.remove', ['token' => $token, 'document' => $document->id])); ?>" id="rfq-pricing-remove-doc-<?php echo e($document->id); ?>" class="ft-rfq-hidden-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="return_step" value="pricing">
            </form>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/rfq/public/pricing.blade.php ENDPATH**/ ?>