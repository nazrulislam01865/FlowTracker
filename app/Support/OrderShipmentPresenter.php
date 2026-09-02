<?php

namespace App\Support;

use App\Models\FlowJob;
use App\Models\WorkflowPhase;
use App\Services\OrderWorkflowActionService;
use Illuminate\Support\Collection;

/**
 * Query-free view model for the Shipment phase on Order Details.
 *
 * The workflow/services own state transitions; this presenter only translates
 * already-hydrated models into the exact Shipment prototype vocabulary.
 */
final class OrderShipmentPresenter
{
    /** @param Collection<int,\App\Models\Task> $tasks */
    public static function isShipmentPhase(?WorkflowPhase $phase, Collection $tasks): bool
    {
        if (! $phase) return false;
        if (strcasecmp(trim((string) $phase->name), 'Shipment') === 0) return true;
        if ((int) $phase->sequence === 5) return true;

        $workflowActions = new OrderWorkflowActionService();
        return $tasks->contains(fn ($task) => in_array(
            $workflowActions->automationKey($task),
            ['SHIP_CONFIRM_INFO', 'SHIP_LABEL', 'SHIP_PACKAGE'],
            true,
        ));
    }

    /** @param Collection<int,\App\Models\Task> $tasks */
    public static function present(FlowJob $job, WorkflowPhase $phase, Collection $tasks, array $context = []): array
    {
        $workflowActions = new OrderWorkflowActionService();
        $taskByKey = $tasks->keyBy(fn ($task) => $workflowActions->automationKey($task))->all();
        $shipmentTasks = collect(['SHIP_CONFIRM_INFO', 'SHIP_LABEL', 'SHIP_PACKAGE'])
            ->map(fn (string $key) => $taskByKey[$key] ?? null)
            ->filter()
            ->values();
        $isCancelled = strcasecmp((string) $job->status, 'Cancelled') === 0;

        $rows = $shipmentTasks->map(function ($task, int $index) use ($job, $phase, $context, $workflowActions, $isCancelled): array {
            $key = $workflowActions->automationKey($task);
            $mode = OrderDetailPresenter::taskMode($job, $task);
            $permissions = data_get($context, 'taskPermissions.'.(int) $task->id, []);
            $assigneeName = trim((string) ($task->assignee?->name ?: 'Unassigned'));
            $isDone = OrderDetailPresenter::isCompletedTask($task);
            [$title, $description] = match ($key) {
                'SHIP_CONFIRM_INFO' => [
                    'Review or update shipment details',
                    'Shipment information was captured when the order was created. Review it before preparing the courier label.',
                ],
                'SHIP_LABEL' => [
                    'Add tracking number & print courier label',
                    'Select the courier, enter the tracking number, then generate and print the shipping label.',
                ],
                'SHIP_PACKAGE' => [
                    'Dispatch shipment',
                    'Complete this task only after the package has been handed to the courier.',
                ],
                default => [
                    (string) $task->title,
                    (string) ($task->description ?: $task->setupTemplate?->description ?: ''),
                ],
            };

            return [
                'task' => $task,
                'key' => $key,
                'mode' => $mode,
                'is_done' => $isDone,
                'can_edit' => (bool) data_get($permissions, 'edit', false) && ! $isCancelled,
                'can_assign' => (bool) data_get($permissions, 'assign', false) && ! $isCancelled,
                'display_code' => OrderDetailPresenter::taskDisplayCode($phase, $task, $index),
                'title' => $title,
                'description' => $description,
                'assignee_name' => $assigneeName,
                'assignee_initial' => mb_strtoupper(mb_substr($assigneeName, 0, 1)) ?: 'U',
                'assignee_avatar' => $task->assignee?->profileImageUrl(),
                'due_date' => $task->due_date?->format('M j, Y') ?: 'No due date',
                'label_generated' => $key === 'SHIP_LABEL' && str_contains(strtolower(trim((string) $task->status)), 'label generated'),
            ];
        })->values();

        $shipmentMeta = $job->relationLoaded('latestShipmentInformationActivity')
            ? (array) ($job->latestShipmentInformationActivity?->meta ?? [])
            : [];
        $labelMeta = $job->relationLoaded('latestCourierLabelActivity')
            ? (array) ($job->latestCourierLabelActivity?->meta ?? [])
            : [];

        $clientName = trim((string) ($shipmentMeta['client_name'] ?? $job->client?->name ?? 'Client'));
        $recipient = trim((string) ($shipmentMeta['contact_name'] ?? $shipmentMeta['recipient'] ?? $job->shipping_contact_name ?? $clientName));
        $countryCode = trim((string) ($shipmentMeta['phone_country_code'] ?? $job->shipping_phone_country_code ?? ''));
        $phoneNumber = trim((string) ($shipmentMeta['phone_number'] ?? $job->shipping_phone ?? ''));
        $couriers = collect((array) data_get($context, 'courierOptions', []))
            ->map(fn ($option) => [
                'value' => trim((string) ($option['value'] ?? $option['id'] ?? '')),
                'label' => trim((string) ($option['label'] ?? $option['name'] ?? $option['value'] ?? '')),
            ])
            ->filter(fn (array $option) => $option['value'] !== '')
            ->unique('value')
            ->values();
        $carrier = trim((string) ($labelMeta['carrier'] ?? data_get($couriers->first(), 'value', '')));
        if ($carrier !== '' && ! $couriers->contains(fn (array $option) => strcasecmp($option['value'], $carrier) === 0)) {
            $couriers->prepend(['value' => $carrier, 'label' => $carrier]);
        }

        return [
            'tasks' => $rows,
            'completed_count' => $rows->where('is_done', true)->count(),
            'total_count' => $rows->count(),
            'client_name' => $clientName,
            'recipient' => $recipient,
            'phone' => trim($countryCode.' '.$phoneNumber),
            'address' => trim((string) ($shipmentMeta['address'] ?? $job->shipping_address ?? '')),
            'postal_code' => trim((string) ($shipmentMeta['postal_code'] ?? $job->shipping_postal_code ?? '')),
            'carrier' => $carrier,
            'tracking' => trim((string) ($labelMeta['tracking_number'] ?? '')),
            'couriers' => $couriers->all(),
        ];
    }
}
