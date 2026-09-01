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
    'officeState' => '',
    'billingState' => '',
    'billingCountry' => '',
    'billingRecipient' => '',
    'billingAddressLine1' => '',
    'billingSuite' => '',
    'billingCity' => '',
    'billingZip' => '',
    'billingSameAsOffice' => true,
    'salesTaxStatus' => 'taxable',
    'shippingAddresses' => [],
    'contacts' => [],
    'mode' => 'create',
    'clientLogoUpload' => null,
    'existingClientLogoUrl' => '',
    'removeClientLogo' => false,
    'addressOptionsReady' => true,
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
    'officeState' => '',
    'billingState' => '',
    'billingCountry' => '',
    'billingRecipient' => '',
    'billingAddressLine1' => '',
    'billingSuite' => '',
    'billingCity' => '',
    'billingZip' => '',
    'billingSameAsOffice' => true,
    'salesTaxStatus' => 'taxable',
    'shippingAddresses' => [],
    'contacts' => [],
    'mode' => 'create',
    'clientLogoUpload' => null,
    'existingClientLogoUrl' => '',
    'removeClientLogo' => false,
    'addressOptionsReady' => true,
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
    $accountManagerOptions = collect($users)->values();
    $selectedManager = $accountManagerOptions->first(fn ($option) => (string) data_get($option, 'id') === (string) $accountManagerId);
    $countryOptions = collect($clientCountries)->map(fn ($country) => [
        'id' => (string) $country,
        'label' => (string) $country,
        'meta' => (string) ($clientCountryFlags[$country] ?? ''),
    ])->values();
    $officeStates = $clientStatesByCountry[$clientCountry] ?? [];
    $billingStates = $clientStatesByCountry[$billingCountry] ?? [];
    $billingLine2Visible = trim((string) $billingSuite) !== '';
    $clientLogoPreview = null;
    if ($clientLogoUpload && in_array(strtolower((string) $clientLogoUpload->getClientOriginalExtension()), ['jpg','jpeg','png','webp'], true)) {
        try { $clientLogoPreview = $clientLogoUpload->temporaryUrl(); } catch (\Throwable $e) { $clientLogoPreview = null; }
    }
    if (! $clientLogoPreview && ! $removeClientLogo && $existingClientLogoUrl !== '') $clientLogoPreview = $existingClientLogoUrl;
    $clientValidationSignature = $errors->any()
        ? sha1((string) json_encode($errors->getMessages(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        : '';
?>
<div class="<?php echo e($isEdit ? 'ft-client-inline-edit ft-client-create-prototype ft-reusable-form-theme' : 'ft-create-client-page ft-client-create-prototype ft-reusable-form-theme ft-form-standard ft-form-standard--client'); ?>" data-ft-feedback-scope="form" data-client-validation-signature="<?php echo e($clientValidationSignature); ?>">
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
                            <b>Client logo <span class="ft-client-logo-optional">(Optional)</span></b>
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
                    <div class="ft-proto-field">
                        <b>Account manager <em>*</em></b>
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Account manager','property' => 'accountManagerId','value' => $accountManagerId ?? '','placeholder' => 'Unassigned','type' => 'users','context' => 'client-account-manager','initialOptions' => $accountManagerOptions,'selectedLabel' => data_get($selectedManager, 'label', 'Unassigned'),'searchPlaceholder' => 'Search account manager…','menuWidth' => 360,'fixedMenu' => true,'hideLabel' => true,'class' => 'ft-form-search-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Account manager','property' => 'accountManagerId','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($accountManagerId ?? ''),'placeholder' => 'Unassigned','type' => 'users','context' => 'client-account-manager','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($accountManagerOptions),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(data_get($selectedManager, 'label', 'Unassigned')),'search-placeholder' => 'Search account manager…','menu-width' => 360,'fixed-menu' => true,'hide-label' => true,'class' => 'ft-form-search-select']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accountManagerId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
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
                        <small>Available currencies are managed in Financial Master Data.</small>
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

            <section class="ft-client-prototype-section ft-client-contacts-editor">
                <div class="ft-client-section-title">
                    <div class="ft-section-title-left"><span>2</span><div><h3>Contact</h3><p>Add the people your team can use for inquiries, orders and documents. The first contact is primary.</p></div></div>
                </div>
                <div class="ft-client-contact-editor-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contactIndex => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <article class="ft-client-contact-editor-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'client-contact-row-'.e($contactIndex).''; ?>wire:key="client-contact-row-<?php echo e($contactIndex); ?>">
                            <div class="ft-client-contact-row-label">
                                <b><?php echo e($contactIndex === 0 ? 'Primary contact' : 'Contact '.($contactIndex + 1)); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contactIndex === 0): ?><span>Primary</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <label class="ft-proto-field"><b>Contact name <em>*</em></b><input wire:model="contacts.<?php echo e($contactIndex); ?>.name" placeholder="Sarah Chen"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contacts.'.$contactIndex.'.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                            <label class="ft-proto-field"><b>Job title <span>(Optional)</span></b><input wire:model="contacts.<?php echo e($contactIndex); ?>.job_title" placeholder="Purchasing Manager"></label>
                            <label class="ft-proto-field"><b>Email <em>*</em></b><input type="email" wire:model="contacts.<?php echo e($contactIndex); ?>.email" placeholder="purchasing@acmeapparel.com"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contacts.'.$contactIndex.'.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                            <label class="ft-proto-field"><b>Phone <span>(Optional)</span></b><input wire:model="contacts.<?php echo e($contactIndex); ?>.phone" placeholder="+1 (212) 555-0184"></label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($contacts) > 1): ?>
                                <button type="button" class="ft-client-remove-contact" wire:click="removeContact(<?php echo e($contactIndex); ?>)" aria-label="Remove <?php echo e($contactIndex === 0 ? 'primary contact' : 'contact '.($contactIndex + 1)); ?>">×</button>
                            <?php else: ?>
                                <span class="ft-client-contact-remove-spacer" aria-hidden="true"></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($contacts) < 20): ?>
                        <div class="ft-client-contact-add-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'client-contact-add-row'; ?>wire:key="client-contact-add-row">
                            <button type="button" class="ft-client-add-contact" wire:click="addContact">+ Add contact</button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contacts'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-client-contacts-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit || $addressOptionsReady): ?>
            <section class="ft-client-prototype-section ft-client-shipping-aligned-section">
                <div class="ft-client-section-title ft-client-section-title-spread">
                    <div class="ft-section-title-left">
                        <span>3</span>
                        <div>
                            <h3>Shipping address</h3>
                            <p>Enter the delivery address for this client.</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="ft-client-shipping-saved-action"
                        wire:click="useSavedAddressForShipping"
                        title="Use the office or billing address already entered on this form"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4.75A1.75 1.75 0 017.75 3h8.5A1.75 1.75 0 0118 4.75V21l-6-3.8L6 21V4.75z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        <span>Use saved address</span>
                    </button>
                </div>

                <div class="ft-shipping-prototype-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shippingAddresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $shippingStates = $clientStatesByCountry[$address['country'] ?? ''] ?? [];
                        ?>
                        <?php if (isset($component)) { $__componentOriginal1c6d5f3db02afdc2f613169b7b93285a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c6d5f3db02afdc2f613169b7b93285a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.shipping-address-editor','data' => ['index' => $index,'address' => $address,'countries' => $clientCountries,'countryFlags' => $clientCountryFlags,'states' => $shippingStates,'canRemove' => count($shippingAddresses) > 1,'recipientRequired' => !$isEdit]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.shipping-address-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index),'address' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($address),'countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCountries),'country-flags' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCountryFlags),'states' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shippingStates),'can-remove' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($shippingAddresses) > 1),'recipient-required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$isEdit)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1c6d5f3db02afdc2f613169b7b93285a)): ?>
<?php $attributes = $__attributesOriginal1c6d5f3db02afdc2f613169b7b93285a; ?>
<?php unset($__attributesOriginal1c6d5f3db02afdc2f613169b7b93285a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c6d5f3db02afdc2f613169b7b93285a)): ?>
<?php $component = $__componentOriginal1c6d5f3db02afdc2f613169b7b93285a; ?>
<?php unset($__componentOriginal1c6d5f3db02afdc2f613169b7b93285a); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($shippingAddresses) < 20): ?>
                    <button type="button" class="ft-shipping-prototype-add" wire:click="addShippingAddress">
                        <span aria-hidden="true">+</span> Add another shipping address
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-client-prototype-section ft-client-shipping-aligned-section">
                <div class="ft-client-section-title ft-client-section-title-spread">
                    <div class="ft-section-title-left">
                        <span>4</span>
                        <div>
                            <h3>Billing address</h3>
                            <p>Enter the billing address for this client.</p>
                        </div>
                    </div>
                </div>

                <article
                    class="ft-shipping-prototype-address"
                    x-data="{ showLine2: <?php echo \Illuminate\Support\Js::from($billingLine2Visible)->toHtml() ?> }"
                >
                    <div class="ft-client-grid ft-shipping-aligned-grid">
                        <label class="ft-proto-field ft-shipping-recipient-field">
                            <b>Recipient name <em>*</em></b>
                            <input wire:model="billingRecipient" placeholder="Recipient name">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingRecipient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <div class="ft-proto-field ft-shipping-country-field">
                            <b>Country / region <em>*</em></b>
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Country / region','property' => 'billingCountry','value' => $billingCountry,'placeholder' => 'Select country / region','options' => $countryOptions,'searchPlaceholder' => 'Search country / region…','menuWidth' => 360,'fixedMenu' => true,'hideLabel' => true,'class' => 'ft-form-search-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Country / region','property' => 'billingCountry','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingCountry),'placeholder' => 'Select country / region','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($countryOptions),'search-placeholder' => 'Search country / region…','menu-width' => 360,'fixed-menu' => true,'hide-label' => true,'class' => 'ft-form-search-select']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingCountry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <label class="ft-proto-field ft-shipping-address-line1-field">
                            <b>Address line 1 <em>*</em></b>
                            <input wire:model="billingAddressLine1" placeholder="Street address">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingAddressLine1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <label class="ft-proto-field ft-shipping-address-line2-field" x-show="showLine2" x-cloak>
                            <b>Address line 2 <span>(Optional)</span></b>
                            <input wire:model="billingSuite" placeholder="Apartment, suite, unit, building, floor, etc.">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingSuite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <label class="ft-proto-field ft-shipping-city-field">
                            <b>City <em>*</em></b>
                            <input wire:model="billingCity" placeholder="City">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingCity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <div class="ft-proto-field ft-shipping-state-field">
                            <b>State <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($billingStates)): ?><em>*</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></b>
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'State','property' => 'billingState','value' => $billingState,'placeholder' => empty($billingStates) ? 'No states configured' : 'Select state','options' => $billingStates,'disabled' => empty($billingStates),'searchPlaceholder' => 'Search state…','menuWidth' => 340,'fixedMenu' => true,'hideLabel' => true,'class' => 'ft-form-search-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'State','property' => 'billingState','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingState),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(empty($billingStates) ? 'No states configured' : 'Select state'),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingStates),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(empty($billingStates)),'search-placeholder' => 'Search state…','menu-width' => 340,'fixed-menu' => true,'hide-label' => true,'class' => 'ft-form-search-select']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingState'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <label class="ft-proto-field ft-shipping-zip-field">
                            <b>ZIP / postal code <em>*</em></b>
                            <input wire:model="billingZip" placeholder="ZIP / postal code">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['billingZip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                    </div>

                    <div class="ft-shipping-prototype-line2" x-show="!showLine2" x-cloak>
                        <button type="button" x-on:click="showLine2 = true">
                            <span aria-hidden="true">+</span> Add address line 2
                        </button>
                    </div>
                </article>
            </section>

            <?php else: ?>
                <section class="ft-client-prototype-section ft-client-shipping-aligned-section" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-client-addresses-placeholder'; ?>wire:key="create-client-addresses-placeholder">
                    <div class="ft-client-section-title ft-client-section-title-spread">
                        <div class="ft-section-title-left">
                            <span>3</span>
                            <div>
                                <h3>Shipping &amp; billing addresses</h3>
                                <p>Country and state options load only when you approach the address section.</p>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'addresses','rows' => 5]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'addresses','rows' => 5]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
                <span>Required fields are marked with&nbsp; <em>*</em></span>
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