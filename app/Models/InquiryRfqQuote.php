<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InquiryRfqQuote extends Model
{
    protected $fillable = ['invitation_id','currency','freight','lead_time_days','validity_days','notes','submitted_total'];

    protected function casts(): array
    {
        return [
            'freight' => 'decimal:2',
            'submitted_total' => 'decimal:2',
            'lead_time_days' => 'integer',
            'validity_days' => 'integer',
        ];
    }

    public function invitation(): BelongsTo { return $this->belongsTo(InquiryRfqInvitation::class, 'invitation_id'); }
    public function items(): HasMany { return $this->hasMany(InquiryRfqQuoteItem::class, 'quote_id')->orderBy('sort_order'); }
}
