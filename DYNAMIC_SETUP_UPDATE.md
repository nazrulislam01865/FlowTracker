# Dynamic Setup Update

This build makes Master Data, Task Pack Setup and Workflow Setup database-driven and aligns those setup areas with the supplied SQL structures.

## Main SQL structures used
- `workspaces`
- `master_records`
- `task_packs`
- `task_pack_items`
- `workflow_templates`
- `workflow_phases`

A compatibility migration preserves the current application's existing operational tables while synchronizing setup data into the SQL-aligned structures.

## Required after deployment
```bash
php artisan migrate
php artisan optimize:clear
```

Do not run `migrate:fresh` on an existing database.
