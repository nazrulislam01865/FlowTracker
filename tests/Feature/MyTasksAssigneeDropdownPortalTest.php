<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyTasksAssigneeDropdownPortalTest extends TestCase
{
    public function test_my_tasks_assignee_picker_uses_the_shared_fixed_menu_portal(): void
    {
        $groupView = file_get_contents(resource_path('views/livewire/my-work/_order-groups-v5.blade.php'));
        $picker = file_get_contents(resource_path('views/components/ui/inline-remote-user.blade.php'));

        $this->assertStringContainsString('<x-ui.inline-remote-user', $groupView);
        $this->assertStringContainsString("'fixedMenu' => true", $picker);
        $this->assertStringContainsString('<template x-teleport="body">', $picker);
        $this->assertStringContainsString('data-ft-inline-remote-menu', $picker);
        $this->assertStringContainsString("open ? menuStyle + ';display:flex!important;' : 'display:none!important;'", $picker);
    }
}
