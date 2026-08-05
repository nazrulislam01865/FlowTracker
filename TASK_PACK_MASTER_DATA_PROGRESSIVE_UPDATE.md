# Task Pack and Master Data progressive rendering

## What changed

- Master Data renders its navigation and grouped counts first, then loads records only for the selected master type.
- The grouped counters use one aggregate query instead of one query per sidebar item.
- Product-category parent options load only while the Product editor is open.
- The Task Pack list renders its page shell first and no longer queries users or master-data form options.
- The Task Pack form renders basic pack/task fields first, then loads assignees and the department, priority, and document-category types independently through `MasterDataService`.
- Workflow phase options now load only when the phase modal is open, and document categories use the same shared master-data service.

## Project-wide contract

Shared master-data consumers should call `MasterDataService::active($type)`. Its cache key contains the workspace and type, so each master type is loaded and invalidated separately. Operational validation and save queries may still access `master_records` directly; page rendering should use the service.

No database migration is required for this update.
