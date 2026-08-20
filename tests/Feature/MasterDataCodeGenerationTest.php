<?php

namespace Tests\Feature;

use App\Models\MasterRecord;
use App\Models\User;
use App\Services\MasterDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_visible_master_data_type_has_an_automatic_code_prefix(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);

        $this->assertSame(array_keys(MasterDataService::LABELS), array_keys(MasterDataService::CODE_PREFIXES));

        foreach (MasterDataService::CODE_PREFIXES as $type => $prefix) {
            $this->assertSame($prefix.'-001', $service->nextCode($type));
        }
    }

    public function test_next_code_advances_past_generated_and_soft_deleted_codes(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();

        MasterRecord::create([
            'workspace_id' => $workspaceId,
            'type' => 'product',
            'code' => 'PRD-001',
            'name' => 'Existing Product',
            'status' => 'active',
        ]);

        $deleted = MasterRecord::create([
            'workspace_id' => $workspaceId,
            'type' => 'product',
            'code' => 'PRD-004',
            'name' => 'Deleted Product',
            'status' => 'inactive',
        ]);
        $deleted->delete();

        $this->assertSame('PRD-005', $service->nextCode('product'));
    }

    public function test_product_code_is_manual_while_other_master_codes_remain_generated_and_locked(): void
    {
        $view = file_get_contents(resource_path('views/livewire/master-data/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/MasterData/Index.php'));

        // The dedicated Product create modal accepts a user-supplied SKU/code.
        $this->assertStringContainsString('wire:model.live.debounce.220ms="code"', $view);
        $this->assertStringContainsString('Enter product code, e.g. TS-SUB-001', $view);
        $this->assertStringContainsString("\$this->group === 'product' ? '' : \$service->nextCode(\$this->group)", $component);
        $this->assertStringContainsString("if (!\$this->editId && \$this->group !== 'product')", $component);

        // Other Master Data types still use the existing generated locked-code UI.
        $this->assertStringContainsString('<div class="ft-admin-locked">{{ $code }}</div>', $view);
        $this->assertStringContainsString('Automatically generated and permanently locked.', $view);
    }
}
