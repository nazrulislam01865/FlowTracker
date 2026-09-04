        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$recordsReady): ?>
            <?php echo $__env->make('livewire.shared.table-rows-placeholder', ['columns' => 9, 'rows' => 8], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-list','data' => ['mainPage' => $categoryMainPage,'productChildren' => $categoryProductChildren,'subcategoryChildren' => $categorySubcategoryChildren,'mainCategories' => $categoryMainCategories,'productCategories' => $categoryProductCategories,'parentOptions' => $categoryParentOptions,'counts' => $categoryCounts,'productCounts' => $categoryProductCounts,'mainProductCounts' => $categoryMainProductCounts,'subcategoryProductCounts' => $categorySubcategoryProductCounts,'productChildTotals' => $categoryProductChildTotals,'subcategoryChildTotals' => $categorySubcategoryChildTotals,'expandedMainIds' => $expandedMainCategoryIds,'expandedProductIds' => $expandedProductCategoryIds,'canCreate' => $canCreateMaster,'canEdit' => $canEditMaster,'canDelete' => $canDeleteMaster,'displayTimezone' => $displayTimezone,'search' => $search,'levelFilter' => $categoryLevelFilter,'parentFilter' => $categoryParentFilter,'statusFilter' => $categoryStatusFilter,'perPage' => $categoryPerPage,'selectedCategoryKeys' => $selectedCategoryKeys,'selectionCount' => $categorySelectionCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['main-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainPage),'product-children' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductChildren),'subcategory-children' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySubcategoryChildren),'main-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainCategories),'product-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductCategories),'parent-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryParentOptions),'counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryCounts),'product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductCounts),'main-product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryMainProductCounts),'subcategory-product-counts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySubcategoryProductCounts),'product-child-totals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryProductChildTotals),'subcategory-child-totals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySubcategoryChildTotals),'expanded-main-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedMainCategoryIds),'expanded-product-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedProductCategoryIds),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateMaster),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteMaster),'display-timezone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayTimezone),'search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'level-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryLevelFilter),'parent-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryParentFilter),'status-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryStatusFilter),'per-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryPerPage),'selected-category-keys' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedCategoryKeys),'selection-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categorySelectionCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2)): ?>
<?php $attributes = $__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2; ?>
<?php unset($__attributesOriginal6a515f01d60ff9ef619a4c8982e2eec2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2)): ?>
<?php $component = $__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2; ?>
<?php unset($__componentOriginal6a515f01d60ff9ef619a4c8982e2eec2); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCategoryDeleteConfirm): ?>
                <?php if (isset($component)) { $__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-delete-modal','data' => ['preview' => $categoryDeletePreview]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryDeletePreview)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5)): ?>
<?php $attributes = $__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5; ?>
<?php unset($__attributesOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5)): ?>
<?php $component = $__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5; ?>
<?php unset($__componentOriginal7fd9fa6e8f816bcb80e0643fc8ffe8d5); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/product-category.blade.php ENDPATH**/ ?>