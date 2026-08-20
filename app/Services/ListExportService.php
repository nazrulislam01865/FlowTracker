<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\Inquiry;
use App\Models\MasterRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListExportService
{
    public function exportOrders(User $user, array $filters): StreamedResponse
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($user, 'reports', 'export'), 403);
        abort_unless($access->can($user, 'jobs', 'view'), 403);

        $query = app(JobService::class)->ordersListQuery(
            $user,
            (string) ($filters['search'] ?? ''),
            $this->positiveInt($filters['client_id'] ?? null),
            $this->positiveInt($filters['phase_id'] ?? null),
            $this->positiveInt($filters['assignee_id'] ?? null),
            $this->positiveInt($filters['owner_id'] ?? null),
            (string) ($filters['metric_filter'] ?? ''),
            (string) ($filters['date_from'] ?? ''),
            (string) ($filters['date_to'] ?? ''),
            $this->positiveInt($filters['bulk_import_id'] ?? null),
        );

        $relations = [
            'client', 'supplier', 'sourceInquiry', 'workflow', 'phase', 'startedFromPhase',
            'owner', 'coordinator', 'creator', 'attentionRequester', 'orderFlag',
            'shippingSourceAddress', 'items.updatedBy', 'members.user',
            'phaseHistories.phase', 'phaseHistories.actor',
            'tasks' => fn ($tasks) => $access->applyTaskScope($tasks, $user)->with([
                'assignee', 'completionAssignee', 'phase', 'orderTaskStatus', 'orderTaskFlag',
                'checklistItems', 'comments.user', 'documents.uploader', 'links.creator',
            ]),
            'documents.uploader', 'documents.task', 'activities.user',
        ];

        $canViewFinance = $access->can($user, 'finance', 'view');
        if ($canViewFinance) {
            $relations[] = 'invoices.billingContact';
            $relations[] = 'invoices.creator';
            $relations[] = 'invoices.items';
            $relations[] = 'invoices.payments.recorder';
            $relations[] = 'payments.invoice';
            $relations[] = 'payments.recorder';
            $relations[] = 'collection.owner';
            $relations[] = 'collection.updates.actor';
        }

        /** @var Collection<int, FlowJob> $orders */
        $orders = $query->with($relations)->get();
        $urgencyNames = $this->urgencyNameMap();
        $spreadsheet = $this->orderWorkbook($orders, $urgencyNames, $canViewFinance);

        return $this->download(
            $spreadsheet,
            'FlowTrack-orders-all-details-'.app(WorkspaceSettingsService::class)->localNow()->format('Ymd-His').'.xlsx'
        );
    }

    public function exportInquiries(User $user, array $filters): StreamedResponse
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($user, 'reports', 'export'), 403);
        abort_unless($access->can($user, 'inquiries', 'view'), 403);

        /** @var Builder $query */
        $query = app(InquiryService::class)->listQuery($user, $filters)
            ->reorder()
            ->orderByDesc('inquiries.created_at')
            ->orderByDesc('inquiries.id');

        /** @var Collection<int, Inquiry> $inquiries */
        $inquiries = $query->with([
            'client', 'owner', 'creator', 'sourceTaskPack', 'sourceWorkflow', 'convertedJob',
            'items',
            'tasks' => fn ($tasks) => $access->applyInquiryTaskScope($tasks, $user)->with([
                'assignee', 'completionAssignee', 'setupAssignee', 'sourceTaskPackItem',
                'sourceWorkflowPhase', 'taskStatus', 'documents.uploader', 'comments.user', 'links.creator',
            ]),
            'documents.uploader', 'documents.task', 'activities.user',
        ])->get();

        $canViewFinance = $access->can($user, 'finance', 'view');
        $spreadsheet = $this->inquiryWorkbook($inquiries, $canViewFinance);

        return $this->download(
            $spreadsheet,
            'FlowTrack-inquiries-all-details-'.app(WorkspaceSettingsService::class)->localNow()->format('Ymd-His').'.xlsx'
        );
    }

    private function orderWorkbook(Collection $orders, Collection $urgencyNames, bool $canViewFinance): Spreadsheet
    {
        $book = new Spreadsheet();
        $book->getProperties()
            ->setCreator('FlowTrack')
            ->setTitle('Order list export')
            ->setSubject('Orders and related details');

        $this->fillSheet($book->getActiveSheet(), 'Orders', [
            'Order ID', 'Order Number', 'Reference Order No.', 'Repeat Order?', 'Repeat Order No.',
            'Order Title', 'Order Description', 'Notes', 'Client ID', 'Client Code', 'Client Name',
            'Source Inquiry', 'Workflow', 'Current Phase', 'Started From Phase', 'Status', 'Health',
            'Order Flag', 'Priority', 'Progress %', 'Owner', 'Coordinator', 'Created By', 'Received Date',
            'Customer Requested Delivery Date', 'Estimated Delivery Date', 'Shipping Address',
            'Phone Country Code', 'Phone Number', 'Postal Code', 'Production Urgency', 'Shipment Urgency',
            'Primary Product', 'Category', 'Quantity', 'Commercial Value', 'Currency', 'Supplier', 'Warehouse',
            'Supplier Instruction', 'Source Row ID', 'Import Profile', 'Needs Attention?', 'Attention Requested?',
            'Attention Reason', 'Attention By', 'Completed At', 'Created At', 'Updated At',
        ], $orders->map(fn (FlowJob $order) => [
            $order->id,
            $order->displayOrderNumber(),
            $order->order_number,
            $this->yesNo($order->is_repeat_order),
            $order->repeat_order_number,
            $order->title,
            $this->plainText($order->description),
            $this->plainText($order->notes),
            $order->client_id,
            $order->client?->code,
            $order->client?->name,
            $order->sourceInquiry?->inquiry_number,
            $order->workflow?->name,
            $order->phase?->name,
            $order->startedFromPhase?->name,
            $order->status,
            $order->health,
            $order->orderFlag?->name,
            $order->priority,
            $order->progress,
            $order->owner?->name,
            $order->coordinator?->name,
            $order->creator?->name,
            $this->date($order->received_date),
            $this->date($order->delivery_date),
            $this->date($order->estimated_delivery_date),
            $this->plainText($order->shipping_address),
            $order->shipping_phone_country_code,
            $order->shipping_phone,
            $order->shipping_postal_code,
            $this->masterNames($order->production_urgency_ids, $urgencyNames),
            $this->masterNames($order->shipment_urgency_ids, $urgencyNames),
            $order->product,
            $order->category,
            $order->quantity,
            $canViewFinance ? $order->commercial_value : '',
            $canViewFinance ? $order->currency : '',
            $order->supplier?->name,
            $order->warehouse,
            $this->plainText($order->supplier_instruction),
            $order->source_row_id,
            $order->import_profile,
            $this->yesNo($order->needs_attention),
            $this->yesNo($order->attention_requested),
            $this->plainText($order->attention_reason),
            $order->attentionRequester?->name,
            $this->dateTime($order->completed_at),
            $this->dateTime($order->created_at),
            $this->dateTime($order->updated_at),
        ]));

        $this->fillSheet($book->createSheet(), 'Products', [
            'Order Number', 'Item ID', 'Product', 'Category', 'Quantity', 'Unit Price', 'Notes', 'Updated By', 'Updated At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->items->map(fn ($item) => [
            $order->displayOrderNumber(), $item->id, $item->product_name, $item->category_name, $item->quantity,
            $item->unit_price, $this->plainText($item->notes), $item->updatedBy?->name, $this->dateTime($item->updated_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Tasks', [
            'Order Number', 'Task Number', 'Phase', 'Task', 'Description', 'Assignee', 'Status', 'Status Master',
            'Flag', 'Priority', 'Progress %', 'Start Date', 'Due Date', 'Needs Attention?', 'Attention Reason',
            'Completed At', 'Completed By', 'Created At', 'Updated At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->tasks->map(fn ($task) => [
            $order->displayOrderNumber(), $task->task_number, $task->phase?->name, $task->title,
            $this->plainText($task->description), $task->assignee?->name, $task->status, $task->orderTaskStatus?->name,
            $task->orderTaskFlag?->name, $task->priority, $task->progress, $this->date($task->start_date),
            $this->date($task->due_date), $this->yesNo($task->needs_attention), $this->plainText($task->attention_reason),
            $this->dateTime($task->completed_at), $task->completionAssignee?->name,
            $this->dateTime($task->created_at), $this->dateTime($task->updated_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Task Checklist', [
            'Order Number', 'Task Number', 'Task', 'Checklist Item', 'Completed?', 'Sort Order', 'Created At', 'Updated At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->tasks->flatMap(fn ($task) => $task->checklistItems->map(fn ($item) => [
            $order->displayOrderNumber(), $task->task_number, $task->title, $item->label,
            $this->yesNo($item->is_completed), $item->sort_order, $this->dateTime($item->created_at), $this->dateTime($item->updated_at),
        ]))));

        $this->fillSheet($book->createSheet(), 'Task Comments', [
            'Order Number', 'Task Number', 'Task', 'Comment By', 'Comment', 'Created At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->tasks->flatMap(fn ($task) => $task->comments->map(fn ($comment) => [
            $order->displayOrderNumber(), $task->task_number, $task->title, $comment->user?->name,
            $this->plainText($comment->body), $this->dateTime($comment->created_at),
        ]))));

        $this->fillSheet($book->createSheet(), 'Task Links', [
            'Order Number', 'Task Number', 'Task', 'URL', 'Created By', 'Created At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->tasks->flatMap(fn ($task) => $task->links->map(fn ($link) => [
            $order->displayOrderNumber(), $task->task_number, $task->title, $link->url,
            $link->creator?->name, $this->dateTime($link->created_at),
        ]))));

        $this->fillSheet($book->createSheet(), 'Documents', [
            'Order Number', 'Document Number', 'Task', 'Category', 'Name', 'Note', 'MIME Type', 'Size Bytes',
            'Version', 'Final?', 'Uploaded By', 'Created At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->documents->map(fn ($document) => [
            $order->displayOrderNumber(), $document->document_number, $document->task?->title, $document->category,
            $document->name, $this->plainText($document->note), $document->mime_type, $document->size,
            $document->version, $this->yesNo($document->is_final), $document->uploader?->name, $this->dateTime($document->created_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Members', [
            'Order Number', 'User', 'Access Level', 'Manage Tasks?', 'Upload Documents?', 'View Financials?', 'Added At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->members->map(fn ($member) => [
            $order->displayOrderNumber(), $member->user?->name, $member->access_level,
            $this->yesNo($member->can_manage_tasks), $this->yesNo($member->can_upload_documents),
            $this->yesNo($member->can_view_financials), $this->dateTime($member->created_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Phase History', [
            'Order Number', 'Phase', 'Status', 'Phase Owner ID', 'Target Date', 'Health Override', 'Changed By',
            'Entered At', 'Completed At', 'Created At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->phaseHistories->map(fn ($history) => [
            $order->displayOrderNumber(), $history->phase?->name, $history->status, $history->phase_owner_id,
            $this->date($history->target_date), $history->health_override, $history->actor?->name,
            $this->dateTime($history->entered_at), $this->dateTime($history->completed_at), $this->dateTime($history->created_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Activities', [
            'Order Number', 'Event', 'Description', 'User', 'Metadata', 'Created At',
        ], $orders->flatMap(fn (FlowJob $order) => $order->activities->map(fn ($activity) => [
            $order->displayOrderNumber(), $activity->event, $this->plainText($activity->description), $activity->user?->name,
            $this->json($activity->meta), $this->dateTime($activity->created_at),
        ])));

        if ($canViewFinance) {
            $this->fillSheet($book->createSheet(), 'Invoices', [
                'Order Number', 'Invoice Number', 'Type', 'Currency', 'Issue Date', 'Due Date', 'Billing Contact',
                'Billing Email', 'PO Reference', 'Notes', 'Subtotal', 'Tax Rate', 'Tax Amount', 'Previously Invoiced',
                'Total', 'Status', 'Created By', 'Created At', 'Updated At',
            ], $orders->flatMap(fn (FlowJob $order) => $order->invoices->map(fn ($invoice) => [
                $order->displayOrderNumber(), $invoice->invoice_number, $invoice->type, $invoice->currency,
                $this->date($invoice->issue_date), $this->date($invoice->due_date),
                $invoice->billing_contact_name ?: $invoice->billingContact?->name, $invoice->billing_contact_email,
                $invoice->purchase_order_reference, $this->plainText($invoice->notes), $invoice->subtotal, $invoice->tax_rate,
                $invoice->tax_amount, $invoice->previously_invoiced, $invoice->total, $invoice->status,
                $invoice->creator?->name, $this->dateTime($invoice->created_at), $this->dateTime($invoice->updated_at),
            ])));

            $this->fillSheet($book->createSheet(), 'Invoice Items', [
                'Order Number', 'Invoice Number', 'Description', 'Quantity', 'Unit Price', 'Amount', 'Sort Order',
            ], $orders->flatMap(fn (FlowJob $order) => $order->invoices->flatMap(fn ($invoice) => $invoice->items->map(fn ($item) => [
                $order->displayOrderNumber(), $invoice->invoice_number, $item->description, $item->quantity,
                $item->unit_price, $item->amount, $item->sort_order,
            ]))));

            $this->fillSheet($book->createSheet(), 'Payments', [
                'Order Number', 'Payment Number', 'Invoice Number', 'Payment Date', 'Method', 'Amount', 'Reference',
                'Notes', 'Recorded By', 'Created At',
            ], $orders->flatMap(fn (FlowJob $order) => $order->payments->map(fn ($payment) => [
                $order->displayOrderNumber(), $payment->payment_number, $payment->invoice?->invoice_number,
                $this->date($payment->payment_date), $payment->method, $payment->amount, $payment->reference,
                $this->plainText($payment->notes), $payment->recorder?->name, $this->dateTime($payment->created_at),
            ])));

            $this->fillSheet($book->createSheet(), 'Collections', [
                'Order Number', 'Collection Owner', 'Last Follow-up', 'Next Follow-up', 'Latest Note', 'Updated At',
            ], $orders->filter(fn (FlowJob $order) => $order->collection)->map(fn (FlowJob $order) => [
                $order->displayOrderNumber(), $order->collection?->owner?->name,
                $this->date($order->collection?->last_follow_up_at), $this->date($order->collection?->next_follow_up_at),
                $this->plainText($order->collection?->latest_note), $this->dateTime($order->collection?->updated_at),
            ]));
        }

        $book->setActiveSheetIndex(0);
        return $book;
    }

    private function inquiryWorkbook(Collection $inquiries, bool $canViewFinance): Spreadsheet
    {
        $book = new Spreadsheet();
        $book->getProperties()
            ->setCreator('FlowTrack')
            ->setTitle('Inquiry list export')
            ->setSubject('Inquiries and related details');

        $this->fillSheet($book->getActiveSheet(), 'Inquiries', [
            'Inquiry ID', 'Inquiry Number', 'Reference Number', 'Subject', 'Requirement Notes', 'Client ID', 'Client Code',
            'Client Name', 'Client Contact', 'Owner', 'Created By', 'Request Source', 'Received Date', 'Required Delivery Date',
            'Initial Follow-up Date', 'Priority', 'Status', 'Result', 'Dead Reason', 'Dead Note', 'Target Price', 'Currency',
            'Source Task Pack', 'Source Workflow', 'Converted Order', 'Needs Attention?', 'Attention Reason', 'Started At',
            'Completed At', 'Created At', 'Updated At',
        ], $inquiries->map(fn (Inquiry $inquiry) => [
            $inquiry->id, $inquiry->inquiry_number, $inquiry->reference_number, $inquiry->subject,
            $this->plainText($inquiry->requirement_notes), $inquiry->client_id, $inquiry->client?->code, $inquiry->client?->name,
            $inquiry->client_contact, $inquiry->owner?->name, $inquiry->creator?->name, $inquiry->request_source,
            $this->date($inquiry->received_date), $this->date($inquiry->required_delivery_date),
            $this->date($inquiry->initial_follow_up_date), $inquiry->priority, $inquiry->status, $inquiry->result,
            $inquiry->dead_reason, $this->plainText($inquiry->dead_note),
            $canViewFinance ? $inquiry->target_price : '', $canViewFinance ? $inquiry->currency : '',
            $inquiry->sourceTaskPack?->name, $inquiry->sourceWorkflow?->name,
            $inquiry->convertedJob?->displayOrderNumber(), $this->yesNo($inquiry->needs_attention),
            $this->plainText($inquiry->attention_reason), $this->dateTime($inquiry->started_at),
            $this->dateTime($inquiry->completed_at), $this->dateTime($inquiry->created_at), $this->dateTime($inquiry->updated_at),
        ]));

        $this->fillSheet($book->createSheet(), 'Products', [
            'Inquiry Number', 'Item ID', 'Category', 'Product / Item', 'Quantity', 'Unit', 'Unit Price', 'Notes', 'Created At', 'Updated At',
        ], $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->items->map(fn ($item) => [
            $inquiry->inquiry_number, $item->id, $item->category, $item->item_name, $item->quantity, $item->unit,
            $item->unit_price, $this->plainText($item->notes), $this->dateTime($item->created_at), $this->dateTime($item->updated_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Tasks', [
            'Inquiry Number', 'Task ID', 'Sequence', 'Workflow Phase', 'Task', 'Description', 'Assignee', 'Setup Assignee',
            'Status', 'Status Master', 'Due Date', 'Requires Submission?', 'Submission Label', 'Needs Attention?',
            'Attention Reason', 'Started At', 'Completed At', 'Completed By', 'Created At', 'Updated At',
        ], $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks->map(fn ($task) => [
            $inquiry->inquiry_number, $task->id, $task->sequence, $task->sourceWorkflowPhase?->name, $task->title,
            $this->plainText($task->description), $task->assignee?->name, $task->setupAssignee?->name, $task->status,
            $task->taskStatus?->name, $this->date($task->due_date), $this->yesNo($task->requires_submission),
            $task->submission_label, $this->yesNo($task->needs_attention), $this->plainText($task->attention_reason),
            $this->dateTime($task->started_at), $this->dateTime($task->completed_at), $task->completionAssignee?->name,
            $this->dateTime($task->created_at), $this->dateTime($task->updated_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Task Comments', [
            'Inquiry Number', 'Task ID', 'Task', 'Comment By', 'Comment', 'Created At',
        ], $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks->flatMap(fn ($task) => $task->comments->map(fn ($comment) => [
            $inquiry->inquiry_number, $task->id, $task->title, $comment->user?->name,
            $this->plainText($comment->body), $this->dateTime($comment->created_at),
        ]))));

        $this->fillSheet($book->createSheet(), 'Task Links', [
            'Inquiry Number', 'Task ID', 'Task', 'URL', 'Created By', 'Created At',
        ], $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks->flatMap(fn ($task) => $task->links->map(fn ($link) => [
            $inquiry->inquiry_number, $task->id, $task->title, $link->url, $link->creator?->name, $this->dateTime($link->created_at),
        ]))));

        $this->fillSheet($book->createSheet(), 'Documents', [
            'Inquiry Number', 'Task', 'Name', 'MIME Type', 'Size Bytes', 'Uploaded By', 'Created At',
        ], $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->documents->map(fn ($document) => [
            $inquiry->inquiry_number, $document->task?->title, $document->name, $document->mime_type,
            $document->size, $document->uploader?->name, $this->dateTime($document->created_at),
        ])));

        $this->fillSheet($book->createSheet(), 'Activities', [
            'Inquiry Number', 'Event', 'Description', 'User', 'Metadata', 'Created At',
        ], $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->activities->map(fn ($activity) => [
            $inquiry->inquiry_number, $activity->event, $this->plainText($activity->description), $activity->user?->name,
            $this->json($activity->meta), $this->dateTime($activity->created_at),
        ])));

        $book->setActiveSheetIndex(0);
        return $book;
    }

    private function fillSheet(Worksheet $sheet, string $title, array $headers, iterable $rows): void
    {
        $sheet->setTitle(mb_substr($title, 0, 31));
        $this->writeRow($sheet, 1, $headers, true);

        $rowNumber = 2;
        foreach ($rows as $row) {
            $this->writeRow($sheet, $rowNumber++, array_values((array) $row));
        }

        $lastColumn = Coordinate::stringFromColumnIndex(max(1, count($headers)));
        $lastRow = max(1, $rowNumber - 1);
        $range = 'A1:'.$lastColumn.$lastRow;

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:'.$lastColumn.$lastRow);
        $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E78');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFD9E2F3');
        $sheet->getRowDimension(1)->setRowHeight(24);

        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $label = mb_strtolower((string) $header);
            $width = 16;
            if (preg_match('/description|notes|address|comment|metadata|instruction|reason|url/', $label)) {
                $width = 38;
            } elseif (preg_match('/title|product|client|workflow|phase|assignee|owner|status|number|reference/', $label)) {
                $width = 22;
            } elseif (preg_match('/date| at$|created|updated|completed|started|entered|follow-up/', $label)) {
                $width = 20;
            }
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    private function writeRow(Worksheet $sheet, int $rowNumber, array $values, bool $header = false): void
    {
        foreach ($values as $index => $value) {
            $cell = Coordinate::stringFromColumnIndex($index + 1).$rowNumber;

            if ($header || is_string($value) || $value === null || is_bool($value)) {
                $sheet->setCellValueExplicit($cell, $value === null ? '' : (string) $value, DataType::TYPE_STRING);
                continue;
            }

            $sheet->setCellValue($cell, $value);
        }
    }

    private function download(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    private function urgencyNameMap(): Collection
    {
        return MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->whereIn('type', ['production_urgency', 'shipment_urgency'])
            ->pluck('name', 'id');
    }

    private function masterNames(mixed $ids, Collection $map): string
    {
        return collect((array) $ids)
            ->map(fn ($id) => $map->get((int) $id))
            ->filter()
            ->implode(', ');
    }

    private function positiveInt(mixed $value): ?int
    {
        $value = is_numeric($value) ? (int) $value : 0;
        return $value > 0 ? $value : null;
    }

    private function yesNo(mixed $value): string
    {
        return $value ? 'Yes' : 'No';
    }

    private function plainText(mixed $value): string
    {
        if ($value === null) return '';
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\t ]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\R{3,}/u', "\n\n", $text) ?? $text;
        return trim($text);
    }

    private function date(mixed $value): string
    {
        if (!$value) return '';
        if (is_object($value) && method_exists($value, 'format')) return $value->format('Y-m-d');
        return (string) $value;
    }

    private function dateTime(mixed $value): string
    {
        if (!$value) return '';
        if (is_object($value) && method_exists($value, 'timezone') && method_exists($value, 'format')) {
            return $value->copy()->timezone(app(WorkspaceSettingsService::class)->displayTimezone())->format('Y-m-d H:i:s');
        }
        if (is_object($value) && method_exists($value, 'format')) return $value->format('Y-m-d H:i:s');
        return (string) $value;
    }

    private function json(mixed $value): string
    {
        if ($value === null || $value === [] || $value === '') return '';
        if (is_string($value)) return $value;
        return (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
