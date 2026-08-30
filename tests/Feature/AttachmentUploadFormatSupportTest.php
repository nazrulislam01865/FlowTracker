<?php

namespace Tests\Feature;

use App\Support\AttachmentUpload;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Support\OrderPhase5Source;

class AttachmentUploadFormatSupportTest extends TestCase
{
    public function test_eps_and_esp_are_accepted_by_shared_attachment_rules(): void
    {
        foreach (['eps', 'esp'] as $extension) {
            $file = UploadedFile::fake()->create('artwork.'.$extension, 32, 'application/octet-stream');
            $validator = validator(
                ['attachment' => $file],
                ['attachment' => AttachmentUpload::requiredRules(AttachmentUpload::DOCUMENTS, 20480)],
            );

            $this->assertTrue($validator->passes(), $validator->errors()->first('attachment'));
        }
    }

    public function test_shared_accept_list_contains_eps_and_esp(): void
    {
        $accept = AttachmentUpload::accept(AttachmentUpload::DOCUMENTS_WITH_AI);

        $this->assertStringContainsString('.eps', $accept);
        $this->assertStringContainsString('.esp', $accept);
    }

    public function test_key_order_inquiry_task_and_archive_uploaders_expose_eps_and_esp(): void
    {
        $views = [
            resource_path('views/components/jobs/create.blade.php') => file_get_contents(resource_path('views/components/jobs/create.blade.php')),
            resource_path('views/components/jobs/order-detail/attachments.blade.php') => file_get_contents(resource_path('views/components/jobs/order-detail/attachments.blade.php')),
            resource_path('views/components/jobs/order-detail/document-modal.blade.php') => file_get_contents(resource_path('views/components/jobs/order-detail/document-modal.blade.php')),
            resource_path('views/components/jobs/task-detail.blade.php') => OrderPhase5Source::taskDetailView(),
            resource_path('views/livewire/inquiries/_attachments.blade.php') => file_get_contents(resource_path('views/livewire/inquiries/_attachments.blade.php')),
            resource_path('views/livewire/inquiries/index.blade.php') => $this->inquiryViewSource(),
            resource_path('views/livewire/documents/index.blade.php') => file_get_contents(resource_path('views/livewire/documents/index.blade.php')),
        ];

        foreach ($views as $view => $contents) {
            $this->assertStringContainsString('.eps', $contents, $view);
            $this->assertStringContainsString('.esp', $contents, $view);
        }
    }
}
