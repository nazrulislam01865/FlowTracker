<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FlowJob extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_number',
        'order_number',
        'client_id',
        'workflow_id',
        'workflow_phase_id',
        'started_from_phase_id',
        'owner_id',
        'coordinator_id',
        'title',
        'product',
        'category',
        'quantity',
        'commercial_value',
        'currency',
        'status',
        'health',
        'priority',
        'progress',
        'delivery_date',
        'description',
        'next_action',
        'start_handling',
        'start_reason',
        'needs_attention',
        'completed_at',
        'source_workflow_id',
        'source_workflow_phase_id',
        'source_inquiry_id',
        'received_date',
        'supplier_id',
        'warehouse',
        'supplier_instruction',
        'source_row_id',
        'import_profile',
        'bulk_import_id',
        'created_by',
        'is_repeat_order',
        'repeat_order_number',
        'estimated_delivery_date',
        'production_urgency_ids',
        'shipment_urgency_ids',
        'notes',
        'order_flag_id',
        'attention_requested',
        'attention_reason',
        'attention_by',
        'attention_at',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'shipping_address',
        'shipping_phone_country_code',
        'shipping_phone',
        'shipping_postal_code',
        'shipping_source_address_id',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'estimated_delivery_date' => 'date',
            'received_date' => 'date',
            'needs_attention' => 'boolean',
            'attention_requested' => 'boolean',
            'attention_at' => 'datetime',
            'is_repeat_order' => 'boolean',
            'production_urgency_ids' => 'array',
            'shipment_urgency_ids' => 'array',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'commercial_value' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function shippingSourceAddress(): BelongsTo { return $this->belongsTo(ClientShippingAddress::class, 'shipping_source_address_id'); }
    public function supplier(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'supplier_id'); }
    public function sourceInquiry(): BelongsTo { return $this->belongsTo(Inquiry::class, 'source_inquiry_id'); }
    public function workflow(): BelongsTo { return $this->belongsTo(Workflow::class); }
    public function phase(): BelongsTo { return $this->belongsTo(WorkflowPhase::class, 'workflow_phase_id'); }
    public function startedFromPhase(): BelongsTo { return $this->belongsTo(WorkflowPhase::class, 'started_from_phase_id'); }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function coordinator(): BelongsTo { return $this->belongsTo(User::class, 'coordinator_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function attentionRequester(): BelongsTo { return $this->belongsTo(User::class, 'attention_by'); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }
    public function orderFlag(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'order_flag_id'); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function flaggedTasks(): HasMany { return $this->hasMany(Task::class)->whereNotNull('order_task_flag_id')->whereNull('completed_at')->orderBy('id'); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }
    public function items(): HasMany { return $this->hasMany(FlowJobItem::class, 'flow_job_id')->orderBy('sort_order'); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class, 'flow_job_id')->orderByDesc('issue_date')->orderByDesc('id'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class, 'flow_job_id')->orderByDesc('payment_date')->orderByDesc('id'); }
    public function collection(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(OrderCollection::class, 'flow_job_id'); }
    public function members(): HasMany { return $this->hasMany(FlowJobMember::class, 'flow_job_id'); }
    public function phaseHistories(): HasMany { return $this->hasMany(FlowJobPhaseHistory::class, 'flow_job_id'); }
    public function activities(): MorphMany { return $this->morphMany(Activity::class, 'subject'); }
    public function redoRecords(): HasMany { return $this->hasMany(OrderRedo::class, 'original_order_id')->orderBy('sequence'); }
    public function redoRecord(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(OrderRedo::class, 'redo_order_id'); }
    public function createdActivity(): MorphOne { return $this->morphOne(Activity::class, 'subject')->oldestOfMany(); }
    public function latestActivity(): MorphOne { return $this->morphOne(Activity::class, 'subject')->latestOfMany(); }
    public function latestShipmentActivity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')
            ->ofMany(['id' => 'max'], fn ($query) => $query->where('activities.event', 'job.package_shipped'));
    }

    public function latestWorkflowInvoiceActivity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')
            ->ofMany(['id' => 'max'], fn ($query) => $query->where('activities.event', 'job.workflow_invoice_prepared'));
    }

    public function latestQcActivity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')
            ->ofMany(['id' => 'max'], fn ($query) => $query->whereIn('activities.event', ['job.qc_passed', 'job.qc_issue_reported']));
    }

    public function latestArtworkRevisionActivity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')
            ->ofMany(['id' => 'max'], fn ($query) => $query->where('activities.event', 'job.artwork_revision_requested'));
    }

    /**
     * The database keeps the legacy job_number column for backwards
     * compatibility, while the product UI now presents this entity as an
     * Order. Existing JOB-* identifiers are therefore displayed as ORDER-*
     * without breaking foreign keys, URLs, notifications, or imports.
     */
    public function displayOrderNumber(): string
    {
        $number = (string) $this->job_number;

        return str_starts_with($number, 'JOB-')
            ? 'ORDER-'.substr($number, 4)
            : $number;
    }
}
