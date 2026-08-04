# Pagination / setup delete / Job document UI fix

Updated on 2026-08-04.

- Job and Task activity feeds now paginate at 30 records and only show pagination when more than 30 records exist.
- Notifications paginate at 30 records.
- Master Data records paginate at 30 records per selected category.
- Task Pack cards now expose a guarded Delete action using the existing TaskPackService rules.
- Workflow Setup now exposes a guarded Delete Workflow action using the existing WorkflowService rules.
- Job Details > Documents uses the same compact attachment upload layout as Task Details, with the required-document selector remaining full width above it.
- Removed the Job Details Workflow "View workflow setup" shortcut.
