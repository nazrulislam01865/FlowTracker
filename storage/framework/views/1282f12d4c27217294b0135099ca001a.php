<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'invitation', 'token', 'step', 'quote', 'firstProduct', 'currency', 'totalQuantity', 'productSubtotal',
    'sampleCost', 'otherCosts', 'totalQuotedValue', 'detailsComplete', 'pricingComplete', 'documents',
    'documentsComplete' => false, 'readyToSubmit', 'locked' => false, 'submitted' => false, 'canRevise' => false,
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
    'invitation', 'token', 'step', 'quote', 'firstProduct', 'currency', 'totalQuantity', 'productSubtotal',
    'sampleCost', 'otherCosts', 'totalQuotedValue', 'detailsComplete', 'pricingComplete', 'documents',
    'documentsComplete' => false, 'readyToSubmit', 'locked' => false, 'submitted' => false, 'canRevise' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $formId = match($step) {
        'details' => 'rfq-details-form',
        'pricing' => 'rfq-pricing-form',
        'documents' => 'rfq-documents-form',
        default => 'rfq-review-form',
    };
    $saveAction = match($step) {
        'details' => 'save_details',
        'pricing' => 'save_pricing',
        'documents' => 'save_documents',
        default => 'save_review',
    };
    $primaryAction = match($step) {
        'details' => 'continue_pricing',
        'pricing' => 'continue_documents',
        'documents' => 'continue_review',
        default => 'submit',
    };
    $primaryLabel = match($step) {
        'details' => 'Continue to pricing',
        'pricing' => 'Review & submit',
        'documents' => 'Continue to review',
        default => 'Submit quotation',
    };
    $docs = collect($documents);
?>
<section class="ft-rfq-summary-card">
    <h2>Quotation summary</h2>
    <div class="ft-rfq-summary-product">
        <?php if (isset($component)) { $__componentOriginal2b950ae2ac1d5af09e2e6f0db8d380c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2b950ae2ac1d5af09e2e6f0db8d380c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.product-thumb','data' => ['product' => $firstProduct ?? [],'size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.product-thumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($firstProduct ?? []),'size' => 'lg']); ?>
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
        <strong><?php echo e($firstProduct['name'] ?? 'Requested product'); ?></strong>
    </div>
    <dl class="ft-rfq-summary-meta">
        <div><dt>Supplier</dt><dd><?php echo e($invitation->supplier?->name ?: '—'); ?></dd></div>
        <div><dt>Requested quantity</dt><dd><?php echo e(number_format((float) $totalQuantity, 0)); ?> units</dd></div>
        <div><dt>Quotation due</dt><dd><?php echo e($invitation->due_at?->format('M j, Y') ?? 'No due date'); ?></dd></div>
    </dl>
    <div class="ft-rfq-summary-divider"></div>
    <dl class="ft-rfq-summary-costs">
        <div><dt>Product subtotal</dt><dd data-rfq-summary-product-subtotal><?php echo e($currency); ?> <?php echo e(number_format((float) $productSubtotal, 2)); ?></dd></div>
        <div><dt>Sample cost</dt><dd data-rfq-summary-sample-cost><?php echo e($currency); ?> <?php echo e(number_format((float) $sampleCost, 2)); ?></dd></div>
        <div><dt>Other costs</dt><dd data-rfq-summary-other-costs><?php echo e($currency); ?> <?php echo e(number_format((float) $otherCosts, 2)); ?></dd></div>
    </dl>
    <div class="ft-rfq-summary-divider"></div>
    <div class="ft-rfq-summary-total"><span>Total quoted value</span><strong><?php echo e($currency); ?> <?php echo e(number_format((float) $totalQuotedValue, 2)); ?></strong></div>

    <div class="ft-rfq-summary-progress">
        <div class="<?php echo e($detailsComplete ? 'is-complete' : ''); ?>"><span><?php echo e($detailsComplete ? '✓' : '○'); ?></span> Product reviewed</div>
        <div class="<?php echo e($pricingComplete ? 'is-complete' : ''); ?>"><span><?php echo e($pricingComplete ? '✓' : '○'); ?></span> Pricing completed</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'pricing'): ?>
            <div class="<?php echo e($docs->isNotEmpty() ? 'is-complete' : ''); ?>" data-rfq-summary-document data-existing-documents="<?php echo e($docs->count()); ?>"><span><?php echo e($docs->isNotEmpty() ? '✓' : '○'); ?></span> Document attached</div>
            <div class="is-pending" data-rfq-summary-confirmation><span>○</span> Confirmation required</div>
        <?php else: ?>
            <div class="<?php echo e($documentsComplete ? 'is-complete' : ''); ?>"><span><?php echo e($documentsComplete ? '✓' : '○'); ?></span> <?php echo e($docs->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('document', $docs->count())); ?> attached</div>
            <div class="<?php echo e(($step === 'review' && $readyToSubmit) || $submitted ? 'is-complete' : 'is-pending'); ?>"><span><?php echo e((($step === 'review' && $readyToSubmit) || $submitted) ? '✓' : '○'); ?></span> <?php echo e($submitted ? 'Quotation submitted' : (($step === 'review' && $readyToSubmit) ? 'Ready to submit' : 'Review not completed')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?>
        <div class="ft-rfq-summary-actions">
            <button type="submit" class="ft-rfq-btn is-secondary is-full" form="<?php echo e($formId); ?>" name="action" value="<?php echo e($saveAction); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
            <button type="submit" class="ft-rfq-btn is-primary is-full" form="<?php echo e($formId); ?>" name="action" value="<?php echo e($primaryAction); ?>" <?php if($step === 'review' && !$readyToSubmit): echo 'disabled'; endif; ?>><?php echo e($primaryLabel); ?> <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
            <form method="post" action="<?php echo e(route('rfq.public.respond', ['token' => $token])); ?>"><?php echo csrf_field(); ?><button type="submit" name="action" value="decline" class="ft-rfq-decline-link" data-rfq-decline>Decline to quote</button></form>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'review'): ?><div class="ft-rfq-secure-submission"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> Secure submission</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php elseif($submitted): ?>
        <div class="ft-rfq-summary-submitted">✓ Submitted</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRevise): ?>
            <div class="ft-rfq-summary-actions is-submitted-actions">
                <form method="post" action="<?php echo e(route('rfq.public.respond', ['token' => $token])); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" name="action" value="revise" class="ft-rfq-btn is-secondary is-full"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
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
<?php endif; ?> Revise quotation</button>
                </form>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/rfq/public/summary.blade.php ENDPATH**/ ?>