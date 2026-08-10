<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Models\WorkflowTemplate;
use App\Services\FilterOptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateOrderWorkflowSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_specific_inquiry_workflow_is_the_default_for_create_order_and_can_be_changed(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $nep = Client::create(['name' => 'NEP', 'code' => 'NEP', 'is_active' => true]);

        $orderWorkflow = $this->workflow('Standard Order Workflow', 'ORDER', 'orders', 'all', true);
        $orderPhase = $this->phase($orderWorkflow, 'Order start');

        $inquiryWorkflow = $this->workflow('NEP Inquiry Workflow', 'NEP-INQ', 'inquiries', 'specific');
        $inquiryWorkflow->clients()->sync([$nep->id]);
        $inquiryPhase = $this->phase($inquiryWorkflow, 'Inquiry start');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Jobs\Index::class)
            ->call('openCreate')
            ->assertSet('clientId', $nep->id)
            ->call('loadCreateSection', 'catalog')
            ->call('loadCreateSection', 'assignment')
            ->call('loadCreateSection', 'workflow')
            ->assertSet('workflowId', $inquiryWorkflow->id)
            ->assertSet('workflowPhaseId', $inquiryPhase->id)
            ->assertSee('NEP Inquiry Workflow')
            ->call('setCreateSelector', 'workflowId', (string) $orderWorkflow->id)
            ->assertSet('workflowId', $orderWorkflow->id)
            ->assertSet('workflowPhaseId', $orderPhase->id);
    }


    public function test_changing_client_replaces_the_previous_workflow_with_the_new_clients_configured_default(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $nep = Client::create(['name' => 'NEP', 'code' => 'NEP', 'is_active' => true]);
        $other = Client::create(['name' => 'Other Client', 'code' => 'OTHER', 'is_active' => true]);

        $genericOrder = $this->workflow('Standard Order Workflow', 'ORDER', 'orders', 'all', true);
        $genericPhase = $this->phase($genericOrder, 'Standard start');

        $nepInquiry = $this->workflow('NEP Inquiry Workflow', 'NEP-INQ', 'inquiries', 'specific');
        $nepInquiry->clients()->sync([$nep->id]);
        $nepPhase = $this->phase($nepInquiry, 'NEP inquiry start');

        $otherOrder = $this->workflow('Other Client Order Workflow', 'OTHER-ORDER', 'orders', 'specific');
        $otherOrder->clients()->sync([$other->id]);
        $otherPhase = $this->phase($otherOrder, 'Other order start');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Jobs\Index::class)
            ->call('openCreate')
            ->call('loadCreateSection', 'workflow')
            ->call('setCreateSelector', 'clientId', (string) $nep->id)
            ->assertSet('clientId', $nep->id)
            ->assertSet('workflowId', $nepInquiry->id)
            ->assertSet('workflowPhaseId', $nepPhase->id)
            // A manual override is allowed while the Client remains NEP.
            ->call('setCreateSelector', 'workflowId', (string) $genericOrder->id)
            ->assertSet('workflowId', $genericOrder->id)
            ->assertSet('workflowPhaseId', $genericPhase->id)
            // Changing Client must throw away NEP's/manual Workflow selection
            // and resolve the new Client's Workflow Setup configuration.
            ->call('setCreateSelector', 'clientId', (string) $other->id)
            ->assertSet('clientId', $other->id)
            ->assertSet('workflowId', $otherOrder->id)
            ->assertSet('workflowPhaseId', $otherPhase->id)
            ->assertSee('Other Client Order Workflow')
            // Switching back must deterministically restore NEP's configured
            // Inquiry Workflow rather than retaining Other Client's Workflow.
            ->call('setCreateSelector', 'clientId', (string) $nep->id)
            ->assertSet('workflowId', $nepInquiry->id)
            ->assertSet('workflowPhaseId', $nepPhase->id)
            ->assertSee('NEP Inquiry Workflow');
    }

    public function test_create_order_workflow_options_include_only_client_specific_inquiry_workflows_for_that_client(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $nep = Client::create(['name' => 'NEP', 'code' => 'NEP', 'is_active' => true]);
        $other = Client::create(['name' => 'Other Client', 'code' => 'OTHER', 'is_active' => true]);

        $this->workflow('Standard Order Workflow', 'ORDER', 'orders', 'all', true);
        $nepInquiry = $this->workflow('Renamed NEP Workflow', 'NEP-INQ', 'inquiries', 'specific');
        $nepInquiry->clients()->sync([$nep->id]);

        $genericInquiry = $this->workflow('Generic Inquiry Workflow', 'GEN-INQ', 'inquiries', 'all');
        $otherInquiry = $this->workflow('Other Inquiry Workflow', 'OTHER-INQ', 'inquiries', 'specific');
        $otherInquiry->clients()->sync([$other->id]);

        $items = app(FilterOptionService::class)
            ->options($user, 'workflows', 'create-job', '', null, 20, ['client_id' => $nep->id]);

        $labels = $items->pluck('label')->all();
        $this->assertContains('Standard Order Workflow', $labels);
        $this->assertContains('Renamed NEP Workflow', $labels);
        $this->assertNotContains($genericInquiry->name, $labels);
        $this->assertNotContains($otherInquiry->name, $labels);
        $this->assertSame('Renamed NEP Workflow', $labels[0]);
    }

    private function workflow(
        string $name,
        string $code,
        string $appliesTo,
        string $availability,
        bool $default = false,
    ): WorkflowTemplate {
        $legacy = Workflow::create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $code)),
            'is_active' => true,
            'is_snapshot' => false,
        ]);

        return WorkflowTemplate::create([
            'id' => $legacy->id,
            'workspace_id' => 1,
            'name' => $name,
            'code' => $code,
            'description' => null,
            'applies_to' => $appliesTo,
            'client_availability' => $availability,
            'is_active' => true,
            'is_default' => $default,
            'version' => 1,
        ]);
    }

    private function phase(WorkflowTemplate $workflow, string $name): WorkflowPhase
    {
        return WorkflowPhase::create([
            'workflow_template_id' => $workflow->id,
            'workflow_id' => $workflow->id,
            'sequence' => 1,
            'name' => $name,
            'short_name' => $name,
            'allow_job_start' => true,
            'is_active' => true,
        ]);
    }
}
