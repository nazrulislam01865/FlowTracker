<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderShipmentEditingUxImplementationTest extends TestCase
{
    public function test_shipment_plan_uses_shipment_wise_editing_without_a_global_edit_mode(): void
    {
        $plan = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/plan-table.blade.php'));

        $this->assertStringContainsString('Edit each shipment individually.', $plan);
        $this->assertStringContainsString('openEditShipment', $plan);
        $this->assertStringContainsString('openAddShipment', $plan);
        $this->assertStringContainsString('No changes — continue', $plan);
        $this->assertStringNotContainsString('shipmentPlanEditing', $plan);
        $this->assertStringNotContainsString('Edit shipment details', $plan);
        $this->assertStringNotContainsString('Done editing', $plan);
    }

    public function test_shipment_plan_groups_delivery_details_to_prevent_table_breakage(): void
    {
        $plan = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/plan-table.blade.php'));
        $css = file_get_contents(resource_path('css/modules/orders/detail/shipment-row-layout.css'));

        $this->assertStringContainsString('<th>Delivery details</th>', $plan);
        $this->assertStringContainsString('ft-ms-delivery__meta', $plan);
        $this->assertStringContainsString('th:nth-child(2) { width: 43%; }', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $css);
    }

    public function test_tracking_is_row_based_and_has_no_print_label_action(): void
    {
        $tracking = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/tracking-table.blade.php'));
        $presenter = file_get_contents(app_path('Support/OrderShipmentPresenter.php'));

        $this->assertStringContainsString("editing: false", $tracking);
        $this->assertStringContainsString('Add tracking', $tracking);
        $this->assertStringContainsString('Edit courier & tracking', $tracking);
        $this->assertStringContainsString('<th>Courier</th>', $tracking);
        $this->assertStringContainsString('courier-select', $tracking);
        $this->assertStringNotContainsString('Print label', $tracking);
        $this->assertStringNotContainsString('printOrderShipmentLabel', $tracking);
        $this->assertStringContainsString("'Add courier & tracking number'", $presenter);
    }

    public function test_shipping_method_picker_uses_a_fixed_teleported_menu(): void
    {
        $picker = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/method-picker.blade.php'));
        $css = file_get_contents(resource_path('css/modules/orders/detail/shipment-floating-overlays.css'));

        $this->assertStringContainsString('floatingActionMenu()', $picker);
        $this->assertStringContainsString('x-teleport="body"', $picker);
        $this->assertStringContainsString('ft-ms-method-portal', $picker);
        $this->assertStringContainsString('max-height: min(360px, calc(100vh - 20px));', $css);
        $this->assertStringContainsString('overflow-y: auto;', $css);
    }

    public function test_shipment_address_uses_reusable_country_and_state_master_data(): void
    {
        $modal = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/add-modal.blade.php'));
        $pageData = file_get_contents(app_path('Livewire/Jobs/Concerns/BuildsOrderPageData.php'));
        $locations = file_get_contents(app_path('Services/LocationMasterDataService.php'));
        $shipmentService = file_get_contents(app_path('Services/OrderShipmentService.php'));

        $this->assertStringContainsString('property="shipmentForm.country"', $modal);
        $this->assertStringContainsString('property="shipmentForm.state"', $modal);
        $this->assertStringContainsString(':fixed-menu="true"', $modal);
        $this->assertStringContainsString('Same as Shipment 1', $modal);
        $this->assertStringContainsString('Different address', $modal);
        $this->assertStringContainsString('LocationMasterDataService::class', $pageData);
        $this->assertStringContainsString("active('country')", $locations);
        $this->assertStringContainsString("active('state')", $locations);
        $this->assertStringContainsString('stateBelongsToCountry', $shipmentService);
    }

    public function test_multiple_shipment_flags_are_derived_from_actual_shipment_rows(): void
    {
        $service = file_get_contents(app_path('Services/OrderShipmentService.php'));

        $this->assertStringContainsString('syncPlanFromShipments', $service);
        $this->assertStringContainsString("'allow_multiple_shipments' => \$allowMultiple", $service);
        $this->assertStringNotContainsString('Enable Allow multiple shipments before adding another shipment.', $service);
    }
    public function test_tracking_uses_courier_master_data_and_shipping_method_eta_copy_is_hidden(): void
    {
        $pageData = file_get_contents(app_path('Livewire/Jobs/Concerns/BuildsOrderPageData.php'));
        $presenter = file_get_contents(app_path('Support/OrderShipmentPresenter.php'));
        $tracking = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/tracking-table.blade.php'));
        $dispatch = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/dispatch-table.blade.php'));
        $picker = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/method-picker.blade.php'));
        $plan = file_get_contents(resource_path('views/components/jobs/order-detail/shipment/plan-table.blade.php'));

        $this->assertStringContainsString("active('courier')", $pageData);
        $this->assertStringContainsString("'shipmentCouriers'", $pageData);
        $this->assertStringContainsString("'courier_name'", $presenter);
        $this->assertStringContainsString('<th>Courier</th>', $tracking);
        $this->assertStringContainsString('<th>Courier</th>', $dispatch);
        $this->assertStringNotContainsString('Courier / Method', $tracking);
        $this->assertStringNotContainsString('Courier / Method', $dispatch);
        $this->assertStringNotContainsString("['estimate']", $picker);
        $this->assertStringNotContainsString("['estimate']", $plan);
    }

}
