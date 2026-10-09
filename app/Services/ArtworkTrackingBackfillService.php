<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use App\Support\OrderDetailPresenter;
use Illuminate\Support\Collection;

/**
 * Prepare tracking tokens for historical Orders and generate the identical
 * confirmed-artwork PDF used by the live Artwork confirmation action.
 * This service never completes tasks or changes an artwork approval state.
 */
final class ArtworkTrackingBackfillService
{
    public function __construct(private readonly ArtworkTrackingPdfService $pdfs)
    {
    }

    /**
     * @return array{qr:string,pdf:string,reason:string}
     */
    public function process(FlowJob $job, ?User $actor = null, bool $dryRun = false): array
    {
        $qr = filled($job->tracking_token) ? 'existing' : ($dryRun ? 'would_generate' : 'generated');
        if (! $dryRun && $qr === 'generated') {
            $job->ensureTrackingToken();
        }

        // An already stored PDF is a historical record; do not rewrite it or
        // depend on its old task graph still being present after migrations.
        $job->loadMissing('artworkTrackingPdf');
        $existing = $job->artworkTrackingPdf;
        if ($existing && app(SecureDocumentStorage::class)->locate((string) $existing->path)) {
            return ['qr' => $qr, 'pdf' => 'existing', 'reason' => 'Confirmed-artwork PDF already exists.'];
        }

        [$review, $artwork, $reason] = $this->eligibleArtwork($job);

        if ($dryRun) {
            return [
                'qr' => $qr,
                'pdf' => $review ? 'would_generate' : 'skipped',
                'reason' => $review ? 'Confirmed artwork is available.' : $reason,
            ];
        }

        // A QR was already persisted above even if the artwork is missing.
        // The public feature flag still controls whether scanning can open it.
        if (! $review) {
            return ['qr' => $qr, 'pdf' => 'skipped', 'reason' => $reason];
        }

        $this->pdfs->generate($job, $review, $actor, $artwork);

        return ['qr' => $qr, 'pdf' => 'generated', 'reason' => 'Confirmed-artwork PDF generated.'];
    }

    /** @return array{0:?Task,1:Collection<int,Document>,2:string} */
    private function eligibleArtwork(FlowJob $job): array
    {
        // The bulk command eager-loads tasks; document/revision details are
        // fetched only for Orders with a completed review.
        // Most historical Orders do not have an approved artwork review.
        // Check the cheap task graph before loading any document bodies/records.
        $job->loadMissing('tasks.setupTemplate');

        $tasks = $job->tasks;
        $actions = app(OrderWorkflowActionService::class);
        $uploadTasks = $tasks->filter(
            fn (Task $task): bool => $actions->automationKey($task) === 'ART_PREPARE_UPLOAD'
        );
        $reviews = $tasks->filter(
            fn (Task $task): bool => $actions->automationKey($task) === 'ART_INTERNAL_REVIEW'
        )->sortByDesc('id');

        if ($reviews->isEmpty()) {
            return [null, collect(), 'No Internal Artwork Review task exists.'];
        }
        if (! $reviews->contains(fn (Task $review): bool => OrderDetailPresenter::isCompletedTask($review))) {
            return [null, collect(), 'Internal Artwork Review has not been completed.'];
        }
        if ($uploadTasks->isEmpty()) {
            return [null, collect(), 'The Artwork upload task is unavailable.'];
        }

        $job->loadMissing([
            'documents',
            'activities' => static fn ($query) => $query
                ->whereIn('event', [
                    'job.artwork_revision_applied',
                    'job.artwork_revision_requested',
                    'job.artwork_cancelled',
                ])->orderByDesc('id'),
        ]);

        foreach ($reviews as $review) {
            if (! OrderDetailPresenter::isCompletedTask($review)) {
                continue;
            }

            $upload = $uploadTasks->first(
                fn (Task $task): bool => (int) $task->workflow_phase_id === (int) $review->workflow_phase_id
            );
            if (! $upload) {
                continue;
            }

            $hasUnresolvedRevision = $job->activities
                ->where('event', 'job.artwork_revision_requested')
                ->contains(function ($activity) use ($review, $upload): bool {
                    $phaseMatches = (int) data_get($activity->meta, 'workflow_phase_id', 0)
                        === (int) $review->workflow_phase_id;
                    $uploadMatches = (int) data_get($activity->meta, 'target_task_id', 0)
                        === (int) $upload->id;

                    return ($phaseMatches || $uploadMatches)
                        && (! $review->completed_at || (
                            $activity->created_at
                            && $activity->created_at->greaterThan($review->completed_at)
                        ));
                });
            if ($hasUnresolvedRevision) {
                return [null, collect(), 'Artwork revision requested after the latest completed review.'];
            }

            // DocumentService resolves selective revisions and cancellations.
            // Reusing it prevents archived/replaced artwork being labelled as
            // the current approved version in the retroactive PDF.
            $upload->setRelation('job', $job);
            $artwork = app(DocumentService::class)->currentArtworkDocuments(
                $upload,
                $job->documents->where('task_id', $upload->id)->values(),
                $job->activities->where('event', 'job.artwork_revision_applied')->values(),
                $job->activities->where('event', 'job.artwork_cancelled')->values(),
            );
            if ($artwork->isEmpty()) {
                return [null, collect(), 'No active confirmed artwork files remain for this Order.'];
            }

            // An upload after the last recorded confirmation must be reviewed
            // again before it is used in a document marked "confirmed".
            if ($review->completed_at && $artwork->contains(
                fn (Document $document): bool => $document->created_at
                    && $document->created_at->greaterThan($review->completed_at)
            )) {
                return [null, collect(), 'Artwork has changed since the last completed review.'];
            }

            $storage = app(SecureDocumentStorage::class);
            if ($artwork->contains(fn (Document $document): bool => ! $storage->locate((string) $document->path))) {
                return [null, collect(), 'One or more confirmed artwork files are missing from storage.'];
            }

            return [$review, $artwork, ''];
        }

        return [null, collect(), 'Artwork confirmation is incomplete or its upload task is unavailable.'];
    }
}
