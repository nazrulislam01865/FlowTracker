<?php

namespace App\Livewire\Dashboard;

use App\Models\FlowNotification;
use App\Services\DashboardService;
use Livewire\Component;

class TaggedComments extends Component
{
    public string $filter = 'all';

    public function setFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['all', 'unread', 'job', 'task'], true), 422);
        $this->filter = $filter;
    }

    public function markAllRead(): void
    {
        FlowNotification::query()
            ->where('user_id', auth()->id())
            ->where('type', 'mention')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        app(DashboardService::class)->forgetMentions((int) auth()->id());
    }

    public function render()
    {
        $service = app(DashboardService::class);
        $mentions = $service->mentions(auth()->user())
            ->filter(function (FlowNotification $notification): bool {
                return match ($this->filter) {
                    'unread' => $notification->read_at === null,
                    'job' => $notification->flow_job_id !== null && $notification->flow_task_id === null,
                    'task' => $notification->flow_task_id !== null,
                    default => true,
                };
            })
            ->take(3)
            ->values();

        return view('livewire.dashboard.tagged-comments', [
            'mentions' => $mentions,
            'unreadMentionCount' => $service->unreadMentionCount(auth()->user()),
        ]);
    }
}
