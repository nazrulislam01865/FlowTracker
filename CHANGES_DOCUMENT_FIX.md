# Document 500 fix and Documents-page performance update

## Root causes fixed

1. The previous open route passed an unsafe manually assembled `Content-Disposition` header to the storage response. Non-ASCII/Chinese names and special characters could produce an invalid header and an HTTP 500.
2. The route assumed the configured disk and stored path always matched. Existing records could point to a legacy public path, an absolute path, or a file stored on a different configured disk.
3. Storage exceptions were not converted to a controlled response, so a missing/unreadable object produced an unhandled server error.
4. Render's normal filesystem is not a durable upload location. A record could remain in PostgreSQL while the underlying file disappeared after a deploy/restart.
5. The Documents page eagerly loaded all tasks for each visible Job, all lookup catalogs, four metric queries, selected-document details, and version history during the first request.

## Updated behavior

- `DocumentFileController` authorizes open/download requests before file delivery.
- `DocumentFileService` resolves current and legacy path formats across the primary and fallback disks.
- Unicode-safe RFC-compatible file headers are generated with Symfony `HeaderUtils`.
- Missing files return HTTP 404 with a clear log entry instead of HTTP 500.
- Local files support range delivery; Render/Nginx uses private `X-Accel-Redirect` delivery.
- New uploads use the private `documents` disk and verify the object exists before creating the database row.
- Render stores uploads at `/var/data/documents` on a persistent disk.
- The list starts at 10 documents per page.
- First paint contains only the paginated document query; metrics and lookup data load through `wire:init`.
- Job rows use a scoped task count rather than eagerly loading every task.
- Upload tasks are queried only while the upload modal is open and a Job is selected.
- Details and up to 20 versions load only after the user selects a document.
- The Pusher browser client no longer blocks the initial page load.
