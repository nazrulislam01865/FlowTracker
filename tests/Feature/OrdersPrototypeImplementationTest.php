<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrdersPrototypeImplementationTest extends TestCase
{
    public function test_orders_list_keeps_the_supplied_prototype_structure(): void
    {
        $view = file_get_contents(resource_path('views/components/jobs/table.blade.php'));
        $component = file_get_contents(app_path('Livewire/Orders/Index.php'));
        $jobsPage = file_get_contents(resource_path('views/pages/jobs.blade.php'));
        $service = file_get_contents(app_path('Services/JobService.php'));
        $sidebar = file_get_contents(resource_path('views/layouts/partials/sidebar.blade.php'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString('<h1>Orders</h1><p>Fast access to every active and completed order</p>', $view);
        $this->assertStringContainsString('Created by / on', $view);
        $this->assertStringContainsString('<span>Order</span><span>Inquiry</span><span>Client / Products</span>', $view);
        $this->assertStringContainsString('<span>Phase</span>', $view);
        $this->assertStringContainsString('$phaseName = $job->phase?->name', $view);
        $this->assertStringContainsString('Owner / Delivery', $view);
        $this->assertStringContainsString('Search order, inquiry, client, product, creator or owner', $view);
        $this->assertStringContainsString('wire:model.live.debounce.700ms="search"', $view);
        $this->assertStringNotContainsString('results update after 700 ms', $view);
        $this->assertStringNotContainsString('wire:model.live.debounce.350ms="search"', $view);
        $this->assertStringContainsString('+ New Order', $view);
        $this->assertStringNotContainsString('ft-job-bulk-bar', $view);
        $this->assertStringNotContainsString('toggleSelectAllJobs', $view);
        $this->assertStringContainsString('public int $perPage = 25;', $component);
        $this->assertStringContainsString('paginateOrders(', $component);
        $this->assertStringContainsString('$this->search', $component);
        $this->assertStringContainsString('$this->perPage', $component);
        $this->assertStringContainsString('<livewire:orders.index />', $jobsPage);
        $this->assertStringContainsString('<livewire:jobs.index />', $jobsPage);
        $this->assertStringNotContainsString("#[On('flowtrack-notification')]", $component);
        $this->assertStringContainsString('wire:loading.delay.grid', $view);
        $this->assertStringContainsString('.ft-orders-prototype .ft-load-skeleton{display:none;', $view);
        $this->assertStringContainsString("'items:id,flow_job_id,product_name,category_name,quantity,sort_order'", $service);
        $this->assertStringContainsString("'activities.subject_type'", $service);
        $this->assertStringContainsString("'activities.subject_id'", $service);
        $this->assertStringNotContainsString("'createdActivity:id,subject_type,subject_id,user_id,created_at'", $service);
        $this->assertStringNotContainsString("'tasks' => fn", substr($service, strpos($service, 'public function paginateOrders'), strpos($service, 'public function summaryCounts') - strpos($service, 'public function paginateOrders')));
        $this->assertStringContainsString('if ($searchLength > 0 && $searchLength < 3)', $service);
        $this->assertStringContainsString("preg_match('/^(ORDER|JOB|ORD)[-0-9]/i', \$token)", $service);
        $this->assertStringContainsString("\$like = \$looksLikeReference ? \$token.'%' : '%'.\$token.'%'", $service);
        $this->assertStringContainsString('label="Order"', $sidebar);
        $this->assertStringContainsString("Route::get('/orders'", $routes);
    }
}
