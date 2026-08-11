<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'users',
    'clientCode',
    'clientName' => '',
    'clientCountries' => [],
    'clientCountryFlags' => [],
    'clientStatesByCountry' => [],
    'clientLanguages' => [],
    'clientCurrencies' => [],
    'paymentTermOptions' => [],
    'accountManagerId' => null,
    'preferredCurrency' => '',
    'clientCountry' => '',
    'billingCountry' => '',
    'billingSameAsOffice' => true,
    'salesTaxStatus' => 'taxable',
    'shippingAddresses' => [],
    'mode' => 'create',
    'clientLogoUpload' => null,
    'existingClientLogoUrl' => '',
    'removeClientLogo' => false,
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
    'users',
    'clientCode',
    'clientName' => '',
    'clientCountries' => [],
    'clientCountryFlags' => [],
    'clientStatesByCountry' => [],
    'clientLanguages' => [],
    'clientCurrencies' => [],
    'paymentTermOptions' => [],
    'accountManagerId' => null,
    'preferredCurrency' => '',
    'clientCountry' => '',
    'billingCountry' => '',
    'billingSameAsOffice' => true,
    'salesTaxStatus' => 'taxable',
    'shippingAddresses' => [],
    'mode' => 'create',
    'clientLogoUpload' => null,
    'existingClientLogoUrl' => '',
    'removeClientLogo' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $isEdit = $mode === 'edit';
    $selectedManager = $users->firstWhere('id', (int) $accountManagerId);
    $managerInitials = $selectedManager ? \App\Support\BoardPresenter::initials($selectedManager->name) : 'U';
    $officeStates = $clientStatesByCountry[$clientCountry] ?? [];
    $billingStates = $clientStatesByCountry[$billingCountry] ?? [];
    $clientLogoPreview = null;
    if ($clientLogoUpload && in_array(strtolower((string) $clientLogoUpload->getClientOriginalExtension()), ['jpg','jpeg','png','webp'], true)) {
        try { $clientLogoPreview = $clientLogoUpload->temporaryUrl(); } catch (\Throwable $e) { $clientLogoPreview = null; }
    }
    if (! $clientLogoPreview && ! $removeClientLogo && $existingClientLogoUrl !== '') $clientLogoPreview = $existingClientLogoUrl;
?>
<div class="<?php echo e($isEdit ? 'ft-client-inline-edit ft-client-create-prototype' : 'ft-create-client-page ft-client-create-prototype'); ?>">
    <div class="ft-client-create-shell">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($isEdit)): ?>
            <div class="ft-client-create-top">
                <div>
                    <div class="ft-create-breadcrumb">Add Client</div>
                    <h1>Add Client</h1>
                    <p>Client Information is saved as a full page, not a drawer.</p>
                </div>
                <button type="button" class="ft-back-clients" wire:click="closeCreate">← Back to Clients</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ft-client-prototype-card">
            <header class="ft-client-prototype-head">
                <div>
                    <h2><?php echo e($isEdit ? 'Edit Client' : 'Create New Client'); ?></h2>
                    <p><?php echo e($isEdit ? 'Update the client business, contact, address and commercial information.' : "Add the client's business, contact and delivery information."); ?></p>
                </div>
                <div class="ft-client-required-note"><span>*</span> Required <b>•</b> Optional fields are labeled</div>
            </header>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>1</span><div><h3>Client details</h3></div></div>
                <div class="ft-client-grid ft-client-grid-3">
                    <div class="ft-client-logo-upload">
                        <div class="ft-client-logo-preview">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientLogoPreview): ?>
                                <img src="<?php echo e($clientLogoPreview); ?>" alt="Client logo preview">
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['name' => $clientName ?: 'Client','size' => 82]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientName ?: 'Client'),'size' => 82]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ft-client-logo-upload-copy">
                            <b>Client logo <span style="font-weight:500;color:#71829a">(Optional)</span></b>
                            <p>Upload the company logo once and FlowTrack will reuse it anywhere this client is shown. JPG, PNG or WebP · max 5 MB.</p>
                            <div class="ft-client-logo-actions">
                                <label class="ft-client-logo-file">
                                    <input type="file" wire:model="clientLogoUpload" accept="image/jpeg,image/png,image/webp">
                                    <span wire:loading.remove wire:target="clientLogoUpload"><?php echo e($clientLogoPreview ? 'Choose new logo' : 'Choose logo'); ?></span>
                                    <span wire:loading wire:target="clientLogoUpload">Preparing…</span>
                                </label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit && $existingClientLogoUrl !== '' && ! $removeClientLogo && ! $clientLogoUpload): ?>
                                    <button type="button" class="ft-client-logo-remove" wire:click="markClientLogoForRemoval">Remove logo</button>
                                <?php elseif($removeClientLogo): ?>
                                    <button type="button" class="ft-client-logo-undo" wire:click="restoreClientLogo">Undo remove</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($removeClientLogo): ?><small class="ft-client-logo-removal-note">The existing logo will be removed when you save the client.</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientLogoUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <label class="ft-proto-field">
                        <b>Client code</b>
                        <div class="ft-client-code-lock"><span><?php echo e($clientCode); ?></span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10V7a5 5 0 0110 0v3m-9 0h8a2 2 0 012 2v7H6v-7a2 2 0 012-2z" fill="none" stroke="currentColor" stroke-width="1.8"/></svg></div>
                        <small>Generated automatically after the client is created.</small>
                    </label>
                    <label class="ft-proto-field">
                        <b>Client name <em>*</em></b>
                        <input wire:model="clientName" placeholder="Acme Apparel Inc.">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <label class="ft-proto-field">
                        <b>Legal business name <span>(Optional)</span></b>
                        <input wire:model="legalBusinessName" placeholder="Acme Apparel Incorporated">
                    </label>
                    <label class="ft-proto-field">
                        <b>Website <span>(Optional)</span></b>
                        <input wire:model="website" placeholder="www.acmeapparel.com">
                    </label>
                    <label class="ft-proto-field">
                        <b>Account manager <em>*</em></b>
                        <div class="ft-manager-select"><span><?php echo e($managerInitials); ?></span><select wire:model.live="accountManagerId"><option value="">Unassigned</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accountManagerId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <label class="ft-proto-field">
                        <b>Preferred language <span>(Optional)</span></b>
                        <select wire:model="preferredLanguage"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($language); ?>"><?php echo e($language); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                    </label>
                    <label class="ft-proto-field ft-currency-field">
                        <b>Preferred currency <em>*</em></b>
                        <select wire:model="preferredCurrency">
                            <option value="">Select currency</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientCurrencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currencyName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($code); ?>"><?php echo e($code); ?> · <?php echo e($currencyName); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <small>Available currencies are managed in Master Data.</small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['preferredCurrency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                </div>
            </section>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>2</span><div><h3>Primary contact</h3><p>Main person for inquiries, orders and documents.</p></div></div>
                <div class="ft-client-grid ft-client-grid-2">
                    <label class="ft-proto-field"><b>Contact name <em>*</em></b><input wire:model="contactName" placeholder="Sarah Chen"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contactName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-proto-field"><b>Job title <span>(Optional)</span></b><input wire:model="contactJobTitle" placeholder="Purchasing Manager"></label>
                    <label class="ft-proto-field"><b>Email <em>*</em></b><input type="email" wire:model="email" placeholder="purchasing@acmeapparel.com"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-proto-field"><b>Phone <span>(Optional)</span></b><input wire:model="phone" placeholder="+1 (212) 555-0184"></label>
                </div>
            </section>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>3</span><div><h3>Office &amp; billing address</h3><p>Used for correspondence and billing.</p></div></div>
                <div class="ft-client-grid ft-office-address-grid">
                    <label class="ft-proto-field ft-address-line"><b>Address line 1 <em>*</em></b><input wire:model="officeAddressLine1" placeholder="123 W 37th Street"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['officeAddressLine1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-proto-field"><b>Suite / unit <span>(Optional)</span></b><input wire:model="officeSuite" placeholder="Suite 800"></label>
                    <label class="ft-proto-field"><b>City <em>*</em></b><input wire:model="officeCity" placeholder="New York"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['officeCity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-proto-field"><b>State <em>*</em></b><select wire:model="officeState" <?php if(empty($officeStates)): echo 'disabled'; endif; ?>><option value=""><?php echo e(empty($officeStates) ? 'No states configured' : 'Select state'); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $officeStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($state); ?>"><?php echo e($state); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['officeState'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-proto-field"><b>ZIP code <em>*</em></b><input wire:model="officeZip" placeholder="10018"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['officeZip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label class="ft-proto-field ft-country-field"><b>Country <em>*</em></b><div class="ft-country-select"><span><?php echo e($clientCountryFlags[$clientCountry] ?? '🌐'); ?></span><select wire:model.live="clientCountry"><option value="">Select country</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($country); ?>"><?php echo e($country); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div><small>Countries are managed in Master Data.</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientCountry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                </div>
                <div class="ft-billing-choice">
                    <label><input type="checkbox" wire:model.live="billingSameAsOffice"> <span>Use office address as billing address</span></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($billingSameAsOffice): ?>
                        <button type="button" wire:click="showDifferentBillingAddress">Add a different billing address</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$billingSameAsOffice): ?>
                    <div class="ft-client-grid ft-billing-grid">
                        <label class="ft-proto-field ft-address-line"><b>Billing address line 1</b><input wire:model="billingAddressLine1" placeholder="Billing street address"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingAddressLine1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <label class="ft-proto-field"><b>Suite / unit <span>(Optional)</span></b><input wire:model="billingSuite"></label>
                        <label class="ft-proto-field"><b>City</b><input wire:model="billingCity"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingCity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <label class="ft-proto-field"><b>State</b><select wire:model="billingState" <?php if(empty($billingStates)): echo 'disabled'; endif; ?>><option value=""><?php echo e(empty($billingStates) ? 'No states configured' : 'Select state'); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $billingStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($state); ?>"><?php echo e($state); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingState'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <label class="ft-proto-field"><b>ZIP code</b><input wire:model="billingZip"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingZip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <label class="ft-proto-field ft-country-field"><b>Country</b><select wire:model.live="billingCountry"><option value="">Select country</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($country); ?>"><?php echo e($country); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingCountry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-client-prototype-section ft-shipping-section">
                <div class="ft-client-section-title ft-client-section-title-spread">
                    <div class="ft-section-title-left"><span>4</span><div><h3>Shipping addresses</h3><p>Add one or more delivery locations for orders.</p></div></div>
                    <small class="ft-address-count"><?php echo e(count($shippingAddresses)); ?> <?php echo e(count($shippingAddresses) === 1 ? 'address' : 'addresses'); ?></small>
                </div>

                <div class="ft-shipping-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shippingAddresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $expanded = (bool)($address['expanded'] ?? false);
                            $displayLabel = trim((string)($address['label'] ?? '')) ?: 'Shipping address '.($index + 1);
                            $displayAddress = collect([$address['address_line1'] ?? '', $address['suite'] ?? '', $address['city'] ?? '', $address['state'] ?? '', $address['zip'] ?? '', $address['country'] ?? ''])->filter()->implode(', ');
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expanded): ?>
                            <article class="ft-shipping-card is-expanded" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipping-expanded-'.e($index).''; ?>wire:key="shipping-expanded-<?php echo e($index); ?>">
                                <header>
                                    <div class="ft-shipping-card-title"><b><?php echo e($displayLabel); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address['is_default'] ?? false): ?><span>Default shipping</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                                    <div class="ft-shipping-card-actions"><button type="button" wire:click="duplicateShippingAddress(<?php echo e($index); ?>)">⧉&nbsp; Duplicate</button><button type="button" class="icon-only" aria-label="More options">⋮</button><button type="button" class="icon-only" wire:click="toggleShippingAddress(<?php echo e($index); ?>)" aria-label="Collapse">⌃</button></div>
                                </header>
                                <div class="ft-client-grid ft-shipping-grid">
                                    <label class="ft-proto-field"><b>Location label <em>*</em></b><input wire:model="shippingAddresses.<?php echo e($index); ?>.label" placeholder="New York Warehouse"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["shippingAddresses.$index.label"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                    <label class="ft-proto-field"><b>Recipient <span>(Optional)</span></b><input wire:model="shippingAddresses.<?php echo e($index); ?>.recipient" placeholder="Receiving Department"></label>
                                    <label class="ft-proto-field ft-address-line"><b>Address line 1 <em>*</em></b><input wire:model="shippingAddresses.<?php echo e($index); ?>.address_line1" placeholder="450 10th Avenue"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["shippingAddresses.$index.address_line1"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                    <label class="ft-proto-field"><b>Suite / unit <span>(Optional)</span></b><input wire:model="shippingAddresses.<?php echo e($index); ?>.suite" placeholder="Dock 4"></label>
                                    <label class="ft-proto-field"><b>City <em>*</em></b><input wire:model="shippingAddresses.<?php echo e($index); ?>.city" placeholder="New York"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["shippingAddresses.$index.city"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                    <?php
                                        $shippingStates = $clientStatesByCountry[$address['country'] ?? ''] ?? [];
                                    ?>
                                    <label class="ft-proto-field"><b>State <em>*</em></b><select wire:model="shippingAddresses.<?php echo e($index); ?>.state" <?php if(empty($shippingStates)): echo 'disabled'; endif; ?>><option value=""><?php echo e(empty($shippingStates) ? 'No states configured' : 'Select state'); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shippingStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($state); ?>"><?php echo e($state); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["shippingAddresses.$index.state"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                    <label class="ft-proto-field"><b>ZIP code <em>*</em></b><input wire:model="shippingAddresses.<?php echo e($index); ?>.zip" placeholder="10001"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["shippingAddresses.$index.zip"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                    <label class="ft-proto-field ft-country-field"><b>Country <em>*</em></b><div class="ft-country-select"><span><?php echo e($clientCountryFlags[$address['country'] ?? ''] ?? '🌐'); ?></span><select wire:model.live="shippingAddresses.<?php echo e($index); ?>.country"><option value="">Select country</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($country); ?>"><?php echo e($country); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["shippingAddresses.$index.country"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                </div>
                                <label class="ft-default-shipping-check"><input type="checkbox" <?php if($address['is_default'] ?? false): echo 'checked'; endif; ?> wire:click="setDefaultShippingAddress(<?php echo e($index); ?>)"> <span>Set as default shipping address</span></label>
                            </article>
                        <?php else: ?>
                            <article class="ft-shipping-card is-collapsed" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'shipping-collapsed-'.e($index).''; ?>wire:key="shipping-collapsed-<?php echo e($index); ?>">
                                <div><b><?php echo e($displayLabel); ?></b><p><?php echo e($displayAddress ?: 'Address details not added yet'); ?></p></div>
                                <div class="ft-shipping-collapsed-actions"><button type="button" wire:click="editShippingAddress(<?php echo e($index); ?>)">✎&nbsp; Edit</button><button type="button" class="danger" wire:click="removeShippingAddress(<?php echo e($index); ?>)">♙&nbsp; Remove</button><button type="button" class="icon-only" wire:click="toggleShippingAddress(<?php echo e($index); ?>)">⌄</button></div>
                            </article>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <button type="button" class="ft-add-shipping" wire:click="addShippingAddress"><span>＋</span> Add another shipping address</button>
            </section>

            <section class="ft-client-prototype-section">
                <div class="ft-client-section-title"><span>5</span><div><h3>Business &amp; billing preferences</h3></div></div>
                <div class="ft-business-preferences">
                    <label class="ft-proto-field"><b>EIN / Tax ID <span>(Optional)</span></b><input wire:model="einTaxId" placeholder="XX-XXXXXXX"></label>
                    <div class="ft-proto-field"><b>Sales tax status <em>*</em></b><div class="ft-tax-toggle"><button type="button" class="<?php echo e($salesTaxStatus === 'taxable' ? 'active' : ''); ?>" wire:click="$set('salesTaxStatus','taxable')">Taxable</button><button type="button" class="<?php echo e($salesTaxStatus === 'tax_exempt' ? 'active' : ''); ?>" wire:click="$set('salesTaxStatus','tax_exempt')">Tax exempt</button></div></div>
                    <label class="ft-proto-field"><b>Payment terms <span>(Optional)</span></b><select wire:model="paymentTerms"><option value="">Select terms</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $paymentTermOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($term); ?>"><?php echo e($term); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label>
                </div>
                <div class="ft-po-row"><span>PO required <small>(Optional)</small></span><label class="ft-switch"><input type="checkbox" wire:model="poRequired"><i></i></label></div>
                <p class="ft-tax-certificate-note">Tax exemption certificates can be added from the client profile after creation.</p>
            </section>

            <section class="ft-client-prototype-section ft-notes-section">
                <div class="ft-client-section-title"><span>6</span><div><h3>Internal notes <small>(Optional)</small></h3></div></div>
                <label class="ft-proto-field"><textarea wire:model="notes" placeholder="Commercial preferences, account instructions or internal notes..."></textarea></label>
            </section>

            <footer class="ft-client-prototype-footer">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                        <em class="validation-error">Please correct the highlighted fields before saving.</em>
                    <?php else: ?>
                        Required fields are marked with&nbsp; <em>*</em>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit): ?>
                        <button type="button" class="ft-create-cancel" wire:click="cancelEditClient">Cancel</button>
                        <button type="button" class="ft-create-primary" wire:click="updateClient" wire:loading.attr="disabled" wire:target="updateClient">Save Client</button>
                    <?php else: ?>
                        <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
                        <button type="button" class="ft-client-save-draft" wire:click="saveClientDraft" wire:loading.attr="disabled" wire:target="saveClientDraft,createClient">Save as draft</button>
                        <button type="button" class="ft-create-primary" wire:click="createClient" wire:loading.attr="disabled" wire:target="saveClientDraft,createClient">Create client</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </footer>
        </section>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/clients/create.blade.php ENDPATH**/ ?>