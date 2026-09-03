<div class="ft-cancelled-orders-page">
    <div class="ft-cancelled-page-head">
        <div>
            <div class="ft-cancelled-breadcrumb">Order / <span>Cancelled Orders</span></div>
            <h1>Cancelled Orders</h1>
            <p>Review orders that were cancelled, why they were cancelled, and who cancelled them.</p>
        </div>

        <div class="ft-cancelled-head-actions">
            <a href="<?php echo e(route('jobs.index')); ?>" wire:navigate class="ft-cancelled-btn">
                <span aria-hidden="true">←</span>
                <span>Back to active orders</span>
            </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExport): ?>
                <a href="<?php echo e(route('orders.cancelled.export', $exportQuery)); ?>" class="ft-cancelled-btn">
                    Export cancelled orders
                </a>
            <?php else: ?>
                <button type="button" class="ft-cancelled-btn" disabled>Export cancelled orders</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="ft-cancelled-history-notice" role="note">
        <span class="ft-cancelled-history-icon" aria-hidden="true">i</span>
        <p><strong>Cancelled orders are kept as historical records.</strong> They are excluded from active workflow-stage totals and normal production progress.</p>
    </div>

    <section class="ft-cancelled-metrics" aria-label="Cancelled order summary" wire:loading.class="is-loading">
        <article class="ft-cancelled-metric-card is-red">
            <span class="ft-cancelled-metric-label">TOTAL CANCELLED</span>
            <strong><?php echo e(number_format((int) $metrics['total'])); ?></strong>
            <small>Matching the current view</small>
        </article>

        <article class="ft-cancelled-metric-card is-blue">
            <span class="ft-cancelled-metric-label">CANCELLED THIS MONTH</span>
            <strong><?php echo e(number_format((int) $metrics['this_month'])); ?></strong>
            <small><?php echo e($metrics['month_label']); ?></small>
        </article>

        <article class="ft-cancelled-metric-card is-purple">
            <span class="ft-cancelled-metric-label"><?php echo e(\Illuminate\Support\Str::upper((string) $metrics['common_reason_label'])); ?></span>
            <strong><?php echo e(number_format((int) $metrics['common_reason_count'])); ?></strong>
            <small>Most common reason</small>
        </article>

        <article class="ft-cancelled-metric-card is-green">
            <span class="ft-cancelled-metric-label">RESTORABLE</span>
            <strong><?php echo e(number_format((int) $metrics['restorable'])); ?></strong>
            <small>Can return to active workflow</small>
        </article>
    </section>

    <section class="ft-cancelled-history-card">
        <header class="ft-cancelled-history-head">
            <div>
                <h2>Cancelled order history</h2>
                <p>Search and review cancellation details without mixing them with active orders.</p>
            </div>
            <span class="ft-cancelled-history-only">Historical records only</span>
        </header>

        <div class="ft-cancelled-filters">
            <label class="ft-cancelled-search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6"></circle><path d="m16 16 4 4"></path></svg>
                <input
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Search order, reference, client or product"
                    autocomplete="off"
                    aria-label="Search cancelled orders"
                >
            </label>

            <select wire:model.live="clientId" aria-label="Filter cancelled orders by client">
                <option value="">All clients</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($client->id); ?>"><?php echo e($client->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>

            <select wire:model.live="phaseId" aria-label="Filter cancelled orders by last stage">
                <option value="">All last stages</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phaseOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($phase->id); ?>"><?php echo e($phase->short_name ?: $phase->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>

            <select wire:model.live="reason" aria-label="Filter cancelled orders by reason">
                <option value="">All reasons</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $reasonOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reasonKey => $reasonLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($reasonKey); ?>"><?php echo e($reasonLabel); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>

            <select wire:model.live="cancelledBy" aria-label="Filter cancelled orders by user">
                <option value="">Cancelled by anyone</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cancellerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($person->id); ?>"><?php echo e($person->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>

            <label class="ft-cancelled-date-field" title="Cancelled from">
                <span>From</span>
                <input type="date" wire:model.live="fromDate" aria-label="Cancelled from date">
            </label>

            <label class="ft-cancelled-date-field" title="Cancelled to">
                <span>To</span>
                <input type="date" wire:model.live="toDate" aria-label="Cancelled to date">
            </label>

            <button type="button" class="ft-cancelled-clear" wire:click="clearFilters">Clear</button>
        </div>

        <div class="ft-cancelled-list-meta">
            <span><?php echo e(number_format($orders->total())); ?> cancelled <?php echo e(\Illuminate\Support\Str::plural('order', $orders->total())); ?></span>
            <span>Sorted by latest cancellation</span>
        </div>

        <div class="ft-cancelled-table-wrap" role="region" aria-label="Cancelled orders table" tabindex="0">
            <table class="ft-cancelled-table">
                <colgroup>
                    <col class="ft-co-col-order">
                    <col class="ft-co-col-client">
                    <col class="ft-co-col-stage">
                    <col class="ft-co-col-status">
                    <col class="ft-co-col-reason">
                    <col class="ft-co-col-cancelled">
                    <col class="ft-co-col-owner">
                    <col class="ft-co-col-action">
                </colgroup>
                <thead>
                    <tr>
                        <th>ORDER</th>
                        <th>CLIENT &amp; PRODUCT</th>
                        <th>LAST STAGE</th>
                        <th>STATUS</th>
                        <th>CANCELLATION REASON</th>
                        <th>CANCELLED BY / DATE</th>
                        <th>ORDER OWNER</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cancelled-order-row-'.e($row['id']).''; ?>wire:key="cancelled-order-row-<?php echo e($row['id']); ?>">
                            <td class="ft-cancelled-order-cell">
                                <a href="<?php echo e($row['open_url']); ?>" wire:navigate><?php echo e($row['order_number']); ?></a>
                                <small title="<?php echo e($row['reference']); ?> · <?php echo e($row['created_date']); ?>"><?php echo e($row['reference']); ?> · <?php echo e($row['created_date']); ?></small>
                            </td>

                            <td>
                                <div class="ft-cancelled-client-product">
                                    <span class="ft-cancelled-client-logo" aria-hidden="true"><?php echo e($row['client_initial']); ?></span>
                                    <span class="ft-cancelled-client-product-copy">
                                        <strong title="<?php echo e($row['client_name']); ?>"><?php echo e($row['client_name']); ?></strong>
                                        <small title="<?php echo e($row['product_name']); ?> · <?php echo e(number_format((int) $row['quantity'])); ?> pcs"><?php echo e($row['product_name']); ?> · <?php echo e(number_format((int) $row['quantity'])); ?> pcs</small>
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span class="ft-cancelled-stage-pill stage-<?php echo e(max(1, min(7, (int) $row['stage_sequence']))); ?>" title="<?php echo e($row['stage_name']); ?>">
                                    <i aria-hidden="true"></i>
                                    <?php echo e($row['stage_name']); ?>

                                </span>
                            </td>

                            <td>
                                <span class="ft-cancelled-status-pill">Cancelled</span>
                            </td>

                            <td class="ft-cancelled-reason-cell">
                                <strong><?php echo e($row['reason_label']); ?></strong>
                                <small title="<?php echo e($row['reason_detail'] ?: 'No text reason was recorded. Open the order to review cancellation attachments.'); ?>">
                                    <?php echo e($row['reason_detail'] ?: 'No text reason was recorded. Open the order to review cancellation attachments.'); ?>

                                </small>
                            </td>

                            <td>
                                <div class="ft-cancelled-person">
                                    <span class="ft-cancelled-person-avatar" aria-hidden="true"><?php echo e($row['cancelled_by_initial']); ?></span>
                                    <span class="ft-cancelled-person-copy">
                                        <strong title="<?php echo e($row['cancelled_by_name']); ?>"><?php echo e($row['cancelled_by_name']); ?></strong>
                                        <small><?php echo e($row['cancelled_at_date']); ?> · <?php echo e($row['cancelled_at_time']); ?></small>
                                    </span>
                                </div>
                            </td>

                            <td>
                                <div class="ft-cancelled-person">
                                    <span class="ft-cancelled-person-avatar" aria-hidden="true"><?php echo e($row['owner_initial']); ?></span>
                                    <span class="ft-cancelled-person-copy">
                                        <strong title="<?php echo e($row['owner_name']); ?>"><?php echo e($row['owner_name']); ?></strong>
                                        <small>Order owner</small>
                                    </span>
                                </div>
                            </td>

                            <td class="ft-cancelled-action-cell">
                                <div
                                    class="ft-cancelled-row-menu"
                                    x-data="{
                                        open: false,
                                        top: 0,
                                        left: 0,
                                        toggle() {
                                            if (this.open) {
                                                this.open = false;
                                                return;
                                            }

                                            const trigger = this.$refs.trigger;
                                            if (!trigger) return;

                                            const rect = trigger.getBoundingClientRect();
                                            const menuWidth = 148;
                                            const menuHeight = 44;
                                            const gap = 6;
                                            const edge = 12;

                                            this.left = Math.max(
                                                edge,
                                                Math.min(window.innerWidth - menuWidth - edge, rect.right - menuWidth)
                                            );

                                            this.top = window.innerHeight - rect.bottom >= menuHeight + gap + edge
                                                ? rect.bottom + gap
                                                : Math.max(edge, rect.top - menuHeight - gap);

                                            this.open = true;
                                        }
                                    }"
                                    x-on:keydown.escape.window="open = false"
                                    x-on:resize.window="open = false"
                                    x-on:scroll.window="open = false"
                                >
                                    <button
                                        x-ref="trigger"
                                        type="button"
                                        class="ft-cancelled-row-menu-trigger"
                                        aria-label="Cancelled order actions"
                                        aria-haspopup="menu"
                                        x-bind:aria-expanded="open ? 'true' : 'false'"
                                        x-on:click.stop="toggle()"
                                    >
                                        <span aria-hidden="true">•••</span>
                                    </button>

                                    <template x-teleport="body">
                                        <div
                                            x-cloak
                                            x-show="open"
                                            x-transition.opacity.duration.120ms
                                            class="ft-cancelled-row-menu-popover"
                                            role="menu"
                                            x-bind:style="`top: ${top}px; left: ${left}px;`"
                                            x-on:click.outside="open = false"
                                        >
                                            <a href="<?php echo e($row['open_url']); ?>" wire:navigate role="menuitem" x-on:click="open = false">
                                                Open order
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="8" class="ft-cancelled-empty">No cancelled orders match the selected filters.</td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="ft-cancelled-footer">
            <span>
                Showing <?php echo e($orders->total() ? $orders->firstItem() : 0); ?>–<?php echo e($orders->total() ? $orders->lastItem() : 0); ?> of <?php echo e(number_format($orders->total())); ?> cancelled orders
            </span>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->lastPage() > 1): ?>
                <?php
                    $current = $orders->currentPage();
                    $last = $orders->lastPage();
                    $start = max(1, $current - 1);
                    $end = min($last, $current + 1);
                ?>
                <div class="ft-cancelled-pagination" aria-label="Cancelled order pagination">
                    <button type="button" wire:click="goToCancelledPage(<?php echo e(max(1, $current - 1)); ?>)" <?php if($current <= 1): echo 'disabled'; endif; ?>>←</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($page = $start; $page <= $end; $page++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="<?php echo e($page === $current ? 'active' : ''); ?>" wire:click="goToCancelledPage(<?php echo e($page); ?>)"><?php echo e($page); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" wire:click="goToCancelledPage(<?php echo e(min($last, $current + 1)); ?>)" <?php if($current >= $last): echo 'disabled'; endif; ?>>→</button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/orders/cancelled-orders.blade.php ENDPATH**/ ?>