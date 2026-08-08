<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InquiryTaskComment extends Model { protected $guarded=[]; public function task(): BelongsTo { return $this->belongsTo(InquiryTask::class, 'inquiry_task_id'); } public function user(): BelongsTo { return $this->belongsTo(User::class); } }
