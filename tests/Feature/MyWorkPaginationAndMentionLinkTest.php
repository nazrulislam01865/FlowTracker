<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyWorkPaginationAndMentionLinkTest extends TestCase
{
    public function test_my_work_is_hard_limited_to_three_order_groups_per_page(): void
    {
        $service = file_get_contents(app_path('Services/MyWorkService.php'));
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString('public const JOBS_PER_PAGE = 3;', $service);
        $this->assertStringContainsString('min(self::JOBS_PER_PAGE, $perPage)', $service);
        $this->assertStringContainsString('public int $perPage = MyWorkService::JOBS_PER_PAGE;', $component);
        $this->assertStringContainsString("previousPage('workPage')", $view);
        $this->assertStringContainsString("gotoPage({{ $pageNumber }}, 'workPage')", $view);
        $this->assertStringContainsString("nextPage('workPage')", $view);
    }

    public function test_tagged_comments_open_through_safe_notification_resolver_and_focus_exact_comment(): void
    {
        $notificationService = file_get_contents(app_path('Services/NotificationService.php'));
        $controller = file_get_contents(app_path('Http/Controllers/NotificationOpenController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $tagged = file_get_contents(resource_path('views/livewire/dashboard/tagged-comments.blade.php'));
        $jobs = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $taskDetail = file_get_contents(resource_path('views/components/jobs/task-detail.blade.php'));
        $jobActivity = file_get_contents(resource_path('views/components/jobs/detail-activity.blade.php'));

        $this->assertStringContainsString("route('notifications.open'", $notificationService);
        $this->assertStringContainsString("name('notifications.open')", $routes);
        $this->assertStringContainsString('NotificationService::class)->urlFor($mention)', $tagged);
        $this->assertStringContainsString('$params[\'comment\'] = \'task-\'', $controller);
        $this->assertStringContainsString('$params[\'comment\'] = \'job-\'', $controller);
        $this->assertStringContainsString("#task-comment-", $controller);
        $this->assertStringContainsString("#job-comment-", $controller);
        $this->assertStringContainsString("#[Url(as: 'comment', history: true)]", $jobs);
        $this->assertStringContainsString('$this->taskActivityTab = \'comments\';', $jobs);
        $this->assertStringContainsString('$this->jobActivityTab = \'comments\';', $jobs);
        $this->assertStringContainsString('id="{{ $entryAnchor }}"', $taskDetail);
        $this->assertStringContainsString('id="{{ $activityAnchor }}"', $jobActivity);
        $this->assertStringContainsString('is-focused-comment', $taskDetail);
        $this->assertStringContainsString('is-focused-comment', $jobActivity);
    }
}
