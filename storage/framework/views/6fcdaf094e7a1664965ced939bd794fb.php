<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name']));

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

foreach (array_filter((['name']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<span <?php echo e($attributes->class('ft-detail-svg-icon')); ?> aria-hidden="true">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($name):
        case ('edit'): ?>
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M4.5 19.5h4l10-10a2.8 2.8 0 0 0-4-4l-10 10v4Z"></path>
                <path d="m13.5 6.5 4 4"></path>
            </svg>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
        <?php case ('calendar'): ?>
            <svg viewBox="0 0 24 24" fill="none">
                <rect x="4" y="5.5" width="16" height="14" rx="2"></rect>
                <path d="M8 3.5v4M16 3.5v4M4 10h16"></path>
            </svg>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
        <?php case ('copy'): ?>
            <svg viewBox="0 0 24 24" fill="none">
                <rect x="8" y="8" width="10" height="10" rx="1.5"></rect>
                <path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"></path>
            </svg>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
        <?php default: ?>
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8"></circle></svg>
    <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/detail-icon.blade.php ENDPATH**/ ?>