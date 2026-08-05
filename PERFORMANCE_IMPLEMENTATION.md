# FlowTrack Performance Implementation

This project contains the first production performance pass requested for the cloud deployment.

## Implemented changes

### Realtime and polling

- Pusher is disabled unless `PUSHER_ENABLED=true` and every credential is present.
- Failed Pusher authentication/delivery opens a cache-backed circuit breaker so repeated 401/403/time-out calls do not block application work.
- Database notification creation and recipient fan-out run through queued jobs.
- Realtime delivery uses the dedicated `realtime` queue; notification fan-out uses `notifications`.
- Global notification fallback polling changed from 5 seconds to 120 seconds and only runs while Pusher is unavailable.
- Session ownership checking changed from 10 seconds to 60 seconds, plus initial/focus/visibility checks.
- Livewire page polling was removed from Dashboard, Jobs, Board, My Work, and Notifications.

### Redis production configuration

`.env.production.example` separates Redis usage:

- database `0`: queues/default Redis work
- database `1`: application cache
- database `2`: sessions

Production values use:

```dotenv
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_STORE=redis-session
SESSION_CONNECTION=session
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
```

The cloud platform must provide Redis and the PHP `redis` extension.

### Board and My Work

- Board queries are mode-specific. Job mode does not query Task cards; Task mode does not query Job cards or workflow phases.
- Job and Task cards use explicit columns and compact relationships.
- Task cards use relationship counts instead of loading all checklist items, comments, and documents.
- Job and Task counter chips use one conditional aggregate query per mode.
- Stable Board selectors are cached briefly.
- Initial card loading is limited to 60 with an explicit **Load 60 more** action, up to 300, instead of silently loading a large graph.

### Jobs list and detail drawer

- The Jobs list and Job/Task detail states use mutually exclusive data-loading branches.
- The list query selects only table fields, compact open Task data, and product counts.
- Full Job relationships are loaded only after a Job is opened.
- Task details load only the compact Job context they use.
- The document picker query runs only while the picker is open.
- Opening a Job no longer synchronizes workflow Tasks or recalculates progress as a read-side effect.
- Job summary cards are calculated with one conditional aggregate query.

### Application shell

- Sidebar and top-bar counters are supplied once by `ShellDataService`.
- Layout Blade files no longer issue duplicate unread-notification or My Work queries.
- Shell values are short-lived cached values and notification actions invalidate them.

### Monitoring

`MonitorPerformance` and `RequestPerformanceMonitor` now capture:

- total request duration and response status
- SQL query count and cumulative SQL time
- individual slow SQL queries
- cache hits, misses, writes, forgets, and failovers
- outgoing Laravel HTTP client duration, response status, and failure
- peak request memory
- failed requests
- optional `Server-Timing` response headers

Slow entries are written to the normal Laravel log under:

- `FlowTrack request performance`
- `FlowTrack failed request performance`
- `FlowTrack outgoing request performance`

Configure thresholds through the `PERFORMANCE_*` environment variables. The production example samples 25% of requests by default to limit monitoring overhead; raise it temporarily when diagnosing a problem.

### Database indexes

The migration `2026_08_05_000200_add_flowtrack_performance_indexes.php` adds indexes for the audited high-frequency predicates on:

- active Jobs, delivery dates, owners, clients, workflows, and phases
- active Tasks, assignees, due dates, statuses, Jobs, phases, and Task Pack templates
- reverse Job membership lookup
- unread notifications
- Task document requirements
- polymorphic activity timelines
- active/sorted Master Data
- active workflow-phase ordering

Run production query-plan verification after migration:

```bash
php artisan flowtrack:performance:explain --user=1
```

Use a real active user ID and confirm that `key` / query-plan output references the new `ft_*` indexes. Production data distribution determines whether the database optimizer chooses a particular index.

## Deployment

1. Copy `.env.production.example` to the cloud secret/environment configuration and insert real values.
2. Keep `PUSHER_ENABLED=false` until valid Pusher credentials have been tested.
3. Start Redis before the application and queue workers.
4. Run:

```bash
./scripts/deploy.sh
```

This performs optimized Composer installation, a reproducible `npm ci` build when a lock file exists (or a no-audit install fallback for this archive), cache clearing, migrations, `php artisan optimize`, and queue restart.

Run persistent workers using the included Supervisor example or:

```bash
./scripts/queue-worker.sh
```

The worker prioritizes `realtime`, then `notifications`, then `default`. Without a running worker, queued notifications and realtime delivery will remain pending.

## Recommended production process layout

- Web process: PHP-FPM or the cloud platform's PHP web service
- Redis: managed Redis in the same region/private network
- Worker process: at least one queue worker; use two when notification volume increases
- Database: in the same region as the application
- Scheduler: `php artisan schedule:run` every minute when scheduled features are used

## Verification commands

```bash
php artisan migrate:status
php artisan config:show cache
php artisan config:show session
php artisan config:show queue
php artisan flowtrack:performance:explain --user=1
php artisan queue:monitor realtime,notifications,default --max=100
php artisan test
```
