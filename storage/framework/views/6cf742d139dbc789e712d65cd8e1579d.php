        <div class="ft-master-breadcrumb" aria-label="Breadcrumb">
            <span><?php echo e($masterSectionLabel); ?></span><i>/</i><strong><?php echo e($pageTitle); ?></strong>
        </div>

        <div class="ft-master-page-head">
            <div>
                <h1><?php echo e($pageTitle); ?></h1>
                <p><?php echo e($pageSubtitle); ?></p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateMaster): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'supplier'): ?>
                    <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'create' => 1])); ?>" wire:navigate class="primary ft-master-add-button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Create supplier</span>
                    </a>
                <?php else: ?>
                    <button type="button" class="primary ft-master-add-button" wire:click="open">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Add <?php echo e($singularLabel); ?></span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash success ft-master-flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['record'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="flash error ft-master-flash"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-master-single-stat ft-master-generic-stat">
            <div class="ft-master-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v14H5zM8 9h8M8 13h8M8 17h5"/></svg>
            </div>
            <div class="ft-master-stat-copy">
                <span>Total <?php echo e(strtolower($pageTitle)); ?></span>
                <strong><?php echo e(number_format($selectedTotal)); ?></strong>
            </div>
            <small><?php echo e(number_format($selectedActive)); ?> active</small>
        </div>

        <section class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-master-generic-card', 'ft-master-supplier-card' => $group === 'supplier']); ?>">
            <div class="ft-master-generic-toolbar">
                <label class="ft-master-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search <?php echo e(strtolower($pageTitle)); ?>..." aria-label="Search <?php echo e(strtolower($pageTitle)); ?>">
                </label>
            </div>

            <div class="ft-master-product-count">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recordsReady && $rows): ?>
                    Showing <?php echo e($rows->firstItem() ?? 0); ?>–<?php echo e($rows->lastItem() ?? 0); ?> of <?php echo e(number_format($rows->total())); ?> records
                <?php else: ?>
                    Loading records…
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$recordsReady): ?>
                <?php echo $__env->make('livewire.shared.table-rows-placeholder', ['columns' => $columnCount, 'rows' => 8], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <div class="table-wrap ft-master-generic-table-wrap" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'master-records-'.e($group).''; ?>wire:key="master-records-<?php echo e($group); ?>">
                    <table class="<?php echo \Illuminate\Support\Arr::toCssClasses(['master-table', 'ft-master-generic-table', 'ft-master-supplier-table' => $group === 'supplier']); ?>">
                        <thead>
                            <tr>
                                <th>Sort order</th>
                                <th>Code</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group !== 'remote_area'): ?><th><?php echo e($group === 'phone_country_code' ? 'Phone code' : 'Name'); ?></th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'remote_area'): ?><th>Carrier</th><th>Country</th><th>Postal range / City</th><th>Origin surcharge</th><th>Destination surcharge</th><th>Extra charge</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'task_pack_work_calendar'): ?><th>Days</th><th>Working hours</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'inquiry_task_status'): ?><th>Inquiry status auto</th><th>Flag</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_status'): ?><th>Automatic task flag</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_flag'): ?><th>Order flag</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasParent): ?><th><?php echo e($group === 'state' ? 'Country' : 'Product Category'); ?></th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group !== 'remote_area'): ?><th>Description / Use</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasColor): ?><th>Color</th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td class="ft-master-mobile-sort" data-label="Sort order"><?php echo e($r->sort_order); ?></td>
                                <td class="ft-master-mobile-code" data-label="Code"><strong class="ft-master-product-code"><?php echo e($r->code); ?></strong></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group !== 'remote_area'): ?><td class="ft-master-mobile-name" data-label="<?php echo e($group === 'phone_country_code' ? 'Phone code' : 'Name'); ?>"><?php echo e($r->name); ?></td><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'remote_area'): ?>
                                    <?php $remoteAreaExtraCharge = $r->remoteAreaExtraCharge(); ?>
                                    <td data-label="Carrier"><strong><?php echo e($r->remoteAreaCarrier() ?: '—'); ?></strong></td>
                                    <td data-label="Country">
                                        <strong><?php echo e($r->remoteAreaCountry() ?: '—'); ?></strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->remoteAreaIataCode() !== ''): ?><div class="small muted"><?php echo e($r->remoteAreaIataCode()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td data-label="Postal range / City"><strong><?php echo e($r->remoteAreaLocationLabel()); ?></strong></td>
                                    <td data-label="Origin surcharge"><?php echo e($r->remoteAreaOriginSurcharge() ?: 'No'); ?></td>
                                    <td data-label="Destination surcharge"><?php echo e($r->remoteAreaDestinationSurcharge() ?: 'No'); ?></td>
                                    <td data-label="Extra charge"><?php echo e($remoteAreaExtraCharge === null ? '—' : number_format($remoteAreaExtraCharge, 2)); ?></td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'task_pack_work_calendar'): ?>
                                    <td data-label="Days"><strong class="ft-work-calendar-table-value"><?php echo e($r->taskPackWorkCalendarDayRange()); ?></strong></td>
                                    <td data-label="Working hours"><strong class="ft-work-calendar-table-value"><?php echo e($r->taskPackWorkCalendarTimeRange()); ?></strong></td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'inquiry_task_status'): ?>
                                    <td class="ft-master-mobile-auto-status" data-label="Inquiry status auto"><strong><?php echo e($r->inquiryAutoStatus()); ?></strong></td>
                                    <td class="ft-master-mobile-flag" data-label="Flag">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->requiresAttention()): ?>
                                            <span class="ft-inquiry-status-rule-flag is-attention">Requires attention</span>
                                        <?php else: ?>
                                            <span class="ft-inquiry-status-rule-flag">Not needed</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_status'): ?>
                                    <?php $mappedTaskFlag = $orderTaskFlagOptions->firstWhere('id', $r->orderTaskFlagId()); ?>
                                    <td class="ft-master-mobile-flag" data-label="Automatic task flag">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mappedTaskFlag): ?>
                                            <span class="ft-inquiry-status-rule-flag is-attention" style="<?php echo e(\App\Support\MasterColor::style($mappedTaskFlag->color)); ?>"><?php echo e($mappedTaskFlag->name); ?></span>
                                        <?php else: ?>
                                            <span class="ft-inquiry-status-rule-flag">No flag</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group === 'order_task_flag'): ?>
                                    <?php $mappedOrderFlag = $orderFlagOptions->firstWhere('id', $r->orderFlagId()); ?>
                                    <td class="ft-master-mobile-flag" data-label="Order flag">
                                        <strong><?php echo e($mappedOrderFlag?->name ?? 'Not mapped'); ?></strong>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasParent): ?><td class="ft-master-mobile-parent" data-label="<?php echo e($group === 'state' ? 'Country' : 'Product Category'); ?>"><?php echo e($r->parent?->name ?? '—'); ?></td><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group !== 'remote_area'): ?><td class="ft-master-mobile-description" data-label="Description / Use"><?php echo e($r->description ?: '—'); ?></td><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasColor): ?>
                                    <?php
                                        $rowColor = \App\Support\MasterColor::normalize($r->color) ?: \App\Support\MasterColor::defaultFor($group, $r->name);
                                    ?>
                                    <td class="ft-master-mobile-color" data-label="Color">
                                        <label class="ft-master-color-chip" style="<?php echo e(\App\Support\MasterColor::style($rowColor)); ?>" title="Choose color for <?php echo e($r->name); ?>">
                                            <input
                                                class="ft-master-inline-color"
                                                type="color"
                                                value="<?php echo e($rowColor); ?>"
                                                wire:change="updateColor(<?php echo e($r->id); ?>, $event.target.value)"
                                                wire:loading.attr="disabled"
                                                <?php if(!$canEditMaster): echo 'disabled'; endif; ?>
                                                aria-label="Choose color for <?php echo e($r->name); ?>"
                                            >
                                            <span><?php echo e($rowColor); ?></span>
                                        </label>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <td class="ft-master-mobile-status" data-label="Status"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['label' => $r->status === 'active' ? 'Active' : 'Inactive']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status === 'active' ? 'Active' : 'Inactive')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                                <td class="ft-master-mobile-actions" data-label="Actions">
                                    <div class="row-actions">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditMaster): ?>
                                            <button class="mini-btn" wire:click="open(<?php echo e($r->id); ?>)">Edit</button>
                                            <button class="mini-btn" wire:click="toggle(<?php echo e($r->id); ?>)"><?php echo e($r->status === 'active' ? 'Deactivate' : 'Activate'); ?></button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteMaster): ?>
                                            <button class="mini-btn" wire:click="deleteRecord(<?php echo e($r->id); ?>)" wire:confirm="Delete this master record?">Delete</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canEditMaster && !$canDeleteMaster): ?>
                                            <span class="small muted">View only</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-master-empty-row"><td colspan="<?php echo e($columnCount); ?>"><div class="empty-state">No records found.</div></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rows->total() > 30): ?>
                    <div class="ft-list-pagination ft-master-pagination">
                        <span>Showing <b><?php echo e($rows->firstItem() ?? 0); ?>–<?php echo e($rows->lastItem() ?? 0); ?></b> of <?php echo e($rows->total()); ?> records</span>
                        <div class="ft-page-actions">
                            <button type="button" wire:click="previousPage('masterPage')" <?php if($rows->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                            <span>Page <?php echo e($rows->currentPage()); ?> of <?php echo e($rows->lastPage()); ?></span>
                            <button type="button" wire:click="nextPage('masterPage')" <?php if(!$rows->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/master-data/sections/generic-list.blade.php ENDPATH**/ ?>