<?php

namespace Tests\Feature;

use App\Models\FlowJob;
use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\Task;
use App\Models\User;
use App\Services\AccessControlService;
use Tests\TestCase;

class CreatorOwnershipAndTaskPackAssigneeTest extends TestCase
{
    public function test_order_creator_can_edit_and_assign_every_task_in_their_order(): void
    {
        $creator = User::query()->firstOrFail();
        $job = FlowJob::query()->whereNotNull('created_by')->where('created_by', $creator->id)->first();
        if (! $job) $this->markTestSkipped('No creator-backed order fixture is available.');
        $task = Task::query()->where('flow_job_id', $job->id)->first();
        if (! $task) $this->markTestSkipped('No order task fixture is available.');

        $access = app(AccessControlService::class);
        $this->assertTrue($access->canEditJob($creator, $job));
        $this->assertTrue($access->canEditTask($creator, $task));
        $this->assertTrue($access->canAssignTask($creator, $task));
    }

    public function test_inquiry_creator_can_edit_and_assign_every_task_in_their_inquiry(): void
    {
        $inquiry = Inquiry::query()->whereNotNull('created_by')->first();
        if (! $inquiry) $this->markTestSkipped('No creator-backed inquiry fixture is available.');
        $creator = User::query()->findOrFail($inquiry->created_by);
        $task = InquiryTask::query()->where('inquiry_id', $inquiry->id)->first();
        if (! $task) $this->markTestSkipped('No inquiry task fixture is available.');

        $access = app(AccessControlService::class);
        $this->assertTrue($access->isInquiryCreator($creator, $inquiry));
        $this->assertTrue($access->canEditInquiryTask($creator, $task));
        $this->assertTrue($access->canAssignInquiryTask($creator, $task));
    }
}
