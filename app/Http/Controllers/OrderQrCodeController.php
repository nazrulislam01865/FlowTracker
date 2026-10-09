<?php

namespace App\Http\Controllers;

use App\Models\FlowJob;
use App\Services\JobService;
use App\Services\ArtworkTrackingBackfillService;
use Illuminate\Http\RedirectResponse;
use Throwable;
use App\Support\StoredFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderQrCodeController extends Controller
{
    public function generate(FlowJob $job, ArtworkTrackingBackfillService $backfill): RedirectResponse
    {
        $actor = auth()->user();
        abort_unless($actor->canModule('documents', 'create'), 403);
        $visibleJob = app(JobService::class)->findVisibleBase($actor, (int) $job->id);

        try {
            $result = $backfill->process($visibleJob, $actor);
        } catch (Throwable $exception) {
            report($exception);
            return back()->withErrors(['artworkTracking' => 'The tracking document could not be generated. Check the confirmed artwork files and try again.']);
        }

        if ($result['pdf'] === 'skipped') {
            return back()->with('success', 'The order tracking QR is ready.')
                ->withErrors(['artworkTracking' => $result['reason']]);
        }

        return back()->with('success', $result['pdf'] === 'existing'
            ? 'The existing confirmed-artwork PDF is available.'
            : 'The confirmed-artwork PDF and tracking QR are ready.');
    }

    public function download(FlowJob $job): StreamedResponse
    {
        $visibleJob = app(JobService::class)->findVisibleBase(auth()->user(), (int) $job->id);
        $document = $visibleJob->artworkTrackingPdf;

        abort_unless($document, 404, 'The confirmed artwork tracking PDF has not been generated yet.');

        return StoredFileResponse::download(
            (string) $document->path,
            (string) $document->name,
            'application/pdf',
        );
    }
}
