# Notification, Jobs bulk actions, and Task Pack save fixes

- Global topbar search and global New Job action removed; page-level actions remain.
- Realtime notification badges now use the exact unread count from Pusher payloads and have a lightweight unread-count fallback sync.
- Assignment notifications can be delivered to the assigned user even when they assigned the record to themselves; other self-generated update noise remains suppressed.
- Jobs page no longer shows the All active chip, More filters, or the active-filter chip row.
- Priority and Job Status filters are now direct filters in the main responsive filter bar.
- Jobs support multi-select bulk Deactivate, Cancel, and permission-controlled Delete actions.
- Inactive/Cancelled Jobs are removed from normal active Job/Board/My Work operational views, but remain queryable using the Job Status filter.
- Task Pack edit validation now preserves Task Pack item IDs. Legacy task_pack_tasks mirroring is collision-safe, fixing duplicate (task_pack_id, sequence) errors on save/reorder.
