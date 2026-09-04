<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderShipmentModalValidationAlignmentTest extends TestCase
{
    public function test_shipment_modal_reserves_validation_rows_for_aligned_fields(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/add-modal.blade.php'));
        $css = file_get_contents(resource_path('css/modules/orders/detail/shipment-modal.css'));

        $this->assertGreaterThanOrEqual(11, substr_count($view, 'ft-ms-validation-slot'));
        $this->assertStringContainsString("@error('shipmentForm.city'){{ \$message }}@enderror", $view);
        $this->assertStringContainsString("@error('shipmentForm.postal_code'){{ \$message }}@enderror", $view);
        $this->assertStringContainsString('.ft-ms-validation-slot:empty', $css);
        $this->assertStringContainsString('grid-template-rows: auto minmax(37px, auto) 16px;', $css);
        $this->assertStringContainsString('.ft-ms-form-grid--two {', $css);
    }
}
