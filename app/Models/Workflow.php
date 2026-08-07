<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Workflow extends Model { protected $guarded=[]; protected function casts(): array { return ['is_active'=>'boolean','is_snapshot'=>'boolean']; } public function phases(): HasMany { return $this->hasMany(WorkflowPhase::class)->orderBy('sequence'); } }
