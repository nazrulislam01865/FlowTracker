<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyWorkPersonalScopeAndMobileCardTest extends TestCase
{
    public function test_my_tasks_scope_keeps_admin_broad_and_regular_users_personal(): void
    {
        $service = file_get_contents(app_path('Services/MyWorkService.php'));
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString("public string \$quick = 'my_tasks';", $component);
        $this->assertStringContainsString("'my_tasks' => null", $component);
        $this->assertStringContainsString("if (!\$access->isAdministrator(\$user))", $service);
        $this->assertStringContainsString("->where('tasks.assignee_id', \$user->id)", $service);
        $this->assertStringContainsString("->where('created_by', \$user->id)", $service);
        $this->assertStringContainsString("if (!\$administrator)", $service);
        $this->assertStringContainsString('All Order tasks', $view);
        $this->assertStringContainsString('Tasks assigned to you or from Orders you created', $view);
        $this->assertStringContainsString('My Tasks', $view);
        $this->assertStringNotContainsString('Needs my action', $view);
    }

    public function test_mobile_task_rows_use_compact_card_actions(): void
    {
        $css = $this->compatibilityCss('flowtrack-my-work.css');
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString('row-action-mobile', $view);
        $this->assertStringContainsString('my-work mobile card refinement', $css);
        $this->assertStringContainsString('grid-template-areas:', $css);
    }
}
