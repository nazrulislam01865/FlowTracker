<?php

namespace Tests\Feature;

use App\Services\AccessControlService;
use Tests\TestCase;

class InquiryOrderTabPermissionTest extends TestCase
{
    public function test_product_and_finance_are_shared_parent_record_modules(): void
    {
        foreach (['products', 'finance'] as $module) {
            $this->assertArrayHasKey($module, AccessControlService::MODULES);
            $this->assertSame(AccessControlService::ACTIONS, AccessControlService::supportedActions($module));
            $this->assertTrue(AccessControlService::isParentRecordModule($module));
            $this->assertFalse(AccessControlService::supportsScope($module));
        }

        $this->assertSame('Product', AccessControlService::MODULES['products']['name']);
        $this->assertSame('Finance', AccessControlService::MODULES['finance']['name']);
        $this->assertArrayNotHasKey('inquiry_products', AccessControlService::MODULES);
        $this->assertArrayNotHasKey('inquiry_finance', AccessControlService::MODULES);
        $this->assertArrayNotHasKey('order_products', AccessControlService::MODULES);
        $this->assertArrayNotHasKey('order_finance', AccessControlService::MODULES);
    }

    public function test_inquiry_detail_uses_archive_10_layout_and_shared_product_permission(): void
    {
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $service = file_get_contents(app_path('Services/InquiryService.php'));
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));

        $this->assertStringContainsString('<button class="tab active" type="button">Overview</button>', $view);
        $this->assertStringNotContainsString("setDetailTab('products')", $view);
        $this->assertStringNotContainsString("setDetailTab('finance')", $view);
        $this->assertStringContainsString('Products &amp; quantities', $view);
        $this->assertStringContainsString('@if($canViewInquiryProducts)', $view);
        $this->assertStringContainsString("can(\$user, 'products', 'view')", $component);
        $this->assertStringContainsString("canEditParentRecordModule(\$actor, 'products', \$inquiry)", $service);
        $this->assertStringContainsString("canEditParentRecordModule(\$actor, 'finance', \$inquiry)", $service);
        $this->assertStringContainsString("can(\$actor, 'products', 'create')", $service);
        $this->assertStringContainsString("can(\$actor, 'products', 'delete')", $service);
    }

    public function test_order_detail_uses_archive_10_layout_and_shared_product_permission(): void
    {
        $service = file_get_contents(app_path('Services/JobService.php'));
        $detail = file_get_contents(resource_path('views/components/jobs/detail.blade.php'));
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $component = file_get_contents(app_path('Livewire/Jobs/Index.php'));

        $this->assertStringContainsString("\$tabs = ['overview'=>'Overview','inquiry'=>'Inquiry'];", $detail);
        $this->assertStringNotContainsString("\$tabs['products']", $detail);
        $this->assertStringNotContainsString("\$tabs['finance']", $detail);
        $this->assertStringContainsString('Products &amp; quantities', $overview);
        $this->assertStringContainsString("can(auth()->user(), 'products', 'view')", $overview);
        $this->assertStringContainsString("in_array(\$tab, ['overview','inquiry'], true)", $component);
        $this->assertStringContainsString("canEditParentRecordModule(\$actor, 'products', \$job)", $service);
        $this->assertStringContainsString("canEditParentRecordModule(\$actor, 'finance', \$job)", $service);
        $this->assertStringContainsString("can(\$actor, 'products', 'create')", $service);
        $this->assertStringContainsString("can(\$actor, 'products', 'delete')", $service);
    }

    public function test_permission_migrations_create_shared_rows_and_cleanup_legacy_rows(): void
    {
        $initial = file_get_contents(database_path('migrations/2026_08_12_160000_add_inquiry_order_tab_permissions.php'));
        $consolidation = file_get_contents(database_path('migrations/2026_08_12_170000_consolidate_product_finance_permissions.php'));

        $this->assertStringContainsString("'module_code' => 'products'", $initial);
        $this->assertStringContainsString("'module_code' => 'finance'", $initial);
        $this->assertStringContainsString("\$hasLegacyFinanceAccess ? ['view'] : []", $initial);
        $this->assertStringContainsString("['products', 'inquiry_products', 'order_products']", $consolidation);
        $this->assertStringContainsString("['finance', 'inquiry_finance', 'order_finance']", $consolidation);
        $this->assertStringContainsString("whereIn('module_code', ['inquiry_products', 'inquiry_finance', 'order_products', 'order_finance'])", $consolidation);
    }
}
