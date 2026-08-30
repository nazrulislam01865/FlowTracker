<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Support\OrderPhase5Source;

class OrderDocumentUploadPrototypeImplementationTest extends TestCase
{
    public function test_documents_tab_uses_single_file_prototype_uploader_states(): void
    {
        $view = OrderPhase5Source::detailDocumentsView();

        $this->assertStringContainsString('Choose the document type, then upload a file, select an existing file, or use the task Add link action.', $view);
        $this->assertStringContainsString('$wire.upload(', $view);
        $this->assertStringContainsString("'jobRequiredDocumentUpload'", $view);
        $this->assertStringContainsString('event?.detail?.progress', $view);
        $this->assertStringContainsString('persistJobRequiredDocumentUpload', $view);
        $this->assertStringContainsString('Retry upload', $view);
        $this->assertStringContainsString('Choose another file', $view);
        $this->assertStringContainsString('Upload complete', $view);
        $this->assertStringContainsString("'eps', 'esp'", $view);
        $this->assertStringContainsString('to replace this document.', $view);
        $this->assertStringNotContainsString('Upload &amp; link', $view);
    }

    public function test_documents_tab_auto_links_and_replaces_only_after_new_file_is_stored(): void
    {
        $component = OrderPhase5Source::livewire();

        $this->assertStringContainsString('public function updatedJobRequiredDocumentUpload(): void', $component);
        $this->assertStringContainsString("'jobRequiredDocumentUpload' => AttachmentUpload::requiredRules(AttachmentUpload::ORDER_REQUIRED, 20480)", $component);
        $this->assertStringContainsString("'require_task_pack_requirement' => true", $component);

        $storePosition = strpos($component, '$document = app(DocumentService::class)->store');
        $deletePosition = strpos($component, 'app(DocumentService::class)->delete($replace');
        $this->assertNotFalse($storePosition);
        $this->assertNotFalse($deletePosition);
        $this->assertLessThan($deletePosition, $storePosition);
    }
    public function test_order_workflow_upload_modal_keeps_the_same_size_after_file_selection(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/order-detail/document-modal.blade.php'));
        $css = $this->orderDetailCss();

        $this->assertStringContainsString("ft-order-task-document-modal ft-order-attachment-upload-modal {{ \$prototypeUpload ? 'ft-order-prototype-upload-modal' : '' }}", $view);
        $this->assertStringContainsString('ft-prototype-selected-file-name', $view);
        $this->assertStringContainsString('ft-order-attachment-selected-file', $view);
        $this->assertStringContainsString('1 file selected', $view);
        $this->assertStringContainsString("wire:click=\"\$set('overviewTaskDocumentUpload', null)\"", $view);
        $this->assertStringContainsString('Add another file', $view);
        $this->assertStringContainsString('Ready to upload', $view);
        $this->assertStringContainsString('title="Choose file"', $view);
        $this->assertStringContainsString('height:500px;', $css);
        $this->assertStringContainsString('grid-template-rows:auto minmax(0,1fr) auto;', $css);
        $this->assertStringContainsString('overflow-y:auto;', $css);
        $this->assertStringContainsString('text-overflow:ellipsis;', $css);
        $this->assertStringContainsString('white-space:nowrap;', $css);
    }

}
