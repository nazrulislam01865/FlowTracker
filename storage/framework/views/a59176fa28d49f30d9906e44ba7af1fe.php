<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'rfqReminderEnabled' => false,
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
    'rfqReminderEnabled' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="ft-rfq-settings-backdrop" wire:click.self="closeRfqSettings" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-rfq-settings-modal'; ?>wire:key="inquiry-rfq-settings-modal">
    <section class="ft-rfq-settings-modal" role="dialog" aria-modal="true" aria-labelledby="rfq-settings-title" data-ft-feedback-scope="form">
        <header class="ft-rfq-settings-head">
            <div>
                <h2 id="rfq-settings-title">Supplier quotation settings</h2>
                <p>Control what suppliers receive, how long the secure quotation link stays active, and what happens after they respond.</p>
            </div>
            <button type="button" class="ft-rfq-settings-close" wire:click="closeRfqSettings" aria-label="Close supplier quotation settings">×</button>
        </header>

        <form class="ft-rfq-settings-form" wire:submit.prevent="saveRfqSettings">
            <div class="ft-rfq-settings-scroll">
                <section class="ft-rfq-settings-section" aria-labelledby="rfq-settings-content-title">
                    <div class="ft-rfq-settings-section-head">
                        <span class="ft-rfq-settings-section-icon">1</span>
                        <div>
                            <h3 id="rfq-settings-content-title">Invitation content</h3>
                            <p>Add supplier-facing context without changing the standard RFQ email template.</p>
                        </div>
                    </div>

                    <label class="ft-rfq-settings-field">
                        <span>Special note <small>Optional</small></span>
                        <textarea rows="3" wire:model="rfqSpecialNote" maxlength="4000" placeholder="Example: Please include tooling cost separately and confirm food-grade packaging compliance."></textarea>
                        <small>This note is highlighted in the invitation email and shown in the quotation form.</small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqSpecialNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><em><?php echo e($message); ?></em><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>

                    <label class="ft-rfq-settings-field">
                        <span>Inquiry / product details for supplier <small>Optional</small></span>
                        <textarea rows="4" wire:model="rfqSupplierDetails" maxlength="8000" placeholder="Add specifications, delivery expectations, packaging requirements, testing requirements, or other information the supplier needs before quoting."></textarea>
                        <small>Only add information that is safe to expose through the supplier invitation link. Internal Inquiry notes are not sent automatically.</small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqSupplierDetails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><em><?php echo e($message); ?></em><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                </section>

                <section class="ft-rfq-settings-section" aria-labelledby="rfq-settings-access-title">
                    <div class="ft-rfq-settings-section-head">
                        <span class="ft-rfq-settings-section-icon">2</span>
                        <div>
                            <h3 id="rfq-settings-access-title">Deadline &amp; secure-link access</h3>
                            <p>Set the quotation deadline separately from the lifetime of the secure supplier link.</p>
                        </div>
                    </div>

                    <div class="ft-rfq-settings-grid is-two">
                        <label class="ft-rfq-settings-field">
                            <span>Quotation due <b>*</b></span>
                            <input type="datetime-local" wire:model="rfqDefaultDueAt">
                            <small>Displayed in the supplier email and quotation summary.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqDefaultDueAt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><em><?php echo e($message); ?></em><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <div class="ft-rfq-settings-field">
                            <span>Secure link valid for <b>*</b></span>
                            <div class="ft-rfq-settings-duration">
                                <input type="number" min="1" wire:model="rfqLinkValidityValue" aria-label="Secure link validity value">
                                <select wire:model="rfqLinkValidityUnit" aria-label="Secure link validity unit">
                                    <option value="hours">Hours</option>
                                    <option value="days">Days</option>
                                </select>
                            </div>
                            <small>The timer starts when each invitation is sent or resent. Maximum 90 days.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqLinkValidityValue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><em><?php echo e($message); ?></em><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqLinkValidityUnit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><em><?php echo e($message); ?></em><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="ft-rfq-settings-section" aria-labelledby="rfq-settings-automation-title">
                    <div class="ft-rfq-settings-section-head">
                        <span class="ft-rfq-settings-section-icon">3</span>
                        <div>
                            <h3 id="rfq-settings-automation-title">Supplier response automation</h3>
                            <p>Choose which follow-up actions FlowTrack handles automatically.</p>
                        </div>
                    </div>

                    <div class="ft-rfq-settings-options">
                        <label class="ft-rfq-settings-option">
                            <input type="checkbox" wire:model.live="rfqAutoReplyEnabled">
                            <span class="ft-rfq-settings-switch" aria-hidden="true"><i></i></span>
                            <span class="ft-rfq-settings-option-copy">
                                <strong>Send submission confirmation automatically</strong>
                                <small>Email the supplier a receipt immediately after a quotation is submitted.</small>
                            </span>
                        </label>

                        <div class="ft-rfq-settings-option-block">
                            <label class="ft-rfq-settings-option">
                                <input type="checkbox" wire:model.live="rfqReminderEnabled">
                                <span class="ft-rfq-settings-switch" aria-hidden="true"><i></i></span>
                                <span class="ft-rfq-settings-option-copy">
                                    <strong>Send quotation deadline reminder</strong>
                                    <small>Send one reminder only if the supplier has not submitted or declined.</small>
                                </span>
                            </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rfqReminderEnabled): ?>
                                <label class="ft-rfq-settings-inline-select">
                                    <span>Send reminder</span>
                                    <select wire:model="rfqReminderHoursBeforeDue">
                                        <option value="12">12 hours before due</option>
                                        <option value="24">24 hours before due</option>
                                        <option value="48">2 days before due</option>
                                        <option value="72">3 days before due</option>
                                        <option value="168">7 days before due</option>
                                    </select>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqReminderHoursBeforeDue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><em class="ft-rfq-settings-error"><?php echo e($message); ?></em><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <label class="ft-rfq-settings-option">
                            <input type="checkbox" wire:model.live="rfqAllowRevision">
                            <span class="ft-rfq-settings-switch" aria-hidden="true"><i></i></span>
                            <span class="ft-rfq-settings-option-copy">
                                <strong>Allow supplier to revise after submission</strong>
                                <small>The supplier can reopen and resubmit while the secure link is still valid and before an award decision.</small>
                            </span>
                        </label>

                        <label class="ft-rfq-settings-option">
                            <input type="checkbox" wire:model.live="rfqAwardEmailEnabled">
                            <span class="ft-rfq-settings-switch" aria-hidden="true"><i></i></span>
                            <span class="ft-rfq-settings-option-copy">
                                <strong>Email the awarded supplier automatically</strong>
                                <small>Send the winner notification when an internal user awards a submitted quotation.</small>
                            </span>
                        </label>

                        <label class="ft-rfq-settings-option">
                            <input type="checkbox" wire:model.live="rfqNotSelectedEmailEnabled">
                            <span class="ft-rfq-settings-switch" aria-hidden="true"><i></i></span>
                            <span class="ft-rfq-settings-option-copy">
                                <strong>Email non-selected suppliers automatically</strong>
                                <small>Notify the other invited suppliers after a quotation is awarded.</small>
                            </span>
                        </label>
                    </div>
                </section>

                <div class="ft-rfq-settings-info">
                    <span>i</span>
                    <p>Automation changes apply to open invitations immediately. Invitation content and deadline apply to drafts and the next send or resend. A delivered link keeps its current expiry until it is resent.</p>
                </div>
            </div>

            <footer class="ft-rfq-settings-actions">
                <button type="button" class="ft-rfq-settings-btn is-secondary" wire:click="closeRfqSettings">Cancel</button>
                <button type="submit" class="ft-rfq-settings-btn is-primary" wire:loading.attr="disabled" wire:target="saveRfqSettings">
                    <span wire:loading.remove wire:target="saveRfqSettings">Save settings</span>
                    <span wire:loading wire:target="saveRfqSettings">Saving…</span>
                </button>
            </footer>
        </form>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries-rfq-settings.blade.php ENDPATH**/ ?>