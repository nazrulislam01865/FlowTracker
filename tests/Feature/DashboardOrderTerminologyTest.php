<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardOrderTerminologyTest extends TestCase
{
    public function test_dashboard_uses_order_terminology_for_user_facing_copy(): void
    {
        $primary = file_get_contents(resource_path('views/livewire/dashboard/index.blade.php'));
        $secondary = file_get_contents(resource_path('views/livewire/dashboard/secondary.blade.php'));
        $tagged = file_get_contents(resource_path('views/livewire/dashboard/tagged-comments.blade.php'));

        $this->assertStringContainsString('Pre-order opportunities', $primary);
        $this->assertStringContainsString('Current order health', $primary);
        $this->assertStringContainsString('<small>active orders</small>', $primary);
        $this->assertStringNotContainsString('Pre-job opportunities', $primary);
        $this->assertStringNotContainsString('Current job health', $primary);
        $this->assertStringNotContainsString('<small>active jobs</small>', $primary);

        foreach ([
            'Highest-priority tasks across current orders',
            '<th>Order</th>',
            'data-label="Order"',
            'No ongoing orders.',
            'Latest order, task, inquiry, document and comment events',
            '<th>Orders</th>',
            'data-label="Orders"',
        ] as $copy) {
            $this->assertStringContainsString($copy, $secondary);
        }

        foreach ([
            'Highest-priority tasks across current jobs',
            '<th>Job</th>',
            'data-label="Job"',
            'No ongoing jobs.',
            'Latest job, task, inquiry, document and comment events',
            '<th>Jobs</th>',
            'data-label="Jobs"',
        ] as $legacyCopy) {
            $this->assertStringNotContainsString($legacyCopy, $secondary);
        }

        $this->assertStringContainsString('$orderTerminology($notification->title)', $secondary);
        $this->assertStringContainsString('$orderTerminology($mention->title)', $tagged);
    }

    public function test_dashboard_remains_record_driven_and_workspace_versioned(): void
    {
        $service = file_get_contents(app_path('Services/DashboardService.php'));

        $this->assertStringContainsString('app(JobService::class)->activeQuery($user)', $service);
        $this->assertStringContainsString('app(TaskService::class)->visibleQuery($user)', $service);
        $this->assertStringContainsString('app(InquiryService::class)->visibleQuery($user)', $service);
        $this->assertStringContainsString("'jobs as active_jobs_count'", $service);
        $this->assertStringContainsString('WorkspaceRefreshService::class)->version()', $service);
        $this->assertStringContainsString("->latest('flow_jobs.updated_at')", $service);
        $this->assertStringContainsString("->latest('inquiries.updated_at')", $service);
    }
}
