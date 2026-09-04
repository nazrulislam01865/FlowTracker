<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderShipmentBillingTransitionImplementationTest extends TestCase
{
    public function test_shipment_completion_reselects_the_fresh_current_stage(): void
    {
        $component = file_get_contents(app_path('Livewire/Jobs/Concerns/ManagesOrderWorkflow.php'));

        $this->assertStringContainsString('public function dispatchShipment(int $taskId): void', $component);
        $this->assertStringContainsString('$this->refreshShipmentWorkflowSelection();', $component);
        $this->assertStringContainsString('$this->overviewPhaseId = (int) $currentPhaseId;', $component);
        $this->assertStringContainsString("\$this->orderDetailSectionsReady['workflow'] = true;", $component);
        $this->assertStringContainsString('Shipment marked as dispatched. Billing is now active.', $component);
    }

    public function test_workflow_dom_key_changes_when_current_or_selected_stage_changes(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/order-detail/workflow.blade.php'));

        $this->assertStringContainsString(
            'wire:key="order-detail-workflow-{{ $job->id }}-current-{{ (int) $job->workflow_phase_id }}-selected-{{ (int) ($selectedPhase?->id ?? 0) }}"',
            $view,
        );
    }
}
