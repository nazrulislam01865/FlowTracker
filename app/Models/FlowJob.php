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

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'needs_attention' => 'boolean',
            'completed_at' => 'datetime',
            'commercial_value' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function sourceInquiry(): BelongsTo { return $this->belongsTo(Inquiry::class, 'source_inquiry_id'); }
    public function workflow(): BelongsTo { return $this->belongsTo(Workflow::class); }
    public function phase(): BelongsTo { return $this->belongsTo(WorkflowPhase::class, 'workflow_phase_id'); }
    public function startedFromPhase(): BelongsTo { return $this->belongsTo(WorkflowPhase::class, 'started_from_phase_id'); }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function coordinator(): BelongsTo { return $this->belongsTo(User::class, 'coordinator_id'); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }
    public function items(): HasMany { return $this->hasMany(FlowJobItem::class, 'flow_job_id')->orderBy('sort_order'); }
    public function members(): HasMany { return $this->hasMany(FlowJobMember::class, 'flow_job_id'); }
    public function phaseHistories(): HasMany { return $this->hasMany(FlowJobPhaseHistory::class, 'flow_job_id'); }
    public function activities(): MorphMany { return $this->morphMany(Activity::class, 'subject'); }
    public function createdActivity(): MorphOne { return $this->morphOne(Activity::class, 'subject')->oldestOfMany(); }
    public function latestActivity(): MorphOne { return $this->morphOne(Activity::class, 'subject')->latestOfMany(); }

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
