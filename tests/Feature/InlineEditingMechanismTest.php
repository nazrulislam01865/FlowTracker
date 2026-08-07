<?php

namespace Tests\Feature;

use Tests\TestCase;

class InlineEditingMechanismTest extends TestCase
{
    public function test_inline_save_actions_are_renderless_so_the_page_is_not_requeried_after_each_field_save(): void
    {
        $jobs = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $board = file_get_contents(app_path('Livewire/Board/Index.php'));
        $myWork = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $administration = file_get_contents(app_path('Livewire/Administration/Index.php'));

        foreach ([
            'updateJobOwner',
            'updateJobCoordinator',
            'updateJobDeliveryDate',
            'updateJobPriority',
            'updateJobHealth',
            'updateJobTextField',
            'updateJobItem',
            'updateTaskAssigneeFromJob',
            'updateTaskDueDateFromJob',
            'updateTaskStatusFromJob',
            'updateSelectedTaskField',
        ] as $method) {
            $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function '.preg_quote($method, '/').'\b/', $jobs);
        }

        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function updateTaskDueDate\b/', $board);
        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function updateJobDueDate\b/', $board);
        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function updateTaskDueDate\b/', $myWork);
        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function setMatrixAction\b/', $administration);
        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function setModuleScope\b/', $administration);
        $this->assertMatchesRegularExpression('/#\[Renderless\]\s+public function assignRole\b/', $administration);
    }

    public function test_inline_actions_return_safe_structured_results_instead_of_leaking_exceptions(): void
    {
        $trait = file_get_contents(app_path('Livewire/Concerns/HandlesInlineEdits.php'));

        $this->assertStringContainsString("'ok' => true", $trait);
        $this->assertStringContainsString('DB::transaction($callback, 2)', $trait);
        $this->assertStringContainsString("'ok' => false", $trait);
        $this->assertStringContainsString('catch (ValidationException', $trait);
        $this->assertStringContainsString('catch (QueryException', $trait);
        $this->assertStringContainsString('catch (Throwable', $trait);
        $this->assertStringContainsString('Your previous value was restored. Please retry.', $trait);
    }

    public function test_post_commit_notification_failures_do_not_turn_a_saved_inline_edit_into_a_false_failure(): void
    {
        $notifications = file_get_contents(app_path('Services/NotificationService.php'));

        $this->assertStringContainsString('Post-commit notification work failed.', $notifications);
        $this->assertStringContainsString('DB::afterCommit($safeCallback)', $notifications);
    }

    public function test_all_known_inline_edit_views_use_the_shared_optimistic_runtime(): void
    {
        $views = [
            resource_path('views/components/jobs/table.blade.php'),
            resource_path('views/components/jobs/detail.blade.php'),
            resource_path('views/components/jobs/detail-overview.blade.php'),
            resource_path('views/components/jobs/detail-workflow.blade.php'),
            resource_path('views/components/jobs/task-detail.blade.php'),
            resource_path('views/components/board/task-card.blade.php'),
            resource_path('views/components/board/job-card.blade.php'),
            resource_path('views/livewire/administration/index.blade.php'),
        ];

        foreach ($views as $view) {
            $source = file_get_contents($view);
            $this->assertStringContainsString('FlowTrackInlineEdit', $source, $view);
            $this->assertStringContainsString('inline-save-state', $source, $view);
            $this->assertDoesNotMatchRegularExpression('/wire:change=\"update(?:Job|Task|Selected)/', $source, $view);
        }
    }

    public function test_inline_runtime_supports_optimistic_save_rollback_and_retry(): void
    {
        $runtime = file_get_contents(public_path('js/flowtrack-inline-editing.js'));

        $this->assertStringContainsString("this.status = 'saving'", $runtime);
        $this->assertStringContainsString("this.status = 'saved'", $runtime);
        $this->assertStringContainsString("this.status = 'error'", $runtime);
        $this->assertStringContainsString('this.value = previousValue', $runtime);
        $this->assertStringContainsString('retry()', $runtime);
        $this->assertStringContainsString('requestSequence', $runtime);
        $this->assertStringNotContainsString('flowtrackInlineSync', $runtime);
        $this->assertStringNotContainsString('All changes saved', $runtime);
        $this->assertStringContainsString('flowtrackInlineToasts', $runtime);
    }

    public function test_notifications_remain_in_notification_center_without_realtime_popups(): void
    {
        $tasks = file_get_contents(app_path('Services/TaskService.php'));
        $notifications = file_get_contents(app_path('Services/NotificationService.php'));
        $notificationModel = file_get_contents(app_path('Models/FlowNotification.php'));
        $profile = file_get_contents(app_path('Livewire/Profile/Index.php'));
        $runtime = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("$isAssignment ? 'Task assigned: '", $tasks);
        $this->assertStringContainsString("'Task assigned: '.$task->title", $notifications);
        $this->assertStringNotContainsString('hide_task_assignment_notifications', $notificationModel);
        $this->assertStringContainsString("['Task assignments', 'When a task is assigned to you']", $profile);
        $this->assertStringNotContainsString('showRealtimeToast', $runtime);
        $this->assertStringNotContainsString('ft-realtime-toast', $runtime);
        $this->assertStringNotContainsString('isSuppressedRealtimeNotification', $runtime);
        $this->assertStringContainsString('markRealtimeUnread(payload);', $runtime);
        $this->assertStringContainsString("window.Livewire?.dispatch?.('flowtrack-notification');", $runtime);
    }
}
