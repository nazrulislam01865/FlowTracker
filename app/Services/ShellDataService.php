<?php

namespace App\Services;

use App\Models\FlowNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ShellDataService
{
    public function for(User $user): array
    {
        return Cache::remember($this->key($user->id), now()->addSeconds(60), function () use ($user) {
            return [
                'unread_notifications' => FlowNotification::query()
                    ->where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
                'open_my_work' => $user->canModule('tasks', 'view')
                    ? app(TaskService::class)
                        ->visibleQuery($user)
                        ->whereNull('completed_at')
                        ->whereHas('job', fn ($job) => $job
                            ->whereHas('client', fn ($client) => $client->where('is_active', true))
                            ->whereNull('completed_at')
                            ->whereNotIn('status', JobService::INACTIVE_STATUSES))
                        ->count()
                    : 0,
            ];
        });
    }

    public function forget(int $userId): void
    {
        Cache::forget($this->key($userId));
    }

    private function key(int $userId): string
    {
        return 'flowtrack:shell:clients-'.app(ClientService::class)->lifecycleVersion().':user:'.$userId;
    }
}
