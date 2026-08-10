<?php

namespace Tests\Feature;

use Tests\TestCase;

class BulkOrderImportImplementationTest extends TestCase
{
    public function test_bulk_order_import_keeps_the_supplied_prototype_contract_and_real_backend_hooks(): void
    {
        $view = file_get_contents(resource_path('views/pages/bulk-order-import.blade.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $service = file_get_contents(app_path('Services/BulkOrderImportService.php'));
        $reader = file_get_contents(app_path('Services/SpreadsheetRowReader.php'));
        $jobService = file_get_contents(app_path('Services/JobService.php'));
        $migration = file_get_contents(database_path('migrations/2026_08_10_200000_add_bulk_order_import_support.php'));
        $controller = file_get_contents(app_path('Http/Controllers/BulkOrderImportController.php'));
        $bulkCss = file_get_contents(public_path('css/flowtrack-bulk-order-import.css'));
        $ordersTable = file_get_contents(resource_path('views/components/jobs/table.blade.php'));

        $this->assertStringContainsString('<h1>Import orders</h1>', $view);
        $this->assertStringContainsString('Create many orders safely from an Excel or CSV file.', $view);
        $this->assertStringContainsString('Drop your order file here', $view);
        $this->assertStringContainsString('up to 10,000 rows', $view);
        $this->assertStringContainsString('If reference already exists *', $view);
        $this->assertStringContainsString('Skip existing order', $view);
        $this->assertStringContainsString('Update matching order', $view);
        $this->assertStringContainsString('Create a separate order', $view);
        $this->assertStringContainsString('No master data created silently', $view);
        $this->assertStringContainsString('Client-based workflow', $view);
        $this->assertStringContainsString('Fallback Client ID', $view);
        $this->assertStringNotContainsString('Import profile', $view);
        $this->assertStringContainsString('Audit log and source fingerprint saved.', $view);

        $this->assertStringContainsString("Route::get('/orders/bulk-import'", $routes);
        $this->assertStringContainsString("Route::post('/orders/bulk-import/validate'", $routes);
        $this->assertStringContainsString("Route::post('/orders/bulk-import/import'", $routes);
        $this->assertStringContainsString("name('orders.bulk-import.template')", $routes);

        $this->assertStringContainsString("'referenceorderno'", $service);
        $this->assertStringContainsString("'sourcerowid'", $service);
        $this->assertStringContainsString("'Critical'", $service);
        $this->assertStringContainsString('Source Row ID was already imported', $service);
        $this->assertStringContainsString('Urgent? must be Yes or No', $service);
        $this->assertStringContainsString('Required delivery date cannot be earlier than received date', $service);
        $this->assertStringContainsString('Client ID is required so FlowTrack can select the client workflow', $service);
        $this->assertStringContainsString('resolvePreferredWorkflow', $service);
        $this->assertStringContainsString('workflowAvailableForClient', $service);
        $this->assertStringContainsString('bulk_order_import_rows', $service);
        $this->assertStringContainsString("'profile' => 'CLIENT_AUTO'", $service);
        $this->assertStringNotContainsString("'profile' => ['required', 'in:IID,NEP']", $controller);
        $this->assertStringContainsString('.steps .step::before', $bulkCss);
        $this->assertStringContainsString('content:none!important', $bulkCss);
        $this->assertStringContainsString('ft-bulk-import-button', $ordersTable);

        $this->assertStringContainsString('findHeaderRow', $reader);
        $this->assertStringContainsString('header_row', $reader);
        $this->assertStringContainsString('legacy binary .xls', $reader);

        $this->assertStringContainsString("'order_number' => blank(\$data['order_number']", $jobService);
        $this->assertStringContainsString("'received_date' => \$data['received_date']", $jobService);
        $this->assertStringContainsString("'source_row_id' => blank(\$data['source_row_id']", $jobService);

        $this->assertStringContainsString("dropUnique(['order_number'])", $migration);
        $this->assertStringContainsString("unique('source_row_id'", $migration);
        $this->assertStringContainsString("Schema::create('bulk_order_imports'", $migration);
        $this->assertStringContainsString("Schema::create('bulk_order_import_rows'", $migration);
        $this->assertFileExists(storage_path('app/templates/FlowTrack_Bulk_Order_Import_Template_v2.xlsx'));
    }
}
