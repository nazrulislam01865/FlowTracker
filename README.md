# FlowTrack Livewire Redesign

This package keeps the previous database schema/migrations and redesigns the Jobs list, Task Details, and Job Details screens to match the supplied references.

Updated areas:
- Jobs list page with reference-style filter bar, quick chips, owner/date/progress/invoice cells, and pagination.
- Task Details page with top action bar, property grid, checklist, attachment upload area, activity panel, management attention, job context, and dependencies.
- Job Details overview with header/tabs, metric cards, overview/product table, workflow strip, phase task table, planning panel, and activity.
- Job Details workflow section with blocker banner, stepper, transition readiness, phase controls, workflow details, and phase history.
- Job Details documents section with upload area, grouped document table, required document sidebar, and document health panel.

Database schema note:
- A performance-only migration adds indexes for high-frequency Job, Task, notification, activity, document, Master Data, and workflow queries.
- It does not change application records or remove existing columns.

Run after replacing files:

```bash
php artisan optimize:clear
npm install
npm run build
php artisan serve
```

If you already ran `npm install`, only run:

```bash
php artisan optimize:clear
npm run build
```

## Production performance deployment

The cloud performance implementation, Redis/Pusher configuration, queue worker setup, monitoring, and query-plan verification steps are documented in [`PERFORMANCE_IMPLEMENTATION.md`](PERFORMANCE_IMPLEMENTATION.md).

For production deployment, begin with `.env.production.example` and run:

```bash
./scripts/deploy.sh
./scripts/queue-worker.sh
```

After upgrading from a legacy FlowTrack schema, run the compatibility sync once:

```bash
php artisan flowtrack:sync-legacy
```

Legacy synchronization no longer runs during normal page requests. China-specific deployment notes and diagnostics are in [`CHINA_PERFORMANCE_UPDATE.md`](CHINA_PERFORMANCE_UPDATE.md).
