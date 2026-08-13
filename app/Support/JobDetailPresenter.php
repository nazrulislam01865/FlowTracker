<?php

namespace App\Support;

use App\Models\FlowJob;
use App\Models\Task;
use App\Models\WorkflowPhase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class JobDetailPresenter
{
    public static function team(FlowJob $job): Collection
    {
        return BoardPresenter::team($job);
    }

    public static function products(FlowJob $job): Collection
    {
        if ($job->relationLoaded('items') && $job->items->isNotEmpty()) return $job->items;
        if (blank($job->product)) return collect();

        return collect([(object) [
            'id' => null,
            'category_name' => $job->category ?: 'General',
            'product_name' => $job->product,
            'quantity' => $job->quantity,
        ]]);
    }

    public static function phaseTasks(FlowJob $job, ?WorkflowPhase $phase = null): Collection
    {
        $phase ??= $job->workflow?->phases?->firstWhere('id', $job->workflow_phase_id) ?: $job->phase;
        if (!$phase) return collect();

        $tasks = $job->tasks->where('workflow_phase_id', $phase->id);
        if ($phase->taskPack) {
            $allowedItemIds = $phase->taskPack->items->pluck('id')->map(fn ($id) => (int) $id)->all();
            $tasks = $tasks->filter(function (Task $task) use ($allowedItemIds, $phase) {
                // Manual Order tasks belong to the phase directly and do not have
                // a Task Pack item. Keep them visible beside configured tasks.
                if (!$task->task_pack_task_id) return true;
                if (in_array((int) $task->task_pack_task_id, $allowedItemIds, true)) return true;
                return (int) ($task->setupTemplate?->task_pack_id ?? 0) === (int) $phase->task_pack_id;
            });
        } else {
            // A phase without a Task Pack can still contain manually added tasks.
            $tasks = $tasks->filter(fn (Task $task) => !$task->task_pack_task_id);
        }

        return $tasks
            ->sortBy(fn (Task $task) => [
                $task->setupTemplate?->sort_order ?? $task->template?->sequence ?? 9999,
                $task->id,
            ])
            ->values();
    }

    public static function completedCount(Collection $tasks): int
    {
        return $tasks->filter(fn (Task $task) => $task->completed_at || $task->status === 'Completed')->count();
    }

    public static function nextTask(FlowJob $job): ?Task
    {
        return BoardPresenter::nextTask($job);
    }

    public static function phaseRequiredDocuments(FlowJob $job, WorkflowPhase $phase): Collection
    {
        if (!$phase->taskPack) return collect();

        $phaseTasks = self::phaseTasks($job, $phase);
        $requirements = collect();

        // Primary source: explicit document requirements on Task Pack items.
        foreach ($phase->taskPack->items->filter(fn ($item) => filled($item->document_category_id)) as $item) {
            $task = $phaseTasks->first(fn (Task $candidate) =>
                (int) $candidate->task_pack_task_id === (int) $item->id
            );

            $task ??= $job->tasks->first(fn (Task $candidate) =>
                (int) $candidate->workflow_phase_id === (int) $phase->id
                && $candidate->title === $item->title
            );

            // Required-document presentation must use the task collection
            // prepared by JobService. Never issue a per-item fallback query
            // from a presenter: on large Task Packs that becomes an N+1.
            if (!$task) continue;

            $name = $item->documentCategory?->name
                ?: $task->documentCategory?->name
                ?: $task->setupTemplate?->documentCategory?->name
                ?: 'Required document';
            $received = self::documentsForTask($job, $task)->filter(fn ($document) =>
                strcasecmp(trim((string) $document->category), trim((string) $name)) === 0
            )->count();

            $requirements->push((object) [
                'phase' => $phase,
                'task' => $task,
                'template' => $item,
                'name' => $name,
                'received' => $received,
                'complete' => $received > 0,
                'current' => (int) $phase->id === (int) $job->workflow_phase_id,
            ]);
        }

        // Compatibility source: generated tasks already marked as having a
        // Task Pack document requirement. This covers older Jobs where the
        // generated task carries the requirement but the Task Pack relation
        // was stale when the Job was first opened. It never introduces a
        // workflow-only requirement.
        foreach ($phaseTasks->filter(fn (Task $task) =>
            filled($task->document_category_id)
            && (($task->document_requirement_source ?? null) === 'task_pack'
                || filled($task->setupTemplate?->document_category_id))
        ) as $task) {
            if ($requirements->contains(fn ($row) => (int) $row->task->id === (int) $task->id)) continue;

            $name = $task->documentCategory?->name
                ?: $task->setupTemplate?->documentCategory?->name
                ?: 'Required document';
            $received = self::documentsForTask($job, $task)->filter(fn ($document) =>
                strcasecmp(trim((string) $document->category), trim((string) $name)) === 0
            )->count();

            $requirements->push((object) [
                'phase' => $phase,
                'task' => $task,
                'template' => $task->setupTemplate,
                'name' => $name,
                'received' => $received,
                'complete' => $received > 0,
                'current' => (int) $phase->id === (int) $job->workflow_phase_id,
            ]);
        }

        return $requirements
            ->unique(fn ($row) => $row->phase->id.'-'.$row->task->id.'-'.strtolower(trim($row->name)))
            ->values();
    }

    private static function documentsForTask(FlowJob $job, Task $task): Collection
    {
        if ($job->relationLoaded('documents')) {
            return $job->documents
                ->where('task_id', $task->id)
                ->values();
        }

        // A presenter must not trigger a lazy relationship query. Legacy
        // callers using findVisible() already eager-load task documents; the
        // lightweight detail tabs eager-load the Order document collection.
        return $task->relationLoaded('documents')
            ? $task->documents
            : collect();
    }

    public static function requiredDocuments(FlowJob $job): Collection
    {
        return $job->workflow->phases
            ->flatMap(fn ($phase) => self::phaseRequiredDocuments($job, $phase))
            ->values();
    }

    public static function missingCurrentDocuments(FlowJob $job): Collection
    {
        return self::phaseRequiredDocuments($job, $job->phase)
            ->filter(fn ($doc) => ! $doc->complete)
            ->values();
    }

    public static function blockers(FlowJob $job): Collection
    {
        $currentTasks = self::phaseTasks($job);
        $requiredOpen = $currentTasks
            ->filter(fn (Task $task) => ($task->setupTemplate?->is_required ?? $task->template?->is_required ?? true) !== false && !$task->completed_at && $task->status !== 'Completed')
            ->values();

        $blockers = collect();
        if ($requiredOpen->isNotEmpty()) {
            $blockers->push((object) [
                'type' => 'task',
                'label' => $requiredOpen->count().' required task'.($requiredOpen->count() === 1 ? '' : 's').' block'.($requiredOpen->count() === 1 ? 's' : '').' the next phase',
                'description' => 'Complete the required Task Pack work before moving to '.(self::nextPhase($job)?->name ?? 'the next phase').'.',
            ]);
        }

        $missingDocs = self::missingCurrentDocuments($job);
        if ($missingDocs->isNotEmpty()) {
            $blockers->push((object) [
                'type' => 'document',
                'label' => $missingDocs->count().' Task Pack document'.($missingDocs->count() === 1 ? '' : 's').' still required',
                'description' => 'Upload the required document for '.$missingDocs->pluck('task.title')->filter()->implode(', ').' before moving forward.',
            ]);
        }

        return $blockers;
    }

    public static function nextPhase(FlowJob $job): ?WorkflowPhase
    {
        return $job->workflow->phases->firstWhere('sequence', $job->phase->sequence + 1);
    }

    public static function phaseHistoryRows(FlowJob $job): Collection
    {
        return $job->workflow->phases->map(function ($phase) use ($job) {
            $history = $job->phaseHistories->firstWhere('workflow_phase_id', $phase->id);
            $isDone = $phase->sequence < $job->phase->sequence;
            $isCurrent = (int) $phase->id === (int) $job->workflow_phase_id;

            return (object) [
                'phase' => $phase,
                'status' => $isCurrent ? 'Current' : ($isDone ? 'Completed' : 'Planned'),
                'entered' => $history?->entered_at,
                'completed' => $history?->completed_at,
                'time' => $history?->entered_at ? max(1, (int) $history->entered_at->diffInDays(($history->completed_at ?: now()))) : null,
                'outcome' => $isCurrent ? (self::blockers($job)->isNotEmpty() ? 'Blocked' : 'Ready') : ($isDone ? 'Passed' : '—'),
            ];
        });
    }

    public static function taskStatusClass(string $status): string
    {
        return match (true) {
            str_contains($status, 'Completed') || str_contains($status, 'Done') => 'done',
            str_contains($status, 'Waiting') => 'waiting',
            str_contains($status, 'Blocked') || str_contains($status, 'Revision') => 'blocked',
            str_contains($status, 'Progress') => 'progress',
            default => 'ready',
        };
    }

    public static function healthClass(?string $health): string
    {
        return match ($health) {
            'On Track', 'Completed' => 'green',
            'Blocked' => 'purple',
            'At Risk', 'Delayed', 'Needs Attention' => 'red',
            default => 'amber',
        };
    }
}
