<div class="ft-order-summary-report">
    <div class="ft-osr-breadcrumb">Reports / <b>Order Summary</b></div>

    <div class="ft-osr-title-row">
        <div>
            <h1>Order Summary Report</h1>
            <div class="ft-osr-subtitle">Supplier, material, sample and delivery tracking in one operational report.</div>
        </div>

        <div class="ft-osr-actions">
            <button type="button" wire:click="resetFilters">Reset</button>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExport): ?>
                <a
                    class="ft-osr-btn ft-osr-btn-primary"
                    href="<?php echo e(route('order-summary.export', $exportQuery)); ?>"
                >⇩ Download Excel</a>
            <?php else: ?>
                <button type="button" class="ft-osr-btn ft-osr-btn-primary" disabled>⇩ Download Excel</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <section class="ft-osr-panel">
        <div class="ft-osr-filters">
            <div class="ft-osr-field">
                <label>SEARCH</label>
                <input
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Order no., supplier, material..."
                    autocomplete="off"
                >
            </div>

            <div class="ft-osr-field">
                <label>SUPPLIER</label>
                <select wire:model.live="supplierId">
                    <option value="">All suppliers</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $supplierOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div class="ft-osr-field">
                <label>WAREHOUSE</label>
                <select wire:model.live="warehouse">
                    <option value="">All warehouses</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $warehouseOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouseOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($warehouseOption); ?>"><?php echo e($warehouseOption); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div class="ft-osr-field">
                <label>URGENCY</label>
                <select wire:model.live="urgency">
                    <option value="">All</option>
                    <option value="Y">Urgent</option>
                    <option value="N">Normal</option>
                </select>
            </div>

            <div class="ft-osr-field">
                <label>RECEIVED FROM</label>
                <input type="date" wire:model.live="fromDate">
            </div>

            <div class="ft-osr-field">
                <label>RECEIVED TO</label>
                <input type="date" wire:model.live="toDate">
            </div>

            <div class="ft-osr-filter-actions">
                <button type="button" wire:click="applyFilters">Apply</button>
            </div>
        </div>

        <?php if (isset($component)) { $__componentOriginale7a84969afadf9ba9e613df893afaa64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7a84969afadf9ba9e613df893afaa64 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.reports.client-checkbox-filter','data' => ['clients' => $clientOptions,'selectedIds' => $clientIds]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('reports.client-checkbox-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientOptions),'selected-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientIds)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7a84969afadf9ba9e613df893afaa64)): ?>
<?php $attributes = $__attributesOriginale7a84969afadf9ba9e613df893afaa64; ?>
<?php unset($__attributesOriginale7a84969afadf9ba9e613df893afaa64); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7a84969afadf9ba9e613df893afaa64)): ?>
<?php $component = $__componentOriginale7a84969afadf9ba9e613df893afaa64; ?>
<?php unset($__componentOriginale7a84969afadf9ba9e613df893afaa64); ?>
<?php endif; ?>

        <div class="ft-osr-quickbar">
            <div class="ft-osr-chips">
                <button type="button" class="ft-osr-chip <?php echo e($quick === 'all' ? 'active' : ''); ?>" wire:click="setQuick('all')">
                    All <b><?php echo e(number_format($counts['all'])); ?></b>
                </button>
                <button type="button" class="ft-osr-chip <?php echo e($quick === 'urgent' ? 'active' : ''); ?>" wire:click="setQuick('urgent')">
                    Urgent <b><?php echo e(number_format($counts['urgent'])); ?></b>
                </button>
                <button type="button" class="ft-osr-chip <?php echo e($quick === 'awaiting' ? 'active' : ''); ?>" wire:click="setQuick('awaiting')">
                    Awaiting supplier reply <b><?php echo e(number_format($counts['awaiting'])); ?></b>
                </button>
                <button type="button" class="ft-osr-chip <?php echo e($quick === 'overdue' ? 'active' : ''); ?>" wire:click="setQuick('overdue')">
                    Overdue <b><?php echo e(number_format($counts['overdue'])); ?></b>
                </button>
            </div>

            <div class="ft-osr-legend">
                <span class="ft-osr-legend-item"><span class="ft-osr-dot red"></span>Overdue</span>
                <span class="ft-osr-legend-item"><span class="ft-osr-dot orange"></span>Urgent</span>
                <span class="ft-osr-legend-item"><span class="ft-osr-dot green"></span>Completed/on track</span>
                <span class="ft-osr-legend-item"><span class="ft-osr-dot yellow"></span>Supplier reply</span>
            </div>
        </div>
    </section>

    <section class="ft-osr-table-card">
        <div class="ft-osr-table-head">
            <div class="ft-osr-table-title">Order summary</div>
            <div class="ft-osr-table-meta"><?php echo e(number_format($orders->total())); ?> records · Horizontal scroll available</div>
        </div>

        <div class="ft-osr-table-wrap">
            <table class="ft-osr-table">
                <colgroup>
                    <col class="ft-osr-col-supplier">
                    <col class="ft-osr-col-warehouse">
                    <col class="ft-osr-col-order">
                    <col class="ft-osr-col-received">
                    <col class="ft-osr-col-urgency">
                    <col class="ft-osr-col-quantity">
                    <col class="ft-osr-col-material">
                    <col class="ft-osr-col-erp">
                    <col class="ft-osr-col-special">
                    <col class="ft-osr-col-sample-sent">
                    <col class="ft-osr-col-sample-confirmed">
                    <col class="ft-osr-col-revise">
                    <col class="ft-osr-col-delivery">
                    <col class="ft-osr-col-reply">
                </colgroup>
                <thead>
                <tr>
                    <th class="sticky1">Supplier</th>
                    <th class="sticky2">Warehouse</th>
                    <th>Order No.</th>
                    <th>Received Date</th>
                    <th>Urgent or Not</th>
                    <th>Quantity</th>
                    <th>Material</th>
                    <th>ERP Approval Date</th>
                    <th>Special Orders</th>
                    <th>Sample/Swatch Sent Date</th>
                    <th>Sample/Swatch Confirmed Date</th>
                    <th>Revise / Sample Confirm Date</th>
                    <th>Supplier Delivery Date<br><span class="muted">供应商到货日期</span></th>
                    <th>Supplier Reply<br><span class="muted">供应商回复交期</span></th>
                </tr>
                </thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-summary-row-'.e($row['id']).''; ?>wire:key="order-summary-row-<?php echo e($row['id']); ?>" class="row-<?php echo e($row['state']); ?>">
                        <td class="sticky1 supplier-cell"><?php echo e($row['supplier']); ?></td>
                        <td class="sticky2"><?php echo e($row['warehouse']); ?></td>
                        <td class="order-no ft-osr-nowrap"><?php echo e($row['order']); ?></td>
                        <td class="ft-osr-nowrap"><?php echo e($row['received'] ?: '—'); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['urgent'] === 'Y'): ?>
                                <span class="ft-osr-badge ft-osr-badge-danger">Urgent</span>
                            <?php else: ?>
                                <span class="ft-osr-badge ft-osr-badge-neutral">Normal</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="center ft-osr-nowrap"><?php echo e(number_format((int) $row['quantity'])); ?></td>
                        <td class="ft-osr-wrap"><?php echo e($row['material']); ?></td>
                        <td class="ft-osr-nowrap"><?php echo e($row['erp_approval'] ?: '—'); ?></td>
                        <td class="special"><?php echo e($row['special_orders'] ?: '—'); ?></td>
                        <td class="ft-osr-nowrap"><?php echo e($row['sample_sent'] ?: '—'); ?></td>
                        <td class="ft-osr-nowrap"><?php echo e($row['sample_confirmed'] ?: '—'); ?></td>
                        <td class="ft-osr-nowrap"><?php echo e($row['revise_confirm'] ?: '—'); ?></td>
                        <td class="ft-osr-nowrap"><?php echo e($row['supplier_delivery'] ?: '—'); ?></td>
                        <td class="<?php echo e($row['supplier_reply'] !== '' ? 'reply-cell' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['supplier_reply'] !== ''): ?>
                                <?php echo e($row['supplier_reply']); ?>

                            <?php else: ?>
                                <span class="ft-osr-badge ft-osr-badge-warning">Awaiting reply</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="14" class="ft-osr-empty">No Orders match the selected report filters.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="ft-osr-footer">
            <div>
                Showing <b><?php echo e($orders->total() ? $orders->firstItem() : 0); ?>–<?php echo e($orders->total() ? $orders->lastItem() : 0); ?></b>
                of <b><?php echo e(number_format($orders->total())); ?></b> records
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->lastPage() > 1): ?>
                <?php
                    $current = $orders->currentPage();
                    $last = $orders->lastPage();
                    $start = max(1, $current - 1);
                    $end = min($last, $current + 1);
                ?>
                <div class="ft-osr-pages">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($current > 1): ?>
                        <button type="button" class="ft-osr-page" wire:click="goToReportPage(<?php echo e($current - 1); ?>)">‹</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($page = $start; $page <= $end; $page++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="ft-osr-page <?php echo e($page === $current ? 'active' : ''); ?>" wire:click="goToReportPage(<?php echo e($page); ?>)"><?php echo e($page); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($current < $last): ?>
                        <button type="button" class="ft-osr-page" wire:click="goToReportPage(<?php echo e($current + 1); ?>)">›</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/reports/order-summary.blade.php ENDPATH**/ ?>