# FlowTracker: QR and confirmed-artwork PDFs for historical Orders

## What changed

- `flowtrack:backfill-artwork-tracking` processes every existing Order (or `--order=ID`).
- Every Order receives a stable tracking token, which is reused on subsequent runs.
- Orders with an existing valid generated PDF keep it; there are no duplicate documents on rerun.
- Older Orders with a completed Internal Artwork Review and available confirmed artwork get the **same PDF** as newly confirmed Orders (company setup branding, artwork, and small live-tracking QR on the same page).
- Orders without verifiable confirmed artwork receive the tracking token **but do not get an incorrectly labelled "confirmed artwork" PDF**. The report explains why.
- When public order tracking is enabled, staff with permission to create documents can use **Generate QR & confirmed-artwork PDF** in Order Details. The button does not confirm/complete any artwork task and respects existing Order visibility permissions.
- The existing automatic generation on new artwork confirmation remains unchanged.

No migrations, Composer packages, background worker, or frontend Vite rebuild are required **for this change**, assuming the existing tracking-token migration is already installed.

## Before running on the server

Deploy the updated PHP and Blade files using the established Alibaba SOP. Back up the production database first. Confirm the 2026-10-05 tracking token migration is applied (`php artisan migrate:status`).

Server commands **after** code deployment:

```bash
cd /var/www/FlowTracker
php artisan flowtrack:backfill-artwork-tracking --dry-run
php artisan flowtrack:backfill-artwork-tracking
```

To test just one Order (use an actual numeric Order database ID):

```bash
php artisan flowtrack:backfill-artwork-tracking --order=123 --dry-run
php artisan flowtrack:backfill-artwork-tracking --order=123
```

For smaller batches of database reads:

```bash
php artisan flowtrack:backfill-artwork-tracking --chunk=50
```

The command reports counts of generated/existing/skipped PDFs and writes a detailed CSV under `storage/app/private/reports/artwork-tracking-backfill-*.csv`. This file is not placed in the public web directory. `--dry-run` does not create QR tokens or PDFs (but does write its CSV report). Existing PDF files are not replaced on normal repeat runs.

## Order tracking intentionally muted?

The change **does not modify** `FLOWTRACK_ORDER_TRACKING_ENABLED`. You can run the backfill while tracking is disabled. PDFs and QR tokens will be generated privately, but customer QR destinations and the Order Detail tracking section will remain unavailable until you explicitly turn public order tracking back on and refresh Laravel config/route cache as part of a planned deployment.

When you enable public tracking without a domain, ensure `APP_URL` matches your real publicly accessible host and protocol, for example `http://8.163.67.79` **only if that is still your server's current public IP**. QR codes contain this absolute URL. Check the address before generating them in bulk.

## Eligibility and skipped cases

A confirmed-artwork PDF is generated only when the project can identify a completed `ART_INTERNAL_REVIEW` task and a same-phase `ART_PREPARE_UPLOAD` task, resolve the current non-cancelled artwork files (including selective revisions), verify that the files are stored, and confirm no newer artwork or unresolved revision supersedes that review. Legacy records with no such evidence are reported as skipped instead of changing order status or inventing artwork. Existing valid PDFs are reused regardless of whether their original task records have since been retired.

## Verification

After restoring Composer dependencies on your local machine:

```bash
php artisan test --filter=ArtworkTrackingBackfillTest
php artisan route:list --name=orders.tracking.generate
php artisan help flowtrack:backfill-artwork-tracking
```

The test suite **could not be executed in the supplied source archive** because the archive does not contain `vendor/` and this build environment has no Composer binary. The changed PHP files passed `php -l` syntax checks. Run the tests before deploying to production.

## Changed files

- `app/Services/ArtworkTrackingPdfService.php` — reuse current PDF renderer with pre-validated historical artwork and optional system attribution
- `app/Services/ArtworkTrackingBackfillService.php` — token setup, approval checks, idempotency and revision handling
- `app/Console/Commands/BackfillArtworkTracking.php` — batch or single-order command and detailed private CSV report
- `app/Http/Controllers/OrderQrCodeController.php` — authorized single-order generation action
- `routes/web.php` — POST route, respecting the public-tracking feature flag
- `resources/views/components/jobs/order-detail/tracking.blade.php` — missing-PDF UI and QR-only state
- `tests/Feature/ArtworkTrackingBackfillTest.php` — regression tests
