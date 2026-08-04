<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
class Client extends Model { protected $guarded=[]; protected function casts(): array { return ['outstanding_balance'=>'decimal:2','is_active'=>'boolean']; } public function accountManager(): BelongsTo { return $this->belongsTo(User::class,'account_manager_id'); } public function jobs(): HasMany { return $this->hasMany(FlowJob::class); } public function tasks(): HasManyThrough { return $this->hasManyThrough(Task::class, FlowJob::class, 'client_id', 'flow_job_id'); } }
