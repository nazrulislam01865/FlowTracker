# China performance update

Implemented in this archive:

- All top-level Livewire components now render in the first HTTP response.
- Dashboard and Reports islands were consolidated into their parent render.
- Pusher remains disabled; the future default cluster is Singapore (`ap1`).
- Visible Job/Task authorization avoids per-row database scope checks.
- Board Job cards load only tasks from their current workflow phase.
- Legacy synchronization was removed from web requests and moved to `flowtrack:sync-legacy`.
- Workspace resolution is cached once per request.
- Client summary aggregates and indexed date filters were reduced.
- A new migration adds indexes for hot China request paths.
- Redis/MySQL production environment examples and static-asset cache examples were added.

## Deploy

```bash
# On an existing server, merge the database, Redis, queue and performance
# settings from .env.production.example into the current .env. Do not replace
# the existing APP_KEY.
./scripts/deploy.sh
php artisan flowtrack:sync-legacy
./scripts/queue-worker.sh
```

For a fresh deployment only, copy `.env.production.example` to `.env`, fill the
real values, and generate an application key before running the commands above.

Run `flowtrack:sync-legacy` once after deploying this version. It is no longer run during page loads.

## Temporary diagnostics

Temporarily set:

```dotenv
PERFORMANCE_LOG_ALL_REQUESTS=true
PERFORMANCE_SAMPLE_RATE=1
PERFORMANCE_INCLUDE_QUERY_SQL=true
PERFORMANCE_SERVER_TIMING=true
```

Then run `php artisan optimize:clear && php artisan optimize`, reproduce the slow request, inspect `storage/logs/laravel.log`, and restore the production defaults.

From a mainland-China connection run:

```bash
./scripts/china-diagnostic.sh https://your-domain.example/login
```
