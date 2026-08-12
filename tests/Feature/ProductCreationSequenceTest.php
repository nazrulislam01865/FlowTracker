<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductCreationSequenceTest extends TestCase
{
    public function test_order_and_inquiry_product_creation_require_code_before_category(): void
    {
        $orderView = file_get_contents(resource_path('views/components/jobs/create-products.blade.php'));
        $inquiryView = file_get_contents(resource_path('views/components/inquiries/create-products.blade.php'));
        $orderComponent = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $inquiryComponent = file_get_contents(app_path('Livewire/Inquiries/Index.php'));

        foreach ([$orderView, $inquiryView] as $view) {
            $this->assertStringContainsString('ft-product-step-number">1</b> SKU / Product code', $view);
            $this->assertStringContainsString('ft-product-step-number">2</b> Product category', $view);
            $this->assertStringContainsString('@disabled(!$productCodeReady)', $view);
            $this->assertStringContainsString('@disabled(!$productCategoryReady)', $view);
            $this->assertStringContainsString('@disabled(!$productNameReady || $hasDuplicateCode)', $view);
        }

        $this->assertStringContainsString('private function newProductCodeReadyForCategory(): bool', $orderComponent);
        $this->assertStringContainsString('private function newProductCodeReadyForCategory(): bool', $inquiryComponent);
    }

    public function test_master_product_creation_uses_manual_code_and_progressive_fields(): void
    {
        $view = file_get_contents(resource_path('views/livewire/master-data/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/MasterData/Index.php'));

        $this->assertStringContainsString('wire:model.live.debounce.220ms="code"', $view);
        $this->assertStringContainsString('Enter product code, e.g. TS-SUB-001', $view);
        $this->assertStringContainsString('@disabled(!$productCodeReady)', $view);
        $this->assertStringContainsString('private function productCodeReadyForCategory(): bool', $component);
        $this->assertStringContainsString("\$this->code = \$this->group === 'product' ? '' : \$service->nextCode(\$this->group);", $component);
    }
}
