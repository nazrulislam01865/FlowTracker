<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyWorkPrototypeImplementationTest extends TestCase
{
    public function test_my_work_matches_the_grouped_list_interaction_model(): void
    {
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString('Needs my action', $view);
        $this->assertStringContainsString('Overdue', $view);
        $this->assertStringContainsString('Due today', $view);
        $this->assertStringContainsString('Upcoming', $view);
        $this->assertStringContainsString('Waiting', $view);
        $this->assertStringContainsString('Needs action', $view);
        $this->assertStringContainsString('All my tasks', $view);
        $this->assertStringNotContainsString('Prototype only', $view);
        $this->assertStringContainsString('Mentions (', $view);
        $this->assertStringContainsString('Sort: Action priority', $view);
        $this->assertStringContainsString('class="order-group"', $view);
        $this->assertStringContainsString('wire:key="my-work-task-', $view);
    }

    public function test_my_work_keeps_the_shared_application_sidebar_instead_of_overriding_it(): void
    {
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringNotContainsString('width:198px', $view);
        $this->assertStringNotContainsString('flex:0 0 198px', $view);
        $this->assertStringNotContainsString('.ft-sidebar-logout-form{display:none}', $view);
        $this->assertStringNotContainsString('.sidebar{width:66px', $view);
        $this->assertStringNotContainsString('.nav-btn{font-size:13px}', $view);
    }

    public function test_my_work_groups_and_pages_jobs_before_loading_task_rows(): void
    {
        $service = file_get_contents(app_path('Services/MyWorkService.php'));

        $this->assertStringContainsString("->groupBy('tasks.flow_job_id')", $service);
        $this->assertStringContainsString("->fromSub(\$grouped, 'my_work_groups')", $service);
        $this->assertStringContainsString('->paginate(max(1, min(25, $perPage))', $service);
        $this->assertStringContainsString("->whereIn('tasks.flow_job_id', \$jobIds)", $service);
        $this->assertStringContainsString("->where('tasks.assignee_id', \$user->id)", $service);
        $this->assertStringContainsString('$administratorAllTasks', $service);
        $this->assertStringContainsString('includeOpenConstraint: !$administratorAllTasks', $service);
        $this->assertStringNotContainsString('includeInactiveJobsForAdministrator', $service);
        $this->assertStringContainsString("->whereNull('completed_at')", $service);
        $this->assertStringContainsString("->whereNotIn('status', JobService::INACTIVE_STATUSES)", $service);
        $this->assertStringContainsString("flow_notifications.type', 'mention'", $service);
    }

    public function test_inline_status_update_is_renderless_and_uses_optimistic_concurrency(): void
    {
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function updateTaskStatus\b/', $component);
        $this->assertStringContainsString('lockForUpdate()', $component);
        $this->assertStringContainsString('This task changed since the list was loaded.', $component);
        $this->assertStringContainsString('select.disabled=true', $view);
        $this->assertStringContainsString('select.value=previous', $view);
    }
}
