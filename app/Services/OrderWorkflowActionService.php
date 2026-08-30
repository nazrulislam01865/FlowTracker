<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Backend action engine for the prototype-matched Order Details task flow.
 *
 * The prototype defines the user interaction (direct actions, purpose-built
 * dialogs, revision loops and conditional branches). This service preserves
 * those rules server-side so Blade only renders the UI and never decides which
 * task/stage unlocks next.
 */
class OrderWorkflowActionService
{
    private const TITLE_KEYS = [
        'upload purchase order' => 'NEW_UPLOAD_PO',
        'send purchase order to artwork team' => 'NEW_SEND_PO_ARTWORK',
        'prepare & upload artwork' => 'ART_PREPARE_UPLOAD',
        'prepare and upload artwork' => 'ART_PREPARE_UPLOAD',
        'internal artwork review' => 'ART_INTERNAL_REVIEW',
        'send artwork to order team' => 'ART_SEND_ORDER_TEAM',
        'client erp / approval' => 'ART_CLIENT_ERP_DECISION',
        'sample approval (when required)' => 'ART_SAMPLE_APPROVAL',
        'set estimated delivery date' => 'PROD_SET_ESTIMATED_DELIVERY',
        'start production' => 'PROD_START',
        'monitor / resolve production issue' => 'PROD_ISSUE',
        'finish production' => 'PROD_FINISH',
        'perform qc check' => 'QC_CHECK',
        'resolve qc issue (when needed)' => 'QC_ISSUE',
        'approve for shipment' => 'QC_APPROVE_SHIPMENT',
        'confirm shipment information' => 'SHIP_CONFIRM_INFO',
        'generate & print courier label' => 'SHIP_LABEL',
        'ship package' => 'SHIP_PACKAGE',
        'prepare invoice' => 'BILL_PREPARE',
        'send invoice' => 'BILL_SEND',
        'receive & process payment' => 'PAY_PROCESS',
    ];

    private const DOCUMENT_ACTIONS = ['NEW_UPLOAD_PO', 'ART_PREPARE_UPLOAD', 'ART_SAMPLE_APPROVAL'];

    public function automationKey(Task $task): ?string
    {
        $task->loadMissing('setupTemplate');
        $key = trim((string) ($task->setupTemplate?->automation_key ?? ''));
        if ($key !== '') return $key;

        return self::TITLE_KEYS[strtolower(trim((string) $task->title))] ?? null;
    }

    /**
     * Presentation descriptor only. $hasEvidence is supplied by the already
     * hydrated Order detail context so rendering the task list performs no
     * per-task evidence queries.
     */
    public function descriptor(Task $task, ?bool $hasEvidence = null): array
    {
        $key = $this->automationKey($task);
        $status = strtolower(trim((string) $task->status));
        $hasEvidence ??= $this->loadedEvidenceState($task);

        $label = match ($key) {
            'NEW_UPLOAD_PO' => 'Upload Purchase Order',
            'NEW_SEND_PO_ARTWORK' => 'Send to Artwork Team',
            'ART_PREPARE_UPLOAD' => $hasEvidence ? 'Upload Revised Artwork' : 'Upload Artwork',
            'ART_INTERNAL_REVIEW' => 'Review Artwork',
            'ART_SEND_ORDER_TEAM' => 'Send Artwork',
            'ART_CLIENT_ERP_DECISION' => str_contains($status, 'waiting for client') || str_contains($status, 'client approval')
                ? 'Record Client Decision'
                : 'Artwork Uploaded to Client ERP',
            'ART_SAMPLE_APPROVAL' => 'Upload Sample Approval',
            'PROD_SET_ESTIMATED_DELIVERY' => 'Set date',
            'PROD_START' => 'Production Ongoing',
            'PROD_ISSUE' => str_contains($status, 'issue') ? 'Issue Resolved' : 'Monitor Production',
            'PROD_FINISH' => 'Production Finished',
            'QC_CHECK' => 'Open QC Check',
            'QC_ISSUE' => str_contains($status, 'issue') ? 'Issue Resolved' : 'Continue',
            'QC_APPROVE_SHIPMENT' => 'Proceed to Shipment',
            'SHIP_CONFIRM_INFO' => 'Review Information',
            'SHIP_LABEL' => str_contains($status, 'label generated') ? 'Preview / Print' : 'Generate Label',
            'SHIP_PACKAGE' => 'Ship Package',
            'BILL_PREPARE' => 'Prepare Invoice',
            'BILL_SEND' => 'Preview & Send',
            'PAY_PROCESS' => 'Record Payment',
            default => 'Take action',
        };

        $interaction = match (true) {
            in_array($key, self::DOCUMENT_ACTIONS, true) => 'document',
            in_array($key, ['PROD_START', 'PROD_FINISH', 'QC_APPROVE_SHIPMENT'], true) => 'direct',
            $key === 'SHIP_LABEL' && ! str_contains($status, 'label generated') => 'direct',
            default => 'modal',
        };

        return ['key' => $key, 'label' => $label, 'type' => $interaction, 'interaction' => $interaction];
    }

    public function modalCopy(Task $task): array
    {
        $key = $this->automationKey($task);
        $status = strtolower(trim((string) $task->status));

        return match ($key) {
            'NEW_SEND_PO_ARTWORK' => [
                'variant' => 'purchase_order_email',
                'title' => 'Send Purchase Order to Artwork Team',
                'copy' => 'Review the destination team and uploaded Purchase Order before sending.',
                'choices' => ['confirm' => 'Send Purchase Order'],
            ],
            'ART_INTERNAL_REVIEW' => [
                'variant' => 'artwork_review',
                'title' => 'Review Artwork',
                'copy' => 'Review the latest uploaded artwork before sending it to the Order Team.',
                'choices' => ['revise' => 'Revise Artwork', 'confirm' => 'Artwork Confirmed'],
            ],
            'ART_SEND_ORDER_TEAM' => [
                'variant' => 'artwork_email',
                'title' => 'Send Artwork to Order Team',
                'copy' => 'Preview the latest confirmed artwork and internal handoff before sending.',
                'choices' => ['confirm' => 'Send Artwork'],
            ],
            'ART_CLIENT_ERP_DECISION' => str_contains($status, 'waiting for client') || str_contains($status, 'client approval')
                ? [
                    'variant' => 'client_decision',
                    'title' => 'Record Client Artwork Decision',
                    'copy' => 'Choose the decision received from the client for the latest artwork.',
                    'choices' => ['revise' => 'Client Requested Revision', 'approved' => 'Client Approved Artwork'],
                ]
                : [
                    'variant' => 'client_erp',
                    'title' => 'Confirm Client ERP Upload',
                    'copy' => 'Confirm that the latest artwork has been uploaded to the client ERP.',
                    'choices' => ['erp_uploaded' => 'Artwork Uploaded'],
                ],
            'PROD_SET_ESTIMATED_DELIVERY' => [
                'variant' => 'estimated_delivery',
                'title' => 'Set estimated delivery date',
                'copy' => 'Assign the estimated delivery date before Production can start.',
                'choices' => ['confirm' => 'Save date'],
            ],
            'PROD_ISSUE' => str_contains($status, 'issue')
                ? [
                    'variant' => 'issue_resolution',
                    'issue_type' => 'Production',
                    'title' => 'Resolve Production Issue',
                    'copy' => 'Record the supplier/corrective resolution before Production can finish.',
                    'choices' => ['resolve' => 'Issue Resolved'],
                ]
                : [
                    'variant' => 'production_check',
                    'title' => 'Production Check',
                    'copy' => 'Report a production issue or confirm there is no open issue.',
                    'choices' => ['issue' => 'Report Issue', 'confirm' => 'No Issue'],
                ],
            'QC_CHECK' => [
                'variant' => 'qc_check',
                'title' => 'Product Quality Check',
                'copy' => 'Record received/inspected quantities and either pass QC or report an issue.',
                'choices' => ['issue' => 'Report Issue', 'pass' => 'QC Passed'],
            ],
            'QC_ISSUE' => [
                'variant' => 'issue_resolution',
                'issue_type' => 'QC',
                'title' => 'Resolve QC Issue',
                'copy' => 'Record the corrective action before Shipment can be activated.',
                'choices' => ['resolve' => 'Issue Resolved'],
            ],
            'SHIP_CONFIRM_INFO' => [
                'variant' => 'shipment_info',
                'title' => 'Shipment Information',
                'copy' => 'Confirm recipient, package and declared-value information before generating the courier label.',
                'choices' => ['confirm' => 'Confirm Information'],
            ],
            'SHIP_LABEL' => [
                'variant' => 'courier_label',
                'title' => 'Courier Label Preview',
                'copy' => 'Review the generated shipping label and confirm it has been printed.',
                'choices' => ['print' => 'Print Label'],
            ],
            'SHIP_PACKAGE' => [
                'variant' => 'ship_package',
                'title' => 'Ship the Package',
                'copy' => 'Record the carrier, tracking number and shipment date.',
                'choices' => ['confirm' => 'Confirm Shipment'],
            ],
            'BILL_PREPARE' => [
                'variant' => 'invoice_prepare',
                'title' => 'Prepare Bulk Invoice',
                'copy' => 'Prepare the invoice details for this shipped Order.',
                'choices' => ['confirm' => 'Prepare Bulk Invoice'],
            ],
            'BILL_SEND' => [
                'variant' => 'invoice_send',
                'title' => 'Send Invoice to Client',
                'copy' => 'Preview the invoice email before sending it to the client.',
                'choices' => ['confirm' => 'Send Invoice'],
            ],
            'PAY_PROCESS' => [
                'variant' => 'payment',
                'title' => 'Record Client Payment',
                'copy' => 'Record the payment amount, date and reference.',
                'choices' => ['confirm' => 'Process Payment'],
            ],
            default => [
                'variant' => 'confirm',
                'title' => $this->descriptor($task)['label'],
                'copy' => 'Confirm this workflow action.',
                'choices' => ['confirm' => $this->descriptor($task)['label']],
            ],
        };
    }

    /** @return array<string, mixed> */
    public function initialPayload(Task $task, FlowJob $job): array
    {
        $invoice = $job->activities()
            ->where('event', 'job.workflow_invoice_prepared')
            ->latest('id')
            ->first();
        $total = $this->orderTotal($job);
        $paid = $this->recordedPaymentTotal($job);
        $units = (int) $job->items->filter(fn ($item) => ! ($item->is_removed ?? false))->sum(fn ($item) => (int) ($item->quantity ?? 0));
        $inspected = $units > 0 ? max(1, min($units, (int) ceil($units * 0.10))) : 1;

        return [
            'qty_received' => $units ?: 1,
            'qty_inspected' => $inspected,
            'qty_accepted' => $inspected,
            'qty_rejected' => 0,
            'qc_comments' => '',
            'erp_reference' => trim((string) ($job->order_number ?: $job->job_number)),
            'issue_category' => 'Other',
            'recipient' => trim((string) ($job->client?->name ?: 'Client')).' — Receiving',
            'contact' => trim((string) (($job->shipping_phone_country_code ? $job->shipping_phone_country_code.' ' : '').($job->shipping_phone ?: ''))),
            'address' => trim((string) ($job->shipping_address ?: '')),
            'packages' => '',
            'weight' => '',
            'dimensions' => '',
            'declared_value' => $total > 0 ? number_format($total, 2, '.', '') : '',
            'carrier' => 'UPS',
            'tracking_number' => '',
            'shipment_date' => app(WorkspaceSettingsService::class)->localToday()->toDateString(),
            'estimated_delivery_date' => $job->estimated_delivery_date?->format('Y-m-d') ?: '',
            'invoice_number' => (string) data_get($invoice?->meta, 'invoice_number', ''),
            'invoice_date' => app(WorkspaceSettingsService::class)->localToday()->toDateString(),
            'invoice_amount' => $total > 0 ? number_format($total, 2, '.', '') : '0.00',
            'invoice_currency' => 'USD',
            'payment_terms' => 'Net 30',
            'invoice_due_date' => app(WorkspaceSettingsService::class)->localToday()->addDays(30)->toDateString(),
            'payment_amount' => max(0, $total - $paid) > 0 ? number_format(max(0, $total - $paid), 2, '.', '') : '',
            'payment_date' => app(WorkspaceSettingsService::class)->localToday()->toDateString(),
            'payment_reference' => '',
            'payment_notes' => '',
        ];
    }

    /**
     * Apply a task action. All task completion goes through TaskService so the
     * normal sequencing, progress, audit and automatic stage advance hooks run.
     *
     * @param array<string,mixed> $payload
     */
    public function perform(Task $task, User $actor, ?string $decision = null, ?string $comment = null, array $payload = []): Task
    {
        return DB::transaction(function () use ($task, $actor, $decision, $comment, $payload): Task {
            $locked = Task::query()->whereKey($task->id)->lockForUpdate()->with(['job.phase', 'setupTemplate'])->firstOrFail();
            $job = FlowJob::query()->whereKey($locked->flow_job_id)->lockForUpdate()->with(['client', 'items'])->firstOrFail();
            abort_unless((int) $locked->workflow_phase_id === (int) $job->workflow_phase_id, 422, 'This task is locked until its workflow stage is active.');
            app(OrderTaskSequenceService::class)->assertStatusActionable($locked);

            $key = $this->automationKey($locked);
            $decision = strtolower(trim((string) $decision));
            $comment = trim((string) $comment);

            if ($key === 'ART_INTERNAL_REVIEW' && $decision === 'revise') {
                $this->requireComment($comment, 'Add revision instructions before requesting a revision.');
                $this->restartArtwork($locked, $actor, $comment, 'Internal artwork revision requested');
                return $locked->refresh();
            }

            if ($key === 'ART_CLIENT_ERP_DECISION' && $decision === 'erp_uploaded') {
                $reference = trim((string) ($payload['erp_reference'] ?? ''));
                $locked->update(['status' => 'Waiting for Client', 'completed_at' => null, 'progress' => 60]);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.artwork_client_erp_uploaded',
                    'description' => 'Artwork uploaded to Client ERP'.($reference !== '' ? ' · Reference '.$reference : '').'.',
                    'meta' => ['erp_reference' => $reference],
                ]);
                app(JobService::class)->syncAutomaticStatus($job->refresh(), $actor);
                return $locked->refresh();
            }

            if ($key === 'ART_CLIENT_ERP_DECISION' && $decision === 'revise') {
                $this->requireComment($comment, 'Add the client revision request before continuing.');
                $this->restartArtwork($locked, $actor, $comment, 'Client artwork revision requested');
                return $locked->refresh();
            }

            if ($key === 'ART_CLIENT_ERP_DECISION' && $decision === 'sample') {
                $sample = Task::query()
                    ->where('flow_job_id', $job->id)
                    ->where('workflow_phase_id', $locked->workflow_phase_id)
                    ->whereNotNull('task_pack_task_id')
                    ->with('setupTemplate')
                    ->get()
                    ->first(fn (Task $candidate) => $this->automationKey($candidate) === 'ART_SAMPLE_APPROVAL');
                abort_unless($sample, 422, 'The Sample Approval task is not configured for this Order.');

                $ready = app(OrderTaskFlagService::class)->readyStatus();
                $readyId = app(OrderTaskFlagService::class)->statusRecord($ready, false)?->id;
                $locked->update(['status' => 'Waiting for Sample Approval', 'completed_at' => null, 'progress' => 80]);
                $sample->update([
                    'status' => $ready,
                    'order_task_status_id' => $readyId,
                    'completed_at' => null,
                    'progress' => 0,
                    'start_date' => $sample->start_date ?: app(WorkspaceSettingsService::class)->localToday(),
                ]);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.sample_required',
                    'description' => 'Client approved artwork. Sample approval is required before Production.',
                ]);
                return $locked->refresh();
            }

            if ($key === 'ART_CLIENT_ERP_DECISION' && $decision === 'confirm') {
                // Prototype normal path: Client approved the artwork and chose
                // NO sample/swatch. The optional Sample Approval branch must be
                // explicitly skipped before the client-decision task completes;
                // otherwise an untouched optional row can look actionable and
                // its document requirement can keep Artwork from advancing.
                $sample = Task::query()
                    ->where('flow_job_id', $job->id)
                    ->where('workflow_phase_id', $locked->workflow_phase_id)
                    ->whereNotNull('task_pack_task_id')
                    ->with('setupTemplate')
                    ->get()
                    ->first(fn (Task $candidate) => $this->automationKey($candidate) === 'ART_SAMPLE_APPROVAL');

                if ($sample && ! $sample->completed_at) {
                    $sample->update([
                        'status' => 'Skipped',
                        'order_task_status_id' => null,
                        'completed_at' => null,
                        'progress' => 0,
                    ]);
                }

                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.sample_not_required',
                    'description' => 'Client approved artwork. No sample or swatch is required; Production can start directly.',
                ]);

                return $this->complete($locked, $actor);
            }

            if ($key === 'PROD_SET_ESTIMATED_DELIVERY') {
                $estimatedDeliveryDate = trim((string) ($payload['estimated_delivery_date'] ?? ''));
                if ($estimatedDeliveryDate === '') {
                    throw ValidationException::withMessages([
                        'orderWorkflowActionPayload.estimated_delivery_date' => 'Estimated delivery date is required.',
                    ]);
                }

                $parsedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $estimatedDeliveryDate);
                if (! $parsedDate || $parsedDate->format('Y-m-d') !== $estimatedDeliveryDate) {
                    throw ValidationException::withMessages([
                        'orderWorkflowActionPayload.estimated_delivery_date' => 'Enter a valid estimated delivery date.',
                    ]);
                }

                $job->update(['estimated_delivery_date' => $estimatedDeliveryDate]);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.estimated_delivery_date_set',
                    'description' => 'Estimated delivery date set to '.$parsedDate->format('M j, Y').'.',
                    'meta' => ['estimated_delivery_date' => $estimatedDeliveryDate],
                ]);

                return $this->complete($locked, $actor);
            }

            if ($key === 'QC_CHECK' && $decision === 'issue') {
                $this->validateQcPayload($payload);
                $this->requireComment($comment, 'Describe the QC issue before reporting it.');
                $issueTask = Task::query()
                    ->where('flow_job_id', $job->id)
                    ->where('workflow_phase_id', $locked->workflow_phase_id)
                    ->whereNotNull('task_pack_task_id')
                    ->with('setupTemplate')
                    ->get()
                    ->first(fn (Task $candidate) => $this->automationKey($candidate) === 'QC_ISSUE');
                abort_unless($issueTask, 422, 'The QC issue task is not configured for this Order.');

                $ready = app(OrderTaskFlagService::class)->readyStatus();
                $locked->update(['status' => 'Waiting for QC Issue Resolution', 'completed_at' => null, 'progress' => 70]);
                $issueTask->update([
                    'status' => 'QC Issue Reported',
                    'order_task_status_id' => app(OrderTaskFlagService::class)->statusRecord($ready, false)?->id,
                    'completed_at' => null,
                    'progress' => 50,
                    'start_date' => $issueTask->start_date ?: app(WorkspaceSettingsService::class)->localToday(),
                ]);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.qc_issue_reported',
                    'description' => $comment,
                    'meta' => $this->qcMeta($payload),
                ]);
                return $locked->refresh();
            }

            if ($key === 'QC_CHECK' && in_array($decision, ['pass', 'confirm'], true)) {
                $this->validateQcPayload($payload);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.qc_passed',
                    'description' => 'QC passed.',
                    'meta' => $this->qcMeta($payload),
                ]);
                return $this->complete($locked, $actor);
            }

            if (in_array($key, ['PROD_ISSUE', 'QC_ISSUE'], true) && $decision === 'issue') {
                $this->requireComment($comment, 'Describe the issue before reporting it.');
                $category = trim((string) ($payload['issue_category'] ?? 'Other')) ?: 'Other';
                $locked->update([
                    'status' => $key === 'PROD_ISSUE' ? 'Production Issue Reported' : 'QC Issue Reported',
                    'completed_at' => null,
                    'progress' => 50,
                ]);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => $key === 'PROD_ISSUE' ? 'job.production_issue_reported' : 'job.qc_issue_reported',
                    'description' => $comment,
                    'meta' => ['category' => $category],
                ]);
                app(JobService::class)->syncAutomaticStatus($job->refresh(), $actor);
                return $locked->refresh();
            }

            if (in_array($key, ['PROD_ISSUE', 'QC_ISSUE'], true) && $decision === 'resolve') {
                $this->requireComment($comment, 'Add the resolution before closing this issue.');
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => $key === 'PROD_ISSUE' ? 'job.production_issue_resolved' : 'job.qc_issue_resolved',
                    'description' => $comment,
                ]);
                $completed = $this->complete($locked, $actor);
                if ($key === 'QC_ISSUE') {
                    $waitingQc = Task::query()
                        ->where('flow_job_id', $job->id)
                        ->where('workflow_phase_id', $locked->workflow_phase_id)
                        ->where('status', 'Waiting for QC Issue Resolution')
                        ->with('setupTemplate')
                        ->get()
                        ->first(fn (Task $candidate) => $this->automationKey($candidate) === 'QC_CHECK');
                    if ($waitingQc) $this->complete($waitingQc, $actor);
                }
                return $completed;
            }

            if ($key === 'SHIP_CONFIRM_INFO') {
                $this->validateShipmentInfo($payload);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.shipment_information_confirmed',
                    'description' => 'Shipment information confirmed.',
                    'meta' => $this->onlyPayload($payload, ['recipient','contact','address','packages','weight','dimensions','declared_value']),
                ]);
                return $this->complete($locked, $actor);
            }

            if ($key === 'SHIP_LABEL' && $decision === 'generate') {
                $locked->update(['status' => 'Courier Label Generated', 'completed_at' => null, 'progress' => 55]);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.courier_label_generated',
                    'description' => 'Courier label generated and ready for preview/print.',
                ]);
                app(JobService::class)->syncAutomaticStatus($job->refresh(), $actor);
                return $locked->refresh();
            }

            if ($key === 'SHIP_LABEL' && in_array($decision, ['print', 'confirm'], true)) {
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.courier_label_printed',
                    'description' => 'Courier label preview confirmed and marked as printed.',
                ]);
                return $this->complete($locked, $actor);
            }

            if ($key === 'SHIP_PACKAGE') {
                $this->validateShipPackage($payload);
                $carrier = trim((string) $payload['carrier']);
                $tracking = trim((string) $payload['tracking_number']);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.package_shipped',
                    'description' => $carrier.' tracking '.$tracking.' recorded.',
                    'meta' => $this->onlyPayload($payload, ['carrier','tracking_number','shipment_date','estimated_delivery_date']),
                ]);
                return $this->complete($locked, $actor);
            }

            if ($key === 'BILL_PREPARE') {
                $this->validateInvoice($payload);
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.workflow_invoice_prepared',
                    'description' => 'Bulk invoice '.$payload['invoice_number'].' prepared.',
                    'meta' => $this->onlyPayload($payload, ['invoice_number','invoice_date','invoice_amount','invoice_currency','payment_terms','invoice_due_date']),
                ]);
                return $this->complete($locked, $actor);
            }

            if ($key === 'BILL_SEND') {
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.workflow_invoice_sent',
                    'description' => 'Invoice preview confirmed and sent to the client.',
                ]);
                return $this->complete($locked, $actor);
            }

            if (in_array($key, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true)) {
                abort_unless($decision === 'confirm', 422, 'Confirm the email handoff before sending.');

                app(\App\Services\Orders\OrderWorkflowEmailService::class)->send($locked, $actor);

                return $this->complete($locked, $actor);
            }

            if ($key === 'PAY_PROCESS') {
                $this->validatePayment($payload);
                $amount = (float) $payload['payment_amount'];
                $total = $this->orderTotal($job);
                $alreadyPaid = $this->recordedPaymentTotal($job);
                $newPaid = $alreadyPaid + $amount;
                $job->activities()->create([
                    'user_id' => $actor->id,
                    'event' => 'job.workflow_payment_recorded',
                    'description' => 'Payment '.number_format($amount, 2).' recorded.',
                    'meta' => $this->onlyPayload($payload, ['payment_amount','payment_date','payment_reference','payment_notes']),
                ]);

                if ($total > 0 && $newPaid + 0.0001 < $total) {
                    $progress = max(1, min(99, (int) round(($newPaid / $total) * 100)));
                    $locked->update(['status' => 'Partially Paid', 'completed_at' => null, 'progress' => $progress]);
                    app(JobService::class)->syncAutomaticStatus($job->refresh(), $actor);
                    return $locked->refresh();
                }

                return $this->complete($locked, $actor);
            }

            // Client approved with no sample and normal task confirmations
            // finish through the common lifecycle. Email handoffs are handled
            // above so a task cannot complete unless delivery is accepted.
            return $this->complete($locked, $actor);
        }, 3);
    }

    /**
     * Explicit escape hatch for the two email-blocking workflow handoffs.
     * This can only be called with the server-side failure marker created after
     * the current user exhausted all three delivery attempts for this task.
     *
     * @param array<string,mixed> $failure
     */
    public function completeEmailHandoffAfterFailure(Task $task, User $actor, array $failure): Task
    {
        return DB::transaction(function () use ($task, $actor, $failure): Task {
            $locked = Task::query()
                ->whereKey($task->id)
                ->lockForUpdate()
                ->with(['job.phase', 'setupTemplate'])
                ->firstOrFail();
            $job = FlowJob::query()
                ->whereKey($locked->flow_job_id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless((int) $locked->workflow_phase_id === (int) $job->workflow_phase_id, 422, 'This task is locked until its workflow stage is active.');
            app(OrderTaskSequenceService::class)->assertStatusActionable($locked);

            $key = $this->automationKey($locked);
            abort_unless(in_array($key, ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true), 422, 'This task does not support manual completion after email failure.');
            abort_unless((int) ($failure['task_id'] ?? 0) === (int) $locked->id, 422, 'The email-failure confirmation does not belong to this task.');
            abort_unless((string) ($failure['handoff_key'] ?? '') === (string) $key, 422, 'The email-failure confirmation is no longer valid for this task.');
            abort_unless((int) ($failure['attempts'] ?? 0) >= 3, 422, 'Manual completion is available only after three failed email delivery attempts.');

            $attachmentLabel = $key === 'ART_SEND_ORDER_TEAM' ? 'Artwork' : 'Purchase Order';
            $job->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.workflow_email_manual_completion',
                'description' => $attachmentLabel.' email delivery failed after three attempts. The task was manually completed so the file can be sent outside FlowTrack.',
                'meta' => [
                    'task_id' => (int) $locked->id,
                    'document_id' => (int) ($failure['document_id'] ?? 0) ?: null,
                    'document_name' => (string) ($failure['document_name'] ?? ''),
                    'delivery_attempts' => (int) ($failure['attempts'] ?? 3),
                    'tracking_id' => (string) ($failure['tracking_id'] ?? ''),
                    'manual_delivery_required' => true,
                ],
            ]);

            return $this->complete($locked, $actor);
        }, 3);
    }

    public function afterDocumentAdded(Task $task, User $actor): void
    {
        $task->refresh()->loadMissing(['job.phase', 'setupTemplate']);
        $key = $this->automationKey($task);
        if (! in_array($key, self::DOCUMENT_ACTIONS, true)) return;

        if ($key === 'ART_SAMPLE_APPROVAL') {
            $this->complete($task, $actor);
            $waiting = Task::query()
                ->where('flow_job_id', $task->flow_job_id)
                ->where('workflow_phase_id', $task->workflow_phase_id)
                ->where('status', 'Waiting for Sample Approval')
                ->with('setupTemplate')
                ->get()
                ->first(fn (Task $candidate) => $this->automationKey($candidate) === 'ART_CLIENT_ERP_DECISION');
            if ($waiting) $this->complete($waiting, $actor);
            return;
        }

        // PO and artwork upload are file-backed actions in the prototype.
        $this->complete($task, $actor);
    }

    private function complete(Task $task, User $actor): Task
    {
        return app(TaskService::class)->moveStatus($task, app(OrderTaskFlagService::class)->completedStatus(), $actor);
    }

    private function restartArtwork(Task $current, User $actor, string $comment, string $description): void
    {
        $job = $current->job;
        $tasks = Task::query()
            ->where('flow_job_id', $current->flow_job_id)
            ->where('workflow_phase_id', $current->workflow_phase_id)
            ->whereNotNull('task_pack_task_id')
            ->with('setupTemplate')
            ->get();
        $upload = $tasks->first(fn (Task $candidate) => $this->automationKey($candidate) === 'ART_PREPARE_UPLOAD');
        abort_unless($upload, 422, 'Artwork upload task is not configured.');

        $ready = app(OrderTaskFlagService::class)->readyStatus();
        $notStarted = app(OrderTaskFlagService::class)->notStartedStatus();
        $upload->update([
            'status' => $ready,
            'order_task_status_id' => app(OrderTaskFlagService::class)->statusRecord($ready, false)?->id,
            'completed_at' => null,
            'progress' => 0,
            'start_date' => app(WorkspaceSettingsService::class)->localToday(),
        ]);
        foreach ($tasks as $candidate) {
            if ((int) $candidate->id === (int) $upload->id) continue;
            $candidate->update([
                'status' => $notStarted,
                'order_task_status_id' => app(OrderTaskFlagService::class)->statusRecord($notStarted, false)?->id,
                'completed_at' => null,
                'progress' => 0,
            ]);
        }
        // Freeze the exact artwork file that the revision request refers to.
        // This keeps the revision panel historically correct after a revised
        // artwork is uploaded later and becomes the task's newest document.
        $referenceDocumentId = (int) ($upload->documents()
            ->latest('id')
            ->value('id') ?? 0);

        $job?->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.artwork_revision_requested',
            'description' => $description.': '.$comment,
            'meta' => [
                'revision_comment' => $comment,
                'source_task_id' => (int) $current->id,
                'target_task_id' => (int) $upload->id,
                'workflow_phase_id' => (int) $current->workflow_phase_id,
                'reference_document_id' => $referenceDocumentId > 0 ? $referenceDocumentId : null,
            ],
        ]);
    }

    private function loadedEvidenceState(Task $task): bool
    {
        if ($task->relationLoaded('documents') && $task->documents->isNotEmpty()) return true;
        if ($task->relationLoaded('links') && $task->links->isNotEmpty()) return true;
        return false;
    }

    private function requireComment(string $comment, string $message): void
    {
        if ($comment === '') {
            throw ValidationException::withMessages(['orderWorkflowActionComment' => $message]);
        }
    }

    /** @param array<string,mixed> $payload */
    private function validateQcPayload(array $payload): void
    {
        $received = max(0, (int) ($payload['qty_received'] ?? 0));
        $inspected = max(0, (int) ($payload['qty_inspected'] ?? 0));
        $accepted = max(0, (int) ($payload['qty_accepted'] ?? 0));
        $rejected = max(0, (int) ($payload['qty_rejected'] ?? 0));

        $errors = [];
        if ($received <= 0) {
            $errors['orderWorkflowActionPayload.qty_received'] = 'Quantity received is required.';
        }
        if ($inspected <= 0) {
            $errors['orderWorkflowActionPayload.qty_inspected'] = 'Quantity inspected is required.';
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        if ($inspected > $received || ($accepted + $rejected) > $inspected) {
            throw ValidationException::withMessages(['orderWorkflowActionPayload.qty_inspected' => 'QC quantities are inconsistent.']);
        }
    }

    /** @param array<string,mixed> $payload */
    private function qcMeta(array $payload): array
    {
        return $this->onlyPayload($payload, ['qty_received','qty_inspected','qty_accepted','qty_rejected','qc_comments','issue_category']);
    }

    /** @param array<string,mixed> $payload */
    private function validateShipmentInfo(array $payload): void
    {
        $errors = $this->requiredPayloadErrors($payload, [
            'recipient' => 'Recipient',
            'address' => 'Delivery address',
            'packages' => 'Package count',
            'weight' => 'Weight',
        ]);
        $this->throwPayloadErrors($errors);
    }

    /** @param array<string,mixed> $payload */
    private function validateShipPackage(array $payload): void
    {
        $errors = $this->requiredPayloadErrors($payload, [
            'carrier' => 'Shipping provider',
            'tracking_number' => 'Tracking number',
            'shipment_date' => 'Shipment date',
        ]);
        $this->throwPayloadErrors($errors);
    }

    /** @param array<string,mixed> $payload */
    private function validateInvoice(array $payload): void
    {
        $errors = $this->requiredPayloadErrors($payload, [
            'invoice_number' => 'Invoice number',
            'invoice_date' => 'Invoice date',
            'invoice_amount' => 'Invoice amount',
            'invoice_due_date' => 'Due date',
        ]);

        if (! isset($errors['orderWorkflowActionPayload.invoice_amount'])
            && (float) ($payload['invoice_amount'] ?? 0) <= 0) {
            $errors['orderWorkflowActionPayload.invoice_amount'] = 'Invoice amount must be greater than zero.';
        }

        $this->throwPayloadErrors($errors);
    }

    /** @param array<string,mixed> $payload */
    private function validatePayment(array $payload): void
    {
        $errors = $this->requiredPayloadErrors($payload, [
            'payment_amount' => 'Payment amount',
            'payment_date' => 'Payment date',
            'payment_reference' => 'Payment reference',
        ]);

        if (! isset($errors['orderWorkflowActionPayload.payment_amount'])
            && (float) ($payload['payment_amount'] ?? 0) <= 0) {
            $errors['orderWorkflowActionPayload.payment_amount'] = 'Payment amount must be greater than zero.';
        }

        $this->throwPayloadErrors($errors);
    }

    /**
     * Return all missing workflow payload fields at once so Livewire can render
     * every validation message directly beneath the field that needs attention.
     *
     * @param array<string,mixed> $payload
     * @param array<string,string> $fields
     * @return array<string,string>
     */
    private function requiredPayloadErrors(array $payload, array $fields): array
    {
        $errors = [];
        foreach ($fields as $key => $label) {
            if (trim((string) ($payload[$key] ?? '')) === '') {
                $errors['orderWorkflowActionPayload.'.$key] = $label.' is required.';
            }
        }
        return $errors;
    }

    /** @param array<string,string> $errors */
    private function throwPayloadErrors(array $errors): void
    {
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function orderTotal(FlowJob $job): float
    {
        if (! $job->relationLoaded('items')) $job->load('items');
        return round((float) $job->items
            ->filter(fn ($item) => ! ($item->is_removed ?? false))
            ->sum(fn ($item) => (float) ($item->unit_price ?? 0) * (int) ($item->quantity ?? 0)), 2);
    }

    private function recordedPaymentTotal(FlowJob $job): float
    {
        return round((float) $job->activities()
            ->where('event', 'job.workflow_payment_recorded')
            ->get(['meta'])
            ->sum(fn ($activity) => (float) data_get($activity->meta, 'payment_amount', 0)), 2);
    }

    /** @param array<string,mixed> $payload @param array<int,string> $keys */
    private function onlyPayload(array $payload, array $keys): array
    {
        $out = [];
        foreach ($keys as $key) $out[$key] = $payload[$key] ?? null;
        return $out;
    }
}
