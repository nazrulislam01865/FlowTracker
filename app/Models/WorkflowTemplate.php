<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTemplate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_default' => 'boolean', 'version' => 'integer'];
    }

    public function phases(): HasMany
    {
        return $this->hasMany(WorkflowPhase::class, 'workflow_template_id')->orderBy('sequence');
    }
}
