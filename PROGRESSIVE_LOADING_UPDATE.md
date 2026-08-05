# Progressive Loading Update

## What changed

- Removed the fixed “Preparing workspace” loader card from the shared first-load placeholder and the Dashboard placeholder.
- Kept lightweight inline skeletons so the page layout remains stable while data arrives.
- Split Dashboard data into independent Livewire 4 islands:
  - metrics load just after the Dashboard shell;
  - attention, phase, workload, deliveries, and activity load only when their section enters the viewport.
- Split Reports data into independent islands for KPIs, phase totals, workload, and delivery performance.
- Refactored `DashboardService` and `ReportService` into section-level query methods. Their existing `data()` methods remain available for tests and non-Livewire callers.
- Preserved the existing app-wide page-level lazy loading, list pagination, and Board card limits.

## Result

The application shell appears first, then visible sections fill progressively. Below-the-fold Dashboard and Reports queries are not executed until the user scrolls near those sections, and the old fixed loader no longer covers the side of the page.

## Deployment

```bash
php artisan optimize:clear
npm install
npm run build
php artisan test --filter=DashboardReportsLazyLoadingTest
```
