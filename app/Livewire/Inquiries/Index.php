<?php

namespace App\Livewire\Inquiries;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\InquiryItem;
use App\Models\Document;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Services\AccessControlService;
use App\Services\InquiryService;
use App\Services\MentionService;
use App\Services\WorkspaceSettingsService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use HandlesInlineEdits, WithFileUploads, WithPagination;

    public string $search = '';
    public string $quick = 'all';
    public bool $hideCompleted = true;
    public int $perPage = 20;
    public array $metrics = ['active' => 0, 'converted' => 0, 'dead' => 0, 'dueToday' => 0];

    public bool $showCreate = false;
    public ?int $selectedInquiryId = null;
    public string $detailTab = 'overview';
    public ?int $selectedTaskId = null;
    public bool $showWorkflowManager = false;

    // Create Inquiry fields.
    public ?int $clientId = null;
    public string $clientContact = '';
    public array $clientContactOptions = [];
    public string $referenceNumber = '';
    public string $subject = '';
    public string $requirementNotes = '';
    public string $requestSource = 'Email';
    public string $createReceivedDate = '';
    public ?int $createOwnerId = null;

    // Quick client creation from Create Inquiry.
    public bool $showCreateClientModal = false;
    public string $newClientName = '';
    public string $newClientContactName = '';
    public string $newClientEmail = '';
    public string $newClientPhone = '';
    public string $newClientCountry = '';
    public bool $useNewClientContactForInquiry = true;
    public bool $showCreateContactModal = false;
    public string $newContactName = '';
    public string $newContactEmail = '';
    public string $newContactPhone = '';
    public int $createWorkflowTaskCount = 0;
    public int $createWorkflowPhaseCount = 0;
    public ?int $createWorkflowId = null;
    public string $selectedWorkflowLabel = '';
    public array $createAttachments = [];
    public array $createProductRows = [];
    public array $createProductCategoryOptions = [];

    // Inquiry product editor (Inquiry details).
    public bool $editingInquiryProducts = false;
    public array $inquiryProductRows = [];
    public array $inquiryCategoryFilterOptions = [];

    // Options are loaded only when create/workflow management is opened.
    public array $userOptions = [];
    public array $clientFilterOptions = [];
    public string $selectedClientLabel = '';
    public array $ownerFilterOptions = [];
    public string $selectedOwnerLabel = '';
    public array $taskPackOptions = [];
    public array $workflowFilterOptions = [];

    // Detail actions.
    public array $inquiryUploads = [];
    public array $taskQuickUploads = [];
    public $taskUpload = null;
    public bool $showTaskDocumentModal = false;
    public ?int $taskDocumentModalTaskId = null;
    public ?int $pendingCompletionTaskId = null;
    public string $taskDocumentSource = 'upload';
    public $taskDocumentUpload = null;
    public ?int $taskExistingDocumentId = null;
    public string $taskDocumentNote = '';
    public ?int $taskLinkFormTaskId = null;
    public string $taskLinkUrl = '';
    public string $taskComment = '';
    public string $inquiryComment = '';
    public string $inquiryActivityTab = 'all';
    public bool $showInquiryDocumentPicker = false;
    public ?int $inquiryExistingDocumentId = null;
    public ?int $taskAssigneeId = null;
    public string $taskDueDate = '';
    public string $taskStatus = 'In Progress';

    // Admin-only task append form on Inquiry details.
    public bool $showAddTaskForm = false;
    public string $newTaskName = '';
    public string $newTaskDescription = '';
    public ?int $newTaskAssigneeId = null;
    public string $newTaskDueDate = '';
    public bool $newTaskRequiresSubmission = false;
    public string $newTaskSubmissionLabel = '';

    // Workflow manager.
    public array $managerRows = [];
    public ?int $managerTemplateId = null;

    // Final decision.
    public string $deadReason = 'Price too high';
    public string $deadNote = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->canModule('inquiries', 'view'), 403);
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        $this->resetCreateCollections();

        if (request()->boolean('create')) {
            $this->openCreate();
            return;
        }

        if ($open = request()->integer('open')) {
            app(InquiryService::class)->findVisible(auth()->user(), $open);
            $this->selectedInquiryId = $open;
            // The separate Taskflow tab was removed; old workflow URLs now land on Overview.
            $this->detailTab = 'overview';
            if ($taskId = request()->integer('task')) {
                $this->detailTab = 'overview';
                $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId);
                if ((int) $task->inquiry_id === $open) {
                    // Task deep-links now highlight the row in the inline workflow.
                    // No task-detail modal or heavy task relationship hydration is needed.
                    $this->selectedTaskId = $taskId;
                }
            }
        }
    }

    public function updatedSearch(): void { $this->resetPage('inquiryPage'); }
    public function updatedPerPage(): void { $this->perPage = max(10, min(50, $this->perPage)); $this->resetPage('inquiryPage'); }
    public function updatedHideCompleted(): void { $this->resetPage('inquiryPage'); }

    public function setQuick(string $quick): void
    {
        abort_unless(in_array($quick, ['all', 'active', 'converted', 'dead'], true), 422);
        $this->quick = $quick;
        $this->resetPage('inquiryPage');
    }

    public function deleteInquiry(int $id): void
    {
        $service = app(InquiryService::class);
        $inquiry = $service->findVisible(auth()->user(), $id);
        $number = (string) $inquiry->inquiry_number;

        $service->delete($inquiry, auth()->user());
        $this->metrics = $service->metrics(auth()->user());
        $this->resetPage('inquiryPage');

        session()->flash('success', $number.' deleted successfully.');
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->canModule('inquiries', 'create'), 403);
        $this->showCreate = true;
        $this->selectedInquiryId = null;
        $this->userOptions = [];
        $this->selectedTaskId = null;
        $this->createOwnerId ??= (int) auth()->id();
        if ($this->createReceivedDate === '') {
            $this->createReceivedDate = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        }
        // Products remain optional, but keep one empty row visible by default so
        // users can enter product details immediately without opening the section first.
        if ($this->createProductRows === []) {
            $this->createProductRows[] = ['category' => '', 'product' => '', 'quantity' => 1];
        }
        $this->loadCreateOptions();
    }

    public function addCreateProductRow(): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('inquiries', 'create'), 403);
        abort_if(count($this->createProductRows) >= 25, 422, 'An Inquiry can contain up to 25 product rows.');
        $this->createProductRows[] = ['category' => '', 'product' => '', 'quantity' => 1];
    }

    public function removeCreateProductRow(int $index): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('inquiries', 'create'), 403);
        abort_unless(array_key_exists($index, $this->createProductRows), 422);
        unset($this->createProductRows[$index]);
        $this->createProductRows = array_values($this->createProductRows);
        $this->resetValidation('createProductRows');
    }

    public function cancelCreate(): void
    {
        $this->showCreate = false;
        $this->resetCreateForm();
    }

    public function openCreateClientModal(): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('clients', 'create'), 403);
        $this->resetCreateClientModal();
        $this->showCreateClientModal = true;
    }

    public function closeCreateClientModal(): void
    {
        $this->showCreateClientModal = false;
        $this->showCreateContactModal = false;
        $this->resetCreateClientModal();
        $this->resetValidation([
            'newClientName', 'newClientContactName', 'newClientEmail',
            'newClientPhone', 'newClientCountry',
        ]);
    }

    public function createClientAndSelect(): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('clients', 'create'), 403);

        $data = $this->validate([
            'newClientName' => ['required', 'string', 'max:255'],
            'newClientContactName' => ['nullable', 'string', 'max:255'],
            'newClientEmail' => ['nullable', 'email', 'max:255'],
            'newClientPhone' => ['nullable', 'string', 'max:60'],
            'newClientCountry' => ['nullable', 'string', 'max:120'],
            'useNewClientContactForInquiry' => ['boolean'],
        ]);

        $client = Client::create([
            'code' => $this->nextClientCode(),
            'name' => trim($data['newClientName']),
            'country' => trim((string) $data['newClientCountry']) ?: null,
            'contact_name' => trim((string) $data['newClientContactName']) ?: null,
            'email' => trim((string) $data['newClientEmail']) ?: null,
            'phone' => trim((string) $data['newClientPhone']) ?: null,
            'account_manager_id' => auth()->id(),
            'created_by' => auth()->id(),
            'preferred_language' => 'English',
            'outstanding_balance' => 0,
            'is_active' => true,
        ]);

        $this->clientId = (int) $client->id;
        $this->selectedClientLabel = (string) $client->name;
        $this->clientContact = $this->useNewClientContactForInquiry
            ? (string) ($client->contact_name ?: '')
            : '';
        $this->clientFilterOptions = app(\App\Services\FilterOptionService::class)
            ->options(auth()->user(), 'clients', 'create-inquiry', '', $client->id, 6)
            ->all();
        $this->refreshCreateWorkflowOptions();

        $this->showCreateClientModal = false;
        $this->resetCreateClientModal();
        $this->resetValidation('clientId');

        try {
            app(\App\Services\NotificationService::class)->notifyUser(
                auth()->user(),
                'Client created',
                $client->name.' was created from Create Inquiry.',
                'update',
                null,
                null,
                auth()->user(),
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    public function openCreateContactModal(): void
    {
        abort_unless($this->showCreate && $this->clientId, 422, 'Select a client first.');
        $client = app(\App\Services\ClientService::class)->visibleQuery(auth()->user())->findOrFail((int) $this->clientId);
        abort_unless($this->canEditClientRecord($client), 403);
        $this->newContactName = '';
        $this->newContactEmail = '';
        $this->newContactPhone = '';
        $this->showCreateContactModal = true;
    }

    public function closeCreateContactModal(): void
    {
        $this->showCreateContactModal = false;
        $this->newContactName = '';
        $this->newContactEmail = '';
        $this->newContactPhone = '';
        $this->resetValidation(['newContactName', 'newContactEmail', 'newContactPhone']);
    }

    public function saveCreateContact(): void
    {
        abort_unless($this->showCreate && $this->clientId, 422, 'Select a client first.');
        $data = $this->validate([
            'newContactName' => ['required', 'string', 'max:255'],
            'newContactEmail' => ['nullable', 'email', 'max:255'],
            'newContactPhone' => ['nullable', 'string', 'max:60'],
        ]);

        $client = app(\App\Services\ClientService::class)
            ->visibleQuery(auth()->user())
            ->findOrFail((int) $this->clientId);
        abort_unless($this->canEditClientRecord($client), 403);
        $client->update([
            'contact_name' => trim($data['newContactName']),
            'email' => trim((string) $data['newContactEmail']) ?: $client->email,
            'phone' => trim((string) $data['newContactPhone']) ?: $client->phone,
        ]);

        $this->clientContact = (string) $client->contact_name;
        $this->showCreateContactModal = false;
        $this->newContactName = '';
        $this->newContactEmail = '';
        $this->newContactPhone = '';
    }

    private function loadClientContactOptions(?Client $client): void
    {
        if (! $client) {
            $this->clientContactOptions = [];
            $this->clientContact = '';
            return;
        }

        $contacts = collect();
        if (Schema::hasTable('client_contacts')) {
            $contacts = $client->contacts()->get(['name', 'email', 'job_title', 'is_primary', 'sort_order']);
        }

        if ($contacts->isEmpty() && trim((string) $client->contact_name) !== '') {
            $contacts = collect([(object) [
                'name' => $client->contact_name,
                'email' => $client->email,
                'job_title' => $client->contact_job_title,
                'is_primary' => true,
            ]]);
        }

        $this->clientContactOptions = $contacts->map(function ($contact): array {
            $name = trim((string) ($contact->name ?? ''));
            $email = trim((string) ($contact->email ?? ''));
            $jobTitle = trim((string) ($contact->job_title ?? ''));
            $meta = collect([$jobTitle, $email])->filter()->implode(' · ');
            return [
                'value' => $name,
                'label' => $name,
                'meta' => $meta,
                'primary' => (bool) ($contact->is_primary ?? false),
            ];
        })->filter(fn ($contact) => $contact['value'] !== '')->values()->all();

        $values = collect($this->clientContactOptions)->pluck('value');
        if (! $values->contains($this->clientContact)) {
            $this->clientContact = (string) ($values->first() ?? '');
        }
    }

    public function updatedClientId($value): void
    {
        $this->resetValidation(['clientId', 'clientContact']);
        if (!$this->showCreate || !$value) {
            $this->clientContact = '';
            $this->clientContactOptions = [];
            return;
        }
        $client = app(\App\Services\ClientService::class)->referenceQuery(auth()->user(), 'create-inquiry')->where('is_active', true)->find((int) $value);
        $this->loadClientContactOptions($client);
    }

    public function setCreateSelector(string $property, mixed $value): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('inquiries', 'create'), 403);

        $user = auth()->user();
        $raw = trim((string) $value);
        $options = app(\App\Services\FilterOptionService::class);

        if (preg_match('/^createProductRows\.(\d+)\.(category|product)$/', $property, $matches) === 1) {
            $index = (int) $matches[1];
            $field = $matches[2];
            abort_unless(array_key_exists($index, $this->createProductRows), 422, 'That product row is no longer available.');
            abort_unless($raw !== '', 422, 'Please choose a valid option.');

            $category = $field === 'product'
                ? trim((string) ($this->createProductRows[$index]['category'] ?? ''))
                : '';
            $type = $field === 'category' ? 'product-categories' : 'products';
            $valid = $options->options(
                $user,
                $type,
                'create-inquiry',
                '',
                $raw,
                20,
                $field === 'product' ? ['category' => $category] : [],
            )->contains(fn ($item) => (string) ($item['id'] ?? '') === $raw);
            abort_unless($valid, 422, 'That option is no longer available.');

            $this->createProductRows[$index][$field] = $raw;
            $this->resetValidation("createProductRows.$index.$field");

            if ($field === 'category') {
                $this->createProductRows[$index]['product'] = '';
                $this->resetValidation("createProductRows.$index.product");
            }
            return;
        }

        if ($property === 'clientId') {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid option.');
            $id = (int) $raw;
            $selected = $options->options($user, 'clients', 'create-inquiry', '', $id, 20)
                ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($selected, 422, 'That option is no longer available.');

            $this->clientId = $id;
            $this->selectedClientLabel = (string) ($selected['label'] ?? '');
            $this->resetValidation(['clientId', 'clientContact']);
            $client = app(\App\Services\ClientService::class)->referenceQuery($user, 'create-inquiry')
                ->where('is_active', true)
                ->find($id);
            $this->loadClientContactOptions($client);
            $this->refreshCreateWorkflowOptions();
            return;
        }

        if ($property === 'createOwnerId') {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid assignee.');
            $id = (int) $raw;
            $selected = $options->options($user, 'users', 'create-inquiry', '', $id, 20)
                ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($selected, 422, 'That assignee is no longer available.');

            $this->createOwnerId = $id;
            $name = (string) ($selected['label'] ?? '');
            $this->selectedOwnerLabel = $id === (int) $user->id ? 'Me · '.$name : $name;
            $this->resetValidation('createOwnerId');
            return;
        }

        if ($property === 'createWorkflowId') {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid Workflow.');
            $id = (int) $raw;
            $selected = $options->options($user, 'workflows', 'create-inquiry', '', $id, 20, ['client_id' => $this->clientId])
                ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($selected, 422, 'That Workflow is no longer available.');

            $this->createWorkflowId = $id;
            $this->selectedWorkflowLabel = (string) ($selected['label'] ?? '');
            $summary = app(InquiryService::class)->workflowSummary($id);
            $this->createWorkflowTaskCount = (int) ($summary['tasks'] ?? 0);
            $this->createWorkflowPhaseCount = (int) ($summary['phases'] ?? 0);
            $this->resetValidation('createWorkflowId');
            return;
        }

        abort(422, 'Unsupported Create Inquiry selector.');
    }


    #[Renderless]
    public function updateInquiryItem(int $itemId, string $field, mixed $value): array
    {
        $label = match ($field) {
            'category' => 'product category',
            'item_name' => 'product',
            'quantity' => 'quantity',
            default => 'product detail',
        };

        return $this->persistInlineEdit($label, function () use ($itemId, $field, $value): void {
            $user = auth()->user();
            $inquiry = $this->selectedInquiry();
            $item = InquiryItem::query()
                ->where('inquiry_id', $inquiry->id)
                ->findOrFail($itemId);

            if ($field === 'category') {
                abort_unless(
                    app(\App\Services\MasterDataService::class)
                        ->active('product_category')
                        ->contains('name', trim((string) $value)),
                    422,
                    'Select a valid active product category.'
                );
            }

            if ($field === 'item_name') {
                abort_if(blank($item->category), 422, 'Select a product category first.');
                $validProduct = app(\App\Services\FilterOptionService::class)
                    ->options($user, 'products', 'inquiry-detail', '', trim((string) $value), 20, [
                        'category' => (string) $item->category,
                    ])
                    ->contains(fn ($option) => (string) ($option['id'] ?? '') === trim((string) $value));
                abort_unless($validProduct, 422, 'Select a valid active product for this category.');
            }

            app(InquiryService::class)->updateItem($inquiry, $item, $field, $value, $user);
        });
    }

    public function addInquiryItem(): void
    {
        $user = auth()->user();
        $inquiry = $this->selectedInquiry();
        abort_unless(app(InquiryService::class)->canEdit($user, $inquiry), 403);
        abort_if($inquiry->result, 422, 'Products on a closed Inquiry cannot be changed.');

        // Repeated clicks should never create a stack of unfinished rows.
        if ($inquiry->items()->where('item_name', '')->exists()) {
            return;
        }

        app(InquiryService::class)->addItem($inquiry, '', '', 1, $user);
    }

    public function removeInquiryItem(int $itemId): void
    {
        $user = auth()->user();
        $inquiry = $this->selectedInquiry();
        $item = InquiryItem::query()
            ->where('inquiry_id', $inquiry->id)
            ->findOrFail($itemId);

        app(InquiryService::class)->removeItem($inquiry, $item, $user);
    }


    public function beginInquiryProductEdit(): void
    {
        $service = app(InquiryService::class);
        $inquiry = $this->selectedInquiry(['items']);
        abort_unless($service->canEdit(auth()->user(), $inquiry), 403);
        abort_if($inquiry->result, 422, 'Products on a closed Inquiry cannot be changed.');

        $this->inquiryProductRows = $inquiry->items
            ->map(fn ($item) => [
                'id' => (int) $item->id,
                'category' => (string) ($item->category ?? ''),
                'product' => (string) $item->item_name,
                'quantity' => max(1, (int) round((float) $item->quantity)),
            ])
            ->values()
            ->all();

        if ($this->inquiryProductRows === []) {
            $this->inquiryProductRows = [['id' => null, 'category' => '', 'product' => '', 'quantity' => 1]];
        }

        $this->inquiryCategoryFilterOptions = app(\App\Services\FilterOptionService::class)
            ->options(auth()->user(), 'product-categories', 'inquiry-detail', '', null, 8)
            ->all();
        $this->editingInquiryProducts = true;
        $this->resetValidation('inquiryProductRows');
    }

    public function cancelInquiryProductEdit(): void
    {
        $this->editingInquiryProducts = false;
        $this->inquiryProductRows = [];
        $this->inquiryCategoryFilterOptions = [];
        $this->resetValidation('inquiryProductRows');
    }

    public function addInquiryProductRow(): void
    {
        abort_unless($this->editingInquiryProducts, 422);
        abort_if(count($this->inquiryProductRows) >= 25, 422, 'An Inquiry can contain up to 25 product rows.');
        $this->inquiryProductRows[] = ['id' => null, 'category' => '', 'product' => '', 'quantity' => 1];
    }

    public function removeInquiryProductRow(int $index): void
    {
        abort_unless($this->editingInquiryProducts, 422);
        abort_unless(array_key_exists($index, $this->inquiryProductRows), 422);
        if (count($this->inquiryProductRows) <= 1) return;
        unset($this->inquiryProductRows[$index]);
        $this->inquiryProductRows = array_values($this->inquiryProductRows);
        $this->resetValidation('inquiryProductRows');
    }

    public function setInquiryProductSelector(string $property, mixed $value): void
    {
        abort_unless($this->editingInquiryProducts && $this->selectedInquiryId, 403);
        $inquiry = $this->selectedInquiry();
        abort_unless(app(InquiryService::class)->canEdit(auth()->user(), $inquiry), 403);
        abort_if($inquiry->result, 422, 'Products on a closed Inquiry cannot be changed.');

        if (preg_match('/^inquiryProductRows\.(\d+)\.(category|product)$/', $property, $matches) !== 1) {
            abort(422, 'Unsupported Inquiry product selector.');
        }

        $index = (int) $matches[1];
        $field = $matches[2];
        abort_unless(array_key_exists($index, $this->inquiryProductRows), 422, 'That product row is no longer available.');

        $raw = trim((string) $value);
        abort_unless($raw !== '', 422, 'Please choose a valid option.');
        $category = $field === 'product' ? trim((string) ($this->inquiryProductRows[$index]['category'] ?? '')) : '';
        $type = $field === 'category' ? 'product-categories' : 'products';
        $valid = app(\App\Services\FilterOptionService::class)->options(
            auth()->user(),
            $type,
            'inquiry-detail',
            '',
            $raw,
            20,
            $field === 'product' ? ['category' => $category] : [],
        )->contains(fn ($item) => (string) ($item['id'] ?? '') === $raw);
        abort_unless($valid, 422, 'That option is no longer available.');

        $this->inquiryProductRows[$index][$field] = $raw;
        $this->resetValidation("inquiryProductRows.$index.$field");

        if ($field === 'category') {
            $this->inquiryProductRows[$index]['product'] = '';
            $this->resetValidation("inquiryProductRows.$index.product");
        }
    }

    public function saveInquiryProducts(): void
    {
        $service = app(InquiryService::class);
        $inquiry = $this->selectedInquiry();
        abort_unless($this->editingInquiryProducts && $service->canEdit(auth()->user(), $inquiry), 403);
        abort_if($inquiry->result, 422, 'Products on a closed Inquiry cannot be changed.');

        $data = $this->validate([
            'inquiryProductRows' => ['required', 'array', 'min:1', 'max:25'],
            'inquiryProductRows.*.category' => ['required', 'string', 'max:255'],
            'inquiryProductRows.*.product' => ['required', 'string', 'max:255'],
            'inquiryProductRows.*.quantity' => ['required', 'integer', 'min:1', 'max:999999999'],
        ]);

        $options = app(\App\Services\FilterOptionService::class);
        $catalogInvalid = false;
        foreach ($data['inquiryProductRows'] as $index => $row) {
            $category = trim((string) $row['category']);
            $product = trim((string) $row['product']);
            $categoryValid = $options->options(auth()->user(), 'product-categories', 'inquiry-detail', '', $category, 20)
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === $category);
            $productValid = $options->options(auth()->user(), 'products', 'inquiry-detail', '', $product, 20, ['category' => $category])
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === $product);

            if (! $categoryValid) {
                $catalogInvalid = true;
                $this->addError("inquiryProductRows.$index.category", 'That product category is no longer available.');
            }
            if (! $productValid) {
                $catalogInvalid = true;
                $this->addError("inquiryProductRows.$index.product", 'That product is not available for the selected category.');
            }
        }
        if ($catalogInvalid) return;

        $service->replaceItems($inquiry, array_map(fn (array $row): array => [
            'category' => trim((string) $row['category']),
            'name' => trim((string) $row['product']),
            'quantity' => (int) $row['quantity'],
            'unit' => 'pcs',
        ], $data['inquiryProductRows']), auth()->user());

        $this->editingInquiryProducts = false;
        $this->inquiryProductRows = [];
        $this->inquiryCategoryFilterOptions = [];
        session()->flash('success', 'Inquiry products updated.');
    }


    public function saveDraft(): void { $this->persistInquiry(true); }
    public function createInquiry(): void { $this->persistInquiry(false); }

    public function openInquiry(int $id): void
    {
        app(InquiryService::class)->findVisible(auth()->user(), $id);
        $this->selectedInquiryId = $id;
        $this->userOptions = [];
        $this->showCreate = false;
        $this->detailTab = 'overview';
        $this->selectedTaskId = null;
        $this->showAddTaskForm = false;
        $this->editingInquiryProducts = false;
        $this->inquiryProductRows = [];
        $this->inquiryCategoryFilterOptions = [];
        $this->resetPage('inquiryDocumentsPage');
        $this->resetPage('inquiryActivityPage');
    }

    public function closeInquiry(): void
    {
        $this->selectedInquiryId = null;
        $this->selectedTaskId = null;
        $this->showWorkflowManager = false;
        $this->showAddTaskForm = false;
        $this->editingInquiryProducts = false;
        $this->inquiryProductRows = [];
        $this->inquiryCategoryFilterOptions = [];
        $this->showInquiryDocumentPicker = false;
        $this->inquiryExistingDocumentId = null;
    }

    public function setDetailTab(string $tab): void
    {
        // Overview is now the single Inquiry details page. Keep this method for
        // backward compatibility with stale Livewire DOM/history entries.
        abort_unless(in_array($tab, ['overview', 'workflow'], true), 422);
        $this->detailTab = 'overview';
        $this->selectedTaskId = null;
        $this->resetPage('inquiryDocumentsPage');
        $this->resetPage('inquiryActivityPage');
    }

    #[Renderless]
    public function updateListStatus(int $inquiryId, string $status): array
    {
        $inquiry = app(InquiryService::class)->findVisible(auth()->user(), $inquiryId);
        $saved = app(InquiryService::class)->updateStatus($inquiry, $status, auth()->user());
        return ['ok' => true, 'status' => $saved->status, 'tone' => $this->tone($saved->status), 'color' => app(\App\Services\MasterDataService::class)->displayColorFor('inquiry_status', (string) $saved->status)];
    }

    public function convertInquiryFromList(int $inquiryId): void
    {
        $service = app(InquiryService::class);
        $inquiry = $service->findVisible(auth()->user(), $inquiryId);
        $job = $service->convertToOrder($inquiry, auth()->user());
        $this->metrics = $service->metrics(auth()->user());
        session()->flash('success', $job->displayOrderNumber().' created from Inquiry.');
    }

    public function markInquiryDeadFromList(int $inquiryId, string $reason): void
    {
        $reason = trim($reason);
        abort_if($reason === '' || mb_strlen($reason) > 255, 422, 'Please provide a closure reason.');

        $service = app(InquiryService::class);
        $inquiry = $service->findVisible(auth()->user(), $inquiryId);
        $service->markDead($inquiry, $reason, null, auth()->user());
        $this->metrics = $service->metrics(auth()->user());
        session()->flash('success', 'Inquiry closed.');
    }

    #[Renderless]
    public function updateInquiryField(string $field, mixed $value): array
    {
        abort_unless(in_array($field, ['subject', 'owner_id', 'priority', 'requirement_notes'], true), 422);
        $inquiry = $this->selectedInquiry(['owner:id,name,profile_image_path']);
        $saved = app(InquiryService::class)->updateDetailField($inquiry, $field, $value, auth()->user());

        $result = [
            'ok' => true,
            'value' => match ($field) {
                'owner_id' => $saved->owner_id,
                default => $saved->{$field},
            },
            'display' => match ($field) {
                'owner_id' => $saved->owner?->name ?: 'Unassigned',
                default => (string) $saved->{$field},
            },
        ];

        if ($field === 'priority') {
            $result['color'] = app(\App\Services\MasterDataService::class)->displayColorFor('priority', (string) $saved->priority);
        }

        if ($field === 'owner_id') {
            $result['avatarUrl'] = $saved->owner?->profileImageUrl() ?? '';
        }

        if ($field === 'requirement_notes') {
            $result['displayHtml'] = app(\App\Services\MentionService::class)
                ->render((string) ($saved->requirement_notes ?? ''));
        }

        return $result;
    }

    #[Renderless]
    public function updateInquiryStartInline(?string $value): array
    {
        $saved = app(InquiryService::class)->updateStartedAt($this->selectedInquiry(), $value, auth()->user());
        $localized = \App\Support\UserLocalTime::localize($saved->started_at);

        return [
            'ok' => true,
            'value' => $localized?->format('Y-m-d\\TH:i') ?? '',
            'display' => $localized?->format('M j, Y · g:i A') ?? '—',
        ];
    }

    #[Renderless]
    public function updateInquiryStatus(string $status): array
    {
        $inquiry = $this->selectedInquiry();
        $saved = app(InquiryService::class)->updateStatus($inquiry, $status, auth()->user());
        return ['ok' => true, 'status' => $saved->status, 'tone' => $this->tone($saved->status)];
    }

    public function updateTaskStatusInline(int $taskId, string $status): array
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId);
        $saved = app(InquiryService::class)->updateTaskStatus($task, $status, auth()->user());
        $updatedInquiry = $saved->inquiry()->first(['id', 'status', 'started_at']);
        $inquiryStatus = (string) $updatedInquiry->status;
        $localizedStart = \App\Support\UserLocalTime::localize($updatedInquiry->started_at);
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());

        return [
            'ok' => true,
            'status' => $saved->status,
            'completed' => $saved->completed_at !== null,
            'inquiryStatus' => $inquiryStatus,
            'inquiryTone' => $this->tone($inquiryStatus),
            'inquiryColor' => app(\App\Services\MasterDataService::class)->displayColorFor('inquiry_status', $inquiryStatus),
            'inquiryStartValue' => $localizedStart?->format('Y-m-d\\TH:i') ?? '',
            'inquiryStartDisplay' => $localizedStart?->format('M j, Y · g:i A') ?? '—',
        ];
    }

    #[Renderless]
    public function updateTaskDueInline(int $taskId, ?string $date): array
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId);
        $saved = app(InquiryService::class)->updateTaskDueDate($task, $date, auth()->user());
        return ['ok' => true, 'date' => $saved->due_date?->toDateString()];
    }

    #[Renderless]
    public function updateTaskAssigneeInline(int $taskId, mixed $assigneeId): array
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId);
        $assigneeId = $assigneeId === '' || $assigneeId === null ? null : (int) $assigneeId;
        $assignee = $assigneeId ? User::query()->where('is_active', true)->findOrFail($assigneeId) : null;

        // Assignee is intentionally editable even after the task is completed.
        // Use the dedicated field updater so completed_at/status are preserved.
        $saved = app(InquiryService::class)->updateTaskAssignee($task, $assigneeId, auth()->user());

        return [
            'ok' => true,
            'assigneeId' => $saved->assignee_id,
            'assigneeName' => $assignee?->name ?: 'Unassigned',
            'avatarUrl' => $assignee?->profileImageUrl(),
        ];
    }

    public function completeTaskInline(int $taskId): void
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId);
        app(InquiryService::class)->completeTask($task, auth()->user());
        $this->selectedTaskId = null;
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        session()->flash('success', 'Inquiry task completed.');
    }

    public function openTask(int $taskId): void
    {
        $task = app(InquiryService::class)->taskDetail(auth()->user(), $taskId);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        $this->selectedTaskId = $taskId;
        $this->loadManagementOptions();
        $this->hydrateTaskEditor($task);
    }

    public function closeTask(): void
    {
        $this->selectedTaskId = null;
        $this->taskUpload = null;
        $this->taskComment = '';
    }

    public function saveTask(): void
    {
        $this->validate([
            'taskAssigneeId' => ['nullable', 'exists:users,id'],
            'taskDueDate' => ['nullable', 'date'],
            'taskStatus' => ['required', Rule::in(InquiryService::WORKING_STATUSES)],
        ]);
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), (int) $this->selectedTaskId);
        app(InquiryService::class)->updateTask($task, [
            'assignee_id' => $this->taskAssigneeId,
            'due_date' => $this->taskDueDate,
            'status' => $this->taskStatus,
        ], auth()->user());
        session()->flash('success', 'Task changes saved.');
    }

    public function completeTask(): void
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), (int) $this->selectedTaskId);
        app(InquiryService::class)->completeTask($task, auth()->user());
        $this->selectedTaskId = null;
        $this->taskUpload = null;
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        session()->flash('success', 'Inquiry task completed.');
    }

    public function openTaskDocumentModal(int $taskId): void
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        abort_unless(app(InquiryService::class)->canEditTask(auth()->user(), $task), 403);
        abort_if($task->inquiry->result, 422, 'Tasks on a closed Inquiry cannot receive documents.');
        $canCreateDocument = auth()->user()->canModule('documents', 'create');
        $canLinkDocument = auth()->user()->canModule('documents', 'link');
        abort_unless($canCreateDocument || $canLinkDocument, 403, 'Your role cannot add documents.');

        $this->pendingCompletionTaskId = null;
        $this->resetTaskDocumentModal();
        $this->taskDocumentSource = $canCreateDocument ? 'upload' : 'existing';
        $this->taskDocumentModalTaskId = $taskId;
        $this->showTaskDocumentModal = true;
    }

    public function requestTaskCompletionFile(int $taskId): void
    {
        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        abort_unless($service->canEditTask(auth()->user(), $task), 403);
        abort_if($task->inquiry->result, 422, 'Tasks on a closed Inquiry cannot change status.');

        // If a required submission was added by another request between renders,
        // complete immediately instead of opening an unnecessary modal.
        if (! $task->requires_submission || $task->documents()->exists()) {
            $service->updateTaskStatus($task, InquiryService::AUTO_COMPLETED_STATUS, auth()->user());
            $this->metrics = $service->metrics(auth()->user());
            return;
        }

        $canCreateDocument = auth()->user()->canModule('documents', 'create');
        $canLinkDocument = auth()->user()->canModule('documents', 'link');
        abort_unless($canCreateDocument || $canLinkDocument, 403, 'A required file is missing and your role cannot add documents.');
        $this->pendingCompletionTaskId = $taskId;
        $this->resetTaskDocumentModal();
        $this->taskDocumentSource = $canCreateDocument ? 'upload' : 'existing';
        $this->taskDocumentModalTaskId = $taskId;
        $this->showTaskDocumentModal = true;
    }

    public function closeTaskDocumentModal(): void
    {
        $this->showTaskDocumentModal = false;
        $this->pendingCompletionTaskId = null;
        $this->resetTaskDocumentModal();
        $this->resetValidation([
            'taskDocumentUpload',
            'taskExistingDocumentId',
            'taskDocumentNote',
        ]);
    }

    public function setTaskDocumentSource(string $source): void
    {
        abort_unless(in_array($source, ['upload', 'existing'], true), 422);
        if ($source === 'upload') {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
        } else {
            abort_unless(auth()->user()->canModule('documents', 'link'), 403);
        }

        $this->taskDocumentSource = $source;
        $this->taskDocumentUpload = null;
        $this->taskExistingDocumentId = null;
        $this->resetValidation(['taskDocumentUpload', 'taskExistingDocumentId']);
    }

    public function saveTaskDocument(): void
    {
        abort_unless($this->taskDocumentModalTaskId, 422);
        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), (int) $this->taskDocumentModalTaskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        abort_unless($service->canEditTask(auth()->user(), $task), 403);
        abort_if($task->inquiry->result, 422, 'Tasks on a closed Inquiry cannot receive documents.');

        $this->validate([
            'taskDocumentSource' => ['required', Rule::in(['upload', 'existing'])],
            'taskDocumentNote' => ['nullable', 'string', 'max:2000'],
        ]);

        $note = trim($this->taskDocumentNote);
        $note = $note !== '' ? $note : null;

        if ($this->taskDocumentSource === 'upload') {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
            $this->validate([
                'taskDocumentUpload' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai'],
            ]);
            $service->upload($task->inquiry, $this->taskDocumentUpload, auth()->user(), $task, $note);
        } else {
            abort_unless(auth()->user()->canModule('documents', 'link'), 403);
            $this->validate(['taskExistingDocumentId' => ['required', 'integer', 'exists:documents,id']]);
            $source = app(AccessControlService::class)
                ->applyDocumentScope(Document::query()->whereKey((int) $this->taskExistingDocumentId), auth()->user())
                ->firstOrFail();
            $service->linkExistingDocumentToTask($task, $source, auth()->user(), $note);
        }

        $completedAfterUpload = (int) ($this->pendingCompletionTaskId ?? 0) === (int) $task->id;
        if ($completedAfterUpload) {
            // The document now exists, so the normal service-level completion
            // guard succeeds and completed_at/status are written atomically.
            $task = $service->updateTaskStatus($task->fresh(), InquiryService::AUTO_COMPLETED_STATUS, auth()->user());
            $this->metrics = $service->metrics(auth()->user());
        }

        $this->showTaskDocumentModal = false;
        $this->pendingCompletionTaskId = null;
        $this->resetTaskDocumentModal();
        session()->flash('success', $completedAfterUpload
            ? 'Document added and '.$task->title.' completed.'
            : 'Document added to '.$task->title.'.');
    }

    public function uploadTaskFile(): void
    {
        $this->validate(['taskUpload' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai']]);
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), (int) $this->selectedTaskId, ['inquiry']);
        app(InquiryService::class)->upload($task->inquiry, $this->taskUpload, auth()->user(), $task);
        $this->taskUpload = null;
    }

    public function uploadQuickTaskFile(int $taskId): void
    {
        $upload = $this->taskQuickUploads[$taskId] ?? null;
        if (!$upload) return;
        $this->validate(["taskQuickUploads.$taskId" => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai']]);
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        app(InquiryService::class)->upload($task->inquiry, $upload, auth()->user(), $task);
        unset($this->taskQuickUploads[$taskId]);
    }

    public function updatedTaskQuickUploads($value, $key): void
    {
        if (!$value || !is_numeric($key)) return;
        $this->uploadQuickTaskFile((int) $key);
    }

    public function addTaskComment(): void
    {
        $this->validate(['taskComment' => ['required', 'string', 'max:60000']]);
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), (int) $this->selectedTaskId, ['inquiry']);
        app(InquiryService::class)->addTaskComment($task, trim($this->taskComment), auth()->user());
        $this->taskComment = '';
    }

    public function setInquiryActivityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['all', 'comments', 'history'], true), 422);
        $this->inquiryActivityTab = $tab;
        $this->resetPage('inquiryActivityPage');
    }

    public function updatedInquiryUploads(): void
    {
        if (count($this->inquiryUploads) === 0) {
            return;
        }

        // New upload and stored-document linking are mutually exclusive. Do not
        // persist from this lifecycle hook: the browser calls uploadInquiryFiles()
        // after Livewire reports that the temporary upload has actually finished.
        $this->showInquiryDocumentPicker = false;
        $this->inquiryExistingDocumentId = null;
        $this->resetValidation(['inquiryExistingDocumentId']);
    }

    public function toggleInquiryDocumentPicker(): void
    {
        abort_unless(auth()->user()->canModule('documents', 'link'), 403);

        $opening = ! $this->showInquiryDocumentPicker;
        if ($opening) {
            // Existing-document mode replaces Upload new, so clear any pending
            // temporary files instead of showing both link actions together.
            $this->inquiryUploads = [];
            $this->resetValidation(['inquiryUploads', 'inquiryUploads.*']);
        } else {
            $this->inquiryExistingDocumentId = null;
            $this->resetValidation(['inquiryExistingDocumentId']);
        }

        $this->showInquiryDocumentPicker = $opening;
    }

    public function attachExistingInquiryDocument(): void
    {
        abort_unless(auth()->user()->canModule('documents', 'link'), 403);
        $this->validate(['inquiryExistingDocumentId' => ['required', 'integer', 'exists:documents,id']]);
        $inquiry = $this->selectedInquiry();
        $source = app(AccessControlService::class)
            ->applyDocumentScope(Document::query()->whereKey((int) $this->inquiryExistingDocumentId), auth()->user())
            ->firstOrFail();
        app(InquiryService::class)->linkExistingDocument($inquiry, $source, auth()->user());
        $this->inquiryUploads = [];
        $this->inquiryExistingDocumentId = null;
        $this->showInquiryDocumentPicker = false;
        $this->resetValidation(['inquiryUploads', 'inquiryUploads.*', 'inquiryExistingDocumentId']);
        session()->flash('success', 'Stored document linked to this Inquiry.');
    }

    public function uploadInquiryFiles(): array
    {
        abort_unless(auth()->user()->canModule('documents', 'create'), 403);
        $this->resetValidation(['inquiryUploads', 'inquiryUploads.*']);
        $validator = validator(['inquiryUploads' => $this->inquiryUploads], [
            'inquiryUploads' => ['required','array','min:1'],
            'inquiryUploads.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai'],
        ], [
            'inquiryUploads.required' => 'Choose at least one file to upload.',
            'inquiryUploads.*.max' => 'The file is too large. Maximum file size is 20 MB.',
            'inquiryUploads.*.mimes' => 'Unsupported file type. Use PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT, CSV or AI.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $messages) {
                foreach ($messages as $message) $this->addError($key, $message);
            }
            return ['ok' => false, 'message' => $validator->errors()->first()];
        }

        $inquiry = $this->selectedInquiry();
        try {
            foreach ($this->inquiryUploads as $upload) {
                app(InquiryService::class)->upload($inquiry, $upload, auth()->user());
            }
        } catch (\Throwable $e) {
            report($e);
            $message = 'FlowTrack could not store this Inquiry attachment. Please try again.';
            $this->addError('inquiryUploads', $message);
            return ['ok' => false, 'message' => $message];
        }

        $this->inquiryUploads = [];
        $this->inquiryExistingDocumentId = null;
        $this->showInquiryDocumentPicker = false;
        $this->resetValidation(['inquiryUploads', 'inquiryUploads.*']);
        session()->flash('success', 'Attachment uploaded and linked to this Inquiry.');
        return ['ok' => true];
    }

    public function deleteInquiryDocument(int $documentId): void
    {
        app(InquiryService::class)->removeDocument($this->selectedInquiry(), $documentId, auth()->user());
        session()->flash('success', 'Inquiry attachment removed.');
    }

    public function openTaskLinkForm(int $taskId): void
    {
        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        abort_unless($service->canEditTask(auth()->user(), $task), 403);
        abort_if($task->inquiry->result, 422, 'Tasks on a closed Inquiry cannot receive links.');

        $this->taskLinkFormTaskId = $taskId;
        $this->taskLinkUrl = '';
        $this->resetValidation(['taskLinkUrl']);
    }

    public function cancelTaskLinkForm(): void
    {
        $this->taskLinkFormTaskId = null;
        $this->taskLinkUrl = '';
        $this->resetValidation(['taskLinkUrl']);
    }

    public function saveTaskLink(int $taskId): void
    {
        abort_unless((int) $this->taskLinkFormTaskId === $taskId, 422);

        $url = trim($this->taskLinkUrl);
        if ($url !== '' && ! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }
        $this->taskLinkUrl = $url;

        $this->validate([
            'taskLinkUrl' => ['required', 'string', 'max:2048', 'url'],
        ], [
            'taskLinkUrl.required' => 'Enter a link to add.',
            'taskLinkUrl.url' => 'Enter a valid website or file link.',
            'taskLinkUrl.max' => 'The link is too long.',
        ]);

        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        $service->addTaskLink($task, $this->taskLinkUrl, auth()->user());

        $this->taskLinkFormTaskId = null;
        $this->taskLinkUrl = '';
        $this->resetValidation(['taskLinkUrl']);
        session()->flash('success', 'Link added to '.$task->title.'.');
    }

    public function deleteTaskLink(int $taskId, int $linkId): void
    {
        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);
        $service->removeTaskLink($task, $linkId, auth()->user());
        session()->flash('success', 'Task link removed.');
    }

    public function deleteTaskDocument(int $taskId, int $documentId): void
    {
        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);

        $reopened = $service->removeTaskDocument($task, $documentId, auth()->user());
        $this->metrics = $service->metrics(auth()->user());
        session()->flash('success', $reopened
            ? 'Task attachment removed. The required-file task was reopened to In Progress.'
            : 'Task attachment removed.');
    }

    public function addInquiryComment(): void
    {
        $this->validate(['inquiryComment' => ['required', 'string', 'max:60000']]);
        app(InquiryService::class)->addInquiryComment($this->selectedInquiry(), trim($this->inquiryComment), auth()->user());
        $this->inquiryComment = '';
        $this->resetPage('inquiryActivityPage');
    }

    public function openAddTaskForm(): void
    {
        $inquiry = $this->selectedInquiry();
        abort_unless(app(AccessControlService::class)->canCreateInquiryTask(auth()->user(), $inquiry), 403);
        abort_if($inquiry->result, 422, 'A completed Inquiry cannot receive another task.');

        $this->loadUserOptions();
        $this->newTaskName = '';
        $this->newTaskDescription = '';
        $this->newTaskAssigneeId = auth()->id();
        $this->newTaskDueDate = app(WorkspaceSettingsService::class)->localToday()->addDays(3)->toDateString();
        $this->newTaskRequiresSubmission = false;
        $this->newTaskSubmissionLabel = '';
        $this->showAddTaskForm = true;
        $this->resetValidation();
    }

    public function cancelAddTask(): void
    {
        $this->showAddTaskForm = false;
        $this->newTaskName = '';
        $this->newTaskDescription = '';
        $this->newTaskAssigneeId = null;
        $this->newTaskDueDate = '';
        $this->newTaskRequiresSubmission = false;
        $this->newTaskSubmissionLabel = '';
        $this->resetValidation();
    }

    public function addInquiryTask(): void
    {
        abort_unless(app(AccessControlService::class)->canCreateInquiryTask(auth()->user(), $this->selectedInquiry()), 403);

        $data = $this->validate([
            'newTaskName' => ['required', 'string', 'max:255'],
            'newTaskDescription' => ['nullable', 'string', 'max:60000'],
            'newTaskAssigneeId' => ['nullable', 'exists:users,id'],
            'newTaskDueDate' => ['nullable', 'date'],
            'newTaskRequiresSubmission' => ['boolean'],
            'newTaskSubmissionLabel' => ['nullable', 'string', 'max:255'],
        ]);

        app(InquiryService::class)->appendTask($this->selectedInquiry(), [
            'name' => $data['newTaskName'],
            'description' => $data['newTaskDescription'] ?? null,
            'assignee_id' => $data['newTaskAssigneeId'] ?? null,
            'due_date' => $data['newTaskDueDate'] ?? null,
            'requires_submission' => (bool) ($data['newTaskRequiresSubmission'] ?? false),
            'submission_label' => $data['newTaskSubmissionLabel'] ?? null,
        ], auth()->user());

        $this->cancelAddTask();
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        session()->flash('success', 'Inquiry task added.');
    }

    public function openWorkflowManager(): void
    {
        $inquiry = $this->selectedInquiry(['tasks.assignee:id,name']);
        $service = app(InquiryService::class);
        foreach ($inquiry->tasks->filter(fn (InquiryTask $task) => !$task->completed_at) as $openTask) {
            abort_unless($service->canEditTask(auth()->user(), $openTask), 403, 'You do not have permission to manage the full Inquiry taskflow.');
        }
        $activeId = $inquiry->tasks->first(fn (InquiryTask $task) => !$task->completed_at)?->id;
        $this->managerRows = $inquiry->tasks->map(fn (InquiryTask $task) => [
            'id' => (int) $task->id,
            'source_id' => $task->source_task_pack_item_id ? (int) $task->source_task_pack_item_id : null,
            'name' => (string) $task->title,
            'description' => (string) ($task->description ?: ''),
            'assignee_id' => $task->assignee_id ? (int) $task->assignee_id : null,
            'setup_assignee_id' => $task->setup_assignee_id ? (int) $task->setup_assignee_id : null,
            'due_date' => $task->due_date?->toDateString() ?: '',
            'requires_submission' => (bool) $task->requires_submission,
            'submission_label' => (string) ($task->submission_label ?: ''),
            'state' => $task->completed_at ? 'completed' : ((int) $task->id === (int) $activeId ? 'active' : 'future'),
        ])->values()->all();
        $this->loadManagementOptions();
        $this->showWorkflowManager = true;
    }

    public function closeWorkflowManager(): void
    {
        $this->showWorkflowManager = false;
        $this->managerRows = [];
    }

    public function addManagerTask(): void
    {
        $row = $this->blankWorkflowRow();
        $row['state'] = 'future';
        $this->managerRows[] = $row;
    }

    public function appendManagerTemplate(): void
    {
        if (!$this->managerTemplateId) return;
        $inquiry = $this->selectedInquiry();
        $rows = app(InquiryService::class)->taskPackRows($this->managerTemplateId, $inquiry->received_date?->toDateString(), $inquiry->owner_id);
        foreach ($rows as $row) {
            $row['state'] = 'future';
            $this->managerRows[] = $row;
        }
    }

    public function removeManagerTask(int $index): void
    {
        $row = $this->managerRows[$index] ?? null;
        if (!$row || in_array($row['state'] ?? '', ['completed', 'active'], true)) return;
        array_splice($this->managerRows, $index, 1);
        $this->managerRows = array_values($this->managerRows);
    }

    public function moveManagerTask(int $index, int $direction): void
    {
        $target = $index + $direction;
        if ($target < 0 || $target >= count($this->managerRows)) return;
        $a = $this->managerRows[$index] ?? null;
        $b = $this->managerRows[$target] ?? null;
        if (!$a || !$b || ($a['state'] ?? '') !== 'future' || ($b['state'] ?? '') !== 'future') return;
        [$this->managerRows[$index], $this->managerRows[$target]] = [$this->managerRows[$target], $this->managerRows[$index]];
        $this->managerRows = array_values($this->managerRows);
    }

    public function saveWorkflow(): void
    {
        $this->validate([
            'managerRows' => ['required', 'array', 'min:1'],
            'managerRows.*.name' => ['required', 'string', 'max:255'],
            'managerRows.*.description' => ['nullable', 'string', 'max:60000'],
            'managerRows.*.assignee_id' => ['nullable', 'exists:users,id'],
            'managerRows.*.due_date' => ['nullable', 'date'],
            'managerRows.*.requires_submission' => ['boolean'],
            'managerRows.*.submission_label' => ['nullable', 'string', 'max:255'],
        ]);
        app(InquiryService::class)->saveWorkflow($this->selectedInquiry(), $this->managerRows, auth()->user());
        $this->showWorkflowManager = false;
        $this->managerRows = [];
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        session()->flash('success', 'Inquiry taskflow saved.');
    }

    public function convertToOrder(): void
    {
        $job = app(InquiryService::class)->convertToOrder($this->selectedInquiry(), auth()->user());
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        session()->flash('success', $job->displayOrderNumber().' created from Inquiry.');
    }

    public function markDead(): void
    {
        $this->validate([
            'deadReason' => ['required', 'string', 'max:255'],
            'deadNote' => ['nullable', 'string', 'max:2000'],
        ]);
        app(InquiryService::class)->markDead($this->selectedInquiry(), $this->deadReason, $this->deadNote, auth()->user());
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        session()->flash('success', 'Inquiry closed.');
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        if (!$this->showCreate) $this->metrics = app(InquiryService::class)->metrics(auth()->user());
    }

    public function render()
    {
        $user = auth()->user();
        if ($this->showCreate) return view('livewire.inquiries.index', $this->createPageData());
        if ($this->selectedInquiryId) return view('livewire.inquiries.index', $this->detailPageData($user));
        return view('livewire.inquiries.index', $this->listPageData($user));
    }

    private function listPageData(User $user): array
    {
        $service = app(InquiryService::class);
        $paginator = $service->paginate($user, [
            'search' => $this->search,
            'quick' => $this->quick,
            'hide_completed' => $this->hideCompleted,
        ], $this->perPage);
        return [
            'mode' => 'list',
            'inquiryPaginator' => $paginator,
            'inquiryRows' => $service->listRows($paginator, $user),
            'selectedInquiry' => null,
            'selectedTask' => null,
        ];
    }

    private function createPageData(): array
    {
        return [
            'mode' => 'create',
            'selectedInquiry' => null,
            'selectedTask' => null,
        ];
    }

    private function detailPageData(User $user): array
    {
        $service = app(InquiryService::class);
        $canViewTasks = $user->canModule('tasks', 'view')
            || Inquiry::query()->whereKey($this->selectedInquiryId)->where('created_by', $user->id)->exists();
        $with = [
            'client:id,name,logo_path',
            'creator:id,name,profile_image_path',
            'owner:id,name,profile_image_path',
            'convertedJob:id,job_number,order_number',
            'sourceWorkflow:id,name',
            'items:id,inquiry_id,category,item_name,quantity,unit,sort_order',
            'currentTask:id,inquiry_id,assignee_id,title,due_date,status,started_at,completed_at',
            'currentTask.assignee:id,name,profile_image_path',
        ];
        if ($this->detailTab === 'overview' && $canViewTasks) {
            // Overview owns the fully interactive Inquiry Taskflow. Load only tasks allowed by the Tasks matrix.
            $with['tasks'] = fn ($query) => app(AccessControlService::class)->applyInquiryTaskScope($query, $user)
                ->with([
                    'assignee:id,name,profile_image_path',
                    'documents:id,inquiry_id,inquiry_task_id,name,note,mime_type,created_at',
                    'links:id,inquiry_task_id,url,created_at',
                ])
                ->withCount(['documents', 'comments'])
                ->orderBy('sequence');
        }

        $inquiry = $service->visibleQuery($user)
            ->with($with)
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($q) => $q->whereNotNull('completed_at'),
                'documents',
            ])
            ->findOrFail($this->selectedInquiryId);

        if (!$canViewTasks) {
            $inquiry->setRelation('tasks', collect());
            $inquiry->setRelation('currentTask', null);
            $inquiry->setAttribute('tasks_count', 0);
            $inquiry->setAttribute('completed_tasks_count', 0);
        }

        // Documents and Activity remain part of Overview, but no longer have separate tabs.
        $documents = $this->detailTab === 'overview' && $user->canModule('documents', 'view') ? $service->documentsPage($user, $inquiry) : null;
        $activities = $this->detailTab === 'overview' ? $service->activityPage($user, $inquiry, 30, $this->inquiryActivityTab) : null;
        $mentionUsers = $this->detailTab === 'overview' ? app(MentionService::class)->optionsForCreate($user) : collect();
        $availableInquiryDocuments = $this->showInquiryDocumentPicker && $this->detailTab === 'overview'
            ? app(AccessControlService::class)->applyDocumentScope(Document::query(), $user)
                ->where('client_id', $inquiry->client_id)
                ->latest('id')
                ->limit(60)
                ->get(['id','name','flow_job_id','task_id','client_id'])
            : collect();
        $taskDocumentModalTask = $this->showTaskDocumentModal && $this->taskDocumentModalTaskId
            ? $inquiry->tasks->firstWhere('id', (int) $this->taskDocumentModalTaskId)
            : null;
        $availableTaskDocuments = $this->showTaskDocumentModal
            && $this->taskDocumentSource === 'existing'
            && $user->canModule('documents', 'link')
            ? app(AccessControlService::class)->applyDocumentScope(Document::query(), $user)
                ->where('client_id', $inquiry->client_id)
                ->latest('id')
                ->limit(80)
                ->get(['id', 'name', 'flow_job_id', 'task_id', 'client_id', 'size', 'mime_type'])
            : collect();
        // Inquiry task management is inline on the workflow tab. Avoid loading
        // task documents/comments into a modal on every task deep-link/render.
        $task = null;
        $canEditInquiry = $service->canEditVisible($user, $inquiry);
        // Keep overview editing aligned with the same current-task rule used by
        // the Inquiry list: furthest started open task, otherwise first queued.
        $activeTask = $inquiry->currentTask ?: $inquiry->tasks->first(fn (InquiryTask $row) => !$row->completed_at);
        $canEditActiveTask = $activeTask ? $service->canEditTask($user, $activeTask) : false;

        return [
            'mode' => 'detail',
            'selectedInquiry' => $inquiry,
            'selectedTask' => $task,
            'inquiryDocuments' => $documents,
            'inquiryActivities' => $activities,
            'inquiryMentionUsers' => $mentionUsers,
            'availableInquiryDocuments' => $availableInquiryDocuments,
            'taskDocumentModalTask' => $taskDocumentModalTask,
            'availableTaskDocuments' => $availableTaskDocuments,
            'canLinkDocuments' => $user->canModule('documents', 'link'),
            'canCreateDocuments' => $user->canModule('documents', 'create'),
            'canDeleteDocuments' => $user->canModule('documents', 'delete'),
            'canExportDocuments' => $user->canModule('documents', 'export'),
            'canAssignInquiry' => $user->canModule('inquiries', 'assign') || app(AccessControlService::class)->isInquiryCreator($user, $inquiry),
            'canEditInquiry' => $canEditInquiry,
            'canEditActiveTask' => $canEditActiveTask,
            'canAddInquiryTask' => app(AccessControlService::class)->canCreateInquiryTask($user, $inquiry) && !$inquiry->result,
            'inquiryPriorities' => $this->detailTab === 'overview' ? app(\App\Services\MasterDataService::class)->active('priority') : collect(),
            'canCreateOrder' => $user->canModule('jobs', 'create'),
            'selectedTaskIsActive' => false,
            'selectedTaskCanEdit' => false,
        ];
    }

    private function persistInquiry(bool $draft): void
    {
        // Product rows are optional. A completely blank row is treated as if the
        // user did not add a product, while a partially completed row is still
        // validated normally so incomplete catalogue data cannot be saved.
        $this->createProductRows = collect($this->createProductRows)
            ->map(fn (array $row): array => [
                'category' => trim((string) ($row['category'] ?? '')),
                'product' => trim((string) ($row['product'] ?? '')),
                'quantity' => $row['quantity'] ?? 1,
            ])
            ->filter(fn (array $row): bool => $row['category'] !== '' || $row['product'] !== '')
            ->values()
            ->all();

        $data = $this->validate([
            'clientId' => ['required', 'exists:clients,id'],
            'referenceNumber' => ['nullable', 'string', 'max:255'],
            'clientContact' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'requirementNotes' => ['nullable', 'string', 'max:60000'],
            'requestSource' => ['required', Rule::in(['Email', 'Phone', 'Other'])],
            'createReceivedDate' => ['required', 'date_format:Y-m-d'],
            'createOwnerId' => ['required', 'exists:users,id'],
            'createWorkflowId' => ['required', 'exists:workflow_templates,id'],
            'createProductRows' => ['array', 'max:25'],
            'createProductRows.*.category' => ['required', 'string', 'max:255'],
            'createProductRows.*.product' => ['required', 'string', 'max:255'],
            'createProductRows.*.quantity' => ['required', 'integer', 'min:1', 'max:999999999'],
            'createAttachments.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai'],
        ]);

        // Client is shared reference data for Inquiry creation. Fetch the
        // authorized active Client once and reuse it for contact validation.
        $selectedClient = app(\App\Services\ClientService::class)
            ->referenceQuery(auth()->user(), 'create-inquiry')
            ->where('is_active', true)
            ->find((int) $data['clientId']);
        if (! $selectedClient) {
            $this->addError('clientId', 'That client is no longer available.');
            return;
        }

        // Client contact is mandatory and must belong to the selected Client.
        // Multiple contacts are supported; the Inquiry stores the selected name
        // as its historical snapshot so later Client edits do not rewrite history.
        $allowedContacts = collect();
        if (Schema::hasTable('client_contacts')) {
            $allowedContacts = $selectedClient->contacts()->pluck('name')->map(fn ($name) => trim((string) $name))->filter();
        }
        if ($allowedContacts->isEmpty() && trim((string) ($selectedClient->contact_name ?? '')) !== '') {
            $allowedContacts = collect([trim((string) $selectedClient->contact_name)]);
        }
        $requestedContact = trim((string) $data['clientContact']);
        if ($requestedContact === '' || ! $allowedContacts->containsStrict($requestedContact)) {
            $this->addError('clientContact', 'Select a valid contact for this client.');
            return;
        }
        $data['clientContact'] = $requestedContact;

        // Assigned-to uses the same remote option source as the UI. Re-check it
        // on save so a stale/inactive user cannot be submitted by changing the
        // Livewire payload manually.
        $ownerAvailable = app(\App\Services\FilterOptionService::class)
            ->options(auth()->user(), 'users', 'create-inquiry', '', (int) $data['createOwnerId'], 20)
            ->contains(fn ($item) => (int) ($item['id'] ?? 0) === (int) $data['createOwnerId']);
        if (! $ownerAvailable) {
            $this->addError('createOwnerId', 'That assignee is no longer available.');
            return;
        }

        $workflowAvailable = WorkflowTemplate::query()
            ->where('workspace_id', app(\App\Services\WorkflowService::class)->workspaceId())
            ->where('is_active', true)
            ->availableFor('inquiries', (int) $data['clientId'])
            ->whereKey((int) $data['createWorkflowId'])
            ->exists();

        if (!$workflowAvailable) {
            $this->addError('createWorkflowId', 'That Workflow is not available for the selected client.');
            return;
        }

        $catalogOptions = app(\App\Services\FilterOptionService::class);
        $catalogInvalid = false;
        foreach ($data['createProductRows'] as $index => $row) {
            $category = trim((string) $row['category']);
            $product = trim((string) $row['product']);
            $categoryValid = $catalogOptions->options(auth()->user(), 'product-categories', 'create-inquiry', '', $category, 20)
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === $category);
            $productValid = $catalogOptions->options(auth()->user(), 'products', 'create-inquiry', '', $product, 20, ['category' => $category])
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === $product);

            if (! $categoryValid) {
                $catalogInvalid = true;
                $this->addError("createProductRows.$index.category", 'That product category is no longer available.');
            }
            if (! $productValid) {
                $catalogInvalid = true;
                $this->addError("createProductRows.$index.product", 'That product is not available for the selected category.');
            }
        }
        if ($catalogInvalid) return;

        $service = app(InquiryService::class);
        $canonicalRows = $service->workflowRows(
            (int) $data['createWorkflowId'],
            $data['createReceivedDate'],
        );
        if ($canonicalRows === []) {
            $this->addError('createWorkflowId', 'The selected Workflow has no active Task Pack tasks. Add Task Packs in Workflow Setup first.');
            return;
        }

        // Workflow Setup / Task Pack Setup remain the source of truth.
        // The create screen shows only the workflow summary; tasks are rebuilt canonically on save.
        $tasks = $canonicalRows;

        $inquiry = $service->create([
            'client_id' => $data['clientId'],
            'reference_number' => $data['referenceNumber'],
            'client_contact' => $data['clientContact'],
            'received_date' => $data['createReceivedDate'],
            'request_source' => $data['requestSource'],
            'subject' => $data['subject'],
            'requirement_notes' => $data['requirementNotes'],
            'target_price' => null,
            'currency' => 'USD',
            'required_delivery_date' => null,
            'priority' => 'Medium',
            'owner_id' => (int) $data['createOwnerId'],
            'initial_follow_up_date' => null,
            'items' => array_map(fn (array $row): array => [
                'category' => trim((string) $row['category']),
                'name' => trim((string) $row['product']),
                'quantity' => (int) $row['quantity'],
                'unit' => 'pcs',
            ], $data['createProductRows']),
            'tasks' => $tasks,
            'source_task_pack_id' => null,
            'source_workflow_template_id' => (int) $data['createWorkflowId'],
        ], auth()->user(), $draft);

        foreach ($this->createAttachments as $upload) app(InquiryService::class)->upload($inquiry, $upload, auth()->user());

        $this->showCreate = false;
        $this->selectedInquiryId = $inquiry->id;
        $this->detailTab = 'overview';
        $this->metrics = app(InquiryService::class)->metrics(auth()->user());
        $this->resetCreateForm();
        session()->flash('success', $draft ? 'Inquiry draft saved.' : $inquiry->inquiry_number.' created with its taskflow tasks.');
    }

    private function loadCreateOptions(): void
    {
        $user = auth()->user();
        $options = app(\App\Services\FilterOptionService::class);

        // Keep create rendering bounded: only a handful of initial selector
        // options are hydrated; searching is handled by the same remote
        // endpoint used by Create Order.
        $this->clientFilterOptions = $options->options($user, 'clients', 'create-inquiry', '', $this->clientId, 6)->all();

        $this->ownerFilterOptions = $options->options($user, 'users', 'create-inquiry', '', $this->createOwnerId, 6)->all();
        $this->createProductCategoryOptions = $options->options($user, 'product-categories', 'create-inquiry', '', null, 8)->all();
        $selectedOwner = collect($this->ownerFilterOptions)
            ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $this->createOwnerId);
        if ($selectedOwner) {
            $name = (string) ($selectedOwner['label'] ?? '');
            $this->selectedOwnerLabel = (int) $this->createOwnerId === (int) $user->id ? 'Me · '.$name : $name;
        }

        $this->refreshCreateWorkflowOptions();
    }

    private function refreshCreateWorkflowOptions(): void
    {
        if (!$this->showCreate) return;

        $user = auth()->user();
        $options = app(\App\Services\FilterOptionService::class);
        $constraints = ['client_id' => $this->clientId];

        $available = $options->options($user, 'workflows', 'create-inquiry', '', $this->createWorkflowId, 20, $constraints);
        $selected = $this->createWorkflowId
            ? $available->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $this->createWorkflowId)
            : null;

        if (!$selected) {
            // Workflow preference must come from setup configuration, not from
            // a hard-coded Workflow name. This keeps client-specific defaults
            // working after a Workflow is renamed (for example NEP).
            $preferredId = WorkflowTemplate::query()
                ->where('workspace_id', app(\App\Services\WorkflowService::class)->workspaceId())
                ->where('is_active', true)
                ->availableFor('inquiries', $this->clientId)
                ->orderByRaw("CASE WHEN client_availability = 'specific' THEN 0 ELSE 1 END")
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->value('id');

            $this->createWorkflowId = $preferredId ? (int) $preferredId : null;
            $selected = $this->createWorkflowId
                ? $options->options($user, 'workflows', 'create-inquiry', '', $this->createWorkflowId, 20, $constraints)
                    ->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $this->createWorkflowId)
                : null;
        }

        $this->workflowFilterOptions = $options
            ->options($user, 'workflows', 'create-inquiry', '', $this->createWorkflowId, 6, $constraints)
            ->all();

        if ($this->createWorkflowId) {
            $selected = collect($this->workflowFilterOptions)
                ->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $this->createWorkflowId)
                ?? $selected;
            $this->selectedWorkflowLabel = (string) ($selected['label'] ?? '');
            $summary = app(InquiryService::class)->workflowSummary($this->createWorkflowId);
            $this->createWorkflowTaskCount = (int) ($summary['tasks'] ?? 0);
            $this->createWorkflowPhaseCount = (int) ($summary['phases'] ?? 0);
        } else {
            $this->selectedWorkflowLabel = '';
            $this->createWorkflowTaskCount = 0;
            $this->createWorkflowPhaseCount = 0;
        }
    }

    private function loadUserOptions(): void
    {
        if ($this->userOptions !== []) return;

        $query = User::query()->where('is_active', true);
        $actor = auth()->user();
        $isCreator = $this->selectedInquiryId
            ? Inquiry::query()->whereKey($this->selectedInquiryId)->where('created_by', $actor->id)->exists()
            : false;
        if (! $isCreator && ! $actor->canModule('tasks', 'assign')) $query->whereKey($actor->id);

        $this->userOptions = $query
            ->orderBy('name')
            ->limit(250)
            ->get(['id', 'name'])
            ->map(fn (User $row) => ['id' => (int) $row->id, 'name' => (string) $row->name])
            ->all();
    }

    private function loadManagementOptions(): void
    {
        $this->loadUserOptions();
        $this->taskPackOptions = app(InquiryService::class)->taskPackOptions()->map(fn ($pack) => ['id' => (int) $pack->id, 'name' => (string) $pack->name])->all();
        $this->managerTemplateId ??= $this->taskPackOptions[0]['id'] ?? null;
    }

    private function resetTaskDocumentModal(): void
    {
        $this->taskDocumentModalTaskId = null;
        $this->taskDocumentSource = 'upload';
        $this->taskDocumentUpload = null;
        $this->taskExistingDocumentId = null;
        $this->taskDocumentNote = '';
    }

    private function selectedInquiry(array $with = []): Inquiry
    {
        abort_unless($this->selectedInquiryId, 404);
        return app(InquiryService::class)->findVisible(auth()->user(), $this->selectedInquiryId, $with);
    }

    private function hydrateTaskEditor(InquiryTask $task): void
    {
        $this->taskAssigneeId = $task->assignee_id;
        $this->taskDueDate = $task->due_date?->toDateString() ?: '';
        $this->taskStatus = in_array($task->status, InquiryService::WORKING_STATUSES, true) ? $task->status : 'In Progress';
    }

    private function blankWorkflowRow(): array
    {
        return [
            'id' => null,
            'source_id' => null,
            'name' => 'New Inquiry Task',
            'description' => 'Describe what must be completed for this task.',
            'assignee_id' => auth()->id(),
            'assignee_name' => (string) auth()->user()->name,
            'due_date' => app(WorkspaceSettingsService::class)->localToday()->addDays(3)->toDateString(),
            'requires_submission' => false,
            'submission_label' => '',
            'state' => 'future',
        ];
    }

    private function resetCreateCollections(): void
    {
        $this->createWorkflowTaskCount = 0;
        $this->createWorkflowPhaseCount = 0;
    }

    private function resetCreateForm(): void
    {
        $this->clientId = null;
        $this->clientContact = '';
        $this->clientContactOptions = [];
        $this->selectedClientLabel = '';
        $this->referenceNumber = '';
        $this->subject = '';
        $this->requirementNotes = '';
        $this->requestSource = 'Email';
        $this->createReceivedDate = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $this->createOwnerId = (int) auth()->id();
        $this->selectedOwnerLabel = 'Me · '.(string) auth()->user()->name;
        $this->ownerFilterOptions = [];
        $this->showCreateClientModal = false;
        $this->showCreateContactModal = false;
        $this->newContactName = '';
        $this->newContactEmail = '';
        $this->newContactPhone = '';
        $this->resetCreateClientModal();
        $this->createAttachments = [];
        $this->createProductRows = [];
        $this->createProductCategoryOptions = [];
        $this->createWorkflowId = null;
        $this->selectedWorkflowLabel = '';
        $this->resetCreateCollections();
    }

    private function canEditClientRecord(Client $client): bool
    {
        $access = app(AccessControlService::class);
        if ($access->isAdministrator(auth()->user()) || $access->canEditAll(auth()->user(), 'clients')) {
            return true;
        }

        return $access->canEditOwn(auth()->user(), 'clients')
            && (int) ($client->account_manager_id ?? 0) === (int) auth()->id();
    }

    private function resetCreateClientModal(): void
    {
        $this->newClientName = '';
        $this->newClientContactName = '';
        $this->newClientEmail = '';
        $this->newClientPhone = '';
        $this->newClientCountry = '';
        $this->useNewClientContactForInquiry = true;
    }

    private function nextClientCode(): string
    {
        $next = (int) Client::max('id') + 1;
        do {
            $code = 'CL-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $next++;
        } while (Client::where('code', $code)->exists());

        return $code;
    }

    private function tone(string $status): string
    {
        return match (true) {
            str_contains($status, 'Converted'), str_contains($status, 'Completed') => 'green',
            str_contains($status, 'Dead'), str_contains($status, 'Closed') => 'red',
            str_contains($status, 'Ready'), str_contains($status, 'On Hold') => 'amber',
            str_contains($status, 'Waiting') => 'purple',
            default => 'blue',
        };
    }
}
