<?php

namespace App\Services\SupplierBridge;

use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Support\OrderStageResolver;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Deliberately limited supplier-owned snapshot; no customers, margins or internal notes. */
final class SupplierOrderProjection
{
    private const TASKS = [
        2 => [
            'ART_PREPARE_UPLOAD' => 'Prepare & Upload Artwork',
            'ART_INTERNAL_REVIEW' => 'Internal Artwork Review',
            'ART_SEND_ORDER_TEAM' => 'Send Artwork to Order Team',
            'ART_CLIENT_ERP_DECISION' => 'Client ERP / Approval',
            'ART_SAMPLE_APPROVAL' => 'Sample Approval (when required)',
        ],
        3 => [
            'PROD_SET_ESTIMATED_DELIVERY' => 'Set estimated delivery date',
            'PROD_START' => 'Start Production',
            'PROD_ISSUE' => 'Monitor / Resolve Production Issue',
            'PROD_FINISH' => 'Finish Production',
        ],
        4 => [
            'QC_CHECK' => 'Perform QC Check',
            'QC_ISSUE' => 'Resolve QC Issue (when needed)',
            'QC_APPROVE_SHIPMENT' => 'Approve for Shipment',
        ],
    ];

    public function build(int $orderId, int $supplierId, string $eventId): array
    {
        $order = FlowJob::withTrashed()->with('phase:id,name,short_name,sequence')->findOrFail($orderId);
        $items = DB::table('flow_job_items as i')
            ->join('master_records as p', 'p.id', '=', 'i.catalog_product_id')
            ->join('master_records as s', 's.id', '=', 'i.supplier_id')
            ->leftJoin('product_supplier_links as l', function ($join): void {
                $join->on('l.product_id', '=', 'p.id')->on('l.supplier_id', '=', 's.id')
                    ->on('l.workspace_id', '=', 'p.workspace_id');
            })
            ->where('i.flow_job_id', $orderId)->where('i.supplier_id', $supplierId)
            ->where('i.is_removed', false)->where('i.quantity', '>', 0)
            ->where('p.type', 'product')->whereNull('p.deleted_at')
            ->where('s.type', 'supplier')->where('s.status', 'active')->whereNull('s.deleted_at')
            ->whereColumn('p.workspace_id', 's.workspace_id')
            ->orderBy('i.id')
            ->get(['i.id', 'i.catalog_product_id', 'p.name as product_name', 'p.metadata',
                'i.quantity', 'i.unit_price', 'i.notes', 'l.id as linked_id']);

        $lines = [];
        foreach ($items->unique('id') as $item) {
            if (! $item->linked_id) {
                $product = new MasterRecord();
                $product->setRawAttributes(['type' => 'product', 'metadata' => $item->metadata]);
                if (! $product->hasProductSupplier($supplierId)) continue;
            }
            $unitPrice = is_numeric($item->unit_price) && (float) $item->unit_price > 0
                ? round((float) $item->unit_price, 2) : null;
            $lines[] = [
                'product_name' => mb_substr((string) ($item->product_name ?: 'Product'), 0, 255),
                'product_code' => 'PRD-'.str_pad((string) $item->catalog_product_id, 6, '0', STR_PAD_LEFT),
                'quantity' => (int) $item->quantity,
                // The selected order line's own price, never another supplier's table.
                'unit_price' => $unitPrice,
                'notes' => mb_substr((string) ($item->notes ?? ''), 0, 2000),
            ];
        }
        if (count($lines) > 100) throw new RuntimeException('Supplier order has too many assigned lines.');
        $revision = (int) now()->format('Uu');
        if ($lines === [] || $order->trashed() || strcasecmp((string) $order->status, 'Draft') === 0) {
            return ['event_id' => $eventId, 'supplier_id' => $supplierId,
                'order' => ['reference' => $order->displayOrderNumber(), 'revoked' => true, 'revision' => $revision]];
        }

        $resolved = OrderStageResolver::resolve(
            $order->phase?->name, $order->phase?->short_name, $order->phase?->sequence, $order->status,
        );
        $sequence = (int) $resolved['sequence'];
        $names = array_merge(...array_values(self::TASKS));
        // One order-scoped query, no internal comments, assignees, or other suppliers' records.
        $taskRows = DB::table('tasks as t')
            ->join('task_pack_items as pack', 'pack.id', '=', 't.task_pack_task_id')
            ->where('t.flow_job_id', $orderId)->whereNull('t.deleted_at')
            ->whereIn('pack.automation_key', array_keys($names))
            ->orderByDesc('t.id')->get(['t.id', 'pack.automation_key', 't.status',
                't.completed_at', 't.created_at', 't.updated_at']);
        $byKey = $taskRows->unique('automation_key')->keyBy('automation_key');

        // Never treat a skipped Client ERP task as approval while Sample Approval is pending.
        $clientApproval = $byKey->get('ART_CLIENT_ERP_DECISION');
        $sampleApproval = $byKey->get('ART_SAMPLE_APPROVAL');
        $clientApproved = $clientApproval && ($clientApproval->completed_at !== null
            || strcasecmp((string) $clientApproval->status, 'Completed') === 0);
        $sampleStatus = strtolower(trim((string) ($sampleApproval?->status ?? '')));
        $sampleSatisfied = ! $sampleApproval || $sampleApproval->completed_at !== null
            || in_array($sampleStatus, ['completed', 'skipped', 'not applicable', 'n/a'], true);
        $sampleApproved = $sampleApproval && ($sampleApproval->completed_at !== null || $sampleStatus === 'completed');
        $artworkConfirmed = $sequence > 2 || $order->completed_at !== null
            || ($clientApproved && $sampleSatisfied) || $sampleApproved;
        $stages = [];
        foreach ([2 => 'Artwork', 3 => 'Production', 4 => 'Quality control'] as $index => $name) {
            $state = ($sequence > $index || $order->completed_at !== null) ? 'completed'
                : ($sequence === $index ? 'in_progress' : 'upcoming');
            $stageTasks = [];
            if ($artworkConfirmed) {
                foreach (self::TASKS[$index] as $key => $label) {
                    $task = $byKey->get($key);
                    if (! $task) continue; // Never manufacture a task for a historical order.
                    $status = strtolower(trim((string) $task->status));
                    $taskState = $task->completed_at !== null || $status === 'completed' ? 'completed'
                        : (in_array($status, ['skipped', 'not applicable', 'n/a'], true) ? 'skipped'
                        : ($state === 'upcoming' || in_array($status, ['not started', 'not start'], true) ? 'upcoming'
                        : 'in_progress'));
                    $stageTasks[] = [
                        'name' => $label, 'status' => $taskState,
                        'updated_at' => $task->completed_at ?? $task->updated_at,
                    ];
                }
            }
            $stageTaskRecords = $taskRows->filter(fn ($task) => isset(self::TASKS[$index][$task->automation_key]));
            $completedAt = $stageTaskRecords->pluck('completed_at')->filter()->sort()->last();
            $stages[] = [
                'name' => $name, 'status' => $state,
                'updated_at' => $state === 'completed' ? $completedAt : null,
                'tasks' => $stageTasks,
            ];
        }

        // Only original approved artwork stored privately, never invoices or client files.
        $documents = $artworkConfirmed ? DB::table('documents')
            ->where('flow_job_id', $orderId)->where('category', 'Artwork')->where('is_final', true)
            ->where('size', '>', 0)->where('size', '<=', 20971520)
            ->orderByDesc('id')->limit(20)->get(['id', 'name', 'size', 'version', 'created_at'])
            ->map(static fn ($file): array => [
                'id' => (int) $file->id,
                'name' => mb_substr(basename(str_replace('\\', '/', (string) $file->name)), 0, 255),
                'size' => (int) $file->size,
                'version' => (int) $file->version,
                'uploaded_at' => $file->created_at,
            ])->all() : [];

        // The audit log is the source for actual changes, not invented dates on stage cards.
        $recent = [];
        if ($artworkConfirmed && $taskRows->isNotEmpty()) {
            $taskIds = $taskRows->pluck('id')->map(fn ($id) => (int) $id)->all();
            $titleById = [];
            foreach ($taskRows as $task) $titleById[(int) $task->id] = $names[$task->automation_key];
            $activities = DB::table('activities')
                ->where('subject_type', FlowJob::class)->where('subject_id', $orderId)
                ->whereIn('event', ['job.task_activity', 'phase.activated'])
                ->orderByDesc('id')->limit(80)->get(['event', 'meta', 'description', 'created_at']);
            foreach ($activities as $activity) {
                if (count($recent) >= 8) break;
                if ($activity->event === 'phase.activated') {
                    $phase = trim((string) $activity->description);
                    if (preg_match('/^(Artwork|Production|QC|Quality control) activated$/i', $phase, $match)) {
                        $recent[] = [
                            'title' => $match[1].' started', 'status' => 'in_progress',
                            'date' => $activity->created_at,
                        ];
                    }
                    continue;
                }
                $meta = json_decode((string) ($activity->meta ?? ''), true);
                if (! is_array($meta) || ! in_array((int) ($meta['task_id'] ?? 0), $taskIds, true)) continue;
                $status = null;
                if (($meta['field'] ?? '') === 'status') $status = $meta['new'] ?? null;
                elseif (isset($meta['changes']['status'])) $status = $meta['changes']['status']['new'] ?? null;
                if (! is_string($status)) continue;
                $mapped = strtolower(trim($status));
                $taskState = $mapped === 'completed' ? 'completed'
                    : (in_array($mapped, ['skipped', 'not applicable', 'n/a'], true) ? 'skipped' : 'in_progress');
                $recent[] = [
                    'title' => $titleById[(int) $meta['task_id']].' · '.ucwords(str_replace('_', ' ', $mapped)),
                    'status' => $taskState, 'date' => $activity->created_at,
                ];
            }
        }
        $recent[] = [
            'title' => 'Order created', 'status' => 'created',
            'date' => $order->created_at?->toIso8601String(),
        ];

        return ['event_id' => $eventId, 'supplier_id' => $supplierId, 'order' => [
            'reference' => $order->displayOrderNumber(),
            'title' => mb_substr(trim((string) $order->title) ?: ('Order '.$order->displayOrderNumber()), 0, 255),
            'source_id' => (int) $orderId, 'revision' => $revision,
            'created_at' => $order->created_at?->toIso8601String(),
            'delivery_date' => $order->supplier_delivery_date?->toDateString()
                ?? $order->estimated_delivery_date?->toDateString(),
            'status' => mb_substr((string) $order->status, 0, 80),
            'currency' => preg_match('/^[A-Z]{3}$/', strtoupper((string) $order->currency))
                ? strtoupper((string) $order->currency) : 'USD',
            'supplier_instruction' => mb_substr((string) ($order->supplier_instruction ?? ''), 0, 2000),
            'items' => $lines, 'stages' => $stages, 'documents' => $documents,
            'recent_updates' => $recent,
        ]];
    }
}
