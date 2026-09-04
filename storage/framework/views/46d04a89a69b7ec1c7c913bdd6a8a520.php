<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'detail',
    'users',
    'editing' => false,
    'tab' => 'overview',
    'orders' => null,
    'documents' => null,
    'activities' => null,
    'orderStatusOptions' => null,
    'orderOwnerOptions' => null,
    'clientOrderSearch' => '',
    'clientOrderStatus' => '',
    'clientOrderOwner' => '',
    'clientOrderRange' => '3m',
    'documentCount' => 0,
    'orderMetrics' => [],
    'orderCount' => 0,
    'detailSectionsReady' => [],
    'clientCode' => '',
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
    'editShippingAddresses' => [],
    'contacts' => [],
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
    'detail',
    'users',
    'editing' => false,
    'tab' => 'overview',
    'orders' => null,
    'documents' => null,
    'activities' => null,
    'orderStatusOptions' => null,
    'orderOwnerOptions' => null,
    'clientOrderSearch' => '',
    'clientOrderStatus' => '',
    'clientOrderOwner' => '',
    'clientOrderRange' => '3m',
    'documentCount' => 0,
    'orderMetrics' => [],
    'orderCount' => 0,
    'detailSectionsReady' => [],
    'clientCode' => '',
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
    'editShippingAddresses' => [],
    'contacts' => [],
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
    $client = $detail['client'];
    $jobs = $detail['jobs'] ?? collect();
    $addressesReady = (bool) ($detailSectionsReady['addresses'] ?? false);
    $initials = collect(preg_split('/\s+/', trim((string) $client->name)))
        ->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'CL';
    $access = app(\App\Services\AccessControlService::class);
    $canEdit = $access->isAdministrator(auth()->user())
        || $access->canEditAll(auth()->user(), 'clients')
        || ($access->canEditOwn(auth()->user(), 'clients') && (int) $client->account_manager_id === (int) auth()->id());
    $canDelete = auth()->user()->canModule('clients', 'delete');
    $canCreateOrder = auth()->user()->canModule('jobs', 'create');

    $location = collect([$client->office_city, $client->office_state])->filter()->implode(', ');
    if ($location === '') $location = $client->country ?: '—';
    $clientSince = $client->created_at?->format('M Y') ?: '—';
    $currencyCode = $client->preferred_currency ?: 'USD';
    $currencyNames = ['USD'=>'US Dollar','CNY'=>'Chinese Yuan','BDT'=>'Bangladeshi Taka','EUR'=>'Euro','GBP'=>'British Pound','CAD'=>'Canadian Dollar','AUD'=>'Australian Dollar','AED'=>'UAE Dirham'];
    $currencyText = $currencyCode.(isset($currencyNames[$currencyCode]) ? ' · '.$currencyNames[$currencyCode] : '');
    $primaryInitials = collect(preg_split('/\s+/', trim((string) ($client->contact_name ?: 'Primary Contact'))))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p,0,1)))->implode('') ?: 'PC';
    $managerInitials = collect(preg_split('/\s+/', trim((string) ($client->accountManager?->name ?: 'Unassigned'))))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p,0,1)))->implode('') ?: 'AM';
    $contactCount = (int) ($client->contacts_count ?? ($client->relationLoaded('contacts') ? $client->contacts->count() : 0));
    if ($contactCount === 0 && (filled($client->contact_name) || filled($client->email) || filled($client->phone))) $contactCount = 1;

    $formatAddress = function (?string $line1, ?string $suite, ?string $city, ?string $state, ?string $zip, ?string $country): array {
        $first = collect([$line1, $suite])->filter(fn ($v) => filled($v))->implode(', ');
        $second = collect([$city, $state, $zip])->filter(fn ($v) => filled($v))->implode(', ');
        return array_values(array_filter([$first, $second, $country], fn ($v) => filled($v)));
    };

    $officeLines = $formatAddress($client->office_address_line1 ?: $client->office_address, $client->office_suite, $client->office_city, $client->office_state, $client->office_zip, $client->country);
    $billingLines = $formatAddress($client->billing_address_line1, $client->billing_suite, $client->billing_city, $client->billing_state, $client->billing_zip, $client->billing_country ?: $client->country);
    $shippingAddresses = $addressesReady && $client->relationLoaded('shippingAddresses') ? $client->shippingAddresses : collect();
    $addressCount = 1 + ($client->billing_same_as_office ? 0 : ($billingLines ? 1 : 0)) + $shippingAddresses->count();

    $statusLabel = function ($job) {
        if ($job->completed_at) return 'Completed';
        $status = trim((string) $job->status);
        if ($status === '' || in_array(mb_strtolower($status), ['new','active','open','in progress'], true)) {
            return $job->phase?->name ?: ($status ?: 'Not started');
        }
        return $status;
    };
    $statusClass = function (string $status): string {
        $s = mb_strtolower($status);
        if (str_contains($s, 'complete')) return 'is-green';
        if (str_contains($s, 'await') || str_contains($s, 'approval') || str_contains($s, 'attention')) return 'is-amber';
        if (str_contains($s, 'hold') || str_contains($s, 'block') || str_contains($s, 'cancel')) return 'is-red';
        if (str_contains($s, 'quote') || str_contains($s, 'sample')) return 'is-purple';
        if (str_contains($s, 'not started') || str_contains($s, 'new')) return 'is-gray';
        return 'is-blue';
    };
?>

<div class="ft-client-prototype-page" data-client-detail-tab="<?php echo e($tab); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash success"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-client-proto-breadcrumb">
        <button type="button" wire:click="backToClients">Clients</button>
        <span>/</span>
        <b><?php echo e($client->code ?: 'CL-'.str_pad((string) $client->id, 3, '0', STR_PAD_LEFT)); ?></b>
    </div>

    <header class="ft-client-proto-header">
        <div class="ft-client-proto-identity">
            <?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $client,'name' => $client->name,'size' => 60,'shape' => 'circle','class' => 'ft-client-proto-logo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client->name),'size' => 60,'shape' => 'circle','class' => 'ft-client-proto-logo']); ?>
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
            <div class="ft-client-proto-title-block">
                <div class="ft-client-proto-title-line">
                    <h1><?php echo e($client->name); ?></h1>
                    <span class="ft-client-status-pill <?php echo e($client->is_active ? 'is-active' : 'is-archived'); ?>"><?php echo e($client->is_active ? 'Active' : 'Archived'); ?></span>
                </div>
                <div class="ft-client-proto-subline">
                    <span><?php echo e($client->legal_business_name ?: $client->name); ?></span><i></i>
                    <span><?php echo e($location); ?></span><i></i>
                    <span>Client since <?php echo e($clientSince); ?></span>
                </div>
            </div>
        </div>
        <div class="ft-client-proto-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
                <button class="ft-client-proto-secondary" type="button" wire:click="editClient(<?php echo e($client->id); ?>)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                    Edit client
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateOrder): ?>
                <a class="ft-client-proto-primary" href="<?php echo e(route('jobs.index', ['create'=>1,'client'=>$client->id])); ?>" wire:navigate>
                    <span>+</span> Create order
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <details class="ft-client-proto-more">
                <summary aria-label="More client actions">⋮</summary>
                <div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)">Edit client</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?><button class="danger" type="button" wire:click="deleteClient(<?php echo e($client->id); ?>)" wire:confirm="Archive this client? Existing order history will remain available.">Archive client</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </details>
        </div>
    </header>

    <nav class="ft-client-proto-tabs" aria-label="Client detail sections">
        <button type="button" wire:click="setClientDetailTab('overview')" class="<?php echo e($tab === 'overview' ? 'active' : ''); ?>">Overview</button>
        <button type="button" wire:click="setClientDetailTab('contacts')" class="<?php echo e($tab === 'contacts' ? 'active' : ''); ?>">Contacts <span><?php echo e($contactCount); ?></span></button>
        <button type="button" wire:click="setClientDetailTab('orders')" class="<?php echo e($tab === 'orders' ? 'active' : ''); ?>">Orders <span><?php echo e($orderCount); ?></span></button>
        <button type="button" wire:click="setClientDetailTab('documents')" class="<?php echo e($tab === 'documents' ? 'active' : ''); ?>">Documents <span><?php echo e($documentCount); ?></span></button>
        <button type="button" wire:click="setClientDetailTab('activity')" class="<?php echo e($tab === 'activity' ? 'active' : ''); ?>">Activity</button>
    </nav>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editing): ?>
        <?php if (isset($component)) { $__componentOriginalfa2d9a437a932329040def908f7b2e55 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa2d9a437a932329040def908f7b2e55 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clients.create','data' => ['mode' => 'edit','users' => $users,'clientCode' => $clientCode,'clientName' => $client->name ?? '','clientCountries' => $clientCountries,'clientCountryFlags' => $clientCountryFlags,'clientStatesByCountry' => $clientStatesByCountry,'clientLanguages' => $clientLanguages,'clientCurrencies' => $clientCurrencies,'paymentTermOptions' => $paymentTermOptions,'accountManagerId' => $accountManagerId,'preferredCurrency' => $preferredCurrency,'clientCountry' => $clientCountry,'officeState' => $officeState,'billingState' => $billingState,'billingCountry' => $billingCountry,'billingRecipient' => $billingRecipient,'billingAddressLine1' => $billingAddressLine1,'billingSuite' => $billingSuite,'billingCity' => $billingCity,'billingZip' => $billingZip,'billingSameAsOffice' => $billingSameAsOffice,'salesTaxStatus' => $salesTaxStatus,'shippingAddresses' => $editShippingAddresses,'contacts' => $contacts,'clientLogoUpload' => $clientLogoUpload,'existingClientLogoUrl' => $existingClientLogoUrl,'removeClientLogo' => $removeClientLogo]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clients.create'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => 'edit','users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'client-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCode),'client-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client->name ?? ''),'client-countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCountries),'client-country-flags' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCountryFlags),'client-states-by-country' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientStatesByCountry),'client-languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientLanguages),'client-currencies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCurrencies),'payment-term-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentTermOptions),'account-manager-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($accountManagerId),'preferred-currency' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferredCurrency),'client-country' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCountry),'office-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($officeState),'billing-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingState),'billing-country' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingCountry),'billing-recipient' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingRecipient),'billing-address-line1' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingAddressLine1),'billing-suite' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingSuite),'billing-city' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingCity),'billing-zip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingZip),'billing-same-as-office' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($billingSameAsOffice),'sales-tax-status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($salesTaxStatus),'shipping-addresses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editShippingAddresses),'contacts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contacts),'client-logo-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientLogoUpload),'existing-client-logo-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($existingClientLogoUrl),'remove-client-logo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($removeClientLogo)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa2d9a437a932329040def908f7b2e55)): ?>
<?php $attributes = $__attributesOriginalfa2d9a437a932329040def908f7b2e55; ?>
<?php unset($__attributesOriginalfa2d9a437a932329040def908f7b2e55); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa2d9a437a932329040def908f7b2e55)): ?>
<?php $component = $__componentOriginalfa2d9a437a932329040def908f7b2e55; ?>
<?php unset($__componentOriginalfa2d9a437a932329040def908f7b2e55); ?>
<?php endif; ?>
    <?php elseif($tab === 'overview'): ?>
        <div class="ft-client-summary-grid">
            <article class="ft-client-summary-card">
                <span class="ft-client-summary-icon is-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg></span>
                <div><small>Primary contact</small><b><?php echo e($client->contact_name ?: 'Not set'); ?></b><span><?php echo e($client->email ?: 'No email recorded'); ?></span></div>
            </article>
            <article class="ft-client-summary-card">
                <span class="ft-client-summary-icon is-purple"><?php echo e($managerInitials); ?></span>
                <div><small>Account manager</small><b><?php echo e($client->accountManager?->name ?? 'Unassigned'); ?></b></div>
            </article>
            <article class="ft-client-summary-card">
                <span class="ft-client-summary-icon is-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                <div><small>Office location</small><b><?php echo e($location); ?></b><span><?php echo e($client->country ?: '—'); ?></span></div>
            </article>
            <article class="ft-client-summary-card">
                <span class="ft-client-summary-icon is-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg></span>
                <div><small>Client since</small><b><?php echo e($clientSince); ?></b><span class="ft-client-active-dot"><i></i><?php echo e($client->is_active ? 'Active' : 'Archived'); ?></span></div>
            </article>
        </div>

        <div class="ft-client-overview-main-grid is-single">
            <section class="ft-client-proto-card ft-client-info-card">
                <div class="ft-client-card-head"><h2>Client information</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg> Edit details</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <dl class="ft-client-info-list">
                    <div><dt>Client code</dt><dd><?php echo e($client->code ?: 'CL-'.str_pad((string)$client->id,3,'0',STR_PAD_LEFT)); ?></dd></div>
                    <div><dt>Legal business name</dt><dd><?php echo e($client->legal_business_name ?: '—'); ?></dd></div>
                    <div><dt>Website</dt><dd><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($client->website): ?><a href="<?php echo e(str_starts_with($client->website, 'http') ? $client->website : 'https://'.$client->website); ?>" target="_blank" rel="noopener"><?php echo e($client->website); ?></a><?php else: ?>—<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></dd></div>
                    <div><dt>Preferred language</dt><dd><?php echo e($client->preferred_language ?: 'English'); ?></dd></div>
                    <div><dt>Preferred currency</dt><dd><?php echo e($currencyText); ?></dd></div>
                    <div><dt>Country</dt><dd><?php echo e($client->country ?: '—'); ?></dd></div>
                    <div><dt>Created by</dt><dd>—</dd></div>
                    <div><dt>Last updated</dt><dd><?php echo e($client->updated_at?->format('M j, Y · g:i A') ?: '—'); ?></dd></div>
                </dl>
            </section>

        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($addressesReady): ?>
        <section class="ft-client-proto-card ft-client-addresses-card">
            <div class="ft-client-card-head ft-client-address-card-head">
                <div><h2>Addresses</h2><p>Office, billing and delivery locations.</p></div>
                <div><span><?php echo e($addressCount); ?> <?php echo e(\Illuminate\Support\Str::plural('address', $addressCount)); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)">Manage addresses</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            </div>
            <div class="ft-client-address-grid">
                <article class="ft-client-address-item">
                    <span class="ft-client-address-icon is-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 2v4M15 2v4M8 10h8M8 14h8"/></svg></span>
                    <div><small><?php echo e($client->billing_same_as_office ? 'Office & billing' : 'Office'); ?></small><b><?php echo e($client->office_city ? $client->office_city.' Office' : 'Office address'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $officeLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($line); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><span>Address not set</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><div class="ft-client-address-tags"><em>Office</em><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($client->billing_same_as_office): ?><em class="purple">Billing</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
                    <span class="ft-client-address-more">⋮</span>
                </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$client->billing_same_as_office && $billingLines): ?>
                    <article class="ft-client-address-item">
                        <span class="ft-client-address-icon is-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 2v4M15 2v4M8 10h8M8 14h8"/></svg></span>
                        <div><small>Billing</small><b>Billing address</b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $billingLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($line); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ft-client-address-tags"><em class="purple">Billing</em></div></div>
                        <span class="ft-client-address-more">⋮</span>
                    </article>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shippingAddresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $shippingLines = $formatAddress($address->address_line1, $address->suite, $address->city, $address->state, $address->zip, $address->country); ?>
                    <article class="ft-client-address-item">
                        <span class="ft-client-address-icon <?php echo e($address->is_default ? 'is-green' : 'is-purple'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address->is_default): ?><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><?php else: ?><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></svg></span>
                        <div><small><?php echo e($address->is_default ? 'Default shipping' : 'Shipping'); ?></small><b><?php echo e($address->label); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shippingLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($line); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ft-client-address-tags"><em class="<?php echo e($address->is_default ? 'green' : 'purple'); ?>"><?php echo e($address->is_default ? 'Default shipping' : 'Shipping'); ?></em></div></div>
                        <span class="ft-client-address-more">⋮</span>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'addresses','method' => 'loadClientDetailSection','keyPrefix' => 'client-detail','rows' => 4,'message' => 'Loading client addresses when needed…','rootMargin' => '340px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'addresses','method' => 'loadClientDetailSection','key-prefix' => 'client-detail','rows' => 4,'message' => 'Loading client addresses when needed…','root-margin' => '340px 0px']); ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-client-overview-bottom-grid">
            <section class="ft-client-proto-card ft-client-commercial-card">
                <div class="ft-client-card-head"><h2>Commercial settings</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)" aria-label="Edit commercial settings">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <div class="ft-client-commercial-grid">
                    <dl><div><dt>Payment terms</dt><dd><?php echo e($client->payment_terms ?: '—'); ?></dd></div><div><dt>Sales tax status</dt><dd><?php echo e($client->sales_tax_status === 'tax_exempt' ? 'Tax exempt' : 'Taxable'); ?></dd></div><div><dt>EIN / Tax ID</dt><dd><?php echo e($client->ein_tax_id ?: '—'); ?></dd></div></dl>
                    <dl><div><dt>PO required</dt><dd><?php echo e($client->po_required ? 'Yes' : 'No'); ?></dd></div><div><dt>Credit status</dt><dd class="ft-credit-good"><i></i><?php echo e((float)$client->outstanding_balance > 0 ? 'Balance outstanding' : 'In good standing'); ?></dd></div><div><dt>Currency</dt><dd><?php echo e($currencyCode); ?></dd></div></dl>
                </div>
                <p class="ft-client-commercial-note"><span>i</span> Outstanding balance is calculated from transactions and shown in reporting.</p>
            </section>
            <section class="ft-client-proto-card ft-client-notes-card">
                <div class="ft-client-card-head"><h2>Internal notes</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)" aria-label="Edit notes">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <p><?php echo e($client->notes ?: 'No internal notes have been added for this client.'); ?></p>
                <small>Last edited <?php echo e($client->updated_at?->format('M j, Y') ?: '—'); ?></small>
                <div class="ft-client-info-banner"><span>i</span> Orders, documents and activity are available in their respective tabs.</div>
            </section>
        </div>
    <?php elseif($tab === 'contacts'): ?>
        <section class="ft-client-proto-card ft-client-contacts-tab-card">
            <div class="ft-client-card-head ft-client-contacts-tab-head">
                <div>
                    <h2>Client contacts</h2>
                    <p>People available for inquiries, orders and documents for <?php echo e($client->name); ?>.</p>
                </div>
                <div class="ft-client-contacts-tab-actions">
                    <span><?php echo e($contactCount); ?> <?php echo e(\Illuminate\Support\Str::plural('contact', $contactCount)); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button class="outlined" type="button" wire:click="editClient(<?php echo e($client->id); ?>)">＋ Manage contacts</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="ft-client-contact-directory">
                <div class="ft-client-contact-directory-head" aria-hidden="true">
                    <span>Contact</span><span>Job title</span><span>Email</span><span>Phone</span><span>Action</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $client->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contactIndex => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $contactInitials = collect(preg_split('/\s+/', trim((string) $contact->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'C';
                    ?>
                    <article class="ft-client-contact-directory-row <?php echo e($contactIndex === 0 ? 'is-primary' : ''); ?>">
                        <div class="ft-client-contact-person" data-label="Contact">
                            <span class="ft-client-contact-avatar <?php echo e($contactIndex % 2 === 0 ? 'is-blue' : 'is-purple'); ?>"><?php echo e($contactInitials); ?></span>
                            <div><b><?php echo e($contact->name); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contactIndex === 0): ?><em>Primary</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        </div>
                        <div class="ft-client-contact-field" data-label="Job title"><span><?php echo e($contact->job_title ?: '—'); ?></span></div>
                        <div class="ft-client-contact-field" data-label="Email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->email): ?><a href="mailto:<?php echo e($contact->email); ?>"><?php echo e($contact->email); ?></a><?php else: ?><span>—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="ft-client-contact-field" data-label="Phone"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->phone): ?><a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $contact->phone)); ?>"><?php echo e($contact->phone); ?></a><?php else: ?><span>—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="ft-client-contact-row-action" data-label="Action">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)" aria-label="Edit <?php echo e($contact->name); ?>">Edit</button><?php else: ?><span>—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($client->contact_name) || filled($client->email) || filled($client->phone)): ?>
                        <article class="ft-client-contact-directory-row is-primary">
                            <div class="ft-client-contact-person" data-label="Contact"><span class="ft-client-contact-avatar is-blue"><?php echo e($primaryInitials); ?></span><div><b><?php echo e($client->contact_name ?: 'Primary contact'); ?></b><em>Primary</em></div></div>
                            <div class="ft-client-contact-field" data-label="Job title"><span><?php echo e($client->contact_job_title ?: '—'); ?></span></div>
                            <div class="ft-client-contact-field" data-label="Email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($client->email): ?><a href="mailto:<?php echo e($client->email); ?>"><?php echo e($client->email); ?></a><?php else: ?><span>—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <div class="ft-client-contact-field" data-label="Phone"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($client->phone): ?><a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $client->phone)); ?>"><?php echo e($client->phone); ?></a><?php else: ?><span>—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <div class="ft-client-contact-row-action" data-label="Action"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)">Edit</button><?php else: ?><span>—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        </article>
                    <?php else: ?>
                        <div class="ft-client-contacts-empty">
                            <span class="ft-client-contacts-empty-icon">+</span>
                            <div><b>No contacts added yet</b><p>Add a contact so the team can select the right person on inquiries and orders.</p></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?><button type="button" wire:click="editClient(<?php echo e($client->id); ?>)">Add contact</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php elseif($tab === 'orders'): ?>
        <div class="ft-client-order-metrics">
            <article><span class="is-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg></span><div><small>Open orders</small><b><?php echo e(number_format($orderMetrics['open'] ?? 0)); ?></b><em>Across active phases</em></div></article>
            <article><span class="is-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2h12M6 22h12M7 2c0 5 2 6 5 10-3 4-5 5-5 10M17 2c0 5-2 6-5 10 3 4 5 5 5 10"/></svg></span><div><small>Awaiting action</small><b><?php echo e(number_format($orderMetrics['attention'] ?? 0)); ?></b><em>Needs your response</em></div></article>
            <article><span class="is-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></span><div><small>Completed orders</small><b><?php echo e(number_format($orderMetrics['completed'] ?? 0)); ?></b><em>All time</em></div></article>
            <article><span class="is-purple">$</span><div><small>Total order value</small><b>$<?php echo e(number_format($orderMetrics['value'] ?? 0, 0)); ?></b><em>All time · <?php echo e($currencyCode); ?></em></div></article>
        </div>

        <section class="ft-client-proto-card ft-client-orders-card">
            <div class="ft-client-orders-head">
                <div><h2>Client orders</h2><p>All orders for <?php echo e($client->name); ?>.</p></div>
                <div><a href="<?php echo e(route('jobs.index', ['client'=>$client->id])); ?>" wire:navigate><span>↗</span> Open in Orders</a><small>Opens the Orders workspace with<br><?php echo e($client->name); ?> filter applied.</small></div>
            </div>
            <?php if (isset($component)) { $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-bar','data' => ['class' => 'ft-client-order-filters','label' => 'Client order filters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-client-order-filters','label' => 'Client order filters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginalf6ee3670073e124e2f361de392ee6597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf6ee3670073e124e2f361de392ee6597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-input','data' => ['class' => 'ft-client-order-search ft-client-order-search-shared','property' => 'clientOrderSearch','value' => $clientOrderSearch,'label' => 'Search client orders','placeholder' => 'Search order number or description...','debounce' => 300,'hideLabel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-client-order-search ft-client-order-search-shared','property' => 'clientOrderSearch','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientOrderSearch),'label' => 'Search client orders','placeholder' => 'Search order number or description...','debounce' => 300,'hide-label' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf6ee3670073e124e2f361de392ee6597)): ?>
<?php $attributes = $__attributesOriginalf6ee3670073e124e2f361de392ee6597; ?>
<?php unset($__attributesOriginalf6ee3670073e124e2f361de392ee6597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf6ee3670073e124e2f361de392ee6597)): ?>
<?php $component = $__componentOriginalf6ee3670073e124e2f361de392ee6597; ?>
<?php unset($__componentOriginalf6ee3670073e124e2f361de392ee6597); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-client-order-selector','label' => 'Status','property' => 'clientOrderStatus','value' => $clientOrderStatus,'placeholder' => 'All statuses','options' => collect($orderStatusOptions ?? [])->map(fn ($status) => ['id' => $status, 'label' => $status]),'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 220]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-client-order-selector','label' => 'Status','property' => 'clientOrderStatus','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientOrderStatus),'placeholder' => 'All statuses','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($orderStatusOptions ?? [])->map(fn ($status) => ['id' => $status, 'label' => $status])),'hide-label' => true,'fixed-menu' => true,'menu-width' => 220]); ?>
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
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-client-order-selector','label' => 'Owner','property' => 'clientOrderOwner','type' => 'users','context' => 'client-orders','value' => $clientOrderOwner,'placeholder' => 'All owners','initialOptions' => $orderOwnerOptions ?? collect(),'params' => ['client_id' => $client->id],'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 260]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-client-order-selector','label' => 'Owner','property' => 'clientOrderOwner','type' => 'users','context' => 'client-orders','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientOrderOwner),'placeholder' => 'All owners','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderOwnerOptions ?? collect()),'params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['client_id' => $client->id]),'hide-label' => true,'fixed-menu' => true,'menu-width' => 260]); ?>
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
                <select class="ft-client-order-range" wire:model.live="clientOrderRange" aria-label="Client order date range"><option value="3m">Last 3 months</option><option value="6m">Last 6 months</option><option value="12m">Last 12 months</option><option value="all">All time</option></select>
                <?php if (isset($component)) { $__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-reset','data' => ['action' => 'clearClientOrderFilters','label' => 'Clear filters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-reset'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'clearClientOrderFilters','label' => 'Clear filters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266)): ?>
<?php $attributes = $__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266; ?>
<?php unset($__attributesOriginal6f21a7d61664ddbb53ab0f97f87e5266); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266)): ?>
<?php $component = $__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266; ?>
<?php unset($__componentOriginal6f21a7d61664ddbb53ab0f97f87e5266); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed)): ?>
<?php $attributes = $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed; ?>
<?php unset($__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed)): ?>
<?php $component = $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed; ?>
<?php unset($__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed); ?>
<?php endif; ?>
            <div class="ft-client-order-filter-meta"><span>Client: <?php echo e($client->name); ?> <b>×</b></span><em><?php echo e(number_format($orders?->total() ?? 0)); ?> matching orders</em></div>
            <div class="ft-client-orders-table-wrap">
                <table class="ft-client-orders-table">
                    <thead><tr><th><span class="ft-fake-checkbox"></span></th><th>ORDER</th><th>CREATED</th><th>DESCRIPTION</th><th>STATUS</th><th>OWNER</th><th>DUE DATE</th><th>VALUE</th><th>UPDATED</th><th>ACTION</th></tr></thead>
                    <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $label = $statusLabel($job);
                            $jobStatus = mb_strtolower(trim((string) $job->status));
                            $showPhaseStatus = !$job->completed_at
                                && $job->phase
                                && ($jobStatus === '' || in_array($jobStatus, ['new', 'active', 'open', 'in progress'], true));
                            $ownerName = $job->owner?->name ?: 'Unassigned';
                            $overdue = $job->delivery_date && !$job->completed_at && $job->delivery_date->isPast();
                        ?>
                        <tr>
                            <td><span class="ft-fake-checkbox"></span></td>
                            <td><a href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a></td>
                            <td><?php echo e($job->created_at?->format('M j, Y')); ?></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($job->title ?: $job->product ?: '—', 42)); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPhaseStatus): ?>
                                    <?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $job->phase,'short' => true,'class' => 'ft-client-order-status']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->phase),'short' => true,'class' => 'ft-client-order-status']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $attributes = $__attributesOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__attributesOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $component = $__componentOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__componentOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <span class="ft-client-order-status <?php echo e($statusClass($label)); ?>"><?php echo e($label); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><div class="ft-client-order-owner"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $job->owner,'name' => $ownerName,'size' => 23]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerName),'size' => 23]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><span><?php echo e($ownerName); ?></span></div></td>
                            <td class="<?php echo e($overdue ? 'is-overdue' : ''); ?>"><?php echo e($job->delivery_date?->format('M j, Y') ?? '—'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overdue): ?><span>△</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                            <td><?php echo e($job->commercial_value > 0 ? '$'.number_format((float)$job->commercial_value, 0) : '—'); ?></td>
                            <td><?php echo e($job->updated_at?->diffForHumans(['short'=>true]) ?? '—'); ?></td>
                            <td><a class="ft-client-order-view" href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate>View</a></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="10" class="ft-client-table-empty">No matching orders found for this client.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders): ?>
                <div class="ft-client-orders-pagination">
                    <div>Showing <?php echo e($orders->firstItem() ?? 0); ?>–<?php echo e($orders->lastItem() ?? 0); ?> of <?php echo e($orders->total()); ?> orders <select wire:model.live="clientOrderPerPage"><option value="8">8 per page</option><option value="16">16 per page</option><option value="24">24 per page</option></select></div>
                    <div class="ft-client-page-buttons">
                        <button type="button" wire:click="previousPage('clientOrdersPage')" <?php if($orders->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                        <?php
                            $pageStart = max(1, min($orders->currentPage() - 1, max(1, $orders->lastPage() - 2)));
                            $pageEnd = min($orders->lastPage(), $pageStart + 2);
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($page = $pageStart; $page <= $pageEnd; $page++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><button type="button" wire:click="gotoPage(<?php echo e($page); ?>, 'clientOrdersPage')" class="<?php echo e($orders->currentPage() === $page ? 'active' : ''); ?>"><?php echo e($page); ?></button><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <button type="button" wire:click="nextPage('clientOrdersPage')" <?php if(!$orders->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
        <div class="ft-client-info-banner ft-client-order-info"><span>i</span> Order totals and statuses update automatically as tasks progress.</div>
    <?php elseif($tab === 'documents'): ?>
        <section class="ft-client-proto-card ft-client-generic-tab-card">
            <div class="ft-client-card-head"><div><h2>Client documents</h2><p>Documents linked to <?php echo e($client->name); ?>.</p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('document_archive', 'view')): ?><a href="<?php echo e(route('documents.index', ['client'=>$client->id])); ?>" wire:navigate>Open Document Archive</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            <div class="ft-client-generic-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $documents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div><span class="ft-client-doc-icon">▤</span><div><b><?php echo e($document->name); ?></b><small><?php echo e($document->category ?: 'Document'); ?> · <?php echo e($document->document_number); ?></small></div><span><?php echo e($document->updated_at?->format('M j, Y')); ?></span></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ft-client-generic-empty">No documents are linked to this client yet.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php elseif($tab === 'activity'): ?>
        <section class="ft-client-proto-card ft-client-generic-tab-card">
            <div class="ft-client-card-head"><div><h2>Client activity</h2><p>Recent activity from this client’s orders.</p></div></div>
            <div class="ft-client-activity-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $activity->user,'name' => $activity->user?->name ?: 'System','size' => 30]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->user),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->user?->name ?: 'System'),'size' => 30]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><div><b><?php echo e($activity->user?->name ?: 'System'); ?></b><p><?php echo e($activity->description); ?></p><small><?php echo e($activity->created_at?->diffForHumans()); ?></small></div></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ft-client-generic-empty">No activity is available for this client yet.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/clients/detail.blade.php ENDPATH**/ ?>