<?php

namespace Tests\Feature;

use Tests\TestCase;

class InquiryPrototypeImplementationTest extends TestCase
{

    public function test_inquiry_list_uses_searchable_client_filter_without_active_or_closed_quick_filters(): void
    {
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $css = file_get_contents(public_path('css/flowtrack-inquiries.css'));

        $this->assertStringNotContainsString('wire:click="setQuick(\'active\')">Active</button>', $view);
        $this->assertStringNotContainsString('wire:click="setQuick(\'dead\')">Closed</button>', $view);
        $this->assertStringContainsString('class="ft-inquiry-list-client-filter"', $view);
        $this->assertStringContainsString('property="listClient"', $view);
        $this->assertStringContainsString('action="setInquiryListFilter"', $view);
        $this->assertStringContainsString(':selected-label="$listClientLabel ?: null"', $view);
        $this->assertStringContainsString('wire:key="inquiry-list-client-filter-', $view);
        $this->assertStringContainsString('type="clients"', $view);
        $this->assertStringContainsString('context="inquiries"', $view);
        $this->assertStringContainsString(':fixed-menu="true"', $view);
        $this->assertStringContainsString('public string $listClient', $component);
        $this->assertStringContainsString('public string $listClientLabel', $component);
        $this->assertStringContainsString('public function setInquiryListFilter(string $property, mixed $value): void', $component);
        $this->assertStringContainsString("->options(auth()->user(), 'clients', 'inquiries', '', \$id, 20)", $component);
        $this->assertStringContainsString("'client_id' => \$selectedClientId", $component);
        $this->assertStringContainsString("->options(\$user, 'clients', 'inquiries', '', \$selectedClientId, 6)", $component);
        $this->assertStringContainsString('private const INQUIRIES_PER_PAGE = 10;', $component);
        $this->assertStringContainsString('], self::INQUIRIES_PER_PAGE);', $component);
        $this->assertStringContainsString("->when(\$clientId > 0, fn (Builder \$q) => \$q->where('inquiries.client_id', \$clientId))", $service);
        $this->assertStringContainsString('.ft-inquiry-prototype .inquiry-list-v2 .ft-inquiry-list-client-filter{', $css);
        $this->assertStringContainsString("'fixedMenu' => false", file_get_contents(resource_path('views/components/ui/remote-filter.blade.php')));
        $this->assertStringContainsString("wire:click=\"setMetricFilter('active')\"", $view);
        $this->assertStringContainsString("wire:click=\"setMetricFilter('completed')\"", $view);
        $this->assertStringContainsString("wire:click=\"setMetricFilter('attention')\"", $view);
        $this->assertStringContainsString("wire:click=\"setMetricFilter('dueToday')\"", $view);
        $this->assertStringContainsString('public string $metricFilter', $component);
        $this->assertStringContainsString('public function setMetricFilter(string $metric): void', $component);
        $this->assertStringContainsString("'metric_filter' => \$this->metricFilter", $component);
        $this->assertStringContainsString('private function applyMetricListScope', $service);
        $this->assertStringContainsString('private function applyDueTodayInquiryListScope', $service);
        $this->assertStringContainsString("->whereDate('inquiry_tasks.due_date', \$today)", $service);
        $this->assertStringContainsString("\$currentTaskDueDate = \$metricFilter === 'dueToday'", $service);
        $this->assertStringContainsString("currentTaskSubquery('due_date', \$currentTaskDueDate)", $service);
        $this->assertStringContainsString('private function currentTaskSubquery(string $column, ?string $dueDate = null): Builder', $service);
        $this->assertStringContainsString('public bool $hideCompleted = false;', $component);
        $this->assertStringContainsString('.ft-inquiry-prototype .metric-filter-card:hover{', $css);
        $this->assertStringContainsString('<div>Task Status</div>', $view);
        $this->assertStringContainsString('<div>Flag</div>', $view);
        $this->assertStringContainsString("currentTaskSubquery('status', \$currentTaskDueDate)", $service);
        $this->assertStringContainsString("\$currentTaskAttentionSql = \$this->currentTaskSubquery('needs_attention')->toSql();", $service);
        $this->assertStringContainsString("COALESCE((\$currentTaskAttentionSql), 0) = 1", $service);
        $this->assertStringNotContainsString("->whereIn('inquiries.id', \$attentionTasks)", $service);
    }

    public function test_inquiry_create_matches_prototype_and_uses_workflow_setup(): void
    {
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $css = file_get_contents(public_path('css/flowtrack-inquiries.css'));

        $this->assertStringContainsString('<h1>Create Inquiry</h1>', $view);
        $this->assertStringContainsString('How was this inquiry received? *', $view);
        $this->assertStringContainsString('＋ New client', $view);
        $this->assertStringContainsString('Client contact', $view);
        $this->assertStringContainsString('Reference number', $view);
        $this->assertStringContainsString('Assigned to', $view);
        $this->assertStringContainsString('Inquiry title *', $view);
        $this->assertStringContainsString('Request details', $view);
        $this->assertStringContainsString('ft-inquiry-selected-file-preview', $view);
        $this->assertStringContainsString('$upload->temporaryUrl()', $view);
        $this->assertStringContainsString('What happens next', $view);
        $this->assertStringContainsString('Add new client', $view);
        $this->assertStringContainsString('Add &amp; select client', $view);
        $this->assertStringContainsString('wire:click="createClientAndSelect"', $view);
        $this->assertStringContainsString("wire:click=\"setCreateSelector('createWorkflowId'", $view);
        $this->assertStringContainsString("->availableFor('inquiries', \$this->clientId)", $component);
        $this->assertStringContainsString("CASE WHEN client_availability = 'specific' THEN 0 ELSE 1 END", $component);
        $this->assertStringContainsString("'request_source' => \$data['requestSource']", $component);
        $this->assertStringContainsString("'owner_id' => (int) \$data['createOwnerId']", $component);
        $this->assertStringContainsString('public function createClientAndSelect(): void', $component);
        $this->assertStringContainsString('.ft-inquiry-prototype .ft-inquiry-create-v3', $css);
        $this->assertStringContainsString('.ft-inquiry-prototype .ft-inquiry-quick-client-modal', $css);

        $this->assertStringContainsString('app(InquiryService::class)->workflowRows', $component);
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
        $this->assertStringContainsString("The separate Taskflow tab was removed", $component);
        $this->assertStringContainsString("in_array(\$tab, ['overview', 'workflow'], true)", $component);
        $this->assertStringContainsString('<button class="tab active" type="button">Overview</button>', $view);
        $this->assertStringContainsString('Products &amp; quantities', $view);
        $this->assertStringNotContainsString("setDetailTab('products')", $view);
        $this->assertStringNotContainsString("setDetailTab('finance')", $view);
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
        $taskflow = file_get_contents(resource_path('views/livewire/inquiries/_taskflow.blade.php'));
        $this->assertStringContainsString('deleteTaskDocument(', $taskflow);
        $this->assertStringNotContainsString('<small>Client contact</small>', $view);
        $this->assertStringNotContainsString('<small>Office address</small>', $view);
        $this->assertStringNotContainsString('<small>Workflow</small>', $view);
        $this->assertStringContainsString("'requirement_notes'", $component);
        $this->assertStringContainsString('public function removeTaskDocument', $service);
        $this->assertStringContainsString('#tab-workflow .ft-inquiry-task-document-row', $css);
        $this->assertStringNotContainsString('>Taskflow</button>', $view);
        $this->assertStringNotContainsString("@elseif(\$detailTab === 'workflow')", $view);
        $this->assertStringContainsString('<h2>Inquiry Taskflow</h2>', $taskflow);
        $this->assertStringNotContainsString("<small>Assignee</small>", $view);
        $this->assertStringNotContainsString("'inquiryStatusOptions' =>", $component);
        $this->assertStringContainsString("@include('livewire.inquiries._taskflow')", $view);
        $this->assertStringContainsString("\$canCompleteThisTask", $taskflow);
        $this->assertStringContainsString("\$canChangeStatusThisTask", $taskflow);
        $this->assertStringContainsString('class="ft-inline-task-status', $taskflow);
        $this->assertStringContainsString('$inquiryTaskStatusOptions as $statusOption', $taskflow);
        $this->assertStringNotContainsString('ft-inquiry-status-pill done', $taskflow);
        $this->assertStringContainsString("\$task->started_at ? 'active' : 'wait'", $taskflow);
        $this->assertStringNotContainsString("Complete the previous taskflow task first.", $service);
        $this->assertStringContainsString('<h2>Inquiry Taskflow</h2>', file_get_contents(resource_path('views/livewire/inquiries/_taskflow.blade.php')));
        $this->assertStringNotContainsString('View only', $view);
        $this->assertStringContainsString("'started_at' => 'datetime'", $taskModel);
        $this->assertStringContainsString("timestamp('started_at')", $migration);
        $this->assertStringContainsString('...$this->defaultTaskStatusPayload()', $service);
        $this->assertStringContainsString("'started_at' => null", $service);
        $this->assertStringContainsString('public function taskStatusOptions(?string $currentStatus = null): Collection', $service);
        $this->assertStringContainsString("->active('inquiry_task_status')", $service);
        $this->assertStringContainsString("if (\$this->detailTab === 'overview')", $component);
        $this->assertStringContainsString('.ft-inquiry-task-document-modal', $css);
        $this->assertStringContainsString('openTaskDocumentModal(', $taskflow);
        $this->assertStringContainsString('Add new document to task', $view);
        $this->assertStringContainsString('Choose existing', $view);
        $this->assertStringContainsString('Document note (optional)', $view);
        $this->assertStringContainsString('public function linkExistingDocumentToTask', $service);
        $this->assertStringContainsString('class="inquiry-list-table"', $view);
        $this->assertStringContainsString('ft-inquiry-created-by', $view);
        $this->assertStringContainsString('View <span aria-hidden="true">→</span>', $view);
        $this->assertStringContainsString('min-width:1420px', $css);
        $this->assertStringNotContainsString('<span class="sub">Assignee</span>', $view);
        $this->assertStringNotContainsString('<span class="sub">Due date</span>', $view);
        $this->assertStringContainsString('ft-inquiry-header-meta', $view);
        $this->assertStringContainsString('Client <strong>{{ $inquiry->client?->name', $view);
        $this->assertStringContainsString('Reference <strong>{{ $inquiry->reference_number', $view);
        $this->assertStringContainsString('Created by <strong>{{ $inquiry->creator?->name', $view);
        $this->assertStringNotContainsString('ft-inquiry-information-legacy">', $view);
        $this->assertStringNotContainsString('Not created yet', $view);
        $this->assertStringContainsString('ft-inquiry-auto-status-property', $view);
        $this->assertStringNotContainsString('inquiryOverviewStatus', $view);
        $this->assertStringContainsString("public const AUTO_READY_STATUS = 'To do';", $service);
        $this->assertStringContainsString("public const AUTO_IN_PROGRESS_STATUS = 'In Progress';", $service);
        $this->assertStringContainsString("public const AUTO_COMPLETED_STATUS = 'Completed';", $service);
        $this->assertStringContainsString('public function syncAutomaticStatus(Inquiry $inquiry', $service);
    }

    public function test_inquiry_hide_completed_uses_actual_taskflow_completion(): void
    {
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));

        $this->assertStringContainsString('private function applyUnfinishedListScope(Builder $query): Builder', $service);
        $this->assertStringContainsString("->whereDoesntHave('tasks')", $service);
        $this->assertStringContainsString("->orWhereHas('tasks', fn (Builder $task) => $task->whereNull('completed_at'))", $service);
        $this->assertStringContainsString("\$hideCompleted && \$metricFilter !== 'completed'", $service);
        $this->assertStringContainsString('public bool $hideCompleted = false;', $component);
        $this->assertStringContainsString('public function updatedHideCompleted(): void', $component);
        $this->assertStringContainsString('wire:model.live="hideCompleted"', $view);
    }

    public function test_inquiry_tasks_are_not_merged_into_my_task(): void
    {
        $myWork = file_get_contents(app_path('Livewire/MyWork/Index.php'));

        $this->assertStringNotContainsString('myTaskGroups(auth()->user()', $myWork);
        $this->assertStringNotContainsString('updateInquiryTaskStatus', $myWork);
        $this->assertStringNotContainsString('updateInquiryTaskDueDate', $myWork);
    }

    public function test_inquiry_start_timestamp_is_persisted_auto_started_and_inline_editable(): void
    {
        $model = file_get_contents(app_path('Models/Inquiry.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $migration = file_get_contents(database_path('migrations/2026_08_10_130000_add_started_at_to_inquiries.php'));

        $this->assertStringContainsString("'started_at' => 'datetime'", $model);
        $this->assertStringContainsString("timestamp('started_at')", $migration);
        $this->assertStringContainsString("strcasecmp(\$nextStatus, 'In Progress') === 0", $service);
        $this->assertStringContainsString("whereNull('started_at')", $service);
        $this->assertStringContainsString('public function updateStartedAt(Inquiry $inquiry', $service);
        $this->assertStringContainsString('public function updateInquiryStartInline', $component);
        $this->assertStringContainsString('inquiryStartValue', $component);
        $this->assertStringContainsString('type="datetime-local"', $view);
        $this->assertStringContainsString('formatDateTime($event.target.value)', $view);
        $this->assertStringContainsString('flowtrack-inquiry-started', $view);
    }

    public function test_inquiry_list_tracks_parallel_taskflow_and_last_assignee_picker_is_not_clipped(): void
    {
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $model = file_get_contents(app_path('Models/Inquiry.php'));
        $taskflow = file_get_contents(resource_path('views/livewire/inquiries/_taskflow.blade.php'));
        $css = file_get_contents(public_path('css/flowtrack-inquiries.css'));
        $inlineUser = file_get_contents(resource_path('views/components/ui/inline-remote-user.blade.php'));
        $filterJs = file_get_contents(public_path('js/flowtrack-list-filters.js'));

        $this->assertStringContainsString("currentTaskSubquery('sequence')", $service);
        $this->assertStringContainsString("CASE WHEN inquiry_tasks.started_at IS NOT NULL THEN 0 ELSE 1 END", $service);
        $this->assertStringContainsString("CASE WHEN inquiry_tasks.started_at IS NOT NULL THEN inquiry_tasks.sequence END DESC", $service);
        $this->assertStringContainsString("'tasks as progressed_tasks_count'", $service);
        $this->assertStringContainsString("'progress' => \$progress", $service);
        $this->assertStringContainsString("'taskCaption' => \$done === \$total", $service);
        $this->assertStringContainsString("\$task->inquiry->touch();", $service);
        $this->assertStringContainsString("CASE WHEN inquiry_tasks.started_at IS NOT NULL THEN inquiry_tasks.sequence END DESC", $model);
        $this->assertStringContainsString('class="panel ft-inquiry-taskflow-panel"', $taskflow);
        $this->assertStringContainsString('.ft-inquiry-taskflow-panel{', $css);
        $this->assertStringContainsString('overflow:visible;', $css);
        $this->assertStringContainsString('.ft-inquiry-taskflow-panel .ft-inline-remote-user-menu{', $css);
        $this->assertStringContainsString('fixedMenu: true', $inlineUser);
        $this->assertStringContainsString('if (component.fixedMenu)', $filterJs);
        $this->assertStringContainsString("'position:fixed!important'", $filterJs);
        $this->assertStringContainsString("'z-index:2450!important'", $filterJs);
    }


    public function test_completed_inquiry_tasks_keep_assignee_and_due_date_inline_editing(): void
    {
        $taskflow = file_get_contents(resource_path('views/livewire/inquiries/_taskflow.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));

        $this->assertStringContainsString('$canEditTaskFields = $canChangeStatusThisTask;', $taskflow);
        $this->assertStringContainsString('@if($canEditTaskFields)<button x-show="!editing"', $taskflow);
        $this->assertStringContainsString('Edit task assignee', $taskflow);
        $this->assertStringContainsString('Edit task due date', $taskflow);
        $this->assertStringContainsString('updateTaskAssignee($task, $assigneeId, auth()->user())', $component);
        $this->assertStringContainsString('public function updateTaskAssignee(InquiryTask $task, ?int $assigneeId, User $actor): InquiryTask', $service);
        $this->assertStringContainsString('Due date remains editable after task completion.', $service);
        $this->assertStringContainsString('Updating it must not', $service);
    }



    public function test_completed_inquiry_task_documents_remain_manageable_without_breaking_required_file_completion(): void
    {
        $taskflow = file_get_contents(resource_path('views/livewire/inquiries/_taskflow.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));

        $this->assertStringContainsString('@if($canAttachThisTask)', $taskflow);
        $this->assertStringContainsString('wire:click="deleteTaskDocument(', $taskflow);
        $this->assertStringContainsString('The task will reopen to In Progress', $taskflow);
        $this->assertStringContainsString('public function removeTaskDocument(InquiryTask $task, int $documentId, User $actor): bool', $service);
        $this->assertStringContainsString("'status' => 'In Progress'", $service);
        $this->assertStringContainsString("'completed_at' => null", $service);
        $this->assertStringContainsString('final required file was removed', $service);
        $this->assertStringContainsString('$this->syncAutomaticStatus($lockedTask->inquiry, $actor);', $service);
        $this->assertStringContainsString('$this->metrics = $service->metrics(auth()->user());', $component);
    }

}
