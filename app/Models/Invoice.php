<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:4',
            'tax_amount' => 'decimal:2',
            'previously_invoiced' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
            'emailed_at' => 'datetime',
            'pdf_generated_at' => 'datetime',
            'company_snapshot' => 'array',
            'client_snapshot' => 'array',
            'pdf_layout_version' => 'integer',
        ];
    }

    public function job(): BelongsTo { return $this->belongsTo(FlowJob::class, 'flow_job_id'); }
    public function billingContact(): BelongsTo { return $this->belongsTo(ClientContact::class, 'billing_contact_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class)->orderBy('sort_order')->orderBy('id'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class)->orderBy('payment_date')->orderBy('id'); }

    public function collectedAmount(): float
    {
        if ($this->relationLoaded('payments')) return (float) $this->payments->sum('amount');
        return (float) $this->payments()->sum('amount');
    }

    public function balanceAmount(): float
    {
        return max(0, (float) $this->total - $this->collectedAmount());
    }
}
