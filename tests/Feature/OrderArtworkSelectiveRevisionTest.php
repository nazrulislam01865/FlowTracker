<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderArtworkSelectiveRevisionTest extends TestCase
{
    public function test_artwork_review_can_preview_every_file_and_request_multiple_file_specific_revisions(): void
    {
        $modal = file_get_contents(resource_path('views/components/jobs/order-detail/workflow-action-modal.blade.php'));
        $actions = file_get_contents(app_path('Services/OrderWorkflowActionService.php'));

        $this->assertStringContainsString('Current artwork files', $modal);
        $this->assertStringContainsString('Select a file below to preview it on the left.', $modal);
        $this->assertStringContainsString('selectedArtworkId', $modal);
        $this->assertStringContainsString('Which artwork needs revision?', $modal);
        $this->assertStringContainsString('type="checkbox"', $modal);
        $this->assertStringContainsString('wire:model.live="orderWorkflowActionPayload.revision_document_ids"', $modal);
        $this->assertStringContainsString('Each selected artwork opens its own required change and supporting attachments.', $modal);
        $this->assertStringContainsString('wire:model="orderWorkflowActionRevisionComments.{{ $revisionDocumentId }}"', $modal);
        $this->assertStringContainsString('multiple', $modal);
        $this->assertStringContainsString('wire:model="orderWorkflowActionRevisionAttachments.{{ $revisionDocumentId }}"', $modal);
        $this->assertStringNotContainsString("@if(\$automationKey !== 'ART_INTERNAL_REVIEW')", $modal);
        $this->assertStringContainsString("\$isArtworkRevisionRequest = \$step === 'revision';", $modal);
        $this->assertStringContainsString('class="danger ft-artwork-revision-submit"', $modal);

        $this->assertStringContainsString("'revision_document_ids' => []", $actions);
        $this->assertStringContainsString("'revision_items' => []", $actions);
        $this->assertStringContainsString("'revision_document_ids' => \$revisionDocumentIds", $actions);
        $this->assertStringContainsString("'revision_document_id' => \$requestedDocumentId", $actions);
        $this->assertStringContainsString("'source_artwork_version' => \$latestVersion", $actions);
        $this->assertStringContainsString("'revision_selection_pending' => false", $actions);
        $this->assertStringContainsString('Select at least one artwork file that needs revision.', $actions);
    }

    public function test_revised_upload_replaces_selected_files_and_carries_unselected_artwork_forward(): void
    {
        $documents = file_get_contents(app_path('Services/DocumentService.php'));
        $uploadModal = file_get_contents(resource_path('views/components/jobs/order-detail/document-modal.blade.php'));
        $jobUploads = file_get_contents(app_path('Livewire/Jobs/Concerns/ManagesOrderTaskResources.php'));
        $orderUploads = file_get_contents(app_path('Livewire/Orders/Index.php'));

        $this->assertStringContainsString('public function pendingArtworkRevision(', $documents);
        $this->assertStringContainsString('public function storeArtworkRevision(', $documents);
        $this->assertStringContainsString('mapArtworkRevisionReplacementFiles(', $documents);
        $this->assertStringContainsString("'retained_documents' =>", $documents);
        $this->assertStringContainsString("'event' => 'job.artwork_revision_applied'", $documents);
        $this->assertStringContainsString('currentArtworkDocuments(', $documents);
        $this->assertStringContainsString('$replacementVersion = max(1, (int) $source->version + 1);', $documents);
        $this->assertStringContainsString("'current_document_ids' => \$currentDocumentIds", $documents);
        $this->assertStringContainsString("'replacement_document_map' => \$replacementDocumentMap", $documents);
        $this->assertStringNotContainsString("'path' => \$source->path", $documents);

        $this->assertStringContainsString('Artwork selected for revision', $uploadModal);
        $this->assertStringContainsString('Upload one replacement under each artwork below.', $uploadModal);
        $this->assertStringContainsString('wire:model="overviewTaskRevisionUpload.{{ $revisionDocumentId }}"', $uploadModal);
        $this->assertStringContainsString('removeOverviewTaskDocumentUpload({{ $revisionDocumentId }})', $uploadModal);
        $this->assertStringContainsString('ft-artwork-revision-replacement-dropzone', $uploadModal);
        $this->assertStringContainsString('Upload Revised Artwork', $uploadModal);
        $this->assertStringContainsString('Files not listed here remain unchanged.', $uploadModal);

        foreach ([$jobUploads, $orderUploads] as $component) {
            $this->assertStringContainsString('pendingArtworkRevision(', $component);
            $this->assertStringContainsString('updatePendingArtworkRevisionSelection(', $component);
            $this->assertStringContainsString('storeArtworkRevision(', $component);
            $this->assertStringContainsString("'overviewTaskRevisionDocumentIds' => ['required', 'array', 'min:1']", $component);
            $this->assertStringContainsString("'overviewTaskRevisionUpload' => ['required', 'array', 'size:'", $component);
            $this->assertStringContainsString("\$revisionRules['overviewTaskRevisionUpload.'.\$revisionDocumentId] = AttachmentUpload::requiredRules", $component);
            $this->assertStringContainsString('$uploads = $isArtworkRevision ? $this->overviewTaskRevisionUpload : $this->overviewTaskDocumentUpload;', $component);
        }
    }

    public function test_artwork_revision_state_is_wired_through_order_detail_components(): void
    {
        $page = file_get_contents(resource_path('views/livewire/jobs/index.blade.php'));
        $detail = file_get_contents(resource_path('views/components/jobs/detail.blade.php'));
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));

        $this->assertStringContainsString(':overview-task-artwork-revision="$overviewTaskArtworkRevision ?? []"', $page);
        $this->assertStringContainsString("'overviewTaskArtworkRevision'=>[]", $detail);
        $this->assertStringContainsString(':overview-task-artwork-revision="$overviewTaskArtworkRevision"', $detail);
        $this->assertStringContainsString("'overviewTaskArtworkRevision' => []", $overview);
        $this->assertStringContainsString(':artwork-revision="$overviewTaskArtworkRevision"', $overview);
        $this->assertStringContainsString(':overview-task-revision-document-ids="$overviewTaskRevisionDocumentIds"', $detail);
        $this->assertStringContainsString(':revision-document-ids="$overviewTaskRevisionDocumentIds"', $overview);
        $this->assertStringContainsString(':revision-upload="$overviewTaskRevisionUpload"', $overview);
        $this->assertStringContainsString(':revision-comments="$orderWorkflowActionRevisionComments"', $overview);
        $this->assertStringContainsString(':revision-attachments="$orderWorkflowActionRevisionAttachments"', $overview);
    }

    public function test_revision_activity_card_lists_each_selected_artwork_and_resolution_uses_version(): void
    {
        $jobs = file_get_contents(app_path('Services/LegacyJobService.php'));
        $card = file_get_contents(resource_path('views/components/jobs/order-detail/artwork-revision-card.blade.php'));

        $this->assertStringContainsString("data_get(\$note->meta, 'source_artwork_version'", $jobs);
        $this->assertStringContainsString("data_get(\$note->meta, 'revision_document_ids'", $jobs);
        $this->assertStringContainsString("setRelation('revisionDocuments'", $jobs);
        $this->assertStringContainsString('Artwork selected for revision', $card);
        $this->assertStringContainsString('$revisionItems', $card);
        $this->assertStringContainsString('ft-order-artwork-revision-pair-grid', $card);
        $this->assertStringContainsString('Supporting attachments', $card);
    }

    public function test_revision_instructions_reuse_rich_text_mentions_and_support_evidence_attachments(): void
    {
        $modal = file_get_contents(resource_path('views/components/jobs/order-detail/workflow-action-modal.blade.php'));
        $component = file_get_contents(app_path('Livewire/Jobs/Concerns/ManagesOrderWorkflow.php'));
        $actions = file_get_contents(app_path('Services/OrderWorkflowActionService.php'));
        $jobs = file_get_contents(app_path('Services/LegacyJobService.php'));
        $card = file_get_contents(resource_path('views/components/jobs/order-detail/artwork-revision-card.blade.php'));

        $this->assertStringContainsString('Supporting attachments', $modal);
        $this->assertStringContainsString('data-file-dropzone', $modal);
        $this->assertStringContainsString('multiple', $modal);
        $this->assertStringContainsString('orderWorkflowActionRevisionComments', $component);
        $this->assertStringContainsString('orderWorkflowActionRevisionAttachments', $component);
        $this->assertStringContainsString('AttachmentUpload::itemRules', $component);
        $this->assertStringContainsString('storeArtworkRevisionAttachments(', $actions);
        $this->assertStringContainsString("'revision_attachment_document_ids'", $actions);
        $this->assertStringContainsString("setRelation('revisionAttachments'", $jobs);
        $this->assertStringContainsString('<x-ui.mention-text :text="$requiredChangeText" />', $card);
        $this->assertStringContainsString('Supporting attachments', $card);
        $this->assertStringContainsString('imageAttachments($revisionComment)', $card);
        $this->assertStringContainsString('withoutImages($revisionComment)', $card);
        $this->assertStringContainsString('<x-ui.file-type-badge', $card);
        $taskRow = file_get_contents(resource_path('views/components/jobs/order-detail/task-row.blade.php'));
        $this->assertStringContainsString('<x-ui.file-type-badge :name="$document->name"', $taskRow);
        $uploadModal = file_get_contents(resource_path('views/components/jobs/order-detail/document-modal.blade.php'));
        $this->assertStringContainsString('<x-ui.mention-text :text="$artworkRevision[\'comment\']" />', $uploadModal);
    }
}
