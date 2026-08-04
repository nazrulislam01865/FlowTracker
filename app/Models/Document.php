<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['is_final' => 'boolean']; }
    public function job(): BelongsTo { return $this->belongsTo(FlowJob::class, 'flow_job_id'); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function task(): BelongsTo { return $this->belongsTo(Task::class, 'task_id'); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
