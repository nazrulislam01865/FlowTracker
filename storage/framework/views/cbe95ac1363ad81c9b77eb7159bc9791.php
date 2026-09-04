<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'invitation', 'token', 'quote', 'products', 'documents', 'documentTypes', 'contact', 'rfqReference', 'currency',
    'productSubtotal', 'sampleCost', 'otherCosts', 'totalQuotedValue', 'clientName', 'readyToSubmit', 'locked' => false, 'submitted' => false, 'canRevise' => false,
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
    'invitation', 'token', 'quote', 'products', 'documents', 'documentTypes', 'contact', 'rfqReference', 'currency',
    'productSubtotal', 'sampleCost', 'otherCosts', 'totalQuotedValue', 'clientName', 'readyToSubmit', 'locked' => false, 'submitted' => false, 'canRevise' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $quoteItems = collect($quote?->items ?? [])->keyBy('inquiry_item_id');
    $documentCollection = collect($documents);
    $firstProduct = collect($products)->first();
    $validity = (int) ($quote?->validity_days ?? 30);
    $compliance = match($quote?->specification_compliance) { 'yes' => 'Yes, fully compliant', 'partial' => 'Partially compliant', 'no' => 'Not compliant', default => '—' };
?>
<div class="ft-rfq-portal-stack">
    <section class="ft-rfq-portal-card ft-rfq-review-intro">
        <h2>Review your quotation</h2>
        <p>Check all information before submitting. You can return to any section to make changes.</p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($submitted)): ?>
            <div class="ft-rfq-warning-strip"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info']); ?>
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
<?php endif; ?> Submitting will send this quotation to <?php echo e($clientName); ?>. You can revise it later from this secure link until the RFQ is closed.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    <section class="ft-rfq-portal-card ft-rfq-review-card">
        <div class="ft-rfq-review-card__head"><h2><span class="ft-rfq-review-check">✓</span> Supplier and RFQ details</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?><a href="<?php echo e(route('rfq.public.show', ['token' => $token, 'step' => 'details'])); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> Edit</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <div class="ft-rfq-review-details-grid">
            <div><small>Supplier company</small><strong><?php echo e($invitation->supplier?->name ?: '—'); ?></strong></div>
            <div><small>Contact person</small><strong><?php echo e($contact['name'] ?: '—'); ?></strong></div>
            <div><small>Email</small><strong><?php echo e($contact['email'] ?: '—'); ?></strong></div>
            <div><small>Phone</small><strong><?php echo e($contact['phone'] ?: '—'); ?></strong></div>
            <div><small>Inquiry reference</small><strong><?php echo e($invitation->inquiry->inquiry_number); ?></strong></div>
            <div><small>RFQ reference</small><strong><?php echo e($rfqReference); ?></strong></div>
        </div>
    </section>

    <section class="ft-rfq-portal-card ft-rfq-review-card">
        <div class="ft-rfq-review-card__head"><h2><span class="ft-rfq-review-check">✓</span> Product and pricing</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?><a href="<?php echo e(route('rfq.public.show', ['token' => $token, 'step' => 'pricing'])); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> Edit</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $item = $quoteItems->get($product['item_id']);
                $subtotal = (float) $product['quantity'] * (float) ($item?->unit_price ?? 0);
            ?>
            <div class="ft-rfq-review-product-row">
                <div class="ft-rfq-review-product-info">
                    <?php if (isset($component)) { $__componentOriginal2b950ae2ac1d5af09e2e6f0db8d380c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2b950ae2ac1d5af09e2e6f0db8d380c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.product-thumb','data' => ['product' => $product,'size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.product-thumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'size' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2b950ae2ac1d5af09e2e6f0db8d380c4)): ?>
<?php $attributes = $__attributesOriginal2b950ae2ac1d5af09e2e6f0db8d380c4; ?>
<?php unset($__attributesOriginal2b950ae2ac1d5af09e2e6f0db8d380c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2b950ae2ac1d5af09e2e6f0db8d380c4)): ?>
<?php $component = $__componentOriginal2b950ae2ac1d5af09e2e6f0db8d380c4; ?>
<?php unset($__componentOriginal2b950ae2ac1d5af09e2e6f0db8d380c4); ?>
<?php endif; ?>
                    <div><strong><?php echo e($product['name']); ?></strong><span><?php echo e($product['code'] ?: 'Product'); ?> &nbsp;·&nbsp; Requested <?php echo e(number_format((float) $product['quantity'], 0)); ?> <?php echo e($product['unit']); ?></span></div>
                </div>
                <div class="ft-rfq-review-price-table">
                    <div><small>Quantity</small><strong><?php echo e(number_format((float) $product['quantity'], 0)); ?></strong></div>
                    <div><small>Currency</small><strong><?php echo e($currency); ?></strong></div>
                    <div><small>Unit price</small><strong><?php echo e(number_format((float) ($item?->unit_price ?? 0), 4)); ?></strong></div>
                    <div><small>MOQ</small><strong><?php echo e($item?->moq !== null ? number_format((float) $item->moq, 0) : '—'); ?></strong></div>
                    <div><small>Subtotal</small><strong><?php echo e($currency); ?> <?php echo e(number_format($subtotal, 2)); ?></strong></div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="ft-rfq-review-costs-row">
            <div><small>Tooling / setup</small><strong><?php echo e($currency); ?> <?php echo e(number_format((float) ($quote?->tooling_cost ?? 0), 2)); ?></strong></div>
            <div><small>Sample cost</small><strong><?php echo e($currency); ?> <?php echo e(number_format((float) ($quote?->sample_cost ?? 0), 2)); ?></strong></div>
            <div><small>Discount</small><strong><?php echo e($currency); ?> <?php echo e(number_format((float) ($quote?->discount ?? 0), 2)); ?></strong></div>
            <div><small>Tax</small><strong><?php echo e(ucfirst((string) ($quote?->tax_status ?? 'excluded'))); ?></strong></div>
            <div class="is-total"><small>Total quoted value</small><strong><?php echo e($currency); ?> <?php echo e(number_format($totalQuotedValue, 2)); ?></strong></div>
        </div>
    </section>

    <section class="ft-rfq-portal-card ft-rfq-review-card">
        <div class="ft-rfq-review-card__head"><h2><span class="ft-rfq-review-check">✓</span> Production and delivery</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?><a href="<?php echo e(route('rfq.public.show', ['token' => $token, 'step' => 'pricing'])); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> Edit</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <div class="ft-rfq-review-delivery-grid">
            <div><small>Production lead time</small><strong><?php echo e($quote?->lead_time_days !== null ? $quote->lead_time_days.' days' : '—'); ?></strong></div>
            <div><small>Sample lead time</small><strong><?php echo e($quote?->sample_lead_time_days !== null ? $quote->sample_lead_time_days.' days' : '—'); ?></strong></div>
            <div><small>Incoterm</small><strong><?php echo e($quote?->incoterm ?: '—'); ?></strong></div>
            <div><small>Shipping port</small><strong><?php echo e($quote?->shipping_port ?: '—'); ?></strong></div>
            <div><small>Estimated delivery</small><strong><?php echo e($quote?->estimated_delivery_date?->format('M j, Y') ?: '—'); ?></strong></div>
            <div><small>Quote validity</small><strong><?php echo e($validity); ?> days</strong></div>
            <div><small>Specification compliance</small><strong><?php echo e($compliance); ?></strong></div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($quote?->notes)): ?><p class="ft-rfq-review-production-note"><?php echo e($quote->notes); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    <section class="ft-rfq-portal-card ft-rfq-review-card">
        <div class="ft-rfq-review-card__head"><h2><span class="ft-rfq-review-check">✓</span> Documents (<?php echo e($documentCollection->count()); ?>)</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?><a href="<?php echo e(route('rfq.public.show', ['token' => $token, 'step' => 'documents'])); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> Edit</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <div class="ft-rfq-review-docs">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $documentCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $extension = strtolower(pathinfo((string) $document->name, PATHINFO_EXTENSION));
                    $size = (int) $document->size;
                    $sizeLabel = $size >= 1048576 ? number_format($size / 1048576, 1).' MB' : number_format(max(1, (int) ceil($size / 1024)), 0).' KB';
                ?>
                <div class="ft-rfq-review-doc-row">
                    <span class="ft-rfq-file-icon is-<?php echo e($extension); ?>"><?php echo e(strtoupper(substr($extension ?: 'FILE', 0, 4))); ?></span>
                    <strong><?php echo e($document->name); ?></strong>
                    <small><?php echo e($documentTypes[$document->document_type] ?? 'Document'); ?> &nbsp;·&nbsp; <?php echo e($sizeLabel); ?></small>
                    <span class="ft-rfq-ready-status">✓ Ready</span>
                    <a href="<?php echo e(route('rfq.public.documents.preview', ['token' => $token, 'document' => $document->id])); ?>" target="_blank" rel="noopener">Preview</a>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-rfq-empty-docs">No documents attached.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <form method="post" action="<?php echo e(route('rfq.public.respond', ['token' => $token])); ?>" id="rfq-review-form">
        <?php echo csrf_field(); ?>
        <section class="ft-rfq-final-declaration <?php echo e($submitted ? 'is-submitted' : ''); ?>">
            <h2>Final declaration</h2>
            <label><input type="checkbox" name="declaration_accuracy" value="1" <?php if($submitted): echo 'checked'; endif; ?> <?php if($submitted): echo 'disabled'; endif; ?>><span>I confirm that the information provided is accurate and that this quotation is valid for <?php echo e($validity); ?> days.</span></label>
            <label><input type="checkbox" name="declaration_authority" value="1" <?php if($submitted): echo 'checked'; endif; ?> <?php if($submitted): echo 'disabled'; endif; ?>><span>I am authorized to submit this quotation on behalf of <strong><?php echo e($invitation->supplier?->name); ?></strong>.</span></label>
            <p>Submitted by <strong><?php echo e($quote?->submitted_by_name ?: ($contact['name'] ?? '—')); ?></strong> &nbsp;·&nbsp; <?php echo e($quote?->submitted_by_email ?: ($contact['email'] ?? '—')); ?></p>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$locked): ?>
            <div class="ft-rfq-portal-bottom-actions">
                <a class="ft-rfq-btn is-secondary" href="<?php echo e(route('rfq.public.show', ['token' => $token, 'step' => 'documents'])); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left']); ?>
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
<?php endif; ?> Back to documents</a>
                <div>
                    <button type="submit" class="ft-rfq-btn is-secondary" name="action" value="save_review"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'save']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'save']); ?>
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
<?php endif; ?> Save draft</button>
                    <button type="submit" class="ft-rfq-btn is-primary" name="action" value="submit" <?php if(!$readyToSubmit): echo 'disabled'; endif; ?>>Submit quotation <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'chevron-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right']); ?>
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
<?php endif; ?></button>
                </div>
            </div>
            <p class="ft-rfq-confirmation-note"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> A confirmation email will be sent after submission.</p>
        <?php elseif($submitted): ?>
            <div class="ft-rfq-submitted-banner">
                <span>✓ Quotation submitted successfully.</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRevise): ?>
                    <button type="submit" class="ft-rfq-btn is-secondary" form="rfq-revise-form">
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
<?php endif; ?> Revise quotation
                    </button>
                <?php else: ?>
                    <small>This quotation is locked because the RFQ is closed or the revision window has ended.</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submitted && $canRevise): ?>
        <form method="post" action="<?php echo e(route('rfq.public.respond', ['token' => $token])); ?>" id="rfq-revise-form" class="ft-rfq-hidden-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="revise">
        </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/rfq/public/review.blade.php ENDPATH**/ ?>