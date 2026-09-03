<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'level' => 'main',
    'editing' => false,
    'readOnly' => false,
    'mainCategories' => collect(),
    'productCategories' => collect(),
    'selectedParentId' => null,
    'nameValue' => '',
    'descriptionValue' => '',
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
    'level' => 'main',
    'editing' => false,
    'readOnly' => false,
    'mainCategories' => collect(),
    'productCategories' => collect(),
    'selectedParentId' => null,
    'nameValue' => '',
    'descriptionValue' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $titleLevel = match($level) { 'main' => 'main category', 'product' => 'product category', 'sub' => 'subcategory', default => 'category' };
    $mainOptions = collect($mainCategories)->map(fn($item) => ['id' => $item->id, 'label' => $item->name, 'meta' => $item->code]);
    $productOptions = collect($productCategories)->map(function($item){
        $main = trim((string)(data_get($item->metadata,'main_category') ?: data_get($item->metadata,'excel_main_category')));
        return ['id'=>$item->id,'label'=>$item->name,'meta'=>$main];
    });
?>
<div class="ft-category-editor-backdrop" wire:click.self="closeCategoryEditor" wire:keydown.escape="closeCategoryEditor">
    <section class="ft-category-editor" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="ft-category-editor-title" x-data x-on:click.stop>
        <header class="ft-category-editor-head">
            <div>
                <h2 id="ft-category-editor-title"><?php echo e($readOnly ? 'View' : ($editing ? 'Edit' : 'Add')); ?> <?php echo e($titleLevel); ?></h2>
                <p><?php echo e($readOnly ? 'Review the category hierarchy and details.' : 'Codes are generated automatically and the hierarchy is used by Product create/edit.'); ?></p>
            </div>
            <button type="button" wire:click="closeCategoryEditor" aria-label="Close">×</button>
        </header>

        <div class="ft-category-editor-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$editing && !$readOnly): ?>
                <label class="ft-category-editor-field">
                    <span>Category level <i>*</i></span>
                    <select wire:model.live="categoryEditorLevel">
                        <option value="main">Main category</option>
                        <option value="product">Product category</option>
                        <option value="sub">Subcategory</option>
                    </select>
                </label>
            <?php else: ?>
                <div class="ft-category-editor-level-pill"><?php echo e(ucfirst($titleLevel)); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($level === 'product'): ?>
                <div class="ft-category-editor-field">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($readOnly): ?>
                        <span>Main category <i>*</i></span>
                        <div class="ft-category-editor-readonly"><?php echo e(optional(collect($mainCategories)->firstWhere('id', (int)$selectedParentId))->name ?? '—'); ?></div>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-search-select ft-taxonomy-search-select','label' => 'Main category','property' => 'categoryEditorParentId','value' => $selectedParentId,'placeholder' => 'Select main category','options' => $mainOptions,'clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 430,'searchPlaceholder' => 'Search main category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select ft-taxonomy-search-select','label' => 'Main category','property' => 'categoryEditorParentId','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedParentId),'placeholder' => 'Select main category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mainOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 430,'search-placeholder' => 'Search main category…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categoryEditorParentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php elseif($level === 'sub'): ?>
                <div class="ft-category-editor-field">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($readOnly): ?>
                        <span>Product category <i>*</i></span>
                        <div class="ft-category-editor-readonly"><?php echo e(optional(collect($productCategories)->firstWhere('id', (int)$selectedParentId))->name ?? '—'); ?></div>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-product-search-select ft-taxonomy-search-select','label' => 'Product category','property' => 'categoryEditorParentId','value' => $selectedParentId,'placeholder' => 'Select product category','options' => $productOptions,'clearable' => false,'required' => true,'fixedMenu' => true,'menuWidth' => 430,'searchPlaceholder' => 'Search product category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-product-search-select ft-taxonomy-search-select','label' => 'Product category','property' => 'categoryEditorParentId','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedParentId),'placeholder' => 'Select product category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'required' => true,'fixed-menu' => true,'menu-width' => 430,'search-placeholder' => 'Search product category…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categoryEditorParentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <label class="ft-category-editor-field">
                <span>Name <i>*</i></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($readOnly): ?>
                    <div class="ft-category-editor-readonly"><?php echo e($nameValue ?: '—'); ?></div>
                <?php else: ?>
                    <input type="text" wire:model.blur="categoryEditorName" maxlength="255" placeholder="Enter category name">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categoryEditorName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <label class="ft-category-editor-field">
                <span>Description</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($readOnly): ?>
                    <div class="ft-category-editor-readonly is-description"><?php echo e($descriptionValue ?: '—'); ?></div>
                <?php else: ?>
                    <textarea wire:model.blur="categoryEditorDescription" rows="4" maxlength="5000" placeholder="Add a short description"></textarea>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categoryEditorDescription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editing && !$readOnly): ?>
                <label class="ft-category-editor-field">
                    <span>Status</span>
                    <select wire:model="categoryEditorStatus">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$readOnly): ?>
                <div class="ft-category-editor-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/></svg>
                    Code is generated automatically. Product forms will use this hierarchy immediately after saving.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <footer class="ft-category-editor-foot">
            <button type="button" class="ft-category-secondary" wire:click="closeCategoryEditor"><?php echo e($readOnly ? 'Close' : 'Cancel'); ?></button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$readOnly): ?>
                <button type="button" class="ft-category-primary" wire:click="saveCategoryEditor" wire:loading.attr="disabled" wire:target="saveCategoryEditor">
                    <span wire:loading.remove wire:target="saveCategoryEditor"><?php echo e($editing ? 'Save changes' : 'Create category'); ?></span>
                    <span wire:loading wire:target="saveCategoryEditor">Saving…</span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/category-editor.blade.php ENDPATH**/ ?>