<?php

namespace Tests\Feature;

use App\Services\OrderWorkflowSetupService;
use Tests\TestCase;

class ProductionEstimatedDeliveryRequiredTaskTest extends TestCase
{
    public function test_production_stage_defines_required_estimated_delivery_task_before_start_production(): void
    {
        $production = collect(OrderWorkflowSetupService::fixedStages())
            ->firstWhere('key', 'prod');

        $this->assertNotNull($production);

        $tasks = collect($production['tasks']);
        $keys = $tasks->pluck('automation_key')->values()->all();

        $this->assertSame('PROD_SET_ESTIMATED_DELIVERY', $keys[0] ?? null);
        $this->assertSame('PROD_START', $keys[1] ?? null);

        $estimatedTask = $tasks->firstWhere('automation_key', 'PROD_SET_ESTIMATED_DELIVERY');
        $this->assertTrue((bool) ($estimatedTask['is_required'] ?? false));
        $this->assertSame('#f28c28', strtolower((string) ($estimatedTask['color'] ?? '')));
    }

    public function test_workflow_action_supports_estimated_delivery_task_and_required_date_validation(): void
    {
        $service = file_get_contents(app_path('Services/OrderWorkflowActionService.php'));
        $modal = file_get_contents(resource_path('views/components/jobs/order-detail/workflow-action-modal.blade.php'));
        $row = file_get_contents(resource_path('views/components/jobs/order-detail/task-row.blade.php'));

        $this->assertStringContainsString("'set estimated delivery date' => 'PROD_SET_ESTIMATED_DELIVERY'", $service);
        $this->assertStringContainsString("'variant' => 'estimated_delivery'", $service);
        $this->assertStringContainsString("'job.estimated_delivery_date_set'", $service);
        $this->assertStringContainsString("'estimated_delivery_date' => \$estimatedDeliveryDate", $service);
        $this->assertStringContainsString("\$variant === 'estimated_delivery'", $modal);
        $this->assertStringContainsString('Required before Production', $modal);
        $this->assertStringContainsString('ft-prototype-clickable-date', $modal);
        $this->assertStringContainsString("typeof this.showPicker === 'function'", $modal);
        $this->assertStringContainsString('ft-order-task-row--estimated-delivery', $row);
        $this->assertStringContainsString('ft-order-required-task-badge', $row);
    }
}
