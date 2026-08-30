<?php

namespace Tests\Feature;

use Tests\TestCase;

class InquiryAttachmentUploadUxTest extends TestCase
{
    public function test_inquiry_task_document_modal_is_upload_only_with_progress_feedback(): void
    {
        $detail = file_get_contents(resource_path('views/livewire/inquiries/sections/detail.blade.php'));
        $taskflow = file_get_contents(resource_path('views/livewire/inquiries/_taskflow.blade.php'));
        $documents = file_get_contents(app_path('Livewire/Inquiries/Concerns/ManagesInquiryDocuments.php'));
        $service = file_get_contents(app_path('Services/LegacyInquiryService.php'));

        $this->assertStringNotContainsString('Choose existing', $detail);
        $this->assertStringContainsString('wire:model="taskDocumentUpload"', $detail);
        $this->assertStringContainsString('x-on:livewire-upload-progress="progress = $event.detail.progress"', $detail);
        $this->assertStringContainsString('ft-inquiry-task-document-upload-progress', $detail);
        $this->assertStringContainsString('ft-inquiry-attachment-selected-file', $detail);
        $this->assertStringContainsString('1 file selected', $detail);
        $this->assertStringContainsString("wire:click=\"\$set('taskDocumentUpload', null)\"", $detail);
        $this->assertStringContainsString('Add another file', $detail);
        $this->assertStringContainsString('Ready to upload', $detail);
        $this->assertStringContainsString('@disabled(!$taskDocumentUpload)', $detail);
        $this->assertStringContainsString('(bool) $taskDocumentModalTask->requires_submission', $detail);
        $this->assertStringContainsString('$shouldCompleteAfterDocument = (bool) $task->requires_submission', $documents);
        $this->assertStringContainsString('$this->completeTask($task->fresh(), $actor);', $service);
        $this->assertStringContainsString('wire:key="inquiry-task-status-{{ $task->id }}-', $taskflow);
        $this->assertStringContainsString("md5((string) \$task->status.'|'", $taskflow);
    }

    public function test_create_inquiry_attachment_dropzone_has_no_choose_files_button_and_shows_progress(): void
    {
        $create = file_get_contents(resource_path('views/livewire/inquiries/sections/create.blade.php'));

        $this->assertStringNotContainsString('>Choose files</button>', $create);
        $this->assertStringContainsString('wire:model="createAttachments"', $create);
        $this->assertStringContainsString('x-on:livewire-upload-progress="progress = $event.detail.progress"', $create);
        $this->assertStringContainsString('ft-inquiry-create-upload-progress', $create);
    }
}
