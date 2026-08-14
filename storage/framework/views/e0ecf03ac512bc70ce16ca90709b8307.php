<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label','value','subline'=>null,'tone'=>'blue','icon'=>'document','danger'=>false]));

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

foreach (array_filter((['label','value','subline'=>null,'tone'=>'blue','icon'=>'document','danger'=>false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<article class="ft-finance-metric <?php echo e($danger ? 'is-danger' : ''); ?>">
    <div>
        <span><?php echo e($label); ?></span>
        <strong><?php echo e($value); ?></strong>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subline): ?><small><?php echo e($subline); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <span class="ft-finance-metric-icon <?php echo e($tone); ?>" aria-hidden="true">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon === 'money'): ?>
            <svg viewBox="0 0 24 24"><path d="M7 3.5h8l4 4v13H7z"></path><path d="M15 3.5v4h4"></path><path d="M12.2 9v7M14.8 10.4c-.5-.8-1.4-1.2-2.6-1.2-1.5 0-2.5.7-2.5 1.8 0 2.9 5.2 1.2 5.2 3.9 0 1.2-1 2-2.7 2-1.3 0-2.4-.5-3-1.4"></path></svg>
        <?php elseif($icon === 'collect'): ?>
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v10M8.5 13.5 12 17l3.5-3.5"></path></svg>
        <?php elseif($icon === 'outstanding'): ?>
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v10M14.6 9.1c-.6-.7-1.4-1.1-2.5-1.1-1.4 0-2.4.7-2.4 1.7 0 2.7 5 1.2 5 3.8 0 1.2-1 2-2.7 2-1.2 0-2.3-.5-2.9-1.4"></path></svg>
        <?php elseif($icon === 'warning'): ?>
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle><path d="M12 7.5v5.7M12 16.3h.01"></path></svg>
        <?php else: ?>
            <svg viewBox="0 0 24 24"><path d="M7 3.5h8l4 4v13H7z"></path><path d="M15 3.5v4h4M10 11h6M10 14h6M10 17h4"></path></svg>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/finance/metric-card.blade.php ENDPATH**/ ?>