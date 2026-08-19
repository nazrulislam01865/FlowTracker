<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardGlobalPeriodSectionsTest extends TestCase
{
    public function test_team_performance_and_client_portfolio_use_global_dashboard_period(): void
    {
        $dashboard = file_get_contents(resource_path('views/livewire/dashboard/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Dashboard/Index.php'));
        $service = file_get_contents(app_path('Services/DashboardService.php'));

        $this->assertStringContainsString('wire:click="setRange(1)"', $dashboard);
        $this->assertStringContainsString('wire:click="setRange(7)"', $dashboard);
        $this->assertStringContainsString('wire:click="setRange(30)"', $dashboard);

        // The dashboard Team Performance card must not keep a second period selector.
        $this->assertStringNotContainsString('wire:model.live="teamPeriod"', $dashboard);
        $this->assertStringNotContainsString('Team performance reporting period', $dashboard);

        $this->assertStringContainsString('$this->rangeDays,', $component);
        $this->assertStringContainsString("'clientPortfolio' => \$this->clientPortfolio(\$user, \$clientId, \$departmentId, \$rangeDays)", $service);
        $this->assertStringContainsString("'teamReportingPeriod' => \$dashboardPeriod", $service);
        $this->assertStringContainsString("whereBetween('flow_jobs.updated_at', \$rangeBounds)", $service);
        $this->assertStringContainsString("whereBetween('inquiries.updated_at', \$rangeBounds)", $service);
    }
}
