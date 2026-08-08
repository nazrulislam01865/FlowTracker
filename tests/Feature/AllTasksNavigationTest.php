<?php

namespace Tests\Feature;

use Tests\TestCase;

class AllTasksNavigationTest extends TestCase
{
    public function test_job_board_route_and_links_are_removed_in_favor_of_all_tasks(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $sidebar = file_get_contents(resource_path('views/layouts/partials/sidebar.blade.php'));
        $mobile = file_get_contents(resource_path('views/layouts/partials/mobile-bottom.blade.php'));
        $board = file_get_contents(resource_path('views/livewire/board/index.blade.php'));

        $this->assertStringNotContainsString("Route::get('/board'", $routes);
        $this->assertStringContainsString("Route::get('/all-tasks'", $routes);
        $this->assertStringContainsString("name('all-tasks')", $routes);

        $this->assertStringContainsString('route="all-tasks" label="All Task"', $sidebar);
        $this->assertStringNotContainsString('route="board"', $sidebar);
        $this->assertStringContainsString("route('all-tasks')", $mobile);
        $this->assertStringNotContainsString("route('board')", $mobile);

        $this->assertStringContainsString('<h1>All Tasks</h1>', $board);
        $this->assertStringNotContainsString('Job Board', $board);
        $this->assertStringNotContainsString("setMode('jobs')", $board);
    }
}
