<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Services\BoardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BoardLookupCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_lookups_cache_only_scalar_rows_and_return_property_accessible_objects(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);
        $client = Client::create([
            'name' => 'Cache Safe Client',
            'code' => 'CACHE-SAFE',
            'is_active' => true,
        ]);
        $workflow = Workflow::create([
            'name' => 'Cache Safe Workflow',
            'slug' => 'cache-safe-workflow',
            'is_active' => true,
        ]);
        $phase = WorkflowPhase::create([
            'workflow_id' => $workflow->id,
            'sequence' => 1,
            'name' => 'Request',
            'short_name' => 'Request',
            'allow_job_start' => true,
            'is_active' => true,
        ]);

        $service = app(BoardService::class);
        $lookups = $service->lookups($user);
        $phases = $service->phases($workflow->id);

        $this->assertSame($client->id, $lookups['clients']->first()->id);
        $this->assertSame('Cache Safe Client', $lookups['clients']->first()->name);
        $this->assertSame($user->id, $lookups['users']->first()->id);
        $this->assertSame($workflow->id, $lookups['workflows']->first()->id);
        $this->assertSame($phase->id, $phases->first()->id);

        $cachedLookups = Cache::get('flowtrack:board:lookups:v2:user:'.$user->id);
        $cachedWorkflows = Cache::get('flowtrack:board:workflows:v2');
        $cachedPhases = Cache::get('flowtrack:board:phases:v2:'.$workflow->id);

        $this->assertIsArray($cachedLookups['clients'][0]);
        $this->assertIsArray($cachedLookups['users'][0]);
        $this->assertIsArray($cachedWorkflows[0]);
        $this->assertIsArray($cachedPhases[0]);
        $this->assertNotInstanceOf(Client::class, $cachedLookups['clients'][0]);
        $this->assertNotInstanceOf(Workflow::class, $cachedWorkflows[0]);
        $this->assertNotInstanceOf(WorkflowPhase::class, $cachedPhases[0]);
    }

    public function test_invalid_legacy_lookup_cache_is_rebuilt_instead_of_reaching_blade(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);
        $client = Client::create([
            'name' => 'Recovered Client',
            'code' => 'RECOVERED',
            'is_active' => true,
        ]);

        Cache::put('flowtrack:board:lookups:v2:user:'.$user->id, [
            'clients' => ['legacy-serialized-value'],
            'users' => ['legacy-serialized-value'],
        ], now()->addMinutes(3));

        $lookups = app(BoardService::class)->lookups($user, false);

        $this->assertSame($client->id, $lookups['clients']->first()->id);
        $this->assertSame($user->id, $lookups['users']->first()->id);
        $this->assertIsArray(Cache::get('flowtrack:board:lookups:v2:user:'.$user->id)['clients'][0]);
    }
}
