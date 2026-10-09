# Product supplier filter - visibility fix

## Cause
The previous complete source archive included an old PHP-compiled Blade view at
`storage/framework/views/438fb010192c3eb44205ed3cda717551.php`.
That compiled view renders the Product List without the new **All suppliers**
filter, despite the updated source Blade view already containing the filter.
The refreshed full archive excludes compiled Blade files and other environment-
specific runtime cache files. No product or export business logic was changed
in this visibility fix.

## Apply
Either replace your project source with the refreshed full ZIP (keep your
existing .env, storage data, credentials and uploads), or extract only the
source files in the patch ZIP into your existing project root.

Run from your Laravel project root:

    grep -n 'placeholder="All suppliers"' resources/views/livewire/master-data/sections/product.blade.php
    php artisan view:clear
    php artisan optimize:clear
    npm run build

Refresh the Products page (Ctrl+Shift+R or Cmd+Shift+R).

If grep returns no line, the updated Blade file has not been copied to the
correct project root. Do not expect clearing cache alone to add the filter.
If grep matches but the old page remains, confirm your web server points to
the same project directory, and clear the PHP OPcache/restart the long-running
PHP workers in the environment as appropriate.

## Expected behavior
- All suppliers appears between Product category and Client availability.
- Supplier choices show short codes; options load only when opened and can
  load more via the existing remote search-select.
- The normal Export Excel uses the current active table filters, including
  supplier; it exports all matching rows, not only the current page.
- Existing permissions and supplier assignments remain unchanged.

## Packaging note
This archive preserves the supplied prebuilt public/build assets so the app
remains bootstrappable. `npm run build` refreshes those assets with the updated
seven-control responsive filter CSS; do not rely on the previous build for
final layout testing.
