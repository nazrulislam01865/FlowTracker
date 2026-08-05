# Dashboard, Reports, Logout and Lazy Loading Update

## Implemented

- Removed the Dashboard Outstanding card and its client-balance query.
- Dashboard cards now use live Job, Task and workflow-phase aggregates.
- Dashboard overdue and approval counts include only Tasks belonging to active Jobs.
- Removed Reports receivable KPIs, Receivable Ageing and all hard-coded KPI values.
- Reports now calculate active Jobs, on-time Jobs, Task completion, overdue Tasks, average Artwork phase duration and on-time Shipment phases from database records.
- Report and Dashboard scalar metrics use short cache windows and realtime invalidation.
- Added a POST logout action to the sidebar.
- Enabled Livewire lazy loading on every authenticated page.
- Added one shared responsive skeleton placeholder for all page components.

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
