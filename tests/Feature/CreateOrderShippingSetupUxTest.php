<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateOrderShippingSetupUxTest extends TestCase
{
    public function test_shipping_setup_uses_reusable_card_rows_instead_of_a_dense_table(): void
    {
        $setup = file_get_contents(resource_path('views/components/jobs/create/shipping-setup.blade.php'));
        $row = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));

        $this->assertStringContainsString('ft-create-shipment-workspace', $setup);
        $this->assertStringContainsString('<x-jobs.create.shipping-row', $setup);
        $this->assertStringNotContainsString('ft-create-shipment-table-head', $setup);
        $this->assertStringContainsString('data-ft-ui-component="create-order-shipment-card"', $row);
        $this->assertStringContainsString('Shipment {{ $shipmentNumber }}', $row);
    }

    public function test_editable_shipment_follows_reference_form_structure(): void
    {
        $row = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));

        $contact = strpos($row, '<span>Contact person</span>');
        $phone = strpos($row, '<span>Phone</span>');
        $address = strpos($row, '<span>Shipping address</span>');
        $country = strpos($row, '<span>Country</span>');
        $state = strpos($row, '<span>State</span>');
        $city = strpos($row, '<span>City</span>');
        $postal = strpos($row, '<span>Postal code</span>');
        $shipmentNo = strpos($row, '<span>Shipment no.</span>');
        $reference = strpos($row, 'Package / reference <em>Optional</em>');
        $method = strpos($row, '<span>Shipping method</span>');

        foreach ([$contact, $phone, $address, $country, $state, $city, $postal, $shipmentNo, $reference, $method] as $position) {
            $this->assertNotFalse($position);
        }

        $this->assertLessThan($phone, $contact);
        $this->assertLessThan($address, $phone);
        $this->assertLessThan($country, $address);
        $this->assertLessThan($state, $country);
        $this->assertLessThan($city, $state);
        $this->assertLessThan($postal, $city);
        $this->assertLessThan($shipmentNo, $postal);
        $this->assertLessThan($reference, $shipmentNo);
        $this->assertLessThan($method, $reference);
    }

    public function test_same_address_mode_keeps_repeated_shipments_compact(): void
    {
        $setup = file_get_contents(resource_path('views/components/jobs/create/shipping-setup.blade.php'));
        $row = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));

        $this->assertStringContainsString('Enter the delivery details once and reuse them for every shipment.', $setup);
        $this->assertStringContainsString('Same delivery address as Shipment 1', $row);
        $this->assertStringContainsString('ft-create-shipment-shared-address', $row);
        $this->assertStringContainsString('ft-create-shipment-service-grid', $row);
    }

    public function test_saved_address_remove_and_package_actions_are_directly_discoverable(): void
    {
        $row = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));

        $this->assertStringContainsString('Use saved address', $row);
        $this->assertStringContainsString('Remove shipment', $row);
        $this->assertStringContainsString('Package / reference <em>Optional</em>', $row);
        $this->assertStringNotContainsString('ft-create-shipment-actions-menu', $row);
    }

    public function test_phone_validation_is_scoped_to_the_phone_input_column(): void
    {
        $row = file_get_contents(resource_path('views/components/jobs/create/shipping-row.blade.php'));

        $phoneNumberWrapper = strpos($row, 'ft-create-shipment-phone-number');
        $phoneError = strpos($row, '@error("createShipments.$index.phone")');
        $phoneRowEnd = strpos($row, '</div>\n            </div>', $phoneError);

        $this->assertNotFalse($phoneNumberWrapper);
        $this->assertNotFalse($phoneError);
        $this->assertNotFalse($phoneRowEnd);
        $this->assertLessThan($phoneError, $phoneNumberWrapper);
        $this->assertLessThan($phoneRowEnd, $phoneError);
    }

    public function test_layout_is_responsive_to_available_component_width_without_horizontal_scrolling(): void
    {
        $css = file_get_contents(resource_path('css/modules/orders/create-shipping-setup.css'));

        $this->assertStringContainsString('container-name:create-shipping-setup;', $css);
        $this->assertStringContainsString('container-name:create-shipment-card;', $css);
        $this->assertStringContainsString('@container create-shipment-card (max-width: 880px)', $css);
        $this->assertStringContainsString('@container create-shipment-card (max-width: 620px)', $css);
        $this->assertStringContainsString('max-width:100%;', $css);
        $this->assertStringNotContainsString('min-width:1060px', $css);
    }
}
