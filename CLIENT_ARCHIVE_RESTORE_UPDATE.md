# Client archive and restore update

- The client action now consistently archives instead of attempting a hard delete.
- Archived Clients has a dedicated list and restore action on the Clients page.
- The archived view uses a compact history table with direct View history and Restore actions; operational health/task columns and the clipped action popover are not shown there.
- Active client lists, Job Board, Task Board, My Work, Dashboard, Reports, and shell metrics exclude archived clients.
- Client-dependent caches use a lifecycle version so archive and restore changes appear immediately.
- A secondary notification failure is logged but no longer turns a successful archive or restore into a 500 response.
- Existing client, Job, task, document, and activity history remains in the database and is available through the archived client record.

No database migration is required.
