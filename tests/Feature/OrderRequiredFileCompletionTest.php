<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderRequiredFileCompletionTest extends TestCase
{
    public function test_required_order_task_completion_uses_task_document_link_not_category_text(): void
    {
        $service = file_get_contents(app_path('Services/TaskService.php'));
        $presenter = file_get_contents(app_path('Support/JobDetailPresenter.php'));

        $this->assertStringContainsString('$task->documents()->exists()', $service);
        $this->assertStringNotContainsString('$hasMatchingDocument', $service);
        $this->assertStringContainsString('$received = self::documentsForTask($job, $task)->count();', $presenter);
        $this->assertStringNotContainsString('strcasecmp(trim((string) $document->category)', $presenter);
    }
}
