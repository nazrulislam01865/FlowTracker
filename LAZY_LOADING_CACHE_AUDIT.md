# Lazy loading and cache audit

## New first-load lazy behavior

The Dashboard Livewire component is now mounted with `lazy`, so the authenticated application shell (sidebar/topbar) and a YouTube-style skeleton are returned first. The Dashboard shell loads after the first paint, then Livewire islands request metrics and visible sections independently; below-the-fold sections wait until they enter the viewport. The placeholder contains inline metric, chart, list, and table skeletons. The previous fixed “Preparing your workspace” side card has been removed.

Files:
- `resources/views/pages/dashboard.blade.php`
- `app/Livewire/Dashboard/Index.php`
- `resources/views/livewire/dashboard/placeholder.blade.php`
- `resources/css/flowtrack.css`

## Cached data

1. `MasterDataService::active()` caches active master-data lookup rows per workspace/type for 5 minutes. It is invalidated on save, status toggle, and delete.
2. `MasterDataService::syncLegacy()` uses a 5-minute cache guard so legacy master synchronization is not repeated on every lookup.
3. `DashboardService` caches scalar Dashboard metrics per user for 20 seconds. Notification creation invalidates that user's metric cache.
4. Single-login ownership uses cache key `flowtrack:active-login:{userId}` with a one-day TTL. This is session ownership/security state, not business-record caching.
5. Laravel `RateLimiter` uses the configured cache store for login-attempt throttling.
6. `AccessControlService` keeps role/module access rows in an in-memory array for the current PHP request only. It is not persistent cross-request caching.

## Intentionally not cached

These remain live database queries so stale operational data is not shown:
- Job lists and Job Details
- Task lists and Task Details
- My Work and Board cards/statuses
- Task/Job status, assignee, progress and due dates
- Documents and required-document completion
- Client detail/aggregates
- Notifications list and unread count
- Activity/history/comments
- Workflow and Task Pack editing data
- Roles & Access administration
- Reports

Board/My Work are loaded only when their pages are opened; they are not loaded during login/Dashboard first paint.

## Production recommendation

The app supports Laravel's configured cache store. For production use Redis (`CACHE_STORE=redis`) if Redis is available. The application logic does not require Redis; database cache remains supported.
