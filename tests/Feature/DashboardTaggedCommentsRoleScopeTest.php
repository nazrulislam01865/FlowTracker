<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTaggedCommentsRoleScopeTest extends TestCase
{
    public function test_tagged_comments_are_workspace_wide_for_administrators_and_personal_for_users(): void
    {
        $notifications = file_get_contents(app_path('Services/NotificationService.php'));
        $dashboard = file_get_contents(app_path('Services/DashboardService.php'));
        $tagged = file_get_contents(resource_path('views/livewire/dashboard/tagged-comments.blade.php'));
        $index = file_get_contents(resource_path('views/livewire/dashboard/index.blade.php'));
        $resolver = file_get_contents(app_path('Http/Controllers/NotificationOpenController.php'));
        $migration = file_get_contents(database_path('migrations/2026_08_10_231000_backfill_admin_tagged_comment_notifications.php'));

        $this->assertStringContainsString('$directIds->merge($this->administratorIds())', $notifications);
        $this->assertStringContainsString('$direct ? \'mention\' : \'mention_admin\'', $notifications);
        $this->assertStringContainsString("->where('type', '!=', 'mention_admin')", $notifications);
        $this->assertStringContainsString("whereIn('flow_notifications.type', ['mention', 'mention_admin'])", $dashboard);
        $this->assertStringContainsString("->where('flow_notifications.type', 'mention')", $dashboard);
        $this->assertStringContainsString('All mentions across orders, tasks and inquiries', $tagged);
        $this->assertStringContainsString('Unread tagged comments across FlowTrack', $index);
        $this->assertStringContainsString("['mention', 'mention_admin', 'comment']", $resolver);
        $this->assertStringContainsString("type === 'mention_admin'", $resolver);
        $this->assertStringContainsString("'type' => 'mention_admin'", $migration);
    }
}
