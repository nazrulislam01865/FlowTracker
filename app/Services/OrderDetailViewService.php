<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\User;
use App\Support\JobDetailPresenter;
use App\Support\OrderDetailPresenter;
use Illuminate\Support\Collection;

/**
 * Builds the already-authorized, query-free view context for Order Details.
 * Database hydration remains in JobService; Blade receives only presentation
 * data and loaded model relations.
 */
class OrderDetailViewService
{
    public function build(FlowJob $job, User $user, Collection $shipmentUrgencyOptions, ?Collection $courierOptions = null): array
    {
        $access = app(AccessControlService::class);
        $courierOptions ??= collect();
        $canEdit = $access->canEditVisibleJob($user, $job);
        $inactive = (bool) $job->completed_at
            || $job->status === 'Completed'
            || in_array((string) $job->status, JobService::INACTIVE_STATUSES, true);

        $shipmentUrgencyName = OrderDetailPresenter::shipmentUrgencyName($job, $shipmentUrgencyOptions);
        $workflowActions = app(OrderWorkflowActionService::class);
        $documentTaskIds = $job->relationLoaded('documents')
            ? $job->documents->pluck('task_id')->filter()->map(fn ($id) => (int) $id)->flip()
            : collect();
        $taskActionDescriptors = $job->relationLoaded('tasks')
            ? $job->tasks->mapWithKeys(function ($task) use ($workflowActions, $documentTaskIds): array {
                $hasEvidence = $documentTaskIds->has((int) $task->id)
                    || ($task->relationLoaded('links') && $task->links->isNotEmpty());
                return [(int) $task->id => $workflowActions->descriptor($task, $hasEvidence)];
            })->all()
            : [];
        $taskActionModals = $job->relationLoaded('tasks')
            ? $job->tasks->mapWithKeys(fn ($task) => [(int) $task->id => $workflowActions->modalCopy($task)])->all()
            : [];

        $taskPermissions = $job->relationLoaded('tasks')
            ? $job->tasks->mapWithKeys(fn ($task) => [(int) $task->id => [
                'edit' => $access->canEditVisibleTask($user, $task, $job),
                'assign' => $access->canAssignVisibleTask($user, $task, $job),
                'delete' => $access->can($user, 'tasks', 'delete'),
            ]])->all()
            : [];

        $workflowEmailStatuses = [];
        if ($job->relationLoaded('tasks') && $job->relationLoaded('workflowEmailActivities')) {
            $emailService = app(\App\Services\Orders\OrderWorkflowEmailService::class);
            $invoiceEmailService = app(\App\Services\Orders\OrderInvoiceWorkflowEmailService::class);
            $workflowEmailStatuses = $job->tasks
                ->filter(fn ($task) => in_array($workflowActions->automationKey($task), ['ART_SEND_ORDER_TEAM', 'BILL_SEND'], true))
                ->mapWithKeys(function ($task) use ($job, $emailService, $invoiceEmailService, $workflowActions): array {
                    $task->setRelation('job', $job);
                    $status = $workflowActions->automationKey($task) === 'BILL_SEND'
                        ? $invoiceEmailService->deliveryStatus($task)
                        : $emailService->artworkHandoffDeliveryStatus($task);

                    return [(int) $task->id => $status];
                })
                ->all();
        }

        $workflowInvoices = [];
        $hasPreparedWorkflowInvoice = $job->relationLoaded('workflowInvoiceActivities')
            && $job->workflowInvoiceActivities->isNotEmpty();
        if ($access->can($user, 'finance', 'view')
            && $hasPreparedWorkflowInvoice
            && $job->relationLoaded('tasks')
            && $job->relationLoaded('invoices')) {
            $preparedInvoice = app(\App\Services\Orders\OrderInvoiceWorkflowEmailService::class)
                ->preparedInvoice($job);

            if ($preparedInvoice) {
                $workflowInvoices = $job->tasks
                    ->filter(fn ($task) => $workflowActions->automationKey($task) === 'BILL_PREPARE')
                    ->mapWithKeys(fn ($task) => [(int) $task->id => [
                        'id' => (int) $preparedInvoice->id,
                        'invoice_number' => (string) $preparedInvoice->invoice_number,
                        'pdf_name' => (string) ($preparedInvoice->pdf_name ?: $preparedInvoice->invoice_number.'.pdf'),
                        'pdf_path' => (string) ($preparedInvoice->pdf_path ?: ''),
                    ]])
                    ->all();
            }
        }

        return [
            'team' => JobDetailPresenter::team($job),
            'canEditJob' => $canEdit,
            'canChangeOwner' => $access->isAdministrator($user),
            'canViewProducts' => $access->can($user, 'catalog_products', 'view'),
            'canEditProducts' => $canEdit && $access->can($user, 'catalog_products', 'view') && $access->can($user, 'catalog_products', 'edit'),
            'canCreateProducts' => $canEdit && $access->can($user, 'catalog_products', 'view') && $access->can($user, 'catalog_products', 'create'),
            'canDeleteProducts' => $canEdit && $access->can($user, 'catalog_products', 'view') && $access->can($user, 'catalog_products', 'delete'),
            'canCreateTask' => $access->canCreateJobTask($user, $job) && !$inactive,
            'canViewDocumentArchive' => $access->can($user, 'document_archive', 'view'),
            'canDeleteDocument' => $access->can($user, 'documents', 'delete'),
            'canUploadDocument' => $access->can($user, 'documents', 'create'),
            'canLinkDocument' => $access->can($user, 'documents', 'link'),
            'canExportDocument' => $access->can($user, 'documents', 'export'),
            'canComment' => $canEdit,
            'taskPermissions' => $taskPermissions,
            'taskActions' => $taskActionDescriptors,
            'taskActionModals' => $taskActionModals,
            'workflowEmailStatuses' => $workflowEmailStatuses,
            'workflowInvoices' => $workflowInvoices,
            'canCancel' => $canEdit && !$inactive && (int) ($job->phase?->sequence ?? 999) <= 4,
            'attentionLocked' => $inactive,
            'flagged' => (bool) ($job->attention_requested ?? false),
            'flagReason' => trim((string) ($job->attention_reason ?? '')),
            'orderFlagLabel' => (string) ($job->orderFlag?->name ?? ''),
            'shipmentUrgencyId' => OrderDetailPresenter::shipmentUrgencyId($job),
            'shipmentUrgencyName' => $shipmentUrgencyName,
            'shipmentUrgencyTone' => OrderDetailPresenter::urgencyTone($shipmentUrgencyName),
            'courierOptions' => $courierOptions
                ->map(fn ($courier) => [
                    'value' => trim((string) ($courier->name ?? '')),
                    'label' => trim((string) ($courier->name ?? '')),
                ])
                ->filter(fn (array $courier) => $courier['value'] !== '')
                ->values()
                ->all(),
        ];
    }
}
