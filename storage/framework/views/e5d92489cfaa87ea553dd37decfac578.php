<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'level',
    'recordId',
    'isActive' => true,
    'canEdit' => false,
    'canDelete' => false,
    'canCreate' => false,
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
    'level',
    'recordId',
    'isActive' => true,
    'canEdit' => false,
    'canDelete' => false,
    'canCreate' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $menuId = 'category-actions-'.$level.'-'.$recordId;
    $estimatedMenuHeight = 190;
?>
<div class="ft-category-action-menu" x-data="{ open:false, busy:false }">
    <button
        type="button"
        class="ft-category-action-trigger"
        aria-haspopup="menu"
        aria-controls="<?php echo e($menuId); ?>"
        :aria-expanded="open ? 'true' : 'false'"
        x-on:click.stop="
            const menu=$refs.menu;
            if(menu.matches(':popover-open')){menu.hidePopover();return;}
            const rect=$el.getBoundingClientRect(), width=168, height=<?php echo e($estimatedMenuHeight); ?>, edge=10, gap=6;
            const above=(window.innerHeight-rect.bottom)<height+gap+edge && rect.top>(window.innerHeight-rect.bottom);
            menu.style.left=Math.min(window.innerWidth-width-edge,Math.max(edge,rect.right-width))+'px';
            menu.style.top=Math.min(window.innerHeight-height-edge,Math.max(edge,above?rect.top-height-gap:rect.bottom+gap))+'px';
            menu.showPopover();
        "
        aria-label="Category actions"
    ><span></span><span></span><span></span></button>

    <div id="<?php echo e($menuId); ?>" class="ft-category-action-panel" x-ref="menu" popover="auto" role="menu" x-on:toggle="open=$event.newState==='open'">
        <button type="button" role="menuitem" x-on:click="$refs.menu.hidePopover(); $wire.viewCategory('<?php echo e($level); ?>', <?php echo e($recordId); ?>)">View category</button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreate && $level === 'product'): ?>
            <button type="button" role="menuitem" x-on:click="$refs.menu.hidePopover(); $wire.openCategoryEditor('sub', null, <?php echo e($recordId); ?>)">Add subcategory</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
            <button type="button" role="menuitem" x-on:click="$refs.menu.hidePopover(); $wire.openCategoryEditor('<?php echo e($level); ?>', <?php echo e($recordId); ?>)">Edit category</button>
            <button type="button" role="menuitem" x-bind:disabled="busy" x-on:click="busy=true; $refs.menu.hidePopover(); $wire.toggleCategoryStatus('<?php echo e($level); ?>', <?php echo e($recordId); ?>).finally(()=>busy=false)"><?php echo e($isActive ? 'Deactivate' : 'Activate'); ?></button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
            <button type="button" role="menuitem" class="is-danger" x-bind:disabled="busy" x-on:click="busy=true; $refs.menu.hidePopover(); $wire.deleteCategory('<?php echo e($level); ?>', <?php echo e($recordId); ?>).finally(()=>busy=false)">Delete permanently</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/category-action-menu.blade.php ENDPATH**/ ?>