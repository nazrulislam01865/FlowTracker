<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InquiryIntelligenceService
{
    public function data(User $user, array $filters = []): array
    {
        $this->authorize($user);

        [$fromLocal, $toLocal, $periodLabel] = $this->periodBounds((string) ($filters['period'] ?? 'month'));
        $timezone = app(WorkspaceSettingsService::class)->displayTimezone();
        $fromUtc = $fromLocal->utc();
        $toUtc = $toLocal->utc();

        $query = app(InquiryService::class)->visibleQuery($user)
            ->whereBetween('inquiries.created_at', [$fromUtc, $toUtc]);

        $search = trim((string) ($filters['search'] ?? ''));
        $priority = trim((string) ($filters['priority'] ?? ''));
        $assigneeId = (int) ($filters['assignee_id'] ?? 0);

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function (Builder $match) use ($like): void {
                $match->whereLike('inquiry_number', $like)
                    ->orWhereLike('reference_number', $like)
                    ->orWhereLike('subject', $like)
                    ->orWhereLike('requirement_notes', $like)
                    ->orWhereHas('client', fn (Builder $client) => $client->whereLike('name', $like))
                    ->orWhereHas('items', fn (Builder $item) => $item->whereLike('item_name', $like)->orWhereLike('category', $like))
                    ->orWhereHas('tasks.assignee', fn (Builder $assignee) => $assignee->whereLike('name', $like));
            });
        }

        if ($priority !== '') $query->where('inquiries.priority', $priority);
        if ($assigneeId > 0) $query->whereHas('tasks', fn (Builder $task) => $task->where('assignee_id', $assigneeId));

        $inquiries = $query
            ->reorder()
            ->orderByDesc('inquiries.created_at')
            ->with([
                'client:id,name',
                'owner:id,name,profile_image_path',
                'creator:id,name,profile_image_path',
                'items:id,inquiry_id,category,item_name,quantity,unit,notes,sort_order',
                'tasks' => fn ($task) => $task->select([
                    'id','inquiry_id','assignee_id','title','sequence','due_date','status','requires_submission','started_at','completed_at','created_at','updated_at','needs_attention','attention_reason',
                ])->with([
                    'assignee:id,name,profile_image_path',
                    'documents:id,inquiry_id,inquiry_task_id',
                    'inquiry:id,inquiry_number',
                ]),
                'convertedJob:id,source_inquiry_id,job_number,order_number,status,created_at,completed_at',
                'sourceOrder:id,source_inquiry_id,job_number,order_number,status,created_at,completed_at',
            ])
            ->get([
                'id','inquiry_number','client_id','owner_id','created_by','reference_number','subject','requirement_notes','priority','status','result','needs_attention','attention_reason','started_at','completed_at','converted_job_id','created_at','updated_at',
            ]);

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $inquiries = $inquiries->filter(fn (Inquiry $inquiry) => strcasecmp($this->inquiryDisplayStatus($inquiry), $status) === 0)->values();
        }

        $inquiryIds = $inquiries->pluck('id')->map(fn ($id) => (int) $id)->all();
        $reopenedTaskIds = $this->reopenedTaskIds($inquiryIds);
        $assignmentTimes = $this->taskAssignmentTimes($inquiryIds);

        $portfolio = $this->portfolio($inquiries, $reopenedTaskIds, $fromLocal, $toLocal, $timezone);
        $people = $this->people($inquiries, $reopenedTaskIds, $assignmentTimes, $timezone, $assigneeId);
        $products = $this->products($inquiries);

        return [
            'period' => [
                'label' => $periodLabel,
                'from' => $fromLocal,
                'to' => $toLocal,
            ],
            'filters' => [
                'statuses' => $this->statusOptions($user, $fromUtc, $toUtc),
                'priorities' => $this->priorityOptions($user, $fromUtc, $toUtc),
                'assignees' => $this->assigneeOptions($user, $fromUtc, $toUtc),
            ],
            'portfolio' => $portfolio,
            'people' => $people,
            'products' => $products,
        ];
    }

    public function exportRows(User $user, array $filters = []): array
    {
        abort_unless(app(AccessControlService::class)->can($user, 'reports', 'export'), 403);
        $report = $this->data($user, $filters);

        return $report['portfolio']['rows'];
    }

    private function portfolio(Collection $inquiries, array $reopenedTaskIds, CarbonImmutable $from, CarbonImmutable $to, string $timezone): array
    {
        $total = $inquiries->count();
        $completed = $inquiries->filter(fn (Inquiry $inquiry) => $this->isCompleted($inquiry))->count();
        $open = max(0, $total - $completed);
        $tasks = $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks)->values();
        $completedTasks = $tasks->whereNotNull('completed_at')->values();
        $taskTotal = $tasks->count();
        $taskDone = $completedTasks->count();

        $submissionTasks = $completedTasks->where('requires_submission', true)->values();
        $submissionWithFiles = $submissionTasks
            ->filter(fn (InquiryTask $task) => $task->documents->isNotEmpty())
            ->count();
        $fileCompliance = $submissionTasks->count() > 0
            ? round($submissionWithFiles / $submissionTasks->count() * 100, 1)
            : null;

        $structuredCount = $inquiries->filter(fn (Inquiry $inquiry) => $inquiry->items->isNotEmpty())->count();
        $structuredPct = $total > 0 ? round($structuredCount / $total * 100, 1) : 0;
        $attentionCount = $inquiries->filter(fn (Inquiry $inquiry) => $this->needsAttention($inquiry))->count();
        $unstructuredCount = max(0, $total - $structuredCount);
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $overdueTasks = $tasks->filter(fn (InquiryTask $task) => !$task->completed_at
            && $task->due_date
            && $task->due_date->toDateString() < $today)->count();
        $unassignedOpenTasks = $tasks->filter(fn (InquiryTask $task) => !$task->completed_at && !$task->assignee_id)->count();

        $prioritySequence = app(MasterDataService::class)->active('priority')
            ->values()
            ->mapWithKeys(fn ($record, int $index) => [mb_strtolower(trim((string) $record->name)) => $index]);
        $priorityMix = $inquiries
            ->groupBy(fn (Inquiry $inquiry) => trim((string) $inquiry->priority) ?: 'Unspecified')
            ->map->count()
            ->map(fn (int $count, string $name) => [
                'name' => $name,
                'count' => $count,
                'sort' => $prioritySequence->get(mb_strtolower($name), 9999),
                'color' => app(MasterDataService::class)->displayColorFor('priority', $name),
            ])
            ->sort(function (array $a, array $b): int {
                if ($a['sort'] !== $b['sort']) return $a['sort'] <=> $b['sort'];
                if ($a['count'] !== $b['count']) return $b['count'] <=> $a['count'];
                return strcasecmp($a['name'], $b['name']);
            })
            ->values();
        $priorityMax = max(1, (int) ($priorityMix->max('count') ?? 1));
        $priorityMix = $priorityMix->map(function (array $row) use ($priorityMax, $total): array {
            unset($row['sort']);
            $row['width'] = (int) round($row['count'] / $priorityMax * 100);
            $row['share'] = $total > 0 ? round($row['count'] / $total * 100, 1) : 0;
            return $row;
        })->all();

        $rows = $inquiries->map(function (Inquiry $inquiry) use ($timezone): array {
            $tasks = $inquiry->tasks;
            $done = $tasks->whereNotNull('completed_at')->count();
            $taskCount = $tasks->count();
            $progress = $taskCount > 0 ? (int) round($done / $taskCount * 100) : 0;
            $currentTask = $this->currentOpenTask($tasks);
            $lead = $currentTask?->assignee
                ?: $inquiry->owner
                ?: $tasks->sortByDesc('sequence')->first()?->assignee
                ?: $inquiry->creator;
            $product = $inquiry->items->first();
            $attention = $this->attentionLabel($inquiry);
            $created = $inquiry->created_at?->copy()->setTimezone($timezone);
            $displayStatus = $this->inquiryDisplayStatus($inquiry);

            return [
                'id' => (int) $inquiry->id,
                'reference' => (string) $inquiry->inquiry_number,
                'subject' => (string) $inquiry->subject,
                'product' => $product?->item_name ?: ($product?->category ?: 'No structured product'),
                'created' => $created?->format('M j, Y') ?: '—',
                'priority' => trim((string) $inquiry->priority) ?: 'Unspecified',
                'priority_color' => app(MasterDataService::class)->displayColorFor('priority', trim((string) $inquiry->priority) ?: 'Unspecified'),
                'assignee' => $lead?->name ?: 'Unassigned',
                'progress' => $progress,
                'progress_text' => $taskCount > 0 ? $done.' of '.$taskCount.' tasks' : 'No workflow tasks',
                'status' => $displayStatus,
                'status_color' => app(InquiryService::class)->inquiryStatusColor($displayStatus, $currentTask?->status),
                'attention' => $attention['label'],
                'attention_tone' => $attention['tone'],
                'url' => route('inquiries.index', ['open' => $inquiry->id]),
            ];
        })->values()->all();

        $attentionSignals = [
            [
                'key' => 'attention',
                'count' => $attentionCount,
                'label' => $attentionCount === 1 ? 'inquiry requires attention' : 'inquiries require attention',
                'description' => 'Includes inquiry-level attention and open task statuses configured to require attention in Master Data.',
                'tone' => 'amber',
            ],
            [
                'key' => 'unstructured',
                'count' => $unstructuredCount,
                'label' => $unstructuredCount === 1 ? 'inquiry lacks structured product lines' : 'inquiries lack structured product lines',
                'description' => 'Product analysis is limited where requests remain only in descriptions or attachments.',
                'tone' => 'red',
            ],
            [
                'key' => 'overdue',
                'count' => $overdueTasks,
                'label' => $overdueTasks === 1 ? 'open inquiry task is overdue' : 'open inquiry tasks are overdue',
                'description' => 'Calculated from open task due dates in the workspace timezone.',
                'tone' => 'neutral',
            ],
            [
                'key' => 'unassigned',
                'count' => $unassignedOpenTasks,
                'label' => $unassignedOpenTasks === 1 ? 'open inquiry task is unassigned' : 'open inquiry tasks are unassigned',
                'description' => 'Unassigned work follows the same attention logic used by the Inquiry list.',
                'tone' => 'neutral',
            ],
        ];

        $previewLimit = 8;
        $previewRows = array_slice($rows, 0, $previewLimit);

        return [
            'kpis' => [
                'total' => $total,
                'open' => $open,
                'completed' => $completed,
                'task_completion' => $taskTotal > 0 ? round($taskDone / $taskTotal * 100, 1) : 0,
                'task_done' => $taskDone,
                'task_total' => $taskTotal,
                'file_compliance' => $fileCompliance,
                'evidenced' => $submissionWithFiles,
                'evidence_total' => $submissionTasks->count(),
                'structured_products' => $structuredPct,
                'structured_count' => $structuredCount,
            ],
            'trend' => $this->trend($inquiries, $from, $to, $timezone),
            'priority_mix' => $priorityMix,
            'attention' => [
                'attention_count' => $attentionCount,
                'unstructured_count' => $unstructuredCount,
                'overdue_tasks' => $overdueTasks,
                'unassigned_open_tasks' => $unassignedOpenTasks,
                'signals' => $attentionSignals,
            ],
            'rows' => $rows,
            'preview_rows' => $previewRows,
            'preview_count' => count($previewRows),
            'preview_limit' => $previewLimit,
            'row_count' => count($rows),
            'view_all_url' => route('inquiries.index'),
        ];
    }

    private function people(Collection $inquiries, array $reopenedTaskIds, array $assignmentTimes, string $timezone, int $assigneeId = 0): array
    {
        $tasks = $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks)
            ->filter(fn (InquiryTask $task) => $task->assignee_id && $task->assignee)
            ->when($assigneeId > 0, fn (Collection $rows) => $rows->where('assignee_id', $assigneeId)->values())
            ->values();
        $groups = $tasks->groupBy('assignee_id');
        $completedTasks = $tasks->whereNotNull('completed_at')->values();
        $durations = $completedTasks
            ->map(fn (InquiryTask $task) => $this->taskHours($task, $assignmentTimes[(int) $task->id] ?? null))
            ->filter(fn ($hours) => $hours !== null && $hours >= 0)
            ->values();
        $teamMedianHours = $durations->count() ? (float) $durations->median() : null;
        $completedCounts = $groups
            ->map(fn (Collection $rows) => $rows->whereNotNull('completed_at')->count())
            ->filter(fn ($n) => $n > 0);
        $medianCompleted = $completedCounts->count() ? max(1.0, (float) $completedCounts->median()) : 1.0;
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        $ranking = $groups->map(function (Collection $personTasks) use ($reopenedTaskIds, $assignmentTimes, $teamMedianHours, $medianCompleted, $today, $timezone): array {
            /** @var InquiryTask $first */
            $first = $personTasks->first();
            $completed = $personTasks->whereNotNull('completed_at')->values();
            $assigned = $personTasks->count();
            $completedCount = $completed->count();
            $completionPct = $assigned > 0 ? $completedCount / $assigned * 100 : 0;
            $hours = $completed
                ->map(fn (InquiryTask $task) => $this->taskHours($task, $assignmentTimes[(int) $task->id] ?? null))
                ->filter(fn ($value) => $value !== null && $value >= 0)
                ->values();
            $avgHours = $hours->count() ? (float) $hours->avg() : null;
            $dueEligible = $completed->filter(fn (InquiryTask $task) => $task->due_date !== null)->values();
            $onTimeCount = $dueEligible->filter(function (InquiryTask $task) use ($timezone): bool {
                if (!$task->completed_at || !$task->due_date) return false;
                return $task->completed_at->copy()->setTimezone($timezone)->toDateString() <= $task->due_date->toDateString();
            })->count();
            $onTime = $dueEligible->count() > 0 ? $onTimeCount / $dueEligible->count() * 100 : null;
            $reopened = $completed->filter(fn (InquiryTask $task) => in_array((int) $task->id, $reopenedTaskIds, true))->count();
            $reopenPct = $completedCount > 0 ? $reopened / $completedCount * 100 : null;
            $quality = $reopenPct !== null ? max(0, 100 - $reopenPct) : null;
            $open = $personTasks->whereNull('completed_at')->values();
            $overdue = $open->filter(fn (InquiryTask $task) => $task->due_date && $task->due_date->toDateString() < $today)->count();
            $reliability = $open->count() > 0 ? max(0, 100 - ($overdue / $open->count() * 100)) : 100.0;
            $speed = $avgHours !== null && $avgHours > 0 && $teamMedianHours !== null && $teamMedianHours > 0
                ? min(100, $teamMedianHours / $avgHours * 100)
                : null;
            $productivity = $completedCount > 0 ? min(100, $completedCount / $medianCompleted * 100) : 0.0;
            $efficiency = $this->weightedEfficiency($speed, $onTime, $productivity, $quality, $reliability);

            return [
                'id' => (int) $first->assignee_id,
                'name' => (string) $first->assignee->name,
                'initials' => $this->initials((string) $first->assignee->name),
                'assigned' => $assigned,
                'open' => $open->count(),
                'overdue' => $overdue,
                'completed' => $completedCount,
                'completion' => round($completionPct, 1),
                'avg_hours' => $avgHours !== null ? round($avgHours, 1) : null,
                'on_time' => $onTime !== null ? round($onTime, 1) : null,
                'due_samples' => $dueEligible->count(),
                'reopen' => $reopenPct !== null ? round($reopenPct, 1) : null,
                'quality' => $quality !== null ? round($quality, 1) : null,
                'efficiency' => $efficiency,
                'qualified' => $completedCount >= 10,
                'signal' => $this->managementSignal($efficiency, $onTime, $quality, $reopenPct, $completedCount),
            ];
        })->sort(function (array $a, array $b): int {
            if ($a['qualified'] !== $b['qualified']) return $a['qualified'] ? -1 : 1;
            if ($a['efficiency'] !== $b['efficiency']) return $b['efficiency'] <=> $a['efficiency'];
            if ($a['completed'] !== $b['completed']) return $b['completed'] <=> $a['completed'];
            return strcasecmp($a['name'], $b['name']);
        })->values();

        $ranking = $ranking->map(function (array $row, int $index): array {
            $row['rank'] = $index + 1;
            return $row;
        });

        $teamDue = $completedTasks->filter(fn (InquiryTask $task) => $task->due_date !== null)->values();
        $teamOnTime = $teamDue->count() > 0
            ? $teamDue->filter(function (InquiryTask $task) use ($timezone): bool {
                return $task->completed_at
                    && $task->due_date
                    && $task->completed_at->copy()->setTimezone($timezone)->toDateString() <= $task->due_date->toDateString();
            })->count() / $teamDue->count() * 100
            : null;
        $teamReopened = $completedTasks->filter(fn (InquiryTask $task) => in_array((int) $task->id, $reopenedTaskIds, true))->count();
        $teamQuality = $completedTasks->count() > 0
            ? max(0, 100 - ($teamReopened / $completedTasks->count() * 100))
            : null;

        $taskDetails = $tasks
            ->sortByDesc(fn (InquiryTask $task) => $task->completed_at?->timestamp ?? $task->updated_at?->timestamp ?? 0)
            ->take(100)
            ->map(function (InquiryTask $task) use ($reopenedTaskIds, $assignmentTimes, $timezone, $today): array {
                $effectiveStart = $this->taskStartAt($task, $assignmentTimes[(int) $task->id] ?? null);
                $started = $effectiveStart?->copy()->setTimezone($timezone);
                $completed = $task->completed_at?->copy()->setTimezone($timezone);
                $hours = $this->taskHours($task, $assignmentTimes[(int) $task->id] ?? null);
                $reopened = in_array((int) $task->id, $reopenedTaskIds, true);
                $sla = 'No due date';
                $slaTone = 'blue';

                if ($task->due_date) {
                    $dueDate = $task->due_date->toDateString();
                    if (!$task->completed_at) {
                        $sla = $dueDate < $today ? 'Overdue · '.$task->due_date->format('M j') : ($dueDate === $today ? 'Due today' : 'Due '.$task->due_date->format('M j'));
                        $slaTone = $dueDate < $today ? 'red' : 'amber';
                    } elseif ($completed && $completed->toDateString() <= $dueDate) {
                        $sla = 'On time';
                        $slaTone = 'green';
                    } else {
                        $sla = 'Late';
                        $slaTone = 'red';
                    }
                }

                return [
                    'assignee_id' => (int) $task->assignee_id,
                    'assignee' => $task->assignee?->name ?: 'Unassigned',
                    'inquiry_id' => (int) $task->inquiry_id,
                    'inquiry' => $task->inquiry?->inquiry_number ?: '',
                    'inquiry_url' => $task->inquiry_id ? route('inquiries.index', ['open' => $task->inquiry_id, 'task' => $task->id]) : null,
                    'task_id' => (int) $task->id,
                    'task' => (string) $task->title,
                    'status' => (string) $task->status,
                    'started' => $started?->format('M j · g:i A') ?: '—',
                    'completed' => $completed?->format('M j · g:i A') ?: '— Waiting',
                    'hours' => $hours !== null ? $this->formatHours($hours) : 'Open',
                    'hours_value' => $hours,
                    'sla' => $sla,
                    'sla_tone' => $slaTone,
                    'quality' => $reopened ? 'Reopened' : ($task->completed_at ? 'First pass' : 'Pending'),
                    'reopened' => $reopened,
                    'is_open' => !$task->completed_at,
                    'is_completed' => (bool) $task->completed_at,
                    'completed_timestamp' => $task->completed_at?->timestamp ?? 0,
                    'updated_timestamp' => $task->updated_at?->timestamp ?? 0,
                ];
            })->values()->all();

        $conversion = $this->assigneeConversion($inquiries, $ranking, $assigneeId);
        $qualified = $ranking->where('qualified');
        $best = $qualified->first() ?: $ranking->first();
        $throughput = ($qualified->isNotEmpty() ? $qualified : $ranking)->sortByDesc('completed')->first();
        $coaching = ($qualified->isNotEmpty() ? $qualified : $ranking)->sortBy('efficiency')->first();
        $workspaceId = app(SetupContext::class)->workspaceId();
        $roster = User::query()
            ->where('is_active', true)
            ->whereHas('workspaceMemberships', fn ($membership) => $membership
                ->where('workspace_id', $workspaceId)
                ->where('status', 'active'))
            ->count();

        return [
            'kpis' => [
                'roster' => $roster,
                'active' => $groups->count(),
                'assigned' => $tasks->count(),
                'completed' => $completedTasks->count(),
                'completion_rate' => $tasks->count() > 0 ? round($completedTasks->count() / $tasks->count() * 100, 1) : 0,
                'avg_hours' => $durations->count() ? round((float) $durations->avg(), 1) : null,
                'on_time' => $teamOnTime !== null ? round($teamOnTime, 1) : null,
                'on_time_samples' => $teamDue->count(),
                'quality' => $teamQuality !== null ? round($teamQuality, 1) : null,
                'quality_samples' => $completedTasks->count(),
            ],
            'ranking' => $ranking->all(),
            'highlights' => [
                'best' => $best,
                'throughput' => $throughput,
                'coaching' => $coaching,
            ],
            'conversion' => $conversion,
            'task_details' => $taskDetails,
        ];
    }

    private function products(Collection $inquiries): array
    {
        $withProducts = $inquiries->filter(fn (Inquiry $inquiry) => $inquiry->items->isNotEmpty())->values();
        $allItems = $withProducts->flatMap(fn (Inquiry $inquiry) => $inquiry->items->map(function ($item) use ($inquiry) {
            $item->setRelation('inquiry', $inquiry);
            return $item;
        }))->values();
        $categories = $allItems->groupBy(fn ($item) => trim((string) $item->category) ?: 'Uncategorized');
        $totalProductInquiries = $withProducts->count();

        $categoryRows = $categories->map(function (Collection $items, string $category) use ($totalProductInquiries): array {
            $categoryInquiries = $items->pluck('inquiry')->unique('id')->values();
            $completed = $categoryInquiries->filter(fn (Inquiry $inquiry) => $this->isCompleted($inquiry))->values();
            $converted = $categoryInquiries->filter(fn (Inquiry $inquiry) => $this->isConverted($inquiry))->values();
            $hours = $completed
                ->map(fn (Inquiry $inquiry) => $this->inquiryCycleHours($inquiry))
                ->filter(fn ($value) => $value !== null)
                ->values();
            $quantityValues = $items
                ->map(fn ($item) => is_numeric($item->quantity) ? (float) $item->quantity : null)
                ->filter(fn ($value) => $value !== null && $value >= 0)
                ->values();
            $avgQty = $quantityValues->count() ? (float) $quantityValues->avg() : null;
            $conversion = $completed->count() > 0 ? $converted->count() / $completed->count() * 100 : null;
            $avgHours = $hours->count() ? (float) $hours->avg() : null;

            $signal = match (true) {
                $avgHours !== null && $avgHours > 24 => ['label' => 'Slow workflow', 'tone' => 'amber'],
                $conversion !== null && $completed->count() >= 3 && $conversion >= 40 => ['label' => 'Strong conversion', 'tone' => 'green'],
                $categoryInquiries->count() >= max(3, (int) ceil(max(1, $totalProductInquiries) * 0.2)) => ['label' => 'High demand', 'tone' => 'green'],
                $completed->count() === 0 => ['label' => 'Awaiting outcomes', 'tone' => 'blue'],
                default => ['label' => 'Healthy', 'tone' => 'blue'],
            };

            return [
                'category' => $category,
                'sample_product' => (string) ($items->pluck('item_name')->filter()->first() ?: ''),
                'inquiries' => $categoryInquiries->count(),
                'share' => $totalProductInquiries > 0 ? round($categoryInquiries->count() / $totalProductInquiries * 100, 1) : 0,
                'avg_hours' => $avgHours !== null ? round($avgHours, 1) : null,
                'completed' => $completed->count(),
                'converted' => $converted->count(),
                'conversion' => $conversion !== null ? round($conversion, 1) : null,
                'avg_quantity' => $avgQty !== null ? round($avgQty) : null,
                'signal' => $signal,
            ];
        })->sort(function (array $a, array $b): int {
            if ($a['inquiries'] !== $b['inquiries']) return $b['inquiries'] <=> $a['inquiries'];
            return strcasecmp($a['category'], $b['category']);
        })->values();

        $completedInquiries = $withProducts->filter(fn (Inquiry $inquiry) => $this->isCompleted($inquiry))->values();
        $convertedInquiries = $withProducts->filter(fn (Inquiry $inquiry) => $this->isConverted($inquiry))->values();
        $cycleHours = $completedInquiries
            ->map(fn (Inquiry $inquiry) => $this->inquiryCycleHours($inquiry))
            ->filter(fn ($value) => $value !== null)
            ->values();
        $dataCoverage = $inquiries->count() > 0 ? round($withProducts->count() / $inquiries->count() * 100, 1) : 0;
        $topCategory = $categoryRows->first();
        $slowest = $categoryRows->filter(fn (array $row) => $row['avg_hours'] !== null)->sortByDesc('avg_hours')->first();
        $bestConversion = $categoryRows->filter(fn (array $row) => $row['conversion'] !== null)->sortByDesc('conversion')->first();
        $queryThemes = $this->queryThemes($inquiries);
        $maxDemand = max(1, (int) ($categoryRows->max('inquiries') ?? 1));

        return [
            'kpis' => [
                'product_inquiries' => $totalProductInquiries,
                'completed' => $completedInquiries->count(),
                'converted' => $convertedInquiries->count(),
                'conversion' => $completedInquiries->count() > 0 ? round($convertedInquiries->count() / $completedInquiries->count() * 100, 1) : null,
                'avg_quote_hours' => $cycleHours->count() ? round((float) $cycleHours->avg(), 1) : null,
                'data_coverage' => $dataCoverage,
                'top_category' => $topCategory['category'] ?? '—',
                'top_category_share' => $topCategory['share'] ?? 0,
            ],
            'categories' => $categoryRows->take(7)->all(),
            'demand_bars' => $categoryRows->take(5)->map(fn (array $row) => $row + ['width' => (int) round($row['inquiries'] / $maxDemand * 100)])->all(),
            'query_themes' => $queryThemes,
            'insights' => [
                'top' => $topCategory,
                'slowest' => $slowest,
                'best_conversion' => $bestConversion,
                'coverage_gap' => max(0, 100 - $dataCoverage),
            ],
        ];
    }

    private function assigneeConversion(Collection $inquiries, Collection $ranking, int $assigneeId = 0): array
    {
        $byAssignee = [];

        foreach ($inquiries as $inquiry) {
            if (!$this->isCompleted($inquiry)) continue;

            $lead = $inquiry->owner ?: $this->currentOpenTask($inquiry->tasks)?->assignee ?: $inquiry->tasks->sortByDesc('sequence')->first()?->assignee;
            if (!$lead) continue;

            $id = (int) $lead->id;
            if ($assigneeId > 0 && $id !== $assigneeId) continue;
            if (!isset($byAssignee[$id])) {
                $byAssignee[$id] = ['id' => $id, 'name' => $lead->name, 'completed_inquiries' => 0, 'orders' => 0];
            }

            $byAssignee[$id]['completed_inquiries']++;
            if ($this->isConverted($inquiry)) $byAssignee[$id]['orders']++;
        }

        $rows = collect($byAssignee)->map(function (array $row): array {
            $row['conversion'] = $row['completed_inquiries'] > 0 ? round($row['orders'] / $row['completed_inquiries'] * 100, 1) : 0;
            return $row;
        })->sortByDesc('conversion')->values();

        $totalCompleted = (int) $rows->sum('completed_inquiries');
        $totalOrders = (int) $rows->sum('orders');
        $target = $totalCompleted > 0 ? round($totalOrders / $totalCompleted * 100, 1) : 0;

        return $rows->map(function (array $row) use ($target): array {
            $delta = round($row['conversion'] - $target, 1);
            $row['target'] = $target;
            $row['delta'] = $delta;
            $row['tone'] = $row['completed_inquiries'] < 5 ? 'amber' : ($delta >= 0 ? 'green' : ($delta <= -10 ? 'red' : 'amber'));
            $row['interpretation'] = $row['completed_inquiries'] < 5
                ? 'Limited completed-inquiry sample'
                : ($delta >= 5 ? 'Above team conversion' : ($delta < -10 ? 'Review handoff and close reasons' : 'Near team average'));
            return $row;
        })->all();
    }

    private function queryThemes(Collection $inquiries): array
    {
        $definitions = [
            'Personalization / logo' => ['logo','personalization','personalise','personalize','embroidery','printing','print'],
            'Shipping cost' => ['shipping','freight','delivery cost','courier'],
            'Quantity-tier pricing' => ['quantity','qty','pricing','price','tier','moq'],
            'Production lead time' => ['lead time','production time','deadline','delivery date'],
            'Color / material options' => ['color','colour','material','fabric'],
            'Sample request' => ['sample','prototype'],
            'Artwork format' => ['artwork','ai file','eps','vector','design file'],
            'Packaging' => ['packaging','packing','box','bagging'],
        ];
        $texts = $inquiries->map(fn (Inquiry $inquiry) => mb_strtolower(strip_tags(implode(' ', [
            (string) $inquiry->subject,
            (string) $inquiry->requirement_notes,
            $inquiry->items->pluck('notes')->filter()->implode(' '),
        ]))));

        return collect($definitions)->map(function (array $needles, string $label) use ($texts): array {
            $count = $texts->filter(function (string $text) use ($needles): bool {
                foreach ($needles as $needle) if (Str::contains($text, mb_strtolower($needle))) return true;
                return false;
            })->count();
            return ['label' => $label, 'count' => $count];
        })->filter(fn (array $row) => $row['count'] > 0)->sortByDesc('count')->values()->all();
    }

    private function trend(Collection $inquiries, CarbonImmutable $from, CarbonImmutable $to, string $timezone): array
    {
        $span = max(1, $from->diffInSeconds($to));
        $buckets = 7;
        $labels = [];
        $created = array_fill(0, $buckets, 0);
        $completed = array_fill(0, $buckets, 0);

        for ($i = 0; $i < $buckets; $i++) {
            $at = $from->addSeconds((int) floor($span * $i / max(1, $buckets - 1)));
            $labels[] = $at->format('M j');
        }

        $bucketIndex = static function ($date) use ($from, $to, $span, $buckets): ?int {
            if (!$date || $date->lt($from) || $date->gt($to)) return null;
            $offset = max(0, $from->diffInSeconds($date, false));
            return min($buckets - 1, (int) floor(($offset / $span) * $buckets));
        };

        foreach ($inquiries as $inquiry) {
            if ($inquiry->created_at) {
                $local = $inquiry->created_at->copy()->setTimezone($timezone);
                $index = $bucketIndex($local);
                if ($index !== null) $created[$index]++;
            }

            if ($this->isCompleted($inquiry)) {
                $completedAt = $this->inquiryCompletedAt($inquiry);
                if ($completedAt) {
                    $local = $completedAt->copy()->setTimezone($timezone);
                    $index = $bucketIndex($local);
                    if ($index !== null) $completed[$index]++;
                }
            }
        }

        $max = max(1, ...$created, ...$completed);
        $createdPoints = [];
        $completedPoints = [];
        for ($i = 0; $i < $buckets; $i++) {
            $x = round($i * 700 / max(1, $buckets - 1), 1);
            $createdPoints[] = $x.','.round(165 - ($created[$i] / $max * 130), 1);
            $completedPoints[] = $x.','.round(165 - ($completed[$i] / $max * 130), 1);
        }

        return [
            'labels' => $labels,
            'created' => $created,
            'completed' => $completed,
            'created_points' => implode(' ', $createdPoints),
            'completed_points' => implode(' ', $completedPoints),
            'created_fill_points' => '0,180 '.implode(' ', $createdPoints).' 700,180',
        ];
    }

    private function taskAssignmentTimes(array $inquiryIds): array
    {
        if ($inquiryIds === []) return [];

        return Activity::query()
            ->where('subject_type', Inquiry::class)
            ->whereIn('subject_id', $inquiryIds)
            ->where('event', 'inquiry.task_assignee_changed')
            ->orderBy('created_at')
            ->get(['meta', 'created_at'])
            ->filter(fn (Activity $activity) => (int) data_get($activity->meta, 'inquiry_task_id') > 0)
            ->groupBy(fn (Activity $activity) => (int) data_get($activity->meta, 'inquiry_task_id'))
            ->map(fn (Collection $events) => $events->last()?->created_at)
            ->filter()
            ->all();
    }

    private function reopenedTaskIds(array $inquiryIds): array
    {
        if ($inquiryIds === []) return [];

        return Activity::query()
            ->where('subject_type', Inquiry::class)
            ->whereIn('subject_id', $inquiryIds)
            ->where('event', 'inquiry.task_reopened')
            ->get(['meta'])
            ->map(fn (Activity $activity) => (int) data_get($activity->meta, 'inquiry_task_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function statusOptions(User $user, CarbonImmutable $fromUtc, CarbonImmutable $toUtc): array
    {
        $configured = app(InquiryService::class)->inquiryStatusOptions();
        $stored = app(InquiryService::class)->visibleQuery($user)
            ->whereBetween('created_at', [$fromUtc, $toUtc])
            ->whereNotNull('status')
            ->distinct()
            ->pluck('status');

        return $configured
            ->merge($stored)
            ->merge(['Completed', 'Converted', 'Dead'])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn (string $value) => mb_strtolower($value))
            ->values()
            ->all();
    }

    private function priorityOptions(User $user, CarbonImmutable $fromUtc, CarbonImmutable $toUtc): array
    {
        $configured = app(MasterDataService::class)->active('priority')
            ->pluck('name')
            ->map(fn ($value) => trim((string) $value))
            ->filter();
        $stored = app(InquiryService::class)->visibleQuery($user)
            ->whereBetween('created_at', [$fromUtc, $toUtc])
            ->whereNotNull('priority')
            ->distinct()
            ->pluck('priority')
            ->map(fn ($value) => trim((string) $value))
            ->filter();

        return $configured
            ->merge($stored)
            ->unique(fn (string $value) => mb_strtolower($value))
            ->values()
            ->all();
    }

    private function assigneeOptions(User $user, CarbonImmutable $fromUtc, CarbonImmutable $toUtc): array
    {
        $visibleIds = app(InquiryService::class)->visibleQuery($user)
            ->whereBetween('inquiries.created_at', [$fromUtc, $toUtc])
            ->select('inquiries.id');

        $assigneeIds = InquiryTask::query()
            ->whereIn('inquiry_id', clone $visibleIds)
            ->whereNotNull('assignee_id')
            ->select('assignee_id');

        return User::query()
            ->where('is_active', true)
            ->whereIn('id', $assigneeIds)
            ->orderBy('name')
            ->get(['id','name'])
            ->map(fn (User $assignee) => ['id' => (int) $assignee->id, 'name' => (string) $assignee->name])
            ->all();
    }

    private function periodBounds(string $period): array
    {
        $now = app(WorkspaceSettingsService::class)->localNow();
        $period = in_array($period, ['month','30d','qtd','ytd'], true) ? $period : 'month';

        return match ($period) {
            '30d' => [$now->subDays(29)->startOfDay(), $now->endOfDay(), 'Last 30 days'],
            'qtd' => [$now->startOfQuarter()->startOfDay(), $now->endOfDay(), 'Quarter to date'],
            'ytd' => [$now->startOfYear()->startOfDay(), $now->endOfDay(), 'Year to date'],
            default => [$now->startOfMonth()->startOfDay(), $now->endOfDay(), $now->format('F Y')],
        };
    }

    private function inquiryDisplayStatus(Inquiry $inquiry): string
    {
        if ($this->isConverted($inquiry)) return 'Converted';
        if (strcasecmp((string) $inquiry->result, 'Dead') === 0 || strcasecmp((string) $inquiry->status, 'Dead') === 0) return 'Dead';
        if ($this->isCompleted($inquiry)) return 'Completed';
        return trim((string) $inquiry->status) ?: 'In Progress';
    }

    private function isCompleted(Inquiry $inquiry): bool
    {
        if ($this->isConverted($inquiry)) return true;
        if ($inquiry->completed_at || filled($inquiry->result)) return true;

        $status = mb_strtolower(trim((string) $inquiry->status));
        if (in_array($status, ['completed', 'converted', 'closed', 'dead'], true)) return true;

        return $inquiry->tasks->isNotEmpty()
            && $inquiry->tasks->every(fn (InquiryTask $task) => $task->completed_at !== null);
    }

    private function isConverted(Inquiry $inquiry): bool
    {
        return (bool) ($inquiry->converted_job_id || $inquiry->convertedJob || $inquiry->sourceOrder || strcasecmp((string) $inquiry->result, 'Converted') === 0);
    }

    private function currentOpenTask(Collection $tasks): ?InquiryTask
    {
        return $tasks
            ->whereNull('completed_at')
            ->sortBy(function (InquiryTask $task): string {
                $startedBucket = $task->started_at ? '0' : '1';
                $sequence = $task->started_at ? (999999 - (int) $task->sequence) : (int) $task->sequence;
                return $startedBucket.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            })
            ->first();
    }

    private function needsAttention(Inquiry $inquiry): bool
    {
        if ($inquiry->needs_attention) return true;

        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        return $inquiry->tasks->contains(function (InquiryTask $task) use ($today): bool {
            if ($task->completed_at) return false;
            if (!$task->assignee_id) return true;
            if ($task->due_date && $task->due_date->toDateString() < $today) return true;
            return $task->needs_attention || app(InquiryService::class)->taskStatusNeedsAttention((string) $task->status);
        });
    }

    private function attentionLabel(Inquiry $inquiry): array
    {
        if ($this->isCompleted($inquiry)) {
            return ['label' => 'No open task', 'tone' => 'green'];
        }

        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $openTasks = $inquiry->tasks->whereNull('completed_at')->values();

        $overdue = $openTasks->first(fn (InquiryTask $task) => $task->due_date && $task->due_date->toDateString() < $today);
        if ($overdue) return ['label' => 'Overdue', 'tone' => 'red'];

        $attentionTask = $openTasks->first(fn (InquiryTask $task) => $task->needs_attention
            || app(InquiryService::class)->taskStatusNeedsAttention((string) $task->status));
        if ($attentionTask) {
            return ['label' => trim((string) $attentionTask->status) ?: 'Requires attention', 'tone' => 'amber'];
        }

        if ($inquiry->needs_attention) {
            return ['label' => trim((string) $inquiry->attention_reason) ?: 'Requires attention', 'tone' => 'amber'];
        }

        if ($openTasks->contains(fn (InquiryTask $task) => !$task->assignee_id)) {
            return ['label' => 'Unassigned', 'tone' => 'amber'];
        }

        return ['label' => 'On track', 'tone' => 'blue'];
    }

    private function taskHours(InquiryTask $task, $assignmentAt = null): ?float
    {
        if (!$task->completed_at) return null;
        $start = $this->taskStartAt($task, $assignmentAt);
        if (!$start) return null;
        return max(0, $start->diffInSeconds($task->completed_at) / 3600);
    }

    private function taskStartAt(InquiryTask $task, $assignmentAt = null)
    {
        $candidates = collect([$task->started_at, $assignmentAt])->filter();
        if ($candidates->isNotEmpty()) {
            return $candidates->sortByDesc(fn ($date) => $date->timestamp)->first();
        }
        return $task->created_at;
    }

    private function inquiryCycleHours(Inquiry $inquiry): ?float
    {
        $end = $this->inquiryCompletedAt($inquiry);
        $start = $inquiry->started_at ?: $inquiry->created_at;
        if (!$start || !$end) return null;
        return max(0, $start->diffInSeconds($end) / 3600);
    }

    private function inquiryCompletedAt(Inquiry $inquiry)
    {
        if ($inquiry->completed_at) return $inquiry->completed_at;
        if ($inquiry->convertedJob?->created_at) return $inquiry->convertedJob->created_at;
        if ($inquiry->sourceOrder?->created_at) return $inquiry->sourceOrder->created_at;

        $taskCompletedAt = $inquiry->tasks->whereNotNull('completed_at')->max('completed_at');
        if ($taskCompletedAt && $inquiry->tasks->isNotEmpty() && $inquiry->tasks->every(fn (InquiryTask $task) => $task->completed_at !== null)) {
            return $taskCompletedAt;
        }

        if (filled($inquiry->result) || in_array(mb_strtolower(trim((string) $inquiry->status)), ['completed', 'converted', 'closed', 'dead'], true)) {
            return $inquiry->updated_at;
        }

        return null;
    }

    private function formatHours(float $hours): string
    {
        $minutes = (int) round($hours * 60);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($h <= 0) return $m.'m';
        return $h.'h '.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'m';
    }

    private function weightedEfficiency(?float $speed, ?float $onTime, float $productivity, ?float $quality, float $reliability): int
    {
        $components = [
            [$speed, 30],
            [$onTime, 25],
            [$productivity, 20],
            [$quality, 15],
            [$reliability, 10],
        ];
        $weighted = 0.0;
        $weight = 0.0;

        foreach ($components as [$value, $componentWeight]) {
            if ($value === null) continue;
            $weighted += max(0, min(100, (float) $value)) * $componentWeight;
            $weight += $componentWeight;
        }

        return $weight > 0 ? (int) round($weighted / $weight) : 0;
    }

    private function managementSignal(int $efficiency, ?float $onTime, ?float $quality, ?float $reopen, int $completed): array
    {
        if ($completed < 10) return ['label' => 'Insufficient data', 'tone' => 'blue'];
        if ($efficiency >= 90 && ($onTime === null || $onTime >= 95) && ($quality === null || $quality >= 95) && $completed >= 20) {
            return ['label' => 'Top performer', 'tone' => 'green'];
        }
        if (($reopen !== null && $reopen > 15) || $efficiency < 50) return ['label' => 'Immediate review', 'tone' => 'red'];
        if ($efficiency < 60) return ['label' => 'Coaching priority', 'tone' => 'red'];
        if (($quality !== null && $quality < 92) || ($reopen !== null && $reopen > 8)) return ['label' => 'Quality watch', 'tone' => 'amber'];
        if ($onTime !== null && $onTime < 90) return ['label' => 'SLA watch', 'tone' => 'amber'];
        if ($efficiency >= 85) return ['label' => 'Strong execution', 'tone' => 'green'];
        return ['label' => 'On track', 'tone' => 'blue'];
    }

    private function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'U';
    }

    private function authorize(User $user): void
    {
        abort_unless(app(AccessControlService::class)->can($user, 'reports', 'view'), 403);
    }
}
