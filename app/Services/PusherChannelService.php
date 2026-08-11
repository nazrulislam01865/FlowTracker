<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PusherChannelService
{
    public function enabled(): bool
    {
        return (bool) config('services.pusher.enabled', true)
            && filled(config('services.pusher.app_id'))
            && filled(config('services.pusher.key'))
            && filled(config('services.pusher.secret'));
    }

    public function userChannel(int $userId): string
    {
        return 'private-flowtrack.user.'.$userId;
    }

    public function workspaceChannel(int $workspaceId): string
    {
        return 'private-flowtrack.workspace.'.max(1, $workspaceId);
    }

    public function authenticate(string $socketId, string $channelName, int $userId): array
    {
        abort_unless($this->enabled(), 404);
        $workspaceId = max(1, (int) config('flowtrack.workspace_id', 1));
        abort_unless(in_array($channelName, [
            $this->userChannel($userId),
            $this->workspaceChannel($workspaceId),
        ], true), 403);
        abort_unless(preg_match('/^\d+\.\d+$/', $socketId) === 1, 422, 'Invalid socket ID.');

        $signature = hash_hmac('sha256', $socketId.':'.$channelName, (string) config('services.pusher.secret'));

        return ['auth' => config('services.pusher.key').':'.$signature];
    }

    public function triggerUser(int $userId, string $event, array $payload): void
    {
        $this->triggerChannels([$this->userChannel($userId)], $event, $payload);
    }

    public function triggerWorkspace(int $workspaceId, string $event, array $payload): void
    {
        $this->triggerChannels([$this->workspaceChannel($workspaceId)], $event, $payload);
    }

    private function triggerChannels(array $channels, string $event, array $payload): void
    {
        if (!$this->enabled() || Cache::get($this->circuitKey())) return;

        $channels = collect($channels)->map(fn ($channel) => trim((string) $channel))->filter()->unique()->values()->all();
        if ($channels === []) return;

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
            'channels' => $channels,
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
            $seconds = max(60, (int) config('services.pusher.circuit_seconds', 300));
            Cache::put($this->circuitKey(), true, now()->addSeconds($seconds));
            throw new RuntimeException('Pusher rejected the event with HTTP '.$response->status().'. Realtime delivery is temporarily disabled.');
        }

        Cache::forget($this->circuitKey());
    }

    private function circuitKey(): string
    {
        $fingerprint = implode('|', [
            (string) config('services.pusher.app_id'),
            (string) config('services.pusher.key'),
            (string) config('services.pusher.cluster'),
            (string) config('services.pusher.host'),
        ]);

        return 'flowtrack:pusher:circuit-open:'.sha1($fingerprint);
    }
}
