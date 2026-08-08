<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InquiryItem extends Model { protected $guarded=[]; protected function casts(): array { return ['quantity'=>'decimal:2']; } public function inquiry(): BelongsTo { return $this->belongsTo(Inquiry::class); } }
