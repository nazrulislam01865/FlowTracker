<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'other']));

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

foreach (array_filter((['type' => 'other']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'sea'): ?>
    <svg <?php echo e($attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true'])); ?>>
        <path d="M4 15.5 6.2 8h11.6l2.2 7.5"/>
        <path d="M8.2 8V5.5h7.6V8M10.2 5.5V3.8h3.6v1.7"/>
        <path d="M3 16.2c1.2 0 1.8 1.4 3 1.4s1.8-1.4 3-1.4 1.8 1.4 3 1.4 1.8-1.4 3-1.4 1.8 1.4 3 1.4 1.8-1.4 3-1.4"/>
        <path d="M4 20c1.2 0 1.8 1.2 3 1.2S8.8 20 10 20s1.8 1.2 3 1.2 1.8-1.2 3-1.2 1.8 1.2 3 1.2"/>
    </svg>
<?php elseif($type === 'air'): ?>
    <svg <?php echo e($attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true'])); ?>>
        <path d="m3.4 13.2 6.3 1.7 5.7-5.7-8.7-3.1 1.6-1.6 10.1 1.7 2-2c.8-.8 2-.9 2.5-.4.5.5.4 1.7-.4 2.5l-2 2 1.7 10.1-1.6 1.6-3.1-8.7-5.7 5.7 1.7 6.3-1.3 1.3-3.3-5-5-3.3 1.5-1.4Z"/>
    </svg>
<?php elseif($type === 'express'): ?>
    <svg <?php echo e($attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.9', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true'])); ?>>
        <path d="M13.2 2.8 5.5 13h5.4l-.4 8.2L18.5 11h-5.4l.1-8.2Z"/>
    </svg>
<?php else: ?>
    <svg <?php echo e($attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true'])); ?>>
        <path d="M4 7.5h10v9H4zM14 10h3.5l2.5 3v3.5h-6z"/>
        <circle cx="7.5" cy="18" r="1.5"/><circle cx="17.5" cy="18" r="1.5"/>
    </svg>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/shipping-method-icon.blade.php ENDPATH**/ ?>