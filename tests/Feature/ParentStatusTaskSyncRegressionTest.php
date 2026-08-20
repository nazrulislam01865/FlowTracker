<?php

namespace Tests\Feature;

use Tests\TestCase;

class ParentStatusTaskSyncRegressionTest extends TestCase
{
    public function test_order_task_sync_recalculates_parent_order_status(): void
    {
        $taskRules = file_get_contents(app_path('Services/OrderTaskFlagService.php'));
        $jobService = file_get_contents(app_path('Services/JobService.php'));

        $this->assertStringContainsString('app(JobService::class)->syncAutomaticStatus($job);', $taskRules);
        $this->assertStringContainsString('public function syncAutomaticStatus(FlowJob $job', $jobService);
        $this->assertStringContainsString("\$nextStatus = \$hasProgress ? 'In Progress' : 'New';", $jobService);
        $this->assertStringContainsString("'event' => 'job.status_auto_changed'", $jobService);
        $this->assertStringContainsString('use App\\Support\\BoardLaneResolver;', $jobService);

        $detail = file_get_contents(resource_path('views/components/jobs/detail.blade.php'));
        $this->assertStringContainsString("{{ \$job->status ?: 'New' }}", $detail);
    }

    public function test_inquiry_parent_sync_uses_task_status_even_when_legacy_timestamps_are_missing(): void
    {
        $service = file_get_contents(app_path('Services/InquiryService.php'));

        $this->assertStringContainsString('$isCompleted = fn (InquiryTask $task): bool', $service);
        $this->assertStringContainsString('|| $this->isCompletionTaskStatus((string) $task->status);', $service);
        $this->assertStringContainsString('$workingTask = $openTasks->first(', $service);
        $this->assertStringContainsString('$hasProgress = $completed > 0 || $workingTask !== null;', $service);
        $this->assertStringContainsString("\$currentTask = \$workingTask ?: \$openTasks->sortBy('sequence')->first();", $service);
    }

    public function test_inquiry_all_not_started_tasks_reset_parent_to_todo_and_assignee_is_centered(): void
    {
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $repair = file_get_contents(database_path('migrations/2026_08_20_193500_repair_inquiry_status_for_unstarted_taskflows.php'));
        $css = file_get_contents(resource_path('css/legacy/compatibility/flowtrack-inquiries.css'));

        $this->assertStringContainsString('$hasProgress = $completed > 0 || $workingTask !== null;', $service);
        $this->assertStringContainsString("'status' => 'To do'", $repair);
        $this->assertStringContainsString('$allNotStarted = $tasks->every(', $repair);
        $this->assertStringContainsString('.ft-inquiry-assignee-name{', $css);
        $this->assertStringContainsString('align-items:center;', $css);
        $this->assertStringContainsString('.ft-inquiry-assignee-display .ft-inline-avatar-slot{', $css);
    }

    public function test_order_task_status_refreshes_dependent_order_state_and_link_flow_stays_inline(): void
    {
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $jobs = file_get_contents(app_path('Livewire/Jobs/Index.php'));

        $this->assertStringContainsString('updateTaskStatusFromJob({{ $task->id }}, draftValue)).then(ok => { if(ok) $wire.$refresh(); })', $overview);
        $this->assertStringContainsString('wire:click.stop="openOverviewTaskLinkForm({{ $task->id }})"', $overview);
        $this->assertStringContainsString('wire:model="overviewTaskLinkUrl"', $overview);
        $this->assertStringContainsString('wire:submit.prevent="saveOverviewTaskLink({{ $task->id }})"', $overview);
        $this->assertStringContainsString('private function editableOverviewTask(int $taskId): Task', $jobs);
        $this->assertStringContainsString('$task = $this->editableOverviewTask($taskId);', $jobs);
        $linkRepair = file_get_contents(database_path('migrations/2026_08_20_194700_ensure_order_task_links_table_exists.php'));
        $this->assertStringContainsString("Schema::hasTable('task_links')", $linkRepair);
        $this->assertStringContainsString("Schema::create('task_links'", $linkRepair);
    }

}
