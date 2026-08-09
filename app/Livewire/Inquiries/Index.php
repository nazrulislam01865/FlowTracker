<?php

namespace App\Livewire\Inquiries;

use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\Document;
use App\Models\Client;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\InquiryService;
use App\Services\MentionService;
use App\Services\WorkspaceSettingsService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $quick = 'all';
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
    public string $referenceNumber = '';
    public string $subject = '';
    public string $requirementNotes = '';
    public string $requestSource = 'Email';
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

    // Options are loaded only when create/workflow management is opened.
    public array $userOptions = [];
    public array $clientFilterOptions = [];
    public string $selectedClientLabel = '';
    public array $taskPackOptions = [];
    public array $workflowFilterOptions = [];

    // Detail actions.
    public array $inquiryUploads = [];
    public array $taskQuickUploads = [];
    public $taskUpload = null;
    public bool $showTaskDocumentModal = false;
    public ?int $taskDocumentModalTaskId = null;
    public string $taskDocumentSource = 'upload';
    public $taskDocumentUpload = null;
    public ?int $taskExistingDocumentId = null;
    public string $taskDocumentNote = '';
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

    public function setQuick(string $quick): void
    {
        abort_unless(in_array($quick, ['all', 'active', 'converted', 'dead'], true), 422);
        $this->quick = $quick;
        $this->resetPage('inquiryPage');
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->canModule('inquiries', 'create'), 403);
        $this->showCreate = true;
        $this->selectedInquiryId = null;
        $this->selectedTaskId = null;
        $this->createOwnerId ??= (int) auth()->id();
        $this->loadCreateOptions();
        $this->loadUserOptions();
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

    public function updatedClientId($value): void
    {
        if (!$this->showCreate || !$value) {
            $this->clientContact = '';
            return;
        }
        $client = app(\App\Services\ClientService::class)->visibleQuery(auth()->user())->find((int) $value);
        $this->clientContact = $client?->contact_name ?: '';
    }

    public function setCreateSelector(string $property, mixed $value): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('inquiries', 'create'), 403);

        $user = auth()->user();
        $raw = trim((string) $value);
        $options = app(\App\Services\FilterOptionService::class);

        if ($property === 'clientId') {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid option.');
            $id = (int) $raw;
            $selected = $options->options($user, 'clients', 'create-inquiry', '', $id, 20)
                ->first(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($selected, 422, 'That option is no longer available.');

            $this->clientId = $id;
            $this->selectedClientLabel = (string) ($selected['label'] ?? '');
            $this->resetValidation('clientId');
            $client = app(\App\Services\ClientService::class)->visibleQuery($user)
                ->where('is_active', true)
                ->find($id, ['id', 'contact_name']);
            $this->clientContact = (string) ($client?->contact_name ?: '');
            return;
        }

        if ($property === 'createWorkflowId') {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid Workflow.');
            $id = (int) $raw;
            $selected = $options->options($user, 'workflows', 'create-inquiry', '', $id, 20)
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


    public function saveDraft(): void { $this->persistInquiry(true); }
    public function createInquiry(): void { $this->persistInquiry(false); }

    public function openInquiry(int $id): void
    {
        app(InquiryService::class)->findVisible(auth()->user(), $id);
        $this->selectedInquiryId = $id;
        $this->showCreate = false;
        $this->detailTab = 'overview';
        $this->selectedTaskId = null;
        $this->showAddTaskForm = false;
        $this->resetPage('inquiryDocumentsPage');
        $this->resetPage('inquiryActivityPage');
    }

    public function closeInquiry(): void
    {
        $this->selectedInquiryId = null;
        $this->selectedTaskId = null;
        $this->showWorkflowManager = false;
        $this->showAddTaskForm = false;
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
        return ['ok' => true, 'status' => $saved->status, 'tone' => $this->tone($saved->status)];
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

        if ($field === 'requirement_notes') {
            $result['displayHtml'] = app(\App\Services\MentionService::class)
                ->render((string) ($saved->requirement_notes ?? ''));
        }

        return $result;
    }

    #[Renderless]
    public function updateInquiryStatus(string $status): array
    {
        $inquiry = $this->selectedInquiry();
        $saved = app(InquiryService::class)->updateStatus($inquiry, $status, auth()->user());
        return ['ok' => true, 'status' => $saved->status, 'tone' => $this->tone($saved->status)];
    }

    #[Renderless]
    public function updateTaskStatusInline(int $taskId, string $status): array
    {
        $task = app(InquiryService::class)->findVisibleTask(auth()->user(), $taskId);
        $saved = app(InquiryService::class)->updateTaskStatus($task, $status, auth()->user());
        $inquiryStatus = (string) $saved->inquiry()->value('status');
        return [
            'ok' => true,
            'status' => $saved->status,
            'inquiryStatus' => $inquiryStatus,
            'inquiryTone' => $this->tone($inquiryStatus),
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

        $saved = app(InquiryService::class)->updateTask($task, [
            'assignee_id' => $assigneeId,
            'due_date' => $task->due_date?->toDateString(),
            'status' => $task->status,
        ], auth()->user());

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
        abort_if($task->completed_at, 422, 'Completed tasks are locked.');

        $this->resetTaskDocumentModal();
        $this->taskDocumentModalTaskId = $taskId;
        $this->showTaskDocumentModal = true;
    }

    public function closeTaskDocumentModal(): void
    {
        $this->showTaskDocumentModal = false;
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
        if ($source === 'existing') {
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
        abort_if($task->completed_at, 422, 'Completed tasks are locked.');

        $this->validate([
            'taskDocumentSource' => ['required', Rule::in(['upload', 'existing'])],
            'taskDocumentNote' => ['nullable', 'string', 'max:2000'],
        ]);

        $note = trim($this->taskDocumentNote);
        $note = $note !== '' ? $note : null;

        if ($this->taskDocumentSource === 'upload') {
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

        $this->showTaskDocumentModal = false;
        $this->resetTaskDocumentModal();
        session()->flash('success', 'Document added to '.$task->title.'.');
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

    public function toggleInquiryDocumentPicker(): void
    {
        abort_unless(auth()->user()->canModule('documents', 'link'), 403);
        $this->showInquiryDocumentPicker = ! $this->showInquiryDocumentPicker;
        if (! $this->showInquiryDocumentPicker) $this->inquiryExistingDocumentId = null;
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
        $this->inquiryExistingDocumentId = null;
        $this->showInquiryDocumentPicker = false;
        session()->flash('success', 'Stored document linked to this Inquiry.');
    }

    public function uploadInquiryFiles(): void
    {
        $this->validate(['inquiryUploads.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai']]);
        $inquiry = $this->selectedInquiry();
        foreach ($this->inquiryUploads as $upload) app(InquiryService::class)->upload($inquiry, $upload, auth()->user());
        $this->inquiryUploads = [];
    }

    public function deleteInquiryDocument(int $documentId): void
    {
        app(InquiryService::class)->removeDocument($this->selectedInquiry(), $documentId, auth()->user());
        session()->flash('success', 'Inquiry attachment removed.');
    }

    public function deleteTaskDocument(int $taskId, int $documentId): void
    {
        $service = app(InquiryService::class);
        $task = $service->findVisibleTask(auth()->user(), $taskId, ['inquiry']);
        abort_unless((int) $task->inquiry_id === (int) $this->selectedInquiryId, 404);

        $service->removeTaskDocument($task, $documentId, auth()->user());
        session()->flash('success', 'Task attachment removed.');
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
        abort_unless(app(AccessControlService::class)->isAdministrator(auth()->user()), 403);
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
        abort_unless(app(AccessControlService::class)->isAdministrator(auth()->user()), 403);

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
        abort_unless(app(InquiryService::class)->canEdit(auth()->user(), $inquiry), 403);
        $activeId = $inquiry->tasks->first(fn (InquiryTask $task) => !$task->completed_at)?->id;
        $this->managerRows = $inquiry->tasks->map(fn (InquiryTask $task) => [
            'id' => (int) $task->id,
            'source_id' => $task->source_task_pack_item_id ? (int) $task->source_task_pack_item_id : null,
            'name' => (string) $task->title,
            'description' => (string) ($task->description ?: ''),
            'assignee_id' => $task->assignee_id ? (int) $task->assignee_id : null,
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
        $paginator = $service->paginate($user, ['search' => $this->search, 'quick' => $this->quick], $this->perPage);
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
        $with = [
            'client:id,name',
            'creator:id,name,profile_image_path',
            'convertedJob:id,job_number,order_number',
            'sourceWorkflow:id,name',
            'currentTask:id,inquiry_id,assignee_id,title,due_date,status,started_at,completed_at',
            'currentTask.assignee:id,name,profile_image_path',
        ];
        if ($this->detailTab === 'overview') {
            // Overview owns the fully interactive Inquiry Taskflow. Load its task graph once.
            $with['tasks'] = fn ($query) => $query
                ->with([
                    'assignee:id,name,profile_image_path',
                    'documents:id,inquiry_id,inquiry_task_id,name,note,created_at',
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

        // Documents and Activity remain part of Overview, but no longer have separate tabs.
        $documents = $this->detailTab === 'overview' ? $service->documentsPage($user, $inquiry) : null;
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
        $activeTask = $inquiry->tasks->first(fn (InquiryTask $row) => !$row->completed_at) ?: $inquiry->currentTask;
        $canEditActiveTask = $activeTask
            ? ($canEditInquiry || ((int) $activeTask->assignee_id === (int) $user->id && $user->canModule('inquiries', 'view')))
            : false;

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
            'canEditInquiry' => $canEditInquiry,
            'canEditActiveTask' => $canEditActiveTask,
            'canAddInquiryTask' => app(AccessControlService::class)->isAdministrator($user) && !$inquiry->result,
            'inquiryPriorities' => $this->detailTab === 'overview' ? app(\App\Services\MasterDataService::class)->active('priority') : collect(),
            'canCreateOrder' => $user->canModule('jobs', 'create'),
            'selectedTaskIsActive' => false,
            'selectedTaskCanEdit' => false,
        ];
    }

    private function persistInquiry(bool $draft): void
    {
        $data = $this->validate([
            'clientId' => ['required', 'exists:clients,id'],
            'referenceNumber' => ['nullable', 'string', 'max:255'],
            'clientContact' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'requirementNotes' => ['nullable', 'string', 'max:60000'],
            'requestSource' => ['required', Rule::in(['Email', 'Phone', 'Other'])],
            'createOwnerId' => ['required', 'exists:users,id'],
            'createWorkflowId' => ['required', 'exists:workflow_templates,id'],
            'createAttachments.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai'],
        ]);

        // A user may only create against a Client they can actually see.
        app(\App\Services\ClientService::class)->visibleQuery(auth()->user())->findOrFail((int) $data['clientId']);

        $service = app(InquiryService::class);
        $canonicalRows = $service->workflowRows(
            (int) $data['createWorkflowId'],
            app(WorkspaceSettingsService::class)->localToday()->toDateString(),
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
            'received_date' => app(WorkspaceSettingsService::class)->localToday()->toDateString(),
            'request_source' => $data['requestSource'],
            'subject' => $data['subject'],
            'requirement_notes' => $data['requirementNotes'],
            'target_price' => null,
            'currency' => 'USD',
            'required_delivery_date' => null,
            'priority' => 'Medium',
            'owner_id' => (int) $data['createOwnerId'],
            'initial_follow_up_date' => null,
            'items' => [],
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

        // New inquiries should start with the dedicated Inquiry Workflow
        // selected automatically. Keep any explicit/previous selection intact
        // (for example after validation) and only apply the default when the
        // create form has no workflow yet.
        if (! $this->createWorkflowId) {
            $inquiryWorkflow = $options
                ->options($user, 'workflows', 'create-inquiry', 'Inquiry', null, 20)
                ->sortBy(fn (array $item) => strcasecmp(trim((string) ($item['label'] ?? '')), 'Inquiry Workflow') === 0 ? 0 : 1)
                ->first();

            if ($inquiryWorkflow) {
                $this->createWorkflowId = (int) $inquiryWorkflow['id'];
                $this->selectedWorkflowLabel = (string) ($inquiryWorkflow['label'] ?? 'Inquiry Workflow');

                $summary = app(InquiryService::class)->workflowSummary($this->createWorkflowId);
                $this->createWorkflowTaskCount = (int) ($summary['tasks'] ?? 0);
                $this->createWorkflowPhaseCount = (int) ($summary['phases'] ?? 0);
            }
        }

        $this->workflowFilterOptions = $options->options($user, 'workflows', 'create-inquiry', '', $this->createWorkflowId, 6)->all();

        if ($this->clientId) {
            $selected = collect($this->clientFilterOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $this->clientId);
            $this->selectedClientLabel = (string) ($selected['label'] ?? $this->selectedClientLabel);
            $client = app(\App\Services\ClientService::class)->visibleQuery($user)
                ->where('is_active', true)
                ->find($this->clientId, ['id', 'contact_name']);
            $this->clientContact = (string) ($client?->contact_name ?: '');
        }


        if ($this->createWorkflowId) {
            $selected = collect($this->workflowFilterOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $this->createWorkflowId);
            $this->selectedWorkflowLabel = (string) ($selected['label'] ?? $this->selectedWorkflowLabel);
        }
    }

    private function loadUserOptions(): void
    {
        if ($this->userOptions !== []) return;

        $this->userOptions = User::query()
            ->where('is_active', true)
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
        $this->selectedClientLabel = '';
        $this->referenceNumber = '';
        $this->subject = '';
        $this->requirementNotes = '';
        $this->requestSource = 'Email';
        $this->createOwnerId = (int) auth()->id();
        $this->showCreateClientModal = false;
        $this->showCreateContactModal = false;
        $this->newContactName = '';
        $this->newContactEmail = '';
        $this->newContactPhone = '';
        $this->resetCreateClientModal();
        $this->createAttachments = [];
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
