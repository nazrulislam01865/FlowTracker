<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateProductWhenSearchMissingTest extends TestCase
{
    public function test_inquiry_and_order_only_offer_search_create_when_no_product_matches(): void
    {
        $inquiryView = file_get_contents(resource_path('views/components/inquiries/create-products.blade.php'));
        $orderView = file_get_contents(resource_path('views/components/jobs/create-products.blade.php'));
        $inquiryComponent = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $orderComponent = file_get_contents(app_path('Livewire/Jobs/Index.php'));

        foreach ([$inquiryView, $orderView] as $view) {
            $this->assertStringContainsString("\$showCreateProductSuggestion = \$productSearchValue !== '' && (int) \$productResultTotal === 0;", $view);
            $this->assertStringContainsString('@if($showCreateProductSuggestion)', $view);
            $this->assertStringContainsString('wire:click="openCreateOrderProductModalFromSearch"', $view);
            $this->assertStringContainsString('No matching product found.', $view);
        }

        foreach ([$inquiryComponent, $orderComponent] as $component) {
            $this->assertStringContainsString('public function openCreateOrderProductModalFromSearch(): void', $component);
            $this->assertStringContainsString('$searchedName = trim($this->createProductSearch);', $component);
            $this->assertStringContainsString('$this->newProductName = $searchedName;', $component);
        }
    }
}
