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

    public function test_master_product_creation_uses_generated_code_and_shared_taxonomy_fields(): void
    {
        $form = file_get_contents(resource_path('views/components/catalog/product-form.blade.php'));
        $component = file_get_contents(app_path('Livewire/MasterData/Index.php'));

        $this->assertStringContainsString('Generated automatically after the product is created.', $form);
        $this->assertStringContainsString('wire:model.blur="productReferenceCode"', $form);
        $this->assertSame(3, substr_count($form, '<x-ui.search-select'));
        $this->assertStringContainsString('label="Main category"', $form);
        $this->assertStringContainsString('label="Product category"', $form);
        $this->assertStringContainsString('label="Subcategory"', $form);
        $this->assertStringContainsString('$this->code = $service->nextCode($this->group);', $component);
    }
}
