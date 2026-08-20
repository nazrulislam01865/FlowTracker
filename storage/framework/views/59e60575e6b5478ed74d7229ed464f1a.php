<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'mainPage',
    'productChildren' => collect(),
    'subcategoryChildren' => collect(),
    'mainCategories' => collect(),
    'productCategories' => collect(),
    'parentOptions' => collect(),
    'counts' => ['main'=>0,'product'=>0,'sub'=>0],
    'productCounts' => collect(),
    'mainProductCounts' => collect(),
    'subcategoryProductCounts' => collect(),
    'productChildTotals' => collect(),
    'subcategoryChildTotals' => collect(),
    'expandedMainIds' => [],
    'expandedProductIds' => [],
    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'displayTimezone' => 'UTC',
    'search' => '',
    'levelFilter' => '',
    'parentFilter' => '',
    'statusFilter' => '',
    'perPage' => 6,
    'selectedCategoryKeys' => [],
    'selectionCount' => 0,
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
    'mainPage',
    'productChildren' => collect(),
    'subcategoryChildren' => collect(),
    'mainCategories' => collect(),
    'productCategories' => collect(),
    'parentOptions' => collect(),
    'counts' => ['main'=>0,'product'=>0,'sub'=>0],
    'productCounts' => collect(),
    'mainProductCounts' => collect(),
    'subcategoryProductCounts' => collect(),
    'productChildTotals' => collect(),
    'subcategoryChildTotals' => collect(),
    'expandedMainIds' => [],
    'expandedProductIds' => [],
    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'displayTimezone' => 'UTC',
    'search' => '',
    'levelFilter' => '',
    'parentFilter' => '',
    'statusFilter' => '',
    'perPage' => 6,
    'selectedCategoryKeys' => [],
    'selectionCount' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $expandedMain = collect($expandedMainIds)->map(fn($id)=>(int)$id);
    $expandedProduct = collect($expandedProductIds)->map(fn($id)=>(int)$id);
    $searchNeedle = mb_strtolower(trim((string)$search));
    [$parentType,$parentId] = str_contains((string)$parentFilter, ':') ? array_pad(explode(':',(string)$parentFilter,2),2,'') : ['',''];
    $parentId = (int)$parentId;
    $levelOptions = collect([
        ['id'=>'main','label'=>'Main category'],
        ['id'=>'product','label'=>'Product category'],
        ['id'=>'sub','label'=>'Subcategory'],
    ]);
    $statusOptions = collect([
        ['id'=>'active','label'=>'Active'],
        ['id'=>'inactive','label'=>'Inactive'],
    ]);
    $parentSelectOptions = collect($parentOptions)->map(fn($item)=>['id'=>$item['value'],'label'=>$item['label'],'meta'=>$item['meta']]);
    $selectedCategoryKeySet = collect($selectedCategoryKeys)->map(fn($key)=>(string)$key);
    $visibleCategoryKeys = collect($mainPage?->items() ?? [])->map(fn($main)=>'main:'.$main->id);
    foreach(collect($productChildren) as $children){
        foreach(collect($children) as $category){
            $visibleCategoryKeys->push('product:'.$category->id);
            foreach(collect($subcategoryChildren)->get((int)$category->id, collect()) as $sub){
                $visibleCategoryKeys->push('sub:'.$sub->id);
            }
        }
    }
    $visibleCategoryKeys = $visibleCategoryKeys->unique()->values();
    $allVisibleCategoriesSelected = $visibleCategoryKeys->isNotEmpty() && $visibleCategoryKeys->every(fn($key)=>$selectedCategoryKeySet->contains($key));
    $matches = function($record,string $level,?int $mainId=null,?int $productId=null) use($searchNeedle,$levelFilter,$parentType,$parentId,$statusFilter){
        if($levelFilter!=='' && $levelFilter!==$level) return false;
        if($statusFilter!=='' && $record->status!==$statusFilter) return false;
        if($searchNeedle!=='' && !str_contains(mb_strtolower($record->name.' '.$record->code),$searchNeedle)) return false;
        if($parentType==='main' && $parentId>0){
            if($level==='main') return (int)$record->id===$parentId;
            if((int)$mainId!==$parentId) return false;
        }
        if($parentType==='product' && $parentId>0){
            if($level==='main') return false;
            if($level==='product') return (int)$record->id===$parentId;
            if((int)$productId!==$parentId) return false;
        }
        return true;
    };
?>
<div class="ft-category-page">
    <header class="ft-category-page-head">
        <div>
            <h1>Product Categories</h1>
            <p>Manage the hierarchy used to organize and find products.</p>
        </div>
        <div class="ft-category-head-right">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreate): ?>
                <button type="button" class="ft-category-add" wire:click="openCategoryEditor('main')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Add category</span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash success ft-master-flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['record'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error ft-master-flash"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-category-toolbar">
        <label class="ft-category-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search category name or code" aria-label="Search category name or code">
        </label>
        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-category-filter-select','label' => 'Category level','property' => 'categoryLevelFilter','value' => $levelFilter,'placeholder' => 'Category level','options' => $levelOptions,'fixedMenu' => true,'menuWidth' => 250,'searchPlaceholder' => 'Search level…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-category-filter-select','label' => 'Category level','property' => 'categoryLevelFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($levelFilter),'placeholder' => 'Category level','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($levelOptions),'fixed-menu' => true,'menu-width' => 250,'search-placeholder' => 'Search level…']); ?>
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
        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-category-filter-select','label' => 'Parent category','property' => 'categoryParentFilter','value' => $parentFilter,'placeholder' => 'Parent category','options' => $parentSelectOptions,'fixedMenu' => true,'menuWidth' => 320,'searchPlaceholder' => 'Search parent category…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-category-filter-select','label' => 'Parent category','property' => 'categoryParentFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($parentFilter),'placeholder' => 'Parent category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($parentSelectOptions),'fixed-menu' => true,'menu-width' => 320,'search-placeholder' => 'Search parent category…']); ?>
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
        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-category-filter-select','label' => 'Status','property' => 'categoryStatusFilter','value' => $statusFilter,'placeholder' => 'Status','options' => $statusOptions,'fixedMenu' => true,'menuWidth' => 220,'searchPlaceholder' => 'Search status…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-category-filter-select','label' => 'Status','property' => 'categoryStatusFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusFilter),'placeholder' => 'Status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusOptions),'fixed-menu' => true,'menu-width' => 220,'search-placeholder' => 'Search status…']); ?>
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
        <button type="button" class="ft-category-clear" wire:click="clearCategoryFilters">Clear</button>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectionCount > 0): ?>
        <?php if (isset($component)) { $__componentOriginald1ba8159b34c502729bf74c5f4254d3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1ba8159b34c502729bf74c5f4254d3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-bulk-actions','data' => ['count' => $selectionCount,'canEdit' => $canEdit,'canDelete' => $canDelete]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-bulk-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectionCount),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEdit),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDelete)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald1ba8159b34c502729bf74c5f4254d3b)): ?>
<?php $attributes = $__attributesOriginald1ba8159b34c502729bf74c5f4254d3b; ?>
<?php unset($__attributesOriginald1ba8159b34c502729bf74c5f4254d3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald1ba8159b34c502729bf74c5f4254d3b)): ?>
<?php $component = $__componentOriginald1ba8159b34c502729bf74c5f4254d3b; ?>
<?php unset($__componentOriginald1ba8159b34c502729bf74c5f4254d3b); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-category-subbar ft-category-subbar-actions-only">
        <div class="ft-category-expand-controls">
            <button type="button" class="ft-category-expand-btn" wire:click="collapseAllCategories" title="Collapse all" aria-label="Collapse all categories">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m5.5 10.5 4.5-4 4.5 4"/>
                    <path d="m5.5 15 4.5-4 4.5 4"/>
                </svg>
            </button>
            <button type="button" class="ft-category-expand-btn is-teal" wire:click="expandAllCategories" title="Expand all" aria-label="Expand all categories">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m5.5 5 4.5 4 4.5-4"/>
                    <path d="m5.5 9.5 4.5 4 4.5-4"/>
                </svg>
            </button>
        </div>
    </div>

    <section class="ft-category-card">
        <div class="ft-category-table-scroll">
            <div class="ft-category-tree-grid ft-category-tree-grid-head">
                <span class="ft-category-check"><input type="checkbox" aria-label="Select all visible categories" <?php if($allVisibleCategoriesSelected): echo 'checked'; endif; ?> x-on:change="$wire.toggleCategoryPageSelection(<?php echo \Illuminate\Support\Js::from($visibleCategoryKeys->all())->toHtml() ?>, $event.target.checked)"></span>
                <span>Category</span><span>Code</span><span>Level</span><span>Parent</span><span>Products</span><span>Status</span><span>Updated</span><span></span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = ($mainPage?->items() ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $main): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $mainProducts = collect($productChildren)->get((int)$main->id, collect());
                    $mainIsExpanded = $expandedMain->contains((int)$main->id);
                    $mainChildTotal = (int)(collect($productChildTotals)->get((int)$main->id, 0));
                    $mainCount = (int)($mainProductCounts[mb_strtolower($main->name)] ?? 0);
                    $mainUpdated = $main->updated_at?->copy()->timezone($displayTimezone);
                ?>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-category-tree-grid ft-category-row is-main', 'is-selected' => $selectedCategoryKeySet->contains('main:'.$main->id)]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cat-main-'.e($main->id).''; ?>wire:key="cat-main-<?php echo e($main->id); ?>">
                    <span class="ft-category-check"><input class="ft-category-row-check" type="checkbox" aria-label="Select <?php echo e($main->name); ?>" <?php if($selectedCategoryKeySet->contains('main:'.$main->id)): echo 'checked'; endif; ?> wire:change="toggleCategorySelection('main', <?php echo e($main->id); ?>)"></span>
                    <div class="ft-category-name">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mainChildTotal > 0): ?>
                            <button type="button" class="ft-category-chevron" wire:click="toggleCategoryExpansion('main', <?php echo e($main->id); ?>)" aria-label="<?php echo e($mainIsExpanded ? 'Collapse' : 'Expand'); ?> <?php echo e($main->name); ?>"><?php echo e($mainIsExpanded ? '⌄' : '›'); ?></button>
                        <?php else: ?><span class="ft-category-chevron is-placeholder"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="ft-category-tag-icon">◇</span><strong><?php echo e($main->name); ?></strong>
                    </div>
                    <span><?php echo e($main->code); ?></span>
                    <span><b class="ft-category-level is-main">Main category</b></span>
                    <span class="ft-category-muted">—</span>
                    <strong><?php echo e(number_format($mainCount)); ?></strong>
                    <span><b class="ft-category-status <?php echo e($main->status==='active'?'':'is-off'); ?>"><?php echo e($main->status==='active'?'Active':'Inactive'); ?></b></span>
                    <span class="ft-category-updated" title="<?php echo e($mainUpdated?->format('M j, Y g:i A')); ?> <?php echo e($displayTimezone); ?>"><?php echo e($mainUpdated?->diffForHumans() ?? '—'); ?></span>
                    <?php if (isset($component)) { $__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-action-menu','data' => ['level' => 'main','recordId' => $main->id,'isActive' => $main->status==='active','canEdit' => $canEdit,'canDelete' => $canDelete,'canCreate' => $canCreate]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-action-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => 'main','record-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($main->id),'is-active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($main->status==='active'),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEdit),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDelete),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreate)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83)): ?>
<?php $attributes = $__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83; ?>
<?php unset($__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83)): ?>
<?php $component = $__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83; ?>
<?php unset($__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83); ?>
<?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mainIsExpanded && $levelFilter !== 'main'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mainProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $subs = collect($subcategoryChildren)->get((int)$category->id, collect());
                            $subcategoryTotal = (int)(collect($subcategoryChildTotals)->get((int)$category->id, 0));
                            $categoryIsExpanded = $expandedProduct->contains((int)$category->id);
                            $categoryUpdated = $category->updated_at?->copy()->timezone($displayTimezone);
                        ?>
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-category-tree-grid ft-category-row', 'is-selected' => $selectedCategoryKeySet->contains('product:'.$category->id)]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cat-product-'.e($category->id).''; ?>wire:key="cat-product-<?php echo e($category->id); ?>">
                                <span class="ft-category-check"><input class="ft-category-row-check" type="checkbox" aria-label="Select <?php echo e($category->name); ?>" <?php if($selectedCategoryKeySet->contains('product:'.$category->id)): echo 'checked'; endif; ?> wire:change="toggleCategorySelection('product', <?php echo e($category->id); ?>)"></span>
                                <div class="ft-category-name is-indent-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subcategoryTotal > 0): ?>
                                        <button type="button" class="ft-category-chevron" wire:click="toggleCategoryExpansion('product', <?php echo e($category->id); ?>)" aria-label="<?php echo e($categoryIsExpanded ? 'Collapse' : 'Expand'); ?> <?php echo e($category->name); ?>"><?php echo e($categoryIsExpanded ? '⌄' : '›'); ?></button>
                                    <?php else: ?><span class="ft-category-chevron is-placeholder"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <strong><?php echo e($category->name); ?></strong>
                                </div>
                                <span><?php echo e($category->code); ?></span>
                                <span><b class="ft-category-level">Product category</b></span>
                                <span class="ft-category-muted"><?php echo e($main->name); ?></span>
                                <strong><?php echo e(number_format((int)($productCounts[$category->id] ?? 0))); ?></strong>
                                <span><b class="ft-category-status <?php echo e($category->status==='active'?'':'is-off'); ?>"><?php echo e($category->status==='active'?'Active':'Inactive'); ?></b></span>
                                <span class="ft-category-updated" title="<?php echo e($categoryUpdated?->format('M j, Y g:i A')); ?> <?php echo e($displayTimezone); ?>"><?php echo e($categoryUpdated?->diffForHumans() ?? '—'); ?></span>
                                <?php if (isset($component)) { $__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-action-menu','data' => ['level' => 'product','recordId' => $category->id,'isActive' => $category->status==='active','canEdit' => $canEdit,'canDelete' => $canDelete,'canCreate' => $canCreate]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-action-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => 'product','record-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category->id),'is-active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category->status==='active'),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEdit),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDelete),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreate)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83)): ?>
<?php $attributes = $__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83; ?>
<?php unset($__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83)): ?>
<?php $component = $__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83; ?>
<?php unset($__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83); ?>
<?php endif; ?>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoryIsExpanded && in_array($levelFilter,['','sub'],true)): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $subUpdated = $sub->updated_at?->copy()->timezone($displayTimezone);
                                        $subCountKey = $category->id.'|'.mb_strtolower($sub->name);
                                    ?>
                                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-category-tree-grid ft-category-row', 'is-selected' => $selectedCategoryKeySet->contains('sub:'.$sub->id)]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cat-sub-'.e($sub->id).''; ?>wire:key="cat-sub-<?php echo e($sub->id); ?>">
                                        <span class="ft-category-check"><input class="ft-category-row-check" type="checkbox" aria-label="Select <?php echo e($sub->name); ?>" <?php if($selectedCategoryKeySet->contains('sub:'.$sub->id)): echo 'checked'; endif; ?> wire:change="toggleCategorySelection('sub', <?php echo e($sub->id); ?>)"></span>
                                        <div class="ft-category-name is-indent-2"><span class="ft-category-chevron is-placeholder"></span><span><?php echo e($sub->name); ?></span></div>
                                        <span><?php echo e($sub->code); ?></span>
                                        <span><b class="ft-category-level">Subcategory</b></span>
                                        <span><?php echo e($category->name); ?></span>
                                        <strong><?php echo e(number_format((int)($subcategoryProductCounts[$subCountKey] ?? 0))); ?></strong>
                                        <span><b class="ft-category-status <?php echo e($sub->status==='active'?'':'is-off'); ?>"><?php echo e($sub->status==='active'?'Active':'Inactive'); ?></b></span>
                                        <span class="ft-category-updated" title="<?php echo e($subUpdated?->format('M j, Y g:i A')); ?> <?php echo e($displayTimezone); ?>"><?php echo e($subUpdated?->diffForHumans() ?? '—'); ?></span>
                                        <?php if (isset($component)) { $__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.category-action-menu','data' => ['level' => 'sub','recordId' => $sub->id,'isActive' => $sub->status==='active','canEdit' => $canEdit,'canDelete' => $canDelete,'canCreate' => $canCreate]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.category-action-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => 'sub','record-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sub->id),'is-active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sub->status==='active'),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEdit),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDelete),'can-create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreate)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83)): ?>
<?php $attributes = $__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83; ?>
<?php unset($__attributesOriginalf9b5d9e63d5eeac6470e649b1ca99b83); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83)): ?>
<?php $component = $__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83; ?>
<?php unset($__componentOriginalf9b5d9e63d5eeac6470e649b1ca99b83); ?>
<?php endif; ?>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php
                                    $loadedSubcategories = $subs->count();
                                    $subProgress = $subcategoryTotal > 0 ? min(100, (int) round(($loadedSubcategories / $subcategoryTotal) * 100)) : 100;
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loadedSubcategories < $subcategoryTotal): ?>
                                    <div class="ft-category-load-row is-sub" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cat-product-more-'.e($category->id).''; ?>wire:key="cat-product-more-<?php echo e($category->id); ?>">
                                        <span>Showing <?php echo e(number_format($loadedSubcategories)); ?> of <?php echo e(number_format($subcategoryTotal)); ?> subcategories</span>
                                        <span class="ft-category-load-progress" aria-hidden="true"><i style="width:<?php echo e($subProgress); ?>%"></i></span>
                                        <button type="button" wire:click="loadMoreCategorySubcategories(<?php echo e($category->id); ?>)" wire:loading.attr="disabled" wire:target="loadMoreCategorySubcategories(<?php echo e($category->id); ?>)">Load 3 more</button>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <?php
                        $loadedMainChildren = $mainProducts->count();
                        $mainProgress = $mainChildTotal > 0 ? min(100, (int) round(($loadedMainChildren / $mainChildTotal) * 100)) : 100;
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loadedMainChildren < $mainChildTotal): ?>
                        <div class="ft-category-load-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cat-main-more-'.e($main->id).''; ?>wire:key="cat-main-more-<?php echo e($main->id); ?>">
                            <span>Showing <?php echo e(number_format($loadedMainChildren)); ?> of <?php echo e(number_format($mainChildTotal)); ?> product categories</span>
                            <span class="ft-category-load-progress" aria-hidden="true"><i style="width:<?php echo e($mainProgress); ?>%"></i></span>
                            <button type="button" wire:click="loadMoreCategoryProducts(<?php echo e($main->id); ?>)" wire:loading.attr="disabled" wire:target="loadMoreCategoryProducts(<?php echo e($main->id); ?>)">Load 4 more</button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-category-empty">No categories found.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mainPage): ?>
            <footer class="ft-category-footer">
                <div class="ft-category-footer-left">
                    <span class="ft-category-footer-count">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mainPage->total()): ?>
                            Showing <b><?php echo e($mainPage->firstItem()); ?>–<?php echo e($mainPage->lastItem()); ?></b> of <b><?php echo e(number_format($mainPage->total())); ?></b> main categories
                        <?php else: ?>
                            Showing <b>0</b> main categories
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <label>Rows per page
                        <select wire:model.live="categoryPerPage">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [6,10,20,50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($size); ?>"><?php echo e($size); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </label>
                </div>
                <span>Page <?php echo e($mainPage->currentPage()); ?> of <?php echo e(max(1,$mainPage->lastPage())); ?></span>
                <div class="ft-category-pages">
                    <button type="button" wire:click="setPage(1, 'masterPage')" <?php if($mainPage->onFirstPage()): echo 'disabled'; endif; ?> aria-label="First page">|‹</button>
                    <button type="button" wire:click="previousPage('masterPage')" <?php if($mainPage->onFirstPage()): echo 'disabled'; endif; ?> aria-label="Previous page">‹</button>
                    <?php
                        $start=max(1,$mainPage->currentPage()-1); $end=min($mainPage->lastPage(),$start+3); $start=max(1,$end-3);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($page=$start;$page<=$end;$page++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" wire:click="setPage(<?php echo e($page); ?>, 'masterPage')" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-active'=>$page===$mainPage->currentPage()]); ?>"><?php echo e($page); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" wire:click="nextPage('masterPage')" <?php if(!$mainPage->hasMorePages()): echo 'disabled'; endif; ?> aria-label="Next page">›</button>
                    <button type="button" wire:click="setPage(<?php echo e(max(1,$mainPage->lastPage())); ?>, 'masterPage')" <?php if(!$mainPage->hasMorePages()): echo 'disabled'; endif; ?> aria-label="Last page">›|</button>
                </div>
            </footer>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/category-list.blade.php ENDPATH**/ ?>