<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateOrderPurchaseOrderUploadTest extends TestCase
{
    public function test_create_order_exposes_purchase_order_and_other_document_sections(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/create.blade.php'));
        $indexView = file_get_contents(resource_path('views/livewire/jobs/index.blade.php'));
        $index = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $creation = file_get_contents(app_path('Livewire/Jobs/Concerns/ManagesOrderCreation.php'));

        $this->assertStringContainsString('<div class="ft-create-section-title"><span>5</span><h2>Purchase Order</h2></div>', $view);
        $this->assertStringContainsString('step="6"', $view);
        $this->assertStringContainsString('number="6" title="What happens next"', $view);
        $this->assertLessThan(
            strpos($view, 'title="What happens next"'),
            strpos($view, '<h2>Purchase Order</h2>'),
            'Purchase Order must appear before What happens next on Create Order.'
        );
        $this->assertStringContainsString('wire:model="purchaseOrderUpload"', $view);
        $this->assertStringContainsString('<h2>Other document</h2>', $view);
        $this->assertStringContainsString('wire:model="jobAttachments" multiple', $view);
        $this->assertStringContainsString(':purchase-order-upload="$purchaseOrderUpload"', $indexView);
        $this->assertStringContainsString('public $purchaseOrderUpload = null;', $index);
        $this->assertStringContainsString("'purchaseOrderUpload' => AttachmentUpload::nullableRules(AttachmentUpload::DOCUMENTS_WITH_AI, 20480)", $creation);
        $this->assertStringContainsString("'purchaseOrderUpload',", $creation);
    }

    public function test_create_order_purchase_order_is_linked_to_po_task_and_completes_normal_workflow_action(): void
    {
        $action = file_get_contents(app_path('Actions/Orders/CreateOrder.php'));

        $this->assertStringContainsString("=== 'NEW_UPLOAD_PO'", $action);
        $this->assertStringContainsString("'task_id' => \$purchaseOrderTask->id", $action);
        $this->assertStringContainsString("'require_task_pack_requirement' => true", $action);
        $this->assertStringContainsString('afterDocumentAdded($purchaseOrderTask->refresh(), $actor)', $action);
        $this->assertStringContainsString('if (! $draft)', $action);
    }

    public function test_other_document_card_excludes_task_specific_documents(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/order-detail/attachments.blade.php'));

        $this->assertStringContainsString('<h2>Other document', $view);
        $this->assertStringContainsString('filter(fn ($document) => blank($document->task_id))', $view);
        $this->assertStringContainsString('@forelse($otherDocuments as $document)', $view);
        $this->assertStringNotContainsString('@forelse($job->documents->sortByDesc', $view);
    }
}
