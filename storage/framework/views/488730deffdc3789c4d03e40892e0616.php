<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingSupplierCurrencyTable && $currencyTableSupplierId === (int) $supplierId): ?>
    <?php
        $currencyChoices = app(\App\Services\SupplierBridge\SupplierCurrencyPriceTables::class)
            ->currencyOptions((int) $product->workspace_id);
    ?>
    <div class="ft-product-client-price-entry ft-price-editor">
        <div class="ft-product-price-section-header">
            <strong><?php echo e($currencyTableOriginalCurrency !== '' ? 'Edit additional currency table' : 'Add currency price table'); ?></strong>
        </div>
        <label class="ft-product-field" style="display:block;max-width:280px;margin:12px 0">
            <span>Currency</span>
            <select wire:model="currencyTableCurrency" <?php if($currencyTableOriginalCurrency !== ''): echo 'disabled'; endif; ?>>
                <option value="">Select currency</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currencyChoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currencyChoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($currencyChoice['code']); ?>"><?php echo e($currencyChoice['code']); ?> — <?php echo e($currencyChoice['name']); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </label>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currencyTableCurrency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-product-matrix-toolbar">
            <small>Enter quantity, supplier price and optional charges. This additional table does not replace approved primary supplier costs.</small>
            <button type="button" class="ft-product-matrix-add" wire:click="addSupplierCurrencyTier">+ Add quantity</button>
        </div>
        <div class="ft-product-price-preview-wrap ft-product-matrix-scroll" role="region" aria-label="Additional currency pricing" tabindex="0">
            <table class="ft-product-price-preview ft-product-price-matrix">
                <thead><tr><th scope="col">Quantity</th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currencyTableTiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tierIndex => $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <th scope="col" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'currency-table-quantity-'.e($tierIndex).''; ?>wire:key="currency-table-quantity-<?php echo e($tierIndex); ?>">
                            <div class="ft-product-matrix-quantity">
                                <input type="number" min="1" max="100000000" step="1" inputmode="numeric" placeholder="Quantity"
                                    aria-label="Quantity <?php echo e($tierIndex + 1); ?>"
                                    wire:model.blur="currencyTableTiers.<?php echo e($tierIndex); ?>.quantity">
                                <button type="button" class="ft-product-matrix-remove" aria-label="Remove quantity <?php echo e($tierIndex + 1); ?>"
                                    wire:click="removeSupplierCurrencyTier(<?php echo e($tierIndex); ?>)">×</button>
                            </div>
                        </th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['price'=>'Supplier price','remote_surcharge'=>'Remote surcharge','standard_shipment'=>'Standard shipment','urgent_shipment'=>'Urgent shipment']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$heading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr><th scope="row"><?php echo e($heading); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currencyTableTiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tierIndex => $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td><input type="number" min="0" max="999999999.99" step="0.000001" placeholder="—"
                                    aria-label="<?php echo e($heading); ?> for quantity column <?php echo e($tierIndex + 1); ?>"
                                    wire:model.blur="currencyTableTiers.<?php echo e($tierIndex); ?>.<?php echo e($field); ?>"></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currencyTableTiers'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-product-form-bottom-actions" style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currencyTableOriginalCurrency !== ''): ?>
                <button type="button" class="ft-product-page-btn is-secondary" wire:click="deleteSupplierCurrencyTable"
                    wire:confirm="Remove this additional currency price table?">Remove table</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="ft-product-page-btn is-secondary" wire:click="closeSupplierCurrencyTable">Cancel</button>
            <button type="button" class="ft-product-page-btn is-primary" wire:click="saveSupplierCurrencyTable">Save currency table</button>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/catalog/supplier-currency-editor.blade.php ENDPATH**/ ?>