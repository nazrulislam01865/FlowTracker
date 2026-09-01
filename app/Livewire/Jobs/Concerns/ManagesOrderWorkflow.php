<?php

namespace App\Livewire\Jobs\Concerns;

use App\Exceptions\EmailDeliveryException;
use App\Queries\Orders\VisibleOrderQuery;
use App\Models\FlowJob;
use App\Models\Task;
use App\Services\AccessControlService;
use App\Services\Orders\OrderWorkflowEmailService;
use App\Services\TaskService;
use Illuminate\Validation\ValidationException;

/**
 * Phase 5 Order UI workflow extracted from the legacy Jobs coordinator.
 *
 * Public method names and parent Livewire state are intentionally preserved so
 * existing Blade bindings, deep links, validation keys and realtime behavior do
 * not change during the incremental decomposition.
 */
trait ManagesOrderWorkflow
{
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

        $job = app(VisibleOrderQuery::class)->scoped(
            auth()->user(),
            $this->selectedJobId,
            ['workflow.phases:id,workflow_id'],
            ['id', 'workflow_id'],
        );

        $this->expandedPhaseIds = $job->workflow->phases->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    }

    public function collapseAllJobPhases(): void { $this->expandedPhaseIds = []; }

    public function openOrderWorkflowAction(int $taskId): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job.client', 'job.items', 'job.phase', 'setupTemplate', 'documents', 'links'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);
        app(\App\Services\OrderTaskSequenceService::class)->assertStatusActionable($task);

        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
        $hasEvidence = $task->documents->isNotEmpty() || $task->links->isNotEmpty();
        $descriptor = $workflowActions->descriptor($task, $hasEvidence);
        // Only file-backed prototype actions open the document picker. Other
        // actions (courier label, invoice preparation, QC, etc.) have their own
        // purpose-built interactions and must not be replaced by the generic
        // document modal just because the Task Pack also records a document
        // category. Completion requirements are still validated server-side.
        if (($descriptor['interaction'] ?? $descriptor['type'] ?? null) === 'document') {
            $this->openOverviewTaskDocumentModal($taskId);
            return;
        }

        if (($descriptor['interaction'] ?? null) === 'direct') {
            $decision = ($descriptor['key'] ?? null) === 'SHIP_LABEL' ? 'generate' : 'confirm';
            $workflowActions->perform($task, auth()->user(), $decision);
            $currentPhaseId = FlowJob::query()->whereKey($this->selectedJobId)->value('workflow_phase_id');
            if ($currentPhaseId) $this->overviewPhaseId = (int) $currentPhaseId;
            session()->flash('success', 'Order workflow updated.');
            return;
        }

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
        $this->resetOrderWorkflowEmailFallbackState();
        $this->resetValidation(['orderWorkflowActionComment', 'orderWorkflowActionPayload', 'orderWorkflowActionEmail']);
    }

    public function confirmShipmentDetailsWithoutChanges(int $taskId): void
    {
        $task = $this->shipmentActionTask($taskId, 'SHIP_CONFIRM_INFO');
        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
        $payload = $workflowActions->initialPayload($task, $task->job);

        try {
            $workflowActions->perform($task, auth()->user(), 'confirm', null, $payload);
        } catch (ValidationException $exception) {
            $this->orderWorkflowActionTaskId = $taskId;
            $this->orderWorkflowActionStep = 'main';
            $this->orderWorkflowActionPayload = $payload;
            $this->showOrderWorkflowActionModal = true;
            foreach ($exception->errors() as $field => $messages) {
                foreach ((array) $messages as $message) $this->addError($field, $message);
            }
            return;
        }

        $this->refreshShipmentWorkflowSelection();
        session()->flash('success', 'Shipment details confirmed. Tracking setup is now available.');
    }

    public function generateShipmentCourierLabel(int $taskId, string $carrier, string $trackingNumber): void
    {
        $task = $this->shipmentActionTask($taskId, 'SHIP_LABEL');
        $carrier = trim($carrier);
        $trackingNumber = trim($trackingNumber);
        $this->resetValidation('shipmentLabel');

        if ($carrier === '' || $trackingNumber === '') {
            $this->addError('shipmentLabel', 'Select a courier and enter the tracking number first.');
            return;
        }

        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
        $payload = $workflowActions->initialPayload($task, $task->job);
        $payload['carrier'] = $carrier;
        $payload['tracking_number'] = $trackingNumber;
        $workflowActions->perform($task, auth()->user(), 'generate', null, $payload);

        $this->refreshShipmentWorkflowSelection();
        session()->flash('success', 'Courier label generated. Review and print it to continue.');
    }

    public function completeShipmentTrackingTask(int $taskId, string $carrier, string $trackingNumber): void
    {
        $task = $this->shipmentActionTask($taskId, 'SHIP_LABEL');
        $carrier = trim($carrier);
        $trackingNumber = trim($trackingNumber);
        $this->resetValidation('shipmentLabel');

        if ($carrier === '' || $trackingNumber === '') {
            $this->addError('shipmentLabel', 'Select a courier and enter the tracking number first.');
            return;
        }

        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
        $payload = $workflowActions->initialPayload($task, $task->job);
        $payload['carrier'] = $carrier;
        $payload['tracking_number'] = $trackingNumber;
        $workflowActions->perform($task, auth()->user(), 'complete', null, $payload);

        $this->refreshShipmentWorkflowSelection();
        session()->flash('success', 'Tracking details saved. Dispatch shipment is now available.');
    }

    public function dispatchShipment(int $taskId): void
    {
        $task = $this->shipmentActionTask($taskId, 'SHIP_PACKAGE');
        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
        $payload = $workflowActions->initialPayload($task, $task->job);
        $this->resetValidation('shipmentDispatch');

        if (trim((string) ($payload['carrier'] ?? '')) === '' || trim((string) ($payload['tracking_number'] ?? '')) === '') {
            $this->addError('shipmentDispatch', 'Generate the courier label with a tracking number before dispatching the shipment.');
            return;
        }

        $workflowActions->perform($task, auth()->user(), 'confirm', null, $payload);
        $this->refreshShipmentWorkflowSelection();
        session()->flash('success', 'Shipment marked as dispatched.');
    }

    public function selectShipmentContact(string $selection): void
    {
        abort_unless($this->selectedJobId && $this->orderWorkflowActionTaskId, 422);
        $options = collect($this->orderWorkflowActionPayload['contact_options'] ?? []);
        $option = $options->firstWhere('value', $selection);
        if (! $option) return;

        $this->orderWorkflowActionPayload['contact_selection'] = $selection;
        $this->orderWorkflowActionPayload['contact_name'] = (string) ($option['name'] ?? '');
        $this->orderWorkflowActionPayload['contact_type'] = (string) ($option['contact_type'] ?? 'middle_client');
        $this->orderWorkflowActionPayload['phone_country_code'] = (string) ($option['country_code'] ?? '');
        $this->orderWorkflowActionPayload['phone_number'] = (string) ($option['phone'] ?? '');
        $this->orderWorkflowActionPayload['recipient'] = (string) ($option['name'] ?? '');
        $this->orderWorkflowActionPayload['contact'] = trim((string) (($option['country_code'] ?? '').' '.($option['phone'] ?? '')));
        $this->resetValidation([
            'orderWorkflowActionPayload.contact_name',
            'orderWorkflowActionPayload.phone_country_code',
            'orderWorkflowActionPayload.phone_number',
        ]);
    }

    public function useShipmentSavedAddress(string $selection = ''): void
    {
        abort_unless($this->selectedJobId && $this->orderWorkflowActionTaskId, 422);
        $options = collect($this->orderWorkflowActionPayload['address_options'] ?? []);
        if ($options->isEmpty()) return;

        $option = $selection !== '' ? $options->firstWhere('value', $selection) : null;
        $option ??= $options->firstWhere('is_default', true) ?: $options->first();
        if (! $option) return;

        $this->orderWorkflowActionPayload['address_selection'] = (string) ($option['value'] ?? '');
        $this->orderWorkflowActionPayload['address'] = (string) ($option['address'] ?? '');
        $this->orderWorkflowActionPayload['city'] = (string) ($option['city'] ?? '');
        $this->orderWorkflowActionPayload['state'] = (string) ($option['state'] ?? '');
        $this->orderWorkflowActionPayload['country'] = (string) ($option['country'] ?? '');
        $this->orderWorkflowActionPayload['postal_code'] = (string) ($option['postal_code'] ?? '');
        $this->resetValidation([
            'orderWorkflowActionPayload.address',
            'orderWorkflowActionPayload.country',
            'orderWorkflowActionPayload.postal_code',
        ]);
    }

    public function resetShipmentActionDetails(): void
    {
        abort_unless($this->selectedJobId && $this->orderWorkflowActionTaskId, 422);
        $task = $this->shipmentActionTask((int) $this->orderWorkflowActionTaskId, 'SHIP_CONFIRM_INFO');
        $this->orderWorkflowActionPayload = app(\App\Services\OrderWorkflowActionService::class)->initialPayload($task, $task->job);
        $this->resetValidation('orderWorkflowActionPayload');
    }

    public function submitOrderWorkflowAction(string $decision = 'confirm'): void
    {
        abort_unless($this->selectedJobId && $this->orderWorkflowActionTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job.client', 'job.items', 'job.phase', 'setupTemplate'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail((int) $this->orderWorkflowActionTaskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
        $key = $workflowActions->automationKey($task);

        // Preserve the prototype's nested dialogs instead of collapsing every
        // action into one generic confirmation screen.
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

        // The prototype intentionally uses two dialogs here: first record the
        // client artwork decision, then ask whether a sample/swatch is required.
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
            // A fresh click retries delivery from attempt one again, so clear
            // the older manual-fallback marker before starting the new cycle.
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
        $currentPhaseId = FlowJob::query()->whereKey($this->selectedJobId)->value('workflow_phase_id');
        if ($currentPhaseId) $this->overviewPhaseId = (int) $currentPhaseId;
        session()->flash('success', $successMessage);
    }

    private function shipmentActionTask(int $taskId, string $expectedKey): Task
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job.client', 'job.items', 'job.phase', 'setupTemplate', 'documents', 'links'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);
        abort_unless(app(\App\Services\OrderWorkflowActionService::class)->automationKey($task) === $expectedKey, 422);
        app(\App\Services\OrderTaskSequenceService::class)->assertStatusActionable($task);

        return $task;
    }

    private function refreshShipmentWorkflowSelection(): void
    {
        $currentPhaseId = FlowJob::query()->whereKey($this->selectedJobId)->value('workflow_phase_id');
        if ($currentPhaseId) $this->overviewPhaseId = (int) $currentPhaseId;
    }

    public function completeOrderWorkflowEmailTaskAfterFailure(): void
    {
        abort_unless($this->selectedJobId && $this->orderWorkflowActionTaskId, 422);

        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job.client', 'job.items', 'job.phase', 'setupTemplate'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail((int) $this->orderWorkflowActionTaskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        $workflowActions = app(\App\Services\OrderWorkflowActionService::class);
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
        $currentPhaseId = FlowJob::query()->whereKey($this->selectedJobId)->value('workflow_phase_id');
        if ($currentPhaseId) $this->overviewPhaseId = (int) $currentPhaseId;
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

}
