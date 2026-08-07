# FlowTrack deployment on Render

The repository includes a production Docker image and a `render.yaml` Blueprint. The web service runs Nginx, PHP-FPM, and the Laravel queue worker in one container. Uploaded documents are stored on the Render persistent disk at `/var/data/documents`.

## Deploy

1. Push this project to GitHub or GitLab.
2. In Render, create a **Blueprint** from the repository. Render reads `render.yaml`.
3. Enter these prompted secret values:
   - `APP_KEY`: generate locally with `php artisan key:generate --show` and paste the complete `base64:...` value.
   - `APP_URL`: the final HTTPS URL, for example `https://flowtrack-app.onrender.com` or your custom domain.
4. Deploy. The container creates writable directories, runs database migrations, caches Laravel configuration/routes/views, and starts Nginx, PHP-FPM, and the queue worker.
5. Create the first administrator using your normal seeding/administration process. Do not put a real admin password in `render.yaml`.

## Existing uploaded files

The database stores relative document paths such as:

```text
flowtrack/documents/123/example.pdf
```

Copy existing files to the persistent disk while preserving that path below `/var/data/documents`:

```text
/var/data/documents/flowtrack/documents/123/example.pdf
```

The application also checks the legacy `public` disk because `FLOWTRACK_DOCUMENT_FALLBACK_DISKS=public`. This supports old records during migration, but files that must survive Render restarts should be copied to `/var/data/documents`.

## Why document delivery is private and fast

The browser still requests `/documents/{id}/open`. Laravel authorizes that document and resolves legacy/current paths. For the primary local disk it then sends an internal `X-Accel-Redirect`; Nginx transfers the bytes and supports range requests without exposing a public storage URL or making PHP buffer the file.

Do not make `/_protected_documents/` public. Its Nginx location is deliberately marked `internal`.

## China-facing performance choices

- The Blueprint uses Render's `singapore` region for the application and database.
- Hashed Vite assets receive a one-year immutable browser cache.
- The application no longer blocks first paint on the Pusher CDN; the client is loaded after the page load/idle period and polling remains available.
- The Documents page renders 10 rows first and fetches metrics/filter catalogs after first paint. It loads upload tasks only after the upload dialog and a Job are selected, and versions only after a document is selected.

No public cloud provider can guarantee identical connectivity from every network in mainland China. Test the final custom domain from the specific provinces and carriers your users use. Keep fonts, JavaScript, CSS, images, and document downloads on your own domain wherever possible.

## Important Render constraints

- Keep the persistent disk attached. Without it, local uploads are not durable.
- A service with an attached persistent disk runs as a single instance, so do not set `numInstances` above 1 for this web service.
- The application and PostgreSQL database should remain in the same region.
- Back up both PostgreSQL and `/var/data/documents`.

## Useful checks

```bash
# Render shell
php artisan about
php artisan migrate:status
php artisan route:list --name=documents
ls -lah /var/data/documents
curl -I http://127.0.0.1:${PORT:-10000}/up
```

For a document that still cannot open, compare its database `path` value with the actual relative path below `/var/data/documents`, then inspect the Render logs for `Document record points to a missing storage object`.
