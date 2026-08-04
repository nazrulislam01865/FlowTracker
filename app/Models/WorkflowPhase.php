<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowPhase extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'allow_job_start' => 'boolean',
            'can_skip' => 'boolean',
            'is_skippable' => 'boolean',
            'requires_approval' => 'boolean',
            'auto_advance_on_ready' => 'boolean',
            'is_active' => 'boolean',
            'sequence' => 'integer',
        ];
    }

    /** Legacy operational workflow relation. */
    public function workflow(): BelongsTo { return $this->belongsTo(Workflow::class, 'workflow_id'); }

    /** SQL workflow_templates relation used by setup. */
    public function workflowTemplate(): BelongsTo { return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id'); }

    public function taskPack(): BelongsTo { return $this->belongsTo(TaskPack::class); }
    public function documentCategory(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'document_category_id'); }
}
