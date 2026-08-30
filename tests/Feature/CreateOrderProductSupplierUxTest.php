<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Support\OrderPhase5Source;

class CreateOrderProductSupplierUxTest extends TestCase
{
    public function test_create_order_uses_product_supplier_and_labelled_product_cards(): void
    {
        $jobs = OrderPhase5Source::livewire();
        $model = file_get_contents(app_path('Models/MasterRecord.php'));
        $selector = file_get_contents(resource_path('views/components/catalog/create-product-quantity.blade.php'));
        $card = file_get_contents(resource_path('views/components/catalog/create-order-product-card.blade.php'));
        $create = file_get_contents(resource_path('views/livewire/jobs/index.blade.php'));
        $css = $this->compatibilityCss('flowtrack-order-create-products.css');

        $this->assertStringContainsString('supplierForProduct($product)', $jobs);
        $this->assertStringContainsString('appendCreateOrderProduct($product, (int) $productSupplier->id)', $jobs);
        $this->assertStringContainsString('skipMissingCreateOrderProductSupplier', $jobs);
        $this->assertStringContainsString('createOrderSupplierSkipProductIds', $jobs);
        $this->assertStringContainsString('in_array($productId, $this->createOrderSupplierSkipProductIds, true)', $jobs);
        $this->assertStringNotContainsString("'supplier_skipped' =>", $jobs);
        $this->assertStringNotContainsString("'jobItems.*.supplier_skipped'", $jobs);
        $this->assertStringContainsString('productSupplierId()', $model);
        $this->assertStringContainsString('<x-catalog.create-order-product-card', $selector);
        $this->assertStringContainsString('From product', $card);
        $this->assertStringContainsString('Supplier is not linked', $card);
        $this->assertStringContainsString('Supplier skipped for now', $card);
        $this->assertStringContainsString('You can assign a supplier later from Order Details.', $card);
        $this->assertStringNotContainsString('<x-ui.search-select', $card);
        $this->assertStringContainsString('showMissingProductSupplierModal', $jobs);
        $this->assertStringContainsString('synchronizeCreateOrderProductSuppliersFromCatalog', $jobs);
        $this->assertStringContainsString('Skip supplier for now', $jobs);
        $this->assertStringContainsString('Quantity', $card);
        $this->assertStringContainsString('Unit price', $card);
        $this->assertStringContainsString('Notes', $card);
        $this->assertStringContainsString(':selected-product-suppliers="$selectedProductSuppliers"', $create);
        $this->assertStringContainsString(':create-order-supplier-skip-product-ids="$createOrderSupplierSkipProductIds"', $create);
        $this->assertStringContainsString(':new-product-supplier-id="$newProductSupplierId"', $create);
        $createProducts = OrderPhase5Source::createProductsView();
        $this->assertStringContainsString('Supplier not linked', $createProducts);
        $this->assertStringContainsString('Skip supplier &amp; add product', $createProducts);
        $this->assertStringContainsString(':supplier-skipped-product-ids="$createOrderSupplierSkipProductIds"', $createProducts);
        $this->assertStringContainsString('variant="secondary"', $createProducts);
        $this->assertStringContainsString('.ft-order-selected-product-card-fields', $css);
        $this->assertStringContainsString('.ft-order-product-supplier-modal-action--skip', $css);
        $this->assertStringContainsString('color: #fff !important;', $css);
        $this->assertStringContainsString('.ft-create-job-page .ft-order-products-prototype', $css);
    }
}
