<?php

namespace App\Livewire\Orders;

use App\Actions\Orders\DeleteOrder;
use App\Actions\Orders\DeleteOrders;
use App\Exceptions\EmailDeliveryException;
use App\Queries\Orders\OrderListQuery;
use App\Queries\Orders\VisibleOrderQuery;
use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Models\Document;
use App\Models\Task;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\FilterOptionService;
use App\Services\DocumentService;
use App\Services\OrderDetailViewService;
use App\Services\OrderListPrototypeService;
use App\Services\OrderTaskSequenceService;
use App\Services\OrderWorkflowActionService;
use App\Services\Orders\OrderWorkflowEmailService;
use App\Services\TaskService;
use App\Support\AttachmentUpload;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use RefreshesFromWorkspace;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';
    public string $client = '';
    public string $phase = '';
    public string $owner = '';
    #[Url(as: 'metric', history: true, except: '')]
    public string $metricFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    #[Url(as: 'import', history: true, except: 0)]
    public int $importBatchId = 0;
    public string $importBatchLabel = '';
    public int $perPage = 10;
    public string $stageQuick = 'all';
    public string $stageSupplier = '';
    public string $stageAssignee = '';
    public string $stageUrgency = '';
    public string $stageCarrier = '';
    public string $stageClient = '';
    public array $selectedOrderIds = [];
    public bool $showBulkDeleteConfirm = false;

    // CHANGE 2026-08-24: phase-wise list actions now reuse the exact Order
    // Details workflow engine and modal components instead of redirecting to
    // the Order Details page before the user can perform the required action.
    public ?int $listActionOrderId = null;
    public bool $showOrderWorkflowActionModal = false;
    public ?int $orderWorkflowActionTaskId = null;
    public string $orderWorkflowActionComment = '';
    public string $orderWorkflowActionStep = 'main';
    /** @var array<string,mixed> */
    public array $orderWorkflowActionPayload = [];
    public bool $orderWorkflowEmailFallback = false;
    public string $orderWorkflowEmailFallbackMessage = '';
    public int $orderWorkflowEmailFallbackAttempts = 0;

    public bool $showOverviewTaskDocumentModal = false;
    public ?int $overviewTaskDocumentModalTaskId = null;
    public string $overviewTaskDocumentSource = 'upload';
    /** Files selected in the Order workflow upload modal. */
    public array $overviewTaskDocumentUpload = [];
    public ?int $overviewTaskExistingDocumentId = null;
    public string $overviewTaskDocumentNote = '';

    public function mount(): void
    {
        $this->search = trim((string) request('search', ''));
        $this->client = $this->numericFilterFromRequest('client');
        $this->phase = $this->numericFilterFromRequest('phase');
        $this->owner = $this->numericFilterFromRequest('owner');
        $this->metricFilter = trim((string) request('metric', $this->metricFilter));
        if (! in_array($this->metricFilter, ['', 'createdToday', 'notStarted', 'inProgress', 'dueThisWeek', 'completedThisWeek', 'attention', 'dashboardActive', 'dashboardAttention', 'dashboardOverdueTasks'], true)) {
            $this->metricFilter = '';
        }
        $this->dateFrom = $this->normalizeDateFilter((string) request('date_from', ''));
        $this->dateTo = $this->normalizeDateFilter((string) request('date_to', ''));
        $this->normalizeDateRange('from');

        $this->importBatchId = max(0, (int) request('import', $this->importBatchId));
        if ($this->importBatchId > 0) {
            $this->importBatchLabel = app(OrderListQuery::class)->bulkImportNumber($this->importBatchId) ?? '';
            if ($this->importBatchLabel === '') {
                $this->importBatchId = 0;
            }
        }

        if ($this->importBatchId > 0) {
            // A completed import opens as its own deterministic result set.
            $this->clearListFiltersExcept('importBatch');
        }
    }

    public function updatedSearch(): void
    {
        $this->metricFilter = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedClient(): void
    {
        $this->client = $this->normalizeNumericFilter($this->client);
        $this->metricFilter = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedPhase(): void
    {
        $this->phase = $this->normalizeNumericFilter($this->phase);
        $this->metricFilter = '';
        $this->resetStageSpecificFilters();
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedOwner(): void
    {
        $this->owner = $this->normalizeNumericFilter($this->owner);
        $this->metricFilter = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    /**
     * Commit the Orders-list owner filter through an explicit Livewire action.
     *
     * The shared remote selector updates its label optimistically. On this
     * high-traffic list that optimistic Alpine state could survive even when a
     * deferred/stale Livewire property update lost the race with another list
     * request, leaving the dropdown showing a user while the query still used
     * owner = "".  Use a deterministic action for this filter so the visible
     * selection and the server-side query state are always committed together.
     */
    public function applyOwnerFilter(string $property, string|int|null $value = null): void
    {
        abort_unless($property === 'owner', 422);

        $this->owner = $this->normalizeNumericFilter((string) ($value ?? ''));
        $this->metricFilter = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->dateFrom = $this->normalizeDateFilter($this->dateFrom);
        $this->clearListFiltersExcept('dateRange');
        $this->normalizeDateRange('from');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->dateTo = $this->normalizeDateFilter($this->dateTo);
        $this->clearListFiltersExcept('dateRange');
        $this->normalizeDateRange('to');
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function updatedStageSupplier(): void { $this->stageSupplier = $this->normalizeNumericFilter($this->stageSupplier); $this->resetPage(); }
    public function updatedStageAssignee(): void { $this->stageAssignee = $this->normalizeNumericFilter($this->stageAssignee); $this->resetPage(); }
    public function updatedStageUrgency(): void { $this->stageUrgency = $this->normalizeNumericFilter($this->stageUrgency); $this->resetPage(); }
    public function updatedStageClient(): void { $this->stageClient = $this->normalizeNumericFilter($this->stageClient); $this->resetPage(); }
    public function updatedStageCarrier(): void { $this->stageCarrier = trim($this->stageCarrier); $this->resetPage(); }

    public function selectStage(?int $phaseId = null): void
    {
        $this->phase = $phaseId && $phaseId > 0 ? (string) $phaseId : '';
        $this->metricFilter = '';
        $this->resetStageSpecificFilters();
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function setStageQuick(string $quick): void
    {
        $allowed = collect(OrderListPrototypeService::QUICK_FILTERS)->flatMap(fn ($filters) => array_keys($filters));
        if (! $allowed->contains($quick)) return;
        $this->stageQuick = $quick;
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function clearStageSpecificFilters(): void
    {
        $this->resetStageSpecificFilters();
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        if ($this->search === '') {
            return;
        }

        $this->search = '';
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function setMetricFilter(string $metric): void
    {
        if (! in_array($metric, ['createdToday', 'notStarted', 'inProgress', 'dueThisWeek', 'completedThisWeek', 'attention'], true)) {
            return;
        }

        $nextMetric = $this->metricFilter === $metric ? '' : $metric;

        // Summary cards and toolbar filters are mutually exclusive. Selecting
        // a card clears the search/dropdowns so only one Order list filter is
        // active and the visible rows always correspond to the selected card.
        $this->clearToolbarFilters();
        $this->metricFilter = $nextMetric;
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->client = '';
        $this->phase = '';
        $this->owner = '';
        $this->metricFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->importBatchId = 0;
        $this->importBatchLabel = '';
        $this->resetStageSpecificFilters();
        $this->resetOrderSelection();
        $this->resetPage();
    }

    public function openInvoiceAndPayment(int $id): void
    {
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'view'), 403);

        // Confirm the Order is inside the current user's visible Order scope
        // before navigating to its finance section.
        $job = app(OrderListQuery::class)->visible($user, $id);

        $this->redirectRoute('jobs.index', [
            'open' => $job->id,
            'tab' => 'finance',
        ], navigate: true);
    }

    public function deleteOrder(int $id): void
    {
        $job = app(DeleteOrder::class)->handle(auth()->user(), $id);
        $this->selectedOrderIds = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->reject(fn ($value) => $value === $id)
            ->values()
            ->all();
        $this->resetPage();

        session()->flash('success', $job->displayOrderNumber().' deleted successfully.');
    }

    public function toggleOrderSelection(int $id): void
    {
        $id = (int) $id;
        if ($id < 1 || ! app(OrderListQuery::class)->exists(auth()->user(), $id)) {
            return;
        }

        $selected = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique();

        $this->selectedOrderIds = $selected->contains($id)
            ? $selected->reject(fn ($value) => $value === $id)->values()->all()
            : $selected->push($id)->unique()->values()->all();
    }

    public function toggleOrderPageSelection(array $ids, bool $checked): void
    {
        $ids = collect($ids)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showBulkDeleteConfirm = false;
            return;
        }

        $visibleIds = app(OrderListQuery::class)->visibleIds(auth()->user(), $ids);

        $selected = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique();

        $this->selectedOrderIds = $checked
            ? $selected->concat($visibleIds)->unique()->values()->all()
            : $selected->reject(fn ($value) => $visibleIds->contains($value))->values()->all();
    }

    public function clearOrderSelection(): void
    {
        $this->resetOrderSelection();
    }

    public function openBulkDeleteConfirmation(): void
    {
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'jobs', 'delete'), 403);

        $ids = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showBulkDeleteConfirm = false;
            return;
        }

        // Keep only Orders that are still inside the user's current visible scope.
        $visibleIds = app(OrderListQuery::class)->visibleIds($user, $ids);

        $this->selectedOrderIds = $visibleIds->all();
        $this->showBulkDeleteConfirm = $visibleIds->isNotEmpty();
    }

    public function closeBulkDeleteConfirmation(): void
    {
        $this->showBulkDeleteConfirm = false;
    }

    public function bulkDeleteOrders(): void
    {
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'jobs', 'delete'), 403);

        $ids = collect($this->selectedOrderIds)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showBulkDeleteConfirm = false;
            return;
        }

        $deletedCount = app(DeleteOrders::class)->handle($user, $ids);

        if ($deletedCount === 0) {
            $this->resetOrderSelection();
            $this->showBulkDeleteConfirm = false;
            return;
        }
        $this->showBulkDeleteConfirm = false;
        $this->resetOrderSelection();
        $this->resetPage();

        session()->flash(
            'success',
            $deletedCount.' '.\Illuminate\Support\Str::plural('order', $deletedCount).' deleted successfully.'
        );
    }

    /**
     * CHANGE 2026-08-24:
     * Execute the phase-list Next Action in place. The descriptor, sequence
     * validation and workflow mutation all come from the same services used by
     * Order Details, so list actions cannot drift from the detail-page logic.
     */
    public function openListWorkflowAction(int $orderId, int $taskId): void
    {
        $task = $this->editableListWorkflowTask($orderId, $taskId, [
            'job.client',
            'job.items',
            'job.phase',
            'setupTemplate',
            'documents',
            'links',
        ]);

        app(OrderTaskSequenceService::class)->assertStatusActionable($task);

        $workflowActions = app(OrderWorkflowActionService::class);
        $hasEvidence = $task->documents->isNotEmpty() || $task->links->isNotEmpty();
        $descriptor = $workflowActions->descriptor($task, $hasEvidence);
        $interaction = (string) ($descriptor['interaction'] ?? $descriptor['type'] ?? 'modal');

        $this->listActionOrderId = $orderId;

        if ($interaction === 'document') {
            $this->openListTaskDocumentModal($task);
            return;
        }

        if ($interaction === 'direct') {
            $decision = ($descriptor['key'] ?? null) === 'SHIP_LABEL' ? 'generate' : 'confirm';
            $workflowActions->perform($task, auth()->user(), $decision);
            $this->listActionOrderId = null;
            session()->flash('success', 'Order workflow updated.');
            return;
        }

        $this->showOverviewTaskDocumentModal = false;
        $this->orderWorkflowActionTaskId = $taskId;
        $this->orderWorkflowActionComment = '';
        $this->orderWorkflowActionStep = 'main';
        $this->orderWorkflowActionPayload = $workflowActions->initialPayload($task, $task->job);
        $this->resetOrderWorkflowEmailFallbackState();

        if (in_array($descriptor['key'] ?? null, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true)) {
            $failure = $this->orderWorkflowEmailFallbackMarker($task);
            if ($failure) {
                $this->showOrderWorkflowEmailFallback($descriptor['key'], (int) ($failure['attempts'] ?? 3));
            }
        }

        $this->resetValidation(['orderWorkflowActionComment', 'orderWorkflowActionPayload', 'orderWorkflowActionEmail']);
        $this->showOrderWorkflowActionModal = true;
    }

    public function closeOrderWorkflowAction(): void
    {
        $this->showOrderWorkflowActionModal = false;
        $this->orderWorkflowActionTaskId = null;
        $this->orderWorkflowActionComment = '';
        $this->orderWorkflowActionStep = 'main';
        $this->orderWorkflowActionPayload = [];
        $this->listActionOrderId = null;
        $this->resetOrderWorkflowEmailFallbackState();
        $this->resetValidation(['orderWorkflowActionComment', 'orderWorkflowActionPayload', 'orderWorkflowActionEmail']);
    }

    public function submitOrderWorkflowAction(string $decision = 'confirm'): void
    {
        abort_unless($this->listActionOrderId && $this->orderWorkflowActionTaskId, 422);

        $task = $this->editableListWorkflowTask(
            (int) $this->listActionOrderId,
            (int) $this->orderWorkflowActionTaskId,
            ['job.client', 'job.items', 'job.phase', 'setupTemplate'],
        );

        $workflowActions = app(OrderWorkflowActionService::class);
        $key = $workflowActions->automationKey($task);

        // Keep the same nested Artwork / issue dialogs used by Order Details.
        if ($this->orderWorkflowActionStep === 'main' && $decision === 'revise'
            && in_array($key, ['ART_INTERNAL_REVIEW', 'ART_CLIENT_ERP_DECISION'], true)) {
            $this->orderWorkflowActionStep = 'revision';
            $this->orderWorkflowActionComment = '';
            $this->resetValidation(['orderWorkflowActionComment', 'orderWorkflowActionPayload', 'orderWorkflowActionEmail']);
            return;
        }

        if ($this->orderWorkflowActionStep === 'main' && $decision === 'issue'
            && in_array($key, ['PROD_ISSUE', 'QC_CHECK'], true)) {
            $this->orderWorkflowActionStep = 'issue';
            $this->orderWorkflowActionComment = '';
            $this->resetValidation(['orderWorkflowActionComment', 'orderWorkflowActionPayload', 'orderWorkflowActionEmail']);
            return;
        }

        if ($key === 'ART_CLIENT_ERP_DECISION' && $decision === 'approved' && $this->orderWorkflowActionStep === 'main') {
            $this->orderWorkflowActionStep = 'sample';
            $this->orderWorkflowActionComment = '';
            $this->resetValidation(['orderWorkflowActionComment', 'orderWorkflowActionPayload', 'orderWorkflowActionEmail']);
            return;
        }

        if ($key === 'ART_CLIENT_ERP_DECISION' && $this->orderWorkflowActionStep === 'sample') {
            $decision = $decision === 'sample_yes' ? 'sample' : 'confirm';
        }

        if (in_array($key, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true)) {
            $this->resetOrderWorkflowEmailFallbackState();
            $this->forgetOrderWorkflowEmailFallbackMarker($task);
        }

        try {
            $workflowActions->perform(
                $task,
                auth()->user(),
                $decision,
                $this->orderWorkflowActionComment,
                $this->orderWorkflowActionPayload,
            );
        } catch (EmailDeliveryException $exception) {
            if (! in_array($key, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true)) {
                throw $exception;
            }

            $preview = app(OrderWorkflowEmailService::class)->preview($task, auth()->user());
            $trackingId = '';
            if (preg_match('/Reference:\s*([A-Za-z0-9-]+)/', $exception->getMessage(), $matches) === 1) {
                $trackingId = (string) ($matches[1] ?? '');
            }
            $failure = [
                'task_id' => (int) $task->id,
                'flow_job_id' => (int) $task->flow_job_id,
                'handoff_key' => (string) $key,
                'document_id' => (int) ($preview['document_id'] ?? 0),
                'document_name' => (string) ($preview['document_name'] ?? ''),
                'attempts' => 3,
                'tracking_id' => $trackingId,
                'failed_at' => now()->toIso8601String(),
            ];
            session()->put($this->orderWorkflowEmailFallbackSessionKey($task), $failure);

            $this->showOrderWorkflowEmailFallback($key, 3);
            $this->resetValidation('orderWorkflowActionEmail');
            return;
        }

        if (in_array($key, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true)) {
            $this->forgetOrderWorkflowEmailFallbackMarker($task);
        }

        $successMessage = match ($key) {
            'NEW_SEND_PO_ARTWORK' => 'Purchase Order emailed to the Artwork Team.',
            'ART_SEND_ORDER_TEAM' => 'Artwork emailed to the Order Team.',
            default => 'Order workflow updated.',
        };

        $this->closeOrderWorkflowAction();
        session()->flash('success', $successMessage);
    }

    public function completeOrderWorkflowEmailTaskAfterFailure(): void
    {
        abort_unless($this->listActionOrderId && $this->orderWorkflowActionTaskId, 422);

        $task = $this->editableListWorkflowTask(
            (int) $this->listActionOrderId,
            (int) $this->orderWorkflowActionTaskId,
            ['job.client', 'job.items', 'job.phase', 'setupTemplate'],
        );

        $workflowActions = app(OrderWorkflowActionService::class);
        $key = $workflowActions->automationKey($task);
        abort_unless(in_array($key, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true), 422);

        $failure = $this->orderWorkflowEmailFallbackMarker($task);
        if (! $failure) {
            $this->resetOrderWorkflowEmailFallbackState();
            $this->addError('orderWorkflowActionEmail', 'Manual completion is available only after the email service has failed three delivery attempts.');
            return;
        }

        $workflowActions->completeEmailHandoffAfterFailure($task, auth()->user(), $failure);
        $this->forgetOrderWorkflowEmailFallbackMarker($task);

        $attachmentLabel = $key === 'ART_SEND_ORDER_TEAM' ? 'artwork' : 'Purchase Order';
        $this->closeOrderWorkflowAction();
        session()->flash('success', 'Task completed manually. Please send the '.$attachmentLabel.' outside FlowTrack using the downloaded file.');
    }

    private function showOrderWorkflowEmailFallback(?string $key, int $attempts = 3): void
    {
        $attempts = max(3, $attempts);
        $attachmentLabel = $key === 'ART_SEND_ORDER_TEAM' ? 'artwork' : 'Purchase Order';

        $this->orderWorkflowEmailFallback = true;
        $this->orderWorkflowEmailFallbackAttempts = $attempts;
        $this->orderWorkflowEmailFallbackMessage = 'Due to some technical issue, the email could not be sent after '.$attempts.' attempts. Please download the '.$attachmentLabel.' and send it manually. After sending it manually, you can complete this task to continue the workflow.';
    }

    /** @return array<string,mixed>|null */
    private function orderWorkflowEmailFallbackMarker(Task $task): ?array
    {
        $value = session()->get($this->orderWorkflowEmailFallbackSessionKey($task));
        return is_array($value) ? $value : null;
    }

    private function forgetOrderWorkflowEmailFallbackMarker(Task $task): void
    {
        session()->forget($this->orderWorkflowEmailFallbackSessionKey($task));
    }

    private function orderWorkflowEmailFallbackSessionKey(Task $task): string
    {
        return 'order_workflow_email_fallback.'.(int) auth()->id().'.'.(int) $task->id;
    }

    private function resetOrderWorkflowEmailFallbackState(): void
    {
        $this->orderWorkflowEmailFallback = false;
        $this->orderWorkflowEmailFallbackMessage = '';
        $this->orderWorkflowEmailFallbackAttempts = 0;
    }

    /** Initialize the same file-upload action modal used on Order Details. */
    private function openListTaskDocumentModal(Task $task): void
    {
        $canCreate = auth()->user()->canModule('documents', 'create');
        $canLink = auth()->user()->canModule('documents', 'link');
        abort_unless($canCreate || $canLink, 403, 'Your role cannot add documents.');

        $this->showOrderWorkflowActionModal = false;
        $this->orderWorkflowActionTaskId = null;
        $this->overviewTaskDocumentModalTaskId = (int) $task->id;
        $this->overviewTaskDocumentSource = $canCreate ? 'upload' : 'existing';
        $this->overviewTaskDocumentUpload = [];
        $this->overviewTaskExistingDocumentId = null;
        $this->overviewTaskDocumentNote = '';
        $this->resetValidation([
            'overviewTaskDocumentUpload',
            'overviewTaskExistingDocumentId',
            'overviewTaskDocumentNote',
        ]);
        $this->showOverviewTaskDocumentModal = true;
    }

    public function closeOverviewTaskDocumentModal(): void
    {
        $this->showOverviewTaskDocumentModal = false;
        $this->overviewTaskDocumentModalTaskId = null;
        $this->overviewTaskDocumentSource = 'upload';
        $this->overviewTaskDocumentUpload = [];
        $this->overviewTaskExistingDocumentId = null;
        $this->overviewTaskDocumentNote = '';
        $this->listActionOrderId = null;
        $this->resetValidation([
            'overviewTaskDocumentUpload',
            'overviewTaskExistingDocumentId',
            'overviewTaskDocumentNote',
        ]);
    }

    public function setOverviewTaskDocumentSource(string $source): void
    {
        abort_unless(in_array($source, ['upload', 'existing'], true), 422);

        if ($source === 'upload') {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
        } else {
            abort_unless(auth()->user()->canModule('documents', 'link'), 403);
        }

        $this->overviewTaskDocumentSource = $source;
        $this->overviewTaskDocumentUpload = [];
        $this->overviewTaskExistingDocumentId = null;
        $this->resetValidation(['overviewTaskDocumentUpload', 'overviewTaskExistingDocumentId']);
    }

    public function saveOverviewTaskDocument(): void
    {
        abort_unless($this->listActionOrderId && $this->overviewTaskDocumentModalTaskId, 422);

        $task = $this->editableListWorkflowTask(
            (int) $this->listActionOrderId,
            (int) $this->overviewTaskDocumentModalTaskId,
            ['job', 'documentCategory', 'setupTemplate.documentCategory'],
        );

        $this->validate([
            'overviewTaskDocumentSource' => ['required', Rule::in(['upload', 'existing'])],
            'overviewTaskDocumentNote' => ['nullable', 'string', 'max:2000'],
        ]);

        $note = trim($this->overviewTaskDocumentNote);
        $note = $note !== '' ? $note : null;
        $documentService = app(DocumentService::class);

        if ($this->overviewTaskDocumentSource === 'upload') {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
            $allowsMultiple = app(OrderWorkflowActionService::class)->automationKey($task) === 'ART_PREPARE_UPLOAD'
                || (bool) ($task->setupTemplate?->allow_multiple_documents ?? false);
            $this->validate([
                'overviewTaskDocumentUpload' => ['required', 'array', 'min:1', 'max:'.($allowsMultiple ? 10 : 1)],
                'overviewTaskDocumentUpload.*' => AttachmentUpload::itemRules(AttachmentUpload::DOCUMENTS_WITH_AI, 20480),
            ], [
                'overviewTaskDocumentUpload.max' => $allowsMultiple
                    ? 'You can upload a maximum of 10 files at a time.'
                    : 'Choose one file for this task.',
                'overviewTaskDocumentUpload.*.max' => 'Each file must be 20 MB or smaller.',
            ]);

            $storeData = [
                'flow_job_id' => $task->flow_job_id,
                'client_id' => $task->job?->client_id,
                'task_id' => $task->id,
                'note' => $note,
            ];

            if ($documentService->taskHasRequirement($task)) {
                $storeData['require_task_pack_requirement'] = true;
            } else {
                $storeData['category'] = 'Task attachment';
            }

            $documentService->storeMany($this->overviewTaskDocumentUpload, $storeData, auth()->user());
        } else {
            abort_unless(auth()->user()->canModule('documents', 'link'), 403);
            $this->validate([
                'overviewTaskExistingDocumentId' => ['required', 'integer', 'exists:documents,id'],
            ]);

            $source = app(AccessControlService::class)
                ->applyDocumentScope(
                    Document::query()->whereKey((int) $this->overviewTaskExistingDocumentId),
                    auth()->user(),
                )
                ->firstOrFail();

            abort_unless(
                (int) $source->client_id === (int) $task->job?->client_id,
                403,
                'The selected document does not belong to this client.',
            );

            $documentService->linkExisting($source, $task, auth()->user(), true, $note);
        }

        // File-backed workflow actions complete/advance through the same hook
        // as the Order Details page after the document has persisted.
        app(OrderWorkflowActionService::class)->afterDocumentAdded($task->refresh(), auth()->user());

        $title = (string) $task->title;
        $this->closeOverviewTaskDocumentModal();
        session()->flash('success', 'Document added to '.$title.'.');
    }

    public function removeOverviewTaskDocumentUpload(int $index): void
    {
        if (! array_key_exists($index, $this->overviewTaskDocumentUpload)) return;

        unset($this->overviewTaskDocumentUpload[$index]);
        $this->overviewTaskDocumentUpload = array_values($this->overviewTaskDocumentUpload);
        $this->resetValidation(['overviewTaskDocumentUpload', 'overviewTaskDocumentUpload.*']);
    }

    private function editableListWorkflowTask(int $orderId, int $taskId, array $with = []): Task
    {
        abort_unless($orderId > 0 && $taskId > 0, 422);

        // Verify the Order itself remains in the current user's list scope.
        app(OrderListQuery::class)->visible(auth()->user(), $orderId);

        $task = app(TaskService::class)
            ->visibleQuery(auth()->user())
            ->with($with)
            ->where('flow_job_id', $orderId)
            ->findOrFail($taskId);

        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        return $task;
    }

    public function render()
    {
        $user = auth()->user();
        $options = app(FilterOptionService::class);
        $list = app(OrderListQuery::class);
        $stages = $list->stages($user);
        $urgencies = $list->urgencyOptions();

        $jobs = $list->paginate($user, [
            'search' => $this->search,
            'client_id' => $this->filterId($this->client),
            'phase_id' => $this->filterId($this->phase),
            'owner_id' => $this->filterId($this->owner),
            'metric' => $this->metricFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'import_id' => $this->importBatchId > 0 ? $this->importBatchId : null,
            'stage_quick' => $this->stageQuick,
            'stage_supplier_id' => $this->filterId($this->stageSupplier),
            'stage_assignee_id' => $this->filterId($this->stageAssignee),
            'stage_urgency_id' => $this->filterId($this->stageUrgency),
            'stage_carrier' => $this->stageCarrier,
            'stage_client_id' => $this->filterId($this->stageClient),
        ], $stages, $this->perPage);

        $selectedStage = $this->phase !== '' ? $stages->firstWhere('id', (int) $this->phase) : null;
        $stageSequence = (int) data_get($selectedStage, 'sequence', 0);

        // CHANGE 2026-08-24: hydrate one Order only while an inline list action
        // modal is open. Normal list renders keep the existing bounded queries.
        $listActionOrder = null;
        $listActionTask = null;
        $listActionContext = [];
        $listActionWorkflowModal = [];
        $listActionAvailableDocuments = collect();

        if ($this->listActionOrderId && ($this->showOrderWorkflowActionModal || $this->showOverviewTaskDocumentModal)) {
            $orderQuery = app(VisibleOrderQuery::class);
            $listActionOrder = $orderQuery->base($user, (int) $this->listActionOrderId);
            $orderQuery->loadTab($listActionOrder, $user, 'overview');

            $actionTaskId = $this->showOrderWorkflowActionModal
                ? $this->orderWorkflowActionTaskId
                : $this->overviewTaskDocumentModalTaskId;

            $listActionTask = $actionTaskId
                ? $listActionOrder->tasks->firstWhere('id', (int) $actionTaskId)
                : null;

            if ($listActionTask) {
                $listActionContext = app(OrderDetailViewService::class)->build($listActionOrder, $user, $urgencies);
                $listActionWorkflowModal = data_get(
                    $listActionContext,
                    'taskActionModals.'.(int) $listActionTask->id,
                    [],
                );

                if ($this->showOverviewTaskDocumentModal && $this->overviewTaskDocumentSource === 'existing') {
                    $listActionAvailableDocuments = app(DocumentService::class)
                        ->query($user, ['client' => $listActionOrder->client_id])
                        ->with(['job:id,job_number', 'task:id,title'])
                        ->latest('id')
                        ->limit(60)
                        ->get();
                }
            }
        }

        return view('livewire.orders.index', [
            'jobs' => $jobs,
            'orderRows' => $list->rows($jobs, $urgencies),
            'orderStages' => $stages,
            'selectedStage' => $selectedStage,
            'stageQuickFilters' => OrderListPrototypeService::QUICK_FILTERS[$stageSequence] ?? ['all' => 'All'],
            'clientFilterOptions' => $options->options($user, 'clients', 'jobs', '', $this->filterId($this->client), 20),
            'ownerFilterOptions' => $options->options($user, 'users', 'order-list-user-filter', '', $this->filterId($this->owner), 20),
            'stageAssigneeOptions' => $options->options($user, 'users', 'order-list-user-filter', '', $this->filterId($this->stageAssignee), 20),
            'stageClientFilterOptions' => $options->options($user, 'clients', 'jobs', '', $this->filterId($this->stageClient), 20),
            'supplierFilterOptions' => $options->options($user, 'suppliers', 'order-list', '', $this->filterId($this->stageSupplier), 20),
            'shipmentUrgencyOptions' => $urgencies,
            'listActionOrder' => $listActionOrder,
            'listActionTask' => $listActionTask,
            'listActionContext' => $listActionContext,
            'listActionWorkflowModal' => $listActionWorkflowModal,
            'listActionAvailableDocuments' => $listActionAvailableDocuments,
        ]);
    }

    private function clearListFiltersExcept(string $except): void
    {
        if ($except !== 'search') {
            $this->search = '';
        }
        if ($except !== 'client') {
            $this->client = '';
        }
        if ($except !== 'phase') {
            $this->phase = '';
        }
        if ($except !== 'owner') {
            $this->owner = '';
        }
        if ($except !== 'dateRange') {
            $this->dateFrom = '';
            $this->dateTo = '';
        }
        if ($except !== 'importBatch') {
            $this->importBatchId = 0;
            $this->importBatchLabel = '';
        }

        $this->metricFilter = '';
    }

    private function clearToolbarFilters(): void
    {
        $this->search = '';
        $this->client = '';
        $this->phase = '';
        $this->owner = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->importBatchId = 0;
        $this->importBatchLabel = '';
    }

    private function normalizeDateFilter(string $value): string
    {
        $value = trim($value);
        if ($value === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }

        try {
            $date = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $value, 'UTC');
        } catch (\Throwable) {
            return '';
        }

        return $date && $date->format('Y-m-d') === $value ? $value : '';
    }

    private function normalizeDateRange(string $changed): void
    {
        if ($this->dateFrom === '' || $this->dateTo === '' || $this->dateFrom <= $this->dateTo) {
            return;
        }

        if ($changed === 'to') {
            $this->dateFrom = $this->dateTo;
            return;
        }

        $this->dateTo = $this->dateFrom;
    }

    private function selectedFilterOptions(
        FilterOptionService $options,
        User $user,
        string $type,
        string $context,
        string $value,
    ): Collection {
        $id = $this->filterId($value);

        return $id
            ? $options->options($user, $type, $context, '', $id, 5)
            : collect();
    }

    private function resetStageSpecificFilters(): void
    {
        $this->stageQuick = 'all';
        $this->stageSupplier = '';
        $this->stageAssignee = '';
        $this->stageUrgency = '';
        $this->stageCarrier = '';
        $this->stageClient = '';
    }

    private function resetOrderSelection(): void
    {
        $this->selectedOrderIds = [];
        $this->showBulkDeleteConfirm = false;
    }

    private function numericFilterFromRequest(string $key): string
    {
        return $this->normalizeNumericFilter((string) request($key, ''));
    }

    private function normalizeNumericFilter(string $value): string
    {
        $value = trim($value);

        return $value !== '' && ctype_digit($value) && (int) $value > 0
            ? (string) ((int) $value)
            : '';
    }

    private function filterId(string $value): ?int
    {
        return $value !== '' && ctype_digit($value) && (int) $value > 0
            ? (int) $value
            : null;
    }
}
