<?php

namespace App\Console\Commands;

use App\Models\FlowJob;
use App\Services\ArtworkTrackingBackfillService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

final class BackfillArtworkTracking extends Command
{
    protected $signature = 'flowtrack:backfill-artwork-tracking
        {--order= : Process one Order ID instead of all historical Orders}
        {--dry-run : Check eligibility without generating QR tokens or PDFs}
        {--chunk=100 : Orders processed per batch (10-200)}';

    protected $description = 'Generate QR tokens for historical Orders and confirmed-artwork PDFs where approved artwork exists';

    public function handle(ArtworkTrackingBackfillService $backfill): int
    {
        $rawId = $this->option('order');
        if ($rawId !== null && (! ctype_digit((string) $rawId) || (int) $rawId < 1)) {
            $this->error('--order must be a positive Order ID.');
            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');
        if (! $dryRun && app()->environment('production')) {
            $host = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
            if ($host === '' || in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1'], true)) {
                $this->error('Configure APP_URL with the public server address before embedding tracking links in PDFs.');
                return self::FAILURE;
            }
        }

        $chunk = max(10, min(200, (int) $this->option('chunk')));
        $directory = storage_path('app/private/reports');
        File::ensureDirectoryExists($directory, 0700, true);
        $report = $directory.'/artwork-tracking-backfill-'.now()->format('Ymd-His-u').'.csv';
        $stream = fopen($report, 'wb');
        if ($stream === false) {
            $this->error('Unable to create the private backfill report.');
            return self::FAILURE;
        }

        @chmod($report, 0600);
        fputcsv($stream, ['order_id', 'order_number', 'qr', 'pdf', 'reason']);
        $totals = ['orders' => 0, 'qr' => 0, 'pdf' => 0, 'existing' => 0, 'skipped' => 0, 'failed' => 0];

        $process = function (FlowJob $job) use ($backfill, $dryRun, $stream, &$totals): void {
            $totals['orders']++;
            try {
                $result = $backfill->process($job, null, $dryRun);
                if (in_array($result['qr'], ['generated', 'would_generate'], true)) $totals['qr']++;
                if (in_array($result['pdf'], ['generated', 'would_generate'], true)) $totals['pdf']++;
                if ($result['pdf'] === 'existing') $totals['existing']++;
                if ($result['pdf'] === 'skipped') $totals['skipped']++;
            } catch (Throwable $exception) {
                report($exception);
                $result = ['qr' => filled($job->tracking_token) ? 'existing' : 'unknown', 'pdf' => 'failed', 'reason' => $exception->getMessage()];
                $totals['failed']++;
            }

            $number = (string) ($job->job_number ?: $job->order_number ?: '');
            // Prevent spreadsheet formula interpretation when the CSV is reviewed.
            if (preg_match('/^[\s]*[=+@\-]/', $number)) $number = "'".$number;
            fputcsv($stream, [$job->id, $number, $result['qr'], $result['pdf'], $result['reason']]);
        };

        try {
            $orders = FlowJob::query()
                ->select(['id', 'client_id', 'job_number', 'order_number', 'tracking_token', 'tracking_token_created_at'])
                ->with([
                    'artworkTrackingPdf',
                    'tasks.setupTemplate',
                ]);

            if ($rawId !== null) {
                $job = $orders->find((int) $rawId);
                if (! $job) {
                    $this->error('Order '.$rawId.' was not found.');
                    return self::FAILURE;
                }
                $process($job);
            } else {
                $orders->chunkById($chunk, static function ($jobs) use ($process): void {
                    foreach ($jobs as $job) $process($job);
                });
            }
        } finally {
            fclose($stream);
        }

        $mode = $dryRun ? 'DRY RUN' : 'COMPLETED';
        $this->info("Artwork tracking backfill: {$mode}");
        $this->line("Orders: {$totals['orders']} | QR tokens ".($dryRun ? 'needed' : 'created').": {$totals['qr']} | PDFs ".($dryRun ? 'eligible' : 'created').": {$totals['pdf']} | Existing PDFs: {$totals['existing']} | Skipped PDFs: {$totals['skipped']} | Failed: {$totals['failed']}");
        $this->line('Private CSV report: '.$report);

        if (! (bool) config('flowtrack.order_tracking_enabled', false)) {
            $this->warn('Public order tracking is disabled. Generated QR links will only open after you deliberately re-enable it.');
        }

        return $totals['failed'] ? self::FAILURE : self::SUCCESS;
    }
}
