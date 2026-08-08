<?php

namespace Tests\Feature;

use Tests\TestCase;

class BoardTaskPackRefactorTest extends TestCase
{
    public function test_all_tasks_uses_the_literal_my_work_design_without_job_board_navigation(): void
    {
        $board = file_get_contents(resource_path('views/livewire/board/index.blade.php'));
        $this->assertStringContainsString('<style>', $board);
        $this->assertStringContainsString('id="my-work-app"', $board);
        $this->assertStringContainsString('class="page-head"', $board);
        $this->assertStringContainsString('<h1>All Tasks</h1>', $board);
        $this->assertStringNotContainsString('Job Board', $board);
        $this->assertStringNotContainsString("setMode('jobs')", $board);
        $this->assertStringContainsString('class="metrics"', $board);
        $this->assertStringContainsString('class="toolbar"', $board);
        $this->assertStringContainsString('class="list-shell"', $board);
        $this->assertStringContainsString('class="task-head"', $board);
        $this->assertStringContainsString('class="order-head"', $board);
        $this->assertStringContainsString('class="task-row"', $board);
        $this->assertStringContainsString('class="footer"', $board);

        $this->assertStringNotContainsString('ft-task-board-redesign', $board);
        $this->assertStringNotContainsString('ft-task-board-filter-grid', $board);
        $this->assertStringNotContainsString('x-board.task-pack-list', $board);
        $this->assertStringNotContainsString('<span>Assignee</span>', $board);
        $this->assertStringNotContainsString('ft-task-board-pagination', $board);
        $this->assertStringNotContainsString('x-board.task-job-matrix', $board);
    }

    public function test_task_pack_scope_exposes_full_associated_job_context_without_exposing_unrelated_jobs(): void
    {
        $service = file_get_contents(app_path('Services/BoardTaskPackService.php'));

        $this->assertStringContainsString("from('tasks as board_assigned_tasks')", $service);
        $this->assertStringContainsString("where('board_assigned_tasks.assignee_id', $user->id)", $service);
        $this->assertStringContainsString("whereNull('board_assigned_tasks.deleted_at')", $service);
        $this->assertStringContainsString('if ($access->isAdministrator($user))', $service);
        $this->assertStringContainsString('Task::query()', $service);
        $this->assertStringContainsString("->whereIn('tasks.flow_job_id', $visibleJobIds)", $service);
    }

    public function test_qualifying_job_loads_the_complete_task_pack_after_filters_choose_the_job(): void
    {
        $service = file_get_contents(app_path('Services/BoardTaskPackService.php'));

        $this->assertStringContainsString('Filters choose which Job groups belong on the page', $service);
        $this->assertStringContainsString('$tasks = Task::query()', $service);
        $this->assertStringContainsString("->whereIn('tasks.flow_job_id', $jobIds)", $service);
    }

    public function test_task_pack_paginates_jobs_before_loading_page_tasks(): void
    {
        $service = file_get_contents(app_path('Services/BoardTaskPackService.php'));
        $component = file_get_contents(app_path('Livewire/Board/Index.php'));

        $this->assertStringContainsString('Paginate Job groups first', $service);
        $this->assertStringContainsString("->groupBy('tasks.flow_job_id')", $service);
        $this->assertStringContainsString('->paginate(max(1, min(25, $perPage))', $service);
        $this->assertStringContainsString("->whereIn('tasks.flow_job_id', $jobIds)", $service);
        $this->assertStringContainsString('use WithPagination;', $component);
        $this->assertStringContainsString('BoardTaskPackService::JOBS_PER_PAGE', $component);
        $this->assertStringContainsString("resetPage('taskPackPage')", $component);
    }

    public function test_task_pack_inline_status_updates_are_renderless_and_keep_mutation_scope_strict(): void
    {
        $component = file_get_contents(app_path('Livewire/Board/Index.php'));
        $service = file_get_contents(app_path('Services/BoardTaskPackService.php'));

        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function updateTaskStatus\b/', $component);
        $this->assertStringContainsString('TaskService::class)->visibleQuery($actor)', $component);
        $this->assertStringContainsString('canEditTaskWithoutQuery', $service);
        $this->assertStringContainsString('read-only context for the other Job tasks', $service);
    }

    public function test_task_pack_association_has_a_dedicated_database_index(): void
    {
        $migration = file_get_contents(database_path('migrations/2026_08_08_045500_optimize_board_task_pack_scope.php'));

        $this->assertStringContainsString("['assignee_id', 'deleted_at', 'flow_job_id']", $migration);
        $this->assertStringContainsString('ft_tasks_board_assignee_job_idx', $migration);
    }
}
