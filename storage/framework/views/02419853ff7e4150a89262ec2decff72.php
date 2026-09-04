<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value' => 0,
    'icon' => 'product',
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
    'label',
    'value' => 0,
    'icon' => 'product',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<article <?php echo e($attributes->class(['ft-inquiry-prq-stat'])); ?>>
    <span class="ft-inquiry-prq-stat__icon" aria-hidden="true">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($icon):
            case ('suppliers'): ?>
                <svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3"></circle><path d="M3.5 19c.45-3.2 2.3-4.8 5.5-4.8s5.05 1.6 5.5 4.8"></path><circle cx="17.5" cy="9" r="2.2"></circle><path d="M15.7 14.2c2.8-.25 4.4 1.35 4.8 4.15"></path></svg>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
            <?php case ('sent'): ?>
                <svg viewBox="0 0 24 24" fill="none"><path d="M3.5 11.2 20 4.5l-5.1 15-3.2-6.2-8.2-2.1Z"></path><path d="m11.7 13.3 4.4-4.5"></path></svg>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
            <?php case ('quotes'): ?>
                <svg viewBox="0 0 24 24" fill="none"><rect x="5" y="3.5" width="14" height="17" rx="1.5"></rect><path d="M8.5 8h7M8.5 11.5h7M8.5 15h4.5"></path></svg>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
            <?php default: ?>
                <svg viewBox="0 0 24 24" fill="none"><path d="m4 7 8-4 8 4-8 4-8-4Z"></path><path d="M4 7v9l8 4 8-4V7M12 11v9"></path></svg>
        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
    <div class="ft-inquiry-prq-stat__copy">
        <strong><?php echo e(number_format((int) $value)); ?></strong>
        <span><?php echo e($label); ?></span>
    </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/product-rfq-stat.blade.php ENDPATH**/ ?>