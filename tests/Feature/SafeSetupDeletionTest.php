<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\TaskPack;
use App\Models\TaskPackItem;
use App\Models\TaskPackTask;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Models\WorkflowTemplate;
use App\Services\JobService;
use App\Services\TaskPackService;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafeSetupDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_job_copies_workflow_phases_and_all_task_pack_tasks_into_a_private_snapshot(): void
    {
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $this->actingAs($user);
        $client = Client::query()->create(['name' => 'Snapshot Client', 'code' => 'SNAP-1', 'is_active' => true]);
        [$legacy, $template] = $this->workflowPair('WF-SNAP', 'Snapshot Workflow', false);

        $packA = $this->taskPack('PACK-A', 'Pack A', ['Prepare artwork', 'Approve proof']);
        $packB = $this->taskPack('PACK-B', 'Pack B', ['Arrange shipment']);

        $phaseA = $this->phase($legacy, $template, 1, 'Artwork', $packA, true);
        $this->phase($legacy, $template, 2, 'Shipping', $packB, false);

        $job = app(JobService::class)->create([
            'client_id' => $client->id,
            'workflow_id' => $legacy->id,
            'workflow_phase_id' => $phaseA->id,
            'owner_id' => $user->id,
            'coordinator_id' => $user->id,
            'title' => 'Independent Job',
            'product' => 'Sample Product',
            'category' => 'Samples',
            'quantity' => 10,
            'priority' => 'Medium',
            'description' => null,
            'items' => [['product' => 'Sample Product', 'category' => 'Samples', 'quantity' => 10]],
            'draft' => false,
        ], $user);

        $job->refresh()->load('workflow.phases.taskPack.items', 'tasks');

        $this->assertNotSame($legacy->id, $job->workflow_id);
        $this->assertSame($legacy->id, $job->source_workflow_id);
        $this->assertTrue((bool) $job->workflow->is_snapshot);
        $this->assertSame($job->id, (int) $job->workflow->snapshot_job_id);
        $this->assertCount(2, $job->workflow->phases);
        $this->assertCount(3, $job->tasks);
        $this->assertTrue($job->workflow->phases->every(fn ($phase) => $phase->workflow_template_id === null));
        $this->assertTrue($job->workflow->phases->every(fn ($phase) => $phase->taskPack?->is_snapshot === true));
        $this->assertTrue($job->tasks->every(fn ($task) => $task->workflow_phase_id !== $phaseA->id));
    }

    public function test_deleting_reusable_setup_after_new_job_creation_keeps_the_job_snapshot_unchanged(): void
    {
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $this->actingAs($user);
        $client = Client::query()->create(['name' => 'Independent Setup Client', 'code' => 'IND-1', 'is_active' => true]);
        [$legacy, $template] = $this->workflowPair('WF-IND', 'Independent Workflow', false);

        $pack = $this->taskPack('PACK-IND', 'Independent Pack', ['Copied Task A', 'Copied Task B']);
        $phase = $this->phase($legacy, $template, 1, 'Independent Phase', $pack, true);

        $job = app(JobService::class)->create([
            'client_id' => $client->id,
            'workflow_id' => $legacy->id,
            'workflow_phase_id' => $phase->id,
            'owner_id' => $user->id,
            'coordinator_id' => $user->id,
            'title' => 'Setup-independent Job',
            'product' => 'Independent Product',
            'category' => 'Samples',
            'quantity' => 1,
            'priority' => 'Medium',
            'description' => null,
            'items' => [['product' => 'Independent Product', 'category' => 'Samples', 'quantity' => 1]],
            'draft' => false,
        ], $user);

        $job->refresh()->load('tasks');
        $snapshotWorkflowId = (int) $job->workflow_id;
        $snapshotPhaseId = (int) $job->workflow_phase_id;
        $taskIds = $job->tasks->pluck('id')->sort()->values()->all();
        $taskTitles = $job->tasks->pluck('title')->sort()->values()->all();

        $workflowResult = app(WorkflowService::class)->deleteWorkflow($template->id);
        $packResult = app(TaskPackService::class)->deletePack($pack->id);

        $this->assertSame(0, $workflowResult['job_count']);
        $this->assertSame(0, $workflowResult['task_count']);
        $this->assertSame(0, $packResult['job_count']);
        $this->assertSame(0, $packResult['task_count']);

        $preserved = FlowJob::query()->with('tasks')->findOrFail($job->id);
        $this->assertSame($snapshotWorkflowId, (int) $preserved->workflow_id);
        $this->assertSame($snapshotPhaseId, (int) $preserved->workflow_phase_id);
        $this->assertSame($legacy->id, (int) $preserved->source_workflow_id);
        $this->assertSame($taskIds, $preserved->tasks->pluck('id')->sort()->values()->all());
        $this->assertSame($taskTitles, $preserved->tasks->pluck('title')->sort()->values()->all());

        $this->assertDatabaseMissing('workflow_templates', ['id' => $template->id]);
        $this->assertDatabaseMissing('workflows', ['id' => $legacy->id]);
        $this->assertDatabaseMissing('task_packs', ['id' => $pack->id]);
        $this->assertDatabaseHas('workflows', ['id' => $snapshotWorkflowId, 'is_snapshot' => 1, 'snapshot_job_id' => $job->id]);
        foreach ($taskIds as $taskId) {
            $this->assertDatabaseHas('tasks', ['id' => $taskId, 'flow_job_id' => $job->id]);
        }
    }

    public function test_workflow_delete_preserves_linked_jobs_and_tasks_by_snapshotting_them_first(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true, 'is_active' => true]));
        $client = Client::query()->create(['name' => 'Safe Delete Client', 'code' => 'SAFE-1', 'is_active' => true]);

        [$legacyA] = $this->workflowPair('WF-A', 'Primary Workflow', true);
        [$legacyB, $templateB] = $this->workflowPair('WF-B', 'Workflow To Delete', false);
        $phaseB = $this->phase($legacyB, $templateB, 1, 'Delete Phase', null, true);

        // Reproduce the old production inconsistency: workflow_id points at A,
        // while the current phase belongs to B. Safe delete must still preserve
        // the Job instead of raising an FK error or deleting operational data.
        $job = FlowJob::query()->create([
            'job_number' => 'JOB-SAFE-001',
            'client_id' => $client->id,
            'workflow_id' => $legacyA->id,
            'workflow_phase_id' => $phaseB->id,
            'started_from_phase_id' => $phaseB->id,
            'title' => 'Phase-linked Job',
        ]);
        $task = Task::query()->create([
            'task_number' => 'TASK-SAFE-001',
            'flow_job_id' => $job->id,
            'workflow_phase_id' => $phaseB->id,
            'title' => 'Linked Task',
        ]);

        $service = app(WorkflowService::class);
        $impact = $service->workflowDeleteImpact($templateB->id);
        $this->assertSame(1, $impact['job_count']);
        $this->assertSame(1, $impact['task_count']);

        $result = $service->deleteWorkflow($templateB->id);

        $this->assertSame(1, $result['job_count']);
        $this->assertSame(0, $result['task_count']);
        $this->assertDatabaseHas('flow_jobs', ['id' => $job->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
        $this->assertDatabaseMissing('workflow_phases', ['id' => $phaseB->id]);
        $this->assertDatabaseMissing('workflow_templates', ['id' => $templateB->id]);
        $this->assertDatabaseMissing('workflows', ['id' => $legacyB->id]);

        $preservedJob = FlowJob::query()->findOrFail($job->id);
        $preservedTask = Task::query()->findOrFail($task->id);
        $this->assertNotSame($legacyB->id, $preservedJob->workflow_id);
        $this->assertSame($legacyB->id, $preservedJob->source_workflow_id);
        $this->assertNotSame($phaseB->id, $preservedJob->workflow_phase_id);
        $this->assertSame($preservedJob->workflow_phase_id, $preservedTask->workflow_phase_id);
    }

    public function test_task_pack_delete_keeps_existing_job_tasks_using_copied_pack_data(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true, 'is_active' => true]));
        $client = Client::query()->create(['name' => 'Task Pack Client', 'code' => 'SAFE-2', 'is_active' => true]);
        [$legacy, $template] = $this->workflowPair('WF-TP', 'Task Pack Workflow', true);

        $pack = $this->taskPack('TP-SAFE', 'Task Pack To Delete', ['Generated Task']);
        $item = $pack->items()->firstOrFail();
        $phase = $this->phase($legacy, $template, 1, 'Packed Phase', $pack, true);

        $job = FlowJob::query()->create([
            'job_number' => 'JOB-SAFE-002',
            'client_id' => $client->id,
            'workflow_id' => $legacy->id,
            'workflow_phase_id' => $phase->id,
            'started_from_phase_id' => $phase->id,
            'title' => 'Task Pack Job',
        ]);
        $generated = Task::query()->create([
            'task_number' => 'TASK-SAFE-002',
            'flow_job_id' => $job->id,
            'workflow_phase_id' => $phase->id,
            'task_pack_task_id' => $item->id,
            'title' => 'Generated Task',
        ]);
        $otherTask = Task::query()->create([
            'task_number' => 'TASK-SAFE-003',
            'flow_job_id' => $job->id,
            'workflow_phase_id' => $phase->id,
            'title' => 'Other Task In Same Job',
        ]);

        $service = app(TaskPackService::class);
        $impact = $service->packDeleteImpact($pack->id);
        $this->assertSame(1, $impact['mapped_phase_count']);
        $this->assertSame(1, $impact['job_count']);
        $this->assertSame(2, $impact['task_count']);

        $result = $service->deletePack($pack->id);

        $this->assertSame(1, $result['job_count']);
        $this->assertSame(0, $result['task_count']);
        $this->assertDatabaseHas('flow_jobs', ['id' => $job->id]);
        $this->assertDatabaseHas('tasks', ['id' => $generated->id]);
        $this->assertDatabaseHas('tasks', ['id' => $otherTask->id]);
        $this->assertDatabaseMissing('task_packs', ['id' => $pack->id]);
        $this->assertDatabaseMissing('task_pack_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('task_pack_tasks', ['id' => $item->id]);
        $this->assertDatabaseHas('workflow_phases', ['id' => $phase->id, 'task_pack_id' => null]);

        $preservedJob = FlowJob::query()->findOrFail($job->id);
        $preservedGenerated = Task::query()->findOrFail($generated->id);
        $this->assertNotSame($legacy->id, $preservedJob->workflow_id);
        $this->assertNotSame($phase->id, $preservedGenerated->workflow_phase_id);
        $this->assertNotSame($item->id, $preservedGenerated->task_pack_task_id);
        $this->assertDatabaseHas('task_pack_items', ['id' => $preservedGenerated->task_pack_task_id]);
    }

    private function workflowPair(string $code, string $name, bool $default): array
    {
        $legacy = Workflow::query()->create([
            'name' => $name,
            'slug' => strtolower($code).'-'.uniqid(),
            'is_active' => true,
        ]);

        $template = WorkflowTemplate::query()->create([
            'id' => $legacy->id,
            'workspace_id' => 1,
            'code' => $code,
            'name' => $name,
            'is_active' => true,
            'is_default' => $default,
            'version' => 1,
        ]);

        return [$legacy, $template];
    }

    private function taskPack(string $code, string $name, array $tasks): TaskPack
    {
        $pack = TaskPack::query()->create([
            'workspace_id' => 1,
            'code' => $code,
            'name' => $name,
            'slug' => strtolower($code).'-'.uniqid(),
            'is_active' => true,
        ]);

        foreach ($tasks as $index => $title) {
            $sharedId = max((int) TaskPackItem::query()->max('id'), (int) TaskPackTask::query()->max('id')) + 1;
            TaskPackTask::query()->create([
                'id' => $sharedId,
                'task_pack_id' => $pack->id,
                'title' => $title,
                'sequence' => $index + 1,
                'is_required' => true,
            ]);
            TaskPackItem::query()->create([
                'id' => $sharedId,
                'task_pack_id' => $pack->id,
                'title' => $title,
                'due_offset_days' => $index + 1,
                'is_required' => true,
                'sort_order' => $index,
            ]);
        }

        return $pack->refresh();
    }

    private function phase(Workflow $legacy, WorkflowTemplate $template, int $sequence, string $name, ?TaskPack $pack, bool $canStart): WorkflowPhase
    {
        return WorkflowPhase::query()->create([
            'workflow_id' => $legacy->id,
            'workflow_template_id' => $template->id,
            'task_pack_id' => $pack?->id,
            'sequence' => $sequence,
            'name' => $name,
            'short_name' => $name,
            'allow_job_start' => $canStart,
            'can_skip' => false,
            'is_skippable' => false,
            'requires_approval' => false,
            'auto_advance_on_ready' => false,
            'is_active' => true,
        ]);
    }
}
