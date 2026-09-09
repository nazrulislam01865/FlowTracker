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
        $create = file_get_contents(resource_path('views/components/jobs/create.blade.php'));

        $contact = strpos($row, 'Contact person <b class="ft-order-required-star"');
        $phone = strpos($row, 'Phone <b class="ft-order-required-star"');
        $address = strpos($row, 'Shipping address <b class="ft-order-required-star"');
        $country = strpos($row, 'Country <b class="ft-order-required-star"');
        $state = strpos($row, 'State @if($states->isNotEmpty())<b class="ft-order-required-star"');
        $city = strpos($row, 'City <em>Optional</em>');
        $postal = strpos($row, 'Postal code <b class="ft-order-required-star"');
        $shipmentNo = strpos($row, '<span>Shipment no.</span>');
        $reference = strpos($row, 'Package / reference <em>Optional</em>');

        foreach ([$contact, $phone, $address, $country, $state, $city, $postal, $shipmentNo, $reference] as $position) {
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

        $this->assertStringNotContainsString('<span>Shipping method</span>', $row);
        $this->assertStringContainsString('<h2>Schedule & owner</h2>', $create);
        $this->assertStringContainsString('<b>Hand Date</b>', $create);
        $this->assertStringContainsString('<x-jobs.create.shipping-method-picker', $create);
        $this->assertStringContainsString('Applied automatically to every shipment address.', $create);

        $date = strpos($create, '<b>Hand Date</b>');
        $method = strpos($create, '<x-jobs.create.shipping-method-picker', $date);
        $owner = strpos($create, 'label="Order owner *"');
        $this->assertNotFalse($date);
        $this->assertNotFalse($method);
        $this->assertNotFalse($owner);
        $this->assertLessThan($method, $date);
        $this->assertLessThan($owner, $method);
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
