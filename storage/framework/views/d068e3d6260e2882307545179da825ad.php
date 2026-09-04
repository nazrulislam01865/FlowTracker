<?php
    $submittedQuotes = $rfqInvitations
        ->filter(fn ($row) => $row->quote_status === 'submitted' && $row->quote)
        ->values();

    $winner = $rfqInvitations->first(fn ($row) => (bool) $row->awarded_at);
    $lowestTotal = $submittedQuotes->min(fn ($row) => (float) $row->quote->submitted_total);
    $fastestLeadTime = $submittedQuotes
        ->filter(fn ($row) => $row->quote->lead_time_days !== null)
        ->min(fn ($row) => (int) $row->quote->lead_time_days);

    $symbolFor = static fn (string $currency): string => match (strtoupper($currency)) {
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'CNY', 'RMB' => '¥',
        default => strtoupper($currency).' ',
    };

    $currency = (string) ($submittedQuotes->first()?->quote?->currency ?: $selectedInquiry->currency ?: 'USD');
    $inquiryItems = $selectedInquiry->items->values();
    $totalRequestedQuantity = (float) $inquiryItems->sum(fn ($item) => (float) $item->quantity);
    $normalisedProduct = $inquiryItems->count() === 1
        ? (string) ($inquiryItems->first()?->item_name ?: 'product')
        : number_format($inquiryItems->count()).' products';

    $defaultSelection = $winner ?: $submittedQuotes->first(
        fn ($row) => (float) $row->quote->submitted_total === (float) $lowestTotal
    ) ?: $submittedQuotes->first();
    $defaultSelectedId = $defaultSelection?->id;
    $defaultSelectedName = $defaultSelection?->supplier?->name ?: 'Supplier';

    $quoteSubtotal = static function ($quote): float {
        return (float) $quote->items->sum(
            fn ($item) => (float) $item->quantity * (float) $item->unit_price
        );
    };

    $weightedUnitPrice = static function ($quote) use ($quoteSubtotal): ?float {
        $quantity = (float) $quote->items->sum(fn ($item) => (float) $item->quantity);
        return $quantity > 0 ? $quoteSubtotal($quote) / $quantity : null;
    };

    $supplierMetaValue = static function ($invitation, array $keys): string {
        foreach ($keys as $key) {
            $raw = data_get($invitation->supplier?->metadata, $key);
            if (! is_scalar($raw)) continue;
            $value = trim((string) $raw);
            if ($value !== '') return $value;
        }
        return '—';
    };

    $tableMinWidth = 250 + max(1, $submittedQuotes->count()) * 300;
?>

<div class="ft-rfq-pane ft-rfq-comparison-pane" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-comparison-pane-'.e($selectedInquiry->id).''; ?>wire:key="inquiry-comparison-pane-<?php echo e($selectedInquiry->id); ?>">
    <section
        class="ft-rfq-comparison-card"
        x-data="{
            selectedSupplierId: <?php echo \Illuminate\Support\Js::from($defaultSelectedId)->toHtml() ?>,
            selectedSupplierName: <?php echo \Illuminate\Support\Js::from($defaultSelectedName)->toHtml() ?>,
            shareComparison() {
                const payload = { title: 'Supplier comparison statement', url: window.location.href };
                if (navigator.share) {
                    navigator.share(payload).catch(() => {});
                    return;
                }
                window.prompt('Copy comparison link', window.location.href);
            }
        }"
    >
        <header class="ft-rfq-comparison-head">
            <div class="ft-rfq-comparison-heading">
                <h2>Supplier comparison statement</h2>
                <p>
                    Normalised for <?php echo e(number_format($totalRequestedQuantity, 0)); ?> units of <?php echo e($normalisedProduct); ?>

                    <span aria-hidden="true">·</span> <?php echo e(strtoupper($currency)); ?>

                </p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$submittedQuotes->isEmpty()): ?>
                <div class="ft-rfq-comparison-head-actions" aria-label="Comparison actions">
                    <button type="button" class="ft-rfq-comparison-secondary-btn" x-on:click="window.print()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>
                        <span>Export</span>
                    </button>
                    <button type="button" class="ft-rfq-comparison-secondary-btn" x-on:click="shareComparison()">Share</button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqAward'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="ft-rfq-inline-error"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submittedQuotes->isEmpty()): ?>
            <div class="ft-rfq-comparison-empty">
                <strong>No quotations received yet</strong>
                <span>Submitted supplier quotations will appear here automatically.</span>
                <button type="button" class="ft-outline-btn" wire:click="setDetailTab('rfq')">Back to RFQ</button>
            </div>
        <?php else: ?>
            <div class="ft-rfq-comparison-scroll" role="region" aria-label="Supplier comparison" tabindex="0">
                <table class="ft-rfq-comparison-matrix" style="min-width: <?php echo e($tableMinWidth); ?>px">
                    <colgroup>
                        <col class="ft-rfq-comparison-criteria-col">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <col class="ft-rfq-comparison-supplier-col">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" class="ft-rfq-comparison-criteria-head">Comparison criteria</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <th
                                    scope="col"
                                    class="ft-rfq-comparison-supplier-head"
                                    :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'comparison-head-'.e($invitation->id).''; ?>wire:key="comparison-head-<?php echo e($invitation->id); ?>"
                                >
                                    <strong><?php echo e(\Illuminate\Support\Str::upper($invitation->supplier?->name ?: 'Supplier')); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invitation->quote->submitted_total === (float) $lowestTotal): ?>
                                        <span class="ft-rfq-comparison-badge is-best">Best value</span>
                                    <?php elseif(
                                        $invitation->quote->lead_time_days !== null
                                        && $fastestLeadTime !== null
                                        && (int) $invitation->quote->lead_time_days === (int) $fastestLeadTime
                                    ): ?>
                                        <span class="ft-rfq-comparison-badge is-fastest">Fastest</span>
                                    <?php else: ?>
                                        <span class="ft-rfq-comparison-badge is-participant">RFQ participant</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Select supplier</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }">
                                    <label class="ft-rfq-comparison-radio">
                                        <input
                                            type="radio"
                                            name="rfq-comparison-supplier-<?php echo e($selectedInquiry->id); ?>"
                                            value="<?php echo e($invitation->id); ?>"
                                            x-model.number="selectedSupplierId"
                                            x-on:change="selectedSupplierName = <?php echo \Illuminate\Support\Js::from($invitation->supplier?->name ?: 'Supplier')->toHtml() ?>"
                                            <?php if($winner || !$canManageInquiryRfq): echo 'disabled'; endif; ?>
                                        >
                                        <span><?php echo e($invitation->awarded_at ? 'Selected' : 'Select'); ?></span>
                                    </label>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">Unit price</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }">
                                    <strong class="ft-rfq-comparison-price">
                                        <?php echo e($weightedUnitPrice($invitation->quote) !== null
                                            ? $symbolFor((string) $invitation->quote->currency).number_format($weightedUnitPrice($invitation->quote), 2)
                                            : '—'); ?>

                                    </strong>
                                    <small>
                                        <?php echo e($symbolFor((string) $invitation->quote->currency)); ?><?php echo e(number_format($quoteSubtotal($invitation->quote), 2)); ?> subtotal
                                    </small>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">Freight</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><?php echo e($symbolFor((string) $invitation->quote->currency)); ?><?php echo e(number_format((float) $invitation->quote->freight, 2)); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">Landed total</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><strong class="ft-rfq-comparison-total"><?php echo e($symbolFor((string) $invitation->quote->currency)); ?><?php echo e(number_format((float) $invitation->quote->submitted_total, 2)); ?></strong></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">Lead time</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><?php echo e($invitation->quote->lead_time_days !== null ? number_format($invitation->quote->lead_time_days).' days' : '—'); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">MOQ</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><?php echo e($supplierMetaValue($invitation, ['moq', 'minimum_order_quantity', 'minimum_order_qty'])); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">Payment terms</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><?php echo e($supplierMetaValue($invitation, ['payment_terms', 'supplier_payment_terms'])); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr>
                            <th scope="row">Sample</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><?php echo e($supplierMetaValue($invitation, ['sample_terms', 'sample', 'sample_cost'])); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr class="ft-rfq-comparison-attachments-row">
                            <th scope="row">Attachments</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><span class="ft-rfq-comparison-empty-value">—</span></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>

                        <tr class="ft-rfq-comparison-note-row">
                            <th scope="row">Supplier note</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submittedQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td :class="{ 'is-selected': selectedSupplierId === <?php echo e($invitation->id); ?> }"><?php echo e($invitation->quote->notes ?: '—'); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer class="ft-rfq-comparison-award-bar">
                <div class="ft-rfq-comparison-selection-copy">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner): ?>
                        <strong><?php echo e($winner->supplier?->name ?: 'Supplier'); ?> selected</strong>
                        <span>This supplier has already been awarded for the inquiry.</span>
                    <?php else: ?>
                        <strong><span x-text="selectedSupplierName"></span> selected</strong>
                        <span>Review the commercial terms and submitted lead time before awarding.</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner): ?>
                    <button type="button" class="ft-rfq-comparison-award-btn" disabled>Awarded</button>
                <?php elseif($canManageInquiryRfq): ?>
                    <button
                        type="button"
                        class="ft-rfq-comparison-award-btn"
                        x-bind:disabled="!selectedSupplierId"
                        x-on:click="if (selectedSupplierId && window.confirm('Award selected supplier? The selected supplier will be linked to the Inquiry products and the other invited suppliers will be notified.')) { $wire.awardRfqSupplier(selectedSupplierId) }"
                        wire:loading.attr="disabled"
                        wire:target="awardRfqSupplier"
                    >
                        <span wire:loading.remove wire:target="awardRfqSupplier">Award selected supplier</span>
                        <span wire:loading wire:target="awardRfqSupplier">Awarding...</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </footer>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/sections/comparison.blade.php ENDPATH**/ ?>