<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'itemId' => null,
    'canEdit' => false,
    'editMethod' => '',
    'editLabel' => 'Edit product',
    'canDelete' => false,
    'removeMethod' => '',
    'removeLabel' => 'Remove product',
    'confirmText' => 'Remove this product?',
    'canRestore' => false,
    'restoreMethod' => '',
    'restoreLabel' => 'Restore product',
    'disabled' => false,
    'disabledTitle' => 'This product cannot be changed.',
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
    'itemId' => null,
    'canEdit' => false,
    'editMethod' => '',
    'editLabel' => 'Edit product',
    'canDelete' => false,
    'removeMethod' => '',
    'removeLabel' => 'Remove product',
    'confirmText' => 'Remove this product?',
    'canRestore' => false,
    'restoreMethod' => '',
    'restoreLabel' => 'Restore product',
    'disabled' => false,
    'disabledTitle' => 'This product cannot be changed.',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $hasEdit = $itemId && $canEdit && $editMethod !== '';
    $hasDelete = $itemId && $canDelete && $removeMethod !== '';
    $hasRestore = $itemId && $canRestore && $restoreMethod !== '';

    $editTarget = $hasEdit ? $editMethod.'('.(int) $itemId.')' : null;
    $removeTarget = $hasDelete ? $removeMethod.'('.(int) $itemId.')' : null;
    $restoreTarget = $hasRestore ? $restoreMethod.'('.(int) $itemId.')' : null;

    $actionCount = (int) $hasEdit + (int) $hasDelete + (int) $hasRestore;
    // Matches the compact menu button sizing: 36px per action + 12px popover padding.
    $estimatedMenuHeight = max(48, ($actionCount * 36) + 12);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasEdit || $hasDelete || $hasRestore): ?>
    <div
        class="ft-order-product-row-menu"
        x-data="{
            open: false,
            top: 0,
            left: 0,
            menuWidth: 156,
            menuHeight: <?php echo e($estimatedMenuHeight); ?>,
            place() {
                const trigger = this.$refs.trigger;
                if (!trigger) return;

                const rect = trigger.getBoundingClientRect();
                const gap = 6;
                const edge = 10;
                const maxLeft = Math.max(edge, window.innerWidth - this.menuWidth - edge);

                this.left = Math.max(edge, Math.min(maxLeft, rect.right - this.menuWidth));

                const roomBelow = window.innerHeight - rect.bottom;
                const roomAbove = rect.top;
                const needed = this.menuHeight + gap + edge;

                if (roomBelow >= needed) {
                    this.top = rect.bottom + gap;
                    return;
                }

                if (roomAbove >= needed) {
                    this.top = rect.top - this.menuHeight - gap;
                    return;
                }

                const preferred = roomBelow >= roomAbove
                    ? rect.bottom + gap
                    : rect.top - this.menuHeight - gap;

                this.top = Math.max(
                    edge,
                    Math.min(window.innerHeight - this.menuHeight - edge, preferred)
                );
            },
            toggle() {
                if (this.open) {
                    this.open = false;
                    return;
                }

                this.place();
                this.open = true;
            }
        }"
        x-on:keydown.escape.window="open = false"
        x-on:resize.window="open = false"
        x-on:scroll.window="open = false"
    >
        <button
            x-ref="trigger"
            type="button"
            class="ft-order-product-kebab"
            x-on:click.stop="toggle()"
            x-bind:aria-expanded="open ? 'true' : 'false'"
            aria-haspopup="menu"
            aria-label="Product actions"
        >⋮</button>

        <template x-teleport="body">
            <div
                class="ft-detail-product-menu-portal"
                x-cloak
                x-show="open"
                x-transition.opacity.duration.120ms
                x-bind:style="`top: ${top}px; left: ${left}px;`"
                role="menu"
                x-on:click.outside="open = false"
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasRestore): ?>
                    <button
                        type="button"
                        class="is-neutral"
                        role="menuitem"
                        wire:click.stop="<?php echo e($restoreTarget); ?>"
                        wire:loading.attr="disabled"
                        wire:target="<?php echo e($restoreTarget); ?>"
                        x-on:click="open = false"
                        <?php if($disabled): ?> disabled title="<?php echo e($disabledTitle); ?>" <?php endif; ?>
                    ><?php echo e($restoreLabel); ?></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasEdit): ?>
                    <button
                        type="button"
                        class="is-neutral"
                        role="menuitem"
                        wire:click.stop="<?php echo e($editTarget); ?>"
                        wire:loading.attr="disabled"
                        wire:target="<?php echo e($editTarget); ?>"
                        x-on:click="open = false"
                        <?php if($disabled): ?> disabled title="<?php echo e($disabledTitle); ?>" <?php endif; ?>
                    ><?php echo e($editLabel); ?></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDelete): ?>
                    <button
                        type="button"
                        role="menuitem"
                        wire:click.stop="<?php echo e($removeTarget); ?>"
                        wire:confirm="<?php echo e($confirmText); ?>"
                        wire:loading.attr="disabled"
                        wire:target="<?php echo e($removeTarget); ?>"
                        x-on:click="open = false"
                        <?php if($disabled): ?> disabled title="<?php echo e($disabledTitle); ?>" <?php endif; ?>
                    ><?php echo e($removeLabel); ?></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </template>
    </div>
<?php else: ?>
    <span class="ft-order-product-action-placeholder">—</span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/detail-product-actions.blade.php ENDPATH**/ ?>