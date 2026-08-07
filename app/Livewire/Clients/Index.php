<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\User;
use App\Services\ClientService;
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

    public string $clientCode = '';
    public string $clientName = '';
    public string $clientCountry = '';
    public string $contactName = '';
    public string $email = '';
    public string $phone = '';
    public ?int $accountManagerId = null;
    public string $preferredLanguage = 'English';
    public string $outstandingBalance = '0';
    public string $notes = '';

    public function mount(): void
    {
        $this->showCreate = request()->boolean('create');
        $this->clientCode = $this->nextClientCode();
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCountry(): void { $this->resetPage(); }
    public function updatedManager(): void { $this->resetPage(); }
    public function updatedJobHealth(): void { $this->resetPage(); }
    public function updatedOutstanding(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

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

    public function openCreate(): void
    {
        abort_unless(auth()->user()->canModule('clients','create'), 403);
        $this->showCreate = true;
        $this->clientCode = $this->nextClientCode();
    }

    public function closeCreate(): void
    {
        $this->showCreate = false;
        $this->resetValidation();
    }

    public function createClient(): void
    {
        abort_unless(auth()->user()->canModule('clients','create'), 403);
        $data = $this->validate([
            'clientName' => ['required','string','max:255'],
            'clientCountry' => ['nullable','string','max:120'],
            'contactName' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:60'],
            'accountManagerId' => ['nullable','exists:users,id'],
            'preferredLanguage' => ['required','string','max:50'],
            'outstandingBalance' => ['required','numeric','min:0'],
            'notes' => ['nullable','string','max:5000'],
        ]);

        if ($data['accountManagerId'] && (int) $data['accountManagerId'] !== (int) auth()->id()) {
            abort_unless(auth()->user()->canModule('clients','assign') || auth()->user()->canModule('clients','edit_all'), 403);
        }

        $client = Client::create([
            'code' => $this->nextClientCode(), 'name' => $data['clientName'],
            'country' => $data['clientCountry'] ?: null, 'contact_name' => $data['contactName'] ?: null,
            'email' => $data['email'] ?: null, 'phone' => $data['phone'] ?: null,
            'account_manager_id' => $data['accountManagerId'], 'preferred_language' => $data['preferredLanguage'],
            'outstanding_balance' => $data['outstandingBalance'], 'notes' => $data['notes'] ?: null, 'is_active' => true,
        ]);

        $this->showCreate = false;
        $this->selectedClientId = $client->id;
        $this->showClientPreview = true;
        session()->flash('success', 'Client created successfully.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Client created', $client->name.' was created.', 'update', null, null, auth()->user());
    }

    public function openClient(int $id): void
    {
        app(ClientService::class)->visibleQuery(auth()->user())->findOrFail($id);
        $this->selectedClientId = $id;
        $this->showClientPreview = true;
        $this->showDetail = false;
        $this->showEdit = false;
        $this->actionMenuClientId = null;
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
        $this->actionMenuClientId = null;
    }

    public function backToClients(): void
    {
        $this->showDetail = false;
        $this->showEdit = false;
        $this->showClientPreview = false;
        $this->selectedClientId = null;
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
        $client = app(ClientService::class)->visibleQuery(auth()->user())->findOrFail($id);
        abort_unless($this->canEditClient($client), 403);
        $this->selectedClientId = $id;
        $this->showClientPreview = false;
        $this->showDetail = true;
        $this->showEdit = true;
        $this->actionMenuClientId = null;
        $this->clientName = $client->name;
        $this->clientCountry = $client->country ?? '';
        $this->contactName = $client->contact_name ?? '';
        $this->email = $client->email ?? '';
        $this->phone = $client->phone ?? '';
        $this->accountManagerId = $client->account_manager_id;
        $this->preferredLanguage = $client->preferred_language ?: 'English';
        $this->outstandingBalance = (string) ($client->outstanding_balance ?? 0);
        $this->notes = $client->notes ?? '';
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

        $data = $this->validate([
            'clientName' => ['required','string','max:255'],
            'clientCountry' => ['nullable','string','max:120'],
            'contactName' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:60'],
            'accountManagerId' => ['nullable','exists:users,id'],
            'preferredLanguage' => ['required','string','max:50'],
            'outstandingBalance' => ['required','numeric','min:0'],
            'notes' => ['nullable','string','max:5000'],
        ]);

        if ($data['accountManagerId'] && (int) $data['accountManagerId'] !== (int) $client->account_manager_id) {
            abort_unless(auth()->user()->canModule('clients','assign') || auth()->user()->canModule('clients','edit_all'), 403);
        }

        $client->update([
            'name' => $data['clientName'], 'country' => $data['clientCountry'] ?: null,
            'contact_name' => $data['contactName'] ?: null, 'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null, 'account_manager_id' => $data['accountManagerId'],
            'preferred_language' => $data['preferredLanguage'], 'outstanding_balance' => $data['outstandingBalance'],
            'notes' => $data['notes'] ?: null,
        ]);

        $this->showEdit = false;
        session()->flash('success', 'Client updated successfully.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Client updated', $client->name.' was updated.', 'update', null, null, auth()->user());
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
        $service = app(ClientService::class);
        $clients = $service->paginate(auth()->user(), [
            'search' => $this->search,
            'country' => $this->country,
            'manager' => $this->manager,
            'health' => $this->jobHealth,
            'outstanding' => $this->outstanding,
            'quick' => $this->quick,
            'archived' => $this->showArchived,
        ], $this->perPage);

        $detail = $this->selectedClientId ? $service->detail(auth()->user(), $this->selectedClientId) : null;

        $users = (auth()->user()->canModule('clients','assign') || auth()->user()->canModule('clients','edit_all'))
            ? User::where('is_active', true)->orderBy('name')->get(['id','name','profile_image_path'])
            : collect([auth()->user()]);

        return view('livewire.clients.index', [
            'clients' => $clients,
            'summary' => $service->summary(auth()->user()),
            'detail' => $detail,
            'countries' => $service->visibleQuery(auth()->user())->where('is_active', !$this->showArchived)->whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'managers' => User::where('is_active', true)
                ->whereIn('id', $service->visibleQuery(auth()->user())->where('is_active', !$this->showArchived)->whereNotNull('account_manager_id')->distinct()->pluck('account_manager_id'))
                ->orderBy('name')->get(['id','name','profile_image_path']),
            'healthOptions' => app(\App\Services\AccessControlService::class)->applyJobScope(FlowJob::query(), auth()->user())
                ->whereHas('client', fn ($client) => $client->where('is_active', !$this->showArchived))
                ->whereNotNull('health')->distinct()->orderBy('health')->pluck('health'),
            'users' => $users,
        ]);
    }
}
