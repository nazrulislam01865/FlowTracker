<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job','contacts'=>collect(),'summary'=>[],'invoiceTypes'=>collect(),'currencies'=>collect(),'paymentTerms'=>collect(),'invoiceLineItems'=>[],'invoiceTaxRate'=>'0','invoiceCurrency'=>'USD','invoiceType'=>'Final invoice','invoiceRemoteAreaCharge'=>0,'invoiceRemoteAreaName'=>'','invoiceRemoteAreaPostalCode'=>'','invoiceSupportingDocument'=>null]));

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

foreach (array_filter((['job','contacts'=>collect(),'summary'=>[],'invoiceTypes'=>collect(),'currencies'=>collect(),'paymentTerms'=>collect(),'invoiceLineItems'=>[],'invoiceTaxRate'=>'0','invoiceCurrency'=>'USD','invoiceType'=>'Final invoice','invoiceRemoteAreaCharge'=>0,'invoiceRemoteAreaName'=>'','invoiceRemoteAreaPostalCode'=>'','invoiceSupportingDocument'=>null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $itemsSubtotal = collect($invoiceLineItems)->sum(fn($item) => max(0, (float)($item['quantity'] ?? 0)) * max(0, (float)($item['unit_price'] ?? 0)));
    $remoteAreaCharge = max(0, (float) $invoiceRemoteAreaCharge);
    $subtotal = $itemsSubtotal + $remoteAreaCharge;
    $taxRate = max(0, min(100, (float)$invoiceTaxRate));
    $taxAmount = $subtotal * ($taxRate / 100);
    // The current invoice total is based only on its own line items, the
    // authoritative Remote Area charge (when applicable), and tax. Payments are
    // handled separately through Record Payment.
    $total = $subtotal + $taxAmount;
    $currencyValue = strtoupper(trim((string) $invoiceCurrency));
    $currencyPrefix = match ($currencyValue) {
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'CNY', 'RMB' => '¥',
        'BDT' => '৳',
        'JPY' => '¥',
        'KRW' => '₩',
        'INR' => '₹',
        'CAD' => 'C$',
        'AUD' => 'A$',
        default => $currencyValue !== '' ? $currencyValue.' ' : '',
    };

    $currencyMaster = app(\App\Services\MasterDataService::class);
    $invoiceCurrencies = collect($currencies)
        ->map(function ($option) use ($currencyMaster) {
            $value = $currencyMaster->currencyValue($option);
            return [
                'value' => $value,
                'label' => trim((string) ($option->name ?? '')) ?: $value,
            ];
        })
        ->filter(fn ($option) => $option['value'] !== '')
        ->unique('value')
        ->values();

    // One dropdown option per real client contact. The legacy client contact is
    // shown only when there are no structured contacts, avoiding duplicate rows
    // for the same person.
    $billingContacts = collect($contacts)
        ->filter(fn ($contact) => !empty($contact->id))
        ->unique(function ($contact) {
            $email = strtolower(trim((string) ($contact->email ?? '')));
            if ($email !== '') return 'email:'.$email;
            return 'contact:'.strtolower(trim((string) ($contact->name ?? ''))).'|'.trim((string) ($contact->phone ?? ''));
        })
        ->values();
    $billingContactNameCounts = $billingContacts
        ->groupBy(fn ($contact) => strtolower(trim((string) ($contact->name ?? ''))))
        ->map->count();
?>
<div class="ft-finance-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-invoice-modal'; ?>wire:key="create-invoice-modal" wire:click.self="closeCreateInvoice">
    <section class="ft-finance-modal ft-create-invoice-modal" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="createInvoiceTitle">
        <header class="ft-finance-modal-head">
            <div><h2 id="createInvoiceTitle">Create invoice</h2><p>Create and issue an invoice for this order.</p></div>
            <button type="button" wire:click="closeCreateInvoice" aria-label="Close">×</button>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceForm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-finance-form-alert"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-invoice-form-grid">
            <label><span>Invoice type <b>*</b></span><select wire:model.live="invoiceType"><option value="">Select invoice type</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoiceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($option->name); ?>"><?php echo e($option->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceType'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <label><span>Currency <b>*</b></span><select wire:model.live="invoiceCurrency"><option value="">Select currency</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoiceCurrencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($option['value']); ?>"><?php echo e($option['label']); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceCurrency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <label><span>Issue date <b>*</b></span><input type="date" wire:model.live="invoiceIssueDate"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceIssueDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <label><span>Payment terms <b>*</b></span><select wire:model.live="invoicePaymentTerms"><option value="">Select payment terms</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $paymentTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($option->name); ?>"><?php echo e($option->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoicePaymentTerms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <label><span>Due date <b>*</b></span><input type="date" wire:model="invoiceDueDate"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceDueDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
            <label><span>Billing contact <b>*</b></span><select wire:model="invoiceBillingContactId">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($billingContacts->isNotEmpty()): ?>
                    <option value="">Select billing contact</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $billingContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $contactName = trim((string) ($contact->name ?? '')) ?: trim((string) ($contact->email ?? '')) ?: 'Contact';
                            $sameNameCount = (int) ($billingContactNameCounts[strtolower(trim((string) ($contact->name ?? '')))] ?? 0);
                            $contactLabel = $contactName;
                            if ($sameNameCount > 1 && trim((string) ($contact->email ?? '')) !== '') $contactLabel .= ' · '.trim((string) $contact->email);
                        ?>
                        <option value="<?php echo e($contact->id); ?>"><?php echo e($contactLabel); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php else: ?>
                    <option value=""><?php echo e($job->client?->contact_name ?: $job->client?->email ?: 'Primary client contact'); ?></option>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceBillingContactId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
        </div>

        <section class="ft-invoice-items-box">
            <h3>Invoice items</h3>
            <div class="ft-invoice-item-head"><span>Description</span><span>Quantity</span><span>Unit price</span><span>Amount</span><span>action</span></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoiceLineItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php ($lineAmount = max(0,(float)($line['quantity'] ?? 0)) * max(0,(float)($line['unit_price'] ?? 0))); ?>
                <div class="ft-invoice-item-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'invoice-line-'.e($index).''; ?>wire:key="invoice-line-<?php echo e($index); ?>">
                    <div><input type="text" wire:model.live.debounce.250ms="invoiceLineItems.<?php echo e($index); ?>.description" aria-label="Description"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceLineItems.'.$index.'.description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div><input type="number" min="0.01" step="0.01" wire:model.live.debounce.250ms="invoiceLineItems.<?php echo e($index); ?>.quantity" aria-label="Quantity"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceLineItems.'.$index.'.quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="money-input"><span><?php echo e($currencyPrefix); ?></span><input type="number" min="0" step="0.01" wire:model.live.debounce.250ms="invoiceLineItems.<?php echo e($index); ?>.unit_price" aria-label="Unit price"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceLineItems.'.$index.'.unit_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <strong><?php echo e($currencyPrefix); ?><?php echo e(number_format($lineAmount, 2)); ?></strong>
                    <button type="button" wire:click="removeInvoiceLineItem(<?php echo e($index); ?>)" aria-label="Remove line item" <?php if(count($invoiceLineItems) <= 1): echo 'disabled'; endif; ?>>
                        <svg viewBox="0 0 24 24"><path d="M8 8v10M12 8v10M16 8v10M5 5h14M9 5V3h6v2M7 5l1 16h8l1-16"></path></svg>
                    </button>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceLineItems'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error ft-line-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="ft-add-invoice-line" wire:click="addInvoiceLineItem">＋ Add line item</button>
        </section>

        <div class="ft-invoice-lower-grid">
            <div class="ft-invoice-extra-fields">
                <label><span>Purchase order reference</span><input type="text" wire:model="invoicePurchaseOrderReference" placeholder="PO-2026-4481"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoicePurchaseOrderReference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <label><span>Notes / payment instructions</span><textarea wire:model="invoiceNotes"></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceNotes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                <label class="ft-invoice-upload"><span class="ft-paperclip" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8.5 12.5 14.8 6.2a3.1 3.1 0 1 1 4.4 4.4l-8.1 8.1a5 5 0 0 1-7.1-7.1l8.2-8.2"></path></svg></span><span class="ft-upload-copy">Attach supporting document</span><span class="ft-upload-browse">Browse</span><input type="file" wire:model="invoiceSupportingDocument" accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>"></label>
                <div class="ft-finance-uploading" wire:loading wire:target="invoiceSupportingDocument">Uploading document…</div>
                <?php if (isset($component)) { $__componentOriginal0493255fe8f58e45a72c968a3d197607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0493255fe8f58e45a72c968a3d197607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.finance.upload-preview','data' => ['file' => $invoiceSupportingDocument,'removeAction' => 'clearInvoiceSupportingDocument','title' => 'Supporting document']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.finance.upload-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['file' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoiceSupportingDocument),'remove-action' => 'clearInvoiceSupportingDocument','title' => 'Supporting document']); ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceSupportingDocument'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <aside class="ft-invoice-summary">
                <div><span>Subtotal</span><strong><?php echo e($currencyPrefix); ?><?php echo e(number_format($itemsSubtotal, 2)); ?></strong></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($remoteAreaCharge > 0): ?>
                    <div title="<?php echo e(trim((string) $invoiceRemoteAreaName)); ?><?php echo e($invoiceRemoteAreaPostalCode !== '' ? ' · '.$invoiceRemoteAreaPostalCode : ''); ?>"><span>Remote area charge</span><strong><?php echo e($currencyPrefix); ?><?php echo e(number_format($remoteAreaCharge, 2)); ?></strong></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div><span>Tax <?php echo e(number_format($taxRate, $taxRate == floor($taxRate) ? 0 : 2)); ?>%</span><strong><?php echo e($currencyPrefix); ?><?php echo e(number_format($taxAmount, 2)); ?></strong></div>
                <div class="total amount"><span>Total</span><strong><?php echo e($currencyPrefix); ?><?php echo e(number_format($total, 2)); ?></strong></div>
            </aside>
        </div>

        <footer class="ft-finance-modal-foot">
            <span>Invoice number will be generated automatically.</span>
            <div><button type="button" class="secondary" wire:click="createInvoice(true)" wire:loading.attr="disabled" wire:target="createInvoice">Save as draft</button><button type="button" class="primary" wire:click="createInvoice(false)" wire:loading.attr="disabled" wire:target="createInvoice">Create</button></div>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/finance/create-invoice-modal.blade.php ENDPATH**/ ?>