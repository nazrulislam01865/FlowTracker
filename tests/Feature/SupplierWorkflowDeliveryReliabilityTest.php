<?php

namespace Tests\Feature;

use Tests\TestCase;

final class SupplierWorkflowDeliveryReliabilityTest extends TestCase
{
    public function test_reconciliation_runs_before_delivery_and_includes_assignment_only_changes(): void
    {
        $console = file_get_contents(base_path('routes/console.php'));

        $this->assertStringContainsString("from('supplier_task_assignments as a')", $console);
        $this->assertStringContainsString("->whereColumn('a.flow_job_id', 'd.flow_job_id')", $console);
        $this->assertStringContainsString(
            "Schedule::command('flowtrack:refresh-supplier-orders')->everyMinute()", $console
        );
        $this->assertStringContainsString(
            "Schedule::command('flowtrack:sync-supplier-orders')->everyMinute()", $console
        );
        $this->assertLessThan(
            strpos($console, "Schedule::command('flowtrack:sync-supplier-orders')->everyMinute()"),
            strpos($console, "Schedule::command('flowtrack:refresh-supplier-orders')->everyMinute()")
        );
    }

    public function test_changes_during_signed_delivery_are_not_overwritten(): void
    {
        $observer = file_get_contents(app_path('Observers/SupplierBridgeSyncObserver.php'));
        $assigner = file_get_contents(app_path('Services/SupplierBridge/SupplierDefaultTaskAssigner.php'));
        $sender = file_get_contents(app_path('Services/SupplierBridge/DeliverOrder.php'));

        $this->assertStringContainsString("->whereIn('status', ['delivered', 'sending'])", $observer);
        $this->assertStringContainsString("->whereIn('status', ['delivered', 'sending'])", $assigner);
        $this->assertStringContainsString("->where('status', 'sending')->update([", $sender);
        $this->assertStringContainsString("->syncForOrder(\$order)", $sender);
        $this->assertStringContainsString("\$response->json('applied') === false", $sender);
    }
}
