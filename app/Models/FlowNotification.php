<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class FlowNotification extends Model { protected $guarded=[]; protected $table='flow_notifications'; protected function casts(): array { return ['read_at'=>'datetime']; } public function user(): BelongsTo { return $this->belongsTo(User::class); } public function job(): BelongsTo { return $this->belongsTo(FlowJob::class,'flow_job_id'); } public function task(): BelongsTo { return $this->belongsTo(Task::class,'flow_task_id'); } }
