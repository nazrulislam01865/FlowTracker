<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'paymentInvoiceId' => null,
    'paymentAmount' => '',
    'paymentReceipt' => null,
    'paymentMethods' => collect(),
    'receivedAccounts' => collect(),
]));

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

foreach (array_filter(([
    'job',
    'paymentInvoiceId' => null,
    'paymentAmount' => '',
    'paymentReceipt' => null,
    'paymentMethods' => collect(),
    'receivedAccounts' => collect(),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $selectedPaymentInvoice = $job->invoices->firstWhere('id', (int) $paymentInvoiceId);
    $invoiceBalance = $selectedPaymentInvoice ? (float) $selectedPaymentInvoice->balanceAmount() : 0.0;
    $amountToApply = is_numeric($paymentAmount) ? max(0, (float) $paymentAmount) : 0.0;
    $amountToApply = min($amountToApply, $invoiceBalance);
    $afterPayment = max(0, $invoiceBalance - $amountToApply);
    $paymentCurrency = $selectedPaymentInvoice?->currency ?: 'USD';
    $currencySymbol = match ($paymentCurrency) {
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'CNY', 'RMB' => '¥',
        default => $paymentCurrency.' ',
    };
    $willFullyPay = $selectedPaymentInvoice && $invoiceBalance > 0 && $afterPayment <= 0.009;
?>

<div class="ft-finance-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'record-payment-modal'; ?>wire:key="record-payment-modal" wire:click.self="closeRecordPayment">
    <section class="ft-finance-modal ft-record-payment-modal" role="dialog" aria-modal="true" aria-labelledby="recordPaymentTitle">
        <header class="ft-finance-modal-head ft-record-payment-head">
            <div>
                <h2 id="recordPaymentTitle">Record payment</h2>
                <p>Record money received and apply it to an invoice.</p>
            </div>
            <button type="button" wire:click="closeRecordPayment" aria-label="Close">×</button>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentForm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="ft-finance-form-alert"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-record-payment-body">
            <div class="ft-record-payment-grid">
                <label>
                    <span>Payment amount <b>*</b></span>
                    <div class="ft-payment-money-input">
                        <span><?php echo e($currencySymbol); ?></span>
                        <input type="number" step="0.01" min="0.01" wire:model.live.debounce.250ms="paymentAmount" inputmode="decimal">
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label>
                    <span>Payment date <b>*</b></span>
                    <input type="date" wire:model="paymentDate">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label>
                    <span>Payment method <b>*</b></span>
                    <select wire:model="paymentMethod">
                        <option value="">Select payment method</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($option->name); ?>"><?php echo e($option->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentMethod'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label>
                    <span>Reference number</span>
                    <input type="text" wire:model="paymentReference" placeholder="TRX-240926-1088">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentReference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label>
                    <span>Received in account</span>
                    <select wire:model="paymentReceivedAccount">
                        <option value="">Select received account</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $receivedAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($option->name); ?>"><?php echo e($option->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentReceivedAccount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label>
                    <span>Apply payment to <b>*</b></span>
                    <select wire:model.live="paymentInvoiceId">
                        <option value="">Select invoice</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($invoice->status, ['draft','cancelled','paid'], true) && $invoice->balanceAmount() > 0): ?>
                                <option value="<?php echo e($invoice->id); ?>"><?php echo e($invoice->invoice_number); ?> · <?php echo e($invoice->type); ?></option>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentInvoiceId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
            </div>

            <section class="ft-payment-allocation" aria-label="Payment allocation">
                <h3>Allocation</h3>
                <div class="ft-payment-allocation-panel">
                    <div class="ft-payment-allocation-stat">
                        <span>Invoice balance</span>
                        <strong><?php echo e($currencySymbol); ?><?php echo e(number_format($invoiceBalance, 2)); ?></strong>
                    </div>
                    <div class="ft-payment-allocation-stat">
                        <span>Amount to apply</span>
                        <strong><?php echo e($currencySymbol); ?><?php echo e(number_format($amountToApply, 2)); ?></strong>
                    </div>
                    <div class="ft-payment-allocation-stat">
                        <span>After payment</span>
                        <strong><?php echo e($currencySymbol); ?><?php echo e(number_format($afterPayment, 2)); ?> outstanding</strong>
                    </div>
                    <div class="ft-payment-allocation-result <?php echo e($willFullyPay ? 'is-paid' : ''); ?>">
                        <span class="ft-payment-result-icon"><?php echo e($willFullyPay ? '✓' : '•'); ?></span>
                        <span><?php echo e($willFullyPay ? 'Invoice will be fully paid' : 'Invoice will remain partially paid'); ?></span>
                    </div>
                </div>
            </section>

            <label class="ft-payment-upload">
                <input type="file" wire:model="paymentReceipt" accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>">
                <span class="ft-payment-paperclip" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M21.4 11.6 12 21a6 6 0 0 1-8.5-8.5l10-10a4 4 0 0 1 5.7 5.7L9.7 17.7a2 2 0 1 1-2.8-2.8l8.6-8.6"/></svg>
                </span>
                <span class="ft-payment-upload-copy">Attach receipt or bank advice</span>
                <span class="ft-payment-browse">Browse</span>
            </label>
            <div class="ft-finance-uploading" wire:loading wire:target="paymentReceipt">Uploading receipt…</div>
            <?php if (isset($component)) { $__componentOriginal0493255fe8f58e45a72c968a3d197607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0493255fe8f58e45a72c968a3d197607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.upload-preview','data' => ['file' => $paymentReceipt,'removeAction' => 'clearPaymentReceipt','title' => 'Receipt / bank advice']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.upload-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['file' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentReceipt),'remove-action' => 'clearPaymentReceipt','title' => 'Receipt / bank advice']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0493255fe8f58e45a72c968a3d197607)): ?>
<?php $attributes = $__attributesOriginal0493255fe8f58e45a72c968a3d197607; ?>
<?php unset($__attributesOriginal0493255fe8f58e45a72c968a3d197607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0493255fe8f58e45a72c968a3d197607)): ?>
<?php $component = $__componentOriginal0493255fe8f58e45a72c968a3d197607; ?>
<?php unset($__componentOriginal0493255fe8f58e45a72c968a3d197607); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentReceipt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error ft-payment-upload-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <label class="ft-payment-note">
                <span>Note</span>
                <textarea wire:model="paymentNotes" placeholder="Client paid the remaining final invoice balance."></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentNotes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>

            <label class="ft-payment-checkbox">
                <input type="checkbox" wire:model="paymentMarkInvoicePaid">
                <span>Mark invoice as paid when its balance reaches zero</span>
            </label>
        </div>

        <footer class="ft-finance-modal-foot ft-record-payment-foot">
            <span>Payment ID will be generated automatically.</span>
            <div>
                <button type="button" class="secondary" wire:click="closeRecordPayment">Cancel</button>
                <button type="button" class="primary" wire:click="recordPayment" wire:loading.attr="disabled" wire:target="recordPayment,paymentReceipt">Record payment</button>
            </div>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/finance/payment-modal.blade.php ENDPATH**/ ?>