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

    public function test_master_data_form_displays_code_as_locked_instead_of_editable(): void
    {
        $view = file_get_contents(resource_path('views/livewire/master-data/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/MasterData/Index.php'));

        $this->assertStringContainsString('<div class="ft-admin-locked">{{ $code }}</div>', $view);
        $this->assertStringContainsString('Automatically generated and permanently locked.', $view);
        $this->assertStringNotContainsString('<input wire:model="code"', $view);
        $this->assertStringContainsString('$this->code = $service->nextCode($this->group);', $component);
    }
}
