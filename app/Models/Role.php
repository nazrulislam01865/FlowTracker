<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
            'sensitive_fields' => 'array',
        ];
    }

    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function moduleAccess(): HasMany { return $this->hasMany(RoleModuleAccess::class); }
    public function memberships(): HasMany { return $this->hasMany(WorkspaceMembership::class); }
}
