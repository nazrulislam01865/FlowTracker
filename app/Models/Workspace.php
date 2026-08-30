<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'timezone',
        'default_currency',
        'logo_path',
        'favicon_path',
        'is_active',
        'company_profile',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'company_profile' => 'array',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(WorkspaceMembership::class);
    }
}
