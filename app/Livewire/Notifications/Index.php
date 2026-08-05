<?php

namespace App\Livewire\Notifications;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\FlowNotification;
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
        FlowNotification::where('user_id', auth()->id())->whereKey($id)->update(['read_at' => now()]);
        app(\App\Services\ShellDataService::class)->forget((int) auth()->id());
        $this->dispatch('flowtrack-unread-count', count: app(NotificationService::class)->unreadCount(auth()->user()));
    }

    #[On('flowtrack-notification')]
    public function refreshNotifications(): void
    {
        // The next render pulls the new database notification. The Pusher event
        // only tells Livewire that fresh data is available.
    }

    public function render()
    {
        return view('livewire.notifications.index', [
            'notifications' => app(NotificationService::class)->paginate(auth()->user(), 30),
        ]);
    }
}
