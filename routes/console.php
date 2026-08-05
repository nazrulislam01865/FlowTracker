<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('flowtrack:performance:explain {--user=1 : User ID used for assignee/member query plans}', function (): int {
    $userId = max(1, (int) $this->option('user'));
    $driver = DB::connection()->getDriverName();
    $prefix = $driver === 'sqlite' ? 'EXPLAIN QUERY PLAN ' : 'EXPLAIN ';

    $queries = [
        'Active Jobs ordered by delivery' => [
            'flow_jobs',
            "select id from flow_jobs where deleted_at is null and completed_at is null and status not in (?, ?) order by delivery_date asc limit 60",
            ['Inactive', 'Cancelled'],
        ],
        'Open Tasks ordered by due date' => [
            'tasks',
            "select id from tasks where deleted_at is null and completed_at is null order by due_date asc limit 60",
            [],
        ],
        'Open Tasks for an assignee' => [
            'tasks',
            "select id from tasks where assignee_id = ? and deleted_at is null and completed_at is null order by due_date asc limit 60",
            [$userId],
        ],
        'Tasks belonging to an active Job' => [
            'tasks',
            "select id from tasks where flow_job_id = ? and deleted_at is null and completed_at is null and status <> ? order by due_date asc",
            [1, 'Completed'],
        ],
        'Unread notifications' => [
            'flow_notifications',
            "select id from flow_notifications where user_id = ? and read_at is null order by created_at desc limit 30",
            [$userId],
        ],
        'Job membership lookup' => [
            'flow_job_members',
            "select flow_job_id from flow_job_members where user_id = ? order by flow_job_id",
            [$userId],
        ],
        'Task document requirement lookup' => [
            'documents',
            "select id from documents where task_id = ? and category = ? order by version desc",
            [1, 'Artwork'],
        ],
        'Recent subject activity' => [
            'activities',
            "select id from activities where subject_type = ? and subject_id = ? order by created_at desc limit 30",
            ['App\\Models\\FlowJob', 1],
        ],
        'Active Master Data ordering' => [
            'master_records',
            "select id from master_records where workspace_id = ? and type = ? and status = ? and deleted_at is null order by sort_order, name",
            [1, 'task_status', 'active'],
        ],
        'Workflow phase ordering' => [
            'workflow_phases',
            "select id from workflow_phases where workflow_id = ? and is_active = ? order by sequence",
            [1, 1],
        ],
    ];

    foreach ($queries as $label => [$table, $sql, $bindings]) {
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
    }

    $this->newLine();
    $this->comment('Confirm that the key column names include the ft_* indexes added by the performance migration.');

    return 0;
})->purpose('Run EXPLAIN against FlowTrack high-frequency query shapes');
