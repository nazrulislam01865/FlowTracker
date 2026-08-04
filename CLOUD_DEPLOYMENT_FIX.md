# FlowTrack cloud deployment fix

This package contains an application fix for the `master_records_workspace_type_code_uq` duplicate-key error.

## Root causes

1. The repository lock file selects Symfony 8.1 packages, which require PHP 8.4.1 or newer. Do not deploy it with PHP 8.3.
2. `MasterDataService::syncLegacy()` previously used Eloquent `firstOrCreate()`. Concurrent Livewire requests, or a matching soft-deleted row, could make it attempt a duplicate insert. It now uses one atomic database upsert.

## Deploy on Ubuntu 24.04

Run these commands from the application server after backing up the database and `.env` file.

```bash
cd /var/www/flowtrack

# Keep CLI and FPM on the same PHP release.
update-alternatives --set php /usr/bin/php8.4
php -v
systemctl enable --now php8.4-fpm

# Restore dependency files if composer update/config was run on the server.
git restore composer.json composer.lock

COMPOSER_ALLOW_SUPERUSER=1 composer install \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-interaction

php artisan optimize:clear
php artisan migrate --force

# Runs safely after this patch; it repairs/restores the matching legacy rows.
php artisan tinker --execute='app(\App\Services\MasterDataService::class)->syncLegacy();'

php artisan storage:link 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

systemctl restart php8.4-fpm
nginx -t && systemctl reload nginx
supervisorctl restart 'flowtrack-worker:*' 2>/dev/null || true
```

## Verify

```bash
cd /var/www/flowtrack
php artisan about
php artisan migrate:status
systemctl is-active php8.4-fpm nginx mysql redis-server

mysql -uroot -p -e "USE flowtrack; SELECT id,workspace_id,type,code,status,deleted_at FROM master_records WHERE workspace_id=1 AND type='product' AND code='PRD-001';"

tail -n 100 storage/logs/laravel.log
```

The `PRD-001` query should return one row, and loading Master Data / Workflow Setup should no longer write a duplicate record.

## Production rule

Do not run `composer update` on the production server. Update dependencies in development/CI, commit `composer.lock`, and run only `composer install` during deployment.
