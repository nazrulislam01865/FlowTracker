<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PusherChannelService
{
    public function enabled(): bool
    {
        // Temporarily disabled. Keep the integration code in place so it can be
        // re-enabled later without affecting database-backed notifications.
        return false;
    }

    public function userChannel(int $userId): string
    {
        return 'private-flowtrack.user.'.$userId;
    }

    public function authenticate(string $socketId, string $channelName, int $userId): array
    {
        abort_unless($this->enabled(), 404);
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
        $cluster = (string) config('services.pusher.cluster', 'ap1');
        $scheme = (string) config('services.pusher.scheme', 'https');
        $host = (string) (config('services.pusher.host') ?: 'api-'.$cluster.'.pusher.com');
        $port = (int) (config('services.pusher.port') ?: ($scheme === 'https' ? 443 : 80));
        $path = '/apps/'.$appId.'/events';

        $body = json_encode([
            'name' => $event,
            'channels' => [$this->userChannel($userId)],
            'data' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($body === false) throw new RuntimeException('Unable to encode Pusher payload.');

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

        $response = Http::connectTimeout((float) config('services.pusher.connect_timeout', 1))
            ->timeout((float) config('services.pusher.timeout', 3))
            ->withBody($body, 'application/json')
            ->post($url);

        if ($response->failed()) {
            $seconds = in_array($response->status(), [401, 403], true)
                ? max(3600, (int) config('services.pusher.circuit_seconds', 300))
                : max(60, (int) config('services.pusher.circuit_seconds', 300));
            Cache::put($this->circuitKey(), true, now()->addSeconds($seconds));
            throw new RuntimeException('Pusher rejected the event with HTTP '.$response->status().'. Realtime delivery is temporarily disabled.');
        }

        Cache::forget($this->circuitKey());
    }

    private function circuitKey(): string
    {
        return 'flowtrack:pusher:circuit-open';
    }
}
