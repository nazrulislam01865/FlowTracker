<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskPackItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'due_offset_days' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function taskPack(): BelongsTo { return $this->belongsTo(TaskPack::class); }
    public function defaultAssignee(): BelongsTo { return $this->belongsTo(User::class, 'default_assignee_id'); }
    public function defaultDepartment(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'default_department_id'); }
    public function priority(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'priority_id'); }
    public function documentCategory(): BelongsTo { return $this->belongsTo(MasterRecord::class, 'document_category_id'); }
}
