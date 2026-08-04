<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PusherChannelService
{
    public function enabled(): bool
    {
        return filled(config('services.pusher.app_id'))
            && filled(config('services.pusher.key'))
            && filled(config('services.pusher.secret'));
    }

    public function userChannel(int $userId): string
    {
        return 'private-flowtrack.user.'.$userId;
    }

    public function authenticate(string $socketId, string $channelName, int $userId): array
    {
        abort_unless($this->enabled(), 503, 'Realtime notifications are not configured.');
        abort_unless($channelName === $this->userChannel($userId), 403);
        abort_unless(preg_match('/^\d+\.\d+$/', $socketId) === 1, 422, 'Invalid socket ID.');

        $signature = hash_hmac('sha256', $socketId.':'.$channelName, (string) config('services.pusher.secret'));

        return ['auth' => config('services.pusher.key').':'.$signature];
    }

    public function triggerUser(int $userId, string $event, array $payload): void
    {
        if (!$this->enabled()) return;

        $appId = (string) config('services.pusher.app_id');
        $key = (string) config('services.pusher.key');
        $secret = (string) config('services.pusher.secret');
        $cluster = (string) config('services.pusher.cluster', 'mt1');
        $scheme = (string) config('services.pusher.scheme', 'https');
        $host = (string) (config('services.pusher.host') ?: 'api-'.$cluster.'.pusher.com');
        $port = (int) (config('services.pusher.port') ?: ($scheme === 'https' ? 443 : 80));
        $path = '/apps/'.$appId.'/events';

        $body = json_encode([
            'name' => $event,
            'channels' => [$this->userChannel($userId)],
            'data' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($body === false) return;

        $params = [
            'auth_key' => $key,
            'auth_timestamp' => time(),
            'auth_version' => '1.0',
            'body_md5' => md5($body),
        ];
        ksort($params);
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $params['auth_signature'] = hash_hmac('sha256', "POST\n{$path}\n{$query}", $secret);
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $url = $scheme.'://'.$host.(in_array($port, [80, 443], true) ? '' : ':'.$port).$path.'?'.$query;

        try {
            $response = Http::connectTimeout(0.5)
                ->timeout(1.2)
                ->withBody($body, 'application/json')
                ->post($url);

            if ($response->failed()) {
                Log::warning('FlowTrack Pusher notification rejected', [
                    'user_id' => $userId,
                    'event' => $event,
                    'status' => $response->status(),
                ]);
            }
        } catch (Throwable $e) {
            // A Pusher outage must never block a Job/Task update. The database
            // notification remains available and the realtime delivery is best effort.
            Log::warning('FlowTrack Pusher notification failed', [
                'user_id' => $userId,
                'event' => $event,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
