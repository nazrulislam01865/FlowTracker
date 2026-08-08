<?php

namespace App\Livewire\Jobs;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\Document;
use App\Models\FlowJob;
use App\Models\FlowJobItem;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Services\AccessControlService;
use App\Services\ClientService;
use App\Services\DocumentService;
use App\Services\JobService;
use App\Services\MasterDataService;
use App\Services\TaskService;
use App\Services\WorkspaceSettingsService;
use App\Support\BoardLaneResolver;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class Index extends Component
{
    use UsesPagePlaceholder;
    use HandlesInlineEdits;
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $phase = '';
    public string $health = '';
    public string $client = '';
    public string $owner = '';
    public string $delivery = '';
    public string $invoice = '';
    public string $priorityFilter = '';
    public string $jobStatusFilter = '';
    public string $sort = 'updated_desc';
    public bool $showMoreFilters = false;
    public int $perPage = 25;
    public array $selectedJobIds = [];

    #[Url(as: 'open', history: true)]
    public ?int $selectedJobId = null;

    #[Url(as: 'task', history: true)]
    public ?int $selectedTaskId = null;
    public bool $taskEditMode = false;
    public string $detailTab = 'overview';
    public array $expandedPhaseIds = [];
    public string $jobTaskSearch = '';
    public bool $showCreate = false;
    public bool $createCatalogReady = false;
    public bool $createAssignmentReady = false;
    public bool $createWorkflowReady = false;

    public string $jobTitle = '';
    public string $priority = 'Medium';
    public ?int $clientId = null;
    public ?int $workflowId = null;
    public ?int $workflowPhaseId = null;
    public ?int $ownerId = null;
    public ?int $coordinatorId = null;
    public string $deliveryDate = '';
    public string $description = '';
    public array $jobItems = [];
    public array $jobAttachments = [];
    public array $jobDocumentUploads = [];
    public ?int $jobDocumentTaskId = null;
    public ?int $existingDocumentId = null;
    public bool $showDocumentPicker = false;

    public string $taskStatus = 'Ready';
    public ?int $taskAssigneeId = null;
    public int $taskProgress = 0;
    public bool $taskAttention = false;
    public string $taskAttentionReason = '';
    public string $taskComment = '';
    public string $newChecklistItem = '';
    public string $taskActivityTab = 'all';
    public int $taskActivityPage = 1;
    public string $jobComment = '';
    public string $jobActivityTab = 'all';
    public int $jobActivityPage = 1;
    public int $activityPerPage = 30;
    public array $taskDocumentUploads = [];
    public ?int $taskExistingDocumentId = null;
    public bool $showTaskDocumentPicker = false;

    public function mount(): void
    {
        $this->search = (string) request('search', '');
        $this->selectedJobId = request()->integer('open') ?: null;
        $this->selectedTaskId = request()->integer('task') ?: null;
        $this->showCreate = request()->boolean('create');

        if ($this->showCreate) {
            abort_unless(auth()->user()->canAccess('jobs.create'), 403);
            $this->selectedJobId = null;
            $this->selectedTaskId = null;
            $this->initializeCreateForm(request()->integer('client') ?: null);
            return;
        }

        if ($this->selectedTaskId) {
            $this->taskEditMode = true;
            $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
            $this->selectedJobId = $this->selectedJobId ?: $task->flow_job_id;
            $this->loadTaskForm($this->selectedTaskId);
        }

        if ($this->selectedJobId) $this->prepareSelectedJob($this->selectedJobId);
    }

    public function updatedWorkflowId(): void
    {
        if ($this->showCreate) $this->setDefaultStartPhase();
    }

    public function setCreateSelector(string $property, mixed $value): void
    {
        abort_unless($this->showCreate && auth()->user()->canAccess('jobs.create'), 403);

        $user = auth()->user();
        $raw = trim((string) $value);
        $options = app(\App\Services\FilterOptionService::class);

        if (in_array($property, ['clientId', 'ownerId', 'workflowId'], true)) {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid option.');
            $id = (int) $raw;
            $type = match ($property) {
                'clientId' => 'clients',
                'ownerId' => 'users',
                'workflowId' => 'workflows',
            };

            $valid = $options->options($user, $type, 'create-job', '', $id, 20)
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($valid, 422, 'That option is no longer available.');

            $this->{$property} = $id;
            $this->resetValidation($property);

            if ($property === 'workflowId') {
                $this->setDefaultStartPhase();
                $this->resetValidation('workflowPhaseId');
            }

            return;
        }

        if (preg_match('/^jobItems\.(\d+)\.(category|product)$/', $property, $matches) !== 1) {
            abort(422, 'Unsupported Create Order selector.');
        }

        $index = (int) $matches[1];
        $field = $matches[2];
        abort_unless(array_key_exists($index, $this->jobItems), 422, 'That product row is no longer available.');
        abort_unless($raw !== '', 422, 'Please choose a valid option.');

        $category = $field === 'product' ? trim((string) ($this->jobItems[$index]['category'] ?? '')) : '';
        $type = $field === 'category' ? 'product-categories' : 'products';
        $valid = $options->options(
            $user,
            $type,
            'create-job',
            '',
            $raw,
            20,
            $field === 'product' ? ['category' => $category] : [],
        )->contains(fn ($item) => (string) ($item['id'] ?? '') === $raw);
        abort_unless($valid, 422, 'That option is no longer available.');

        $this->jobItems[$index][$field] = $raw;
        $this->resetValidation("jobItems.$index.$field");

        if ($field === 'category') {
            // A Product is scoped to its category; changing the category must
            // invalidate the previous Product before the next render.
            $this->jobItems[$index]['product'] = '';
            $this->resetValidation("jobItems.$index.product");
        }
    }

    public function updatedJobItems(mixed $value, string $key): void
    {
        if (!$this->showCreate || !str_ends_with($key, '.category')) return;

        $index = (int) str($key)->before('.')->toString();
        if (!array_key_exists($index, $this->jobItems)) return;

        // A product belongs to the selected category. Clear any stale product
        // immediately so the remote selector cannot submit a value from the
        // previous category.
        $this->jobItems[$index]['product'] = '';
    }
    public function updatedSearch(): void { $this->resetJobSelection(); }
    public function updatedPhase(): void { $this->resetJobSelection(); }
    public function updatedHealth(): void { $this->resetJobSelection(); }
    public function updatedClient(): void { $this->resetJobSelection(); }
    public function updatedOwner(): void { $this->resetJobSelection(); }
    public function updatedDelivery(): void { $this->resetJobSelection(); }
    public function updatedInvoice(): void { $this->resetJobSelection(); }
    public function updatedPriorityFilter(): void { $this->resetJobSelection(); }
    public function updatedJobStatusFilter(): void { $this->resetJobSelection(); }
    public function updatedSort(): void { $this->resetJobSelection(); }

    public function clearFilters(): void
    {
        $this->reset(['search','phase','health','client','owner','delivery','invoice','priorityFilter','jobStatusFilter']);
        $this->sort = 'updated_desc';
        $this->resetJobSelection();
    }

    public function clearFilter(string $filter): void
    {
        $allowed = ['search','phase','health','client','owner','delivery','invoice','priorityFilter','jobStatusFilter'];
        abort_unless(in_array($filter, $allowed, true), 422);
        $this->{$filter} = '';
        $this->resetJobSelection();
    }

    public function toggleMoreFilters(): void { $this->showMoreFilters = !$this->showMoreFilters; }

    public function toggleSelectAllJobs(): void
    {
        $ids = app(JobService::class)->filteredIds(auth()->user(), $this->jobFilters());

        if ($ids->isNotEmpty() && count($this->selectedJobIds) === $ids->count()) {
            $selected = collect($this->selectedJobIds)->map(fn ($id) => (int) $id)->sort()->values();
            if ($selected->all() === $ids->sort()->values()->all()) {
                $this->selectedJobIds = [];
                return;
            }
        }

        $this->selectedJobIds = $ids->all();
    }

    public function bulkUpdateJobs(string $action): void
    {
        abort_unless(in_array($action, ['deactivate','cancel','delete'], true), 422);

        $ids = collect($this->selectedJobIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) return;

        $service = app(JobService::class);
        $actor = auth()->user();
        $jobs = $service->visibleQuery($actor)->whereIn('id', $ids)->get();

        foreach ($jobs as $job) {
            match ($action) {
                'deactivate' => $service->deactivate($job, $actor),
                'cancel' => $service->cancel($job, $actor),
                'delete' => $service->delete($job, $actor),
            };
        }

        $this->resetJobSelection();
        session()->flash('success', match ($action) {
            'deactivate' => 'Selected Orders deactivated.',
            'cancel' => 'Selected Orders cancelled.',
            'delete' => 'Selected Orders deleted.',
        });
    }
    public function openCreate(): void
    {
        abort_unless(auth()->user()->canAccess('jobs.create'), 403);
        $this->selectedJobId = null;
        $this->selectedTaskId = null;
        $this->showCreate = true;
        $this->initializeCreateForm();
    }

    public function closeCreate(): void
    {
        $this->resetCreateForm();
        $this->redirectRoute('jobs.index', navigate: true);
    }

    public function loadCreateSection(string $section): void
    {
        abort_unless($this->showCreate && auth()->user()->canAccess('jobs.create'), 403);

        if ($section === 'catalog') {
            $this->createCatalogReady = true;
            return;
        }

        if ($section === 'assignment') {
            $this->createCatalogReady = true;
            $this->ownerId ??= auth()->id();
            $this->coordinatorId ??= auth()->id();
            $this->createAssignmentReady = true;
            return;
        }

        if ($section === 'workflow') {
            $this->createCatalogReady = true;
            $this->createAssignmentReady = true;
            $this->ownerId ??= auth()->id();
            $this->coordinatorId ??= auth()->id();
            $this->workflowId ??= Workflow::where('is_snapshot', false)->where('is_active', true)->orderBy('id')->value('id');
            $this->setDefaultStartPhase();
            $this->createWorkflowReady = true;
            return;
        }

        abort(422, 'Unknown Create Order section.');
    }
    public function addProductRow(): void { $this->jobItems[] = ['category' => '', 'product' => '', 'quantity' => 1]; }
    public function removeProductRow(int $index): void { if (count($this->jobItems) <= 1) return; unset($this->jobItems[$index]); $this->jobItems = array_values($this->jobItems); }

    public function openJob(int $id): void
    {
        $this->selectedJobId = $id;
        $this->selectedTaskId = null;
        $this->taskEditMode = false;
        $this->detailTab = 'overview';
        $this->jobTaskSearch = '';
        $this->jobDocumentUploads = [];
        $this->jobDocumentTaskId = null;
        $this->existingDocumentId = null;
        $this->showDocumentPicker = false;
        $this->jobComment = '';
        $this->jobActivityTab = 'all';
        $this->jobActivityPage = 1;
        $this->prepareSelectedJob($id);
    }

    public function closeDrawer(): void
    {
        $this->selectedJobId = null;
        $this->selectedTaskId = null;
        $this->taskEditMode = false;
        $this->expandedPhaseIds = [];
        $this->jobTaskSearch = '';
        $this->jobDocumentUploads = [];
        $this->jobDocumentTaskId = null;
        $this->existingDocumentId = null;
        $this->showDocumentPicker = false;

        $this->redirectRoute('jobs.index', navigate: true);
    }

    public function setDetailTab(string $tab): void
    {
        abort_unless(in_array($tab, ['overview','workflow','documents'], true), 422);
        $this->detailTab = $tab;
        if ($tab === 'documents' && $this->selectedJobId) {
            $this->setDefaultDocumentTask();
        }
    }

    public function toggleJobPhase(int $phaseId): void
    {
        if (in_array($phaseId, $this->expandedPhaseIds, true)) {
            $this->expandedPhaseIds = array_values(array_filter($this->expandedPhaseIds, fn ($id) => (int) $id !== $phaseId));
        } else {
            $this->expandedPhaseIds[] = $phaseId;
            $this->expandedPhaseIds = array_values(array_unique(array_map('intval', $this->expandedPhaseIds)));
        }
    }

    public function expandAllJobPhases(): void
    {
        if (!$this->selectedJobId) return;
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $this->expandedPhaseIds = $job->workflow->phases->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    }

    public function collapseAllJobPhases(): void { $this->expandedPhaseIds = []; }

    public function createJob(): void { $this->persistJob(false); }
    public function saveDraft(): void { $this->persistJob(true); }

    private function persistJob(bool $draft): void
    {
        abort_unless(auth()->user()->canAccess('jobs.create'), 403);

        if (!$this->createCatalogReady || !$this->createAssignmentReady || !$this->createWorkflowReady) {
            $this->addError('createLoading', 'Please wait for the remaining Create Order fields to finish loading.');
            return;
        }

        $data = $this->validate([
            'jobTitle' => ['required','string','max:255'],
            'priority' => ['required','string','max:30'],
            'clientId' => ['required','exists:clients,id'],
            'workflowId' => ['required','exists:workflows,id'],
            'workflowPhaseId' => ['required','exists:workflow_phases,id'],
            'ownerId' => ['required','exists:users,id'],
            'coordinatorId' => ['nullable','exists:users,id'],
            'deliveryDate' => ['required','date'],
            'description' => ['nullable','string'],
            'jobItems' => ['required','array','min:1'],
            'jobItems.*.category' => ['required','string','max:255'],
            'jobItems.*.product' => ['required','string','max:255'],
            'jobItems.*.quantity' => ['required','integer','min:1'],
            'jobAttachments.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ]);

        if (count($this->jobAttachments) > 0) {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
        }

        $first = collect($data['jobItems'])->first();
        $job = app(JobService::class)->create([
            'title' => $data['jobTitle'],
            'product' => $first['product'],
            'category' => $first['category'],
            'quantity' => collect($data['jobItems'])->sum('quantity'),
            'items' => $data['jobItems'],
            'priority' => $data['priority'],
            'client_id' => $data['clientId'],
            'workflow_id' => $data['workflowId'],
            'workflow_phase_id' => $data['workflowPhaseId'],
            'owner_id' => $data['ownerId'],
            'coordinator_id' => $data['coordinatorId'] ?: $data['ownerId'],
            'delivery_date' => $data['deliveryDate'],
            'description' => $data['description'],
            'draft' => $draft,
        ], auth()->user());

        foreach ($this->jobAttachments as $upload) {
            app(\App\Services\DocumentService::class)->store($upload, [
                'flow_job_id' => $job->id,
                'client_id' => $job->client_id,
                'category' => 'Order attachment',
            ], auth()->user());
        }

        $this->showCreate = false;
        $this->resetCreateForm();
        $this->selectedJobId = $job->id;
        $this->detailTab = 'overview';
        $this->prepareSelectedJob($job->id);
        session()->flash('success', $draft ? 'Order draft saved.' : 'Order created and all configured Workflow Task Packs were loaded.');
    }

    #[Renderless]
    public function updateJobOwner(int $jobId, mixed $ownerId): array
    {
        return $this->persistInlineEdit('Order owner', function () use ($jobId, $ownerId) {
            abort_unless(auth()->user()->canModule('jobs','assign'), 403);
            $ownerId = $ownerId === '' ? null : (int) $ownerId;
            if ($ownerId) User::where('is_active', true)->findOrFail($ownerId);
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateOwner($job, $ownerId, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobCoordinator(int $jobId, mixed $coordinatorId): array
    {
        return $this->persistInlineEdit('Order coordinator', function () use ($jobId, $coordinatorId) {
            abort_unless(auth()->user()->canModule('jobs','assign'), 403);
            $coordinatorId = $coordinatorId === '' ? null : (int) $coordinatorId;
            if ($coordinatorId) User::where('is_active', true)->findOrFail($coordinatorId);
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateCoordinator($job, $coordinatorId, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobDeliveryDate(int $jobId, mixed $date): array
    {
        return $this->persistInlineEdit('delivery date', function () use ($jobId, $date) {
            abort_unless(auth()->user()->canAccess('jobs.update'), 403);
            $date = trim((string) $date);
            if ($date !== '') validator(['date' => $date], ['date' => ['date']])->validate();
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateDeliveryDate($job, $date ?: null, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobPriority(int $jobId, mixed $priority): array
    {
        return $this->persistInlineEdit('priority', function () use ($jobId, $priority) {
            abort_unless(auth()->user()->canAccess('jobs.update'), 403);
            $priority = trim((string) $priority);
            abort_if($priority === '', 422, 'Priority is required.');
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updatePriority($job, $priority, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobHealth(int $jobId, mixed $health): array
    {
        return $this->persistInlineEdit('Order health', function () use ($jobId, $health) {
            abort_unless(auth()->user()->canAccess('jobs.update'), 403);
            $health = trim((string) $health);
            abort_if($health === '', 422, 'Health is required.');
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateHealth($job, $health, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobTextField(int $jobId, string $field, mixed $value): array
    {
        $label = $field === 'title' ? 'Order name' : 'Order description';

        return $this->persistInlineEdit($label, function () use ($jobId, $field, $value) {
            abort_unless(auth()->user()->canAccess('jobs.update'), 403);
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateTextField($job, $field, (string) $value, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobItem(int $itemId, string $field, mixed $value): array
    {
        $label = match ($field) {
            'category_name' => 'product category',
            'product_name' => 'product',
            'quantity' => 'quantity',
            default => 'product detail',
        };

        return $this->persistInlineEdit($label, function () use ($itemId, $field, $value) {
            $user = auth()->user();
            abort_unless($user->canAccess('jobs.update'), 403);
            abort_unless($this->selectedJobId, 422);

            $job = app(JobService::class)->findVisible($user, $this->selectedJobId);
            $item = FlowJobItem::where('flow_job_id', $job->id)->findOrFail($itemId);

            if ($field === 'category_name') {
                abort_unless(app(MasterDataService::class)->active('product_category')->contains('name', (string) $value), 422, 'Select a valid active product category.');
            }
            if ($field === 'product_name') {
                abort_if(blank($item->category_name), 422, 'Select a product category first.');
                $validProduct = app(\App\Services\FilterOptionService::class)
                    ->options($user, 'products', 'job-detail', '', (string) $value, 20, [
                        'category' => (string) $item->category_name,
                    ])
                    ->contains(fn ($option) => (string) ($option['id'] ?? '') === (string) $value);
                abort_unless($validProduct, 422, 'Select a valid active product for this category.');
            }

            app(JobService::class)->updateItem($job, $item, $field, $value, $user);
        });
    }

    public function addJobItem(int $jobId): void
    {
        $user = auth()->user();
        abort_unless($user->canAccess('jobs.update'), 403);
        $job = app(JobService::class)->findVisible($user, $jobId);

        // Keep a single unfinished row at a time so repeated clicks cannot
        // accumulate invisible/partial product records.
        if ($job->items()->where(fn ($query) => $query
            ->whereNull('product_name')
            ->orWhere('product_name', '')
        )->exists()) {
            return;
        }

        // Add an intentionally blank draft row. The Job Details view renders
        // category, product, and quantity as editable controls immediately so
        // the user chooses the actual values instead of receiving master-data defaults.
        app(JobService::class)->addItem($job, '', '', 1, $user);
    }

    public function removeJobItem(int $itemId): void
    {
        abort_unless(auth()->user()->canAccess('jobs.update'), 403);
        abort_unless($this->selectedJobId, 422);
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $item = FlowJobItem::where('flow_job_id', $job->id)->findOrFail($itemId);
        app(JobService::class)->removeItem($job, $item, auth()->user());
    }

    #[Renderless]
    public function updateTaskAssigneeFromJob(int $taskId, mixed $assigneeId): array
    {
        return $this->persistInlineEdit('task assignee', function () use ($taskId, $assigneeId) {
            abort_unless(auth()->user()->canModule('tasks','assign'), 403);
            abort_unless($this->selectedJobId, 422);
            $task = Task::where('flow_job_id', $this->selectedJobId)->findOrFail($taskId);
            $assigneeId = $assigneeId === '' ? null : (int) $assigneeId;
            if ($assigneeId) User::where('is_active', true)->findOrFail($assigneeId);
            app(TaskService::class)->updateDetailField($task, 'assignee_id', $assigneeId, auth()->user());
        });
    }

    #[Renderless]
    public function updateTaskDueDateFromJob(int $taskId, mixed $date): array
    {
        return $this->persistInlineEdit('task due date', function () use ($taskId, $date) {
            abort_unless(auth()->user()->canAccess('tasks.update') || auth()->user()->canAccess('jobs.update'), 403);
            abort_unless($this->selectedJobId, 422);
            $task = Task::where('flow_job_id', $this->selectedJobId)->findOrFail($taskId);
            $date = trim((string) $date);
            if ($date !== '') validator(['date' => $date], ['date' => ['date']])->validate();
            app(TaskService::class)->updateDueDate($task, $date ?: null, auth()->user());
        });
    }

    public function updatedJobDocumentUploads(): void
    {
        // Files remain in Livewire temporary storage until the user confirms
        // “Upload & link”. On Overview there is no separate requirement selector,
        // so use the same default/missing Task Pack requirement chosen by Documents.
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*']);
        if ($this->selectedJobId && count($this->jobDocumentUploads) > 0 && !$this->jobDocumentTaskId) {
            $this->setDefaultDocumentTask();
        }
    }

    public function uploadJobDocuments(): void
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        abort_unless($this->selectedJobId, 422);
        $this->validate([
            'jobDocumentTaskId' => ['required','integer','exists:tasks,id'],
            'jobDocumentUploads' => ['required','array','min:1'],
            'jobDocumentUploads.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ]);

        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $task = $job->tasks->firstWhere('id', (int) $this->jobDocumentTaskId);
        abort_unless($task && ($task->document_category_id || $task->setupTemplate?->document_category_id), 422, 'Select a Task Pack document requirement for this Order.');

        foreach ($this->jobDocumentUploads as $upload) {
            app(\App\Services\DocumentService::class)->store($upload, [
                'flow_job_id' => $job->id,
                'client_id' => $job->client_id,
                'task_id' => $task->id,
                'require_task_pack_requirement' => true,
            ], auth()->user());
        }

        $this->jobDocumentUploads = [];
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*', 'jobDocumentTaskId']);

        // Re-read the Job immediately so the linked file and requirement count
        // are visible in the same Livewire response. Then move the selector to
        // the next missing Task Pack document, if there is one.
        $fresh = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $missing = \App\Support\JobDetailPresenter::requiredDocuments($fresh)
            ->first(fn ($requirement) => !$requirement->complete);
        $this->jobDocumentTaskId = $missing?->task?->id ?: $task->id;

        session()->flash('success', 'Document uploaded and linked to '.$task->title.'.');
    }

    public function attachExistingDocument(): void
    {
        abort_unless(auth()->user()->canModule('documents','link'), 403);
        abort_unless($this->selectedJobId, 422);
        $this->validate([
            'jobDocumentTaskId' => ['required','integer','exists:tasks,id'],
            'existingDocumentId' => ['required','integer','exists:documents,id'],
        ]);

        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $task = $job->tasks->firstWhere('id', (int) $this->jobDocumentTaskId);
        abort_unless($task && ($task->document_category_id || $task->setupTemplate?->document_category_id), 422, 'Select a Task Pack document requirement for this Order.');
        $source = Document::findOrFail((int) $this->existingDocumentId);
        abort_unless((int) $source->client_id === (int) $job->client_id, 403, 'The selected document does not belong to this client.');
        app(\App\Services\DocumentService::class)->linkExisting($source, $task, auth()->user());
        $this->existingDocumentId = null;
        $this->showDocumentPicker = false;
        session()->flash('success', 'Existing document linked to the selected Task Pack task.');
    }

    public function deleteJobDocument(int $documentId): void
    {
        abort_unless(auth()->user()->canModule('documents','delete'), 403);
        abort_unless($this->selectedJobId, 422);
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $document = Document::where('flow_job_id', $job->id)->findOrFail($documentId);
        app(\App\Services\DocumentService::class)->delete($document, auth()->user());
    }

    public function toggleDocumentPicker(): void
    {
        $this->showDocumentPicker = !$this->showDocumentPicker;
    }

    #[Renderless]
    public function updateTaskStatusFromJob(int $taskId, mixed $status): array
    {
        return $this->persistInlineEdit('task status', function () use ($taskId, $status) {
            abort_unless(auth()->user()->canAccess('tasks.update'), 403);
            abort_unless($this->selectedJobId, 422);
            $status = trim((string) $status);
            abort_if($status === '', 422, 'Task status is required.');

            $task = Task::where('flow_job_id', $this->selectedJobId)->findOrFail($taskId);
            app(TaskService::class)->moveStatus($task, $status, auth()->user());
        });
    }

    public function completePhase(): void
    {
        abort_unless(auth()->user()->canAccess('jobs.update'), 403);
        if (!$this->selectedJobId) return;
        try {
            $job = app(JobService::class)->completePhase(app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId), auth()->user());
            $this->expandedPhaseIds = $job->phase ? [(int) $job->phase->id] : [];
            session()->flash('success', 'Phase completed and the next configured phase is active.');
        } catch (Throwable $e) {
            $this->addError('phaseCompletion', $e->getMessage());
        }
    }

    public function openTask(int $id): void
    {
        // Preserve the original task-detail behavior: opening a task normally
        // exposes inline editing when the current user has edit permission.
        $this->openTaskWithMode($id, true);
    }

    public function viewTask(int $id): void
    {
        // Explicit View from the overview action menu remains read-only.
        $this->openTaskWithMode($id, false);
    }

    public function editTask(int $id): void
    {
        $this->openTaskWithMode($id, true);
    }

    private function openTaskWithMode(int $id, bool $editMode): void
    {
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($id);
        if ($editMode) {
            abort_unless(app(AccessControlService::class)->canEditVisibleTask(auth()->user(), $task), 403);
        }

        $this->selectedJobId = (int) $task->flow_job_id;
        $this->selectedTaskId = $task->id;
        $this->taskEditMode = $editMode;
        $this->taskDocumentUploads = [];
        $this->taskExistingDocumentId = null;
        $this->showTaskDocumentPicker = false;
        $this->loadTaskForm($id);
    }

    public function deleteTaskFromJob(int $id): void
    {
        abort_unless($this->selectedJobId, 422);
        $actor = auth()->user();
        abort_unless(app(AccessControlService::class)->can($actor, 'tasks', 'delete'), 403);

        $task = app(TaskService::class)->visibleQuery($actor)
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($id);
        $job = app(JobService::class)->findVisible($actor, $this->selectedJobId);
        $title = (string) $task->title;
        $taskNumber = (string) ($task->task_number ?? '');

        $task->delete();
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.task_deleted',
            'description' => 'Task deleted: '.$title,
            'meta' => ['task_id' => $id, 'task_number' => $taskNumber, 'title' => $title],
        ]);
        app(JobService::class)->recalculateProgress($job->refresh());

        if ((int) $this->selectedTaskId === $id) {
            $this->closeTask();
        }
    }

    public function closeTask(): void { $this->selectedTaskId = null; $this->taskEditMode = false; $this->taskComment = ''; $this->newChecklistItem = ''; $this->taskActivityTab = 'all'; $this->taskActivityPage = 1; $this->taskDocumentUploads = []; $this->taskExistingDocumentId = null; $this->showTaskDocumentPicker = false; }
    public function markTaskComplete(): void
    {
        abort_unless($this->selectedTaskId, 422);
        $this->resetErrorBag('taskCompletion');

        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['documents','documentCategory','setupTemplate.documentCategory'])
            ->findOrFail($this->selectedTaskId);
        abort_unless(app(\App\Services\AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        app(TaskService::class)->moveStatus($task, 'Completed', auth()->user());
        $this->loadTaskForm($task->id);
        session()->flash('success', 'Task marked complete.');
    }

    public function addTaskComment(): void
    {
        if (!$this->selectedTaskId || trim($this->taskComment) === '') return;
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        app(TaskService::class)->addComment($task, $this->taskComment, auth()->user());
        $this->taskComment = '';
        $this->taskActivityPage = 1;
    }

    public function setTaskActivityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['all','comments','history'], true), 422);
        $this->taskActivityTab = $tab;
        $this->taskActivityPage = 1;
    }

    public function setTaskActivityPage(int $page): void
    {
        $this->taskActivityPage = max(1, $page);
    }

    #[Renderless]
    public function updateSelectedTaskField(string $field, mixed $value): array
    {
        $labels = [
            'title' => 'task name',
            'assignee_id' => 'task assignee',
            'status' => 'task status',
            'priority' => 'task priority',
            'start_date' => 'task start date',
            'due_date' => 'task due date',
            'description' => 'task description',
        ];

        $updatedTask = null;
        $result = $this->persistInlineEdit($labels[$field] ?? 'task field', function () use ($field, $value, &$updatedTask) {
            if ($field === 'assignee_id') abort_unless(auth()->user()->canModule('tasks','assign'), 403);
            else abort_unless(auth()->user()->canAccess('tasks.update'), 403);
            abort_unless($this->selectedTaskId, 422);
            $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
            if ($field === 'assignee_id' && filled($value)) User::where('is_active', true)->findOrFail((int) $value);
            if (in_array($field, ['start_date','due_date'], true) && filled($value)) validator(['date'=>$value], ['date'=>['date']])->validate();
            $updatedTask = app(TaskService::class)->updateDetailField($task, $field, $value, auth()->user());

            if ($field === 'status') $this->taskStatus = (string) $updatedTask->status;
            if ($field === 'assignee_id') $this->taskAssigneeId = $updatedTask->assignee_id ? (int) $updatedTask->assignee_id : null;
        });

        if ($field === 'status' && ($result['ok'] ?? false) && $updatedTask) {
            $timezone = app(WorkspaceSettingsService::class)->displayTimezone();
            $completedLocal = $updatedTask->completed_at?->copy()->timezone($timezone);
            $this->dispatch('task-completion-updated',
                completedDate: $completedLocal?->format('M j, Y') ?? '—',
                completedTime: $completedLocal?->format('g:i A') ?? ''
            );
        }

        return $result;
    }

    public function addTaskChecklistItem(): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        abort_unless($this->selectedTaskId, 422);
        $this->validate(['newChecklistItem'=>['required','string','max:255']]);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        app(TaskService::class)->addChecklistItem($task, $this->newChecklistItem, auth()->user());
        $this->newChecklistItem = '';
    }

    public function toggleTaskChecklistItem(int $itemId, bool $completed): void
    {
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with('checklistItems')->findOrFail($this->selectedTaskId);
        abort_unless(app(\App\Services\AccessControlService::class)->canEditTask(auth()->user(), $task), 403, 'Only the assigned person or an authorised administrator can complete checklist items.');
        $item = $task->checklistItems->firstWhere('id', $itemId);
        abort_unless($item, 404);
        app(TaskService::class)->toggleChecklistItem($task, $item, $completed, auth()->user());
    }

    public function deleteTaskChecklistItem(int $itemId): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with('checklistItems')->findOrFail($this->selectedTaskId);
        $item = $task->checklistItems->firstWhere('id', $itemId);
        abort_unless($item, 404);
        app(TaskService::class)->deleteChecklistItem($task, $item, auth()->user());
    }

    public function setJobActivityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['all','comments','history'], true), 422);
        $this->jobActivityTab = $tab;
        $this->jobActivityPage = 1;
    }

    public function setJobActivityPage(int $page): void
    {
        $this->jobActivityPage = max(1, $page);
    }

    public function addJobComment(): void
    {
        abort_unless($this->selectedJobId, 422);
        $body = trim($this->jobComment);
        if ($body === '') return;
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        abort_unless(app(\App\Services\AccessControlService::class)->canEditJob(auth()->user(), $job), 403);
        $actor = auth()->user();
        $mentionIds = app(\App\Services\MentionService::class)->userIdsFromText($body);
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.comment',
            'description' => $body,
            'meta' => ['body' => $body, 'mention_user_ids' => $mentionIds],
        ]);
        app(\App\Services\NotificationService::class)->notifyJobParticipants(
            $job,
            'New comment on '.$job->displayOrderNumber(),
            $body,
            'comment',
            $actor,
            [],
            $mentionIds,
        );
        app(\App\Services\NotificationService::class)->notifyMentionedUsers(
            $mentionIds,
            $actor->name.' mentioned you in '.$job->displayOrderNumber(),
            $body,
            $job,
            null,
            $actor,
        );
        $this->jobComment = '';
        $this->jobActivityPage = 1;
    }

    public function uploadSelectedTaskDocuments(): void
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with(['job','documentCategory','setupTemplate.documentCategory'])->findOrFail($this->selectedTaskId);
        $this->validate([
            'taskDocumentUploads' => ['required','array','min:1'],
            'taskDocumentUploads.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ]);
        foreach ($this->taskDocumentUploads as $upload) {
            app(\App\Services\DocumentService::class)->store($upload, ['flow_job_id'=>$task->flow_job_id,'client_id'=>$task->job?->client_id,'task_id'=>$task->id,'category'=>'Task attachment'], auth()->user());
        }
        $this->taskDocumentUploads = [];
        session()->flash('success', 'Attachment uploaded and linked to this Task Pack task.');
    }

    public function attachExistingToSelectedTask(): void
    {
        abort_unless(auth()->user()->canModule('documents','link'), 403);
        abort_unless($this->selectedTaskId, 422);
        $this->validate(['taskExistingDocumentId'=>['required','integer','exists:documents,id']]);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with(['job','documentCategory','setupTemplate.documentCategory'])->findOrFail($this->selectedTaskId);
        $source = Document::findOrFail((int)$this->taskExistingDocumentId);
        abort_unless((int) $source->client_id === (int) $task->job?->client_id, 403, 'The selected document does not belong to this client.');
        app(\App\Services\DocumentService::class)->linkExisting($source, $task, auth()->user(), true);
        $this->taskExistingDocumentId = null;
        $this->showTaskDocumentPicker = false;
        session()->flash('success', 'Stored document linked to this task.');
    }

    public function deleteSelectedTaskDocument(int $documentId): void
    {
        abort_unless(auth()->user()->canModule('documents','delete'), 403);
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        $document = Document::where('task_id',$task->id)->findOrFail($documentId);
        app(\App\Services\DocumentService::class)->delete($document, auth()->user());
    }

    public function toggleTaskDocumentPicker(): void
    {
        $this->showTaskDocumentPicker = !$this->showTaskDocumentPicker;
    }

    public function setTaskFlag(string $flag): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        abort_unless($this->selectedTaskId, 422);

        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        $flag = trim($flag);

        if ($flag !== '') {
            $allowed = app(MasterDataService::class)->active('task_flag')->pluck('name')->map(fn ($name) => trim((string) $name));
            $currentLegacyFlag = $task->needs_attention ? trim((string) $task->attention_reason) : '';
            abort_unless($allowed->contains($flag) || ($currentLegacyFlag !== '' && $currentLegacyFlag === $flag), 422, 'Select a valid Task Flag.');
        }

        $updated = app(TaskService::class)->setAttentionFlag($task, $flag !== '' ? $flag : null, auth()->user());
        $this->taskAttention = (bool) $updated->needs_attention;
        $this->taskAttentionReason = (string) $updated->attention_reason;
    }

    public function toggleTaskAttention(): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        if (!$this->selectedTaskId) return;
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        $flag = $task->needs_attention ? null : (trim((string) $task->attention_reason) ?: 'Management attention');
        $updated = app(TaskService::class)->setAttentionFlag($task, $flag, auth()->user());
        $this->taskAttention = (bool) $updated->needs_attention;
        $this->taskAttentionReason = (string) $updated->attention_reason;
    }

    public function saveTask(): void
    {
        abort_unless(auth()->user()->canAccess('tasks.update'), 403);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        $this->validate([
            'taskStatus' => ['required','string','max:50'],
            'taskAssigneeId' => ['nullable','exists:users,id'],
            'taskProgress' => ['required','integer','between:0,100'],
            'taskAttention' => ['boolean'],
            'taskAttentionReason' => [Rule::requiredIf($this->taskAttention || $this->taskStatus === 'Blocked'),'nullable','string','max:1000'],
        ]);
        app(TaskService::class)->update($task, [
            'status' => $this->taskStatus,
            'assignee_id' => $this->taskAssigneeId,
            'progress' => $this->taskProgress,
            'needs_attention' => $this->taskAttention,
            'attention_reason' => $this->taskAttentionReason,
        ], auth()->user());
        if (trim($this->taskComment) !== '') app(TaskService::class)->addComment($task, $this->taskComment, auth()->user());
        session()->flash('success', 'Task update saved.');
        $this->selectedTaskId = null;
        $this->taskComment = '';
    }

    private function loadTaskForm(int $id): void
    {
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($id);
        $this->taskStatus = $task->status;
        $this->taskAssigneeId = $task->assignee_id;
        $this->taskProgress = $task->progress;
        $this->taskAttention = $task->needs_attention;
        $this->taskAttentionReason = (string) $task->attention_reason;
        $this->taskComment = '';
        $this->newChecklistItem = '';
        $this->taskActivityTab = 'all';
        $this->taskActivityPage = 1;
    }

    private function setDefaultStartPhase(): void
    {
        if (!$this->workflowId) {
            $this->workflowPhaseId = null;
            return;
        }

        $this->workflowPhaseId = WorkflowPhase::where('workflow_id', $this->workflowId)
            ->where('is_active', true)
            ->where('allow_job_start', true)
            ->orderBy('sequence')
            ->value('id');
    }

    private function initializeCreateForm(?int $requestedClientId = null): void
    {
        $this->resetCreateForm();
        $this->deliveryDate = app(WorkspaceSettingsService::class)->localNow()->addMonth()->format('Y-m-d');

        $clientQuery = app(ClientService::class)
            ->visibleQuery(auth()->user())
            ->where('is_active', true);

        $this->clientId = $requestedClientId && (clone $clientQuery)->whereKey($requestedClientId)->exists()
            ? $requestedClientId
            : $clientQuery->value('id');
        $this->jobItems = [['category' => '', 'product' => '', 'quantity' => 1000]];
    }

    private function resetCreateForm(): void
    {
        $this->resetValidation();
        $this->reset([
            'jobTitle',
            'priority',
            'clientId',
            'workflowId',
            'workflowPhaseId',
            'ownerId',
            'coordinatorId',
            'deliveryDate',
            'description',
            'jobItems',
            'jobAttachments',
            'createCatalogReady',
            'createAssignmentReady',
            'createWorkflowReady',
        ]);
    }

    private function prepareSelectedJob(int $id): void
    {
        $job = app(JobService::class)->visibleQuery(auth()->user())
            ->with(['workflow.phases:id,workflow_id'])
            ->select(['id', 'workflow_id', 'workflow_phase_id'])
            ->findOrFail($id);

        if (!$this->expandedPhaseIds) {
            $phaseIds = $job->workflow?->phases?->pluck('id') ?? collect();
            $this->expandedPhaseIds = $phaseIds
                ->map(fn ($phaseId) => (int) $phaseId)
                ->values()
                ->all();
        }
    }

    private function setDefaultDocumentTask(?FlowJob $job = null): void
    {
        if (!$this->selectedJobId && !$job) return;
        $job ??= app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $valid = $job->tasks->first(fn ($task) => (int) $task->id === (int) $this->jobDocumentTaskId && ($task->document_category_id || $task->setupTemplate?->document_category_id));
        if ($valid) return;

        $task = $job->tasks
            ->filter(fn ($task) => $task->document_category_id || $task->setupTemplate?->document_category_id)
            ->sortBy(fn ($task) => [
                (int) ($task->workflow_phase_id === $job->workflow_phase_id ? 0 : 1),
                (int) ($task->phase?->sequence ?? 999),
                (int) ($task->setupTemplate?->sort_order ?? 999),
            ])->first();
        $this->jobDocumentTaskId = $task?->id;
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render open Job/Task details when another permitted user updates them.
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->showCreate) {
            return view('livewire.jobs.index', $this->createPageData($user));
        }

        if ($this->selectedTaskId) {
            return view('livewire.jobs.index', $this->taskPageData($user));
        }

        if ($this->selectedJobId) {
            return view('livewire.jobs.index', $this->jobPageData($user));
        }

        return view('livewire.jobs.index', $this->jobsTableData($user));
    }

    private function createPageData(User $user): array
    {
        $master = app(MasterDataService::class);
        $options = app(\App\Services\FilterOptionService::class);

        // Create Job is a separate render branch. Keep only the selected
        // records needed to render dependent fields; large option lists are
        // loaded by the shared remote selector only when the user opens them.
        $clients = $this->clientId
            ? app(ClientService::class)
                ->visibleQuery($user)
                ->where('is_active', true)
                ->whereKey($this->clientId)
                ->get(['id', 'name', 'contact_name'])
            : collect();

        $workflows = $this->createWorkflowReady && $this->workflowId
            ? Workflow::query()
                ->with('phases.taskPack.templates')
                ->where('is_snapshot', false)
                ->where('is_active', true)
                ->whereKey($this->workflowId)
                ->get()
            : collect();

        return [
            'selectedJob' => null,
            'selectedTask' => null,
            'clients' => $clients,
            'workflows' => $workflows,
            'categories' => collect(),
            'priorities' => $this->createAssignmentReady ? $master->active('priority') : collect(),
            'categoryFilterOptions' => $this->createCatalogReady
                ? $options->options($user, 'product-categories', 'create-job', '', null, 6)
                : collect(),
            'clientFilterOptions' => $options->options(
                $user,
                'clients',
                'create-job',
                '',
                $this->clientId,
                6,
            ),
            'ownerFilterOptions' => $this->createAssignmentReady
                ? $options->options($user, 'users', 'create-job', '', $this->ownerId, 6)
                : collect(),
            'workflowFilterOptions' => $this->createWorkflowReady
                ? $options->options($user, 'workflows', 'create-job', '', $this->workflowId, 6)
                : collect(),
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForCreate($user),
        ];
    }

    private function taskPageData(User $user): array
    {
        $master = app(MasterDataService::class);
        $task = app(TaskService::class)->visibleQuery($user)->with([
            'job.client:id,name',
            'job.tasks' => fn ($query) => app(AccessControlService::class)
                ->applyTaskScope($query, $user)
                ->select(['tasks.id', 'tasks.flow_job_id', 'tasks.workflow_phase_id', 'tasks.title'])
                ->orderBy('tasks.id'),
            'assignee', 'phase', 'documentCategory', 'setupTemplate.documentCategory',
            'checklistItems', 'comments.user', 'documents', 'activities.user',
        ])->findOrFail($this->selectedTaskId);

        $availableDocuments = $this->showTaskDocumentPicker
            ? app(DocumentService::class)
                ->query($user, ['client' => $task->job?->client_id])
                ->with(['job:id,job_number', 'task:id,title'])
                ->latest('id')
                ->limit(60)
                ->get()
            : collect();

        return [
            'selectedJob' => null,
            'selectedTask' => $task,
            'taskStatuses' => $this->taskStatusOptions($master),
            'priorities' => $master->active('priority'),
            'taskFlags' => $master->active('task_flag'),
            'displayTimezone' => app(WorkspaceSettingsService::class)->displayTimezone(),
            'availableDocuments' => $availableDocuments,
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForTask($task, $user),
        ];
    }

    private function jobPageData(User $user): array
    {
        $master = app(MasterDataService::class);
        $selected = app(JobService::class)->findVisible($user, $this->selectedJobId);
        $availableDocuments = $this->detailTab === 'documents'
            ? app(DocumentService::class)
                ->query($user, ['client' => $selected->client_id])
                ->with(['job:id,job_number', 'task:id,title'])
                ->latest('id')
                ->limit(60)
                ->get()
            : collect();

        return [
            'selectedJob' => $selected,
            'selectedTask' => null,
            'taskStatuses' => $this->taskStatusOptions($master),
            'users' => $this->userOptions($user),
            'priorities' => $master->active('priority'),
            // Product/category options on Job Details are loaded remotely only
            // when an inline dropdown opens, avoiding full catalog payloads.
            'products' => collect(),
            'categories' => collect(),
            'availableDocuments' => $availableDocuments,
            'healthOptions' => $this->healthOptions(),
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForJob($selected, $user),
        ];
    }

    private function jobsTableData(User $user): array
    {
        // The Orders list is intentionally its own lightweight render branch.
        // It does not hydrate filter catalogs, task collections, members or
        // inline-edit option lists that are not visible in the supplied
        // performance prototype.
        $jobs = app(JobService::class)->paginateOrders($user, $this->search, $this->perPage);

        return [
            'selectedJob' => null,
            'selectedTask' => null,
            'jobs' => $jobs,
        ];
    }

    private function userOptions(User $user)
    {
        $canAssign = $user->canModule('tasks', 'assign') || $user->canModule('jobs', 'assign');

        return $canAssign
            ? User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'profile_image_path'])
            : collect([(object) ['id' => $user->id, 'name' => $user->name, 'profile_image_path' => $user->profile_image_path]]);
    }

    private function taskStatusOptions(MasterDataService $master)
    {
        return collect(BoardLaneResolver::taskStatuses(
            $master->active('task_status')->pluck('name')
        ));
    }

    private function healthOptions()
    {
        return collect(['On Track', 'At Risk', 'Delayed', 'Blocked', 'Completed']);
    }

    private function jobFilters(): array
    {
        return [
            'search' => $this->search,
            'phase' => $this->phase,
            'health' => $this->health,
            'client' => $this->client,
            'owner' => $this->owner,
            'delivery' => $this->delivery,
            'invoice' => $this->invoice,
            'priority' => $this->priorityFilter,
            'status' => $this->jobStatusFilter,
            'sort' => $this->sort,
        ];
    }

    private function resetJobSelection(): void
    {
        $this->selectedJobIds = [];
        $this->resetPage();
    }
}
