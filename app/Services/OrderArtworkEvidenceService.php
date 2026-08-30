<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\TaskLink;
use Illuminate\Support\Str;

/**
 * Repairs artwork evidence that became detached from the current generated
 * Artwork upload task after an Order workflow / Task Pack definition changed.
 *
 * Runtime generated Tasks are soft-deleted when their setup identity changes,
 * while Documents and TaskLinks intentionally remain for audit/history. Older
 * Orders can therefore have valid artwork files whose task_id still points at a
 * retired Task. This service safely rebinds only historical/orphan artwork
 * evidence to the one active ART_PREPARE_UPLOAD task for the same Order.
 */
final class OrderArtworkEvidenceService
{
    public function repair(int $jobId): int
    {
        $job = FlowJob::query()->find($jobId);
        if (! $job) {
            return 0;
        }

        $activeTasks = Task::query()
            ->where('flow_job_id', $jobId)
            ->with([
                'setupTemplate.documentCategory',
                'documentCategory',
                'phase:id,name,sequence',
            ])
            ->get();

        /** @var Task|null $artworkTask */
        $artworkTask = $activeTasks->first(
            fn (Task $task): bool => $this->isArtworkUploadTask($task)
        );

        if (! $artworkTask) {
            return 0;
        }

        $activeTaskIds = $activeTasks
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $candidateDocuments = Document::query()
            ->where('flow_job_id', $jobId)
            ->where(function ($query) use ($activeTaskIds): void {
                $query->whereNull('task_id');
                if ($activeTaskIds->isNotEmpty()) {
                    $query->orWhereNotIn('task_id', $activeTaskIds->all());
                }
            })
            ->orderBy('id')
            ->get();

        $candidateLinks = TaskLink::query()
            ->whereIn(
                'task_id',
                Task::onlyTrashed()
                    ->where('flow_job_id', $jobId)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->all(),
            )
            ->get();

        $historicalTaskIds = $candidateDocuments
            ->pluck('task_id')
            ->merge($candidateLinks->pluck('task_id'))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $historicalTasks = $historicalTaskIds->isEmpty()
            ? collect()
            : Task::withTrashed()
                ->where('flow_job_id', $jobId)
                ->whereIn('id', $historicalTaskIds->all())
                ->with([
                    'setupTemplate.documentCategory',
                    'documentCategory',
                    'phase:id,name,sequence',
                ])
                ->get()
                ->keyBy(fn (Task $task) => (int) $task->id);

        $artworkCategory = $this->normalizedCategory($artworkTask);
        $historicalArtworkTaskIds = $historicalTasks
            ->filter(fn (Task $task): bool => $this->isArtworkUploadTask($task))
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->values();

        $movedDocuments = 0;

        foreach ($candidateDocuments as $document) {
            $sourceTaskId = (int) ($document->task_id ?? 0);
            $sourceTask = $sourceTaskId > 0
                ? $historicalTasks->get($sourceTaskId)
                : null;

            $belongsToArtwork = $sourceTask
                ? $this->isArtworkUploadTask($sourceTask)
                : false;

            // Compatibility fallback for old Orders whose retired task no
            // longer has a resolvable Task Pack item/title. The Artwork task
            // has a dedicated document category, so an orphan document with
            // that exact category can be safely restored to it.
            if (! $belongsToArtwork && $artworkCategory !== '') {
                $belongsToArtwork = $this->normalize((string) $document->category) === $artworkCategory;
            }

            if (! $belongsToArtwork) {
                continue;
            }

            $document->update(['task_id' => (int) $artworkTask->id]);
            $movedDocuments++;
        }

        $movedLinks = 0;
        if ($historicalArtworkTaskIds->isNotEmpty()) {
            $movedLinks = TaskLink::query()
                ->whereIn('task_id', $historicalArtworkTaskIds->all())
                ->update(['task_id' => (int) $artworkTask->id]);

            // Revision cards reference the upload task in activity metadata.
            // Keep those references aligned with the repaired task identity.
            $job->activities()
                ->where('event', 'job.artwork_revision_requested')
                ->get()
                ->each(function ($activity) use ($historicalArtworkTaskIds, $artworkTask): void {
                    $meta = is_array($activity->meta) ? $activity->meta : [];
                    $targetTaskId = (int) ($meta['target_task_id'] ?? 0);

                    if (! $historicalArtworkTaskIds->contains($targetTaskId)) {
                        return;
                    }

                    $meta['target_task_id'] = (int) $artworkTask->id;
                    $activity->update(['meta' => $meta]);
                });
        }

        $changed = $movedDocuments + (int) $movedLinks;
        if ($changed > 0) {
            $this->normalizeArtworkVersions((int) $artworkTask->id);
            app(WorkspaceRefreshService::class)->touch('OrderArtworkEvidence:repaired');
        }

        return $changed;
    }

    private function isArtworkUploadTask(Task $task): bool
    {
        $key = trim((string) app(OrderWorkflowActionService::class)->automationKey($task));
        if ($key === 'ART_PREPARE_UPLOAD') {
            return true;
        }

        $title = $this->normalize((string) $task->title);

        return in_array($title, [
            'prepare upload artwork',
            'prepare and upload artwork',
            'prepare artwork',
            'upload artwork',
            'artwork upload',
        ], true)
            || (str_contains($title, 'artwork') && str_contains($title, 'upload'));
    }

    private function normalizedCategory(Task $task): string
    {
        return $this->normalize((string) (
            $task->documentCategory?->name
            ?: $task->setupTemplate?->documentCategory?->name
            ?: ''
        ));
    }

    private function normalize(string $value): string
    {
        return (string) Str::of($value)
            ->lower()
            ->replace('&', ' and ')
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish();
    }

    private function normalizeArtworkVersions(int $taskId): void
    {
        Document::query()
            ->where('task_id', $taskId)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'version'])
            ->values()
            ->each(function (Document $document, int $index): void {
                $version = $index + 1;
                if ((int) $document->version !== $version) {
                    $document->update(['version' => $version]);
                }
            });
    }
}
