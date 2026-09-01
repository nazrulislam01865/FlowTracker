<?php
    $supplier = $supplierDetail;
    $products = collect($supplierDetailProducts);
?>

<div class="ft-supplier-create-page ft-supplier-edit-page" data-ft-feedback-scope="form">
    <div class="ft-supplier-create-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo e(route('master-data', ['group' => 'supplier'])); ?>" wire:navigate>Suppliers</a>
        <span>/</span>
        <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'supplier' => $supplier->id])); ?>" wire:navigate><?php echo e($supplier->name); ?></a>
        <span>/</span>
        <strong>Edit</strong>
    </div>

    <header class="ft-supplier-create-head ft-supplier-edit-head">
        <div>
            <h1>Edit supplier</h1>
            <p>Update supplier contact information and availability without changing existing product links.</p>
        </div>
        <?php if (isset($component)) { $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.status-badge','data' => ['status' => $supplierEditStatus]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplierEditStatus)]); ?>
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
    </header>

    <div class="ft-supplier-create-layout">
        <div class="ft-supplier-create-main">
            <?php if (isset($component)) { $__componentOriginal5394d01e7c0f10a03f1a16ad75509428 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5394d01e7c0f10a03f1a16ad75509428 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.form-card','data' => ['title' => 'Supplier information','copy' => 'Keep the supplier name and contact information current.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.form-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Supplier information','copy' => 'Keep the supplier name and contact information current.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="ft-supplier-form-grid">
                    <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Supplier name','required' => true,'error' => 'name','wide' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier name','required' => true,'error' => 'name','wide' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="name" type="text" placeholder="Supplier name" autocomplete="organization">
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $attributes = $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $component = $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Supplier reference','help' => 'The reference is generated by FlowTrack and cannot be changed.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier reference','help' => 'The reference is generated by FlowTrack and cannot be changed.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input type="text" value="<?php echo e($supplier->code); ?>" disabled readonly>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $attributes = $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $component = $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Status','error' => 'supplierEditStatus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status','error' => 'supplierEditStatus']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <select wire:model="supplierEditStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $attributes = $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $component = $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Contact person','error' => 'supplierEditContactPerson']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Contact person','error' => 'supplierEditContactPerson']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="supplierEditContactPerson" type="text" placeholder="Full name" autocomplete="name">
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $attributes = $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $component = $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Email','error' => 'supplierEditEmail']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Email','error' => 'supplierEditEmail']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="supplierEditEmail" type="email" placeholder="name@company.com" autocomplete="email">
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $attributes = $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $component = $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Phone','error' => 'supplierEditPhone','wide' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Phone','error' => 'supplierEditPhone','wide' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="supplierEditPhone" type="tel" placeholder="Country code + number" autocomplete="tel">
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $attributes = $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b)): ?>
<?php $component = $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b; ?>
<?php unset($__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b); ?>
<?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5394d01e7c0f10a03f1a16ad75509428)): ?>
<?php $attributes = $__attributesOriginal5394d01e7c0f10a03f1a16ad75509428; ?>
<?php unset($__attributesOriginal5394d01e7c0f10a03f1a16ad75509428); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5394d01e7c0f10a03f1a16ad75509428)): ?>
<?php $component = $__componentOriginal5394d01e7c0f10a03f1a16ad75509428; ?>
<?php unset($__componentOriginal5394d01e7c0f10a03f1a16ad75509428); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal5394d01e7c0f10a03f1a16ad75509428 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5394d01e7c0f10a03f1a16ad75509428 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.form-card','data' => ['title' => 'Assigned products','badge' => number_format($products->count()).' linked','copy' => 'Product relationships are managed from the Product catalogue so supplier editing never removes an existing link accidentally.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.form-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Assigned products','badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($products->count()).' linked'),'copy' => 'Product relationships are managed from the Product catalogue so supplier editing never removes an existing link accidentally.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isEmpty()): ?>
                    <div class="ft-supplier-edit-products-empty">No products are currently assigned to this supplier.</div>
                <?php else: ?>
                    <div class="ft-supplier-edit-product-tags">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products->take(12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('master-data', ['group' => 'product', 'open' => $product->id])); ?>" wire:navigate>
                                <strong><?php echo e($product->productDisplayCode()); ?></strong>
                                <span><?php echo e($product->name); ?></span>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->count() > 12): ?>
                            <span class="ft-supplier-edit-product-more">+<?php echo e($products->count() - 12); ?> more</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('catalog_products', 'view')): ?>
                    <div class="ft-supplier-edit-product-action">
                        <a href="<?php echo e(route('master-data', ['group' => 'product', 'supplier_id' => $supplier->id])); ?>" wire:navigate class="ft-supplier-list-button is-secondary">
                            Manage assigned products
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5394d01e7c0f10a03f1a16ad75509428)): ?>
<?php $attributes = $__attributesOriginal5394d01e7c0f10a03f1a16ad75509428; ?>
<?php unset($__attributesOriginal5394d01e7c0f10a03f1a16ad75509428); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5394d01e7c0f10a03f1a16ad75509428)): ?>
<?php $component = $__componentOriginal5394d01e7c0f10a03f1a16ad75509428; ?>
<?php unset($__componentOriginal5394d01e7c0f10a03f1a16ad75509428); ?>
<?php endif; ?>

            <div class="ft-supplier-create-actions">
                <button type="button" class="ft-supplier-secondary-button" wire:click="cancelSupplierEdit">Cancel</button>
                <button
                    type="button"
                    class="ft-supplier-primary-button"
                    wire:click="saveSupplier"
                    wire:loading.attr="disabled"
                    wire:target="saveSupplier"
                >
                    <span wire:loading.remove wire:target="saveSupplier">Save changes</span>
                    <span wire:loading wire:target="saveSupplier">Saving…</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/supplier-edit.blade.php ENDPATH**/ ?>