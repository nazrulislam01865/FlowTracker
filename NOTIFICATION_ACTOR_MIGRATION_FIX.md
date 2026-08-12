# Notification actor migration fix

The Tagged Comments update requires the `flow_notifications.actor_id` database column.

## Required after deploying the code

```bash
php artisan optimize:clear
php artisan migrate --force
php artisan queue:restart
php artisan optimize
```

For local development, `--force` is optional:

```bash
php artisan optimize:clear
php artisan migrate
php artisan queue:restart
```

Then refresh the dashboard.

The application now also has a compatibility guard so a short code-before-migration deployment window does not return an SQL `Unknown column actor_id` 500. Legacy mention rows are resolved from their stored notification title in a batched lookup so their user name/avatar can still render until/if `actor_id` is backfilled.
