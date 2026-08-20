<?php

namespace Tests\Feature;

use Tests\TestCase;

class MasterDataProductActionsTest extends TestCase
{
    public function test_product_row_actions_use_dedicated_workspace_scoped_livewire_actions(): void
    {
        $view = file_get_contents(resource_path('views/livewire/master-data/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/MasterData/Index.php'));

        $this->assertStringContainsString('$wire.editProduct({{ $r->id }})', $view);
        $this->assertStringContainsString('$wire.deleteProduct({{ $r->id }})', $view);
        $this->assertStringContainsString("window.confirm('Delete this product?')", $view);
        $this->assertStringContainsString('wire:key="product-actions-{{ $r->id }}"', $view);

        $this->assertStringContainsString('public function editProduct(int $id): void', $component);
        $this->assertStringContainsString('public function deleteProduct(int $id): void', $component);
        $this->assertStringContainsString("->ofType('product')", $component);
        $this->assertStringContainsString("session()->flash('success', 'Product deleted.')", $component);
    }
}
