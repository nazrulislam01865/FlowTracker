<div class="ft-company-setup">
    <div class="ft-company-setup-head">
        <div>
            <span class="ft-company-kicker">Settings</span>
            <h1>Company Setup</h1>
            <p>Set the legal, contact and payment details that FlowTrack prints automatically on newly generated invoices.</p>
        </div>

        <div class="ft-company-head-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEditing): ?>
                <div class="ft-company-save-status" wire:loading wire:target="save">Saving company details…</div>
                <button type="button" class="secondary ft-company-head-cancel" wire:click="cancelEditing" wire:loading.attr="disabled" wire:target="cancelEditing,save">Cancel</button>
            <?php else: ?>
                <button type="button" class="primary ft-company-edit-button" wire:click="beginEditing" wire:loading.attr="disabled" wire:target="beginEditing">
                    <span wire:loading.remove wire:target="beginEditing">Edit Company Details</span>
                    <span wire:loading wire:target="beginEditing">Opening…</span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="flash"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEditing): ?>
        <form wire:submit="save" class="ft-company-form" data-ft-feedback-scope="form">
            <section class="card ft-company-card">
                <div class="ft-company-card-head">
                    <div><h2>Company identity</h2><p>These details identify the seller / issuing company on the invoice.</p></div>
                    <span class="ft-company-required-note">Legal name is required</span>
                </div>
                <div class="ft-company-grid">
                    <div class="field">
                        <label>Legal company name *</label>
                        <input wire:model="legalName" maxlength="180" placeholder="e.g. Step Promo Co., Ltd.">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['legalName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field">
                        <label>Trading / display name</label>
                        <input wire:model="tradingName" maxlength="180" placeholder="Optional trading name">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tradingName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field">
                        <label>Company registration number</label>
                        <input wire:model="registrationNumber" maxlength="100" placeholder="Registration / company number">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['registrationNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field">
                        <label>Tax / VAT number</label>
                        <input wire:model="taxNumber" maxlength="100" placeholder="Tax ID / VAT / GST number">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taxNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="card ft-company-card">
                <div class="ft-company-card-head"><div><h2>Contact & registered address</h2><p>Shown in the seller section of the invoice.</p></div></div>
                <div class="ft-company-grid">
                    <div class="field">
                        <label>Invoice / accounts email</label>
                        <input wire:model="billingEmail" type="email" maxlength="180" placeholder="accounts@company.com">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field">
                        <label>Phone</label>
                        <input wire:model="phone" maxlength="80" placeholder="Company phone number">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field full">
                        <label>Website</label>
                        <input wire:model="website" maxlength="180" placeholder="www.company.com">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field full">
                        <label>Address line 1</label>
                        <input wire:model="addressLine1" maxlength="180" placeholder="Building, road, district">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['addressLine1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field full">
                        <label>Address line 2</label>
                        <input wire:model="addressLine2" maxlength="180" placeholder="Suite, floor or additional address">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['addressLine2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field"><label>City</label><input wire:model="city" maxlength="100"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>State / region</label><input wire:model="stateRegion" maxlength="100"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['stateRegion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>Postal code</label><input wire:model="postalCode" maxlength="40"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['postalCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>Country</label><input wire:model="country" maxlength="100"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                </div>
            </section>

            <section class="card ft-company-card">
                <div class="ft-company-card-head"><div><h2>Payment details</h2><p>Optional bank and remittance information printed on invoices so clients know how to pay.</p></div></div>
                <div class="ft-company-grid">
                    <div class="field"><label>Bank name</label><input wire:model="bankName" maxlength="160"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bankName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>Account name</label><input wire:model="bankAccountName" maxlength="160"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bankAccountName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>Account number</label><input wire:model="bankAccountNumber" maxlength="120"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bankAccountNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>IBAN</label><input wire:model="bankIban" maxlength="120"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bankIban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field"><label>SWIFT / BIC</label><input wire:model="bankSwift" maxlength="80"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bankSwift'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="field full"><label>Payment instructions</label><textarea wire:model="paymentInstructions" rows="3" maxlength="1000" placeholder="e.g. Please include the invoice number with your transfer."></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentInstructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                </div>
            </section>

            <section class="card ft-company-card">
                <div class="ft-company-card-head"><div><h2>Invoice footer</h2><p>Add an optional legal or thank-you line at the bottom of invoices.</p></div></div>
                <div class="field">
                    <label>Footer text</label>
                    <textarea wire:model="invoiceFooter" rows="3" maxlength="500" placeholder="Optional invoice footer"></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoiceFooter'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-company-info-note">Company details are copied into each new invoice when it is created. Updating Company Setup changes future invoices without silently changing previously generated invoices.</div>
            </section>

            <div class="ft-company-actions">
                <button type="button" class="secondary" wire:click="cancelEditing" wire:loading.attr="disabled" wire:target="cancelEditing,save">Cancel</button>
                <button type="submit" class="primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Save Company Details</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
            </div>
        </form>
    <?php else: ?>
        <div class="ft-company-form ft-company-view-mode">
            <section class="card ft-company-card">
                <div class="ft-company-card-head">
                    <div><h2>Company identity</h2><p>These details identify the seller / issuing company on the invoice.</p></div>
                </div>
                <div class="ft-company-view-grid">
                    <div class="ft-company-view-item"><span>Legal company name</span><strong><?php echo e(filled($legalName) ? $legalName : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Trading / display name</span><strong><?php echo e(filled($tradingName) ? $tradingName : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Company registration number</span><strong><?php echo e(filled($registrationNumber) ? $registrationNumber : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Tax / VAT number</span><strong><?php echo e(filled($taxNumber) ? $taxNumber : '—'); ?></strong></div>
                </div>
            </section>

            <section class="card ft-company-card">
                <div class="ft-company-card-head"><div><h2>Contact & registered address</h2><p>Shown in the seller section of the invoice.</p></div></div>
                <div class="ft-company-view-grid">
                    <div class="ft-company-view-item"><span>Invoice / accounts email</span><strong><?php echo e(filled($billingEmail) ? $billingEmail : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Phone</span><strong><?php echo e(filled($phone) ? $phone : '—'); ?></strong></div>
                    <div class="ft-company-view-item full"><span>Website</span><strong><?php echo e(filled($website) ? $website : '—'); ?></strong></div>
                    <div class="ft-company-view-item full"><span>Address line 1</span><strong><?php echo e(filled($addressLine1) ? $addressLine1 : '—'); ?></strong></div>
                    <div class="ft-company-view-item full"><span>Address line 2</span><strong><?php echo e(filled($addressLine2) ? $addressLine2 : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>City</span><strong><?php echo e(filled($city) ? $city : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>State / region</span><strong><?php echo e(filled($stateRegion) ? $stateRegion : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Postal code</span><strong><?php echo e(filled($postalCode) ? $postalCode : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Country</span><strong><?php echo e(filled($country) ? $country : '—'); ?></strong></div>
                </div>
            </section>

            <section class="card ft-company-card">
                <div class="ft-company-card-head"><div><h2>Payment details</h2><p>Optional bank and remittance information printed on invoices so clients know how to pay.</p></div></div>
                <div class="ft-company-view-grid">
                    <div class="ft-company-view-item"><span>Bank name</span><strong><?php echo e(filled($bankName) ? $bankName : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Account name</span><strong><?php echo e(filled($bankAccountName) ? $bankAccountName : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>Account number</span><strong><?php echo e(filled($bankAccountNumber) ? $bankAccountNumber : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>IBAN</span><strong><?php echo e(filled($bankIban) ? $bankIban : '—'); ?></strong></div>
                    <div class="ft-company-view-item"><span>SWIFT / BIC</span><strong><?php echo e(filled($bankSwift) ? $bankSwift : '—'); ?></strong></div>
                    <div class="ft-company-view-item full"><span>Payment instructions</span><strong class="multiline"><?php echo e(filled($paymentInstructions) ? $paymentInstructions : '—'); ?></strong></div>
                </div>
            </section>

            <section class="card ft-company-card">
                <div class="ft-company-card-head"><div><h2>Invoice footer</h2><p>Add an optional legal or thank-you line at the bottom of invoices.</p></div></div>
                <div class="ft-company-view-grid single">
                    <div class="ft-company-view-item full"><span>Footer text</span><strong class="multiline"><?php echo e(filled($invoiceFooter) ? $invoiceFooter : '—'); ?></strong></div>
                </div>
                <div class="ft-company-info-note">Company details are copied into each new invoice when it is created. Updating Company Setup changes future invoices without silently changing previously generated invoices.</div>
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/company-setup/index.blade.php ENDPATH**/ ?>