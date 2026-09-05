<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderShipmentCityOptionalTest extends TestCase
{
    public function test_city_is_optional_on_create_order_and_shipment_stage(): void
    {
        $creation = file_get_contents(app_path('Livewire/Jobs/Concerns/ManagesOrderCreation.php'));
        $shipmentService = file_get_contents(app_path('Services/OrderShipmentService.php'));
        $createRow = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));
        $shipmentModal = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/add-modal.blade.php'));

        $this->assertStringContainsString("'createShipments.*.city' => ['nullable', 'string', 'max:120']", $creation);
        $this->assertStringNotContainsString("'createShipments.*.city.required'", $creation);
        $this->assertStringContainsString('City <em>Optional</em>', $createRow);

        $this->assertStringNotContainsString("'city' => 'City is required.'", $shipmentService);
        $this->assertStringContainsString('CITY <small class="ft-ms-field-hint">Optional</small>', $shipmentModal);
        $this->assertStringNotContainsString('wire:model.defer="shipmentForm.city" maxlength="120" placeholder="e.g. Miami" aria-required="true"', $shipmentModal);
    }
}
