<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\User;
use App\Support\OrderDetailPresenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderSummaryReportService
{
    public const PER_PAGE = 20;

    private const SUPPLIER_REPLY_EVENTS = [
        'job.supplier_reply',
        'job.supplier_delivery_reply',
        'job.supplier_delivery_date_reply',
        'job.supplier_reply_date_recorded',
    ];

    /**
     * Paginate the operational Order Summary report using the same Order
     * visibility scope as the rest of FlowTrack.
     */
    public function paginate(User $user, array $filters, int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        return $this->filteredQuery($user, $filters)
            ->with($this->relations())
            ->paginate(max(1, min(100, $perPage)));
    }

    /** @return array{all:int,urgent:int,awaiting:int,overdue:int} */
    public function counts(User $user, array $filters): array
    {
        $baseFilters = $filters;
        $baseFilters['quick'] = 'all';
        $base = $this->baseQuery($user, $baseFilters);

        return [
            'all' => (clone $base)->count('flow_jobs.id'),
            'urgent' => $this->applyUrgentScope(clone $base)->count('flow_jobs.id'),
            'awaiting' => $this->applyAwaitingReplyScope(clone $base)->count('flow_jobs.id'),
            'overdue' => $this->applyOverdueScope(clone $base)->count('flow_jobs.id'),
        ];
    }

    public function supplierOptions(): Collection
    {
        return MasterRecord::query()
            ->where('workspace_id', app(SetupContext::class)->workspaceId())
            ->where('type', 'supplier')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function warehouseOptions(User $user): Collection
    {
        return app(JobService::class)
            ->visibleQuery($user)
            ->whereNotNull('flow_jobs.warehouse')
            ->where('flow_jobs.warehouse', '<>', '')
            ->reorder()
            ->select('flow_jobs.warehouse')
            ->distinct()
            ->orderBy('flow_jobs.warehouse')
            ->limit(150)
            ->pluck('warehouse')
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->values();
    }

    /** @return array<int,array<string,mixed>> */
    public function rows(LengthAwarePaginator|Collection $orders): array
    {
        $items = $orders instanceof LengthAwarePaginator
            ? collect($orders->items())
            : $orders;

        return $items->map(fn (FlowJob $order) => $this->present($order))->all();
    }

    public function export(User $user, array $filters): StreamedResponse
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($user, 'reports', 'export'), 403);
        abort_unless($access->can($user, 'jobs', 'view'), 403);

        $book = new Spreadsheet();
        $book->getProperties()
            ->setCreator('FlowTrack')
            ->setTitle('Order Summary Report')
            ->setSubject('Supplier, material, sample and delivery tracking');

        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Order Summary');

        $headers = [
            'Supplier',
            'Warehouse',
            'Order No.',
            'Received Date',
            'Urgent or Not',
            'Quantity',
            'Material',
            'ERP Approval Date',
            'Special Orders',
            'Sample/Swatch Sent Date',
            'Sample/Swatch Confirmed Date',
            'Revise / Sample Confirm Date',
            "Supplier Delivery Date\n供应商到货日期",
            "Supplier Reply\n供应商回复交期",
        ];
        $sheet->fromArray($headers, null, 'A1');

        $rowNumber = 2;
        $query = $this->filteredQuery($user, $filters)
            ->reorder()
            ->orderBy('flow_jobs.id')
            ->with($this->relations());

        $query->chunkById(250, function (Collection $orders) use ($sheet, &$rowNumber): void {
            foreach ($orders as $order) {
                $row = $this->present($order);
                $sheet->fromArray([
                    $row['supplier'],
                    $row['warehouse'],
                    $row['order'],
                    $row['received'],
                    $row['urgent'] === 'Y' ? 'Urgent' : 'Normal',
                    $row['quantity'],
                    $row['material'],
                    $row['erp_approval'],
                    $row['special_orders'],
                    $row['sample_sent'],
                    $row['sample_confirmed'],
                    $row['revise_confirm'],
                    $row['supplier_delivery'],
                    $row['supplier_reply'],
                ], null, 'A'.$rowNumber);

                $fill = match ($row['state']) {
                    'overdue' => 'FFF6F5',
                    'urgent' => 'FFF9F2',
                    'complete' => 'F4FBF5',
                    default => null,
                };
                if ($fill) {
                    $sheet->getStyle('A'.$rowNumber.':N'.$rowNumber)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($fill);
                }
                if ($row['supplier_reply'] !== '') {
                    $sheet->getStyle('N'.$rowNumber)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFFFE2');
                    $sheet->getStyle('N'.$rowNumber)->getFont()->setBold(true);
                }

                $rowNumber++;
            }
        }, 'flow_jobs.id', 'id');

        $lastRow = max(1, $rowNumber - 1);
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $fullRange = 'A1:'.$lastColumn.$lastRow;

        $sheet->freezePane('C2');
        $sheet->setAutoFilter('A1:'.$lastColumn.'1');
        $sheet->getStyle($fullRange)->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle($fullRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('DFE6DF');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true)->getColor()->setRGB('243629');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F1F8EF');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(36);

        $widths = [15, 14, 16, 14, 13, 10, 18, 17, 28, 19, 21, 21, 23, 23];
        foreach ($widths as $index => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index + 1))->setWidth($width);
        }

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.35)->setRight(0.25)->setBottom(0.35)->setLeft(0.25);

        $filename = 'FlowTrack-Order-Summary-'.app(WorkspaceSettingsService::class)->localNow()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($book): void {
            $writer = new Xlsx($book);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            $book->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
        ]);
    }

    public function filteredQuery(User $user, array $filters): Builder
    {
        $query = $this->baseQuery($user, $filters);
        $quick = strtolower(trim((string) ($filters['quick'] ?? 'all')));

        return match ($quick) {
            'urgent' => $this->applyUrgentScope($query),
            'awaiting' => $this->applyAwaitingReplyScope($query),
            'overdue' => $this->applyOverdueScope($query),
            default => $query,
        };
    }

    private function baseQuery(User $user, array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $supplierId = max(0, (int) ($filters['supplier_id'] ?? 0));
        $warehouse = trim((string) ($filters['warehouse'] ?? ''));
        $urgency = strtoupper(trim((string) ($filters['urgency'] ?? '')));
        $fromDate = trim((string) ($filters['from_date'] ?? ''));
        $toDate = trim((string) ($filters['to_date'] ?? ''));

        $query = app(JobService::class)
            ->visibleQuery($user)
            ->whereNotIn('flow_jobs.status', JobService::INACTIVE_STATUSES)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.$search.'%';
                $legacy = preg_replace('/^ORDER-/i', 'JOB-', $search) ?: $search;
                $legacyLike = '%'.$legacy.'%';

                $query->where(function (Builder $match) use ($like, $legacyLike): void {
                    $match->whereLike('flow_jobs.job_number', $like)
                        ->orWhereLike('flow_jobs.job_number', $legacyLike)
                        ->orWhereLike('flow_jobs.order_number', $like)
                        ->orWhereLike('flow_jobs.warehouse', $like)
                        ->orWhereLike('flow_jobs.supplier_instruction', $like)
                        ->orWhereHas('supplier', fn (Builder $supplier) => $supplier->whereLike('name', $like))
                        ->orWhereHas('items', fn (Builder $item) => $item
                            ->where('is_removed', false)
                            ->where(function (Builder $line) use ($like): void {
                                $line->whereLike('product_name', $like)
                                    ->orWhereLike('category_name', $like)
                                    ->orWhereHas('supplier', fn (Builder $supplier) => $supplier->whereLike('name', $like));
                            }));
                });
            })
            ->when($supplierId > 0, function (Builder $query) use ($supplierId): void {
                $query->where(function (Builder $supplierScope) use ($supplierId): void {
                    $supplierScope->where('flow_jobs.supplier_id', $supplierId)
                        ->orWhereHas('items', fn (Builder $items) => $items
                            ->where('is_removed', false)
                            ->where('supplier_id', $supplierId));
                });
            })
            ->when($warehouse !== '', fn (Builder $query) => $query->where('flow_jobs.warehouse', $warehouse))
            ->when($urgency === 'Y', fn (Builder $query) => $this->applyUrgentScope($query))
            ->when($urgency === 'N', function (Builder $query): void {
                $query->where(function (Builder $normal): void {
                    $normal->whereNull('flow_jobs.shipment_urgency_ids')
                        ->orWhereJsonLength('flow_jobs.shipment_urgency_ids', 0);
                });
            })
            ->when($fromDate !== '', function (Builder $query) use ($fromDate): void {
                [$fromUtc] = app(WorkspaceSettingsService::class)->localDateRangeUtcBounds($fromDate, '');
                $query->where(function (Builder $received) use ($fromDate, $fromUtc): void {
                    $received->whereDate('flow_jobs.received_date', '>=', $fromDate)
                        ->orWhere(function (Builder $created) use ($fromUtc): void {
                            $created->whereNull('flow_jobs.received_date');
                            if ($fromUtc) $created->where('flow_jobs.created_at', '>=', $fromUtc);
                        });
                });
            })
            ->when($toDate !== '', function (Builder $query) use ($toDate): void {
                [, $toUtc] = app(WorkspaceSettingsService::class)->localDateRangeUtcBounds('', $toDate);
                $query->where(function (Builder $received) use ($toDate, $toUtc): void {
                    $received->whereDate('flow_jobs.received_date', '<=', $toDate)
                        ->orWhere(function (Builder $created) use ($toUtc): void {
                            $created->whereNull('flow_jobs.received_date');
                            if ($toUtc) $created->where('flow_jobs.created_at', '<=', $toUtc);
                        });
                });
            });

        return $query
            ->reorder()
            ->orderByDesc('flow_jobs.received_date')
            ->orderByDesc('flow_jobs.created_at')
            ->orderByDesc('flow_jobs.id');
    }

    private function applyUrgentScope(Builder $query): Builder
    {
        return $query->whereNotNull('flow_jobs.shipment_urgency_ids')
            ->whereJsonLength('flow_jobs.shipment_urgency_ids', '>', 0);
    }

    private function applyAwaitingReplyScope(Builder $query): Builder
    {
        return $query->whereDoesntHave('activities', function (Builder $activity): void {
            $activity->where(function (Builder $reply): void {
                $reply->whereIn('event', self::SUPPLIER_REPLY_EVENTS)
                    ->orWhere('event', 'like', '%supplier%reply%');
            });
        });
    }

    private function applyOverdueScope(Builder $query): Builder
    {
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        return $query
            ->whereNull('flow_jobs.completed_at')
            ->where(function (Builder $dates) use ($today): void {
                $dates->where(function (Builder $estimated) use ($today): void {
                    $estimated->whereNotNull('flow_jobs.estimated_delivery_date')
                        ->whereDate('flow_jobs.estimated_delivery_date', '<', $today);
                })->orWhere(function (Builder $requested) use ($today): void {
                    $requested->whereNull('flow_jobs.estimated_delivery_date')
                        ->whereNotNull('flow_jobs.delivery_date')
                        ->whereDate('flow_jobs.delivery_date', '<', $today);
                });
            });
    }

    /** @return array<string,mixed> */
    private function present(FlowJob $order): array
    {
        $tasks = $order->tasks;
        $taskByKey = function (string $key) use ($tasks): ?Task {
            return $tasks->first(fn (Task $task) => app(OrderWorkflowActionService::class)->automationKey($task) === $key);
        };

        $clientDecision = $taskByKey('ART_CLIENT_ERP_DECISION');
        $sampleTask = $taskByKey('ART_SAMPLE_APPROVAL');
        $activities = $order->activities;

        /*
         * The latest sample/swatch decision controls whether sample dates
         * are allowed to appear in the report. Optional sample tasks may
         * still have lifecycle timestamps even when the branch was skipped,
         * so those task dates must never be used unless the latest decision
         * explicitly says the sample is required.
         */
        $sampleDecisionActivity = $activities->first(fn ($activity) => in_array(
            (string) $activity->event,
            ['job.sample_required', 'job.sample_not_required'],
            true
        ));
        $sampleIsRequired = (string) ($sampleDecisionActivity?->event ?? '') === 'job.sample_required';
        $sampleRequiredActivity = $sampleIsRequired ? $sampleDecisionActivity : null;
        $revisionActivity = $activities->firstWhere('event', 'job.artwork_revision_requested');
        $supplierReplyActivity = $activities->first(function ($activity): bool {
            $event = strtolower((string) $activity->event);
            return in_array((string) $activity->event, self::SUPPLIER_REPLY_EVENTS, true)
                || (str_contains($event, 'supplier') && str_contains($event, 'reply'));
        });

        $activeItems = $order->items->where('is_removed', false)->values();
        $supplierNames = $activeItems->pluck('supplier.name')->filter()->unique()->values();
        if ($supplierNames->isEmpty() && filled($order->supplier?->name)) {
            $supplierNames = collect([(string) $order->supplier->name]);
        }
        $materialNames = $activeItems->pluck('product_name')->filter()->unique()->values();
        if ($materialNames->isEmpty() && filled($order->product)) {
            $materialNames = collect([(string) $order->product]);
        }

        $quantity = (int) $activeItems->sum(fn ($item) => max(0, (int) $item->quantity));
        if ($quantity < 1) $quantity = max(0, (int) $order->quantity);

        $urgent = collect($order->shipment_urgency_ids ?? [])->filter(fn ($value) => (int) $value > 0)->isNotEmpty();
        $supplierDelivery = $order->estimated_delivery_date ?: $order->delivery_date;
        $today = app(WorkspaceSettingsService::class)->localToday();
        $overdue = ! $order->completed_at && $supplierDelivery && $supplierDelivery->lt($today);
        $complete = (bool) $order->completed_at || strcasecmp(trim((string) $order->status), 'Completed') === 0;

        $state = $overdue ? 'overdue' : ($urgent ? 'urgent' : ($complete ? 'complete' : 'normal'));

        return [
            'id' => (int) $order->id,
            'supplier' => $supplierNames->implode(', ') ?: '—',
            'warehouse' => trim((string) $order->warehouse) ?: '—',
            'order' => trim((string) $order->order_number) ?: $order->displayOrderNumber(),
            'received' => $this->date($order->received_date ?: $order->created_at),
            'urgent' => $urgent ? 'Y' : 'N',
            'quantity' => $quantity,
            'material' => $materialNames->implode(', ') ?: '—',
            'erp_approval' => $this->date($sampleDecisionActivity?->created_at ?: $clientDecision?->completed_at),
            'special_orders' => app(RichTextService::class)->plainText((string) $order->supplier_instruction),

            // Optional sample/swatch branch: keep all sample dates blank when skipped.
            'sample_sent' => $sampleIsRequired
                ? $this->date($sampleRequiredActivity?->created_at ?: $sampleTask?->start_date)
                : '',
            'sample_confirmed' => $sampleIsRequired
                ? $this->date($sampleTask?->completed_at)
                : '',
            'revise_confirm' => $revisionActivity
                ? $this->date($revisionActivity->created_at)
                : ($sampleIsRequired ? $this->date($sampleTask?->completed_at) : ''),
            'supplier_delivery' => $this->date($supplierDelivery),
            'supplier_reply' => $this->supplierReplyText($supplierReplyActivity),
            'state' => $state,
        ];
    }

    private function supplierReplyText($activity): string
    {
        if (! $activity) return '';

        foreach (['supplier_reply', 'reply', 'reply_date', 'supplier_reply_date'] as $key) {
            $value = data_get($activity->meta, $key);
            if (is_scalar($value) && trim((string) $value) !== '') return trim((string) $value);
        }

        foreach (['dates', 'reply_dates', 'supplier_reply_dates'] as $key) {
            $values = collect((array) data_get($activity->meta, $key))->filter(fn ($value) => is_scalar($value) && trim((string) $value) !== '');
            if ($values->isNotEmpty()) return $values->map(fn ($value) => trim((string) $value))->implode(' · ');
        }

        $description = trim(app(RichTextService::class)->plainText((string) $activity->description));
        return $description !== '' ? $description : $this->date($activity->created_at);
    }

    private function date($value): string
    {
        if (! $value) return '';
        try {
            return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return '';
        }
    }

    /** @return array<string,mixed> */
    private function relations(): array
    {
        return [
            'supplier:id,name',
            'items' => fn ($items) => $items
                ->select(['id', 'flow_job_id', 'supplier_id', 'product_name', 'category_name', 'quantity', 'is_removed', 'sort_order'])
                ->with('supplier:id,name')
                ->orderBy('sort_order')
                ->orderBy('id'),
            'tasks' => fn ($tasks) => $tasks
                ->select(['id', 'flow_job_id', 'workflow_phase_id', 'task_pack_task_id', 'title', 'status', 'start_date', 'completed_at'])
                ->with('setupTemplate:id,title,automation_key')
                ->orderBy('id'),
            'activities' => fn ($activities) => $activities
                ->where(function (Builder $query): void {
                    $query->whereIn('event', [
                        'job.sample_required',
                        'job.sample_not_required',
                        'job.artwork_revision_requested',
                        ...self::SUPPLIER_REPLY_EVENTS,
                    ])->orWhere('event', 'like', '%supplier%reply%');
                })
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
        ];
    }
}
