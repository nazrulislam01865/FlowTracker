<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobSelectionAndCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtered_ids_include_every_matching_job_across_pages(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        [$client, $workflow, $phase] = $this->jobDependencies();

        foreach (range(1, 11) as $index) {
            $this->createJob($client, $workflow, $phase, "JOB-TEST-{$index}");
        }

        $service = app(JobService::class);
        $filters = ['quick' => 'all'];

        $this->assertCount(11, $service->filteredIds($user, $filters));
        $this->assertCount(10, $service->paginate($user, $filters, 10)->items());
        $this->assertSame(11, $service->paginate($user, $filters, 10)->total());
    }

    public function test_jobs_component_select_all_includes_later_pages(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        [$client, $workflow, $phase] = $this->jobDependencies();

        foreach (range(1, 11) as $index) {
            $this->createJob($client, $workflow, $phase, "JOB-COMPONENT-{$index}");
        }

        Livewire::actingAs($user)
            ->test(\App\Livewire\Jobs\Index::class)
            ->call('toggleSelectAllJobs')
            ->assertCount('selectedJobIds', 11);
    }

    public function test_jobs_list_does_not_initialize_or_render_the_create_form(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Jobs\Index::class)
            ->assertSet('showCreate', false)
            ->assertSet('workflowId', null)
            ->assertSet('workflowPhaseId', null)
            ->assertSet('clientId', null)
            ->assertSet('jobItems', [])
            ->assertSee('Manage active jobs from request to collection')
            ->assertDontSee('Create new job');
    }

    public function test_open_create_switches_from_the_list_to_create_only_data(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        [, $workflow, $phase] = $this->jobDependencies();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Jobs\Index::class)
            ->call('openCreate')
            ->assertSet('showCreate', true)
            ->assertSet('workflowId', $workflow->id)
            ->assertSet('workflowPhaseId', $phase->id)
            ->assertCount('jobItems', 1)
            ->assertSee('Create new job')
            ->assertDontSee('Manage active jobs from request to collection')
            ->call('closeCreate')
            ->assertSet('showCreate', false)
            ->assertSet('workflowId', null)
            ->assertSet('jobItems', [])
            ->assertSee('Manage active jobs from request to collection')
            ->assertDontSee('Create new job');
    }

    public function test_active_job_query_does_not_count_hidden_or_deleted_jobs(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        [$client, $workflow, $phase] = $this->jobDependencies();

        $active = $this->createJob($client, $workflow, $phase, 'JOB-ACTIVE');
        $this->createJob($client, $workflow, $phase, 'JOB-INACTIVE', 'Inactive');
        $this->createJob($client, $workflow, $phase, 'JOB-CANCELLED', 'Cancelled');
        $this->createJob($client, $workflow, $phase, 'JOB-COMPLETED', 'Completed', now());
        $deleted = $this->createJob($client, $workflow, $phase, 'JOB-DELETED');
        $deleted->delete();

        $ids = app(JobService::class)->activeQuery($user)->pluck('id')->all();

        $this->assertSame([$active->id], $ids);
    }

    private function jobDependencies(): array
    {
        $client = Client::create([
            'name' => 'Test Client',
            'code' => 'TEST-CLIENT',
            'is_active' => true,
        ]);
        $workflow = Workflow::create([
            'name' => 'Test Workflow',
            'slug' => 'test-workflow',
            'is_active' => true,
        ]);
        $phase = WorkflowPhase::create([
            'workflow_id' => $workflow->id,
            'sequence' => 1,
            'name' => 'Request',
            'short_name' => 'Request',
            'allow_job_start' => true,
            'is_active' => true,
        ]);

        return [$client, $workflow, $phase];
    }

    private function createJob(
        Client $client,
        Workflow $workflow,
        WorkflowPhase $phase,
        string $jobNumber,
        string $status = 'New',
        mixed $completedAt = null,
    ): FlowJob {
        return FlowJob::create([
            'job_number' => $jobNumber,
            'client_id' => $client->id,
            'workflow_id' => $workflow->id,
            'workflow_phase_id' => $phase->id,
            'title' => $jobNumber,
            'status' => $status,
            'health' => $completedAt ? 'Completed' : 'On Track',
            'priority' => 'Medium',
            'completed_at' => $completedAt,
        ]);
    }
}
