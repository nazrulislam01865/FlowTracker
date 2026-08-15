<?php

namespace Tests\Feature;

use Tests\TestCase;

class BulkOrderImportImplementationTest extends TestCase
{
    public function test_bulk_order_import_matches_the_refined_template_and_backend_contract(): void
    {
        $view = file_get_contents(resource_path('views/pages/bulk-order-import.blade.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $service = file_get_contents(app_path('Services/BulkOrderImportService.php'));
        $reader = file_get_contents(app_path('Services/SpreadsheetRowReader.php'));
        $jobService = file_get_contents(app_path('Services/JobService.php'));
        $baseMigration = file_get_contents(database_path('migrations/2026_08_10_200000_add_bulk_order_import_support.php'));
        $refinedMigration = file_get_contents(database_path('migrations/2026_08_15_051500_refine_bulk_order_import_fields.php'));
        $controller = file_get_contents(app_path('Http/Controllers/BulkOrderImportController.php'));
        $bulkCss = file_get_contents(public_path('css/flowtrack-bulk-order-import.css'));
        $ordersTable = file_get_contents(resource_path('views/components/jobs/table.blade.php'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $bulkJs = file_get_contents(public_path('js/flowtrack-bulk-order-import.js'));

        $this->assertStringContainsString('<h1>Import orders</h1>', $view);
        $this->assertStringContainsString('Only Client ID and Order Title are mandatory.', $view);
        $this->assertStringContainsString('Repeat Order No. becomes required only when Repeat Order? is Yes.', $view);
        $this->assertStringContainsString('Product ID may be blank.', $view);
        $this->assertStringContainsString('Normal, Urgent or Super Urgent', $view);
        $this->assertStringContainsString('If Reference Order No. already exists', $view);
        $this->assertStringContainsString('Client &amp; Product IDs validated', $view);
        $this->assertStringNotContainsString('Fallback Client ID', $view);
        $this->assertStringNotContainsString('Fallback Supplier ID', $view);
        $this->assertStringNotContainsString('What is Source Row ID?', $view);

        $this->assertStringContainsString("Route::get('/orders/bulk-import'", $routes);
        $this->assertStringContainsString("Route::post('/orders/bulk-import/validate'", $routes);
        $this->assertStringContainsString("Route::post('/orders/bulk-import/import'", $routes);
        $this->assertStringContainsString("name('orders.bulk-import.template')", $routes);

        $this->assertStringContainsString("'client_id' => ['clientid', 'clientcode']", $service);
        $this->assertStringContainsString("'is_repeat' => ['repeatorderyesno'", $service);
        $this->assertStringContainsString("'product_id' => ['productid'", $service);
        $this->assertStringContainsString("'estimated_delivery' => ['estimateddeliverydate']", $service);
        $this->assertStringContainsString("'production_urgency' => ['productionurgency'", $service);
        $this->assertStringContainsString("'shipment_urgency' => ['shipmenturgency'", $service);
        $this->assertStringContainsString("'notes' => ['notes', 'ordernotes']", $service);
        $this->assertStringContainsString('Client ID is required', $service);
        $this->assertStringContainsString('Order Title is required', $service);
        $this->assertStringContainsString('Repeat Order? must be Yes or No', $service);
        $this->assertStringContainsString('Repeat Order No. is required when Repeat Order? is Yes', $service);
        $this->assertStringContainsString('Product ID does not match an active Product', $service);
        $this->assertStringContainsString('Product Quantity must be a whole number', $service);
        $this->assertStringContainsString('Invalid Customer Requested Delivery Date', $service);
        $this->assertStringContainsString('Invalid Estimated Delivery Date', $service);
        $this->assertStringContainsString('resolveUrgency', $service);
        $this->assertStringContainsString("'notes' => blank(\$row['notes']) ? null : \$row['notes']", $service);
        $this->assertStringContainsString('resolveClientOrderWorkflow', $service);
        $this->assertStringContainsString('Select an Order workflow for this client', $service);
        $this->assertStringContainsString('workflowAvailableForClient', $service);
        $this->assertStringContainsString('bulk_order_import_rows', $service);
        $this->assertStringContainsString("'profile' => 'CLIENT_AUTO'", $service);
        $this->assertStringNotContainsString('Urgent? is required', $service);
        $this->assertStringNotContainsString('Order description is required', $service);
        $this->assertStringNotContainsString('Invalid received date', $service);

        $this->assertStringNotContainsString("'default_client_id' => ['nullable', 'integer']", $controller);
        $this->assertStringNotContainsString("'default_supplier_id' => ['nullable', 'integer']", $controller);
        $this->assertStringContainsString('.steps .step::before', $bulkCss);
        $this->assertStringContainsString('content:none!important', $bulkCss);
        $this->assertStringContainsString('ft-bulk-import-button', $ordersTable);
        $this->assertStringContainsString('/css/flowtrack-bulk-order-import.css', $layout);
        $this->assertStringContainsString('manual_workflows', $bulkJs);
        $this->assertStringContainsString('Client ID *', $bulkJs);
        $this->assertStringContainsString('Production Urgency', $bulkJs);
        $this->assertStringContainsString('Shipment Urgent', $bulkJs);

        $this->assertStringContainsString("in_array('clientid', \$keys, true)", $reader);
        $this->assertStringContainsString("in_array('ordertitle', \$keys, true)", $reader);
        $this->assertStringContainsString('legacy binary .xls', $reader);

        $this->assertStringContainsString("'is_repeat_order' => (bool) (\$data['is_repeat_order'] ?? false)", $jobService);
        $this->assertStringContainsString("'estimated_delivery_date' => \$data['estimated_delivery_date'] ?? null", $jobService);
        $this->assertStringContainsString("'production_urgency_ids'", $jobService);
        $this->assertStringContainsString("'shipment_urgency_ids'", $jobService);
        $this->assertStringContainsString("'notes' => blank(\$data['notes'] ?? null)", $jobService);

        $this->assertStringContainsString("Schema::create('bulk_order_imports'", $baseMigration);
        $this->assertStringContainsString("Schema::create('bulk_order_import_rows'", $baseMigration);
        $this->assertStringContainsString("Schema::hasColumn('flow_jobs', 'notes')", $refinedMigration);
        $this->assertStringContainsString("'name' => 'Super Urgent'", $refinedMigration);
        $this->assertFileExists(storage_path('app/templates/FlowTrack_Bulk_Order_Import_Template_v2.xlsx'));
    }
}
