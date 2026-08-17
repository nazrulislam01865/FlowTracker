<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'productId',
    'isActive' => true,
    'canEdit' => false,
    'canDelete' => false,
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
    'productId',
    'isActive' => true,
    'canEdit' => false,
    'canDelete' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $actionCount = 1 + ($canEdit ? 2 : 0) + ($canDelete ? 1 : 0);
    $estimatedMenuHeight = 10 + ($actionCount * 33) + ($canDelete ? 3 : 0);
    $menuId = 'product-actions-'.$productId;
?>

<div
    class="ft-product-action-menu"
    x-data="{ open: false, busy: false }"
    x-on:scroll.window="if (open && $refs.menu.matches(':popover-open')) $refs.menu.hidePopover()"
    x-on:resize.window="if (open && $refs.menu.matches(':popover-open')) $refs.menu.hidePopover()"
>
    <button
        type="button"
        class="ft-product-action-trigger"
        :aria-expanded="open ? 'true' : 'false'"
        aria-haspopup="menu"
        aria-controls="<?php echo e($menuId); ?>"
        aria-label="Product actions"
        x-on:click.stop="
            const menu = $refs.menu;
            if (menu.matches(':popover-open')) {
                menu.hidePopover();
                return;
            }

            const rect = $el.getBoundingClientRect();
            const menuWidth = 148;
            const menuHeight = <?php echo e($estimatedMenuHeight); ?>;
            const edge = 10;
            const gap = 6;
            const availableBelow = window.innerHeight - rect.bottom;
            const availableAbove = rect.top;
            const openAbove = availableBelow < (menuHeight + gap + edge) && availableAbove > availableBelow;
            const left = Math.min(
                window.innerWidth - menuWidth - edge,
                Math.max(edge, rect.right - menuWidth)
            );
            const desiredTop = openAbove
                ? rect.top - menuHeight - gap
                : rect.bottom + gap;
            const top = Math.min(
                window.innerHeight - menuHeight - edge,
                Math.max(edge, desiredTop)
            );

            menu.style.left = `${left}px`;
            menu.style.top = `${top}px`;
            menu.showPopover();
        "
    >
        <span></span><span></span><span></span>
    </button>

    <div
        id="<?php echo e($menuId); ?>"
        class="ft-product-action-panel"
        x-ref="menu"
        popover="auto"
        role="menu"
        x-on:toggle="open = $event.newState === 'open'"
    >
        <button
            type="button"
            role="menuitem"
            x-on:click="$refs.menu.hidePopover(); $wire.viewProduct(<?php echo e($productId); ?>)"
        >View product</button>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
            <button
                type="button"
                role="menuitem"
                x-on:click="$refs.menu.hidePopover(); $wire.editProduct(<?php echo e($productId); ?>)"
            >Edit product</button>
            <button
                type="button"
                role="menuitem"
                x-bind:disabled="busy"
                x-on:click="
                    if (busy) return;
                    busy = true;
                    $refs.menu.hidePopover();
                    $wire.toggleProductStatus(<?php echo e($productId); ?>).finally(() => busy = false);
                "
            ><?php echo e($isActive ? 'Deactivate' : 'Activate'); ?></button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
            <button
                type="button"
                role="menuitem"
                class="is-danger"
                x-bind:disabled="busy"
                x-on:click="
                    if (busy || !window.confirm('Delete this product?')) return;
                    busy = true;
                    $refs.menu.hidePopover();
                    $wire.deleteProduct(<?php echo e($productId); ?>).finally(() => busy = false);
                "
            >Delete product</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/action-menu.blade.php ENDPATH**/ ?>