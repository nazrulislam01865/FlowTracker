<?php

namespace Tests\Feature;

use App\Services\SupplierBridge\SupplierDefaultTaskAssigner;
use App\Services\SupplierBridge\SupplierWorkflowProjection;
use Tests\TestCase;

class SupplierTaskPackDefaultsTest extends TestCase
{
    public function test_order_level_supplier_is_only_selected_when_unambiguous(): void
    {
        $this->assertNull(SupplierDefaultTaskAssigner::uniqueSupplierId([]));
        $this->assertSame(12, SupplierDefaultTaskAssigner::uniqueSupplierId([12]));
        $this->assertSame(12, SupplierDefaultTaskAssigner::uniqueSupplierId([12, 12]));
        $this->assertNull(SupplierDefaultTaskAssigner::uniqueSupplierId([12, 14]));
        $this->assertNull(SupplierDefaultTaskAssigner::uniqueSupplierId([0]));
    }

    public function test_billing_and_payment_can_never_be_supplier_tasks(): void
    {
        $this->assertNull(SupplierWorkflowProjection::actionKey('BILL_PREPARE', 6));
        $this->assertNull(SupplierWorkflowProjection::actionKey('PAY_PROCESS', 7));
        $this->assertSame('QC_CHECK', SupplierWorkflowProjection::actionKey('QC_CHECK', 4));
    }
}
