<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkspaceMembership;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserEditorService
{
    public function canEdit(User $actor, User $target): bool
    {
        if ($actor->id === $target->id) {
            return true;
        }

        if (! app(AccessControlService::class)->isAdministrator($actor)) {
            return false;
        }

        return WorkspaceMembership::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('user_id', $target->id)
            ->exists();
    }

    public function canManageAccess(User $actor): bool
    {
        return app(AccessControlService::class)->isAdministrator($actor);
    }

    public function update(User $target, array $data, User $actor): User
    {
        abort_unless($this->canEdit($actor, $target), 403);

        $canManageAccess = $this->canManageAccess($actor);
        $oldStatus = $this->accountStatus($target);
        $passwordChanged = filled($data['password'] ?? null);
        $signOutSessions = (bool) ($data['sign_out_sessions'] ?? true);

        DB::transaction(function () use ($target, $data, $actor, $canManageAccess, $passwordChanged, $signOutSessions, $oldStatus) {
            $changes = [
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'wechat_id' => $this->nullableString($data['wechat_id'] ?? null),
                'phone' => $this->nullableString($data['phone'] ?? null),
            ];

            if ($canManageAccess) {
                $role = Role::query()
                    ->where('workspace_id', $this->workspaceId())
                    ->findOrFail((int) $data['role_id']);

                $departmentId = filled($data['department_id'] ?? null) ? (int) $data['department_id'] : null;
                if ($departmentId) {
                    Department::query()->findOrFail($departmentId);
                }

                $status = (string) ($data['account_status'] ?? 'active');
                abort_unless(in_array($status, ['active', 'inactive', 'suspended'], true), 422);

                if ($target->isSuperAdmin()) {
                    $changes['role_id'] = $target->role_id;
                    $changes['is_active'] = true;
                    $changes['account_status'] = 'active';
                } else {
                    abort_if($target->id === $actor->id && $status !== 'active', 422, 'You cannot deactivate or suspend your own signed-in account.');
                    $changes['role_id'] = $role->id;
                    $changes['department_id'] = $departmentId;
                    $changes['account_status'] = $status;
                    $changes['is_active'] = $status === 'active';
                }
            }

            if ($passwordChanged) {
                $changes['password'] = Hash::make((string) $data['password']);
            }

            $target->update($changes);
            $target->refresh();

            $membership = WorkspaceMembership::query()->firstOrNew([
                'workspace_id' => $this->workspaceId(),
                'user_id' => $target->id,
            ]);
            $membership->role_id = $target->role_id;
            $membership->department_id = $target->department_id;
            $membership->job_title = $this->nullableString($data['position'] ?? null);
            $membership->status = $this->accountStatus($target);
            $membership->joined_at ??= $target->created_at ?: now();

            if ($canManageAccess && array_key_exists('business_unit', $data)) {
                $businessUnit = (string) $data['business_unit'];
                abort_unless(in_array($businessUnit, ['iid', 'nep', 'both'], true), 422);
                $membership->business_unit = $businessUnit;
            } elseif (! $membership->exists && Schema::hasColumn('workspace_memberships', 'business_unit')) {
                $membership->business_unit = 'both';
            }

            $membership->save();

            if ($passwordChanged && $signOutSessions) {
                $this->invalidateSessions($target, $actor);
            }

            $newStatus = $this->accountStatus($target);
            if ($oldStatus === 'active' && $newStatus !== 'active') {
                $this->invalidateSessions($target, $actor, true);
            }

            Activity::create([
                'subject_type' => User::class,
                'subject_id' => $target->id,
                'user_id' => $actor->id,
                'event' => $canManageAccess ? 'access.user_updated' : 'profile.user_updated',
                'description' => ($actor->id === $target->id ? 'Updated own user profile' : 'Updated user '.$target->name).($passwordChanged ? ' and changed password' : ''),
                'meta' => [
                    'account_status' => $this->accountStatus($target),
                    'password_changed' => $passwordChanged,
                ],
            ]);
        });

        return $target->refresh()->loadMissing(['role', 'department']);
    }

    public function updateProfileImage(User $target, UploadedFile $image, User $actor): User
    {
        abort_unless($this->canEdit($actor, $target), 403);

        return app(ProfileService::class)->updateProfileImage($target, $image);
    }

    public function accountStatus(User $user): string
    {
        $stored = trim((string) ($user->account_status ?? ''));
        if (in_array($stored, ['active', 'inactive', 'suspended'], true)) {
            return $stored;
        }

        return $user->is_active ? 'active' : 'inactive';
    }

    public function businessUnit(User $user): string
    {
        $value = WorkspaceMembership::query()
            ->where('workspace_id', $this->workspaceId())
            ->where('user_id', $user->id)
            ->value('business_unit');

        return in_array($value, ['iid', 'nep', 'both'], true) ? $value : 'both';
    }

    private function invalidateSessions(User $target, User $actor, bool $forceAll = false): void
    {
        if (Schema::hasTable('sessions')) {
            $query = DB::table('sessions')->where('user_id', $target->id);

            if (! $forceAll && $target->id === $actor->id && session()->isStarted()) {
                $query->where('id', '!=', session()->getId());
            }

            $query->delete();
        }

        if ($forceAll || $target->id !== $actor->id) {
            Cache::forget('flowtrack:active-login:'.$target->id);
        }
    }

    private function workspaceId(): int
    {
        return app(SetupContext::class)->workspaceId();
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value !== '' ? $value : null;
    }
}
