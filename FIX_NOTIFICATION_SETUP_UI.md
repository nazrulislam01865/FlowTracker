# Notification, setup feedback, client action and task edit-icon fix

- Client list keeps one action menu only; the selected-client summary no longer opens a duplicate kebab menu.
- Task title pencil is explicitly sized to match the other edit controls.
- Task/Job update notifications now include the acting user as well as assigned/participating users, subject to the Role Matrix.
- Pusher remains the primary realtime path. Database unread-count fallback now checks every 5 seconds, and the Notifications page polls every 5 seconds so missed websocket events are recovered automatically.
- Master Data, Task Pack and Workflow configuration changes create notifications for the administrator who made the change.
- Setup success feedback renders once: the setup page owns its success banner instead of duplicating the layout banner.
- Existing Task Pack legacy-sequence collision protection is preserved.

No database migration is required for this update.
