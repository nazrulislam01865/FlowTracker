# Task details / documents / activity update

This build adds:

- Pencil-to-edit task assignee, status, priority, start date, due date and description.
- Task `description` and `start_date` compatibility columns (migration `2026_08_04_000500_add_task_detail_fields.php`).
- Dynamic checklist add/delete and assigned-user checklist completion.
- Task attention propagation to the parent Job and dashboard/board/list attention state.
- Task attachments uploaded to the configured FlowTrack document disk and linked to the Task/Job document system.
- Working "Choose from Documents" for task attachments and Task Pack requirements.
- Task and Job activity/history with actor, action and timestamp; comments are persisted and included in history.
- Job Documents required-document UI sourced strictly from Task Pack task requirements, including direct Upload controls.
- Task Pack required documents block task completion until a document has been uploaded or linked.

Deployment:

```bash
php artisan migrate
php artisan optimize:clear
npm run build
```

If production uses S3 or another cloud disk, configure `FLOWTRACK_DOCUMENT_DISK` to that Laravel filesystem disk.
