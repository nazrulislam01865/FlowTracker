# FlowTrack deployment and rollback

## Build from a clean revision

```bash
composer install --no-dev --classmap-authoritative --no-interaction --prefer-dist
npm ci
npm run build
npm run quality:bundle
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan queue:restart
```

Run `php artisan flowtrack:infrastructure:check --prepare-storage` before adding a horizontally scaled node to the load balancer. `/health/ready` is the traffic-readiness endpoint; `/up` is liveness only.

Production horizontal mode uses Redis cache/session/queues, independently supervised queue workers/Reverb/scheduler, and shared/object storage. See `docs/infrastructure-scalability.md`.

## Before schema/deployment changes

Create and verify a database backup with `php artisan flowtrack:database:backup`. Restore drills use `php artisan flowtrack:database:restore ... --force`; see `docs/backup-restore.md`.

## Rollback

Application rollback deploys the previous tested revision and rebuilds its dependencies/assets from that revision's lockfiles. Phase 14 infrastructure changes are configuration-level and retain the single-node rollback profile. Never roll back a database migration by editing migration history; use the documented forward/rollback procedure and restore only when explicitly required.
