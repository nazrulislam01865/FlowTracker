<?php

namespace Tests\Feature;

use Tests\TestCase;

class InquiryPrototypeImplementationTest extends TestCase
{
    public function test_inquiry_create_uses_workflow_setup_and_task_pack_tasks(): void
    {
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $css = file_get_contents(public_path('css/flowtrack-inquiries.css'));

        $this->assertStringContainsString('<h1>Create Inquiry</h1>', $view);
        $this->assertStringContainsString('property="createWorkflowId"', $view);
        $this->assertStringContainsString('type="workflows"', $view);
        $this->assertStringContainsString('Workflow Setup is the source of truth', $view);
        $this->assertStringContainsString('<b>Title *</b>', $view);
        $this->assertStringContainsString('<b>Description</b>', $view);
        $this->assertStringNotContainsString('<b>Inquiry subject *</b>', $view);
        $this->assertStringNotContainsString('<b>Client requirement / notes</b>', $view);
        $this->assertStringContainsString("->options(\$user, 'workflows', 'create-inquiry', 'Inquiry', null, 20)", $component);
        $this->assertStringContainsString("'Inquiry Workflow'", $component);
        $this->assertStringContainsString('.inquiry-workflow-selector-wrap{width:100%;max-width:none', $css);
        $this->assertStringContainsString('Task Pack Setup', $view);
        $this->assertStringNotContainsString('Inquiry received date *', $view);
        $this->assertStringNotContainsString('<h2>Commercial information</h2>', $view);
        $this->assertStringNotContainsString('Request source</b><select wire:model="requestSource"', $view);
        $this->assertStringNotContainsString('wire:click="addCreateTask"', $view);

        $this->assertStringContainsString("app(InquiryService::class)->workflowRows", $component);
        $this->assertStringContainsString("'source_workflow_template_id' => (int) \$data['createWorkflowId']", $component);
        $this->assertStringContainsString('public function workflowRows(int $workflowId', $service);
        $this->assertStringContainsString("'phases.taskPack.items.defaultAssignee:id,name'", $service);
        $this->assertStringContainsString("'phases.taskPack.items.documentCategory:id,name'", $service);
    }

    public function test_inquiry_livewire_render_is_branch_and_tab_aware(): void
    {
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));

        $this->assertStringContainsString("if (\$this->showCreate) return view('livewire.inquiries.index', \$this->createPageData());", $component);
        $this->assertStringContainsString("if (\$this->selectedInquiryId) return view('livewire.inquiries.index', \$this->detailPageData(\$user));", $component);
        $this->assertStringContainsString("public string \$detailTab = 'overview';", $component);
        $this->assertStringContainsString("request()->query('tab', 'overview')", $component);
        $this->assertStringContainsString("in_array(\$tab, ['overview', 'workflow'], true)", $component);
        $this->assertStringNotContainsString("setDetailTab('documents')", $view);
        $this->assertStringNotContainsString("setDetailTab('activity')", $view);
        $this->assertStringContainsString("@include('livewire.inquiries._attachments')", $view);
        $this->assertStringContainsString("@include('livewire.inquiries._activity')", $view);
        $this->assertStringContainsString("\$this->detailTab === 'overview' ? \$service->documentsPage", $component);
        $this->assertStringContainsString("\$this->detailTab === 'overview' ? \$service->activityPage", $component);
        $this->assertStringContainsString('#[Renderless]', $component);
    }


    public function test_inquiry_detail_has_compact_workflow_and_inline_description(): void
    {
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $taskModel = file_get_contents(app_path('Models/InquiryTask.php'));
        $migration = file_get_contents(database_path('migrations/2026_08_09_000200_add_started_at_to_inquiry_tasks.php'));
        $css = file_get_contents(public_path('css/flowtrack-inquiries.css'));

        $this->assertStringContainsString('id="tab-workflow"', $view);
        $this->assertStringContainsString("updateInquiryField('requirement_notes'", $view);
        $this->assertStringContainsString('deleteTaskDocument(', $view);
        $this->assertStringNotContainsString('<small>Client contact</small>', $view);
        $this->assertStringNotContainsString('<small>Office address</small>', $view);
        $this->assertStringNotContainsString('<small>Workflow</small>', $view);
        $this->assertStringContainsString("'requirement_notes'", $component);
        $this->assertStringContainsString('public function removeTaskDocument', $service);
        $this->assertStringContainsString('#tab-workflow .ft-inquiry-task-document-row', $css);
        $this->assertStringContainsString('>Taskflow</button>', $view);
        $this->assertStringContainsString('<h2>Inquiry Taskflow</h2>', $view);
        $this->assertStringNotContainsString("<small>Assignee</small>", $view);
        $this->assertStringContainsString('$service->inquiryStatusOptions()', $component);
        $this->assertStringContainsString('ft-inquiry-overview-task-card', $view);
        $this->assertStringContainsString('<span>Started at</span>', $view);
        $this->assertStringContainsString('View only', $view);
        $this->assertStringContainsString("'started_at'=>'datetime'", $taskModel);
        $this->assertStringContainsString("timestamp('started_at')", $migration);
        $this->assertStringContainsString("'started_at' => ! \$draft && \$index === 0 ? now() : null", $service);
        $this->assertStringContainsString("'started_at' => \$next->started_at ?: now()", $service);
        $this->assertStringContainsString("elseif (\$this->detailTab === 'overview')", $component);
        $this->assertStringContainsString('.ft-inquiry-overview-task-columns', $css);
        $this->assertStringContainsString('class="inquiry-list-table"', $view);
        $this->assertStringContainsString('ft-inquiry-created-by', $view);
        $this->assertStringContainsString('View <span aria-hidden="true">→</span>', $view);
        $this->assertStringContainsString('min-width:1420px', $css);
        $this->assertStringNotContainsString('<span class="sub">Assignee</span>', $view);
        $this->assertStringNotContainsString('<span class="sub">Due date</span>', $view);
    }

    public function test_inquiry_tasks_are_not_merged_into_my_task(): void
    {
        $myWork = file_get_contents(app_path('Livewire/MyWork/Index.php'));

        $this->assertStringNotContainsString('myTaskGroups(auth()->user()', $myWork);
        $this->assertStringNotContainsString('updateInquiryTaskStatus', $myWork);
        $this->assertStringNotContainsString('updateInquiryTaskDueDate', $myWork);
    }
}
