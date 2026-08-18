<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Services\DashboardService;
use Livewire\Attributes\On;
use Livewire\Component;

class TaggedComments extends Component
{
    use RefreshesFromWorkspace;
    public string $filter = 'all';

    public function placeholder(): string
    {
        return <<<'HTML'
            <section class="ft-panel">
                <div class="ft-panel-head">
                    <h2 class="ft-panel-title">Mentions for you</h2>
                </div>
                <div style="padding:24px">Loading comments...</div>
            </section>
        HTML;
    }

    public function setFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['all', 'unread', 'orders', 'inquiries'], true), 422);
        $this->filter = $filter;
    }

    public function markAllRead(): void
    {
        app(DashboardService::class)->markAllMentionsRead(auth()->user());
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // A normal Livewire re-render is enough. NotificationService clears the
        // recipient's dashboard caches before the browser receives this event.
    }

    public function render()
    {
        $service = app(DashboardService::class);
        // Apply the selected category in SQL before LIMIT. Filtering an already
        // limited collection can incorrectly hide older matching mentions.
        $mentions = $service->mentions(auth()->user(), $this->filter, 4);

        return view('livewire.dashboard.tagged-comments', [
            'mentions' => $mentions,
            'unreadMentionCount' => $service->unreadMentionCount(auth()->user()),
        ]);
    }
}
