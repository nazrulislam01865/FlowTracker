<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InquiryDocument extends Model { protected $guarded=[]; public function inquiry(): BelongsTo { return $this->belongsTo(Inquiry::class); } public function task(): BelongsTo { return $this->belongsTo(InquiryTask::class, 'inquiry_task_id'); } public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); } }
