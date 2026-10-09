<?php

namespace Tests\Unit;

use App\Models\MasterRecord;
use PHPUnit\Framework\TestCase;

class SupplierProductPricingTest extends TestCase
{
    public function test_suppliers_have_independent_quantity_prices_without_any_catalogue_queries(): void
    {
        $product = new MasterRecord([
            'type' => 'product',
            'metadata' => [
                'supplier_id' => 10,
                'supplier_ids' => [10, 20],
                'price_breakpoints' => [
                    ['quantity' => 10, 'price' => 5],
                    ['quantity' => 100, 'price' => 4],
                ],
                'supplier_price_tables' => [
                    '10' => ['price_breakpoints' => [
                        ['quantity' => 10, 'price' => 5],
                        ['quantity' => 100, 'price' => 4],
                    ]],
                    '20' => [
                        'price_breakpoints' => [
                            ['quantity' => 10, 'price' => 9],
                            ['quantity' => 100, 'price' => 7],
                        ],
                        'remote_surcharge_breakpoints' => [
                            ['quantity' => 10, 'price' => 2],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertTrue($product->hasProductPricing());
        self::assertSame(5.0, $product->productPriceForQuantity(50, 10));
        self::assertSame(9.0, $product->productPriceForQuantity(50, 20));
        self::assertSame(4.0, $product->productPriceForQuantity(150));
        self::assertSame(7.0, $product->productPriceForQuantity(150, 20));
        self::assertSame(2.0, $product->productRemoteSurchargeForQuantity(50, 20));
        self::assertNull($product->productPriceForQuantity(5, 20));
        self::assertNull($product->productPriceForQuantity(50, 30));
    }

    public function test_legacy_table_is_used_only_for_its_default_supplier(): void
    {
        $product = new MasterRecord([
            'type' => 'product',
            'metadata' => [
                'supplier_id' => 10,
                'supplier_ids' => [10, 20],
                'price_breakpoints' => [['quantity' => 1, 'price' => 4.50]],
            ],
        ]);

        self::assertSame(4.5, $product->productPriceForQuantity(10));
        self::assertSame(4.5, $product->productPriceForQuantity(10, 10));
        self::assertNull($product->productPriceForQuantity(10, 20));
        self::assertTrue($product->hasProductPricing());
    }

    public function test_products_without_pricing_are_unpriced_not_implicitly_zero(): void
    {
        $product = new MasterRecord(['type' => 'product', 'metadata' => ['supplier_ids' => [10, 20]]]);

        self::assertFalse($product->hasProductPricing());
        self::assertNull($product->productPriceForQuantity(100, 10));
        self::assertNull($product->productPriceForQuantity(100, 20));
    }
}
