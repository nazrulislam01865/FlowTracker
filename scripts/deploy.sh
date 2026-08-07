#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
if [[ -f package-lock.json || -f npm-shrinkwrap.json ]]; then
    npm ci
else
    npm install --no-audit --no-fund
fi
npm run build

# Remove stale framework caches before migrations, then rebuild all production
# caches after the new code and schema are in place.
php artisan optimize:clear
php artisan migrate --force
if [[ ! -L public/storage && ! -e public/storage ]]; then
    php artisan storage:link
fi
php artisan optimize
php artisan queue:restart

echo "FlowTrack deployment optimization completed."
