<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderArtworkMultiFileUploadTest extends TestCase
{
    public function test_order_artwork_upload_accepts_and_persists_a_file_set_as_one_revision(): void
    {
        $modal = file_get_contents(resource_path('views/components/jobs/order-detail/document-modal.blade.php'));
        $jobComponent = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $jobUploads = file_get_contents(app_path('Livewire/Jobs/Concerns/ManagesOrderTaskResources.php'));
        $orderList = file_get_contents(app_path('Livewire/Orders/Index.php'));
        $documents = file_get_contents(app_path('Services/DocumentService.php'));

        $this->assertStringContainsString("\$allowMultipleUploads = \$automationKey === 'ART_PREPARE_UPLOAD'", $modal);
        $this->assertStringContainsString('@if($inputAllowsMultiple) multiple @endif', $modal);
        $this->assertStringContainsString('Up to 10 files', $modal);
        $this->assertStringContainsString('removeOverviewTaskDocumentUpload({{ $index }})', $modal);

        $this->assertStringContainsString('public array $overviewTaskDocumentUpload = [];', $jobComponent);
        foreach ([$jobUploads, $orderList] as $component) {
            $this->assertStringContainsString("'overviewTaskDocumentUpload' => ['required', 'array', 'min:1'", $component);
            $this->assertStringContainsString("'overviewTaskDocumentUpload.*' => AttachmentUpload::itemRules", $component);
            $this->assertStringContainsString('storeMany($this->overviewTaskDocumentUpload', $component);
            $this->assertStringContainsString('storeArtworkRevision(', $component);
            $this->assertStringContainsString('catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception)', $component);
            $this->assertStringContainsString("\$this->addError(\n                    'overviewTaskDocumentUpload'", $component);
            $this->assertStringContainsString('function removeOverviewTaskDocumentUpload(', $component);
        }

        $this->assertStringContainsString('public function storeMany(', $documents);
        $this->assertStringContainsString('return DB::transaction(function () use ($files, $data, $user, $permissionModule, &$storedPaths)', $documents);
        $this->assertStringContainsString("\$fileData['artwork_batch_version'] = \$artworkBatchVersion", $documents);
        $this->assertStringContainsString('$version = $isArtworkTask && $batchVersion > 0', $documents);
    }

    public function test_latest_artwork_batch_is_visible_and_all_files_are_emailed(): void
    {
        $row = file_get_contents(resource_path('views/components/jobs/order-detail/task-row.blade.php'));
        $taskAttachments = file_get_contents(resource_path('views/components/jobs/task-detail/attachments.blade.php'));
        $workflowModal = file_get_contents(resource_path('views/components/jobs/order-detail/workflow-action-modal.blade.php'));
        $emailService = file_get_contents(app_path('Services/Orders/OrderWorkflowEmailService.php'));

        $this->assertStringContainsString("where('version', \$latestArtworkVersion)", $row);
        $this->assertStringContainsString('? $latestArtworkDocuments', $row);
        $this->assertStringContainsString("where('version', \$latestArtworkVersion)", $taskAttachments);
        $this->assertStringContainsString("where('version', \$artworkVersion)", $workflowModal);

        $this->assertStringContainsString('private function sourceDocuments(', $emailService);
        $this->assertStringContainsString("where('version', \$latestVersion)", $emailService);
        $this->assertStringContainsString('attachments: $attachments', $emailService);
        $this->assertStringContainsString("'document_ids' => \$documents->pluck('id')", $emailService);
    }
}
