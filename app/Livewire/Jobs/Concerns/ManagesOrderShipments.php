<?php

namespace App\Livewire\Jobs\Concerns;

use App\Models\OrderShipment;
use App\Models\Task;
use App\Services\AccessControlService;
use App\Services\OrderShipmentService;
use App\Services\LocationMasterDataService;
use App\Services\OrderTaskSequenceService;
use App\Services\OrderWorkflowActionService;
use App\Services\TaskService;
use Illuminate\Validation\ValidationException;

/**
 * Livewire coordinator for the Order Details multi-shipment prototype.
 * Persistence and workflow aggregation remain in OrderShipmentService.
 */
trait ManagesOrderShipments
{
    public bool $showShipmentModal = false;
    public ?int $shipmentModalTaskId = null;
    public ?int $shipmentEditingId = null;
    public string $shipmentModalMode = OrderShipmentService::MODE_SAME_ADDRESS;
    /** @var array<string,mixed> */
    public array $shipmentForm = [];

    public bool $showShipmentDetailsModal = false;
    public ?int $shipmentDetailsId = null;

    public function confirmShipmentPlan(int $taskId): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_CONFIRM_INFO', true);
        $this->resetValidation('shipmentSettings');

        try {
            app(OrderShipmentService::class)->confirmSetup($task, auth()->user());
        } catch (ValidationException $exception) {
            $this->applyShipmentValidation($exception);
            return;
        }

        session()->flash('success', 'Shipment details confirmed. Tracking setup is now available.');
    }

    public function openAddShipment(int $taskId, ?string $addressMode = null): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_CONFIRM_INFO');
        $job = $task->job()->with(['shipments.shippingMethod', 'shipments.shipmentUrgency', 'shipments.courier'])->firstOrFail();
        abort_unless((int) $job->workflow_phase_id === (int) $task->workflow_phase_id, 422, 'Shipments can only be added while the Shipment stage is active.');

        $primary = $job->shipments->firstWhere('is_primary', true) ?: $job->shipments->first();
        $this->shipmentModalTaskId = $task->id;
        $this->shipmentEditingId = null;
        $this->shipmentModalMode = $addressMode === OrderShipmentService::MODE_MULTIPLE_ADDRESS
            ? OrderShipmentService::MODE_MULTIPLE_ADDRESS
            : (string) ($job->shipment_address_mode ?: OrderShipmentService::MODE_SAME_ADDRESS);
        $this->shipmentForm = [
            'recipient' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->recipient ?? '') : '',
            'phone_country_code' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->phone_country_code ?? '') : '',
            'phone' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->phone ?? '') : '',
            'address' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->address ?? '') : '',
            'city' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->city ?? '') : '',
            'state' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->state ?? '') : '',
            'postal_code' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->postal_code ?? '') : '',
            'country' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? (string) ($primary?->country ?? $this->defaultShipmentCountry()) : $this->defaultShipmentCountry(),
            'shipping_source_address_id' => $this->shipmentModalMode === OrderShipmentService::MODE_SAME_ADDRESS ? $primary?->shipping_source_address_id : null,
            'shipment_method_id' => $primary?->shipment_method_id,
            'shipment_urgency_id' => $primary?->shipment_urgency_id,
            'quantity' => null,
            'package_reference' => '',
        ];
        $this->resetShipmentPrototypeErrors();
        $this->showShipmentModal = true;
    }

    public function openEditShipment(int $taskId, int $shipmentId): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_CONFIRM_INFO');
        $shipment = $this->shipmentForTask($task, $shipmentId);
        abort_if($shipment->dispatched_at, 422, 'A dispatched shipment can no longer be edited.');

        $this->shipmentModalTaskId = $task->id;
        $this->shipmentEditingId = $shipment->id;

        $taskShipments = $task->job?->shipments ?? collect();
        $primary = $taskShipments->firstWhere('is_primary', true) ?: $taskShipments->sortBy('sequence')->first();
        $this->shipmentModalMode = $shipment->is_primary
            ? OrderShipmentService::MODE_MULTIPLE_ADDRESS
            : ($primary && OrderShipmentService::sameDeliveryAddress($shipment, $primary)
                ? OrderShipmentService::MODE_SAME_ADDRESS
                : OrderShipmentService::MODE_MULTIPLE_ADDRESS);
        $this->shipmentForm = [
            'recipient' => (string) ($shipment->recipient ?? ''),
            'phone_country_code' => (string) ($shipment->phone_country_code ?? ''),
            'phone' => (string) ($shipment->phone ?? ''),
            'address' => (string) ($shipment->address ?? ''),
            'city' => (string) ($shipment->city ?? ''),
            'state' => (string) ($shipment->state ?? ''),
            'postal_code' => (string) ($shipment->postal_code ?? ''),
            'country' => (string) ($shipment->country ?? $this->defaultShipmentCountry()),
            'shipping_source_address_id' => $shipment->shipping_source_address_id,
            'shipment_method_id' => $shipment->shipment_method_id,
            'shipment_urgency_id' => $shipment->shipment_urgency_id,
            'quantity' => $shipment->quantity !== null ? (int) $shipment->quantity : null,
            'package_reference' => (string) ($shipment->package_reference ?? ''),
        ];
        $this->resetShipmentPrototypeErrors();
        $this->showShipmentModal = true;
    }

    public function setShipmentModalAddressMode(string $mode): void
    {
        $mode = $mode === OrderShipmentService::MODE_MULTIPLE_ADDRESS
            ? OrderShipmentService::MODE_MULTIPLE_ADDRESS
            : OrderShipmentService::MODE_SAME_ADDRESS;
        $this->shipmentModalMode = $mode;

        if (! $this->shipmentModalTaskId) return;

        $task = $this->shipmentPrototypeTask($this->shipmentModalTaskId, 'SHIP_CONFIRM_INFO');
        $taskShipments = $task->job?->shipments ?? collect();
        $primary = $taskShipments->firstWhere('is_primary', true) ?: $taskShipments->sortBy('sequence')->first();

        if ($mode === OrderShipmentService::MODE_SAME_ADDRESS) {
            if ($primary) {
                foreach (['recipient', 'phone_country_code', 'phone', 'address', 'city', 'state', 'postal_code', 'country', 'shipping_source_address_id'] as $field) {
                    $this->shipmentForm[$field] = $primary->{$field};
                }
            }
        } elseif ($this->shipmentEditingId) {
            // Restore the shipment's own address when an edit switches from
            // "same as Shipment 1" back to a different delivery address.
            $editing = $taskShipments->firstWhere('id', (int) $this->shipmentEditingId)
                ?: $this->shipmentForTask($task, (int) $this->shipmentEditingId);
            foreach (['recipient', 'phone_country_code', 'phone', 'address', 'city', 'state', 'postal_code', 'country', 'shipping_source_address_id'] as $field) {
                $this->shipmentForm[$field] = $editing->{$field};
            }
        } else {
            foreach (['recipient', 'phone_country_code', 'phone', 'address', 'city', 'state', 'postal_code'] as $field) {
                $this->shipmentForm[$field] = '';
            }
            $this->shipmentForm['country'] = $this->defaultShipmentCountry();
            $this->shipmentForm['shipping_source_address_id'] = null;
        }

        $this->resetShipmentPrototypeErrors();
    }

    public function selectShipmentModalMethod(int $methodId, ?int $urgencyId = null): void
    {
        $this->shipmentForm['shipment_method_id'] = $methodId > 0 ? $methodId : null;
        $this->shipmentForm['shipment_urgency_id'] = $urgencyId && $urgencyId > 0 ? $urgencyId : null;
        $this->resetValidation('shipmentMethod');
    }

    /**
     * Keep State selection tied to the Country master row. Livewire calls this
     * hook when the reusable Country search-select updates shipmentForm.country.
     */
    public function updatedShipmentForm(mixed $value, string $key): void
    {
        if ($key !== 'country') {
            return;
        }

        $this->shipmentForm['country'] = trim((string) $value);
        $this->shipmentForm['state'] = '';
        $this->resetValidation(['shipmentForm.country', 'shipmentForm.state']);
    }

    public function saveShipment(): void
    {
        abort_unless($this->shipmentModalTaskId, 422);
        $task = $this->shipmentPrototypeTask((int) $this->shipmentModalTaskId, 'SHIP_CONFIRM_INFO');
        $payload = array_merge($this->shipmentForm, ['address_mode' => $this->shipmentModalMode]);
        $this->resetShipmentPrototypeErrors();

        try {
            if ($this->shipmentEditingId) {
                $shipment = $this->shipmentForTask($task, (int) $this->shipmentEditingId);
                app(OrderShipmentService::class)->updateShipment($task, $shipment, auth()->user(), $payload);
                $message = 'Shipment updated.';
            } else {
                app(OrderShipmentService::class)->addShipment($task, auth()->user(), $payload);
                $message = 'Shipment added.';
            }
        } catch (ValidationException $exception) {
            $this->applyShipmentValidation($exception);
            return;
        }

        $this->closeShipmentModal();
        session()->flash('success', $message);
    }

    public function closeShipmentModal(): void
    {
        $this->showShipmentModal = false;
        $this->shipmentModalTaskId = null;
        $this->shipmentEditingId = null;
        $this->shipmentModalMode = OrderShipmentService::MODE_SAME_ADDRESS;
        $this->shipmentForm = [];
        $this->resetShipmentPrototypeErrors();
    }

    public function removeOrderShipment(int $taskId, int $shipmentId): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_CONFIRM_INFO');
        $shipment = $this->shipmentForTask($task, $shipmentId);

        try {
            app(OrderShipmentService::class)->removeShipment($task, $shipment, auth()->user());
        } catch (ValidationException $exception) {
            $this->applyShipmentValidation($exception);
            return;
        }

        session()->flash('success', 'Shipment removed.');
    }

    public function selectOrderShipmentMethod(int $taskId, int $shipmentId, int $methodId, ?int $urgencyId = null): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_CONFIRM_INFO');
        $shipment = $this->shipmentForTask($task, $shipmentId);
        $this->resetValidation('shipmentMethod');

        try {
            app(OrderShipmentService::class)->updateShippingMethod($task, $shipment, auth()->user(), $methodId, $urgencyId);
        } catch (ValidationException $exception) {
            $this->applyShipmentValidation($exception);
            return;
        }
    }

    public function saveOrderShipmentTracking(int $taskId, int $shipmentId, int $courierId, string $trackingNumber): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_LABEL', true);
        $shipment = $this->shipmentForTask($task, $shipmentId);
        $this->resetValidation('shipmentTracking');

        try {
            app(OrderShipmentService::class)->saveTracking($task, $shipment, auth()->user(), $courierId, $trackingNumber);
        } catch (ValidationException $exception) {
            $this->applyShipmentValidation($exception);
            return;
        }

        session()->flash('success', 'Courier and tracking number saved.');
    }

    public function dispatchOrderShipment(int $taskId, int $shipmentId): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_PACKAGE', true);
        $shipment = $this->shipmentForTask($task, $shipmentId);
        $this->resetValidation('shipmentDispatch');

        try {
            app(OrderShipmentService::class)->dispatch($task, $shipment, auth()->user());
        } catch (ValidationException $exception) {
            $this->applyShipmentValidation($exception);
            return;
        }

        $this->syncOverviewWorkflowSelectionToCurrentPhase();
        session()->flash('success', 'Shipment marked as dispatched.');
    }

    public function openOrderShipmentDetails(int $taskId, int $shipmentId): void
    {
        $task = $this->shipmentPrototypeTask($taskId, 'SHIP_PACKAGE');
        $shipment = $this->shipmentForTask($task, $shipmentId);
        $this->shipmentDetailsId = $shipment->id;
        $this->showShipmentDetailsModal = true;
    }

    public function closeOrderShipmentDetails(): void
    {
        $this->showShipmentDetailsModal = false;
        $this->shipmentDetailsId = null;
    }

    /** Reset Shipment-only transient UI state when changing/closing Orders. */
    private function resetShipmentPrototypeUi(): void
    {
        $this->showShipmentModal = false;
        $this->shipmentModalTaskId = null;
        $this->shipmentEditingId = null;
        $this->shipmentModalMode = OrderShipmentService::MODE_SAME_ADDRESS;
        $this->shipmentForm = [];
        $this->showShipmentDetailsModal = false;
        $this->shipmentDetailsId = null;
        $this->resetShipmentPrototypeErrors();
    }

    private function shipmentPrototypeTask(int $taskId, string $expectedKey, bool $mustBeActionable = false): Task
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job.shipments.shippingMethod', 'job.shipments.shipmentUrgency', 'job.shipments.courier', 'job.phase', 'setupTemplate'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);
        abort_unless(app(OrderWorkflowActionService::class)->automationKey($task) === $expectedKey, 422);
        abort_if(strcasecmp((string) $task->job?->status, 'Cancelled') === 0, 422, 'Cancelled Orders cannot be edited.');

        $completed = (bool) $task->completed_at || \App\Support\BoardLaneResolver::isCompleted((string) $task->status);
        $currentStage = (int) $task->workflow_phase_id === (int) $task->job?->workflow_phase_id;
        abort_unless($completed || $currentStage, 422, 'This Shipment task is not available yet.');

        if ($mustBeActionable && ! $completed) {
            app(OrderTaskSequenceService::class)->assertStatusActionable($task);
        }

        return $task;
    }

    private function shipmentForTask(Task $task, int $shipmentId): OrderShipment
    {
        return OrderShipment::query()
            ->with(['shippingMethod', 'shipmentUrgency', 'courier'])
            ->where('flow_job_id', $task->flow_job_id)
            ->findOrFail($shipmentId);
    }

    private function resetShipmentPrototypeErrors(): void
    {
        $this->resetValidation([
            'shipmentSettings',
            'shipmentMethod',
            'shipmentTracking',
            'shipmentDispatch',
            'shipmentForm',
            'shipmentForm.recipient',
            'shipmentForm.phone_country_code',
            'shipmentForm.phone',
            'shipmentForm.address',
            'shipmentForm.city',
            'shipmentForm.state',
            'shipmentForm.postal_code',
            'shipmentForm.country',
            'shipmentForm.quantity',
        ]);
    }

    private function defaultShipmentCountry(): string
    {
        return app(LocationMasterDataService::class)->defaultCountryName();
    }

    private function applyShipmentValidation(ValidationException $exception): void
    {
        foreach ($exception->errors() as $field => $messages) {
            foreach ((array) $messages as $message) {
                $this->addError($field, $message);
            }
        }
    }
}
