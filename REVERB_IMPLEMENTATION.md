# FlowTrack Laravel Reverb implementation

This build replaces Pusher Cloud with a self-hosted Laravel Reverb transport while preserving FlowTrack's existing database notifications, notification center, unread badges, workspace refresh events, Livewire refresh events, queue isolation, and polling fallback.

## What changed

- `laravel/reverb` is added to Composer requirements.
- FlowTrack now ships a small native WebSocket client in `public/js/flowtrack-reverb-client.js` that speaks the Reverb/Pusher protocol. No realtime JavaScript is downloaded from an external CDN and no traffic is sent to Pusher Cloud.
- `ReverbChannelService` replaces `PusherChannelService` and signs events for the local/self-hosted Reverb HTTP API.
- Private FlowTrack channels remain:
  - `private-flowtrack.user.{id}`
  - `private-flowtrack.workspace.{workspaceId}`
- The authorization endpoint is now `/realtime/auth`.
- The existing `realtime` database queue remains in place for phase 1.
- The existing HTTP polling fallback remains active whenever the WebSocket is disconnected.

## Important dependency note for this archive

The source archive did not contain Composer itself, so the included `composer.lock` could not be regenerated in the build environment. `composer.json` already requires Reverb, and `scripts/deploy.sh` safely performs a one-time targeted `composer update laravel/reverb --with-all-dependencies` when the lock file does not yet contain Reverb. After that first run, Composer returns to normal deterministic `composer install` behavior.

For development, run the targeted update once and keep the resulting `composer.lock` in your normal source-control/deployment workflow.

## Local development

Merge `.env.reverb.local.example` into the existing local `.env`, then run once:

```bash
composer update laravel/reverb --with-all-dependencies
npm install
php artisan optimize:clear
npm run build
```

For normal local development, use four terminals:

```bash
php artisan serve
```

```bash
./scripts/reverb-local.sh
```

```bash
./scripts/queue-worker.sh
```

```bash
npm run dev
```

If you prefer compiled assets instead of the Vite dev server, run `npm run build` and omit `npm run dev`.

The default local addresses are:

- FlowTrack: `http://127.0.0.1:8000`
- Reverb: `ws://127.0.0.1:8080`

The queue worker is required because FlowTrack intentionally dispatches realtime delivery to the `realtime` queue so notification delivery cannot slow down the user's normal request.

## Production / Alibaba Cloud

1. Generate production credentials. Use different values for key and secret:

```bash
php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

2. Merge `.env.reverb.production.example` into the production `.env` and replace:

```env
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=your-real-flowtrack-domain.com
REVERB_ALLOWED_ORIGINS=your-real-flowtrack-domain.com
```

3. Add the contents of `deploy/nginx-reverb-snippet.conf.example` inside the existing HTTPS FlowTrack `server { ... }` block. This proxies browser WebSocket/API paths `/app` and `/apps` to Reverb on `127.0.0.1:8080`; the rest of the application still goes to Laravel/PHP-FPM. FlowTrack queue jobs publish directly to `REVERB_API_HOST=127.0.0.1`, so server-side realtime delivery does not need to leave the ECS instance.

4. Install the Supervisor program:

```bash
sudo cp deploy/flowtrack-reverb.conf.example /etc/supervisor/conf.d/flowtrack-reverb.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start flowtrack-reverb
```

5. Deploy normally:

```bash
./scripts/deploy.sh
```

6. Verify:

```bash
sudo supervisorctl status flowtrack-reverb
sudo supervisorctl status flowtrack-worker:*
```

The supplied Supervisor configuration binds Reverb to `127.0.0.1:8080`, so port `8080` is not public. Only normal HTTPS/WSS port `443` needs to be reachable by users. If you later place Reverb behind a separate load balancer or move it to another server, change the listener address deliberately at that time.

## Browser verification

After logging in, open DevTools Console and run:

```javascript
window.FlowTrackRealtime?.connection?.state
```

Expected value:

```text
connected
```

Also inspect the Network tab and filter for `WS`. The connection URL should point to your own FlowTrack hostname (or `127.0.0.1:8080` locally), never to a `pusher.com` hostname.

## Failure behavior

If Reverb is stopped or temporarily unavailable:

- the normal FlowTrack database transaction still succeeds;
- the notification remains stored in MySQL;
- the realtime queue job retries;
- the browser automatically falls back to the existing polling path;
- no realtime popup is introduced; FlowTrack keeps its current notification-center behavior.

## Phase 2 (not enabled in this build)

Do not add Redis/Tair and Horizon at the same time as this transport migration. After Reverb is stable, move the queues to Alibaba Tair/Redis and add Horizon in a separate deployment. `REVERB_SCALING_ENABLED` remains `false` because 50-100 users do not need multiple Reverb servers.
