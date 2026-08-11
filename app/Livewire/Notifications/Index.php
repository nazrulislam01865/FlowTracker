<?php

namespace App\Livewire\Notifications;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Services\NotificationService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use UsesPagePlaceholder;
    use WithPagination;

    public function markAllRead(): void
    {
        app(NotificationService::class)->markAllRead(auth()->user());
        $this->dispatch('flowtrack-unread-count', count: 0);
    }

    public function markRead(int $id): void
    {
        $user = auth()->user();
        $service = app(NotificationService::class);
        $notification = $service->visibleQuery($user)->whereKey($id)->firstOrFail();
        $notification->forceFill(['read_at' => now()])->save();
        app(\App\Services\DashboardService::class)->forgetMentions($user);
        app(\App\Services\ShellDataService::class)->forget((int) $user->id);
        $this->dispatch('flowtrack-unread-count', count: $service->unreadCount($user));
    }

    #[On('flowtrack-notification')]
    public function refreshNotifications(): void
    {
        // The next render pulls the new database notification. The Pusher event
        // only tells Livewire that fresh data is available.
    }

    #[On('flowtrack-refresh')]
    public function refreshWorkspace(): void
    {
        // Re-query so notifications attached to deleted records disappear at once.
    }

    public function render()
    {
        return view('livewire.notifications.index', [
            'notifications' => app(NotificationService::class)->paginate(auth()->user(), 30),
        ]);
    }
}
