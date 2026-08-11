<?php

namespace App\Jobs;

use App\Services\PusherChannelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverRealtimeWorkspaceEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 90];
    public int $timeout = 10;

    public function __construct(
        public readonly int $workspaceId,
        public readonly string $event,
        public readonly array $payload,
    ) {
        $this->onQueue((string) config('services.pusher.queue', 'realtime'));
    }

    public function handle(PusherChannelService $pusher): void
    {
        $pusher->triggerWorkspace($this->workspaceId, $this->event, $this->payload);
    }
}
