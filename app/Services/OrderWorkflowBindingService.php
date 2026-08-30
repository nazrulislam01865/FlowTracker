<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\FlowJobPhaseHistory;
use App\Models\Task;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Support\OrderStageResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Keeps active Orders synchronized with the Order workflow they were created
 * from. Multiple Order workflows can coexist; no workflow is allowed to steal
 * active Orders that belong to another template. Completed/cancelled Orders
 * remain untouched for historical integrity.
 */
class OrderWorkflowBindingService
{
    /**
     * Capture an active Order's destination while legacy phase names still
     * exist. This is used during the one-time 5-stage -> 7-stage upgrade so
     * renaming phase 5 cannot turn an Invoice/Payment Order into Shipment.
     *
     * @return array<int,int> job id => target seven-stage sequence
     */
    public function captureActiveOrderTargetSequences(int $workflowId): array
    {
        $targets = [];

        FlowJob::query()
            ->whereNull('deleted_at')
            ->whereNull('completed_at')
            ->whereNotIn('status', JobService::INACTIVE_STATUSES)
            ->where(function ($query) use ($workflowId): void {
                $query->where('source_workflow_id', $workflowId)
                    ->orWhere(function ($legacy) use ($workflowId): void {
                        $legacy->whereNull('source_workflow_id')->where('workflow_id', $workflowId);
                    });
            })
            ->with('phase:id,name,short_name,sequence')
            ->orderBy('id')
            ->get(['id', 'workflow_phase_id', 'status'])
            ->each(function (FlowJob $job) use (&$targets): void {
                $targets[(int) $job->id] = $this->targetSequence($job, 7);
            });

        return $targets;
    }

    /**
     * @param array<int,int> $targetSequences Optional captured pre-upgrade map.
     */
    public function syncActiveOrders(int $workflowId, array $targetSequences = []): int
    {
        [$workflow, $phases] = $this->publishedWorkflow($workflowId);
        if ($phases->isEmpty()) return 0;

        $count = 0;
        FlowJob::query()
            ->whereNull('deleted_at')
            ->whereNull('completed_at')
            ->whereNotIn('status', JobService::INACTIVE_STATUSES)
            ->where(function ($query) use ($workflowId): void {
                $query->where('source_workflow_id', $workflowId)
                    ->orWhere(function ($legacy) use ($workflowId): void {
                        $legacy->whereNull('source_workflow_id')->where('workflow_id', $workflowId);
                    });
            })
            ->orderBy('id')
            ->select(['id'])
            ->chunkById(50, function ($orders) use ($workflow, $phases, $targetSequences, &$count): void {
                foreach ($orders as $order) {
                    $jobId = (int) $order->id;
                    $this->syncOrder($jobId, $workflow, $phases, $targetSequences[$jobId] ?? null);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Repair one active Order while opening Order Details. This makes old
     * five-stage snapshots disappear immediately instead of waiting for a
     * second admin save in Workflow Setup.
     */
    public function syncSingleActiveOrder(int $jobId): bool
    {
        $job = FlowJob::query()
            ->whereKey($jobId)
            ->whereNull('deleted_at')
            ->whereNull('completed_at')
            ->whereNotIn('status', JobService::INACTIVE_STATUSES)
            ->first(['id', 'workflow_id', 'source_workflow_id']);
        if (! $job) return false;

        $workflowId = (int) ($job->source_workflow_id ?: $job->workflow_id);
        if (! $workflowId) return false;

        $workflowExists = OrderWorkflowSetupService::orderWorkflowQuery()
            ->whereKey($workflowId)
            ->where('is_active', true)
            ->exists();
        if (! $workflowExists || ! app(OrderWorkflowSetupService::class)->isReadyForOrderCreation($workflowId)) return false;

        [$workflow, $phases] = $this->publishedWorkflow($workflowId);
        if ($phases->isEmpty()) return false;
        $this->syncOrder($jobId, $workflow, $phases);
        return true;
    }

    /** @return array{0:Workflow,1:Collection<int,WorkflowPhase>} */
    private function publishedWorkflow(int $workflowId): array
    {
        app(OrderWorkflowSetupService::class)->ensureRuntimeMirror($workflowId);
        $workflow = Workflow::query()
            ->whereKey($workflowId)
            ->where('is_snapshot', false)
            ->firstOrFail();

        // IMPORTANT: read dedicated setup phases through workflow_template_id,
        // not the legacy Workflow::phases relation. Old installations can have
        // historical workflow_id rows that otherwise resurrect an old 5-stage
        // design on Order Details/Create Order.
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

        return [$workflow, $phases];
    }

    private function syncOrder(int $jobId, Workflow $workflow, Collection $phases, ?int $targetSequenceOverride = null): void
    {
        DB::transaction(function () use ($jobId, $workflow, $phases, $targetSequenceOverride): void {
            $job = FlowJob::query()
                ->lockForUpdate()
                ->with(['phase:id,name,short_name,sequence', 'workflow:id,is_snapshot,source_workflow_id'])
                ->findOrFail($jobId);

            $targetSequence = $targetSequenceOverride !== null
                ? max(1, min($phases->count(), $targetSequenceOverride))
                : $this->targetSequence($job, $phases->count());
            /** @var WorkflowPhase|null $targetPhase */
            $targetPhase = $phases->firstWhere('sequence', $targetSequence) ?: $phases->first();
            $firstPhase = $phases->first();
            if (! $targetPhase || ! $firstPhase) return;

            $phaseIds = $phases->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

            // Archive every generated row that no longer belongs to the
            // published 7-stage definition. This runs even when workflow_id is
            // already correct; that is what removes stale old-stage tasks after
            // an interrupted/partial workflow migration.
            Task::query()
                ->where('flow_job_id', $job->id)
                ->whereNotNull('task_pack_task_id')
                ->whereNotIn('workflow_phase_id', $phaseIds)
                ->delete();

            foreach ($phases as $phase) {
                $allowed = $phase->taskPack?->items?->pluck('id')->map(fn ($id) => (int) $id)->values()->all() ?? [];
                $query = Task::query()
                    ->where('flow_job_id', $job->id)
                    ->where('workflow_phase_id', $phase->id)
                    ->whereNotNull('task_pack_task_id');
                if ($allowed) $query->whereNotIn('task_pack_task_id', $allowed);
                else $query->whereNotNull('task_pack_task_id');
                $query->delete();
            }

            $changedWorkflow = (int) $job->workflow_id !== (int) $workflow->id;
            $changedPhase = (int) $job->workflow_phase_id !== (int) $targetPhase->id;

            $job->update([
                'workflow_id' => (int) $workflow->id,
                'source_workflow_id' => (int) $workflow->id,
                'workflow_phase_id' => (int) $targetPhase->id,
                'source_workflow_phase_id' => (int) $targetPhase->id,
                'started_from_phase_id' => (int) $firstPhase->id,
            ]);

            // Close any old active history row and ensure the current published
            // phase has exactly one active runtime history row.
            FlowJobPhaseHistory::query()
                ->where('flow_job_id', $job->id)
                ->whereNotIn('workflow_phase_id', $phaseIds)
                ->whereNull('completed_at')
                ->update(['status' => 'replaced', 'completed_at' => now()]);

            FlowJobPhaseHistory::query()->updateOrCreate(
                ['flow_job_id' => $job->id, 'workflow_phase_id' => $targetPhase->id],
                [
                    'changed_by' => auth()->id() ?: $job->created_by,
                    'phase_owner_id' => $job->coordinator_id,
                    'target_date' => $job->delivery_date,
                    'health_override' => $job->health,
                    'status' => 'active',
                    'entered_at' => now(),
                    'completed_at' => null,
                ]
            );

            $fresh = $job->fresh();
            // Inject the exact dedicated phases so syncWorkflowTasks cannot
            // accidentally see historical legacy Workflow::phases rows.
            $fresh->load(['workflow']);
            $fresh->workflow->setRelation('phases', $phases);
            $fresh->setRelation('phase', $targetPhase);
            app(JobService::class)->syncWorkflowTasks($fresh, null, true);

            // A publish can replace legacy generated task identities. Repair
            // artwork evidence immediately, before the Order is rendered, so
            // Documents/links never disappear from a completed Artwork stage.
            app(OrderArtworkEvidenceService::class)->repair((int) $job->id);

            app(OrderTaskSequenceService::class)->synchronizeCurrentPhase($fresh->fresh(['phase']));

            if ($changedWorkflow || $changedPhase) {
                $job->activities()->create([
                    'user_id' => auth()->id() ?: $job->created_by,
                    'event' => 'job.order_workflow_rebound',
                    'description' => 'Order synchronized with the published seven-stage Order workflow.',
                ]);
            }
        }, 3);
    }

    private function targetSequence(FlowJob $job, int $stageCount): int
    {
        // Keep physical workflow rebinding and every operational read model on
        // one mapping contract. This prevents the maintenance sync from
        // re-introducing a different interpretation of historical stages than
        // Orders / My Tasks / All Tasks use.
        $sequence = OrderStageResolver::resolve(
            $job->phase?->name,
            $job->phase?->short_name,
            $job->phase?->sequence,
            (string) $job->status,
        )['sequence'];

        return max(1, min($stageCount, (int) $sequence));
    }
}
