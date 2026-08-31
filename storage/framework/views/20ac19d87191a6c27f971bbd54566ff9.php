<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['size' => 24]));

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

foreach (array_filter((['size' => 24]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $size = max(18, (int) $size);
    $fontSizeRem = max(9, round($size / 3.4)) / 16;
    $style = "width:{$size}px;height:{$size}px;font-size:{$fontSizeRem}rem";
?>
<span <?php echo e($attributes->class(['avatar', 'ft-inline-live-avatar'])->merge(['style' => $style])); ?>>
    <template x-if="avatarUrl">
        <img
            :src="avatarUrl"
            alt=""
            aria-hidden="true"
            decoding="async"
            data-ft-image-fallback="managed"
            x-on:error="avatarUrl = ''; savedAvatarUrl = ''"
        >
    </template>
    <span
        class="avatar-initials"
        x-show="!avatarUrl"
        x-text="initials(display)"
        aria-hidden="true"
    ></span>
</span>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/inline-live-avatar.blade.php ENDPATH**/ ?>