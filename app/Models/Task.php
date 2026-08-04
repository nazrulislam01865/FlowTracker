<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'needs_attention' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(FlowJob::class, 'flow_job_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(WorkflowPhase::class, 'workflow_phase_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TaskPackTask::class, 'task_pack_task_id');
    }

    public function setupTemplate(): BelongsTo
    {
        return $this->belongsTo(TaskPackItem::class, 'task_pack_task_id');
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(MasterRecord::class, 'document_category_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(FlowTaskChecklistItem::class, 'flow_task_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FlowTaskComment::class, 'flow_task_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'task_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
