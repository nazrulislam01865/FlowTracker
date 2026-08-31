<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('flowtrack:sync-legacy', function (): int {
    $this->info('Synchronizing legacy Master Data...');
    app(\App\Services\MasterDataService::class)->syncLegacy();

    $this->info('Synchronizing legacy Task Packs...');
    app(\App\Services\TaskPackService::class)->syncLegacy();

    $this->info('Synchronizing legacy Workflows...');
    app(\App\Services\WorkflowService::class)->syncLegacy();

    $this->info('Legacy synchronization completed.');
    return 0;
})->purpose('Run legacy compatibility synchronization explicitly outside web requests');

Artisan::command('flowtrack:sync-order-flags', function (): int {
    $count = app(\App\Services\OrderTaskFlagService::class)->syncDueTransitions(true);
    $this->info('Order task/order flags synchronized for '.$count.' due-date transition(s).');

    return 0;
})->purpose('Persist automatic overdue Order Task Flags and parent Order Flags');

// Due-date flags must change even when nobody edits the task. The normal web
// paths also run a five-minute bounded sync, while the scheduler guarantees the
// persisted values are refreshed independently of page traffic.
Schedule::command('flowtrack:sync-order-flags')->hourly()->withoutOverlapping()->onOneServer();


Artisan::command('flowtrack:send-rfq-reminders', function (): int {
    $result = app(\App\Services\Inquiries\InquiryRfqService::class)->sendDueReminders();
    $this->info('RFQ due-date reminders sent: '.(int) $result['sent'].'. Failed: '.(int) $result['failed'].'.');
    return (int) $result['failed'] > 0 ? 1 : 0;
})->purpose('Send supplier RFQ reminders at each inquiry configured reminder window');

Schedule::command('flowtrack:send-rfq-reminders')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Artisan::command('flowtrack:performance:explain {--user=1 : User ID used for assignee/member query plans}', function (): int {
    $userId = max(1, (int) $this->option('user'));
    $driver = DB::connection()->getDriverName();
    $prefix = $driver === 'sqlite' ? 'EXPLAIN QUERY PLAN ' : 'EXPLAIN ';

    $queries = [
        'Open Jobs for owner / delivery' => [
            'flow_jobs', 'ft_jobs_owner_open_due_idx',
            "select id from flow_jobs where owner_id = ? and deleted_at is null and completed_at is null order by delivery_date asc limit 60",
            [$userId],
        ],
        'Open Jobs in workflow phase' => [
            'flow_jobs', 'ft_jobs_phase_open_idx',
            "select id from flow_jobs where workflow_phase_id = ? and deleted_at is null and completed_at is null order by id desc limit 60",
            [1],
        ],
        'Attention Jobs ordered by update' => [
            'flow_jobs', 'ft_jobs_attention_updated_idx',
            "select id from flow_jobs where attention_requested = ? and completed_at is null order by updated_at desc limit 60",
            [1],
        ],
        'Open Tasks for Job / due date' => [
            'tasks', 'ft_tasks_job_open_due_idx',
            "select id from tasks where flow_job_id = ? and deleted_at is null and completed_at is null order by due_date asc limit 60",
            [1],
        ],
        'Open Tasks for workflow phase' => [
            'tasks', 'ft_tasks_phase_open_due_idx',
            "select id from tasks where workflow_phase_id = ? and deleted_at is null and completed_at is null order by due_date asc limit 60",
            [1],
        ],
        'Open Inquiries in workspace' => [
            'inquiries', 'ft_inquiries_workspace_open_updated_idx',
            "select id from inquiries where workspace_id = ? and deleted_at is null and completed_at is null order by updated_at desc limit 60",
            [1],
        ],
        'Open Inquiries for owner' => [
            'inquiries', 'ft_inquiries_owner_open_idx',
            "select id from inquiries where workspace_id = ? and owner_id = ? and deleted_at is null and completed_at is null limit 60",
            [1, $userId],
        ],
        'Client Inquiries ordered by update' => [
            'inquiries', 'ft_inquiries_client_updated_idx',
            "select id from inquiries where workspace_id = ? and client_id = ? and deleted_at is null order by updated_at desc limit 60",
            [1, 1],
        ],
        'Open Inquiry Tasks in sequence' => [
            'inquiry_tasks', 'ft_inquiry_tasks_parent_open_seq_idx',
            "select id from inquiry_tasks where inquiry_id = ? and deleted_at is null and completed_at is null order by sequence asc limit 60",
            [1],
        ],
        'Open Inquiry Tasks for assignee' => [
            'inquiry_tasks', 'ft_inquiry_tasks_assignee_open_due_idx',
            "select id from inquiry_tasks where assignee_id = ? and deleted_at is null and completed_at is null order by due_date asc limit 60",
            [$userId],
        ],
        'Inquiry Documents by parent update' => [
            'inquiry_documents', 'ft_inquiry_documents_parent_updated_idx',
            "select id from inquiry_documents where inquiry_id = ? order by updated_at desc limit 60",
            [1],
        ],
        'Active non-archived Clients' => [
            'clients', 'ft_clients_active_archived_name_idx',
            "select id from clients where is_active = ? and archived_at is null order by name limit 60",
            [1],
        ],
        // Existing indexes are intentionally re-used instead of being duplicated
        // by Phase 11. Keep representative checks for those query families too.
        'Unread notifications (existing index)' => [
            'flow_notifications', 'ft_notifications_user_read_created_idx',
            "select id from flow_notifications where user_id = ? and read_at is null order by created_at desc limit 30",
            [$userId],
        ],
        'Recent subject activity (existing index)' => [
            'activities', 'ft_activities_subject_created_idx',
            "select id from activities where subject_type = ? and subject_id = ? order by created_at desc limit 30",
            ['App\\Models\\FlowJob', 1],
        ],
        'Active Master Data ordering (existing index)' => [
            'master_records', 'ft_master_active_deleted_sort_idx',
            "select id from master_records where workspace_id = ? and type = ? and status = ? and deleted_at is null order by sort_order, name",
            [1, 'order_task_status', 'active'],
        ],
    ];
    foreach ($queries as $label => [$table, $expectedIndex, $sql, $bindings]) {
        if (!Schema::hasTable($table)) {
            $this->warn($label.': skipped because '.$table.' does not exist.');
            continue;
        }

        $this->newLine();
        $this->info($label);
        $rows = collect(DB::select($prefix.$sql, $bindings))->map(fn ($row) => (array) $row);
        if ($rows->isEmpty()) {
            $this->line('No EXPLAIN rows returned.');
            continue;
        }
        $this->table(array_keys($rows->first()), $rows->map(fn ($row) => array_values($row))->all());
        $chosenKeys = $rows->pluck('key')->filter()->map('strval')->all();
        $this->line('Expected index: '.$expectedIndex);
        if ($driver !== 'sqlite' && $chosenKeys !== [] && ! in_array($expectedIndex, $chosenKeys, true)) {
            $this->warn('Optimizer selected a different index. Review rows/access type before deciding whether the expected index is useful.');
        }
    }

    $this->newLine();
    $this->comment('Review the chosen key/access type/rows against each expected index on a representative production-like dataset.');

    return 0;
})->purpose('Run EXPLAIN against FlowTrack high-frequency query shapes');

// Rejected/failed uploads are never promoted to business records. Keep them
// private for a short operator-review window, then purge them automatically.
Schedule::command('flowtrack:purge-document-quarantine')->dailyAt('02:30')->withoutOverlapping()->onOneServer();


// Phase 14: queue depth monitoring is deliberately scheduler-owned so web
// requests never spend time inspecting worker state.
if ((bool) config('scalability.queues.monitor_enabled', true)) {
    $connection = (string) config('queue.default', 'database');
    $queues = (array) config('scalability.queues.names', ['realtime', 'notifications', 'default']);
    $targets = collect($queues)->filter()->map(fn ($queue) => $connection.':'.$queue)->implode(',');
    if ($targets !== '') {
        Schedule::command('queue:monitor '.$targets.' --max='.(int) config('scalability.queues.max_depth', 100))
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();
    }
}

if ((bool) config('scalability.backup.enabled', false)) {
    Schedule::command('flowtrack:database:backup')
        ->dailyAt((string) config('scalability.backup.schedule', '01:30'))
        ->withoutOverlapping()
        ->onOneServer();
}

// Phase 15: Redis-backed operational snapshot/alert surface. This stays CLI
// only so observability does not add another authenticated web UI or expose
// internal metrics through a public endpoint.
Artisan::command('flowtrack:observability:snapshot {--minutes= : Rolling window in minutes} {--json : Emit machine-readable JSON}', function (): int {
    $minutes = $this->option('minutes');
    $snapshot = app(\App\Services\Observability\OperationsMetrics::class)
        ->snapshot($minutes !== null ? max(1, (int) $minutes) : null);

    if ($this->option('json')) {
        $this->line((string) json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return ($snapshot['available'] ?? true) ? 0 : 2;
    }

    $this->info('FlowTrack observability snapshot ('.$snapshot['window_minutes'].' minute window)');
    $this->table(['Metric', 'Value'], [
        ['Requests', $snapshot['requests'] ?? 0],
        ['HTTP 5xx rate', ($snapshot['http_error_rate_percent'] ?? 0).'%'],
        ['Request p95', data_get($snapshot, 'request_ms.p95', 0).' ms'],
        ['Query-time p95', data_get($snapshot, 'query_time_ms.p95', 0).' ms'],
        ['Memory p95', data_get($snapshot, 'memory_peak_mb.p95', 0).' MB'],
        ['Slow queries', $snapshot['slow_queries'] ?? 0],
        ['Cache hit rate', ($snapshot['cache_hit_rate_percent'] ?? 'n/a').(is_numeric($snapshot['cache_hit_rate_percent'] ?? null) ? '%' : '')],
        ['Queue delay p95', data_get($snapshot, 'queue.delay_seconds.p95', 0).' s'],
        ['Queue failures', data_get($snapshot, 'queue.failures', 0)],
        ['Realtime reconnects', data_get($snapshot, 'realtime.reconnect', 0)],
        ['Realtime error rate', data_get($snapshot, 'realtime.error_rate_percent', 0).'%'],
    ]);

    return ($snapshot['available'] ?? true) ? 0 : 2;
})->purpose('Show rolling FlowTrack HTTP/DB/cache/queue/realtime operational metrics');

Artisan::command('flowtrack:observability:check {--minutes= : Rolling window in minutes}', function (): int {
    $metrics = app(\App\Services\Observability\OperationsMetrics::class);
    $minutes = $this->option('minutes');
    $snapshot = $metrics->snapshot($minutes !== null ? max(1, (int) $minutes) : null);
    if (($snapshot['available'] ?? true) === false) {
        logger()->error('flowtrack.observability.check_unavailable', ['snapshot' => $snapshot]);
        $this->error('Observability store is unavailable.');
        return 2;
    }

    $alerts = $metrics->alerts($snapshot);
    if ($alerts === []) {
        $this->info('FlowTrack observability thresholds are healthy.');
        return 0;
    }

    foreach ($alerts as $alert) logger()->warning('flowtrack.observability.alert', $alert + ['window_minutes' => $snapshot['window_minutes']]);
    $this->warn(count($alerts).' FlowTrack observability threshold(s) exceeded.');
    $this->table(['Metric', 'Value', 'Direction', 'Limit'], array_map(
        static fn (array $alert): array => [$alert['metric'], $alert['value'], $alert['direction'], $alert['limit']],
        $alerts,
    ));
    return 1;
})->purpose('Evaluate FlowTrack rolling operational metrics against alert thresholds');

if ((bool) config('observability.enabled', false)) {
    Schedule::command('flowtrack:observability:check')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->onOneServer();
}
