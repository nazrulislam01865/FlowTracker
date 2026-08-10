<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\Client;
use App\Models\ClientShippingAddress;
use App\Models\Activity;
use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\User;
use App\Services\ClientService;
use App\Services\DocumentService;
use App\Services\JobService;
use App\Services\MasterDataService;
use App\Services\SetupContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use UsesPagePlaceholder;
    use WithPagination;

    public string $search = '';
    public string $country = '';
    public string $manager = '';
    public string $jobHealth = '';
    public string $outstanding = '';
    public string $quick = 'all';
    public bool $showArchived = false;
    public int $perPage = 10;
    public ?int $selectedClientId = null;
    public bool $showClientPreview = false;
    public bool $showCreate = false;
    public bool $showDetail = false;
    public bool $showEdit = false;
    public ?int $actionMenuClientId = null;
    public string $clientDetailTab = 'overview';
    public string $clientOrderSearch = '';
    public string $clientOrderStatus = '';
    public string $clientOrderOwner = '';
    public string $clientOrderRange = '12m';
    public int $clientOrderPerPage = 8;

    public string $clientCode = '';
    public string $clientName = '';
    public string $legalBusinessName = '';
    public string $website = '';
    public string $clientCountry = 'United States';
    public string $preferredCurrency = 'USD';
    public string $officeAddress = '';
    public string $officeAddressLine1 = '';
    public string $officeSuite = '';
    public string $officeCity = '';
    public string $officeState = '';
    public string $officeZip = '';
    public bool $billingSameAsOffice = true;
    public string $billingAddressLine1 = '';
    public string $billingSuite = '';
    public string $billingCity = '';
    public string $billingState = '';
    public string $billingZip = '';
    public string $billingCountry = 'United States';
    public string $contactName = '';
    public string $contactJobTitle = '';
    public string $email = '';
    public string $phone = '';
    public ?int $accountManagerId = null;
    public string $preferredLanguage = 'English';
    public string $outstandingBalance = '0';
    public string $einTaxId = '';
    public string $salesTaxStatus = 'taxable';
    public string $paymentTerms = '';
    public bool $poRequired = false;
    public string $notes = '';
    public array $shippingAddresses = [];

    public function mount(): void
    {
        $this->showCreate = request()->boolean('create');
        if ($this->showCreate) $this->resetCreateForm();
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCountry(): void { $this->resetPage(); }
    public function updatedManager(): void { $this->resetPage(); }
    public function updatedJobHealth(): void { $this->resetPage(); }
    public function updatedOutstanding(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }
    public function updatedClientOrderSearch(): void { $this->resetPage('clientOrdersPage'); }
    public function updatedClientOrderStatus(): void { $this->resetPage('clientOrdersPage'); }
    public function updatedClientOrderOwner(): void { $this->resetPage('clientOrdersPage'); }
    public function updatedClientOrderRange(): void { $this->resetPage('clientOrdersPage'); }
    public function updatedClientOrderPerPage(): void { $this->resetPage('clientOrdersPage'); }

    public function setClientDetailTab(string $tab): void
    {
        abort_unless(in_array($tab, ['overview','orders','documents','activity'], true), 422);
        $this->clientDetailTab = $tab;
    }

    public function clearClientOrderFilters(): void
    {
        $this->clientOrderSearch = '';
        $this->clientOrderStatus = '';
        $this->clientOrderOwner = '';
        $this->clientOrderRange = '12m';
        $this->resetPage('clientOrdersPage');
    }

    public function updatedClientCountry(string $country): void
    {
        $this->officeState = '';
        if ($this->billingSameAsOffice) {
            $this->billingCountry = $country;
            $this->billingState = '';
        }
    }

    public function updatedBillingCountry(): void
    {
        $this->billingState = '';
    }

    public function updatedShippingAddresses(mixed $value, string $key): void
    {
        if (!str_ends_with($key, '.country')) return;
        $index = (int) explode('.', $key, 2)[0];
        if (isset($this->shippingAddresses[$index])) $this->shippingAddresses[$index]['state'] = '';
    }

    public function setQuick(string $quick): void
    {
        abort_unless(in_array($quick, ['all','active_jobs','attention','outstanding'], true), 422);
        $this->quick = $quick;
        $this->resetPage();
    }

    public function showActiveClients(): void
    {
        $this->showArchived = false;
        $this->quick = 'all';
        $this->actionMenuClientId = null;
        $this->resetPage();
    }

    public function showArchivedClients(): void
    {
        $this->showArchived = true;
        $this->quick = 'all';
        $this->actionMenuClientId = null;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search','country','manager','jobHealth','outstanding']);
        $this->quick = 'all';
        $this->resetPage();
    }

    public function clearFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['search','country','manager','jobHealth','outstanding'], true), 422);
        $this->{$filter} = '';
        $this->resetPage();
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->canModule('clients','create'), 403);
        $this->showCreate = true;
        $this->resetCreateForm();
    }

    public function closeCreate(): void
    {
        $this->showCreate = false;
        $this->resetValidation();
    }

    public function createClient(): void
    {
        $this->persistNewClient(false);
    }

    public function saveClientDraft(): void
    {
        $this->persistNewClient(true);
    }

    private function clientProfileRules(bool $draft, bool $requireShipping, bool $strictMasterData = true): array
    {
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $countryExists = fn () => Rule::exists('master_records', 'name')->where(fn ($query) => $query
            ->where('workspace_id', $workspaceId)
            ->where('type', 'country')
            ->where('status', 'active')
            ->whereNull('deleted_at'));
        $currencyExists = Rule::exists('master_records', 'code')->where(fn ($query) => $query
            ->where('workspace_id', $workspaceId)
            ->where('type', 'currency')
            ->where('status', 'active')
            ->whereNull('deleted_at'));
        $stateExistsForCountry = function (string $country) use ($workspaceId) {
            $countryId = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('country')
                ->active()
                ->where('name', $country)
                ->value('id');

            return Rule::exists('master_records', 'name')->where(fn ($query) => $query
                ->where('workspace_id', $workspaceId)
                ->where('type', 'state')
                ->where('status', 'active')
                ->where('parent_id', $countryId ?: 0)
                ->whereNull('deleted_at'));
        };
        $hasStatesForCountry = function (string $country) use ($workspaceId): bool {
            $countryId = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('country')
                ->active()
                ->where('name', $country)
                ->value('id');

            return $countryId && MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('state')
                ->active()
                ->where('parent_id', $countryId)
                ->exists();
        };

        $countryRule = fn () => $strictMasterData ? [$countryExists()] : [];
        $currencyRule = $strictMasterData ? [$currencyExists] : [];
        $stateRule = fn (string $country) => $strictMasterData ? [$stateExistsForCountry($country)] : [];

        $rules = [
            'clientName' => ['required','string','max:255'],
            'legalBusinessName' => ['nullable','string','max:255'],
            'website' => ['nullable','string','max:255'],
            // Creation is strict against Master Data. Editing intentionally allows
            // legacy values that may pre-date the current Country/Currency master
            // records; otherwise a perfectly valid existing client can never be
            // saved after the profile form was expanded.
            'clientCountry' => array_merge(['required','string','max:120'], $countryRule()),
            'preferredCurrency' => array_merge(['required','string','max:40'], $currencyRule),
            'contactName' => [$draft ? 'nullable' : 'required','string','max:255'],
            'contactJobTitle' => ['nullable','string','max:255'],
            'email' => [$draft ? 'nullable' : 'required','email','max:255'],
            'phone' => ['nullable','string','max:60'],
            'accountManagerId' => [$draft ? 'nullable' : 'required','nullable','exists:users,id'],
            'preferredLanguage' => ['nullable','string','max:50'],
            'officeAddressLine1' => [$draft ? 'nullable' : 'required','string','max:255'],
            'officeSuite' => ['nullable','string','max:120'],
            'officeCity' => [$draft ? 'nullable' : 'required','string','max:120'],
            'officeState' => array_merge(
                [(!$draft && $hasStatesForCountry($this->clientCountry)) ? 'required' : 'nullable','string','max:120'],
                $stateRule($this->clientCountry)
            ),
            'officeZip' => [$draft ? 'nullable' : 'required','string','max:30'],
            'billingAddressLine1' => ['nullable','string','max:255'],
            'billingSuite' => ['nullable','string','max:120'],
            'billingCity' => ['nullable','string','max:120'],
            // Hidden billing fields must not block saving when "same as office"
            // is enabled. They may contain old values from an earlier profile.
            'billingState' => $this->billingSameAsOffice
                ? ['nullable','string','max:120']
                : array_merge(['nullable','string','max:120'], $stateRule($this->billingCountry)),
            'billingZip' => ['nullable','string','max:30'],
            'billingCountry' => $this->billingSameAsOffice
                ? ['nullable','string','max:120']
                : array_merge(['nullable','string','max:120'], $countryRule()),
            'einTaxId' => ['nullable','string','max:80'],
            'salesTaxStatus' => ['required','in:taxable,tax_exempt'],
            'paymentTerms' => ['nullable','string','max:60'],
            'poRequired' => ['boolean'],
            'notes' => ['nullable','string','max:5000'],
            'shippingAddresses' => $requireShipping ? ['required','array','min:1','max:20'] : ['array','max:20'],
            'shippingAddresses.*.label' => ['nullable','string','max:255'],
            'shippingAddresses.*.recipient' => ['nullable','string','max:255'],
            'shippingAddresses.*.address_line1' => ['nullable','string','max:255'],
            'shippingAddresses.*.suite' => ['nullable','string','max:120'],
            'shippingAddresses.*.city' => ['nullable','string','max:120'],
            'shippingAddresses.*.state' => ['nullable','string','max:120'],
            'shippingAddresses.*.zip' => ['nullable','string','max:30'],
            'shippingAddresses.*.country' => array_merge(['nullable','string','max:120'], $countryRule()),
            'shippingAddresses.*.is_default' => ['boolean'],
        ];

        if (!$draft) {
            foreach ($this->shippingAddresses as $index => $address) {
                $hasContent = collect(['label','recipient','address_line1','suite','city','state','zip'])
                    ->contains(fn (string $field) => trim((string) ($address[$field] ?? '')) !== '');
                if (!$requireShipping && !$hasContent) continue;

                foreach (['label','address_line1','city','zip'] as $field) {
                    $rules["shippingAddresses.{$index}.{$field}"] = ['required','string','max:255'];
                }
                $shippingCountry = (string) ($address['country'] ?? '');
                $rules["shippingAddresses.{$index}.country"] = array_merge(['required','string','max:120'], $countryRule());
                $rules["shippingAddresses.{$index}.state"] = array_merge(
                    [$hasStatesForCountry($shippingCountry) ? 'required' : 'nullable','string','max:120'],
                    $stateRule($shippingCountry)
                );
            }
            if (!$this->billingSameAsOffice) {
                foreach (['billingAddressLine1','billingCity','billingZip'] as $field) {
                    $rules[$field] = ['required','string','max:255'];
                }
                $rules['billingCountry'] = array_merge(['required','string','max:120'], $countryRule());
                $rules['billingState'] = array_merge(
                    [$hasStatesForCountry($this->billingCountry) ? 'required' : 'nullable','string','max:120'],
                    $stateRule($this->billingCountry)
                );
            }
        }

        return $rules;
    }

    private function persistNewClient(bool $draft): void
    {
        abort_unless(auth()->user()->canModule('clients','create'), 403);

        $data = $this->validate($this->clientProfileRules($draft, !$draft));

        if ($data['accountManagerId'] && (int) $data['accountManagerId'] !== (int) auth()->id()) {
            abort_unless(auth()->user()->canModule('clients','assign') || auth()->user()->canModule('clients','edit_all'), 403);
        }

        $client = DB::transaction(function () use ($data, $draft) {
            $officeAddress = $this->formatAddress(
                $data['officeAddressLine1'] ?? '', $data['officeSuite'] ?? '', $data['officeCity'] ?? '',
                $data['officeState'] ?? '', $data['officeZip'] ?? '', $data['clientCountry'] ?? ''
            );
            $billing = $this->billingSameAsOffice ? [
                'line1' => $data['officeAddressLine1'] ?? '', 'suite' => $data['officeSuite'] ?? '', 'city' => $data['officeCity'] ?? '',
                'state' => $data['officeState'] ?? '', 'zip' => $data['officeZip'] ?? '', 'country' => $data['clientCountry'] ?? '',
            ] : [
                'line1' => $data['billingAddressLine1'] ?? '', 'suite' => $data['billingSuite'] ?? '', 'city' => $data['billingCity'] ?? '',
                'state' => $data['billingState'] ?? '', 'zip' => $data['billingZip'] ?? '', 'country' => $data['billingCountry'] ?? '',
            ];

            $client = Client::create([
                'code' => $this->nextClientCode(), 'name' => $data['clientName'], 'legal_business_name' => $data['legalBusinessName'] ?: null,
                'website' => $data['website'] ?: null, 'country' => $data['clientCountry'] ?: null, 'preferred_currency' => strtoupper($data['preferredCurrency']),
                'office_address' => $officeAddress ?: null, 'office_address_line1' => $data['officeAddressLine1'] ?: null,
                'office_suite' => $data['officeSuite'] ?: null, 'office_city' => $data['officeCity'] ?: null, 'office_state' => $data['officeState'] ?: null,
                'office_zip' => $data['officeZip'] ?: null, 'billing_same_as_office' => $this->billingSameAsOffice,
                'billing_address_line1' => $billing['line1'] ?: null, 'billing_suite' => $billing['suite'] ?: null, 'billing_city' => $billing['city'] ?: null,
                'billing_state' => $billing['state'] ?: null, 'billing_zip' => $billing['zip'] ?: null, 'billing_country' => $billing['country'] ?: null,
                'contact_name' => $data['contactName'] ?: null, 'contact_job_title' => $data['contactJobTitle'] ?: null, 'email' => $data['email'] ?: null,
                'phone' => $data['phone'] ?: null, 'account_manager_id' => $data['accountManagerId'], 'preferred_language' => $data['preferredLanguage'] ?: 'English',
                'outstanding_balance' => 0, 'ein_tax_id' => $data['einTaxId'] ?: null, 'sales_tax_status' => $data['salesTaxStatus'],
                'payment_terms' => $data['paymentTerms'] ?: null, 'po_required' => (bool) $data['poRequired'], 'notes' => $data['notes'] ?: null,
                'is_active' => true, 'is_draft' => $draft,
            ]);

            $addresses = collect($data['shippingAddresses'] ?? [])
                ->filter(fn ($address) => trim((string) ($address['label'] ?? '')) !== '' || trim((string) ($address['address_line1'] ?? '')) !== '')
                ->values();
            $defaultIndex = $addresses->search(fn ($address) => (bool) ($address['is_default'] ?? false));
            if ($defaultIndex === false && $addresses->isNotEmpty()) $defaultIndex = 0;
            foreach ($addresses as $index => $address) {
                ClientShippingAddress::create([
                    'client_id' => $client->id, 'label' => trim((string) ($address['label'] ?? '')) ?: 'Shipping address '.($index + 1),
                    'recipient' => trim((string) ($address['recipient'] ?? '')) ?: null, 'address_line1' => trim((string) ($address['address_line1'] ?? '')),
                    'suite' => trim((string) ($address['suite'] ?? '')) ?: null, 'city' => trim((string) ($address['city'] ?? '')),
                    'state' => trim((string) ($address['state'] ?? '')), 'zip' => trim((string) ($address['zip'] ?? '')),
                    'country' => trim((string) ($address['country'] ?? '')) ?: $this->defaultClientCountry(), 'is_default' => $index === $defaultIndex, 'sort_order' => $index,
                ]);
            }
            return $client;
        });

        $this->showCreate = false;
        $this->selectedClientId = $client->id;
        $this->showClientPreview = true;
        session()->flash('success', $draft ? 'Client draft saved successfully.' : 'Client created successfully.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), $draft ? 'Client draft saved' : 'Client created', $client->name.($draft ? ' was saved as a draft.' : ' was created.'), 'update', null, null, auth()->user());
    }

    public function addShippingAddress(): void
    {
        foreach ($this->shippingAddresses as &$address) $address['expanded'] = false;
        unset($address);
        $this->shippingAddresses[] = $this->blankShippingAddress(empty($this->shippingAddresses));
    }

    public function duplicateShippingAddress(int $index): void
    {
        abort_unless(isset($this->shippingAddresses[$index]), 404);
        foreach ($this->shippingAddresses as &$address) $address['expanded'] = false;
        unset($address);
        $copy = $this->shippingAddresses[$index];
        $copy['label'] = trim((string) ($copy['label'] ?? '')).' Copy';
        $copy['is_default'] = false;
        $copy['expanded'] = true;
        array_splice($this->shippingAddresses, $index + 1, 0, [$copy]);
    }

    public function removeShippingAddress(int $index): void
    {
        abort_unless(isset($this->shippingAddresses[$index]), 404);
        $wasDefault = (bool) ($this->shippingAddresses[$index]['is_default'] ?? false);
        array_splice($this->shippingAddresses, $index, 1);
        if (!$this->shippingAddresses) $this->shippingAddresses[] = $this->blankShippingAddress(true);
        elseif ($wasDefault) $this->shippingAddresses[0]['is_default'] = true;
    }

    public function toggleShippingAddress(int $index): void
    {
        abort_unless(isset($this->shippingAddresses[$index]), 404);
        $this->shippingAddresses[$index]['expanded'] = !($this->shippingAddresses[$index]['expanded'] ?? false);
    }

    public function editShippingAddress(int $index): void
    {
        abort_unless(isset($this->shippingAddresses[$index]), 404);
        foreach ($this->shippingAddresses as &$address) $address['expanded'] = false;
        unset($address);
        $this->shippingAddresses[$index]['expanded'] = true;
    }

    public function setDefaultShippingAddress(int $index): void
    {
        abort_unless(isset($this->shippingAddresses[$index]), 404);
        foreach ($this->shippingAddresses as $key => &$address) $address['is_default'] = $key === $index;
        unset($address);
    }

    public function showDifferentBillingAddress(): void { $this->billingSameAsOffice = false; }

    private function blankShippingAddress(bool $default = false): array
    {
        return ['label'=>'','recipient'=>'','address_line1'=>'','suite'=>'','city'=>'','state'=>'','zip'=>'','country'=>$this->defaultClientCountry(),'is_default'=>$default,'expanded'=>true];
    }

    private function resetCreateForm(): void
    {
        $defaultCountry = $this->defaultClientCountry();
        $defaultCurrency = $this->defaultClientCurrency();
        $this->clientCode = $this->nextClientCode(); $this->clientName = ''; $this->legalBusinessName = ''; $this->website = '';
        $this->clientCountry = $defaultCountry; $this->preferredCurrency = $defaultCurrency; $this->officeAddress = ''; $this->officeAddressLine1 = '';
        $this->officeSuite = ''; $this->officeCity = ''; $this->officeState = ''; $this->officeZip = ''; $this->billingSameAsOffice = true;
        $this->billingAddressLine1 = ''; $this->billingSuite = ''; $this->billingCity = ''; $this->billingState = ''; $this->billingZip = '';
        $this->billingCountry = $defaultCountry; $this->contactName = ''; $this->contactJobTitle = ''; $this->email = ''; $this->phone = '';
        $this->accountManagerId = auth()->id(); $this->preferredLanguage = 'English'; $this->outstandingBalance = '0'; $this->einTaxId = '';
        $this->salesTaxStatus = 'taxable'; $this->paymentTerms = ''; $this->poRequired = false; $this->notes = '';
        $this->shippingAddresses = [$this->blankShippingAddress(true)]; $this->resetValidation();
    }

    private function defaultClientCountry(): string
    {
        $countries = app(MasterDataService::class)->active('country');
        $default = $countries->first(fn (MasterRecord $record) => (bool) data_get($record->metadata, 'is_default', false));
        return (string) (($default ?? $countries->first())?->name ?? '');
    }

    private function defaultClientCurrency(): string
    {
        $service = app(MasterDataService::class);
        $currencies = $service->active('currency');
        $workspaceCurrency = \App\Models\Workspace::query()->whereKey(app(SetupContext::class)->workspaceId())->value('default_currency');
        $preferred = $currencies->firstWhere('code', strtoupper((string) $workspaceCurrency))
            ?? $currencies->first(fn (MasterRecord $record) => (bool) data_get($record->metadata, 'is_default', false))
            ?? $currencies->first();
        return (string) ($preferred?->code ?? '');
    }

    private function formatAddress(string $line1, string $suite, string $city, string $state, string $zip, string $country): string
    {
        $first = trim(implode(', ', array_filter([$line1, $suite])));
        $second = trim(implode(' ', array_filter([$city ? $city.',' : '', $state, $zip])));
        return trim(implode(', ', array_filter([$first, $second, $country])));
    }

    public function openClient(int $id): void
    {
        // Client rows now open the full client view directly. Keep this method
        // as a compatibility alias so stale Livewire payloads cannot reopen the
        // retired preview modal after a deployment.
        $this->viewClient($id);
    }

    public function closeClientPreview(): void
    {
        $this->showClientPreview = false;
        $this->selectedClientId = null;
        $this->actionMenuClientId = null;
    }

    public function toggleClientMenu(int $id): void
    {
        app(ClientService::class)->visibleQuery(auth()->user())->findOrFail($id);
        $this->actionMenuClientId = $this->actionMenuClientId === $id ? null : $id;
    }

    public function viewClient(int $id): void
    {
        app(ClientService::class)->visibleQuery(auth()->user())->findOrFail($id);
        $this->selectedClientId = $id;
        $this->showClientPreview = false;
        $this->showDetail = true;
        $this->showEdit = false;
        $this->clientDetailTab = 'overview';
        $this->clearClientOrderFilters();
        $this->actionMenuClientId = null;
    }

    public function backToClients(): void
    {
        $this->showDetail = false;
        $this->showEdit = false;
        $this->showClientPreview = false;
        $this->selectedClientId = null;
        $this->clientDetailTab = 'overview';
        $this->actionMenuClientId = null;
        $this->resetValidation();
    }

    private function canEditClient(Client $client): bool
    {
        $access = app(\App\Services\AccessControlService::class);
        if ($access->isAdministrator(auth()->user()) || $access->canEditAll(auth()->user(), 'clients')) return true;
        return $access->canEditOwn(auth()->user(), 'clients')
            && (int) ($client->account_manager_id ?? 0) === (int) auth()->id();
    }

    public function editClient(int $id): void
    {
        $client = app(ClientService::class)->visibleQuery(auth()->user())->with('shippingAddresses')->findOrFail($id);
        abort_unless($this->canEditClient($client), 403);
        $this->selectedClientId = $id;
        $this->showClientPreview = false;
        $this->showDetail = true;
        $this->showEdit = true;
        $this->clientDetailTab = 'overview';
        $this->actionMenuClientId = null;

        $hasStructuredOffice = collect([$client->office_address_line1, $client->office_suite, $client->office_city, $client->office_state, $client->office_zip])
            ->contains(fn ($value) => filled($value));

        $this->clientCode = $client->code ?: 'CL-'.str_pad((string) $client->id, 3, '0', STR_PAD_LEFT);
        $this->clientName = $client->name ?? '';
        $this->legalBusinessName = $client->legal_business_name ?? '';
        $this->website = $client->website ?? '';
        $this->clientCountry = $client->country ?: $this->defaultClientCountry();
        $this->preferredCurrency = $client->preferred_currency ?: $this->defaultClientCurrency();
        $this->officeAddress = $client->office_address ?? '';
        $this->officeAddressLine1 = $client->office_address_line1 ?: ($hasStructuredOffice ? '' : ($client->office_address ?? ''));
        $this->officeSuite = $client->office_suite ?? '';
        $this->officeCity = $client->office_city ?? '';
        $this->officeState = $client->office_state ?? '';
        $this->officeZip = $client->office_zip ?? '';
        $this->billingSameAsOffice = (bool) $client->billing_same_as_office;
        $this->billingAddressLine1 = $client->billing_address_line1 ?? '';
        $this->billingSuite = $client->billing_suite ?? '';
        $this->billingCity = $client->billing_city ?? '';
        $this->billingState = $client->billing_state ?? '';
        $this->billingZip = $client->billing_zip ?? '';
        $this->billingCountry = $client->billing_country ?: $this->clientCountry;
        $this->contactName = $client->contact_name ?? '';
        $this->contactJobTitle = $client->contact_job_title ?? '';
        $this->email = $client->email ?? '';
        $this->phone = $client->phone ?? '';
        $this->accountManagerId = $client->account_manager_id;
        $this->preferredLanguage = $client->preferred_language ?: 'English';
        $this->outstandingBalance = (string) ($client->outstanding_balance ?? 0);
        $this->einTaxId = $client->ein_tax_id ?? '';
        $this->salesTaxStatus = in_array($client->sales_tax_status, ['taxable','tax_exempt'], true) ? $client->sales_tax_status : 'taxable';
        $this->paymentTerms = $client->payment_terms ?? '';
        $this->poRequired = (bool) $client->po_required;
        $this->notes = $client->notes ?? '';
        $this->shippingAddresses = $client->shippingAddresses->values()->map(function (ClientShippingAddress $address, int $index) {
            return [
                'label' => $address->label ?? '',
                'recipient' => $address->recipient ?? '',
                'address_line1' => $address->address_line1 ?? '',
                'suite' => $address->suite ?? '',
                'city' => $address->city ?? '',
                'state' => $address->state ?? '',
                'zip' => $address->zip ?? '',
                'country' => $address->country ?: $this->clientCountry,
                'is_default' => (bool) $address->is_default,
                'expanded' => $index === 0,
            ];
        })->all();
        if (!$this->shippingAddresses) $this->shippingAddresses = [$this->blankShippingAddress(true)];

        $this->resetValidation();
    }

    public function cancelEditClient(): void
    {
        $this->showEdit = false;
        $this->resetValidation();
    }

    public function updateClient(): void
    {
        abort_unless($this->selectedClientId, 404);
        $client = app(ClientService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedClientId);
        abort_unless($this->canEditClient($client), 403);

        // Edit mode is deliberately tolerant of legacy Country/Currency/State
        // values already stored on older clients. The dropdowns still provide
        // current Master Data options, but stale historical values no longer make
        // the Save Client action appear to do nothing because validation failed.
        $data = $this->validate($this->clientProfileRules(false, false, false));

        if ($data['accountManagerId'] && (int) $data['accountManagerId'] !== (int) $client->account_manager_id) {
            abort_unless(auth()->user()->canModule('clients','assign') || auth()->user()->canModule('clients','edit_all'), 403);
        }

        DB::transaction(function () use ($client, $data) {
            $officeAddress = $this->formatAddress(
                $data['officeAddressLine1'] ?? '', $data['officeSuite'] ?? '', $data['officeCity'] ?? '',
                $data['officeState'] ?? '', $data['officeZip'] ?? '', $data['clientCountry'] ?? ''
            );
            $billing = $this->billingSameAsOffice ? [
                'line1' => $data['officeAddressLine1'] ?? '', 'suite' => $data['officeSuite'] ?? '', 'city' => $data['officeCity'] ?? '',
                'state' => $data['officeState'] ?? '', 'zip' => $data['officeZip'] ?? '', 'country' => $data['clientCountry'] ?? '',
            ] : [
                'line1' => $data['billingAddressLine1'] ?? '', 'suite' => $data['billingSuite'] ?? '', 'city' => $data['billingCity'] ?? '',
                'state' => $data['billingState'] ?? '', 'zip' => $data['billingZip'] ?? '', 'country' => $data['billingCountry'] ?? '',
            ];

            $client->update([
                'name' => $data['clientName'],
                'legal_business_name' => $data['legalBusinessName'] ?: null,
                'website' => $data['website'] ?: null,
                'country' => $data['clientCountry'] ?: null,
                'preferred_currency' => strtoupper($data['preferredCurrency']),
                'office_address' => $officeAddress ?: null,
                'office_address_line1' => $data['officeAddressLine1'] ?: null,
                'office_suite' => $data['officeSuite'] ?: null,
                'office_city' => $data['officeCity'] ?: null,
                'office_state' => $data['officeState'] ?: null,
                'office_zip' => $data['officeZip'] ?: null,
                'billing_same_as_office' => $this->billingSameAsOffice,
                'billing_address_line1' => $billing['line1'] ?: null,
                'billing_suite' => $billing['suite'] ?: null,
                'billing_city' => $billing['city'] ?: null,
                'billing_state' => $billing['state'] ?: null,
                'billing_zip' => $billing['zip'] ?: null,
                'billing_country' => $billing['country'] ?: null,
                'contact_name' => $data['contactName'] ?: null,
                'contact_job_title' => $data['contactJobTitle'] ?: null,
                'email' => $data['email'] ?: null,
                'phone' => $data['phone'] ?: null,
                'account_manager_id' => $data['accountManagerId'],
                'preferred_language' => $data['preferredLanguage'] ?: 'English',
                'ein_tax_id' => $data['einTaxId'] ?: null,
                'sales_tax_status' => $data['salesTaxStatus'],
                'payment_terms' => $data['paymentTerms'] ?: null,
                'po_required' => (bool) $data['poRequired'],
                'notes' => $data['notes'] ?: null,
                'is_draft' => false,
            ]);

            $addresses = collect($data['shippingAddresses'] ?? [])
                ->filter(fn ($address) => trim((string) ($address['label'] ?? '')) !== '' || trim((string) ($address['address_line1'] ?? '')) !== '')
                ->values();
            $defaultIndex = $addresses->search(fn ($address) => (bool) ($address['is_default'] ?? false));
            if ($defaultIndex === false && $addresses->isNotEmpty()) $defaultIndex = 0;

            $client->shippingAddresses()->delete();
            foreach ($addresses as $index => $address) {
                ClientShippingAddress::create([
                    'client_id' => $client->id,
                    'label' => trim((string) ($address['label'] ?? '')) ?: 'Shipping address '.($index + 1),
                    'recipient' => trim((string) ($address['recipient'] ?? '')) ?: null,
                    'address_line1' => trim((string) ($address['address_line1'] ?? '')),
                    'suite' => trim((string) ($address['suite'] ?? '')) ?: null,
                    'city' => trim((string) ($address['city'] ?? '')),
                    'state' => trim((string) ($address['state'] ?? '')),
                    'zip' => trim((string) ($address['zip'] ?? '')),
                    'country' => trim((string) ($address['country'] ?? '')) ?: $this->defaultClientCountry(),
                    'is_default' => $index === $defaultIndex,
                    'sort_order' => $index,
                ]);
            }
        });

        $this->showEdit = false;
        session()->flash('success', 'Client updated successfully.');
        try {
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Client updated', $client->name.' was updated.', 'update', null, null, auth()->user());
        } catch (\Throwable $exception) {
            // A notification/Pusher failure must never roll back or visually
            // block a client profile update that was already saved successfully.
            report($exception);
        }
    }

    public function deleteClient(int $id): void
    {
        abort_unless(auth()->user()->canModule('clients','delete'), 403);
        $client = app(ClientService::class)->archive(auth()->user(), $id);
        session()->flash('success', 'Client archived. It is available from Archived Clients and can be restored.');
        try {
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Client archived', $client->name.' was archived.', 'update', null, null, auth()->user());
        } catch (\Throwable $exception) {
            report($exception);
        }

        if ($this->selectedClientId === $id) $this->selectedClientId = null;
        $this->showClientPreview = false;
        $this->showDetail = false;
        $this->showEdit = false;
        $this->actionMenuClientId = null;
        $this->resetPage();
    }

    public function restoreClient(int $id): void
    {
        abort_unless(auth()->user()->canModule('clients','delete'), 403);
        $client = app(ClientService::class)->restore(auth()->user(), $id);
        session()->flash('success', $client->name.' was restored to Active Clients.');
        try {
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Client restored', $client->name.' was restored.', 'update', null, null, auth()->user());
        } catch (\Throwable $exception) {
            report($exception);
        }
        $this->actionMenuClientId = null;
        $this->resetPage();
    }

    private function nextClientCode(): string
    {
        $next = (int) Client::max('id') + 1;
        do { $code = 'CL-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT); $next++; } while (Client::where('code', $code)->exists());
        return $code;
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->showCreate) {
            return view('livewire.clients.index', $this->createPageData($user));
        }

        if ($this->showDetail && $this->selectedClientId) {
            return view('livewire.clients.index', $this->detailPageData($user));
        }

        return view('livewire.clients.index', $this->clientsListData($user));
    }

    private function createPageData(User $user): array
    {
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $countries = $service->active('country');
        $currencies = $service->active('currency');
        $states = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('state')
            ->active()
            ->with('parent:id,name')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $countryFlags = $countries->mapWithKeys(function (MasterRecord $country) {
            $flag = (string) data_get($country->metadata, 'flag', '');
            if ($flag === '' && preg_match('/^[A-Z]{2}$/', strtoupper($country->code))) {
                $flag = collect(str_split(strtoupper($country->code)))
                    ->map(fn (string $letter) => mb_chr(127397 + ord($letter), 'UTF-8'))
                    ->implode('');
            }
            return [$country->name => $flag ?: '🌐'];
        })->all();

        return [
            'users' => ($user->canModule('clients','assign') || $user->canModule('clients','edit_all'))
                ? User::where('is_active', true)->orderBy('name')->get(['id','name','profile_image_path'])
                : collect([$user]),
            'detail' => null,
            'clientCountries' => $countries->pluck('name')->values()->all(),
            'clientCountryFlags' => $countryFlags,
            'clientStatesByCountry' => $states
                ->filter(fn (MasterRecord $state) => $state->parent)
                ->groupBy(fn (MasterRecord $state) => $state->parent->name)
                ->map(fn ($group) => $group->pluck('name')->values()->all())
                ->all(),
            'clientLanguages' => ['English','Chinese','Spanish','French','German','Arabic','Bengali'],
            'clientCurrencies' => $currencies->mapWithKeys(fn (MasterRecord $currency) => [$currency->code => $currency->name])->all(),
            'paymentTermOptions' => ['Net 15','Net 30','Net 45','Net 60','Due on receipt','Prepaid'],
        ];
    }

    private function detailPageData(User $user): array
    {
        $detail = app(ClientService::class)->detail($user, (int) $this->selectedClientId);
        $client = $detail['client'];
        $jobService = app(JobService::class);
        $jobQuery = $jobService->visibleQuery($user)->where('flow_jobs.client_id', $client->id);

        $jobMetrics = (clone $jobQuery)
            ->reorder()
            ->selectRaw("sum(case when completed_at is null and status not in ('Inactive','Cancelled') then 1 else 0 end) as open_count")
            ->selectRaw("sum(case when completed_at is null and status not in ('Inactive','Cancelled') and (needs_attention = 1 or health in ('Needs Attention','At Risk','Delayed','Blocked')) then 1 else 0 end) as attention_count")
            ->selectRaw('sum(case when completed_at is not null then 1 else 0 end) as completed_count')
            ->selectRaw('coalesce(sum(commercial_value), 0) as total_value')
            ->first();

        $documentQuery = app(DocumentService::class)->query($user, ['client' => $client->id]);

        $clientOrders = null;
        $clientDocuments = null;
        $clientActivities = null;
        $clientOrderStatusOptions = collect();
        $clientOrderOwnerOptions = collect();

        if ($this->clientDetailTab === 'orders') {
            $clientOrderStatusOptions = (clone $jobQuery)
                ->reorder()
                ->whereNotNull('status')
                ->where('status', '<>', '')
                ->distinct()
                ->orderBy('status')
                ->pluck('status');

            $ownerIds = (clone $jobQuery)
                ->reorder()
                ->whereNotNull('owner_id')
                ->distinct()
                ->pluck('owner_id');
            $clientOrderOwnerOptions = $ownerIds->isEmpty()
                ? collect()
                : User::query()->whereIn('id', $ownerIds)->orderBy('name')->get(['id','name','profile_image_path']);

            $orders = (clone $jobQuery)
                ->with(['phase:id,name,short_name','owner:id,name,profile_image_path'])
                ->when(trim($this->clientOrderSearch) !== '', function ($query) {
                    $search = trim($this->clientOrderSearch);
                    $legacy = preg_replace('/^ORDER-/i', 'JOB-', $search) ?: $search;
                    $query->where(function ($match) use ($search, $legacy) {
                        $match->where('job_number', 'like', "%{$search}%")
                            ->orWhere('job_number', 'like', "%{$legacy}%")
                            ->orWhere('order_number', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%")
                            ->orWhere('product', 'like', "%{$search}%");
                    });
                })
                ->when($this->clientOrderStatus !== '', fn ($query) => $query->where('status', $this->clientOrderStatus))
                ->when($this->clientOrderOwner !== '', fn ($query) => $query->where('owner_id', (int) $this->clientOrderOwner))
                ->when($this->clientOrderRange === '3m', fn ($query) => $query->where('created_at', '>=', now()->subMonths(3)))
                ->when($this->clientOrderRange === '6m', fn ($query) => $query->where('created_at', '>=', now()->subMonths(6)))
                ->when($this->clientOrderRange === '12m', fn ($query) => $query->where('created_at', '>=', now()->subMonths(12)))
                ->reorder()
                ->latest('created_at')
                ->latest('id');

            $clientOrders = $orders->paginate(
                max(1, min($this->clientOrderPerPage, 50)),
                ['flow_jobs.*'],
                'clientOrdersPage'
            );
        } elseif ($this->clientDetailTab === 'documents') {
            $clientDocuments = (clone $documentQuery)
                ->latest('documents.updated_at')
                ->paginate(12, ['documents.*'], 'clientDocumentsPage');
        } elseif ($this->clientDetailTab === 'activity') {
            $visibleJobIds = (clone $jobQuery)->reorder()->pluck('flow_jobs.id');
            $activityQuery = Activity::query()
                ->with('user:id,name,profile_image_path')
                ->where('subject_type', FlowJob::class)
                ->when(
                    $visibleJobIds->isNotEmpty(),
                    fn ($query) => $query->whereIn('subject_id', $visibleJobIds),
                    fn ($query) => $query->whereRaw('1 = 0')
                )
                ->latest('created_at');
            $clientActivities = $activityQuery->paginate(20, ['*'], 'clientActivityPage');
        }

        $pageData = [
            'detail' => $detail,
            'users' => $this->showEdit && ($user->canModule('clients','assign') || $user->canModule('clients','edit_all'))
                ? User::where('is_active', true)->orderBy('name')->get(['id','name','profile_image_path'])
                : collect([$user]),
            'clientDetailTab' => $this->clientDetailTab,
            'clientOrders' => $clientOrders,
            'clientDocuments' => $clientDocuments,
            'clientActivities' => $clientActivities,
            'clientOrderStatusOptions' => $clientOrderStatusOptions,
            'clientOrderOwnerOptions' => $clientOrderOwnerOptions,
            'clientDocumentCount' => (clone $documentQuery)->count(),
            'clientOrderMetrics' => [
                'open' => (int) ($jobMetrics?->open_count ?? 0),
                'attention' => (int) ($jobMetrics?->attention_count ?? 0),
                'completed' => (int) ($jobMetrics?->completed_count ?? 0),
                'value' => (float) ($jobMetrics?->total_value ?? 0),
            ],
        ];

        if ($this->showEdit) {
            $formData = $this->createPageData($user);
            unset($formData['detail']);
            $pageData = array_merge($pageData, $formData);
        }

        return $pageData;
    }

    private function clientsListData(User $user): array
    {
        $service = app(ClientService::class);
        $clients = $service->paginate($user, [
            'search' => $this->search,
            'country' => $this->country,
            'manager' => $this->manager,
            'health' => $this->jobHealth,
            'outstanding' => $this->outstanding,
            'quick' => $this->quick,
            'archived' => $this->showArchived,
        ], $this->perPage);

        return [
            'clients' => $clients,
            'summary' => $service->summary($user),
            'detail' => $this->showClientPreview && $this->selectedClientId ? $service->detail($user, $this->selectedClientId) : null,
            'countryFilterOptions' => app(\App\Services\FilterOptionService::class)->options($user, 'countries', $this->showArchived ? 'clients-archived' : 'clients', '', $this->country, 5),
            'managerFilterOptions' => app(\App\Services\FilterOptionService::class)->options($user, 'users', 'clients', '', $this->manager !== '' ? (int) $this->manager : null, 5),
            'healthOptions' => app(\App\Services\AccessControlService::class)->applyJobScope(FlowJob::query(), $user)
                ->whereHas('client', fn ($client) => $client->where('is_active', !$this->showArchived))
                ->whereNotNull('health')->distinct()->orderBy('health')->pluck('health'),
            'users' => collect(),
        ];
    }
}
