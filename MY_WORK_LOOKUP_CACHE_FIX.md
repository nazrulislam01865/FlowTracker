# My Work lookup cache serialization fix

## Problem

`/my-work` failed with:

```text
Attempt to read property "id" on string
resources/views/livewire/my-work/index.blade.php:12
```

The performance update cached Eloquent collections for Board clients, users,
workflows, and phases. The project keeps Laravel 13's secure
`cache.serializable_classes=false` setting, so cached model objects are not a
safe cross-request payload. A later request could receive a non-model value
while the Blade template expected `$row->id` and `$row->name`.

## Fix

- Board lookup caches now store scalar arrays only.
- Lookup rows are converted to lightweight property-accessible objects after
  they are read from cache.
- Versioned cache keys bypass the old incompatible entries immediately.
- Malformed cache payloads are detected, removed, and rebuilt automatically.
- The same treatment is applied to clients, users, workflows, and workflow
  phases.
- Regression tests cover scalar cache payloads and automatic recovery from an
  invalid legacy payload.

## Deployment

```bash
php artisan optimize:clear
php artisan cache:clear
```

`cache:clear` is recommended once during this upgrade to remove unused old
Board cache keys. The versioned keys already prevent the application from
reading them.
