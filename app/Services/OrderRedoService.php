<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FlowJob;
use App\Models\FlowJobItem;
use App\Models\FlowJobMember;
use App\Models\FlowJobPhaseHistory;
use App\Models\MasterRecord;
use App\Models\OrderRedo;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderRedoService
{
    public function canInitiate(User $actor, FlowJob $order): bool
    {
        $access = app(AccessControlService::class);

        if (OrderRedo::query()->where('redo_order_id', $order->id)->exists()) return false;

        return $access->canEditVisibleJob($actor, $order)
            && $access->can($actor, 'jobs', 'create');
    }

    /**
     * Return all presentation data needed by the Redo button, tab, banner and
     * relationship panel without mutating the Order or its workflow.
     *
     * @return array<string,mixed>
     */
    public function context(FlowJob $order, User $actor): array
    {
        $incoming = OrderRedo::query()
            ->with([
                'originalOrder:id,job_number,title,quantity,currency,commercial_value',
                'redoOrder:id,job_number,title,workflow_phase_id,health,progress',
                'redoOrder.phase:id,name,sequence,color',
                'supplier:id,name,code',
                'creator:id,name,profile_image_path',
            ])
            ->where('redo_order_id', $order->id)
            ->first();

        $rootOrderId = (int) ($incoming?->original_order_id ?: $order->id);
        $outgoing = OrderRedo::query()
            ->with([
                'originalOrder:id,job_number,title,quantity,currency,commercial_value',
                'redoOrder:id,job_number,title,workflow_phase_id,health,progress',
                'redoOrder.phase:id,name,sequence,color',
                'supplier:id,name,code',
                'creator:id,name,profile_image_path',
            ])
            ->where('original_order_id', $rootOrderId)
            ->orderByDesc('sequence')
            ->get();

        $displayRecord = $incoming ?: $outgoing->first();

        return [
            'canInitiate' => $this->canInitiate($actor, $order),
            'incoming' => $incoming,
            'records' => $outgoing,
            'displayRecord' => $displayRecord,
            'redoCount' => $outgoing->count(),
            'redoOrderCount' => $outgoing->whereNotNull('redo_order_id')->count(),
            'hasRedo' => (bool) $incoming || $outgoing->isNotEmpty(),
            'isRedoOrder' => (bool) $incoming,
            'rootOrderId' => $rootOrderId,
        ];
    }

    /** @return array<int,array{id:int,label:string}> */
    public function supplierOptions(FlowJob $order): array
    {
        $ids = $order->items()
            ->active()
            ->whereNotNull('supplier_id')
            ->pluck('supplier_id')
            ->push($order->supplier_id)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) return [];

        return MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType('supplier')
            ->active()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (MasterRecord $supplier): array => [
                'id' => (int) $supplier->id,
                'label' => (string) $supplier->name,
            ])
            ->values()
            ->all();
    }

    /**
     * Return only the CURRENT/LATEST artwork proof for the Redo issue step.
     *
     * Order Details intentionally keeps every artwork revision for audit and
     * marks older versions as Archived. The Redo modal is operational, not an
     * archive browser, so it must surface only the newest document attached to
     * the active ART_PREPARE_UPLOAD task and must not mix in PO/QC/other task
     * documents or older artwork versions.
     *
     * @return array<int,string>
     */
    public function evidenceLabels(FlowJob $order): array
    {
        $artworkTask = Task::query()
            ->where('flow_job_id', $order->id)
            ->with('setupTemplate:id,automation_key')
            ->get(['id', 'task_pack_task_id', 'title'])
            ->filter(
                fn (Task $task): bool =>
                    app(OrderWorkflowActionService::class)->automationKey($task) === 'ART_PREPARE_UPLOAD'
            )
            ->sortByDesc('id')
            ->first();

        if (! $artworkTask) {
            return [];
        }

        $latestArtwork = Document::query()
            ->where('flow_job_id', $order->id)
            ->where('task_id', $artworkTask->id)
            ->orderByDesc('version')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first(['id', 'name', 'version']);

        $name = trim((string) ($latestArtwork?->name ?? ''));

        return $name !== '' ? [$name] : [];
    }

    /**
     * Preview the commercial values using the same rules used when the Redo is
     * finally persisted. This keeps the modal calculation and saved audit row
     * in lock-step.
     *
     * @return array<string,float|int>
     */
    public function financialPreview(
        FlowJob $order,
        int $redoQuantity,
        string $customerResolution,
        float $customerDiscountPercent,
        float $supplierRedoChargePercent,
        bool $deductFreight,
        float $freightAmount,
    ): array {
        $redoQuantity = max(1, min(max(1, (int) $order->quantity), $redoQuantity));
        $unitValue = $this->averageUnitValue($order);
        $affectedValue = round($redoQuantity * $unitValue, 2);
        $customerImpact = $customerResolution === 'discount'
            ? round($affectedValue * max(0, $customerDiscountPercent) / 100, 2)
            : 0.0;
        $supplierCharge = round($affectedValue * max(0, $supplierRedoChargePercent) / 100, 2);
        $freight = $deductFreight ? round(max(0, $freightAmount), 2) : 0.0;

        return [
            'quantity' => $redoQuantity,
            'unitValue' => $unitValue,
            'affectedValue' => $affectedValue,
            'customerImpact' => $customerImpact,
            'supplierCharge' => $supplierCharge,
            'freight' => $freight,
            'recovery' => round($supplierCharge + $freight, 2),
        ];
    }

    /**
     * Create a separate linked Order. The original Order, its tasks, invoices
     * and payments are intentionally left unchanged.
     *
     * @param array<string,mixed> $data
     */
    public function createRedo(FlowJob $sourceOrder, array $data, User $actor): OrderRedo
    {
        abort_unless($this->canInitiate($actor, $sourceOrder), 403);

        return DB::transaction(function () use ($sourceOrder, $data, $actor): OrderRedo {
            $source = FlowJob::query()->lockForUpdate()->findOrFail($sourceOrder->id);
            $incoming = OrderRedo::query()->where('redo_order_id', $source->id)->first();
            $rootId = (int) ($incoming?->original_order_id ?: $source->id);
            $root = FlowJob::query()
                ->lockForUpdate()
                ->with(['items' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('id')])
                ->findOrFail($rootId);

            $this->validatePayload($root, $data);

            // Redo issue reasons use the same safe rich-text pipeline as Order
            // descriptions/comments. This preserves formatting and pasted
            // screenshots while stripping unsupported HTML. Mention IDs are
            // extracted from the normalized value so @mentions can notify users.
            $storedIssueDescription = app(RichTextService::class)->normalize(
                (string) ($data['issue_description'] ?? ''),
                5000,
                'redoIssueDescription',
            );

            if ($storedIssueDescription === null) {
                throw ValidationException::withMessages([
                    'redoIssueDescription' => 'Add an issue description or pasted image.',
                ]);
            }

            $data['issue_description'] = $storedIssueDescription;
            $mentionIds = app(MentionService::class)->userIdsFromText($storedIssueDescription);

            $sequence = ((int) OrderRedo::query()
                ->where('original_order_id', $root->id)
                ->lockForUpdate()
                ->max('sequence')) + 1;

            $scope = (string) $data['scope'];
            $redoQuantity = $scope === 'discount'
                ? (int) $data['affected_quantity']
                : (int) $data['redo_quantity'];
            $supplierId = filled($data['supplier_id'] ?? null) ? (int) $data['supplier_id'] : null;

            if ($supplierId) {
                MasterRecord::query()
                    ->forWorkspace(app(SetupContext::class)->workspaceId())
                    ->ofType('supplier')
                    ->active()
                    ->findOrFail($supplierId);
            }

            // Discount is a financial-only resolution. It never creates a new
            // FlowJob, never rewinds the current workflow, and never generates
            // replacement tasks. The affected quantity is used only to
            // calculate the customer credit and optional supplier recovery.
            $customerResolution = $scope === 'discount'
                ? 'discount'
                : (string) $data['customer_resolution'];

            $preview = $this->financialPreview(
                $root,
                $redoQuantity,
                $customerResolution,
                (float) ($data['customer_discount_percent'] ?? 0),
                (float) ($data['supplier_redo_charge_percent'] ?? 0),
                (bool) ($data['deduct_freight'] ?? false),
                (float) ($data['freight_amount'] ?? 0),
            );

            if ($scope === 'discount') {
                return $this->createDiscountAdjustment(
                    $root,
                    $data,
                    $actor,
                    $sequence,
                    $redoQuantity,
                    $supplierId,
                    $preview,
                );
            }

            [$workflow, $phases] = $this->publishedWorkflow($root);
            $restartPhase = $this->restartPhase($phases, $scope);
            $restartSourcePhaseId = (int) ($restartPhase->source_workflow_phase_id ?: $restartPhase->id);
            // Discount-only records participate in the audit sequence but must
            // not consume an R-number. ORDER-...-R1 therefore remains the
            // first actual replacement Order even if a discount was recorded
            // earlier.
            $redoOrderSequence = OrderRedo::query()
                ->where('original_order_id', $root->id)
                ->whereNotNull('redo_order_id')
                ->lockForUpdate()
                ->pluck('id')
                ->count() + 1;

            $jobNumber = $this->redoOrderNumber($root, $redoOrderSequence);
            $reference = $this->redoReferenceNumber($root, $redoOrderSequence);

            $redoOrder = FlowJob::create([
                'job_number' => $jobNumber,
                'order_number' => $reference,
                'client_id' => $root->client_id,
                'workflow_id' => $workflow->id,
                'source_workflow_id' => (int) ($root->source_workflow_id ?: $workflow->id),
                // The selected Redo scope is authoritative. The new Redo
                // Order starts at this runtime phase; the source phase id keeps
                // list/report filtering aligned when the workflow is mirrored.
                'workflow_phase_id' => $restartPhase->id,
                'source_workflow_phase_id' => $restartSourcePhaseId,
                'started_from_phase_id' => $restartPhase->id,
                'owner_id' => $root->owner_id ?: $actor->id,
                'coordinator_id' => $root->coordinator_id ?: $actor->id,
                'created_by' => $actor->id,
                'title' => $root->title,
                'product' => $root->product,
                'category' => $root->category,
                'quantity' => $redoQuantity,
                'commercial_value' => $preview['affectedValue'],
                'currency' => $root->currency ?: 'USD',
                'status' => 'New',
                'health' => 'On Track',
                'priority' => $root->priority ?: 'Medium',
                'progress' => 0,
                'delivery_date' => $root->delivery_date,
                'estimated_delivery_date' => null,
                'received_date' => app(WorkspaceSettingsService::class)->localToday(),
                'supplier_id' => $supplierId ?: $root->supplier_id,
                'warehouse' => $root->warehouse,
                'supplier_instruction' => $root->supplier_instruction,
                'description' => $root->description,
                'next_action' => $restartPhase->entry_condition ?: $restartPhase->entry_rule,
                'start_handling' => 'Redo',
                'start_reason' => app(RichTextService::class)->plainText($storedIssueDescription),
                'needs_attention' => false,
                'source_inquiry_id' => null,
                'is_repeat_order' => false,
                'repeat_order_number' => null,
                'production_urgency_ids' => $root->production_urgency_ids ?: [],
                'shipment_urgency_ids' => $root->shipment_urgency_ids ?: [],
                'notes' => trim((string) ($data['internal_instructions'] ?? '')) ?: null,
                'shipping_address' => $root->shipping_address,
                'shipping_phone_country_code' => $root->shipping_phone_country_code,
                'shipping_phone' => $root->shipping_phone,
                'shipping_postal_code' => $root->shipping_postal_code,
                'shipping_source_address_id' => $root->shipping_source_address_id,
            ]);

            $this->cloneRedoItems($root, $redoOrder, $redoQuantity, $supplierId, $actor);
            $this->ensureCoreMembers($redoOrder);

            $redoOrder->load(['workflow', 'phase', 'items']);
            $redoOrder->workflow->setRelation('phases', $phases);
            $redoOrder->setRelation('phase', $restartPhase);

            // Generate the configured Task Pack rows first, then explicitly
            // normalize them around the selected restart phase. This guarantees
            // that Artwork + Production starts at Artwork, while Production-only
            // starts at Production, even when the source Order was already in a
            // later stage. The original Order is never rewound.
            app(JobService::class)->syncWorkflowTasks($redoOrder, $actor, true);
            $this->initializeRedoWorkflowAtPhase($redoOrder, $phases, $restartPhase, $actor);

            $record = OrderRedo::create([
                'original_order_id' => $root->id,
                'redo_order_id' => $redoOrder->id,
                'sequence' => $sequence,
                'issue_reported_by' => (string) $data['issue_reported_by'],
                'issue_category' => (string) $data['issue_category'],
                'reported_date' => app(WorkspaceSettingsService::class)->localToday(),
                'affected_quantity' => (int) $data['affected_quantity'],
                'issue_description' => $storedIssueDescription,
                'scope' => $scope,
                'redo_quantity' => $redoQuantity,
                'supplier_id' => $supplierId,
                'internal_instructions' => trim((string) ($data['internal_instructions'] ?? '')) ?: null,
                'customer_resolution' => $customerResolution,
                'customer_discount_percent' => (float) ($data['customer_discount_percent'] ?? 0),
                'supplier_redo_charge_percent' => (float) ($data['supplier_redo_charge_percent'] ?? 0),
                'deduct_freight' => (bool) ($data['deduct_freight'] ?? false),
                'freight_amount' => $preview['freight'],
                'affected_order_value' => $preview['affectedValue'],
                'customer_impact' => $preview['customerImpact'],
                'supplier_redo_charge' => $preview['supplierCharge'],
                'total_supplier_recovery' => $preview['recovery'],
                'created_by' => $actor->id,
            ]);

            $root->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.redo_created',
                'description' => $redoOrder->displayOrderNumber().' created as redo order.',
                'meta' => [
                    'redo_order_id' => $redoOrder->id,
                    'redo_sequence' => $sequence,
                    'redo_scope' => $scope,
                    'redo_quantity' => $redoQuantity,
                    'restart_phase_id' => (int) $restartPhase->id,
                    'restart_phase_name' => (string) $restartPhase->name,
                    'restart_phase_sequence' => (int) $restartPhase->sequence,
                    'mention_user_ids' => $mentionIds,
                ],
            ]);

            $redoOrder->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.redo_started',
                'description' => 'Redo order created from '.$root->displayOrderNumber().'.',
                'meta' => [
                    'original_order_id' => $root->id,
                    'redo_sequence' => $sequence,
                    'issue_category' => (string) $data['issue_category'],
                    'redo_scope' => $scope,
                    'restart_phase_id' => (int) $restartPhase->id,
                    'restart_phase_name' => (string) $restartPhase->name,
                    'restart_phase_sequence' => (int) $restartPhase->sequence,
                    'mention_user_ids' => $mentionIds,
                ],
            ]);

            if ($mentionIds) {
                app(NotificationService::class)->notifyMentionedUsers(
                    $mentionIds,
                    $actor->name.' mentioned you in '.$redoOrder->displayOrderNumber(),
                    $storedIssueDescription,
                    $redoOrder,
                    null,
                    $actor,
                );
            }

            return $record->fresh([
                'originalOrder',
                'redoOrder.phase',
                'supplier',
                'creator',
            ]);
        }, 3);
    }

    /**
     * Store a discount-instead-of-redo decision as a financial adjustment on
     * the original Order. No new FlowJob or workflow tasks are created.
     *
     * @param array<string,mixed> $data
     * @param array<string,float|int> $preview
     */
    private function createDiscountAdjustment(
        FlowJob $root,
        array $data,
        User $actor,
        int $sequence,
        int $affectedQuantity,
        ?int $supplierId,
        array $preview,
    ): OrderRedo {
        $storedIssueDescription = app(RichTextService::class)->normalize(
            (string) ($data['issue_description'] ?? ''),
            5000,
            'redoIssueDescription',
        );
        $mentionIds = app(MentionService::class)->userIdsFromText($storedIssueDescription);

        $record = OrderRedo::create([
            'original_order_id' => $root->id,
            'redo_order_id' => null,
            'sequence' => $sequence,
            'issue_reported_by' => (string) $data['issue_reported_by'],
            'issue_category' => (string) $data['issue_category'],
            'reported_date' => app(WorkspaceSettingsService::class)->localToday(),
            'affected_quantity' => (int) $data['affected_quantity'],
            'issue_description' => $storedIssueDescription,
            'scope' => 'discount',
            'redo_quantity' => $affectedQuantity,
            'supplier_id' => $supplierId,
            'internal_instructions' => trim((string) ($data['internal_instructions'] ?? '')) ?: null,
            'customer_resolution' => 'discount',
            'customer_discount_percent' => (float) ($data['customer_discount_percent'] ?? 0),
            'supplier_redo_charge_percent' => (float) ($data['supplier_redo_charge_percent'] ?? 0),
            'deduct_freight' => (bool) ($data['deduct_freight'] ?? false),
            'freight_amount' => $preview['freight'],
            'affected_order_value' => $preview['affectedValue'],
            'customer_impact' => $preview['customerImpact'],
            'supplier_redo_charge' => $preview['supplierCharge'],
            'total_supplier_recovery' => $preview['recovery'],
            'created_by' => $actor->id,
        ]);

        $discount = rtrim(rtrim(number_format((float) ($data['customer_discount_percent'] ?? 0), 2), '0'), '.');

        $root->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.redo_discount_recorded',
            'description' => $discount.'% customer discount recorded instead of redo.',
            'meta' => [
                'redo_sequence' => $sequence,
                'redo_scope' => 'discount',
                'affected_quantity' => $affectedQuantity,
                'customer_discount_percent' => (float) ($data['customer_discount_percent'] ?? 0),
                'customer_credit' => $preview['customerImpact'],
                'supplier_recovery' => $preview['recovery'],
                'workflow_unchanged' => true,
                'mention_user_ids' => $mentionIds,
            ],
        ]);

        if ($mentionIds) {
            app(NotificationService::class)->notifyMentionedUsers(
                $mentionIds,
                $actor->name.' mentioned you in '.$root->displayOrderNumber(),
                (string) $storedIssueDescription,
                $root,
                null,
                $actor,
            );
        }

        return $record->fresh([
            'originalOrder',
            'supplier',
            'creator',
        ]);
    }

    /** @param array<string,mixed> $data */
    private function validatePayload(FlowJob $root, array $data): void
    {
        $maxQuantity = max(1, (int) $root->quantity);
        $affected = (int) ($data['affected_quantity'] ?? 0);
        $redoQuantity = (int) ($data['redo_quantity'] ?? 0);

        $errors = [];
        if (!in_array((string) ($data['issue_reported_by'] ?? ''), ['Customer', 'Quality Control', 'Internal Team'], true)) {
            $errors['redoIssueSource'] = 'Select who reported the issue.';
        }
        if (trim((string) ($data['issue_category'] ?? '')) === '') {
            $errors['redoIssueCategory'] = 'Select an issue category.';
        }
        if ($affected < 1 || $affected > $maxQuantity) {
            $errors['redoAffectedQuantity'] = 'Affected quantity must be between 1 and '.$maxQuantity.'.';
        }
        $scope = (string) ($data['scope'] ?? '');
        if ($scope !== 'discount' && ($redoQuantity < 1 || $redoQuantity > $maxQuantity)) {
            $errors['redoQuantity'] = 'Redo quantity must be between 1 and '.$maxQuantity.'.';
        }
        if (trim((string) ($data['issue_description'] ?? '')) === '') {
            $errors['redoIssueDescription'] = 'Add an issue description.';
        }
        if (!in_array($scope, ['artwork', 'production', 'discount'], true)) {
            $errors['redoScope'] = 'Choose the redo scope.';
        }
        if (!in_array((string) ($data['customer_resolution'] ?? ''), ['free', 'discount'], true)) {
            $errors['redoCustomerResolution'] = 'Select the customer resolution.';
        }
        if ($scope === 'discount' && (string) ($data['customer_resolution'] ?? '') !== 'discount') {
            $errors['redoCustomerResolution'] = 'Discount scope requires a customer discount.';
        }

        foreach (['customer_discount_percent', 'supplier_redo_charge_percent'] as $key) {
            $value = (float) ($data[$key] ?? 0);
            if ($value < 0 || $value > 100) {
                $errors[$key === 'customer_discount_percent' ? 'redoCustomerDiscount' : 'redoSupplierChargePercent'] = 'Percentage must be between 0 and 100.';
            }
        }

        if ((float) ($data['freight_amount'] ?? 0) < 0) {
            $errors['redoFreightAmount'] = 'Freight amount cannot be negative.';
        }

        if ($errors) throw ValidationException::withMessages($errors);
    }

    /** @return array{0:Workflow,1:Collection<int,WorkflowPhase>} */
    private function publishedWorkflow(FlowJob $root): array
    {
        $workflowId = (int) ($root->source_workflow_id ?: $root->workflow_id);
        abort_unless($workflowId > 0, 422, 'The original Order does not have a workflow.');

        $isPublishedOrderWorkflow = OrderWorkflowSetupService::orderWorkflowQuery()
            ->whereKey($workflowId)
            ->exists();

        if ($isPublishedOrderWorkflow) {
            app(OrderWorkflowSetupService::class)->ensureRuntimeMirror($workflowId);
            $workflow = Workflow::query()
                ->whereKey($workflowId)
                ->where('is_snapshot', false)
                ->firstOrFail();
            $phases = WorkflowPhase::query()
                ->where('workflow_template_id', $workflowId)
                ->where('is_active', true)
                ->with([
                    'taskPack.items.defaultAssignee',
                    'taskPack.items.defaultDepartment',
                    'taskPack.items.priority',
                    'taskPack.items.documentCategory',
                ])
                ->orderBy('sequence')
                ->get()
                ->values();
        } else {
            $workflow = Workflow::query()->findOrFail((int) $root->workflow_id);
            $phases = $workflow->phases()
                ->where('is_active', true)
                ->with([
                    'taskPack.items.defaultAssignee',
                    'taskPack.items.defaultDepartment',
                    'taskPack.items.priority',
                    'taskPack.items.documentCategory',
                ])
                ->orderBy('sequence')
                ->get()
                ->values();
        }

        abort_if($phases->isEmpty(), 422, 'The Order workflow does not contain active phases.');
        $workflow->setRelation('phases', $phases);

        return [$workflow, $phases];
    }

    private function restartPhase(Collection $phases, string $scope): WorkflowPhase
    {
        $needle = $scope === 'artwork' ? 'artwork' : 'production';
        $fallbackSequence = $scope === 'artwork' ? 2 : 3;

        $phase = $phases->first(function (WorkflowPhase $phase) use ($needle): bool {
            return Str::contains(Str::lower((string) $phase->name), $needle);
        }) ?: $phases->firstWhere('sequence', $fallbackSequence);

        abort_unless($phase, 422, ucfirst($needle).' phase is not configured in this Order workflow.');

        return $phase;
    }

    /**
     * Make the selected Redo scope the actual operational starting point.
     *
     * Phases before the selected restart phase are carried forward as complete
     * from the original Order. The selected phase is made current and its first
     * required generated task is unlocked by the normal sequencing service.
     * Future phases remain Not Started/locked. This is deliberately applied to
     * the NEW Redo Order only; the source Order keeps its existing phase/tasks.
     *
     * @param Collection<int,WorkflowPhase> $phases
     */
    private function initializeRedoWorkflowAtPhase(
        FlowJob $redoOrder,
        Collection $phases,
        WorkflowPhase $restartPhase,
        User $actor,
    ): void {
        $rules = app(OrderTaskFlagService::class);
        $completedStatus = $rules->completedStatus();
        $notStartedStatus = $rules->notStartedStatus();
        $completedStatusId = $rules->statusRecord($completedStatus, false)?->id;
        $notStartedStatusId = $rules->statusRecord($notStartedStatus, false)?->id;
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $restartSequence = (int) $restartPhase->sequence;
        $phaseIds = $phases->pluck('id')->map(fn ($id) => (int) $id)->values();

        foreach ($phases as $phase) {
            $phaseId = (int) $phase->id;
            $sequence = (int) $phase->sequence;

            $taskQuery = Task::query()
                ->where('flow_job_id', $redoOrder->id)
                ->where('workflow_phase_id', $phaseId)
                ->whereNotNull('task_pack_task_id')
                ->whereNull('deleted_at');

            if ($sequence < $restartSequence) {
                // Earlier phases are inherited from the original Order and do
                // not become actionable again in this Redo cycle.
                $taskQuery->update([
                    'status' => $completedStatus,
                    'order_task_status_id' => $completedStatusId,
                    'order_task_flag_id' => null,
                    'needs_attention' => false,
                    'attention_reason' => null,
                    'progress' => 100,
                    'start_date' => $today,
                    'completed_at' => now(),
                ]);

                FlowJobPhaseHistory::query()->updateOrCreate(
                    ['flow_job_id' => $redoOrder->id, 'workflow_phase_id' => $phaseId],
                    [
                        'changed_by' => $actor->id,
                        'phase_owner_id' => $redoOrder->coordinator_id,
                        'target_date' => $redoOrder->delivery_date,
                        'health_override' => $redoOrder->health,
                        'status' => 'completed',
                        'entered_at' => now(),
                        'completed_at' => now(),
                    ]
                );
                continue;
            }

            if ($sequence > $restartSequence) {
                // Future phases stay locked until the normal workflow advances.
                $taskQuery->update([
                    'status' => $notStartedStatus,
                    'order_task_status_id' => $notStartedStatusId,
                    'order_task_flag_id' => null,
                    'needs_attention' => false,
                    'attention_reason' => null,
                    'progress' => 0,
                    'start_date' => null,
                    'due_date' => null,
                    'completed_at' => null,
                ]);
                continue;
            }

            // Reset the selected phase before the standard sequence service
            // unlocks exactly its first incomplete required Task Pack task.
            $taskQuery->update([
                'status' => $notStartedStatus,
                'order_task_status_id' => $notStartedStatusId,
                'order_task_flag_id' => null,
                'needs_attention' => false,
                'attention_reason' => null,
                'progress' => 0,
                'completed_at' => null,
            ]);
        }

        // Remove any accidental history rows outside the active workflow and
        // make the selected phase the single active phase for the Redo Order.
        FlowJobPhaseHistory::query()
            ->where('flow_job_id', $redoOrder->id)
            ->whereNotIn('workflow_phase_id', $phaseIds->all())
            ->whereNull('completed_at')
            ->update(['status' => 'replaced', 'completed_at' => now()]);

        FlowJobPhaseHistory::query()->updateOrCreate(
            ['flow_job_id' => $redoOrder->id, 'workflow_phase_id' => (int) $restartPhase->id],
            [
                'changed_by' => $actor->id,
                'phase_owner_id' => $redoOrder->coordinator_id,
                'target_date' => $redoOrder->delivery_date,
                'health_override' => $redoOrder->health,
                'status' => 'active',
                'entered_at' => now(),
                'completed_at' => null,
            ]
        );

        $redoOrder->update([
            'workflow_phase_id' => (int) $restartPhase->id,
            'source_workflow_phase_id' => (int) ($restartPhase->source_workflow_phase_id ?: $restartPhase->id),
            'started_from_phase_id' => (int) $restartPhase->id,
            'status' => 'In Progress',
            'health' => 'On Track',
            'completed_at' => null,
            'next_action' => $restartPhase->entry_condition ?: $restartPhase->entry_rule,
        ]);

        $fresh = $redoOrder->fresh(['workflow']);
        $fresh->workflow->setRelation('phases', $phases);
        $fresh->setRelation('phase', $restartPhase);

        app(OrderTaskSequenceService::class)->synchronizePhase($fresh, $restartPhase, $actor);

        $firstTask = app(OrderTaskSequenceService::class)
            ->firstIncompleteRequiredTask($fresh, (int) $restartPhase->id);
        if ($firstTask) {
            $fresh->update(['next_action' => (string) $firstTask->title]);
        }

        // Recalculate only after the selected phase has been made authoritative;
        // this keeps the progress bar and status derived from the chosen restart.
        app(OrderTaskFlagService::class)->syncJob($fresh);
        app(JobService::class)->syncAutomaticStatus($fresh, $actor);

        $progressJob = $fresh->fresh(['workflow', 'phase']);
        $progressJob->workflow->setRelation('phases', $phases);
        $progressJob->setRelation('phase', $restartPhase);
        app(JobService::class)->recalculateProgress($progressJob);
    }

    private function redoOrderNumber(FlowJob $root, int $sequence): string
    {
        $base = $root->displayOrderNumber();
        $candidate = $base.'-R'.$sequence;
        $suffix = $sequence;

        while (FlowJob::withTrashed()->where('job_number', $candidate)->exists()) {
            $suffix++;
            $candidate = $base.'-R'.$suffix;
        }

        return $candidate;
    }

    private function redoReferenceNumber(FlowJob $root, int $sequence): ?string
    {
        $base = trim((string) $root->order_number);
        if ($base === '') return null;

        $candidate = $base.'-R'.$sequence;
        $suffix = $sequence;
        while (FlowJob::withTrashed()->where('order_number', $candidate)->exists()) {
            $suffix++;
            $candidate = $base.'-R'.$suffix;
        }

        return $candidate;
    }

    /**
     * Resolve a trustworthy unit value for Redo financial calculations.
     *
     * Orders created from the current Product selector intentionally do not ask
     * the user to override a unit price. As a result, older Redo logic could see
     * flow_job_items.unit_price = 0 and commercial_value = 0 even though the
     * Product master has a valid quantity-based price. That made the live
     * Financial preview show $0.00 for affected value, customer credit and
     * supplier recovery.
     *
     * Resolution order (most authoritative first):
     *  1. Persisted Order line prices, when every active line is priced.
     *  2. Order commercial_value.
     *  3. Latest non-draft/non-cancelled invoice value.
     *  4. Product Master quantity-break pricing for every active Order line.
     *
     * We only use a weighted line average when every active line can be priced,
     * so a partially priced multi-product Order never produces a misleading
     * affected value.
     */
    private function averageUnitValue(FlowJob $order): float
    {
        $items = $order->items()
            ->active()
            ->with(['catalogProduct:id,type,metadata'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'catalog_product_id', 'quantity', 'unit_price']);

        $orderQuantity = max(0, (int) $order->quantity);
        $activeQuantity = (int) $items->sum(fn (FlowJobItem $item): int => max(0, (int) $item->quantity));

        // 1) Persisted Order line prices are the strongest line-level source.
        if ($items->isNotEmpty() && $activeQuantity > 0) {
            $allPersistedLinesPriced = $items->every(
                fn (FlowJobItem $item): bool => max(0, (int) $item->quantity) === 0
                    || (float) $item->unit_price > 0
            );

            if ($allPersistedLinesPriced) {
                $lineValue = (float) $items->sum(
                    fn (FlowJobItem $item): float => max(0, (int) $item->quantity) * max(0, (float) $item->unit_price)
                );

                if ($lineValue > 0) {
                    return round($lineValue / $activeQuantity, 4);
                }
            }
        }

        // 2) Explicit Order commercial value overrides inferred catalogue price.
        if ($orderQuantity > 0 && (float) $order->commercial_value > 0) {
            return round((float) $order->commercial_value / $orderQuantity, 4);
        }

        // 3) Once an invoice exists, use its real commercial value before
        // falling back to Product Master pricing. Draft/cancelled invoices are
        // intentionally ignored because they are not authoritative.
        $invoice = $order->invoices()
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->with('items')
            ->orderByDesc('sequence')
            ->orderByDesc('id')
            ->first();

        if ($invoice) {
            $invoiceQuantity = (float) $invoice->items->sum(
                fn ($item): float => max(0, (float) $item->quantity)
            );
            $invoiceLineValue = (float) $invoice->items->sum(
                fn ($item): float => max(0, (float) ($item->amount ?: ((float) $item->quantity * (float) $item->unit_price)))
            );

            if ($invoiceQuantity > 0 && $invoiceLineValue > 0) {
                return round($invoiceLineValue / $invoiceQuantity, 4);
            }

            if ($orderQuantity > 0 && (float) $invoice->total > 0) {
                return round((float) $invoice->total / $orderQuantity, 4);
            }
        }

        // 4) Current Create Order UI derives price from Product Master instead
        // of asking for a unit-price override. Resolve each line using the same
        // quantity breakpoint table. All active lines must resolve before this
        // weighted average is accepted.
        if ($items->isNotEmpty() && $activeQuantity > 0) {
            $catalogValue = 0.0;
            $resolvedQuantity = 0;
            $allCatalogLinesPriced = true;

            foreach ($items as $item) {
                $quantity = max(0, (int) $item->quantity);
                if ($quantity === 0) continue;

                $price = $item->catalogProduct?->productPriceForQuantity($quantity);
                if ($price === null || $price <= 0) {
                    $allCatalogLinesPriced = false;
                    break;
                }

                $catalogValue += $quantity * (float) $price;
                $resolvedQuantity += $quantity;
            }

            if ($allCatalogLinesPriced && $resolvedQuantity > 0 && $catalogValue > 0) {
                return round($catalogValue / $resolvedQuantity, 4);
            }
        }

        return 0.0;
    }

    private function cloneRedoItems(
        FlowJob $root,
        FlowJob $redoOrder,
        int $redoQuantity,
        ?int $supplierId,
        User $actor,
    ): void {
        $items = $root->items()->active()->orderBy('sort_order')->orderBy('id')->get();
        $remaining = $redoQuantity;
        $sort = 0;

        foreach ($items as $item) {
            if ($remaining <= 0) break;
            $quantity = min(max(0, (int) $item->quantity), $remaining);
            if ($quantity <= 0) continue;

            FlowJobItem::create([
                'flow_job_id' => $redoOrder->id,
                'catalog_product_id' => $item->catalog_product_id,
                'supplier_id' => $supplierId ?: $item->supplier_id,
                'product_name' => $item->product_name,
                'category_name' => $item->category_name,
                'quantity' => $quantity,
                'unit_price' => $item->unit_price,
                'notes' => $item->notes,
                'updated_by' => $actor->id,
                'sort_order' => $sort++,
            ]);
            $remaining -= $quantity;
        }

        if ($remaining > 0) {
            FlowJobItem::create([
                'flow_job_id' => $redoOrder->id,
                'supplier_id' => $supplierId ?: $root->supplier_id,
                'product_name' => $root->product ?: $root->title,
                'category_name' => $root->category,
                'quantity' => $remaining,
                'unit_price' => $this->averageUnitValue($root),
                'updated_by' => $actor->id,
                'sort_order' => $sort,
            ]);
        }
    }

    private function ensureCoreMembers(FlowJob $redoOrder): void
    {
        foreach (array_filter([(int) $redoOrder->owner_id, (int) $redoOrder->coordinator_id]) as $userId) {
            FlowJobMember::firstOrCreate(
                ['flow_job_id' => $redoOrder->id, 'user_id' => $userId],
                [
                    'access_level' => 'member',
                    'can_manage_tasks' => true,
                    'can_upload_documents' => true,
                    'can_view_financials' => false,
                ],
            );
        }
    }
}
