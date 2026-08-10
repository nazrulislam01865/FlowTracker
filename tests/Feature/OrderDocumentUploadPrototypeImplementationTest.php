<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderDocumentUploadPrototypeImplementationTest extends TestCase
{
    public function test_documents_tab_uses_single_file_prototype_uploader_states(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/detail-documents.blade.php'));

        $this->assertStringContainsString('Choose the document type, then upload a new file or select one that already exists.', $view);
        $this->assertStringContainsString('wire:model="jobRequiredDocumentUpload"', $view);
        $this->assertStringContainsString('livewire-upload-progress', $view);
        $this->assertStringContainsString('Retry upload', $view);
        $this->assertStringContainsString('Choose another file', $view);
        $this->assertStringContainsString('Upload complete', $view);
        $this->assertStringContainsString('to replace this document.', $view);
        $this->assertStringNotContainsString('Upload &amp; link', $view);
    }

    public function test_documents_tab_auto_links_and_replaces_only_after_new_file_is_stored(): void
    {
        $component = file_get_contents(app_path('Livewire/Jobs/Index.php'));

        $this->assertStringContainsString('public function updatedJobRequiredDocumentUpload(): void', $component);
        $this->assertStringContainsString("'jobRequiredDocumentUpload' => ['required','file','max:20480','mimes:pdf,docx,xlsx,jpg,jpeg,png,zip']", $component);
        $this->assertStringContainsString("'require_task_pack_requirement' => true", $component);

        $storePosition = strpos($component, '$document = app(DocumentService::class)->store');
        $deletePosition = strpos($component, 'app(DocumentService::class)->delete($replace');
        $this->assertNotFalse($storePosition);
        $this->assertNotFalse($deletePosition);
        $this->assertLessThan($deletePosition, $storePosition);
    }
}
