<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\FlowJobPhaseHistory;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Services\DashboardService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardReportsLazyLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_report_metrics_are_calculated_from_records(): void
    {
        Cache::flush();
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $client = Client::create(['name' => 'Metrics Client', 'code' => 'METRICS', 'is_active' => true]);
        $workflow = Workflow::create(['name' => 'Metrics Workflow', 'slug' => 'metrics-workflow', 'is_active' => true]);
        $artwork = WorkflowPhase::create([
            'workflow_id' => $workflow->id,
            'sequence' => 1,
            'name' => 'Artwork Approval',
            'short_name' => 'Artwork',
            'allow_job_start' => true,
            'is_active' => true,
        ]);
        $shipment = WorkflowPhase::create([
            'workflow_id' => $workflow->id,
            'sequence' => 2,
            'name' => 'Shipment',
            'short_name' => 'Ship',
            'allow_job_start' => true,
            'is_active' => true,
        ]);

        $activeJob = FlowJob::create([
            'job_number' => 'JOB-METRICS-ACTIVE',
            'client_id' => $client->id,
            'workflow_id' => $workflow->id,
            'workflow_phase_id' => $shipment->id,
            'title' => 'Active shipping job',
            'status' => 'Active',
            'health' => 'On Track',
            'priority' => 'Medium',
            'delivery_date' => today()->addDay(),
        ]);
        $completedJob = FlowJob::create([
            'job_number' => 'JOB-METRICS-COMPLETE',
            'client_id' => $client->id,
            'workflow_id' => $workflow->id,
            'workflow_phase_id' => $shipment->id,
            'title' => 'Completed shipping job',
            'status' => 'Completed',
            'health' => 'Completed',
            'priority' => 'Medium',
            'delivery_date' => today(),
            'completed_at' => today()->setTime(12, 0),
        ]);

        Task::create([
            'task_number' => 'TASK-METRICS-DONE',
            'flow_job_id' => $completedJob->id,
            'workflow_phase_id' => $artwork->id,
            'title' => 'Completed artwork',
            'status' => 'Completed',
            'priority' => 'Medium',
            'completed_at' => now(),
        ]);
        Task::create([
            'task_number' => 'TASK-METRICS-OVERDUE',
            'flow_job_id' => $activeJob->id,
            'workflow_phase_id' => $shipment->id,
            'title' => 'Overdue shipment task',
            'status' => 'In Progress',
            'priority' => 'High',
            'due_date' => today()->subDay(),
        ]);

        FlowJobPhaseHistory::create([
            'flow_job_id' => $completedJob->id,
            'workflow_phase_id' => $artwork->id,
            'status' => 'completed',
            'entered_at' => now()->subDays(4),
            'completed_at' => now()->subDays(2),
        ]);
        FlowJobPhaseHistory::create([
            'flow_job_id' => $completedJob->id,
            'workflow_phase_id' => $shipment->id,
            'status' => 'completed',
            'target_date' => today(),
            'entered_at' => now()->subDay(),
            'completed_at' => today()->setTime(10, 0),
        ]);

        $dashboard = app(DashboardService::class)->data($user);
        $reports = app(ReportService::class)->data($user);

        $this->assertSame(1, $dashboard['metrics']['activeJobs']);
        $this->assertSame(1, $dashboard['metrics']['shipping']);
        $this->assertSame(1, $dashboard['metrics']['overdueTasks']);
        $this->assertSame(1, $reports['kpis']['active_jobs']);
        $this->assertSame(1, $reports['kpis']['completed_jobs']);
        $this->assertSame(100, $reports['kpis']['on_time']);
        $this->assertSame(50, $reports['kpis']['task_completion']);
        $this->assertSame(1, $reports['kpis']['overdue_tasks']);
        $this->assertSame(2.0, $reports['kpis']['avg_artwork_cycle']);
        $this->assertSame(100, $reports['kpis']['shipment_on_time']);
    }

    public function test_all_page_components_are_lazy_and_removed_financial_cards_do_not_render(): void
    {
        foreach (glob(resource_path('views/pages/*.blade.php')) as $page) {
            $contents = file_get_contents($page);
            $this->assertStringContainsString(' lazy />', $contents, basename($page).' is not lazy loaded.');
        }

        $dashboard = file_get_contents(resource_path('views/livewire/dashboard/index.blade.php'));
        $reports = file_get_contents(resource_path('views/livewire/reports/index.blade.php'));
        $sidebar = file_get_contents(resource_path('views/layouts/partials/sidebar.blade.php'));

        $this->assertStringNotContainsString('Outstanding', $dashboard);
        $this->assertStringNotContainsString('Receivable', $reports);
        $this->assertStringNotContainsString('4.2d', $reports);
        $this->assertStringNotContainsString('91%', $reports);
        $this->assertStringContainsString("route('logout')", $sidebar);
    }
}
