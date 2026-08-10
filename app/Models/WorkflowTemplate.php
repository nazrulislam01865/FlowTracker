<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTemplate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function phases(): HasMany
    {
        return $this->hasMany(WorkflowPhase::class, 'workflow_template_id')->orderBy('sequence');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'workflow_template_client')
            ->withTimestamps();
    }

    public function scopeAvailableFor(Builder $query, string $appliesTo, ?int $clientId = null): Builder
    {
        return $query
            ->where('applies_to', $appliesTo)
            ->where(function (Builder $availability) use ($clientId): void {
                $availability->where('client_availability', 'all');

                if ($clientId) {
                    $availability->orWhere(function (Builder $specific) use ($clientId): void {
                        $specific->where('client_availability', 'specific')
                            ->whereHas('clients', fn (Builder $clients) => $clients->whereKey($clientId));
                    });
                }
            });
    }
}
