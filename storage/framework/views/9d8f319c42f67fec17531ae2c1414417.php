<?php
    $pricingTable = $currencyTable ?? $extraTable ?? [];
    $currencyCode = (string) ($pricingTable['currency'] ?? '');
    $currencyRows = collect($pricingTable['rows'] ?? [])->values();
    $currencyChargeMap = collect($pricingTable['charges'] ?? [])->keyBy('quantity');
    $primaryTable = (bool) ($isPrimary ?? false);
?>
<details class="ft-price-currency" <?php if($expanded ?? false): ?> open <?php endif; ?>>
    <summary class="ft-price-currency-heading">
        <span class="ft-price-currency-identity">
            <strong><?php echo e($currencyCode); ?></strong>
            <span class="ft-price-badge <?php echo e($primaryTable ? 'is-primary' : 'is-muted'); ?>"><?php echo e($primaryTable ? 'Primary table' : 'Additional currency'); ?></span>
        </span>
        <span class="ft-price-currency-meta">
            <?php echo e($currencyRows->count() ? $currencyRows->count().' quantity '.($currencyRows->count() === 1 ? 'tier' : 'tiers') : 'No prices set'); ?>

            <span class="ft-price-chevron" aria-hidden="true"></span>
        </span>
    </summary>
    <div class="ft-price-currency-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currencyRows->isNotEmpty()): ?>
            <div class="ft-product-price-preview-wrap ft-product-detail-price-wrap ft-price-matrix-scroll"
                 role="region" aria-label="<?php echo e($supplierName ?? 'Supplier'); ?> <?php echo e($currencyCode); ?> prices" tabindex="0">
                <table class="ft-product-price-preview ft-product-detail-matrix">
                    <thead><tr><th scope="col">Quantity</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currencyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <th scope="col"><?php echo e(number_format((int) $tier['quantity'])); ?></th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['price' => 'Supplier price', 'remote_surcharge' => 'Remote surcharge', 'standard_shipment' => 'Standard shipment', 'urgent_shipment' => 'Urgent shipment']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $heading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr><th scope="row"><?php echo e($heading); ?></th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currencyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $value = $field === 'price' ? ($tier['price'] ?? null)
                                            : data_get($currencyChargeMap->get((int) $tier['quantity']), $field);
                                    ?>
                                    <td><?php echo e($value === null || $value === '' ? '—' : ((float) $value === 0.0 ? '0' : rtrim(rtrim(number_format((float) $value, 6, '.', ''), '0'), '.'))); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="ft-price-empty-state">No quantity-based prices configured for this currency.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($allowEdit ?? false) && !$primaryTable): ?>
            <div class="ft-price-currency-actions">
                <button type="button" class="ft-product-page-btn is-secondary"
                    wire:click="openSupplierCurrencyTable(<?php echo e((int) $pricingTable['supplier_id']); ?>, '<?php echo e($currencyCode); ?>')">Edit <?php echo e($currencyCode); ?> table</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</details>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/catalog/supplier-currency-matrix.blade.php ENDPATH**/ ?>