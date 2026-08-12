<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MasterDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataCreatorColumnsTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_record_keeps_its_original_creator_when_edited(): void
    {
        $creator = User::factory()->create(['is_super_admin' => true]);
        $editor = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($creator);
        $service = app(MasterDataService::class);

        $product = $service->save('product', [
            'code' => 'PRD-CREATOR',
            'name' => 'Creator Test Product',
            'status' => 'active',
            'sort_order' => 0,
        ]);

        $this->assertSame($creator->id, $product->created_by);

        $this->actingAs($editor);
        $product = $service->save('product', [
            'code' => $product->code,
            'name' => 'Creator Test Product Updated',
            'status' => 'active',
            'sort_order' => 0,
        ], $product->id);

        $this->assertSame($creator->id, $product->fresh()->created_by);
    }

    public function test_product_table_exposes_created_by_and_created_at_columns(): void
    {
        $view = file_get_contents(resource_path('views/livewire/master-data/index.blade.php'));

        $this->assertStringContainsString('x-model="visible.createdBy"> Created by', $view);
        $this->assertStringContainsString('x-model="visible.createdAt"> Created at', $view);
        $this->assertStringContainsString('<th x-show="visible.createdBy">Created by</th>', $view);
        $this->assertStringContainsString('<th x-show="visible.createdAt">Created at</th>', $view);
        $this->assertStringContainsString("{{ \$r->creator?->name ?: 'System' }}", $view);
        $this->assertStringContainsString("\$createdAt?->format('M j, Y g:i A')", $view);
    }
}
