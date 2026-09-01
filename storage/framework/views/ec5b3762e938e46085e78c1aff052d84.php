<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'context' => []]));

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

foreach (array_filter((['job', 'context' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $record = $context['displayRecord'] ?? null;
    $records = collect($context['records'] ?? []);
    $isDiscountScope = $record?->scope === 'discount';
    $scopeLabel = match ($record?->scope) {
        'production' => 'Production only',
        'discount' => 'Discount instead of redo',
        default => 'Artwork + production',
    };
    $restartLabel = $isDiscountScope
        ? 'No workflow restart'
        : ($record?->redoOrder?->phase?->name
            ?: ($record?->scope === 'production' ? 'Production phase' : 'Artwork phase'));
    $resolution = $record?->customer_resolution === 'discount'
        ? rtrim(rtrim(number_format((float) $record->customer_discount_percent, 2), '0'), '.').'% customer discount instead of redo'
        : 'Free redo for customer';
    $currency = (string) ($record?->originalOrder?->currency ?: $job->currency ?: 'USD');
    $money = fn ($value) => ($currency === 'USD' ? '$' : $currency.' ').number_format((float) $value, 2);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record): ?>
    <section class="ft-redo-panel show">
        <div class="ft-redo-grid">
            <article class="ft-redo-card">
                <header class="ft-redo-cardhead">
                    <h2><?php echo e($isDiscountScope ? 'Discount adjustment relationship' : 'Redo order relationship'); ?></h2>
                    <span class="pill redo"><?php echo e($isDiscountScope ? 'Discount adjustment' : '↻ Redo order'); ?></span>
                </header>
                <div class="ft-redo-cardbody">
                    <div class="ft-redo-relation">
                        <button
                            type="button"
                            class="ft-redo-order-chip"
                            wire:click="openLinkedRedoOrder(<?php echo e((int) $record->original_order_id); ?>)"
                            title="Open original Order"
                        >
                            <small>Original order</small>
                            <b><?php echo e($record->originalOrder?->displayOrderNumber() ?? '—'); ?></b>
                            <span><?php echo e(number_format((int) ($record->originalOrder?->quantity ?? 0))); ?> pcs · source Order remains intact</span>
                        </button>

                        <div class="ft-redo-relation-arrow">→</div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope): ?>
                            <div class="ft-redo-order-chip current ft-redo-order-chip-static">
                                <small>Resolution</small>
                                <b>Customer discount</b>
                                <span><?php echo e(rtrim(rtrim(number_format((float) $record->customer_discount_percent, 2), '0'), '.')); ?>% discount · <?php echo e(number_format((int) $record->affected_quantity)); ?> affected units</span>
                            </div>
                        <?php else: ?>
                            <button
                                type="button"
                                class="ft-redo-order-chip current"
                                wire:click="openLinkedRedoOrder(<?php echo e((int) $record->redo_order_id); ?>)"
                                title="Open redo Order"
                            >
                                <small>Redo order</small>
                                <b><?php echo e($record->redoOrder?->displayOrderNumber() ?? '—'); ?></b>
                                <span><?php echo e($scopeLabel); ?> · <?php echo e(number_format((int) $record->redo_quantity)); ?> units</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ft-redo-info-row"><span>Issue source</span><b><?php echo e($record->issue_reported_by); ?></b></div>
                    <div class="ft-redo-info-row"><span>Issue category</span><b><?php echo e($record->issue_category); ?></b></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($record->issue_description)): ?>
                        <div class="ft-redo-info-row">
                            <span>Issue reason</span>
                            <div class="ft-rich-text-content"><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $record->issue_description]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($record->issue_description)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $attributes = $__attributesOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $component = $__componentOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__componentOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?></div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="ft-redo-info-row"><span>Resolution</span><b><?php echo e($resolution); ?></b></div>
                    <div class="ft-redo-info-row"><span>Workflow restart</span><b><?php echo e($restartLabel); ?></b></div>
                    <div class="ft-redo-info-row"><span>Supplier</span><b><?php echo e($record->supplier?->name ?: 'Supplier not decided'); ?></b></div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($records->count() > 1): ?>
                        <div class="ft-redo-history-note">
                            This original Order has <?php echo e($records->count()); ?> redo/discount records. The newest record is shown here.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </article>

            <div class="ft-redo-stack">
                <article class="ft-redo-card">
                    <header class="ft-redo-cardhead"><h2><?php echo e($isDiscountScope ? 'Discount financial impact' : 'Redo financial impact'); ?></h2></header>
                    <div class="ft-redo-cardbody">
                        <table class="ft-redo-fin-table">
                            <tr><td>Affected value</td><td><?php echo e($money($record->affected_order_value)); ?></td></tr>
                            <tr>
                                <td>Customer charge / credit</td>
                                <td><?php echo e($record->customer_resolution === 'discount' ? '-'.$money($record->customer_impact) : $money(0)); ?></td>
                            </tr>
                            <tr><td><?php echo e($isDiscountScope ? 'Supplier recovery' : 'Supplier redo charge'); ?></td><td><?php echo e($money($record->supplier_redo_charge)); ?></td></tr>
                            <tr><td>Freight deduction</td><td><?php echo e($money($record->freight_amount)); ?></td></tr>
                            <tr class="total"><td>Total supplier recovery</td><td><?php echo e($money($record->total_supplier_recovery)); ?></td></tr>
                        </table>
                        <p class="ft-redo-footnote">
                            <?php echo e($isDiscountScope
                                ? 'The customer credit is recorded against the original Order. No replacement Order or workflow restart was created; the original invoice remains unchanged.'
                                : 'All amounts are recorded as financial adjustments against the redo Order; the original invoice remains unchanged.'); ?>

                        </p>
                    </div>
                </article>

                <article class="ft-redo-card">
                    <header class="ft-redo-cardhead"><h2>Redo audit trail</h2></header>
                    <div class="ft-redo-cardbody ft-redo-audit">
                        <div class="ft-redo-event">
                            <i></i>
                            <div>
                                <b>Issue reported</b>
                                <small><?php echo e(number_format((int) $record->affected_quantity)); ?> units · <?php echo e($record->issue_reported_by); ?> · <?php echo e($record->reported_date?->format('M j, Y')); ?></small>
                            </div>
                        </div>
                        <div class="ft-redo-event">
                            <i></i>
                            <div>
                                <b><?php echo e($isDiscountScope ? 'Discount approved' : 'Redo approved'); ?></b>
                                <small><?php echo e($scopeLabel); ?> · <?php echo e($record->creator?->name ?: 'FlowTrack user'); ?></small>
                            </div>
                        </div>
                        <div class="ft-redo-event">
                            <i></i>
                            <div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope): ?>
                                    <b>Discount adjustment recorded</b>
                                    <small>No redo Order created · original workflow remained unchanged</small>
                                <?php else: ?>
                                    <b>Redo order created</b>
                                    <small><?php echo e($record->redoOrder?->displayOrderNumber() ?? 'Linked Order'); ?> · linked to original order automatically</small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/redo-panel.blade.php ENDPATH**/ ?>