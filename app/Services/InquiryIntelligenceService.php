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
                $match->where('inquiry_number', 'like', $like)
                    ->orWhere('reference_number', 'like', $like)
                    ->orWhere('subject', 'like', $like)
                    ->orWhere('requirement_notes', 'like', $like)
                    ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $like))
                    ->orWhereHas('items', fn (Builder $item) => $item->where('item_name', 'like', $like)->orWhere('category', 'like', $like))
                    ->orWhereHas('tasks.assignee', fn (Builder $assignee) => $assignee->where('name', 'like', $like));
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

        $portfolio = $this->portfolio($inquiries, $reopenedTaskIds, $fromLocal, $toLocal, $timezone);
        $people = $this->people($inquiries, $reopenedTaskIds, $timezone, $assigneeId);
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
        $tasks = $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks);
        $completedTasks = $tasks->whereNotNull('completed_at');
        $taskTotal = $tasks->count();
        $taskDone = $completedTasks->count();
        $submissionTasks = $completedTasks->where('requires_submission', true);
        $submissionWithFiles = $submissionTasks->filter(fn (InquiryTask $task) => $task->documents->isNotEmpty())->count();
        $fileCompliance = $submissionTasks->count() > 0 ? (int) round($submissionWithFiles / $submissionTasks->count() * 100) : 100;
        $structuredCount = $inquiries->filter(fn (Inquiry $inquiry) => $inquiry->items->isNotEmpty())->count();
        $structuredPct = $total > 0 ? (int) round($structuredCount / $total * 100) : 0;
        $attentionCount = $inquiries->filter(fn (Inquiry $inquiry) => $this->needsAttention($inquiry))->count();
        $unstructuredCount = max(0, $total - $structuredCount);
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();
        $overdueTasks = $tasks->filter(fn (InquiryTask $task) => !$task->completed_at && $task->due_date && $task->due_date->toDateString() < $today)->count();

        $priorityMix = $inquiries->groupBy(fn (Inquiry $inquiry) => trim((string) $inquiry->priority) ?: 'Unspecified')
            ->map->count()->sortDesc()->take(3);
        $priorityMax = max(1, (int) ($priorityMix->max() ?? 1));

        $rows = $inquiries->map(function (Inquiry $inquiry) use ($timezone): array {
            $tasks = $inquiry->tasks;
            $done = $tasks->whereNotNull('completed_at')->count();
            $taskCount = $tasks->count();
            $progress = $taskCount > 0 ? (int) round($done / $taskCount * 100) : 0;
            $openTasks = $tasks->whereNull('completed_at');
            $currentTask = $openTasks->sortBy(function (InquiryTask $task): string {
                return ($task->started_at ? '0' : '1').str_pad((string) (999999 - (int) $task->sequence), 6, '0', STR_PAD_LEFT);
            })->first();
            $lead = $currentTask?->assignee ?: $inquiry->owner ?: $tasks->sortByDesc('sequence')->first()?->assignee;
            $product = $inquiry->items->first();
            $attention = $this->attentionLabel($inquiry);
            $created = $inquiry->created_at?->copy()->setTimezone($timezone);

            return [
                'reference' => (string) $inquiry->inquiry_number,
                'subject' => (string) $inquiry->subject,
                'product' => $product?->item_name ?: ($product?->category ?: 'No structured product'),
                'created' => $created?->format('M j, Y') ?: '—',
                'priority' => trim((string) $inquiry->priority) ?: 'Unspecified',
                'assignee' => $lead?->name ?: 'Unassigned',
                'progress' => $progress,
                'progress_text' => $taskCount > 0 ? $done.' of '.$taskCount.' tasks' : 'No workflow tasks',
                'status' => $this->inquiryDisplayStatus($inquiry),
                'attention' => $attention['label'],
                'attention_tone' => $attention['tone'],
            ];
        })->values()->all();

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
            'priority_mix' => $priorityMix->map(fn ($count, $name) => [
                'name' => $name,
                'count' => $count,
                'width' => (int) round($count / $priorityMax * 100),
            ])->values()->all(),
            'attention' => [
                'attention_count' => $attentionCount,
                'unstructured_count' => $unstructuredCount,
                'overdue_tasks' => $overdueTasks,
            ],
            'rows' => $rows,
            'row_count' => count($rows),
        ];
    }

    private function people(Collection $inquiries, array $reopenedTaskIds, string $timezone, int $assigneeId = 0): array
    {
        $tasks = $inquiries->flatMap(fn (Inquiry $inquiry) => $inquiry->tasks)
            ->filter(fn (InquiryTask $task) => $task->assignee_id && $task->assignee)
            ->when($assigneeId > 0, fn (Collection $rows) => $rows->where('assignee_id', $assigneeId)->values());
        $groups = $tasks->groupBy('assignee_id');
        $completedTasks = $tasks->whereNotNull('completed_at');
        $durations = $completedTasks->map(fn (InquiryTask $task) => $this->taskHours($task))->filter(fn ($hours) => $hours !== null && $hours >= 0);
        $teamMedianHours = (float) ($durations->median() ?? 0);
        $completedCounts = $groups->map(fn (Collection $rows) => $rows->whereNotNull('completed_at')->count())->filter(fn ($n) => $n > 0);
        $medianCompleted = max(1.0, (float) ($completedCounts->median() ?? 1));
        $today = app(WorkspaceSettingsService::class)->localToday()->toDateString();

        $ranking = $groups->map(function (Collection $personTasks) use ($reopenedTaskIds, $teamMedianHours, $medianCompleted, $today): array {
            /** @var InquiryTask $first */
            $first = $personTasks->first();
            $completed = $personTasks->whereNotNull('completed_at');
            $assigned = $personTasks->count();
            $completedCount = $completed->count();
            $completionPct = $assigned > 0 ? $completedCount / $assigned * 100 : 0;
            $hours = $completed->map(fn (InquiryTask $task) => $this->taskHours($task))->filter(fn ($value) => $value !== null && $value >= 0);
            $avgHours = $hours->count() ? (float) $hours->avg() : 0.0;
            $dueEligible = $completed->filter(fn (InquiryTask $task) => $task->due_date !== null);
            $onTimeCount = $dueEligible->filter(fn (InquiryTask $task) => $task->completed_at?->toDateString() <= $task->due_date?->toDateString())->count();
            $onTime = $dueEligible->count() > 0 ? $onTimeCount / $dueEligible->count() * 100 : $completionPct;
            $reopened = $completed->filter(fn (InquiryTask $task) => in_array((int) $task->id, $reopenedTaskIds, true))->count();
            $reopenPct = $completedCount > 0 ? $reopened / $completedCount * 100 : 0;
            $quality = max(0, 100 - $reopenPct);
            $open = $personTasks->whereNull('completed_at');
            $overdue = $open->filter(fn (InquiryTask $task) => $task->due_date && $task->due_date->toDateString() < $today)->count();
            $reliability = $open->count() > 0 ? max(0, 100 - ($overdue / $open->count() * 100)) : 100;
            $speed = $avgHours > 0 && $teamMedianHours > 0 ? min(100, $teamMedianHours / $avgHours * 100) : $completionPct;
            $productivity = min(100, $completedCount / $medianCompleted * 100);
            $efficiency = round(0.30 * $speed + 0.25 * $onTime + 0.20 * $productivity + 0.15 * $quality + 0.10 * $reliability);

            return [
                'id' => (int) $first->assignee_id,
                'name' => (string) $first->assignee->name,
                'initials' => $this->initials((string) $first->assignee->name),
                'assigned' => $assigned,
                'completed' => $completedCount,
                'completion' => round($completionPct, 1),
                'avg_hours' => round($avgHours, 1),
                'on_time' => round($onTime, 1),
                'reopen' => round($reopenPct, 1),
                'quality' => round($quality, 1),
                'efficiency' => (int) $efficiency,
                'qualified' => $completedCount >= 10,
                'signal' => $this->managementSignal((int) $efficiency, $onTime, $quality, $reopenPct, $completedCount),
            ];
        })->sort(function (array $a, array $b): int {
            if ($a['qualified'] !== $b['qualified']) return $a['qualified'] ? -1 : 1;
            if ($a['efficiency'] !== $b['efficiency']) return $b['efficiency'] <=> $a['efficiency'];
            return $b['completed'] <=> $a['completed'];
        })->values();

        $ranking = $ranking->map(function (array $row, int $index): array {
            $row['rank'] = $index + 1;
            return $row;
        });

        $completed = $tasks->whereNotNull('completed_at');
        $teamDue = $completed->filter(fn (InquiryTask $task) => $task->due_date !== null);
        $teamOnTime = $teamDue->count() > 0
            ? $teamDue->filter(fn (InquiryTask $task) => $task->completed_at?->toDateString() <= $task->due_date?->toDateString())->count() / $teamDue->count() * 100
            : 0;
        $teamReopened = $completed->filter(fn (InquiryTask $task) => in_array((int) $task->id, $reopenedTaskIds, true))->count();
        $teamQuality = $completed->count() > 0 ? max(0, 100 - ($teamReopened / $completed->count() * 100)) : 100;

        $taskDetails = $tasks->sortByDesc(fn (InquiryTask $task) => $task->completed_at?->timestamp ?? $task->updated_at?->timestamp ?? 0)
            ->take(80)
            ->map(function (InquiryTask $task) use ($reopenedTaskIds, $timezone): array {
                $started = ($task->started_at ?: $task->created_at)?->copy()->setTimezone($timezone);
                $completed = $task->completed_at?->copy()->setTimezone($timezone);
                $hours = $this->taskHours($task);
                $reopened = in_array((int) $task->id, $reopenedTaskIds, true);
                $sla = 'No due date';
                $slaTone = 'blue';
                if ($task->due_date) {
                    if (!$task->completed_at) {
                        $sla = 'Due '.$task->due_date->format('M j');
                        $slaTone = $task->due_date->isPast() ? 'red' : 'amber';
                    } elseif ($task->completed_at->toDateString() <= $task->due_date->toDateString()) {
                        $sla = 'On time';
                        $slaTone = 'green';
                    } else {
                        $sla = 'Late';
                        $slaTone = 'red';
                    }
                }

                return [
                    'assignee' => $task->assignee?->name ?: 'Unassigned',
                    'inquiry' => $task->inquiry?->inquiry_number ?: '',
                    'task' => (string) $task->title,
                    'started' => $started?->format('M j · g:i A') ?: '—',
                    'completed' => $completed?->format('M j · g:i A') ?: '— Waiting',
                    'hours' => $hours !== null ? $this->formatHours($hours) : 'Open',
                    'hours_value' => $hours,
                    'sla' => $sla,
                    'sla_tone' => $slaTone,
                    'quality' => $reopened ? 'Reopened' : ($task->completed_at ? 'First pass' : 'Pending'),
                    'reopened' => $reopened,
                    'is_open' => !$task->completed_at,
                ];
            })->values()->all();

        $conversion = $this->assigneeConversion($inquiries, $ranking, $assigneeId);
        $qualified = $ranking->where('qualified');
        $best = $qualified->first() ?: $ranking->first();
        $throughput = $qualified->sortByDesc('completed')->first() ?: $ranking->sortByDesc('completed')->first();
        $coaching = $qualified->sortBy('efficiency')->first() ?: $ranking->sortBy('efficiency')->first();

        return [
            'kpis' => [
                'roster' => User::query()->where('is_active', true)->count(),
                'active' => $groups->count(),
                'assigned' => $tasks->count(),
                'completed' => $completed->count(),
                'completion_rate' => $tasks->count() > 0 ? round($completed->count() / $tasks->count() * 100, 1) : 0,
                'avg_hours' => $durations->count() ? round((float) $durations->avg(), 1) : 0,
                'on_time' => round($teamOnTime, 1),
                'quality' => round($teamQuality, 1),
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
        $withProducts = $inquiries->filter(fn (Inquiry $inquiry) => $inquiry->items->isNotEmpty());
        $allItems = $withProducts->flatMap(fn (Inquiry $inquiry) => $inquiry->items->map(function ($item) use ($inquiry) {
            $item->setRelation('inquiry', $inquiry);
            return $item;
        }));
        $categories = $allItems->groupBy(fn ($item) => trim((string) $item->category) ?: 'Uncategorized');
        $totalProductInquiries = $withProducts->count();

        $categoryRows = $categories->map(function (Collection $items, string $category) use ($totalProductInquiries): array {
            $categoryInquiries = $items->pluck('inquiry')->unique('id')->values();
            $completed = $categoryInquiries->filter(fn (Inquiry $inquiry) => $this->isCompleted($inquiry));
            $converted = $categoryInquiries->filter(fn (Inquiry $inquiry) => $this->isConverted($inquiry));
            $hours = $completed->map(fn (Inquiry $inquiry) => $this->inquiryCycleHours($inquiry))->filter(fn ($value) => $value !== null);
            $avgQty = $items->avg(fn ($item) => (float) $item->quantity);
            $conversion = $completed->count() > 0 ? $converted->count() / $completed->count() * 100 : 0;
            $signal = match (true) {
                $converted->count() > 0 && $conversion >= 40 => ['label' => 'Best conversion', 'tone' => 'green'],
                $categoryInquiries->count() >= max(3, (int) ceil($totalProductInquiries * 0.2)) => ['label' => 'High demand', 'tone' => 'green'],
                $hours->count() && $hours->avg() > 24 => ['label' => 'Slow quote', 'tone' => 'amber'],
                default => ['label' => 'Healthy', 'tone' => 'blue'],
            };

            return [
                'category' => $category,
                'sample_product' => (string) ($items->pluck('item_name')->filter()->first() ?: ''),
                'inquiries' => $categoryInquiries->count(),
                'share' => $totalProductInquiries > 0 ? round($categoryInquiries->count() / $totalProductInquiries * 100) : 0,
                'avg_hours' => $hours->count() ? round((float) $hours->avg(), 1) : 0,
                'completed' => $completed->count(),
                'conversion' => round($conversion, 1),
                'avg_quantity' => $avgQty !== null ? round((float) $avgQty) : null,
                'signal' => $signal,
            ];
        })->sortByDesc('inquiries')->values();

        $completedInquiries = $withProducts->filter(fn (Inquiry $inquiry) => $this->isCompleted($inquiry));
        $convertedInquiries = $withProducts->filter(fn (Inquiry $inquiry) => $this->isConverted($inquiry));
        $cycleHours = $completedInquiries->map(fn (Inquiry $inquiry) => $this->inquiryCycleHours($inquiry))->filter(fn ($value) => $value !== null);
        $dataCoverage = $inquiries->count() > 0 ? round($withProducts->count() / $inquiries->count() * 100) : 0;
        $topCategory = $categoryRows->first();
        $slowest = $categoryRows->sortByDesc('avg_hours')->first();
        $bestConversion = $categoryRows->sortByDesc('conversion')->first();
        $queryThemes = $this->queryThemes($inquiries);
        $maxDemand = max(1, (int) ($categoryRows->max('inquiries') ?? 1));

        return [
            'kpis' => [
                'product_inquiries' => $totalProductInquiries,
                'completed' => $completedInquiries->count(),
                'converted' => $convertedInquiries->count(),
                'conversion' => $completedInquiries->count() > 0 ? round($convertedInquiries->count() / $completedInquiries->count() * 100, 1) : 0,
                'avg_quote_hours' => $cycleHours->count() ? round((float) $cycleHours->avg(), 1) : 0,
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
            $lead = $inquiry->owner ?: $inquiry->tasks->sortByDesc('sequence')->first()?->assignee;
            if (!$lead) continue;
            $id = (int) $lead->id;
            if ($assigneeId > 0 && $id !== $assigneeId) continue;
            if (!isset($byAssignee[$id])) $byAssignee[$id] = ['id' => $id, 'name' => $lead->name, 'completed_inquiries' => 0, 'orders' => 0];
            if ($this->isCompleted($inquiry)) $byAssignee[$id]['completed_inquiries']++;
            if ($this->isConverted($inquiry)) $byAssignee[$id]['orders']++;
        }

        $rows = collect($byAssignee)->map(function (array $row): array {
            $row['conversion'] = $row['completed_inquiries'] > 0 ? round($row['orders'] / $row['completed_inquiries'] * 100, 1) : 0;
            return $row;
        })->sortByDesc('conversion')->values();
        $target = $rows->count() ? round((float) $rows->avg('conversion'), 1) : 0;

        return $rows->map(function (array $row) use ($target): array {
            $delta = round($row['conversion'] - $target, 1);
            $row['target'] = $target;
            $row['delta'] = $delta;
            $row['tone'] = $row['completed_inquiries'] < 5 ? 'amber' : ($delta >= 0 ? 'green' : ($delta <= -10 ? 'red' : 'amber'));
            $row['interpretation'] = $row['completed_inquiries'] < 5 ? 'Limited completed-inquiry sample' : ($delta >= 5 ? 'Above team conversion' : ($delta < -10 ? 'Review handoff and close reasons' : 'Near team average'));
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
            $labels[] = $at->format($span > 90 * 86400 ? 'M j' : 'M j');
        }

        foreach ($inquiries as $inquiry) {
            if ($inquiry->created_at) {
                $local = $inquiry->created_at->copy()->setTimezone($timezone);
                $index = min($buckets - 1, max(0, (int) floor($from->diffInSeconds($local, false) / $span * $buckets)));
                $created[$index]++;
            }
            if ($this->isCompleted($inquiry)) {
                $completedAt = $inquiry->completed_at ?: $inquiry->tasks->max('completed_at');
                if ($completedAt) {
                    $local = $completedAt->copy()->setTimezone($timezone);
                    if ($local->between($from, $to)) {
                        $index = min($buckets - 1, max(0, (int) floor($from->diffInSeconds($local, false) / $span * $buckets)));
                        $completed[$index]++;
                    }
                }
            }
        }

        $max = max(1, ...$created, ...$completed);
        $createdPoints = [];
        $completedPoints = [];
        for ($i = 0; $i < $buckets; $i++) {
            $x = round($i * 700 / ($buckets - 1), 1);
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
        $stored = app(InquiryService::class)->visibleQuery($user)
            ->whereBetween('created_at', [$fromUtc, $toUtc])
            ->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return $stored->merge(['Completed','Converted','Dead'])->map(fn ($value) => trim((string) $value))->filter()->unique()->values()->all();
    }

    private function priorityOptions(User $user, CarbonImmutable $fromUtc, CarbonImmutable $toUtc): array
    {
        return app(InquiryService::class)->visibleQuery($user)
            ->whereBetween('created_at', [$fromUtc, $toUtc])
            ->whereNotNull('priority')->distinct()->orderBy('priority')->pluck('priority')
            ->map(fn ($value) => trim((string) $value))->filter()->values()->all();
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
        if ($inquiry->completed_at || $inquiry->result) return true;
        return $inquiry->tasks->isNotEmpty() && $inquiry->tasks->every(fn (InquiryTask $task) => $task->completed_at !== null);
    }

    private function isConverted(Inquiry $inquiry): bool
    {
        return (bool) ($inquiry->converted_job_id || $inquiry->convertedJob || $inquiry->sourceOrder || strcasecmp((string) $inquiry->result, 'Converted') === 0);
    }

    private function needsAttention(Inquiry $inquiry): bool
    {
        if ($inquiry->needs_attention) return true;
        return $inquiry->tasks->contains(fn (InquiryTask $task) => !$task->completed_at && ($task->needs_attention || app(InquiryService::class)->taskStatusNeedsAttention((string) $task->status)));
    }

    private function attentionLabel(Inquiry $inquiry): array
    {
        if ($this->needsAttention($inquiry)) {
            $task = $inquiry->tasks->first(fn (InquiryTask $task) => !$task->completed_at && ($task->needs_attention || app(InquiryService::class)->taskStatusNeedsAttention((string) $task->status)));
            return ['label' => $task?->status ?: ($inquiry->attention_reason ?: 'Requires attention'), 'tone' => 'amber'];
        }
        if ($this->isCompleted($inquiry)) return ['label' => 'No open task', 'tone' => 'green'];
        return ['label' => 'On track', 'tone' => 'blue'];
    }

    private function taskHours(InquiryTask $task): ?float
    {
        if (!$task->completed_at) return null;
        $start = $task->started_at ?: $task->created_at;
        if (!$start) return null;
        return max(0, $start->diffInSeconds($task->completed_at) / 3600);
    }

    private function inquiryCycleHours(Inquiry $inquiry): ?float
    {
        $end = $inquiry->completed_at ?: $inquiry->tasks->max('completed_at');
        $start = $inquiry->started_at ?: $inquiry->created_at;
        if (!$start || !$end) return null;
        return max(0, $start->diffInSeconds($end) / 3600);
    }

    private function formatHours(float $hours): string
    {
        $minutes = (int) round($hours * 60);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($h <= 0) return $m.'m';
        return $h.'h '.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'m';
    }

    private function managementSignal(int $efficiency, float $onTime, float $quality, float $reopen, int $completed): array
    {
        if ($completed < 10) return ['label' => 'Insufficient data', 'tone' => 'blue'];
        if ($efficiency >= 90 && $onTime >= 95 && $quality >= 95 && $completed >= 20) return ['label' => 'Top performer', 'tone' => 'green'];
        if ($reopen > 15 || $efficiency < 50) return ['label' => 'Immediate review', 'tone' => 'red'];
        if ($efficiency < 60) return ['label' => 'Coaching priority', 'tone' => 'red'];
        if ($quality < 92 || $reopen > 8) return ['label' => 'Quality watch', 'tone' => 'amber'];
        if ($onTime < 90) return ['label' => 'SLA watch', 'tone' => 'amber'];
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
