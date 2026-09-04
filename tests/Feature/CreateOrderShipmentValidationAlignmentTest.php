<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateOrderShipmentValidationAlignmentTest extends TestCase
{
    public function test_location_fields_reserve_a_stable_validation_row(): void
    {
        $row = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));
        $css = file_get_contents(resource_path('css/modules/orders/create-shipping-setup.css'));

        $this->assertSame(4, substr_count($row, 'ft-create-shipment-validation-slot'));
        $this->assertStringContainsString('createShipments.$index.country', $row);
        $this->assertStringContainsString('createShipments.$index.state', $row);
        $this->assertStringContainsString('createShipments.$index.city', $row);
        $this->assertStringContainsString('createShipments.$index.postal_code', $row);
        $this->assertStringContainsString('.ft-create-shipment-location-grid .ft-create-shipment-validation-slot', $css);
        $this->assertStringContainsString('min-height:16px;', $css);
        $this->assertStringContainsString('.ft-create-shipment-validation-slot:empty', $css);
    }
}
