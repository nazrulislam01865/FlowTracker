<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Support\OrderPhase5Source;

class OrderDetailsAddProductSupplierFallbackTest extends TestCase
{
    public function test_order_details_add_product_uses_linked_supplier_and_only_allows_manual_fallback_when_missing(): void
    {
        $jobs = OrderPhase5Source::livewire();
        $picker = file_get_contents(resource_path('views/components/catalog/detail-add-product.blade.php'));
        $products = file_get_contents(resource_path('views/components/jobs/order-detail/products.blade.php'));
        $css = $this->orderDetailCss();

        $this->assertStringContainsString('public string $jobProductSupplierLabel', $jobs);
        $this->assertStringContainsString('public bool $jobProductSupplierLocked', $jobs);
        $this->assertStringContainsString('$linkedSupplier = app(\\App\\Services\\ProductCatalogService::class)->supplierForProduct($product);', $jobs);
        $this->assertStringContainsString('updateAddJobProductSupplierFromSelector', $jobs);
        $this->assertStringContainsString('$this->jobProductSupplierLocked = (bool) $linkedSupplier;', $jobs);
        $this->assertStringContainsString('$supplierId = $linkedSupplier', $jobs);

        $this->assertStringContainsString('@if($supplierLocked)', $picker);
        $this->assertStringContainsString('Linked from Product Master', $picker);
        $this->assertStringContainsString('No supplier is linked in Product Master. Select a supplier for this {{ $recordLabel }}.', $picker);
        $this->assertStringContainsString('action="updateAddJobProductSupplierFromSelector"', $picker);
        $this->assertStringContainsString(':selected-label="$supplierLabel ?: null"', $picker);

        $this->assertStringContainsString(':supplier-label="$jobProductSupplierLabel"', $products);
        $this->assertStringContainsString(':supplier-locked="$jobProductSupplierLocked"', $products);
        $this->assertStringContainsString('.ft-order-detail-supplier-readonly', $css);
    }
}
