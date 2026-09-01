<?php
    $supplier = $supplierDetail;
    $products = collect($supplierDetailProducts);
    $supplierName = trim((string) $supplier?->name);
    $initials = collect(preg_split('/\s+/u', $supplierName, -1, PREG_SPLIT_NO_EMPTY))
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $contactPerson = trim((string) data_get($supplier?->metadata, 'contact_person'));
    $email = trim((string) data_get($supplier?->metadata, 'email'));
    $phone = trim((string) data_get($supplier?->metadata, 'phone'));
    $createdAt = $supplier?->created_at?->copy()->timezone($displayTimezone);
    $updatedAt = $supplier?->updated_at?->copy()->timezone($displayTimezone);
?>

<div class="ft-supplier-detail-page">
    <div class="ft-supplier-detail-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo e(route('master-data', ['group' => 'supplier'])); ?>" wire:navigate>Suppliers</a>
        <span>/</span>
        <strong><?php echo e($supplier->name); ?></strong>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="flash success ft-master-flash"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <header class="ft-supplier-detail-head">
        <div class="ft-supplier-detail-identity">
            <span class="ft-supplier-detail-avatar" aria-hidden="true"><?php echo e($initials ?: 'S'); ?></span>
            <div>
                <div class="ft-supplier-detail-title-row">
                    <h1><?php echo e($supplier->name); ?></h1>
                    <?php if (isset($component)) { $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.status-badge','data' => ['status' => $supplier->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->status)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $attributes = $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $component = $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?>
                </div>
                <p>Supplier reference <?php echo e($supplier->code ?: '—'); ?></p>
            </div>
        </div>

        <div class="ft-supplier-detail-actions">
            <a href="<?php echo e(route('master-data', ['group' => 'supplier'])); ?>" wire:navigate class="ft-supplier-list-button is-secondary">
                Back to suppliers
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditMaster): ?>
                <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'edit_supplier' => $supplier->id])); ?>" wire:navigate class="ft-supplier-list-button is-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                    <span>Edit supplier</span>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </header>

    <div class="ft-supplier-detail-summary">
        <?php if (isset($component)) { $__componentOriginal5df04d245990620a30c8dba7f65be508 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5df04d245990620a30c8dba7f65be508 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.stat-card','data' => ['label' => 'Assigned products','value' => number_format($products->count()),'icon' => 'product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Assigned products','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($products->count())),'icon' => 'product']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.stat-card','data' => ['label' => 'Supplier status','value' => $supplier->status === 'active' ? 'Active' : 'Inactive','icon' => 'supplier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier status','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->status === 'active' ? 'Active' : 'Inactive'),'icon' => 'supplier']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.stat-card','data' => ['label' => 'Last updated','value' => $updatedAt?->format('M j, Y') ?? '—','icon' => 'clock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Last updated','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($updatedAt?->format('M j, Y') ?? '—'),'icon' => 'clock']); ?>
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

    <div class="ft-supplier-detail-grid">
        <section class="ft-supplier-detail-card">
            <div class="ft-supplier-detail-card-head">
                <div>
                    <h2>Supplier information</h2>
                    <p>Primary contact and supplier record information.</p>
                </div>
            </div>

            <div class="ft-supplier-detail-fields">
                <div class="ft-supplier-detail-field">
                    <span>Supplier name</span>
                    <strong><?php echo e($supplier->name); ?></strong>
                </div>
                <div class="ft-supplier-detail-field">
                    <span>Reference code</span>
                    <strong><?php echo e($supplier->code ?: '—'); ?></strong>
                </div>
                <div class="ft-supplier-detail-field">
                    <span>Contact person</span>
                    <strong><?php echo e($contactPerson !== '' ? $contactPerson : '—'); ?></strong>
                </div>
                <div class="ft-supplier-detail-field">
                    <span>Email</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($email !== ''): ?>
                        <a href="mailto:<?php echo e($email); ?>"><?php echo e($email); ?></a>
                    <?php else: ?>
                        <strong>—</strong>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-supplier-detail-field">
                    <span>Phone</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone !== ''): ?>
                        <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phone)); ?>"><?php echo e($phone); ?></a>
                    <?php else: ?>
                        <strong>—</strong>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-supplier-detail-field">
                    <span>Status</span>
                    <?php if (isset($component)) { $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.status-badge','data' => ['status' => $supplier->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->status)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $attributes = $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $component = $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?>
                </div>
            </div>
        </section>

        <aside class="ft-supplier-detail-card ft-supplier-detail-record-card">
            <div class="ft-supplier-detail-card-head">
                <div>
                    <h2>Record details</h2>
                    <p>Audit information for this supplier.</p>
                </div>
            </div>
            <div class="ft-supplier-detail-record-list">
                <div><span>Created</span><strong><?php echo e($createdAt?->format('M j, Y · g:i A') ?? '—'); ?></strong></div>
                <div><span>Last updated</span><strong><?php echo e($updatedAt?->format('M j, Y · g:i A') ?? '—'); ?></strong></div>
                <div><span>Created by</span><strong><?php echo e($supplier->creator?->name ?: '—'); ?></strong></div>
                <div><span>Products linked</span><strong><?php echo e(number_format($products->count())); ?></strong></div>
            </div>
        </aside>
    </div>

    <section class="ft-supplier-detail-card ft-supplier-detail-products-card">
        <div class="ft-supplier-detail-card-head is-with-action">
            <div>
                <h2>Assigned products</h2>
                <p>Products currently linked to this supplier.</p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('catalog_products', 'view')): ?>
                <a href="<?php echo e(route('master-data', ['group' => 'product', 'supplier_id' => $supplier->id])); ?>" wire:navigate class="ft-supplier-list-button is-secondary">
                    View all products
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isEmpty()): ?>
            <div class="ft-supplier-detail-empty">
                <strong>No products assigned</strong>
                <span>Link products from the Product catalogue when this supplier is ready to be used.</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('catalog_products', 'view')): ?>
                    <a href="<?php echo e(route('master-data', ['group' => 'product', 'supplier_id' => $supplier->id])); ?>" wire:navigate>Open product catalogue</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="ft-supplier-detail-product-table-wrap">
                <table class="ft-supplier-detail-product-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th aria-label="Actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products->take(12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td><strong><?php echo e($product->name); ?></strong></td>
                                <td><?php echo e($product->productDisplayCode()); ?></td>
                                <td><?php echo e($product->productClassificationPath() ?: ($product->parent?->name ?: '—')); ?></td>
                                <td><?php if (isset($component)) { $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.status-badge','data' => ['status' => $product->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->status)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $attributes = $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $component = $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('catalog_products', 'view')): ?>
                                        <a href="<?php echo e(route('master-data', ['group' => 'product', 'open' => $product->id])); ?>" wire:navigate>View</a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->count() > 12): ?>
                <div class="ft-supplier-detail-product-footer">
                    Showing 12 of <?php echo e(number_format($products->count())); ?> linked products.
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('catalog_products', 'view')): ?>
                        <a href="<?php echo e(route('master-data', ['group' => 'product', 'supplier_id' => $supplier->id])); ?>" wire:navigate>View all products</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/supplier-detail.blade.php ENDPATH**/ ?>