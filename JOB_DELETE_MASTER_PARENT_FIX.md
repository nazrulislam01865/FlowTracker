# Job deletion and Master Data parent fix

Updated on 2026-08-05.

## Jobs

- Added a real **Select all** checkbox to the Jobs table.
- Select all now includes every Job matching the current filters, including Jobs on later pagination pages.
- Bulk delete, deactivate and cancel use the complete selected ID set and return to the first page afterward.
- Centralized the Jobs filter query so pagination and select-all cannot disagree.
- Active Job totals now exclude completed, inactive, cancelled and soft-deleted Jobs consistently across Jobs, Dashboard and Client summaries.
- Job deletion explicitly invalidates the acting user's cached Dashboard metrics.

## Master Data

- Removed the Parent column and Parent field from every Master Data section except **Products**.
- Renamed the Product relationship to **Product Category**.
- Product parent values are validated to ensure they reference a Product Category in the same workspace.
- Non-product records are always saved with `parent_id = null`.
- Added a migration that clears invalid historical parent links and restores legacy Product → Product Category links where possible.
- Legacy `master_values` mirroring now preserves Product category relationships.

## Deployment

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan view:clear
npm run build
php artisan test --filter='JobSelectionAndCountsTest|MasterDataParentScopeTest'
```
