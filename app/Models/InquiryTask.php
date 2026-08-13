<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InquiryTask extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'requires_submission' => 'boolean',
            'needs_attention' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function inquiry(): BelongsTo { return $this->belongsTo(Inquiry::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assignee_id'); }
    public function setupAssignee(): BelongsTo { return $this->belongsTo(User::class, 'setup_assignee_id'); }
    public function sourceTaskPackItem(): BelongsTo { return $this->belongsTo(TaskPackItem::class, 'source_task_pack_item_id'); }
    public function taskStatus(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'inquiry_task_status_id'); }
    public function documents(): HasMany { return $this->hasMany(InquiryDocument::class)->latest('id'); }
    public function links(): HasMany { return $this->hasMany(InquiryTaskLink::class)->latest('id'); }
    public function comments(): HasMany { return $this->hasMany(InquiryTaskComment::class)->latest('id'); }
}
