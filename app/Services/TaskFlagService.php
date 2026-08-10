<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TaskFlagService
{
    public function activeFlags(): Collection
    {
        return app(MasterDataService::class)->active('task_flag');
    }

    public function resolveActive(?string $value): ?MasterRecord
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $normalized = mb_strtolower($value);

        return $this->activeFlags()->first(function (MasterRecord $flag) use ($normalized): bool {
            return mb_strtolower(trim((string) $flag->name)) === $normalized
                || mb_strtolower(trim((string) $flag->code)) === $normalized;
        });
    }

    public function requireActive(?string $value): ?MasterRecord
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $flag = $this->resolveActive($value);
        if (!$flag) {
            throw ValidationException::withMessages([
                'taskFlag' => 'Select an active Task Flag from Master Data.',
            ]);
        }

        return $flag;
    }

    public function defaultActive(): ?MasterRecord
    {
        $flags = $this->activeFlags();

        return $flags->first(fn (MasterRecord $flag) => strcasecmp(trim((string) $flag->name), 'Management attention') === 0)
            ?? $flags->first();
    }

    public function labelForTask(Task $task): ?string
    {
        if (!$task->needs_attention) return null;

        $joinedName = trim((string) $task->getAttribute('task_flag_name'));
        if ($joinedName !== '') return $joinedName;

        if ($task->relationLoaded('attentionFlag') && $task->attentionFlag) {
            $name = trim((string) $task->attentionFlag->name);
            if ($name !== '') return $name;
        }

        $legacy = trim((string) $task->attention_reason);
        if ($legacy !== '') return $legacy;

        return $this->defaultActive()?->name ?: 'Management attention';
    }

    public function labelForOrder(FlowJob $job): ?string
    {
        if (!$job->needs_attention) return null;

        $tasks = $job->relationLoaded('flaggedTasks')
            ? $job->flaggedTasks
            : $job->flaggedTasks()->with('attentionFlag:id,name,status,sort_order')->get();

        $labels = $tasks
            ->sortBy(fn (Task $task) => sprintf(
                '%010d-%010d',
                (int) ($task->attentionFlag?->sort_order ?? PHP_INT_MAX),
                (int) $task->id,
            ))
            ->map(fn (Task $task) => $this->labelForTask($task))
            ->filter()
            ->values();

        if ($labels->isNotEmpty()) return (string) $labels->first();

        return $this->defaultActive()?->name ?: 'Management attention';
    }
}
