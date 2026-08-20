<?php

namespace Tests\Feature;

use Tests\TestCase;

class MasterDataCreateProductPrototypeTest extends TestCase
{
    public function test_create_product_modal_matches_the_supplied_catalogue_structure(): void
    {
        $view = file_get_contents(resource_path('views/livewire/master-data/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/MasterData/Index.php'));
        $css = file_get_contents(public_path('css/flowtrack-master-data.css'));

        $this->assertStringContainsString('class="modal livewire-modal ft-product-create-modal"', $view);
        $this->assertStringContainsString('Create new product', $view);
        $this->assertStringContainsString('SKU / Product code', $view);
        $this->assertStringContainsString('Product category', $view);
        $this->assertStringContainsString('No category found', $view);
        $this->assertStringContainsString('Similar categories', $view);
        $this->assertStringContainsString('Create category', $view);
        $this->assertStringContainsString('Drop an image here or', $view);
        $this->assertStringContainsString('You have permission to create products and categories', $view);
        $this->assertStringContainsString('wire:click="createProductCategory"', $view);
        $this->assertStringContainsString('wire:click="selectProductCategory(', $view);

        $this->assertStringContainsString('public string $productCategorySearch', $component);
        $this->assertStringContainsString('public function createProductCategory(): void', $component);
        $this->assertStringContainsString("\$this->editId ? 'nullable' : 'required'", $component);
        $this->assertStringContainsString("\$service->nextCode('product_category')", $component);

        $this->assertStringContainsString('.ft-product-create-modal', $css);
        $this->assertStringContainsString('.ft-product-category-create-row', $css);
        $this->assertStringContainsString('.ft-product-drop-zone', $css);
    }
}
