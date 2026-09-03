<?php
    $supplierProducts = collect($supplierListSummary['products_by_supplier'] ?? []);
    $assignedProducts = (int) ($supplierListSummary['assigned_products'] ?? 0);
    $totalProducts = (int) ($supplierListSummary['total_products'] ?? 0);
?>

<div class="ft-supplier-list-page">
    <div class="ft-supplier-list-breadcrumb" aria-label="Breadcrumb">
        <span>Master data</span><i>/</i><strong>Suppliers</strong>
    </div>

    <header class="ft-supplier-list-head">
        <div>
            <h1>Suppliers</h1>
            <p>Manage supplier information and see which products each supplier supports.</p>
        </div>
        <div class="ft-supplier-list-head-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('catalog_products', 'view')): ?>
                <a href="<?php echo e(route('master-data', ['group' => 'product', 'supplier_assign' => 1])); ?>" wire:navigate class="ft-supplier-list-button is-secondary">Assign from products</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateMaster): ?>
                <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'create' => 1])); ?>" wire:navigate class="ft-supplier-list-button is-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Create supplier</span>
                </a>
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

    <div class="ft-supplier-list-summary">
        <?php if (isset($component)) { $__componentOriginal5df04d245990620a30c8dba7f65be508 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5df04d245990620a30c8dba7f65be508 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.stat-card','data' => ['label' => 'Active suppliers','value' => number_format((int) ($supplierListSummary['active_suppliers'] ?? 0)),'icon' => 'supplier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Active suppliers','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((int) ($supplierListSummary['active_suppliers'] ?? 0))),'icon' => 'supplier']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5df04d245990620a30c8dba7f65be508)): ?>
<?php $attributes = $__attributesOriginal5df04d245990620a30c8dba7f65be508; ?>
<?php unset($__attributesOriginal5df04d245990620a30c8dba7f65be508); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5df04d245990620a30c8dba7f65be508)): ?>
<?php $component = $__componentOriginal5df04d245990620a30c8dba7f65be508; ?>
<?php unset($__componentOriginal5df04d245990620a30c8dba7f65be508); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal5df04d245990620a30c8dba7f65be508 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5df04d245990620a30c8dba7f65be508 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.stat-card','data' => ['label' => 'Products assigned','value' => number_format($assignedProducts).' of '.number_format($totalProducts),'icon' => 'product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Products assigned','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($assignedProducts).' of '.number_format($totalProducts)),'icon' => 'product']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5df04d245990620a30c8dba7f65be508)): ?>
<?php $attributes = $__attributesOriginal5df04d245990620a30c8dba7f65be508; ?>
<?php unset($__attributesOriginal5df04d245990620a30c8dba7f65be508); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5df04d245990620a30c8dba7f65be508)): ?>
<?php $component = $__componentOriginal5df04d245990620a30c8dba7f65be508; ?>
<?php unset($__componentOriginal5df04d245990620a30c8dba7f65be508); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal5df04d245990620a30c8dba7f65be508 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5df04d245990620a30c8dba7f65be508 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.stat-card','data' => ['label' => 'Products without supplier','value' => number_format((int) ($supplierListSummary['unassigned_products'] ?? 0)),'icon' => 'attention']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Products without supplier','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((int) ($supplierListSummary['unassigned_products'] ?? 0))),'icon' => 'attention']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5df04d245990620a30c8dba7f65be508)): ?>
<?php $attributes = $__attributesOriginal5df04d245990620a30c8dba7f65be508; ?>
<?php unset($__attributesOriginal5df04d245990620a30c8dba7f65be508); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5df04d245990620a30c8dba7f65be508)): ?>
<?php $component = $__componentOriginal5df04d245990620a30c8dba7f65be508; ?>
<?php unset($__componentOriginal5df04d245990620a30c8dba7f65be508); ?>
<?php endif; ?>
    </div>

    <section class="ft-supplier-list-card">
        <div class="ft-supplier-list-toolbar">
            <label class="ft-supplier-list-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Search supplier or product code"
                    aria-label="Search supplier or product code"
                >
            </label>

            <div class="ft-supplier-list-toolbar-actions">
                <select wire:model.live="supplierStatus" class="ft-supplier-list-filter" aria-label="Filter supplier status">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button type="button" class="ft-supplier-list-button is-secondary" wire:click="exportSuppliers" wire:loading.attr="disabled" wire:target="exportSuppliers">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12M7 10l5 5 5-5M5 20h14"/></svg>
                    <span wire:loading.remove wire:target="exportSuppliers">Export</span>
                    <span wire:loading wire:target="exportSuppliers">Exporting…</span>
                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$recordsReady): ?>
            <?php echo $__env->make('livewire.shared.table-rows-placeholder', ['columns' => 6, 'rows' => 8], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <div class="ft-supplier-list-table-wrap" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'supplier-records'; ?>wire:key="supplier-records">
                <table class="ft-supplier-list-table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Contact</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th aria-label="Actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal2596f0899356c8ae81409a52e188a3af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2596f0899356c8ae81409a52e188a3af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.list-row','data' => ['supplier' => $supplier,'products' => $supplierProducts->get((int) $supplier->id, collect()),'displayTimezone' => $displayTimezone,'canEdit' => $canEditMaster]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.list-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierProducts->get((int) $supplier->id, collect())),'display-timezone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayTimezone),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditMaster)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2596f0899356c8ae81409a52e188a3af)): ?>
<?php $attributes = $__attributesOriginal2596f0899356c8ae81409a52e188a3af; ?>
<?php unset($__attributesOriginal2596f0899356c8ae81409a52e188a3af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2596f0899356c8ae81409a52e188a3af)): ?>
<?php $component = $__componentOriginal2596f0899356c8ae81409a52e188a3af; ?>
<?php unset($__componentOriginal2596f0899356c8ae81409a52e188a3af); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6">
                                    <div class="ft-supplier-list-empty">
                                        <strong>No suppliers found</strong>
                                        <span>Try a different search or filter.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rows->total() > 30): ?>
                <div class="ft-supplier-list-pagination">
                    <span>Showing <b><?php echo e($rows->firstItem() ?? 0); ?>–<?php echo e($rows->lastItem() ?? 0); ?></b> of <?php echo e(number_format($rows->total())); ?> suppliers</span>
                    <div>
                        <button type="button" wire:click="previousPage('masterPage')" <?php if($rows->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                        <span>Page <?php echo e($rows->currentPage()); ?> of <?php echo e($rows->lastPage()); ?></span>
                        <button type="button" wire:click="nextPage('masterPage')" <?php if(!$rows->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/supplier-list.blade.php ENDPATH**/ ?>