<?php

namespace Tests\Feature;

use App\Services\AccessControlService;
use Tests\TestCase;

class InquiryOrderTabPermissionTest extends TestCase
{
    public function test_product_lines_module_is_removed_and_products_is_the_single_product_authority(): void
    {
        $this->assertArrayNotHasKey('products', AccessControlService::MODULES);
        $this->assertArrayHasKey('catalog_products', AccessControlService::MODULES);
        $this->assertSame('Products', AccessControlService::MODULES['catalog_products']['name']);
        $this->assertSame(AccessControlService::ACTIONS, AccessControlService::supportedActions('catalog_products'));
        $this->assertTrue(AccessControlService::isUniversalRecordModule('catalog_products'));
        $this->assertFalse(AccessControlService::isParentRecordModule('catalog_products'));

        $this->assertArrayHasKey('finance', AccessControlService::MODULES);
        $this->assertTrue(AccessControlService::isParentRecordModule('finance'));
    }

    public function test_inquiry_detail_product_rows_use_products_matrix(): void
    {
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));

        $this->assertStringContainsString('Products &amp; quantities', $view);
        $this->assertStringContainsString('@if($canViewInquiryProducts)', $view);
        $this->assertStringContainsString("can(\$user, 'catalog_products', 'view')", $component);
        $this->assertStringContainsString("can(\$actor, 'catalog_products', 'edit')", $service);
        $this->assertStringContainsString("can(\$actor, 'catalog_products', 'create')", $service);
        $this->assertStringContainsString("can(\$actor, 'catalog_products', 'delete')", $service);
        $this->assertStringNotContainsString("canEditParentRecordModule(\$actor, 'products'", $service);
    }

    public function test_order_detail_product_rows_use_products_matrix(): void
    {
        $service = file_get_contents(app_path('Services/JobService.php'));
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));

        $this->assertStringContainsString('Products &amp; quantities', $overview);
        $this->assertStringContainsString("can(auth()->user(), 'catalog_products', 'view')", $overview);
        $this->assertStringContainsString("can(auth()->user(), 'catalog_products', 'edit')", $overview);
        $this->assertStringContainsString("can(\$actor, 'catalog_products', 'create')", $service);
        $this->assertStringContainsString("can(\$actor, 'catalog_products', 'delete')", $service);
        $this->assertStringNotContainsString("canEditParentRecordModule(\$actor, 'products'", $service);
    }

    public function test_cleanup_migration_removes_obsolete_product_lines_rows(): void
    {
        $migration = file_get_contents(database_path('migrations/2026_08_13_120500_remove_inquiry_order_product_lines_permission.php'));
        $this->assertStringContainsString("where('module_code', 'products')", $migration);
        $this->assertStringContainsString('->delete()', $migration);
    }
}
