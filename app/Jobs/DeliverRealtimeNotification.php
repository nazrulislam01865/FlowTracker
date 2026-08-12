<?php

namespace App\Jobs;

use App\Services\ReverbChannelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverRealtimeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 90];
    public int $timeout = 10;

    public function __construct(
        public readonly int $userId,
        public readonly string $event,
        public readonly array $payload,
    ) {
        $this->onQueue((string) config('services.realtime.queue', 'realtime'));
    }

    public function handle(ReverbChannelService $reverb): void
    {
        $reverb->triggerUser($this->userId, $this->event, $this->payload);
    }
}
