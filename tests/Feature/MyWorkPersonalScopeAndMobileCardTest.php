<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyWorkPersonalScopeAndMobileCardTest extends TestCase
{
    public function test_my_tasks_shows_only_active_rows_with_participant_visibility_and_admin_exemption(): void
    {
        $service = file_get_contents(app_path('Services/MyWorkService.php'));
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString("public string \$quick = 'my_tasks';", $component);
        $this->assertStringContainsString("'my_tasks' => null", $component);
        $this->assertStringContainsString('if (!$access->isAdministrator($user))', $service);
        $this->assertStringContainsString("->where('tasks.assignee_id', \$user->id)", $service);
        $this->assertStringContainsString("->whereColumn('flow_jobs.workflow_phase_id', 'tasks.workflow_phase_id')", $service);
        $this->assertStringContainsString('activeAssignedTaskQuery', $service);
        $this->assertStringContainsString('if (!$access->isAdministrator($user))', $service);
        $this->assertStringContainsString('Only Orders whose current active task is assigned to you appear here.', $view);
        $this->assertStringContainsString('My Tasks', $view);
        $this->assertStringNotContainsString('Needs my action', $view);
    }

    public function test_my_tasks_uses_the_previous_responsive_table_styles_and_not_order_list_parity_styles(): void
    {
        $legacyCss = file_get_contents(resource_path('css/modules/work/my-work.css'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('task-table-scroll', $view);
        $this->assertStringContainsString('order-group', $view);
        $this->assertStringContainsString('task-row', $view);
        $this->assertStringContainsString('@media', $legacyCss);
        $this->assertStringNotContainsString('ft-my-task-v5', $view);
        $this->assertStringNotContainsString("routeIs('jobs.index', 'my-work')", $layout);
        $this->assertStringNotContainsString("routeIs('all-tasks', 'my-work')", $layout);
    }
}
