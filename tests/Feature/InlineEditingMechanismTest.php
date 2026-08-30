<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Support\OrderPhase5Source;

class InlineEditingMechanismTest extends TestCase
{
    public function test_livewire_methods_never_repeat_the_renderless_attribute(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path('Livewire'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($file->getPathname());
            $this->assertDoesNotMatchRegularExpression(
                '/(#\[Renderless\]\s*){2,}/',
                $source,
                'Duplicate #[Renderless] attribute found in '.$file->getPathname()
            );
        }
    }

    public function test_inline_save_actions_are_renderless_so_the_page_is_not_requeried_after_each_field_save(): void
    {
        $jobs = OrderPhase5Source::livewire();
        $board = file_get_contents(app_path('Livewire/Board/Index.php'));
        $myWork = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $administration = file_get_contents(app_path('Livewire/Administration/Index.php'));

        foreach ([
            'updateJobUrgencies',
            'updateJobOwner',
            'updateJobCoordinator',
            'updateJobDeliveryDate',
            'updateJobPriority',
            'updateJobTextField',
            'updateJobShippingField',
            'updateJobShippingPhone',
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
            resource_path('views/components/jobs/order-detail/header.blade.php'),
            resource_path('views/components/jobs/order-detail/overview-card.blade.php'),
            resource_path('views/components/jobs/order-detail/planning.blade.php'),
            resource_path('views/components/jobs/order-detail/task-row.blade.php'),
            resource_path('views/components/jobs/task-detail.blade.php'),
            resource_path('views/components/board/task-card.blade.php'),
            resource_path('views/components/board/job-card.blade.php'),
        ];

        foreach ($views as $view) {
            $source = file_get_contents($view);
            $this->assertStringContainsString('window.FlowTrack.ui.inlineEdit', $source, $view);
            $this->assertStringContainsString('inline-save-state', $source, $view);
            $this->assertDoesNotMatchRegularExpression('/wire:change=\"update(?:Job|Task|Selected)/', $source, $view);
        }

        // Shipping and product edits are intentionally grouped/atomic rather than
        // per-field optimistic controls. They still cross renderless Livewire
        // action boundaries and therefore do not belong in the inline-runtime list.
        $shipping = file_get_contents(resource_path('views/components/jobs/order-detail/shipping.blade.php'));
        $products = file_get_contents(resource_path('views/components/jobs/order-detail/products.blade.php'));
        $this->assertStringContainsString('updateJobShippingDetails', $shipping);
        $this->assertStringContainsString('openEditOrderProductModal', $products);

        // Administration still uses the shared optimistic runtime for the role matrix,
        // but user role assignment is now an explicit multi-role edit rather than a
        // single inline role select. The matrix has its own save summary.
        $administration = file_get_contents(resource_path('views/livewire/administration/index.blade.php'));
        $this->assertStringContainsString('window.FlowTrack.ui.inlineEdit', $administration);
        $this->assertStringContainsString('ft-matrix-save-summary', $administration);
    }

    public function test_order_overview_uses_only_shipment_urgency_and_keeps_owner_editing_modular(): void
    {
        $planning = file_get_contents(resource_path('views/components/jobs/order-detail/planning.blade.php'));
        $header = file_get_contents(resource_path('views/components/jobs/order-detail/header.blade.php'));
        $jobs = OrderPhase5Source::livewire();
        $service = $this->jobServiceSource();

        $this->assertStringContainsString("updateJobUrgencies({{ \$job->id }}, 'shipment'", $planning);
        $this->assertStringNotContainsString("updateJobUrgencies({{ \$job->id }}, 'production'", $planning);
        $this->assertStringContainsString('Shipment urgency', $planning);
        $this->assertStringContainsString('order-owner-field', $planning);
        $this->assertStringContainsString('aria-label="Edit order owner"', $header);
        $this->assertStringContainsString('Select only one ', $jobs);
        $this->assertStringContainsString('accepts only one selection', $service);
        $this->assertStringContainsString('public function updateJobUrgencies', $jobs);
        $this->assertStringContainsString('public function updateUrgencies', $service);
    }

    public function test_inline_runtime_supports_optimistic_save_rollback_and_retry(): void
    {
        $runtime = file_get_contents(resource_path('js/components/inline-edit.js'));

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
        $notificationRuntime = file_get_contents(resource_path('js/features/notifications.js'));

        $this->assertStringContainsString("\$isAssignment ? 'Task assigned: '", $tasks);
        $this->assertStringContainsString("'Task assigned: '.\$task->title", $notifications);
        $this->assertStringNotContainsString('hide_task_assignment_notifications', $notificationModel);
        $this->assertStringContainsString("['Task assignments', 'When a task is assigned to you']", $profile);
        $this->assertStringNotContainsString('showRealtimeToast', $runtime);
        $this->assertStringNotContainsString('ft-realtime-toast', $runtime);
        $this->assertStringNotContainsString('isSuppressedRealtimeNotification', $runtime);
        $this->assertStringContainsString('syncUnreadCount', $notificationRuntime);
        $this->assertStringContainsString('REALTIME_EVENTS.NOTIFICATION', $notificationRuntime);
        $this->assertStringContainsString('window.Livewire?.dispatch?.(LIVEWIRE_EVENTS.NOTIFICATION);', $notificationRuntime);
    }
}
