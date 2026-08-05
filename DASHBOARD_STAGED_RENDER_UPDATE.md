# Dashboard staged rendering update

The Dashboard now loads in two controlled stages without changing its final design.

## What loads first

- Management Dashboard heading
- Five top metrics
- Needs Attention list

These are the most useful above-the-fold sections and are rendered in the initial response.

## What loads next

One grouped Livewire request loads all four secondary datasets:

- Jobs by Phase
- Team Workload
- Upcoming Deliveries
- Recent Activity

Fixed-size skeleton cards reserve the final layout while that request is running, so cards do not jump or collapse.

## Query and cache behavior

Each Dashboard section has its own short per-user cache. The default lifetime is 30 seconds and can be changed with:

```dotenv
DASHBOARD_CACHE_SECONDS=30
```

Dashboard invalidation clears every section cache for the affected user. The existing `DashboardService::data()` method remains available for tests and non-Livewire callers.

Dashboard cache keys are versioned. Cached values are also checked before they reach Blade; incompatible values left by an older deployment are discarded and rebuilt automatically. This prevents malformed cache data from causing a Dashboard 500 response.

## Deployment

```bash
npm ci
npm run build
php artisan optimize:clear
php artisan optimize
```

The production CSS assets in `public/build` are already rebuilt in this archive.
