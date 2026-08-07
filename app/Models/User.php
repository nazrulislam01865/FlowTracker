<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id', 'department_id', 'name', 'email', 'password',
        'is_super_admin', 'is_active', 'locale', 'profile_image_path',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function assignedTasks(): HasMany { return $this->hasMany(Task::class, 'assignee_id'); }
    public function workspaceMemberships(): HasMany { return $this->hasMany(WorkspaceMembership::class); }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin || $this->role?->slug === 'super-admin';
    }

    public function canAccess(string $permission): bool
    {
        return app(\App\Services\AccessControlService::class)->canPermission($this, $permission);
    }

    public function canModule(string $module, string $action = 'view'): bool
    {
        return app(\App\Services\AccessControlService::class)->can($this, $module, $action);
    }

    public function accessScope(string $module): string
    {
        return app(\App\Services\AccessControlService::class)->scope($this, $module);
    }
}
