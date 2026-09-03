<div class="ft-supplier-create-page" data-ft-feedback-scope="form">
    <div class="ft-supplier-create-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo e(route('master-data', ['group' => 'supplier'])); ?>" wire:navigate>Suppliers</a>
        <span>/</span>
        <strong>Create supplier</strong>
    </div>

    <header class="ft-supplier-create-head">
        <h1>Create supplier</h1>
        <p>Add the essentials now. More details can be added later.</p>
    </header>

    <div class="ft-supplier-create-layout">
        <div class="ft-supplier-create-main">
            <?php if (isset($component)) { $__componentOriginal5394d01e7c0f10a03f1a16ad75509428 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5394d01e7c0f10a03f1a16ad75509428 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.form-card','data' => ['title' => 'Supplier information','copy' => 'Only the supplier name is required.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.form-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Supplier information','copy' => 'Only the supplier name is required.']); ?>
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

                        <input wire:model.blur="name" type="text" placeholder="e.g. Guangzhou Apex Sports" autocomplete="organization">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Contact person','error' => 'supplierContactPerson']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Contact person','error' => 'supplierContactPerson']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="supplierContactPerson" type="text" placeholder="Full name" autocomplete="name">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Email','error' => 'supplierEmail']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Email','error' => 'supplierEmail']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="supplierEmail" type="email" placeholder="name@company.com" autocomplete="email">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Phone','error' => 'supplierPhone']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Phone','error' => 'supplierPhone']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <input wire:model.blur="supplierPhone" type="tel" placeholder="Country code + number" autocomplete="tel">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Status','error' => 'status']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status','error' => 'status']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <select wire:model="status">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.form-card','data' => ['title' => 'Assign products','badge' => 'Optional','copy' => 'Paste product codes separated by commas, spaces, or new lines. Matching products are added automatically.','class' => 'ft-supplier-assign-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.form-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Assign products','badge' => 'Optional','copy' => 'Paste product codes separated by commas, spaces, or new lines. Matching products are added automatically.','class' => 'ft-supplier-assign-card']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


                <?php if (isset($component)) { $__componentOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal662a7bf3ca5cfd0cd6e338adebac1b2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.field','data' => ['label' => 'Product codes','error' => 'supplierProductCodes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Product codes','error' => 'supplierProductCodes']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div
                        class="ft-supplier-codebox"
                        x-data
                        x-on:click="$refs.productCodeInput.focus()"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierCreateCodeRows->isNotEmpty()): ?>
                            <div class="ft-supplier-code-tokens" aria-label="Entered product codes">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $supplierCreateCodeRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button
                                        type="button"
                                        class="ft-supplier-code-token <?php echo e($row['valid'] ? '' : 'is-invalid'); ?>"
                                        wire:click.stop="removeSupplierProductCode('<?php echo e($row['code']); ?>')"
                                        title="Remove <?php echo e($row['code']); ?>"
                                    >
                                        <span><?php echo e($row['code']); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$row['valid']): ?><small>not found</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17"/></svg>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <input
                            x-ref="productCodeInput"
                            wire:model="supplierCodeDraft"
                            wire:keydown.enter.prevent="commitSupplierProductCodes"
                            wire:keydown.tab="commitSupplierProductCodes"
                            wire:blur="commitSupplierProductCodes"
                            x-on:paste="setTimeout(() => $wire.commitSupplierProductCodes($refs.productCodeInput.value), 0)"
                            type="text"
                            placeholder="Try PRD-1007, PRD-1009"
                            aria-label="Product codes"
                            autocomplete="off"
                        >
                    </div>
                    <div class="ft-supplier-code-help">Press Enter after each code. Unknown codes are flagged before saving.</div>
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

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supplierCreateCodeRows->where('valid', true)->isNotEmpty()): ?>
                    <div class="ft-supplier-product-preview" aria-label="Products to assign">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $supplierCreateCodeRows->where('valid', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="ft-supplier-product-preview-row">
                                <div>
                                    <strong><?php echo e($row['name']); ?></strong>
                                    <span><?php echo e($row['code']); ?> · <?php echo e($row['category']); ?></span>
                                </div>
                                <span class="ft-supplier-preview-state <?php echo e($row['has_supplier'] ? 'is-reassign' : 'is-ready'); ?>">
                                    <?php echo e($row['has_supplier'] ? 'Will also link' : 'Ready'); ?>

                                </span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ft-supplier-tip">
                    <strong>Need to assign many products visually?</strong>
                    <span>Save the supplier first, then use checkboxes on the Product list and choose “Assign supplier”.</span>
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

            <div class="ft-supplier-create-actions">
                <button type="button" class="ft-supplier-secondary-button" wire:click="cancelSupplierCreate">Cancel</button>
                <button
                    type="button"
                    class="ft-supplier-primary-button"
                    wire:click="createSupplier"
                    wire:loading.attr="disabled"
                    wire:target="createSupplier"
                >
                    <span wire:loading.remove wire:target="createSupplier">Create supplier</span>
                    <span wire:loading wire:target="createSupplier">Creating…</span>
                </button>
            </div>
        </div>

    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/supplier-create.blade.php ENDPATH**/ ?>