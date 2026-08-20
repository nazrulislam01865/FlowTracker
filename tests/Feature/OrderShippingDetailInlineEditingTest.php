<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderShippingDetailInlineEditingTest extends TestCase
{
    public function test_order_detail_shipping_section_is_responsive_and_inline_editable(): void
    {
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $jobs = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $service = file_get_contents(app_path('Services/JobService.php'));
        $picker = file_get_contents(resource_path('views/components/ui/inline-remote-catalog.blade.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/FilterOptionController.php'));
        $filterOptions = file_get_contents(app_path('Services/FilterOptionService.php'));
        $css = file_get_contents(resource_path('css/flowtrack.css'));

        $planningPosition = strpos($overview, 'Planning &amp; ownership');
        $shippingPosition = strpos($overview, 'ft-order-shipping-side-panel');
        $productsPosition = strpos($overview, 'order-products-card');

        $this->assertNotFalse($planningPosition);
        $this->assertNotFalse($shippingPosition);
        $this->assertNotFalse($productsPosition);
        $this->assertGreaterThan($planningPosition, $shippingPosition);
        $this->assertLessThan($productsPosition, $shippingPosition);

        $this->assertStringContainsString('shipping_address', $overview);
        $this->assertStringContainsString('shipping_phone_country_code', $service);
        $this->assertStringContainsString('shipping_phone', $overview);
        $this->assertStringContainsString('shipping_postal_code', $overview);
        $this->assertStringContainsString('updateJobShippingField', $overview);
        $this->assertStringContainsString('updateJobShippingPhone', $overview);
        $this->assertStringContainsString('type="phone-country-codes"', $overview);
        $this->assertStringContainsString(':clearable="true"', $overview);
        $this->assertStringContainsString('ft-inline-remote-sync', $picker);
        $this->assertStringContainsString("ofType('phone_country_code')", $service);
        $this->assertStringContainsString("Route::get('/filter-options/{type}', FilterOptionController::class)", $routes);
        $this->assertStringContainsString('phone-country-codes', $controller);
        $this->assertStringContainsString("'phone-country-codes' => \$this->phoneCountryCodes(\$search, \$limit, \$offset)", $filterOptions);
        $this->assertStringContainsString('@media (max-width:640px)', $css);
        $this->assertStringContainsString('.ft-order-shipping-detail-grid', $css);
        $this->assertMatchesRegularExpression('/#\\[Renderless\\]\\s+public function updateJobShippingField\\b/', $jobs);
        $this->assertMatchesRegularExpression('/#\\[Renderless\\]\\s+public function updateJobShippingPhone\\b/', $jobs);
    }
}
