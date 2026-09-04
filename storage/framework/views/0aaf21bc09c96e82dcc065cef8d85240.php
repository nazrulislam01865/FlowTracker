    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'product_category' && $categoryEditorLevel): ?>
        <?php if (isset($component)) { $__componentOriginal0152ba453f28532ca522a6f84f1ccee6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0152ba453f28532ca522a6f84f1ccee6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-editor','data' => ['level' => $categoryEditorLevel,'editing' => (bool) $categoryEditorId,'readOnly' => $categoryEditorReadOnly,'mainCategories' => $categoryMainCategories,'productCategories' => $categoryProductCategories,'selectedParentId' => $categoryEditorParentId,'nameValue' => $categoryEditorName,'descriptionValue' => $categoryEditorDescription]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorLevel),'editing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $categoryEditorId),'read-only' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorReadOnly),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainCategories),'product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductCategories),'selected-parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorParentId),'name-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorName),'description-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryEditorDescription)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0152ba453f28532ca522a6f84f1ccee6)): ?>
<?php $attributes = $__attributesOriginal0152ba453f28532ca522a6f84f1ccee6; ?>
<?php unset($__attributesOriginal0152ba453f28532ca522a6f84f1ccee6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0152ba453f28532ca522a6f84f1ccee6)): ?>
<?php $component = $__componentOriginal0152ba453f28532ca522a6f84f1ccee6; ?>
<?php unset($__componentOriginal0152ba453f28532ca522a6f84f1ccee6); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/category-editor.blade.php ENDPATH**/ ?>