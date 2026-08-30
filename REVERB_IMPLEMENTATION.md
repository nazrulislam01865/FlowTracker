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
- Single-node compatibility may still use the database queue; the Phase 14 horizontal profile moves realtime delivery to the shared Redis queue.
- The existing HTTP polling fallback remains active whenever the WebSocket is disconnected.

## Systemwide realtime architecture

FlowTrack uses two reusable realtime paths rather than adding socket code inside every Create/Edit/Delete method:

1. **Private user channel** — `private-flowtrack.user.{id}`
   - `flowtrack.notification` for assignments, mentions, attention alerts and other user-facing notifications.
   - `flowtrack.notification-state` for read/unread synchronization across the user's open tabs.
   - Database notifications remain the source of truth; Reverb only delivers the immediate signal.

2. **Private workspace channel** — `private-flowtrack.workspace.{workspaceId}`
   - `flowtrack.refresh` is a lightweight invalidation event. It never pushes record data or bypasses permissions.
   - `WorkspaceDataObserver::observedModels()` centrally covers FlowTrack's operational parent and child models: Orders, Tasks, comments, checklists, documents, finance records, Inquiries, Inquiry tasks/comments/documents/links, Clients, master data, workflow/task-pack setup, users/roles and related records.
   - `RefreshesFromWorkspace` is the shared Livewire concern used by the application's Livewire screens. Receiving the event causes the existing authorized query/render path to fetch fresh state. Components with cached metrics can implement `prepareForWorkspaceRefresh()` before the render.
   - Set-based SQL operations that intentionally bypass Eloquent observers publish one explicit `WorkspaceRefreshService::touch(...)` after the mutation.

`WorkspaceRefreshService` is transaction-aware and coalesces bursts of writes. A transaction emits only after commit, and closely spaced commits share one delayed Reverb invalidation. The workspace data-version cache is updated even if Reverb is disabled, so the polling fallback still detects changes.

This means Order create/edit/delete, Task create/edit/status/assignee/delete/comment/document/checklist/link changes, Inquiry create/edit/delete/task/assignee/comment/document/link changes, Client changes, finance changes, Documents, Master Data and setup changes all use the same reusable refresh mechanism. Mention/assignment notifications continue to use the recipient-only channel.

## Dependency note

`composer.json` and `composer.lock` both contain Laravel Reverb. Normal deployments therefore use deterministic `composer install`. `scripts/deploy.sh` still contains a safe compatibility check for an older server checkout whose lock file predates Reverb; only in that situation does it perform the one-time targeted Reverb update.

## Local development

Merge `.env.reverb.local.example` into the existing local `.env`, then install the locked dependencies/build assets:

```bash
composer install
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

## Phase 14 horizontal scaling

Redis/Tair/Valkey is now an explicit horizontal-production option. Set `FLOWTRACK_HORIZONTAL_SCALING=true`, use `deploy/env.horizontal.example`, and run `php artisan flowtrack:infrastructure:check --prepare-storage` before adding a node to the load balancer. `REVERB_SCALING_ENABLED` defaults on in that profile so multiple Reverb processes coordinate through Redis; single-node deployments remain compatible with scaling disabled.
