<?php

namespace Tests\Feature;

use App\Models\MasterRecord;
use App\Models\User;
use App\Services\MasterDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RemoteAreaMasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_remote_area_is_a_visible_master_data_type_with_automatic_code_generation(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);

        $this->assertSame('Remote Areas', MasterDataService::LABELS['remote_area']);
        $this->assertSame('RMA', MasterDataService::CODE_PREFIXES['remote_area']);
        $this->assertSame('RMA-001', $service->nextCode('remote_area'));
        $this->assertSame('masterdata', MasterDataService::permissionModuleForType('remote_area'));
    }

    public function test_remote_area_stores_normalized_postal_code_and_optional_extra_charge(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);

        $record = $service->save('remote_area', [
            'code' => 'RMA-001',
            'name' => 'Highland District',
            'description' => 'Remote delivery zone',
            'status' => 'active',
            'sort_order' => 1,
            'metadata' => [
                'postal_code' => ' sw1a   1aa ',
                'extra_charge' => '27.50',
            ],
        ]);

        $this->assertSame('SW1A 1AA', $record->fresh()->remoteAreaPostalCode());
        $this->assertSame(27.5, $record->fresh()->remoteAreaExtraCharge());
        $this->assertSame($record->id, $service->remoteAreaForPostalCode(' sw1a 1aa ')?->id);
        // Matching is intentionally whitespace-insensitive so a shipping form
        // value such as SW1A1AA still matches the stored SW1A 1AA postal code.
        $this->assertSame($record->id, $service->remoteAreaForPostalCode('SW1A1AA')?->id);

        $withoutCharge = $service->save('remote_area', [
            'code' => 'RMA-002',
            'name' => 'Island Route',
            'status' => 'active',
            'sort_order' => 2,
            'metadata' => ['postal_code' => 'AB12 3CD'],
        ]);

        $this->assertNull($withoutCharge->fresh()->remoteAreaExtraCharge());
    }

    public function test_remote_area_postal_code_is_unique_within_the_workspace(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);

        $service->save('remote_area', [
            'code' => 'RMA-001',
            'name' => 'First Area',
            'status' => 'active',
            'sort_order' => 1,
            'metadata' => ['postal_code' => '90210'],
        ]);

        try {
            $service->save('remote_area', [
                'code' => 'RMA-002',
                'name' => 'Second Area',
                'status' => 'active',
                'sort_order' => 2,
                // Different spacing/case must not create a second logical
                // remote postal code in the same workspace.
                'metadata' => ['postal_code' => ' 90210 '],
            ]);
            $this->fail('Expected duplicate postal code validation to fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('remoteAreaPostalCode', $exception->errors());
        }
    }

    public function test_remote_area_search_matches_postal_code(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();

        MasterRecord::create([
            'workspace_id' => $workspaceId,
            'type' => 'remote_area',
            'code' => 'RMA-010',
            'name' => 'Outer District',
            'metadata' => ['postal_code' => 'ZX9 4PQ', 'extra_charge' => 12.5],
            'status' => 'active',
            'sort_order' => 10,
        ]);

        $this->assertSame(['RMA-010'], $service->list('remote_area', 'ZX9')->pluck('code')->all());
    }
    public function test_inactive_remote_area_does_not_match_shipping_postal_code(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $service = app(MasterDataService::class);

        $service->save('remote_area', [
            'code' => 'RMA-020',
            'name' => 'Inactive Island',
            'status' => 'inactive',
            'sort_order' => 20,
            'metadata' => ['postal_code' => 'ZZ1 1ZZ', 'extra_charge' => 99],
        ]);

        $this->assertNull($service->remoteAreaForPostalCode('ZZ11ZZ'));
    }

}
