# Dashboard, Reports, Logout and Progressive Rendering Update

## Implemented

- Removed the Dashboard Outstanding card and its client-balance query.
- Dashboard cards now use live Job, Task and workflow-phase aggregates.
- Dashboard overdue and approval counts include only Tasks belonging to active Jobs.
- Removed Reports receivable KPIs, Receivable Ageing and all hard-coded KPI values.
- Reports now calculate active Jobs, on-time Jobs, Task completion, overdue Tasks, average Artwork phase duration and on-time Shipment phases from database records.
- Report and Dashboard scalar metrics use short cache windows and realtime invalidation.
- Added a POST logout action to the sidebar.
- Primary authenticated page components render immediately so useful content is present in the first response.
- Removed the fixed side loader card.
- Dashboard metrics and Needs Attention render first.
- One grouped Livewire request loads Jobs by Phase, Team Workload, Upcoming Deliveries and Recent Activity.
- Added fixed-size Dashboard skeleton cards to preserve the final grid while secondary data loads.
- Added a short per-user cache for every Dashboard dataset with complete invalidation.
- Reports KPIs and Delivery Performance render first; phase and workload charts load in one grouped secondary request.

## After replacing the project

```bash
php artisan optimize:clear
php artisan cache:clear
npm run build
```

Optional regression test:

```bash
php artisan test --filter=DashboardReportsLazyLoadingTest
```
