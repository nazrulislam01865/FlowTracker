<?php

namespace Tests\Feature;

use Tests\TestCase;

class TaskDetailArtworkLatestOnlyTest extends TestCase
{
    public function test_task_details_artwork_attachments_show_only_latest_version_with_order_detail_naming(): void
    {
        $attachments = file_get_contents(resource_path('views/components/jobs/task-detail/attachments.blade.php'));
        $documents = file_get_contents(app_path('Services/DocumentService.php'));

        $this->assertStringContainsString("\$taskAutomationKey === 'ART_PREPARE_UPLOAD'", $attachments);
        $this->assertStringContainsString('collect([$latestArtworkDocument])->filter()->values()', $attachments);
        $this->assertStringContainsString('@foreach($visibleTaskDocuments as $doc)', $attachments);
        $this->assertStringContainsString('· Version {{ max(1, (int) $doc->version) }}', $attachments);
        $this->assertStringContainsString('· Latest', $attachments);
        $this->assertStringContainsString('Older artwork versions remain available in document/version history.', $attachments);

        // Artwork uploads remain one continuous version sequence, even when
        // a revised upload uses a different original file name.
        $this->assertStringContainsString("=== 'ART_PREPARE_UPLOAD'", $documents);
        $this->assertStringContainsString('if (! $isArtworkTask) {', $documents);
    }
}
