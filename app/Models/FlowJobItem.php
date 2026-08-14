<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowJobItem extends Model
{
    protected $table = 'flow_job_items';
    protected $guarded = [];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(FlowJob::class, 'flow_job_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
